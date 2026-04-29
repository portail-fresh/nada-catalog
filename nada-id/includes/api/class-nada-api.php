<?php

/**
 * Classe Nada_API
 *
 * Cette classe gère toute la communication avec le serveur NADA.
 * Responsabilités :
 * - Construire les requêtes HTTP vers l’API
 * - Authentifier les appels (token, clé API…)
 * - Envoyer / récupérer des datasets
 * - Fournir des méthodes simples pour interagir avec NADA
 */

class Nada_API
{

    private $base_url;

    public function __construct()
    {
        $this->base_url = get_nada_server_url() . "/api";
    }

    /* =========================================================
     * ===============  MÉTHODES D’APPELS GÉNÉRIQUES  ==========
     * ========================================================= */

    /** Appel générique vers l’API */
    private function request(string $endpoint, string $method = 'GET', $body = []): array
    {

        $url = $this->base_url . $endpoint;

        $args = [
            'method' => $method,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-API-KEY' => $this->get_token()
            ],
            'body' => !empty($body) ? wp_json_encode($body) : null
        ];

        $response = wp_remote_request($url, $args);

        // Erreur wp (DNS,SSL ...)
        if (is_wp_error($response)) {
            nada_id_log("Erreur API [$method $endpoint]: " . $response->get_error_message());
            return [
                'success' => false,
                'error' => 'Erreur de connexion à l’API NADA : ' . $response->get_error_message(),
                'status' => 500
            ];
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);


        // Erreur HTTP
        if ($status_code < 200 || $status_code >= 300) {
            nada_id_log("Réponse API [$status_code] $endpoint : " . print_r($data, true));
            return [
                'success' => false,
                'error' => $data['message'] ?? "Erreur API ($status_code)",
                'status' => $status_code,
            ];
        }

        return [
            'success' => true,
            'data' => $data,
            'status' => 200
        ];
    }

    /** Appel POST spécifique pour form-data / url-encoded */
    private function request_form_data(string $endpoint, mixed $body, ?string $nada_token = null): array
    {
        $url = $this->base_url . $endpoint;

        if (is_array($body)) {
            $body = http_build_query($body);
        }

        $args = [
            'method' => 'POST',
            'body' => $body, // envoie directement comme form-data
            'headers' => [
                'X-API-KEY' => $nada_token ?? $this->get_token(),
            ],
        ];

        $response = wp_remote_request($url, $args);

        // Erreur wp (DNS,SSL ...)
        if (is_wp_error($response)) {
            nada_id_log("Erreur API [$endpoint]: " . $response->get_error_message());

            return [
                'success' => false,
                'error' => 'Erreur de connexion à l’API NADA : ' . $response->get_error_message(),
                'status' => 500
            ];
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Erreur HTTP
        if ($status_code < 200 || $status_code >= 300) {
            nada_id_log("Réponse API [$status_code] $endpoint : " . print_r($data, true));
            return [
                'success' => false,
                'error' => $data['message'] ?? "Erreur API ($status_code)",
                'status' => $status_code,
                'details' => $data['errors'] ?? []
            ];
        }

        return [
            'success' => true,
            'data' => $data,
            'status' => 200
        ];
    }

    /* =========================================================
     * ===============  UTILISATEURS & AUTH  ===================
     * ========================================================= */

    /**
     * Créer un utilisateur côté NADA
     */
    public function createUser($data): array
    {
        $response = $this->request_form_data('/auth/register', $data);

        // Si l'utilisateur est créé, créer le token
        if ($response['data']['status'] == 'success') {
            $apiData = [
                "email" => $data['email'],
                "password" => $data['password']
            ];
            return $this->create_api_key($apiData);
        }

        return $response;
    }

    /**
     * modifier un utilisateur côté NADA
     */
    public function update_user($data): array
    {
        return $this->request_form_data('/auth/update_user', $data);
    }

    /**
     * Création d'un token
     */
    public function create_api_key(array $data): array
    {
        return $this->request_form_data('/auth/create_api_key', $data);
    }

    /** login NADA */
    public function login(): array
    {
        $user = wp_get_current_user();
        if (!$user || empty($user->user_email)) {
            return ['success' => false, 'error' => 'Utilisateur non connecté'];
        }

        // Récupérer le mot de passe
        $nada_default_password = get_default_password_nada();
        if (!$nada_default_password) {
            return ['success' => false, 'error' => 'Mot de passe NADA manquant'];
        }

        $data = [
            "email" => $user->user_email,
            "password" => $nada_default_password
        ];

        return $this->request_form_data('/auth/login/', $data);
    }

    /* =========================================================
     * ===============  DATASETS (ÉTUDES)  =====================
     * ========================================================= */

    /** Récuperer toute la liste des etudes */
    public function get_datasets()
    {
        $lang = pll_current_language() ?? 'fr';
        return $this->request("/datasets/?limit=-1&lang=$lang", 'GET');
    }

    /** Récuperer la liste des etudes sselon utilisateur connécté */
    public function user_datasets_post($user_id, $is_admin, $offset, $limit, $sort_by, $sort_order, $search, $lang, $is_advanced = 0, $advanced_criteria = [], $global_operator = 'AND'): array
    {
        $limit = $limit ?? 10;

        $data = [
            'user_id' => $user_id,
            'is_admin' => $is_admin,
            'offset' => $offset,
            'limit' => $limit,
            'sort_by' => $sort_by,
            'sort_order' => $sort_order,
            'search' => $search,
            'lang' => $lang,
            'is_advanced' => $is_advanced,
            'global_operator' => $global_operator
        ];
        if (!empty($advanced_criteria)) {
            $data['advanced_criteria'] = $advanced_criteria;
        }
        $response = $this->request_form_data("/datasets/user_datasets", $data);

        return $response['success']
            ? ['success' => true, 'datasets' => $response['data'] ?? []]
            : $response;
    }
    /**
     * Récuperer une étude
     */
    public function get_catalog(string $idno, ?string $originPage = ''): array
    {

        $response = $this->request("/catalog/$idno", 'GET');

        if (!$response['success'] || empty($response['data']['dataset'])) {
            return ['success' => false, 'error' => 'Dataset introuvable ou réponse invalide', 'status' => $response['status']];
        }


        $dataset = $response['data']['dataset'];

        // Incrementer le nombre de vue en cas l'action du detail est arrivé depuis liste catalog
        if ($originPage == 'catalog') {

            $views = (int)($dataset['total_views'] ?? 0) + 1;

            $res = $this->request("/datasets/$idno", 'PUT', ['total_views' => $views]);
            if ($res['success'] === false) {
                // logging uniquement pas d'empechement de recuperation d'une etude
                nada_id_log(
                    "Impossible d'incrémenter le nombre de total vues pourt l'etude $idno (status {$res['status']})"
                );
            }

            // On reflète l'incrément côté retour
            $dataset['total_views'] = $views;
        }

        return [
            'success' => true,
            'dataset' => $dataset
        ];
    }

    /**
     * Créer une étude
     */
    public function create_study($data, ?string $nada_token = null, ?string $updated_by = null, ?string $created_by = null): array
    {
        $params = [];

        if (!empty($updated_by)) {
            $params[] = 'updatedby=' . urlencode($updated_by);
        }

        if (!empty($created_by)) {
            $params[] = 'createdby=' . urlencode($created_by);
        }

        $endpoint = '/datasets/create/survey';

        if (!empty($params)) {
            $endpoint .= '?' . implode('&', $params);
        }
        
        // Appel POST SURVEY
        return $this->request_form_data($endpoint, $data, $nada_token);
    }

    /**
     * Créer une étude
     */
    public function update_study($idno, $data, ?string $nada_token): array
    {
        // Appel UPDATE SURVEY
        return $this->request_form_data("/datasets/update/survey/$idno", $data, $nada_token);
    }

    public function update_idno_study($data): array
    {
        return $this->request("/datasets/replace_idno", 'POST', $data);
    }

    /**
     * Modifier un champs de l'etude
     */
    public function update_study_field(string $idno, array $data): array
    {
        return $this->request("/datasets/$idno", 'PUT', $data);
    }

    /*
    * Supprimer une étude
    */
    public function delete_study($idno): array
    {
        return $this->request("/datasets/$idno", 'DELETE');
    }

    public function fech_study_id(): array
    {
        $response = $this->request('/catalog/last_survey_id', 'GET');
        return $response;
    }

    /** Supprimer tous les datasets pour les deux langues */
    public function delete_all_datasets()
    {
        $all_deleted = [];

        foreach (['fr', 'en'] as $lang) {
            $response = $this->request("/datasets/?limit=-1&lang=$lang", 'GET');

            if (!($response['success'] ?? false) || empty($response['data']['datasets'])) {
                continue; // Aucun dataset pour cette langue
            }

            $datasets = $response['data']['datasets'];
            $deleted = [];

            foreach ($datasets as $dataset) {
                $idno = $dataset['idno'];

                // Supprimer uniquement les datasets correspondant à PEF
                if (str_contains($idno, 'PEF')) {
                    $resdel = $this->request("/datasets/$idno", 'DELETE');

                    if ($resdel['success'] ?? false) {
                        $deleted[] = $idno;
                        $all_deleted[] = $idno;
                    }

                    // Pause de 2 secondes entre chaque suppression
                    sleep(seconds: 2);
                }
            }
        }

        return [
            'success' => true,
            'deleted_count' => count($all_deleted),
            'deleted_ids' => $all_deleted,
        ];
    }


    /**
     * Importer un fichier DDI (XML) vers NADA
     */
    public function import_ddi(array $params): array
    {
        $url = $this->base_url . '/datasets/import_ddi';

        $tmpXmlPath = sys_get_temp_dir() . '/import_' . time() . '.xml';
        copy($params['file_path'], $tmpXmlPath);

        $postfields = [
            'type' => $params['type'] ?? 'survey',
            'repositoryid' => $params['repositoryid'] ?? 'FReSH',
            'overwrite' => $params['overwrite'] ?? 'no',
            'published' => $params['published'] ?? '1',
            'file' => new CURLFile($tmpXmlPath, 'text/xml', basename($tmpXmlPath))
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_HTTPHEADER => [
                'X-API-KEY: ' . $this->api_key,
            ],
        ]);

        $response = curl_exec($ch);
        $error = curl_error(handle: $ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        $data = json_decode($response, true);
        if (is_null($data)) {
            $data = ['raw_response' => trim($response)];
        }

        return [
            'success' => $status < 400,
            'status' => $status,
            'data' => $data,
        ];
    }



    /* =========================================================
     * ===============  CATALOGUES & FACETTES  =================
     * ========================================================= */

    /* Récuperer la liste des catalogues avec tri et limite */
    public function get_catalogs(string $lang = "", string $filters = "", string $search = "", string $sortBy = "", string $sortOrder = "", string $limit = "", int $page = 1): array
    {

        //Test commit
        $params = [];

        if ($sortBy) $params['sort_by'] = $sortBy;
        if ($sortOrder) $params['sort_order'] = $sortOrder;
        if ($limit) $params['ps'] = ($limit === '-1') ? 999999 : (int)$limit;
        if ($lang) $params['lang'] = $lang;
        $params['page'] = $page;

        $baseQuery = http_build_query($params);
        $queryParams = [];

        if ($search) $queryParams[] = 'sk=' . urlencode($search);
        if ($filters) $queryParams[] = $filters;
        if ($baseQuery) $queryParams[] = $baseQuery;

        $endpoint = ($search || $filters) ? '/catalog/search' : '/catalog';
        if ($queryParams) $endpoint .= '?' . implode('&', $queryParams);

        return $this->request($endpoint);
    }

    /* Recuperer les facets */
    public function get_facet_custom(?string $language = null): array
    {
        $lang = $language ?? pll_current_language();
        return $this->request("/catalog/filters_ID/$lang");
    }

    /**
     * Recherche Avancée dans les catalogues
     * * @param array $criteria Tableau des critères ex: [['field'=>'year', 'value'=>'2020']]
     * @param string $operator 'AND' ou 'OR'
     * @param string $lang Langue (fr/en)
     * @param int $limit Nombre de résultats par page
     * @param int $page Numéro de page
     * @param string $sortBy Champ de tri (ex: 'created', 'title')
     * @param string $sortOrder Ordre de tri ('asc' ou 'desc')
     */
    public function get_catalogs_advanced(array $criteria, string $operator = 'AND', string $lang = 'fr', int $limit = 15, int $page = 1, string $sortBy = 'created', string $sortOrder = 'desc'): array
    {
        // Construction du corps de la requête
        $body = [
            'criteria' => $criteria,
            'operator' => $operator,
            'lang' => $lang,
            'limit' => $limit,
            'page' => $page,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder
        ];

        return $this->request('/catalog/advanced_search', 'POST', $body);
    }

    // =========================================================
    // ===============  CHECK EXIST USER  ======================
    // =========================================================
    public function check_exist_user(string $email)
    {
        return wp_remote_post(
            $this->base_url . '/auth/check_existing_user',
            [
                'timeout' => 15,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => [
                    'email' => $email,
                ],
            ]
        );
    }

    private function get_token(): ?string
    {
        return get_nada_token(); // récupère toujours le token de l'utilisateur courant
    }

    /**
     * récupération statistics
    */
    public function get_statistics_catalog_dashboard(string $lang = 'fr'):array
    {
       return $this->request("/catalog/dashboard_stats?lang=$lang", 'GET');
    }
}

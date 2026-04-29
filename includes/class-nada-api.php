<?php

/**
 * Classe Nada_API
 *
 * Cette classe gère toute la communication avec l’API NADA.
 * Responsabilités :
 * - Construire les requêtes HTTP vers l’API
 * - Authentifier les appels (token, clé API…)
 * - Envoyer / récupérer des datasets
 * - Fournir des méthodes simples pour interagir avec NADA
 */

class Nada_API
{

    private $base_url;
    private $api_key;

    public function __construct()
    {
        $this->base_url = get_nada_server_url()."/api";
        $this->api_key  =  $this->get_nada_token();
    }

    /* =========================================================
     * ===============  MÉTHODES D’APPELS GÉNÉRIQUES  ==========
     * ========================================================= */

    /**
     * Appel générique vers l’API
     */
    private function request(string $endpoint, string $method = 'GET', $body = []): array
    {

        $url = $this->base_url . $endpoint;

        $args = [
            'method'  => $method,
            'headers' => [
                'Content-Type'  => 'application/json',
                'X-API-KEY' => $this->api_key
            ],
        ];

        if (!empty($body)) {
            $args['body'] = wp_json_encode($body);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            nada_id_log("Erreur API [$method $endpoint]: " . $response->get_error_message());
            return [
                'success' => false,
                'error'   =>  'Erreur de connexion à l’API NADA : ' . $response->get_error_message(),
            ];
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $code = wp_remote_retrieve_response_code($response);

        if ($code >= 400) {
            nada_id_log("Réponse API [$code] $endpoint : " . print_r($data, true));
            return [
                'success' => false,
                'error'   => $data['message'] ?? "Erreur API ($code)",
                'status'  => $code,
            ];
        }

        return [
            'success' => true,
            'data'    => $data,
        ];
    }

    /**
     * Appel POST spécifique pour form-data / url-encoded
     */
    private function request_form_data(string $endpoint, mixed $body, ?string $nada_token = null): array
    {
        $url = $this->base_url . $endpoint;

        $args = [
            'method'  => 'POST',
            'body'    => $body, // envoie directement comme form-data
            'headers' => [
                'X-API-KEY' => $nada_token ?? $this->api_key,
            ],
        ];

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            nada_id_log("Erreur form-data [$endpoint]: " . $response->get_error_message());

            return [
                'success' => false,
                'error'   => 'Erreur de connexion à l’API NADA : ' . $response->get_error_message(),
            ];
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        return [
            'success' => true,
            'data'    => $data,
        ];
    }

    /* =========================================================
     * ===============  UTILISATEURS & AUTH  ===================
     * ========================================================= */

    /**
     * Créer un utilisateur côté NADA
     */
    public function create_user($data): array
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
     * Création d'un token
     */
    public function create_api_key(array $data): array
    {
        return $this->request_form_data('/auth/create_api_key', $data);
    }

    /** login NADA */
    private function login(): array
    {
        $user = wp_get_current_user();
        if (!$user || empty($user->user_email)) {
            return ['success' => false, 'error' => 'Utilisateur non connecté'];
        }

        // Récupérer le mot de passe depuis une option sécurisée
        $nada_default_password = get_option('nada_id_default_password', null);
        if (!$nada_default_password) {
            nada_id_log("Mot de passe par défaut NADA non défini dans les options !");
            return ['success' => false, 'error' => 'Mot de passe NADA manquant'];
        }

        $data = [
            "email" => $user->user_email,
            "password" =>  $nada_default_password
        ];

        $response = $this->request_form_data('/auth/login/', $data);
        return $response;
    }

    /**
     * Récupère le token NADA de l'utilisateur connecté
     */
    private function get_nada_token()
    {
        $user_id = get_current_user_id();
        if (!$user_id) {
            return null;
        }
        // Récupérer le token existant
        $nada_token = get_user_meta($user_id, 'nada_token', true);

        if (!empty($nada_token)) {
            return $nada_token;
        }

        // Relogger côté NADA, alimenter nada token
        $response = $this->login();

        if ($response['success'] && !empty($response['data']['user']['api_keys'][0])) {
            $new_token = sanitize_text_field($response['data']['user']['api_keys'][0]);
            update_user_meta($user_id, 'nada_token', $new_token);

            return $new_token;
        }

        return null;
    }

    /* =========================================================
     * ===============  DATASETS (ÉTUDES)  =====================
     * ========================================================= */

    /** Récuperer la liste des etudes */
    public function get_datasets()
    {
        $lang = pll_current_language();
        $response = $this->request("/datasets/?limit=-1&lang=$lang", 'GET');

        return $response['success']
            ? ['success' => true, 'datasets' => $response['data']['datasets'] ?? []]
            : $response;
    }

    /**
     * Récuperer une étude (dataset)
     */
    public function get_dataset(string $idno): array
    {
        $response = $this->request("/catalog/$idno", 'GET');

        if (!$response['success'] || empty($response['data']['dataset'])) {
            return ['success' => false, 'error' => 'Dataset introuvable ou réponse invalide'];
        }

        return [
            'success'  => true,
            'dataset' => $response['data']['dataset']
        ];
    }

    /**
     * Créer une étude
     */
    public function create_study($data, ?string $nada_token = null): array
    {
        // Appel POST SURVEY
        return $this->request_form_data('/datasets/create/survey/', $data,  $nada_token);
    }

    /**
     * Créer une étude
     */
    public function update_study($idno, $data): array
    {
        // Appel UPDATE SURVEY
        return $this->request_form_data("/datasets/update/survey/$idno", $data);
    }

    /**
     * Modifier un champs de l'etude
     */
    public function update_study_field(string $idno, array $data): array
    {
        $response = $this->request("/datasets/$idno", 'PUT', $data);
        return $response;
    }

    /*
    * Supprimer une étude
    */
    public function delete_study($idno): array
    {
        $response = $this->request("/datasets/$idno", 'DELETE');
        return $response;
    }

    public function fech_study_id(): array
    {
        $response = $this->request('/catalog/last_survey_id', 'GET');
        return $response;
    }


    /* =========================================================
     * ===============  CATALOGUES & FACETTES  =================
     * ========================================================= */

    /* Récuperer la liste des catalogues avec tri et limite */
    public function get_catalogs(string $filters = "", string $search = "", string $sortBy = "", string $sortOrder = "", string $limit = "", string $lang = ""): array
    {
        $params = [];

        if ($sortBy) $params['sort_by'] = $sortBy;
        if ($sortOrder) $params['sort_order'] = $sortOrder;
        if ($limit) $params['ps'] = ($limit === '-1') ? 999999 : (int)$limit;
        if ($lang) $params['lang'] = $lang;

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
}

<?php

/**
 * Ce fichier gère le téléchargement de fichiers JSON, leur analyse et l'initialisation
 * de la création du payload.
 * On suppose que nada-payload.php est inclus là où cette fonction est appelée.
 */

/**
 * Fonction de rappel pour gérer l'importation de fichiers JSON afin de créer
 * des payloads Nada.
 * Lit le fichier JSON téléchargé, sépare son contenu par langue et génère
 * un payload pour chaque langue.
 */
function nada_import_study_callback(): void
{
    try {
        if (!isset($_FILES['xmlFile']) || empty($_FILES['xmlFile']['tmp_name'])) {
            throw new Exception("No JSON file provided.");
        }

        $filePath = $_FILES['xmlFile']['tmp_name'];

        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new Exception("The specified file does not exist or is not readable.");
        }

        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            throw new Exception("Could not read the JSON file.");
        }

        save_survey($fileContent, true);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}


function splitJsonByLang(array $jsonData): array
{
    $result = ['fr' => [], 'en' => []];

    $process = function ($data, string $path = '') use (&$process, &$result) {
        // --- Case 1: scalar value ---
        if (!is_array($data)) {
            $result['fr'][$path] = $data;
            $result['en'][$path] = $data;
            return;
        }

        // --- Detect list/array reliably ---
        $keys = array_keys($data);
        $isList = $keys === range(0, count($data) - 1) || $keys === array_map('strval', range(0, count($data) - 1));
        // --- Case 2: array of items with lang ---
        if ($isList && (isset($data[0]['lang']))) {
            foreach ($data as $item) {
                $lang = $item['lang'] ?? null;
                if (!$lang || !isset($result[$lang]))
                    continue;
                // --- Special cases ---
                if (str_contains($path, 'collDate') && isset($item['event'])) {
                    $event = strtolower($item['event']);
                    $eventPath = $path . '/event_' . $event;
                    $result[$lang][$eventPath] = $item['value'];
                    continue;
                }
                if (str_contains($path, 'universe')) {
                    $level = $item['level'] ?? null;
                    $clusion = $item['clusion'] ?? null;
                    $value = $item['value'] ?? null;

                    if ($clusion) {
                        $clusionKey = $clusion === 'I' ? 'clusion_I' : ($clusion === 'E' ? 'clusion_E' : $clusion);
                        if ($level) {
                            // exemple " level=sex, clusion=I → level_sex_clusion_I
                            $key = "level_{$level}_clusion_{$clusion}";
                            $result[$lang][$key][] = $value;
                        } else {
                            // general case inclusion/exclusion population
                            $result[$lang][$clusionKey][] = $value;
                        }
                    }
                    continue;
                }
                if (str_contains($path, 'studyDevelopment')) {
                    if (!isset($result[$lang][$path]) || !is_array($result[$lang][$path])) {
                        $result[$lang][$path] = [];
                    }

                    if (!empty($item)) {
                        $result[$lang][$path][] = [
                            'activity_type' => $item['type'] ?? '',
                            'activity_description' => $item['description'] ?? ''
                        ];
                    }
                    continue;
                }
                if (str_contains($path, 'nation')) {
                    if (!isset($result[$lang][$path]) || !is_array($result[$lang][$path])) {
                        $result[$lang][$path] = [];
                    }
                    if (is_array($item)) {
                        $nation = [
                            'name' => $item['value'] ?? ($item['name'] ?? ''),
                            'abbr' => $item['abbr'] ?? ''
                        ];
                    } else {
                        $nation = [
                            'name' => (string) $item,
                            'abbr' => ''
                        ];
                    }
                    $result[$lang][$path][] = $nation;
                    continue;
                }
                if (str_contains($path, 'topcClas')) {
                    if (!isset($result[$lang][$path]) || !is_array($result[$lang][$path])) {
                        $result[$lang][$path] = [];
                    }
                    $topic = [
                        'topic' => str_or_empty($item['value'] ?? ''),
                        'vocab' => str_or_empty($item['vocab'] ?? ''),
                        'uri' => str_or_empty($item['ExtLink']['uri'] ?? '')
                    ];

                    $result[$lang][$path][] = $topic;
                    continue;
                }
                if (str_contains($path, 'sampProc')) {
                    $values = [];

                    // Normalize $item to an array of entries
                    $entries = [];
                    if (is_array($item) && array_keys($item) === range(0, count($item) - 1)) {
                        // $item already a numeric-indexed array of entries
                        $entries = $item;
                    } else {
                        // single entry (could be string or associative array)
                        $entries = [$item];
                    }

                    foreach ($entries as $entry) {
                        // if entry is a simple string
                        if (is_string($entry) && $entry !== '') {
                            $values[] = $entry;
                            continue;
                        }

                        // prefer concept.value, fall back to value
                        if (isset($entry['concept']['value']) && $entry['concept']['value'] !== '') {
                            $values[] = $entry['concept']['value'];
                        }
                    }

                    // produce the requested string format; if none, produce empty array string
                    $result[$lang][$path] = empty($values)
                        ? "[]"
                        : "['" . implode("','", $values) . "']";

                    continue;
                }

                if (str_contains($path, 'othId')) {
                    // Ensure $item is always an array
                    $othIdRaw = $item ?? [];
                    // If it's a single object or string, wrap in array
                    if (!is_array($othIdRaw) || isset($othIdRaw['value'])) {
                        $othIdRaw = [$othIdRaw];
                    }

                    // Map each entry to an object with 'name'
                    $result[$lang][$path] = array_map(function ($item) {
                        return [
                            'name' => '', //  $item['value'] ??  empty pour solve le problem detail import
                            'type' => $item['type'] ?? '',
                        ];
                    }, $othIdRaw);

                    // update addianal
                    $names = array_filter(array_map(function ($it) {
                        return is_array($it) ? trim($it['value'] ?? '') : (is_string($it) ? trim($it) : '');
                    }, $othIdRaw));

                    if (count($names) > 0) {
                        // marqueur boolean
                        $result[$lang]['additional/collaborations/networkConsortium'] = $lang == 'fr' ? 'Oui' : 'Yes';
                        // stocke les valeurs non vides sous forme de tableau (ou prenez first($names) si vous voulez une string)
                        $result[$lang]['additional/collaborations/collaboration'] = array_values($names);
                    }
                    continue;
                }

                // If item has 'value', use it; otherwise use the whole item
                $value = $item['value'] ?? $item;
                $result[$lang][$path][] = $value;
            }
            return;
        }

        //  single object with lang/value ---
        if (!$isList && isset($data['lang']) && isset($data['value'])) {
            $lang = $data['lang'];
            $item = $data['value'];
            if (isset($result[$lang])) {
                $result[$lang][$path] = $item;
            }

            if (str_contains($path, 'othId')) {
                $result[$lang][$path] =
                    [
                        [
                            'name' =>  '', //  $data['value'] ??  empty pour solve le problem detail import
                            'type' => $data['type'] ?? '',
                        ]
                    ];

                $val = is_string($data['value']) ? trim($data['value']) : '';
                if ($val !== '') {
                    $result[$lang]['additional/collaborations/networkConsortium'] = $lang == 'fr' ? 'Oui' : 'Yes';
                    // on met la valeur dans un tableau pour garder la même structure que ci-dessus
                    $result[$lang]['additional/collaborations/collaboration'] = [$val];
                }

                return;
            }
            return;
        }

        // associative object → recurse ---
        foreach ($data as $key => $value) {
            $currentPath = $path ? "$path/$key" : $key;
            $process($value, $currentPath);
        }
    };

    $process($jsonData);
    return $result;
}

function str_or_empty($v): string
{
    if (is_string($v))
        return $v;
    if (is_numeric($v))
        return (string) $v;
    return '';
}

function join_non_empty(array $parts, string $sep = '; '): string
{
    $parts = array_values(array_filter(array_map('strval', $parts), fn($x) => trim((string) $x) !== ''));
    return implode($sep, $parts);
}

function as_list($v): array
{
    if ($v === null)
        return [];
    if (is_array($v) && array_keys($v) !== range(0, count($v) - 1)) {
        return [$v]; // transforme un seul objet associatif en tableau
    }
    return is_array($v) ? $v : [];
}


/** Expose les fichiers du dossier PEF via l'API REST Wordpress */
add_action('rest_api_init', function () {
    register_rest_route('nada/v1', '/pef-files(?:/(?P<filename>[^/]+))?', [
        'methods' => 'GET',
        'callback' => 'nada_get_pef_file_content',
        'permission_callback' => '__return_true', // accès public
    ]);
});


/**
 * Callback pour lire tous les fichiers et continuer malgré les erreurs
 */
function nada_get_pef_file_content(WP_REST_Request $request)
{
    // Vider les logs au début
    nada_clear_logs();

    $nada_token = authentificate($request);

    $dir = WP_PLUGIN_DIR . '/nada-id/public/PEF';
    $filename = $request->get_param('filename');
    $results = [];
    $errors = [];

    if (!is_dir($dir)) {
        $errorMsg = 'Le dossier PEF est introuvable';
        nada_log($errorMsg, 'error');
        return new WP_Error('no_dir', $errorMsg, ['status' => 404]);
    }

    if (empty($filename)) {
        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;

            if (!is_file($path)) continue;

            try {
                $fileContent = nada_read_file($path, $file);

                if (!$fileContent) {
                    $msg = "Le fichier $file est vide ou illisible";
                    $errors[] = $msg;
                    nada_log($msg, 'error');
                    continue;
                }

                $res = save_survey($fileContent, false, $nada_token, $file);
                $results[] = $res;
                nada_log("Fichier $file traité avec succès : " . json_encode($res));
            } catch (\Exception $e) {
                $msg = "Erreur avec le fichier $file : " . $e->getMessage();
                $errors[] = $msg;
                nada_log($msg, 'error');
                continue; // continuer malgré l'erreur
            }
        }
    } else {
        // Lire un seul fichier
        $filename = sanitize_file_name($filename);
        $path = $dir . '/' . $filename;
        if (!file_exists($path)) {
            $msg = "Fichier introuvable: $filename";
            nada_log($msg, 'error');
            return new WP_Error('no_file', $msg, ['status' => 404]);
        }

        try {
            $fileContent = nada_read_file($path, $filename);
            if ($fileContent) {
                $res = save_survey($fileContent, false, $nada_token, $filename);
                $results[] = $res;
                nada_log("Fichier $filename traité avec succès : " . json_encode($res));
            }
        } catch (\Exception $e) {
            $msg = "Erreur avec le fichier $filename : " . $e->getMessage();
            $errors[] = $msg;
            nada_log($msg, 'error');
        }
    }

    wp_send_json([
        'success' => true,
        'message' => 'Parcours terminé, certaines erreurs peuvent être présentes',
        'response' => $results,
        'errors' => $errors
    ]);
}


/**
 * Fonction utilitaire pour lire un fichier (texte ou base64)
 */
function nada_read_file($path, $filename)
{
    $ext = pathinfo($path, PATHINFO_EXTENSION);

    $text_extensions = ['json', 'xml'];
    $content = null;
    if (in_array(strtolower($ext), $text_extensions)) {
        $content = file_get_contents($path);
    }
    return $content;
}

function save_survey($fileContent, $isTeleversment = true, ?string $nada_token = null, ?string $filename = null)
{
    try {
        $jsonData = json_decode($fileContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON file: " . json_last_error_msg());
        }
        $langData = splitJsonByLang($jsonData);

        // --- Build final payload + send to NADA API ---
        $responses = [];
        $nada_api = get_nada_api_instance();
        $newId = getSurveyNewId();
        $resultMultiplePEF = [];
        foreach (['fr', 'en'] as $lang) {
            if (!empty($langData[$lang])) {
                try {
                    $idno = "FReSH_PEF-{$newId}-{$lang}";
                    $study = prepare_nada_import_payload($langData[$lang], $lang, $idno);

                    $response = $nada_api->create_study(json_encode($study), $nada_token);

                    if (empty($response['success']) || $response['success'] != 1 || $response['data']['status'] != 'success') {
                        throw new Exception($response['data']['message'] ?? 'Erreur inconnue lors de l\'API NADA');
                    }

                    $responses[$lang] = $response;
                    $resultMultiplePEF[] = $response['data']['dataset']['idno'];
                } catch (\Throwable $e) {
                    // Log l'erreur pour cette langue mais continue le traitement
                    nada_log("Erreur pour le fichier $filename : " . $e->getMessage(), 'error');
                    continue;
                }
            }
        }
        // --- Ajout dans la base WordPress ---
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . "nada_list_studies";
            $current_user_id = get_current_user_id();

            $idno = $responses['fr']['data']['dataset']['idno']; // recuperation idno
            $nada_study_id_fr = $responses['fr']['data']['dataset']['id']; // recuperation id study fr
            $nada_study_id_en = $responses['en']['data']['dataset']['id']; // recuperation id study en
            // Sécuriser get_base_idno
            if (!$idno) {
                throw new Exception("Impossible de récupérer l’idno (fichier : $filename)");
            }

            $base_idno = get_base_idno($idno);

            if (!$base_idno) {
                throw new Exception("get_base_idno() a retourné une valeur vide (fichier : $filename)");
            }

            try {
                if ($idno) {
                    $base_idno = get_base_idno($idno);
                } else {
                    throw new Exception("Impossible de récupérer idno pour le fichier $filename");
                }
            } catch (\Throwable $e) {
                nada_log("Erreur get_base_idno pour le fichier $filename : " . $e->getMessage(), 'error');
            }

            $insert_data = [
                'nada_study_idno' => $base_idno,
                'nada_study_id_fr' => $nada_study_id_fr,
                'nada_study_id_en' => $nada_study_id_en,
                'status' => 'imported',
                'created_by' => $current_user_id,
                'created_at' => current_time('mysql'),
            ];

            $result = $wpdb->insert($table_name, $insert_data);

            if ($result === false) {
                throw new Exception('Erreur lors de la création dans WP');
            }
        } catch (\Throwable $e) {
            nada_log("Erreur base de données pour le fichier $filename : " . $e->getMessage(), 'error');
            // Pas d'insertion => on continue simplement
        }


        if (!$isTeleversment) {
            return $resultMultiplePEF;
        }
        // Tout est OK
        wp_send_json([
            'success' => true,
            'message' => 'Études sauvegardées avec succès !',
            'response' => $responses
        ]);
    } catch (\Exception $e) {
        nada_log('Erreur save_survey : ' . $e->getMessage(), 'error');
        return null; // continuer le parcours malgré l'erreur
    }
}


function authentificate($request)
{
    $username = null;
    $password = null;
    $body = $request->get_json_params();
    if (empty($body)) {
        // fallback to form-data / params
        $body = $request->get_params();
    }
    if (!empty($body['username'])) {
        $username = $body['username'];
    }
    if (!empty($body['password'])) {
        $password = $body['password'];
    }

    if (empty($username) || empty($password)) {
        wp_send_json([
            'success' => false,
            'status' => 400,
            'message' => 'Username et password requis !',
        ]);
    }

    $user = wp_authenticate(sanitize_user($username), $password);
    if (is_wp_error($user)) {
        wp_send_json([
            'success' => false,
            'status' => 401,
            'message' => 'Authentification échouée !',
        ]);
    }
    $nada_token = get_user_meta($user->ID, 'nada_token', true);
    return $nada_token;
}


// Fonction utilitaire pour logger
function nada_log($message, $type = 'info')
{
    $log_dir = WP_PLUGIN_DIR . '/nada-id/logs-PEFF';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $filename = $type === 'error' ? 'error.log' : 'success.log';
    $filepath = $log_dir . '/' . $filename;

    $time = date('Y-m-d H:i:s');
    file_put_contents($filepath, "[$time] $message\n", FILE_APPEND);
}

// Fonction pour vider les logs avant chaque lancement
function nada_clear_logs()
{
    $log_dir = WP_PLUGIN_DIR . '/nada-id/logs-PEFF';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $files = ['success.log', 'error.log'];
    foreach ($files as $file) {
        $filepath = $log_dir . '/' . $file;
        if (file_exists($filepath)) {
            file_put_contents($filepath, ""); // vide le fichier
        }
    }
}

<?php

// ================================= Institutions Code  =========

/**
 * Extrait les institutions depuis les données d'une étude
 */
function extractInstitutionsFromStudy(array $study): array
{
    $institutions = [];
    $institutions = array_merge(
        $institutions,
        extractEntitiesToInstitutions($study, 'producers')
    );
    $institutions = array_merge(
        $institutions,
        extractEntitiesToInstitutions($study, 'fundingAgencies')
    );

    // check is institution existe déjà dans la base WP avant de l'ajouter pour éviter les doublons
    foreach ($institutions as $index => $institution) {
        if (checkInstitutionExists($institution['label_fr'])) {
            unset($institutions[$index]);
        }
    }

    return $institutions;
}

function extractEntitiesToInstitutions(array $study, string $entityKey): array
{
    $institutions = [];
    $current_user_id = get_current_user_id();

    $entities = $study[$entityKey] ?? [];

    if (empty($entities) || !is_array($entities)) {
        return $institutions;
    }

    foreach ($entities as $entity) {
        // Vérifier que le nom existe et n'est pas vide
        if (empty($entity['name']) || trim($entity['name']) === '') {
            continue;
        }
        $institutions[] = [
            'label_fr' => trim($entity['name']),
            'label_en' => trim($entity['name']),
            'is_active' => false,
            'state' => 'pending',
            'created_by' => $current_user_id,
            'updated_by' => $current_user_id,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
    }

    return $institutions;
}

/** Récupérer et afficher le tableau des institutions via AJAX */
function nadaAjaxFetchTable()
{
    $search_key = isset($_POST['search_key']) ? sanitize_key($_POST['search_key']) : 'search_valid';
    $state = $search_key === 'search_valid' ? 'approved' : 'pending';
    $is_active = $state === 'approved' ? 1 : 0;
    $search_term = isset($_POST['term']) ? sanitize_text_field(wp_unslash($_POST['term'])) : '';
    $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;

    // Stocker les valeurs dans $_REQUEST pour que WP_List_Table puisse les récupérer
    $_REQUEST[$search_key] = $search_term;
    $_REQUEST['paged'] = $paged;

    // Créer et afficher le tableau
    $table = new InstitutionsTable($is_active, $search_key, $state);
    $table->prepare_items();
    $table->display();

    wp_die();
}

/** Ajout/modification institution */
function nadaSaveInstitution()
{
    global $wpdb;
    $lang = pll_current_language() ?? 'fr';
    $table_items = $wpdb->prefix . 'nada_institutions';
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    try {
        if ($id > 0) {
            // UPDATE
            $data = [
                'desc_fr'    => sanitize_textarea_field(wp_unslash($_POST['desc_fr'])),
                'desc_en'    => sanitize_textarea_field(wp_unslash($_POST['desc_en'])),
                'uri'    => sanitize_textarea_field(wp_unslash($_POST['uri'])),
                'siren'    => sanitize_textarea_field(wp_unslash($_POST['siren'])),
                'updated_at' => current_time('mysql'),
                'updated_by' => get_current_user_id()
            ];

            $where = ['id' => $id];
            $result = $wpdb->update($table_items, $data, $where);

            if ($result === false) {
                throw new Exception('Erreur lors de la mise à jour de la base de données.');
            }
            $message = $lang === 'fr' ? 'Institution mise à jour avec succès.' : 'Institution updated successfully.';
        } else {
            // INSERT
            if (empty($_POST['label_fr']) || empty($_POST['label_en'])) {
                wp_send_json_error(['message' => $lang === 'fr' ? 'Les Labels FR et EN sont obligatoires.' : 'Labels FR and EN are required.']);
            }
            if (checkInstitutionExists($_POST['label_fr'])) {
                wp_send_json_error(['message' => $lang === 'fr' ? 'Une institution avec ce nom existe déjà.' : 'An institution with this name already exists.']);
            }

            $data = [
                'identifier' => sanitize_text_field(wp_unslash($_POST['identifier'])),
                'label_fr'   => sanitize_text_field(wp_unslash($_POST['label_fr'])),
                'label_en'   => sanitize_text_field(wp_unslash($_POST['label_en'])),
                'desc_fr'    => sanitize_textarea_field(wp_unslash($_POST['desc_fr'])),
                'desc_en'    => sanitize_textarea_field(wp_unslash($_POST['desc_en'])),
                'uri'        => sanitize_text_field(wp_unslash($_POST['uri'])),
                'siren'      => sanitize_text_field(wp_unslash($_POST['siren'])),
                'status'     => sanitize_text_field(wp_unslash($_POST['status'])),
                'is_active' => false,
                'state'      => 'pending',
                'created_by' => get_current_user_id(),
                'updated_by' => get_current_user_id(),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ];

            $result = insertInstitution($data);

            if ($result === false) {
                throw new Exception('Erreur lors de l\'insertion dans la base de données.');
            }
            $item_id = $wpdb->insert_id;
            $message = $lang === 'fr' ? 'Institution créée avec succès.' : 'Institution created successfully.';
        }

        // Envoyer la réponse de succès
        wp_send_json_success([
            'message' => $message,
            'item_id' => $item_id
        ]);
    } catch (Exception $e) {
        nada_id_log("Erreur function nadaSaveInstitution: " . $e->getMessage());
        wp_send_json_error([
            'success'  => false,
            'message'  => $lang === 'fr' ?
                'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                'A technical error occurred while generating the view. Please try again later or contact support.',
            'response' => "KO"
        ]);
    }
}
/**
 * Insère une nouvelle institution
 */
function insertInstitution(array $institution): bool
{
    global $wpdb;
    $table = $wpdb->prefix . 'nada_institutions';

    $result = $wpdb->insert($table, $institution);

    return $result !== false;
}

/** Mise à jour état institution */
function nadaUpdateStateInstitution()
{
    global $wpdb;
    $lang = pll_current_language() ?? 'fr';
    $table_items = $wpdb->prefix . 'nada_institutions';
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $new_state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';

    try {
        if ($id <= 0 || empty($new_state)) {
            throw new Exception('ID ou état invalide');
        }

        $data = [
            'state' => $new_state,
            'is_active' => $new_state === 'approved' ? 1 : 0,
            'updated_by' => get_current_user_id(),
            'updated_at' => current_time('mysql')
        ];

        $where = ['id' => $id];
        $result = $wpdb->update($table_items, $data, $where);

        if ($result === false) {
            throw new Exception('Erreur lors de la mise à jour de la base de données.');
        }

        wp_send_json_success([
            'message' => $lang === 'fr' ? 'Institution mise à jour avec succès.' : 'Institution updated successfully.'
        ]);
    } catch (Exception $e) {
        nada_id_log("Erreur function nadaUpdateStateInstitution: " . $e->getMessage());
        wp_send_json_error([
            'success'  => false,
            'message'  => $lang === 'fr' ?
                'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                'A technical error occurred while generating the view. Please try again later or contact support.',
            'response' => "KO"
        ]);
    }
}


/** Recupere la listes des institutions */
function getInstitutionsList(): array
{
    global $wpdb;

    $table = $wpdb->prefix . 'nada_institutions';

    $rows = $wpdb->get_results("
        SELECT id, label_fr, label_en, uri, siren, status, identifier
        FROM {$table}
        WHERE is_active = 1 AND state ='approved'
        ORDER BY id ASC
    ", ARRAY_A);

    return $rows ?: [];
}

/** Vérifie si une institution existe par son nom */
function checkInstitutionExists(string $label): bool
{
    global $wpdb;

    $table = $wpdb->prefix . 'nada_institutions';

    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT id FROM {$table} WHERE label_fr = %s LIMIT 1",
            $label
        )
    );

    return !is_null($row);
}

// ===================================== Institutions Code  =========

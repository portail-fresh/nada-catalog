<?php

use JetBrains\PhpStorm\NoReturn;

/** Récupérer et afficher le tableau des référentiels via AJAX */
function nada_ajax_fetch_table_ref()
{
    $_REQUEST['search_term'] = isset($_POST['term']) ? sanitize_text_field(wp_unslash($_POST['term'])) : '';
    $_REQUEST['paged'] = isset($_POST['paged']) ? absint($_POST['paged']) : 1;

    $table = new RepositoryTable();
    $table->prepare_items();
    $table->display();

    wp_die();
}

/** Récupérer et afficher le tableau des items d'un référentiel via AJAX */
function nada_ajax_fetch_table_ref_items()
{
    $_REQUEST['ref_id'] = isset($_POST['ref_id']) ? absint($_POST['ref_id']) : 0;
    $_REQUEST['search_term']    = isset($_POST['term']) ? sanitize_text_field(wp_unslash($_POST['term'])) : '';
    $_REQUEST['paged']          = isset($_POST['paged']) ? absint($_POST['paged']) : 1;
    $_REQUEST['per_page']       = isset($_POST['per_page']) ? sanitize_text_field($_POST['per_page']) : '10';

    $table = new RepositoryItemsTable();
    $table->prepare_items();
    $table->display();

    wp_die();
}

/** Ajout/modification item référentiel */
function nada_save_referentiel_item()
{

    global $wpdb;
    $table_items = $wpdb->prefix . 'nada_referentiel_items';
    $lang = pll_current_language() ?? 'fr';
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $referentiel_id = isset($_POST['ref_id']) ? intval($_POST['ref_id']) : 0;

    // Valider les données
    if ($referentiel_id <= 0) {
        wp_send_json_error(['message' => $lang == 'fr' ? 'ID de référentiel invalide.' : 'Invalid repository ID.']);
    }

    try {
        if ($item_id > 0) {
            // UPDATE
            $data = [
                'desc_fr'    => sanitize_textarea_field(wp_unslash($_POST['desc_fr'])),
                'desc_en'    => sanitize_textarea_field(wp_unslash($_POST['desc_en'])),
                'uri'    => sanitize_textarea_field(wp_unslash($_POST['uri'])),
                'siren'    => sanitize_textarea_field(wp_unslash($_POST['siren'])),
                'updated_at' => current_time('mysql')
            ];

            $where = ['id' => $item_id];
            $result = $wpdb->update($table_items, $data, $where);

            if ($result === false) {
                throw new Exception('Erreur lors de la mise à jour de la base de données.');
            }
            $message = $lang == 'fr' ? 'Référentiel mis à jour avec succès.' : 'Repository successfully updated.';
        } else {
            // INSERT
            if (empty($_POST['label_fr']) || empty($_POST['label_en'])) {
                wp_send_json_error(['message' => $lang == 'fr' ? 'Les Labels FR et EN sont obligatoires.' : 'The FR and EN labels are mandatory.']);
            }

            $data = [
                'referentiel_id' => $referentiel_id,
                'label_fr'   => sanitize_text_field(wp_unslash($_POST['label_fr'])),
                'label_en'   => sanitize_text_field(wp_unslash($_POST['label_en'])),
                'desc_fr'    => sanitize_textarea_field(wp_unslash($_POST['desc_fr'])),
                'desc_en'    => sanitize_textarea_field(wp_unslash($_POST['desc_en'])),
                'uri'        => sanitize_text_field(wp_unslash($_POST['uri'])),
                'uri_esv'    => sanitize_text_field(wp_unslash($_POST['uri_esv'])),
                'uri_mesh'   => sanitize_text_field(wp_unslash($_POST['uri_mesh'])),
                'identifier' => sanitize_text_field(wp_unslash($_POST['identifier'])),
                'siren'      => sanitize_text_field(wp_unslash($_POST['siren'])),
                'status'     => sanitize_text_field(wp_unslash($_POST['status'])),
                'is_enabled' => true,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ];

            $result = $wpdb->insert($table_items, $data);

            if ($result === false) {
                throw new Exception('Erreur lors de l\'insertion dans la base de données.');
            }
            $item_id = $wpdb->insert_id;
            $message =  $lang == 'fr' ?  'Référentiel créé avec succès.' : 'Repository successfully created.';
        }

        // Envoyer la réponse de succès
        wp_send_json_success([
            'message' => $message,
            'item_id' => $item_id
        ]);
    } catch (Exception $e) {
        nada_id_log("Erreur function nada_save_referentiel_item: " . $e->getMessage());
        wp_send_json_error([
            'success'  => false,
            'message'  => $lang === 'fr' ?
                'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                'A technical error occurred while generating the view. Please try again later or contact support.',
            'response' => "KO"
        ]);
    }
}

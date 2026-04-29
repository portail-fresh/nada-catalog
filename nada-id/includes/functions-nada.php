<?php

/**
 * Fichier functions-nada.php
 *
 * Contient des fonctions utilitaires génériques (globaux) du plugin.
 * Ces fonctions ne devraient pas dépendre directement de WordPress
 * (mais peuvent l’utiliser si nécessaire).
 */

// Sécurité
if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'nada-payload-import.php';
require_once plugin_dir_path(__FILE__) . 'import-file.php';
require_once plugin_dir_path(__FILE__) . 'nada-statistics.php';
require_once plugin_dir_path(__FILE__) . 'class-nada-institutions-table.php';
require_once plugin_dir_path(__FILE__) . 'class-nada-institution.php';

require_once plugin_dir_path(__FILE__) . 'class-nada-repository-table.php';
require_once plugin_dir_path(__FILE__) . 'class-nada-repository-items-table.php';
require_once plugin_dir_path(__FILE__) . 'class-nada-repository.php';


add_action('nada_user_registered', 'nada_create_user_after_ur_register', 10, 1);
add_action('profile_update', 'nada_update_user_after_profile_edit', 10, 1);
add_action('wp_ajax_nada_import_study', 'nada_import_study_callback');
add_action('wp_ajax_nada_delete_study', 'nada_delete_study_callback');
add_action('wp_ajax_nada_load_catalogs', 'nada_load_catalogs_callback'); // wp_ajax_nopriv_{action} → réservé aux utilisateurs non connectés
add_action('wp_ajax_nopriv_nada_load_catalogs', 'nada_load_catalogs_callback');  // wp_ajax_{action} → réservé aux utilisateurs connectés
add_action('wp_ajax_nada_advanced_search', 'nada_advanced_search_callback');
add_action('wp_ajax_nopriv_nada_advanced_search', 'nada_advanced_search_callback');
add_action('wp_ajax_publish_unpublish_study', 'publish_unpublish_study_callback');
add_action('wp_ajax_request_changes_study', 'request_changes_study_callback');
add_action('wp_ajax_reject_study', 'reject_study_callback');
add_action('wp_ajax_nada_load_facets', 'nada_load_facets');
add_action('wp_ajax_nopriv_nada_load_facets', 'nada_load_facets');
add_action('wp_ajax_get_icd_entities', 'handle_get_icd_entities');
add_action('wp_ajax_get_search_list_cim', 'handle_get_search_list_cim');
add_action('set_current_user', 'after_login_force');
add_action('check_existing_user', 'check_existing_user_callback');
add_action('wp_ajax_nada_update_vd_description_field', 'nada_update_vd_description_field');
add_action('wp_ajax_cancel_study', 'cancel_study_callback');

add_action('wp_ajax_check_orcid', 'check_orcid_callback');
add_action('wp_ajax_nopriv_check_orcid', 'check_orcid_callback');

// Institution actions
add_action('wp_ajax_nada_save_institution', 'nadaSaveInstitution');
add_action('wp_ajax_nada_update_state_institution', 'nadaUpdateStateInstitution');
add_action('wp_ajax_nada_fetch_table', 'nadaAjaxFetchTable');
// referentiel actions
add_action('wp_ajax_nada_fetch_table_ref', 'nada_ajax_fetch_table_ref');
add_action('wp_ajax_nada_fetch_table_ref_items', 'nada_ajax_fetch_table_ref_items');
add_action('wp_ajax_nopriv_nada_fetch_table_ref_items', 'nada_ajax_fetch_table_ref_items');
add_action('wp_ajax_nada_save_referentiel_item', 'nada_save_referentiel_item');

/* =================================== Hooks ================================================/

/** Crée un utilisateur NADA lors de l'inscription WP */
function nada_create_user_after_ur_register($user_id): void
{
    // Récupérer les informations WP
    $user_info = get_userdata($user_id);
    $email = $user_info->user_email;
    $username = $user_info->user_login;

    $first_name = get_user_meta($user_id, 'first_name', true) ?: $username;
    $last_name = get_user_meta($user_id, 'last_name', true) ?: $username;

    // Récupérer l'instance unique de Nada_API
    $nada_api = get_nada_api_instance();


    // Limiter à 18 caractères
    $username = substr($username, 0, 18);
    $first_name = substr($first_name, 0, 18);
    $last_name = substr($last_name, 0, 18);

    // Préparer les données utilisateur
    $data = [
        'email' => $email,
        'username' => $username,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'password' => get_default_password_nada(),
        'password_confirm' => get_default_password_nada(),
        'company' => "",
        'phone' => null,
        'country' => null,
        'active' => true,
        'role_id' => 2
    ];

    // Créer l'utilisateur NADA
    $response = $nada_api->createUser($data);
    // Stocker le token NADA en meta utilisateur WP
    if ($response['success'] && !empty($response['data']['user']['api_keys'][0])) {
        update_user_meta($user_id, 'nada_token', sanitize_text_field($response['data']['user']['api_keys'][0]));
        update_user_meta($user_id, 'nada_user_id', $response['data']['user']['user_id']);

        assign_nada_studies_to_pi($user_id, $email);
        assign_nada_studies_to_contributor($user_id, $email);
        return;
    }
    nada_id_log('Erreur création utilisateur NADA pour user_id ' . $user_id . ': ' . json_encode($response, true));
}

/**
 * Met à jour l'utilisateur NADA et ses études associées lors de l'édition du profil WP.
 */
function nada_update_user_after_profile_edit($user_id): void
{
    $user_info = get_userdata($user_id);
    $nada_token = get_user_meta($user_id, 'nada_token', true);

    if (empty($nada_token)) {
        error_log('No NADA token for user ' . $user_id);
        return;
    }

    $nada_api = get_nada_api_instance();

    $data = [
        'email' => $user_info->user_email,
        'first_name' => substr(get_user_meta($user_id, 'first_name', true) ?: '', 0, 18),
        'last_name' => substr(get_user_meta($user_id, 'last_name', true) ?: '', 0, 18)
    ];
    $response = $nada_api->update_user($data);
    if ($response['success'] && !empty($response['data']['status']) && $response['data']['status'] == 'success') {
        return;
    }
    nada_id_log('Erreur mise à jour utilisateur NADA pour user_id ' . $user_id . ': ' . print_r($response, true));
}

/** Après connexion, forcer la récupération du NADA USER ID */
function after_login_force()
{

    if (!is_user_logged_in()) {
        return;
    }

    $user = wp_get_current_user();

    // Éviter les updates en boucle
    if (get_user_meta($user->ID, 'nada_user_id', true)) {
        return;
    }

    // login to nada
    $nada_api = get_nada_api_instance();
    $response = $nada_api->login();

    // Stocker le ID USER NADA en meta utilisateur WP
    if ($response['success'] && !empty($response['data']['user']['user_id'])) {
        update_user_meta($user->ID, 'nada_user_id', sanitize_text_field($response['data']['user']['user_id']));
        return;
    }
}

/* ================================ Ajax Callback ================================================/

/** Suppression une étude fr/en Coté Nada */
function nada_delete_study_callback(): void
{
    $lang = pll_current_language() ?? 'fr';
    try {
        $study_idno = $_POST['study_idno'];
        if (!$study_idno) {
            throw new Exception("ID d'étude invalide");
        }
        //TODo supprimer les etude versionnés
        // Extraire la partie commune avant -FR ou -EN
        $base_idno = get_base_idno($study_idno);
        $nada_api = get_nada_api_instance();
        $response_fr = $nada_api->delete_study($base_idno . '-fr');

        if (!$response_fr['success'] || $response_fr['data']['status'] != 'success') {
            throw new Exception('Quelque chose qui va pas bien !');
        }

        $response_en = $nada_api->delete_study($base_idno . '-en');

        if (!$response_en['success'] || $response_en['data']['status'] != 'success') {
            throw new Exception("L'étude française a été supprimée, mais la suppression de l'étude anglaise a échoué");
        }

        // Suppression Coté wp
        $is_deleted = delete_nada_study_from_wp($base_idno);

        if (!$is_deleted) {
            throw new Exception('La suppression a échoué côté WP!');
        }

        // Si tout est OK
        wp_send_json_success([
            'success' => true,
            'message' => $lang === 'fr' ? 'Etude supprimé avec succès !' : 'Study deleted successfully!',
            'response' => "OK"
        ]);
    } catch (Exception $e) {
        nada_id_log("Erreur nada_delete_study_callback : " . $e->getMessage());
        wp_send_json_error([
            'success' => false,
            'message' => $lang === 'fr' ? "Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support"
                : "A technical error occurred while generating the view. Please try again later or contact support",
            'response' => 'KO'
        ]);
    }
}

/** Publier/Dépublier une étude */
function publish_unpublish_study_callback(): void
{
    $lang = pll_current_language() ?? 'fr';

    try {

        $study_idno = $_POST['study_idno'] ?? null;
        if (!$study_idno) {
            throw new Exception('ID invalide');
        }

        $base_idno = get_base_idno($study_idno, false);
        $base_idno_prefix = get_base_idno($study_idno);
        $published = $_POST['published'] ?? null;

        if ($published == 1) {
            publish_study($base_idno, $base_idno_prefix);
        } else {
            unpublish_study($base_idno);
        }

        // Send emails
        $study = get_details_study_from_nada($base_idno);
        send_publish_unpublish_email_study($study, $published);

        wp_send_json([
            'success' => true,
            'message' => $lang === 'fr'
                ? 'Étude modifiée avec succès !'
                : 'Study modified successfully !',
            'response' => 'OK'
        ]);
    } catch (Exception $e) {

        error_log("Erreur publish_unpublish_study_callback : " . $e->getMessage());
        wp_send_json([
            'success' => false,
            'message' => $lang === 'fr' ?
                'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                'A technical error occurred while generating the view. Please try again later or contact support.',
            'response' => 'KO'
        ]);
    }
}

function publish_study($base_idno, $prefix_base_idno): void
{

    // 1) Vérifier s’il existe une version déjà publiée
    $wp_study = get_details_study_from_wp($base_idno, 'published');

    if ($wp_study) {
        $old_idno = $wp_study['nada_study_idno'];

        // Dépublier (FR + EN)
        $data_nada = [
            "published" => 0,
            "link_indicator" => "archived"
        ];
        nada_update($old_idno, $data_nada);

        // Générer nouvel IDNO versionné
        $new_idno = generate_next_idno($base_idno);
        // Renommer FR+EN dans NADA
        nada_rename_study($old_idno, $new_idno);
        // Archiver dans WP
        update_wp_study($old_idno, [
            'nada_study_idno' => $new_idno,
            'status' => 'archived'
        ]);
    }

    // Renommer prefix → base
    nada_rename_study($prefix_base_idno, $base_idno);

    // 2) Publier la nouvelle étude
    $data_nada = [
        "published" => 1,
        "link_indicator" => "published"
    ];
    nada_update($base_idno, $data_nada);

    // Update WP statut
    update_wp_study($prefix_base_idno, [
        'nada_study_idno' => $base_idno,
        'status' => 'published'
    ]);
}

function unpublish_study($base_idno): void
{
    // Dépublier dans NADA
    $data_nada = [
        "published" => 0,
        "link_indicator" => "unpublished"
    ];
    nada_update($base_idno, $data_nada);

    // Update WP
    update_wp_study($base_idno, [
        'status' => 'unpublished'
    ]);
}

/* Notifier Contributeur et PI de la publication/depublication de l'etude */
function send_publish_unpublish_email_study($studies, $published): void
{

    if ($studies) {

        $studyFr = $studies['fr']['dataset'];
        $studyEn = $studies['en']['dataset'];
        $contributor_id = $studyFr['created_by'] ? fetch_wp_user_id($studyFr['created_by']) : null;
        $pi_email = $studyFr['link_report'] ?? null;

        $mails_data = [];
        $site_url = get_site_url();

        // Récuperer les données du  contributeur
        $contributor = get_user_by('id', $contributor_id);
        $contributor_email = $contributor->user_email;

        $mails_data[] = [
            'email' => $contributor_email,
            'recipient_name' => trim($contributor->first_name . ' ' . $contributor->last_name)
        ];

        // Récuperer les données du PI en cas il est different de contributeur
        if ($pi_email && $contributor_email != $pi_email) {
            $pi = get_user_by('email', $pi_email);

            if ($pi) {
                $recipient_name = trim($pi->first_name . ' ' . $pi->last_name);
            } else {
                $recipient_name =  $studyFr['link_technical'];
            }

            $mails_data[] = [
                'email' => $pi_email,
                'recipient_name' => $recipient_name
            ];
        }

        $template = $published == 1
            ? 'email-publish.php'
            : 'email-unpublish.php';

        $subject = $published == 1
            ? "[FReSH] Votre étude est publiée ! / Your study has been published!"
            : "[FReSH] Dépublication de votre étude sur le catalogue / Removal of your study from the catalogue";


        // Envoi email à le contributeur et PI
        foreach ($mails_data as $data) {
            $mail_data = [
                'recipient_name' => $data['recipient_name'],
                'study_title_fr' => $studyFr['title'],
                'study_title_en' => $studyEn['title'],
                'logo_url' => "$site_url/wp-content/uploads/2025/10/Logo_FReSH-1.png",
                'project_link' => $site_url,
                'contact_email' => get_nada_default_admin_fresh()
            ];
            nada_send_email(
                $data['email'],
                $subject,
                NADA_ID_PLUGIN_DIR . 'public/templates/emails/' . $template,
                $mail_data
            );
        }
    }
}

function nada_update($baseIdno, $data)
{
    $api = get_nada_api_instance();

    foreach (['fr', 'en'] as $lang) {

        $response = $api->update_study_field("{$baseIdno}-{$lang}", $data);

        if (
            !$response['success'] ||
            ($response['data']['status'] ?? '') !== 'success'
        ) {
            throw new Exception("Erreur update survey {$lang}");
        }
    }
}

// Renommer idnos fr+en
function nada_rename_study($old, $new)
{
    $api = get_nada_api_instance();

    foreach (['fr', 'en'] as $lang) {

        if ($old == $new) {
            continue;
        }
        $payload = [
            "old_idno" => "{$old}-{$lang}",
            "new_idno" => "{$new}-{$lang}"
        ];

        $response = $api->update_idno_study($payload);

        if (
            !$response['success'] ||
            ($response['data']['status'] ?? '') !== 'success'
        ) {
            throw new Exception("Erreur renommage IDNO {$lang}");
        }
    }
}

function update_wp_study($idno, array $data)
{
    $response = update_nada_study_wp($idno, $data);

    if (!$response) {
        throw new Exception("Erreur update WP ($idno)");
    }
}

/** Modifications demandées une étude */
function request_changes_study_callback(): void
{
    $lang = pll_current_language() ?? 'fr';
    try {
        $study_idno = $_POST['study_idno'];
        if (!$study_idno) {
            throw new Exception('ID invalide');
        }

        // Extraire la partie commune avant -FR ou -EN
        $base_idno = get_base_idno($study_idno);

        //Modification coté nada
        $data_nada = [
            "link_indicator" => "changes_requested"
        ];
        nada_update($base_idno, $data_nada);


        // Préparer les données à mettre à jour
        $data = [
            'status' => 'changes_requested'
        ];

        // Modification coté wp
        $response = update_nada_study_wp($base_idno, $data);

        if (!$response) {
            throw new Exception("Probléme lors de la demande des modifcations d'une etude");
        }

        // Notifier
        // notification mail générique indiquant que la fiche nécessite des modifications pour pouvoir être publiée au contributeur et PI (pour plus d’info, contacter le service FReSH).
        $study = get_details_study_from_wp($base_idno);
        if ($study) {
            $current_user = wp_get_current_user();
            $pi_email = $study['pi_email'] ?? null;
            $contributor_id = $study['created_by'] ?? null;
            $site_url = get_site_url();
            $log_url = "$site_url/wp-content/uploads/2025/10/Logo_FReSH-1.png";
            $mails = [];
            // Ajouter le PI si ce n’est pas l’utilisateur actuel
            if ($pi_email && $pi_email != $current_user->user_email) {

                // Verifier si le pi est un user inscri dans wp
                $pi_user = get_user_by('email', $pi_email);
                if ($pi_user) {
                    $mails[] = $pi_email;
                } else {
                    $register_url_fr = site_url('/inscription/');
                    $register_url_en = site_url('/en/register/');
                    nada_send_email(
                        $pi_email,
                        "[FReSH] Demande de modification concernant votre étude sur le catalogue / Request for changes to your study in the catalogue",
                        NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-request-modifications-unregistered-pi.php',
                        [
                            'study_title_fr' => $study['nada_study_title_fr'],
                            'study_title_en' => $study['nada_study_title_en'],
                            'logo_url' => $log_url,
                            'project_link' => $site_url,
                            'register_url' => $register_url_fr,
                            'register_url_en' => $register_url_en,
                            'email_contact' => get_nada_default_admin_fresh()
                        ]
                    );
                }
            }

            // Ajouter le contributeur si différent de l’utilisateur actuel
            if ($contributor_id && $contributor_id != $current_user->ID) {
                $contributor = get_user_by('id', $contributor_id);
                $mails[] = $contributor->user_email;
            }

            // Supprimer doublons éventuels
            $mails = array_unique(array_filter($mails));
            // Envoi mail de notification
            // Envoi d'un mail à chaque destinataire
            if (!empty($mails)) {
                $study_title = sanitize_text_field($_POST['study_title']);
                foreach ($mails as $email) {
                    // Trouver l’utilisateur à partir de son e-mail
                    $user = get_user_by('email', $email);

                    // Récupérer prénom et nom s’ils existent dans usermeta
                    $first_name = $user ? get_user_meta($user->ID, 'first_name', true) : '';
                    $last_name = $user ? get_user_meta($user->ID, 'last_name', true) : '';

                    nada_send_email(
                        $email,
                        "[FReSH] Demande de modification concernant votre étude sur le catalogue / Request for changes to your study in the catalogue",
                        NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-request-modifications.php',
                        [
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'study_title_fr' => $study['nada_study_title_fr'],
                            'study_title_en' => $study['nada_study_title_en'],
                            'logo_url' => $log_url,
                            'project_link' => $site_url,
                            'email_contact' => get_nada_default_admin_fresh()
                        ]
                    );
                }
            }
        }

        // Si tout est OK
        wp_send_json_success([
            'success' => true,
            'message' => $lang === 'fr' ? 'Modifications demandées avec succès !' : 'Changes successfully requested!',
            'response' => "OK"
        ]);
    } catch (Exception $e) {
        error_log("Erreur publish_unpublish_study_callback : " . $e->getMessage());
        wp_send_json_error([
            'success' => false,
            'message' => $lang === 'fr' ?
                'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                'A technical error occurred while generating the view. Please try again later or contact support.',
            'response' => 'KO'
        ]);
    }
}

/** Rejeter une étude */
function reject_study_callback(): void
{
    $lang = pll_current_language() ?? 'fr';

    try {
        $study_idno = $_POST['study_idno'];
        if (!$study_idno) {
            throw new Exception('ID invalide');
        }

        // Extraire la partie commune avant -FR ou -EN
        $version_idno = get_base_idno($study_idno, true);
        $base_idno = get_base_idno($study_idno, false);

        // Modification coté nada
        $data_nada = [
            "link_indicator" => "rejected"
        ];

        if (strpos($study_idno, 'draft')) {
            // Générer nouvel IDNO a archivé
            $new_idno = generate_next_idno($base_idno);
            // Renommer FR+EN dans NADA
            nada_rename_study($version_idno, $new_idno);

            nada_update($new_idno, $data_nada);
        } else {
            nada_update($version_idno, $data_nada);
        }

        // Préparer les données à mettre à jour
        $data = [
            'status' => 'rejected'
        ];

        $response = update_nada_study_wp($version_idno, $data);

        //TODO / UPDATE STATUS NADA

        if (!$response) {
            throw new Exception('Probléme lors de rejet d\'étude');
        }

        // Notifier
        $study = get_details_study_from_wp($version_idno);
        $site_url = get_site_url();
        if ($study) {
            $current_user = wp_get_current_user();
            $pi_email = $study['pi_email'] ?? null;
            $contributor_id = $study['created_by'] ?? null;
            $mails = [];

            // Ajouter le PI si ce n’est pas l’utilisateur actuel
            if ($pi_email && $pi_email != $current_user->user_email) {
                $mails[] = $pi_email;
            }

            // Ajouter le contributeur si différent de l’utilisateur actuel
            if ($contributor_id && $contributor_id != $current_user->ID) {
                $contributor = get_user_by('id', $contributor_id);
                if ($contributor && $contributor->user_email != $current_user->user_email) {
                    $mails[] = $contributor->user_email;
                }
            }

            // Supprimer doublons éventuels
            $mails = array_unique(array_filter($mails));

            // Envoi d'un mail à chaque destinataire
            if (!empty($mails)) {
                foreach ($mails as $email) {
                    // Trouver l’utilisateur à partir de son e-mail
                    $user = get_user_by('email', $email);

                    // Récupérer prénom et nom s’ils existent dans usermeta
                    $first_name = $user ? get_user_meta($user->ID, 'first_name', true) : '';
                    $last_name = $user ? get_user_meta($user->ID, 'last_name', true) : '';

                    nada_send_email(
                        $email,
                        "[FReSH] Rejet de votre étude soumise sur le catalogue / Rejection of your study submitted to the catalogue",
                        NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-reject.php',
                        [
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'study_title_fr' => $study['nada_study_title_fr'],
                            'study_title_en' => $study['nada_study_title_en'],
                            'logo_url' => "$site_url/wp-content/uploads/2025/10/Logo_FReSH-1.png",
                            'project_link' => $site_url,
                            'contact_email' => get_nada_default_admin_fresh()
                        ]
                    );
                }
            }
        }


        // Si tout est OK
        wp_send_json_success([
            'success' => true,
            'message' => $lang === 'fr' ? 'étude rejetée avec succès !' : 'study successfully rejected !',
            'response' => "OK"
        ]);
    } catch (Exception $ex) {
        nada_id_log("Erreur function reject_study_callback: " . $ex->getMessage());
        wp_send_json_error([
            'success' => false,
            'message' => $lang === 'fr' ?
                'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                'A technical error occurred while generating the view. Please try again later or contact support.',
            'response' => "KO"
        ]);
    }
}

/** Rejeter une étude */
function cancel_study_callback(): void
{
    $lang = pll_current_language() ?? 'fr';
    try {
        $study_idno = $_POST['study_idno'];
        if (!$study_idno) {
            throw new Exception('ID invalide');
        }

        // Extraire la partie commune avant -FR ou -EN
        $base_idno = get_base_idno($study_idno);

        // Modification coté nada
        $data_nada = [
            "link_indicator" => "draft"
        ];
        nada_update($base_idno, $data_nada);

        // Modification coté wp
        $data = [
            'status' => 'draft'
        ];
        $response = update_nada_study_wp($base_idno, $data);
        if (!$response) {
            throw new Exception('Probléme lors de l\'anulation d\'étude');
        }

        //TODO / UPDATE STATUS NADA

        $study = get_details_study_from_wp($base_idno);
        $site_url = get_site_url();
        if ($study) {

            $current_user = wp_get_current_user();

            // Notifier tous les admins
            $admins = get_users(['role__in' => ['admin_fresh']]);

            // Envoi d'un mail à chaque admin (#contributeur)
            if (!empty($admins)) {

                $current_user_first_name = get_user_meta($current_user->ID, 'first_name', true);
                $current_user_last_name = get_user_meta($current_user->ID, 'last_name', true);

                foreach ($admins as $admin) {
                    $email = sanitize_email($admin->user_email);

                    // Le contributeur (est le seul qui a le droit d'annuler la demande )
                    if ($email === $current_user->user_email) {
                        continue;
                    }

                    // Récupérer prénom et nom dans les métadonnées
                    $first_name = get_user_meta($admin->ID, 'first_name', true);
                    $last_name = get_user_meta($admin->ID, 'last_name', true);

                    nada_send_email(
                        $email,
                        "[FReSH] Annulation d'une étude / Cancellation of a study",
                        NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-cancel.php',
                        [
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'contributor_name' => "$current_user_first_name $current_user_last_name",
                            'study_title_fr' => $study['nada_study_title_fr'],
                            'study_title_en' => $study['nada_study_title_en'],
                            'logo_url' => "$site_url/wp-content/uploads/2025/10/Logo_FReSH-1.png",
                            'project_link' => $site_url
                        ]
                    );
                }
            }
        }


        // Si tout est OK
        wp_send_json_success([
            'success' => true,
            'message' => $lang === 'fr' ? 'étude annulée avec succès !' : 'study successfully canceled !',
            'response' => "OK"
        ]);
    } catch (Exception $ex) {
        nada_id_log("Erreur function cancel_study_callback: " . $ex->getMessage());
        wp_send_json_error([
            'success' => false,
            'message' => $lang === 'fr' ?
                'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                'A technical error occurred while generating the view. Please try again later or contact support.',
            'response' => "KO"
        ]);
    }
}

/** Récuperer la liste des catalogues */
function nada_load_catalogs_callback()
{
    try {
        $lang = isset($_POST['lang']) ? sanitize_text_field($_POST['lang']) : 'fr';
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
        $sortBy = sanitize_text_field($_POST['sortBy'] ?? '');
        $sortOrder = sanitize_text_field($_POST['sortOrder'] ?? 'desc');
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $filters = $_POST['filters'] ?? '';

        // Mapping des valeurs de tri pour l'API NADA
        $sortMapping = [
            'created' => 'created',
            'title' => 'title',
            '' => 'created'
        ];
        // Convertir le sortBy selon le mapping
        $apiSortBy = $sortMapping[$sortBy] ?? 'created';
        $api = get_nada_api_instance();
        $response = $api->get_catalogs($lang, $filters, $search, $apiSortBy, $sortOrder, $limit, $page);
        if (
            empty($response) ||
            !isset($response['success']) ||
            !$response['success'] ||
            !isset($response['data']['result']['rows']) ||
            !is_array($response['data']['result']['rows'])
        ) {
            wp_send_json_success([
                'html' => '<div class="nada-id-empty">Aucune donnée trouvée.</div>',
                'data' => $response
            ]);
        }

        $rows = $response['data']['result']['rows'];

        $found = intval($response['data']['result']['found']);

        ob_start();
        include NADA_ID_PLUGIN_DIR . 'public/templates/partials/list-catalogs-items.php';
        $html_items = ob_get_clean();

        ob_start();
        include NADA_ID_PLUGIN_DIR . 'public/templates/partials/list-catalogs-pagination.php';
        $html_pagination = ob_get_clean();

        wp_send_json_success([
            'items' => $html_items,
            'pagination' => $html_pagination,
            'found' => $found,
            'page' => $page,
            'limit' => $limit
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['html' => 'Erreur serveur : ' . $e->getMessage()]);
    }
}

function nada_advanced_search_callback()
{
    // 1. Récupération des données AJAX
    $lang = isset($_POST['lang']) ? sanitize_text_field($_POST['lang']) : 'fr';
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
    $operator = isset($_POST['operator']) ? sanitize_text_field($_POST['operator']) : 'AND';
    $sortBy = isset($_POST['sortBy']) ? sanitize_text_field($_POST['sortBy']) : 'created';
    $sortOrder = isset($_POST['sortOrder']) ? sanitize_text_field($_POST['sortOrder']) : 'desc';

    // Nettoyage des critères
    $criteriaRAW = isset($_POST['criteria']) ? $_POST['criteria'] : [];
    $criteria = [];
    if (is_array($criteriaRAW)) {
        foreach ($criteriaRAW as $c) {
            if (isset($c['field']) && isset($c['value'])) {
                $criteria[] = [
                    'field' => sanitize_text_field($c['field']),
                    'operator' => isset($c['operator']) ? sanitize_text_field($c['operator']) : '=',
                    'value' => sanitize_text_field($c['value'])
                ];
            }
        }
    }

    // 2. Appel à l'API NADA
    $api = get_nada_api_instance();
    $response = $api->get_catalogs_advanced($criteria, $operator, $lang, $limit, $page, $sortBy, $sortOrder);

    // 3. Traitement de la réponse
    if (!$response['success']) {
        wp_send_json_success([
            'items' => '<div class="nada-id-empty">Erreur API : ' . ($response['error'] ?? 'Inconnue') . '</div>',
            'pagination' => ''
        ]);
        return;
    }

    $data = $response['data'];
    $rows = isset($data['result']['rows']) ? $data['result']['rows'] : [];

    $found = isset($data['found']) ? intval($data['found']) : 0;


    $current_language = $lang;

    // 4. Rendu HTML
    ob_start();
    include NADA_ID_PLUGIN_DIR . 'public/templates/partials/list-catalogs-items.php';
    $html_items = ob_get_clean();

    ob_start();
    include NADA_ID_PLUGIN_DIR . 'public/templates/partials/list-catalogs-pagination.php';
    $html_pagination = ob_get_clean();

    // 5. Envoi final
    wp_send_json_success([
        'items' => $html_items,
        'pagination' => $html_pagination,
        'found' => $found,
        'page' => $page,
        'limit' => $limit
    ]);
}

/** Récuperer la liste des facets */
function nada_load_facets()
{
    try {
        $lang = isset($_POST['lang']) ? sanitize_text_field($_POST['lang']) : 'fr';

        $api = get_nada_api_instance();
        $res = $api->get_facet_custom($lang);

        if (!$res['success'] || $res['data']['status'] !== 'success') {
            wp_send_json_error(['html' => 'Erreur API']);
        }

        $data_facets = $res['data'];
        ob_start();
        include NADA_ID_PLUGIN_DIR . "public/templates/partials/facets.php";
        $html = ob_get_clean();

        wp_send_json_success([
            'html' => $html,
            'result' => $data_facets
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['html' => 'Erreur serveur : ' . $e->getMessage()]);
    }
}

/** Récuperer les valeurs d'une facette */
function get_facette_values_callback()
{
    try {
        // Récupère les paramètres envoyés par AJAX
        $attrValue = isset($_POST['attrValue']) ? sanitize_text_field($_POST['attrValue']) : '';
        $lang = isset($_POST['lang']) ? sanitize_text_field($_POST['lang']) : 'fr';

        // Vérifie si le paramètre est bien défini
        if (empty($attrValue)) {
            wp_send_json_error('Paramètre manquant.');
            wp_die();
        }

        $api = get_nada_api_instance();


        // Facets dynamiques (custom) pour FR et EN
        $facet_ID = $api->get_facet_custom($lang);
        if ($facet_ID['success'] && $facet_ID['data']['status'] === 'success') {
            $data_ID[$lang] = $facet_ID['data'];
        }

        if (!isset($data_ID[$lang]['values'])) {
            wp_send_json([]);
            wp_die();
        }

        $values = [];
        foreach ($data_ID[$lang]['values'] as $filter_key) {
            if ($filter_key['name'] === $attrValue) {
                $values = $filter_key['values'];
                break;
            }
        }

        // Trie les valeurs par "found" (décroissant)
        usort($values, function ($a, $b) {
            return intval($b['found']) - intval($a['found']);
        });

        // Retourne toutes les valeurs (le JS gère la limite d’affichage)
        wp_send_json($values);

        wp_die();
    } catch (Exception $e) {
        nada_id_log("Erreur function get_facette_values: " . $e->getMessage());
        wp_send_json_error([
            'success' => false,
            'response' => 'KO',
            'message' => $lang === 'fr' ?
                'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                'A technical error occurred while generating the view. Please try again later or contact support.'
        ]);
    }
}

/** Récupérer un token ICD */
function get_icd_token()
{
    $cached = get_transient('who_icd_token');
    if ($cached) return $cached;


    $response = wp_remote_post(get_nada_icd_url(), [
        'body' => [
            'client_id' => get_nada_icd_client_id(),
            'client_secret' => get_nada_icd_client_secret(),
            'scope' => 'icdapi_access',
            'grant_type' => 'client_credentials'
        ]
    ]);

    if (is_wp_error($response)) {
        return null;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    $token = $body['access_token'] ?? null;

    if ($token) {
        set_transient('who_icd_token', $token, HOUR_IN_SECONDS);
    }

    return $token;
}

/** Gérer la recherche ICD via AJAX */
function handle_get_search_list_cim()
{
    if (empty($_POST['searchInput']) || empty($_POST['lang'])) {
        wp_send_json_error('Aucune donne recu.');
    }

    $searchInput = $_POST['searchInput'];
    $lang = $_POST['lang'];
    $data_fr = get_icd_search($searchInput, $lang);

    wp_send_json_success($data_fr);
}

/** Gérer la récupération des entités ICD */
function handle_get_icd_entities()
{
    if (empty($_POST['entities']) || !is_array($_POST['entities'])) {
        wp_send_json_error('Aucune entité reçue.');
    }

    $entities = $_POST['entities'];
    $lang = $_POST['lang'];

    $results = [];

    foreach ($entities as $entity) {
        // $entity contient les clés 'id' et 'title_fr' envoyées depuis JS
        $url = $entity['id'];
        $title = $entity['title'] ?? '';

        // Récupération des données en anglais via ta fonction

        if ($lang == 'fr') {
            $data_en = get_icd_entity($url, "en");
            $results[] = [
                'id' => $url,
                'title_fr' => $title,
                'title_en' => $data_en['title']['@value'] ?? '',
            ];
        }

        if ($lang == 'en') {
            $data_fr = get_icd_entity($url, "fr");
            $results[] = [
                'id' => $url,
                'title_fr' => $data_fr['title']['@value'] ?? '',
                'title_en' => $title,
            ];
        }
    }

    wp_send_json_success($results);
}

/** Effectuer une recherche ICD */
function get_icd_search($searchInput, $lang = 'fr')
{
    $token = get_icd_token();

    if (!$token) {
        wp_send_json_error(['message' => 'Impossible d’obtenir le token ICD'], 401); // code HTTP 401 = Unauthorized
        wp_die();
    }

    /** pour autres options voir https://id.who.int/swagger/index.html */
    /**
     * useFlexisearch
     * flatResults
     * propertiesToBeSearched
     * highlightingEnabled
     */
    $urlIcdSearch = get_nada_icd_search();
    if (!$urlIcdSearch) {
        wp_send_json_error(['message' => 'URL de recherche ICD non disponible'], 500);
        wp_die();
    }

    // Encoder correctement le paramètre de recherche
    $searchQuery = urlencode($searchInput);

    // Construction de l'URL avec le paramètre de recherche
    $url = $urlIcdSearch . '?q=' . "$searchQuery%";

    $response = wp_remote_get($url, [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => $lang,
            'Authorization' => 'Bearer ' . $token,
            'API-Version' => 'v2'
        ],
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        return [];
    }

    return json_decode(wp_remote_retrieve_body($response), true);
}

/** Récupérer une entité ICD */
function get_icd_entity($url, $lang = 'en')
{

    $token = get_icd_token();

    if (!$token) {
        wp_send_json_error(['message' => 'Impossible d’obtenir le token ICD'], 401); // code HTTP 401 = Unauthorized
        wp_die();
    }


    $response = wp_remote_get($url, [
        'headers' => [
            'Accept' => 'application/json',
            'Accept-Language' => $lang,
            'Authorization' => 'Bearer ' . $token,
            'API-Version' => 'v2'
        ],
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        return [];
    }

    return json_decode(wp_remote_retrieve_body($response), true);
}


/* ================================ Fonctions utilitaires ================================================/

/* Recuperer l'id du survey à créer */
function getSurveyNewId(): int
{
    // Récupérer l’instance unique de Nada_API
    $nada_api = get_nada_api_instance();

    // Récuperer la derniére id d'etude + incrementé 1
    $response = $nada_api->fech_study_id();

    if ($response['success'] && $response['data']['status'] == 'success') {
        return $response['data']['last_id'];
    } else {
        return 0;
    }
}

/** Création ou mise à jour d'une étude + PI + status */
function saveStudy(array $data, $lang): array
{

    try {
        global $wpdb;
        $table_name = $wpdb->prefix . "nada_list_studies";
        $current_user = wp_get_current_user();
        $current_user_id = get_current_user_id();


        // Vérifier si l’étude existe déjà
        $study_wp = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE nada_study_idno = %s", $data['study_idno']),
            ARRAY_A
        );

        // Déterminer PI
        [$pi_id, $pi_email] = resolvePI($data, $current_user);

        // Récupérer l'ancien statut
        $old_status = $study_wp['status']; // statut actuel dans WP
        $new_status = $data['status'];     // statut demandé via formulaire (draft ou pending)

        // Si la publication existe déjà, on ne change plus via ce formulaire
        if ($old_status === 'published' || $old_status === 'unpublished') {
            $data['status'] = $old_status;
        } // Si l’étude était en pending et l’utilisateur essaie de revenir en draft -> interdit
        elseif ($old_status === 'pending' && $new_status === 'draft') {
            $data['status'] = 'pending';
        } // Sinon, on applique le statut demandé (draft ou pending)
        else {
            $data['status'] = $new_status;
        }

        $status = $data['status'];
        $isParent = $data['is_parent'];
        $studySource = $data['study_source'];
        $studyType = $data['study_type'];
        $allStudiesProcessed = $data['all_studies_processed'];

        $isVersioningStudy = $status === 'pending' && (strpos($data['study_idno'], 'draft'));
        $events = [];

        /** Création */
        if (!$study_wp) {
            $insert_data = [
                'nada_study_idno' => $data['study_idno'],
                'nada_study_id_fr' => $data['study_id_fr'],
                'nada_study_id_en' => $data['study_id_en'],
                'nada_study_title_fr' => $data['study_title_fr'],
                'nada_study_title_en' => $data['study_title_en'],
                'status' => $status,
                'pi_id' => $pi_id,
                'pi_email' => $pi_email,
                'is_parent' => $isParent,
                'study_source' => $studySource,
                'study_type' => $studyType,
                'created_by' => $data['created_by'],
                'created_at' => current_time('mysql'),
                'added_by' => $current_user_id,
                'updated_by' => $current_user_id,
                'updated_at' => current_time('mysql')
            ];

            $result = $wpdb->insert($table_name, $insert_data);
            if ($result === false) {
                return ['success' => false, 'message' => $lang === 'fr' ? 'Erreur lors de la création' : 'Error during creation'];
            }


            if (!$data['translate_enabled']) {
                $sendEmail = true;
            } else {
                $sendEmail = $allStudiesProcessed == true || 'true' ? true : false;
            }

            // Notifications création => on simule "status_changed"
            if (!$data['auto_save'] && $status != 'draft' && !$isVersioningStudy && $sendEmail) {

                $events = [
                    'status_changed' => [null, $status],
                    'pi_changed' => $pi_email
                ];
                handleStudyNotifications($events, $data, $current_user, $pi_email);
            }

            if (!$data['auto_save'] && $isVersioningStudy  && $sendEmail) {

                $events['modified_pending'] = true;
                handleStudyNotifications($events, $data, $current_user, $pi_email);
            }

            return ['success' => true, 'message' => $lang === 'fr' ? 'Étude créée avec succès' : 'Study created successfully'];
        }


        /** Mise à jour */
        $updates = [];

        // Changement de statut ?
        if ($status !== $study_wp['status']) {
            $updates['status'] = $status;
            if ($old_status != 'changes_requested') {
                $events['status_changed'] = [$study_wp['status'], $status];
            }
        }

        // Changement de PI ?
        if ($pi_id != $study_wp['pi_id'] || $pi_email !== $study_wp['pi_email']) {
            $updates['pi_id'] = $pi_id;
            $updates['pi_email'] = $pi_email;
            $events['pi_changed'] = $pi_email;
        }

        $updates['updated_at'] = current_time('mysql');
        $updates['updated_by'] = $current_user_id;
        $updates['nada_study_title_fr'] = $data['study_title_fr'];
        $updates['nada_study_title_en'] = $data['study_title_en'];
        $updates['study_type'] = $data['study_type'];

        if (!empty($updates)) {

            $result = $wpdb->update(
                $table_name,
                $updates,
                ['nada_study_idno' => $data['study_idno']]
            );

            if ($result === false) {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
            }


            if ((in_array($status, ['pending', 'changes_requested', 'published', 'unpublished']) && $old_status != 'draft') || $isVersioningStudy) {
                $events['modified_pending'] = true;
            }

            if (!$data['auto_save'] && $status != 'draft') {
                // Notifications mise à jour
                handleStudyNotifications($events, $data, $current_user, $pi_email);
            }

            return ['success' => true, 'message' => 'Étude mise à jour'];
        }

        return [
            'success' => true,
            'message' => $lang === 'fr' ? 'Aucune modification détectée' : 'No changes detected'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $lang === 'fr' ?
                'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                'A technical error occurred while generating the view. Please try again later or contact support.',
        ];
    }
}

/**
 * Détermine quel utilisateur est PI pour une étude.
 *
 * Règles :
 * - Si `pi_is_current_user` est vrai -> le PI est l’utilisateur courant.
 * - Sinon -> on prend l’email fourni (`pi_email`).
 *   - Si un compte WordPress existe avec cet email -> on récupère son user ID.
 *   - Sinon -> le PI est "externe" (ID = null, mais email renseigné).
 */
function resolvePI(array $data, WP_User $current_user): array
{

    $raw = $data['pi_is_current_user'] ?? null;

    // Normalisation en booléen (gère "true/false", "1/0", "on/off", "yes/no")
    $pi_is_current_user = is_bool($raw)
        ? $raw
        : filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

    // Branche selon la valeur normalisée
    if ($pi_is_current_user === true) {
        return [$current_user->ID, $current_user->user_email];
    }

    $pi_email = sanitize_email($data['pi_email'] ?? '');
    if (empty($pi_email)) {
        return [null, ''];
    }

    // Récuperer l'utilisateur Wp s'il existe
    $user = get_user_by('email', $pi_email);
    return $user ? [$user->ID, $pi_email] : [null, $pi_email];
}

/** Fonction centralisée pour gérer les notifications (création ou mise à jour) */
function handleStudyNotifications(array $events, array $data, WP_User $current_user, string $pi_email): void
{
    $admins = get_users(['role__in' => ['admin_fresh']]);
    $emails_admins = array_filter(array_map('sanitize_email', wp_list_pluck($admins, 'user_email')));
    $admin_ids = array_map('intval', wp_list_pluck($admins, 'ID'));
    $contributor_id = $data['created_by'];
    $translate_enabled = $data['translate_enabled'];
    $studyTitle = $data['study_title_fr'];
    $studyTitleEn = $data['study_title_en'];
    $pi_name = $data['pi_name'];

    $is_contributor_admin = in_array($contributor_id, $admin_ids);

    // Email L'email de l'utilisateur conencté s'il est admin
    $email_exclu = sanitize_email($current_user->user_email);

    // Supprimer l'email s'il exisarray: te dans le tableau
    $emails_admins = array_filter($emails_admins, function ($email) use ($email_exclu) {
        return $email !== $email_exclu;
    });

    $site_url = get_site_url();
    $log_url = "$site_url/wp-content/uploads/2025/10/Logo_FReSH-1.png";
    $inscription_url_fr = "$site_url/connexion/";
    $inscription_url_en = "$site_url/en/login/";

    $current_user_first_name = get_user_meta($current_user->ID, 'first_name', true);
    $current_user_last_name = get_user_meta($current_user->ID, 'last_name', true);

    // Récupérer infos contributeur (nom/prénom + email)
    $contributor = get_userdata($contributor_id);
    $contributor_email = $contributor ? $contributor->user_email : '';
    $contributor_first = $contributor ? get_user_meta($contributor->ID, 'first_name', true) : '';
    $contributor_last = $contributor ? get_user_meta($contributor->ID, 'last_name', true) : '';

    $translate_blocks = email_get_translate_blocks($translate_enabled);

    if (isset($events['status_changed'])) {
        //Envoi mail aux administrateurs
        foreach ($admins as $admin) {
            $email = sanitize_email($admin->user_email);

            // Sauter si c'est l'email exclu
            if ($email === $email_exclu) {
                continue;
            }

            // Récupérer prénom et nom dans les métadonnées
            $first_name = get_user_meta($admin->ID, 'first_name', true);
            $last_name = get_user_meta($admin->ID, 'last_name', true);


            nada_send_email(
                $email,
                "[FReSH] Nouvelle étude a été soumise / New Study has been submitted",
                NADA_ID_PLUGIN_DIR . 'public/templates/emails/email.submission-admin.php',
                [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'contributor_name' => "$contributor_first $contributor_last",
                    'study_title_fr' => $studyTitle,
                    'study_title_en' => $studyTitleEn,
                    'logo_url' => "$site_url/wp-content/uploads/2025/10/Logo_FReSH-1.png",
                    'project_link' => $site_url
                ]
            );
        }

        if (!$is_contributor_admin) {
            nada_send_email(
                $contributor_email,
                "[FReSH] Votre étude a été soumise avec succès / Your study has been successfully submitted",
                NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-submission.php',
                [
                    'first_name' => $contributor_first,
                    'last_name' => $contributor_last,
                    'study_title_fr' => $studyTitle,
                    'study_title_en' => $studyTitleEn,
                    'logo_url' => "$site_url/wp-content/uploads/2025/10/Logo_FReSH-1.png",
                    'project_link' => $site_url
                ]
            );
        }


        // Mail au PI : si différent du contributeur
        if (!empty($pi_email) && $pi_email !== $contributor_email) {
            $user = get_user_by('email', $pi_email);
            if ($user) {
                $first_name = get_user_meta($user->ID, 'first_name', true);
                $last_name = get_user_meta($user->ID, 'last_name', true);
                nada_send_email(
                    $pi_email,
                    "[FReSH] Nouvelle étude a été soumise / New Study has been submitted",
                    NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-submission-pi.php',
                    [
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'contributor_name' => "$contributor_first $contributor_last",
                        'study_title_fr' => $studyTitle,
                        'study_title_en' => $studyTitleEn,
                        'logo_url' => "$site_url/wp-content/uploads/2025/10/Logo_FReSH-1.png",
                        'project_link' => $site_url,
                        'inscripton_url_fr' => $inscription_url_fr,
                        'inscripton_url_en' => $inscription_url_en,
                        'translate_block_fr' => $translate_blocks['fr'],
                        'translate_block_en' => $translate_blocks['en'],
                    ]
                );
            } else {
                $register_url_fr = site_url('/inscription/');
                $register_url_en = site_url('/en/register/');
                nada_send_email(
                    $pi_email,
                    "[FReSH] Créer votre compte pour accéder à votre étude / [FReSH] Create your account to access your study",
                    NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-submission-unregistered-pi.php',
                    [
                        'full_name' => str_replace(';', ' ', $pi_name),
                        'contributor_name' => "$contributor_first $contributor_last",
                        'study_title_fr' => $studyTitle,
                        'study_title_en' => $studyTitleEn,
                        'logo_url' => "$site_url/wp-content/uploads/2025/10/Logo_FReSH-1.png",
                        'project_link' => $site_url,
                        'register_url' => $register_url_fr,
                        'register_url_en' => $register_url_en,
                        'translate_block_fr' => $translate_blocks['fr'],
                        'translate_block_en' => $translate_blocks['en']
                    ]
                );
            }
        }
    }

    // --- Si le PI a changé --- pas le statut
    if (isset($events['pi_changed']) && !empty($events['pi_changed']) && empty($events['status_changed'])) {
        $new_pi_email = sanitize_email($events['pi_changed']);
        // en cas pi different de user connecté
        if (!empty($new_pi_email) && $current_user->user_email !== $new_pi_email) {
            $user = get_user_by('email', $new_pi_email);
            if ($user) {
                nada_send_email(
                    $pi_email,
                    "[FReSH] Nouvelle étude a été soumise / New Study has been submitted",
                    NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-submission-pi.php',
                    [
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'contributor_name' => "$contributor_first $contributor_last",
                        'study_title_fr' => $studyTitle,
                        'study_title_en' => $studyTitleEn,
                        'logo_url' => "$site_url/wp-content/uploads/2025/10/Logo_FReSH-1.png",
                        'project_link' => $site_url,
                        'inscripton_url_fr' => $inscription_url_fr,
                        'inscripton_url_en' => $inscription_url_en,
                        'translate_block_fr' => $translate_blocks['fr'],
                        'translate_block_en' => $translate_blocks['en'],
                    ]
                );
            } else {
                $register_url_fr =  site_url('/inscription/');
                $register_url_en = site_url('/en/register/');
                nada_send_email(
                    $new_pi_email,
                    "[FReSH] Créer votre compte pour accéder à votre étude / [FReSH] Create your account to access your study",
                    NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-submission-unregistered-pi.php',
                    [
                        'full_name' => str_replace(';', ' ', $pi_name),
                        'contributor_name' => "$contributor_first $contributor_last",
                        'study_title_fr' => $studyTitle,
                        'study_title_en' => $studyTitleEn,
                        'logo_url' => "$site_url/wp-content/uploads/2025/10/Logo_FReSH-1.png",
                        'project_link' => $site_url,
                        'register_url' => $register_url_fr,
                        'register_url_en' => $register_url_en,
                        'translate_block_fr' => $translate_blocks['fr'],
                        'translate_block_en' => $translate_blocks['en']
                    ]
                );
            }
        }
    }
    if (isset($events['modified_pending']) && $events['modified_pending'] == true) {

        $modificator_email = sanitize_email($current_user->user_email);

        // Vérifier les rôles
        $is_admin = user_can($current_user, 'admin_fresh');
        $is_pi = (!empty($pi_email) && $modificator_email === $pi_email);
        $is_contributor = $contributor_email == $modificator_email;

        // Cas 1 : Modifié par un Admin
        if ($is_admin) {
            // Notifier les autres admins
            foreach ($admins as $admin) {
                $email = sanitize_email($admin->user_email);

                // Sauter si c'est l'email exclu
                if ($email === $email_exclu) {
                    continue;
                }

                // Récupérer prénom et nom dans les métadonnées
                $first_name = get_user_meta($admin->ID, 'first_name', true);
                $last_name = get_user_meta($admin->ID, 'last_name', true);
                nada_send_email(
                    $email,
                    "Étude modifiée / Modified study",
                    NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-modified.php',
                    [
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'study_title_fr' => $studyTitle,
                        'study_title_en' => $studyTitleEn,
                        'logo_url' => $log_url,
                        'project_link' => $site_url,
                        'translate_block_fr' => $translate_blocks['fr'],
                        'translate_block_en' => $translate_blocks['en']
                    ]
                );
            }

            // Notifier le PI (si différent de l’admin modificateur)
            if (!empty($pi_email) && $pi_email !== $modificator_email) {
                $user = get_user_by('email', $pi_email);
                if ($user) {
                    $first_name = get_user_meta($user->ID, 'first_name', true);
                    $last_name = get_user_meta($user->ID, 'last_name', true);
                    nada_send_email(
                        $pi_email,
                        "Étude modifiée / Modified study",
                        NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-modified.php',
                        [
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'study_title_fr' => $studyTitle,
                            'study_title_en' => $studyTitleEn,
                            'logo_url' => $log_url,
                            'project_link' => $site_url,
                            'translate_block_fr' => $translate_blocks['fr'],
                            'translate_block_en' => $translate_blocks['en']

                        ]
                    );
                } else {
                    $register_url_fr =  site_url('/inscription/');
                    $register_url_en = site_url('/en/register/');
                    nada_send_email(
                        $pi_email,
                        "Étude modifiée / Modified study",
                        NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-modified-unregistered-pi.php',
                        [
                            'last_name' => $last_name,
                            'study_title_fr' => $studyTitle,
                            'study_title_en' => $studyTitleEn,
                            'logo_url' => $log_url,
                            'project_link' => $site_url,
                            'register_url' => $register_url_fr,
                            'register_url_en' => $register_url_en,
                            'translate_block_fr' => $translate_blocks['fr'],
                            'translate_block_en' => $translate_blocks['en']
                        ]
                    );
                }
            }

            // Notifier le Contributeur (si différent de l’admin modificateur)
            if (!empty($contributor_email) && $contributor_email !== $modificator_email) {
                nada_send_email(
                    $contributor_email,
                    "Étude modifiée / Modified study",
                    NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-modified.php',
                    [
                        'first_name' => $contributor_first,
                        'last_name' => $contributor_last,
                        'study_title_fr' => $studyTitle,
                        'study_title_en' => $studyTitleEn,
                        'logo_url' => $log_url,
                        'project_link' => $site_url,
                        'translate_block_fr' => $translate_blocks['fr'],
                        'translate_block_en' => $translate_blocks['en']
                    ]
                );
            }
        } // Cas 2 : Modifié par PI
        elseif ($is_pi && !$is_admin) {
            // 1. notifier tous les admins
            foreach ($admins as $admin) {
                $email = sanitize_email($admin->user_email);

                // Récupérer prénom et nom dans les métadonnées
                $first_name = get_user_meta($admin->ID, 'first_name', true);
                $last_name = get_user_meta($admin->ID, 'last_name', true);
                nada_send_email(
                    $email,
                    "Étude modifiée / Modified study",
                    NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-modified.php',
                    [
                        'first_name' => $contributor_first,
                        'last_name' => $contributor_last,
                        'study_title_fr' => $studyTitle,
                        'study_title_en' => $studyTitleEn,
                        'logo_url' => $log_url,
                        'project_link' => $site_url,
                        'translate_block_fr' => $translate_blocks['fr'],
                        'translate_block_en' => $translate_blocks['en']
                    ]
                );
            }

            // 2. si le pi != contributeur
            if ($pi_email !== $contributor_email) {
                nada_send_email(
                    $contributor_email,
                    "Étude modifiée / Modified study",
                    NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-modified.php',
                    [
                        'first_name' => $contributor_first,
                        'last_name' => $contributor_last,
                        'study_title_fr' => $studyTitle,
                        'study_title_en' => $studyTitleEn,
                        'logo_url' => $log_url,
                        'project_link' => $site_url,
                        'translate_block_fr' => $translate_blocks['fr'],
                        'translate_block_en' => $translate_blocks['en']
                    ]
                );
            }
        } // Modifié par Contributeur  ---
        elseif ($is_contributor && !$is_admin) {
            // Envoyer aux admins
            foreach ($admins as $admin) {
                $email = sanitize_email($admin->user_email);

                // Récupérer prénom et nom dans les métadonnées
                $first_name = get_user_meta($admin->ID, 'first_name', true);
                $last_name = get_user_meta($admin->ID, 'last_name', true);
                nada_send_email(
                    $email,
                    "Étude modifiée / Modified study",
                    NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-modified.php',
                    [
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'study_title_fr' => $studyTitle,
                        'study_title_en' => $studyTitleEn,
                        'logo_url' => $log_url,
                        'project_link' => $site_url,
                        'translate_block_fr' => $translate_blocks['fr'],
                        'translate_block_en' => $translate_blocks['en']
                    ]
                );
            }

            // cas contributor #  PI
            if (!$is_pi && !empty($pi_email)) {
                $user = get_user_by('email', $pi_email);
                if ($user) {
                    $first_name = get_user_meta($user->ID, 'first_name', true);
                    $last_name = get_user_meta($user->ID, 'last_name', true);
                    nada_send_email(
                        $pi_email,
                        "Étude modifiée / Modified study",
                        NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-modified.php',
                        [
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'study_title_fr' => $studyTitle,
                            'study_title_en' => $studyTitleEn,
                            'logo_url' => $log_url,
                            'project_link' => $site_url,
                            'translate_block_fr' => $translate_blocks['fr'],
                            'translate_block_en' => $translate_blocks['en']
                        ]
                    );
                } else {
                    $register_url_fr = site_url('/inscription/');
                    $register_url_en = site_url('/en/register/');
                    nada_send_email(
                        $pi_email,
                        "Étude modifiée / Modified study",
                        NADA_ID_PLUGIN_DIR . 'public/templates/emails/email-modified-unregistered-pi.php',
                        [
                            'last_name' => $last_name,
                            'study_title_fr' => $studyTitle,
                            'study_title_en' => $studyTitleEn,
                            'logo_url' => $log_url,
                            'project_link' => $site_url,
                            'register_url' => $register_url_fr,
                            'register_url_en' => $register_url_en,
                            'translate_block_fr' => $translate_blocks['fr'],
                            'translate_block_en' => $translate_blocks['en']
                        ]
                    );
                }
            }
        }
    }
}

/**
 * Vérifie si l'email correspond à un PI existant dans nada_list_studies
 * et met à jour pi_id si nécessaire.
 */
function assign_nada_studies_to_pi(int $user_id, string $email): void
{
    global $wpdb;
    $table = $wpdb->prefix . "nada_list_studies";

    $rows = $wpdb->get_results(
        $wpdb->prepare("SELECT id FROM $table WHERE pi_email = %s AND (pi_id IS NULL OR pi_id = 0)", $email)
    );

    if (!empty($rows)) {
        foreach ($rows as $row) {
            $wpdb->update(
                $table,
                ['pi_id' => $user_id],
                ['id' => $row->id],
                ['%d'],
                ['%d']
            );
        }
    }
}

/** liaison etude par son contritbutor selon le table DB */
function assign_nada_studies_to_contributor(int $user_id, string $email, string $nadaUserId): void
{
    global $wpdb;
    $table = $wpdb->prefix . "nada_contributor_pef";
    $api = get_nada_api_instance();

    $studies = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT pef_id, first_name, last_name, affiliation FROM $table WHERE email = %s",
            $email
        )
    );

    if (empty($studies)) {
        nada_id_log('Aucune étude trouvée pour ' . $email);
        return;
    }
    $nada_token = get_user_meta($user_id, 'nada_token', true);

    foreach ($studies as $study) {
        $idno = 'FRESH-PEF' . $study->pef_id;
        $contributorName = sanitize_text_field($study->first_name . ' ' . $study->last_name);
        $contributorAffiliation = sanitize_text_field($study->affiliation);

        try {
            // Boucle FR et EN
            foreach (['fr', 'en'] as $lang) {
                $targetIdno = "{$idno}-{$lang}";

                $data = [
                    "created_by" => $nadaUserId,
                    "additional" => [
                        "contributorName" => $contributorName,
                        "contributorAffiliation" => $contributorAffiliation
                    ]
                ];

                $response = $api->update_study($targetIdno, json_encode($data), $nada_token);

                if (!$response['success']) {
                    nada_id_log("Erreur update {$targetIdno}: " . json_encode($response));
                    continue;
                }

                nada_id_log("Étude {$targetIdno} mise à jour");
            }
        } catch (Exception $e) {
            nada_id_log('Exception ' . $idno . ': ' . $e->getMessage());
        }
    }
}

/**
 * Supprime une étude depuis la table nada_list_studies par idno
 */
function delete_nada_study_from_wp(string $idno): bool
{
    global $wpdb;
    $table = $wpdb->prefix . "nada_list_studies";

    // Supprime la ligne
    $deleted = $wpdb->delete(
        $table,
        ['nada_study_idno' => $idno],
        ['%s'] // format string
    );

    if ($deleted === false) {
        return false; // Erreur SQL
    }

    return true;
}

/**
 * Met à jour le statut d'une étude
 */
function update_nada_study_wp(string $idno, array $data): bool
{
    global $wpdb;
    $table = $wpdb->prefix . "nada_list_studies";

    // Condition WHERE
    $where = ['nada_study_idno' => $idno];

    // Générer dynamiquement les formats
    $formats = [];
    foreach ($data as $key => $value) {
        if (is_int($value)) {
            $formats[] = '%d';
        } elseif (is_float($value)) {
            $formats[] = '%f';
        } else {
            $formats[] = '%s';
        }
    }


    // Exécuter la mise à jour
    $updated = $wpdb->update(
        $table,
        $data,
        $where,
        $formats, // formats : int, datetime
        ['%s']        // condition string
    );

    if ($updated === false) {
        return false; // Erreur SQL
    }

    return $updated > 0; // true si au moins 1 ligne modifiée
}

/**
 */
/**
 * Extraire la partie de base de l'idno (sans -FR ou -EN)
 */
function get_base_idno(string $idno, bool $isVersion = true): string
{
    // Si on veut supprimer la version (-FR / -EN)
    if ($isVersion === true) {
        if (preg_match('/^(.*?)-(?:fr|en)$/i', $idno, $matches)) {
            return $matches[1];
        } else {
            return $idno;
        }
    }

    // Sinon retourner la base composée des deux premiers segments
    // Extraire la partie commune avant -FR ou -EN
    $parts = explode('-', $idno);
    if (count($parts) >= 2) {
        return $parts[0] . '-' . $parts[1];
    }
    return $idno;
}


/**
 * Récuperer une étude depuis la table nada_list_studies par idno +sttaus optionnel
 */
function get_details_study_from_wp(string $base_idno, ?string $status = null)
{
    global $wpdb;
    $table = $wpdb->prefix . "nada_list_studies";

    try {

        // Base query
        $sql = "SELECT * FROM {$table} WHERE nada_study_idno LIKE %s";
        $params = [$base_idno . '%'];

        // Ajouter le filtre status si renseigné
        if (!empty($status)) {

            if ($status === 'published') {
                // Cas spécial : published OU imported
                $sql .= " AND (status = %s OR status = %s)";
                $params[] = 'published';
                $params[] = 'imported';
            } else {
                // Cas normal
                $sql .= " AND status = %s";
                $params[] = $status;
            }
        }

        // Prépare & exécute
        $study_wp = $wpdb->get_row(
            $wpdb->prepare($sql, ...$params),
            ARRAY_A
        );

        return $study_wp ?: null;
    } catch (Exception $e) {
        error_log('Erreur dans get_details_study_from_wp: ' . $e->getMessage());
        return null;
    }
}

/* Générer un nouvel IDNO incrémenté */
function generate_next_idno(string $base_idno): string
{
    global $wpdb;
    $table = $wpdb->prefix . 'nada_list_studies';

    // Récupère toutes les variantes existantes : FReSH-19551, FReSH-19551-1, FReSH-19551-2...
    $rows = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT nada_study_idno 
             FROM {$table}
             WHERE nada_study_idno LIKE %s",
            $base_idno . '%'
        )
    );

    if (empty($rows)) {
        // aucune version existante — on crée la version "-1"
        return $base_idno . '-1';
    }

    $max = 0;

    foreach ($rows as $idno) {

        // Si c’est exactement le base_idno → considérer comme version 0
        if ($idno === $base_idno) {
            continue;
        }

        // Cherche un suffixe numérique : "-1", "-2", "-3"
        if (preg_match('/^' . preg_quote($base_idno, '/') . '\-(\d+)$/', $idno, $m)) {
            $num = intval($m[1]);
            if ($num > $max) {
                $max = $num;
            }
        }
    }

    // Incrémente
    $next = $max + 1;

    return $base_idno . '-' . $next;
}

/**
 * Récuperer une étude depuis nada par idno
 */
function get_details_study_from_nada(string $idno, ?string $source = ''): array
{

    $responses = [];
    // Instance de l’API
    $api = get_nada_api_instance();
    $base_idno = get_base_idno($idno);
    // Récupérer dataset
    $responses['fr'] = $api->get_catalog("$base_idno-fr", $source);
    $responses['en'] = $api->get_catalog("$base_idno-en", $source);

    return $responses;
}

/** Recuperer de la bd la liste des referentiels avec ses options */
function get_list_referentiels_wp()
{
    global $wpdb;

    // Noms des tables (avec préfixe WP si applicable)
    $table_referentiels = $wpdb->prefix . 'nada_referentiels';
    $table_items = $wpdb->prefix . 'nada_referentiel_items';

    // Récupérer tous les référentiels
    $referentiels = $wpdb->get_results("
        SELECT id, referentiel_name
        FROM $table_referentiels
        WHERE is_enabled = 1
        ORDER BY id ASC
    ");

    // Si aucun référentiel trouvé
    if (empty($referentiels)) {
        return [];
    }

    // Récupérer tous les items actifs (non archivés)
    $items = $wpdb->get_results("
        SELECT *
        FROM $table_items
        WHERE is_enabled = 1
        ORDER BY id ASC
    ");

    // Regrouper les items par referentiel_id
    $items_by_ref = [];
    foreach ($items as $item) {
        $items_by_ref[$item->referentiel_id][] = $item;
    }

    // Assembler le tout
    $result = [];
    foreach ($referentiels as $ref) {
        $result[] = [
            'id' => (int)$ref->id,
            'name' => $ref->referentiel_name,
            'items' => isset($items_by_ref[$ref->id]) ? $items_by_ref[$ref->id] : []
        ];
    }

    return $result;
}

/** Recuperer de la bd la liste des tooltips des metadata */
function get_list_metadata_tooltips_wp(): array
{
    global $wpdb;

    $table = $wpdb->prefix . 'nada_metadata_fields';

    $rows = $wpdb->get_results("
        SELECT
            parent,
            variable_name,
            description_fr,
            description_en
        FROM {$table}
        WHERE variable_name IS NOT NULL
    ");

    if (empty($rows)) {
        return [];
    }

    $tooltips = [];

    foreach ($rows as $row) {
        // Créer une clé unique avec parent et variable_name
        $parent = trim((string)$row->parent);
        $varName = trim((string)$row->variable_name);

        // Si parent existe, utiliser "parent.variable_name", sinon juste "variable_name"
        $key = !empty($parent) ? $parent . '.' . $varName : $varName;

        $tooltips[$key] = [
            'fr' => trim((string)$row->description_fr),
            'en' => trim((string)$row->description_en),
        ];
    }

    return $tooltips;
}

/* ================================ Fonctions gloabaux ================================================/

/**
 * Empêche plusieurs traitements pour le même utilisateur pendant $ttl secondes.
 * Retourne true si un verrou existe déjà (donc il FAUT stopper la suite).
 */
function nada_throttle_user(int $ttl = 20): bool
{
    // 1) Identité de l'appelant
    $user_id = get_current_user_id();

    // Si non connecté, on “approxime” l’utilisateur par IP + UA
    if (!$user_id) {
        $fingerprint = ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0') . '|' . ($_SERVER['HTTP_USER_AGENT'] ?? 'ua');
        $who = 'guest:' . md5($fingerprint);
    } else {
        $who = 'user:' . $user_id;
    }

    $lock_key = 'nada:throttle:' . $who;

    // 2) Verrou atomique via cache objet si dispo (Redis/Memcached)
    if (function_exists('wp_cache_add')) {
        // wp_cache_add() renvoie false si la clé existe déjà
        if (wp_cache_add($lock_key, 1, 'nada', $ttl) === false) {
            return true; // déjà verrouillé → on doit bloquer
        }
    }

    // 3) Filet de secours via transient (moins atomique, mais ok)
    if (get_transient($lock_key) !== false) {
        return true; // déjà verrouillé → on doit bloquer
    }
    set_transient($lock_key, 1, $ttl);

    return false; // pas de verrou : on peut continuer
}

/** Formatter une date */
function format_date_fr($date_iso, $pattern = 'd MMMM yyyy à HH\'h\'mm', $locale = 'fr_FR', $timezone = 'Europe/Paris')
{
    if (empty($date_iso)) {
        return '';
    }
    try {
        $date = new DateTime($date_iso);
        $date->setTimezone(new DateTimeZone($timezone));


        $formatter = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            $date->getTimezone()->getName(),
            IntlDateFormatter::GREGORIAN,
            $pattern
        );

        return $formatter->format($date);
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Formate une date selon la langue
 *
 * @param string $date_iso Date ISO (ex: "2026-02-09 11:07:00")
 * @param string $lang Code langue ('fr', 'en', 'es', etc.)
 * @param string $format_type Type de format : 'full', 'short', 'date_only', 'datetime'
 * @return string Date formatée
 */
function format_date($date_iso, $lang = 'fr', $format_type = 'datetime')
{
    if (empty($date_iso)) {
        return '';
    }

    try {
        $date = new DateTime($date_iso);
        // Configuration par langue
        $locale_config = [
            'fr' => [
                'locale' => 'fr_FR',
                'timezone' => 'Europe/Paris',
                'patterns' => [
                    'full' => 'd MMMM yyyy à HH\'h\'mm',
                    'short' => 'd MMM yyyy',
                    'date_only' => 'd MMMM yyyy',
                    'datetime' => 'd MMMM yyyy à HH\'h\'mm'
                ]
            ],
            'en' => [
                'locale' => 'en_US',
                'timezone' => 'Europe/Paris',
                'patterns' => [
                    'full' => 'MMMM d, yyyy \'at\' h:mm a',
                    'short' => 'MMM d, yyyy',
                    'date_only' => 'MMMM d, yyyy',
                    'datetime' => 'MMMM d, yyyy \'at\' h:mm a'
                ]
            ]
        ];

        // Langue par défaut si non supportée
        if (!isset($locale_config[$lang])) {
            $lang = 'en';
        }

        $config = $locale_config[$lang];
        $date->setTimezone(new DateTimeZone($config['timezone']));
        $pattern = $config['patterns'][$format_type] ?? $config['patterns']['datetime'];
        $formatter = new IntlDateFormatter(
            $config['locale'],
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            $date->getTimezone()->getName(),
            IntlDateFormatter::GREGORIAN,
            $pattern
        );

        return $formatter->format($date);
    } catch (Exception $e) {
        error_log("Date formatting error: " . $e->getMessage());
        return '';
    }
}

/** Retourne une instance unique de Nada_API */
function get_nada_api_instance(): Nada_API
{
    static $nada_api = null; // Une seule instance sera créée
    if ($nada_api === null) {
        require_once NADA_ID_PLUGIN_DIR . 'includes/api/class-nada-api.php';
        $nada_api = new Nada_API();
    }
    return $nada_api;
}

/** Retourne une instance unique de NADA_DeepL */
function get_nada_deepl_instance(): NADA_DeepL
{
    static $instance = null;

    if ($instance === null) {
        require_once NADA_ID_PLUGIN_DIR . 'includes/api/class-nada-deepl.php';
        $instance = new NADA_DeepL();
    }

    return $instance;
}

/****** Fonctions pour debogue : A supprimer aprés les developpements *****/
function nada_id_log($message)
{
    $log_dir = WP_PLUGIN_DIR . '/nada-id/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $filepath = $log_dir . '/debug.log';

    $time = date('Y-m-d H:i:s');
    file_put_contents($filepath, "[$time] $message\n", FILE_APPEND);
}


/**
 * Récupère la traduction d’une clé dans deux langues (FR et EN),
 * indépendamment de la langue active dans WordPress.
 */
function get_translated_value_in_both_languages(string $key): array
{
    $domain = 'nada-id';
    $languages_dir = NADA_ID_PLUGIN_DIR . 'languages/';

    $result = [
        'fr' => $key,
        'en' => $key,
    ];

    // --- FR ---
    $fr_mo = $languages_dir . 'nada-id-fr_FR.mo';
    if (file_exists($fr_mo)) {
        $mo = new MO();
        if ($mo->import_from_file($fr_mo)) {
            $translated = $mo->translate($key);
            if (!empty($translated)) {
                $result['fr'] = $translated;
            }
        }
    }

    // --- EN ---
    $en_mo = $languages_dir . 'nada-id-en_US.mo';
    if (file_exists($en_mo)) {
        $mo = new MO();
        if ($mo->import_from_file($en_mo)) {
            $translated = $mo->translate($key);
            if (!empty($translated)) {
                $result['en'] = $translated;
            }
        }
    }


    return $result;
}


add_filter('rest_authentication_errors', function ($result) {
    if (!empty($result)) {
        // Si une erreur est déjà retournée, on loggue le détail
        error_log('REST API bloquée : ' . print_r($result, true));
    } else {
        // Sinon, on loggue que rien ne bloque encore
        error_log('REST API : pas encore bloquée.');
    }
    return $result; // on ne change rien, juste on observe
}, 100);


// Sérialisation / Désérialisation d’un tableau simple en chaîne de caractères
function nada_parse_serialized_array($serialized_string)
{
    if (empty($serialized_string) || !is_string($serialized_string)) {
        return [];
    }

    $str = trim($serialized_string);

    if (!preg_match('/^\[\'(.*)\'(\])?$/', $str)) {
        return [];
    }

    $str = trim($str, "[]'");
    $items = preg_split("/','|','/", $str);

    $cleaned_items = [];
    foreach ($items as $item) {
        $item = trim($item, "'");
        $item = stripslashes($item);
        if (!empty($item)) {
            $cleaned_items[] = $item;
        }
    }

    return $cleaned_items;
}

// Sérialisation d’un tableau simple en chaîne de caractères
function nada_serialize_to_array_string($items)
{
    if (empty($items) || !is_array($items)) {
        return '';
    }

    $escaped_items = array_map(function ($item) {
        return addslashes($item);
    }, $items);

    return "['" . implode("','", wp_unslash($escaped_items)) . "']";
}

// Vérifie si un utilisateur WordPress existe dans NADA, sinon le crée
function check_existing_user_callback($user): void
{
    $user_info = get_userdata($user->ID);

    if (!$user_info) {
        return;
    }

    $user_id = $user->ID;
    $email = $user_info->user_email;
    $username = $user_info->user_login;

    $first_name = get_user_meta($user_id, 'first_name', true) ?: $username;
    $last_name = get_user_meta($user_id, 'last_name', true) ?: $username;

    $nada_api = get_nada_api_instance();

    // Limit length
    $username = substr($username, 0, 18);
    $first_name = substr($first_name, 0, 18);
    $last_name = substr($last_name, 0, 18);

    /**
     * STEP 1 — Check if email exists in NADA
     */
    $response = $nada_api->check_exist_user($email);

    if (is_wp_error($response)) {
        // optional: log error
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (
        isset($data['status'], $data['email_exists']) &&
        $data['status'] === 'success' &&
        $data['email_exists'] === true
    ) {
        // User already exists → do nothing
        return;
    }

    /**
     * STEP 2 — Create user in NADA
     */
    $data = [
        'email' => $email,
        'username' => $username,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'password' => get_default_password_nada(),
        'password_confirm' => get_default_password_nada(),
        'company' => '',
        'phone' => null,
        'country' => null,
        'active' => true,
        'role_id' => 2
    ];

    $response = $nada_api->createUser($data);

    if (
        isset($response['success'], $response['data']['user']['api_keys'][0]) &&
        $response['success'] === true
    ) {
        update_user_meta(
            $user_id,
            'nada_token',
            sanitize_text_field($response['data']['user']['api_keys'][0])
        );
        update_user_meta(
            $user_id,
            'nada_user_id',
            $response['data']['user']['user_id']
        );

        assign_nada_studies_to_pi($user_id, $email);
        assign_nada_studies_to_contributor($user_id, $email, $response['data']['user']['user_id']);
        return;
    }

    nada_id_log(
        'Erreur création utilisateur NADA pour user_id ' .
            $user_id . ' : ' . print_r($response, true)
    );
}

// Met à jour la description FR/EN d'un champ de métadonnée via AJAX 
function nada_update_vd_description_field()
{
    $lang = pll_current_language() ?? 'fr';

    global $wpdb;
    $table_name = $wpdb->prefix . 'nada_metadata_fields';
    // Valider les données reçues
    if (
        !isset($_POST['field_id']) || !intval($_POST['field_id'])
    ) {
        wp_send_json_error([
            'success' => false,
            'message' => $lang === 'fr' ? 'Données manquantes' : 'Missing data'
        ]);
    }
    // Sanitize et valider field_id
    $field_id = intval($_POST['field_id']);

    if ($field_id <= 0) {
        wp_send_json_error([
            'success' => false,
            'message' => $lang === 'fr' ? 'ID invalide' : 'Invalid ID'
        ]);
    }

    try {
        // Préparer les données à mettre à jour
        $data = [
            'description_fr' => sanitize_textarea_field(wp_unslash($_POST['description_fr'])),
            'description_en' => sanitize_textarea_field(wp_unslash($_POST['description_en'])),
            'updated_at' => current_time('mysql')
        ];

        $where = ['id' => $field_id];
        // Exécuter la mise à jour
        $result = $wpdb->update($table_name, $data, $where);

        // Vérifier le résultat
        if ($result === false) {
            throw new Exception('Erreur lors de la mise à jour');
        }
        // succès
        wp_send_json_success([
            'success' => true,
            'message' => $lang === 'fr' ? 'Description mise à jour avec succès.' : 'Description updated successfully.'
        ]);
    } catch (Exception $e) {
        // erreur
        wp_send_json_error([
            'success' => false,
            'error' => $e->getMessage() . ' (Erreur DB: ' . $wpdb->last_error . ')',
            'message' => $lang === 'fr' ? "Erreur lors de la mise à jour de la description." : "Error updating description."
        ]);
    }
}

// Récuperer wp user id a partir nada user_id
function fetch_wp_user_id($nada_user_id)
{

    global $wpdb;
    $wp_user_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT user_id
         FROM {$wpdb->usermeta}
         WHERE meta_key = %s
           AND meta_value = %s
         LIMIT 1",
            'nada_user_id',
            $nada_user_id
        )
    );

    return $wp_user_id;
}

function check_orcid_callback()
{
    // Vérifier que l'ORCID est fourni
    if (empty($_GET['orcid'])) {
        wp_send_json([
            'valid' => false,
            'error' => false,
            'message' => 'ORCID manquant'
        ]);
    }

    $orcid = sanitize_text_field($_GET['orcid']);

    // Valider le format de l'ORCID (0000-0000-0000-000X)
    if (!preg_match('/^\d{4}-\d{4}-\d{4}-\d{3}[0-9X]$/', $orcid)) {
        wp_send_json([
            'valid' => false,
            'error' => false,
            'message' => 'Format ORCID invalide'
        ]);
    }

    // Appeler l'API publique ORCID
    $api_url = get_nada_api_public_orcid_url();
    $url = "$api_url/$orcid/works";

    $response = wp_remote_get($url, [
        'headers' => [
            'Accept' => 'application/json'
        ],
        'timeout' => 15,
        'sslverify' => true
    ]);

    // Vérifier si la requête a échoué
    if (is_wp_error($response)) {
        error_log('ORCID API Error: ' . $response->get_error_message());
        wp_send_json([
            'valid' => false,
            'error' => true,
            'message' => 'Erreur de connexion à l\'API ORCID'
        ]);
    }

    $status_code = wp_remote_retrieve_response_code($response);
    // Vérifier le code de statut HTTP
    if ($status_code === 200) {
        wp_send_json(['valid' => true, 'error' => false]);
    } else {
        wp_send_json(['valid' => false, 'error' => false]);
    }
}

function email_get_translate_blocks(bool $translate_enabled): array
{
    $blocks = [
        'fr' => '',
        'en' => '',
    ];

    if ($translate_enabled === true) {
        $blocks['fr'] = '
            <p>
                Une nouvelle version linguistique de l’étude est désormais disponible.
            </p>
        ';

        $blocks['en'] = '
            <p>
               A new linguistic version of the study is now available.
            </p>
        ';
    }

    return $blocks;
}

function normalizeBooleanValue($value, string $lang)
{
    if ($value === '' || $value === null) return '';
    if (in_array($value, [true, "true", 1], true)) return $lang === 'fr' ? 'Oui' : 'Yes';
    if (in_array($value, [false, "false", 0], true)) return $lang === 'fr' ? 'Non' : 'No';
    if ($value === "Autre" || $value === "Other") return $lang === 'fr' ? 'Autre' : 'Other';
    return '';
}

function convertToBool($value)
{
    if (!is_string($value)) {
        return null; // ou false selon ton besoin
    }

    $trimValue = strtolower(trim($value));

    $trueValues = ['oui', 'yes'];
    $falseValues = ['non', 'no'];

    if (in_array($trimValue, $trueValues)) {
        return true;
    }

    if (in_array($trimValue, $falseValues)) {
        return false;
    }

    return $value; // valeur par défaut
}

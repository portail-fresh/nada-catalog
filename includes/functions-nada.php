<?php

/**
 * Fichier functions-nada.php
 *
 * Contient des fonctions utilitaires génériques (globaux) du plugin.
 * Ces fonctions ne devraient pas dépendre directement de WordPress
 * (mais peuvent l’utiliser si nécessaire).
 */

// Sécurité
if (! defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'nada-payload.php';
require_once plugin_dir_path(__FILE__) . 'nada-payload-import.php';
require_once plugin_dir_path(__FILE__) . 'import-json.php';

add_action('user_registration_after_register_user_action', 'nada_create_user_after_ur_register', 10, 3);
add_action('wp_ajax_nada_save_study', 'nada_save_study_callback');
add_action('wp_ajax_nada_import_study', 'nada_import_study_callback');
add_action('wp_ajax_nada_delete_study', 'nada_delete_study_callback');
add_action('wp_ajax_nada_load_catalogs', 'nada_load_catalogs_callback'); // wp_ajax_nopriv_{action} → réservé aux utilisateurs non connectés
add_action('wp_ajax_nopriv_nada_load_catalogs', 'nada_load_catalogs_callback');  // wp_ajax_{action} → réservé aux utilisateurs connectés
add_action('wp_ajax_publish_unpublish_study', 'publish_unpublish_study_callback');
add_action('wp_ajax_make_decision_study', 'make_decision_study_callback');
add_action('wp_ajax_request_changes_study', 'request_changes_study_callback');
add_action('wp_ajax_reject_study', 'reject_study_callback');
add_action('wp_ajax_nada_load_facets', 'nada_load_facets');
add_action('wp_ajax_nopriv_nada_load_facets', 'nada_load_facets');



/* =================================== Hooks ================================================/

/** Crée un utilisateur NADA lors de l'inscription WP */
function nada_create_user_after_ur_register($valid_form_data, $form_id, $user_id): void
{
    // Récupérer les informations WP
    $user_info = get_userdata($user_id);
    $email     = $user_info->user_email;
    $username  = $user_info->user_login;

    $first_name = get_user_meta($user_id, 'first_name', true) ?: $username;
    $last_name  = get_user_meta($user_id, 'last_name', true)  ?: $username;

    // Récupérer l'instance unique de Nada_API
    $nada_api = get_nada_api_instance();

    // Récuperer mot de passe par défaut
    $nada_default_password = get_option('nada_id_default_password', null);

    // Préparer les données utilisateur
    $data = [
        'email'            => $email,
        'username'         => $username,
        'first_name'       => $first_name,
        'last_name'        => $last_name,
        'password'         => $nada_default_password,
        'password_confirm' => $nada_default_password,
        'company'          => "",
        'phone'            => null,
        'country'          => null,
        'active'           => true,
        'role_id'          => 2
    ];

    // Créer l'utilisateur NADA
    $response = $nada_api->create_user($data);

    // Stocker le token NADA en meta utilisateur WP
    if ($response['success'] && !empty($response['data']['user']['api_keys'][0])) {
        update_user_meta($user_id, 'nada_token', sanitize_text_field($response['data']['user']['api_keys'][0]));
        assign_nada_studies_to_pi($user_id, $email);
    } else {
        wp_delete_user($user_id);
        nada_id_log('Erreur création utilisateur NADA pour user_id ' . $user_id . ': ' . print_r($response, true));
    }
}

/* ================================ Ajax Callback ================================================/

/** Sauvegarde une étude fr/en */
function nada_save_study_callback(): void
{

    $nada_api = get_nada_api_instance();
    $newId    = 0;

    $postData  = $_POST;

    $result    = [
        'fr' => [],
        'en' => [],
    ];

    // Parcourir chaque clé envoyée et séparer par langue
    foreach ($postData as $key => $value) {
        if (preg_match('/^(.*)_(fr|en)$/', $key, $matches)) {
            $baseKey     = $matches[1]; // ex: stdyDscr/citation/titlStmt/titl
            $lang        = $matches[2]; // fr ou en
            if ($baseKey == 'stdyDscr/stdyInfo/subject/keyword') {
                // ex de $value: "test; another"
                // Séparer par ";", et nettoyer les espaces
                $keywordsArray = array_map('trim', explode(';', $value));

                // Construire le tableau "keywords"
                $keywords = [];
                foreach ($keywordsArray as $k) {
                    if ($k !== '') { // éviter les valeurs vides
                        $keywords[] =  (object) ["keyword" => wp_unslash($k)];
                    }
                }
                $result[$lang]['keywords'] =  $keywords;
            } else {
                $result[$lang][$baseKey] = wp_unslash($value);
            }
        } else if ($key == "complexData") {
            $complexData = json_decode(stripslashes($value), true);
            $result['fr']['fundingAgencies'] = $complexData['fundingAgencies_fr'];
            $result['en']['fundingAgencies'] = $complexData['fundingAgencies_en'];
            $result['fr']['fundingAgentTypes'] = $complexData['fundingAgentTypes_fr'];
            $result['en']['fundingAgentTypes'] = $complexData['fundingAgentTypes_en'];
            $result['fr']['callModes'] = $complexData['callModes_fr'];
            $result['en']['callModes'] = $complexData['callModes_en'];
            $result['fr']['agencies'] = [
                "agency" => array_map(
                    fn($agency) => ["name" => wp_unslash($agency)],
                    $complexData['agencies_fr'] ?? []
                )
            ];
            $result['en']['agencies'] = [
                "agency" => array_map(
                    fn($agency) => ["name" => wp_unslash($agency)],
                    $complexData['agencies_en'] ?? []
                )
            ];

            $result['en']['otherAgencies'] = $complexData['otherAgencies_en'];
            $result['fr']['otherAgencies'] = $complexData['otherAgencies_fr'];
            $result['en']['authEntities'] = $complexData['authEntities_en'];
            $result['fr']['authEntities'] = $complexData['authEntities_fr'];
            $result['en']['othIds'] = $complexData['othIds_en'];
            $result['fr']['othIds'] = $complexData['othIds_fr'];


            $result['en']['contacts'] = $complexData['distContacts_en'];
            $result['fr']['contacts'] = $complexData['distContacts_fr'];
            $result['en']['topics'] = $complexData['topics_en'];
            $result['fr']['topics'] = $complexData['topics_fr'];


            // Interventions
            $result['en']['interventions'] = $complexData['interventions_en'];
            $result['fr']['interventions'] = $complexData['interventions_fr'];

            // recrutementTiming
            $result['en']['recrutementTiming'] = $complexData['recrutementTiming_en'];
            $result['fr']['recrutementTiming'] = $complexData['recrutementTiming_fr'];


            // Research Purposes
            $result['en']['researchPurposes'] = $complexData['researchPurposes_en'];
            $result['fr']['researchPurposes'] = $complexData['researchPurposes_fr'];

            // Trial Phases
            $result['en']['trialPhases'] = $complexData['trialPhases_en'];
            $result['fr']['trialPhases'] = $complexData['trialPhases_fr'];

            // Blinded Masking Details
            $result['en']['blindedMaskingDetails'] = $complexData['blindedMaskingDetails_en'];
            $result['fr']['blindedMaskingDetails'] = $complexData['blindedMaskingDetails_fr'];

            // Inclusion Strategy
            $result['en']['inclusionStrategy']   = $complexData['inclusionStrategy_en'];
            $result['fr']['inclusionStrategy']   = $complexData['inclusionStrategy_fr'];

            // Sampling Procedure
            $result['en']['sampProc'] = empty($complexData['sampProc_en']) ? '' : "['" . implode("','", array_values($complexData['sampProc_en'])) . "']";
            $result['fr']['sampProc'] = empty($complexData['sampProc_fr']) ? '' : "['" . implode("','", array_values($complexData['sampProc_fr'])) . "']";

            // Unit Type
            $result['en']['unitType'] = empty($complexData['unitType_en']) ? '' : "['" . implode("','", array_values($complexData['unitType_en'])) . "']";
            $result['fr']['unitType'] = empty($complexData['unitType_fr']) ? '' : "['" . implode("','", array_values($complexData['unitType_fr'])) . "']";

            // Age Inclusion Level I
            $result['en']['level_age_Clusion_I'] = $complexData['level_age_Clusion_I_en'];
            $result['fr']['level_age_Clusion_I'] = $complexData['level_age_Clusion_I_fr'];

            // FR
            $result['fr']['nation'] = array_map(
                fn($code) => [
                    'name' => (string) $code,
                    'abbreviation' => (string) $code,
                ],
                (array) ($complexData['nation_fr'] ?? [])
            );

            // EN
            $result['en']['nation'] = array_map(
                fn($code) => [
                    'name' => (string) $code,
                    'abbreviation' => (string) $code,
                ],
                (array) ($complexData['nation_en'] ?? [])
            );


            // Geographic Coverage
            $result['en']['geogCover'] = empty($complexData['geogCover_en']) ? '' : "['" . implode("','", array_values($complexData['geogCover_en'])) . "']";
            $result['fr']['geogCover'] = empty($complexData['geogCover_fr']) ? '' : "['" . implode("','", array_values($complexData['geogCover_fr'])) . "']";

            // Subject Follow-Up
            $result['en']['subject_followUP'] = $complexData['subject_followUP_en'];
            $result['fr']['subject_followUP'] = $complexData['subject_followUP_fr'];

            // Sources
            $sourcesFr = $complexData['sources_fr'];
            $sourcesEn =  $complexData['sources_en'];
            $result['en']['sources'] = $sourcesEn;
            $result['fr']['sources']  = $sourcesFr;

            // Récupère tous les srcOrig, fusionne et enlève les doublons
            $uniqueSrcOrigFr = array_values(array_unique(array_merge(...array_column($sourcesFr, 'srcOrig'))));
            $uniqueSrcOrigEn = array_values(array_unique(array_merge(...array_column($sourcesEn, 'srcOrig'))));

            //var_export($uniqueSrcOrigFr);

            // Sources origines
            $result['en']['sourcesOrigines'] = $uniqueSrcOrigEn;
            $result['fr']['sourcesOrigines'] = $uniqueSrcOrigFr;

            // Sex Inclusion Level I
            $result['en']['level_sex_Clusion_I'] = $complexData['level_sex_Clusion_I_en'];
            $result['fr']['level_sex_Clusion_I'] = $complexData['level_sex_Clusion_I_fr'];

            //standardsCompliance
            $result['en']['standardsCompliance'] = empty($complexData['standardsCompliance_en']) ? '' : "['" . implode("','", array_values($complexData['standardsCompliance_en'])) . "']";
            $result['fr']['standardsCompliance'] = empty($complexData['standardsCompliance_fr']) ? '' : "['" . implode("','", array_values($complexData['standardsCompliance_fr'])) . "']";

            //otherQualityStatements
            $result['en']['otherQualityStatements'] = empty($complexData['otherQualityStatements_en']) ? '' : "['" . implode("','", array_values($complexData['otherQualityStatements_en'])) . "']";
            $result['fr']['otherQualityStatements'] = empty($complexData['otherQualityStatements_fr']) ? '' : "['" . implode("','", array_values($complexData['otherQualityStatements_fr'])) . "']";


            //useStmtContacts
            $result['en']['useStmtContacts'] = empty($complexData['useStmtContacts_en']) ? '' : "['" . implode("','", array_values($complexData['useStmtContacts_en'])) . "']";
            $result['fr']['useStmtContacts'] = empty($complexData['useStmtContacts_fr']) ? '' : "['" . implode("','", array_values($complexData['useStmtContacts_fr'])) . "']";

            //idnos
            $result['en']['idnos'] = $complexData['idnos_en'];
            $result['fr']['idnos'] = $complexData['idnos_fr'];

            //biobankContent
            $result['en']['biobankContent'] = $complexData['biobankContent_en'];
            $result['fr']['biobankContent'] = $complexData['biobankContent_fr'];

            //dataKind
            $result['en']['dataKind'] = empty($complexData['dataKind_en']) ? '' : "['" . implode("','", array_values($complexData['dataKind_en'])) . "']";
            $result['fr']['dataKind'] = empty($complexData['dataKind_fr']) ? '' : "['" . implode("','", array_values($complexData['dataKind_fr'])) . "']";

            //inclusionGroups
            $result['en']['inclusionGroups'] = $complexData['inclusionGroups_en'];
            $result['fr']['inclusionGroups'] = $complexData['inclusionGroups_fr'];

            //arms
            $result['en']['arms'] = $complexData['arms_en'];
            $result['fr']['arms'] = $complexData['arms_fr'];
        } else {
            // Cas sans suffixe -> copier dans les deux langues
            $result['fr'][$key] = wp_unslash($value);
            $result['en'][$key] = wp_unslash($value);
        }
    }

    // --- Gestion du mode & baseIdno ---
    $idnoInput = $postData['idno'] ?? '';

    $mode = empty($idnoInput) ? 'add' : 'edit';

    if ($mode === 'add') {
        $newId    = getSurveyNewId();
        $baseIdno = "FReSH-$newId";
    } else {
        // Ex: FReSH-630-fr -> on récupère "FReSH-630"
        $baseIdno = preg_replace('/-[a-z]{2}$/', '', $idnoInput);;
        $newId = (int) filter_var($baseIdno, FILTER_SANITIZE_NUMBER_INT);
    }

    // verification si le utilisateur connecte est Pi:  si oui:respValidation= true sinon =''
    $respValidation = '';

    if (isset($postData['creatorIsPi_fr']) && $postData['creatorIsPi_fr'] === 'true') {
        $respValidation = 'true';
    }

    // Boucle pour FR et EN
    foreach ($result as $lang => $langData) {
        $idno = "$baseIdno-$lang";   // ici on fixe fr ou en

        // oui / non pour le facette
        $contactEmail =  check_contacts_has_email($langData['contacts'], $lang);
        // Préparer le payload Nada
        $study = prepare_nada_payload($langData, $lang, $idno, $mode, $respValidation, $contactEmail);

        // Envoyer à l'API
        $response = '';
        if ($mode === 'add') {
            $response = $nada_api->create_study(json_encode($study));
        } else {
            $response = $nada_api->update_study($idno, json_encode($study));
        }
        $responses[$lang] = $response;

        // Si une langue échoue, arrêter
        if (empty($response['success']) || $response['success'] != 1 || ($response['data']['status'] ?? '') === 'failed') {
            wp_send_json([
                'success'  => false,
                'message'  => $response['data']['message'] ?? 'Erreur inconnue',
                'error'    => $response,
                'response' => $responses,
            ]);
        }
    }

    // en cas le PI n'est renseigné, provisoirement mettere le user courant pour qu'on tombe pas 
    // dans le cas d'une etude sans PI en attendant le champs PI Soit obligatoire
    $creatorIsPi = isset($postData['creatorIsPi_fr']) ? $postData['creatorIsPi_fr'] : 'true';

    // Gestion PI (si nécessaire)
    $PI_data = [
        "pi_email"       => sanitize_email($postData['pi-email'] ?? ''),
        "pi_is_current_user" => $creatorIsPi == 'true' ? true : false,
        "study_idno"     => $baseIdno,
        "study_id_fr"    => $responses['fr']['data']['dataset']['id'] ?? '',
        "study_id_en"    => $responses['en']['data']['dataset']['id'] ?? '',
        "status" => $postData['published'] == "0" ? "draft" : "pending"
    ];

    $pi_result = saveStudy($PI_data, $responses['fr']['data']['dataset']['title']);


    if (!$pi_result['success']) {
        wp_send_json([
            'success' => false,
            'message' => 'Erreur lors de l’ajout du PI : ' . $pi_result['message']
        ]);
    }

    // Tout est OK
    wp_send_json([
        'success'  => true,
        'message'  => 'Études sauvegardées avec succès !',
        'response' => $responses,
    ]);
}

function check_contacts_has_email(array $contacts, $lang): string
{
    // 1) le tableau doit exister et ne pas être vide
    if (empty($contacts) || !is_array($contacts)) {
        return $lang === 'fr' ? 'Non' : 'No';
    }
    // 2) au moins un élément de type 'contact' avec un email valide
    foreach ($contacts as $item) {
        if (!empty($item['type']) && $item['type'] === 'contact') {
            if (!empty($item['email']) && filter_var($item['email'], FILTER_VALIDATE_EMAIL)) {
                return  $lang === 'fr' ? 'Oui' : 'Yes';
            }
        }
    }

    return $lang === 'fr' ? 'Non' : 'No';
}

/** Suppression une étude fr/en Coté Nada */
function nada_delete_study_callback(): void
{
    $study_idno = $_POST['study_idno'];
    if (!$study_idno) {
        wp_send_json_error(['message' => 'ID invalide']);
    }

    // Extraire la partie commune avant -FR ou -EN
    $base_idno = get_base_idno($study_idno);
    $nada_api = get_nada_api_instance();
    $response_fr = $nada_api->delete_study($base_idno . '-FR');

    if (!$response_fr['success'] || $response_fr['data']['status'] != 'success') {
        wp_send_json([
            'success'  => false,
            'message'  => 'Quelque chose qui va pas bien !',
            'response' => "KO"
        ]);
    }

    $response_en = $nada_api->delete_study($base_idno . '-EN');

    if (!$response_en['success'] || $response_en['data']['status'] != 'success') {
        wp_send_json([
            'success'  => false,
            'message'  => "L'étude française a été supprimée, mais la suppression de l'étude anglaise a échoué",
            'response' => "KO"
        ]);
    }

    // Suppression Coté wp
    $is_deleted = delete_nada_study_from_wp($base_idno);

    if (!$is_deleted) {
        wp_send_json([
            'success'  => false,
            'message'  => 'La suppression a échoué côté WP!',
            'response' => "KO"
        ]);
    }

    // Si tout est OK
    wp_send_json([
        'success'  => true,
        'message'  => 'Etude supprimé avec succès !',
        'response' => "OK"
    ]);
}

/** Publier/Dépublier une étude */
function publish_unpublish_study_callback(): void
{
    $study_idno = $_POST['study_idno'];
    if (!$study_idno) {
        wp_send_json_error(['message' => 'ID invalide']);
    }

    // Extraire la partie commune avant -FR ou -EN
    $base_idno = get_base_idno($study_idno);

    $published = $_POST['published'];

    $nada_api = get_nada_api_instance();
    $data = [
        "published" => $published
    ];

    $response_fr = $nada_api->update_study_field($base_idno . '-FR', $data);

    if (!$response_fr['success'] || $response_fr['data']['status'] != 'success') {
        wp_send_json([
            'success'  => false,
            'message'  => 'Quelque chose qui va pas bien !',
            'response' => "KO"
        ]);
    }
    $response_en = $nada_api->update_study_field($base_idno . '-EN', $data);

    if (!$response_en['success'] || $response_en['data']['status'] != 'success') {
        wp_send_json([
            'success'  => false,
            'message'  => "L'étude française a été publiée/dépublié, mais la modification de l'étude anglaise a échoué",
            'response' => "KO"
        ]);
    }

    $data = [
        'status' =>  $published == 1 ? 'published' : 'unpublished'
    ];

    $response = update_nada_study_wp($base_idno,  $data);
    if (!$response) {
        wp_send_json([
            'success'  => false,
            'message'  => "Probléme lors de la publication d'une étude",
            'response' => "KO"
        ]);
    }

    $study = get_details_study_from_wp($base_idno);
    if ($study) {
        $current_user    = wp_get_current_user();
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
            $mails[] = $contributor->user_email;
        }
        // Envoi mail de notification
        if (!empty($mails)) {
            $study_title = sanitize_text_field($_POST['study_title']);

            if ($published == 1) {
                $subject = "Publication de votre étude";
                $message = "Bonjour,\n\n" .
                    "L’étude « {$study_title} » a été publiée avec succès et est désormais disponible.\n\n" .
                    "Merci pour votre contribution.\n\n" .
                    "L’équipe Fresh";
            } else {
                $subject = "Dépublication de votre étude";
                $message = "Bonjour,\n\n" .
                    "L’étude « {$study_title} » a été retirée de la publication.\n\n" .
                    "Pour plus d’informations, vous pouvez contacter le service Fresh.\n\n" .
                    "Merci de votre compréhension.\n\n" .
                    "L’équipe Fresh";
            }

            wp_mail($mails, $subject, $message);
        }
    }


    // Si tout est OK
    wp_send_json([
        'success'  => true,
        'message'  => 'Etude modifié avec succès !',
        'response' => "OK"
    ]);
}

/** Approuver/Désapprouver une étude + mise a jour champs respValidation*/
function make_decision_study_callback(): void
{
    $study_idno = $_POST['study_idno'];
    if (!$study_idno) {
        wp_send_json_error(['message' => 'ID invalide']);
    }

    // Extraire la partie commune avant -FR ou -EN
    $base_idno = get_base_idno($study_idno);
    $decision = $_POST['decision'];

    // Préparer les données à mettre à jour
    $data = [
        'is_approved' => $decision == 'approve' ? 1 : 0,
        'approved_at' => current_time('mysql', 1), // date/heure actuelle UTC
    ];

    $response = update_nada_study_wp($base_idno,  $data);

    if (!$response) {
        wp_send_json([
            'success'  => false,
            'message'  => "Probléme lors de la prise décision",
            'response' => "KO"
        ]);
    }
    //Debut mise a jour field additional/RespValidation  
    $nada_api = get_nada_api_instance();
    $data = [
        'additional' => [
            'respValidation' => 'true'
        ]
    ];
    $responseRespValidation = $nada_api->update_study($study_idno, json_encode($data));
    if (!$responseRespValidation) {
        wp_send_json([
            'success'  => false,
            'message'  => "Probléme lors de la validation d'etude",
            'response' => "KO"
        ]);
    }
    //Fin mise a jour field RespValidation 

    // envoie email 
    $study = get_details_study_from_wp($base_idno); // to optimise


    if ($study) {
        $current_user   = wp_get_current_user();
        $pi_email       = $study['pi_email'] ?? null;
        $contributor_id = $study['created_by'] ?? null;

        $mails = [];

        //  Si le PI fait l’action # à le contributeur
        if ($pi_email && $contributor_id != $pi_email) {
            //  Ajouter le contributeur si différent du PI
            $contributor = get_user_by('id', $contributor_id);
            if ($contributor && $contributor->user_email) {
                $mails[] = $contributor->user_email;
            }
        }

        // Ajouter tous les admins (sauf le pi qui a fait l'action et le contributeru si il est un admin déja)
        $admins = get_users(['role' => 'admin_fresh']);
        foreach ($admins as $admin) {
            if ($admin->user_email !== $pi_email && $admin->user_email != $current_user->user_email) {
                $mails[] = $admin->user_email;
            }
        }

        // Envoi seulement si on a des destinataires
        if (!empty($mails)) {
            $study_title = sanitize_text_field($_POST['study_title']);

            if ($decision == 'approve') {
                $subject = "Étude approuvée";
                $message = "Bonjour,\n\n" .
                    "L’étude « {$study_title} » a été approuvée avec succès.\n\n" .
                    "Merci pour votre contribution.\n\n" .
                    "L’équipe Fresh";
            } else {
                $subject = "Étude désapprouvée";
                $message = "Bonjour,\n\n" .
                    "L’étude « {$study_title} » a été désapprouvée de la publication.\n\n" .
                    "Pour plus d’informations, vous pouvez contacter le service Fresh.\n\n" .
                    "Merci de votre compréhension.\n\n" .
                    "L’équipe Fresh";
            }

            // Supprimer doublons (au cas où un email apparaît deux fois)
            $mails = array_unique($mails);

            wp_mail($mails, $subject, $message);
        }
    }


    // Si tout est OK
    wp_send_json([
        'success'  => true,
        'message'  => 'Décision prise avec succès !',
        'response' => "OK"
    ]);
}

/** Modifications demandées une étude */
function request_changes_study_callback(): void
{
    $study_idno = $_POST['study_idno'];
    if (!$study_idno) {
        wp_send_json_error(['message' => 'ID invalide']);
    }

    // Extraire la partie commune avant -FR ou -EN
    $base_idno = get_base_idno($study_idno);

    // Préparer les données à mettre à jour
    $data = [
        'status' => 'changes_requested'
    ];

    $response = update_nada_study_wp($base_idno,  $data);

    if (!$response) {
        wp_send_json([
            'success'  => false,
            'message'  => "Probléme lors de la demande des modifcations d'une etude",
            'response' => "KO"
        ]);
    }

    // Notifier 
    // notification mail générique indiquant que la fiche nécessite des modifications pour pouvoir être publiée au contributeur et PI (pour plus d’info, contacter le service FReSH).
    $study = get_details_study_from_wp($base_idno);
    if ($study) {
        $current_user    = wp_get_current_user();
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
            $mails[] = $contributor->user_email;
        }
        // Envoi mail de notification
        if (!empty($mails)) {
            $study_title = sanitize_text_field($_POST['study_title']);
            $subject = "Modifications demandées sur une étude";
            $message = "Bonjour,\n\n" .
                "L’étude « {$study_title} » nécessite des modifications avant de pouvoir être publiée.\n\n" .
                "Connectez-vous à votre compte pour appliquer les corrections.\n\n" .
                "Pour plus d’info, contacter le service FReSH.\n\n" .
                "Merci,\n" .
                "L’équipe FReSH";

            wp_mail($mails, $subject, $message);
        }
    }

    // Si tout est OK
    wp_send_json([
        'success'  => true,
        'message'  => 'Modifications demandées avec succès !',
        'response' => "OK"
    ]);
}

/** Rejeter une étude */
function reject_study_callback(): void
{
    $study_idno = $_POST['study_idno'];
    if (!$study_idno) {
        wp_send_json_error(['message' => 'ID invalide']);
    }

    // Extraire la partie commune avant -FR ou -EN
    $base_idno = get_base_idno($study_idno);

    // Préparer les données à mettre à jour
    $data = [
        'status' => 'rejected'
    ];

    $response = update_nada_study_wp($base_idno,  $data);

    if (!$response) {
        wp_send_json([
            'success'  => false,
            'message'  => "Probléme lors de rejet d'une etude",
            'response' => "KO"
        ]);
    }

    // Notifier 
    // notification mail générique indiquant que la fiche nécessite des modifications pour pouvoir être publiée au contributeur et PI (pour plus d’info, contacter le service FReSH).
    $study = get_details_study_from_wp($base_idno);
    if ($study) {
        $current_user    = wp_get_current_user();
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
            $mails[] = $contributor->user_email;
        }
        // Envoi mail de notification
        if (!empty($mails)) {
            $study_title = sanitize_text_field($_POST['study_title']);
            $subject = "Rejet d'une étude";
            $message = "Bonjour,\n\n" .
                "L’étude « {$study_title} » a été rejetée suite à son évaluation.\n\n" .
                "Vous pouvez contacter le service Fresh pour obtenir plus d’informations concernant ce rejet.\n\n" .
                "Merci de votre compréhension.\n\n" .
                "L’équipe Fresh";

            wp_mail($mails, $subject, $message);
        }
    }

    // Si tout est OK
    wp_send_json([
        'success'  => true,
        'message'  => 'Modifications demandées avec succès !',
        'response' => "OK"
    ]);
}

/** Récuperer la liste des catalogues */
function nada_load_catalogs_callback()
{
    try {
        $found = null;
        $total = null;
        $html  = '';

        // Récupérer les paramètres existants
        // $filtres = sanitize_text_field($_POST['filtres'] ?? ''); 
        $filtres = $_POST['filtres'] ?? '';
        $search = sanitize_text_field($_POST['search'] ?? '');

        // Récupérer les nouveaux paramètres de tri et limite
        $sortBy = sanitize_text_field($_POST['sortBy'] ?? '');
        $sortOrder = sanitize_text_field($_POST['sortOrder'] ?? 'desc');
        $limit = sanitize_text_field($_POST['limit'] ?? '10');
        $lang = sanitize_text_field($_POST['lang'] ?? pll_current_language());

        // Mapping des valeurs de tri pour l'API NADA
        $sortMapping = [
            'created' => 'created',
            'title' => 'title',
            '' => 'created'
        ];
        // Convertir le sortBy selon le mapping
        $apiSortBy = $sortMapping[$sortBy] ?? 'created';

        // Appeler l'API avec les nouveaux paramètres
        $api =  get_nada_api_instance();

        // "-1" : recuperation tous les etudes 
        $response = $api->get_catalogs($filtres, $search, $apiSortBy, $sortOrder, "-1", $lang);

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

        $data  = $response['data']['result']['rows'];
        $found = $response['data']['result']['found'] ?? 0;
        $found = count(value: $data); // recalculé après filtrage
        $total = $response['data']['result']['found'] ?? 0;
        $per_page = $limit ?? 0;

        $empty_data = __('emptyCatalogData', 'nada-id');

        if (empty($data)) {
            wp_send_json_success([
                'html' => '<div class="nada-id-empty">' . esc_html($empty_data) . '</div>',
                'response' => $response,
                'filtres' => $filtres,
            ]);
        }

        // Capture du template
        ob_start();
        $template_path = NADA_ID_PLUGIN_DIR . 'public/templates/list-catalogs-data.php';

        if (file_exists($template_path)) {
            // Rendre $data, $found, $total disponibles dans le template
            include $template_path;
        } else {
            echo '<div class="nada-id-error">Le template HTML est manquant.</div>';
        }

        $html = ob_get_clean();

        wp_send_json_success([
            'html'  => $html,
            'found' => $found,
            'per_page' => $per_page,
            'total' => $total,
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['html' => 'Erreur serveur : ' . $e->getMessage()]);
    }
}


/** Récuperer la liste des facets */
function nada_load_facets()
{
    try {

        // Déterminer la langue depuis l'AJAX ou fallback
        $lang = isset($_POST['lang']) ? sanitize_text_field($_POST['lang']) : pll_current_language();

        // Récupérer l'instance de l'API
        $api = get_nada_api_instance();

        // Initialiser les données pour les facets
        $data_ID      = [];

        // Facets dynamiques (custom) pour FR et EN
        $facet_ID_fr = $api->get_facet_custom('fr');
        if ($facet_ID_fr['success'] && $facet_ID_fr['data']['status'] === 'success') {
            $data_ID['fr'] = $facet_ID_fr['data'];
        }

        $facet_ID_en = $api->get_facet_custom('en');
        if ($facet_ID_en['success'] && $facet_ID_en['data']['status'] === 'success') {
            $data_ID['en'] = $facet_ID_en['data'];
        }

        // Bufferiser le template
        ob_start();

        if ($lang === 'en') {
            include NADA_ID_PLUGIN_DIR . 'public/templates/partials/facets-en.php';
        } else {
            include NADA_ID_PLUGIN_DIR . 'public/templates/partials/facets-fr.php';
        }

        $html = ob_get_clean();

        // Retourner le HTML via AJAX
        wp_send_json_success([
            'html'    => $html,
            'data_ID' => $data_ID, // <-- envoyé en plus
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['html' => 'Erreur serveur : ' . $e->getMessage()]);
    }
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
        return  0;
    }
}

/** Création ou mise à jour d'une étude + PI + status */
function saveStudy(array $data, ?string $studyTitle): array
{
    global $wpdb;
    $table_name      = $wpdb->prefix . "nada_list_studies";
    $current_user    = wp_get_current_user();
    $current_user_id = get_current_user_id();

    // Vérifier si l’étude existe déjà
    $study = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_name WHERE nada_study_idno = %s", $data['study_idno']),
        ARRAY_A
    );

    // Déterminer PI
    [$pi_id, $pi_email] = resolvePI($data, $current_user);

    // Récupérer le nouveau statut
    $status = $data['status'];

    // Récupérer l'ancien statut
    $old_status = $study['status'];

    if (in_array($old_status, ['published', 'unpublished'])) {
        $status = $old_status;
    }

    /** Création */
    if (!$study) {
        $insert_data = [
            'nada_study_idno'  => $data['study_idno'],
            'nada_study_id_fr' => $data['study_id_fr'],
            'nada_study_id_en' => $data['study_id_en'],
            'status'           => $status,
            'is_approved'  => $pi_id == $current_user_id ? true : null,
            'pi_id'            => $pi_id,
            'pi_email'         => $pi_email,
            'created_by'       => $current_user_id,
            'created_at'       => current_time('mysql'),
        ];

        $result = $wpdb->insert($table_name, $insert_data);
        if ($result === false) {
            return ['success' => false, 'message' => 'Erreur lors de la création'];
        }

        // Notifications création => on simule "status_changed"
        $events = [
            'status_changed' => [null, $status],
            'pi_changed'     => $pi_email
        ];
        handleStudyNotifications($status, $events, $current_user, $pi_email, $current_user_id, $studyTitle);

        return ['success' => true, 'message' => 'Étude créée avec succès'];
    }


    /** Mise à jour */
    $updates = [];
    $events  = [];

    // Changement de statut ?
    if ($status !== $study['status']) {
        $updates['status'] = $status;
        $events['status_changed'] = [$study['status'], $status];
    }

    // Changement de PI ?
    if ($pi_id != $study['pi_id'] || $pi_email !== $study['pi_email']) {
        $updates['pi_id']    = $pi_id;
        $updates['pi_email'] = $pi_email;
        $events['pi_changed'] = $pi_email;
    }

    $updates['updated_at'] = current_time('mysql');
    $updates['updated_by'] = $current_user_id;

    if (!empty($updates)) {

        $result = $wpdb->update(
            $table_name,
            $updates,
            ['nada_study_idno' => $data['study_idno']]
        );

        if ($result === false) {
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
        }

        if (in_array($status, ['pending', 'changes_requested', 'published', 'unpublished']) && $old_status != 'draft') {
            $events['modified_pending'] = true;
        }

        // Notifications mise à jour
        handleStudyNotifications($status, $events, $current_user, $pi_email, $study['created_by'], $studyTitle);

        return ['success' => true, 'message' => 'Étude mise à jour'];
    }

    return ['success' => true, 'message' => 'Aucune modification détectée'];
}


/**
 * Détermine quel utilisateur est PI (Investigateur Principal) pour une étude.
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
function handleStudyNotifications(string $status, array $events, WP_User $current_user, ?string $pi_email = null, $contributor_id = null, ?string $studyTitle): void
{
    if ($status == 'draft') {
        return;
    }

    $admins = get_users(['role__in' => ['admin_fresh'], 'fields' => ['user_email']]);
    $emails_admins = array_filter(array_map('sanitize_email', wp_list_pluck($admins, 'user_email')));

    $is_contibutor_admin = in_array($current_user->user_email, $emails_admins);

    // Email L'email de l'utilisateur conencté s'il est admin
    $email_exclu = sanitize_email($current_user->user_email);

    // Supprimer l'email s'il existe dans le tableau
    $emails_admins = array_filter($emails_admins, function ($email) use ($email_exclu) {
        return $email !== $email_exclu;
    });

    if (isset($events['status_changed'])) {
        //Envoi mail aux administrateurs
        wp_mail(
            $emails_admins,
            "Nouvelle étude soumise",
            "Bonjour,\n\nLe/la contributeur·rice {$current_user->display_name} vient de soumettre une étude « {$studyTitle} » . Connectez-vous à votre compte pour l'examiner.\n\nMerci.\n\nL’équipe Fresh."
        );

        //mail au contributeur pour l'informer de la bonne réception de la contribution
        // Contributeur = PI
        if ($current_user->user_email === $pi_email || $is_contibutor_admin) {
            wp_mail(
                $current_user->user_email,
                "Votre demande a été soumise",
                "Bonjour {$current_user->display_name},\n\nVotre demande « {$studyTitle} » a bien été soumise aux administrateurs de la plateforme FReSH. Elle sera examinée prochainement.\n\nMerci pour votre contribution.\n\nL’équipe Fresh."
            );
        } else {
            // Contributeur != PI
            wp_mail(
                $current_user->user_email,
                "Votre demande a été soumise",
                "Bonjour {$current_user->display_name},\n\nNous avons bien reçu votre demande « {$studyTitle} ». L’Investigateur Principal a été informé et votre soumission sera également examinée par les administrateurs de la plateforme FReSH.\n\n
                Merci pour votre contribution.\n\n
                L’équipe Fresh\n\n."
            );
        }

        // Mail au PI : si différent du contributeur
        if (!empty($pi_email) && $pi_email !== $current_user->user_email) {
            $user = get_user_by('email', $pi_email);
            if ($user) {
                wp_mail(
                    $pi_email,
                    "Nouvelle étude à examiner",
                    "Bonjour {$user->display_name},\n\nUne étude « {$studyTitle} » vous a été assignée. Connectez-vous à votre compte pour l'examiner et l'approuver/désapprouver.\n\nMerci.\n\nL’équipe Fresh."
                );
            } else {
                $register_url = add_query_arg(['user_email' => urlencode($pi_email)], site_url('/inscription/'));
                wp_mail(
                    $pi_email,
                    "Invitation à rejoindre la plateforme",
                    "Bonjour,\n\nUne étude « {$studyTitle} » vous a été assignée mais vous n'avez pas encore de compte.\nMerci de vous inscrire ici : $register_url\n\nAprès inscription, vous pourrez voir et approuver/désapprouver les études.\n\nL’équipe Fresh."
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
                wp_mail(
                    $pi_email,
                    "Nouvelle étude à examiner",
                    "Bonjour {$user->display_name},\n\nUne étude « {$studyTitle} » vous a été assignée. Connectez-vous à votre compte pour l'examiner et l'approuver/désapprouver.\n\nMerci.\n\nL’équipe Fresh."
                );
            } else {
                $register_url = add_query_arg(['user_email' => urlencode($new_pi_email)], site_url('/inscription/'));
                wp_mail(
                    $new_pi_email,
                    "Invitation à rejoindre la plateforme",
                    "Bonjour,\n\nUne étude « {$studyTitle} » vous a été assignée mais vous n'avez pas encore de compte.\nMerci de vous inscrire ici : $register_url\n\nAprès inscription, vous pourrez voir et approuver/désapprouver les études.\n\nL’équipe Fresh."
                );
            }
        }
    }
    if (isset($events['modified_pending']) && $events['modified_pending'] == true) {

        $modificator_email = sanitize_email($current_user->user_email);

        // Vérifier les rôles
        $is_admin = user_can($current_user, 'admin_fresh');
        $is_pi    = (!empty($pi_email) && $modificator_email === $pi_email);
        $is_contributor = $contributor_id == $current_user->ID;
        $contributor = get_userdata($contributor_id);
        $contributor_email = $contributor ? $contributor->user_email : '';

        // Contenus des emails
        $subject = "Étude modifiée";
        $message = "Bonjour,
        \n\nDes modifications ont été apportées à l'étude « {$studyTitle} » en cours .
        \n\nConnectez-vous à votre compte pour examiner les changements.
        \n\nMerci.
        \n\nL’équipe Fresh.";

        // Cas 1 : Modifié par un Admin
        if ($is_admin) {
            // Notifier les autres admins
            foreach ($emails_admins as $admin_email) {
                if ($admin_email !== $current_user->user_email) {
                    wp_mail(
                        $admin_email,
                        $subject,
                        $message
                    );
                }
            }

            // Notifier le PI (si différent de l’admin modificateur)
            if (!empty($pi_email) && $pi_email !== $modificator_email) {
                wp_mail(
                    $pi_email,
                    $subject,
                    $message
                );
            }

            // Notifier le Contributeur (si différent de l’admin modificateur)
            if (!empty($contributor_email) && $contributor_email !==  $modificator_email) {
                wp_mail(
                    $contributor_email,
                    $subject,
                    $message
                );
            }
        }
        // Cas 2 : Modifié par PI
        elseif ($is_pi && !$is_admin) {
            // 1. notifier tous les admins
            foreach ($emails_admins as $admin_email) {
                wp_mail(
                    $admin_email,
                    $subject,
                    $message
                );
            }

            // 2. si le pi != contributeur
            if ($pi_email !== $contributor_email) {
                wp_mail(
                    $contributor_email,
                    $subject,
                    $message
                );
            }
        }

        // Modifié par Contributeur  ---
        elseif ($is_contributor && !$is_admin) {
            // Envoyer aux admins
            foreach ($emails_admins as $admin_email) {
                wp_mail(
                    $admin_email,
                    $subject,
                    $message
                );
            }

            // cas contributor #  PI
            if (!$is_pi && !empty($pi_email)) {
                wp_mail(
                    $pi_email,
                    $subject,
                    $message
                );
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


function get_base_idno(string $idno): string
{
    // Extraire la partie commune avant -FR ou -EN
    if (preg_match('/^(.*?)-(?:FR|EN)$/i', $idno, $matches)) {
        $base_idno = $matches[1]; // 'Survey-id'
    } else {
        $base_idno = $idno; // si pas de suffixe
    }

    return $base_idno;
}

/**
 * Récuperer une étude depuis la table nada_list_studies par idno
 */
function get_details_study_from_wp(string $base_idno)
{
    global $wpdb;
    $table = $wpdb->prefix . "nada_list_studies";

    try {

        $study_wp = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE nada_study_idno = %s",
                $base_idno
            ),
            ARRAY_A
        );

        return $study_wp ?: null;
    } catch (Exception $e) {
        error_log('Erreur dans get_details_study_from_wp: ' . $e->getMessage());
        return null;
    }
}

/**
 * Récuperer une étude depuis nada par idno
 */
function get_details_study_from_nada(string $idno): array
{

    $responses = [];
    // Instance de l’API
    $api = get_nada_api_instance();
    $base_idno = get_base_idno($idno);
    // Récupérer dataset
    $responses['fr'] = $api->get_dataset("$base_idno-fr");
    $responses['en'] = $api->get_dataset("$base_idno-en");

    return $responses;
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

/** Retourne une instance unique de Nada_API */
function get_nada_api_instance(): Nada_API
{
    static $nada_api = null; // Une seule instance sera créée
    if ($nada_api === null) {
        require_once NADA_ID_PLUGIN_DIR . 'includes/class-nada-api.php';
        $nada_api = new Nada_API();
    }
    return $nada_api;
}

/****** Fonctions pour debogue : A supprimer aprés les developpements *****/
function nada_id_log($message)
{
    if (WP_DEBUG === true) {
        error_log('[NADA_ID] ' . print_r($message, true));
    }
}


add_filter('rest_authentication_errors', function ($result) {
    if (! empty($result)) {
        // Si une erreur est déjà retournée, on loggue le détail
        error_log('REST API bloquée : ' . print_r($result, true));
    } else {
        // Sinon, on loggue que rien ne bloque encore
        error_log('REST API : pas encore bloquée.');
    }
    return $result; // on ne change rien, juste on observe
}, 100);

<?php
if (!defined('ABSPATH'))
    exit;

require_once NADA_ID_PLUGIN_DIR . 'includes/class-nada-api.php';

add_shortcode('nada_id_list_studies', 'nada_list_studies_shortcode');
add_shortcode('nada_id_add_study', 'nada_add_study_shortcode');
add_shortcode('nada_id_study_details', 'nada_study_details_shortcode');
add_shortcode('nada_id_list_catalogs', 'nada_list_catalogs_shortcode');
add_shortcode('nada_id_upload_v1', 'nada_import_study_shortcode');

/* ================================ Shortcodes ================================================/

/**
 * Shortcode [nada_id_list_studies]
 * Affiche la liste des études récupérées depuis NADA
 */
function nada_list_studies_shortcode($atts)
{
    $current_user = wp_get_current_user();

    // Récupérer instance de l’API
    $api = get_nada_api_instance();

    // Appel API pour récupérer les datasets
    $response = $api->get_datasets();

    // Gestion des erreurs
    if (!$response['success']) {
        return '<p class="nada-id-error">Erreur API : ' . esc_html($response['error']) . '</p>';
    }

    // Extraire les données ; récuperer dans le template
    $all_studies = $response['datasets'] ?? [];
    // Appliquer le filtre
    $user_studies = nada_filter_studies_by_user($all_studies, $current_user);
    $roles = (array) $current_user->roles;

    $current_language = pll_current_language();

    // Inclure le template pour l’affichage
    ob_start();
    $template_path = NADA_ID_PLUGIN_DIR . 'public/templates/list-studies.php';

    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p class="nada-id-error">Template manquant.</p>';
    }

    return ob_get_clean();
}

/**
 * Shortcode [nada_id_add_study]
 * Ajouter nouvlle étude FR/EN
 */
function nada_add_study_shortcode($atts)
{

    $atts = shortcode_atts([
        'mode' => 'add',   // mode par défaut
        'study_id' => ''
    ], $atts, 'nada_id_add_study');

    global $mode;
    global $jsonRec;

    $mode = 'add';

    // Si on passe un study_id (via shortcode ou via GET), alors edit
    $idno = !empty($atts['study_id']) ? $atts['study_id'] : get_query_var('idno');

    if (!empty($idno)) {
        $mode = 'edit';
    }

    if ($mode == 'edit') {
        $responses = get_details_study_from_nada($idno);

        // Récupérer dataset
        $responseFr = $responses['fr'];
        $responseEn = $responses['en'];


        // Affichage avec un template

        if (!$responseFr['success']) {
            echo '<p class="nada-id-error">Erreur API FR : ' . esc_html($responseFr['error']) . '</p>';
        } else if (!$responseEn['success']) {
            echo '<p class="nada-id-error">Erreur API EN: ' . esc_html($responseEn['error']) . '</p>';
        } else {
            $datasetEn = $responseEn['dataset']['metadata'];
            $datasetFr = $responseFr['dataset']['metadata'];

            $jsonRec = array_merge(
                extract_lang_data_from_payload($datasetEn, 'en'),
                extract_lang_data_from_payload($datasetFr, 'fr')
            );

            $studyDetails = get_details_study_from_wp_sc($idno);
            $current_user = wp_get_current_user();
        }
    }

    // Inclure le template pour l’affichage
    ob_start();
    $template_path = NADA_ID_PLUGIN_DIR . 'public/templates/add-study.php';

    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p class="nada-id-error">Template manquant.</p>';
    }

    return ob_get_clean();
}
/**
 * Shortcode [nada_id_upload_v1]
 * Ajouter nouvlle étude FR/EN
 */
function nada_import_study_shortcode()
{

    // Inclure le template pour l’affichage
    ob_start();
    $template_path = NADA_ID_PLUGIN_DIR . 'public/templates/import-study.php';

    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p class="nada-id-error">Template manquant.</p>';
    }

    return ob_get_clean();
}

/** Shortcode [nada_study_details] pour afficher le détail d'une étude via /catalogue-detail/{idno} */
function nada_study_details_shortcode()
{
    // On récupère l’idno depuis la query var, pas GET
    $idno = get_query_var('idno');

    if (!$idno) {
        ob_start();
        echo '<p class="nada-id-error">IDNO manquant.</p>';
        return ob_get_clean();
    }

    $responses = get_details_study_from_nada($idno);
    // Récupérer dataset
    $responseFr = $responses['fr'];
    $responseEn = $responses['en'];


    // Affichage avec un template

    if (!$responseFr['success']) {
        echo '<p class="nada-id-error">Erreur API FR : ' . esc_html($responseFr['error']) . '</p>';
    } else if (!$responseEn['success']) {
        echo '<p class="nada-id-error">Erreur API EN: ' . esc_html($responseEn['error']) . '</p>';
    } else {
        $datasetEn = $responseEn['dataset']['metadata'];
        $datasetFr = $responseFr['dataset']['metadata'];

        $jsonRec['fr'] = $datasetFr;
        $jsonRec['en'] = $datasetEn;

        $parts = explode("-", $idno);
        $currentLang = end($parts);

        $data['fr'] = $responseFr['dataset'];
        $data['en'] = $responseEn['dataset'];

        $url = get_nada_server_url();

        $base_idno = get_base_idno($idno);
        $studyDetails = get_details_study_from_wp($base_idno);

        $current_user = wp_get_current_user();
        $piEmail = '';
        $isCurrentUserPi = 'Oui';
        if ($studyDetails && $current_user->user_email != $studyDetails['pi_email']) {
            $piEmail = $studyDetails['pi_email'];
            $isCurrentUserPi = 'Non';
        }
    }
    // Inclure le template pour l’affichage
    ob_start();
    $template_path = NADA_ID_PLUGIN_DIR . 'public/templates/details-study.php';

    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p class="nada-id-error">Template manquant.</p>';
    }

    return ob_get_clean();
}

/**
 * Shortcode [nada_id_list_catalogs]
 * Affiche la liste des études pibliés, récupérées depuis NADA
 */
function nada_list_catalogs_shortcode($atts)
{
    // Inclure le template pour l’affichage
    ob_start();
    $template_path = NADA_ID_PLUGIN_DIR . 'public/templates/list-catalogs.php';

    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p class="nada-id-error">Template manquant.</p>';
    }

    return ob_get_clean();
}

/* ================================ Fonctions utilitaires ================================================/

/** Filtre les études selon le rôle et la table list_studies */
function nada_filter_studies_by_user($studies, $current_user)
{
    global $wpdb;

    $user_id = $current_user->ID;
    $user_email = $current_user->user_email;
    $roles = (array) $current_user->roles;
    $rows = [];

    $is_admin = in_array('admin_fresh', $roles, true);

    // Déterminer le champ study_id selon la langue
    $lang = pll_current_language();
    $field_id_name = ($lang === 'fr') ? 'nada_study_id_fr' : 'nada_study_id_en';

    // Récupérer les études où user est PI ou créateur
    $table_name = $wpdb->prefix . 'nada_list_studies';
    if ($is_admin) {
        // Admin → récupère toutes les études
        $rows = $wpdb->get_results(
            "SELECT $field_id_name, status, created_by, pi_id, pi_email, is_approved 
         FROM $table_name",
            ARRAY_A
        );
    } else {
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT $field_id_name, status, created_by, pi_id, pi_email, is_approved 
             FROM $table_name 
             WHERE pi_id = %d OR pi_email = %s OR created_by = %s",
                $user_id,
                $user_email,
                $user_id
            ),
            ARRAY_A
        );
    }

    if (empty($rows)) {
        return []; // aucune étude assignée ou crée
    }

    // Enrichir chaque étude avec les permissions
    // Indexer par study_id pour accès direct
    $studies_map = [];
    foreach ($rows as $row) {
        $studies_map[$row[$field_id_name]] = $row;
    }

    // Filtrer uniquement les études autorisées et enrichir les permissions
    $filtered = [];
    foreach ($studies as $study) {
        $id = $study['id'];
        $row = isset($studies_map[$id]) ? $studies_map[$id] : null;

        // Cas USER normal → filtrage
        if (!$row) {
            continue; // utilisateur non lié → skip
        }

        $status_key =  $row['status'];

        // Exclure les drafts pour tout le monde, même admin

        if ($status_key === 'draft' && $row['created_by'] != $user_id) {
            continue;
        }

        $is_pi = $row['pi_email'] == $user_email;

        $study['is_approved'] = $row['is_approved'];
        $study['status'] = __($status_key, 'nada-id');
        $study['status_key'] = $status_key;

        $study['can_edit'] = (($row['created_by'] == $user_id) || $is_admin || $is_pi) && $status_key != 'rejected';
        $study['can_delete'] = ($row['created_by'] == $user_id && $status_key == 'draft') || $is_admin;
        $study['can_publish'] = $status_key != 'rejected' && $is_admin;
        $study['can_decide'] = $status_key == 'pending' && $is_pi;
        $study['can_require_modifications'] = $status_key == 'pending' && $is_admin;
        $study['can_reject'] = $status_key != 'rejected' && $status_key != 'draft' && $is_admin;

        $filtered[] = $study;
    }

    return $filtered;
}

function extract_lang_data_from_payload(array $payload, string $lang = 'fr'): array
{
    $langData = [];

    $langData["additional/obtainedAuthorization/otherAuthorizingAgency_$lang"] = $payload["additional"]["obtainedAuthorization"]["otherAuthorizingAgency"] ?? [];

    // Exemple: fundingAgentTypes
    $langData["fundingAgentTypes_$lang"] = $payload["additional"]["fundingAgent"]["fundingAgentType"][0] ?? []; // ok

    // Exemple: regulatoryRequirements
    $langData["additional/regulatoryRequirements/conformityDeclaration_$lang"] =  $payload["additional"]["regulatoryRequirements"]["conformityDeclaration"] ?? ''; // ok

    // Exemple: sponsorType
    $langData["additional/sponsor/sponsorType_$lang"] =  $payload["additional"]["sponsor"]["sponsorType"] ?? ''; // ok

    // Exemple: governance committee
    $langData["additional/governance/committee_$lang"] =  $payload["additional"]["governance"]["committee"] ?? ''; // ok

    // Exemple: collaborations
    $langData["additional/collaborations/networkConsortium_$lang"] =  $payload["additional"]["collaborations"]["networkConsortium"] ?? '';

    // Exemple: theme
    $langData["additional/theme/complementaryInformation_$lang"] =        $payload["additional"]["theme"]["complementaryInformation"] ?? '';

    $langData["additional/theme/RareDiseases_$lang"] =        $payload["additional"]["theme"]["RareDiseases"] ?? '';

    $langData["additional/allocation/allocationMode_$lang"] =        $payload["additional"]["allocation"]["allocationMode"] ?? '';


    $langData["additional/allocation/allocationUnit_$lang"] =        $payload["additional"]["allocation"]["allocationUnit"] ?? '';

    $langData["additional/masking/blindedMaskingDetails_$lang"] = $payload["additional"]["masking"]["blindedMaskingDetails"] ?? [];

    $langData["additional/arms/armsNumber_$lang"] = $payload["additional"]["armsNumber"] ?? '';
    $langData["additional/arms_$lang"] = $payload["additional"]["arms"] ?? [];

    $langData["additional/intervention_$lang"] = $payload["additional"]["intervention"] ?? [];

    $langData["additional/otherResearchType/otherResearchTypeDetails_$lang"] = $payload["additional"]["otherResearchType"]["otherResearchTypeDetails"] ?? '';

    $langData["additional/inclusionGroups/nrInclusionGroups_$lang"] = $payload["additional"]["nrInclusionGroups"] ?? '';

    $langData["additional/activeFollowUp/isActiveFollowUp_$lang"] = $payload["additional"]["activeFollowUp"]["isActiveFollowUp"] ?? '';

    $langData["additional/activeFollowUp/followUpModeOther_$lang"] = $payload["additional"]["activeFollowUp"]["followUpModeOther"] ?? '';

    $langData["additional/dataCollectionIntegration/isDataIntegration_$lang"] = $payload["additional"]["dataCollectionIntegration"]["isDataIntegration"] ?? '';


    $langData["additional/dataTypes/clinicalDataDetails_$lang"] = $payload["additional"]["dataTypes"]["clinicalDataDetails"] ?? '';

    $langData["additional/dataTypes/biologicalDataDetails_$lang"] = $payload["additional"]["dataTypes"]["biologicalDataDetails"] ?? '';

    $langData["additional/dataTypes/isDataInBiobank_$lang"] = $payload["additional"]["dataTypes"]["isDataInBiobank"] ?? '';

    $langData["additional/dataTypes/dataTypeOther_$lang"] = $payload["additional"]["dataTypes"]["dataTypeOther"] ?? '';

    $langData["additional/variableDictionnary/variableDictionnaryLink_$lang"] = $payload["additional"]["variableDictionnary"]["variableDictionnaryLink"] ?? '';

    $langData["additional/dataQuality/otherDocumentation_$lang"] = $payload["additional"]["dataQuality"]["otherDocumentation"] ?? '';

    $langData["additional/mockSample/mockSampleAvailable_$lang"] = $payload["additional"]["mockSample"]["mockSampleAvailable"] ?? '';

    $langData["additional/mockSample/mockSampleLocation_$lang"] = $payload["additional"]["mockSample"]["mockSampleLocation"] ?? '';

    $langData["additional/fundingAgent/fundingAgentType_$lang"] = $payload["additional"]["fundingAgent"]["fundingAgentType"] ?? '';

    $langData["additional/interventionalStudy/interventionalStudyModel_$lang"] = $payload["additional"]["interventionalStudy"]["interventionalStudyModel"] ?? '';

    $langData["additional/interventionalStudy/researchPurpose_$lang"] = $payload["additional"]["interventionalStudy"]["researchPurpose"] ?? '';
    $langData["additional/interventionalStudy/trialPhase_$lang"] = $payload["additional"]["interventionalStudy"]["trialPhase"] ?? '';
    $langData["additional/collaborations/collaboration_$lang"] = $payload["additional"]["collaborations"]["collaboration"] ?? '';
    $langData["stdyDscr/citation/IDno/identifiant_$lang"] = $payload["doc_desc"]["idno"] ?? '';
    $langData["stdyDscr/citation/titlStmt/titl_$lang"] = $payload["study_desc"]["title_statement"]["title"] ?? '';
    $langData["stdyDscr/citation/titlStmt/altTitl_$lang"] = $payload["study_desc"]["title_statement"]["alternate_title"] ?? '';
    $langData["stdyDscr/studyAuthorization/authorizingAgency_$lang"] = $payload["study_desc"]["study_authorization"]["agency"] ?? '';

    $langData["stdyDscr/citation/rspStmt/AuthEnty_$lang"] = $payload["study_desc"]["authoring_entity"] ?? [];
    $langData["stdyDscr/citation/rspStmt/othId_$lang"] = $payload["study_desc"]["oth_id"] ?? [];
    $langData["stdyDscr/citation/distStmt/contact_$lang"] = $payload["study_desc"]["distribution_statement"]["contact"] ?? [];
    $langData["stdyDscr/citation/prodStmt/fundAg_$lang"] = $payload["study_desc"]["production_statement"]["funding_agencies"] ?? [];
    $langData["stdyDscr/citation/prodStmt/producer_$lang"] = $payload["study_desc"]["production_statement"]["producers"] ?? '';
    $langData["stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/value_$lang"] =  $payload["study_desc"]["study_info"]["quality_statement"]["standards"][0]["producer"] ?? '';
    $langData["stdyDscr/method/stdyClas_$lang"] = $payload["study_desc"]["method"]["study_class"] ?? '';
    $langData["stdyDscr/stdyInfo/purpose/value_$lang"] = $payload["study_desc"]["study_info"]["purpose"] ?? '';
    $langData["stdyDscr/stdyInfo/abstract/value_$lang"] = $payload["study_desc"]["study_info"]["abstract"] ?? '';
    $langData["stdyDscr/stdyInfo/subject/topcClas_$lang"] = $payload["study_desc"]["study_info"]["topics"] ?? '';
    $langData["stdyDscr/stdyInfo/subject/keyword_$lang"] = $payload["study_desc"]["study_info"]["keywords"] ?
        implode(
            ';',
            array_column($payload["study_desc"]["study_info"]["keywords"], 'keyword')
        ) : '';
    $langData["stdyDscr/method/dataColl/targetSampleSize_$lang"] = $payload["study_desc"]["method"]["data_collection"]["target_sample_size"] ?? '';
    $langData["additional/inclusionGroups_$lang"] = $payload["additional"]["inclusionGroups"] ?? [];

    $langData["stdyDscr/studyDevelopment/developmentActivity"] = $payload["study_desc"]["study_development"]["development_activity"] ?? '';
    $langData["stdyDscr/method/notes/subject_researchType_$lang"] = $payload["study_desc"]["method"]["method_notes"] ?? '';
    $langData["stdyDscr/stdyInfo/sumDscr/anlyUnit"] = $payload["study_desc"]["study_info"]["analysis_unit"] ?? '';
    $langData["additional/thirdPartySource/otherSourceType_$lang"] = $payload["additional"]["thirdPartySource"]["otherSourceType"] ?? '';
    $langData["additional/OtherPopulationType_$lang"] = $payload["additional"]["otherPopulation"]["OtherPopulationType"] ?? '';
    $langData["stdyDscr/dataAccs/setAvail/avlStatus_$lang"] = $payload["study_desc"]["data_access"]["dataset_availability"]["status"] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/restrctn_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["restrictions"] ?? '';
    $langData["stdyDscr/dataAccs/setAvail/conditions_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["conditions"] ?? '';
    $langData["stdyDscr/dataAccs/setAvail/notes_$lang"] = $payload["study_desc"]["data_access"]["notes"] ?? '';
    $langData["stdyDscr/method/dataColl/sources_$lang"] = $payload["study_desc"]["method"]["data_collection"]["sources"] ?? '';
    $langData["stdyDscr/stdyInfo/sumDscr/collDate_$lang"] = $payload["study_desc"]["study_info"]["coll_dates"] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/confDec_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["conf_dec"] ?? '';
    $langData["stdyDscr/citation/rspStmt/othId/collaboration_$lang"] = $payload["additional"]["collaborations"]["collaboration"] ?? '';

    $langData["stdyDscr/method/dataColl/sources/sourceCitation_$lang"] = $payload["study_desc"]["method"]["data_collection"]["sources"] ?? [];
    $langData["stdyDscr/stdyInfo/qualityStatement/otherQualityStatement_$lang"] = $payload["study_desc"]["study_info"]["quality_statement"]["other_quality_statement"] ?? '';
    $langData["stdyDscr/method/dataColl/frequenc_$lang"] = $payload["study_desc"]["method"]["data_collection"]["frequency"] ?? '';
    $langData["additional/collectionProcess/collectionModeDetails_$lang"] = $payload["additional"]["collectionProcess"]["collectionModeDetails"] ?? '';
    $langData["stdyDscr/method/dataColl/timeMeth_$lang"] = $payload["study_desc"]["method"]["data_collection"]["time_method"] ?? '';
    $langData["additional/cohortLongitudinal/recrutementTiming_$lang"] = $payload["additional"]["cohortLongitudinal"]["recrutementTiming"] ?? '';
    $langData["stdyDscr/method/dataColl/collMode_$lang"] = $payload["study_desc"]["method"]["data_collection"]["coll_mode"] ?? '';
    $langData["additional/collectionProcess/collectionModeOther_$lang"] = $payload["additional"]["collectionProcess"]["collectionModeOther"] ?? '';
    $langData["additional/dataCollection/inclusionStrategyOther_$lang"] = $payload["additional"]["dataCollection"]["inclusionStrategyOther"] ?? '';
    $langData["additional/dataCollection/inclusionStrategy_$lang"] = $payload["additional"]["dataCollection"]["inclusionStrategy"] ?? '';
    $langData["stdyDscr/method/dataColl/sampProc_$lang"] = $payload["study_desc"]["method"]["data_collection"]["sampling_procedure"] ?? '';
    $langData["additional/dataCollection/samplingModeOther_$lang"] = $payload["additional"]["dataCollection"]["samplingModeOther"] ?? '';
    $langData["stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_$lang"] = $payload["study_desc"]["method"]["data_collection"]["sample_frame"]["frame_unit"]["unit_type"] ?? '';
    $langData["additional/dataCollection/recruitmentSourceOther_$lang"] = $payload["additional"]["dataCollection"]["recruitmentSourceOther"] ?? '';

    $langData["stdyDscr/stdyInfo/sumDscr/nation_$lang"] = $payload["study_desc"]["study_info"]["nation"] ?? '';
    $langData["stdyDscr/stdyInfo/sumDscr/geogCover_$lang"] = $payload["study_desc"]["study_info"]["geog_coverage"] ?? '';
    $langData["additional/geographicalCoverage/geoDetail_$lang"] = $payload["additional"]["geographicalCoverage"]["geoDetail"] ?? '';

    $langData["stdyDscr/stdyInfo/qualityStatement/complianceDescription_$lang"] = $payload["study_desc"]["study_info"]["quality_statement"]["compliance_description"] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/contact_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["contact"][0]['name'] ?? '';

    $langData["stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName_$lang"] = $payload["study_desc"]["study_info"]["quality_statement"]["standards"][0]['name'] ?? '';
    $langData["additional/dataTypes/biobankContent_$lang"] = $payload["additional"]["dataTypes"]["biobankContent"] ?? '';
    $langData["stdyDscr/stdyInfo/sumDscr/dataKind_$lang"] = $payload["study_desc"]["study_info"]["data_kind"] ?? '';
    $langData["additional/variableDictionnary/variableDictionnaryAvailable_$lang"] = $payload["additional"]["variableDictionnary"]["variableDictionnaryAvailable"] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/specPerm/required_yes_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["spec_perm"][0]['required'] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/specPerm_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["spec_perm"][0]['txt'] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/confDec_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["conf_dec"][0]['txt'] ?? '';
    $langData["stdyDscr/dataAccs/setAvail/accsPlac_$lang"] = $payload["study_desc"]["data_access"]["dataset_availability"]["access_place"] ?? '';
    $langData["stdyDscr/dataAccs/setAvail/complete_$lang"] = $payload["study_desc"]["data_access"]["dataset_availability"]["complete"] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/citReq_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["cit_req"] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/deposReq_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["deposit_req"] ?? '';
    $langData["stdyDscr/othStdMat/relMat_$lang"] = $payload["study_desc"]["method"]["data_collection"]["research_instrument"] ?? '';

    $langData["stdyDscr/method/dataColl/respRate_$lang"] = $payload["study_desc"]["method"]["data_collection"]["response_rate"] ?? '';
    $langData["stdyDscr/citation/IDno_$lang"] = $payload["study_desc"]["title_statement"]["IDno"] ?? [];

    /* Start universe logic */
    $langData["stdyDscr/stdyInfo/sumDscr/universe_$lang"] = $payload['study_desc']['study_info']['universe'] ?? null;
    $universe_data_string = $langData["stdyDscr/stdyInfo/sumDscr/universe_$lang"];
    // Definir le key et le path de chaque valeur
    $mappings = [
        'level_sex_clusion_I'      => 'stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I',
        'level_age_clusion_I'      => 'stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I',
        'level_type_clusion_I'     => 'stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I',
        'level_type_clusion_other' => 'additional/OtherPopulationType',
        'clusion_I'                => 'stdyDscr/stdyInfo/sumDscr/universe/Clusion_I',
        'clusion_E'                => 'stdyDscr/stdyInfo/sumDscr/universe/Clusion_E',
    ];
    // Default: initialiser tous les keys vide
    foreach ($mappings as $source_key => $destination_path) {
        $langData[$destination_path . "_$lang"] = [];
    }
    // decode la valeur universe et verifier JSON string ou non 
    if (is_string($universe_data_string) && $universe_data_string !== '') {
        $universe = json_decode($universe_data_string, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($universe)) {
            foreach ($mappings as $source_key => $destination_path) {
                $final_key = $destination_path . "_$lang";
                if (array_key_exists($source_key, $universe)) {
                    $value = $universe[$source_key];
                    $langData[$final_key] = is_array($value)
                        ? array_values(array_filter($value, fn($v) => $v !== null && $v !== ''))
                        : (string)$value;
                }
            }
        }
    }
    /* End universe logic */

    $langData["additional/masking/maskingType_$lang"] = $payload["additional"]["masking"]["maskingType"] ?? '';
    $langData["additional/dataCollectionIntegration_$lang"] = $payload["additional"]["dataCollectionIntegration"]["isDataIntegration"] ?? '';


    $langData["stdyDscr/method/notes/subject_followUP_$lang"] = $payload["study_desc"]["method"]["notes"] ?? ''; // a verifier avec BO

    $langData["stdyDscr/studyDevelopment/developmentActivity/type_primaryEvaluation/description_$lang"] = '';
    $langData["stdyDscr/studyDevelopment/developmentActivity/type_secondaryEvaluation/description_$lang"] = '';

    // Vérifier si la position 0 existe
    if (isset($payload["study_desc"]["study_development"]["development_activity"][0])) {
        $langData["stdyDscr/studyDevelopment/developmentActivity/type_primaryEvaluation/description_$lang"] =
            $payload["study_desc"]["study_development"]["development_activity"][0]['activity_description'] ?? '';
    }

    // Vérifier si la position 1 existe
    if (isset($payload["study_desc"]["study_development"]["development_activity"][1])) {
        $langData["stdyDscr/studyDevelopment/developmentActivity/type_secondaryEvaluation/description_$lang"] =
            $payload["study_desc"]["study_development"]["development_activity"][1]['activity_description'] ?? '';
    }

    $langData["additional/cohortLongitudinal/recrutementTiming_$lang"] = $payload["additional"]["cohortLongitudinal"]["recrutementTiming"] ?? '';

    return $langData;
}


/**
 * Récuperer une étude depuis la table nada_list_studies par idno
 */
function get_details_study_from_wp_sc(string $idno): ?array
{
    global $wpdb;
    $table = $wpdb->prefix . "nada_list_studies";

    try {

        $base_idno = get_base_idno($idno);


        $study_wp = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE nada_study_idno = %s",
                $base_idno
            ),
            ARRAY_A
        );

        return $study_wp ?: null;
    } catch (Exception $e) {
        error_log('Erreur dans get_details_study_from_wp_sc: ' . $e->getMessage());
        return null;
    }
}

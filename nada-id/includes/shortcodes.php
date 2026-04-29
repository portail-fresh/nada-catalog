<?php
if (!defined('ABSPATH'))
    exit;

require_once NADA_ID_PLUGIN_DIR . 'includes/api/class-nada-api.php';
require_once NADA_ID_PLUGIN_DIR . 'includes/class-nada-institutions-table.php';
require_once NADA_ID_PLUGIN_DIR . 'includes/class-nada-repository-table.php';
require_once NADA_ID_PLUGIN_DIR . 'includes/class-nada-repository-items-table.php';
require_once NADA_ID_PLUGIN_DIR . 'includes/class-nada-institution.php';


add_shortcode('nada_id_list_studies', 'nada_list_studies_shortcode');
add_shortcode('nada_id_add_study', 'nada_add_study_shortcode');
add_shortcode('nada_id_study_details', 'nada_study_details_shortcode');
add_shortcode('nada_id_list_catalogs', 'nada_list_catalogs_shortcode');
add_shortcode('nada_id_upload_v1', 'nada_import_study_shortcode');
add_shortcode('nada_id_list_referentiel', 'nada_list_referentiel_shortcode');
add_shortcode('nada_id_referentiel_details', 'nada_details_referentiel_shortcode');
add_shortcode('nada_statistics', 'nada_statistics_shortcode');
add_shortcode('nada_catalog_statistics', 'nada_catalog_statistics_shortcode');
add_shortcode('variable_dictionary', 'variable_dictionary_shortcode');
add_shortcode('nada_list_institutions_valid', 'nada_list_institutions_valid_shortcode');
add_shortcode('nada_list_institutions_waiting', 'nada_list_institutions_waiting_shortcode');
/* ================================ Shortcodes ================================================*/

function nada_list_studies_shortcode($atts)
{

    // Empêche exécution dans header / hooks custom
    // Bloquer le header du thème (cas spécifique)
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
    foreach ($backtrace as $trace) {
        if (!empty($trace['function']) && $trace['function'] === 'themetechmount_inline_css_header_code') {
            return '';
        }
    }

    // language courant
    $current_language = pll_current_language();
    ob_start();

    include NADA_ID_PLUGIN_DIR . 'public/templates/list-studies.php';

    return ob_get_clean();
}


/**
 * Shortcode [nada_id_add_study]
 * Ajouter nouvlle étude FR/EN
 */
function nada_add_study_shortcode($atts)
{
    // Empêche exécution dans header / hooks custom
    if (!in_the_loop() || !is_main_query()) {
        return '';
    }
    $atts = shortcode_atts([
        'mode' => 'add',   // mode par défaut
        'study_id' => ''
    ], $atts, 'nada_id_add_study');

    global $mode;
    global $jsonRec;
    global $jsonParentStudy;
    global $compareStudy;

    $mode = 'add';

    // Préparer variable global (récuperable dans le JS)
    $data = [
        'mode'         => $mode,
        'studyId'      => null,
        'jsonRec'      => [],
        'jsonParentStudy'      => [],
        'studyDetails' => null,
        'compareStudy' => [],

    ];


    $current_language = pll_current_language() ?? 'fr';
    // Si on passe un study_id (via shortcode ou via GET), alors edit
    $idno = !empty($atts['study_id']) ? $atts['study_id'] : get_query_var('idno');

    global $list_referentiels;
    global $listInstitutions;
    $list_referentiels = get_list_referentiels_wp();
    $listInstitutions = getInstitutionsList();

    if (!empty($idno)) {
        $mode = 'edit';
    }

    if ($mode == 'edit') {
        $data['mode'] = 'edit';
        $responses = get_details_study_from_nada($idno);

        // Récupérer dataset
        $responseFr = $responses['fr'];
        $responseEn = $responses['en'];

        // Affichage avec un template

        if (!$responseFr['success'] || !$responseEn['success']) {
            if ($responseFr['status'] == 400 || $responseEn['status'] == 400) {
                // redirecion  page 404
                redirect_404_page();
            }
            echo '<p class="nada-id-error">Erreur API EN: ' . esc_html($responseEn['error']) . '</p>';
        } else {

            $datasetEn = $responseEn['dataset']['metadata'];
            $datasetFr = $responseFr['dataset']['metadata'];
            $published = $responseFr['dataset']['published'];
            $statusKey = $responseFr['dataset']['link_indicator'];
            $contributorCreatedBy = $responseFr['dataset']['created_by'];

            $jsonRec = array_merge(
                extract_lang_data_from_payload($datasetFr, 'fr'),
                extract_lang_data_from_payload($datasetEn, 'en')
            );

            if (($statusKey === 'pending') && (str_contains($idno, '-draft'))) {
                $baseIdno = get_base_idno($idno);
                $cleanIdno = preg_replace('/-draft$/', '', $baseIdno);
                $parentStudy = get_details_study_from_nada($cleanIdno);

                if ($parentStudy) {

                    $responseFr = $parentStudy['fr'];
                    $responseEn = $parentStudy['en'];

                    $datasetParentStudyEn = $responseEn['dataset']['metadata'];
                    $datasetParentStudyFr = $responseFr['dataset']['metadata'];

                    $jsonParentStudy = array_merge(
                        extract_lang_data_from_payload($datasetParentStudyFr, 'fr'),
                        extract_lang_data_from_payload($datasetParentStudyEn, 'en')
                    );

                    $compareStudy = compareArrays($jsonParentStudy, $jsonRec);
                    $data['jsonParentStudy'] = $jsonParentStudy;
                    $data['compareStudy'] = $compareStudy;
                }
            }


            $studyDetails = get_details_study_from_wp_sc($idno);
            $current_user = wp_get_current_user();

            $data['jsonRec'] = $jsonRec;
            $data['studyDetails'] =  $studyDetails;
            $data['studyId'] = $idno;
            $data['currentUser'] = $current_user->data;
        }
    }

    // Stockage temporaire PHP -> JS
    // Rendre $data disponible dans le template et accesible dans LE js
    set_query_var('nada_contribute_study_data', $data);


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
    $current_language = pll_current_language() ?? 'fr';
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
    // Empêche exécution du shortcode dans le header du thème Brivona
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
    foreach ($backtrace as $trace) {
        if (!empty($trace['function']) && $trace['function'] === 'themetechmount_inline_css_header_code') {
            return '';
        }
    }

    // On récupère l’idno depuis la query var, pas GET
    $idno = get_query_var('idno');

    if (!$idno) {
        ob_start();
        echo '<p class="nada-id-error">IDNO manquant.</p>';
        return ob_get_clean();
    }

    // Récupérer la page d’origine
    $referrer = wp_get_referer();

    // Extraire juste le chemin de l'URL (sans domaine)
    $path = parse_url($referrer, PHP_URL_PATH);
    $catalog_paths = ["/catalogue", "/catalogue/",  "/en/catalog/", "/en/catalog"];
    $space_paths    = ["/mon-espace/", "/mon-espace", "/en/my-space", "/en/my-space/"];
    $from = '';

    if (in_array($path, $catalog_paths, true)) {
        $from = 'catalog';
    } elseif (in_array($path, $space_paths, true)) {
        $from = 'space';
    }

    $responses = get_details_study_from_nada($idno, $from);
    // Récupérer dataset
    $responseFr = $responses['fr'];
    $responseEn = $responses['en'];

    // Affichage avec un template
    if (!$responseFr['success'] || !$responseEn['success']) {
        if ($responseFr['status'] == 400 || $responseEn['status'] == 400) {
            // redirecion  page 404
            redirect_404_page();
        }
        echo '<p class="nada-id-error">Erreur API : ' . esc_html($responseFr['error']) . '</p>';
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
    nada_id_log("appel list catalogs");
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

/* ================================ Fonctions utilitaires ================================================*/

function extract_lang_data_from_payload(array $payload, string $lang = 'fr'): array
{
    $langData = []; // initialisation du tableau de sortie
    $langData["additional/obtainedAuthorization/otherAuthorizingAgency_$lang"] = $payload["additional"]["obtainedAuthorization"]["otherAuthorizingAgency"] ?? [];
    $langData["additional/regulatoryRequirements/conformityDeclaration_$lang"] =  $payload["additional"]["regulatoryRequirements"]["conformityDeclaration"] ?? ''; // ok
    $langData["additional/sponsor/sponsorType_$lang"] =  $payload["additional"]["sponsor"]["sponsorType"] ?? '';
    $langData["additional/governance/committee_$lang"] =  normalizeBooleanValue($payload["additional"]["governance"]["committee"] ?? '', $lang);
    $langData["additional/collaborations/networkConsortium_$lang"] =  normalizeBooleanValue($payload["additional"]["collaborations"]["networkConsortium"] ?? '', $lang);
    $langData["additional/theme/complementaryInformation_$lang"] =        $payload["additional"]["theme"]["complementaryInformation"] ?? '';
    $langData["additional/theme/RareDiseases_$lang"] =       normalizeBooleanValue($payload["additional"]["theme"]["RareDiseases"] ?? '', $lang);
    $langData["additional/allocation/allocationMode_$lang"] =        $payload["additional"]["allocation"]["allocationMode"] ?? '';
    $langData["additional/allocation/allocationUnit_$lang"] =        $payload["additional"]["allocation"]["allocationUnit"] ?? '';
    $langData["additional/masking/blindedMaskingDetails_$lang"] = $payload["additional"]["masking"]["blindedMaskingDetails"] ?? '';
    $langData["additional/arms_$lang"] = $payload["additional"]["arms"] ?? [];
    $langData["additional/intervention_$lang"] = $payload["additional"]["intervention"] ?? '';
    $langData["additional/otherResearchType/otherResearchTypeDetails_$lang"] = $payload["additional"]["otherResearchType"]["otherResearchTypeDetails"] ?? '';
    $langData["additional/activeFollowUp/isActiveFollowUp_$lang"] = normalizeBooleanValue($payload["additional"]["activeFollowUp"]["isActiveFollowUp"] ?? '', $lang);
    $langData["additional/activeFollowUp/followUpModeOther_$lang"] = $payload["additional"]["activeFollowUp"]["followUpModeOther"] ?? '';
    $langData["additional/dataCollectionIntegration/isDataIntegration_$lang"] =  normalizeBooleanValue($payload["additional"]["dataCollectionIntegration"]["isDataIntegration"] ?? '', $lang);
    $langData["additional/dataTypes/clinicalDataDetails_$lang"] = $payload["additional"]["dataTypes"]["clinicalDataDetails"] ?? '';
    $langData["additional/dataTypes/biologicalDataDetails_$lang"] = $payload["additional"]["dataTypes"]["biologicalDataDetails"] ?? '';
    $langData["additional/dataTypes/isDataInBiobank_$lang"] =  normalizeBooleanValue($payload["additional"]["dataTypes"]["isDataInBiobank"] ?? '', $lang);
    $langData["additional/dataTypes/dataTypeOther_$lang"] = $payload["additional"]["dataTypes"]["dataTypeOther"] ?? '';
    $langData["additional/variableDictionnary/variableDictionnaryLink_$lang"] = $payload["additional"]["variableDictionnary"]["variableDictionnaryLink"] ?? '';
    $langData["additional/dataQuality/otherDocumentation_$lang"] = $payload["additional"]["dataQuality"]["otherDocumentation"] ?? '';
    $langData["additional/mockSample/mockSampleAvailable_$lang"] = normalizeBooleanValue($payload["additional"]["mockSample"]["mockSampleAvailable"] ?? '', $lang);
    $langData["additional/mockSample/mockSampleLocation_$lang"] = $payload["additional"]["mockSample"]["mockSampleLocation"] ?? '';
    $langData["additional/fundingAgent/fundingAgentType_$lang"] = $payload["additional"]["fundingAgent"]["fundingAgentType"] ?? '';
    $langData["additional/fundingAgent/otherFundingAgentType_$lang"] = $payload["additional"]["fundingAgent"]["otherFundingAgentType"] ?? '';
    $langData["additional/sponsor/sponsorType_$lang"] = $payload["additional"]["sponsor"]["sponsorType"] ?? '';
    $langData["additional/sponsor/otherSponsorType_$lang"] = $payload["additional"]["sponsor"]["otherSponsorType"] ?? '';
    $langData["additional/interventionalStudy/interventionalStudyModel_$lang"] = $payload["additional"]["interventionalStudy"]["interventionalStudyModel"] ?? '';
    $langData["additional/interventionalStudy/researchPurpose_$lang"] = $payload["additional"]["interventionalStudy"]["researchPurpose"] ?? '';
    $langData["additional/interventionalStudy/trialPhase_$lang"] = $payload["additional"]["interventionalStudy"]["trialPhase"] ?? '';
    $langData["additional/collaborations/collaboration_$lang"] = $payload["additional"]["collaborations"]["collaboration"] ?? '';
    $langData["stdyDscr/citation/IDno/identifiant_$lang"] = $payload["doc_desc"]["idno"] ?? '';
    $langData["stdyDscr/citation/titlStmt/titl_$lang"] = $payload["study_desc"]["title_statement"]["title"] ?? '';
    $langData["stdyDscr/citation/titlStmt/altTitl_$lang"] = $payload["study_desc"]["title_statement"]["alternate_title"] ?? '';
    $langData["stdyDscr/studyAuthorization/authorizingAgency_$lang"] = $payload["study_desc"]["study_authorization"]["agency"] ?? '';
    $langData["stdyDscr/citation/rspStmt/AuthEnty_$lang"] = $payload["study_desc"]["authoring_entity"] ?? '';
    $langData["stdyDscr/citation/rspStmt/othId_$lang"] = $payload["study_desc"]["oth_id"] ?? '';
    $langData["stdyDscr/citation/distStmt/contact_$lang"] = $payload["study_desc"]["distribution_statement"]["contact"] ?? '';
    $langData["stdyDscr/citation/prodStmt/fundAg/values_$lang"] = $payload["study_desc"]["production_statement"]["funding_agencies"] ?? '';
    $langData["stdyDscr/citation/prodStmt/producer/values_$lang"] = $payload["study_desc"]["production_statement"]["producers"] ?? '';
    $langData["stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/committee_$lang"] =  $payload["study_desc"]["study_info"]["quality_statement"]["standards"][0]["committee"] ?? '';
    $langData["stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/governance_$lang"] =  $payload["study_desc"]["study_info"]["quality_statement"]["standards"][0]["governance"] ?? '';
    $langData["stdyDscr/method/stdyClas_$lang"] = $payload["study_desc"]["method"]["study_class"] ?? '';
    $langData["stdyDscr/stdyInfo/purpose/value_$lang"] = $payload["study_desc"]["study_info"]["purpose"] ?? '';
    $langData["stdyDscr/stdyInfo/abstract/value_$lang"] = $payload["study_desc"]["study_info"]["abstract"] ?? '';
    $langData["stdyDscr/stdyInfo/subject/topcClas_$lang"] = $payload["study_desc"]["study_info"]["topics"] ?? '';
    $langData["stdyDscr/stdyInfo/subject/keyword_$lang"] = $payload["study_desc"]["study_info"]["keywords"] ?  implode(';', array_column($payload["study_desc"]["study_info"]["keywords"], 'keyword')) : '';
    $langData["stdyDscr/method/dataColl/targetSampleSize_$lang"] = $payload["study_desc"]["method"]["data_collection"]["target_sample_size"] ?? '';
    $langData["additional/inclusionGroups_$lang"] = $payload["additional"]["inclusionGroups"] ?? '';
    $langData["stdyDscr/studyDevelopment/developmentActivity"] = $payload["study_desc"]["study_development"]["development_activity"] ?? '';
    $langData["stdyDscr/method/notes/subject_researchType_$lang"] = $payload["study_desc"]["method"]["method_notes"] ?? '';
    $langData["stdyDscr/stdyInfo/sumDscr/anlyUnit"] = $payload["study_desc"]["study_info"]["analysis_unit"] ?? '';
    $langData["additional/thirdPartySource/otherSourceType_$lang"] = $payload["additional"]["thirdPartySource"]["otherSourceType"] ?? '';
    $langData["additional/OtherPopulationType_$lang"] = $payload["additional"]["otherPopulation"]["OtherPopulationType"] ?? '';
    $langData["additional/observationalStudy_$lang"] = $payload["additional"]["observationalStudy"] ?? '';
    // Traitement pour extraire la valeur de status
    $status_raw = $payload["study_desc"]["data_access"]["dataset_availability"]["status"] ?? '';
    $status_value = '';
    if (is_string($status_raw) && $status_raw !== '') {
        $decoded = json_decode($status_raw, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['value'])) {
            $status_value = $decoded['value'];
        } else {
            $status_value = $status_raw;
        }
    } elseif (is_array($status_raw) && isset($status_raw['value'])) {
        $status_value = $status_raw['value'];
    }
    $langData["stdyDscr/dataAccs/setAvail/avlStatus_$lang"] = $status_value;
    // Fin traitement status
    $langData["stdyDscr/dataAccs/useStmt/restrctn_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["restrictions"] ?? '';
    $langData["stdyDscr/dataAccs/setAvail/conditions_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["conditions"] ?? '';
    $langData["stdyDscr/dataAccs/setAvail/notes_$lang"] = $payload["study_desc"]["data_access"]["notes"] ?? '';
    $langData["stdyDscr/method/dataColl/sources/values_$lang"] = $payload["study_desc"]["method"]["data_collection"]["sources"] ?? '';
    $langData["stdyDscr/stdyInfo/sumDscr/collDate_$lang"] = $payload["study_desc"]["study_info"]["coll_dates"] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/confDec_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["conf_dec"] ?? '';

    // Extraction de la valeur de collaboration depuis oth_id
    $othId = $payload["study_desc"]["oth_id"] ?? [];
    $collaboration = '';
    if (is_array($othId)) {
        foreach ($othId as $item) {
            if (isset($item['type']) && $item['type'] === 'collaboration' && !empty($item['name'])) {
                $collaboration = $item['name'];
            }
        }
    }
    // Store in langData as text
    $langData["stdyDscr/citation/rspStmt/othId/collaboration_$lang"] = $collaboration;

    $langData["stdyDscr/stdyInfo/qualityStatement/otherQualityStatement_$lang"] = $payload["study_desc"]["study_info"]["quality_statement"]["other_quality_statement"] ?? '';
    $langData["stdyDscr/method/dataColl/frequenc_$lang"] = $payload["study_desc"]["method"]["data_collection"]["frequency"] ?? '';
    $langData["additional/collectionProcess/collectionModeDetails_$lang"] = $payload["additional"]["collectionProcess"]["collectionModeDetails"] ?? '';
    $langData["stdyDscr/method/dataColl/timeMeth_$lang"] = $payload["study_desc"]["method"]["data_collection"]["time_method"] ?? '';
    $langData["additional/cohortLongitudinal/recrutementTiming_$lang"] = $payload["additional"]["cohortLongitudinal"]["recrutementTiming"] ?? '';
    // Traitement pour extraire les valeurs de coll_mode
    $coll_mode_raw = $payload["study_desc"]["method"]["data_collection"]["coll_mode"] ?? [];
    $coll_mode_processed = [];
    if (is_array($coll_mode_raw)) {
        foreach ($coll_mode_raw as $item) {
            if (is_string($item)) {
                $decoded = json_decode($item, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['value'])) {
                    $coll_mode_processed[] = $decoded['value'];
                } else {
                    $coll_mode_processed[] = $item;
                }
            } elseif (is_array($item) && isset($item['value'])) {
                $coll_mode_processed[] = $item['value'];
            }
        }
    }
    $langData["stdyDscr/method/dataColl/collMode_$lang"] = $coll_mode_processed;
    // Fin traitement coll_mode
    $langData["additional/collectionProcess/collectionModeOther_$lang"] = $payload["additional"]["collectionProcess"]["collectionModeOther"] ?? '';
    $langData["additional/dataCollection/inclusionStrategyOther_$lang"] = $payload["additional"]["dataCollection"]["inclusionStrategyOther"] ?? '';
    $langData["additional/dataCollection/inclusionStrategy_$lang"] = $payload["additional"]["dataCollection"]["inclusionStrategy"] ?? '';
    // Traitement pour extraire les valeurs de sampProc
    $samp_proc_raw = $payload["study_desc"]["method"]["data_collection"]["sampling_procedure"] ?? '';
    $samp_proc_processed = [];
    if (is_string($samp_proc_raw) && $samp_proc_raw !== '') {
        $raw = trim($samp_proc_raw);
        $temp_items = [];

        if (preg_match_all("/'([^']*)'|\"([^\"]*)\"/", $raw, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $temp_items[] = ($m[1] !== '') ? $m[1] : $m[2];
            }
        } else {
            $clean = trim($raw, "[]\"'");
            $parts = array_map('trim', explode(',', $clean));
            foreach ($parts as $p) {
                $p = trim($p, "\"'");
                if ($p !== '') $temp_items[] = $p;
            }
        }

        foreach ($temp_items as $item) {
            $decoded = json_decode($item, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['value'])) {
                $samp_proc_processed[] = $decoded['value'];
            } else {
                $samp_proc_processed[] = $item;
            }
        }
    } elseif (is_array($samp_proc_raw)) {
        foreach ($samp_proc_raw as $item) {
            if (is_string($item)) {
                $decoded = json_decode($item, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['value'])) {
                    $samp_proc_processed[] = $decoded['value'];
                } else {
                    $samp_proc_processed[] = $item;
                }
            } elseif (is_array($item) && isset($item['value'])) {
                $samp_proc_processed[] = $item['value'];
            }
        }
    }
    $langData["stdyDscr/method/dataColl/sampProc_$lang"] = $samp_proc_processed;
    // Fin traitement sampProc
    $langData["additional/dataCollection/samplingModeOther_$lang"] = $payload["additional"]["dataCollection"]["samplingModeOther"] ?? '';
    $langData["stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_$lang"] = $payload["study_desc"]["method"]["data_collection"]["sample_frame"]["frame_unit"]["unit_type"] ?? '';
    $langData["additional/dataCollection/recruitmentSourceOther_$lang"] = $payload["additional"]["dataCollection"]["recruitmentSourceOther"] ?? '';

    // Récupérer les nations du payload
    $nations_raw = $payload["study_desc"]["study_info"]["nation"] ?? [];
    // Extraire uniquement les noms
    $nations = [];
    foreach ((array)$nations_raw as $nation) {
        if (is_array($nation) && isset($nation["name"])) {
            $nations[] = $nation["name"];
        } elseif (is_string($nation)) {
            $nations[] = $nation; // compatibilité avec anciens formats
        }
    }
    $langData["stdyDscr/stdyInfo/sumDscr/nation_$lang"] = $nations;
    // Fin traitement nations
    $langData["stdyDscr/stdyInfo/sumDscr/geogCover_$lang"] = $payload["study_desc"]["study_info"]["geog_coverage"] ?? '';
    $langData["additional/geographicalCoverage/geoDetail_$lang"] = $payload["additional"]["geographicalCoverage"]["geoDetail"] ?? '';
    $langData["stdyDscr/stdyInfo/qualityStatement/complianceDescription_$lang"] = $payload["study_desc"]["study_info"]["quality_statement"]["compliance_description"] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/contact_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["contact"] ?? '';
    $langData["stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName/values_$lang"] = $payload["study_desc"]["study_info"]["quality_statement"]["standards"][0]['name'] ?? '';
    $langData["additional/dataTypes/biobankContent_$lang"] = $payload["additional"]["dataTypes"]["biobankContent"] ?? '';
    $langData["stdyDscr/stdyInfo/sumDscr/dataKind_$lang"] = $payload["study_desc"]["study_info"]["data_kind"] ?? '';
    $langData["additional/variableDictionnary/variableDictionnaryAvailable_$lang"] =  normalizeBooleanValue($payload["additional"]["variableDictionnary"]["variableDictionnaryAvailable"] ?? '', $lang);
    $langData["stdyDscr/dataAccs/useStmt/specPerm/required_yes_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["spec_perm"][0]['required'] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/specPerm_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["spec_perm"][0]['txt'] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/confDec_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["conf_dec"][0]['txt'] ?? '';
    $langData["stdyDscr/dataAccs/setAvail/accsPlac_$lang"] = $payload["study_desc"]["data_access"]["dataset_availability"]["access_place"] ?? '';
    $langData["stdyDscr/dataAccs/setAvail/complete_$lang"] = $payload["study_desc"]["data_access"]["dataset_availability"]["complete"] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/citReq_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["cit_req"] ?? '';
    $langData["stdyDscr/dataAccs/useStmt/deposReq_$lang"] = $payload["study_desc"]["data_access"]["dataset_use"]["deposit_req"] ?? '';
    $langData["stdyDscr/othStdMat/relMat_$lang"] = $payload["study_desc"]["method"]["data_collection"]["research_instrument"] ?? '';
    $langData["stdyDscr/method/dataColl/respRate_$lang"] = $payload["study_desc"]["method"]["data_collection"]["response_rate"] ?? '';
    $langData["stdyDscr/citation/titlStmt/IDNo/values_$lang"] = $payload["study_desc"]["title_statement"]["IDno"]["metadata_no"] ?? '';
    $langData["stdyDscr/stdyInfo/sumDscr/universe_$lang"] = $payload['study_desc']['study_info']['universe'] ?? null;
    // Début universe logic
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
                    // verifier si la valeur est un array ou string
                    if (is_array($value)) {
                        $processed_array = array_map(function ($item) {
                            if (is_array($item) && isset($item['value'])) {
                                return $item['value'];
                            }
                            return $item;
                        }, $value);

                        // Filtrer les valeurs nulles ou vides
                        $langData[$final_key] = array_values(array_filter($processed_array, fn($v) => $v !== null && $v !== ''));
                    } else {
                        // Si c'est une string, l'assigner directement
                        $langData[$final_key] = (string)$value;
                    }
                }
            }
        }
    }
    // Fin universe logic
    $langData["additional/masking/maskingType_$lang"] = $payload["additional"]["masking"]["maskingType"] ?? '';
    $langData["additional/dataCollectionIntegration_$lang"] = $payload["additional"]["dataCollectionIntegration"]["isDataIntegration"] ?? '';
    $langData["stdyDscr/method/notes_$lang"] = $payload["study_desc"]["method"]["notes"] ?? '';
    // Traitement pour developmentActivity descriptions
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
    // Fin traitement developmentActivity descriptions
    $langData["additional/cohortLongitudinal/recrutementTiming_$lang"] = $payload["additional"]["cohortLongitudinal"]["recrutementTiming"] ?? '';
    $langData["additional/isHealthTheme_$lang"] = normalizeBooleanValue($payload["additional"]["isHealthTheme"] ?? '', $lang);
    $langData["additional/isContributorPI_$lang"] = normalizeBooleanValue($payload["additional"]["isContributorPI"] ?? '', $lang);
    $langData["additional/addTeamMember_$lang"] = normalizeBooleanValue($payload["additional"]["addTeamMember"] ?? '', $lang);
    $langData["additional/primaryInvestigator/piAffiliation/piLabo_$lang"] = $payload["additional"]["primaryInvestigator"]["piAffiliation"]["piLabo"] ?? '';
    $langData["additional/primaryInvestigator/isPIContact_$lang"] = normalizeBooleanValue($payload["additional"]["primaryInvestigator"]["isPIContact"] ?? '', $lang);
    $langData["additional/interventionalStudy/isClinicalTrial_$lang"] = normalizeBooleanValue($payload["additional"]["interventionalStudy"]["isClinicalTrial"] ?? '', $lang);

    $methodNotes = strtolower($payload["study_desc"]["method"]["method_notes"] ?? '');
    $isInclusion = normalizeBooleanValue($payload["additional"]["isInclusionGroups"] ?? '', $lang);
    $isObservational = str_contains($methodNotes, 'observationnelle') || str_contains($methodNotes, 'observational');
    $isInterventional = str_contains($methodNotes, 'interventionnelle') || str_contains($methodNotes, 'interventional');
    $langData["additional/observationalStudy/isInclusionGroups_$lang"] = $isObservational ? $isInclusion : '';
    $langData["additional/interventionalStudy/isInclusionGroups_$lang"] = $isInterventional ? $isInclusion : '';

    //    $langData["additional/interventionalStudy/isInclusionGroups_$lang"] = $payload["additional"]["isInclusionGroups"] ?? '';
    //    $langData["additional/observationalStudy/isInclusionGroups_$lang"] = $payload["additional"]["isInclusionGroups"] ?? '';
    $langData["additional/interventionalStudy/otherResearchPurpose_$lang"] = $payload["additional"]["interventionalStudy"]["otherResearchPurpose"] ?? '';
    $langData["additional/relatedDocument_$lang"] = $payload["additional"]["relatedDocument"] ?? '';
    $langData["additional/dataTypes/biobankContentOther_$lang"] = $payload["additional"]["dataTypes"]["biobankContentOther"] ?? '';
    $langData["additional/dataTypes/otherLiquidsDetails_$lang"] = $payload["additional"]["dataTypes"]["otherLiquidsDetails"] ?? '';
    $langData["additional/dataCollection/otherDocumentation_$lang"] = $payload["additional"]["dataCollection"]["otherDocumentation"] ?? '';
    $langData["additional/dataTypes/paraclinicalDataOther_$lang"] = $payload["additional"]["dataTypes"]["paraclinicalDataOther"] ?? '';
    // bloc other IDN
    $langData["additional/fileDscr/fileTxt/fileCitation/titlStmt/IDno/values_$lang"] = $payload["additional"]["fileDscr"]["fileTxt"]["fileCitation"]["titlStmt"]["IDno"] ?? '';
    return $langData;
}

function compareArrays(array $parent, array $version): array
{

    $result = [];

    $ignoredKeys = [
        "additional/obtainedAuthorization/otherAuthorizingAgency_fr",
        "additional/obtainedAuthorization/otherAuthorizingAgency_en",
        "additional/primaryInvestigator/piAffiliation/piLabo_fr",
        "additional/primaryInvestigator/piAffiliation/piLabo_en",
        "additional/fundingAgent/fundingAgentType_fr",
        "additional/fundingAgent/fundingAgentType_en",
        "additional/fundingAgent/otherFundingAgentType_fr",
        "additional/sponsor/sponsorType_fr",
        "additional/sponsor/sponsorType_en",
        "additional/sponsor/otherSponsorType_fr",
        "additional/sponsor/otherSponsorType_en",
        "additional/primaryInvestigator/isPIContact_fr",
        "additional/primaryInvestigator/isPIContact_en",
        "additional/addTeamMember_fr",
        "additional/addTeamMember_en",
        "stdyDscr/citation/IDno/identifiant_fr",
        "stdyDscr/citation/IDno/identifiant_en",
        "stdyDscr/method/notes_fr",
        "stdyDscr/method/notes_en"
    ];

    $sortedkeys = [
        "stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I_fr",
        "stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I_en",
        "stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I_fr",
        "stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I_en",
        "stdyDscr/stdyInfo/sumDscr/geogCover_fr",
        "stdyDscr/stdyInfo/sumDscr/geogCover_en",
        "stdyDscr/method/dataColl/collMode_fr",
        "stdyDscr/method/dataColl/collMode_en",
        "stdyDscr/method/dataColl/sampProc_fr",
        "stdyDscr/method/dataColl/sampProc_en",
        "stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_fr",
        "stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_en",
        "stdyDscr/stdyInfo/sumDscr/dataKind_fr",
        "stdyDscr/stdyInfo/sumDscr/dataKind_en"
    ];

    foreach ($parent as $key => $value) {

        $versionValue = $version[$key] ?? null;
        $lang = substr($key, -2);

        // 1. topcClas : comparaison par vocab
        if ($key === "stdyDscr/stdyInfo/subject/topcClas_$lang") {

            $parentGrouped = groupByVocab($value);
            $versionGrouped = groupByVocab($versionValue ?? []);

            $allVocabs = array_unique(array_merge(
                array_keys($parentGrouped),
                array_keys($versionGrouped)
            ));

            foreach ($allVocabs as $vocab) {
                $parentGroup = $parentGrouped[$vocab] ?? [];
                $versionGroup = $versionGrouped[$vocab] ?? [];

                $result[$vocab . '_' . $lang . '_status'] =
                    !areValuesEqual($parentGroup, $versionGroup, true);
            }

            continue;
        }

        // 2. authorizingAgency
        if ($key === "stdyDscr/studyAuthorization/authorizingAgency_$lang") {

            $key = "stdyDscr/studyAuthorization/authorizingAgency/values_$lang";

            $parentTransformed = $parent["additional/obtainedAuthorization/otherAuthorizingAgency_$lang"] ?? [];
            $versionTransformed = $version["additional/obtainedAuthorization/otherAuthorizingAgency_$lang"] ?? [];

            $parentTransformed[] = ["name" => $value[1] ?? null];
            $versionTransformed[] = ["name" => $versionValue[1] ?? null];

            $result[$key . '_status'] =
                !areValuesEqual($parentTransformed, $versionTransformed);

            continue;
        }

        // 4. othId
        if ($key === "tdyDscr/citation/rspStmt/othId_$lang") {

            $value[] = ["teamMemberLabo" => $parent["additional/TeamMember/IsTeamMemberContact_$lang"] ?? []];
            $versionValue[] = ["isPiContact" => $version["additional/TeamMember/IsTeamMemberContact_$lang"] ?? []];

            $result[$key . '_status'] =
                !areValuesEqual($value, $versionValue);

            continue;
        }


        // 5. fundingAgent
        if ($key === "stdyDscr/citation/prodStmt/fundAg/values_$lang") {

            $value[] = ["agentType" => $parent["additional/fundingAgent/otherFundingAgentType_$lang"] ?? []];
            $versionValue[] = ["agentType" => $version["additional/fundingAgent/otherFundingAgentType_$lang"] ?? []];

            $result[$key . '_status'] =
                !areValuesEqual($value, $versionValue);

            continue;
        }

        // 6. producer
        if ($key === "stdyDscr/citation/prodStmt/producer/values_$lang") {

            $value[] = ["sponsorType" => $parent["additional/sponsor/sponsorType_$lang"] ?? []];
            $versionValue[] = ["sponsorType" => $version["additional/sponsor/sponsorType_$lang"] ?? []];
            $value[] = ["otherSponsorType" => $parent["additional/sponsor/otherSponsorType_$lang"] ?? []];
            $versionValue[] = ["otherSponsorType" => $version["additional/sponsor/otherSponsorType_$lang"] ?? []];

            $result[$key . '_status'] =
                !areValuesEqual($value, $versionValue);

            continue;
        }

        if ($key === "stdyDscr/method/notes_$lang") {

            $parentGrouped = prepareBySubject($value);
            $versionGrouped = prepareBySubject($versionValue ?? []);

            $parentFollowupNotes =  $parentGrouped['follow-up'] ?? [];
            $versionFollowupNotes =  $versionGrouped['follow-up'] ?? [];
            $result["followupNotes" . '_' . $lang . '_status'] =
                !areValuesEqual($parentFollowupNotes, $versionFollowupNotes, true);

            continue;
        }

        if ($key === "stdyDscr/citation/distStmt/contact_$lang") {
            $key = "stdyDscr/citation/distStmt/contact/values_$lang";
        }

        if ($key === "stdyDscr/dataAccs/useStmt/contact_$lang") {
            $key = "stdyDscr/dataAccs/useStmt/contact/values_$lang";
        }

        if ($key === "stdyDscr/stdyInfo/qualityStatement/otherQualityStatement_$lang") {
            $key = "stdyDscr/stdyInfo/qualityStatement/otherQualityStatement/values_$lang";
        }

        // 7. comparaison standard
        if (!in_array($key, $ignoredKeys)) {
            $result[$key . '_status'] = !areValuesEqual($value, $versionValue, in_array($key, $sortedkeys));
        }
    }

    return $result;
}

function areValuesEqual($a, $b, $sort = false): bool
{
    if (is_array($a) && is_array($b)) {
        if (count($a) !== count($b)) {
            return false;
        }

        // Trier pour éviter les faux négatifs (ordre différent)
        if ($sort) {
            sort($a);
            sort($b);
        }

        foreach ($a as $key => $value) {
            if (!array_key_exists($key, $b)) {
                return false;
            }

            if (!areValuesEqual($value, $b[$key])) {
                return false;
            }
        }

        return true;
    }

    $a = is_string($a) ? cleanString($a) : $a;
    $b = is_string($b) ? cleanString($b) : $b;

    return $a === $b;
}

function groupByVocab(array $data): array
{
    $grouped = [];

    foreach ($data as $item) {
        $vocab = $item['vocab'];
        $grouped[$vocab][] = $item;
    }

    return $grouped;
}


function prepareBySubject(array $data): array
{
    $result = [];

    foreach ($data as $item) {
        if (!isset($item['subject'])) {
            continue;
        }

        $subject = $item['subject'];

        // On garde seulement ce qui est utile pour la comparaison
        $result[$subject] = $item['values'] ?? null;
    }

    return $result;
}


function cleanString(string $value)
{
    return html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
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



function nada_list_institutions_valid_shortcode()
{
    $table = new InstitutionsTable(1, 'search_valid', 'approved');
    $table->prepare_items();

    ob_start();
?>
    <div class="wrap nada-institutions-wrap">
        <div class="institution-wrapper" data-status="1" data-search-key="search_valid">
            <div class="nada-table-content">
                <?php $table->display(); ?>
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
}

/**
 * Shortcode pour afficher la liste des institutions en attente de validation
 *
 * @return string HTML du tableau
 */
function nada_list_institutions_waiting_shortcode()
{
    $table = new InstitutionsTable(0, 'search_waiting', 'pending');
    $table->prepare_items();

    ob_start();
?>
    <div class="wrap nada-institutions-wrap">
        <div class="institution-wrapper" data-status="0" data-search-key="search_waiting">
            <div class="nada-table-content">
                <?php $table->display(); ?>
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
}

/**
 * Shortcode pour afficher une statistique spécifique selon type
 *
 * Usage:
 * [nada_statistics type="total_published"]
 * [nada_statistics type="studies_observational"]
 * [nada_statistics type="studies_interventional"]
 * [nada_statistics type="new_studies"]
 * [nada_statistics type="catalog_views"]
 *
 * @param array $atts Attributs du shortcode
 * @return string Valeur de la statistique
 */
function nada_statistics_shortcode($atts)
{
    $atts = shortcode_atts([
        'type' => 'total_published',
        'default' => '0',
        'prefix' => '',
        'suffix' => '',
    ], $atts);
    $stats = nada_get_statistics();

    $value = (int) $stats[$atts['type']];
    return esc_html($atts['prefix'] . $value . $atts['suffix']);
}

function nada_catalog_statistics_shortcode($atts)
{
    $lang = function_exists('pll_current_language')
            ? pll_current_language()
            : 'fr';

    $atts = shortcode_atts([
            'type'    => '',
            'default' => '0',
            'prefix'  => '',
            'suffix'  => '',
    ], $atts);

    if (empty($atts['type'])) {
        return esc_html($atts['default']);
    }

    static $data = null;
    if ($data === null) {
        try {
            $service = new Nada_Statistics_Service(new Nada_Api());
            $data = $service->get_statistics_catalog_dashboard($lang);
        } catch (Exception $e) {
            return esc_html($atts['default']);
        }
    }

    $stats = $data['data']['response'] ?? [];
    if (!isset($stats[$atts['type']])) {
        return esc_html($atts['default']);
    }

    $value = (int) $stats[$atts['type']];
    return esc_html($atts['prefix'] . $value . $atts['suffix']);
}

function redirect_404_page()
{
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    include(get_query_template('404'));
    exit;
}


// page dictionar de donnees
function variable_dictionary_shortcode($atts)
{
    if (!current_user_can('admin_fresh')) {
        redirect_404_page();
    }

    $section = get_query_var('vd_section', '') ?? '0';
    $type = get_query_var('vd_type', '') ?? 'metadata';
    $refName = '';
    $is_introduction = (get_query_var('vd_type') === 'introduction');
    if ($type === 'introduction') {
        $is_introduction = true;
    } elseif ($type === 'metadata' && !empty($section)) {
        $type = 'metadata';
        $section = $section;
    } elseif ($type === 'vocabulary') {
        $type = 'vocabulary';
        $refName = get_query_var('vd_ref_name', '');
    }

    // Get language
    $lang = 'fr';
    if (function_exists('pll_current_language')) {
        $current_language = pll_current_language();
        if ($current_language) {
            $lang = $current_language;
        }
    }
    // Inclure le template pour l’affichage
    ob_start();
    $introduction = render_introduction_schema($lang);
    $vocabulary = render_vocabulary($refName, $lang);
    $metadata = render_metadata_fields($section, $lang);
    $template_path = NADA_ID_PLUGIN_DIR . 'public/templates/variable-dictionary/main.php';

    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p class="nada-id-error">Template manquant.</p>';
    }

    return ob_get_clean();
}

function render_sidebar($type, $current_section, $current_ref, $is_introduction, $lang)
{
    $base_url = get_base_url();
    ob_start();
    $template_path = NADA_ID_PLUGIN_DIR . 'public/templates/variable-dictionary/sidebar.php';

    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p class="nada-id-error">Template manquant.</p>';
    }

    return ob_get_clean();
}

/** fonction pour recupere la liste des section et rendre sous format html */
function get_list_sections_schema($current_section, $lang, $base_url)
{
    global $wpdb;
    $table = $wpdb->prefix . 'nada_metadata_fields';
    $response = $wpdb->get_results("
        SELECT section_id, element_id, label_fr, label_en 
        FROM {$table} 
        WHERE parent = 'FreshSchema'
        ORDER BY section_id ASC
    ", ARRAY_A);

    $output = '';

    if ($response) {
        foreach ($response as $row) {
            $sec_id = $row['section_id'];
            $title = ($lang === 'fr') ? $row['label_fr'] : $row['label_en'];
            $section = $row['element_id'] . ' ' . $title;
            $active_class = ($sec_id == $current_section) ? 'active' : '';

            $url = $base_url . 'metadata/' . $sec_id;
            $output .= sprintf(
                '<a href="%s" class="technical-documentation-nav-link %s">%s</a>',
                esc_url($url),
                $active_class,
                esc_html($section)
            );
        }
    }

    return $output;
}

/**
 * foncution pour recupere la list des vocab selon chaque sections 
 */
function get_list_vocabulary_per_sections($current_ref, $lang, $base_url)
{
    global $wpdb;
    $table_fields = $wpdb->prefix . 'nada_metadata_fields';
    $table_refs = $wpdb->prefix . 'nada_referentiels';

    // 1. Fetch Sections
    $sections = $wpdb->get_results("
        SELECT section_id, element_id, label_fr, label_en
        FROM {$table_fields} 
        WHERE parent = 'FreshSchema'
        ORDER BY section_id ASC
    ", ARRAY_A);

    // 2. Fetch Vocab Fields
    $vocab_fields = $wpdb->get_results("
        SELECT section_id, element_id, vc, label_fr, label_en 
        FROM {$table_fields} 
        WHERE vc IS NOT NULL AND vc != '' AND vc != 'NULL'
        ORDER BY element_id ASC
    ", ARRAY_A);

    $valid_refs = $wpdb->get_col("SELECT referentiel_name FROM {$table_refs} WHERE is_enabled = 1");

    $valid_refs_lookup = array_flip($valid_refs);
    $external_vocabs = ['Pathology' => true];
    if (empty($sections) || empty($vocab_fields)) {
        return '';
    }

    $grouped_vocabs = [];
    foreach ($vocab_fields as $field) {
        $vc = $field['vc'];
        if (isset($valid_refs_lookup[$vc]) || isset($external_vocabs[$vc])) {
            $grouped_vocabs[$field['section_id']][] = $field;
        }
    }
    $output = '';

    foreach ($sections as $section) {
        $sec_id = $section['section_id'];
        if (empty($grouped_vocabs[$sec_id])) {
            continue;
        }

        $section_vocabs = $grouped_vocabs[$sec_id];

        $label = ($lang === 'fr') ? $section['label_fr'] : $section['label_en'];
        $sec_title = $section['element_id'] . ' ' . $label;
        $has_active = false;
        foreach ($section_vocabs as $field) {
            if ($field['vc'] === $current_ref) {
                $has_active = true;
                break;
            }
        }

        $output .= sprintf(
            '<div class="technical-documentation-subsection">
                <button class="technical-documentation-subsection-toggle %s" type="button">
                    <span>%s</span>
                    <i class="technical-documentation-arrow">%s</i>
                </button>
                <div class="technical-documentation-subsection-items %s">',
            $has_active ? 'active' : '',
            esc_html($sec_title),
            $has_active ? '<i class="fa fa-angle-down"></i>' : '<i class="fa fa-angle-right"></i>',
            $has_active ? 'show' : ''
        );

        $rendered_vcs = [];
        foreach ($section_vocabs as $field) {
            $vocab_name = $field['vc'];
            if (isset($rendered_vcs[$vocab_name])) continue;
            $rendered_vcs[$vocab_name] = true;

            $active_class = ($vocab_name === $current_ref) ? 'active' : '';
            $display_name = ($lang === 'fr') ? $field['label_fr'] : $field['label_en'];
            $url = $base_url . 'vocabulary/' . $vocab_name;
            $output .= sprintf(
                '<a href="%s" class="technical-documentation-nav-link %s">%s</a>',
                esc_url($url),
                $active_class,
                esc_html($display_name)
            );
        }

        $output .= '</div></div>';
    }
    return $output;
}

/**
 * function pour recupere les infos du vocab 
 */
function get_vocab_data($vocab_name)
{
    global $wpdb;
    $table = $wpdb->prefix . 'nada_metadata_fields';

    return $wpdb->get_row(
        $wpdb->prepare(
            "SELECT id, label_fr, label_en, description_fr, description_en 
             FROM {$table} 
             WHERE vc = %s 
             LIMIT 1",
            $vocab_name
        )
    );
}

/**
 * fonction pour rendre la liste des champs selon chaque section
 */
function render_metadata_fields($section_id, $lang)
{
    global $wpdb;
    $section_id = sanitize_text_field($section_id);
    $table = $wpdb->prefix . 'nada_metadata_fields';

    // Récupération des champs pour cette section
    $metadata_fields = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$table} WHERE section_id = %s ORDER BY element_id",
        $section_id
    ), ARRAY_A);

    if (empty($metadata_fields)) {
        return '';
    }

    // récupère le titre du section  depuis la DB
    $section_title = get_section_title($section_id, $lang);

    // Inclure le template pour l’affichage
    ob_start();
    $template_path = NADA_ID_PLUGIN_DIR . 'public/templates/variable-dictionary/metadata-fields-body.php';

    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p class="nada-id-error">Template manquant.</p>';
    }

    return ob_get_clean();
}


/**
 * Render vocabulary table
 */
function render_vocabulary($ref_name, $lang)
{
    global $wpdb;
    $ref_name = sanitize_text_field($ref_name);

    // récupérer l'ID du référentiel
    $ref_table = $wpdb->prefix . 'nada_referentiels';
    $referentiel = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$ref_table} WHERE referentiel_name = %s AND is_enabled = 1",
        $ref_name
    ), ARRAY_A);

    // Liste des vocabulaires externes autorisés
    $external_vocabs = ['Pathology'];
    $is_external = in_array($ref_name, $external_vocabs);

    // Si le référentiel n'existe pas et que ce n'est pas un vocab externe, retourner vide
    if (!$referentiel && !$is_external) {
        return '';
    }

    // Initialiser le tableau des items
    $items = [];
    // récupérer les items seulement si ce n'est pas un vocab externe
    if (!$is_external && $referentiel) {
        $table_items = $wpdb->prefix . 'nada_referentiel_items';
        $items = $wpdb->get_results($wpdb->prepare(
            "SELECT id, label_fr, label_en, desc_fr, desc_en, uri, uri_esv, uri_mesh, identifier, siren, status 
                FROM {$table_items} 
                WHERE referentiel_id = %d 
                ORDER BY id ASC",
            $referentiel['id']
        ), ARRAY_A);
    }

    // récupérer le titre et description du vc depuis la DB
    $vocab = get_vocab_data($ref_name, $lang);
    if (!$vocab) {
        return '';
    }
    $vocab_title = ($lang === 'fr') ? $vocab->label_fr : $vocab->label_en;
    $vocab_description = ($lang === 'fr')
        ? ($vocab->description_fr ?: 'Aucune Description')
        : ($vocab->description_en ?: 'No Description');
    // Inclure le template pour l’affichage
    ob_start();
    $template_path = NADA_ID_PLUGIN_DIR . 'public/templates/variable-dictionary/vc-body.php';

    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p class="nada-id-error">Template manquant.</p>';
    }

    return ob_get_clean();
}


/**
 * fonction pour recupere le nom du section par id 
 */
function get_section_title($section_id, $lang)
{
    global $wpdb;
    $table = $wpdb->prefix . 'nada_metadata_fields';
    $section = $wpdb->get_row($wpdb->prepare(
        "SELECT label_fr, label_en FROM {$table} WHERE section_id = %s AND parent = 'FreshSchema' LIMIT 1",
        $section_id
    ));
    if ($section) {
        return ($lang === 'fr') ? $section->label_fr : $section->label_en;
    }
    return "Section $section_id";
}


/**
 * code html de l'onglet introduction
 */
function render_introduction_schema($lang)
{
    // Inclure le template pour l’affichage
    ob_start();
    $template_path = NADA_ID_PLUGIN_DIR . 'public/templates/variable-dictionary/metadata-introduction.php';

    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p class="nada-id-error">Template manquant.</p>';
    }

    return ob_get_clean();
}


/**
 * function pour afficher les liens URI sous form un URL
 */
function render_uri_link($uri)
{
    $uri = ($uri !== 'NULL') ? $uri : '';
    if ($uri && filter_var($uri, FILTER_VALIDATE_URL)) {
        echo '<a href="' . esc_url($uri) . '" target="_blank" rel="noopener">' . esc_html($uri) . '</a>';
    } else {
        echo esc_html($uri);
    }
}

// fonction pour recuper lurl du page
function get_base_url()
{
    global $post;
    if ($post) {
        return trailingslashit(get_permalink($post->ID));
    }
    return trailingslashit(get_permalink());
}

// Gestion des referentiel
function nada_list_referentiel_shortcode()
{
    $table = new RepositoryTable();
    $table->prepare_items();

    ob_start();
?>
    <div class="wrap nada-repository-wrap">
        <div class="repository-wrapper">
            <div class="nada-table-content">
                <?php $table->display(); ?>
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
}
function nada_details_referentiel_shortcode()
{
    $referentiel_id = get_query_var('ref_id')
        ? absint(get_query_var('ref_id'))
        : (isset($_GET['ref_id']) ? absint($_GET['ref_id']) : 0);

    if (!$referentiel_id) {
        redirect_404_page();
    }

    global $wpdb;
    $ref = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}nada_referentiels WHERE id = %d AND is_enabled = 1",
        $referentiel_id
    ), ARRAY_A);

    if (!$ref) {
        redirect_404_page();
    }

    $_REQUEST['ref_id'] = $referentiel_id;
    $table = new RepositoryItemsTable();
    $table->prepare_items();

    ob_start();
?>
    <div class="wrap nada-repository-wrap">
        <div class="repository-items-wrapper"
            data-referentiel-id="<?php echo esc_attr($referentiel_id); ?>">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><?php echo esc_html($ref['referentiel_name']); ?></h4>
            </div>
            <div class="nada-table-items-content">
                <?php $table->display(); ?>
            </div>

        </div>
    </div>
<?php
    return ob_get_clean();
}

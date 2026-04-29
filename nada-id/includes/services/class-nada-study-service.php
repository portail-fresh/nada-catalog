<?php

/**
 *  Nada_Study_service : (orchestrateur métier)
 * NB: Le service ne devrait pas dépendre directement de WordPress global (ex une fonction comme wp_get_current_user doit etre idealement dans le controller)
 */
class Nada_Study_Service
{
    private Nada_API $api;
    private Nada_Study_Repository $repository;
    private Nada_Deepl_Service $deeplService;
    private Nada_Study_Input_Parser $studyParser;
    private Nada_Study_Api_Mapper $studyMapper;

    public function __construct(
        Nada_Api $api,
        Nada_Study_Repository $repository,
        Nada_Deepl_Service $deeplService,
        Nada_Study_Input_Parser $studyParser,
        Nada_Study_Api_Mapper $studyMapper
    ) {
        $this->api = $api;
        $this->repository = $repository;
        $this->deeplService = $deeplService;
        $this->studyParser = $studyParser;
        $this->studyMapper = $studyMapper;
    }

    /**
     * Retourne les datasets préparés pour le controller
     */
    public function fetch_studies(array $data): array
    {
        // Tri
        $sort_by =  $data['sortBy'] ?? 'desc';
        $sort_order = $data['orderBy'] ??  null;
        $search_value = $data['search'] ?? null;

        $response = $this->api->user_datasets_post(
            $data['current_user_nada_id'],
            $data['is_admin'],
            $data['start'],
            $data['length'],
            $sort_by,
            $sort_order,
            $search_value,
            $data['lang'],
            $data['is_advanced'],
            $data['advanced_criteria'],
            $data['global_operator']
        );
        if (!$response['success']) {
            return [
                'datasets' => [],
                'total' => 0
            ];
        }

        return $response['datasets'] ?? [
            'datasets' => [],
            'total' => 0
        ];
    }
    /** Enregistrer une étude */
    public function save_study(string $current_lang, array $data): array
    {
        $postData = $data['post_data'];
        $userInfo = $data['user_info'];
        $responses = [];

        //== Gérer le mode (add/edit/versioning)
        $modeData = $this->resolve_mode($postData);
        $mode =  $modeData['mode'];
        $baseIdno = $modeData['baseIdno'];
        $studySource = $modeData['studySource'];

        //== Parser les données du formulaire
        $translatableFields = $this->deeplService->get_translatable_fields();
        $parsed =  $this->studyParser->parse($postData, $userInfo, $studySource, $translatableFields, $mode);
        $structured = $parsed['structured'];

        // TRADUCTION AUTOMATIQUE AVEC DEEPL (champs simples et repeaters)
        if ($parsed['translate_enabled']) {
            try {
                $structured = $this->deeplService->translate_studies($parsed);
            } catch (Exception $e) {
                nada_id_log('Translation failed: ' . $e->getMessage());
                return [
                    'success'  => false,
                    'message'  => $current_lang == 'fr' ?
                        'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                        'A technical error occurred while generating the view. Please try again later or contact support.',
                ];
            }
        }

        //== Préparer payload + Envoyer à l’API
        foreach ($structured as $lang => $langData) {

            $idno = "$baseIdno-$lang";   // ici on fixe fr ou en

            //Préparer le payload Nada
            $study = $this->studyMapper->prepare_nada_payload($langData, $lang, $mode, $idno);
            // Envoyer à l'API
            $response = '';
            if ($mode === 'add') {
                $response = $this->api->create_study(json_encode($study), $userInfo['contributor_nada_token'],  $userInfo['current_nada_user_id']);
            } else {
                $response = $this->api->update_study($idno, json_encode($study), $userInfo['contributor_nada_token']);
            }
            $responses[$lang] = $response;

            // Si une langue échoue, arrêter
            if (
                empty($response['success']) || $response['success'] != 1 ||
                ($response['message'] ?? '') === 'VALIDATION_ERROR' ||
                ($response['data']['status'] ?? '') === 'failed' ||
                ($response['data']['status'] ?? '') === 'ACCESS-DENIED' ||
                ($response['data']) == null
            ) {
                return [
                    'success'  => false,
                    'message'  => $current_lang === 'fr'
                        ? 'Une erreur est survenue lors de la sauvegarde de l\'étude.'
                        : 'An error occurred while saving the study.',
                    'error'    => $response,
                    'response' => $responses,
                ];
            }

            // si une nouvelle institution est ajoutée dans l'etude, on l'ajoute dans le DB
            //TODO à modifier l'emplacement
            $institutions = extractInstitutionsFromStudy($structured[$lang]);
            if (!empty($institutions)) {
                foreach ($institutions as $institution) {
                    $wp_result = insertInstitution($institution);
                    if (!$wp_result) {
                        nada_id_log("Erreur lors de l’ajout de l’institution : " . $wp_result['message']);
                    }
                }
            }
        }

        // Sauvegarde WordPress
        // en cas le PI n'est renseigné, provisoirement mettre le user courant pour qu'on tombe pas 
        // dans le cas d'une etude sans PI en attendant le champs PI Soit obligatoire
        // $creatorIsPi = isset($postData['additional/isContributorPI_fr']) ? $postData['creatorIsPi_fr'] : 'true';

        $pi_data = $parsed['pi_data'];

        // Gestion PI (si nécessaire)
        $study_data = [
            "pi_email"       => $pi_data['first_pi_email'],
            "pi_name"       => $pi_data['first_pi_name'],
            "contact_mail" => $parsed['contact_mail'],
            "pi_is_current_user" => $postData['additional/isContributorPI_fr'] == 'Oui' ? true : false,
            "study_idno"     => $baseIdno,
            "study_id_fr"    => $responses['fr']['data']['dataset']['id'] ?? '',
            "study_id_en"    => $responses['en']['data']['dataset']['id'] ?? '',
            "study_title_fr"    => $responses['fr']['data']['dataset']['title'] ?? '',
            "study_title_en"    => $responses['en']['data']['dataset']['title'] ?? '',
            "status" => $parsed['status_key'],
            "is_parent" => $baseIdno == $studySource,
            "study_source" => $studySource,
            "study_type" => $postData['stdyDscr/method/notes/subject_researchType_fr'],
            "auto_save" => $postData['auto_save'] ?? false,
            "translate_enabled" => $parsed['translate_enabled'],
            "created_by" => $userInfo['contributor_wp_user_id'],
            "all_studies_processed" => $postData['allStudiesProcessed']
        ];

        $wp_result = saveStudy($study_data, $lang);


        if (!$wp_result['success']) {
            throw new Exception('Erreur lors de l’ajout du PI : ' . $wp_result['message']);
        }
        // $this->save_to_wordpress($structured, $responses);

        //Notification

        return [
            'success'  => true,
            'message'  => $current_lang === 'fr' ? 'Étude sauvegardée avec succès !' : 'Study saved successfully!',
            'response' => $responses,
        ];
    }

    private function resolve_mode(array $postData): array
    {
        $mode = '';
        $idnoInput =  $idnoInput = $postData['idno'] ?? '';
        $statusKey = $postData['status_key'];
        $published = $postData['published'];
        $isNew = empty($idnoInput);
        $studySource = '';
        if ($isNew) {
            $mode = 'add';
            $newId = getSurveyNewId();
            $studySource = $baseIdno = "FReSH-$newId";
        } else {
            // verisioning
            $studySource = get_base_idno($idnoInput, false);
            $baseIdno = get_base_idno(idno: $idnoInput);
            $isNewVersioning  = $statusKey == 'published' || ($statusKey == 'imported' && $published);

            $mode = $isNewVersioning ? 'add' : 'edit';
            $baseIdno = $isNewVersioning ? "{$studySource}-draft" : $baseIdno;
        }

        return [
            "mode" => $mode,
            "baseIdno" => $baseIdno,
            "studySource" => $studySource
        ];
    }

    public function validateStudyData(array $data, string $lang): void
    {
        $titleFr = trim($data['stdyDscr/citation/titlStmt/titl_fr'] ?? '');
        $titleEn = trim($data['stdyDscr/citation/titlStmt/titl_en'] ?? '');

        if (mb_strlen($titleFr) > 500 || mb_strlen($titleEn) > 500) {
            throw new InvalidArgumentException(
                $lang === 'fr'
                    ? "Le titre ne doit pas dépasser 500 caractères."
                    : "The title must not exceed 500 characters."
            );
        }
    }
}

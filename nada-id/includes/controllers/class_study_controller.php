<?php

/**
 *Le controller ne connaît jamais les services secondaires.
 * Il connaît seulement le service principal de son domaine. (study service: pas de traduction service)
 */
class Nada_Study_Controller
{
    private Nada_Study_Service $studyService;


    public function __construct(Nada_Study_Service $studyService)
    {
        $this->studyService = $studyService;
    }

    public function register_hooks(): void
    {
        //== List studies
        add_action('wp_ajax_nada_get_studies_paginated', [$this, 'list_studies']);
        add_action('wp_ajax_nopriv_nada_get_studies_paginated', [$this, 'list_studies']);

        //== Add study
        add_action('wp_ajax_nada_save_study', [$this, 'nada_save_study']);
    }

    public function list_studies(): void
    {
        $current_user = wp_get_current_user();
        $lang = pll_current_language() ?? 'fr';
        $data = [
            'page'     => isset($_POST['page']) ? intval($_POST['page']) : 1,
            'start'  => intval($_POST['start'] ?? 0),
            'length' => intval($_POST['length'] ?? 10),
            'sortBy' =>  $_POST['sortBy'] ?? null,
            'orderBy' =>  $_POST['orderBy'] ?? null,
            'search'   => sanitize_text_field($_POST['search'] ?? ''),
            'is_advanced'       => isset($_POST['is_advanced']) ? intval($_POST['is_advanced']) : 0,
            'advanced_criteria' => $_POST['advanced_criteria'] ?? [],
            'global_operator'   => sanitize_text_field($_POST['global_operator'] ?? 'AND'),
            'current_user_nada_id' => get_user_meta($current_user->ID, 'nada_user_id', true),
            'lang' => $lang,
            'is_admin' => user_can($current_user, 'admin_fresh') ? 1 : 0,
        ];

        $response = $this->studyService->fetch_studies($data);
        $datasets = $response['datasets'] ?? [];
        $total    = intval($response['total']);
        $html = $this->render_study_cards($datasets, $lang);

        wp_send_json([
            'html'  => $html,
            'total' => $total
        ]);
    }
    /** Sauvegarde une étude fr/en */
    public function nada_save_study(): void
    {

        $lang = pll_current_language() ?? 'fr';
        try {

            //  Controls
            $this->studyService->validateStudyData($_POST, $lang);
            // Build data
            $data = $this->build_request_data($_POST);
            // Save
            $result = $this->studyService->save_study($lang, $data);
            wp_send_json_success($result);
        } catch (InvalidArgumentException $ex) {
            // Erreur métier -> on retourne le message exact
            wp_send_json_error([
                'success' => false,
                'message' => $ex->getMessage()
            ]);
        } catch (Throwable $ex) {
            // Erreur technique -> message générique
            wp_send_json_error([
                'success'  => false,
                'error_details' => $ex->getTrace(),
                'message'  => $lang === 'fr' ? "Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support"
                    : "A technical error occurred while generating the view. Please try again later or contact support",
                'response' => 'KO'
            ]);
        }
    }

    private function render_study_cards(array $datasets, string $lang): string
    {
        ob_start();
        include NADA_ID_PLUGIN_DIR . 'public/templates/partials/helpers.php';
        include NADA_ID_PLUGIN_DIR . 'public/templates/partials/card-study.php';
        return ob_get_clean();
    }

    private function build_request_data(array $postData): array
    {
        $current_wp_user_id = get_current_user_id();
        // pour assurer : si l'etude créer est une versionné , on doit maintenir tjours le meme contributeur
        $created_by = $postData['created-by']; // nada user_id; vide si l'etude est new non edit ou versioning
        $current_wp_user_id = get_current_user_id();
        $current_nada_user_id = get_user_meta($current_wp_user_id, 'nada_user_id', true);

        // Identifier l'ID wp du contributeur de l'etude
        $wp_user_id = (!empty($created_by) && $created_by !== $current_nada_user_id)
            ? fetch_wp_user_id($created_by)
            : $current_wp_user_id;

        // nom contribiteur
        $first_name = sanitize_text_field(get_user_meta($wp_user_id, 'first_name', true) ?: '');
        $last_name  = sanitize_text_field(get_user_meta($wp_user_id, 'last_name', true)  ?: '');

        $contributorFullName = $first_name . ' ' . $last_name;
        // institution contributor
        $contributorAffiliation =  sanitize_text_field(get_user_meta($wp_user_id, 'institution', true)  ?: '');

        return [
            'post_data' => $postData,
            "user_info" => [
                "current_wp_user_id" => $current_wp_user_id,
                "current_nada_user_id" => get_user_meta($current_wp_user_id, 'nada_user_id', true),
                "contributor_nada_token" =>  get_user_meta($wp_user_id, 'nada_token', true) ?: '',
                "contributor_wp_user_id" => $wp_user_id,
                "contributor_fullname" =>  $contributorFullName,
                "contributor_affiliation" =>  $contributorAffiliation
            ]
        ];
    }
}

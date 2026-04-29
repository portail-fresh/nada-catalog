<?php

/**
 * Classe Nada_Front
 *
 * Gère la partie front-end du plugin :
 * - Chargement conditionnel des scripts/styles (selon les shortcodes présents)
 * - Passage de variables AJAX côté JS
 * - Intégration Bootstrap + Polylang (optionnelle)
 */

class Nada_Front
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Chargement des feuilles de style front-end
     */
    public function enqueue_styles(): void
    {
        global $post;

        if (!($post instanceof WP_Post)) {
            return;
        }

        // === CSS communs ===
        wp_enqueue_style(
            'bootstrap-css',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
            [],
            '5.3.3'
        );

        wp_enqueue_style(
            'nada-id-global',
            plugin_dir_url(dirname(__FILE__)) . 'public/css/global.css',
            ['bootstrap-css'],
            NADA_ID_VERSION
        );

        wp_enqueue_style(
            'nada-data-table-style-css',
            plugin_dir_url(dirname(__FILE__)) . 'public/css/data-table-style.css',
            ['bootstrap-css'],
            NADA_ID_VERSION
        );

        wp_enqueue_style(
            'nada-id-modal',
            plugin_dir_url(dirname(__FILE__)) . 'public/css/modal.css',
            ['bootstrap-css'],
            NADA_ID_VERSION
        );
        // === Add Study ===
        if (has_shortcode($post->post_content, 'nada_id_add_study')) {
            wp_enqueue_style(
                'nada-add-study-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/add-study.css',
                ['bootstrap-css', 'nada-id-global'],
                NADA_ID_VERSION
            );
        }


        // === Liste des études ===
        if (has_shortcode($post->post_content, 'nada_id_list_studies')) {
            wp_enqueue_style(
                'nada-list-studies-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/list-studies.scss',
                ['bootstrap-css', 'nada-id-global'],
                NADA_ID_VERSION
            );
            wp_enqueue_style(
                'nada-studies-shared-filters-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/shared-filters.css',
                ['bootstrap-css', 'nada-id-global'],
                NADA_ID_VERSION
            );
        }

        // === Détails d’étude ===
        if (has_shortcode($post->post_content, 'nada_id_study_details')) {
            wp_enqueue_style(
                'nada-details-study-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/details-study.scss',
                ['bootstrap-css', 'nada-id-global',],
                NADA_ID_VERSION
            );
        }

        // === Détails du referentiel ===
        if (has_shortcode($post->post_content, 'nada_id_referentiel_details')) {
            wp_enqueue_style(
                'nada-details-referentiel-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/details-referentiel.css',
                ['bootstrap-css', 'nada-id-global'],
                NADA_ID_VERSION
            );
        }

        // === List catalogs ===
        if (has_shortcode($post->post_content, 'nada_id_list_catalogs')) {
            wp_enqueue_style(
                'nada-list-catalogs-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/catalogs.css',
                ['bootstrap-css', 'nada-id-global'],
                NADA_ID_VERSION
            );
            wp_enqueue_style(
                'nada-studies-shared-filters-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/shared-filters.css',
                ['bootstrap-css', 'nada-id-global'],
                NADA_ID_VERSION
            );
        }

        // === Import study ===
        if (has_shortcode($post->post_content, 'nada_id_upload_v1')) {
            wp_enqueue_style(
                'nada-list-catalogs-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/import-study.css',
                ['bootstrap-css', 'nada-id-global'],
                NADA_ID_VERSION
            );
        }

        // === variable dictionary ===
        if (has_shortcode($post->post_content, 'variable_dictionary')) {
            wp_enqueue_style(
                'variable-dictionary-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/variable-dictionary.css',
                ['bootstrap-css', 'nada-id-global',],
                NADA_ID_VERSION
            );
        }
    }

    /**
     * Chargement des scripts front-end
     */
    public function enqueue_scripts(): void
    {
        global $post;

        if (!($post instanceof WP_Post)) {
            return;
        }


        // === Bootstrap JS (chargé partout) ===
        wp_enqueue_script(
            'bootstrap-js',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
            [],
            '5.3.3',
            true
        );

        // === Scripts communs ===
        wp_enqueue_script(
            'nada-global-js',
            plugin_dir_url(dirname(__FILE__)) . 'public/js/global.js',
            ['jquery'],
            NADA_ID_VERSION,
            true
        );

        // Passe la variable PHP vers le JS
        $current_language = function_exists('pll_current_language') ? pll_current_language() : 'fr';
        wp_localize_script('nada-global-js', 'nada_global_vars', [
            'lang' => $current_language,
            'ajax_url' => admin_url('admin-ajax.php'),
            'per_page' => 10,
            'auto_save_interval' => get_automatic_save_interval_fresh(),
            'site_url' =>  get_site_url()
        ]);

        // script de gestion du switch de langue
        wp_enqueue_script(
            'language-switcher',
            plugin_dir_url(dirname(__FILE__)) . 'public/js/language-switcher.js',
            ['jquery'],
            NADA_ID_VERSION,
            true
        );
        wp_localize_script(
            'language-switcher',
            'language_vars',
            [
                'lang' => $current_language
            ]
        );

        wp_enqueue_script(
            'orcid-js',
            plugin_dir_url(dirname(__FILE__)) . 'public/js/orcid.js',
            ['jquery'],
            NADA_ID_VERSION,
            true
        );

        wp_localize_script('orcid-js', 'orcid_vars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'lang' => $current_language
        ]);


        // ajout modification referentiel
        wp_enqueue_script(
            'nada-management-repository',
            plugin_dir_url(dirname(__FILE__)) . 'public/js/repository/main-repository.js',
            ['jquery', 'bootstrap-js'],
            NADA_ID_VERSION,
            true
        );

        wp_localize_script('nada-management-repository', 'management_repository_vars', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'lang' => $current_language,
            'nonce' => wp_create_nonce('management_repository_nonce'),
        ]);

        // === Add Study ===

        if (has_shortcode($post->post_content, 'nada_id_add_study')) {

            wp_enqueue_script(
                'nada-contact-points',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/sync-contact-points.js',
                ['jquery'],
                NADA_ID_VERSION,
                true
            );

            if (is_user_logged_in()) {
                $current_user = wp_get_current_user();
                wp_localize_script('nada-contact-points', 'wpUserData', array(
                    'first_name' => $current_user->user_firstname,
                    'last_name' => $current_user->user_lastname,
                    'email' => $current_user->user_email,
                ));
            }

            // Charger le script JS une seule fois
            wp_enqueue_script(
                'nada-add-study', // handle unique
                plugin_dir_url(dirname(__FILE__)) . 'public/js/add-study.js',
                ['jquery'],       // dépendances
                NADA_ID_VERSION,  // version du plugin
                true              // charger dans le footer
            );

            wp_enqueue_script(
                'nada-add-repeater', // handle unique
                plugin_dir_url(dirname(__FILE__)) . 'public/js/init-repeater.js',
                ['jquery', 'jquery-ui-autocomplete'],       // dépendances
                NADA_ID_VERSION,  // version du plugin
                true              // charger dans le footer
            );

            wp_enqueue_script(
                'nada-init-function', // handle unique
                plugin_dir_url(dirname(__FILE__)) . 'public/js/init-function.js',
                ['jquery'],       // dépendances
                NADA_ID_VERSION,  // version du plugin
                true              // charger dans le footer
            );


            // Passer toutes les variables JS nécessaires pour AJAX
            wp_localize_script('nada-add-study', 'nadaAddStudyVars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'icdSearchAction' => 'icd_search',   // action AJAX pour recherche ICD
                'icdFetchAction' => 'icd_fetch',    // action AJAX pour fetch ICD
                'addStudyAction' => 'add_study'     // action AJAX pour ajouter étude
            ]);

            wp_enqueue_script(
                'nada-contribute-study',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/contribute-study/main.js',
                ['jquery'],
                NADA_ID_VERSION,
                true
            );

            $inline_data = [
                'lang' => $current_language,
                'data' => $GLOBALS['nada_contribute_study_data'] ?? [],
            ];

            wp_add_inline_script(
                'nada-add-study',
                'window.nadaContributeStudy = ' . wp_json_encode($inline_data) . ';',
                'before'
            );

            wp_script_add_data('nada-contribute-study', 'type', 'module');
        }

        // === Upload ===
        if (has_shortcode($post->post_content, 'nada_id_upload_v1')) {
            wp_enqueue_script('nada-import-study', plugin_dir_url(dirname(__FILE__)) . 'public/js/import-study.js', ['jquery'], NADA_ID_VERSION, true);
            wp_localize_script('nada-import-study', 'nada_import_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
        }

        // === List Studies ===
        if (has_shortcode($post->post_content, 'nada_id_list_studies')) {
            wp_enqueue_script(
                'nada-list-studies-shared-filters',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/advanced-filter/shared-filters.js',
                ['jquery', 'bootstrap-js'],
                NADA_ID_VERSION,
                true
            );
            wp_enqueue_script(
                'nada-list-studies',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/list-studies-card.js',
                ['jquery', 'bootstrap-js'],
                NADA_ID_VERSION,
                true
            );

            // Localisation spécifique au script list-studies
            wp_localize_script('nada-list-studies', 'study_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'listCreationDateKey' => __('listCreationDateKey', 'nada-id'),
                'listStatusKey' => __('listStatusKey', 'nada-id'),
                'listUserKey' => __('listUserKey', 'nada-id'),
                'listLastModificator' => __('listLastModificator', 'nada-id'),
                'listModificationDateKey' => __('listModificationDateKey', 'nada-id'),
                'listPublishKey' => __('listPublishKey', 'nada-id'),
            ]);
        }
        // === List ref ===
        if (has_shortcode($post->post_content, 'nada_id_list_referentiel')) {
            wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['jquery'], '1.13.6', true);
            wp_enqueue_script(
                'nada-list-referentiel',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/referentiels.js',
                ['jquery', 'bootstrap-js'],
                NADA_ID_VERSION,
                true
            );

            // Localisation spécifique au script list-studies
            wp_localize_script('nada-list-referentiel', 'referentiel_vars', [
                'ajax_url' => admin_url('admin-ajax.php')
            ]);
        }
        // === List Institutions ===
        if (
            has_shortcode($post->post_content, 'nada_list_institutions_valid')
            || has_shortcode($post->post_content, 'nada_list_institutions_waiting')
        ) {
            wp_enqueue_script(
                'nada-list-institutions',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/institutions.js',
                ['jquery'],
                NADA_ID_VERSION,
                true
            );

            // Localisation spécifique au script list-studies
            wp_localize_script('nada-list-institutions', 'institutions_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'lang' => $current_language,
                'nonce' => wp_create_nonce('nada_institutions_nonce'),
            ]);

            wp_enqueue_style(
                'nada-institutions-styles',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/list-institutions.css',
                ['bootstrap-css', 'nada-id-global'],
                NADA_ID_VERSION
            );
        }
        // === List Catalogs ===
        if (has_shortcode($post->post_content, 'nada_id_list_catalogs')) {
            wp_enqueue_script(
                'nada-list-studies',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/advanced-filter/shared-filters.js',
                ['jquery', 'bootstrap-js'],
                NADA_ID_VERSION,
                true
            );
            wp_enqueue_script(
                'nada-list-catalogs',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/list-catalogs.js',
                ['jquery', 'bootstrap-js'],
                NADA_ID_VERSION,
                true
            );


            wp_localize_script('nada-list-catalogs', 'catalog_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                //   'nonce'    => wp_create_nonce('list_catalog_nonce'),
            ]);
        }


        // === details study ===
        if (has_shortcode($post->post_content, 'nada_id_study_details')) {
            wp_enqueue_script(
                'nada-details-study',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/details-study.js',
                ['jquery'],
                NADA_ID_VERSION,
                true
            );
        }

        // === details referentiel ===
        if (has_shortcode($post->post_content, 'nada_id_referentiel_details')) {
            wp_enqueue_script(
                'nada-referentiel-items',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/repository/detail-repository.js',
                ['jquery'],
                NADA_ID_VERSION,
                true
            );
            wp_localize_script('nada-referentiel-items', 'management_repository_items_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'lang' => $current_language,
                'nonce' => wp_create_nonce('nada_repository_items_nonce'),
            ]);
        }


        // === variable-dictionary ===
        if (has_shortcode($post->post_content, 'variable_dictionary')) {
            wp_enqueue_script(
                'variable-dictionary',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/variable-dictionary.js',
                ['jquery'],
                NADA_ID_VERSION,
                true
            );
            $current_language = function_exists('pll_current_language') ? pll_current_language() : 'fr';
            wp_localize_script('variable-dictionary', 'variable_dictionary_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('variable_dictionary_nonce'),
                'lang' => $current_language
            ]);
        }

        //Appel obligatoire ici a la fin des autre appel JS: la gestion de local storage pour le filter mon espace et liste catalogues
        wp_enqueue_script(
            'nada-advanced-filter',
            plugin_dir_url(dirname(__FILE__)) . 'public/js/advanced-filter/main.js',
            ['jquery'],
            NADA_ID_VERSION,
            true
        );
    }

    /*
     *  Charger le script "nada-contribute-study" en tant que module ES (type="module")
     */
    public function force_es_module($tag, $handle, $src)
    {
        $modules = ['nada-add-study'];

        if (in_array($handle, $modules, true)) {
            return '<script type="module" src="' . esc_url($src) . '"></script>';
        }

        return $tag;
    }
}

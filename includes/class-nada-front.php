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

        // === Add Study ===
        if (has_shortcode($post->post_content, 'nada_id_add_study') || has_shortcode($post->post_content, 'nada_id_study_details')) {
            wp_enqueue_style(
                'nada-add-study-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/add-study.css',
                ['bootstrap-css', 'nada-id-global'],
                NADA_ID_VERSION
            );
        }

        // === Liste des études ===
        if (has_shortcode($post->post_content, 'nada_id_list_studies')) {
            wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css');
            wp_enqueue_style(
                'nada-list-studies-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/list-studies.scss',
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
            'nada-admin-js',
            plugin_dir_url(__FILE__) . 'public/js/admin.js',
            ['jquery'],
            NADA_ID_VERSION,
            true
        );

        // Passe la variable PHP vers le JS
        $current_language = function_exists('pll_current_language') ? pll_current_language() : 'fr';
        wp_localize_script('nada-admin-js', 'nada_vars', [
            'lang'     => $current_language,
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);

        // === Add Study ===
        if (has_shortcode($post->post_content, 'nada_id_add_study')) {

            wp_enqueue_script(
                'nada-add-study',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/add-study.js',
                ['jquery'],
                NADA_ID_VERSION,
                true
            );

            wp_localize_script('nada-add-study', 'nadaAddStudyVars', [
                'ajax_url' => admin_url('admin-ajax.php')
            ]);
        }

        // === Upload ===
        if (has_shortcode($post->post_content, 'nada_id_upload_v1')) {
            wp_enqueue_script('nada-import-study', plugin_dir_url(dirname(__FILE__)) . 'public/js/import-study.js', ['jquery'], NADA_ID_VERSION, true);
            wp_localize_script('nada-import-study', 'nada_import_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
        }

        // === List Studies ===
        if (has_shortcode($post->post_content, 'nada_id_list_studies')) {
            wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['jquery'], '1.13.6', true);
            wp_enqueue_script(
                'nada-list-studies',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/list-studies.js',
                ['jquery', 'bootstrap-js'],
                NADA_ID_VERSION,
                true
            );

            // Localisation spécifique au script list-studies
            wp_localize_script('nada-list-studies', 'study_vars', [
                'ajax_url' => admin_url('admin-ajax.php')
            ]);
        }

        // === List Catalogs ===
        if (has_shortcode($post->post_content, 'nada_id_list_catalogs')) {
            wp_enqueue_script(
                'nada-list-catalogs',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/list-catalogs.js',
                ['jquery', 'bootstrap-js'],
                NADA_ID_VERSION,
                true
            );

            wp_localize_script('nada-list-catalogs', 'catalog_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('list_catalog_nonce'),
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
    }
}

<?php

/**
 * Classe Nada_Admin
 *
 * Cette classe gère la partie administration du plugin (interface dans le back-office WordPress).
 * Responsabilités :
 * - Ajouter des menus et sous-menus dans le dashboard
 * - Charger les assets spécifiques à l’administration (CSS/JS)
 * - Afficher les pages de configuration (par ex. : réglages de connexion à NADA)
 * - Gérer les formulaires côté admin
 */

class Nada_Admin
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function add_admin_menu()
    {
        add_menu_page(
            'NADA ID',                // Titre de la page
            'NADA ID',                // Label du menu
            'manage_options',         // Capability
            'nada-id',                // Slug
            [$this, 'render_admin_page'], // Callback
            'dashicons-id',           // Icône
            25                        // Position
        );
    }

    public function render_admin_page(): void 
    {
        include NADA_ID_PLUGIN_DIR . 'admin/templates/settings-page.php';
    }

    public function enqueue_styles(): void
    {
        global $post;

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
            '1.0.0'
        );

        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'nada_id_add_study')) {
            wp_enqueue_style(
                'nada-add-study-css',
                plugin_dir_url(dirname(__FILE__)) . 'public/css/add-study.css',
                ['bootstrap-css', 'nada-id-global'],
                '1.0.0'
            );
        }
    }

    public function enqueue_scripts(): void
    {
        global $post;

        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'nada_id_add_study')) {
            wp_enqueue_script(
                'nada-add-study',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/add-study.js',
                [],
                '1.0',
                true
            );

            wp_localize_script('nada-add-study', 'nada_ajax', [
                'ajax_url' => admin_url('admin-ajax.php')
            ]);
        }


        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'nada_id_upload_v1')) {
            wp_enqueue_script(
                'nada-add-study',
                plugin_dir_url(dirname(__FILE__)) . 'public/js/import-study.js',
                [],
                '1.0',
                true
            );

            wp_localize_script('nada-add-study', 'nada_ajax', [
                'ajax_url' => admin_url('admin-ajax.php')
            ]);
        }

        // Bootstrap JS
        wp_enqueue_script(
            'bootstrap-js',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
            [],
            '5.3.3',
            true
        );
    }
}

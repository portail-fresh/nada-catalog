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
        include_once NADA_ID_PLUGIN_DIR . 'admin/templates/settings-page.php';
    }

    public function enqueue_styles(): void
    {
        wp_enqueue_style(
            'bootstrap-css',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
            [],
            '5.3.3'
        );
    }

    public function enqueue_scripts(): void
    {
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

<?php

/**
 * Plugin Name: Nada Impact
 * Plugin URI:  
 * Description: Plugin WordPress pour intégrer des fonctionnalités personnalisées liées à Plateform Nada.
 * Version:     1.0.6
 * Author:      Impact ID
 * Text Domain: nada-id
 * Ce fichier est le point d'entrée principal du plugin.
 * - Charge les dépendances
 * - Initialise les classes (admin, API, loader…)
 * - Gère les hooks d’installation, d’activation et de désactivation
 * - Lance le plugin.re
 */

if (! defined('WPINC')) {
    die;  // Sécurité : empêche un accès direct
}

/**
 * ------------------------------------------------------
 * 1. Définition des constantes
 * ------------------------------------------------------
 */
define('NADA_ID_VERSION', '1.0.6'); // incrémente la version si tu modifies le plugin
define('NADA_ID_DB_VERSION', '1.0.6'); // incrémente la version si tu modifies la base de données
define('NADA_ID_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NADA_ID_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * ------------------------------------------------------
 * 2. Chargement des dépendances
 * ------------------------------------------------------
 */
require_once NADA_ID_PLUGIN_DIR . 'includes/class-nada-loader.php';
require_once NADA_ID_PLUGIN_DIR . 'includes/class-nada-api.php';

/**
 * ------------------------------------------------------
 * 3. Gestion de la base de données
 * ------------------------------------------------------
 */

/**
 * Crée ou met à jour la table des études NADA dans la base de données
 *
 * @param string $version Version du schéma
 * @return void
 */
function nada_id_update_table($version): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'nada_list_studies'; //  le nom de ta table
    $charset_collate = $wpdb->get_charset_collate();

    // Définition de la structure
    $sql = "CREATE TABLE $table_name (
         id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
         nada_study_idno varchar(255) NOT NULL,
        nada_study_id_fr BIGINT(20) DEFAULT NULL,
        nada_study_id_en BIGINT(20) DEFAULT NULL,
        status varchar(50) DEFAULT NULL,
        pi_id bigint(20) DEFAULT NULL,
        pi_email varchar(255) DEFAULT '',
        created_by bigint(20) DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        is_approved TINYINT(1) DEFAULT NULL,
        approved_at DATETIME DEFAULT NULL,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_by bigint(20) DEFAULT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    update_option('nada_id_db_version', $version);
}

/**
 * Vérifie et applique les mises à jour de la base si nécessaire.
 */
function nada_id_check_db_update(): void
{
    $installed_ver = get_option('nada_id_db_version');
    if ($installed_ver !== NADA_ID_DB_VERSION) {
        nada_id_update_table(NADA_ID_DB_VERSION);
    }
}

/**
 * ------------------------------------------------------
 * 4. Internationalisation
 * ------------------------------------------------------
 */
function nada_id_load_language()
{
    load_plugin_textdomain('nada-id', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

/**
 * ------------------------------------------------------
 * 5. Réécriture des URLs (catalogue et alimentation)
 * ------------------------------------------------------
 */
function nada_id_register_rewrite_rules()
{
    // Détail catalogue
    add_rewrite_rule(
        '^catalogue-detail/([^/]+)/?$',
        'index.php?pagename=catalogue-detail&idno=$matches[1]',
        'top'
    );

    // Alimentation du catalogue
    add_rewrite_rule(
        '^alimentation-du-catalogue/([^/]+)/?$',
        'index.php?pagename=alimentation-du-catalogue&idno=$matches[1]',
        'top'
    );
}

/**
 * Ajoute la variable de requête 'idno'
 */
add_filter('query_vars', function ($vars) {
    $vars[] = 'idno';
    return $vars;
});

/**
 * ------------------------------------------------------
 * 6. Activation / Désactivation du plugin
 * ------------------------------------------------------
 */
register_activation_hook(__FILE__, function () {
    nada_id_update_table(NADA_ID_DB_VERSION);  // MAJ/création table
    nada_id_register_rewrite_rules();;  // Enregistre la rewrite rule
    flush_rewrite_rules();  // Flush pour que la rule soit active
});

// Hook lors de Désactivation du plugin
register_deactivation_hook(__FILE__, function () {
    flush_rewrite_rules(); // Nettoie les rewrite rules
});

/**
 * ------------------------------------------------------
 * 7. Page de configuration (admin)
 * ------------------------------------------------------
 */
if (is_admin()) {
    add_action('admin_init', function () {
        // Enregistre le paramètre d’URL de l’API NADA
        register_setting('nada_settings_group', 'nada_id_api_url', [
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => '',
        ]);

        // Ajoute une section
        add_settings_section(
            'nada_main_section',
            __('Configuration de l’API NADA', 'nada-id'),
            function () {
                echo '<p>' . esc_html__('Définissez l’URL de votre serveur NADA (dev, préprod, prod).', 'nada-id') . '</p>';
            },
            'nada_settings'
        );

        // Ajoute le champ URL
        add_settings_field(
            'nada_id_api_url_field',
            __('URL du serveur NADA', 'nada-id'),
            function () {
                $value = esc_url(get_option('nada_id_api_url', ''));
                echo '<input type="url" name="nada_id_api_url" value="' . $value . '" class="regular-text" placeholder="https://api.example.com">';
            },
            'nada_settings',
            'nada_main_section'
        );
    });
}

/**
 * ------------------------------------------------------
 * 8. Emails (nom d’expéditeur)
 * 
 * */
// Modifier le nom de l'expéditeur des emails
add_filter('wp_mail_from_name', function ($original_name) {
    return get_bloginfo('name'); // récupère le nom du site
});


/**
 * ------------------------------------------------------
 * 9. Initialisation du plugin
 * ------------------------------------------------------
 */
function nada_id_run_plugin()
{
    $plugin = new Nada_Loader();
    $plugin->run();
}

function get_nada_server_url()
{
    return get_option('nada_id_api_url', '');
}

// Hooks d’initialisation
add_action('plugins_loaded', 'nada_id_check_db_update');
add_action('plugins_loaded', 'nada_id_load_language');
add_action('init', 'nada_id_register_rewrite_rules');



// Lancer le plugin
nada_id_run_plugin();

<?php

/**
 * Plugin Name: Nada ImpactDev
 * Plugin URI:  
 * Description: Plugin WordPress pour intégrer des fonctionnalités personnalisées liées à Plateform Nada.
 * Version:     2.3.2
 * Author:      ImpactDev ID
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
define('NADA_ID_VERSION', '2.3.2'); // incrémente la version si tu modifies le plugin
define('NADA_ID_DB_VERSION', '2.3.2'); // incrémente la version si tu modifies la base de données
define('NADA_ID_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NADA_ID_PLUGIN_URL', plugin_dir_url(__FILE__));


/**
 * ------------------------------------------------------
 * 2. Chargement des dépendances
 * ------------------------------------------------------
 */
require_once NADA_ID_PLUGIN_DIR . 'includes/class-nada-loader.php';
require_once NADA_ID_PLUGIN_DIR . 'includes/api/class-nada-api.php';
require_once NADA_ID_PLUGIN_DIR . 'includes/api/class-nada-deepl.php';


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
    $table_name = $wpdb->prefix . 'nada_list_studies';
    $charset_collate = $wpdb->get_charset_collate();

    // Structure "nada_list_studies"
    $desired_columns = [
        'id' => "BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT",
        'nada_study_idno' => "VARCHAR(255) NOT NULL",
        'nada_study_id_fr' => "BIGINT(20) DEFAULT NULL",
        'nada_study_id_en' => "BIGINT(20) DEFAULT NULL",
        'nada_study_title_fr' => "VARCHAR(255) DEFAULT NULL",
        'nada_study_title_en' => "VARCHAR(255) DEFAULT NULL",
        'status' => "VARCHAR(50) DEFAULT NULL",
        'pi_id' => "BIGINT(20) DEFAULT NULL",
        'pi_email' => "VARCHAR(255) DEFAULT ''",
        'contact_email' => "VARCHAR(255) DEFAULT NULL",
        'added_by' => "BIGINT(20) DEFAULT NULL", /* Ajouté Par */
        'created_by' => "BIGINT(20) DEFAULT NULL",
        /** Contributeur */
        'created_at' => "DATETIME DEFAULT CURRENT_TIMESTAMP",
        'updated_at' => "DATETIME DEFAULT CURRENT_TIMESTAMP",
        'updated_by' => "BIGINT(20) DEFAULT NULL",
        'PEFF_filename' => "VARCHAR(255) DEFAULT NULL",
        'study_source' => "VARCHAR(255) DEFAULT NULL",
        'is_parent' => "TINYINT(1) DEFAULT 0",
        'study_type' => "VARCHAR(255) DEFAULT NULL",
    ];

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        nada_study_idno VARCHAR(255) NOT NULL,
        nada_study_id_fr BIGINT(20) DEFAULT NULL,
        nada_study_id_en BIGINT(20) DEFAULT NULL,
        nada_study_title_fr VARCHAR(255) DEFAULT NULL,
        nada_study_title_en VARCHAR(255) DEFAULT NULL,
        status VARCHAR(50) DEFAULT NULL,
        pi_id BIGINT(20) DEFAULT NULL,
        pi_email VARCHAR(255) DEFAULT '',
        contact_email VARCHAR(255) DEFAULT NULL,
        added_by BIGINT(20) DEFAULT NULL, /** Ajouté Par */
        created_by BIGINT(20) DEFAULT NULL, /** Contributeur */
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_by BIGINT(20) DEFAULT NULL,
        PEFF_filename VARCHAR(255) DEFAULT NULL,
        study_source VARCHAR(255) DEFAULT NULL,
        is_parent TINYINT(1) DEFAULT 0,
        study_type VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Vérifie les colonnes existantes
    $existing_columns = [];
    $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
    foreach ($columns as $column) {
        $existing_columns[$column->Field] = $column->Type;
    }

    // Supprime les colonnes qui ne sont plus dans la nouvelle structure
    foreach ($existing_columns as $column_name => $type) {
        if (!array_key_exists($column_name, $desired_columns)) {
            $wpdb->query("ALTER TABLE $table_name DROP COLUMN `$column_name`");
        }
    }

    // Ajoute les colonnes manquantes (au cas où dbDelta n'a pas tout fait)
    foreach ($desired_columns as $column_name => $definition) {
        if (!array_key_exists($column_name, $existing_columns)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN `$column_name` $definition");
        }
    }

    update_option('nada_id_db_version', $version);
}


function nada_id_update_referentiel_table($version): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'nada_referentiels'; //  le nom de ta table
    $charset_collate = $wpdb->get_charset_collate();

    // Définition de la structure
    $sql = "CREATE TABLE $table_name (
         id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        referentiel_name varchar(255) NOT NULL,        
        is_enabled TINYINT(1) DEFAULT true,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Met à jour la version en base
    update_option('nada_id_db_version', $version);
}

function nada_id_update_referentiel_items_table($version): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'nada_referentiel_items'; //  le nom de ta table
    $charset_collate = $wpdb->get_charset_collate();

    // Définition de la structure
    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        referentiel_id BIGINT(20) UNSIGNED NOT NULL,
        identifier VARCHAR(30) NULL,
        label_fr VARCHAR(255) NOT NULL,
        label_en VARCHAR(255) NOT NULL,
        desc_fr TEXT NULL,
        desc_en TEXT NULL,
        siren TEXT NULL,
        status TEXT NULL,
        uri TEXT NULL,
        uri_esv TEXT NULL,
        uri_mesh TEXT NULL,
        is_enabled TINYINT(1) DEFAULT true,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        CONSTRAINT fk_referentiel FOREIGN KEY (referentiel_id)
            REFERENCES {$wpdb->prefix}nada_referentiels(id)
            ON DELETE CASCADE
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Met à jour la version en base
    update_option('nada_id_db_version', $version);
}

function nada_create_metadata_fields_table($version): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'nada_metadata_fields';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        section_id VARCHAR(100) DEFAULT NULL,
        element_id VARCHAR(100) DEFAULT NULL,
        pid VARCHAR(100) DEFAULT NULL,
        parent VARCHAR(255) DEFAULT NULL ,
        variable_name VARCHAR(255) NOT NULL ,
        label_fr TEXT DEFAULT NULL,
        description_fr TEXT DEFAULT NULL,
        label_en TEXT DEFAULT NULL ,
        description_en TEXT DEFAULT NULL ,
        variable_type VARCHAR(100) DEFAULT NULL ,
        min_occur VARCHAR(50) DEFAULT NULL,
        max_occur VARCHAR(50) DEFAULT NULL,
        display_condition TEXT DEFAULT NULL,
        display_type VARCHAR(100) DEFAULT NULL,
        automatically_filled_in VARCHAR(50) DEFAULT NULL,
        vc VARCHAR(50) DEFAULT NULL,
        vc_id_element VARCHAR(100) DEFAULT NULL,
        ddic_cible TEXT DEFAULT NULL,
        notes TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Met à jour la version en base
    update_option('nada_id_db_version', $version);
}
function nada_create_contributor_pef_table($version): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'nada_contributor_pef';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        fresh_id VARCHAR(100) DEFAULT NULL,
        pef_id VARCHAR(50) DEFAULT NULL,
        first_name VARCHAR(255) DEFAULT NULL,
        last_name VARCHAR(255) DEFAULT NULL,
        email VARCHAR(255) DEFAULT NULL,
        affiliation TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    update_option('nada_id_db_version', $version);
}

function nada_id_update_institutions_table($version): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'nada_institutions';
    $charset_collate = $wpdb->get_charset_collate();

    // Définition de la structure
    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        identifier VARCHAR(30) NULL,
        label_fr VARCHAR(255) NOT NULL,
        label_en VARCHAR(255) NOT NULL,
        desc_fr TEXT NULL,
        desc_en TEXT NULL,
        siren TEXT NULL,
        status TEXT NULL,
        uri TEXT NULL,
        is_active TINYINT(1) DEFAULT true,
        state VARCHAR(100) DEFAULT NULL,
        created_by BIGINT(20) DEFAULT NULL,
        updated_by BIGINT(20) DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Met à jour la version en base
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
        nada_id_update_referentiel_table(NADA_ID_DB_VERSION);
        nada_id_update_referentiel_items_table(NADA_ID_DB_VERSION);
        nada_create_metadata_fields_table(NADA_ID_DB_VERSION);
        nada_id_update_institutions_table(NADA_ID_DB_VERSION);
        nada_create_contributor_pef_table(NADA_ID_DB_VERSION);
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
 * Ajoute les variables de requête
 */
add_filter('query_vars', function ($vars) {
    $vars[] = 'idno';
    $vars[] = 'ref_id';
    $vars[] = 'vd_ref_name'; // vd_ : variable dictionary ==> utiliser pour éviter les conflits des noms des variable dans le projet
    $vars[] = 'vd_section';
    $vars[] = 'vd_type';

    return $vars;
});


/**
 * ------------------------------------------------------
 * 5. Réécriture des URLs (catalogue et alimentation)
 * ------------------------------------------------------
 */
function nada_id_register_rewrite_rules()
{
    // Détail catalogue fr
    add_rewrite_rule(
        '^catalogue-detail/([^/]+)/?$',
        'index.php?pagename=catalogue-detail&idno=$matches[1]',
        'top'
    );

    // Détail catalogue en
    add_rewrite_rule(
        '^en/study-details/([^/]+)/?$',
        'index.php?pagename=study-details&idno=$matches[1]',
        'top'
    );

    // Alimentation du catalogue FR
    add_rewrite_rule(
        '^alimentation-du-catalogue/([^/]+)/?$',
        'index.php?pagename=alimentation-du-catalogue&idno=$matches[1]',
        'top'
    );

    // Alimentation du catalogue EN
    add_rewrite_rule(
        '^en/contribute/([^/]+)/?$',
        'index.php?pagename=contribute&idno=$matches[1]',
        'top'
    );

    // Détail référentiel - FRANÇAIS
    add_rewrite_rule(
        '^referentiel-detail/([0-9]+)/?$',
        'index.php?pagename=referentiel-detail&ref_id=$matches[1]',
        'top'
    );

    // Détail référentiel - ANGLAIS
    add_rewrite_rule(
        '^en/repository-detail/([0-9]+)/?$',
        'index.php?pagename=repository-detail&ref_id=$matches[1]',
        'top'
    );

    // Debut Page dictionnaire des variables FR ET EN


    add_rewrite_rule(
        '^en/catalog-variable-dictionary/metadata/introduction/?$',
        'index.php?pagename=catalog-variable-dictionary&vd_type=introduction',
        'top'
    );
    add_rewrite_rule(
        '^dictionnaire-des-variables-du-catalogue/metadata/introduction/?$',
        'index.php?pagename=dictionnaire-des-variables-du-catalogue&vd_type=introduction',
        'top'
    );
    add_rewrite_rule(
        '^dictionnaire-des-variables-du-catalogue/vocabulary/([^/]+)/?$',
        'index.php?pagename=dictionnaire-des-variables-du-catalogue&vd_type=vocabulary&vd_ref_name=$matches[1]',
        'top'
    );

    add_rewrite_rule(
        '^dictionnaire-des-variables-du-catalogue/metadata/([0-9]+)/?$',
        'index.php?pagename=dictionnaire-des-variables-du-catalogue&vd_type=metadata&vd_section=$matches[1]',
        'top'
    );

    add_rewrite_rule(
        '^en/catalog-variable-dictionary/vocabulary/([^/]+)/?$',
        'index.php?pagename=catalog-variable-dictionary&vd_type=vocabulary&vd_ref_name=$matches[1]',
        'top'
    );

    add_rewrite_rule(
        '^en/catalog-variable-dictionary/metadata/([0-9]+)/?$',
        'index.php?pagename=catalog-variable-dictionary&vd_type=metadata&vd_section=$matches[1]',
        'top'
    );


    //Fin Page
}



/**
 * ------------------------------------------------------
 * 6. Activation / Désactivation du plugin
 * ------------------------------------------------------
 */
register_activation_hook(__FILE__, function () {
    nada_id_check_db_update();  // MAJ/création table
    nada_id_register_rewrite_rules();  // Enregistre la rewrite rule
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


        register_setting('nada_settings_group', 'nada_id_pdad_url', [
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => '',
        ]);

        register_setting('nada_settings_group', 'nada_id_fresh_admin_email', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_email',
            'default' => '',
        ]);

        register_setting('nada_settings_group', 'nada_id_fresh_contact_email', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_email',
            'default' => '',
        ]);
        // Pramètres du CIM 11 - ICD
        register_setting('nada_settings_group', 'nada_id_icd_url', [
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => '',
        ]);
        register_setting('nada_settings_group', 'nada_id_icd_search', [
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => '',
        ]);
        register_setting('nada_settings_group', 'nada_id_client_id', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ]);
        register_setting('nada_settings_group', 'nada_id_client_secret', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ]);

        register_setting('nada_settings_group', 'nada_id_fresh_study_automatic_save_interval', [
            'type' => 'integer',
            'sanitize_callback' => 'absint', // Force la valeur à être un entier positif
            'default' => '',
        ]);
        // Paramètres DeepL
        register_setting('nada_settings_group', 'nada_id_deepl_url', [
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => '',
        ]);
        register_setting('nada_settings_group', 'nada_id_deepl_api_key', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ]);
        // Paramètres API ORCID
        register_setting('nada_settings_group', 'nada_id_api_public_orcid', [
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

        add_settings_field(
            'nada_id_pdad_url_field',
            __('URL du serveur PDAD', 'nada-id'),
            function () {
                $value = esc_url(get_option('nada_id_pdad_url', ''));
                echo '<input type="url" name="nada_id_pdad_url" value="' . $value . '" class="regular-text" placeholder="https://api.example.com">';
            },
            'nada_settings',
            'nada_main_section'
        );

        add_settings_field(
            'nada_id_fresh_admin_email_field',
            __('Email Admin Fresh', 'nada-id'),
            function () {
                $value = esc_attr(get_option('nada_id_fresh_admin_email', ''));
                echo '<input 
            type="email" 
            name="nada_id_fresh_admin_email" 
            value="' . $value . '" 
            class="regular-text" 
            placeholder="exemple@email.com"
        >';
            },
            'nada_settings',
            'nada_main_section'
        );

        add_settings_field(
            'nada_id_fresh_contact_email_field',
            __('Email Contact Fresh', 'nada-id'),
            function () {
                $value = esc_attr(get_option('nada_id_fresh_contact_email', ''));
                echo '<input 
            type="email" 
            name="nada_id_fresh_contact_email" 
            value="' . $value . '" 
            class="regular-text" 
            placeholder="exemple@email.com"
        >';
            },
            'nada_settings',
            'nada_main_section'
        );

        // Champs CIM 11 - ICD
        add_settings_field(
            'nada_id_icd_url_field',
            __('URL du serveur ICD', 'nada-id'),
            function () {
                $value = esc_url(get_option('nada_id_icd_url', ''));
                echo '<input type="url" name="nada_id_icd_url" value="' . $value . '" class="regular-text" placeholder="https://api.example.com">';
            },
            'nada_settings',
            'nada_main_section'
        );

        add_settings_field(
            'nada_id_icd_search_field',
            __('URL du serveur ICD Search', 'nada-id'),
            function () {
                $value = esc_url(get_option('nada_id_icd_search', ''));
                echo '<input type="url" name="nada_id_icd_search" value="' . $value . '" class="regular-text" placeholder="https://api.example.com">';
            },
            'nada_settings',
            'nada_main_section'
        );


        add_settings_field(
            'nada_id_client_id_field',
            __('Client ID ICD', 'nada-id'),
            function () {
                $value = esc_attr(get_option('nada_id_client_id', ''));
                echo '<input type="text"
                 name="nada_id_client_id" 
                 value="' . $value . '" 
                 class="regular-text"
                 placeholder="Votre Client ID">';
            },
            'nada_settings',
            'nada_main_section'
        );
        add_settings_field(
            'nada_id_client_secret_field',
            __('Client Secret ICD', 'nada-id'),
            function () {
                $value = esc_attr(get_option('nada_id_client_secret', ''));
                echo '<input type="text"
                 name="nada_id_client_secret" 
                 value="' . $value . '" 
                 class="regular-text" 
                 placeholder="Votre Client Secret">';
            },
            'nada_settings',
            'nada_main_section'
        );


        add_settings_field(
            'nada_id_fresh_study_automatic_save_interval_field',
            __('Automatic save interval', 'nada-id'),
            function () {
                $value = esc_attr(get_option('nada_id_fresh_study_automatic_save_interval'));
                echo '<input 
            type="number" 
            name="nada_id_fresh_study_automatic_save_interval" 
            value="' . $value . '" 
            class="regular-text" 
            placeholder="30"
            min="1"
            step="1">';
            },
            'nada_settings',
            'nada_main_section'
        );

        // deepl
        add_settings_field(
            'nada_id_deepl_url_field',
            __('URL du serveur DeepL', 'nada-id'),
            function () {
                $value = esc_url(get_option('nada_id_deepl_url', ''));
                echo '<input type="url" name="nada_id_deepl_url" value="' . $value . '" class="regular-text" placeholder="https://api.example.com">';
            },
            'nada_settings',
            'nada_main_section'
        );

        add_settings_field(
            'nada_id_deepl_api_key_field',
            __('API Key DeepL', 'nada-id'),
            function () {
                $value = esc_attr(get_option('nada_id_deepl_api_key', ''));
                echo '<input type="text" name="nada_id_deepl_api_key" value="' . $value . '" class="regular-text" placeholder="Votre API Key">';
            },
            'nada_settings',
            'nada_main_section'
        );

        // API Publique ORCID
        add_settings_field(
            'nada_id_api_public_orcid_field',
            __('API Publique ORCID', 'nada-id'),
            function () {
                $value = esc_url(get_option('nada_id_api_public_orcid', ''));
                echo '<input type="url" name="nada_id_api_public_orcid" value="' . $value . '" class="regular-text" placeholder="https://api.example.com">';
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

// Modifier l'email de l'expéditeur des emails
add_filter('wp_mail_from', function ($email) {
    return get_nada_default_admin_fresh();
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

// Récuperer le token de l'utilisateur connecté
function get_nada_token(): string |null
{
    $user_id = get_current_user_id();

    if (!$user_id) {
        return null;
    }

    // Récupérer le token existant
    $nada_token = get_user_meta($user_id, 'nada_token', true);
    return $nada_token ?: null;
}

function get_nada_server_url()
{
    return get_option('nada_id_api_url', '');
}

function get_nada_pdad_url()
{
    return get_option('nada_id_pdad_url', '');
}

function get_nada_default_admin_fresh()
{
    return get_option('nada_id_fresh_admin_email', '');
}

function get_nada_default_contact_fresh()
{
    return get_option('nada_id_fresh_contact_email', '');
}

function get_automatic_save_interval_fresh()
{
    return get_option('nada_id_fresh_study_automatic_save_interval', '');
}
// CIM 11 - ICD
function get_nada_icd_url()
{
    return get_option('nada_id_icd_url', '');
}
function get_nada_icd_search()
{
    return get_option('nada_id_icd_search', '');
}

function get_nada_icd_client_id()
{
    return get_option('nada_id_client_id', '');
}
function get_nada_icd_client_secret()
{
    return get_option('nada_id_client_secret', '');
}
// DeepL
function get_nada_deepl_url()
{
    return get_option('nada_id_deepl_url', '');
}
function get_nada_deepl_api_key()
{
    return get_option('nada_id_deepl_api_key', '');
}

// API publique ORCID
function get_nada_api_public_orcid_url()
{
    return get_option('nada_id_api_public_orcid', '');
}

function get_default_password_nada()
{
    return defined('NADA_API_DEFAULT_PASSWORD') ? NADA_API_DEFAULT_PASSWORD : null;
}


// Hooks d’initialisation
add_action('init', 'nada_id_register_rewrite_rules');
add_action('plugins_loaded', 'nada_id_check_db_update');
add_action('plugins_loaded', 'nada_id_load_language');
add_action('plugins_loaded', function () {
//   $user_controller  = Nada_Container::user_controller();
    $study_controller = Nada_Container::study_controller();

  //  $user_controller->register_hooks();
    $study_controller->register_hooks();
});



/**
 * @param string $url  L'URL de base traduite (fournie par Polylang).
 * @param string $lang Le slug de la langue de destination (ex: 'en').
 * @return string L'URL complète et corrigée.
 */
add_filter('pll_translation_url', function ($url, $lang) {

    // On récupère la requête actuelle
    global $wp;
    $current_path = '/' . trim($wp->request, '/');
    $query_string = $_SERVER['QUERY_STRING'] ?? '';
    $current_lang = pll_current_language();

    // Gère les pages avec des IDs dans l’URL
    $detail_regex = '#/(catalogue-detail|study-details|alimentation-du-catalogue|contribute|referentiel-detail|repository-detail|catalog-variable-dictionary|dictionnaire-des-variables-du-catalogue)(?:/(metadata|vocabulary))?/([0-9A-Za-z_-]+)/?$#';

    if (preg_match($detail_regex, $current_path, $matches)) {
        $page_slug = $matches[1];
        $sub_slug  = $matches[2] ?? null;
        $id        = $matches[3];
        //pages dictionnaire des variables
        if (in_array($page_slug, [
            'catalog-variable-dictionary',
            'dictionnaire-des-variables-du-catalogue'
        ], true) && $sub_slug) {
            $url = trailingslashit($url) . $sub_slug . '/' . $id . '/';
        } else {
            // autres pages
            if ($current_lang && $lang) {
                $id = preg_replace(
                    '/-' . preg_quote($current_lang, '/') . '$/',
                    '-' . $lang,
                    $id
                );
            }
            $url = trailingslashit($url) . $id . '/';
        }
    }
    //Récupère et ajoute les paramètres de requête
    if (!empty($query_string)) {
        parse_str($query_string, $query_args);
        $url = add_query_arg($query_args, $url);
    }

    return $url;
}, 10, 2);

/*** offcanvas dans add study */
add_action('wp_footer', function () {
    global $post;

    // Vérifie qu'on est bien sur un post/page et que le shortcode est utilisé
    if (isset($post->post_content) && has_shortcode($post->post_content, 'nada_id_add_study')) {
        // Chemin complet vers le fichier offcanvas dans le plugin

        $file = plugin_dir_path(__FILE__) . '/public/templates/offcanvas.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    if (isset($post->post_content) && has_shortcode($post->post_content, 'nada_id_add_study')) {
        // Chemin complet vers le fichier offcanvas dans le plugin

        $file = plugin_dir_path(__FILE__) . '/public/templates/modals/modal-add-edit-study.php';
        if (file_exists($file)) {
            include $file;
        }
    }


    if (isset($post->post_content) && has_shortcode($post->post_content, 'nada_id_list_studies')) {
        // Chemin complet vers le fichier offcanvas dans le plugin

        $file = plugin_dir_path(__FILE__) . '/public/templates/modals/modal-list-study.php';
        if (file_exists($file)) {
            include $file;
        }
    }

    if (isset($post->post_content) && (has_shortcode($post->post_content, 'variable_dictionary') || has_shortcode($post->post_content, 'nada_id_referentiel_details'))) {
        $file = plugin_dir_path(__FILE__) . '/public/templates/modals/modal-add-edit-referentiel.php';
        if (file_exists($file)) {
            include $file;
        }
    }
    if (isset($post->post_content) && (has_shortcode($post->post_content, 'variable_dictionary'))) {
        $file = plugin_dir_path(__FILE__) . '/public/templates/modals/modal-add-edit-vd.php';
        if (file_exists($file)) {
            include $file;
        }
    }
    if (isset($post->post_content) && has_shortcode($post->post_content, 'nada_list_institutions_waiting')) {
        $file = plugin_dir_path(__FILE__) . '/public/templates/modals/modal-valide-action.php';
        if (file_exists($file)) {
            include $file;
        }
    }
    if (isset($post->post_content) && has_shortcode($post->post_content, 'nada_list_institutions_valid')) {
        $file = plugin_dir_path(__FILE__) . '/public/templates/modals/modal-add-edit-institution.php';
        if (file_exists($file)) {
            include $file;
        }
    }
});

// Forcer certains scripts en type="module"
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if ($handle === 'nada-contribute-study') {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}, 10, 3);

// Lancer le plugin
nada_id_run_plugin();

<?php
/*
Plugin Name: ImpactDev 
Description:  chargement CSS + JS + fonctions PHP
Version: 1.2
Author: Impactdev
*/

defined('ABSPATH') || exit;

function impactdev_enqueue_assets() {
    wp_enqueue_script('jquery-ui-autocomplete');
    wp_enqueue_style('style-edit-profile', plugins_url('assets/css/style-edit-profile.css', __FILE__));
    wp_enqueue_style('impactdev-style', plugins_url('assets/css/style.css', __FILE__));
    wp_enqueue_script('impactdev-script', plugins_url('assets/js/script.js', __FILE__), ['jquery'], '1.0', true);
    wp_enqueue_script('impactdev-autocomplete-script', plugins_url('assets/js/autocomplete-script.js', __FILE__), ['jquery'], '1.0', true);
}
add_action('wp_enqueue_scripts', 'impactdev_enqueue_assets', 20); // priorité 20


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('cf7-password-fix', plugins_url('assets/js/cf7-password-fix.js', __FILE__), ['jquery'], '1.0', true);
});

add_action('wp_enqueue_scripts', function () {

    wp_enqueue_script('validator-js', plugins_url('assets/js/validator.min.js', __FILE__), ['jquery'], '1.0', true);
    wp_enqueue_script('validate-js','https://cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js', [], '0.13.1',true );
    wp_enqueue_script('validator-js-form', plugins_url('assets/js/validator-form.js', __FILE__), ['jquery'], '1.0', true);
});





// Inclure fonctions sans hook (inclusion immédiate)
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';

// Inclure fonctions sans hook (inclusion immédiate)
require_once plugin_dir_path(__FILE__) . 'includes/proposition.php';

// Inclure fonctions sans hook (inclusion immédiate)
require_once plugin_dir_path(__FILE__) . 'includes/feed-rss.php';

// Inclure fonctions sans hook (inclusion immédiate)
require_once plugin_dir_path(__FILE__) . 'includes/inscription.php';

// Inclure fonctions sans hook (inclusion immédiate)
require_once plugin_dir_path(__FILE__) . 'includes/login.php';

// Inclure fonctions sans hook (inclusion immédiate)
require_once plugin_dir_path(__FILE__) . 'includes/email-admin.php';

// Inclure fonctions sans hook (inclusion immédiate)
require_once plugin_dir_path(__FILE__) . 'includes/reset-password.php';

// Inclure fonctions sans hook (inclusion immédiate)
require_once plugin_dir_path(__FILE__) . 'includes/stat-mon-espace.php';

// Inclure fonctions sans hook (inclusion immédiate)
require_once plugin_dir_path(__FILE__) . 'includes/edit-profil.php';


/**
 * ------------------------------------------------------
 * 1. Définition des constantes
 * ------------------------------------------------------
 */
define('IMPACTDEV_PLUGIN_VERSION', '1.0.1'); 
define('IMPACTDEV_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IMPACTDEV_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * ------------------------------------------------------
 * 2. Activation : enregistre la version à l’installation
 * ------------------------------------------------------
 */
function impactdev_on_activate() {
    update_option('impactdev_plugin_version', IMPACTDEV_PLUGIN_VERSION);
}
register_activation_hook(__FILE__, 'impactdev_on_activate');


/**
 * ------------------------------------------------------
 * 3. Versioning : vérifier si la version a changé
 * ------------------------------------------------------
 */
function impactdev_check_update() {
    $current_version = get_option('impactdev_plugin_version');

    if ($current_version !== IMPACTDEV_PLUGIN_VERSION) {
        // as des actions si la version change (migrations futures)
        update_option('impactdev_plugin_version', IMPACTDEV_PLUGIN_VERSION);
    }
}
add_action('plugins_loaded', 'impactdev_check_update');



/**
 * ------------------------------------------------------
 * 4. Ajouter le menu "ImpactDev" dans le tableau de bord
 * ------------------------------------------------------
 */
add_action('admin_menu', 'impactdev_register_settings_page');
function impactdev_register_settings_page() {
    add_menu_page(
        'ImpactDev - Configuration',
        'ImpactDev',
        'manage_options',
        'impactdev-settings',
        'impactdev_settings_page_html',
        'dashicons-admin-generic',
        70
    );
}

/**
 * ------------------------------------------------------
 * 5. Enregistrer les champs et sections
 * ------------------------------------------------------
 */
add_action('admin_init', function() {

    // Section principale
    add_settings_section(
        'impactdev_main_section',
        '',
        function() {},
        'impactdev-settings'
    );
  // Email Admin formulaire NL et Prop actualité Fr et En
    register_setting('impactdev_settings_group', 'impactdev_email_admin', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_email',
        'default' => '',
    ]);
    add_settings_field(
        'impactdev_email_admin_field',
        'Email Admin - NL/Prop actualité',
        function() {
            $value = esc_attr(get_option('impactdev_email_admin', ''));
            echo '<input type="email" name="impactdev_email_admin" value="' . $value . '" class="regular-text">';
        },
        'impactdev-settings',
        'impactdev_main_section'
    );


    // Email Admin formulaire contact Fr et En
    register_setting('impactdev_settings_group', 'impactdev_email_admin_contact', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_email',
        'default' => '',
    ]);
    add_settings_field(
        'impactdev_email_contact_field',
        'Email Admin - formulaire contact',
        function() {
            $value = esc_attr(get_option('impactdev_email_admin_contact', ''));
            echo '<input type="email" name="impactdev_email_admin_contact" value="' . $value . '" class="regular-text">';
        },
        'impactdev-settings',
        'impactdev_main_section'
    );



   

    // Email From
    register_setting('impactdev_settings_group', 'impactdev_email_from', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_email',
        'default' => '',
    ]);
    add_settings_field(
        'impactdev_email_from_field',
        'Email From - inscription',
        function() {
            $value = esc_attr(get_option('impactdev_email_from', ''));
            echo '<input type="email" name="impactdev_email_from" value="' . $value . '" class="regular-text">';
        },
        'impactdev-settings',
        'impactdev_main_section'
    );

  
    // Email Reply-To
    register_setting('impactdev_settings_group', 'impactdev_email_reply', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_email',
        'default' => '',
    ]);
    add_settings_field(
        'impactdev_email_reply_field',
        'Email Reply-To - inscription',
        function() {
            $value = esc_attr(get_option('impactdev_email_reply', ''));
            echo '<input type="email" name="impactdev_email_reply" value="' . $value . '" class="regular-text">';
        },
        'impactdev-settings',
        'impactdev_main_section'
    );

    // Email Technique
    register_setting('impactdev_settings_group', 'impactdev_email_technique', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_email',
        'default' => '',
    ]);
    add_settings_field(
        'impactdev_email_technique_field',
        'Email admin - inscription',
        function() {
            $value = esc_attr(get_option('impactdev_email_technique', ''));
            echo '<input type="email" name="impactdev_email_technique" value="' . $value . '" class="regular-text">';
        },
        'impactdev-settings',
        'impactdev_main_section'
    );
	
	 // LinkedIn
    register_setting('impactdev_settings_group', 'impactdev_linkedin', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => '',
    ]);
    add_settings_field(
        'impactdev_linkedin_field',
        'LinkedIn',
        function() {
            $value = esc_url(get_option('impactdev_linkedin', ''));
            echo '<input type="url" name="impactdev_linkedin" value="' . $value . '" class="regular-text">';
        },
        'impactdev-settings',
        'impactdev_main_section'
    );
	// Badge CF7 (FR)
register_setting('impactdev_settings_group', 'impactdev_cf7_badge_fr', [
    'type' => 'string',
    'sanitize_callback' => 'sanitize_text_field',
    'default' => '',
]);
add_settings_field(
    'impactdev_cf7_badge_fr_field',
    'Badge CF7 (FR)',
    function() {
        $value = get_option('impactdev_cf7_badge_fr', '');
        echo '<input type="text" name="impactdev_cf7_badge_fr" value="' . esc_attr($value) . '" class="regular-text">';
        echo '<p class="description">Texte du badge CF7 pour le français.</p>';
    },
    'impactdev-settings',
    'impactdev_main_section'
);

// Badge CF7 (EN)
register_setting('impactdev_settings_group', 'impactdev_cf7_badge_en', [
    'type' => 'string',
    'sanitize_callback' => 'sanitize_text_field',
    'default' => '',
]);
add_settings_field(
    'impactdev_cf7_badge_en_field',
    'Badge CF7 (EN)',
    function() {
        $value = get_option('impactdev_cf7_badge_en', '');
        echo '<input type="text" name="impactdev_cf7_badge_en" value="' . esc_attr($value) . '" class="regular-text">';
        echo '<p class="description">Texte du badge CF7 pour l’anglais.</p>';
    },
    'impactdev-settings',
    'impactdev_main_section'
);

	// Image par défaut CF7 proposition actualité
register_setting('impactdev_settings_group', 'impactdev_default_image_id', [
    'type' => 'integer',
    'sanitize_callback' => 'absint',
    'default' => '', // aucun défaut
]);
add_settings_field(
    'impactdev_default_image_id_field',
    'ID image par défaut CF7',
    function() {
        $value = get_option('impactdev_default_image_id', '');
        echo '<input type="number" name="impactdev_default_image_id" value="' . esc_attr($value) . '" class="regular-text">';
        echo '<p class="description">Renseignez l’ID de l’image à utiliser quand aucune image n’est uploadée via CF7.</p>';
    },
    'impactdev-settings',
    'impactdev_main_section'
);


    // Redirection abonnés (subscriber) FR
    register_setting('impactdev_settings_group', 'impactdev_subscriber_redirect_fr', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => '', // vide par défaut
    ]);
    add_settings_field(
        'impactdev_subscriber_redirect_fr_field',
        'Redirection abonnés (FR)',
        function() {
            $value = esc_url(get_option('impactdev_subscriber_redirect_fr', ''));
            echo '<input type="url" name="impactdev_subscriber_redirect_fr" value="' . $value . '" class="regular-text">';
            echo '<p class="description">Lien vers lequel les abonnés FR seront redirigés après connexion.</p>';
        },
        'impactdev-settings',
        'impactdev_main_section'
    );

    // Redirection abonnés (subscriber) EN
    register_setting('impactdev_settings_group', 'impactdev_subscriber_redirect_en', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => '', // vide par défaut
    ]);
    add_settings_field(
        'impactdev_subscriber_redirect_en_field',
        'Redirection abonnés (EN)',
        function() {
            $value = esc_url(get_option('impactdev_subscriber_redirect_en', ''));
            echo '<input type="url" name="impactdev_subscriber_redirect_en" value="' . $value . '" class="regular-text">';
            echo '<p class="description">Lien vers lequel les abonnés EN seront redirigés après connexion.</p>';
        },
        'impactdev-settings',
        'impactdev_main_section'
    );

    // Redirection pages protégées pour utilisateurs non connectés
    register_setting('impactdev_settings_group', 'impactdev_redirect_pages_non_connectes', [
        'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default' => '', // vide par défaut
    ]);
    add_settings_field(
        'impactdev_redirect_pages_non_connectes_field',
        'Redirection pages protégées (non connectés)',
        function() {
            $value = esc_url(get_option('impactdev_redirect_pages_non_connectes', ''));
            echo '<input type="url" name="impactdev_redirect_pages_non_connectes" value="' . $value . '" class="regular-text">';
            echo '<p class="description">Lien vers lequel les utilisateurs non connectés seront redirigés lorsqu’ils tentent d’accéder à une page protégée.</p>';
        },
        'impactdev-settings',
        'impactdev_main_section'
    );

});




/**
 * ------------------------------------------------------
 * 6. Page d'affichage dans l'administration
 * ------------------------------------------------------
 */
function impactdev_settings_page_html() {
    if (!current_user_can('manage_options')) return;
    ?>
    <div class="wrap">
        <h1>Configuration ImpactDev</h1>
        <p>Modifie les valeurs ci-dessous puis utilise les shortcodes dans ton site.</p>
        <hr><br>

        <form method="post" action="options.php">
            <?php
            settings_fields('impactdev_settings_group');
            do_settings_sections('impactdev-settings');
            submit_button('Enregistrer');
            ?>
        </form>
    </div>
    <?php
}


/*** offcanvas dans add study */
add_action('wp_footer', function () {
    global $post;

    if (isset($post->post_content) && has_shortcode($post->post_content, 'edit_profile_form')) {
        $file = plugin_dir_path(__FILE__) . '/includes/modals.php';
        if (file_exists($file)) {
            include $file;
        }
    }
});

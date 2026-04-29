<?php
if (!defined('ABSPATH')) exit;

/*
|--------------------------------------------------------------------------
| 1. VALIDATION MOT DE PASSE CF7
|--------------------------------------------------------------------------
*/
add_filter('wpcf7_validate_password*', 'impactdev_cf7_validate_password', 10, 2);
add_filter('wpcf7_validate_password', 'impactdev_cf7_validate_password', 10, 2);

function impactdev_cf7_validate_password($result, $tag)
{
    // Récupérer le formulaire actuel
    $submission = WPCF7_Submission::get_instance();
    if (!$submission) return $result;

    $form_title = $submission->get_contact_form()->title();

    // Déterminer la langue
    $lang = 'fr'; // par défaut
    if ($form_title === 'Inscription EN') {
        $lang = 'en';
    }

    $name = $tag->name;

    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    // Validation du mot de passe
    if ($name === 'password') {
        $errors = [];
        if (strlen($password) < 12) $errors[] = ($lang === 'fr') ? "Minimum 12 caractères" : "Minimum 12 characters";
        if (!preg_match('/[A-Z]/', $password)) $errors[] = ($lang === 'fr') ? "Une majuscule requise" : "At least one uppercase letter required";
        if (!preg_match('/[0-9]/', $password)) $errors[] = ($lang === 'fr') ? "Un chiffre requis" : "At least one number required";
        if (!preg_match('/[\W]/', $password)) $errors[] = ($lang === 'fr') ? "Un caractère spécial requis" : "At least one special character required";

        if (!empty($errors)) {
            $msg = ($lang === 'fr') ? "Mot de passe invalide : " : "Invalid password: ";
            $result->invalidate($tag, $msg . implode(", ", $errors));
        }
    }

    // Validation de la confirmation
    if ($name === 'password_confirm' && $password !== $confirm) {
        $msg = ($lang === 'fr') ? "Les mots de passe ne correspondent pas" : "Passwords do not match";
        $result->invalidate($tag, $msg);
    }

    return $result;
}

/*
|--------------------------------------------------------------------------
| 2. CRÉATION UTILISATEUR + EMAIL ACTIVATION
|--------------------------------------------------------------------------
*/
add_filter('wpcf7_mail_components', 'impactdev_cf7_modify_mail', 10, 3);
function impactdev_cf7_modify_mail($components, $contact_form, $instance)
{
    $title = $contact_form->title();
    if ($title !== 'Inscription FR' && $title !== 'Inscription EN') return $components;

    $submission = WPCF7_Submission::get_instance();
    if (!$submission) return $components;

    $data = $submission->get_posted_data();
    $firstname = sanitize_text_field($data['firstname']);
    $lastname  = sanitize_text_field($data['lastname']);
    $email     = sanitize_email($data['email']);

    // 1. Extraire la première partie de l'email
    list($base_login) = explode('@', $email);
    $base_login = sanitize_user($base_login, true);

    // 2. Générer un login unique
    $user_login = $base_login;
    $counter = 1;

    // Boucle jusqu'à trouver un login unique
    while (username_exists($user_login)) {
        $user_login = $base_login . $counter;
        $counter++;
    }
    // 3. Créer l'utilisateur
    $user_id = wp_insert_user([
        'user_login' => $user_login,
        'user_pass'  => sanitize_text_field($data['password']),
        'user_email' => $email,
        'first_name' => $firstname,
        'last_name'  => $lastname,
        'role'       => 'subscriber'
    ]);

    if (is_wp_error($user_id)) return $components;

    $lang = pll_current_language();

    update_user_meta($user_id, 'account_active', '0');
    $token = wp_generate_password(32, false, false);
    update_user_meta($user_id, 'activation_token', $token);
    update_user_meta($user_id, 'signup_lang', $lang);

    $home_url = home_url();
    $activation_link = add_query_arg([
        'activate_account' => $user_id,
        'token' => $token
    ], site_url());

    $template_path = plugin_dir_path(__FILE__) . 'email-activation.php';
    $html = include $template_path;

    $html = str_replace(
        ['[prenom]', '[nom]', '[activation_link]', '[home_url]'],
        [$firstname, $lastname, $activation_link, $home_url],
        $html
    );

    $components['body'] = $html;
    $components['recipient'] = $email;
    $components['subject'] = ($lang === 'fr')
        ? '[FReSH] Activer votre compte'
        : '[FReSH] Activate your account';
    $components['additional_headers'] = "Content-Type: text/html; charset=UTF-8";

    return $components;
}



/*
|--------------------------------------------------------------------------
| 3. ACTIVATION DU COMPTE
|--------------------------------------------------------------------------
*/
add_action('init', 'impactdev_account_activation');
function impactdev_account_activation()
{
    if (!session_id()) session_start();

    if (!isset($_GET['activate_account']) || !isset($_GET['token'])) return;

    $user_id = intval($_GET['activate_account']);
    $token   = sanitize_text_field($_GET['token']);

    $stored = get_user_meta($user_id, 'activation_token', true);
    $lang   = get_user_meta($user_id, 'signup_lang', true); // 'fr' ou 'en'

    $redirect_url = ($lang === 'en')
        ? home_url('/en/account-validation/')
        : home_url('/validation-compte/');

    // ----------------------------
    // Token invalide
    // ----------------------------
    if (!$stored || $stored !== $token) {
        $message = ($lang === 'en')
            ? "Invalid or expired activation link."
            : "Lien d’activation invalide ou expiré.";

        // Redirection avec message dans l'URL
        wp_redirect(add_query_arg('activation_msg', urlencode($message), $redirect_url));
        exit;
    }


    do_action('nada_user_registered', $user_id);
    // OC:manque condition si le compte n'est pas crée.


    // ----------------------------
    // Activer le compte
    // ----------------------------
    update_user_meta($user_id, 'account_active', '1');
    delete_user_meta($user_id, 'activation_token');
    
    $user = get_userdata($user_id);
    $theme_dir = get_stylesheet_directory();
    $home_url = home_url();

    // ----------------------------
    // Email utilisateur
    // ----------------------------
    $template_user = plugin_dir_path(__FILE__) . '/email-confirmation.php';
    if (file_exists($template_user)) {
        $email_html = include $template_user;

        $email_html = str_replace(
            ['[prenom]', '[nom]', '[home_url]'],
            [$user->first_name, $user->last_name, $home_url],
            $email_html
        );
    }

    $subject_user = ($lang === 'en') ? '[FReSH] Welcome to FReSH! Your account has been created' : '[FReSH] Bienvenu sur FReSH ! Votre compte a été créé';
    wp_mail($user->user_email, $subject_user, $email_html, ['Content-Type: text/html; charset=UTF-8']);

 // ----------------------------
    // Email admin
    // ----------------------------

    $template_admin_path = plugin_dir_path(__FILE__) . 'email-admin.php';

    // Charger le template (retourne le HTML)
    $html = include $template_admin_path;

    $subject_admin = '[FReSH] Nouvel utilisateur inscrit';
    // Récupérer les emails depuis la configuration du plugin
    $from_email      = get_option('impactdev_email_from', '');
    $reply_to_email  = get_option('impactdev_email_reply', '');
    $technique_email = get_option('impactdev_email_technique', '');
	
	$from_email      = sanitize_email($from_email);
    $reply_to_email  = sanitize_email($reply_to_email);
    // Headers
    $headers_admin = [
    'Content-Type: text/html; charset=UTF-8',
    'From: FReSH <' . $from_email . '>',
    'Reply-To: ' . $reply_to_email,
];
    // Adresse admin
    $admin_email = sanitize_email($technique_email);
    $home_url = home_url();
    // Remplacer les placeholders dans le HTML
    $html = str_replace(
        ['[email]', '[home_url]'],
        [$user->user_email, $home_url],
        $html
    );

    // Envoi du mail
    wp_mail($admin_email, $subject_admin, $html, $headers_admin);


    // ----------------------------
    // Message succès pour page
    // ----------------------------
    $message = ($lang === 'en')
        ? "Your account is activated! You can now log in."
        : "Votre compte est activé ! Vous pouvez maintenant vous connecter.";

    // Redirection avec message dans l'URL
    wp_redirect(add_query_arg('activation_msg', urlencode($message), $redirect_url));
    exit;
}



/*
|--------------------------------------------------------------------------
| 4. BLOQUER CONNEXION SI COMPTE NON ACTIVÉ
|--------------------------------------------------------------------------
*/
add_filter('authenticate', 'impactdev_block_unactivated_users', 30, 3);
function impactdev_block_unactivated_users($user, $username, $password)
{
    if (is_a($user, 'WP_User')) {
        if (get_user_meta($user->ID, 'account_active', true) == '0') {
            return new WP_Error('inactive', 'Votre compte n’est pas encore activé. Vérifiez votre email.');
        }
    }
    return $user;
}

/*
|--------------------------------------------------------------------------
| 5. Shortcode pour afficher le message d'activation
|--------------------------------------------------------------------------
*/

add_shortcode('activation_message', function () {
    if (!session_id()) session_start();

    if (!empty($_SESSION['account_activation_message'])) {
        $msg = '<div class="activation-message" style="color:green;margin-bottom:15px;">'
            . esc_html($_SESSION['account_activation_message'])
            . '</div>';
        unset($_SESSION['account_activation_message']); // effacer après affichage
        return $msg;
    }
    return '';
});

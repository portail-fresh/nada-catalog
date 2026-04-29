<?php
function custom_login_form_shortcode()
{
    if (is_user_logged_in()) {
        return pll__('Vous êtes déjà connecté.', 'text-domain'); // traduction
    }

    $output = '';

    // Détecter la langue active
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';

    // Définir les textes selon la langue
    $texts = array(
        'fr' => array(
            'email' => "Email",
            'password' => "Mot de passe",
            'remember' => "Se souvenir de moi",
            'login' => "Se connecter",
            'security_error' => "Erreur de sécurité, veuillez réessayer.",
            'incorrect_credentials' => "Le nom d'utilisateur ou le mot de passe est incorrect.",
            'too_many_attempts' => "Trop de tentatives échouées. Veuillez réessayer dans 15 minutes.",
        ),
        'en' => array(
            'email' => "Email",
            'password' => "Password",
            'remember' => "Remember me",
            'login' => "Login",
            'security_error' => "Security error, please try again.",
            'incorrect_credentials' => "Incorrect username or password.",
            'too_many_attempts' => "Too many login attempts. Please try again in 15 minutes.",
        )
    );

    $t = isset($texts[$current_lang]) ? $texts[$current_lang] : $texts['fr'];

    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_login_nonce'])) {
        if (!wp_verify_nonce($_POST['custom_login_nonce'], 'custom_login_action')) {
            $output .= '<p style="color:red;">' . $t['security_error'] . '</p>';
        } else {
            $email = sanitize_text_field($_POST['email']);
            $password = $_POST['password'];

            // RATE LIMITING PAR IP
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $ip_attempt_key = 'login_attempts_ip_' . md5($user_ip);
            $ip_attempts = get_transient($ip_attempt_key);

            // Paramètres de limitation
            $max_attempts = 5;         // 5 tentatives par IP avant blocage
            $lockout_time = 15 * MINUTE_IN_SECONDS; // 15 minutes de blocage

            // Vérifier si l'utilisateur a dépassé la limite par IP
            if ($ip_attempts && $ip_attempts >= $max_attempts) {
                $output .= '<p style="color:red;">' . $t['too_many_attempts'] . '</p>';
            } else {
                // Continuer avec l'authentification
                $creds = array(
                    'user_login'    => $email,
                    'user_password' => $password,
                    'remember'      => isset($_POST['remember'])
                );
                $user = wp_authenticate($email, $password);
                do_action('check_existing_user', $user);

                if (is_wp_error($user)) {
                    // Incrémenter le compteur de tentatives échouées par IP
                    if (!$ip_attempts) {
                        set_transient($ip_attempt_key, 1, $lockout_time);
                    } else {
                        set_transient($ip_attempt_key, $ip_attempts + 1, $lockout_time);
                    }

                    // Afficher le message d'erreur personnalisé
                    $output .= '<p style="color:red;">' . $t['incorrect_credentials'] . '</p>';
                } else {
                  // Connexion réussie - supprimer le compteur de tentatives par IP
                  delete_transient($ip_attempt_key);
                  $user = wp_signon($creds);
                  if (!is_wp_error($user)) {
                  wp_redirect(admin_url());
                  exit;
                         }
                       }
            }
        }
    }

    // Formulaire
    $output .= '
    <form method="post" id="loginCompte">
        <div class="mb-default">
            <label for="email">' . $t['email'] . '</label><br>
            <input type="text" name="email" id="email">
            <div class="input-validator error" data-error-for="email"></div>
        </div>
       

        <div class="mb-default">
            <label for="password">' . $t['password'] . '</label><br>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" class="password-field">
                <span class="toggle-password">
                    <i class="fa-solid fa-eye-slash"></i>
                </span>
            </div>
            <div class="input-validator error" data-error-for="password"></div>
        </div>

       

        <div class="mb-default">
            <label>
                <input type="checkbox" name="remember"> ' . $t['remember'] . '
            </label>
        </div>
        <div>
            <input type="hidden" name="custom_login_nonce" value="' . wp_create_nonce('custom_login_action') . '">
            <input id="login-button" style="width:100%!important;border-radius: 8px;" type="submit" value="' . $t['login'] . '">
        </div>
    </form>
    ';

    return $output;
}
add_shortcode('custom_login', 'custom_login_form_shortcode');
add_filter('login_errors', function ($error) {

    // langue active
    $lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';

    if ($lang === 'en') {
        return "Incorrect username or password.";
    }

    return "Nom d'utilisateur ou mot de passe incorrect.";
});

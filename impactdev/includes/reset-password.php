<?php
function send_custom_password_reset_email($user) {
	
    if (!$user) return;
 
    $is_french = function_exists('pll_current_language')
        ? pll_current_language() === 'fr'
        : get_locale() === 'fr_FR';

    $key = get_password_reset_key($user);
    if (is_wp_error($key)) return;

    $reset_url = $is_french
        ? home_url('/reinitialiser-mot-de-passe/?key=' . $key . '&login=' . rawurlencode($user->user_login))
        : home_url('/en/reset-password/?key=' . $key . '&login=' . rawurlencode($user->user_login));

    $subject = '[FReSH] Demande réinitialisation du mot de passe';
    $template_path = plugin_dir_path(__FILE__) . 'email-reset-psw.php';
	$home_url = home_url();
	$email = $user->user_email; 

    $user = get_user_by('email', $email);

    if ($user) {
        $prenom = $user->first_name;
        $nom = $user->last_name;
    }
    $message = include $template_path;
    $message = str_replace(
        ['[prenom]', '[nom]', '[home_url]', '[reset_url]'],
        [$user->first_name, $user->last_name, $home_url, $reset_url],
        $message
    );

	
	// Headers pour un mail propre
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: FReSH <admin@portail-fresh.fr>',
        'Reply-To: admin@portail-fresh.fr'
    ];

    // Convertir le tableau en string pour mail()
    $headers_string = implode("\r\n", $headers);

    wp_mail($user->user_email, $subject, $message, $headers_string); 
}

// Traitement du formulaire POST
function handle_reset_password_post() {

     $is_french = function_exists('pll_current_language') ? pll_current_language() === 'fr' : get_locale() === 'fr_FR';
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['user_email']) &&
        isset($_POST['reset_password_id_nonce']) &&
        wp_verify_nonce($_POST['reset_password_id_nonce'], 'reset_password_id_action')
    ) {
        $is_french = function_exists('pll_current_language') ? pll_current_language() === 'fr' : get_locale() === 'fr_FR';
        $email = sanitize_email($_POST['user_email']);
        $user = get_user_by('email', $email);

        if ($user) {
            send_custom_password_reset_email($user);
        }
        $message = $is_french ? "Si cette adresse e-mail est associée à un compte, un lien de réinitialisation vous sera envoyé." : "If this email is registered, a password reset link will be sent to it.";
        $messageType = "success";
         ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var messageContainer = document.getElementById('reset-message');
                if (!messageContainer) {
                    messageContainer = document.createElement('div');
                    messageContainer.id = 'reset-message';
                    document.querySelector('form#lostPasswordForm').before(messageContainer);
                }
                messageContainer.innerHTML = '<?php echo esc_js($message); ?>';
                messageContainer.style.color = '<?php echo ($messageType === "success") ? "green" : "red"; ?>';
            });
        </script>
        <?php
    }


    if (isset($_POST['confirm_password']) && isset($_POST['reset_password_submit'])) {
        $new_pass = sanitize_text_field($_POST['new_password']);
        $confirm  = sanitize_text_field($_POST['confirm_password']);
        $invalid_password = false;

        // Validation
        if (
            strlen($new_pass) < 12 ||
            !preg_match('/[A-Z]/', $new_pass) ||
            !preg_match('/[0-9]/', $new_pass) ||
            !preg_match('/[\W_]/', $new_pass) ||
            $new_pass !== $confirm
        ) {
            $invalid_password = true;
        }

        if ($invalid_password) {
            $message = $is_french
                ? "Mot de passe invalide : Minimum 12 caractères, une majuscule, un chiffre et un caractère spécial requis."
                : "Invalid password: Minimum 12 characters, one uppercase, one number and one special character required.";

            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var err = document.getElementById('password-error');
                    if(err) err.innerHTML = " . json_encode($message) . ";
                    else alert(" . json_encode($message) . ");
                });
            </script>";
        } else {
            // Changement réel du mot de passe

           
            $login = sanitize_text_field($_GET['login']);
            $user  = get_user_by('login', $login);

            reset_password($user, $new_pass);

            $success = $is_french
                ? "Votre mot de passe a été modifié avec succès !"
                : "Your password has been successfully reset!";

            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var success = document.getElementById('password-success');
                    if(success) success.innerHTML = " . json_encode($success) . ";
                });
            </script>";
        }
    }



}

add_action('template_redirect', 'handle_reset_password_post');

function full_reset_password_shortcode() {
    ob_start();
    $is_french = function_exists('pll_current_language') ? pll_current_language() === 'fr' : get_locale() === 'fr_FR';

   
    if (isset($_GET['key']) && isset($_GET['login'])) {
        $key   = sanitize_text_field($_GET['key']);
        $login = sanitize_text_field($_GET['login']);
        $user  = get_user_by('login', $login);

        $valid_key = $user && !is_wp_error(check_password_reset_key($key, $login));

        if (!$valid_key && !isset($_POST['reset_password_submit'])) {
            echo '<p style="color:red;font-weight:bold;">' .
                ($is_french ? 'Lien invalide ou expiré.' : 'Invalid or expired reset link.') .
            '</p>';
            return ob_get_clean();
        }

        echo change_password($is_french);
        return ob_get_clean();
    }else{
        echo reset_password_id($is_french);
    }

    return ob_get_clean();
}
add_shortcode('full_reset_password', 'full_reset_password_shortcode');
function reset_password_id($is_french, $message = '', $messageType = '') {
    $texts = [
        'email'     => $is_french ? 'Email' : 'Email',
        'reset_btn' => $is_french ? 'Réinitialiser le mot de passe' : 'Reset password',
    ];

    ob_start();
    ?>
    <form method="POST" action="" name="lostPassword" id="lostPasswordForm">
        <?php wp_nonce_field('reset_password_id_action', 'reset_password_id_nonce'); ?>

        <div class="mb-default">
            <label for="user_email"><?php echo esc_html($texts['email']); ?></label>
            <input type="email" name="user_email" id="user_email">
            <div class="input-validator error" data-error-for="user_email"></div>
        </div>

        <div class="mb-default">
            <button type="submit" id="sendEmailResetPassword" name="lost_password_submit" style="width: 100% !important;">
                <?php echo esc_html($texts['reset_btn']); ?>
            </button>
        </div>
    </form>
    <?php
    return ob_get_clean();
}
function change_password($is_french) {
    ob_start(); ?>
    <form method="POST" id="changePasswordForm">
        <div id="password-error" style="color:red;margin-bottom:10px;"></div>
        <div id="password-success" style="color:green;margin-bottom:10px;"></div>

        <div class="mb-default">
            <label><?php echo $is_french ? 'Nouveau mot de passe' : 'New password'; ?></label>
            <div class="password-wrapper">
                <input type="password"  name="new_password"  class="password-field">
                <span class="toggle-password">
                    <i class="fa-solid fa-eye-slash"></i>
                </span>
            </div>
            <div class="input-validator error" data-error-for="new_password"></div>
        </div>
        

        <div class="mb-default">
            <label><?php echo $is_french ? 'Répéter le mot de passe' : 'Repeat password'; ?></label>
        
            <div class="password-wrapper">
                <input type="password"  name="confirm_password"  class="password-field">
                <span class="toggle-password">
                    <i class="fa-solid fa-eye-slash"></i>
                </span>
            </div>

            <div class="input-validator error" data-error-for="confirm_password"></div>

        </div>

        <div class="mb-default">
            <button type="submit" name="reset_password_submit" style="width:100% !important;">
                <?php echo $is_french ? 'Confirmer' : 'Confirm'; ?>
            </button>
        </div>
    </form>
    <?php
    return ob_get_clean();
}
?>
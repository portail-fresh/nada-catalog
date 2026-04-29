<?php
// Charger les scripts et configurer AJAX
add_action('wp_footer', function() {
    wp_enqueue_script('jquery');
    wp_add_inline_script('jquery', 'var ajaxurl = "' . admin_url('admin-ajax.php') . '";');
});

/**
 * Fonction principale pour afficher le formulaire de modification de profil
 */
function custom_edit_profile_form_styled() {
    // Vérifier si l'utilisateur est connecté
    if (!is_user_logged_in()) {
        return get_not_logged_in_message();
    }

    $current_user = wp_get_current_user();
    $output = '';
    $lang = get_site_language();

    if (isset($_GET['profile_updated']) && $_GET['profile_updated'] == '1') {
        $success_message = get_locale() === 'fr_FR'
            ? 'Compte mis à jour avec succès !'
            : 'Profile updated successfully!';
        $output .= '<p class="cep-success">' . $success_message . '</p>';
    }

    // Gérer la soumission du formulaire
    if (isset($_POST['update_profile']) && wp_verify_nonce($_POST['cep_nonce'], 'cep_action')) {
        $result = handle_profile_update($current_user);

        // Si erreurs, les afficher
        if ($result !== true) {
            $output .= $result;
        }
    }

    // Obtenir les institutions pour l'autocomplétion
    $institutions = get_institutions_data($lang);
    // Créer et retourner le formulaire
    $output .= get_profile_form_html($current_user, $institutions, $lang);
    return $output;
}
/**
 * Obtenir le message pour l'utilisateur non connecté
 */
function get_not_logged_in_message() {
    $message = get_locale() === 'fr_FR'
        ? 'Vous devez être connecté pour modifier votre compte.'
        : 'You must be logged in to edit your profile.';

    return '<p>' . $message . '</p>';
}

/**
 * Obtenir la langue du site
 */
function get_site_language() {
    return get_locale() === 'fr_FR' ? 'fr' : 'en';
}

/**
 * Gérer la mise à jour du profil
 */
function handle_profile_update($current_user) {
    // Nettoyer les entrées
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);
    $institution = sanitize_text_field($_POST['institution']);

    // Valider
    $errors = validate_profile_fields($email, $first_name, $last_name);

    if (empty($errors)) {
        // Mettre à jour les données utilisateur
        wp_update_user([
            'ID' => $current_user->ID,
            'first_name' => $first_name,
            'last_name' => $last_name,
        ]);

        // Mettre à jour l'institution
        update_user_meta($current_user->ID, 'institution', $institution);
        $current_url = remove_query_arg('profile_updated', wp_unslash($_SERVER['REQUEST_URI']));
        $redirect_url = add_query_arg('profile_updated', '1', $current_url);

        wp_safe_redirect($redirect_url);
        exit;
    } else {
        // Afficher les erreurs et retourner le HTML
        $output = '';
        foreach ($errors as $error) {
            $output .= '<p class="cep-error">' . esc_html($error) . '</p>';
        }
        return $output;
    }
}
/**
 * Valider les champs du profil
 */
function validate_profile_fields($email, $firstname, $lastname) {
    $errors = [];
    $lang = get_site_language() ?? 'fr';
    // Valider l'email
    if (!is_email($email)) {
        $errors[] = $lang === 'fr' ? 'Email invalide.' : 'Invalid email.';
    }

    if (empty($firstname)) {
        $errors[] = $lang === 'fr' ? 'Nom invalide.' : 'Invalid first name.';
    }
    if (empty($lastname)) {
        $errors[] = $lang === 'fr' ? 'Prénom invalide.' : 'Invalide last name.';
    }
    return $errors;
}

/**
 * Obtenir les données des institutions pour l'autocomplétion
 */
function get_institutions_data($lang) {
    $institutions = getInstitutionsListForProfile();
    return array_map(function ($item) use ($lang) {
        return [
            'label' => $lang === 'fr' ? $item['label_fr'] : $item['label_en']
        ];
    }, $institutions);
}

/**
 * Obtenir le HTML du formulaire
 */
function get_profile_form_html($current_user, $institutions, $lang) {
    $nonce_field = wp_nonce_field('cep_action', 'cep_nonce', true, false);
    $data_list_json = wp_json_encode(array_map(function ($item) {
        return ['label' => $item['label']];
    }, $institutions));

    $form_html = '
    <form method="post" class="cep-form editprofile">
        ' . $nonce_field . '
        ' . get_form_html($current_user, $lang, $data_list_json) . '
    </form>';

    return $form_html;
}



/**
 * Obtenir le code HTML du formulaire
 */
function get_form_html($current_user, $lang, $data_list_json) {
    $last_name_label = $lang === 'fr' ? 'Nom' : 'Last name';
    $last_name_placeholder = $lang === 'fr' ? 'Veuillez entrer votre nom' : 'Please enter your last name';
    $first_name_label = $lang === 'fr' ? 'Prénom' : 'First name';
    $first_name_placeholder = $lang === 'fr' ? 'Veuillez entrer votre prénom' : 'Please enter your first name';
    $institution_label = 'Institution';
    $institution_placeholder = $lang === 'fr'
        ? 'Veuillez choisir votre institution'
        : 'Please select your institution';
    $institution_value = esc_attr(get_user_meta($current_user->ID, 'institution', true));
    $email_label = 'Email';
    $email_placeholder = $lang === 'fr'
        ? 'Veuillez saisir votre adresse email'
        : 'Please enter your email address';
    $password_label = $lang === 'fr' ? 'Mot de passe' : 'Password';
    $password_update_btn = $lang === 'fr' ? 'Modifier' : 'Update';
    $submit_text = $lang === 'fr' ? 'Modifier' : 'Update';

    return '
        <div class="cep-row">
            <div class="blockPassword is-required">
                <label>' . $last_name_label . '</label>
                <input
                    type="text"
                    name="last_name"
                    value="' . esc_attr($current_user->last_name) . '"
                    class="cep-placeholder"
                    placeholder="' . $last_name_placeholder . '"
                    required>
                <div class="input-validator error" data-error-for="last_name"></div>
            </div>
            <div class="blockPassword is-required">
                <label>' . $first_name_label . '</label>
                <input
                    type="text"
                    name="first_name"
                    value="' . esc_attr($current_user->first_name) . '"
                    class="cep-placeholder"
                    placeholder="' . $first_name_placeholder . '"
                    required>
                <div class="input-validator error" data-error-for="first_name"></div>
            </div>
        </div>
        <div class="cep-row">
            <div class="blockPassword autocomplete-container">
                <label>' . $institution_label . '</label>
                <input
                    type="text"
                    name="institution"
                    value="' . $institution_value . '"
                    class="cep-placeholder autocomplete"
                    placeholder="' . $institution_placeholder . '"
                    autocomplete="off">
                <script type="application/json" class="dataList">
                    ' . $data_list_json . '
                </script>
            </div>
        </div>
        <div class="cep-row">
            <div class="blockPassword is-required">
                <label>' . $email_label . '</label>
                <input
                    type="email"
                    name="email"
                    value="' . esc_attr($current_user->user_email) . '"
                    readonly
                    class="cep-placeholder"
                    placeholder="' . $email_placeholder . '"
                    style="background-color: #f0f0f1 !important;">
                <div class="input-validator error" data-error-for="email"></div>
            </div>
            <div class="blockPassword password-bloc">
                <div>
                    <label>' . $password_label . '</label>
                    <a href="" class="edit-password-btn">
                        <i class="fa fa-edit"></i> ' . $password_update_btn . '
                    </a>
                </div>
                <input type="password" value="**************" readonly class="cep-placeholder">
            </div>
        </div>
        <div class="submit-button-row">
            <button style="border-radius: 6px;" type="submit" name="update_profile">
                ' . $submit_text . '
            </button>
        </div>
        ';
}
add_shortcode('edit_profile_form', 'custom_edit_profile_form_styled');
add_action('wp_ajax_change_user_password', 'change_user_password_ajax');

function change_user_password_ajax() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['errors' => ['Non autorisé']]);
    }
 $lang = $_POST['lang'] ?? 'en';
    if (!isset($_POST['old_password'], $_POST['new_password'], $_POST['confirm_password'])) {
        wp_send_json_error(['errors' => ['Formulaire incomplet']]);
    }

    $user         = wp_get_current_user();
    $old_pass     = $_POST['old_password'];
    $new_pass     = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];
    $errors       = [];

  if (!wp_check_password($old_pass, $user->user_pass, $user->ID)) {
 $errors[] = $lang === 'fr_FR'
            ? 'Ancien mot de passe incorrect.'
            : 'Old password is incorrect.';
}

    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

if ($new_pass !== $confirm_pass) {
	$errors[] = $lang === 'fr_FR'
            ? 'La confirmation du mot de passe ne correspond pas.'
            : 'Password confirmation does not match.';
 
}
    if ($errors) {
        wp_send_json_error(['errors' => $errors]);
    }

    wp_set_password($_POST['new_password'], $user->ID);
wp_send_json_success([
    'message' => ($lang === 'fr_FR')
        ? 'Mot de passe mis à jour avec succès !'
        : 'Password updated successfully!',
    'redirect_url' => ($lang === 'fr_FR') ? '/connexion/' : '/en/login/'
]);
}

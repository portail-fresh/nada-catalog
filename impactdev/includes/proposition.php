<?php

defined('ABSPATH') || exit;

/* -----------------------------
   PROPOSITION ACTUALITÉ CF7
------------------------------*/

// Désactiver JS CF7 si nécessaire
add_filter('wpcf7_load_js', '__return_false');

// Création du post à la soumission du formulaire
add_action('wpcf7_mail_sent', 'cf7_create_post_with_image_and_meta');
function cf7_create_post_with_image_and_meta($contact_form) {

    /*$user_fresh = wp_get_current_user();
    var_dump($user_fresh);*/

    if (!is_user_logged_in()) return;

    $form_id = $contact_form->id();
    if ($form_id != 14992 && $form_id != 22597) return;

    $submission = WPCF7_Submission::get_instance();
    if (!$submission) return;

    $data = $submission->get_posted_data();
    if (empty($data['post-title'])) return;

    $excerpt_raw = isset($data['excerpt']) ? trim($data['excerpt']) : '';
    $categorie_id = (isset($data['categorie-article']) && is_numeric($data['categorie-article']) && intval($data['categorie-article']) > 0)
        ? intval($data['categorie-article'])
        : get_option('default_category');

    $files = $submission->uploaded_files();

    // Meta communs (avec underscore)
    $meta_input = array(
        '_cf7_auteur'      => isset($data['cf7_auteur']) ? sanitize_text_field($data['cf7_auteur']) : '',
        '_cf7_orcid'       => isset($data['cf7_orcid']) ? sanitize_text_field($data['cf7_orcid']) : '',
        '_cf7_institution' => isset($data['cf7_institution']) ? sanitize_text_field($data['cf7_institution']) : '',
        '_date_debut'      => isset($data['date_debut']) ? sanitize_text_field($data['date_debut']) : '',
        '_date_fin'        => isset($data['date_fin']) ? sanitize_text_field($data['date_fin']) : '',
        '_lieu'            => isset($data['lieu']) ? sanitize_text_field($data['lieu']) : '',
        '_source'          => 'cf7',
        '_from_cf7_form'   => '1',
    );

    $post_id = null;

    // === CAS 1 : Image uploadée
    if (!empty($files['file-891'])) {
        $file_path = is_array($files['file-891']) ? reset($files['file-891']) : $files['file-891'];
        if (file_exists($file_path)) {
            $upload_dir = wp_upload_dir();
            $new_path = $upload_dir['path'] . '/' . basename($file_path);
            copy($file_path, $new_path);

            $wp_filetype = wp_check_filetype(basename($new_path), null);
            $attachment = array(
                'guid'           => $upload_dir['url'] . '/' . basename($new_path),
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name(basename($new_path)),
                'post_content'   => '',
                'post_status'    => 'inherit',
            );
            $attach_id = wp_insert_attachment($attachment, $new_path);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $new_path);
            wp_update_attachment_metadata($attach_id, $attach_data);

            $meta_input['_cf7_uploaded_image'] = $attach_id;

            $post_id = wp_insert_post(array(
                'post_title'    => sanitize_text_field($data['post-title']),
                'post_content'  => trim($data['post-content']),
                'post_excerpt'  => $excerpt_raw,
                'post_status'   => 'pending',
                'post_category' => [$categorie_id],
                'post_type'     => 'post',
                'post_author'   => get_current_user_id(),
                'meta_input'    => $meta_input
            ));

            set_post_thumbnail($post_id, $attach_id);
        }
    }

   // === CAS 2 : Pas d'image uploadée → image blanche (configurable)
if (!$post_id) {
    $default_image_id = get_option('impactdev_default_image_id'); // récupère depuis la config du plugin
    $meta_input['_cf7_uploaded_image'] = 'aucune image';

    $post_id = wp_insert_post(array(
        'post_title'    => sanitize_text_field($data['post-title']),
        'post_content'  => trim($data['post-content']),
        'post_excerpt'  => $excerpt_raw,
        'post_status'   => 'pending',
        'post_category' => [$categorie_id],
        'post_type'     => 'post',
        'post_author'   => get_current_user_id(),
        'meta_input'    => $meta_input
    ));

    if ($default_image_id && get_post($default_image_id)) {
        set_post_thumbnail($post_id, $default_image_id);
        update_post_meta($post_id, '_cf7_default_image', '1');
    }
}


    // === Définir la langue selon le champ 'lang' du formulaire
    if (function_exists('pll_set_post_language') && !empty($_POST['lang'])) {
        pll_set_post_language($post_id, sanitize_text_field($_POST['lang']));
    }
}

/* -----------------------------
   Rendre utilisable [image-url] dans CF7
------------------------------*/
add_filter('wpcf7_mail_tag_replaced_image-url', function($replaced, $submitted, $html) {
    if (!empty($submitted)) {
        $file_path = is_array($submitted) ? $submitted[0] : $submitted;
        $upload_dir = wp_upload_dir();
        return str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $file_path);
    }
    return $replaced;
}, 10, 3);

/* -----------------------------
   Ajouter Meta Box pour champs CF7-style + Auteur
-----------------------------*/
add_action('add_meta_boxes', 'impactgroup_add_cf7_style_metabox');
function impactgroup_add_cf7_style_metabox() {
    add_meta_box(
        'impactgroup_cf7_fields',
        'Champs supplémentaires proposition actualité',
        'impactgroup_render_cf7_fields',
        'post',
        'normal',
        'default'
    );
}

/* -----------------------------
   Rendre les champs dans le Meta Box
-----------------------------*/
function impactgroup_render_cf7_fields($post) {
    wp_nonce_field('impactgroup_save_cf7_fields', 'impactgroup_cf7_nonce');

    $fields = [
        '_cf7_auteur'      => ['label'=>'Auteur','icon'=>'fa-user','width'=>'120px','type'=>'text'],
        '_cf7_orcid'       => ['label'=>'ORCID','icon'=>'fa-id-badge','width'=>'100px','type'=>'text'],
        '_cf7_institution' => ['label'=>'Institution','icon'=>'fa-building','width'=>'120px','type'=>'text'],
        '_date_debut'      => ['label'=>'Date début','icon'=>'fa-calendar','type'=>'date'],
        '_date_fin'        => ['label'=>'Date fin','icon'=>'fa-calendar-check-o','type'=>'date'],
        '_lieu'            => ['label'=>'Lieu','icon'=>'fa fa-map','width'=>'120px','type'=>'text'],
    ];

    echo '<div class="cf7-meta" style="margin:15px 0; font-size:14px; display:flex; flex-wrap:wrap; gap:15px; align-items:center;">';
    foreach($fields as $meta_key => $data){
        $value = get_post_meta($post->ID, $meta_key, true);
        echo '<span><i class="fa '.$data['icon'].'"></i> <strong>'.$data['label'].' :</strong> ';
        echo '<input type="'.$data['type'].'" name="'.ltrim($meta_key,'_').'" value="'.esc_attr($value).'" style="width:'.($data['width'] ?? '120px').';"></span>';
    }
    echo '</div>';
}

/* -----------------------------
   Sauvegarde des Meta Box
-----------------------------*/
add_action('save_post', 'impactgroup_save_cf7_fields');
function impactgroup_save_cf7_fields($post_id) {
    if (!isset($_POST['impactgroup_cf7_nonce']) || !wp_verify_nonce($_POST['impactgroup_cf7_nonce'], 'impactgroup_save_cf7_fields')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['_cf7_auteur','_cf7_orcid','_cf7_institution','_date_debut','_date_fin','_lieu'];

    foreach($fields as $meta_key){
        $field_name = ltrim($meta_key,'_');
        $new_value = sanitize_text_field($_POST[$field_name] ?? '');
        update_post_meta($post_id, $meta_key, $new_value);
    }
}

/* -----------------------------
   Affichage front des champs CF7 + Meta Box synchronisés
-----------------------------*/
add_filter('the_content', 'impactgroup_display_cf7_fields_synchronized');
function impactgroup_display_cf7_fields_synchronized($content) {
    if (!is_singular('post') || is_admin()) return $content;

    global $post;

    
   // --- Image à la une ---
$thumb_html = '';
$img_style = 'width:350px; height:auto; border-radius:10px;';

if (has_post_thumbnail($post->ID)) {
    $thumb_html = get_the_post_thumbnail($post->ID, 'large', [
        'class' => 'wp-post-image',
        'style' => $img_style
    ]);
} else {
    // Récupère l'ID de l'image par défaut depuis la config du plugin
    $default_image_id = get_option('impactdev_default_image_id');

    if ($default_image_id && get_post($default_image_id)) {
        $thumb_html = wp_get_attachment_image($default_image_id, 'large', false, [
            'class' => 'wp-post-image cf7-default-image',
            'style' => $img_style
        ]);
    }
}


    // --- Bloc CF7 meta ---
    $fields = [
//         '_cf7_auteur'      => ['icon'=>'fa-user','label'=>'Auteur'],
        '_cf7_orcid'       => ['icon'=>'fa-id-badge','label'=>'ORCID'],
        '_cf7_institution' => ['icon'=>'fa-building','label'=>'Institution'],
        '_date_debut'      => ['icon'=>'fa-calendar','label'=>'Date début'],
        '_date_fin'        => ['icon'=>'fa-calendar-check-o','label'=>'Date fin'],
        '_lieu'            => ['icon'=>'fa-map','label'=>'Lieu'],
    ];

    $meta_html = '<div class="cf7-meta" style="margin:15px 0; font-size:14px; display:flex; flex-wrap:wrap; gap:15px; align-items:center;">';
    foreach($fields as $meta_key => $data){
        $value = get_post_meta($post->ID, $meta_key, true);
        if(!empty($value)){
            $meta_html .= '<span class="meta-card"><i class="fa '.$data['icon'].'" aria-hidden="true"></i> <strong>'.$data['label'].' :</strong> '.esc_html($value).'</span>';
        }
    }
    $meta_html .= '</div>';

    return $thumb_html . $meta_html . $content;
}

/* -----------------------------
   Shortcodes CF7
-----------------------------*/
function cf7_meta_shortcode($atts, $field) {
    global $post;
    if (!isset($post->ID)) return '';
    $value = get_post_meta($post->ID, $field, true);
    return !empty($value) ? esc_html($value) : '';
}

add_shortcode('cf7_auteur', function($atts){ return cf7_meta_shortcode($atts, '_cf7_auteur'); });
add_shortcode('cf7_orcid', function($atts){ return cf7_meta_shortcode($atts, '_cf7_orcid'); });
add_shortcode('cf7_institution', function($atts){ return cf7_meta_shortcode($atts, '_cf7_institution'); });
add_shortcode('cf7_date_debut', function($atts){ return cf7_meta_shortcode($atts, '_date_debut'); });
add_shortcode('cf7_date_fin', function($atts){ return cf7_meta_shortcode($atts, '_date_fin'); });
add_shortcode('cf7_lieu', function($atts){ return cf7_meta_shortcode($atts, '_lieu'); });

/* -----------------------------
   Masquer badge CF7 dans l'admin
-----------------------------*/
add_action('admin_head', function() {
    echo '<style>
        .wp-admin .cf7-badge.bloc-badge { display: none !important; }
    </style>';
});

/* -----------------------------
   Forcer l'extrait si vide
-----------------------------*/
add_filter('get_the_excerpt', function($excerpt, $post) {
    if (empty($excerpt)) {
        $content = strip_shortcodes($post->post_content);
        $content = wp_strip_all_tags($content);
        $excerpt = wp_trim_words($content, 30, '...');
    }
    return $excerpt;
}, 10, 2);

/* -----------------------------
   Ajouter le badge CF7 à toutes les images à la une
-----------------------------*/
add_filter('post_thumbnail_html', function($html, $post_id, $post_thumbnail_id, $size, $attr) {

    if (is_single($post_id)) return $html;

    if (get_post_meta($post_id, '_source', true) === 'cf7') {

        $lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';

        // Récupère le texte depuis le plugin, si vide → ne rien afficher
        $badge_text = get_option($lang === 'en' ? 'impactdev_cf7_badge_en' : 'impactdev_cf7_badge_fr', '');
        if (!empty($badge_text)) {
            $badge_html = '<div class="cf7-badge">' . esc_html($badge_text) . '</div>';
            $html = $badge_html . $html;
        }
    }
    return $html;
}, 10, 5);
 // script IB 
 add_action('init', function() {
    // ID de la nouvelle image par défaut
    $new_default_image_id = get_option('impactdev_default_image_id');
    if (!$new_default_image_id || !get_post($new_default_image_id)) return;

    // Requête pour tous les posts CF7
    $cf7_posts = get_posts([
        'post_type'      => 'post',
        'meta_key'       => '_source',
        'meta_value'     => 'cf7',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    ]);

    foreach ($cf7_posts as $post) {
        $thumb_id = get_post_thumbnail_id($post->ID);

        // Si pas de thumbnail ou ancienne image par défaut
        if (!$thumb_id || $thumb_id != $new_default_image_id) {
            set_post_thumbnail($post->ID, $new_default_image_id);
            update_post_meta($post->ID, '_cf7_default_image', '1');
        }
    }

    // Supprime l'action après exécution pour éviter de rerun
    remove_action('init', __FUNCTION__);
});


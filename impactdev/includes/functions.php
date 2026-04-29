<?php

defined('ABSPATH') || exit;
// REDIRECTION APRES LOGIN
add_action('wp_login', function ($user_login, $user) {

    if (!($user instanceof WP_User)) {
        return;
    }

    // redirect_to → priorité
    if (!empty($_REQUEST['redirect_to'])) {
        wp_safe_redirect(esc_url_raw($_REQUEST['redirect_to']));
        exit;
    }

    // Détection exacte de la langue via l’URL d’origine
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $is_english = (strpos($referer, '/en/') !== false);

    // Redirection selon langue détectée
    $redirect_url = $is_english
        ? site_url('/en/my-space/')
        : site_url('/mon-espace/');

    wp_safe_redirect($redirect_url);
    exit;
}, 10, 2);




// Protection des pages "Proposer une actualité" (FR et EN)
add_action('template_redirect', 'impactdev_protect_proposer_pages');
function impactdev_protect_proposer_pages()
{
    if (is_user_logged_in()) return;

    $request_uri = $_SERVER['REQUEST_URI'];

    // Page FR
    if (strpos($request_uri, '/proposez-une-actualite/') !== false) {
        $login_url = site_url('/connexion/');
        wp_safe_redirect(add_query_arg('redirect_to', urlencode(site_url($request_uri)), $login_url));
        exit;
    }

    // Page EN
    if (strpos($request_uri, '/en/propose-a-news-article/') !== false) {
        $login_url = site_url('/en/login/');
        wp_safe_redirect(add_query_arg('redirect_to', urlencode(site_url($request_uri)), $login_url));
        exit;
    }
}
// Modifier dynamiquement le lien du menu "Contribuer" / "Contribute"
add_filter('wp_nav_menu_objects', 'impactdev_modify_contribuer_menu_link', 20, 2);
function impactdev_modify_contribuer_menu_link($items, $args)
{
    $user_id = get_current_user_id(); // 0 si pas connecté
    $is_en = false;

    // Détecter la langue en regardant l'URL
    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/en/') === 0) {
        $is_en = true;
    }

    foreach ($items as $item) {
        $title = trim(mb_strtolower($item->title));
        $url_page = '';

        // Menu Contribuer
        if (strpos($title, 'contribuer') !== false) {
            $url_page = site_url('/alimentation-du-catalogue/');
        } elseif (strpos($title, 'contribute') !== false) {
            $url_page = site_url('/en/contribute/');
        } else {
            continue; // pas notre menu
        }

        if ($user_id > 0) {
            // Utilisateur connecté → lien direct
            $item->url = $url_page;
        } else {
            // Non connecté → redirection login avec redirect_to
            $login_url = $is_en ? site_url('/en/login/') : site_url('/connexion/');
            $item->url = add_query_arg('redirect_to', urlencode($url_page), $login_url);
        }
    }

    return $items;
}

// PROTECTION directe : empêcher accès URL sans connexion
add_action('template_redirect', 'impactdev_protect_catalogue_pages');
function impactdev_protect_catalogue_pages()
{
    if (is_user_logged_in()) return;

    $request_uri = $_SERVER['REQUEST_URI'];

    // Si utilisateur tape directement la page FR
    if (strpos($request_uri, '/alimentation-du-catalogue/') !== false) {
        wp_redirect(add_query_arg('redirect_to', urlencode(site_url($request_uri)), site_url('/connexion/')));
        exit;
    }

    // Si utilisateur tape directement la page EN
    if (strpos($request_uri, '/en/contribute/') !== false) {
        wp_redirect(add_query_arg('redirect_to', urlencode(site_url($request_uri)), site_url('/en/login/')));
        exit;
    }
}

// Forcer la redirection vers la page cible après login
add_action('template_redirect', 'impactdev_redirect_if_logged_in');
function impactdev_redirect_if_logged_in()
{
    if (is_user_logged_in() && isset($_GET['redirect_to'])) {
        $redirect_to = esc_url_raw($_GET['redirect_to']);
        if ($redirect_to && strpos($_SERVER['REQUEST_URI'], $redirect_to) === false) {
            wp_safe_redirect($redirect_to);
            exit;
        }
    }
}




/* categorie article */
function shortcode_categorie_article()
{
    global $post;

    if (is_singular('post')) {
        $categories = get_the_category($post->ID);
        if (!empty($categories)) {
            return esc_html($categories[0]->name);
        } else {
            return 'Aucune catégorie';
        }
    }

    return '';
}
add_shortcode('categorie_article', 'shortcode_categorie_article');
/*Désactiver complètement les archives */

/* flux rss dans les pages articles  */
function ajouter_bouton_flux_rss_sur_articles()
{
    if (is_singular('post')) {  // Seulement pages article
?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var breadcrumb = document.querySelector('.breadcrumb-wrapper-inner');
                if (breadcrumb && !breadcrumb.querySelector('.breadcrumb-right-flux')) {
                    var btn = document.createElement('a');
                    btn.href = '/feed';
                    btn.className = 'breadcrumb-right-flux';
                    btn.setAttribute('aria-label', 'Flux RSS');
                    btn.innerHTML = '<i class="fas fa-rss"></i>';
                    breadcrumb.appendChild(btn);
                }
            });
        </script>
    <?php
    }
}
add_action('wp_footer', 'ajouter_bouton_flux_rss_sur_articles');


/*Fonction multilangage*/
function shortcode_lang_switcher_simple()
{
    $langs = pll_the_languages(array(
        'raw' => 1,
        'hide_current' => 0
    ));

    if (!$langs) return '';

    $output = [];
    foreach ($langs as $code => $lang) {
        $class = $lang['current_lang'] ? 'current-lang' : '';
        $output[] = '<a href="' . esc_url($lang['url']) . '" class="' . $class . '">' . esc_html($lang['name']) . '</a>';
    }

    // Espaces autour du | pour séparateur
    return implode(' | ', $output);
}
add_shortcode('language-switcher', 'shortcode_lang_switcher_simple');



/* traduction du menu */
// Shortcode pour afficher le menu utilisateur avec language switcher
if (!function_exists('shortcode_menu_utilisateur_socials')) {
    function shortcode_menu_utilisateur_socials()
    {
        ob_start();

        // Récupère la langue actuelle
        $lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';

        // Choisit le menu selon la langue
        $menu_name = ($lang === 'en') ? 'menu_login_en' : 'menu_login';

        wp_nav_menu([
            'menu' => $menu_name,
            'container' => 'div',
            'container_class' => 'menu-utilisateur-wrapper',
            'menu_class' => 'menu-utilisateur',
            'fallback_cb' => false
        ]);

        echo '<div class="menu-language">' . do_shortcode('[language-switcher]') . '</div>';

        return ob_get_clean();
    }
    add_shortcode('menu_utilisateur_socials', 'shortcode_menu_utilisateur_socials');
}


if (!function_exists('menu_login_dynamique')) {
    function menu_login_dynamique($items, $args)
    {
        // Vérifie si le menu est le menu utilisateur (FR ou EN)
        $allowed_menus = ['menu_login', 'menu_login_en'];

        if (
            (isset($args->menu) && in_array($args->menu, $allowed_menus)) ||
            (isset($args->menu->slug) && in_array($args->menu->slug, $allowed_menus)) ||
            (isset($args->theme_location) && $args->theme_location === 'menu_login')
        ) {
            // Pattern pour les sous-menus FR/EN
            $pattern = '/<li\b[^>]*>(?:(?!<\/li>).)*(Mon espace|Mes paramètres|Gestion des référentiels|Gestion des institutions|My space|Settings|Repository management|Management of institutions)(?:(?!<\/li>).)*<\/li>/is';

            // masquer sous menu  institutions 
            $pattern_hide = '/<li\b[^>]*>(?:(?!<\/li>).)*(Gestion des institutions|Management of institutions)(?:(?!<\/li>).)*<\/li>/is';

            // les enleve de menu 
            $items = preg_replace($pattern_hide, '', $items);


            if (is_user_logged_in()) {
                $current_user = wp_get_current_user();
                $user_name = esc_html(trim($current_user->first_name . ' ' . $current_user->last_name));

                // Supprime "Login" ou "Se connecter"
                $items = preg_replace('/<li.*?(Connexion|Login).*?<\/li>/is', '', $items);

                // Récupère les sous-menus existants
                preg_match_all($pattern, $items, $matches);
                $submenu_items = '';
                if (!empty($matches[0])) {
                    $submenu_items = implode('', $matches[0]);
                    $items = preg_replace($pattern, '', $items);
                }

                // Crée le sous-menu utilisateur avec nom et logout
                $logout_label = function_exists('pll__') ? pll__('Déconnexion') : __('Logout', 'text-domain');
                $items .= '<li class="menu-item menu-user">
                                <a href="#"><i class="fa fa-user"></i> ' . $user_name . '</a>
                                <ul class="sub-menu">' . $submenu_items .
                    '<li><a href="' . esc_url(add_query_arg('custom_logout', 1, home_url())) . '">' . $logout_label . '</a></li>
                                </ul>
                           </li>';
            } else {
                // Supprime les liens utilisateur si non connecté
                $items = preg_replace($pattern, '', $items);
            }
        }

        return $items;
    }
    add_filter('wp_nav_menu_items', 'menu_login_dynamique', 10, 2);
}




// /* ajout d'un champ lieu pour les artilces pour le filtrage */
// function creer_taxonomie_lieu() {
//     $labels = array(
//         'name' => 'Lieux',
//         'singular_name' => 'Lieu',
//         'search_items' => 'Chercher un lieu',
//         'all_items' => 'Tous les lieux',
//         'edit_item' => 'Modifier lieu',
//         'update_item' => 'Mettre à jour lieu',
//         'add_new_item' => 'Ajouter un nouveau lieu',
//         'new_item_name' => 'Nouveau nom de lieu',
//         'menu_name' => 'Lieux',
//     );

//     register_taxonomy('lieu', 'post', array(
//         'hierarchical' => true,
//         'labels' => $labels,
//         'show_ui' => true,
//         'show_admin_column' => true,
//         'query_var' => true,
//         'rewrite' => array('slug' => 'lieu'),
//     ));
// }
// add_action('init', 'creer_taxonomie_lieu');
// function afficher_lieu_shortcode() {
//     $lieux = get_the_terms( get_the_ID(), 'lieu' );

//     if ( $lieux && ! is_wp_error( $lieux ) ) {
//         $lieu_noms = array();

//         foreach ( $lieux as $lieu ) {
//             $lieu_noms[] = esc_html( $lieu->name );
//         }

//         return '<p><strong>Lieu :</strong> ' . implode( ', ', $lieu_noms ) . '</p>';
//     }

//     return '';
// }
// add_shortcode('lieu', 'afficher_lieu_shortcode');
//recherche avec mot clé
add_filter('mdf_wp_query_args', 'mdf_combined_keyword_category_filter', 10, 2);
function mdf_combined_keyword_category_filter($args, $wp_query)
{
    if (isset($_GET['mdf_s']) && !empty($_GET['mdf_s'])) {
        $args['s'] = sanitize_text_field($_GET['mdf_s']);
    }

    return $args;
}

/* shortcode acf */
add_filter('acf/settings/remove_wp_meta_box', '__return_false');
add_filter('acf/format_value/type=text', 'do_shortcode');
add_filter('acf/format_value/type=textarea', 'do_shortcode');
function activer_acf_shortcode()
{
    add_shortcode('acf', 'acf_shortcode_handler');
}

function acf_shortcode_handler($atts)
{
    $atts = shortcode_atts([
        'field' => '',
        'post_id' => get_the_ID()
    ], $atts);

    if (!$atts['field']) return '';

    return get_field($atts['field'], $atts['post_id']);
}

add_action('init', 'activer_acf_shortcode');
/* ******************  traduction **********************  */
/* traduction footer ca marche ici pas de conf  */
function register_footer_strings()
{
    pll_register_string('footer', 'Piloté par', 'Footer');
    pll_register_string('footer', 'Financé par', 'Footer');
    pll_register_string('footer', 'Accompagné par', 'Footer');
    pll_register_string('footer', 'En association avec', 'Footer');
    pll_register_string('footer', '@Nous contacter', 'Footer');
}
add_action('init', 'register_footer_strings');
function footer_translate_shortcode($atts)
{
    $atts = shortcode_atts([
        'key' => ''
    ], $atts);

    return esc_html(pll__($atts['key']));
}
add_shortcode('footer_translate', 'footer_translate_shortcode');

add_filter('widget_text', 'do_shortcode');
add_filter('widget_text_content', 'do_shortcode');
function disable_category_links_js()
{
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('a[href*="/category/"]').forEach(function(link) {
                const span = document.createElement('span');
                span.innerHTML = link.innerHTML;
                span.className = link.className;
                link.replaceWith(span);
            });
        });
    </script>
<?php
}
add_action('wp_footer', 'disable_category_links_js');


function disable_category_archives()
{
    if (is_category()) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'disable_category_archives');

function disable_tag_archives()
{
    if (is_tag()) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'disable_tag_archives');

function disable_author_archives()
{
    if (is_author()) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'disable_author_archives');

/* deconnexion */
function enqueue_user_menu_inline_script()
{
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trigger = document.querySelector('.custom-user-menu-trigger');
            const dropdown = document.querySelector('.custom-user-menu-dropdown');
            const wrapper = document.querySelector('.custom-user-menu');

            if (trigger && dropdown && wrapper) {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    wrapper.classList.toggle('active');
                });

                document.addEventListener('click', function(e) {
                    if (!wrapper.contains(e.target)) {
                        wrapper.classList.remove('active');
                    }
                });
            }
        });
    </script>
    <style>
        /* Empêche tout hover */
        .custom-user-menu:hover .custom-user-menu-dropdown {
            display: none !important;
        }

        .custom-user-menu-dropdown {
            display: none !important;
            position: absolute;
            background: #fff;
            border: 1px solid #ccc;

            padding: 2px;
            margin: 0;
            min-width: 50px;
            z-index: 999;
        }

        .custom-user-menu.active .custom-user-menu-dropdown {
            display: block !important;
        }

        .custom-user-menu-trigger {
            cursor: pointer;
        }
    </style>
<?php
}
add_action('wp_footer', 'enqueue_user_menu_inline_script');



/*icone twitter x */

function remplacer_icone_twitter_script()
{
?>
    <script>
        jQuery(window).on('load', function() {
            jQuery('i.tm-brivona-icon-twitter').each(function() {
                jQuery(this).replaceWith(
                    '<img src="/wp-content/uploads/2025/06/twitter.png" alt="Twitter" class="custom-twitter-icon">'
                );
            });
        });
    </script>
    <style>
        .custom-twitter-icon {
            width: 14px;
            height: 14px;
            display: inline-block;
            vertical-align: middle;
            object-fit: contain;
            margin: 0 !important;
            padding: 0 !important;
            position: relative;
            top: -1px;
            /* ajuste ici pour monter ou descendre */
        }
    </style>
    <?php
}
add_action('wp_footer', 'remplacer_icone_twitter_script');
/* categorie proposition actualité */
add_filter('wpcf7_form_elements', 'cf7_injecter_categories_dynamiques_radio_vertical');
function cf7_injecter_categories_dynamiques_radio_vertical($form)
{
    if (strpos($form, '[cat_dyn]') !== false) {
        $categories = get_categories(array(
            'orderby' => 'name',
            'hide_empty' => false,
            'exclude' => array(1, 39, 188, 8, 197),
        ));

        $radios = '<span class="cf7-categories-radio">';

        foreach ($categories as $category) {
            // display:block pour que chaque catégorie soit sur sa ligne
            $radios .= '<label style="display:block; margin-bottom:4px;">';
            $radios .= '<input type="radio" name="categorie-article" value="' . esc_attr($category->term_id) . '"> ';
            $radios .= esc_html($category->name);
            $radios .= '</label>';
        }

        $radios .= '</span>';

        $form = str_replace('[cat_dyn]', $radios, $form);
    }

    return $form;
}



//  /*  compteur article */
// function shortcode_animated_post_count() {
//     $count = wp_count_posts()->publish;
//     $id = 'animated-count-' . uniqid();

//     $style = "font-size: 55px; font-weight: normal; display: inline-block; color:white; padding:10px;";

//     $output = '<span id="'. esc_attr($id) .'" data-count="'. esc_attr($count) .'" style="'. $style .'">0</span>';

//     $output .= "
//     <script>
//     document.addEventListener('DOMContentLoaded', function() {
//         var el = document.getElementById('". esc_js($id) ."');
//         var countTo = parseInt(el.getAttribute('data-count'), 10);
//         var current = 0;
//         var stepTime = Math.max(20, Math.floor(3000 / countTo)); // 3 secondes max

//         function animateCount() {
//             var timer = setInterval(function() {
//                 current++;
//                 el.textContent = current;
//                 if(current >= countTo) {
//                     clearInterval(timer);
//                 }
//             }, stepTime);
//         }

//         if ('IntersectionObserver' in window) {
//             var observer = new IntersectionObserver(function(entries, observer) {
//                 entries.forEach(function(entry) {
//                     if(entry.isIntersecting) {
//                         animateCount();
//                         observer.unobserve(el);
//                     }
//                 });
//             }, { threshold: 0.1 });
//             observer.observe(el);
//         } else {
//             animateCount();
//         }
//     });
//     </script>
//     ";

//     return $output;
// }
// add_shortcode('animated_post_count', 'shortcode_animated_post_count');

///////////////////////////////////////////////////////////////////////////////////////////
/* 12 08 2025 ib - Protection et redirections multilingues */

/* 12 08 2025 ib */

// Retourne l'URL de la page login selon la langue
function impactdev_get_login_url_lang()
{
    if (function_exists('pll_current_language')) {
        $lang = pll_current_language();
        if ($lang === 'fr') {
            return rtrim(get_site_url(), '/') . '/connexion/';
        } elseif ($lang === 'en') {
            return rtrim(get_site_url(), '/') . '/login/';
        }
    }
    return rtrim(get_site_url(), '/') . '/login/';
}





// Permet de supprimer la session après avoir redirigé pour que ça ne bloque pas la navigation
add_action('init', 'clear_redirect_subscriber_session');
function clear_redirect_subscriber_session()
{
    if (!session_id()) {
        session_start();
    }
    if (isset($_SESSION['redirected_to_mon_espace'])) {
        unset($_SESSION['redirected_to_mon_espace']);
    }
}







// 3. Redirection après logout selon la langue
add_action('init', function () {
    if (isset($_GET['custom_logout'])) {
        wp_logout();
        if (function_exists('pll_home_url')) {
            wp_redirect(pll_home_url(pll_current_language('slug')));
        } else {
            wp_redirect(home_url());
        }
        exit;
    }
});









/* filtrage des posts par categories */
function register_type_contenu_taxonomy()
{
    $labels = array(
        'name'              => 'Types de contenu',
        'singular_name'     => 'Type de contenu',
        'search_items'      => 'Rechercher un type',
        'all_items'         => 'Tous les types',
        'edit_item'         => 'Modifier le type',
        'update_item'       => 'Mettre à jour le type',
        'add_new_item'      => 'Ajouter un nouveau type',
        'new_item_name'     => 'Nouveau type',
        'menu_name'         => 'Types de contenu',
    );

    register_taxonomy('type_contenu', array('post'), array(
        'hierarchical'      => true, // comme catégories
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'type-contenu'),
    ));
}
add_action('init', 'register_type_contenu_taxonomy');
/* filtrer par type de contenu */
// Shortcode pour filtrer les articles par Type de contenu
function filter_type_contenu_shortcode()
{
    // Récupérer la valeur sélectionnée
    $selected_type = isset($_GET['type_contenu']) ? sanitize_text_field($_GET['type_contenu']) : '';

    // Formulaire de filtre
    $output = '<form method="get">';
    $output .= '<select name="type_contenu" onchange="this.form.submit()">';
    $output .= '<option value="">— Choisir un type —</option>';

    $terms = get_terms(array(
        'taxonomy'   => 'type_contenu',
        'hide_empty' => true,
    ));

    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $selected = ($selected_type == $term->slug) ? 'selected' : '';
            $output .= '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
        }
    }

    $output .= '</select>';
    $output .= '</form>';

    // Query pour afficher les articles filtrés
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
    );

    if (!empty($selected_type)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'type_contenu',
                'field'    => 'slug',
                'terms'    => $selected_type,
            ),
        );
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $output .= '<ul>';
        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        $output .= '</ul>';
    } else {
        $output .= '<p>Aucun article trouvé.</p>';
    }

    wp_reset_postdata();

    return $output;
}
add_shortcode('filter_type_contenu', 'filter_type_contenu_shortcode');
/* si aucune categorie */

/* section mon espace utilisateur */
// Shortcode pour afficher les publications de l'utilisateur
function impactdev_mes_publications()
{
    if (!is_user_logged_in()) {
        return (function_exists('pll_current_language') && pll_current_language() === 'en')
            ? '<p>You must be logged in to see your posts.</p>'
            : '<p>Vous devez être connecté pour voir vos publications.</p>';
    }

    $user_id = get_current_user_id();
    $args = array(
        'post_type'      => array('post', 'evenement', 'offre_emploi'),
        'author'         => $user_id,
        'post_status'    => array('draft', 'pending', 'publish'),
        'posts_per_page' => -1,
    );
    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return (function_exists('pll_current_language') && pll_current_language() === 'en')
            ? '<p>You have not published anything yet.</p>'
            : '<p>Vous n’avez encore rien publié.</p>';
    }

    $is_en = function_exists('pll_current_language') && pll_current_language() === 'en';

    $output  = '<table class="mes-publications">';
    $output .= '<tr>
        <th>' . ($is_en ? 'Title' : 'Titre') . '</th>
        <th>' . ($is_en ? 'Category' : 'Catégorie') . '</th>
        <th>' . ($is_en ? 'Status' : 'Statut') . '</th>
        <th>' . ($is_en ? 'Submission Date' : 'Date de soumission') . '</th>
        <th>' . ($is_en ? 'Publication Date' : 'Date de publication') . '</th>
        <th>' . ($is_en ? 'Action' : 'Action') . '</th>
    </tr>';

    while ($query->have_posts()) {
        $query->the_post();
        $status_slug  = get_post_status();
        $categories = get_the_terms(get_the_ID(), 'category');
        $cat_display = !empty($categories) && !is_wp_error($categories)
            ? implode(', ', wp_list_pluck($categories, 'name'))
            : '—';

        $date_soumission = get_the_date('d/m/Y');
        $date_publication = ($status_slug === 'publish') ? get_the_date('d/m/Y', get_the_ID()) : '—';

        if ($status_slug === 'publish') {
            $status_display = '<span class="badge bg-success" style="font-weight:500;display:inline-block;width:75px;text-align:center;">'
                . ($is_en ? 'Published' : 'Publié') .
                '</span>';
        } elseif ($status_slug === 'pending') {
            $status_display = '<span class="badge bg-warning" style="font-weight:500">'
                . ($is_en ? 'Pending' : 'En attente') .
                '</span>';
        } else {
            $status_display = '<span class="badge" style="font-weight:500;background-color:#ffc91c;display:inline-block;width:75px;text-align:center;">'
                . ($is_en ? 'Pending' : 'En attente') .
                '</span>';
        }

        $output .= '<tr>';
        $output .= '<td>' . esc_html(get_the_title()) . '</td>';
        $output .= '<td>' . esc_html($cat_display) . '</td>';
        $output .= '<td>' . $status_display . '</td>';
        $output .= '<td>' . esc_html($date_soumission) . '</td>';
        $output .= '<td>' . esc_html($date_publication) . '</td>';
        $output .= ($status_slug === 'publish')
            ? '<td><a class="btn-view" href="' . esc_url(get_permalink()) . '" title="Voir" target="_blank"><i class="fa fa-eye"></i></a></td>'
            : '<td>—</td>';
        $output .= '</tr>';
    }

    $output .= '</table>';
    wp_reset_postdata();

    return $output;
}
add_shortcode('mes_publications', 'impactdev_mes_publications');


// Notification email auteur avec template complet et contenu FR/EN
function impactdev_notify_author_on_publish($new_status, $old_status, $post)
{
    if (! in_array($post->post_type, array('post', 'evenement', 'offre_emploi'))) {
        return;
    }

    if ($old_status !== 'publish' && $new_status === 'publish') {
        $author_id     = $post->post_author;
        $author_email  = get_the_author_meta('user_email', $author_id);
        $author_name   = get_the_author_meta('display_name', $author_id);
        $post_title    = get_the_title($post->ID);
        $post_url      = get_permalink($post->ID);
        $site_url = site_url();

        $subject = '[FReSH] Votre actualité est publiée !';

        $message = "
<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <meta http-equiv='x-ua-compatible' content='ie=edge'>
  <title>Email FReSH</title>
  <style type='text/css'>
    body,table,td,a { -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
    table,td { mso-table-lspace:0pt; mso-table-rspace:0pt; }
    img { -ms-interpolation-mode:bicubic; border:0; display:block; outline:none; text-decoration:none; height:auto; }
    body { margin:0; padding:0; width:100% !important; background:#ffffff; font-family: Arial, Helvetica, sans-serif; color:#333333; text-align:left; }
    .email-container { width:100%; max-width:600px; margin:0 auto; text-align:left; }
    .two-col { width:100%; }
    .col { vertical-align:top; }
    @media screen and (max-width:480px) {
      .stack { display:block !important; width:100% !important; max-width:100% !important; }
      .stack-center { text-align:center !important; }
      .footer-logo { border-right:0 !important; border-bottom:1px solid #00a6e2 !important; padding-bottom:12px !important; margin-bottom:8px !important; }
      .footer-text { padding-top:12px !important; }
      .no-pad-mobile { padding:0 !important; }
    }
    p { margin:0 0 12px 0; }
  </style>
</head>
<body style='margin:0;padding:0;'>

  <!-- Wrapper -->
  <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%'>
    <tr>
      <td align='left' style='padding:20px 10px;'>

        <!-- Main container -->
        <table align='left' role='presentation' cellpadding='0' cellspacing='0' border='0' class='email-container' style='width:100%;max-width:600px; border-radius:6px;overflow:hidden;'>

          <!-- Contenu FR -->
          <tr>
            <td style='font-size:15px;line-height:1.6;color:#333333;text-align:left;'>
              <p>Bonjour {$author_name},</p>
              <p>
                L’actualité « {$post_title} » a été publiée avec succès et est désormais disponible sur le portail  
                <a href='{$site_url}' style='color:#00a6e2;text-decoration:underline;'>FReSH</a>.
              </p>
              <p>Merci pour votre contribution.</p>
              <hr style='margin:30px 0; border:none; border-top:1px solid #ddd;'>
            </td>
          </tr>

          <tr>
            <td style='font-size:15px;line-height:1.6;color:#333333;text-align:left;'>
              <p>Dear {$author_name},</p>
              <p>
                The news item ‘{$post_title}’ has been successfully published and is now available on the 
                <a href='{$site_url}' style='color:#00a6e2;text-decoration:underline;'>FReSH</a> portal.
              </p>    
              <p>Thank you for your contribution.</p>
            </td>
          </tr>

          <!-- Separator -->
          <tr>
            <td style='line-height:1px;font-size:1px;background:#00a6e2;height:1px;'>&nbsp;</td>
          </tr>

          <!-- Footer two-column -->
          <tr>
            <td style='padding:18px 0;background:#ffffff;'>
              <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%' class='two-col' style='border-collapse:collapse; text-align:left;'>
                <tr>
                  <!-- Colonne logo -->
                  <td class='col stack footer-logo' style='width:25%; padding-right:12px; border-right:1px solid #00a6e2; vertical-align:top;'>
                    <a href='" . home_url() . "' target='_blank' style='text-decoration:none;'>
                      <img src='" . home_url('/wp-content/uploads/2025/10/Logo_FReSH-1.png') . "' width='100%' style='display:block; max-width:100px; height:auto;' alt='Logo FReSH'>
                    </a>
                  </td>

                  <!-- Colonne texte -->
                  <td class='col stack footer-text' style='width:75%; padding-left:12px; vertical-align:top;'>
                    <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%'>
                      <tr>
                        <td style='font-size:14px; line-height:20px; color:#333333; text-align:left;'>
                          <strong><p>Équipe FReSH</p></strong>
                          <p>Portail des études en santé</p>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

        </table>
        <!-- End main container -->

      </td>
    </tr>
  </table>

</body>
</html>
";


        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($author_email, $subject, $message, $headers);
    }
}
add_action('transition_post_status', 'impactdev_notify_author_on_publish', 10, 3);




/* reset password old */


// add_filter('login_redirect', 'impactgroup_login_redirect_after_reset', 10, 3);

// function impactgroup_login_redirect_after_reset($redirect_to, $requested_redirect_to, $user) {
//     // Si on est sur une action reset (resetpass)
//     if (isset($_POST['action']) && $_POST['action'] === 'resetpass') {
//         // Récupérer l'URL de redirection depuis le paramètre
//         $redirect_url = isset($_POST['redirect_to']) ? esc_url_raw($_POST['redirect_to']) : home_url('/connexion');
//         return $redirect_url;
//     }
//     return $redirect_to; // sinon, laisser WP décider
// }

































// Ajouter un bouton proposer-une-actualite  uniquement sous le contenu des single posts
add_filter('the_content', 'md_bouton_single_post');
function md_bouton_single_post($content)
{
    if (!is_singular('post')) {
        return $content;
    }

    // Détection de la langue
    $lang = get_bloginfo('language'); // 'fr-FR' ou 'en-US'
    $is_english = strpos($lang, 'en') === 0;

    // URL de la page "Proposer une actualité"
    $url_proposer = $is_english ? home_url('/en/propose-a-news-article/') : home_url('/proposez-une-actualite/');
    $text = $is_english ? 'Submit news' : 'Proposez une actualité';

    // Si non connecté → redirection vers login avec redirect vers la page de proposition
    if (!is_user_logged_in()) {
        $login_url = $is_english ? home_url('/en/login/') : home_url('/connexion/');
        $url_proposer = add_query_arg('redirect_to', urlencode($url_proposer), $login_url);
    }

    $button = '<div class="mon-bouton-wrapper" style="text-align: center;">
                   <hr style="margin-bottom: 30px; border: 0; border-top: 1px solid #ccc;">
                   <a href="' . esc_url($url_proposer) . '" class="bouton-post">' . esc_html($text) . '</a>
               </div>';

    return $content . $button;
}



// Exclure "Uncategorized" des listes de catégories
add_filter('wp_list_categories', function ($output) {
    // Supprimer "Uncategorized" et "Non classé"
    $output = preg_replace(
        '/<li[^>]*>\s*<a[^>]*>(Uncategorized|Non classé)<\/a>\s*<\/li>/i',
        '',
        $output
    );
    return $output;
});



// Remplace la fonction par défaut du thème
if (!function_exists('themetechmount_blogbox_readmore')) {
    function themetechmount_blogbox_readmore()
    {
        $return        = '';
        $readMore_text = themetechmount_blogbox_readmore_text();  // texte "Lire plus"

        // On enlève les conditions, et on affiche toujours
        $return .= '<div class="themetechmount-blogbox-footer-left themetechmount-wrap-cell">';
        $return .= '<a href="' . get_permalink() . '">' . $readMore_text . '</a>';
        $return .= '</div>';

        return $return;
    }
}


// Lors de l'inscription via User Registration, on copie la valeur du champ téléphone
add_action('personal_options_update', 'frid_sync_user_phone');
add_action('edit_user_profile_update', 'frid_sync_user_phone');
add_action('user_register', 'frid_sync_user_phone');

function frid_sync_user_phone($user_id)
{
    if (isset($_POST['phone_number'])) {
        $new_phone = sanitize_text_field($_POST['phone_number']);

        // Met à jour la clé standard si elle existe
        update_user_meta($user_id, 'phone_number', $new_phone);

        // Met à jour aussi le champ User Registration
        update_user_meta($user_id, 'user_registration_input_box_1762110056', $new_phone);
    }
}


/* message derreur connexion */
add_filter('login_errors', function ($error) {
    if (strpos($error, 'The username or password you entered is incorrect.') !== false) {
        return '<strong>Erreur</strong> : Identifiant ou mot de passe incorrect. <a href="' . wp_lostpassword_url() . '">Mot de passe oublié ?</a>';
    }
    return $error;
});


add_filter('gettext', function ($translated_text, $text, $domain) {
    if (function_exists('pll_current_language') && pll_current_language() === 'fr') {
        if (strpos($text, 'Search Results for') !== false) {
            $translated_text = str_replace('Search Results for', 'Résultats de recherche pour', $text);
        }
        if (strpos($text, 'You searched for') !== false) {
            $translated_text = str_replace('You searched for', 'Vous avez recherché', $text);
        }
        if (strpos($text, 'Page results') !== false) {
            $translated_text = str_replace('Page results', 'Pages', $text);
        }
        if (strpos($text, 'Article results') !== false) {
            $translated_text = str_replace('Article results', 'Articles', $text);
        }
        if (strpos($text, 'View more') !== false) {
            $translated_text = str_replace('View more', 'Voir plus', $text);
        }
        if (strpos($text, 'results') !== false) {
            $translated_text = str_replace('results', '', $text);
        }
        if (strpos($text, 'Search  for') !== false) {
            $translated_text = str_replace('Search  for', 'Recherche pour', $text);
        }
    }
    return $translated_text;
}, 10, 3);



/* redirection user normal  */
// Forcer redirection abonnés User Registration
add_filter('user_registration_login_redirect', 'force_subscriber_redirect_ur', 100, 2);
function force_subscriber_redirect_ur($redirect_to, $user)
{
    if ($user instanceof WP_User) {
        // Si l'utilisateur est abonné (subscriber)
        if (in_array('subscriber', $user->roles)) {
            return site_url('/mon-espace/'); // Redirection forcée
        }
    }
    return $redirect_to; // Les autres roles gardent la redirection par défaut
}

// add_action('template_redirect', function() {
//     if ( isset($_GET['checkemail']) && $_GET['checkemail'] === 'confirm' ) {
//         $current_url = $_SERVER['REQUEST_URI'];
//         $redirect_page = ( strpos($current_url, '/en/') !== false )
//             ? '/en/reset-password/'
//             : '/reinitialiser-mot-de-passe/';
//         wp_safe_redirect( home_url( $redirect_page . '?reset-link-sent=true' ) );
//         exit;
//     }
// });


/* apres inscriion */
add_action('user_registration_after_register_user', function ($user_id, $form_data, $form_id) {

    $current_url = $_SERVER['REQUEST_URI']; // URL actuelle

    // Vérifie si on est sur la version EN
    if (strpos($current_url, '/en/') !== false) {
        $redirect_url = '/en/login/';
    } else {
        $redirect_url = '/connexion/';
    }

    // Redirection
    if (! headers_sent()) {
        wp_redirect($redirect_url);
        exit;
    }
}, 10, 3);


// nv menu essi 
/* Shortcode pour afficher le menu "menu-topbar" */
function shortcode_menu_topbar()
{
    return wp_nav_menu(array(
        'menu' => 'menu-topbar',  // nom du menu que tu as créé
        'container' => 'div',
        'container_class' => 'menu-topbar-wrapper',
        'menu_class' => 'menu-topbar-items',
        'echo' => false, // retourne le HTML pour le shortcode
    ));
}
add_shortcode('menu_topbar', 'shortcode_menu_topbar');

/* reset password */


// add_filter('login_redirect', 'impactgroup_login_redirect_after_reset', 10, 3);

// function impactgroup_login_redirect_after_reset($redirect_to, $requested_redirect_to, $user) {
//     // Si on est sur une action reset (resetpass)
//     if (isset($_POST['action']) && $_POST['action'] === 'resetpass') {
//         // Récupérer l'URL de redirection depuis le paramètre
//         $redirect_url = isset($_POST['redirect_to']) ? esc_url_raw($_POST['redirect_to']) : home_url('/connexion');
//         return $redirect_url;
//     }
//     return $redirect_to; // sinon, laisser WP décider
// }


// -----------------------------
// Rediriger après reset password selon la langue
// -----------------------------
// Injecter un JS sur la page de reset password


// Intercepter la fausse redirection du plugin UR après reset
// add_action('template_redirect', function () {
//     // Vérifier si on est sur l'URL du plugin
//     if (isset($_GET['password-reset']) && $_GET['password-reset'] === 'true') {

//         // Détecter la langue depuis l'URL
//         $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
//             . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

//         if (strpos($current_url, '/en/') !== false) {
//             wp_safe_redirect(home_url('/en/login'));
//         } else {
//             wp_safe_redirect(home_url('/connexion'));
//         }
//         exit;
//     }
// });


add_filter('wpcf7_posted_data', function ($posted_data) {
    $form = WPCF7_ContactForm::get_current();
    if ($form && in_array($form->id(), [14992, 22597])) {
        $user = wp_get_current_user();
        if ($user && $user->exists()) {
            $posted_data['user-email'] = $user->user_email;
        }
    }
    return $posted_data;
});

/*Redirection language dans recherche*/
add_action('template_redirect', function () {
    if (is_admin()) return;
    if (!function_exists('pll_home_url')) return;

    // Redirection uniquement si c'est une recherche et qu'on n'est pas déjà sur la page racine de la langue
    if (isset($_GET['s']) && !empty($_GET['s']) && !is_search()) {
        $search_query = sanitize_text_field($_GET['s']);
        $current_lang = pll_current_language();

        // URL racine de la langue
        $lang_home = trailingslashit(home_url('/' . $current_lang));

        // Crée l'URL de recherche correcte
        $search_url = add_query_arg('s', $search_query, $lang_home);

        wp_safe_redirect($search_url);
        exit;
    }
});
/* reset */
add_filter('locale', function ($locale) {
    if (isset($_GET['lang']) && $_GET['lang'] === 'en') {
        return 'en_US';
    }
    return $locale;
});
/* team membres */
add_action('template_redirect', function () {
    if (is_singular('team_member')) {
        // détecte la langue selon l’URL
        $current_lang = substr($_SERVER['REQUEST_URI'], 1, 2); // ex: "en" ou "fr"

        if ($current_lang === 'en') {
            $slug = 'team'; // slug anglais
        } else {
            $slug = 'equipe'; // slug français
        }

        $page = get_page_by_path($slug);
        if ($page) {
            include get_page_template_slug($page) ?: locate_template('page.php');
            exit;
        }
    }
});

/* team member forcer redirection vers equipe ou team sans ouvrir la page de chaque membre  */
add_action('template_redirect', function () {
    $url = $_SERVER['REQUEST_URI'];

    // Redirection FR
    if (preg_match('/^\/team-member\//', $url)) {
        wp_redirect(home_url('/equipe/'));
        exit;
    }

    // Redirection EN
    if (preg_match('/^\/en\/team-member\//', $url)) {
        wp_redirect(home_url('/en/team/'));
        exit;
    }
});

/***  contact form 7 */

add_filter('wpcf7_mail_components', function ($components, $contact_form) {
    // Exécuter les shortcodes dans le corps de l'email
    if (!empty($components['body'])) {
        $components['body'] = do_shortcode($components['body']);
    }

    return $components;
}, 10, 2);

add_shortcode('user_name', function () {
    // Vérifie si un utilisateur est connecté
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        // Concatène prénom + nom
        return trim($user->first_name . ' ' . $user->last_name);
    }

    // Si pas connecté → rien
    return '';
});

// Ajoute le shortcode [home_url] utilisable dans CF7 ou ailleurs
add_shortcode('home_url', function () {
    // Récupère l'URL de la page d'accueil
    $site = home_url();
    // Retourne l'URL proprement
    return esc_url(trim($site));
});

// Permet d'exécuter les shortcodes dans les emails et formulaires CF7
add_filter('wpcf7_form_elements', 'do_shortcode');
add_filter('wpcf7_mail_body', 'do_shortcode');
add_filter('wpcf7_mail_html_body', 'do_shortcode');

/* Email de l'utilisateur */
add_shortcode('user_email', function () {
    // Vérifie si un utilisateur est connecté
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        // Retourne l'email de l'utilisateur connecté
        return trim($user->user_email);
    }

    // Si pas connecté → rien
    return '';
});

// Injecter l'email du user connecté dans un champ Contact Form 7
add_filter('wpcf7_form_tag', function ($tag) {
    // Vérifie le nom du champ (ex: your-email)
    if ($tag['name'] == 'your-email-prop') {
        $current_user = wp_get_current_user();
        if ($current_user->exists()) {
            $tag['values'] = [$current_user->user_email];
            $tag['raw_values'] = [$current_user->user_email];
        }
    }
    return $tag;
});


//Empecher l'accès directe aux pages des archives
add_action('template_redirect', 'meks_remove_wp_archives');

//Remove archives 
function meks_remove_wp_archives()
{
    //If we are on category or tag or date or author archive
    if (is_category() || is_tag() || is_date() || is_author()) {
        global $wp_query;
        $wp_query->set_404(); //set to 404 not found page
    }
}


// Ajouter un bouton "Exporter CSV" à droite du bouton Filtrer
add_action('restrict_manage_users', 'add_export_csv_button_final', 100);
function add_export_csv_button_final($which)
{
    if ($which !== 'top') return;

    // Reprendre tous les paramètres GET pour les conserver lors de l’export
    $query = $_GET;
    $query['export_users_csv'] = 1;

    $export_url = add_query_arg($query, admin_url('users.php'));

    echo '<a href="' . esc_url($export_url) . '" class="button button-primary" style="margin-left:10px;margin-bottom: 10px;">Exporter CSV</a>';
}


// Export CSV respectant les filtres appliqués
add_action('admin_init', 'export_users_csv_admin');
function export_users_csv_admin()
{
    if (!current_user_can('administrator')) return;
    if (!isset($_GET['export_users_csv'])) return;

    // Obtenir la liste filtrée via WP_User_Query comme WordPress le fait pour le backoffice
    $query_args = array(
        'number' => -1, // tous les résultats
        'orderby' => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'ID',
        'order' => isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'ASC',
    );

    // Reprendre la recherche globale de WordPress
    if (!empty($_GET['s'])) {
        $query_args['search'] = '*' . sanitize_text_field($_GET['s']) . '*';
        $query_args['search_columns'] = array('user_login', 'user_email', 'user_nicename');
    }

    // Filtre par rôle
    if (!empty($_GET['role'])) {
        $query_args['role'] = sanitize_text_field($_GET['role']);
    }

    // Récupérer les utilisateurs filtrés
    $users = get_users($query_args);

    // Générer le CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="users.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'Nom d’utilisateur', 'Email', 'Rôles', 'Date d’inscription'));

    foreach ($users as $user) {
        fputcsv($output, array(
            $user->ID,
            $user->user_login,
            $user->user_email,
            implode(',', $user->roles),
            $user->user_registered
        ));
    }
    fclose($output);
    exit;
}

//Changement text d'une section

function changer_nom_menu_plugin()
{
    global $menu;

    foreach ($menu as $key => $value) {
        if ($value[0] == 'Flamingo') {
            $menu[$key][0] = 'NL/Contact';
        }
    }
}
add_action('admin_menu', 'changer_nom_menu_plugin', 999);

/* filtrage etiquette */
// Filtre les posts du Blog Box selon le champ personnalisé 'blogbox_tag'
function brivina_blogbox_filter_by_tag($query)
{
    if (!is_admin() && $query->is_main_query() && is_page()) {
        $tag = get_post_meta(get_the_ID(), 'blogbox_tag', true);
        if ($tag) {
            $query->set('tag', $tag);
        }
    }
}
add_action('pre_get_posts', 'brivina_blogbox_filter_by_tag');
/**
 * enable shortcodes
 */
function enable_shortcode_in_text_field($text)
{
    if (!empty($text)) {
        return do_shortcode($text);
    }
    return $text;
}


add_filter('widget_text', 'enable_shortcode_in_text_field');
add_filter('widget_title', 'enable_shortcode_in_text_field');
/* cat video */



function enqueue_my_scripts()
{
    // MediaElement.js
    wp_enqueue_script('wp-mediaelement');
    wp_enqueue_style('wp-mediaelement');

    wp_enqueue_script(
        'my-audioplayer-init',
        get_stylesheet_directory_uri() . '/js/my-audioplayer.js',
        ['jquery', 'wp-mediaelement'], // dépendances pour forcer l’ordre
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_my_scripts');

add_filter('show_admin_bar', function ($show) {
    if (!current_user_can('administrator')) {
        return false;
    }
    return true;
});


//hide menu selon role 
function hide_menu_items_by_role($items, $args)
{
    if (!current_user_can('admin_fresh')) {

        $hidden_slugs = [
            "gestion-institutions", // FR
            "manage-institutions", // EN
            "gestion-referentiel", // FR
            "repository-management", // EN
            "dictionnaire-des-variables-du-catalogue", // FR
            "catalogue-variable-dictionary" // EN
        ];

        foreach ($items as $key => $item) {
            $slug = get_post_field('post_name', $item->object_id);
            if (in_array($slug, $hidden_slugs)) {
                unset($items[$key]);
                continue; // passe au suivant pour éviter double unset
            }
        }
    }
    return $items;
}

add_filter('wp_nav_menu_objects', 'hide_menu_items_by_role', 10, 2);

// iB 30 12 2025
add_action('template_redirect', 'redirect_espace_prive_polylang');
function redirect_espace_prive_polylang()
{

    // Si l'utilisateur est connecté → on ne fait rien
    if (is_user_logged_in()) {
        return;
    }

    // Slugs des pages protégées (toutes langues)
    $protected_pages = array(
        'mon-espace', // FR
        'my-space',   // EN
        'alimentation-du-catalogue/', // FR
        'contribute/', // EN
        'gestion-referentiel', //FR
        'repository-management', // EN
        'referentiel-detail', // FR
        'repository-detail', // EN
        'dictionnaire-des-variables-du-catalogue', // EN
        'catalog-variable-dictionary', // FR
        'catalogue-upload',
        'televersement-catalogue',
        'gestion-institutions',
        'manage-institutions'
    );

    if (is_page($protected_pages)) {

        $request_uri = $_SERVER['REQUEST_URI'];

        // Langue courante Polylang
        $lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';

        // Choix de l'URL selon langue
        if ($lang === 'en') {
            $redirect_url =  add_query_arg(
                'redirect_to',
                site_url($request_uri),
                site_url('/en/login/')
            );
        } else {
            $redirect_url = add_query_arg(
                'redirect_to',
                site_url($request_uri),
                site_url('/connexion')
            );
        }

        wp_safe_redirect($redirect_url);
        exit;
    }
}


add_filter('cmtt_glossary_list_terms', function ($terms) {

    foreach ($terms as &$term) {
        // Normaliser la première lettre
        $letter = mb_substr($term->name, 0, 1);
        $letter_normalized = strtoupper(remove_accents($letter));
        $term->letter = $letter_normalized;
    }
    return $terms;
});

add_filter('cmtt_glossary_term_letter', function ($letter) {
    // Normalise la lettre (É, È, Ê, Ë → E)
    $letter = remove_accents($letter);
    echo $letter;

    // Toujours en majuscule
    return strtoupper($letter);
});

//shortcode email admin NL-Prop actualité
add_filter('wpcf7_special_mail_tags', function ($output, $name) {

    if ($name === 'email-admin') {
        return get_option('impactdev_email_admin', '');
    }

    return $output;
}, 10, 2);

//shortcode email admin formulaire contact
add_filter('wpcf7_special_mail_tags', function ($output, $name) {

    if ($name === 'email-admin-contact') {
        return get_option('impactdev_email_admin_contact', '');
    }

    return $output;
}, 10, 2);

/*Afficher Dashboard que pour les admin fresh et admin wp*/
add_action('wp_head', function () {
    if (! is_user_logged_in()) return;

    $user = wp_get_current_user();

    if (
        in_array('administrator', $user->roles) ||
        in_array('admin_fresh', $user->roles)
    ) {
        echo '<style>
            #dashboard-admin {
                display: flex;
            }
        </style>';
    }
});

/**
 * Override du tools.php de Brivona dans le thème enfant
 * Ce mu-plugin force le chargement du tools.php enfant
 */

$child_tools = get_stylesheet_directory() . '/inc/tools.php';

if (file_exists($child_tools)) {

    // Charger le fichier enfant en priorité

    include_once $child_tools;

    add_action('after_setup_theme', function () {});
}



add_action('wp_ajax_get_custom_posts', 'get_custom_posts_callback');
add_action('wp_ajax_nopriv_get_custom_posts', 'get_custom_posts_callback');

function get_custom_posts_callback()
{
    global $wpdb;


    // Récupérer un array d'IDs de posts publiés (exemple : les 6 derniers)
    // Tu peux adapter la requête SQL comme tu veux
    $results = $wpdb->get_col("
        SELECT post_id 
        FROM {$wpdb->postmeta} 
        WHERE meta_key LIKE '_from_cf7_form'
    ");


    $lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';
    // Récupère le texte depuis le plugin, si vide → ne rien afficher
    $badge_text = get_option($lang === 'en' ? 'impactdev_cf7_badge_en' : 'impactdev_cf7_badge_fr', '');
    if (empty($badge_text)) {
        $badge_text = '';
    }

    // Préparer la réponse
    $response = [
        'badge' => $badge_text,
        'ids'  => $results
    ];

    // Retourner en JSON
    wp_send_json($response); // encode en JSON et arrête le script
}

/*Desactivate comment du core */
add_filter('comments_open', '__return_false', 999);
add_action('pre_comment_on_post', function () {
    wp_die('Commentaires désactivés');
});


/*Afficher un message pour validation demande*/
add_action('f12_cf7_doubleoptin_sent', function ($optin) {
    // Récupérer l'ID du formulaire
    $form_id = $optin->id();
    $lang = pll_current_language();
    add_action('wp_footer', function () use ($form_id, $lang) {
    ?>
        <script>
            let formId = <?php echo (int)$form_id; ?>;
            let lang = "<?php echo esc_js($lang); ?>";
            let div = document.querySelector(`.wpcf7[data-wpcf7-id="${formId}"] .wpcf7-response-output`);

            if (div) {
                div.style.setProperty('display', 'block', 'important');
                // Message en fonction de la langue
                if (lang === "fr") {
                    div.innerHTML += "Merci ! Vérifiez votre email pour valider votre demande.";
                } else if (lang === "en") {
                    div.innerHTML += "Thank you! Please check your email to confirm your request.";
                }
            }
        </script>
<?php
    });
});


function redirect_logged_in_users_to_home()
{
    if (is_user_logged_in()) {

        // Liste des slugs à bloquer
        $restricted_pages = array(
            'connexion',
            'reinitialiser-mot-de-passe',
            'login',
            'reset-password',
            'register',
            'inscription'
        );

        // Vérifie si on est sur une des pages
        if (is_page($restricted_pages)) {
            wp_redirect(home_url());
            exit;
        }
    }
}
add_action('template_redirect', 'redirect_logged_in_users_to_home');

/** Recupere la listes des institutions */
function getInstitutionsListForProfile(): array
{
    global $wpdb;
    $table = $wpdb->prefix . 'nada_institutions';
    $rows = $wpdb->get_results("
        SELECT id, label_fr, label_en
        FROM {$table}
        WHERE is_active = 1 AND state ='approved'
        ORDER BY id ASC
    ", ARRAY_A);

    return $rows ?: [];
}

//============================ Validation mail  ==============================//

define('DOUBLEOPTIN_FORM_IDS', [16349, 16564, 3538, 14702]);

/**
 * 1. Bloquer ajout contact Flamingo sauf si _force_confirmed
 */
add_filter('flamingo_add_contact', function ($args) {

    if (!is_array($args)) return $args;

    // laisser passer si forcé après confirmation
    if (!empty($args['_force_confirmed'])) {
        unset($args['_force_confirmed']);
        return $args;
    }

    // Vérifier directement le form_id depuis $_POST
    // (le post flamingo_inbound n'existe pas encore à ce stade)
    $form_id = (int) ($_POST['_wpcf7'] ?? 0);

    if (!$form_id || !in_array($form_id, DOUBLEOPTIN_FORM_IDS)) {
        return $args; // formulaire non concerné -> laisser passer
    }

    // formulaire concerné -> bloquer, attendre la confirmation
    return false;
}, 10, 1);

/**
 * 2. À la soumission : forcer le statut pending sur le message entrant
 */
add_filter('flamingo_add_inbound', function ($args) {

    if (!is_array($args)) return $args;

    $form_id = $_POST['_wpcf7'] ?? null;

    if (!$form_id || !in_array((int)$form_id, DOUBLEOPTIN_FORM_IDS)) {
        return $args;
    }

    // marquer uniquement les bons formulaires
    $args['meta']['_optin_status'] = 'pending';

    return $args;
}, 10, 1);

add_action('wp_insert_post', function ($post_id, $post) {

    if ($post->post_type !== 'flamingo_inbound') return;
    $form_id = $_POST['_wpcf7'] ?? null;
    if (!$form_id) return;
    if (!in_array($form_id, DOUBLEOPTIN_FORM_IDS)) return;
    update_post_meta($post_id, '_optin_status', 'pending');
}, 10, 2);

/**
 * 3. Cacher les messages entrants non confirmés (flamingo_inbound)
 */
add_action('pre_get_posts', function ($query) {
    if (!is_admin()) return;


    $post_type = $query->get('post_type');

    $types_a_filtrer = ['flamingo_inbound', 'flamingo_contact'];
    if (!in_array($post_type, $types_a_filtrer)) return;

    $query->set('meta_query', [
        'relation' => 'OR',
        [
            'key'     => '_optin_status',
            'compare' => 'NOT EXISTS',
        ],
        [
            'key'     => '_optin_status',
            'value'   => 'confirmed',
            'compare' => '=',
        ],
    ]);
}, 10, 1);

// 4. Stocker le hash au moment de la soumission CF7
add_action('wpcf7_mail_sent', function ($cf7) {
    // On récupère le hash depuis la table via l'email
    global $wpdb;
    $table = $wpdb->prefix . 'f12_cf7_doubleoptin';

    $submission = WPCF7_Submission::get_instance();
    if (!$submission) return;

    $data = $submission->get_posted_data();
    if (!$data) return;

    $email = '';
    if (!empty($data['email'])) {
        $email = sanitize_email($data['email']);
    } elseif (!empty($data['your-email'])) {
        $email = sanitize_email($data['your-email']);
    }

    if (!$email) return;

    // Récupérer le hash le plus récent pour cet email
    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table} WHERE email = %s ORDER BY id DESC LIMIT 1",
            $email
        )
    );

    if (!$row) return;

    // Récupérer le post Flamingo correspondant
    $posts = get_posts([
        'post_type'      => 'flamingo_inbound',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => [[
            'key'   => '_from_email',
            'value' => $email,
        ]],
    ]);

    if (!empty($posts)) {
        // Stocker le hash dans le post meta Flamingo
        update_post_meta($posts[0]->ID, '_optin_hash', $row->hash);
    }
}, 10, 1);


// 5. À la confirmation — retrouver le bon post via le hash
add_action('f12_cf7_doubleoptin_after_confirm', function ($hash) {
    // Trouver le post Flamingo qui a CE hash exact
    $posts = get_posts([
        'post_type'      => 'flamingo_inbound',
        'posts_per_page' => 1,
        'meta_query'     => [[
            'key'   => '_optin_hash',
            'value' => sanitize_text_field($hash),
        ]],
    ]);

    if (empty($posts)) return;

    $post_id = $posts[0]->ID;

    // Confirmer uniquement CE post
    update_post_meta($post_id, '_optin_status', 'confirmed');

    $meta = get_post_meta($post_id, '_meta', true);
    if (!is_array($meta)) $meta = [];
    $meta['_optin_status'] = 'confirmed';
    update_post_meta($post_id, '_meta', $meta);

    // Créer le contact dans le carnet d'adresse Flamingo
    $email = get_post_meta($post_id, '_from_email', true);
    $name  = get_post_meta($post_id, '_from_name', true);

    if ($email) {
        Flamingo_Contact::add([
            'email'             => $email,
            'name'              => $name ?: '',
            '_force_confirmed'  => true, //contourne ton filtre flamingo_add_contact
        ]);
    }
}, 10, 2);

// inscription NL 
//  Vérification des doublons

function email_exists_newsletter($email)
{
    $posts = get_posts([
        'post_type'      => 'flamingo_inbound',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'     => '_field_your-email',
                'value'   => $email,
                'compare' => '=',
            ],
            [
                'key'     => '_fields',
                'value'   => 'newsletter-email',
                'compare' => 'LIKE',
            ],
            [
                'key'   => '_optin_status',
                'value' => 'confirmed',
            ],
        ],
    ]);

    return !empty($posts);
}

add_filter('wpcf7_validate_email*', function ($result, $tag) {

    $form_id = $_POST['_wpcf7'] ?? 0;

    // UNIQUEMENT newsletter
    if (!in_array($form_id, [16349, 16564])) return $result;

    // champ newsletter uniquement
    if (!in_array($tag->name, ['your-email'])) return $result;

    $email = sanitize_email($_POST[$tag->name] ?? '');
    if (!$email) return $result;

    if (email_exists_newsletter($email)) {
        $result->invalidate($tag, "");
    }

    return $result;
}, 10, 2);

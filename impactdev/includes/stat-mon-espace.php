<?php
// Shortcode : Nombre actualités publiées par language
function shortcode_nombre_articles_langue( $atts ) {
    // Vérifie que Polylang est actif
    if ( ! function_exists( 'pll_current_language' ) && ! function_exists( 'pll_get_post_language' ) ) {
        // Si Polylang n'est pas disponible, retourne le total global
        $count_all = wp_count_posts('post')->publish;
        return $count_all;
    }

    // Récupère le code de langue courant (ex: 'fr', 'en', ...)
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : '';

    // Si la langue courante n'est pas trouvée, fallback vers le total global
    if ( empty( $current_lang ) ) {
        $count_all = wp_count_posts('post')->publish;
        return $count_all;
    }

    // Query pour compter les posts publiés dans la langue courante
    $query = new WP_Query( array(
        'post_type'   => 'post',
        'post_status' => 'publish',
        'lang'        => $current_lang,
        'fields'      => 'ids',   // on récupère seulement les ID, plus léger
        'nopaging'    => true,    // on veut tous les posts
    ) );

    $count = (int) $query->found_posts;

    return $count;
}
add_shortcode( 'nombre_articles_publie', 'shortcode_nombre_articles_langue' );

// Shortcode : Nombre actualités archivées
// Marquer un article quand il devient "publié"
function mark_post_as_ever_published( $new_status, $old_status, $post ) {
    if ( $post->post_type !== 'post' ) {
        return;
    }

    // Quand un article passe à "publish"
    if ( $new_status === 'publish' ) {
        // On ajoute un meta indiquant qu'il a déjà été publié
        update_post_meta( $post->ID, 'statut_published', 1 );
    }
}
add_action( 'transition_post_status', 'mark_post_as_ever_published', 10, 3 );

// Shortcode : [nombre_actualites_archivees]
function shortcode_nombre_actualites_archivees() {

    // Langue courante Polylang
    $lang = function_exists('pll_current_language') ? pll_current_language() : '';

    // Query pour les articles déjà publiés puis dépubliés
    $query = new WP_Query( array(
        'post_type'      => 'post',
        'post_status'    => array('draft', 'pending'),
        'meta_key'       => 'statut_published',
        'meta_value'     => 1,
        'fields'         => 'ids',
        'nopaging'       => true,
        'lang'           => $lang,
    ) );

    return (int) $query->found_posts;
}
add_shortcode('nombre_actualites_archivees', 'shortcode_nombre_actualites_archivees');

//Shortcode : Actualités en attente proposés par contributeur
function shortcode_nombre_articles_en_attente_de_publication_contributor( $atts ) {
    // Vérifie que Polylang est actif
    if ( ! function_exists( 'pll_current_language' ) && ! function_exists( 'pll_get_post_language' ) ) {
        // Si Polylang n'est pas disponible, retourne le total global
        $count_all = wp_count_posts('post')->publish;
        return $count_all;
    }

    // Récupère le code de langue courant 
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : '';

    // Si la langue courante n'est pas trouvée, fallback vers le total global
    if ( empty( $current_lang ) ) {
        $count_all = wp_count_posts('post')->pending;
        return $count_all;
    }

    // Query pour compter les posts publiés dans la langue courante
    $query = new WP_Query( array(
            'post_type'   => 'post',
            'post_status' => 'pending',
            'lang'        => $current_lang,
            'fields'      => 'ids',
            'nopaging'    => true,
            'meta_query'  => array(
                array(
                    'key'     => '_cf7_auteur',
                    'compare' => 'EXISTS', // le champ existe => c'est CF7
                ),
            ),
        ) );


    $count = (int) $query->found_posts;

    return $count;
}
add_shortcode( 'nombre_articles_en_attente_contributor', 'shortcode_nombre_articles_en_attente_de_publication_contributor' );

//Actualités en attente proposés par l'administrateur

function shortcode_nombre_articles_en_attente_de_publication_admin( $atts ) {
    // Vérifie que Polylang est actif
    if ( ! function_exists( 'pll_current_language' ) && ! function_exists( 'pll_get_post_language' ) ) {
        // Si Polylang n'est pas disponible, retourne le total global
        $count_all = wp_count_posts('post')->publish;
        return $count_all;
    }

    // Récupère le code de langue courant 
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : '';

    // Si la langue courante n'est pas trouvée, fallback vers le total global
    if ( empty( $current_lang ) ) {
        $count_all = wp_count_posts('post')->pending;
        return $count_all;
    }

    // Query pour compter les posts publiés dans la langue courante
    $query = new WP_Query( array(
        'post_type'   => 'post',
        'post_status' => 'pending',
        'lang'        => $current_lang,
        'fields'      => 'ids',
        'nopaging'    => true,
        'meta_query'  => array(
            array(
                'key'     => '_cf7_auteur',
                'compare' => 'NOT EXISTS', // le champ n'existe pas => c'est admin
            ),
        ),
    ) );


    $count = (int) $query->found_posts;

    return $count;
}
add_shortcode( 'nombre_articles_en_attente_admin', 'shortcode_nombre_articles_en_attente_de_publication_admin' );


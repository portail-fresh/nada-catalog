<?php
add_action('init', function() {
    // Ajouter un nouveau feed accessible via ?feed=fullrss
    add_feed('fresh', 'custom_full_rss_feed_multilang');
});

function custom_full_rss_feed_multilang() {
    header('Content-Type: application/rss+xml; charset='.get_option('blog_charset'));
    // Déterminer la langue depuis l'URL (?lang=fr ou ?lang=en), défaut = fr
    $lang = isset($_GET['lang']) ? sanitize_text_field($_GET['lang']) : 'fr';
    // Récupérer tous les types de contenu publics
    $post_types = get_post_types(['public' => true], 'names');
    $posts = get_posts([
    'post_type'      => $post_types,
    'numberposts'    => 50,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'lang'           => $lang // Polylang filter
     ]);
    

    echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xml:lang="<?php echo esc_attr($lang); ?>"
>
    <channel>
        <title><?php bloginfo_rss('name'); ?> (<?php echo strtoupper($lang); ?>)</title>
        <link><?php bloginfo_rss('url'); ?></link>
        <description><?php bloginfo_rss('description'); ?></description>
        <language><?php echo $lang; ?></language>
        <lastBuildDate><?php echo mysql2date('r', get_lastpostmodified('GMT')); ?></lastBuildDate>

        <?php global $post;
        foreach($posts as $post) : setup_postdata($post); ?>
        <item>
            <title><?php the_title_rss(); ?></title>
            <link><?php the_permalink_rss(); ?></link>
            <guid isPermaLink="true"><?php the_permalink_rss(); ?></guid>
            <pubDate><?php echo mysql2date('r', $post->post_date_gmt); ?></pubDate>
            <dc:creator><?php the_author(); ?></dc:creator>
            <category><![CDATA[<?php echo implode(', ', wp_get_post_categories($post->ID, ['fields'=>'names'])); ?>]]></category>
            <description><![CDATA[ <?php echo esc_html(get_the_excerpt()); ?> ]]></description>
            <content:encoded><![CDATA[ <?php echo esc_html(wp_strip_all_tags(get_the_content())); ?> ]]></content:encoded>
	        <?php if (has_post_thumbnail($post->ID)) : ?>
                <enclosure url="<?php echo get_the_post_thumbnail_url($post->ID, 'full'); ?>" type="image/jpeg" />
            <?php endif; ?>
        </item>
        <?php endforeach; wp_reset_postdata(); ?>
    </channel>
    </rss>
    <?php
}

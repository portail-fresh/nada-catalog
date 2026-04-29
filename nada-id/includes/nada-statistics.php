<?php

/**
 * Fichier: includes/nada-statistics.php
 * Récupérer les indicateurs du tableau de bord
 */



/**
 * Récupère toutes les statistiques du tableau de bord
 */

function nada_get_statistics()
{
    global $wpdb;
    try {
        $catalog_views = (int) get_option('nada_catalog_views_count', 0);
        $lang = pll_current_language() ?? 'fr';
        static $data = null;
        if ($data === null) {
            try {
                $service = new Nada_Statistics_Service(new Nada_Api());
                $data = $service->get_statistics_catalog_dashboard($lang);
            } catch (Exception $e) {
                return esc_html($atts['default']);
            }
        }

        $response = $data['data']['response'] ?? [];

        $stats = [
            'total_published' => $response['total_published'] ?? 0,
            'studies_pending' => $response['total_pending'] ?? 0,
            'studies_changes_requested' => $response['total_changesRequested'] ?? 0,
            'studies_observational' => $response['total_observational'] ?? 0,
            'studies_interventional' => $response['total_intervational'] ?? 0,
            'new_studies' => $response['total_publishedStudiesdPeriod'] ?? 0,
            'catalog_views' => $catalog_views,
        ];

        return $stats;
    } catch (Exception $e) {
        error_log('Erreur stats NADA: ' . $e->getMessage());
        return [
            'total_published' => 0,
            'studies_pending' => 0,
            'studies_changes_requested' => 0,
            'studies_observational' => 0,
            'studies_interventional' => 0,
            'new_studies' => 0,
            'catalog_views' => 0,
        ];
    }
}


/**
 * Incrémenter le compteur de vues du catalogue
 * 
 * @param bool $unique_per_session Ne compter qu'une fois par session
 */
function nada_track_catalog_view($unique_per_session = true)
{
    // Vérifier si déjà compté dans cette session
    if ($unique_per_session && isset($_SESSION['nada_catalog_viewed'])) {
        return;
    }

    // Incrémenter le compteur
    $current_count = get_option('nada_catalog_views_count', 0);
    update_option('nada_catalog_views_count', $current_count + 1);

    // Marquer comme vu dans la session
    if ($unique_per_session) {
        if (!session_id()) {
            session_start();
        }
        $_SESSION['nada_catalog_viewed'] = true;
    }
}

/**
 * Hook pour tracker automatiquement les visites
 */
add_action('wp', function () {
    if (is_page('catalogue') || is_page('catalog')) {
        nada_track_catalog_view(true);
    }
});

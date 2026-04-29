<?php

// Sécurité : empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Trait Table_Helpers_Trait
 * * Ce trait regroupe toutes les méthodes utilitaires communes aux datatables.
 * Il peut être injecté dans n'importe quelle classe héritant de WP_List_Table.
 */
trait TableHelpersTrait
{
    /**
     * Récupère le nombre d'éléments à afficher par page.
     * * @return int
     */
    protected function getPerPageCount(): int
    {
        $per_page_param = $_REQUEST['per_page'] ?? '10';

        if ($per_page_param === 'all') {
            return 999999;
        }

        return max(1, min(100, absint($per_page_param)));
    }

    /**
     * Récupère le terme de recherche courant sécurisé.
     * Nécessite que la propriété $this->search_key soit définie dans la classe.
     * * @return string
     */
    protected function getSearchTerm(): string
    {
        if (isset($this->searchKey) && !empty($_REQUEST[$this->searchKey])) {
            return sanitize_text_field(wp_unslash($_REQUEST[$this->searchKey]));
        }
        if (isset($_REQUEST['search_term'])) {
            return sanitize_text_field(wp_unslash($_REQUEST['search_term']));
        }

        return '';
    }

    /**
     * Surcharge la pagination par défaut de WP_List_Table.
     * Offre un design personnalisé et gère les traductions (FR/EN).
     * * @param string $which Emplacement de la pagination ('top' ou 'bottom')
     */
    protected function pagination($which)
    {
        if (empty($this->_pagination_args)) {
            return;
        }

        $total_items = $this->_pagination_args['total_items'];
        $total_pages = $this->_pagination_args['total_pages'];
        $current = $this->get_pagenum();
        $per_page = $this->_pagination_args['per_page'];

        // Fallback sécurisé pour la langue si elle n'est pas définie dans la classe
        $lang = $this->lang ?? 'fr';

        // Calculer les éléments affichés
        $start_item = (($current - 1) * $per_page) + 1;
        $end_item = min($current * $per_page, $total_items);

        // Dictionnaire de traduction
        $translations = [
            'fr' => [
                'start' => 'Début',
                'previous' => 'Précédent',
                'next' => 'Suivant',
                'end' => 'Fin',
                'showing' => fn($start, $end, $total) => "Affichage de {$start} à {$end} sur {$total} éléments"
            ],
            'en' => [
                'start' => 'Start',
                'previous' => 'Previous',
                'next' => 'Next',
                'end' => 'End',
                'showing' => fn($start, $end, $total) => "Showing {$start} to {$end} of {$total} elements"
            ]
        ];

        $t = $translations[$lang] ?? $translations['fr'];

        // Construction du HTML de pagination
        echo '<div class="tablenav-pages">';

        // 1. Le compteur
        printf(
            '<span class="displaying-num">%s</span>',
            $t['showing']($start_item, $end_item, number_format_i18n($total_items))
        );

        // 2. Les boutons de navigation
        echo '<div class="pagination-links" style="display: inline-flex; gap: 5px; align-items: center;">';

        // Bouton Start
        $this->renderPageLink($current > 1, remove_query_arg('paged'), $t['start']);

        // Bouton Previous
        $this->renderPageLink($current > 1, add_query_arg('paged', max(1, $current - 1)), $t['previous']);

        // Logique des numéros de page
        $range = 2;
        $start = max(1, $current - $range);
        $end = min($total_pages, $current + $range);

        if ($start > 1) {
            $this->renderPageLink(true, remove_query_arg('paged'), '1');
            if ($start > 2) {
                echo '<span>...</span>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i == $current) {
                printf('<span class="button" style="background: #0073aa; color: white; border-color: #0073aa;">%d</span>', $i);
            } else {
                $page_url = ($i == 1) ? remove_query_arg('paged') : add_query_arg('paged', $i);
                $this->renderPageLink(true, $page_url, (string)$i);
            }
        }

        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                echo '<span>...</span>';
            }
            $this->renderPageLink(true, add_query_arg('paged', $total_pages), (string)$total_pages);
        }

        // Bouton Next
        $this->renderPageLink($current < $total_pages, add_query_arg('paged', $current + 1), $t['next']);

        // Bouton End
        $this->renderPageLink($current < $total_pages, add_query_arg('paged', $total_pages), $t['end']);

        echo '</div></div>';
    }

    /**
     * Sous-méthode privée pour générer un bouton de pagination (DRY pattern)
     */
    private function renderPageLink(bool $is_active, string $url, string $label)
    {
        if ($is_active) {
            printf('<a class="button" href="%s">%s</a>', esc_url($url), esc_html($label));
        } else {
            printf('<span class="button disabled" style="opacity: 0.5;">%s</span>', esc_html($label));
        }
    }
}

<?php

/**
 * Template part: Facets (English)
 * Chargé dynamiquement via AJAX (action: nada_load_facets)
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

?>

<form id="nada-filter-form">
    <div class="accordion" id="nadaAccordion">

        <?php
        $index = 1;
        $hasYearFilter = false;

        if (!empty($data_ID['en']['values'])) {

            foreach ($data_ID['en']['values'] as $filter_key) {
                $name = $filter_key['name'];

                // Si c’est le couple date début / fin → bloc Année
                if (($name === 'CollectionStart') && !$hasYearFilter) {
                    $hasYearFilter = true;
        ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?php echo $index; ?>">
                            <button class="accordion-button collapsed d-flex justify-content-between align-items-center"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-<?php echo $index; ?>"
                                aria-expanded="false" aria-controls="collapse-<?php echo $index; ?>">
                                Start and end years of the collection
                                <i class="bi bi-chevron-down ms-2 collapse-icon"></i>
                            </button>
                        </h2>
                        <div id="collapse-<?php echo $index; ?>" class="accordion-collapse collapse"
                            aria-labelledby="heading-<?php echo $index; ?>" data-bs-parent="#nadaAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="from">From</label>
                                        <select id="from" name="from">
                                            <option value=""></option>
                                            <?php
                                            // Tableau pour stocker les années déjà affichées
                                            $years = [];

                                            // Générer les années uniques depuis les valeurs de date_debut
                                            foreach ($filter_key['values'] as $item) {
                                                // Extraire uniquement les 4 premiers caractères (l'année)
                                                $year = substr($item['title'], 0, 4);

                                                // Vérifier que c’est bien une année à 4 chiffres et éviter les doublons
                                                if (preg_match('/^\d{4}$/', $year) && !in_array($year, $years)) {
                                                    $years[] = $year;
                                                }
                                            }

                                            // Trier les années du plus petit au plus grand
                                            sort($years);

                                            // Générer les options
                                            foreach ($years as $year) {
                                                echo '<option value="' . esc_attr($year) . '">' . esc_html($year) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="to">To</label>
                                        <select id="to" name="to">
                                            <option value=""></option>
                                            <?php
                                            // Tableau pour stocker les années déjà ajoutées
                                            $years = [];

                                            // Trouver le filtre date_fin s’il existe
                                            foreach ($data_ID['en']['values'] as $f2) {
                                                if ($f2['name'] === 'CollectionEnd') {
                                                    foreach ($f2['values'] as $item) {
                                                        // Extraire uniquement l'année (les 4 premiers caractères)
                                                        $year = substr($item['title'], 0, 4);

                                                        // Vérifier que c’est bien une année et éviter les doublons
                                                        if (preg_match('/^\d{4}$/', $year) && !in_array($year, $years)) {
                                                            $years[] = $year;
                                                        }
                                                    }
                                                }
                                            }

                                            // Trier les années du plus petit au plus grand
                                            sort($years);

                                            // Générer les options
                                            foreach ($years as $year) {
                                                echo '<option value="' . esc_attr($year) . '">' . esc_html($year) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    $index++;
                    continue; // ne pas réafficher ces filtres ensuite
                }

                // Autres filtres dynamiques normaux
                if ($name === 'CollectionEnd') {
                    continue; // déjà géré
                }

                $filter_label = __($filter_key['name'], 'nada-id');
                $collapse_id = 'collapse-' . $index;
                $heading_id = 'heading-' . $index;
                ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="<?php echo esc_attr($heading_id); ?>">
                        <button class="accordion-button collapsed d-flex justify-content-between align-items-center"
                            type="button" data-bs-toggle="collapse"
                            data-bs-target="#<?php echo esc_attr($collapse_id); ?>"
                            aria-expanded="false" aria-controls="<?php echo esc_attr($collapse_id); ?>">
                            <?php echo esc_html($filter_label); ?>
                            <i class="bi bi-chevron-down ms-2 collapse-icon"></i>
                        </button>
                    </h2>
                    <div id="<?php echo esc_attr($collapse_id); ?>" class="accordion-collapse collapse"
                        aria-labelledby="<?php echo esc_attr($heading_id); ?>" data-bs-parent="#nadaAccordion">
                        <div class="accordion-body">
                            <?php
                            foreach ($filter_key['values'] as $item) {
                                echo '<label>';
                                echo '<input type="checkbox" name="' . esc_attr($filter_key['name']) . '[]" value="' . esc_attr($item['id']) . '"> ';
                                echo esc_html($item['title']) . ' (' . intval($item['found']) . ')';
                                echo '</label><br>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
        <?php
                $index++;
            }
        }
        ?>
    </div>
</form>



<script>
    jQuery(document).ready(function($) {
        $("#facet-en #nada-filter-form").on("change", "input, select", function() {
            const params = getSearchFormParams();
            const filtreData = $("#facet-en #nada-filter-form").serializeArray();
            const queryString = buildQueryStringFromFormArray(filtreData);
            chargerCatalogues(
                queryString,
                params.search,
                params.sortBy,
                params.sortOrder,
                params.limit,
                params.lang
            );
        });
    })
</script>
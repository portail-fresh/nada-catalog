<?php

/**
 * Template part: Facets (MULTI-LANGUES)
 * Chargé dynamiquement via AJAX (action: nada_load_facets)
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<form id="nada-filter-form">
    <div class="accordion" id="nadaAccordion">
        <?php
        $index = 1;
        $hasYearFilter = false;

        foreach ($data_facets['values'] as $filter_key) {
            $name = $filter_key['name'];

            // Pré-calculer les IDs (évite de répéter esc_attr())
            $accordion_id = 'accordion-' . $name;
            $collapse_id = 'collapse-' . $name;
            $heading_id = 'heading-' . $name;
            
            // Ignorer CollectionEnd (déjà géré)
            if ($name === 'CollectionEnd' || $name === 'CollectionStart') {
                continue;
            }

            // ===== FILTRES NORMAUX =====
            $filter_label = __($name, 'nada-id');
            $values = $filter_key['values'];
            $total = count($values);

            // Trier par 'found' UNE SEULE FOIS
            usort($values, function ($a, $b) {
                return $b['found'] <=> $a['found'];
            });

            $show_search = ($name !== 'FinalSampleSize');
        ?>
            <div class="accordion-item" id="<?= $accordion_id ?>">
                <h2 class="accordion-header" id="<?= $heading_id ?>">
                    <button class="accordion-button collapsed d-flex justify-content-between align-items-center"
                        type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapse_id ?>"
                        aria-expanded="false" aria-controls="<?= $collapse_id ?>">
                        <span class="btn-text-collape">
                            <?= esc_html($filter_label) ?>
                            <i class="bi bi-chevron-down ms-2 collapse-icon"></i>
                        </span>
                    </button>
                </h2>
                <div id="<?= $collapse_id ?>" class="accordion-collapse collapse"
                    aria-labelledby="<?= $heading_id ?>" data-bs-parent="#nadaAccordion">
                    <div class="accordion-body">
                        <?php if ($show_search): ?>
                            <div class="facetteSearch">
                                <input type="search" name="facet-input-auto" class="facette-input" placeholder="<?= ($lang == 'fr') ? 'Rechercher...' : 'Search...' ?>">
                            </div>
                        <?php endif; ?>

                        <div class="facette-list">
                            <?php
                            // Afficher les 10 premiers dans l'accordéon                            
                            foreach ($values as $idx => $item):
                                $hiddenClass = ($idx >= 10) ? 'd-none remaining-item' : '';
                                $uniqueId = 'facet-' . $name . '-' . $item['id'];
                            ?>
                                <div class="facette-item <?= $hiddenClass ?>">
                                    <label>
                                        <input type="checkbox"
                                            id="<?= $uniqueId ?>-accordion"
                                            class="facet-input facet-sync"
                                            data-facet="<?= $name ?>"
                                            data-option="<?= esc_attr($item['title']) ?>"
                                            data-sync-id="<?= $uniqueId ?>"
                                            name="<?= $name ?>[]"
                                            value="<?= esc_attr($item['id']) ?>">
                                        <span class="facetteTitle"><?= esc_html($item['title']) ?></span>
                                        <span class="facetteFound"><?= intval($item['found']) ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($total > 10): ?>
                            <?php
                            // Définir les valeurs selon la langue
                            $btnLangClass = $lang === 'fr' ? 'btnToggleFr' : 'btnToggleEn';
                            $btnText = $lang === 'fr' ? 'Afficher plus' : 'Show more';
                            $modalTarget = $lang === 'fr' ? "#facetteModalFr-$name" : "#facetteModalEn-$name";
                            ?>
                            <div class="facette-toggle <?= $btnLangClass ?>"
                                attr-value="<?= $name ?>"
                                attr-lang="<?= $lang ?>"
                                data-bs-toggle="modal"
                                data-bs-target="<?= $modalTarget ?>"
                                attr-label="<?= esc_attr($filter_label) ?>">
                                <?= $btnText ?>
                            </div>      

                            <?php
                                $modalID = $lang === 'fr' ? "facetteModalFr-$name" : "facetteModalEn-$name";
                            ?>

                            <div class="modal facetteModal" id="<?= $modalID ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><?= esc_html($filter_label) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= $lang === 'fr' ? 'Fermer' : 'Close' ?>"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="facetteSearch mb-3">
                                                <input type="search" class="facette-input-modal form-control" placeholder="<?= $lang === 'fr' ? 'Rechercher...' : 'Search...' ?>">
                                            </div>

                                            <div class="form-check mb-2 selectAll">
                                                <input class="form-check-input" type="checkbox" id="selectAllOptions<?= $lang . '-' . $name ?>">
                                                <label class="form-check-label selectAllLabel" for="selectAllOptions<?= $lang . '-' . $name ?>">
                                                    <?= $lang === 'fr' ? 'Tout sélectionner' : 'Select all' ?> 
                                                </label>
                                            </div>

                                            <div class="facette-all-list">
                                                <?php foreach ($values as $item):
                                                    $uniqueId = 'facet-' . $name . '-' . $item['id'];
                                                ?>
                                                    <div class="facette-item">
                                                        <label>
                                                            <input type="checkbox"
                                                                id="<?= $uniqueId ?>-modal"
                                                                class="facet-input facet-sync"
                                                                data-facet="<?= $name ?>"
                                                                data-option="<?= esc_attr($item['title']) ?>"
                                                                data-sync-id="<?= $uniqueId ?>"
                                                                name="<?= $name ?>[]"
                                                                value="<?= esc_attr($item['id']) ?>">
                                                            <span class="facetteTitle"><?= esc_html($item['title']) ?></span>
                                                            <span class="facetteFound"><?= intval($item['found']) ?></span>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                            <button type="button" class="btn btn-primary btn-validate">
                                                <?= $lang === 'fr' ? 'Valider' : 'Validate' ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
            $index++;
        }
        ?>
    </div>
</form>
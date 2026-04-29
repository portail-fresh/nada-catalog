<?php

if (!defined('ABSPATH')) {
    exit;
}

$current_language = function_exists('pll_current_language') ? pll_current_language() : 'fr';
?>

<div class="nada-id-container-x">
    <div class="row">
        <div class="col-md-12">
            <!-- Lang switcher -->
            <div class="d-flex justify-content-end mb-3" id="lang-switcher">
                <div class="btn-group" role="group" aria-label="Language selection">
                    <input type="radio" class="btn-check" name="language-radio" id="lang-fr" value="fr" <?php checked($current_language, 'fr'); ?>>
                    <label class="btn btn-outline-primary" for="lang-fr">FR</label>

                    <input type="radio" class="btn-check" name="language-radio" id="lang-en" value="en" <?php checked($current_language, 'en'); ?>>
                    <label class="btn btn-outline-primary" for="lang-en">EN</label>
                </div>
            </div>

            <!-- Contribuer link -->
            <div class="d-flex justify-content-center mb-3">
                <a href="<?php echo ($current_language === 'fr') ? '/alimentation-du-catalogue' : '/en/contribute/'; ?>"
                    class="link-with-icon link-add">
                    <i class="fa fa-database"></i>
                    <?php echo ($current_language === 'fr') ? 'Contribuer au catalogue' : 'Contribute to the catalogue'; ?>
                </a>
            </div>

            <!-- UNIFIED SEARCH CARD -->
            <div class="nada-unified-search-card mb-4">
                <div class="search-card-body" id="simple-search-section">
                    <form id="nada-search-form" onsubmit="return false;">
                        <label for="nada-search-input" class="form-label">
                            <i class="fa fa-search"></i>
                            <?php echo ($current_language === 'fr') ? 'Recherche libre' : 'Free research'; ?>
                        </label>
                        <div class="nada-autocomplete-wrap">
                            <input
                                id="nada-search-input"
                                name="search"
                                type="text"
                                class="form-control"
                                placeholder="<?php echo ($current_language === 'fr') ? 'Mots-clés, auteur, thème...' : 'Keywords, author, theme...'; ?>"
                                autocomplete="off"
                                role="combobox"
                                aria-autocomplete="list"
                                aria-expanded="false"
                                aria-controls="nada-autocomplete-list"
                                data-autocomplete-url="<?php echo esc_url(get_nada_server_url() . '/api/catalog/autocomplete'); ?>" />
                            <div id="nada-autocomplete-list" class="nada-autocomplete-list" role="listbox"></div>
                        </div>
                    </form>
                </div>

                <div id="footer-slot"></div>

                <div class="search-card-footer d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" id="toggle-search-mode" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-sliders"></i>
                            <span class="toggle-text"><?php echo ($current_language === 'fr') ? 'Recherche Avancée' : 'Advanced Search'; ?></span>
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btn-reset-all">
                            <i class="fa fa-times"></i>
                            <?php echo ($current_language === 'fr') ? 'Réinitialiser' : 'Reset'; ?>
                        </button>
                        <button id="nada-search-button" type="button" class="btn btn-primary btn-sm">
                            <i class="fa fa-search"></i>
                            <?php echo ($current_language === 'fr') ? 'Rechercher' : 'Search'; ?>
                        </button>
                    </div>
                </div>

                <div class="search-card-header">
                    <div class="row align-items-end">
                        <!-- Sort By -->
                        <div class="col-lg-5 col-md-6 col-12">
                            <label for="nada-display-sort" class="form-label">
                                <i class="fa fa-sort"></i>
                                <?php echo ($current_language === 'fr') ? 'Trier par' : 'Sort By'; ?>
                            </label>
                            <select id="nada-display-sort" name="sortBy" class="form-select form-select-sm">
                                <option value="changed-asc" selected><?php echo ($current_language === 'fr') ? "Date de dernière mise à jour : croissant" : "Date of last update : ascending"; ?></option>
                                <option value="changed-asc"><?php echo ($current_language === 'fr') ? "Date de dernière mise à jour : croissant" : "Date of last update : ascending"; ?></option>
                                <option value="changed-desc"><?php echo ($current_language === 'fr') ? "Date de dernière mise à jour : décroissant" : "Date of last update : descending"; ?></option>
                                <option value="title-asc"><?php echo ($current_language === 'fr') ? "Classer par titre : de A à Z" : "Sort by title: A to Z"; ?></option>
                                <option value="title-desc"><?php echo ($current_language === 'fr') ? "Classer par titre : de Z à A" : "Sort by title: Z to A"; ?></option>
                            </select>
                        </div>

                        <!-- Display Limit -->
                        <div class="col-lg-2 col-md-3 col-12">
                            <label for="nada-limit" class="form-label">
                                <i class="fa fa-list"></i>
                                <?php echo ($current_language === 'fr') ? 'Afficher' : 'Display'; ?>
                            </label>
                            <select id="nada-limit" name="limit" class="form-select form-select-sm">
                                <option value="10" selected>10</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <!-- Search Mode Toggle -->

                    </div>
                </div>

                <!-- Advanced Search -->
                <div class="search-card-body d-none" id="advanced-search-section">

                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <label for="adv-global-operator" class="form-label mb-0">
                                <?php echo ($current_language === 'fr') ? 'Opérateur global' : 'Global operator'; ?>:
                            </label>
                            <select id="adv-global-operator" class="form-select form-select-sm">
                                <option value="AND"><?php echo ($current_language === 'fr') ? 'ET' : 'AND'; ?></option>
                                <option value="AND"><?php echo ($current_language === 'fr') ? 'ET' : 'AND'; ?></option>
                                <option value="OR"><?php echo ($current_language === 'fr') ? 'OU' : 'OR'; ?></option>
                            </select>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-add-criteria">
                                <i class="fa fa-plus"></i>
                                <?php echo ($current_language === 'fr') ? 'Ajouter' : 'Add'; ?>
                            </button>
                        </div>
                    </div>

                    <div id="adv-criteria-container"></div>
                </div>
            </div>

            <input type="hidden" id="nada-lang" name="lang" value="<?php echo esc_attr($current_language); ?>">
        </div>

        <div class="col-md-12">
            <div class="row">
                <!-- Left column: facets -->
                <div class="col-md-3 col-sm-12 mb-4 toggle-container">
                    <div class="toggle-item d-lg-none">
                        <i class="fa fa-sliders"></i>
                        <span class="toggle-text"><?php echo ($current_language === 'fr') ? 'Filtrer' : 'Search'; ?></span>
                        <i class="arrow-down"></i>
                    </div>
                    <div class="toggle-content">
                        <div id="facet-fr" class="<?php echo $current_language === 'fr' ? '' : 'd-none'; ?>"></div>
                        <div id="facet-en" class="<?php echo $current_language === 'en' ? '' : 'd-none'; ?>"></div>
                    </div>
                </div>

                <!-- Right column: results + pagination -->
                <div class="col-md-9 col-sm-12">
                    <!-- Filter tags -->
                    <div class="filtresBox-nada d-none mb-3">
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <div id="nada-filtres" class="d-flex flex-wrap gap-2"></div>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-allfilter">
                                <i class="fa fa-times"></i>
                                <?php echo ($current_language === 'fr') ? 'Effacer filtres' : 'Clear filters'; ?>
                            </button>
                        </div>
                    </div>

                    <!-- Pagination top -->
                    <div id="nada-pagination-top" aria-live="polite"></div>

                    <!-- Results -->
                    <div id="nada-resultats" aria-live="polite">
                        <p><?php echo __('loading', 'nada-id'); ?></p>
                    </div>

                    <!-- Pagination bottom -->
                    <div id="nada-pagination-bottom" class="nada-navigation-bottom"></div>
                </div>
            </div>
        </div>
    </div>
</div>

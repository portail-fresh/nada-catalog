<?php
$current_language = pll_current_language();
// fichier : templates/liste-template.php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (!defined('ABSPATH')) exit;
?>
<style>
    /* Form */
    form#nada-search-form {
        background: var(--bg);
        padding: 14px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(40, 40, 50, 0.03);
    }

    .form-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .field {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .field label {
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 6px;
    }

    .field select,
    .field input[type="text"] {
        border-radius: 8px;
        border: 1px solid #e3dff0;
        padding: 8px 10px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e3dff0;
        padding: 8px 10px;
        background: #fff;
    }

    .field.small {
        min-width: 120px;
    }

    .field.grow {
        flex: 1 1 320px;
        min-width: 180px;
    }

    button#nada-search-button {
        background: var(--primary);
        color: white;
        border: 0;
        border-radius: 8px;
        height: 42px;
        padding: 0 14px;
        cursor: pointer;
        font-weight: 600;
    }

    button#nada-search-button:active {
        transform: translateY(1px);
    }

    .contributor_btn {
        background: #351f65;
        color: #fff;
        padding: 8px 15px;
        border-radius: 10px;
        margin: 5px;
        font-size: 15px;
        border: 1px solid #351f65;
    }

    .contributor_btn:hover,
    .contributor_btn:focus {
        background: #fff;
        color: #351f65;
    }
</style>
<div class="nada-id-container-x">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-end mb-3" id="lang-switcher">
                <div class="btn-group" role="group" aria-label="Language selection">
                    <input type="radio" class="btn-check" name="language-radio" id="lang-fr" value="fr" checked>
                    <label class="btn btn-outline-primary" for="lang-fr">FR</label>

                    <input type="radio" class="btn-check" name="language-radio" id="lang-en" value="en">
                    <label class="btn btn-outline-primary" for="lang-en">EN</label>
                </div>
            </div>
            <div class="d-flex justify-content-center mb-3">
                <a
                    href="<?php echo ($current_language === 'fr')
                                ? '/alimentation-du-catalogue'
                                : '/en/contribute/'; ?>"
                    class="link-with-icon link-add">
                    <i class="fa fa-database"></i>
                    <?php echo ($current_language === 'fr')
                        ? 'Contribuer au catalogue'
                        : 'Contribute to the catalog'; ?>
                </a>
            </div>
            <form id="nada-search-form" class="mb-4 col-md-12" method="GET">
                <div class="form-row" role="search" aria-label="Recherche études NADA">
                    <div class="field small">
                        <label for="nada-display-sort"><?php echo __('catalogSortBy', 'nada-id'); ?></label>
                        <select id="nada-display-sort" name="sortBy">
                            <option value="created-desc" selected><?php echo ($current_language === 'fr') ? "Afficher d'abord : Études les plus récentes"
                                                                        : "Show first: Most recent studies"; ?></option>
                            <option value="created-desc"><?php echo ($current_language === 'fr') ? "Afficher d'abord : Études les plus récentes"
                                                                : "Show first: Most recent studies"; ?></option>
                            <option value="created-asc"><?php echo ($current_language === 'fr') ? "Afficher d'abord : Études les plus anciennes"
                                                            : "Show first: Oldest studies"; ?></option>
                            <option value="title-asc"><?php echo ($current_language === 'fr') ? "Classer par titre : de A à Z"
                                                            : "Sort by title: A to Z"; ?></option>
                            <option value="title-desc"><?php echo ($current_language === 'fr') ? "Classer par titre : de Z à A"
                                                            : "Sort by title: Z to A"; ?></option>
                        </select>
                    </div>
                    <div class="field small">
                        <label for="nada-limit"><?php echo __('catalogDisplay', 'nada-id'); ?></label>
                        <select id="nada-limit" name="limit">
                            <option value="10" selected>10</option>
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <input type="hidden" id="nada-lang" name="lang" value="fr">

                    <div class="field grow">
                        <label for="nada-search-input"><?php echo __('catalogFreeResearch', 'nada-id'); ?></label>
                        <input id="nada-search-input" name="sk" type="text" placeholder="<?php echo esc_attr(__('catalogPlaceholder', 'nada-id')); ?>" autocomplete="off" />
                    </div>

                    <div class="field" style="align-items:flex-end;">
                        <button id="nada-search-button" type="submit">

                            <?php echo __('catalogResearch', 'nada-id'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <div class="col-md-3">
                <!-- Conteneurs pour les facettes -->
                <div id="facet-fr"></div>
                <div id="facet-en"></div>
            </div>

            <div class="col-md-9">
                <div id="nada-resultats">
                    <p><?php echo __('catalogLoading', 'nada-id'); ?></p>
                </div>
            </div>
        </div>

    </div>
</div>

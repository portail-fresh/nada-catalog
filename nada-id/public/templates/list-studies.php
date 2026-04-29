<?php
if (!defined('ABSPATH')) {
    exit;
}
$current_language = function_exists('pll_current_language') ? pll_current_language() : 'fr';

?>
<div class="nada-id-container-x">
    <div class="row">
        <div class="col-md-12 d-flex justify-content-end gap-2 btnStudy mb-4">
            <?php
            $isFr = ($current_language === 'fr');
            $urlContribution = $isFr ? '/alimentation-du-catalogue' : '/en/contribute';
            $textContribution = $isFr ? 'Contribuer au catalogue' : 'Contribute to the catalogue';
            $urlTUpload = $isFr ? '/televersement-catalogue' : '/en/catalogue-upload/';
            $textUpload = $isFr ? 'Téléversement catalogue' : 'Upload catalogue';
            ?>

            <a href="<?= $urlContribution ?>" class="link-with-icon link-add">
                <i class="fa fa-database"></i>
                <?= $textContribution ?>
            </a>

            <?php if (current_user_can('admin_fresh')) : ?>
                <a href="<?= $urlTUpload ?>" class="link-with-icon link-import">
                    <i class="fa fa-upload"></i>
                    <?= $textUpload ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="col-md-12">
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
                                data-autocomplete-url="<?php echo esc_url(get_nada_server_url() . '/api/catalog/autocomplete'); ?>"
                                data-is-admin="<?php echo user_can(wp_get_current_user(), 'admin_fresh') ? 1 : 0; ?>"
                                data-user-id="<?php echo esc_attr(get_user_meta(wp_get_current_user()->ID, 'nada_user_id', true)); ?>" />
                            <div id="myspace-autocomplete-list" class="nada-autocomplete-list" role="listbox"></div>
                        </div>
                    </form>
                </div>

                <div id="footer-slot"></div>
                <div class="search-card-header">
                    <div class="row align-items-end">
                        <div class="col-lg-5 col-md-6 col-12">
                            <label for="myspace-sortBy" class="form-label">
                                <i class="fa fa-sort"></i>
                                <?php echo ($current_language === 'fr') ? 'Trier par' : 'Sort By'; ?>
                            </label>
                            <select id="myspace-sortBy" class="form-select form-select-sm" data-live-search="false">
                                <option value="created"><?php echo __('listCreationDateKey', 'nada-id'); ?></option>
                                <option value="title"><?php echo __('listTitleKey', 'nada-id'); ?></option>
                                <option value="abbreviation"><?php echo __('listAbbreviationKey', 'nada-id'); ?></option>
                                <option value="idno"><?php echo __('listIdentifierKey', 'nada-id'); ?></option>
                                <option value="link_technical"><?php echo __('listPIKey', 'nada-id'); ?></option>
                                <option value="user_name"><?php echo __('listUserKey', 'nada-id'); ?></option>
                                <option value="modificator_name"><?php echo __('listLastModificator', 'nada-id'); ?></option>
                                <option value="changed"><?php echo __('listModificationDateKey', 'nada-id'); ?></option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3 col-12">
                            <label for="myspace-orderBy" class="form-label">
                                <i class="fa fa-sort"></i>
                                <?php echo ($current_language === 'fr') ? 'Ordre' : 'Order'; ?>
                            </label>
                            <select id="myspace-orderBy" class="form-select form-select-sm" data-live-search="false">
                                <option value="asc"><?php echo esc_attr(__('orderAsc', 'nada-id')); ?></option>
                                <option value="desc"><?php echo esc_attr(__('orderDesc', 'nada-id')); ?></option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3 col-12">
                            <label for="display-limit" class="form-label">
                                <i class="fa fa-list"></i>
                                <?php echo ($current_language === 'fr') ? 'Afficher' : 'Display'; ?>
                            </label>
                            <select id="display-limit" name="limit" class="form-select form-select-sm">
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="search-card-body d-none" id="advanced-search-section">
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <label for="adv-global-operator" class="form-label mb-0">
                                <?php echo ($current_language === 'fr') ? 'Opérateur global' : 'Global operator'; ?>:
                            </label>
                            <select id="adv-global-operator" class="form-select form-select-sm">
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
                <div class="search-card-footer d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" id="toggle-search-mode" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-sliders"></i>
                            <span class="toggle-text"><?php echo ($current_language === 'fr') ? 'Recherche Simple' : 'Simple Search'; ?></span>
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
            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="filtresBox-nada d-none mb-3">
                        <div class="d-flex flex-wrap align-items-center">
                            <div id="nada-filtres" class="d-flex flex-wrap gap-2"></div>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-allfilter">
                                <i class="fa fa-times"></i>
                                <?php echo ($current_language === 'fr') ? 'Effacer filtres' : 'Clear filters'; ?>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="my-4">
                        <div class="row cardContainerList" id="cardContainer"></div>
                        <nav class="mt-4 cardContainerPagination">
                            <div class="paginationInfo justify-content-start" id="paginationInfo"></div>
                            <ul class="pagination justify-content-end" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3">
    <div id="study-action-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body d-flex justify-content-between align-items-center" id="study-action-toast-body">
            <span class="toast-message"></span>
            <button type="button" class="btn-close ms-3" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
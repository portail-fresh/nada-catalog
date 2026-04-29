jQuery(document).ready(function ($) {
    // 1. CONFIGURATION & VARIABLES GLOBALES
    window.CONTEXT = "catalog";
    let currentLang = $("input[name='language-radio']:checked").val() || (typeof nada_global_vars !== "undefined" ? nada_global_vars.lang : "fr");

    const advFieldsCatalog = [
        {
            key: "title",
            label: {fr: "Titre", en: "Title"},
            type: "text",
            operators: [{value: "like", label: {fr: "Contient", en: "Contains"}}, {
                value: "=",
                label: {fr: "Égale à", en: "Equals"}
            }]
        },
        {
            key: "author",
            label: {fr: "Auteur", en: "Author"},
            type: "text",
            operators: [{value: "like", label: {fr: "Contient", en: "Contains"}}, {
                value: "=",
                label: {fr: "Égale à", en: "Equals"}
            }]
        },
        {
            key: "nation",
            label: {fr: "Pays concerné", en: "Country concerned"},
            type: "text",
            operators: [{value: "=", label: {fr: "Égale à", en: "Equals"}}]
        },
        {
            key: "year_start",
            label: {fr: "Année début de collecte", en: "Collection start year"},
            type: "number",
            operators: [{value: "=", label: {fr: "Égale à", en: "Equals"}}, {
                value: ">",
                label: {fr: "Supérieur à", en: "Greater than"}
            }, {value: "<", label: {fr: "Inférieur à", en: "Less than"}}]
        },
        {
            key: "year_end",
            label: {fr: "Année fin de collecte", en: "Collection end year"},
            type: "number",
            operators: [{value: "=", label: {fr: "Égale à", en: "Equals"}}, {
                value: ">",
                label: {fr: "Supérieur à", en: "Greater than"}
            }, {value: "<", label: {fr: "Inférieur à", en: "Less than"}}]
        },
        {
            key: "idno",
            label: {fr: "Identifiant de la fiche", en: "Record Identifier"},
            type: "text",
            operators: [{value: "like", label: {fr: "Contient", en: "Contains"}}, {
                value: "=",
                label: {fr: "Égale à", en: "Equals"}
            }]
        },
        {
            key: "created",
            label: {fr: "Date de la création de la fiche", en: "Creation date"},
            type: "date",
            operators: [{value: "=", label: {fr: "Égale à", en: "Equals"}}, {
                value: ">",
                label: {fr: "Supérieur à", en: "Greater than"}
            }, {value: "<", label: {fr: "Inférieur à", en: "Less than"}}]
        },
        {
            key: "changed",
            label: {fr: "Date de la mise à jour de la fiche", en: "Last update date"},
            type: "date",
            operators: [{value: "=", label: {fr: "Égale à", en: "Equals"}}, {
                value: ">",
                label: {fr: "Supérieur à", en: "Greater than"}
            }, {value: "<", label: {fr: "Inférieur à", en: "Less than"}}]
        }
    ];

    const translations = {
        fr: {
            "search-label": "Recherche libre",
            "search-placeholder": "Mots-clés, auteur, thème...",
            "btn-reset": "Réinitialiser",
            "btn-search": "Rechercher",
            "sort-label": "Trier par",
            "display-label": "Afficher",
            "global-operator": "Opérateur global",
            "btn-add-criteria": "Ajouter",
            "operator-and": "ET",
            "operator-or": "OU",
            "sort-recent-asc": "Date de dernière mise à jour : croissant",
            "sort-recent-desc": "Date de dernière mise à jour : décroissant",
            "sort-title-asc": "Classer par titre : de A à Z",
            "sort-title-desc": "Classer par titre : de Z à A"
        },
        en: {
            "search-label": "Free research",
            "search-placeholder": "Keywords, author, theme...",
            "btn-reset": "Reset",
            "btn-search": "Search",
            "sort-label": "Sort By",
            "display-label": "Display",
            "global-operator": "Global operator",
            "btn-add-criteria": "Add",
            "operator-and": "AND",
            "operator-or": "OR",
            "sort-recent-asc": "Date of last update : ascending",
            "sort-recent-desc": "Date of last update : descending",
            "sort-title-asc": "Sort by title: A to Z",
            "sort-title-desc": "Sort by title: Z to A"
        }
    };
    // 2. INSTANCIATION DU GESTIONNAIRE DE FILTRES
    const filterManager = new UnifiedFilterManager({
        prefix: "catalog_",
        lang: currentLang,
        advFields: advFieldsCatalog,
        selectors: {
            searchInput: "#nada-search-input",
            autocompleteList: "#nada-autocomplete-list",
            limit: "#nada-limit",
            sortBy: "#nada-display-sort"
        },
        onSearch: function () {
            window.loadCatalogPage(1);
        }
    });

    // Événements de tri/limite
    $("#nada-limit, #nada-display-sort").on("change", function () {
        filterManager.isAdvancedMode() ? filterManager.saveAdvancedFilters() : filterManager.saveSimpleFilters();
        window.loadCatalogPage(1);
    });

    // 3. REQUÊTES AJAX (CATALOGUE)
    window.loadCatalogPage = function (page = 1) {
        $("#nada-pagination-bottom").hide();
        if (filterManager.isAdvancedMode()) {
            loadAdvancedSearchResults(page);
        } else {
            loadNormalSearchResults(page);
        }
    };

    function loadNormalSearchResults(page) {
        const lang = $("input[name='language-radio']:checked").val() || currentLang;
        const [sortBy, sortOrder] = $("#nada-display-sort").val().split("-");
        const filters = collectFilters(lang); // Facettes cochées
        const checkedOptions = readCheckedFiltersObject();


        $.ajax({
            url: catalog_vars.ajax_url,
            method: "POST",
            data: {
                action: "nada_load_catalogs",
                page: page,
                filters: filters,
                lang: lang,
                search: $("#nada-search-input").val(),
                sortBy: sortBy,
                sortOrder: sortOrder,
                limit: $("#nada-limit").val()
            },
            beforeSend: function () {
                $("#nada-resultats").html(`<p>${lang === "en" ? "Loading..." : "Chargement..."}</p>`);
            },
            success: function (res) {
                if (!res.success) {
                    $("#nada-resultats").html(`<p>${lang === "en" ? "Server error." : "Erreur serveur."}</p>`);
                    return;
                }
                $("#nada-resultats").html(res.data.items);
                $("#nada-pagination-top").html(res.data.pagination);
                $("#nada-pagination-bottom").html(res.data.pagination).show();
                window.currentPage = page;

                // Afficher les tags des facettes simples
                displayFilterTags(checkedOptions, lang);
            },
            error: function () {
                $("#nada-resultats").html(`<p>${lang === "en" ? "Error while loading." : "Erreur lors du chargement."}</p>`);
            }
        });
    }

    function loadAdvancedSearchResults(page) {
        const criteria = filterManager.collectAdvancedCriteria();
        if (criteria === false) return; // Erreur de validation

        const operator = $("#adv-global-operator").val();
        const lang = $("input[name='language-radio']:checked").val() || currentLang;
        const [sortBy, sortOrder] = $("#nada-display-sort").val().split("-");
        const limit = $("#nada-limit").val();

        $.ajax({
            url: catalog_vars.ajax_url,
            method: "POST",
            data: {
                action: "nada_advanced_search",
                page: page,
                limit: limit,
                sortBy: sortBy,
                sortOrder: sortOrder,
                lang: lang,
                criteria: criteria,
                operator: operator
            },
            beforeSend: function () {
                $("#nada-resultats").html(`<p>${lang === "en" ? "Searching..." : "Recherche avancée..."}</p>`);
            },
            success: function (res) {
                if (res.success) {
                    $("#nada-resultats").html(res.data.items);
                    $("#nada-pagination-top").html(res.data.pagination);
                    $("#nada-pagination-bottom").html(res.data.pagination).show();
                    window.currentPage = page;
                    window.currentPage = page;
                } else {
                    $("#nada-resultats").html(`<div class="alert alert-warning">${lang === "en" ? "No results found." : "Aucun résultat."}</div>`);
                }
            },
            error: function () {
                $("#nada-resultats").html(`<p>${lang === "en" ? "Connection error." : "Erreur de connexion."}</p>`);
            }
        });
    }

    // 4. GESTION DES FACETTES
    window.collectFilters = function (lang) {
        const formData = $(`#facet-${lang} #nada-filter-form`).serializeArray();
        const uniqueFilters = {};

        formData.forEach(({name, value}) => {
            if (!uniqueFilters[name]) uniqueFilters[name] = new Set();
            uniqueFilters[name].add(value);
        });

        // Construire proprement l'URLSearchParams
        const params = new URLSearchParams();
        Object.keys(uniqueFilters).forEach((name) => {
            uniqueFilters[name].forEach((value) => {
                params.append(name, value);
            });
        });

        return params.toString();
    };

    function readCheckedFiltersObject() {
        const obj = {};
        const processedValues = new Set();
        const lang = $("input[name='language-radio']:checked").val() || currentLang;
        const $activeContainer = $(`#facet-${lang}`);

        $activeContainer.find(".facet-input:checked").each(function () {
            let key = $(this).data("facet") || $(this).attr("name").replace(/\[\]$/, "");
            const val = String($(this).val());
            const optionLabel = $(this).data("option") || "";
            const uniqueKey = `${key}|||${val}`;

            if (!processedValues.has(uniqueKey)) {
                processedValues.add(uniqueKey);
                if (!obj[key]) obj[key] = [];
                obj[key].push({id: val, label: optionLabel});
            }
        });
        return obj;
    }

    function applyFiltersObjectToForm(filtersObj, $formRoot) {
        if (!filtersObj || !$formRoot?.length) return;
        Object.keys(filtersObj).forEach(function (key) {
            const values = filtersObj[key] || [];
            values.forEach(function (item) {
                const valueToMatch = typeof item === "object" ? item.id : String(item);
                const safeValue = valueToMatch.replaceAll('"', '\\"');
                const selector = `input[name="${key}[]"][value="${safeValue}"], input[data-facet="${key}"][value="${safeValue}"]`;
                $formRoot.find(selector.trim()).prop("checked", true);
            });
        });
    }

    function displayFilterTags(checkedOptions, lang) {
        let html = "";

        // 1. On génère le HTML uniquement si on a des options
        if (checkedOptions && Object.keys(checkedOptions).length > 0) {
            Object.entries(checkedOptions).forEach(([facetName, categoryArray]) => {
                categoryArray.forEach((item) => {
                    html += `
                <div class="filter-tag d-inline-flex align-items-center border rounded-pill px-3 me-2 mb-2" 
                     data-facet="${facetName}" data-id="${item.id}">
                  <span class="filter-label me-2">${item.label}</span>
                  <button type="button" class="btn btn-sm btn-outline-danger btn-remove-filter" title="Supprimer">✕</button>
                </div>`;
                });
            });
        }

        const $box = $(".filtresBox-nada");

        // Si la box existe mais pas le conteneur interne, on le crée dynamiquement
        if ($("#nada-filtres").length === 0) {
            $box.html('<div id="nada-filtres" class="d-flex flex-wrap gap-2"></div>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-allfilter"><i class="fa fa-times"></i></button>');
        }
        const $container = $("#nada-filtres");
        if (html !== "") {
            $container.html(html);
            $box.removeClass("d-none");
            const txt = (lang === "fr" ? "Effacer filtres" : "Clear filters");
            $box.find(".btn-remove-allfilter").html('<i class="fa fa-times"></i> ' + txt);
        } else {
            $container.empty();
            $box.addClass("d-none");
        }
    }

    window.loadFacets = function (lang, preservedFilters = null, callback = null) {
        $.post(catalog_vars.ajax_url, {action: "nada_load_facets", lang: lang}, function (res) {
            if (!res.success) return;

            $("#btn-reset-all").trigger("click");

            if (lang === "fr") {
                $("#facet-fr").html(res.data.html).removeClass("d-none");
                $("#facet-en").empty();
                $("#facet-en").addClass("d-none");
                if (preservedFilters) applyFiltersObjectToForm(preservedFilters, $("#facet-fr"));
            } else {
                $("#facet-en").html(res.data.html).removeClass("d-none");
                $("#facet-fr").empty();
                $("#facet-fr").addClass("d-none");
                if (preservedFilters) applyFiltersObjectToForm(preservedFilters, $("#facet-en"));
            }
            $(document).trigger("nada:facets:loaded", [lang, preservedFilters]);
            if (typeof callback === "function") callback();
        });
    };

    $(document).on("change", ".facet-input", function () {
        if (filterManager.isAdvancedMode()) {
            filterManager.switchToSimpleSearch();
        }
        filterManager.saveSimpleFilters(); // Sauvegarder l'input de recherche texte
        window.loadCatalogPage(1);

        // Mettre à jour les badges
        const facetName = $(this).closest(".accordion-item").attr("id").replace("accordion-", "");
        updateFacetBadge(facetName);
    });

    // Supprimer une facette via le tag
    $(document).on("click", ".btn-remove-filter", function (e) {
        e.stopPropagation();
        const $tag = $(this).closest(".filter-tag");
        const idToRemove = $tag.data("id");
        const facetName = $tag.data("facet");
        const $activeContainer = $(`#facet-${currentLang}`);
        const $checkbox = $activeContainer.find(`.facet-input[value="${idToRemove}"]`);
        $checkbox.prop("checked", false);

        updateFacetBadge(facetName);
        $tag.remove();
        filterManager.saveSimpleFilters();
        window.loadCatalogPage(1);
    });

    // 5. CHANGEMENT DE LANGUE
    function changeLanguage(lang) {
        const t = translations[lang];
        filterManager.lang = lang; // Mettre à jour la classe

        $('label[for="nada-search-input"]').html(`<i class="fa fa-search"></i> ${t["search-label"]}`);
        $("#nada-search-input").attr("placeholder", t["search-placeholder"]);
        $("#btn-reset-all").html(`<i class="fa fa-times"></i> ${t["btn-reset"]}`);
        $("#nada-search-button").html(`<i class="fa fa-search"></i> ${t["btn-search"]}`);
        $('label[for="nada-display-sort"]').html(`<i class="fa fa-sort"></i> ${t["sort-label"]}`);
        $('label[for="nada-limit"]').html(`<i class="fa fa-list"></i> ${t["display-label"]}`);
        $('label[for="adv-global-operator"]').text(t["global-operator"] + ":");

        const selectedSort = $("#nada-display-sort").val();
        const selectedOperator = $("#adv-global-operator").val();

        $('#nada-display-sort option[value="changed-asc"]').text(t["sort-recent-asc"]);
        $('#nada-display-sort option[value="changed-desc"]').text(t["sort-recent-desc"]);
        $('#nada-display-sort option[value="title-asc"]').text(t["sort-title-asc"]);
        $('#nada-display-sort option[value="title-desc"]').text(t["sort-title-desc"]);
        $('#adv-global-operator option[value="AND"]').text(t["operator-and"]);
        $('#adv-global-operator option[value="OR"]').text(t["operator-or"]);

        $("#nada-display-sort").val(selectedSort);
        $("#adv-global-operator").val(selectedOperator);

        if ($.fn.select2) {
            $("#nada-display-sort").select2("destroy").select2();
            $("#adv-global-operator").select2("destroy").select2();
        }

        $(".btn-add-criteria").html(`<i class="fa fa-plus"></i> ${t["btn-add-criteria"]}`);

        $("#adv-criteria-container").empty();
        if ($(".nada-unified-search-card").hasClass("advanced")) {
            filterManager.addCriteriaRow();
        }
    }

    $("input[name='language-radio']").on("change", function () {
        currentLang = $(this).val();
        const preserved = readCheckedFiltersObject();
        changeLanguage(currentLang);
        const isAdvanced = filterManager.isAdvancedMode();
        const toggleBtnText = isAdvanced
            ? (currentLang === "fr" ? "Recherche Simple" : "Simple Search")
            : (currentLang === "fr" ? "Recherche Avancée" : "Advanced Search");

        $("#toggle-search-mode .toggle-text").text(toggleBtnText);
        window.loadFacets(currentLang, preserved, function () {
            if (isAdvanced) {
                filterManager.saveAdvancedFilters();
            } else {
                filterManager.saveSimpleFilters();
            }
            window.loadCatalogPage(1);
        });
    });

    // 6. UI FACETTES & UTILITAIRES
    $(document).on("click", ".nada-pagination a", function (e) {
        e.preventDefault();
        window.loadCatalogPage($(this).data("page"));
    });

    function normalizeString(str) {
        return str.normalize("NFD").replaceAll(/[\u0300-\u036f]/g, "").toLowerCase().trim();
    }

    function updateFacetBadge(facetName) {
        const uniqueValues = new Set();
        const lang = $("input[name='language-radio']:checked").val() || currentLang;
        const $parentContainer = $(`#facet-${lang.toLowerCase()}`);

        $parentContainer.find(`.facet-input[data-facet="${facetName}"]:checked`).each(function () {
            uniqueValues.add($(this).val());
        });

        const totalChecked = uniqueValues.size;
        const $accordionItem = $parentContainer.find(`#accordion-${facetName}`);
        const $button = $accordionItem.find(".accordion-button");

        $button.find(".checked-indicator").remove();

        if (totalChecked > 0) {
            $button.append(`
            <span class="checked-indicator badge bg-primary ms-2" data-facet="${facetName}">
                ${totalChecked}
            </span>
        `);
        }
    }

    $(document).on("change", ".facette-list .facet-input", function () {
        const facetName = $(this).data("facet");
        const optionValue = $(this).val();
        const isChecked = $(this).is(":checked");

        // Trouver la checkbox correspondante dans le modal
        const $modalCheckbox = $(`.facetteModal .facet-input[data-facet="${facetName}"][value="${optionValue}"]`);

        if ($modalCheckbox.length) {
            $modalCheckbox.prop("checked", isChecked).trigger("change");
        }

        // Mettre à jour le badge
        updateFacetBadge(facetName);
    });

    $(document).on("click", ".btn-remove-allfilter", function (e) {
        e.stopPropagation();

        $("input[type='checkbox'].facet-input").each(function () {
            $(this).prop("checked", false);
        });
        $(".filtresBox-nada").html('');
        $(".filtresBox-nada").addClass("d-none");
        $(".checked-indicator").text("").hide();

        loadCatalogPage(1);
    });

    $(document).on("click", ".btn-validate", function () {
        // Trouve le modal parent du bouton cliqué
        const $modal = $(this).closest(".facetteModal");
        // Ferme le modal avec Bootstrap
        const modalInstance = bootstrap.Modal.getInstance($modal[0]);
        if (modalInstance) {
            modalInstance.hide();
        }
    });

    // Event: Tout sélectionner dans le modal
    $(document).on("change", "input[id^='selectAllOptions']", function () {
        const isChecked = $(this).is(":checked");
        const $modal = $(this).closest(".facetteModal");
        const $allCheckboxes = $modal.find(".facette-all-list .facet-input:visible");
        const facetName = $allCheckboxes.first().data("facet");

        $allCheckboxes.each(function () {
            const optionValue = $(this).val();
            const $modalCheckbox = $(this);

            // Synchroniser avec l'accordéon
            const $accordionCheckbox = $(`.facette-list .facet-input[data-facet="${facetName}"][value="${optionValue}"]`);

            $modalCheckbox.prop("checked", isChecked);
            if ($accordionCheckbox.length) {
                $accordionCheckbox.prop("checked", isChecked);
            }
        });

        // Mettre à jour le badge
        updateFacetBadge(facetName);
    });

    // Event: Quand on coche/décoche une checkbox individuelle dans le modal
    $(document).on("change", ".facetteModal .facet-input", function () {
        const facetName = $(this).data("facet");
        const optionValue = $(this).val();
        const isChecked = $(this).is(":checked");

        // Trouver la checkbox correspondante dans l'accordéon
        const $accordionCheckbox = $(`.facette-list .facet-input[data-facet="${facetName}"][value="${optionValue}"]`);

        if ($accordionCheckbox.length) {
            $accordionCheckbox.prop("checked", isChecked);
        }

        // Mettre à jour le badge
        updateFacetBadgeFromModal(facetName);
    });

    function updateFacetBadgeFromModal(facetName) {
        updateFacetBadge(facetName);
    }


    $(document).on("input", ".facette-input-modal", function () {
        const searchTerm = normalizeString($(this).val());
        const $modal = $(this).closest(".modal");

        $modal.find(".facette-all-list .facette-item").each(function () {
            const text = normalizeString($(this).find(".facetteTitle").text());
            if (searchTerm === "" || text.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Mettre à jour "Tout sélectionner"
        const $selectAll = $modal.find("input[id^='selectAllOptions']");
        const $visibleCheckboxes = $modal.find(".facette-all-list .facet-input:visible");
        const totalVisible = $visibleCheckboxes.length;
        const checkedVisible = $visibleCheckboxes.filter(":checked").length;

        $selectAll.prop("checked", totalVisible > 0 && totalVisible === checkedVisible);
    });

    // Réinitialiser la recherche quand on ouvre le modal
    $(document).on("show.bs.modal", ".facetteModal", function () {
        const $modal = $(this);
        const modalId = $modal.attr("id");
        const isEn = modalId.includes("facetteModalEn-");
        const langSuffix = isEn ? "en" : "fr";
        const $parentFacet = $(`#facet-${langSuffix}`);

        const facetName = modalId
            .replace("facetteModalFr-", "")
            .replace("facetteModalEn-", "");

        const $accordion = $parentFacet.find(`#accordion-${facetName}`);

        // Synchroniser l'état des checkboxes du modal avec l'accordéon
        const $accordionCheckboxes = $accordion.find(".facet-input");
        const $modalCheckboxes = $modal.find(".facette-all-list .facet-input");

        $modalCheckboxes.each(function () {
            const optionValue = $(this).val();
            const $accordionCheckbox = $accordionCheckboxes.filter(`[value="${optionValue}"]`);

            if ($accordionCheckbox.length && $accordionCheckbox.is(":checked")) {
                $(this).prop("checked", true);
            } else {
                $(this).prop("checked", false);
            }
        });

        // Appliquer la recherche si elle existe
        const accordionSearchValue = $accordion.find(".facette-input").val() || "";
        if (accordionSearchValue && accordionSearchValue.trim() !== "") {
            const $modalSearch = $modal.find(".facette-input-modal");
            $modalSearch.val(accordionSearchValue);

            const searchTerm = normalizeString(accordionSearchValue);
            $modal.find(".facette-all-list .facette-item").each(function () {
                const text = normalizeString($(this).find(".facetteTitle").text());
                if (text.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $modal.find(".facette-all-list .facette-item").show();
            $modal.find(".facette-input-modal").val("");
        }

        // Mettre à jour l'état de "Tout sélectionner"
        const $selectAll = $modal.find("input[id^='selectAllOptions']");
        const $visibleCheckboxes = $modal.find(".facette-all-list .facet-input:visible");
        const totalVisible = $visibleCheckboxes.length;
        const checkedVisible = $visibleCheckboxes.filter(":checked").length;

        $selectAll.prop("checked", totalVisible > 0 && totalVisible === checkedVisible);
    });

    $(document).on("hidden.bs.modal", ".facetteModal", function () {
        const $modal = $(this);
        const modalId = $modal.attr("id");

        const isEn = modalId.includes("facetteModalEn-");
        const langSuffix = isEn ? "en" : "fr";
        const $parentFacet = $(`#facet-${langSuffix}`);
        const facetName = modalId
            .replace("facetteModalFr-", "")
            .replace("facetteModalEn-", "");

        const $accordion = $parentFacet.find(`#accordion-${facetName}`);

        // Réinitialiser la recherche
        $modal.find(".facette-input-modal").val("");
        $accordion.find(".facette-input").val("");

        // Réafficher les 10 premiers éléments dans l'accordéon
        let $items = $accordion.find(".facette-list .facette-item");
        let $toggleBtn = $accordion.find(".facette-toggle");

        $items.removeAttr("style");
        $items.addClass("d-none");
        $items.slice(0, 10).removeClass("d-none");

        $items.each(function (index) {
            if (index >= 10) {
                $(this).addClass("remaining-item");
            } else {
                $(this).removeClass("remaining-item");
            }
        });

        if ($items.length > 10){
            $toggleBtn.show();
        } else {
            $toggleBtn.hide();
        }
        // Afficher tous les items du modal
        $modal.find(".facette-all-list .facette-item").show();
    });

    $(document).on("click", ".facetteModal .btn-validate", function () {
        const $modal = $(this).closest(".facetteModal");
        const modalInstance = bootstrap.Modal.getInstance($modal[0]);

        if (modalInstance) {
            modalInstance.hide();
        }
        loadCatalogPage(1);
    });

    function updateAllBadges() {
        const facets = {};
        $(".facet-input:checked").each(function () {
            const facetName = $(this).data("facet");
            if (facetName) {
                if (!facets[facetName]) facets[facetName] = new Set();
                facets[facetName].add($(this).val());
            }
        });

        Object.keys(facets).forEach(function (facetName) {
            const count = facets[facetName].size;
            const $button = $(`#accordion-${facetName}`).find(".accordion-button");
            $button.find(".checked-indicator").remove();
            if (count > 0) {
                $button.append(`<span class="checked-indicator badge bg-primary ms-2" data-facet="${facetName}">${count}</span>`);
            }
        });
    }

    // Recherche dans les accordéons
    $(document).on("input", ".facette-input", function () {
        const searchTerm = normalizeString($(this).val());
        const $accordionBody = $(this).closest(".accordion-body");
        const $items = $accordionBody.find(".facette-list .facette-item");
        const $toggleBtn = $accordionBody.find(".facette-toggle");

        if (searchTerm === "") {
            $items.removeAttr("style").addClass("d-none");
            $items.slice(0, 10).removeClass("d-none");
            $items.each((index, el) => $(el).toggleClass("remaining-item", index >= 10));
            $toggleBtn.toggle($items.length > 10);
        } else {
            const matchingItems = [];
            $items.each(function () {
                if (normalizeString($(this).text()).includes(searchTerm)) matchingItems.push($(this));
            });
            $items.addClass("d-none").hide();
            matchingItems.slice(0, 10).forEach(($item) => $item.removeClass("d-none remaining-item").show());
            matchingItems.forEach(($item, index) => {
                if (index >= 10) $item.addClass("remaining-item");
            });
            $toggleBtn.toggle(matchingItems.length > 10);
        }
    });

    // Toggle container de contenu
    $(".toggle-item").on("click", function () {
        const container = $(this).closest(".toggle-container");
        container.find(".toggle-content").toggleClass("opened");
        container.toggleClass("is-open");
    });

    // 7. INITIALISATION DU CHARGEMENT
    const savedData = filterManager.getStorage(filterManager.STORAGE_KEY);
    const savedAdvData = filterManager.getStorage(filterManager.ADV_STORAGE_KEY);

    if (filterManager.isAdvancedMode()) {
        // Mode avancé activé par le restoreInitialState()
        window.loadFacets(currentLang, savedAdvData?.filters, function () {
            updateAllBadges();
            window.loadCatalogPage(1);
        });
    } else {
        // Mode simple activé
        window.loadFacets(currentLang, savedData?.filters, function () {
            updateAllBadges();
            window.loadCatalogPage(1);
        });
    }

    // Si le mot-clé de recherche provient de l'URL (?search=...)
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get("search");
    if (searchParam) {
        $("#nada-search-input").val(searchParam);
        filterManager.saveSimpleFilters();
        window.loadCatalogPage(1);
    }
});
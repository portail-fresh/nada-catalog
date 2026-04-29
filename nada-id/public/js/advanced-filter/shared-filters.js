(function ($) {

    class UnifiedFilterManager {
        constructor(config) {
            this.prefix = config.prefix || "default_";
            this.lang = config.lang || "fr";
            this.advFields = config.advFields || [];
            this.onSearch = config.onSearch || function () {
            };

            this.selectors = {
                searchInput: config.selectors?.searchInput || "#nada-search-input",
                autocompleteList: config.selectors?.autocompleteList || "#nada-autocomplete-list",
                limit: config.selectors?.limit || "#nada-limit",
                sortBy: config.selectors?.sortBy || "#nada-display-sort",
                orderBy: config.selectors?.orderBy || null,
                ...config.selectors
            };

            // Clés LocalStorage
            this.STORAGE_KEY = this.prefix + "filters";
            this.ADV_STORAGE_KEY = this.prefix + "advanced_filters";
            this.MODE_STORAGE_KEY = this.prefix + "search_mode";

            // Variables d'état de l'autocomplétion
            this.acTimeoutId = null;
            this.acCurrentItems = [];
            this.acSelectedIndex = -1;
            this.acAbortController = null;

            this.initEvents();
            this.restoreInitialState();
        }

        saveSearchMode(mode) {
            try {
                localStorage.setItem(this.MODE_STORAGE_KEY, mode);
            } catch (e) {
            }
        }

        loadSearchMode() {
            try {
                return localStorage.getItem(this.MODE_STORAGE_KEY) || "simple";
            } catch (e) {
                return "simple";
            }
        }

        saveSimpleFilters() {
            const searchValue = $(this.selectors.searchInput).val();
            const limit = $(this.selectors.limit).val();
            let sortBy = $(this.selectors.sortBy).val();
            let orderBy = this.selectors.orderBy ? $(this.selectors.orderBy).val() : null;

            const data = {search: searchValue, sortBy: sortBy, orderBy: orderBy, limit: limit};
            try {
                localStorage.setItem(this.STORAGE_KEY, JSON.stringify(data));
                localStorage.removeItem(this.ADV_STORAGE_KEY);
            } catch (e) {
            }

            this.renderTags(null);
        }

        saveAdvancedFilters() {
            const criteria = this.collectAdvancedCriteria();
            if (criteria === false) return false;
            if (criteria.length === 0) {
                localStorage.removeItem(this.ADV_STORAGE_KEY);
                this.renderTags(null);
                return true;
            }

            const operator = $("#adv-global-operator").val() || "AND";
            const limit = $(this.selectors.limit).val();
            let sortBy = $(this.selectors.sortBy).val();
            let orderBy = this.selectors.orderBy ? $(this.selectors.orderBy).val() : null;

            const data = {criteria, operator, sortBy, orderBy, limit};
            try {
                localStorage.setItem(this.ADV_STORAGE_KEY, JSON.stringify(data));
                localStorage.removeItem(this.STORAGE_KEY);
            } catch (e) {
            }

            this.renderTags(criteria);
            return true;
        }

        getStorage(key) {
            try {
                return JSON.parse(localStorage.getItem(key));
            } catch (e) {
                return null;
            }
        }

        isAdvancedMode() {
            return !$("#advanced-search-section").hasClass("d-none");
        }

        switchToSimpleSearch() {
            $("#simple-search-section").removeClass("d-none");
            $("#advanced-search-section").addClass("d-none");
            $(".nada-unified-search-card").removeClass("advanced");

            // On replace les boutons en bas de la recherche simple
            if ($("#footer-slot").length) {
                $("#footer-slot").after($(".search-card-footer"));
            }

            const text = this.lang === "fr" ? "Recherche Avancée" : "Advanced Search";
            $("#toggle-search-mode .toggle-text").text(text);

            this.saveSearchMode("simple");
            $("#adv-criteria-container").empty();
            localStorage.removeItem(this.ADV_STORAGE_KEY);
            this.renderTags(null);
        }

        switchToAdvancedSearch() {
            $("#simple-search-section").addClass("d-none");
            $("#advanced-search-section").removeClass("d-none");
            $(".nada-unified-search-card").addClass("advanced");

            // On déplace les boutons en bas de la recherche avancée
            if ($("#advanced-search-section").length) {
                $("#advanced-search-section").after($(".search-card-footer"));
            }

            const text = this.lang === "fr" ? "Recherche Simple" : "Simple Search";
            $("#toggle-search-mode .toggle-text").text(text);

            this.saveSearchMode("advanced");
            if ($(this.selectors.searchInput).length) $(this.selectors.searchInput).val("");
            localStorage.removeItem(this.STORAGE_KEY);

            if ($("#adv-criteria-container .adv-row").length === 0) {
                this.addCriteriaRow();
            }
        }

        renderTags(criteria) {
            let html = "";
            if (criteria && criteria.length > 0) {
                criteria.forEach((criterion, index) => {
                    const fieldObj = this.advFields.find((f) => f.key === criterion.field);
                    const fieldLabel = fieldObj ? (fieldObj.label[this.lang] || fieldObj.label.fr) : criterion.field;
                    html += `
                    <div class="filter-tag d-inline-flex align-items-center border rounded-pill px-3" data-adv-index="${index}">
                      <span class="filter-label me-2">${fieldLabel} : ${criterion.value}</span>
                      <button type="button" class="btn btn-sm btn-outline-danger btn-remove-adv-filter" title="Supprimer">✕</button>
                    </div>`;
                });
                $(".filtresBox-nada").removeClass("d-none");
            } else {
                $(".filtresBox-nada").addClass("d-none");
            }
            const clearText = this.lang === "fr" ? "Effacer filtres" : "Clear filters";
            $(".filtresBox-nada .btn-remove-allfilter").html(`<i class="fa fa-times"></i> ${clearText}`);
            $("#nada-filtres").html(html);
        }

        collectAdvancedCriteria() {
            const criteria = [];
            let hasError = false;
            const lang = this.lang;

            $(".adv-row").each(function () {
                if (hasError) return false;
                const field = $(this).find(".adv-field-select").val();
                const operator = $(this).find(".adv-operator-select").val();
                const value = $(this).find(".adv-value-input").val();

                if ((field === "year_start" || field === "year_end") && value && !/^\d{4}$/.test(value)) {
                    alert(lang === "fr" ? "L'année doit contenir exactement 4 chiffres" : "Year must contain exactly 4 digits");
                    hasError = true;
                    return false;
                }
                if (value && value.trim() !== "") {
                    criteria.push({field, operator, value});
                }
            });
            return hasError ? false : criteria;
        }

        addCriteriaRow(field = null, operator = null, value = null) {
            const rowId = "adv-row-" + Date.now();
            const options = this.advFields.map((f) =>
                `<option value="${f.key}" data-type="${f.type}" data-operators='${JSON.stringify(f.operators)}' ${field === f.key ? "selected" : ""}>${f.label[this.lang] || f.label.fr}</option>`
            ).join("");

            const fieldObj = this.advFields.find((f) => f.key === (field || this.advFields[0].key));
            const selectedType = fieldObj?.type || "text";
            const availableOps = fieldObj?.operators || [{value: "=", label: {fr: "Égale à", en: "Equals"}}];

            const operatorOptions = availableOps.map((op) =>
                `<option value="${op.value}" ${operator === op.value ? "selected" : ""}>${op.label?.[this.lang] || op.label?.fr || op.value}</option>`
            ).join("");

            let inputHtml = selectedType === "date"
                ? `<input type="date" class="form-control form-control-sm adv-value-input" value="${value || ""}">`
                : `<input type="${selectedType}" class="form-control form-control-sm adv-value-input" value="${value || ""}" placeholder="...">`;

            const operatorHtml = $("#adv-criteria-container .adv-row").length > 0
                ? `<div class="col-12 adv-operator-label mb-2" data-operator-for="${rowId}"><strong>${$("#adv-global-operator option:selected").text() || 'ET'}</strong></div>`
                : "";

            const html = `
            ${operatorHtml}
            <div class="mb-2 adv-row align-items-center" id="${rowId}">
                <div class="px-2"><div class="row">
                    <div class="col-4"><select class="form-select form-select-sm adv-field-select">${options}</select></div>
                    <div class="col-2"><select class="form-select form-select-sm adv-operator-select">${operatorOptions}</select></div>
                    <div class="col-5 adv-input-wrapper">${inputHtml}</div>
                    <div class="col-1 text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fa fa-times"></i></button></div>
                </div></div>
            </div>`;
            $("#adv-criteria-container").append(html);
        }

        restoreAdvancedFilters(data) {
            if (!data?.criteria) return;
            if (data.operator) $("#adv-global-operator").val(data.operator);
            $("#adv-criteria-container").empty();
            data.criteria.forEach((c) => this.addCriteriaRow(c.field, c.operator, c.value));
            this.renderTags(data.criteria);
        }

        // LOGIQUE D'AUTOCOMPLÉTION
        acEscapeHtml(str) {
            return String(str).replaceAll(/[&<>"'`=\/]/g, (s) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;',
                '`': '&#x60;',
                '=': '&#x3D;'
            }[s]));
        }

        acOpenList() {
            if (!this.acCurrentItems.length) return;
            $(this.selectors.autocompleteList).addClass('is-open');
            $(this.selectors.searchInput).attr('aria-expanded', 'true');
        }

        acCloseList() {
            $(this.selectors.autocompleteList).removeClass('is-open').empty();
            $(this.selectors.searchInput).attr('aria-expanded', 'false').removeAttr('aria-activedescendant');
            this.acCurrentItems = [];
            this.acSelectedIndex = -1;
        }

        acSetActive(index) {
            const $options = $(this.selectors.autocompleteList).find('[role="option"]');
            $options.removeClass('is-active');

            if (index < 0 || index >= $options.length) {
                this.acSelectedIndex = -1;
                $(this.selectors.searchInput).removeAttr('aria-activedescendant');
                return;
            }

            this.acSelectedIndex = index;
            const $activeOption = $options.eq(index);
            $activeOption.addClass('is-active');
            $(this.selectors.searchInput).attr('aria-activedescendant', $activeOption.attr('id'));
            $activeOption[0].scrollIntoView({block: 'nearest'});
        }

        acChooseItem(item) {
            $(this.selectors.searchInput).val(item.title || '');
            this.acCloseList();
            const success = this.isAdvancedMode() ? this.saveAdvancedFilters() : (this.saveSimpleFilters(), true);
            if (success) this.onSearch();
        }

        acShowLoading() {
            const $list = $(this.selectors.autocompleteList);
            $list.empty();
            $list.append(`<div class="nada-autocomplete-header">${this.lang === 'fr' ? 'Recherche...' : 'Searching...'}</div>`);
            $list.addClass('is-open');
            $(this.selectors.searchInput).attr('aria-expanded', 'true');
        }

        acRenderList(items, q) {
            const $list = $(this.selectors.autocompleteList);
            $list.empty();

            if (!items?.length) {
                this.acCloseList();
                return;
            }

            this.acCurrentItems = items;
            $list.append(`<div class="nada-autocomplete-header">Suggestions</div>`);

            const $scroll = $('<div class="nada-autocomplete-scroll"></div>');

            items.forEach((item, idx) => {
                const meta = [];
                if (item.nation) meta.push(item.nation);
                if (item.year) meta.push(item.year);

                const highlighted = this.acEscapeHtml(item.title || "");

                const $div = $('<div></div>', {
                    class: 'nada-autocomplete-item',
                    role: 'option',
                    id: 'nada-ac-opt-' + idx
                });

                $div.html(`<strong>${highlighted}</strong>` + (meta.length ? `<span class="nada-autocomplete-meta">(${meta.join(', ')})</span>` : ''));

                $div.on('mousedown', (e) => {
                    e.preventDefault();
                    this.acChooseItem(item);
                });

                $div.on('mouseenter', () => {
                    this.acSetActive(idx);
                });

                $scroll.append($div);
            });

            $list.append($scroll);
            this.acOpenList();
            this.acSetActive(-1);
        }

        acFetchSuggestions(q) {
            const $input = $(this.selectors.searchInput);
            const endpoint = $input.attr('data-autocomplete-url');
            if (!endpoint) return;

            const userId = $input.attr('data-user-id') || '';

            if (this.acAbortController) this.acAbortController.abort();
            this.acAbortController = new AbortController();

            const context = window.CONTEXT === 'studies' ? 'studies' : 'catalog';
            const currentLang = this.lang || 'fr';
            const isAdmin = $input.attr('data-is-admin') || 0;
            let url = endpoint + '?q=' + encodeURIComponent(q) + '&context=' + context + '&lang=' + currentLang + '&limit=10';
            if (userId && context === 'studies') {
                url += '&user_id=' + encodeURIComponent(userId) +'&is_admin=' + encodeURIComponent(isAdmin);
            }

            this.acShowLoading();

            fetch(url, {
                credentials: 'omit',
                signal: this.acAbortController.signal
            })
                .then(r => r.ok ? r.json() : Promise.reject(r))
                .then(data => this.acRenderList(data, q))
                .catch(err => {
                    if (err?.name === 'AbortError') return;
                    console.error('Autocomplete error', err);
                    this.acCloseList();
                });
        }
        initEvents() {
            const self = this;
            $("#toggle-search-mode").off('click').on("click", function () {
                self.isAdvancedMode() ? self.switchToSimpleSearch() : self.switchToAdvancedSearch();
                self.onSearch();
            });

            $("#btn-reset-all").off('click').on("click", function () {
                if (self.isAdvancedMode()) {
                    $("#adv-criteria-container").empty();
                    self.addCriteriaRow();
                    localStorage.removeItem(self.ADV_STORAGE_KEY);
                    self.renderTags(null);
                } else {
                    $(self.selectors.searchInput).val("");
                    localStorage.removeItem(self.STORAGE_KEY);
                    $("input[type='checkbox'].facet-input").prop("checked", false);
                }
                self.onSearch();
            });

            $("#nada-search-button").off('click').on("click", function () {
                self.acCloseList();
                const success = self.isAdvancedMode() ? self.saveAdvancedFilters() : (self.saveSimpleFilters(), true);
                if (success) self.onSearch();
            });


            $(document).off("keypress.filterCoreAdv").on("keypress.filterCoreAdv", ".adv-value-input", function (e) {
                if (e.which === 13 || e.keyCode === 13) {
                    e.preventDefault();
                    if (self.saveAdvancedFilters()) self.onSearch();
                }
            });

            $(document).off("input.filterCoreAC").on("input.filterCoreAC", self.selectors.searchInput, function () {
                const q = $(this).val().trim();
                if (q.length < 3) {
                    self.acCloseList();
                    return;
                }
                if (self.acTimeoutId) clearTimeout(self.acTimeoutId);
                self.acTimeoutId = setTimeout(() => self.acFetchSuggestions(q), 200);
            });

            $(document).off("keydown.filterCoreAC").on("keydown.filterCoreAC", self.selectors.searchInput, function (e) {
                const isOpen = $(self.selectors.autocompleteList).hasClass('is-open');
                const max = self.acCurrentItems.length;

                if (isOpen) {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        self.acSetActive((self.acSelectedIndex + 1) >= max ? 0 : (self.acSelectedIndex + 1));
                        return;
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        self.acSetActive((self.acSelectedIndex - 1) < 0 ? (max - 1) : (self.acSelectedIndex - 1));
                        return;
                    } else if (e.key === 'Enter') {
                        if (self.acSelectedIndex >= 0 && self.acCurrentItems[self.acSelectedIndex]) {
                            e.preventDefault();
                            self.acChooseItem(self.acCurrentItems[self.acSelectedIndex]);
                            return;
                        }
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        self.acCloseList();
                        return;
                    }
                }

                if (e.key === 'Enter') {
                    e.preventDefault();
                    self.acCloseList();
                    const success = self.isAdvancedMode() ? self.saveAdvancedFilters() : (self.saveSimpleFilters(), true);
                    if (success) self.onSearch();
                }
            });

            $(document).off("mousedown.filterCoreAC").on("mousedown.filterCoreAC", function (e) {
                const $input = $(self.selectors.searchInput);
                const $list = $(self.selectors.autocompleteList);
                if (!$(e.target).closest($input).length && !$(e.target).closest($list).length) {
                    self.acCloseList();
                }
            });

            $(document).off("blur.filterCoreAC").on("blur.filterCoreAC", self.selectors.searchInput, function () {
                setTimeout(() => {
                    const $active = $(document.activeElement);
                    const $list = $(self.selectors.autocompleteList);
                    if (!$active.closest($list).length) {
                        self.acCloseList();
                    }
                }, 120);
            });


            $(document).off("click.filterCoreAdd").on("click.filterCoreAdd", ".btn-add-criteria", function (e) {
                e.preventDefault();
                self.addCriteriaRow();
            });

            $(document).off("change.filterCoreOp").on("change.filterCoreOp", "#adv-global-operator", function () {
                const newOperator = $(this).find("option:selected").text();
                $(".adv-operator-label").html(`<strong>${newOperator}</strong>`);
            });



            $(document).off("change.filterCoreField").on("change.filterCoreField", ".adv-field-select", function () {
                const $row = $(this).closest(".adv-row");
                const type = $(this).find(":selected").data("type");
                const operators = $(this).find(":selected").data("operators") || [{
                    value: "=",
                    label: {fr: "Égale à", en: "Equals"}
                }];
                const currentVal = $row.find(".adv-value-input").val();

                $row.find(".adv-operator-select").html(operators.map((op) => `<option value="${op.value}">${op.label?.[self.lang] || op.label?.fr || op.value}</option>`).join(""));
                $row.find(".adv-input-wrapper").html(`<input type="${type === 'date' ? 'date' : 'text'}" class="form-control form-control-sm adv-value-input" value="${currentVal}">`);
            });

            $(document).off("click.filterCoreRemove").on("click.filterCoreRemove", ".btn-remove-row", function () {
                const $row = $(this).closest(".adv-row");
                $(`.adv-operator-label[data-operator-for="${$row.attr("id")}"]`).remove();
                $row.remove();
                const $firstRow = $("#adv-criteria-container .adv-row").first();
                if ($firstRow.length > 0) {
                    $(`.adv-operator-label[data-operator-for="${$firstRow.attr("id")}"]`).remove();
                }
                if ($("#adv-criteria-container .adv-row").length === 0) {
                    self.addCriteriaRow();
                }

                if (self.saveAdvancedFilters()) {
                    self.onSearch();
                }
            });
            $(document).off("click.filterCoreRemoveAdv").on("click.filterCoreRemoveAdv", ".btn-remove-adv-filter", function (e) {
                e.stopPropagation();
                const index = Number.parseInt($(this).closest(".filter-tag").data("adv-index"));
                $(".adv-row").eq(index).find('.btn-remove-row').trigger('click');
                self.saveAdvancedFilters();
                self.onSearch();
            });
            $(document).off("click.filterCoreRemoveAll").on("click.filterCoreRemoveAll", ".btn-remove-allfilter", function (e) {
                e.stopPropagation();
                $("#btn-reset-all").trigger("click");
            });
        }

        restoreInitialState() {
            const savedMode = this.loadSearchMode();
            const savedAdvData = this.getStorage(this.ADV_STORAGE_KEY);
            const savedData = this.getStorage(this.STORAGE_KEY);

            if (savedMode === "advanced" && savedAdvData?.criteria && savedAdvData.criteria.length > 0) {
                this.switchToAdvancedSearch();
                this.restoreAdvancedFilters(savedAdvData);
                if (savedAdvData.sortBy) $(this.selectors.sortBy).val(savedAdvData.sortBy);
                if (savedAdvData.orderBy && this.selectors.orderBy) $(this.selectors.orderBy).val(savedAdvData.orderBy);
                if (savedAdvData.limit) $(this.selectors.limit).val(savedAdvData.limit);
            } else if (savedData) {
                this.switchToSimpleSearch();
                if (savedData.search) $(this.selectors.searchInput).val(savedData.search);
                if (savedData.sortBy) $(this.selectors.sortBy).val(savedData.sortBy);
                if (savedData.orderBy && this.selectors.orderBy) $(this.selectors.orderBy).val(savedData.orderBy);
                if (savedData.limit) $(this.selectors.limit).val(savedData.limit);
            } else {
                this.switchToSimpleSearch();
            }
        }
    }

    window.UnifiedFilterManager = UnifiedFilterManager;

})(jQuery);
jQuery(document).ready(function ($) {
  sessionStorage.removeItem("frStudyProcessed");
  sessionStorage.removeItem("enStudyProcessed");
  window.CONTEXT = "studies";
  const lang =
    typeof nada_global_vars !== "undefined" && nada_global_vars.lang
      ? nada_global_vars.lang
      : "fr";
  const advFieldsMySpace = [
    {
      key: "title",
      label: { fr: "Titre", en: "Title" },
      type: "text",
      operators: [
        { value: "like", label: { fr: "Contient", en: "Contains" } },
        { value: "=", label: { fr: "Égale à", en: "Equals" } },
      ],
    },
    {
      key: "abbreviation",
      label: { fr: "Acronyme", en: "Acronym" },
      type: "text",
      operators: [
        { value: "like", label: { fr: "Contient", en: "Contains" } },
        { value: "=", label: { fr: "Égale à", en: "Equals" } },
      ],
    },
    {
      key: "createdby",
      label: { fr: "Créée par", en: "Created by" },
      type: "text",
      operators: [
        { value: "like", label: { fr: "Contient", en: "Contains" } },
        { value: "=", label: { fr: "Égale à", en: "Equals" } },
      ],
    },
    {
      key: "modifiedby",
      label: { fr: "Modifiée par", en: "Modified by" },
      type: "text",
      operators: [
        { value: "like", label: { fr: "Contient", en: "Contains" } },
        { value: "=", label: { fr: "Égale à", en: "Equals" } },
      ],
    },
    {
      key: "link_technical",
      label: {
        fr: "Responsable scientifique (PI)",
        en: "Principal Investigator",
      },
      type: "text",
      operators: [
        { value: "like", label: { fr: "Contient", en: "Contains" } },
        { value: "=", label: { fr: "Égale à", en: "Equals" } },
      ],
    },
    {
      key: "idno",
      label: { fr: "Identifiant", en: "Identifiant" },
      type: "text",
      operators: [
        { value: "like", label: { fr: "Contient", en: "Contains" } },
        { value: "=", label: { fr: "Égale à", en: "Equals" } },
      ],
    },
    {
      key: "created",
      label: { fr: "Date de création", en: "Creation date" },
      type: "date",
      operators: [
        { value: "=", label: { fr: "Égale à", en: "Equals" } },
        { value: ">", label: { fr: "Supérieur à", en: "Greater than" } },
        { value: "<", label: { fr: "Inférieur à", en: "Less than" } },
      ],
    },
    {
      key: "changed",
      label: { fr: "Date de dernière modification", en: "Modification date" },
      type: "date",
      operators: [
        { value: "=", label: { fr: "Égale à", en: "Equals" } },
        { value: ">", label: { fr: "Supérieur à", en: "Greater than" } },
        { value: "<", label: { fr: "Inférieur à", en: "Less than" } },
      ],
    },
  ];

  let currentPage = 1;
  let start = 0;

  // Variables pour les actions modales
  let studyTitle = null;
  let studyIdno = null;
  let newState = null;
  let currentSwitch = null;

  const container = document.getElementById("cardContainer");
  const pagination = document.getElementById("pagination");
  const paginationInfo = document.getElementById("paginationInfo");

  const filterManager = new UnifiedFilterManager({
    prefix: "myspace_",
    lang: lang,
    advFields: advFieldsMySpace,
    selectors: {
      autocompleteList: "#myspace-autocomplete-list",
      searchInput: "#nada-search-input",
      limit: "#display-limit",
      sortBy: "#myspace-sortBy",
      orderBy: "#myspace-orderBy",
    },
    onSearch: function () {
      currentPage = 1;
      start = 0;
      const $btn = $("#nada-search-button");
      if ($btn.length) $btn.prop("disabled", true);
      loadStudies();
    },
  });

    // A-Z pas applicable pour date création , date de modification et IDentifiant ( il faut écrire croissant , décroissant seulement pour ces 3 champs
    const champsDateOuId = ["created", "changed", "idno"];
    $("#myspace-sortBy").on("change", function () {
        const champSelectionne = $(this).val();
        const selectOrder = $("#myspace-orderBy");
        selectOrder.empty();

        if (champsDateOuId.includes(champSelectionne)) {
            selectOrder.append(
                new Option(lang === "fr" ? "Croissant" : "Ascending", "asc"),
            );
            selectOrder.append(
                new Option(lang === "fr" ? "Décroissant" : "Descending", "desc"),
            );
            selectOrder.val("desc");
        } else {
            selectOrder.append(new Option("A-Z", "asc"));
            selectOrder.append(new Option("Z-A", "desc"));
            selectOrder.val("asc");
        }
    });

    // Initialiser les valeurs par défaut
    const $sortBy = $("#myspace-sortBy");
    if ($sortBy.val() !== "created") {
        $sortBy.val("created").trigger("change");
    } else {
        $sortBy.trigger("change");
    }

    $("#display-limit, #myspace-sortBy, #myspace-orderBy").on(
        "change",
        function () {
            filterManager.isAdvancedMode()
                ? filterManager.saveAdvancedFilters()
                : filterManager.saveSimpleFilters();
            currentPage = 1;
            start = 0;
            loadStudies();
        },
    );

  function loadStudies() {
    const isAdv = filterManager.isAdvancedMode();
    let criteria = [];
    let operator = "AND";
    let searchVal = "";

    if (isAdv) {
      criteria = filterManager.collectAdvancedCriteria();
      if (!criteria && $("#adv-criteria-container .adv-row").length > 0) return;
      operator = $("#adv-global-operator").val() || "AND";
    } else {
      searchVal = $("#nada-search-input").val();
    }

    const limit = $("#display-limit").val() || 10;
    const sortBy = $("#myspace-sortBy").val() || 'created';
    const orderBy = $("#myspace-orderBy").val() || "desc";

    $.ajax({
      url: study_vars.ajax_url,
      method: "POST",
      data: {
        action: "nada_get_studies_paginated",
        page: currentPage,
        start: start,
        length: limit,
        search: searchVal,
        orderBy: orderBy,
        sortBy: sortBy,
        is_advanced: isAdv ? 1 : 0,
        advanced_criteria: criteria,
        global_operator: operator,
      },
      success: function (result) {
        const $btn = $("#nada-search-button");
        if ($btn.length) {
          $btn.prop("disabled", false);
        }
        if (container && result.html) {
          container.innerHTML = result.html;
          renderPagination(result.total, limit);
        }
      },
    });
  }
  loadStudies();

  function renderPaginationInfo(total, limit) {
    const end = Math.min(currentPage * limit, total);
    let info = "";
    if (total > 0) {
      info =
        lang === "en"
          ? `Showing ${start + 1} to ${end} of ${total} entries`
          : `Affichage ${start + 1} à ${end} sur ${total} éléments`;
    }
    if (paginationInfo) paginationInfo.textContent = info;
  }

  function renderPagination(total, limit) {
    if (!pagination) return;
    pagination.innerHTML = "";
    const pages = Math.ceil(total / limit);
    const windowSize = 2;

    renderPaginationInfo(total, limit);

    pagination.innerHTML += `<li class="page-item ${currentPage === 1 ? "disabled" : ""}"><button class="page-link" data-page="1">&laquo;</button></li>`;
    pagination.innerHTML += `<li class="page-item ${currentPage === 1 ? "disabled" : ""}"><button class="page-link" data-page="${currentPage - 1}">&lsaquo;</button></li>`;

    let startPage = Math.max(1, currentPage - windowSize);
    let endPage = Math.min(pages, currentPage + windowSize);

    if (startPage > 1) {
      addPageButton(1);
      if (startPage > 2) addDots();
    }
    for (let i = startPage; i <= endPage; i++) {
      addPageButton(i);
    }
    if (endPage < pages) {
      if (endPage < pages - 1) addDots();
      addPageButton(pages);
    }

    pagination.innerHTML += `<li class="page-item ${currentPage === pages ? "disabled" : ""}"><button class="page-link" data-page="${currentPage + 1}">&rsaquo;</button></li>`;
    pagination.innerHTML += `<li class="page-item ${currentPage === pages ? "disabled" : ""}"><button class="page-link" data-page="${pages}">&raquo;</button></li>`;

    pagination.querySelectorAll("button").forEach((btn) => {
      btn.addEventListener("click", () => {
        const page = Number.parseInt(btn.dataset.page);
        if (page >= 1 && page <= pages) {
          start = limit * (page - 1);
          currentPage = page;
          loadStudies();
        }
      });
    });
  }

  function addPageButton(i) {
    pagination.innerHTML += `<li class="page-item ${i === currentPage ? "active" : ""}"><button class="page-link" data-page="${i}">${i}</button></li>`;
  }
  function addDots() {
    pagination.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
  }

  // Utilitaires de base
  document
    .querySelectorAll('[data-bs-toggle="tooltip"]')
    .forEach((el) => new bootstrap.Tooltip(el));
  const t = function (key) {
    const translations = {
      deleting: lang === "fr" ? "Suppression..." : "Deleting...",
      publishing: lang === "fr" ? "Publication..." : "Publishing...",
      unpublishing: lang === "fr" ? "Dépublication..." : "Unpublishing...",
      requesting: lang === "fr" ? "Demande en cours..." : "Requesting...",
      rejecting: lang === "fr" ? "Rejet en cours..." : "Rejecting...",
      Cancel: lang === "fr" ? "Annulation..." : "Canceling...",
    };
    return translations[key] || key;
  };

  // --- MÉTADONNÉES ---
  $(document).on("click", ".open-metadata", function (e) {
    e.stopPropagation();
    const meta = $(this).data("meta");
    $("#meta-identifier").text(meta.identifier || "");
    $("#meta-iDSchema").text(meta.iDSchema || "");
    $("#meta-provenance").text(meta.provenance || "");
    $("#meta-versionLang").text(meta.versionLang || "");
    $("#meta-originLang").text(meta.originLang || "");
    $("#meta-creationDate").text(meta.creationDate || "");
    $("#meta-lastUpdatedAuto").text(meta.lastUpdatedAuto || "");
    $("#meta-lastUpdatedManual").text(meta.lastUpdatedManual || "");
    $("#meta-respValidation").text(meta.respValidation || "");
    $("#meta-autoTranslation").text(meta.autoTranslation || "");
    $("#meta-status").text(meta.status || "");
    $("#meta-contributorName").text(meta.contributorName || "");
    $("#meta-contributorAffiliation").text(meta.contributorAffiliation || "");
    $("#metadata").fadeIn();
  });
  $(document).on("click", ".cancel-metadata, .nada-modal-overlay", function () {
    $("#metadata").fadeOut();
  });

  // --- SUPPRESSION ---
  $(document).on("click", ".delete-study-btn-nada", function (e) {
    e.preventDefault();
    studyIdno = $(this).data("study-idno");
    studyTitle = $(this).data("study-title");
    $("#deleteStudyModal .modal-body .study-title").text(studyTitle);
    $("#deleteStudyModal .confirm-delete-study").attr(
      "data-study-idno",
      studyIdno,
    );
    $("#deleteStudyModal").addClass("show");
  });

  $(".cancel-delete-study").on("click", function () {
    $("#deleteStudyModal").removeClass("show");
    studyIdno = null;
  });

  $(".confirm-delete-study").on("click", function () {
    if (!studyIdno) return;
    const $btn = $(this);
    const $spinner = $btn.find(".spinner-border");
    const $btnText = $btn.find(".btn-text");
    $spinner.removeClass("d-none");
    $btnText.text(t("deleting"));

    $.ajax({
      url: study_vars.ajax_url,
      method: "POST",
      data: { action: "nada_delete_study", study_idno: studyIdno },
      success: function (response) {
        if (response.success) {
          showToast(response.data?.message, "success");
          loadStudies();
        }
      },
      complete: function () {
        $("#deleteStudyModal").removeClass("show");
        $spinner.addClass("d-none");
        $btnText.text(lang === "fr" ? "Supprimer" : "Delete");
        studyIdno = null;
      },
    });
  });

  // --- PUBLICATION / DÉPUBLICATION ---
  $(document).on("click", ".publish-study-switch", function (e) {
    const checkbox = $(this);
    newState = checkbox.is(":checked") ? 1 : 0;
    studyIdno = checkbox.data("study-idno");
    studyTitle = checkbox.data("study-title");
    currentSwitch = this;

    const actionText =
      newState === 1
        ? lang === "fr"
          ? "publier"
          : "publish"
        : lang === "fr"
          ? "dépublier"
          : "unpublish";

    $("#publishStudyModal .study-title").text(studyTitle);
    $("#publishStudyModal .study-action").text(actionText);
    $("#publishStudyModal").show();
  });

  $(".cancel-action-study").on("click", function () {
    if (currentSwitch) {
      $(currentSwitch).prop("checked", !$(currentSwitch).is(":checked"));
    }
    $("#publishStudyModal").hide();
    studyIdno = null;
    currentSwitch = null;
  });

  $(".confirm-action-study").on("click", function () {
    if (!studyIdno) return;
    const $btn = $(this);
    const $spinner = $btn.find(".spinner-border");
    const $btnText = $btn.find(".btn-text");
    $spinner.removeClass("d-none");
    $btnText.text(newState == 0 ? t("unpublishing") : t("publishing"));

    $.ajax({
      url: study_vars.ajax_url,
      method: "POST",
      data: {
        action: "publish_unpublish_study",
        study_idno: studyIdno,
        study_title: studyTitle,
        published: newState,
        _ajax_nonce: study_vars.nonce,
      },
      success: function (response) {
        if (response.success) {
          showToast(response.data?.message, "success");
          loadStudies();
        }
      },
      complete: function () {
        $("#publishStudyModal").hide();
        $spinner.addClass("d-none");
        $btnText.text(lang === "fr" ? "Confirmer" : "Confirm");
        currentSwitch = null;
      },
    });
  });

  // --- MODIFICATIONS REQUISES ---
  $(document).on("click", ".require-study-modifications-btn", function (e) {
    e.preventDefault();
    studyIdno = $(this).data("study-idno");
    studyTitle = $(this).data("study-title");
    $("#requestChangesModal .modal-body .study-title").text(studyTitle);
    $("#requestChangesModal").show();
  });

  $(".confirm-request-changes-study").on("click", function () {
    if (!studyIdno) return;
    const $btn = $(this);
    const $spinner = $btn.find(".spinner-border");
    const $btnText = $btn.find(".btn-text");
    $spinner.removeClass("d-none");
    $btnText.text(t("requesting"));

    $.ajax({
      url: study_vars.ajax_url,
      method: "POST",
      data: {
        action: "request_changes_study",
        study_idno: studyIdno,
        study_title: studyTitle,
        _ajax_nonce: study_vars.nonce,
      },
      success: function (response) {
        if (response.success) {
          showToast(response.data?.message, "success");
          loadStudies();
        }
      },
      complete: function () {
        $("#requestChangesModal").hide();
        $spinner.addClass("d-none");
        studyIdno = null;
        $btnText.text(lang === "fr" ? "Confirmer" : "Confirm");
      },
    });
  });

  $(".requestChanges-cancel-modal").on("click", function () {
    $("#requestChangesModal").hide();
    studyIdno = null;
  });
  $(".cancel-modal").on("click", function () {
    $("#cancelStudyModal").hide();
    studyIdno = null;
  });

  // --- REJET ---
  $(document).on("click", ".reject-study-btn-nada", function (e) {
    e.preventDefault();
    studyIdno = $(this).data("study-idno");
    studyTitle = $(this).data("study-title");
    $("#rejectStudyModal .modal-body .study-title").text(studyTitle);
    $("#rejectStudyModal").show();
  });

  $(".confirm-reject-study").on("click", function () {
    if (!studyIdno) return;
    const $btn = $(this);
    const $spinner = $btn.find(".spinner-border");
    const $btnText = $btn.find(".btn-text");
    $spinner.removeClass("d-none");
    $btnText.text(t("rejecting"));

    $.ajax({
      url: study_vars.ajax_url,
      method: "POST",
      data: {
        action: "reject_study",
        study_idno: studyIdno,
        study_title: studyTitle,
        _ajax_nonce: study_vars.nonce,
      },
      success: function (response) {
        if (response.success) {
          showToast("Étude rejetée avec succès !");
          loadStudies();
        }
      },
      complete: function () {
        $("#rejectStudyModal").hide();
        $spinner.addClass("d-none");
        studyIdno = null;
        $btnText.text(lang === "fr" ? "Confirmer" : "Confirm");
      },
    });
  });

  $(".cancel-reject-study").on("click", function () {
    $("#rejectStudyModal").hide();
    studyIdno = null;
  });

  // --- ANNULATION ---
  $(document).on("click", ".cancel-study-btn-nada", function (e) {
    e.preventDefault();
    studyIdno = $(this).data("study-idno");
    studyTitle = $(this).data("study-title");
    $("#cancelStudyModal .modal-body .study-title").text(studyTitle);
    $("#cancelStudyModal").show();
  });

  $(".confirm-cancel-study").on("click", function () {
    if (!studyIdno) return;
    const $btn = $(this);
    const $spinner = $btn.find(".spinner-border");
    const $btnText = $btn.find(".btn-text");
    $spinner.removeClass("d-none");
    $btnText.text(t("Cancel"));

    $.ajax({
      url: study_vars.ajax_url,
      method: "POST",
      data: {
        action: "cancel_study",
        study_idno: studyIdno,
        study_title: studyTitle,
        _ajax_nonce: study_vars.nonce,
      },
      success: function (response) {
        if (response.success) {
          showToast("Étude annulée avec succès !");
          loadStudies();
        }
      },
      complete: function () {
        $("#cancelStudyModal").hide();
        $spinner.addClass("d-none");
        studyIdno = null;
        $btnText.text(lang === "fr" ? "Confirmer" : "Confirm");
      },
    });
  });

  function showToast(message, type = "success") {
    const toastEl = document.getElementById("study-action-toast");
    const toastBody = document.getElementById("study-action-toast-body");

    if (!toastEl || !toastBody) return;
    toastBody.textContent = message;
    toastEl.classList.remove("text-bg-success", "text-bg-danger");

    if (type === "success") {
      toastEl.classList.add("text-bg-success");
    } else if (type === "error") {
      toastEl.classList.add("text-bg-danger");
    }

    if (typeof bootstrap !== "undefined") {
      const bsToast = new bootstrap.Toast(toastEl);
      bsToast.show();
    }
  }
});

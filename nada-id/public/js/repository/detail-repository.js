(function ($) {
  "use strict";
  const lang = management_repository_vars.lang ?? "fr";
  // Champs cachés en mode édition
  const HIDDEN_IN_EDIT = [
    "uri_esv",
    "uri_mesh",
    "identifier",
    "status",
    "uri",
    "siren",
  ];
  const READONLY_IN_EDIT = ["label_fr", "label_en"];

  function $field(id) {
    return $("#addEditItemModal").find("#" + id);
  }

  //  Visibilité des champs selon le mode
  function toggleFields(isEditMode) {
    HIDDEN_IN_EDIT.forEach(function (id) {
      $field(id).closest(".mb-3, .col-md-6").toggle(!isEditMode);
    });

    READONLY_IN_EDIT.forEach(function (id) {
      $field(id).prop("readonly", isEditMode).prop("required", !isEditMode);
    });
  }

  function resetForm() {
    let fields = [
      "item-id",
      "referentiel_id",
      "label_fr",
      "label_en",
      "desc_fr",
      "desc_en",
      "uri",
      "uri_esv",
      "uri_mesh",
      "identifier",
      "siren",
      "status",
    ];

    fields.forEach(function (id) {
      $field(id).val("");
    });

    $field("item-id").val(0);
    $field("referentiel_id").val(0);
    $("#addEditItemModal").find(".form-control").removeClass("is-invalid");
    hideError();
  }

  function openModal(title) {
    $("#form-content").show();
    $("#loader-add-edit-ref").hide();
    $("#success-message-ref").hide();

    $("#addEditItemModal").find("#addEditItemLabel").text(title);
    $("#addEditItemModal").show();
  }

  function showError(msg) {
    $("#addEditItemModal")
      .find("#item-form-error")
      .html(msg)
      .removeClass("d-none");
  }

  function hideError() {
    $("#addEditItemModal").find("#item-form-error").addClass("d-none").html("");
  }

  var currentAction = "add";
  var currentItemId = 0;
  var wrapper;

  function loadItemsTable(paged) {
    var referentielId = wrapper.data("referentiel-id");
    var contentDiv = wrapper.find(".nada-table-items-content");
    contentDiv.css("opacity", "0.5");
    $.ajax({
      url: management_repository_vars.ajax_url,
      type: "POST",
      data: {
        action: "nada_fetch_table_ref_items",
        ref_id: referentielId,
        term: wrapper.find(".dt-search-input").val() || "",
        paged: paged,
        per_page: wrapper.find(".dt-per-page-select").val() || "10",
      },
      success: function (response) {
        contentDiv.html(response).css("opacity", "1");
        contentDiv.find(".search-box").remove();
        contentDiv
          .find("#add-edit-item-btn")
          .attr("data-ref-id", referentielId);
      },
      error: function () {
        contentDiv.css("opacity", "1");
      },
    });
  }

  $(document).ready(function () {
    wrapper = $(".repository-items-wrapper");
    if (!wrapper.length) return;
    $(document).on("click", ".repository-search-button", function (e) {
      e.preventDefault();
      loadItemsTable(1);
    });

    $(document).on("keypress", ".dt-search-input", function (e) {
      if (e.which !== 13) return;
      e.preventDefault();
      loadItemsTable(1);
    });

    $(document).on("change", ".dt-per-page-select", function () {
      loadItemsTable(1);
    });
    $(document).on("click", ".pagination-links a", function (e) {
      e.preventDefault();
      var match = $(this)
        .attr("href")
        .match(/paged=(\d+)/);
      loadItemsTable(match ? parseInt(match[1], 10) : 1);
    });

    $(document).on("click", "#add-edit-item-btn", function () {
      currentAction = "add";
      currentItemId = 0;
      resetForm();
      toggleFields(false);
      $field("referentiel_id").val(wrapper.data("referentiel-id") || 0);
      openModal(lang === "fr" ? "Ajouter un nouvel élément" : "Add a new item");
    });

    $(document).on("click", ".nada-edit", function (e) {
      e.stopPropagation();

      currentAction = "edit";
      currentItemId = $(this).data("id");

      hideError();
      $("#addEditItemModal").find(".form-control").removeClass("is-invalid");

      // Remplir les champs avec les données de la ligne
      $field("item-id").val(currentItemId);
      $field("referentiel_id").val($(this).data("referentiel-id") || "");
      $field("label_fr").val($(this).data("label-fr") || "");
      $field("label_en").val($(this).data("label-en") || "");
      $field("desc_fr").val($(this).data("desc-fr") || "");
      $field("desc_en").val($(this).data("desc-en") || "");
      $field("uri").val($(this).data("uri") || "");
      $field("uri_esv").val($(this).data("uri-esv") || "");
      $field("uri_mesh").val($(this).data("uri-mesh") || "");
      $field("identifier").val($(this).data("identifier") || "");
      $field("siren").val($(this).data("siren") || "");
      $field("status").val($(this).data("status") || "");

      toggleFields(true);
      openModal(lang === "fr" ? "Modifier l'élément" : "Edit item");
    });

    // Annuler / Fermer le modal
    $(document).on(
      "click",
      ".cancel-action, #addEditItemModal .nada-modal-close",
      function (e) {
        e.preventDefault();
        $("#addEditItemModal").fadeOut(300);
        $("body").removeClass("nada-modal-open");

        setTimeout(function () {
          $("#form-content").show();
          $("#loader-add-edit-ref").hide();
          $("#success-message-ref").hide();
          resetForm();
        }, 300);
      },
    );

    $(document).on("click", ".confirm-add-edit-item", function () {
      var $btn = $(this);
      var $spinner = $btn.find(".spinner-border");
      var $btnText = $btn.find(".btn-text");
      hideError();
      if (currentAction === "add") {
        if (
          !$field("label_fr").val().trim() ||
          !$field("label_en").val().trim()
        ) {
          showError(
            lang === "fr"
              ? "Les labels FR et EN sont obligatoires"
              : "FR and EN labels are required",
          );
          return;
        }
      }

      // Afficher loader
      $("#form-content").hide();
      $("#loader-add-edit-ref").show();
      $btn.prop("disabled", true);
      $spinner.removeClass("d-none");

      $.ajax({
        url: management_repository_items_vars.ajax_url,
        type: "POST",
        data: {
          action: "nada_save_referentiel_item",
          item_id: $field("item-id").val(),
          ref_id: $field("referentiel_id").val(),
          label_fr: $field("label_fr").val(),
          label_en: $field("label_en").val(),
          desc_fr: $field("desc_fr").val(),
          desc_en: $field("desc_en").val(),
          uri: $field("uri").val(),
          uri_esv: $field("uri_esv").val(),
          uri_mesh: $field("uri_mesh").val(),
          identifier: $field("identifier").val(),
          siren: $field("siren").val(),
          status: $field("status").val(),
          nonce: management_repository_items_vars.nonce,
        },

        success: function (response) {
          $("#loader-add-edit-ref").hide();
          if (response.success) {
            $(".message-success-response").html(response.data.message);
            $("#success-message-ref").show();
            setTimeout(function () {
              location.reload();
            }, 2000);
          } else {
            $("#form-content").show();
            showError(response.data.message);
            $btn.prop("disabled", false);
            $spinner.addClass("d-none");
            $btnText.text(lang === "fr" ? "Enregistrer" : "Save");
          }
        },
        error: function (xhr, status, error) {
          $("#loader-add-edit-ref").hide();
          $("#form-content").show();
          showError(lang === "fr" ? "Erreur : " + error : "Error: " + error);
          $btn.prop("disabled", false);
          $spinner.addClass("d-none");
          $btnText.text(lang === "fr" ? "Enregistrer" : "Save");
        },
      });
    });
  });
})(jQuery);

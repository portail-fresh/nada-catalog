jQuery(document).ready(function ($) {
  // Toggle main sections
  $(".technical-documentation-nav-toggle").on("click", function (e) {
    e.preventDefault();
    var $this = $(this);
    var $subsection = $this.next(".technical-documentation-nav-subsection");
    var $arrow = $this.find(".technical-documentation-arrow");

    if ($subsection.is(":visible")) {
      $subsection.slideUp(200, function () {
        $subsection.removeClass("show");
        $arrow.removeClass("fa-angle-down").addClass("fa-angle-right");
      });
    } else {
      $subsection.slideDown(200, function () {
        $subsection.addClass("show");

        $arrow.removeClass("fa-angle-right").addClass("fa-angle-down");
      });
    }
  });

  // Toggle sections header
  $(".technical-documentation-sections-toggle").on("click", function (e) {
    e.preventDefault();
    var $this = $(this);
    var $list = $this
      .closest(".technical-documentation-sections-header")
      .next(".technical-documentation-sections-list");
    var $arrow = $this.find(".technical-documentation-arrow");

    if ($list.is(":visible")) {
      $list.slideUp(200, function () {
        $list.removeClass("show");
        $arrow.removeClass("fa-angle-down").addClass("fa-angle-right");
      });
    } else {
      $list.slideDown(200, function () {
        $list.addClass("show");
        $arrow.removeClass("fa-angle-right").addClass("fa-angle-down");
      });
    }
  });

  // toggle sidebar menu 
  const toggleBtn = $(".toggleSidebar");
  const container = $(".technical-documentation-container");
  const mainContent = $(".technical-documentation-col-9");
  const sideBar = $(".technical-documentation-col-3");

  toggleBtn.on("click", () => {
    if (container.hasClass("closedSideBar")) {
      container.removeClass("closedSideBar");
      mainContent.removeClass("col-11").addClass("col-9");
      sideBar.removeClass("col-1").addClass("col-3");
    } else {
      container.addClass("closedSideBar");
      mainContent.removeClass("col-9").addClass("col-11");
      sideBar.removeClass("col-3").addClass("col-1");
    }
  });

  // afficher breadcrumb 
  const breadcrumbTxt = jQuery(".technical-documentation-breadcrumb");
  const schemaTitle = jQuery(".technical-documentation-nav-toggle.active span")
    .first()
    .text()
    .trim();
  const sectionsTitle = jQuery(".technical-documentation-sections-toggle.active span")
    .first()
    .text()
    .trim();
  const activeSection = jQuery(".technical-documentation-sections-list .technical-documentation-nav-link.active")
    .first()
    .text()
    .trim();

  let breadcrumb = "";

  if (schemaTitle) {
    breadcrumb += schemaTitle;
  }

  if (sectionsTitle && activeSection) {
    breadcrumb += " > " + sectionsTitle;
  }

  if (activeSection) {
    breadcrumb += " > " + activeSection;
  }

  breadcrumbTxt.text(breadcrumb);

  // Toggle vocabulary subsections
  $(".technical-documentation-subsection-toggle").on("click", function (e) {
    e.preventDefault();
    var $this = $(this);
    var $items = $this.next(".technical-documentation-subsection-items");
    var $arrow = $this.find(".technical-documentation-arrow");

    if ($items.is(":visible")) {
      $items.slideUp(200, function () {
        $items.removeClass("show");
        $this.removeClass("active");
        $arrow.html('<i class="fa fa-angle-right"></i>');
      });
    } else {
      $items.slideDown(200, function () {
        $items.addClass("show");
        $this.addClass("active");
        $arrow.html('<i class="fa fa-angle-down"></i>');
      });
    }
  });
  const lang = variable_dictionary_vars.lang;
  const $modal = $("#editVDModal");
  const $form = $("#vd-form");
  const $errorDiv = $("#vd-form-error");

  // Ouvrir le modal d'édition
  $(document).on("click", ".btn-edit-vd-field", function () {
    const $btn = $(this);

    $("#field-id").val($btn.data("id"));
    $("#meta_label_fr").val($btn.data("label-fr") || "");
    $("#meta_label_en").val($btn.data("label-en") || "");
    $("#meta_desc_fr").val($btn.data("desc-fr") || "");
    $("#meta_desc_en").val($btn.data("desc-en") || "");

    $errorDiv.addClass("d-none").text("");
    $modal.addClass("show");
  });

  // Fermer le modal
  $(".cancel-edit-vd-field").on("click", function () {
    $modal.removeClass("show");
    $form[0].reset();
    $errorDiv.addClass("d-none").text("");
  });

  // Clic sur confirmer l'édition des métadonnées
  $(".confirm-edit-vd-field").on("click", function () {
    const $button = $(".confirm-edit-vd-field");
    const $btnText = $button.find(".btn-text");
    const $spinner = $button.find(".spinner-border");
    clear();

    // Afficher le spinner et désactiver le bouton
    $button.prop("disabled", true);
    $btnText.text(lang === "fr" ? "Enregistrement..." : "Saving...");
    $spinner.removeClass("d-none");

    // Préparer les données du formulaire
    const formData = {
      action: "nada_update_vd_description_field",
      field_id: $("#field-id").val(),
      description_fr: $("#meta_desc_fr").val(),
      description_en: $("#meta_desc_en").val(),
      nonce: variable_dictionary_vars.nonce,
    };

    // Appel AJAX
    $.ajax({
      url: variable_dictionary_vars.ajax_url,
      type: "POST",
      data: formData,
      success: function (response) {
        if (response.success) {
          location.reload();
        } else {
          show(response.data.message);
        }
      },
      error: function (xhr, status, error) {
        show(
          lang === "fr"
            ? "Erreur lors de l'enregistrement : " + error
            : "Error saving: " + error
        );
      },
      complete: function () {
        // Réactiver le bouton
        $button.prop("disabled", false);
        $btnText.text(lang === "fr" ? "Confirmer" : "Save");
        $spinner.addClass("d-none");
      },
    });
  });

  // Afficher un message d'erreur
  function show(message) {
    $errorDiv.removeClass("d-none").html(message);
  }
  // Effacer le message d'erreur
  function clear() {
    $errorDiv.addClass("d-none").html("");
  }
  // Fermer en cliquant à l'extérieur
  $(window).on("click", function (e) {
    if ($(e.target).is("#editVDModal")) {
      $modal.removeClass("show");
      $form[0].reset();
      $errorDiv.addClass("d-none").text("");
    }
  });
});

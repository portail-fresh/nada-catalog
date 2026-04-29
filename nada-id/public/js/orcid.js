jQuery(document).ready(function ($) {
  let lang = orcid_vars?.lang || "fr";

  $(document).on("click", ".nav-item.langue .nav-link", function (e) {
    e.preventDefault();
    lang = $(this).data("lang");
  });

  // Fonction de vérification ORCID
  async function checkOrcidValidity(orcid) {
    try {
      const response = await fetch(
        `${orcid_vars.ajax_url}?action=check_orcid&orcid=${encodeURIComponent(orcid)}&_=${Date.now()}`,
      );

      if (!response.ok) {
        throw new Error("Erreur réseau");
      }
      const data = await response.json();
      return data;
    } catch (error) {
      return { valid: false, error: true };
    }
  }

  // Fonction pour supprimer tous les messages de feedback
  function clearFeedbackMessages($field) {
    $field
      .closest(".lang-input")
      .find(".invalid-feedback, .valid-feedback, .text-muted")
      .remove();
  }

  // Fonction pour obtenir les messages selon la langue
  function getMessages() {
    return {
      verifying:
        lang === "fr"
          ? "Vérification en cours..."
          : "Verification in progress...",
      validOrcid:
        lang === "fr" ? "Identifiant ORCID valide" : "Valid ORCID identifier",
      invalidOrcid:
        lang === "fr"
          ? "Identifiant ORCID non valide. Veuillez vérifier votre saisie."
          : "Invalid ORCID identifier. Please check your entry.",
      formatExample: "0000-0002-1825-0097 // 0000-0002-1825-009X",
    };
  }

  $(document).on("input", 'input[data-validator="orcid"]', function (e) {
    const $field = $(this);

    // Ne rien faire si le champ est désactivé (en cours de vérification)
    if ($field.prop("disabled")) {
      return;
    }

    let value = $field.val().replace(/[^0-9Xx]/g, "");

    let formatted = "";
    for (let i = 0; i < value.length; i++) {
      if (i > 0 && i % 4 === 0 && i < 16) formatted += "-";
      formatted += value[i];
    }

    formatted = formatted.substring(0, 19);
    $field.val(formatted);

    const orcidRegex = /^\d{4}-\d{4}-\d{4}-\d{3}[\dXx]$/;
    const messages = getMessages();
    const $wrapper = $field.closest(".lang-input");

    clearFeedbackMessages($field);

    // Si le champ est vide
    if (formatted === "") {
      $field.removeClass("is-invalid is-valid");
      return;
    }

    // Si le format est incomplet ou incorrect
    if (!orcidRegex.test(formatted)) {
      $field.removeClass("is-valid").addClass("is-invalid");
      $wrapper.append(
        `<div class="invalid-feedback orcid-error" style="display:block">${messages.formatExample}</div>`,
      );

      return;
    }

    // Si le format est correct, déclencher la vérification
    if (formatted.length === 19) {
      verifyOrcidWithAPI($field, formatted);
    }
  });

  // Fonction pour vérifier l'ORCID via l'API
  async function verifyOrcidWithAPI($field, orcid) {
    const messages = getMessages();
    clearFeedbackMessages($field);

    // Désactiver le champ pendant la vérification
    $field.prop("disabled", true);
    $field.removeClass("is-invalid is-valid");
    // Afficher le message de chargement
    $field.after(`<div class="text-muted small">${messages.verifying}</div>`);
    // Appeler l'API
    const result = await checkOrcidValidity(orcid);
    const $wrapper = $field.closest(".lang-input");

    // Supprimer le message de chargement
    clearFeedbackMessages($field);
    // Réactiver le champ
    $field.prop("disabled", false);
    $field.focus();

    if (result.valid) {
      // ORCID valide et trouvé
      $field.removeClass("is-invalid").addClass("is-valid");
    } else {
      // ORCID non valide ou non trouvé
      $field.removeClass("is-valid").addClass("is-invalid");
      $wrapper.append(
        `<div class="invalid-feedback orcid-error" style="display:block">${messages.invalidOrcid}</div>`,
      );
    }
  }

  //Gérer le blur pour les champs qui n'ont pas encore été vérifiés
  $(document).on("blur", 'input[data-validator="orcid"]', function () {
    const $field = $(this);
    const orcid = $field.val().trim();
    const orcidRegex = /^\d{4}-\d{4}-\d{4}-\d{3}[\dXx]$/;

    // Si le champ est vide, supprimer les messages
    if (orcid === "") {
      clearFeedbackMessages($field);
      $field.removeClass("is-invalid is-valid");
      return;
    }

    // Si le format est correct mais pas encore vérifié
    if (
      orcidRegex.test(orcid) &&
      !$field.hasClass("is-valid") &&
      !$field.hasClass("is-invalid") &&
      !$field.prop("disabled")
    ) {
      verifyOrcidWithAPI($field, orcid);
    }
  });
});

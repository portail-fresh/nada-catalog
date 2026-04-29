// =========  validation.js - Règles de validation =========//
/**
 * Toutes les règles de validation des champs
 * Affichage des messages d’erreur
 * Dépend de la langue
 */

export function validationForm() {
  jQuery(document).ready(function ($) {
    jQuery(document).on("keydown", "textarea", function (e) {
      const $t = $(this);

      // Autoriser si le textarea ou un parent a la classe .allow-enter
      if (
        $t.closest(".allow-enter").length > 0 ||
        $t.is(".allow-enter, [data-allow-enter='true']")
      ) {
        return; // autorisé
      }

      // Sinon, bloquer Enter
      if (e.key === "Enter") {
        e.preventDefault();
      }
    });

    // Prevent accidental tab change when pressing Enter in an input
    jQuery("#form-add-study input").on("keypress", function (e) {
      if (e.which === 13) {
        e.preventDefault();
        return false;
      }
    });

    // Prevent form submission when pressing Enter anywhere except in textareas
    jQuery("#form-add-study").on("keydown", "input", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        return false;
      }
    });
  });
}

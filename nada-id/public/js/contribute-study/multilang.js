// =========== multilang.js - Synchronisation multilingue et tabs =========== //
/**
 *  Gestion des champs multilingues : La synchronisation des champs multilingues (inputs, radios, checkboxes, selects).
 *  (attaché une seule fois pour tout le document)
 */

export function multilangJs() {
  jQuery(document).ready(function ($) {
    /** ==========================
     *  Gestion des champs multilingues
     * ========================== */
    $("body").on(
      "change",
      ".lang-input input[type=radio], .lang-input input[type=text]",
      function (e, triggeredBySync) {
        if (triggeredBySync) return;

        const $field = $(this);
        const $container = $field.closest(".repeater-item, .lang-input");
        const lng = $container.attr("attr-lng");
        const otherLng = lng === "fr" ? "en" : "fr";

        const baseName = $field.attr("name").replace(/_(fr|en)(\[\])?$/, "");
        const selector = `.lang-input[attr-lng="${otherLng}"] [name="${baseName}_${otherLng}${$field.prop("multiple") ? "[]" : ""}"]`;
        const $other = $container.parent().find(selector);

        if ($other.length) {
          if ($field.is("select")) {
            //$other.val($field.val()).trigger("change", [true]);
          } else if ($field.is("input[type=radio]")) {
            const val = $field.val();
            const $other = $(selector + `[value="${val}"]`);
            if ($other.length) {
              $other.prop("checked", true).trigger("change", [true]);
            }
          } else {
            $other.val($field.val());
          }
        }
      },
    );

    //checkbox : on va travailler avec data-id.
    $("body").on(
      "change",
      ".input-group-prefix.lang-input input[type=checkbox]",
      function (e, triggeredBySync) {
        if (triggeredBySync) return;

        let $other = "";
        const $checkbox = $(this);
        const dataId = $checkbox.data("id");

        const $container = $checkbox.closest(".input-group-prefix.lang-input");
        const lng = $container.attr("attr-lng");
        const otherLng = lng === "fr" ? "en" : "fr";

        // trouver le container de l'autre langue
        const $otherContainer = $container.siblings(
          `.input-group-prefix.lang-input[attr-lng="${otherLng}"]`,
        );
        const $otherCheckboxes = $otherContainer.find("input[type=checkbox]");

        // trouver toutes les checkbox du même groupe (FR ou EN)
        if (!dataId) {
          const $all = $container.find("input[type=checkbox]");
          const index = $all.index($checkbox); // position de la checkbox sélectionnée
          $other = $otherCheckboxes.eq(index); // checkbox à la même position
        } else {
          // checkbox correspondante via data-id
          $other = $otherContainer.find(
            `input[type=checkbox][data-id="${dataId}"]`,
          );
        }

        if ($other.length) {
          $other
            .prop("checked", $checkbox.prop("checked"))
            .trigger("change", [true]);
        }
      },
    );

    $("body").on("change", ".lang-input select", function (e, triggeredBySync) {
      if (triggeredBySync) return;

      const $select = $(this);
      const $container = $select.closest(".lang-input");
      const lng = $container.attr("attr-lng");
      const otherLng = lng === "fr" ? "en" : "fr";

      // Chercher le container de l'autre langue dans le même parent global
      const $parent = $container.closest(".input-group"); // ou un parent commun adapté
      const $otherContainer = $parent.find(
        `.lang-input[attr-lng="${otherLng}"]`,
      );
      const $otherSelects = $otherContainer.find("select");

      // Position du select dans le container
      const index = $container.find("select").index($select);
      const $otherSelect = $otherSelects.eq(index);

      if ($otherSelect.length) {
        if ($select.prop("multiple")) {
          const selectedValues = Array.from($select[0].selectedOptions).map(
            (opt) => opt.value,
          );
          $otherSelect.val(selectedValues).trigger("change", [true]);
        } else {
          const selectedIndex = $select.prop("selectedIndex");
          if (selectedIndex >= 0) {
            $otherSelect
              .prop("selectedIndex", selectedIndex)
              .trigger("change", [true]);
          }
        }
      }
    });

    // Synchronisation radios multilangues par position
    $("body").on(
      "change",
      ".lang-input input[type=radio]",
      function (e, triggeredBySync) {
        if (triggeredBySync) return;

        const $radio = $(this);
        const $container = $radio.closest(".lang-input");
        const lng = $container.attr("attr-lng");
        const otherLng = lng === "fr" ? "en" : "fr";

        // trouver le container de l'autre langue
        const $otherContainer = $container
          .closest(".input-group")
          .find(`.lang-input[attr-lng="${otherLng}"]`);
        if (!$otherContainer.length) return;

        // récupérer toutes les radios dans le même container
        const $allRadios = $container.find("input[type=radio]");
        const index = $allRadios.index($radio);

        // récupérer la radio correspondante à la même position
        const $otherRadios = $otherContainer.find("input[type=radio]");
        const $otherRadio = $otherRadios.eq(index);

        if ($otherRadio.length) {
          $otherRadio
            .prop("checked", $radio.prop("checked"))
            .trigger("change", [true]);
        }
      },
    );
  });
}

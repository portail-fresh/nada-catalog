export function cim11() {
  jQuery(document).ready(function ($) {
    // btn modal CIM 11
    $(document)
      .off("click", ".save-show-modal-cim")
      .on("click", ".save-show-modal-cim", function () {
        // Récupérer toutes les cases cochées
        const checkedItems = $("input[name='titles_CIM[]']:checked")
          .map(function () {
            return {
              id: $(this).val(), // L'URL ICD
              title: $(this).data("value"), // Le titre FR
            };
          })
          .get();

        const lang = $("input[name='langueSearchCIM']").val() || "fr";
        $.ajax({
          url: ajaxurl, // WordPress fournit cette variable automatiquement si localisée
          type: "POST",
          dataType: "json",
          data: {
            action: "get_icd_entities",
            entities: checkedItems,
            lang: lang,
          },
          success: function (response) {
            const $selectFR = $(
              "select[name='stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11_fr[]']",
            );
            const $selectEN = $(
              "select[name='stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11_en[]']",
            );
            response.data.forEach((item) => {
              // Exemple d’affichage FR (et EN entre parenthèses)
              const textFR = item.title_fr;
              const textEN = item.title_en;

              if ($selectFR.find(`option[value='${item.id}']`).length === 0) {
                const cleanTextFR = textFR.replace(/\\'/g, "'");
                const optionFR = new Option(cleanTextFR, item.id, false, true);
                $selectFR.append(optionFR);
              }

              if ($selectEN.find(`option[value='${item.id}']`).length === 0) {
                const optionEN = new Option(textEN, item.id, false, false);
                $selectEN.append(optionEN);
              }

              $("#detailCIM").modal("hide");

              $selectFR.trigger("change");
              $selectEN.trigger("change");
            });
          },
          error: function (xhr) {
            console.error("Erreur AJAX :", xhr.responseText);
          },
        });
      });

    $("#detailCIM").on("hidden.bs.modal", function () {
      // Réinitialiser le champ de recherche
      $("input[name='searchCIM']").val("");

      // Vider la zone des résultats
      $("#listPathologies").html(`
          <div class="text-center my-4">
              <span class="dashicons dashicons-info" style="font-size:32px; color:#555; margin-bottom:15px;"></span>
              <p class="mt-2 mb-0">Lancer la recherche en saisissant un ou plusieurs mot-clés.</p>
          </div>
      `);
    });

    let typingTimer;
    const typingDelay = 600; // temps en ms après la dernière frappe

    $(document)
      .off("input", "input[name='searchCIM']")
      .on("input", "input[name='searchCIM']", function () {
        clearTimeout(typingTimer);

        const value = $(this).val().trim();
        const lang = $("input[name='langueSearchCIM']").val() || "fr";

        // relance le timer à chaque frappe
        typingTimer = setTimeout(function () {
          // Texte par défaut selon la langue
          let minCharMessage =
            lang === "en"
              ? "Start the search by entering one or more keywords."
              : "Lancer la recherche en saisissant un ou plusieurs mot-clés.";

          if (value.length === 0 || value.length < 3) {
            // Message pour input vide ou < 3 caractères
            $("#listPathologies").html(`
          <div class="text-center my-4">
            <span class="dashicons dashicons-info" style="font-size:32px; color:#555; margin-bottom:15px;"></span>
            <p class="mt-2 mb-0">${minCharMessage}</p>
          </div>
        `);
            return;
          }

          // Afficher loader
          let searchingMessage =
            lang === "en" ? "Searching..." : "Recherche en cours...";
          $("#listPathologies").html(`
        <div class="text-center my-4">
          <span class="dashicons dashicons-update spin" style="font-size:32px; color:#0073aa; margin-bottom:10px;"></span>
          <p class="mt-2 mb-0">${searchingMessage}</p>
        </div>
      `);

          // Requête AJAX
          $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            data: {
              action: "get_search_list_cim",
              searchInput: value,
              lang: lang, // envoi de la langue côté serveur
            },
            success: function (response) {
              let html = "";
              response.data.destinationEntities.forEach((item) => {
                const title = item.title
                  ? $("<div>").html(item.title).text().trim()
                  : "";
                const stemId = item.stemId
                  ? $("<div>").text(item.stemId).html()
                  : "";
                if (!title) return;

                html += `
              <div class="item_cim">
                <div class="content-item-cim">
                  <div class="item_cim_checkbox">
                    <input type="checkbox" name="titles_CIM[]" data-value="${title}" value="${stemId}">
                  </div>
                  <div class="item_cim_label">${title}</div>
                </div>
              </div>
            `;
              });

              if (!html) {
                let noItemMessage =
                  lang === "en" ? "No items found" : "Aucun élément trouvé";

                html = `
              <div class="text-center my-4">
                <span class="dashicons dashicons-info-outline" style="font-size:32px; color: red; margin-bottom: 15px;"></span>
                <p class="mt-2 mb-0">${noItemMessage}</p>
              </div>
            `;
              }

              $("#listPathologies").html(html);
            },
            error: function () {
              let errorMessage =
                lang === "en" ? "Loading error" : "Erreur de chargement";

              $("#listPathologies").html(`
            <div class="text-center my-4">
              <span class="dashicons dashicons-dismiss" style="font-size:32px; color:red;"></span>
              <p class="mt-2 mb-0">${errorMessage}</p>
            </div>
          `);
            },
          });
        }, typingDelay);
      });
  });
}

/**Creation des optons des Cim-11 */
export function createOptionCim11(topics, lang) {
  const selectName = `stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11_${lang}[]`;
  const $select = jQuery(`select[name='${selectName}']`);

  if ($select.length === 0) {
    return;
  }

  topics.forEach((item) => {
    let uri = "";
    let text = "";

    // Cas 1: extLink est un tableau
    if (Array.isArray(item.extLink)) {
      // Prendre le premier élément du tableau
      uri = item.extLink[0]?.uri || "";
      text = item.topic || "";
    }
    // Cas 2: extLink est un objet
    else if (item.extLink && typeof item.extLink === "object") {
      uri = item.extLink.uri || "";
      text = item.topic || "";
    }
    // Cas 3: Pas d'extLink (fallback)
    else {
      text = item.topic || "";
      uri = ""; // Pas d'URI disponible
    }

    // Ajouter l'option si URI et texte sont valides
    if (uri && text) {
      // Vérifier si l'option n'existe pas déjà
      if ($select.find(`option[value='${uri}']`).length === 0) {
        $select.append(new Option(text, uri, false, true));
      }
    }
  });
  // Déclencher l'événement change pour mettre à jour Select2 ou autres plugins
  $select.trigger("change");

  const $selPatho_fr = jQuery(
    'select[name="stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11_fr[]"]',
  );
  if ($selPatho_fr.length) {
    if (!$selPatho_fr.val() || $selPatho_fr.val().length === 0) {
      $selPatho_fr.find('option[value="CIM-11"]').prop("selected", true);
      $selPatho_fr.trigger("change");
    }
  }

  const $selPatho_en = jQuery(
    'select[name="stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11_en[]"]',
  );
  if ($selPatho_en.length) {
    if (!$selPatho_en.val() || $selPatho_en.val().length === 0) {
      $selPatho_en.find('option[value="CIM-11"]').prop("selected", true);
      $selPatho_en.trigger("change");
    }
  }
}

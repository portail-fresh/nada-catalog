// ======== utils.js - fonctions spécifiques======== /
/**
 * Fonctions spécifique ou liée à un contexte précis
 ** appels API, formatage spécifique, logique métier **
 */

export function helpersJs() {
  jQuery(document).ready(function ($) {
    $("#nextBtn, #prevBtn").on("click", function () {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });

    // drawer tooltip
    $(document).on("click", ".info-bulle", function (e) {
      const drawerEl = document.getElementById("basicDrawer");
      const drawer = new bootstrap.Offcanvas(drawerEl);

      const lang = $(this).attr("attr-lng") || "fr";

      const translations = {
        fr: {
          field_name: "Nom du champ",
          possible_values: "Valeurs possibles",
          vc_title: "Voir la définition des valeurs possibles",
        },
        en: {
          field_name: "Field name",
          possible_values: "Possible values",
          vc_title: "View possible values definitions",
        },
      };

      const baseDictionaryUrl =
        lang === "en"
          ? "/en/catalog-variable-dictionary/"
          : "/dictionnaire-des-variables-du-catalogue/";

      $(".basicDrawerTitle .textlabel").html(
        // translations[lang].field_name + ' <span class="data-url"></span>', // remove url
        translations[lang].field_name,
      );

      $(".basicDrawerVP .textlabel").text(translations[lang].possible_values);

      const options = $(this).data("options");
      const domain = window.location.origin;

      // URL metadata (toujours)
      let idFull = $(this).closest(".tab-pane.show").attr("id");
      let nameReferentiel = idFull?.replace("step-", "");
      const urlMetadata =
        domain + baseDictionaryUrl + "metadata/" + nameReferentiel;

      // URL vocabulary (seulement si options)
      let urlVocabulary = null;
      if (options && Object.keys(options).length > 0) {
        let nameReferentielVC = $(this).closest(".card").parent("div");
        const keyRef = $(nameReferentielVC).data("key");

        if (keyRef) {
          urlVocabulary = domain + baseDictionaryUrl + "vocabulary/" + keyRef;
        }
      }

      if (options && Object.keys(options).length > 0 && urlVocabulary) {
        let html_vb = '<div class="list-vocab">';

        Object.values(options).forEach((item) => {
          const label = item.label ? item.label : item;

          //     html_vb += `
          //   <div class="form-check mb-2 option-item">
          //     <a
          //       href="${urlVocabulary}"
          //       target="_blank"
          //       class="vc-option-link"
          //       title="${translations[lang].vc_title}"
          //     >
          //       ${label}
          //     </a>
          //   </div>
          // `;
          html_vb += `
        <div class="form-check mb-2 option-item">
            ${label}
        </div>
      `;
        });

        html_vb += "</div>";

        $("#basicDrawer .basicDrawerVP").css("display", "block");
        $("#basicDrawer .basicDrawerVP .data-vc").html(html_vb);

        // lien VC au niveau du label "Valeurs possibles"
        // remove redirection to dictionaire page
        // $("#basicDrawer .basicDrawerVP .textlabel").html(
        //   translations[lang].possible_values +
        //     ` <a href="${urlVocabulary}" target="_blank" title="${translations[lang].vc_title}">
        //     <i class="tm-brivona-icon-link me-2 bullet-icon"></i>
        //   </a>`,
        // );
        $("#basicDrawer .basicDrawerVP .textlabel").html(
          translations[lang].possible_values,
        );
      } else {
        $("#basicDrawer .basicDrawerVP").css("display", "none");
      }

      let title = $(this)
        .closest("label")
        .find(".lang-label")
        .clone()
        .children()
        .remove()
        .end()
        .text()
        .trim();

      if (!title) {
        title = $(this)
          .closest(".lang-text-tooltip")
          .find(".contentSection")
          .text();
      }

      // const titleLink =
      //   "<a href='" + urlMetadata + "' target='_blank'>" + title + "</a>";
      const titleLink = title;

      $("#basicDrawer .basicDrawerTitle .data-title").html(titleLink);

      $("#basicDrawer .basicDrawerTitle .data-url").html(
        "<a href='" +
          urlMetadata +
          "' target='_blank'><i class='tm-brivona-icon-link me-2 bullet-icon'></i></a>",
      );

      const description = $(this).attr("data-text");

      let existURLDiv = $(this).closest(".card").parent("div");
      const existURL = $(existURLDiv).attr("data-tooltip-url");

      if (existURL) {
        $("#basicDrawer .basicDrawerDescription .data-description").html(
          "<a target='_blank' href='" +
            description +
            "'>" +
            description +
            "</a>",
        );
      } else {
        $("#basicDrawer .basicDrawerDescription .data-description").html(
          description,
        );
      }
      drawer.show();
    });
  });
}

export function parsePseudoArray(str) {
  if (typeof str !== "string") return str;

  let cleaned = str
    // convertir les délimiteurs de tableau
    .replace(/^\['/, '["')
    .replace(/','/g, '","')
    .replace(/'\]$/, '"]')
    .replace(/\\'/g, "'");

  try {
    return JSON.parse(cleaned);
  } catch (e) {
    return [];
  }
}

export function toggleFranceRegion(selected) {
  let values = [];

  if (Array.isArray(selected)) {
    values = selected;
  } else if (typeof selected === "string" && selected.trim() !== "") {
    values = [selected];
  }
  const hasFrance = values.some((v) => {
    const lower = v.toLowerCase();
    return lower === "france" || lower === "en";
  });

  if (hasFrance) {
    jQuery("#FranceRegion").removeClass("d-none");
  } else {
    resetContainerFields("FranceRegion");
  }
}

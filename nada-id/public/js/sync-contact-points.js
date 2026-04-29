/**
 * Synchronise le repeater ContactPoint avec les données
 * des PrimaryInvestigators et TeamMembers qui ont "isContact" = oui/yes.
 *
 * Comportement :
 * - Pour chaque item PI : si isPIContact === oui/yes -> place ses données dans ContactPoint[index]
 * - Pour chaque item TeamMember : si IsTeamMemberContact === oui/yes -> place ses données dans ContactPoint[index]
 * - Si les deux existent pour le même index, TeamMember écrase PI (modifiable ci-dessous)
 * - On s'assure d'avoir exactement N contact items nécessaires, puis on remplit chaque slot
 * - On ajoute un slot vide supplémentaire si le dernier créé est rempli (anticipation)
 */

function makeContactKey(data) {
  return [data.firstname, data.lastname, data.email, data.rnsr].join("|").toLowerCase().trim();
}

jQuery(document).ready(function ($) {
  let contactSyncInitialized = false;

  let injectedContacts = new Map();

  function initSyncContactPoints(lang = "fr") {
    if (contactSyncInitialized) return;
    contactSyncInitialized = true;

    const addContactBtn = "#add-ContactPoint";

    function readPIData($piItem) {
      return {
        firstname: $piItem.find(`[name='stdyDscr/citation/rspStmt/AuthEnty/firstname_${lang}']`).val() || "",
        lastname: $piItem.find(`[name='stdyDscr/citation/rspStmt/AuthEnty/lastname_${lang}']`).val() || "",
        email: $piItem.find(`[name='additional/primaryInvestigator/piMail_${lang}']`).val() || "",
        affiliation: $piItem.find(`[name='stdyDscr/citation/rspStmt/AuthEnty/affiliation_${lang}']`).val() || "",
        labo: $piItem.find(`[name='additional/primaryInvestigator/piAffiliation/piLabo_${lang}']`).val() || "",
        rnsr: $piItem.find(`[name='stdyDscr/citation/rspStmt/AuthEnty/ExtLink/RNSR_${lang}']`).val() || "",
        dataIns: $piItem.find(`[name='stdyDscr/citation/rspStmt/AuthEnty/institution_json']`).val() || ""
      };
    }

    function readTeamData($teamItem) {
      return {
        firstname: $teamItem.find(`[name='stdyDscr/citation/rspStmt/othId/type_contributor_${lang}']`).val() || "",
        lastname: $teamItem.find(`[name='stdyDscr/citation/rspStmt/othId/type_contributor/lastname_${lang}']`).val() || "",
        email: "",
        affiliation: $teamItem.find(`[name='stdyDscr/citation/rspStmt/othId/affiliation_${lang}']`).val() || "",
        labo: $teamItem.find(`[name='additional/TeamMember/TeamMemberAffiliation/TeamMemberLabo_${lang}']`).val() || "",
        rnsr: $teamItem.find(`[name='stdyDscr/citation/rspStmt/othId/ExtLink/RNSR_${lang}']`).val() || "",
        dataIns: $teamItem.find(`[name='stdyDscr/citation/rspStmt/othId/institution_json']`).val() || ""
      };
    }

    function isAlreadyInjected($item, data) {
      const email = $item.find(`[name='stdyDscr/citation/distStmt/contact/email']`).val() || "";
      const firstname = $item.find(`[name='stdyDscr/citation/distStmt/contact_${lang}']`).val() || "";
      const lastname = $item.find(`[name='stdyDscr/citation/distStmt/contact/lastname_${lang}']`).val() || "";

      return email === data.email && firstname === data.firstname  && lastname === data.lastname;
    }

    function ensureContactCount(count) {
      let current = $("#repeater-ContactPoint .repeater-item").length;
      while (current < count) {
        $(addContactBtn).trigger("click");
        current++;
      }
    }

    function isDomItemEmpty($item) {
      if (!$item || $item.length === 0) return true;

      const name = $item.find(`[name='stdyDscr/citation/distStmt/contact_${lang}']`).val() || "";
      const lastname = $item.find(`[name='stdyDscr/citation/distStmt/contact/lastname_${lang}']`).val() || "";
      const email = $item.find(`[name='stdyDscr/citation/distStmt/contact/email']`).val() || "";
      const aff = $item.find(`[name='stdyDscr/citation/distStmt/contact/affiliation_${lang}']`).val() || "";

      return !(name.trim() || email.trim() || lastname.trim()|| aff.trim());
    }

    function fillContactBlock($contactItem, data) {
      if (!$contactItem || $contactItem.length === 0) return;

      $contactItem
        .find(`[name='stdyDscr/citation/distStmt/contact_${lang}']`)
        .val(data.firstname || "")
        .change();

      $contactItem
        .find(`[name='stdyDscr/citation/distStmt/contact/lastname_${lang}']`)
        .val(data.lastname || "")
        .change();

      if (data.email) {
        $contactItem.find(`[name='stdyDscr/citation/distStmt/contact/email']`).val(data.email).change();
      }

      $contactItem
        .find(`[name='stdyDscr/citation/distStmt/contact/affiliation_${lang}']`)
        .val(data.affiliation || "")
        .change();

      $contactItem.find(`[name='additional/contactPointLabo_${lang}']`).val(data.labo || "");
      $contactItem.find(`[name='stdyDscr/citation/distStmt/contact/ExtLink/RNSR_${lang}']`).val(data.rnsr || "");
      $contactItem.find(`[name='stdyDscr/citation/distStmt/contact/institution_json']`).val(data.dataIns || "");
    }

    function sync() {
      const contacts = [];
      const seen = new Set();

      $("#repeater-PrimaryInvestigator .repeater-item").each(function () {
        const $it = $(this);
        const isContact = ($it.find(`[name='additional/primaryInvestigator/isPIContact_${lang}']`).val() || "").toLowerCase();

        if (isContact === "oui" || isContact === "yes") {
          const data = readPIData($it);
          const key = makeContactKey(data);
          if (!seen.has(key)) {
            seen.add(key);
            
            contacts.push(data);

          }
        }
      });

      $("#repeater-teamMember .repeater-item").each(function () {
        const $it = $(this);
        const isContact = ($it.find(`[name='additional/TeamMember/IsTeamMemberContact_${lang}']`).val() || "").toLowerCase();

        if (isContact === "oui" || isContact === "yes") {
          const data = readTeamData($it);
          const key = makeContactKey(data);

          if (!seen.has(key)) {
            seen.add(key);
            contacts.push(data);
          }
        }
      });

      if (contacts.length === 0) return;

      let $items = $("#repeater-ContactPoint .repeater-item");
      
      function getFirstAvailable(data) {
        for (let i = 0; i < $items.length; i++) {
          const $item = $items.eq(i);

          if (isDomItemEmpty($item) || isAlreadyInjected($item, data)) {
            return $item;
          }
        }
        return null;
      }


      contacts.forEach((data) => {
        let $target = getFirstAvailable(data);
        if (!$target) {
          ensureContactCount($items.length + 1);
          $items = $("#repeater-ContactPoint .repeater-item");
          $target = $items.last();
        }


        fillContactBlock($target, data);

        injectedContacts.set(makeContactKey(data), true);
      });
    }

    // event
    const watchSelectors = [
      `#repeater-PrimaryInvestigator [name^='additional/primaryInvestigator']`,
      `#repeater-teamMember [name^='additional/TeamMember']`
    ].join(", ");

    $(document)
      .off("input change", watchSelectors)
      .on("input change", watchSelectors, function () {
        clearTimeout(globalThis._syncContactTimeout);
        globalThis._syncContactTimeout = setTimeout(sync, 120);
      });

    setTimeout(sync, 300);
  }

  // init language
  const currentLang = $(".nav-item.langue .nav-link.active").data("lang") || "fr";
  initSyncContactPoints(currentLang);

  $(document).on("click", ".nav-item.langue .nav-link", function () {
    const lang = $(this).data("lang") || "fr";
    initSyncContactPoints(lang);
  });
});

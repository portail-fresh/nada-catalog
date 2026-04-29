jQuery(document).ready(function ($) {
  $(".email-study-btn-nada").on("click", function (e) {
    e.preventDefault(); // évite le scroll en haut dû au href="#"
    // Récupérer la valeur de l'attribut data-bs-value
    let email = $(this).attr("data-bs-value");
    // Mettre à jour le contenu du modal
    $("#emailStudyModal .modal-body.study-title").text(email);
    // Ouvrir le modal avec Bootstrap
    $("#emailStudyModal").modal("show");
  });
  $(".cancel-show-modal-email").on("click", function () {
    $("#emailStudyModal").modal("hide");
  });
});

/** CHANGE multilangue */

jQuery(document).ready(function ($) {
  let currentStep = 1;
  const totalSteps = 3;

  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const finishBtn = document.getElementById("finishBtn");
  const steps = [...document.querySelectorAll(".tab-pane")];
  const tabs_section = [...document.querySelectorAll("#stepperTabs .nav-link")];
  // Cliquer sur les tabs
  tabs_section.forEach((tab, i) => {
    tab.addEventListener("click", (e) => {
      e.preventDefault();
      currentStep = i + 1;
      showStep(currentStep);
    });
  });

  showStep(currentStep);

  jQuery("select[ddiShema='stdyDscr/stdyInfo/sumDscr/nation']").select2({
    placeholder: "Rechercher un pays...",
    allowClear: true,
    width: "100%",
  });

  function showStep(step) {
    steps.forEach((s, i) => {
      s.classList.remove("show", "active");
      if (i === step - 1) s.classList.add("show", "active");
    });

    tabs_section.forEach((t, i) => {
      t.classList.remove("active");
      if (i === step - 1) t.classList.add("active");
    });

    prevBtn.disabled = step === 1;
    nextBtn.classList.toggle("d-none", step === totalSteps);
    finishBtn.classList.toggle("d-none", step !== totalSteps);
  }

  prevBtn.addEventListener("click", () => {
    if (currentStep > 1) currentStep--;
    showStep(currentStep);
  });

  nextBtn.addEventListener("click", () => {
    if (currentStep < totalSteps) currentStep++;
    showStep(currentStep);
  });

  // Soumission du formulaire "Ajouter une étude"
  $(document)
    .off("submit", "#form-add-study")
    .on("submit", "#form-add-study", function (e) {
      e.preventDefault(); // bloque le submit classique
      const submitter = e.originalEvent.submitter;

      if (
        !submitter ||
        (submitter.id !== "finishBtn" && submitter.id !== "saveAsDraft")
      ) {
        return;
      }

      // Désactive le bouton
      submitter.disabled = true;

      // Affiche le spinner
      let spinner = submitter.querySelector(".spinner-border");
      if (spinner) spinner.classList.remove("d-none");

      // Change le texte
      let btnText = submitter.querySelector(".btn-text");
      if (btnText) btnText.textContent = "Chargement...";

      const form = e.target;

      const formData = new FormData(form);

      if (submitter.id === "finishBtn") {
        // Ajouter champ spécial si c'est un draft or no
        formData.append("published", 1);
      } else {
        formData.append("published", 0);
      }

      // --- Résultat final (structure de sortie) ---
      const result = {
        fundingAgencies_fr: [],
        fundingAgencies_en: [],

        fundingAgentTypes_fr: [],
        fundingAgentTypes_en: [],

        callModes_fr: [],
        callModes_en: [],

        recrutementTiming_fr: [],
        recrutementTiming_en: [],

        agencies_fr: [],
        agencies_en: [],

        otherAgencies_fr: [],
        otherAgencies_en: [],

        authEntities_fr: [],
        authEntities_en: [],

        othIds_fr: [],
        othIds_en: [],

        distContacts_fr: [],
        distContacts_en: [],

        topics_fr: [],
        topics_en: [],

        interventions_fr: [],
        interventions_en: [],

        researchPurposes_fr: [],
        researchPurposes_en: [],

        trialPhases_fr: [],
        trialPhases_en: [],

        blindedMaskingDetails_fr: [],
        blindedMaskingDetails_en: [],

        inclusionStrategy_fr: [],
        inclusionStrategy_en: [],

        sampProc_fr: [],
        sampProc_en: [],

        unitType_fr: [],
        unitType_en: [],

        level_sex_Clusion_I_fr: [],
        level_sex_Clusion_I_en: [],

        level_age_Clusion_I_fr: [],
        level_age_Clusion_I_en: [],

        nation_fr: [],
        nation_en: [],

        geogCover_fr: [],
        geogCover_en: [],

        subject_followUP_fr: [],
        subject_followUP_en: [],

        sources_fr: [],
        sources_en: [],

        standardsCompliance_fr: [],
        standardsCompliance_en: [],

        otherQualityStatements_fr: [],
        otherQualityStatements_en: [],

        useStmtContacts_fr: [],
        useStmtContacts_en: [],

        idnos_fr: [],
        idnos_en: [],

        dataKind_fr: [],
        dataKind_en: [],

        biobankContent_fr: [],
        biobankContent_en: [],

        inclusionGroups_fr: [],
        inclusionGroups_en: [],

        arms_fr: [],
        arms_en: [],
      };

      // --- Helpers -------------------------------------------------------------

      // Langue détectée dans la clé (fr/en) ; insensible à la casse
      const getLang = (k) => {
        if (/_fr(\[\])?$/i.test(k)) return "fr";
        if (/_en(\[\])?$/i.test(k)) return "en";
        return null;
      };

      // Raccourcis vers tableaux selon langue

      const faList = (lang) =>
        lang === "fr" ? result.fundingAgencies_fr : result.fundingAgencies_en;

      const aeList = (lang) =>
        lang === "fr" ? result.authEntities_fr : result.authEntities_en;

      const fattList = (lang) =>
        lang === "fr"
          ? result.fundingAgentTypes_fr
          : result.fundingAgentTypes_en;

      const callModesList = (lang) =>
        lang === "fr" ? result.callModes_fr : result.callModes_en;

      const agenciesList = (lang) =>
        lang === "fr" ? result.agencies_fr : result.agencies_en;

      const otherAgenciesList = (lang) =>
        lang === "fr" ? result.otherAgencies_fr : result.otherAgencies_en;

      // OTH-IDs
      const othList = (lang) =>
        lang === "fr" ? result.othIds_fr : result.othIds_en;

      // distStmt/contact
      const distList = (lang) =>
        lang === "fr" ? result.distContacts_fr : result.distContacts_en;

      // Ajoute (ou récupère) le dernier financeur créé pour une langue
      const touchLastFunding = (lang) => {
        const list = faList(lang);
        if (list.length === 0) list.push({});
        return list[list.length - 1];
      };

      // Ajoute une entité d’auteur (investigator/affiliation)
      const pushAuthEnt = (lang, name, type) => {
        if (!name) return;
        aeList(lang).push({ name, type });
      };

      // Ajoute/complète l'extlink du dernier auteur
      const touchAuthLastExt = (lang) => {
        const list = aeList(lang);
        if (list.length === 0) {
          // si aucune entité n'existe encore, on en crée une "vide" pour associer l'extlink
          list.push({ name: "", type: "investigator" });
        }
        const last = list[list.length - 1];
        last.extlink = last.extlink || {};
        return last.extlink;
      };

      // ---- Helpers IDno (repeater) ----
      const idnoList = (lang) =>
        lang === "fr" ? result.idnos_fr : result.idnos_en;

      const pushNewIdno = (lang) => {
        if (!lang) return;
        idnoList(lang).push({ uri: "", agentSchema: "", agentOther: "" });
      };

      const touchLastIdno = (lang) => {
        const list = idnoList(lang);
        if (!list.length) pushNewIdno(lang);
        return list[list.length - 1];
      };

      // ---- NEW: per-language cursor for non-indexed repeater ----
      const idnoCursor = { fr: -1, en: -1 };
      const currentIdno = (lang) => idnoList(lang)[idnoCursor[lang]];
      const startNewIdno = (lang) => {
        pushNewIdno(lang);
        idnoCursor[lang]++;
        return currentIdno(lang);
      };

      // ---- Helpers: arms (repeater) ----
      const armsList = (lang) =>
        lang === "fr" ? result.arms_fr : result.arms_en;
      // Ensure an arms item exists at a given index
      const ensureArmAt = (lang, idx) => {
        const list = armsList(lang);
        while (list.length <= idx) {
          list.push({ name: "", type: "", typeOther: "", description: "" });
        }
        return list[idx];
      };
      const pushArm = (lang, name = "") => {
        if (!lang) return;
        armsList(lang).push({
          name,
          type: "",
          typeOther: "",
          description: "",
        });
      };

      const peekLastArm = (lang) => {
        const list = armsList(lang);
        return list.length ? list[list.length - 1] : null;
      };

      const touchLastArm = (lang) => {
        const list = armsList(lang);
        if (!list.length) pushArm(lang, "");
        return list[list.length - 1];
      };

      // ---- Helpers interventions ----
      const interventionsList = (lang) =>
        lang === "fr" ? result.interventions_fr : result.interventions_en;

      const pushNewIntervention = (lang) => {
        if (!lang) return;
        interventionsList(lang).push({
          name: "",
          type: "",
          typeOther: "", // <-- AJOUT
          description: "",
        });
      };

      const touchLastIntervention = (lang) => {
        const list = interventionsList(lang);
        if (!list.length) pushNewIntervention(lang);
        return list[list.length - 1];
      };

      // ---- Helpers SOURCES ----
      const sourcesList = (lang) =>
        lang === "fr" ? result.sources_fr : result.sources_en;

      const pushNewSource = (lang) => {
        if (!lang) return;
        sourcesList(lang).push({
          citation: "",
          srcOrig: [], // checkbox (multiples)
          notes: { subject_sourcePurpose: "" },
          otherSourceType: "", // <-- AJOUT
        });
      };

      const touchLastSource = (lang) => {
        const list = sourcesList(lang);
        if (!list.length) pushNewSource(lang);
        return list[list.length - 1];
      };

      // ---- Helpers: inclusionGroups (indexed repeater) ----
      const inclusionGroupsList = (lang) =>
        lang === "fr" ? result.inclusionGroups_fr : result.inclusionGroups_en;

      const ensureInclusionGroupAt = (lang, idx) => {
        const list = inclusionGroupsList(lang);
        while (list.length <= idx) {
          list.push({ name: "", description: "", interventionExposition: "" });
        }
        return list[idx];
      };

      const peekLastInclusionGroup = (lang) => {
        const list = inclusionGroupsList(lang);
        return list.length ? list[list.length - 1] : null;
      };

      const pushInclusionGroup = (lang, name = "") => {
        if (!lang) return;
        inclusionGroupsList(lang).push({
          name,
          description: "",
          interventionExposition: "",
        });
      };

      const touchLastInclusionGroup = (lang) => {
        const list = inclusionGroupsList(lang);
        if (!list.length) pushInclusionGroup(lang, "");
        return list[list.length - 1];
      };

      // OTH: Créer des entrées
      const pushContributorOth = (lang, name) => {
        if (!lang) return;
        othList(lang).push({
          name: name || "",
          type: "contributor",
          extlinks: [], // plusieurs extlinks
        });
      };

      const pushAffiliationOth = (lang, name) => {
        if (!lang) return;
        othList(lang).push({
          name: name || "",
          type: "affiliation",
          extlink: {}, // un seul extlink
        });
      };

      // OTH: Récupérer le dernier élément d’un type (en crée un placeholder si besoin)
      const touchLastOfType = (lang, type) => {
        const list = othList(lang);
        for (let i = list.length - 1; i >= 0; i--) {
          if (list[i].type === type) return list[i];
        }
        if (type === "contributor") {
          pushContributorOth(lang, "");
        } else {
          pushAffiliationOth(lang, "");
        }
        return othList(lang)[othList(lang).length - 1];
      };

      // Appariement titre/uri pour contributors (liste d’extlinks)
      const upsertExtLink = (arr, field, value) => {
        if (arr.length && !arr[arr.length - 1][field]) {
          arr[arr.length - 1][field] = value;
        } else {
          const o = {};
          o[field] = value;
          arr.push(o);
        }
      };

      // distStmt/contact: créer/pousser
      const pushDistContact = (lang, name) => {
        if (!lang) return;
        distList(lang).push({
          name: name || "",
          type: "contact",
          email: "", // un seul email (non répété)
        });
      };

      const pushDistAffiliation = (lang, name) => {
        if (!lang) return;
        distList(lang).push({
          name: name || "",
          type: "affiliation",
          extlink: {}, // extlink unique
        });
      };

      // distStmt/contact: récupérer dernier élément d’un type
      const touchLastDistOfType = (lang, type) => {
        const list = distList(lang);
        for (let i = list.length - 1; i >= 0; i--) {
          if (list[i].type === type) return list[i];
        }
        if (type === "contact") {
          pushDistContact(lang, "");
        } else {
          pushDistAffiliation(lang, "");
        }
        return distList(lang)[distList(lang).length - 1];
      };

      // ------- TOPICS accumulators (subject/topcclas) -------

      // Groupe A: topcclas[]/value_{fr|en}[] + topcclas[]/vocab
      const topics_main_vals = { fr: [], en: [] };
      let topics_main_vocab = null;

      // Groupe B: topcclas_{fr|en}[] + topcclas/vocab
      const topics_det_vals = { fr: [], en: [] };
      let topics_det_vocab = null;

      // Groupe C (schémas): topcclas[]/value/{scheme}[] + topcclas[]/vocab/{scheme}
      const topics_scheme_vals = {}; // { scheme: [values...] }
      const topics_scheme_vocab = {}; // { scheme: "label" }

      // helper dédup’ avec conservation d’ordre
      const pushUnique = (arr, v) => {
        const s = String(v);
        if (!arr.includes(s)) arr.push(s);
      };

      // Normalise la clé pour des comparaisons robustes
      const norm = (s) => String(s || "").toLowerCase();

      // --- Parcours des champs -------------------------------------------------
      for (const [rawKey, value] of formData.entries()) {
        const key = norm(rawKey);
        const lang = getLang(key); // 'fr' | 'en' | null

        // -------------------- MODES DE COLLECTE (callModes)
        if (key.includes("stdydscr/method/datacoll/collmode_fr[]")) {
          callModesList("fr").push(value);
          continue;
        }
        if (key.includes("stdydscr/method/datacoll/collmode_en[]")) {
          callModesList("en").push(value);
          continue;
        }

        // -------------------- AGENCES "authorizingAgency"
        if (key.includes("stdydscr/studyauthorization/authorizingagency_fr")) {
          agenciesList("fr").push(value);
          continue;
        }
        if (key.includes("stdydscr/studyauthorization/authorizingagency_en")) {
          agenciesList("en").push(value);
          continue;
        }

        // -------------------- AUTRES AGENCES "otherAuthorizingAgency"
        if (
          key.includes(
            "additional/obtainedauthorization/otherauthorizingagency_fr"
          )
        ) {
          otherAgenciesList("fr").push(value);
          continue;
        }
        if (
          key.includes(
            "additional/obtainedauthorization/otherauthorizingagency_en"
          )
        ) {
          otherAgenciesList("en").push(value);
          continue;
        }

        // ----------------------------------------------------------------------
        // -------------------- FINANCEMENT (fundAg*, fundingAgentType*)
        // ----------------------------------------------------------------------
        if (key.includes("fundingagenttype")) {
          // champ multi; suffixe _fr/_en pour la langue
          if (lang) fattList(lang).push(value);
          continue;
        }

        // Nom de l'agence (ex: fundAg_fr / fundAg_en)
        if (/fundag_(fr|en)$/i.test(key)) {
          if (lang) {
            faList(lang).push({ name: value }); // commence un nouveau bloc financeur
          }
          continue;
        }

        // Titre d'ExtLink de l'agence (ex: fundAg/ExtLink/title_fr)
        if (/fundag\/extlink\/title_(fr|en)$/i.test(key)) {
          if (lang) {
            const f = touchLastFunding(lang);
            f.title = value;
          }
          continue;
        }

        // URI d'ExtLink de l'agence (ex: fundAg/ExtLink/URI) — parfois sans suffixe
        if (/fundag\/extlink\/uri$/i.test(key)) {
          // On met l'URI sur le dernier financeur FR et EN si présents ;
          if (result.fundingAgencies_fr.length) {
            const ffr = touchLastFunding("fr");
            ffr.uri = value;
          }
          if (result.fundingAgencies_en.length) {
            const fen = touchLastFunding("en");
            fen.uri = value;
          }
          continue;
        }

        // ----------------------------------------------------------------------
        // -------------------- AUTEURS / AFFILIATIONS (AuthEnty*)
        // ----------------------------------------------------------------------
        // Nom d’un investigator (ex: AuthEnty_fr / AuthEnty_en)
        if (/authenty_(fr|en)$/i.test(key)) {
          if (lang) pushAuthEnt(lang, value, "investigator");
          continue;
        }

        // Nom d’une affiliation (ex: AuthEnty/affiliation_fr ou .../Affiliation_en)
        if (/authenty\/affiliation_(fr|en)$/i.test(key)) {
          if (lang) pushAuthEnt(lang, value, "affiliation");
          continue;
        }

        // ExtLink.title pour l’auteur courant (investigator ou affiliation)
        if (/authenty\/extlink\/title_(fr|en)$/i.test(key)) {
          if (lang) {
            const ext = touchAuthLastExt(lang);
            ext.title = value;
          }
          continue;
        }

        // ExtLink.URI pour l’auteur courant
        // Supporte "authenty/extlink/uri" (correct) et "authenty/extlink/extlink/uri" (ancienne faute)
        if (
          /authenty\/extlink\/uri(_(fr|en))?$/i.test(key) ||
          /authenty\/extlink\/extlink\/uri(_(fr|en))?$/i.test(key)
        ) {
          if (/_(fr|en)$/.test(key)) {
            if (lang) {
              const ext = touchAuthLastExt(lang);
              ext.uri = value;
            }
          } else {
            if (aeList("fr").length) touchAuthLastExt("fr").uri = value;
            if (aeList("en").length) touchAuthLastExt("en").uri = value;
          }
          continue;
        }

        // ExtLink.title pour l’affiliation explicitement (accepte d’anciennes clés)
        if (/authenty\/affiliation\/extlink\/title_(fr|en)$/i.test(key)) {
          if (lang) touchAuthLastExt(lang).title = value;
          continue;
        }
        if (
          /authenty\/affiliation\/extlink\/uri$/i.test(key) ||
          /authenty\/affiliation\/extlink\/uri_(fr|en)$/i.test(key)
        ) {
          if (/_(fr|en)$/.test(key)) {
            if (lang) touchAuthLastExt(lang).uri = value;
          } else {
            if (aeList("fr").length) touchAuthLastExt("fr").uri = value;
            if (aeList("en").length) touchAuthLastExt("en").uri = value;
          }
          continue;
        }

        // ============== OTH-ID / CONTRIBUTORS & AFFILIATION =================

        // Nom du contributor
        // stdyDscr/citation/rspStmt/othId/type_contributor_fr
        if (
          /stdydscr\/citation\/rspstmt\/othid\/type_contributor_(fr|en)$/i.test(
            key
          )
        ) {
          pushContributorOth(lang, value);
          continue;
        }

        // ExtLink.title pour contributor (plusieurs possibles)
        // stdyDscr/citation/rspStmt/othId/type_contributor/ExtLink/title_fr
        if (
          /stdydscr\/citation\/rspstmt\/othid\/type_contributor\/extlink\/title_(fr|en)$/i.test(
            key
          )
        ) {
          const c = touchLastOfType(lang, "contributor");
          upsertExtLink(c.extlinks, "title", value);
          continue;
        }

        // ExtLink.URI pour contributor (plusieurs possibles) — suffixe ou non
        // stdyDscr/citation/rspStmt/othId/type_contributor/ExtLink/URI(_fr|_en)?
        if (
          /stdydscr\/citation\/rspstmt\/othid\/type_contributor\/extlink\/uri(_(fr|en))?$/i.test(
            key
          )
        ) {
          if (/_fr$/i.test(key)) {
            const c = touchLastOfType("fr", "contributor");
            upsertExtLink(c.extlinks, "uri", value);
          } else if (/_en$/i.test(key)) {
            const c = touchLastOfType("en", "contributor");
            upsertExtLink(c.extlinks, "uri", value);
          } else {
            if (result.othIds_fr.length)
              upsertExtLink(
                touchLastOfType("fr", "contributor").extlinks,
                "uri",
                value
              );
            if (result.othIds_en.length)
              upsertExtLink(
                touchLastOfType("en", "contributor").extlinks,
                "uri",
                value
              );
          }
          continue;
        }

        // Nom de l’affiliation
        // stdyDscr/citation/rspStmt/othId/affiliation_fr
        if (
          /stdydscr\/citation\/rspstmt\/othid\/affiliation_(fr|en)$/i.test(key)
        ) {
          pushAffiliationOth(lang, value);
          continue;
        }

        // ExtLink.title pour l’affiliation (UN SEUL extlink)
        // stdyDscr/citation/rspStmt/othId/affiliation/ExtLink/title(_fr|_en)?
        if (
          /stdydscr\/citation\/rspstmt\/othid\/affiliation\/extlink\/title(_(fr|en))?$/i.test(
            key
          )
        ) {
          if (/_fr$/i.test(key)) {
            const a = touchLastOfType("fr", "affiliation");
            a.extlink = a.extlink || {};
            a.extlink.title = value;
          } else if (/_en$/i.test(key)) {
            const a = touchLastOfType("en", "affiliation");
            a.extlink = a.extlink || {};
            a.extlink.title = value;
          } else {
            if (result.othIds_fr.length) {
              const a = touchLastOfType("fr", "affiliation");
              a.extlink = a.extlink || {};
              a.extlink.title = value;
            }
            if (result.othIds_en.length) {
              const a = touchLastOfType("en", "affiliation");
              a.extlink = a.extlink || {};
              a.extlink.title = value;
            }
          }
          continue;
        }

        // ExtLink.URI pour l’affiliation (UN SEUL extlink)
        // stdyDscr/citation/rspStmt/othId/affiliation/ExtLink/URI(_fr|_en)?
        if (
          /stdydscr\/citation\/rspstmt\/othid\/affiliation\/extlink\/uri(_(fr|en))?$/i.test(
            key
          )
        ) {
          if (/_fr$/i.test(key)) {
            const a = touchLastOfType("fr", "affiliation");
            a.extlink = a.extlink || {};
            a.extlink.uri = value;
          } else if (/_en$/i.test(key)) {
            const a = touchLastOfType("en", "affiliation");
            a.extlink = a.extlink || {};
            a.extlink.uri = value;
          } else {
            if (result.othIds_fr.length) {
              const a = touchLastOfType("fr", "affiliation");
              a.extlink = a.extlink || {};
              a.extlink.uri = value;
            }
            if (result.othIds_en.length) {
              const a = touchLastOfType("en", "affiliation");
              a.extlink = a.extlink || {};
              a.extlink.uri = value;
            }
          }
          continue;
        }

        // ===================== distStmt / contact =====================

        // Nom du contact
        // stdyDscr/citation/distStmt/contact_fr  |  .../contact_en
        if (/stdydscr\/citation\/diststmt\/contact_(fr|en)$/i.test(key)) {
          // crée une entrée contact {name, type:"contact", email:""}
          if (lang) {
            pushDistContact(lang, value);
          }
          continue;
        }

        // Email du contact (non suffixé, un seul champ)
        // stdyDscr/citation/distStmt/contact/email
        if (/stdydscr\/citation\/diststmt\/contact\/email$/i.test(key)) {
          // applique au dernier contact FR et EN (crée placeholder si besoin)
          const cfr = touchLastDistOfType("fr", "contact");
          cfr.email = value;
          const cen = touchLastDistOfType("en", "contact");
          cen.email = value;
          continue;
        }

        // Nom de l’affiliation
        // stdyDscr/citation/distStmt/contact/affiliation_fr  |  .../affiliation_en
        if (
          /stdydscr\/citation\/diststmt\/contact\/affiliation_(fr|en)$/i.test(
            key
          )
        ) {
          if (lang) pushDistAffiliation(lang, value);
          continue;
        }

        // ExtLink.title pour l’affiliation (extlink unique)
        // stdyDscr/citation/distStmt/contact/affiliation/ExtLink/title(_fr|_en)?
        if (
          /stdydscr\/citation\/diststmt\/contact\/affiliation\/extlink\/title(_(fr|en))?$/i.test(
            key
          )
        ) {
          if (/_fr$/i.test(key)) {
            const a = touchLastDistOfType("fr", "affiliation");
            a.extlink = a.extlink || {};
            a.extlink.title = value;
          } else if (/_en$/i.test(key)) {
            const a = touchLastDistOfType("en", "affiliation");
            a.extlink = a.extlink || {};
            a.extlink.title = value;
          } else {
            // sans suffixe -> appliquer aux deux
            const afr = touchLastDistOfType("fr", "affiliation");
            afr.extlink = afr.extlink || {};
            afr.extlink.title = value;
            const aen = touchLastDistOfType("en", "affiliation");
            aen.extlink = aen.extlink || {};
            aen.extlink.title = value;
          }
          continue;
        }

        // ExtLink.URI pour l’affiliation (extlink unique)
        // stdyDscr/citation/distStmt/contact/affiliation/ExtLink/URI(_fr|_en)?
        if (
          /stdydscr\/citation\/diststmt\/contact\/affiliation\/extlink\/uri(_(fr|en))?$/i.test(
            key
          )
        ) {
          if (/_fr$/i.test(key)) {
            const a = touchLastDistOfType("fr", "affiliation");
            a.extlink = a.extlink || {};
            a.extlink.uri = value;
          } else if (/_en$/i.test(key)) {
            const a = touchLastDistOfType("en", "affiliation");
            a.extlink = a.extlink || {};
            a.extlink.uri = value;
          } else {
            // sans suffixe -> appliquer aux deux
            const afr = touchLastDistOfType("fr", "affiliation");
            afr.extlink = afr.extlink || {};
            afr.extlink.uri = value;
            const aen = touchLastDistOfType("en", "affiliation");
            aen.extlink = aen.extlink || {};
            aen.extlink.uri = value;
          }
          continue;
        }

        // ======================= TOPICS (subject/topcclas) =======================

        // A) topcclas[]/value_{fr|en}[]
        if (/subject\/topcclas\[\]\/value_(fr|en)\[\]$/i.test(key)) {
          if (lang) pushUnique(topics_main_vals[lang], value);
          continue;
        }
        // A) vocab commun
        if (/subject\/topcclas\[\]\/vocab$/i.test(key)) {
          topics_main_vocab = String(value);
          continue;
        }

        // C) valeurs sous schéma: topcclas[]/value/{scheme}[]
        {
          const m = key.match(
            /subject\/topcclas\[\]\/value\/([a-z0-9\-]+)\[\]$/i
          );
          if (m) {
            const scheme = m[1].toLowerCase();
            topics_scheme_vals[scheme] ||= [];
            pushUnique(topics_scheme_vals[scheme], value);
            continue;
          }
        }
        // C) vocab du schéma: topcclas[]/vocab/{scheme}
        {
          const m = key.match(/subject\/topcclas\[\]\/vocab\/([a-z0-9\-]+)$/i);
          if (m) {
            const scheme = m[1].toLowerCase();
            topics_scheme_vocab[scheme] = String(value);
            continue;
          }
        }

        // B) topcclas_{fr|en}[]
        if (/subject\/topcclas_(fr|en)\[\]$/i.test(key)) {
          if (lang) pushUnique(topics_det_vals[lang], value);
          continue;
        }
        // B) vocab commun
        if (/subject\/topcclas\/vocab$/i.test(key)) {
          topics_det_vocab = String(value);
          continue;
        }
        // ===================== ADDITIONAL / INTERVENTION (repeater) =====================

        // Name: additional/intervention/interventionName_{fr|en}  (accept optional [] at end)
        if (
          /additional\/intervention\/interventionname_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            // A new name usually means a new intervention entry
            pushNewIntervention(lang);
            touchLastIntervention(lang).name = value;
          }
          continue;
        }

        // Type: additional/intervention/interventionType_{fr|en}
        if (
          /additional\/intervention\/interventiontype_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const it = touchLastIntervention(lang);
            it.type = value;
          }
          continue;
        }

        // TypeOther  <-- NEW
        if (
          /additional\/intervention\/interventiontypeother_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const it = touchLastIntervention(lang);
            it.typeOther = value;
          }
          continue;
        }

        // Description: additional/intervention/interventionDescription_{fr|en}
        if (
          /additional\/intervention\/interventiondescription_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const it = touchLastIntervention(lang);
            it.description = value;
          }
          continue;
        }

        // ===================== ADDITIONAL / INTERVENTIONAL STUDY (checkbox) =====================
        // additional/interventionalStudy/researchPurpose_{fr|en}[]
        if (
          /additional\/interventionalstudy\/researchpurpose_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.researchPurposes_fr, v);
              else pushUnique(result.researchPurposes_en, v);
            }
          }
          continue;
        }

        // ===================== ADDITIONAL / CLINICAL TRIAL (checkbox) =====================
        //additional/interventionalStudy/trialPhase_{fr|en}[]
        if (
          /additional\/interventionalstudy\/trialphase_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.trialPhases_fr, v);
              else pushUnique(result.trialPhases_en, v);
            }
          }
          continue;
        }

        // additional/cohortLongitudinal/recrutementTiming_{fr|en}[]
        if (
          /additional\/cohortLongitudinal\/recrutementTiming_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.recrutementTiming_fr, v);
              else pushUnique(result.recrutementTiming_en, v);
            }
          }
          continue;
        }

        // ===================== ADDITIONAL / MASKING (checkbox) =====================
        //additional/masking/blindedMaskingDetails_{fr|en}[]
        if (
          /additional\/masking\/blindedmaskingdetails_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.blindedMaskingDetails_fr, v);
              else pushUnique(result.blindedMaskingDetails_en, v);
            }
          }
          continue;
        }

        // ===================== ADDITIONAL / DATA COLLECTION (checkbox) =====================
        //additional/dataCollection/inclusionStrategy_{fr|en}[]
        if (
          /additional\/datacollection\/inclusionstrategy_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.inclusionStrategy_fr, v);
              else pushUnique(result.inclusionStrategy_en, v);
            }
          }
          continue;
        }

        // ===================== ADDITIONAL / DATA COLLECTION (checkbox) =====================
        //stdyDscr/method/dataColl/sampProc_{fr|en}[]
        if (/stdydscr\/method\/datacoll\/sampproc_(fr|en)(\[\])?$/i.test(key)) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.sampProc_fr, v);
              else pushUnique(result.sampProc_en, v);
            }
          }
          continue;
        }

        // ===================== ADDITIONAL / DATA COLLECTION (checkbox) =====================
        //stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_{fr|en}[]
        if (
          /stdydscr\/method\/datacoll\/sampleframe\/frameunit\/unittype_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.unitType_fr, v);
              else pushUnique(result.unitType_en, v);
            }
          }
          continue;
        }

        // ===================== ADDITIONAL / DATA COLLECTION (checkbox) =====================
        //stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I_{fr|en}[]
        if (
          /stdydscr\/stdyinfo\/sumdscr\/universe\/level_sex-clusion_i_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.level_sex_Clusion_I_fr, v);
              else pushUnique(result.level_sex_Clusion_I_en, v);
            }
          }
          continue;
        }

        // ===================== ADDITIONAL / DATA COLLECTION (checkbox) =====================
        //stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I_{fr|en}[]
        if (
          /stdydscr\/stdyinfo\/sumdscr\/universe\/level_age-clusion_i_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.level_age_Clusion_I_fr, v);
              else pushUnique(result.level_age_Clusion_I_en, v);
            }
          }
          continue;
        }

        // ===================== ADDITIONAL / GEOGRAPHIC COVERAGE (checkbox) =====================
        ///stdyDscr/stdyInfo/sumDscr/nation_{fr|en}[]
        if (/stdydscr\/stdyinfo\/sumdscr\/nation_(fr|en)(\[\])?$/i.test(key)) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.nation_fr, v);
              else pushUnique(result.nation_en, v);
            }
          }
          continue;
        }

        // ===================== ADDITIONAL / GEOGRAPHIC COVERAGE (checkbox) =====================
        //stdyDscr/stdyInfo/sumDscr/geogCover_{fr|en}[]
        if (
          /stdydscr\/stdyinfo\/sumdscr\/geogcover_(fr|en)(\[\])?$/i.test(key)
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.geogCover_fr, v);
              else pushUnique(result.geogCover_en, v);
            }
          }
          continue;
        }

        // ===================== ADDITIONAL / FOLLOW-UP OF SUBJECTS (checkbox) =====================
        //stdyDscr/method/notes/subject_followUP_{fr|en}
        if (
          /stdydscr\/method\/notes\/subject_followUP_(fr|en)(\[\])?$/i.test(key)
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
              if (lang === "fr") pushUnique(result.subject_followUP_fr, v);
              else pushUnique(result.subject_followUP_en, v);
            }
          }
          continue;
        }

        // ===================== DATA COLL / SOURCES (repeater) =====================

        // sourceCitation_{fr|en}  → démarre un nouvel item SEULEMENT si le précédent a déjà une citation
        if (
          /stdydscr\/method\/datacoll\/sources\/sourcecitation_(fr|en)$/i.test(
            key
          )
        ) {
          if (lang) {
            pushNewSource(lang);
            touchLastSource(lang).citation = value;
          }
          continue;
        }

        // srcOrig_{fr|en}[]  (checkbox, multiples)
        if (
          /stdydscr\/method\/datacoll\/sources\/srcorig_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const s = touchLastSource(lang); // crée placeholder si besoin
            const v = String(value).trim();
            if (v) pushUnique(s.srcOrig, v); // évite les doublons
          }
          continue;
        }

        // notes / subject_sourcePurpose_{fr|en}
        if (
          /stdydscr\/method\/datacoll\/sources\/sourcecitation\/notes\/subject_sourcepurpose_(fr|en)$/i.test(
            key
          )
        ) {
          if (lang) {
            const s = touchLastSource(lang);
            s.notes = s.notes || {};
            s.notes.subject_sourcePurpose = value;
          }
          continue;
        }

        // otherSourceType  <-- NEW
        if (
          /additional\/thirdPartySource\/otherSourceType_(fr|en)(\[\])?$/i.test(
            key
          )
        ) {
          if (lang) {
            const s = touchLastSource(lang);
            s.otherSourceType = value;
          }
          continue;
        }

        // ===================== QUALITY / STANDARDS COMPLIANCE (text list) =====================
        // stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName_{fr|en}
        if (
          /stdydscr\/stdyinfo\/qualitystatement\/standardscompliance\/standard\/standardname_(fr|en)$/i.test(
            key
          )
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              if (lang === "fr") pushUnique(result.standardsCompliance_fr, v);
              else pushUnique(result.standardsCompliance_en, v);
            }
          }
          continue;
        }

        // ===================== QUALITY / OTHER QUALITY STATEMENT (text list) =====================
        // stdyDscr/stdyInfo/qualityStatement/otherQualityStatement_{fr|en}
        if (
          /stdydscr\/stdyinfo\/qualitystatement\/complianceDescription_(fr|en)$/i.test(
            key
          )
        ) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              if (lang === "fr")
                pushUnique(result.otherQualityStatements_fr, v);
              else pushUnique(result.otherQualityStatements_en, v);
              // If you don't want de-duplication: replace pushUnique(...) with result.otherQualityStatements_* .push(v)
            }
          }
          continue;
        }

        // ===================== DATA ACCESS / USE STATEMENT / CONTACT (text list) =====================
        // stdyDscr/dataAccs/useStmt/contact_{fr|en} (accepts optional [] if repeated)
        if (/stdydscr\/dataaccs\/usestmt\/contact_(fr|en)(\[\])?$/i.test(key)) {
          if (lang) {
            const v = String(value).trim();
            if (v) {
              if (lang === "fr") pushUnique(result.useStmtContacts_fr, v);
              else pushUnique(result.useStmtContacts_en, v);
              // If you want to allow duplicates, replace pushUnique(...) with result.useStmtContacts_* .push(v)
            }
          }
          continue;
        }

        // ===================== CITATION / IDno (non-indexed, robust) =====================

        // agentSchema_{fr|en}
        if (
          /stdydscr\/citation\/idno\/agentschema_(fr|en)(\[\])?$/i.test(key)
        ) {
          if (lang) {
            // start a new row if none, or if the current row already looks like a completed/started row
            if (idnoCursor[lang] < 0) startNewIdno(lang);
            const row = currentIdno(lang);
            if (row.agentSchema || row.uri) {
              // boundary: next block
              startNewIdno(lang).agentSchema = value;
            } else {
              row.agentSchema = value; // same block
            }
          }
          continue;
        }

        // otherAgent_{fr|en}
        if (/stdydscr\/citation\/idno\/otheragent_(fr|en)(\[\])?$/i.test(key)) {
          if (lang) {
            if (idnoCursor[lang] < 0) startNewIdno(lang);
            const row = currentIdno(lang);
            // if current row already has all fields filled, start a new row
            if ((row.agentSchema || row.uri) && row.agentOther) {
              startNewIdno(lang).agentOther = value;
            } else {
              row.agentOther = value;
            }
          }
          continue;
        }

        // uri_{fr|en}
        if (/stdydscr\/citation\/idno\/uri_(fr|en)(\[\])?$/i.test(key)) {
          if (lang) {
            if (idnoCursor[lang] < 0) startNewIdno(lang);
            const row = currentIdno(lang);
            // if this row already has a uri, it's definitely a new block
            if (row.uri) {
              startNewIdno(lang).uri = value;
            } else {
              row.uri = value;
            }
          }
          continue;
        }

        //stdyDscr/stdyInfo/sumDscr/dataKind
        if (
          /stdydscr\/stdyinfo\/sumdscr\/datakind_(fr|en)(\[\])?$/i.test(key)
        ) {
          const v = String(value).trim();
          if (v) {
            if (lang === "fr") pushUnique(result.dataKind_fr, v);
            else pushUnique(result.dataKind_en, v);
          }
          continue;
        }

        //additional/dataTypes/biobankContent
        if (
          /additional\/datatypes\/biobankcontent_(fr|en)(\[\])?$/i.test(key)
        ) {
          const v = String(value).trim();
          if (v) {
            if (lang === "fr") pushUnique(result.biobankContent_fr, v);
            else pushUnique(result.biobankContent_en, v);
          }
          continue;
        }

        // ===================== ADDITIONAL / INCLUSION GROUPS (indexed + fallback) =====================
        {
          let m;

          // --- Indexed pattern: .../groupName_{fr|en}_{i}
          if (
            (m = key.match(
              /additional\/inclusiongroups\/groupname_(fr|en)_(\d+)$/i
            ))
          ) {
            const langKey = m[1].toLowerCase(),
              idx = parseInt(m[2], 10);
            ensureInclusionGroupAt(langKey, idx).name = value;
            continue;
          }
          if (
            (m = key.match(
              /additional\/inclusiongroups\/groupdescription_(fr|en)_(\d+)$/i
            ))
          ) {
            const langKey = m[1].toLowerCase(),
              idx = parseInt(m[2], 10);
            ensureInclusionGroupAt(langKey, idx).description = value;
            continue;
          }
          if (
            (m = key.match(
              /additional\/inclusiongroups\/groupinterventionexposition_(fr|en)_(\d+)$/i
            ))
          ) {
            const langKey = m[1].toLowerCase(),
              idx = parseInt(m[2], 10);
            ensureInclusionGroupAt(langKey, idx).interventionExposition = value;
            continue;
          }

          // --- Fallback: []-style (keeps your previous behavior)
          if (
            /additional\/inclusiongroups\/groupname_(fr|en)(\[\])?$/i.test(key)
          ) {
            if (lang) {
              const v = String(value).trim();
              const last = peekLastInclusionGroup(lang);
              if (last && !String(last.name || "").trim()) last.name = v;
              else pushInclusionGroup(lang, v);
            }
            continue;
          }
          if (
            /additional\/inclusiongroups\/groupdescription_(fr|en)(\[\])?$/i.test(
              key
            )
          ) {
            if (lang) {
              const g = touchLastInclusionGroup(lang);
              g.description = value;
            }
            continue;
          }
          if (
            /additional\/inclusiongroups\/groupinterventionexposition_(fr|en)(\[\])?$/i.test(
              key
            )
          ) {
            if (lang) {
              const g = touchLastInclusionGroup(lang);
              g.interventionExposition = value;
            }
            continue;
          }
        }

        // ===================== ADDITIONAL / ARMS (repeater) =====================

        // --- Indexed pattern: .../armsName_{fr|en}_{i}
        {
          let m;
          if ((m = key.match(/additional\/arms\/armsname_(fr|en)_(\d+)$/i))) {
            const langKey = m[1].toLowerCase();
            const idx = parseInt(m[2], 10);
            ensureArmAt(langKey, idx).name = value;
            continue;
          }
          if ((m = key.match(/additional\/arms\/armstype_(fr|en)_(\d+)$/i))) {
            const langKey = m[1].toLowerCase();
            const idx = parseInt(m[2], 10);
            ensureArmAt(langKey, idx).type = value;
            continue;
          }
          if (
            (m = key.match(/additional\/arms\/armstypeother_(fr|en)_(\d+)$/i))
          ) {
            const langKey = m[1].toLowerCase();
            const idx = parseInt(m[2], 10);
            ensureArmAt(langKey, idx).typeOther = value;
            continue;
          }
          if (
            (m = key.match(/additional\/arms\/armsdescription_(fr|en)_(\d+)$/i))
          ) {
            const langKey = m[1].toLowerCase();
            const idx = parseInt(m[2], 10);
            ensureArmAt(langKey, idx).description = value;
            continue;
          }
        }

        // --- Fallback (if you ever use [] instead of _0/_1)
        if (/additional\/arms\/armsname_(fr|en)(\[\])?$/i.test(key)) {
          if (lang) {
            const v = String(value).trim();
            const last = (() => {
              const list = armsList(lang);
              return list.length ? list[list.length - 1] : null;
            })();
            if (last && !String(last.name || "").trim()) last.name = v;
            else pushArm(lang, v);
          }
          continue;
        }
        if (/additional\/arms\/armstype_(fr|en)(\[\])?$/i.test(key)) {
          if (lang) {
            const a = touchLastArm(lang);
            a.type = value;
          }
          continue;
        }
        if (/additional\/arms\/armstypeother_(fr|en)(\[\])?$/i.test(key)) {
          if (lang) {
            const a = touchLastArm(lang);
            a.typeOther = value;
          }
          continue;
        }
        if (/additional\/arms\/armsdescription_(fr|en)(\[\])?$/i.test(key)) {
          if (lang) {
            const a = touchLastArm(lang);
            a.description = value;
          }
          continue;
        }

        // (Facultatif) Débogage : voir les champs non traités
        // console.debug("[IGNORED FIELD]", rawKey, "=", value);
      } // fin for ... entries

      // ----- A) valeurs "main" (value_{fr|en}[]) -----
      if (topics_main_vocab) {
        for (const v of topics_main_vals.fr)
          result.topics_fr.push({ topic: String(v), vocab: topics_main_vocab });
        for (const v of topics_main_vals.en)
          result.topics_en.push({ topic: String(v), vocab: topics_main_vocab });
      }

      // ----- C) valeurs par schéma (ex: cim-11) -----
      // Ces codes sont généralement langue-agnostiques → on les met dans FR et EN
      for (const scheme of Object.keys(topics_scheme_vals)) {
        const vocabLabel = topics_scheme_vocab[scheme] || scheme;
        for (const v of topics_scheme_vals[scheme]) {
          const item = { topic: String(v), vocab: vocabLabel };
          result.topics_fr.push(item);
          result.topics_en.push({ ...item });
        }
      }

      // ----- B) valeurs "determinant" (topcclas_{fr|en}[]) -----
      if (topics_det_vocab) {
        for (const v of topics_det_vals.fr)
          result.topics_fr.push({ topic: String(v), vocab: topics_det_vocab });
        for (const v of topics_det_vals.en)
          result.topics_en.push({ topic: String(v), vocab: topics_det_vocab });
      }

      // --- Envoi AJAX ----------------------------------------------------------
      formData.append("complexData", JSON.stringify(result));
      formData.append("action", "nada_save_study");

      $.ajax({
        url: nadaAddStudyVars.ajax_url,
        type: "POST",
        dataType: "json",
        processData: false, // important pour FormData
        contentType: false, // important pour FormData
        data: formData,
      })
        .done(function (response) {
          submitter.disabled = false;
          if (spinner) spinner.classList.add("d-none");
          if (btnText) {
            submitter.id == "finishBtn"
              ? (btnText.textContent = "Terminer")
              : (btnText.textContent = "Enregistrer brouillon");
          }

          const ok = response && response.success;
          if (response.response.fr.data.dataset) {
            if (
              response.response.fr.data.dataset &&
              response.response.fr.data.dataset.idno
            ) {
              const redirectUrl = `/mon-espace/`;
              window.location.href = redirectUrl;
              return;
            }
          }
          $("#response-success")
            .toggleClass("d-none", !ok)
            .text(ok ? response.message : "");
          $("#response-error")
            .toggleClass("d-none", !!ok)
            .text(
              ok
                ? ""
                : (response && response.message) || "Une erreur est survenue."
            );
        })
        .fail(function (_xhr, _status, error) {
          console.error("Erreur AJAX :", error);
          $("#response-error")
            .text("Impossible de contacter le serveur.")
            .removeClass("d-none");
          $("#response-success").addClass("d-none");
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".tags-input-container").forEach((container) => {
    const tagName = container.dataset.tags;
    const input = container.querySelector(
      `.tags-input[data-tags="${tagName}"]`
    );
    const wrapper = container.querySelector(
      `.tags-wrapper[data-tags="${tagName}"]`
    );
    const hidden = container.querySelector(
      `.tags-hidden[data-tags="${tagName}"]`
    );
    let tags = [];

    function updateHidden() {
      hidden.value = tags.join(",");
    }

    function createTagElement(tagText) {
      const tag = document.createElement("span");
      tag.className = "tag";
      tag.textContent = tagText;

      const remove = document.createElement("span");
      remove.className = "remove-tag";
      remove.textContent = "X";
      remove.addEventListener("click", function () {
        tags = tags.filter((t) => t !== tagText);
        wrapper.removeChild(tag);
        updateHidden();
      });

      tag.appendChild(remove);
      wrapper.appendChild(tag);
    }

    function addTag(tagText) {
      tagText = tagText.trim();
      if (!tagText || tags.includes(tagText)) return;
      tags.push(tagText);
      createTagElement(tagText);
      updateHidden();
    }

    input.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === "," || e.key === " ") {
        e.preventDefault();
        input.value.split(/,|\s/).forEach((tag) => addTag(tag));
        input.value = "";
      }
    });
  });
});

// ===================== JS Section 1 ===========================//
jQuery(document).ready(function ($) {
  $(document).on("change", '[name^="creatorIsPi"]', function () {
    const val = $(this).val();
    if ((val === "No" || val === "Non") && $(this).is(":checked")) {
      $("#creatorIsPiEmail").removeClass("d-none");
    } else {
      $("#creatorIsPiEmail").addClass("d-none");
    }
  });

  $(document).on(
    "change",
    '[name="additional/governance/committee_fr"], [name="additional/governance/committee_en"]',
    function () {
      $("#committeeDetailBloc, #committeeDetailBlocOthers").addClass("d-none");
      const val = $(this).val();
      if ((val === "Yes" || val === "Oui") && $(this).is(":checked")) {
        $("#committeeDetailBloc").removeClass("d-none");
      } else if (
        (val === "Other" || val === "Autre") &&
        $(this).is(":checked")
      ) {
        $("#committeeDetailBlocOthers").removeClass("d-none");
      } else {
        $("#committeeDetailBloc").addClass("d-none");
      }
    }
  );

  $(document).on("click", ".nav-item.langue .nav-link", function (e) {
    e.preventDefault();

    let lang = $(this).data("lang");

    if (lang == "fr") {
      jQuery(" input.lang-input[name^='stdyDscr/method/dataColl/respRate_en']")
        .next(".error-msg")
        .remove();
    } else {
      jQuery("input.lang-input[name^='stdyDscr/method/dataColl/respRate_fr']")
        .next(".error-msg")
        .remove();
    }

    $(this).closest(".nav-pills").find(".nav-link").removeClass("active");
    $(this).addClass("active");

    $(".lang-label, .lang-input").addClass("d-none");

    /** one input placeholder */
    $(".one-input input").each(function () {
      let placeholder = $(this).attr("attr-placeholder-" + lang);
      $(this).attr("placeholder", placeholder);
    });

    $(
      `.lang-label[attr-lng='${lang}'], .lang-input[attr-lng='${lang}']`
    ).removeClass("d-none");
    $(".lang-text").each(function () {
      const $el = $(this);
      const text = $el.data(lang);
      if (text) $el.text(text);
    });
    //applyLangToStaticTexts(lang);
  });
});

jQuery(function ($) {
  const $selPatho_fr = $(
    'select[name="stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11_fr[]"]'
  );
  if ($selPatho_fr.length) {
    if (!$selPatho_fr.val() || $selPatho_fr.val().length === 0) {
      $selPatho_fr.find('option[value="CIM-11"]').prop("selected", true);
      $selPatho_fr.trigger("change");
    }
  }

  const $selPatho_en = $(
    'select[name="stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11_en[]"]'
  );
  if ($selPatho_en.length) {
    if (!$selPatho_en.val() || $selPatho_en.val().length === 0) {
      $selPatho_en.find('option[value="CIM-11"]').prop("selected", true);
      $selPatho_en.trigger("change");
    }
  }
});

// ===================== JS Section 2 ===========================//
jQuery(document).ready(function ($) {
  $(document).ready(function () {
    function handleDynamicSections(containerAttr) {
      const container = $(`[data-containerToDuplicate="${containerAttr}"]`);
      const input = container.find(
        `[data-inputToDuplicate="${containerAttr}"] input`
      );
      const template = container
        .find(`[data-contentToDuplicate="${containerAttr}"]`)
        .first();
      const parent = template.parent();

      template.addClass("d-none");

      input.on("input", function () {
        let number = parseInt($(this).val()) || 0;

        parent
          .find(`[data-contentToDuplicate="${containerAttr}"]`)
          .not(template)
          .remove();

        if (number <= 0) {
          return;
        }

        for (let i = 0; i < number; i++) {
          let clone = template.clone(false, false).removeClass("d-none");

          clone.find(".number").text(` - ${i + 1}`);

          clone.find("input, select, textarea").each(function () {
            $(this).val("");
            let name = $(this).attr("name");
            let id = $(this).attr("id");

            if (name) $(this).attr("name", name.replace(/(_\d+)?$/, `_${i}`));
            if (id) $(this).attr("id", id.replace(/(_\d+)?$/, `_${i}`));
          });

          clone.find("select").each(function () {
            if ($.fn.select2 && $(this).hasClass("select2-hidden-accessible")) {
              //$(this).select2('destroy');
              const $select = $(this);
              $select.next(".select2-container").remove();
            }

            // Réinitialiser la valeur
            $(this).val("");

            // Réinitialiser proprement le Select2
            $(this).select2({
              width: "100%",
            });
          });
          parent.append(clone);
        }
      });
    }
    handleDynamicSections("armsBloc");
    handleDynamicSections("inclusionGroupsBloc");
  });
});

jQuery(function ($) {
  const $selFr = $('select[name="stdyDscr/stdyInfo/sumDscr/anlyUnitFake_fr"]');
  const $selEn = $('select[name="stdyDscr/stdyInfo/sumDscr/anlyUnitFake_en"]');

  [$selFr, $selEn].forEach(($sel) => {
    if ($sel.length && (!$sel.val() || $sel.val() === "")) {
      $sel.val("Individus").trigger("change");
    }
  });
});

//====================== Fonctions pour les champs du formulaire ==============================//

function setFieldValues(values, name, type, $context) {
  if (!Array.isArray(values) || !name || !type) return;

  const $scope = $context && $context.length ? $context : jQuery(document);

  let $el = $scope.find(`input[name="${name}"], select[name="${name}"]`);
  if (!$el.length) return;
  if (type === "select") {
    $el.val(values).trigger("change");
  } else if (type === "checkbox") {
    $el.prop("checked", false);
    values.forEach((val) => {
      $el.filter(`[value="${val}"]`).prop("checked", true).trigger("change");
    });
  }
}

function autoRepeatClickObtainedAuthorization(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // On prend la langue avec le plus grand nombre de blocs pour calculer n
  const n =
    Math.max(...Object.values(allData).map((lang) => lang.data.length)) - 1;

  let i = 0;
  const interval = setInterval(() => {
    if (i < n) {
      $btn.trigger("click");

      const $last = jQuery(
        "#repeater-ObtainedAuthorization .repeater-item"
      ).last();

      // remplir tous les champs pour toutes les langues
      Object.keys(allData).forEach((lang) => {
        const data = allData[lang].data;
        const add = allData[lang].additional;
        fillItem($last, i + 1, data, add, lang);
      });

      i++;
    } else {
      clearInterval(interval);

      const $first = jQuery(
        "#repeater-ObtainedAuthorization .repeater-item"
      ).first();

      Object.keys(allData).forEach((lang) => {
        fillItem($first, 0, allData[lang].data, allData[lang].additional, lang);
      });

      if (typeof callback === "function") callback();
    }
  }, 300);
}

function fillItem($item, idx, data, additional, lang) {
  if (!data[idx]) return;

  $item
    .find(`[name="stdyDscr/studyAuthorization/authorizingAgency_${lang}"]`)
    .val(data[idx]["name"] || "")
    .trigger("change");

  $item
    .find(
      `[name="additional/obtainedAuthorization/otherAuthorizingAgency_${lang}"]`
    )
    .val(additional[idx] || "")
    .trigger("change");
}

function autoRepeatClickPrimaryInvestigator(
  selector,
  n,
  primaryInvestigatorData
) {
  let $btn = jQuery(selector);

  if (!$btn.length) return;

  // Si au moins 1 investigator → on garde le premier bloc existant
  let clicksNeeded = n > 1 ? n - 1 : 0;

  let i = 0;
  const interval = setInterval(function () {
    if (i < clicksNeeded) {
      $btn.trigger("click");
      i++;
    } else {
      clearInterval(interval);

      // Remplir les blocs existants après création
      Object.keys(primaryInvestigatorData).forEach((lang) => {
        const { investigators = [], affiliations = [] } =
          primaryInvestigatorData[lang];

        investigators.forEach((inv, idx) => {
          let $item = jQuery("#repeater-PrimaryInvestigator .repeater-item").eq(
            idx
          );
          if (!$item.length) return;

          // Investigator
          $item
            .find(`[name="stdyDscr/citation/rspStmt/AuthEnty_${lang}"]`)
            .val(inv.name)
            .change();

          if (inv.extlink) {
            $item
              .find(
                `[name="stdyDscr/citation/rspStmt/AuthEnty/ExtLink/ExtLink/URI"]`
              )
              .val(inv.extlink.uri)
              .change();
            $item
              .find(
                `[name="stdyDscr/citation/rspStmt/AuthEnty/ExtLink/title_${lang}"]`
              )
              .val(inv.extlink.title)
              .change();
          }
        });

        affiliations.forEach((aff, idx) => {
          let $item = jQuery("#repeater-PrimaryInvestigator .repeater-item").eq(
            idx
          );
          if (!$item.length) return;

          // Affiliation
          $item
            .find(
              `[name="stdyDscr/citation/rspStmt/AuthEnty/affiliation_${lang}"]`
            )
            .val(aff.name)
            .change();

          if (aff.extlink) {
            $item
              .find(
                `[name="stdyDscr/citation/rspStmt/AuthEnty/affiliation/ExtLink/URI"]`
              )
              .val(aff.extlink.uri)
              .change();
            $item
              .find(
                `[name="stdyDscr/citation/rspStmt/AuthEnty/affiliation/ExtLink/title_${lang}"]`
              )
              .val(aff.extlink.title)
              .change();
          }
        });
      });
    }
  }, 300);
}

function autoRepeatClickContributor(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Validate data
  const validLangs = Object.keys(allData).filter(
    (lang) =>
      allData[lang] && Array.isArray(allData[lang]) && allData[lang].length > 0
  );

  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Helper function to check if object has meaningful data
  function hasData(item) {
    // if (!item) return false;
    // if (item.type === "contributor") {
    //   return !!(item.name && item.name.trim());
    // }
    // if (item.type === "affiliation") {
    //   return !!(item.name && item.name.trim());
    // }
    return true;
  }

  // Use first valid language to calculate blocks needed
  const firstLang = validLangs[0];
  const firstData = allData[firstLang];
  const contributors = firstData.filter(
    (item) => item.type === "contributor" && hasData(item)
  );
  const affiliations = firstData.filter(
    (item) => item.type === "affiliation" && hasData(item)
  );
  const totalBlocks = Math.max(contributors.length, affiliations.length);

  // Create additional blocks if needed
  if (totalBlocks >= 2) {
    let i = 1;
    const interval = setInterval(() => {
      if (i <= totalBlocks) {
        $btn.trigger("click");

        const $lastItem = jQuery("#repeater-Contributor .repeater-item").last();

        // Fill for all languages
        validLangs.forEach((lang) => {
          const data = allData[lang];
          const langContributors = data.filter(
            (item) => item.type === "contributor" && hasData(item)
          );
          const langAffiliations = data.filter(
            (item) => item.type === "affiliation" && hasData(item)
          );

          fillContributorBlock(
            $lastItem,
            langContributors[i],
            langAffiliations[i],
            lang
          );
        });

        i++;
      } else {
        clearInterval(interval);

        // Fill first block after all blocks created
        const $firstItem = jQuery(
          "#repeater-Contributor .repeater-item"
        ).first();

        validLangs.forEach((lang) => {
          const data = allData[lang];
          const langContributors = data.filter(
            (item) => item.type === "contributor" && hasData(item)
          );
          const langAffiliations = data.filter(
            (item) => item.type === "affiliation" && hasData(item)
          );

          fillContributorBlock(
            $firstItem,
            langContributors[0],
            langAffiliations[0],
            lang
          );

          // Handle extlinks for first contributor
          if (
            langContributors[0]?.extlinks &&
            langContributors[0].extlinks.length > 0
          ) {
            fillFirstContributorExtlinks(
              $firstItem,
              langContributors[0].extlinks,
              lang
            );
          }
        });

        if (typeof callback === "function") callback();
      }
    }, 300);
  } else {
    // Only one block
    const $firstItem = jQuery("#repeater-Contributor .repeater-item").first();

    validLangs.forEach((lang) => {
      const data = allData[lang];
      const langContributors = data.filter(
        (item) => item.type === "contributor" && hasData(item)
      );
      const langAffiliations = data.filter(
        (item) => item.type === "affiliation" && hasData(item)
      );

      fillContributorBlock(
        $firstItem,
        langContributors[0],
        langAffiliations[0],
        lang
      );

      // Handle extlinks for first contributor
      if (
        langContributors[0]?.extlinks &&
        langContributors[0].extlinks.length > 0
      ) {
        fillFirstContributorExtlinks(
          $firstItem,
          langContributors[0].extlinks,
          lang
        );
      }
    });

    if (typeof callback === "function") callback();
  }
}

function fillContributorBlock($block, contributor, affiliation, lang) {
  if (contributor) {
    $block
      .find(`[name="stdyDscr/citation/rspStmt/othId/type_contributor_${lang}"]`)
      .val(contributor.name)
      .change();
  }

  if (affiliation) {
    $block
      .find(`[name="stdyDscr/citation/rspStmt/othId/affiliation_${lang}"]`)
      .val(affiliation.name)
      .change();

    if (affiliation.extlink?.uri) {
      $block
        .find(
          `[name="stdyDscr/citation/rspStmt/othId/affiliation/ExtLink/URI"]`
        )
        .val(affiliation.extlink.uri)
        .change();
    }

    if (affiliation.extlink?.title) {
      $block
        .find(
          `select[name="stdyDscr/citation/rspStmt/othId/affiliation/ExtLink/title_${lang}"]`
        )
        .val(affiliation.extlink.title)
        .change();
    }
  }
}

function fillFirstContributorExtlinks($block, extlinks, lang) {
  if (!extlinks || extlinks.length === 0) return;

  // Remplir le premier lien externe (il existe déjà dans le bloc)
  if (extlinks[0]) {
    $block
      .find(
        `[name="stdyDscr/citation/rspStmt/othId/type_contributor/ExtLink/URI"]`
      )
      .val(extlinks[0].uri)
      .change();

    $block
      .find(
        `select[name="stdyDscr/citation/rspStmt/othId/type_contributor/ExtLink/title_${lang}"]`
      )
      .val(extlinks[0].title)
      .change();
  }

  // Create additional extlinks if needed (only for French to avoid duplicates)
  if (extlinks.length > 1 && lang === "fr") {
    const n = extlinks.length - 1;
    autoRepeatClickContributorExtlinks(
      "#add-PersonPIDContributor",
      n,
      extlinks,
      lang
    );
  }
}

function autoRepeatClickContributorExtlinks(selector, n, extlinks, lang) {
  const $btnExtlinks = jQuery(selector);
  if (!$btnExtlinks.length) return;

  let j = 0;
  const intervalExtlinks = setInterval(() => {
    if (j < n) {
      $btnExtlinks.trigger("click");

      const $lastItemExtlinks = jQuery(
        "#repeater-PersonPIDContributor .repeater-item"
      ).last();

      if (extlinks[j + 1]) {
        $lastItemExtlinks
          .find(
            `select[name="stdyDscr/citation/rspStmt/othId/type_contributor/ExtLink/title_${lang}"]`
          )
          .val(extlinks[j + 1].title || "")
          .change();

        $lastItemExtlinks
          .find(
            `[name="stdyDscr/citation/rspStmt/othId/type_contributor/ExtLink/URI"]`
          )
          .val(extlinks[j + 1].uri || "")
          .change();
      }

      j++;
    } else {
      clearInterval(intervalExtlinks);
    }
  }, 300);
}

function autoRepeatClickContactPoint(selector, contactData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Validation data
  const validLangs = Object.keys(contactData).filter(
    (lang) =>
      contactData[lang] &&
      Array.isArray(contactData[lang]) &&
      contactData[lang].length > 0
  );

  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Fonction utilitaire pour vérifier si l’objet contient des données significatives
  function hasData(item) {
    if (!item) return false;

    if (item.type === "contact") {
      return !!(item.name && item.name.trim());
    }

    if (item.type === "affiliation") {
      return !!(item.name && item.name.trim());
    }

    return false;
  }

  // Calculer le nombre maximal de blocs nécessaires (utiliser la première langue valide pour déterminer la structure)
  const firstLang = validLangs[0];
  const firstData = contactData[firstLang];
  const contacts = firstData.filter(
    (item) => item.type === "contact" && hasData(item)
  );
  const affiliations = firstData.filter(
    (item) => item.type === "affiliation" && hasData(item)
  );
  const totalBlocks = Math.max(contacts.length, affiliations.length);

  // Créer des blocs supplémentaires si nécessaire
  if (totalBlocks > 1) {
    let i = 1;
    const interval = setInterval(() => {
      if (i < totalBlocks) {
        $btn.trigger("click");

        const $lastItem = jQuery(
          "#repeater-ContactPoint .repeater-item"
        ).last();

        // Remplir pour toutes les langues
        validLangs.forEach((lang) => {
          const data = contactData[lang];
          const langContacts = data.filter(
            (item) => item.type === "contact" && hasData(item)
          );
          const langAffiliations = data.filter(
            (item) => item.type === "affiliation" && hasData(item)
          );

          fillContactBlock(
            $lastItem,
            langContacts[i],
            langAffiliations[i],
            lang
          );
        });

        i++;
      } else {
        clearInterval(interval);

        // Remplir le premier bloc après la création de tous les blocs
        const $firstItem = jQuery(
          "#repeater-ContactPoint .repeater-item"
        ).first();

        validLangs.forEach((lang) => {
          const data = contactData[lang];
          const langContacts = data.filter(
            (item) => item.type === "contact" && hasData(item)
          );
          const langAffiliations = data.filter(
            (item) => item.type === "affiliation" && hasData(item)
          );

          fillContactBlock(
            $firstItem,
            langContacts[0],
            langAffiliations[0],
            lang
          );
        });

        if (typeof callback === "function") callback();
      }
    }, 300);
  } else {
    // Un seul bloc, le remplir immédiatement
    const $firstItem = jQuery("#repeater-ContactPoint .repeater-item").first();

    validLangs.forEach((lang) => {
      const data = contactData[lang];
      const langContacts = data.filter(
        (item) => item.type === "contact" && hasData(item)
      );
      const langAffiliations = data.filter(
        (item) => item.type === "affiliation" && hasData(item)
      );

      fillContactBlock($firstItem, langContacts[0], langAffiliations[0], lang);
    });

    if (typeof callback === "function") callback();
  }
}

function fillContactBlock($block, contact, affiliation, lang) {
  if (contact) {
    $block
      .find(`[name="stdyDscr/citation/distStmt/contact_${lang}"]`)
      .val(contact.name || "")
      .change();

    if (contact.email) {
      $block
        .find(`[name="stdyDscr/citation/distStmt/contact/email"]`)
        .val(contact.email)
        .change();
    }
  }

  if (affiliation) {
    $block
      .find(`[name="stdyDscr/citation/distStmt/contact/affiliation_${lang}"]`)
      .val(affiliation.name || "")
      .change();

    if (affiliation.extlink?.uri) {
      $block
        .find(
          `[name="stdyDscr/citation/distStmt/contact/affiliation/ExtLink/URI"]`
        )
        .val(affiliation.extlink.uri)
        .change();
    }

    if (affiliation.extlink?.title) {
      const $select = $block.find(
        `select[name="stdyDscr/citation/distStmt/contact/affiliation/ExtLink/title_${lang}"]`
      );
      $select.val(affiliation.extlink.title).trigger("change");
    }
  }
}

function autoRepeatClickFundingAgent(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Vérifier que allData existe et n'est pas vide
  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Filtrer les langues qui ont des données valides
  const validLangs = Object.keys(allData).filter(
    (lang) =>
      allData[lang]?.data &&
      Array.isArray(allData[lang].data) &&
      allData[lang].data.length > 0
  );

  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // On prend la langue avec le plus grand nombre de blocs pour calculer n
  const n =
    Math.max(...validLangs.map((lang) => allData[lang].data.length)) - 1;

  let i = 0;
  let interval = setInterval(function () {
    if (i < n) {
      $btn.trigger("click");

      const $lastItem = jQuery("#repeater-fundingAgent .repeater-item").last();

      // remplir tous les champs pour toutes les langues
      validLangs.forEach((lang) => {
        const data = allData[lang]?.data ?? [];
        const data_additional = allData[lang]?.additional ?? [];

        // +1 si le premier bloc existait déjà
        if (data[i + 1]) {
          $lastItem
            .find(
              `select[name="stdyDscr/citation/prodStmt/fundAg/ExtLink/title_${lang}"]`
            )
            .val(data[i + 1].title ?? "")
            .change();
          $lastItem
            .find(`[name="stdyDscr/citation/prodStmt/fundAg_${lang}"]`)
            .val(data[i + 1].name ?? "")
            .change();
          $lastItem
            .find(`[name="stdyDscr/citation/prodStmt/fundAg/ExtLink/URI"]`)
            .val(data[i + 1].uri ?? "")
            .change();
          $lastItem
            .find(
              `select[name="additional/fundingAgent/fundingAgentType_${lang}"]`
            )
            .val(data_additional[i + 1] ?? "")
            .change();
        }
      });

      i++;
    } else {
      clearInterval(interval);

      // remplir le premier bloc existant APRÈS la création de tous les autres blocs
      const $firstItem = jQuery(
        "#repeater-fundingAgent .repeater-item"
      ).first();

      validLangs.forEach((lang) => {
        const data = allData[lang]?.data ?? [];
        const data_additional = allData[lang]?.additional ?? [];

        if (data[0]) {
          $firstItem
            .find(
              `select[name="stdyDscr/citation/prodStmt/fundAg/ExtLink/title_${lang}"]`
            )
            .val(data[0].title ?? "")
            .change();
          $firstItem
            .find(`[name="stdyDscr/citation/prodStmt/fundAg_${lang}"]`)
            .val(data[0].name ?? "")
            .change();
          $firstItem
            .find(`[name="stdyDscr/citation/prodStmt/fundAg/ExtLink/URI"]`)
            .val(data[0].uri ?? "")
            .change();
          $firstItem
            .find(
              `select[name="additional/fundingAgent/fundingAgentType_${lang}"]`
            )
            .val(data_additional[0] ?? "")
            .change();
        }
      });

      if (typeof callback === "function") callback();
    }
  }, 300);
}

function autoRepeatClickInerExpo(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // On prend la langue avec le plus grand nombre de blocs pour calculer n
  const n = Math.max(...Object.values(allData).map((lang) => lang.length)) - 1;

  let i = 0;
  const interval = setInterval(() => {
    if (i < n) {
      $btn.trigger("click");

      const $lastItem = jQuery("#repeater-intervExpo .repeater-item").last();

      // remplir tous les champs pour toutes les langues
      Object.keys(allData).forEach((lang) => {
        const data = allData[lang];
        fillInterventionItem($lastItem, i + 1, data, lang);
      });

      i++;
    } else {
      clearInterval(interval);

      const $firstItem = jQuery("#repeater-intervExpo .repeater-item").first();

      // remplir le premier bloc existant pour toutes les langues
      Object.keys(allData).forEach((lang) => {
        fillInterventionItem($firstItem, 0, allData[lang], lang);
      });

      if (typeof callback === "function") callback();
    }
  }, 300);
}

function fillInterventionItem($item, idx, data, lang) {
  if (!data[idx]) return;

  $item
    .find(`[name="additional/intervention/interventionName_${lang}"]`)
    .val(data[idx]["name"] || "")
    .change();

  $item
    .find(`select[name="additional/intervention/interventionType_${lang}"]`)
    .val(data[idx]["type"] || "")
    .change();

  $item
    .find(`[name="additional/intervention/interventionDescription_${lang}"]`)
    .val(data[idx]["description"] || "")
    .change();

  // Handle the "Other" intervention type field if it exists
  if (data[idx]["typeOther"]) {
    $item
      .find(`[name="additional/intervention/interventionTypeOther_${lang}"]`)
      .val(data[idx]["typeOther"] || "")
      .change();
  }
}

function autoRepeatClickInformationContact(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Vérifier que allData existe et n'est pas vide
  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Filtrer les langues qui ont des données valides
  const validLangs = Object.keys(allData).filter(
    (lang) =>
      allData[lang] && Array.isArray(allData[lang]) && allData[lang].length > 0
  );

  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // On prend la langue avec le plus grand nombre de blocs pour calculer n
  const n = Math.max(...validLangs.map((lang) => allData[lang].length)) - 1;
  let i = 0;
  const interval = setInterval(() => {
    if (i < n) {
      $btn.trigger("click");

      const $lastItem = jQuery(
        "#repeater-DataInformationContact .repeater-item"
      ).last();

      // remplir tous les champs pour toutes les langues valides
      validLangs.forEach((lang) => {
        const data = allData[lang];
        if (data[i + 1]) {
          $lastItem
            .find(`[name="stdyDscr/dataAccs/useStmt/contact_${lang}"]`)
            .val(data[i + 1])
            .change();
        }
      });

      i++;
    } else {
      clearInterval(interval);

      const $firstItem = jQuery(
        "#repeater-DataInformationContact .repeater-item"
      ).first();

      // remplir le premier bloc existant pour toutes les langues valides
      validLangs.forEach((lang) => {
        const data = allData[lang];
        if (data[0]) {
          $firstItem
            .find(`[name="stdyDscr/dataAccs/useStmt/contact_${lang}"]`)
            .val(data[0])
            .change();
        }
      });

      if (typeof callback === "function") callback();
    }
  }, 300);
}

function autoRepeatClickStandardName(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Vérifier que allData existe et n'est pas vide
  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Filtrer les langues qui ont des données valides
  const validLangs = Object.keys(allData).filter(
    (lang) =>
      allData[lang] && Array.isArray(allData[lang]) && allData[lang].length > 0
  );

  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // On prend la langue avec le plus grand nombre de blocs pour calculer n
  const n = Math.max(...validLangs.map((lang) => allData[lang].length)) - 1;
  let i = 0;
  const interval = setInterval(() => {
    if (i < n) {
      $btn.trigger("click");

      const $lastItem = jQuery("#repeater-standards .repeater-item").last();

      // remplir tous les champs pour toutes les langues valides
      validLangs.forEach((lang) => {
        const data = allData[lang];
        if (data[i + 1]) {
          $lastItem
            .find(
              `[name="stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName_${lang}"]`
            )
            .val(data[i + 1])
            .change();
        }
      });

      i++;
    } else {
      clearInterval(interval);

      const $firstItem = jQuery("#repeater-standards .repeater-item").first();

      // remplir le premier bloc existant pour toutes les langues valides
      validLangs.forEach((lang) => {
        const data = allData[lang];
        if (data[0]) {
          $firstItem
            .find(
              `[name="stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName_${lang}"]`
            )
            .val(data[0])
            .change();
        }
      });

      if (typeof callback === "function") callback();
    }
  }, 300);
}

function autoRepeatClickComplianceDescription(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Vérifier que allData existe et n'est pas vide
  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Filtrer les langues qui ont des données valides
  const validLangs = Object.keys(allData).filter(
    (lang) =>
      allData[lang] && Array.isArray(allData[lang]) && allData[lang].length > 0
  );

  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // On prend la langue avec le plus grand nombre de blocs pour calculer n
  const n = Math.max(...validLangs.map((lang) => allData[lang].length)) - 1;
  let i = 0;
  const interval = setInterval(() => {
    if (i < n) {
      $btn.trigger("click");

      const $lastItem = jQuery("#repeater-DataQuality .repeater-item").last();

      // remplir tous les champs pour toutes les langues valides
      validLangs.forEach((lang) => {
        const data = allData[lang];
        if (data[i + 1]) {
          $lastItem
            .find(
              `[name="stdyDscr/stdyInfo/qualityStatement/complianceDescription_${lang}"]`
            )
            .val(data[i + 1])
            .change();
        }
      });

      i++;
    } else {
      clearInterval(interval);

      const $firstItem = jQuery("#repeater-DataQuality .repeater-item").first();

      // remplir le premier bloc existant pour toutes les langues valides
      validLangs.forEach((lang) => {
        const data = allData[lang];
        if (data[0]) {
          $firstItem
            .find(
              `[name="stdyDscr/stdyInfo/qualityStatement/complianceDescription_${lang}"]`
            )
            .val(data[0])
            .change();
        }
      });

      if (typeof callback === "function") callback();
    }
  }, 300);
}

function autoRepeatClickSources(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;
  // Vérifier que allData existe et n'est pas vide
  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Filtrer les langues qui ont des données valides
  const validLangs = Object.keys(allData).filter(
    (lang) =>
      allData[lang] && Array.isArray(allData[lang]) && allData[lang].length > 0
  );

  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // On prend la langue avec le plus grand nombre de blocs pour calculer n
  const n = Math.max(...validLangs.map((lang) => allData[lang].length)) - 1;
  let i = 0;
  const interval = setInterval(() => {
    if (i == 0) {
      clearInterval(interval);
      const $firstItem = jQuery("#repeater-sources .repeater-item").first();
      // remplir le premier bloc existant pour toutes les langues valides
      validLangs.forEach((lang) => {
        fillSourcesItem($firstItem, 0, allData[lang], lang);
      });

      if (typeof callback === "function") callback();
    }
    if (i < n) {
      $btn.trigger("click");
      const $lastItem = jQuery("#repeater-sources .repeater-item").last();
      // remplir tous les champs pour toutes les langues valides
      validLangs.forEach((lang) => {
        const data = allData[lang];
        fillSourcesItem($lastItem, i + 1, data, lang);
      });

      i++;
    } else {
    }
  }, 0);
}

function fillSourcesItem($item, idx, data, lang) {
  if (!data[idx]) {
    return;
  }

  const item = data[idx];

  // Fill citation field
  const $citation = $item.find(
    `[name="stdyDscr/method/dataColl/sources/sourceCitation_${lang}"]`
  );
  $citation.val(item["citation"] || "").change();

  // Fill source purpose select
  const $sourcePurpose = $item.find(
    `select[name="stdyDscr/method/dataColl/sources/sourceCitation/notes/subject_sourcePurpose_${lang}"]`
  );
  $sourcePurpose.val(item["notes"]?.["subject_sourcePurpose"] || "").change();

  // Handle srcOrig checkboxes
  if (item.srcOrig && Array.isArray(item.srcOrig)) {
    // Check if checkboxes exist in this item
    const $checkboxes = $item.find(
      `input[name="stdyDscr/method/dataColl/sources/srcOrig_${lang}[]"]`
    );
    setFieldValues(
      item.srcOrig,
      `stdyDscr/method/dataColl/sources/srcOrig_${lang}[]`,
      "checkbox",
      $item
    );
  }

  // Fill other source type
  const $otherType = $item.find(
    `[name="additional/thirdPartySource/otherSourceType_${lang}"]`
  );
  $otherType.val(item["otherSourceType"] || "").change();
}

function autoRepeatClickBras(selector, n, data, data_additional) {
  let $btn = jQuery(selector);

  if ($btn.length) {
    let i = 0;
    let interval = setInterval(function () {
      if (i < n) {
        $btn.trigger("click");

        // remplir le dernier bloc créé
        let $lastItem = jQuery("#repeater-bras .repeater-item").last(); // adapter la classe

        if (data[i + 1]) {
          // +1 si le premier bloc existait déjà
          $lastItem
            .find(
              'select[name="stdyDscr/studyAuthorization/authorizingAgency_fr"]'
            )
            .val(data[i + 1]["name"])
            .change();
          $lastItem
            .find(
              '[name="additional/obtainedAuthorization/otherAuthorizingAgency_fr"]'
            )
            .val(data_additional[i + 1])
            .change();
        }

        i++;
      } else {
        clearInterval(interval);
      }
    }, 300);
  }

  // remplir le premier bloc existant
  if (data[0]) {
    jQuery("#repeater-bras .repeater-item")
      .first()
      .find('select[name="stdyDscr/studyAuthorization/authorizingAgency_fr"]')
      .val(data[0]["name"])
      .change();
    jQuery("#repeater-bras .repeater-item")
      .first()
      .find(
        '[name="additional/obtainedAuthorization/otherAuthorizingAgency_fr"]'
      )
      .val(data_additional[0])
      .change();
  }
}

function autoFillArms(data, lang) {
  if (!data || !data.length) return;

  // Remplir le nombre de bras
  const $input = jQuery('[data-inputToDuplicate="armsBloc"] input');
  jQuery('[data-contentToDuplicate="armsBloc"]')
    .not(".d-none")
    .each(function (index) {
      const arm = data[index];

      if (arm) {
        jQuery(this)
          .find(`select[name^="additional/arms/armsType_${lang}"]`)
          .val(arm["type"])
          .trigger("change");
        jQuery(this)
          .find(`input[name^="additional/arms/armsTypeOther_${lang}"]`)
          .val(arm.typeOther)
          .trigger("change");

        jQuery(this)
          .find(`input[name^="additional/arms/armsName_${lang}"]`)
          .val(arm.name)
          .trigger("change");
        jQuery(this)
          .find(`input[name^="additional/arms/armsDescription_${lang}"]`)
          .val(arm.description)
          .trigger("change");
      }
    });
}

function autoFillInclusionGroups(data, lang) {
  if (!data || !data.length) return;

  // Mettre le nombre de groupes
  const $input = jQuery('[data-inputToDuplicate="inclusionGroupsBloc"] input');

  // Attendre la génération automatique des blocs
  jQuery('[data-contentToDuplicate="inclusionGroupsBloc"]')
    .not(".d-none")
    .each(function (index) {
      const group = data[index];
      if (group) {
        jQuery(this)
          .find(`input[name^="additional/inclusionGroups/groupName_${lang}"]`)
          .val(group.name)
          .trigger("change");
        jQuery(this)
          .find(
            `textarea[name^="additional/inclusionGroups/groupDescription_${lang}"]`
          )
          .val(group.description)
          .trigger("change");
        jQuery(this)
          .find(
            `textarea[name^="additional/inclusionGroups/groupInterventionExposition_${lang}"]`
          )
          .val(group.interventionExposition)
          .trigger("change");
      }
    });
}

function autoRepeatClickDatasetPID(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Par précaution : si allData est manquant ou vide, terminer
  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // On prend la langue avec le plus grand nombre de blocs pour calculer n
  const n = Math.max(...Object.values(allData).map((lang) => lang.length)) - 1;

  let i = 0;
  const interval = setInterval(() => {
    if (i < n) {
      $btn.trigger("click");

      const $lastItem = jQuery("#repeater-DatasetPID .repeater-item").last();

      // remplir tous les champs pour toutes les langues
      Object.keys(allData).forEach((lang) => {
        const data = allData[lang];
        fillDatasetPIDItem($lastItem, i + 1, data, lang);
      });

      i++;
    } else {
      clearInterval(interval);

      const $firstItem = jQuery("#repeater-DatasetPID .repeater-item").first();

      // remplir le premier bloc existant pour toutes les langues
      Object.keys(allData).forEach((lang) => {
        fillDatasetPIDItem($firstItem, 0, allData[lang], lang);
      });

      if (typeof callback === "function") callback();
    }
  }, 300);
}

function fillDatasetPIDItem($item, idx, data, lang) {
  if (!data || !data[idx]) return;

  $item
    .find(`select[name="stdyDscr/citation/IDno/agentSchema_${lang}"]`)
    .val(data[idx]["agentSchema"] || "")
    .change();

  $item
    .find(`[name="stdyDscr/citation/IDno/otherAgent_${lang}"]`)
    .val(data[idx]["agentOther"] || "")
    .change();

  $item
    .find(`[name="stdyDscr/citation/IDno/uri_${lang}"]`)
    .val(data[idx]["uri"] || "")
    .change();
}

function parsePseudoArray(str) {
  if (typeof str !== "string") return str;

  // Remplacer uniquement les délimiteurs de tableau
  let cleaned = str
    .replace(/^\['/, '["') // début
    .replace(/','/g, '","') // séparateurs
    .replace(/'\]$/, '"]'); // fin

  try {
    return JSON.parse(cleaned);
  } catch (e) {
    console.error("parsePseudoArray error:", e, "input:", str);
    return [];
  }
}

// Fonction pour masquer/montrer les champs d'une langue
function showLanguageFields(lang) {
  jQuery(".lang-label, .lang-input").addClass("d-none");

  // afficher ceux de la langue sélectionnée
  let $targets = jQuery(
    `.lang-label[attr-lng='${lang}'], .lang-input[attr-lng='${lang}']`
  );
  if ($targets.length) {
    $targets.removeClass("d-none");
  }
}

function removeEmptyContactBlocks() {
  jQuery("#repeater-ContactPoint .repeater-item").each(function () {
    const $item = jQuery(this);

    // Vérifier si tous les champs sont vides
    const contactVal = $item
      .find('[name^="stdyDscr/citation/distStmt/contact_"]')
      .val();
    const emailVal = $item
      .find('[name="stdyDscr/citation/distStmt/contact/email"]')
      .val();
    const affiliationVal = $item
      .find('[name^="stdyDscr/citation/distStmt/contact/affiliation_"]')
      .val();
    const uriVal = $item
      .find(
        '[name="stdyDscr/citation/distStmt/contact/affiliation/ExtLink/URI"]'
      )
      .val();
    const titleVal = $item
      .find(
        'select[name^="stdyDscr/citation/distStmt/contact/affiliation/ExtLink/title_"]'
      )
      .val();

    if (!contactVal && !emailVal && !affiliationVal && !uriVal && !titleVal) {
      // Supprimer le bloc vide
      $item.remove();
    }
  });
}

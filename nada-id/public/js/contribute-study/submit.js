// ========== submit.js - Gestion du submit / AJAX ========== //
/**
 * Gestion des formulaires
 * Envoi via AJAX
 * Reset ou feedback après submit
 */

export function submitJs() {}

let allStudiesProcessed = false;

window.saveStudy = function ($, submitter, btnText, spinner, autoSave) {
  const form = $("#form-add-study");

  form.attr("novalidate", true).attr("method", "post");

  const formData = new FormData(form[0]);

  // --- Résultat final (structure de sortie) ---
  const result = {
    fundingAgencies_fr: [],
    fundingAgencies_en: [],

    fundingAgentTypes_fr: [],
    fundingAgentTypes_en: [],

    otherFundingAgentTypes_fr: [],
    otherFundingAgentTypes_en: [],

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

    keywords_fr: [],
    keywords_en: [],

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

    otherSourceType_fr: [],
    otherSourceType_en: [],

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

    other_idnos_fr: [],
    other_idnos_en: [],

    methodNotes_fr: [],
    methodNotes_en: [],

    relatedDocument_fr: [],
    relatedDocument_en: [],

    datasetPIDs_fr: [],
    datasetPIDs_en: [],

    producers_fr: [],
    producers_en: [],

    producersTypes_fr: [],
    producersTypes_en: [],

    otherProducersTypes_fr: [],
    otherProducersTypes_en: [],

    individual_data_access_fr: null,
    individual_data_access_en: null,
  };

  const pushTopicsIfNotEmpty = (vals, vocab) => {
    if (!vocab || !vals) return;
    for (const v of vals.fr || []) {
      if (v != null && String(v).trim() !== "")
        result.topics_fr.push({ topic: String(v), vocab });
    }
    for (const v of vals.en || []) {
      if (v != null && String(v).trim() !== "")
        result.topics_en.push({ topic: String(v), vocab });
    }
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

  const fattList = (lang) =>
    lang === "fr" ? result.fundingAgentTypes_fr : result.fundingAgentTypes_en;

  const ofattList = (lang) =>
    lang === "fr"
      ? result.otherFundingAgentTypes_fr
      : result.otherFundingAgentTypes_en;

  const callModesList = (lang) =>
    lang === "fr" ? result.callModes_fr : result.callModes_en;

  const agenciesList = (lang) =>
    lang === "fr" ? result.agencies_fr : result.agencies_en;

  const otherAgenciesList = (lang) =>
    lang === "fr" ? result.otherAgencies_fr : result.otherAgencies_en;

  const pushFunding = (lang, name = "") => {
    if (!lang) return;
    faList(lang).push({
      name,
      extlink: [],
    });
  };

  const touchLastFunding = (lang) => {
    const list = faList(lang);
    if (list.length === 0) pushFunding(lang, "");
    return list[list.length - 1];
  };

  // ---------- DATASETPID (FR/EN) HELPERS ----------
  const pidList = (lng) =>
    lng === "fr" ? result.datasetPIDs_fr : result.datasetPIDs_en;

  const pidSkeleton = () => ({
    type: "", // .../IDno/agent  (DOI, Handle, ROR, etc.)
    uri: "", // .../IDno        (URL or code)
  });

  const ensurePIDLang = (lng) => {
    const list = pidList(lng);
    if (!list.length) list.push(pidSkeleton());
    return list[list.length - 1];
  };

  const pushPIDLang = (lng) => pidList(lng).push(pidSkeleton());

  // Helpers
  const aeList = (lng) =>
    lng === "fr" ? result.authEntities_fr : result.authEntities_en;

  const piSkeleton = (firstname = "") => ({
    name: "",
    firstname,
    lastname: "",
    type: "investigator",
    extlink: [
      { title: "ORCID", uri: "", role: "pi id" },
      { title: "IdRef", uri: "", role: "pi id" },
      { title: "RNSR", uri: "", role: "labo id" },
    ],
    email: "",
    affiliationName: "",
    PILabo: "",
    isContact: "",
  });

  const ensurePILang = (lng) => {
    const list = aeList(lng);
    if (!list.length) list.push(piSkeleton(""));
    return list[list.length - 1];
  };

  const pushPILang = (lng, firstname) =>
    aeList(lng).push(piSkeleton(firstname));

  // ---- Helpers IDno (repeater) ----
  const idnoList = (lang) =>
    lang === "fr" ? result.idnos_fr : result.idnos_en;

  const pushNewIdno = (lang) => {
    if (!lang) return;
    idnoList(lang).push({ uri: "", agentSchema: "", agentOther: "" });
  };

  // ---- NEW: per-language cursor for non-indexed repeater ----
  const idnoCursor = { fr: -1, en: -1 };
  const currentIdno = (lang) => idnoList(lang)[idnoCursor[lang]];
  const startNewIdno = (lang) => {
    pushNewIdno(lang);
    idnoCursor[lang]++;
    return currentIdno(lang);
  };

  const notesList = (lang) =>
    lang === "fr" ? result.methodNotes_fr : result.methodNotes_en;

  const upsertNote = (lang, subject) => {
    const list = notesList(lang);
    const s = String(subject || "").toLowerCase();
    let row = list.find((n) => String(n.subject).toLowerCase() === s);
    if (!row) {
      row = { subject, values: [] };
      list.push(row);
    }
    return row;
  };

  // ---- Helpers: other IDNo (agency/code) ----
  const otherIdnoList = (lang) =>
    lang === "fr" ? result.other_idnos_fr : result.other_idnos_en;

  const ensureOtherIdnoAt = (lang, idx) => {
    const list = otherIdnoList(lang);
    while (list.length <= idx) list.push({ agency: "", code: "" });
    return list[idx];
  };

  const peekLastOtherIdno = (lang) => {
    const list = otherIdnoList(lang);
    return list.length ? list[list.length - 1] : null;
  };

  const pushOtherIdno = (lang, agency = "", code = "") => {
    if (!lang) return;
    otherIdnoList(lang).push({ agency, code });
  };

  // ---- Helpers: arms (repeater) ----
  const armsList = (lang) => (lang === "fr" ? result.arms_fr : result.arms_en);

  const pushArm = (lang, name = "") => {
    if (!lang) return;
    armsList(lang).push({
      name,
      type: "",
      typeOther: "",
      description: "",
    });
  };

  const touchLastArm = (lang) => {
    const list = armsList(lang);
    if (!list.length) pushArm(lang, "");
    return list[list.length - 1];
  };

  //---- Helpers relatedDocument ----

  const docsList = (lang) =>
    lang === "fr" ? result.relatedDocument_fr : result.relatedDocument_en;

  const pushDocs = (lang, type = "") => {
    if (!lang) return;
    docsList(lang).push({
      type,
      title: "",
      link: "",
    });
  };

  const touchLastDocs = (lang) => {
    const list = docsList(lang);
    if (!list.length) pushDocs(lang, "");
    return list[list.length - 1];
  };

  // helpears sponsor ----
  const producersList = (lang) =>
    lang === "fr" ? result.producers_fr : result.producers_en;

  const opList = (lang) =>
    lang === "fr"
      ? result.otherProducersTypes_fr
      : result.otherProducersTypes_en;

  const ptList = (lang) =>
    lang === "fr" ? result.producersTypes_fr : result.producersTypes_en;

  const pushProducers = (lang, name = "") => {
    if (!lang) return;
    producersList(lang).push({
      name,
      role: "sponsor",
      extlink: [],
    });
  };

  const touchLastProducers = (lang) => {
    const list = producersList(lang);
    if (list.length === 0) pushProducers(lang, "");
    return list[list.length - 1];
  };

  //---- Helpers useStmtContacts ----

  const contactList = (lang) =>
    lang === "fr" ? result.useStmtContacts_fr : result.useStmtContacts_en;

  const pushContact = (lang, firstname = "") => {
    if (!lang) return;
    contactList(lang).push({
      name: "",
      firstname,
      lastname: "",
      email: "",
    });
  };

  const touchLastContact = (lang) => {
    const list = contactList(lang);
    if (!list.length) pushContact(lang, "");
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
      typeOther: "",
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

  const otherSourceTypeList = (lang) =>
    lang === "fr" ? result.otherSourceType_fr : result.otherSourceType_en;

  const pushNewSource = (lang) => {
    if (!lang) return;
    sourcesList(lang).push({
      sourceCitation: {
        titlStmt: {
          titl: "",
        },
        holdings: "",
        notes: {
          subject: "",
          value: "",
        },
      },
      srcOrig: [],
    });
    otherSourceTypeList(lang).push("");
  };

  const touchLastSource = (lang) => {
    const list = sourcesList(lang);
    if (!list.length) pushNewSource(lang);
    return list[list.length - 1];
  };

  // ---- Helpers: inclusionGroups (indexed repeater) ----
  const inclusionGroupsList = (lang) =>
    lang === "fr" ? result.inclusionGroups_fr : result.inclusionGroups_en;

  const peekLastInclusionGroup = (lang) => {
    const list = inclusionGroupsList(lang);
    return list.length ? list[list.length - 1] : null;
  };

  const pushInclusionGroup = (lang, name = "") => {
    if (!lang) return;
    inclusionGroupsList(lang).push({
      name,
      description: "",
    });
  };

  const touchLastInclusionGroup = (lang) => {
    const list = inclusionGroupsList(lang);
    if (!list.length) pushInclusionGroup(lang, "");
    return list[list.length - 1];
  };

  // ---- Helpers ----
  const oiList = (lng) => (lng === "fr" ? result.othIds_fr : result.othIds_en);
  //Crée un membre complet : contributor + affiliation
  const pushTeamMember = (lng, data = {}) => {
    const contributor = {
      name: "",
      firstname: data.firstName || "",
      lastname: data.lastName || "",
      type: "contributor",
      isContact: data.isContact || "", // true or false
      extlink: [
        {
          title: "ORCID",
          uri: "",
          role: "team member id",
        },
        {
          title: "IdRef",
          uri: "",
          role: "team member id",
        },
      ],
    };

    const affiliation = {
      name: data.affName || "",
      type: "affiliation",
      teamMemberLabo: data.teamMemberLabo || "",
      extlink: [
        {
          title: "RNSR",
          uri: "",
          role: "labo id",
        },
      ],
    };

    oiList(lng).push(contributor, affiliation);
  };

  //Obtenir la dernière affiliation pour ajout ROR, RNSR...
  const lastAff = (lng) => {
    const list = oiList(lng);
    return [...list].reverse().find((i) => i.type === "affiliation") || null;
  };

  //Obtenir le dernier contributeur
  const lastContrib = (lng) => {
    const list = oiList(lng);
    return [...list].reverse().find((i) => i.type === "contributor") || null;
  };

  // Helpers
  const dcList = (lng) =>
    lng === "fr" ? result.distContacts_fr : result.distContacts_en;

  const dcSkeleton = (firstname = "") => ({
    name: "", // <— computed value
    lastname: "", // stdyDscr/citation/distStmt/contact/lastname_{fr|en}
    firstname, // stdyDscr/citation/distStmt/contact/name_{fr|en}
    type: "contact",
    email: "", // stdyDscr/citation/distStmt/contact/email
    affiliationName: "", // stdyDscr/citation/distStmt/contact/affiliation_{fr|en}
    contactPointLabo: "", // stdyDscr/citation/distStmt/contact/contactPointLabo_{fr|en}
    extlink: [
      {
        title: "",
        uri: "",
        role: "organisation id",
      },
      {
        title: "RNSR",
        uri: "",
        role: "labo id",
      },
    ],
  });

  const ensureDCLang = (lng) => {
    const list = dcList(lng);
    if (!list.length) list.push(dcSkeleton(""));
    return list.length ? list[list.length - 1] : null;
  };

  const pushDCLang = (lng, name) => dcList(lng).push(dcSkeleton(name));
  // Apply a setter to both lang “last” items (creating placeholders if needed)
  const setDCBoth = (setter) => {
    setter(ensureDCLang("fr"));
    setter(ensureDCLang("en"));
  };

  // ------- TOPICS accumulators (subject/topcclas) -------

  // Groupe A: topcclas[]/value_{fr|en}[] + topcclas[]/vocab // Health theme
  const topics_main_vals = { fr: [], en: [] };
  let topics_main_vocab = null;

  // Groupe B: topcclas_{fr|en}[] + topcclas/vocab
  const topics_det_vals = { fr: [], en: [] };
  let topics_det_vocab = null;

  //other_socio_demographic_determinant
  const topics_osdd_vals = { fr: [], en: [] };
  let topics_osdd_vocab = null;

  const topics_other_health_vals = { fr: [], en: [] };
  let topics_other_health_vocab = null;

  const topics_env_vals = { fr: [], en: [] };
  let topics_env_vocab = null;

  const topics_healthcare_vals = { fr: [], en: [] };
  let topics_healthcare_vocab = null;

  const topics_behavioral_vals = { fr: [], en: [] };
  let topics_behavioral_vocab = null;

  const topics_biological_vals = { fr: [], en: [] };
  let topics_biological_vocab = null;

  //other determinant
  const topics_od_vals = { fr: [], en: [] };
  let topics_od_vocab = null;

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
    const selectorInput = `input[name='${CSS.escape(rawKey)}'][value='${CSS.escape(value)}']`;
    const selectorSelect = `select[name='${CSS.escape(rawKey)}'] option[value='${CSS.escape(value)}']`;

    // -------------------- MODES DE COLLECTE (callModes)

    if (key.includes("stdydscr/method/datacoll/collmode_fr[]")) {
      const cessda = $(selectorInput).attr("data-uri");
      const concept = { vocab: "CESSDA", vocabURI: cessda };
      callModesList("fr").push(
        JSON.stringify({ concept: concept, value: value }),
      );
      continue;
    }

    if (key.includes("stdydscr/method/datacoll/collmode_en[]")) {
      const cessda = $(selectorInput).attr("data-uri");
      const concept = { vocab: "CESSDA", vocabURI: cessda };
      callModesList("en").push(
        JSON.stringify({ concept: concept, value: value }),
      );
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
      key.includes("additional/obtainedauthorization/otherauthorizingagency_fr")
    ) {
      otherAgenciesList("fr").push(value);
      continue;
    }
    if (
      key.includes("additional/obtainedauthorization/otherauthorizingagency_en")
    ) {
      otherAgenciesList("en").push(value);
      continue;
    }

    // ----------------------------------------------------------------------
    // -------------------- FINANCEMENT (fundAg*, fundingAgentType*)
    // ----------------------------------------------------------------------

    if (/additional\/fundingAgent\/fundingAgentType_(fr|en)$/i.test(key)) {
      if (lang && value) fattList(lang).push(value);
      continue;
    }
    if (/additional\/fundingAgent\/otherFundingAgentType_(fr|en)$/i.test(key)) {
      if (lang) ofattList(lang).push(value);
      continue;
    }
    // Nom de l'agence (ex: fundAg_fr / fundAg_en)
    if (/fundag_(fr|en)$/i.test(key)) {
      if (lang) {
        faList(lang).push({ name: value }); // commence un nouveau bloc financeur
      }
      continue;
    }

    // --- FINANCEUR ID (Via le JSON de l'autocomplete : fundAg/institution_json) ---
    if (/fundag\/institution_json$/i.test(key)) {
      if (value && value !== "") {
        const orgDataF = JSON.parse(value);
        ["fr", "en"].forEach((lng) => {
          const f = touchLastFunding(lng);
          f.extlink = orgDataF.map((item) => ({
            title: item.title,
            uri: item.uri,
          }));
        });
      }
      continue;
    }

    // ==================== producers  =====================
    if (/additional\/sponsor\/sponsorType_(fr|en)$/i.test(key)) {
      if (lang && value) ptList(lang).push(value);
      continue;
    }
    if (/additional\/sponsor\/otherSponsorType_(fr|en)$/i.test(key)) {
      if (lang) opList(lang).push(value);
      continue;
    }
    if (/producer_(fr|en)$/i.test(key)) {
      if (lang) {
        producersList(lang).push({ name: value, role: "sponsor" });
      }
      continue;
    }
    // --- PRODUCER / SPONSOR ID (Via le JSON de l'autocomplete) ---
    if (/prodstmt\/producer\/institution_json$/i.test(key)) {
      if (value && value !== "") {
        const orgDataP = JSON.parse(value);
        ["fr", "en"].forEach((lng) => {
          const p = touchLastProducers(lng);
          p.extlink = orgDataP.map((item) => ({
            title: item.title,
            uri: item.uri,
            role: "sponsor id",
          }));
        });
      }
      continue;
    }

    // ===================== PRIMARY INVESTIGATOR (per-language) =====================
    // Name (suffixed) -> create a NEW row for that language
    if (
      /^stdydscr\/citation\/rspstmt\/authenty\/firstname_(fr|en)$/i.test(key)
    ) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      pushPILang(lng, value);
      continue;
    }
    if (
      /^stdydscr\/citation\/rspstmt\/authenty\/lastname_(fr|en)$/i.test(key)
    ) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      ensurePILang(lng).lastname = value;
      continue;
    }

    // Affiliation NAME textarea you asked to capture
    // stdyDscr/citation/rspStmt/AuthEnty/affiliation_{fr|en}
    if (
      /^stdydscr\/citation\/rspstmt\/authenty\/affiliation_(fr|en)$/i.test(key)
    ) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      ensurePILang(lng).affiliationName = value;
      continue;
    }
    // Email (optional; comment out if you don't want it in payload)
    if (/^additional\/primaryinvestigator\/pimail_(fr|en)$/i.test(key)) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      const pi = ensurePILang(lng);
      pi.email = value; // remove if you must not output email
      continue;
    }

    // ORCID
    if (
      /^stdydscr\/citation\/rspstmt\/authenty\/extlink\/orcid_(fr|en)$/i.test(
        key,
      )
    ) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      ensurePILang(lng).extlink[0].uri = value;
      continue;
    }

    // IdRef
    if (
      /^stdydscr\/citation\/rspstmt\/authenty\/extlink\/idref_(fr|en)$/i.test(
        key,
      )
    ) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      ensurePILang(lng).extlink[1].uri = value;
      continue;
    }

    if (
      /^stdydscr\/citation\/rspstmt\/authenty\/extlink\/rnsr_(fr|en)$/i.test(
        key,
      )
    ) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      const pi = ensurePILang(lng);
      const labo = pi.extlink.find((l) => l.role === "labo id");
      if (labo) labo.uri = value;
      continue;
    }

    if (
      /^stdydscr\/citation\/rspstmt\/authenty\/institution_json$/i.test(key)
    ) {
      if (value && value !== "") {
        ["fr", "en"].forEach((lng) => {
          let pi_so = ensurePILang(lng);
          let piIds = pi_so.extlink.filter((l) => l.role === "pi id");
          let laboId = pi_so.extlink.find((l) => l.role === "labo id") || {
            title: "RNSR",
            uri: "",
            role: "labo id",
          };

          const orgData_so = JSON.parse(value);
          const mappedOrgs = orgData_so.map((item) => ({
            title: item.title,
            uri: item.uri,
            role: "organisation id",
          }));

          pi_so.extlink = [...piIds, ...mappedOrgs, laboId];
        });
      }
      continue;
    }
    if (
      /^additional\/primaryInvestigator\/piAffiliation\/piLabo_(fr|en)$/i.test(
        key,
      )
    ) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      ensurePILang(lng).PILabo = value;
      continue;
    }

    // PI-contact (radio Yes/No)
    if (/^additional\/primaryinvestigator\/ispicontact_(fr|en)$/i.test(key)) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
        const val = String(value || "").toLowerCase().trim();

        ensurePILang(lng).isContact =
            val === ""
                ? ""
                : ["oui", "yes"].includes(val);
      continue;
    }

    // FIRSTNAME
    if (
      /^stdyDscr\/citation\/rspStmt\/othId\/type_contributor_(fr|en)$/i.test(
        key,
      )
    ) {
      const lng = key.endsWith("_fr") ? "fr" : "en";
      pushTeamMember(lng, { firstName: value });
      continue;
    }

    // LASTNAME
    if (
      /^stdyDscr\/citation\/rspStmt\/othId\/type_contributor\/lastname_(fr|en)$/i.test(
        key,
      )
    ) {
      const lng = key.endsWith("_fr") ? "fr" : "en";

      const c = lastContrib(lng);
      if (c) {
        c.lastname = value;
        c.name = `${c.firstname || ""};${c.lastname || ""}`.trim();
      }
      continue;
    }

    // ORCID
    if (
      /^stdydscr\/citation\/rspstmt\/othid\/extlink\/orcid_(fr|en)$/i.test(key)
    ) {
      const lng = key.endsWith("_fr") ? "fr" : "en";
      const c = lastContrib(lng);
      if (c) c.extlink[0].uri = value;
      continue;
    }

    // IdRef
    if (
      /^stdydscr\/citation\/rspstmt\/othid\/extlink\/idref_(fr|en)$/i.test(key)
    ) {
      const lng = key.endsWith("_fr") ? "fr" : "en";
      const c = lastContrib(lng);
      if (c) c.extlink[1].uri = value;
      continue;
    }

    // Contact
    if (/^additional\/teammember\/isteammembercontact_(fr|en)$/i.test(key)) {
      const lng = key.endsWith("_fr") ? "fr" : "en";
      const c = lastContrib(lng);
      if (c) {
          const val = String(value || "").toLowerCase().trim();

          if (val === "oui" || val === "yes") {
              c.isContact = true;
          } else if (!value) {
              c.isContact = "";
          } else {
              c.isContact = false;
          }
      }

      continue;
    }

    // Nom de l'affiliation
    if (
      /^stdydscr\/citation\/rspstmt\/othid\/(type_)?affiliation_(fr|en)$/i.test(
        key,
      )
    ) {
      const lng = key.endsWith("_fr") ? "fr" : "en";
      const a = lastAff(lng);
      if (a) a.name = value;
      continue;
    }

    if (
      /^stdydscr\/citation\/rspstmt\/othid\/extlink\/rnsr_(fr|en)$/i.test(key)
    ) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      const a = lastAff(lng);
      if (a) {
        // Trouver ou créer l'entrée RNSR
        let rnsr = a.extlink.find((l) => l.role === "labo id");

        if (!rnsr) {
          rnsr = { title: "RNSR", uri: "", role: "labo id" };
          a.extlink.push(rnsr);
        }
        rnsr.uri = value;
      }

      continue;
    }
    if (/^stdydscr\/citation\/rspstmt\/othid\/institution_json$/i.test(key)) {
      if (value && value !== "") {
        const orgDataOI = JSON.parse(value);
        ["fr", "en"].forEach((lng) => {
          let affiliation = lastAff(lng);
          if (!affiliation) {
            return;
          }
          const currentLabo = affiliation.extlink.find(
            (l) => l.role === "labo id",
          ) || {
            title: "RNSR",
            uri: "",
            role: "labo id",
          };
          const mappedOrgs = orgDataOI.map((item) => ({
            title: item.title,
            uri: item.uri,
            role: "organisation id",
          }));

          affiliation.extlink = [...mappedOrgs, currentLabo];
        });
      }
      continue;
    }

    // additional/TeamMember/TeamMemberAffiliation/TeamMemberLabo_fr
    if (
      /^additional\/TeamMember\/TeamMemberAffiliation\/TeamMemberLabo_(fr|en)$/i.test(
        key,
      )
    ) {
      const lng = key.endsWith("_fr") ? "fr" : "en";
      const a = lastAff(lng);
      if (a) a.teamMemberLabo = value;
      continue;
    }

    // -------------------- COLLABORATION NAME --------------------
    if (
      /^stdydscr\/citation\/rspstmt\/othid\/collaboration_(fr|en)$/i.test(key)
    ) {
      const lng = key.endsWith("_fr") ? "fr" : "en";
      const coll = {
        name: value || "",
        type: "collaboration",
      };

      oiList(lng).push(coll);
      continue;
    }

    // ---- stdyDscr/method/notes (three subjects into one array) ----
    /* 1) Follow-up (checkboxes): stdyDscr/method/notes/subject_followUP_{fr|en}[] */
    if (/stdydscr\/method\/notes\/subject_followup_(fr|en)\[\]$/i.test(key)) {
      if (lang) {
        const note = upsertNote(lang, "follow-up");
        const v = String(value).trim();
        if (v) note.values.push(v);
      }
      continue;
    }

    /* 2) Observational study method (single select): stdyDscr/method/notes_{fr|en} */
    if (/stdydscr\/method\/notes_(fr|en)$/i.test(key)) {
      if (lang) {
        const v = String(value).trim();
        if (v) {
          const note = upsertNote(lang, "observational study method");
          note.values = [v]; // single-choice; keep last non-empty
        }
      }
      continue;
    }

    /* 3) Research type (single select): stdyDscr/method/notes/subject_researchType_{fr|en} */
    if (/stdydscr\/method\/notes\/subject_researchtype_(fr|en)$/i.test(key)) {
      if (lang) {
        const v = String(value).trim();
        if (v) {
          const note = upsertNote(lang, "research type");
          note.values = [v]; // single-choice; keep last non-empty
        }
      }
      continue;
    }

    // -------------------- MAPPINGS --------------------

    // Contact NAME — per language (creates a NEW row in that language)
    if (/^stdydscr\/citation\/diststmt\/contact_(fr|en)$/i.test(key)) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      pushDCLang(lng, value);
      continue;
    }
    if (
      /^stdydscr\/citation\/diststmt\/contact\/lastname_(fr|en)$/i.test(key)
    ) {
      if (value) {
        setDCBoth((dc) => {
          dc.lastname = value;
        });
      }
      continue;
    }

    // Contact EMAIL — unsuffixed (applies to the most-recent FR/EN rows separately if they exist,
    // else creates placeholders first, then sets both)
    if (/^stdydscr\/citation\/diststmt\/contact\/email$/i.test(key)) {
      if (value) {
        setDCBoth((dc) => {
          dc.email = value;
        });
      }
      continue;
    }

    // // Affiliation NAME — per language
    // stdyDscr/citation/distStmt/contact/affiliation_{fr|en}
    if (
      /^stdydscr\/citation\/diststmt\/contact\/affiliation_(fr|en)$/i.test(key)
    ) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      if (lng && value) ensureDCLang(lng).affiliationName = value;
      continue;
    }

    if (
      /^stdydscr\/citation\/diststmt\/contact\/institution_json$/i.test(key)
    ) {
      if (value) {
        const orgDataCP = JSON.parse(value);
        setDCBoth((dc) => {
          const laboObject = dc.extlink[1];
          dc.extlink = orgDataCP
            .map((item) => ({
              title: item.title,
              uri: item.uri,
              role: "organisation id",
            }))
            .concat(laboObject);
        });
      }
      continue;
    }

    if (
      /^stdydscr\/citation\/diststmt\/contact\/extlink\/rnsr_(fr|en)$/i.test(
        key,
      )
    ) {
      const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      if (lng && value) {
        const dc = ensureDCLang(lng);
        const labo = dc.extlink.find((link) => link.role === "labo id");
        if (labo) labo.uri = value;
      }
      continue;
    }
    // Laboratory NAME — per language
    // additional/contactPointLabo_{fr|en}
    if (/^additional\/contactPointLabo_(fr|en)$/i.test(key)) {
      const lang = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
      if (lang) {
        ensureDCLang(lang).contactPointLabo = value;
      }
      continue;
    }

    // ======================= TOPICS (subject/topcclas) =======================

    //  Health theme
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/value\/health\s+theme_(fr|en)\[\]$/i.test(
        key,
      )
    ) {
      const esv = $(selectorInput).attr("data-uri-esv");
      const mesh = $(selectorInput).attr("data-uri-mesh");
      const extLink = [
        { title: "ESV", uri: esv },
        { title: "MeSH", uri: mesh },
      ];

      topics_main_vals[lang].push({ topic: value, extLink: extLink });
      continue;
    }

    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/vocab\/health\s+theme$/i.test(
        key,
      )
    ) {
      topics_main_vocab = String(value);
      continue;
    }

    // Other health theme (texte libre ou éventuellement tableau)
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/value\/other\s+health\s+theme_(fr|en)(?:\[\])?$/i.test(
        key,
      )
    ) {
      if (lang) pushUnique(topics_other_health_vals[lang], value);
      continue;
    }
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/vocab\/other\s+health\s+theme$/i.test(
        key,
      )
    ) {
      topics_other_health_vocab = String(value);
      continue;
    }
    // A) health determinant
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/value\/health\s+determinant_(fr|en)\[\]$/i.test(
        key,
      )
    ) {
      if (lang) pushUnique(topics_det_vals[lang], value);
      continue;
    }

    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/vocab\/health\s+determinant$/i.test(
        key,
      )
    ) {
      topics_det_vocab = String(value);
      continue;
    }
    //other socio-demographic determinant
    if (
      /^stdydscr\/stdyInfo\/subject\/topcclas\[\]\/value\/other\s+socio\s+demographic\s+determinant_(fr|en)(?:\[\])?$/i.test(
        key,
      )
    ) {
      if (lang) pushUnique(topics_osdd_vals[lang], value);
      continue;
    }

    if (
      /^stdydscr\/stdyInfo\/subject\/topcclas\[\]\/vocab\/other\s+socio\s+demographic\s+determinant$/i.test(
        key,
      )
    ) {
      topics_osdd_vocab = String(value);
      continue;
    }
    // Other environmental determinant
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/value\/other\s+environmental\s+determinant_(fr|en)(?:\[\])?$/i.test(
        key,
      )
    ) {
      if (lang) pushUnique(topics_env_vals[lang], value);
      continue;
    }
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/vocab\/other\s+environmental\s+determinant$/i.test(
        key,
      )
    ) {
      topics_env_vocab = String(value);
      continue;
    }

    // other healthcare determinant
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/value\/other\s+healthcare\s+determinant_(fr|en)(?:\[\])?$/i.test(
        key,
      )
    ) {
      if (lang) pushUnique(topics_healthcare_vals[lang], value);
      continue;
    }
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/vocab\/other\s+healthcare\s+determinant$/i.test(
        key,
      )
    ) {
      topics_healthcare_vocab = String(value);
      continue;
    }

    // Other behavioural determinant
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/value\/other\s+behavioural\s+determinant_(fr|en)(?:\[\])?$/i.test(
        key,
      )
    ) {
      if (lang) pushUnique(topics_behavioral_vals[lang], value);
      continue;
    }
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/vocab\/other\s+behavioural\s+determinant$/i.test(
        key,
      )
    ) {
      topics_behavioral_vocab = String(value);
      continue;
    }

    // Other biological determinant
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/value\/other\s+biological\s+determinant_(fr|en)(?:\[\])?$/i.test(
        key,
      )
    ) {
      if (lang) pushUnique(topics_biological_vals[lang], value);
      continue;
    }
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/vocab\/other\s+biological\s+determinant$/i.test(
        key,
      )
    ) {
      topics_biological_vocab = String(value);
      continue;
    }
    // other determinant
    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/value\/other\s+determinant_(fr|en)$/i.test(
        key,
      )
    ) {
      if (lang) pushUnique(topics_od_vals[lang], value);
      continue;
    }

    if (
      /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/vocab\/other\s+determinant$/i.test(
        key,
      )
    ) {
      topics_od_vocab = String(value);
      continue;
    }

    // C) valeurs sous schéma: topcclas[]/value/{scheme}[]
    {
      const m = key.match(
        /stdydscr\/stdyInfo\/subject\/topcclas\[\]\/value\/([a-z0-9\-]+)_(fr|en)\[\]$/i,
      );
      if (m) {
        const scheme = m[1].toLowerCase();
        const lang = m[2].toLowerCase();

        const val = String(value).trim();
        const selector = `select[name="stdyDscr/stdyInfo/subject/topcClas[]/value/${scheme}_${lang}[]"]`;
        const optionText =
          $(selector).find(`option[value="${val}"]`).text().trim() || val;

        topics_scheme_vals[scheme] ||= { fr: [], en: [] };
        const extLink = [{ title: "CIM-11", uri: val }];
        topics_scheme_vals[scheme][lang].push({
          extLink: extLink,
          label: optionText,
          code: val,
        });
        continue;
      }
    }

    // C) vocab du schéma: topcClas[]/vocab/{scheme}
    {
      const m = key.match(
        /stdydscr\/stdyinfo\/subject\/topcclas\[\]\/vocab\/([a-z0-9\-]+)$/i,
      );
      if (m) {
        const scheme = m[1].toLowerCase();
        topics_scheme_vocab[scheme] = String(value).trim();
        continue;
      }
    }

    // ===================== ADDITIONAL / INTERVENTION (repeater) =====================

    // Name: additional/intervention/interventionName_{fr|en}  (accept optional [] at end)
    if (
      /additional\/intervention\/interventionname_(fr|en)(\[\])?$/i.test(key)
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
      /additional\/intervention\/interventiontype_(fr|en)(\[\])?$/i.test(key)
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
        key,
      )
    ) {
      if (lang) {
        const it = touchLastIntervention(lang);
        it.typeOther = value;
      }
      continue;
    }

    // ===================== ADDITIONAL / INTERVENTIONAL STUDY (checkbox) =====================
    // additional/interventionalStudy/researchPurpose_{fr|en}[]
    if (
      /additional\/interventionalstudy\/researchpurpose_(fr|en)(\[\])?$/i.test(
        key,
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
      /additional\/interventionalstudy\/trialphase_(fr|en)(\[\])?$/i.test(key)
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
        key,
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

    // ===================== CITATION / titlStmt / IDNo (agency & code) =====================
    {
      let m;

      // --- Indexed pattern: .../agency_{fr|en}_{i}  /  .../code_{fr|en}_{i}
      if (
        (m = key.match(
          /stdydscr\/citation\/titlstmt\/idno\/agency_(fr|en)_(\d+)$/i,
        ))
      ) {
        const langKey = m[1].toLowerCase(),
          idx = parseInt(m[2], 10);
        ensureOtherIdnoAt(langKey, idx).agency = value;
        continue;
      }
      if (
        (m = key.match(/stdydscr\/citation\/titlstmt\/idno_(fr|en)_(\d+)$/i))
      ) {
        const langKey = m[1].toLowerCase(),
          idx = parseInt(m[2], 10);
        ensureOtherIdnoAt(langKey, idx).code = value;
        continue;
      }

      // --- Fallback: non-indexed names (multiple rows without _0/_1)
      if (
        /stdydscr\/citation\/titlstmt\/idno\/agency_(fr|en)(\[\])?$/i.test(key)
      ) {
        if (lang) {
          const last = peekLastOtherIdno(lang);
          if (!last || last.agency || last.code) {
            // start a new row if none OR current row already started
            pushOtherIdno(lang, String(value), "");
          } else {
            last.agency = String(value);
          }
        }
        continue;
      }

      if (/stdydscr\/citation\/titlstmt\/idno\_(fr|en)(\[\])?$/i.test(key)) {
        if (lang) {
          const last = peekLastOtherIdno(lang);
          if (last && !String(last.code || "").trim()) {
            last.code = String(value);
          } else {
            // if a code arrives and current row already has one, open a new row
            pushOtherIdno(lang, "", String(value));
          }
        }
        continue;
      }
    }

    // ===================== ADDITIONAL / MASKING (checkbox) =====================
    //additional/masking/blindedMaskingDetails_{fr|en}[]
    if (
      /additional\/masking\/blindedmaskingdetails_(fr|en)(\[\])?$/i.test(key)
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
      /additional\/datacollection\/inclusionstrategy_(fr|en)(\[\])?$/i.test(key)
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
        const cessda = $(selectorInput).attr("data-uri");
        const concept = { vocab: "CESSDA", vocabURI: cessda };
        const v = String(value).trim();
        if (v) {
          // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
          if (lang === "fr")
            result.sampProc_fr.push(
              JSON.stringify({ concept: concept, value: v }),
            );
          else
            result.sampProc_en.push(
              JSON.stringify({ concept: concept, value: v }),
            );
        }
      }
      continue;
    }

    // ===================== ADDITIONAL / DATA COLLECTION (checkbox) =====================
    //stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_{fr|en}[]
    if (
      /stdydscr\/method\/datacoll\/sampleframe\/frameunit\/unittype_(fr|en)(\[\])?$/i.test(
        key,
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
        key,
      )
    ) {
      if (lang) {
        const mesh = $(selectorInput).attr("data-uri");
        const concept = { vocab: "MeSH", vocabURI: mesh };
        const v = String(value).trim();
        if (v) {
          // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
          if (lang === "fr")
            result.level_sex_Clusion_I_fr.push({
              concept: concept,
              value: v,
            });
          else
            result.level_sex_Clusion_I_en.push({
              concept: concept,
              value: v,
            });
        }
      }
      continue;
    }

    // ===================== ADDITIONAL / DATA COLLECTION (checkbox) =====================
    //stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I_{fr|en}[]
    if (
      /stdydscr\/stdyinfo\/sumdscr\/universe\/level_age-clusion_i_(fr|en)(\[\])?$/i.test(
        key,
      )
    ) {
      if (lang) {
        const mesh = $(selectorInput).attr("data-uri");
        const concept = { vocab: "MeSH", vocabURI: mesh };
        const v = String(value).trim();
        if (v) {
          // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
          if (lang === "fr")
            result.level_age_Clusion_I_fr.push({
              concept: concept,
              value: v,
            });
          else
            result.level_age_Clusion_I_en.push({
              concept: concept,
              value: v,
            });
        }
      }
      continue;
    }

    // ===================== ADDITIONAL / GEOGRAPHIC COVERAGE (checkbox) =====================
    ///stdyDscr/stdyInfo/sumDscr/nation_{fr|en}[]
    if (/stdydscr\/stdyinfo\/sumdscr\/nation_(fr|en)(\[\])?$/i.test(key)) {
      if (lang) {
        const iso = $(selectorSelect).attr("data-uri");
        const concept = { vocab: "ISO", vocabURI: iso };
        const v = String(value).trim();
        if (v) {
          // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
          if (lang === "fr")
            result.nation_fr.push({ concept: concept, value: v });
          else result.nation_en.push({ concept: concept, value: v });
        }
      }
      continue;
    }

    // ===================== ADDITIONAL / IndividualDataAccess (select) =====================
    ///stdyDscr/stdyInfo/sumDscr/nation_{fr|en}[]
    if (/stdydscr\/dataAccs\/setAvail\/avlStatus_(fr|en)(\[\])?$/i.test(key)) {
      if (lang) {
        const coar = $(selectorSelect).attr("data-uri");
        const extLink = { title: "COAR", uri: coar };
        const v = String(value).trim();
        if (v) {
          // pushUnique existe déjà dans ton code; sinon remplace par un simple .push(v)
          if (lang === "fr")
            result.individual_data_access_fr = JSON.stringify({
              extLink: extLink,
              value: v,
            });
          else
            result.individual_data_access_en = JSON.stringify({
              extLink: extLink,
              value: v,
            });
        }
      }
      continue;
    }

    // ===================== ADDITIONAL / GEOGRAPHIC COVERAGE (checkbox) =====================
    //stdyDscr/stdyInfo/sumDscr/geogCover_{fr|en}[]
    if (/stdydscr\/stdyinfo\/sumdscr\/geogcover_(fr|en)(\[\])?$/i.test(key)) {
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

    if (
      /stdydscr\/method\/datacoll\/sources\/sourcecitation_(fr|en)$/i.test(key)
    ) {
      if (lang) {
        pushNewSource(lang);
        touchLastSource(lang).sourceCitation.titlStmt.titl = value;
      }
      continue;
    }
    if (
      /stdydscr\/method\/datacoll\/sources\/sourcecitation\/holdings_(fr|en)$/i.test(
        key,
      )
    ) {
      if (lang) {
        touchLastSource(lang).sourceCitation.holdings = value;
      }
      continue;
    }
    if (
      /stdydscr\/method\/datacoll\/sources\/srcorig_(fr|en)(\[\])?$/i.test(key)
    ) {
      if (lang) {
        const s = touchLastSource(lang);
        const v = String(value).trim();
        if (v && !s.srcOrig.includes(v)) {
          s.srcOrig.push(v);
        }
      }
      continue;
    }

    if (
      /stdydscr\/method\/datacoll\/sources\/sourcecitation\/notes\/subject_sourcepurpose_(fr|en)$/i.test(
        key,
      )
    ) {
      if (lang) {
        const s = touchLastSource(lang);
        s.sourceCitation.notes.subject = "source purpose";
        s.sourceCitation.notes.value = value;
      }
      continue;
    }

    if (
      /additional\/thirdPartySource\/otherSourceType_(fr|en)(\[\])?$/i.test(key)
    ) {
      if (lang) {
        const arr = otherSourceTypeList(lang);
        if (arr.length > 0) {
          arr[arr.length - 1] = value;
        } else {
          arr.push(value);
        }
      }
      continue;
    }

    // ===================== QUALITY / STANDARDS COMPLIANCE (text list) =====================
    // stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName_{fr|en}
    if (
      /stdydscr\/stdyinfo\/qualitystatement\/standardscompliance\/standard\/standardname_(fr|en)$/i.test(
        key,
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
      /stdydscr\/stdyinfo\/qualitystatement\/otherQualityStatement_(fr|en)$/i.test(
        key,
      )
    ) {
      if (lang) {
        const v = String(value).trim();
        if (v) {
          if (lang === "fr") pushUnique(result.otherQualityStatements_fr, v);
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
        const last = (() => {
          const list = contactList(lang);
          return list.length ? list[list.length - 1] : null;
        })();
        if (last && !String(last.firstname || "").trim()) last.firstname = v;
        else pushContact(lang, v);
      }
      continue;
    }
    if (
      /stdydscr\/dataaccs\/usestmt\/contact\/lastname_(fr|en)(\[\])?$/i.test(
        key,
      )
    ) {
      const contactFr = touchLastContact(lang);
      contactFr.lastname = value;
      continue;
    }
    if (/stdydscr\/dataaccs\/usestmt\/contact\/mail$/i.test(key)) {
      const emailsFr = touchLastContact("fr");
      const emailsEn = touchLastContact("en");
      emailsFr.email = value;
      emailsEn.email = value;
      continue;
    }

    // ===================== CITATION / IDno (non-indexed, robust) =====================

    // agentSchema_{fr|en}
    if (/stdydscr\/citation\/idno\/agentschema_(fr|en)(\[\])?$/i.test(key)) {
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
    if (/stdydscr\/stdyinfo\/sumdscr\/datakind_(fr|en)(\[\])?$/i.test(key)) {
      const v = String(value).trim();
      if (v) {
        if (lang === "fr") pushUnique(result.dataKind_fr, v);
        else pushUnique(result.dataKind_en, v);
      }
      continue;
    }

    //additional/dataTypes/biobankContent
    if (/additional\/datatypes\/biobankcontent_(fr|en)(\[\])?$/i.test(key)) {
      const v = String(value).trim();
      if (v) {
        if (lang === "fr") pushUnique(result.biobankContent_fr, v);
        else pushUnique(result.biobankContent_en, v);
      }
      continue;
    }

    // ===================== ADDITIONAL / INCLUSION GROUPS (indexed + fallback) =====================
    {
      // --- Fallback: []-style (keeps your previous behavior)
      if (/additional\/inclusiongroups\/groupname_(fr|en)(\[\])?$/i.test(key)) {
        if (lang && value) {
          const v = String(value).trim();
          const last = peekLastInclusionGroup(lang);
          if (last && !String(last.name || "").trim()) last.name = v;
          else pushInclusionGroup(lang, v);
        }
        continue;
      }
      if (
        /additional\/inclusiongroups\/groupdescription_(fr|en)(\[\])?$/i.test(
          key,
        )
      ) {
        if (lang && value) {
          const g = touchLastInclusionGroup(lang);
          g.description = value;
        }
        continue;
      }
    }

    // ===================== DATASET PIDs (FR / EN) =====================
    // Agent/type (select) — row delimiter per language
    // Suffix version: .../IDno/agent_{fr|en}
    if (
      /additional\/filedscr\/filetxt\/filecitation\/titlstmt\/IDno\/agent_(fr|en)$/i.test(
        key,
      )
    ) {
      if (value) {
        const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
        // new repeater row for that language
        pushPIDLang(lng);
        ensurePIDLang(lng).type = value;
      }

      continue;
    }

    // Identifier value (URL/code) — per language
    // .../IDno_{fr|en}
    if (
      /additional\/filedscr\/filetxt\/filecitation\/titlstmt\/idno_(fr|en)$/i.test(
        key,
      )
    ) {
      if (value) {
        const lng = key.toLowerCase().endsWith("_fr") ? "fr" : "en";
        ensurePIDLang(lng).uri = value;
      }
      continue;
    }

    // ===================== ADDITIONAL / ARMS (repeater) =====================

    // --- Indexed pattern: .../armsName_{fr|en}_{i}

    // --- Fallback (if you ever use [] instead of _0/_1)
    if (/additional\/arms\/armsname_(fr|en)(\[\])?$/i.test(key)) {
      if (lang) {
        const v = String(value).trim();
        pushArm(lang, v);
      }
      continue;
    }
    if (/additional\/arms\/armstype_(fr|en)(\[\])?$/i.test(key)) {
      if (lang && value) {
        const a = touchLastArm(lang);
        a.type = value;
      }
      continue;
    }
    if (/additional\/arms\/armstypeother_(fr|en)(\[\])?$/i.test(key)) {
      if (lang && value) {
        const a = touchLastArm(lang);
        a.typeOther = value;
      }
      continue;
    }
    if (/additional\/arms\/armsdescription_(fr|en)(\[\])?$/i.test(key)) {
      if (lang && value) {
        const a = touchLastArm(lang);
        a.description = value;
      }
      continue;
    }

    // -- relatedDocument
    if (
      /additional\/relatedDocument\/documentType_(fr|en)(\[\])?$/i.test(key)
    ) {
      if (lang && value) {
        const v = String(value).trim();
        const last = (() => {
          const list = docsList(lang);
          return list.length ? list[list.length - 1] : null;
        })();
        if (last && !String(last.type || "").trim()) last.type = v;
        else pushDocs(lang, v);
      }
      continue;
    }
    if (
      /additional\/relatedDocument\/documentTitle_(fr|en)(\[\])?$/i.test(key)
    ) {
      if (lang && value) {
        const a = touchLastDocs(lang);
        a.title = value;
      }
      continue;
    }
    if (
      /additional\/relatedDocument\/documentLink_(fr|en)(\[\])?$/i.test(key)
    ) {
      if (value) {
        const linkFr = touchLastDocs("fr");
        const linkEn = touchLastDocs("en");
        linkFr.link = value;
        linkEn.link = value;
      }
      continue;
    }
  }

  // ----- A) valeurs "main" (value_{fr|en}[]) -----
  if (topics_main_vocab) {
    for (const v of topics_main_vals.fr)
      result.topics_fr.push({
        topic: v.topic,
        extLink: v.extLink,
        vocab: topics_main_vocab,
      });
    for (const v of topics_main_vals.en)
      result.topics_en.push({
        topic: v.topic,
        extLink: v.extLink,
        vocab: topics_main_vocab,
      });
  }

  for (const scheme of Object.keys(topics_scheme_vals)) {
    const vocabLabel = topics_scheme_vocab[scheme] || scheme;
    const vals = topics_scheme_vals[scheme];

    for (const v of vals.fr || []) {
      const item = {
        topic: v.label,
        vocab: vocabLabel,
        extLink: v.extLink,
      };
      result.topics_fr.push(item);
    }

    for (const v of vals.en || []) {
      const item = {
        topic: v.label,
        vocab: vocabLabel,
        extLink: v.extLink,
      };
      result.topics_en.push(item);
    }
  }

  pushTopicsIfNotEmpty(topics_det_vals, topics_det_vocab);
  pushTopicsIfNotEmpty(topics_osdd_vals, topics_osdd_vocab);
  pushTopicsIfNotEmpty(topics_other_health_vals, topics_other_health_vocab);
  pushTopicsIfNotEmpty(topics_env_vals, topics_env_vocab);
  pushTopicsIfNotEmpty(topics_healthcare_vals, topics_healthcare_vocab);
  pushTopicsIfNotEmpty(topics_behavioral_vals, topics_behavioral_vocab);
  pushTopicsIfNotEmpty(topics_biological_vals, topics_biological_vocab);
  pushTopicsIfNotEmpty(topics_od_vals, topics_od_vocab);

  // --- Keywords (FR / EN) ---
  const getKeywordsByLang = (lang) => {
    const tags = [];
    $(
      `.tags-wrapper[data-tags="stdyDscr/stdyInfo/subject/keyword_${lang}"] .tag`,
    ).each(function () {
      const tagText = $(this).clone().children().remove().end().text().trim();
      if (tagText) tags.push(tagText);
    });
    return tags.map((t) => ({ keyword: t }));
  };

  // Get FR and EN tags
  const keywords_fr = getKeywordsByLang("fr");
  const keywords_en = getKeywordsByLang("en");

  // Save only if they exist
  if (keywords_fr.length) result.keywords_fr = keywords_fr;
  if (keywords_en.length) result.keywords_en = keywords_en;

  // --- Envoi AJAX ----------------------------------------------------------
  const formatName = (first, last) => {
    if (first && last) return `${first};${last}`;
    if (first) return `${first};`;
    if (last) return `;${last}`;
    return "";
  };

  result.distContacts_fr = result.distContacts_fr.map((dc) => ({
    ...dc,
    name: formatName(dc.firstname, dc.lastname),
  }));
  result.distContacts_en = result.distContacts_en.map((dc) => ({
    ...dc,
    name: formatName(dc.firstname, dc.lastname),
  }));

  result.authEntities_fr = result.authEntities_fr.map((ae) => ({
    ...ae,
    name: formatName(ae.firstname, ae.lastname),
  }));
  result.authEntities_en = result.authEntities_en.map((ae) => ({
    ...ae,
    name: formatName(ae.firstname, ae.lastname),
  }));

  result.useStmtContacts_fr = result.useStmtContacts_fr.map((usc) => ({
    ...usc,
    name: formatName(usc.firstname, usc.lastname),
  }));
  result.useStmtContacts_en = result.useStmtContacts_en.map((usc) => ({
    ...usc,
    name: formatName(usc.firstname, usc.lastname),
  }));

  formData.append("complexData", JSON.stringify(result));
  formData.append("action", "nada_save_study");

  if (submitter && submitter.id === "finishBtn") {
    // Sauvegarde temporaire

    lastFormData = formData;
    lastSubmitter = submitter;
    lastBtnText = btnText;
    lastSpinner = spinner;

    if (lang === "fr") {
      sessionStorage.setItem("frStudyProcessed", "true"); // toujours string
    } else if (lang === "en") {
      sessionStorage.setItem("enStudyProcessed", "true");
    }

    // Vérifier
    const frDone = sessionStorage.getItem("frStudyProcessed") === "true";
    const enDone = sessionStorage.getItem("enStudyProcessed") === "true";
    if (!frDone || !enDone) {
      const modal = new bootstrap.Modal(
        document.getElementById("translationConfirmModal"),
      );
      modal.show();
      return;
    }
  }

  if (autoSave) {
    // autoSave ne change pas le statut d'étude (par défaut draft)
    formData.append("is_submit", 0);
  }

  formData.append("status_key", $('input[name="status-key"]').val());
  formData.append("enable_translation", false);
  formData.append("auto_save", autoSave);

  sendAjax(formData, submitter, btnText, spinner, autoSave);
};

window.sendAjax = function (
  formData,
  submitter,
  btnText,
  spinner,
  autoSave = false,
) {
  isSaving = true; // Etude en cours d'ajout/modification

  // recupérer la langue active
  const lang = jQuery(".nav-item.langue .nav-link.active").data("lang");

  // Loader
  if (submitter.id == "saveAsDraft" || submitter.id == "finishBtn") {
    jQuery("#translationConfirmModal #loader").css("display", "block");
    jQuery("#translationConfirmModal .modalContent").css("display", "none");

    const modal = new bootstrap.Modal(
      document.getElementById("translationConfirmModal"),
    );
    modal.show();
  }

  const frDone = sessionStorage.getItem("frStudyProcessed") === "true";
  const enDone = sessionStorage.getItem("enStudyProcessed") === "true";
  allStudiesProcessed = frDone && enDone;

  formData.append("allStudiesProcessed", allStudiesProcessed);

  const data = Object.fromEntries(formData.entries());

  jQuery(function ($) {
    $.ajax({
      url: nadaAddStudyVars.ajax_url,
      type: "POST",
      dataType: "json",
      processData: false, // important pour FormData
      contentType: false, // important pour FormData
      data: formData,
    })
      .done(function (response) {
        const responseData = response.data
          ? response.data.response
          : response.response;
        if (!autoSave) {
          // Réactiver le bouton et masquer le spinner
          submitter.disabled = false;
          if (spinner) spinner.classList.add("d-none");
          if (btnText) {
            submitter.id == "finishBtn"
              ? (btnText.textContent = lang === "fr" ? "Terminer" : "Finish")
              : (btnText.textContent =
                  lang === "fr" ? "Enregistrer brouillon" : "Save draft");
          }

          const ok = response && response.success;

          // Extraire le message
          let message =
            response.data && response.data.message
              ? response.data.message
              : response.message || "";

          if (ok) {
            // Redirection si dataset créé avec succès
            if (
              responseData &&
              responseData.fr &&
              responseData.fr.data &&
              responseData.fr.data.dataset &&
              responseData.fr.data.dataset.idno
            ) {
              if (submitter.id == "finishBtn") {
                if (lang == "en") {
                  message = "The study has been successfully submitted.";
                } else {
                  message = "L'étude a été soumise avec succès.";
                }

                jQuery(".message-succes-response").html(message);

                jQuery("#translationConfirmModal #loader").css(
                  "display",
                  "none",
                );
                jQuery("#translationConfirmModal #succes-message").css(
                  "display",
                  "block",
                );
                //TODO à optimser
                setTimeout(function () {
                  jQuery("#translationConfirmModal").modal("hide");
                  //   const translateMirrorCount =
                  //     Number(
                  //       sessionStorage.getItem("translateMirrorCount") || 0,
                  //     ) + 1;

                  //   sessionStorage.setItem(
                  //     "translateMirrorCount",
                  //     translateMirrorCount,
                  //   );
                  //   if (enableTranslation) {
                  //     if (allStudiesProcessed == false) {
                  //       if (lang == "en") {
                  //         const redirectUrl =
                  //           nada_global_vars.site_url +
                  //           "/alimentation-du-catalogue/" +
                  //           responseData.fr.data.dataset.idno;
                  //         window.location.href = redirectUrl;
                  //       } else {
                  //         const redirectUrl =
                  //           nada_global_vars.site_url +
                  //           "/en/contribute/" +
                  //           responseData.en.data.dataset.idno;
                  //         window.location.href = redirectUrl;
                  //       }
                  //     } else {
                  //       sessionStorage.removeItem("translateMirrorCount");
                  //       sessionStorage.removeItem("frStudyProcessed");
                  //       sessionStorage.removeItem("enStudyProcessed");
                  //       // redicrection mon espace
                  //       window.location.href =
                  //         lang == "fr"
                  //           ? nada_global_vars.site_url + "/mon-espace/"
                  //           : nada_global_vars.site_url + "/en/my-space";
                  //     }
                  //   } else {
                  //     // Pas de traduction activée
                  //     if (lang == "en") {
                  //       const redirectUrl =
                  //         nada_global_vars.site_url +
                  //         "/en/contribute/" +
                  //         response.data.response.en.data.dataset.idno;
                  //       window.location.href = redirectUrl;
                  //     } else {
                  //       const redirectUrl =
                  //         nada_global_vars.site_url +
                  //         "/alimentation-du-catalogue/" +
                  //         response.data.response.fr.data.dataset.idno;
                  //       window.location.href = redirectUrl;
                  //     }
                  //   }
                  if (allStudiesProcessed || !enableTranslation) {
                    sessionStorage.removeItem("frStudyProcessed");
                    sessionStorage.removeItem("enStudyProcessed");
                    // redicrection mon espace
                    window.location.href =
                      lang == "fr"
                        ? nada_global_vars.site_url + "/mon-espace/"
                        : nada_global_vars.site_url + "/en/my-space";
                  } else {
                    if (lang == "en") {
                      const redirectUrl =
                        nada_global_vars.site_url +
                        "/alimentation-du-catalogue/" +
                        responseData.fr.data.dataset.idno;
                      window.location.href = redirectUrl;
                    } else {
                      const redirectUrl =
                        nada_global_vars.site_url +
                        "/en/contribute/" +
                        responseData.en.data.dataset.idno;
                      window.location.href = redirectUrl;
                    }
                  }
                }, 3000); // 3 secondes
              } else {
                //saveAsDraft

                let succes_message =
                  "Les modifications apportées à votre étude ont été enregistrées avec succès en tant que brouillon.";
                let redirectUrlUpdate =
                  nada_global_vars.site_url +
                  "/alimentation-du-catalogue/" +
                  response.data.response.fr.data.dataset.idno;

                if (lang == "en") {
                  succes_message =
                    "The changes made to your study have been successfully saved as a draft.";
                  redirectUrlUpdate =
                    nada_global_vars.site_url +
                    "/en/contribute/" +
                    response.data.response.en.data.dataset.idno;
                }

                jQuery("#translationConfirmModal").modal("hide");
                $("#response-success")
                  .removeClass("d-none")
                  .text(succes_message);
                $("#response-error").addClass("d-none");

                setTimeout(function () {
                  if (ok) {
                    window.location.href = redirectUrlUpdate;
                  }
                }, 3000); // 3 secondes
              }
              return;
            }

            // Afficher le message de succès ou d'erreur
            $("#response-success").removeClass("d-none").text(message);
            $("#response-error").addClass("d-none");
          } else {
            jQuery("#translationConfirmModal").modal("hide");
            $("#response-error").removeClass("d-none").text(message);
            $("#response-success").addClass("d-none");
          }
        }

        // remplir la valeur du idno
        if (
          responseData &&
          responseData.fr &&
          responseData.fr.data &&
          responseData.fr.data.dataset &&
          responseData.fr.data.dataset.idno
        ) {
          $('input[name="idno"]').val(responseData.fr.data.dataset.idno);
          $('input[name="status-key"]').val(
            responseData.fr.data.dataset.link_indicator,
          ); // remplir la valeur du status key
        }
      })
      .fail(function (xhr, status, error) {
        // Réactiver le bouton et masquer le spinner
        jQuery("#translationConfirmModal").modal("hide");

        submitter.disabled = false;
        if (spinner) spinner.classList.add("d-none");
        if (btnText) {
          submitter.id == "finishBtn"
            ? (btnText.textContent = lang === "fr" ? "Terminer" : "Finish")
            : (btnText.textContent =
                lang === "fr" ? "Enregistrer brouillon" : "Save draft");
        }

        let errorMessage = ""; // Message vide par défaut

        // Tenter d'extraire le message d'erreur du backend
        try {
          if (xhr.responseJSON) {
            if (xhr.responseJSON.data && xhr.responseJSON.data.message) {
              errorMessage = xhr.responseJSON.data.message;
            } else if (xhr.responseJSON.message) {
              errorMessage = xhr.responseJSON.message;
            }
          } else if (xhr.responseText) {
            // Tenter de parser la réponse texte
            const parsedResponse = JSON.parse(xhr.responseText);
            if (parsedResponse.data && parsedResponse.data.message) {
              errorMessage = parsedResponse.data.message;
            } else if (parsedResponse.message) {
              errorMessage = parsedResponse.message;
            }
          }
        } catch (e) {
          // Si aucun message n'est trouvé, laisser vide
          // Le message sera géré par PHP lors du prochain appel
        }

        // Afficher le message seulement s'il existe
        if (errorMessage) {
          $("#response-error").removeClass("d-none").text(errorMessage);
          $("#response-success").addClass("d-none");
        }
      })
      .complete(function () {
        isSaving = false;
      });
  });
};

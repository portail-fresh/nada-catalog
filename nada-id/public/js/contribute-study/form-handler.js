// =========  form-handler.js - Initialisations globales =========//
/**
 * Fonctions d’initialisation générales
 * Setup global behaviors (animations, tooltips, etc.)
 */

import {
  autoRepeatClickInerExpo,
  autoRepeatClickInclusionInterventional,
  autoRepeatClickObtainedAuthorization,
  autoRepeatClickPrimaryInvestigator,
  autoRepeatClickFundingAgent,
  autoRepeatClickSponsor,
  autoRepeatClickContactPoint,
  autoRepeatClickContributor,
  autoRepeatClickArms,
  autoRepeatClickSources,
  autoRepeatClickDatasetPID,
  autoRepeatClickInformationContact,
  autoRepeatClickStandardName,
  autoRepeatClickOtherQualityStatement,
  autoRepeatClickIDno,
  autoRepeatClickDocs,
  autoRepeatClickInerExpo2,
  autoRepeatClickInclusionObservational
} from "./handler-action.js";

import { parsePseudoArray, toggleFranceRegion } from "./helpers.js";
import { createOptionCim11 } from "./cim11.js";

export function formHandler(jsonRec, jsonParentStudy, studyDetails, currentUser) {
  if (studyDetails) {
    let piEmail = studyDetails["pi_email"];

    // email du user connecté
    let userEmail = currentUser["user_email"];

    // Initialisation PI au chargement
    if (piEmail === userEmail) {
      let radioCreatorIsPiValue = jQuery(`[name^="creatorIsPi"][value="Yes"]`);
      radioCreatorIsPiValue.prop("disabled", false).prop("checked", true).trigger("change");
    } else {
      let radioCreatorIsPiValue = jQuery(`[name^="creatorIsPi"][value="No"]`);
      radioCreatorIsPiValue.prop("disabled", false).prop("checked", true).trigger("change");
      let creatorIsPiValueEmail = piEmail;
      if (creatorIsPiValueEmail) {
        jQuery('[name="pi-email"]').val(creatorIsPiValueEmail).change();
      }
    }
  }

  if (jsonRec) {
    const langs = ["fr", "en"];
    let authorizingAgencyData = {}; // objet et non tableau
    let investigatorData = {};
    let contributorData = {};
    let contactData = {};
    let fundingAgenciesData = {};
    let prodStmtProducerData = {};
    let investigatorsNumber = 0;
    let additionalGovernanceCommittee = "";
    let additionalNetworkConsortium = "";
    let topcClasTopic = "";
    let armsData = {};
    let interventionData = {};
    let sourcesData = {};
    let contactUseStmtData = {};
    let standardNameData = {};
    let otherQualityStatementData = {};
    let IDnoData = {};
    let docsData = {};
    let inclusionGroupsData = {};
    let datasetPIDData = {};

    for (const lng of langs) {
      let nation = jsonRec["stdyDscr/stdyInfo/sumDscr/nation_" + lng];

      if (Array.isArray(nation)) {
        const select = jQuery(`[name="stdyDscr/stdyInfo/sumDscr/nation_${lng}[]"]`);
        select.val(nation);

        let placeholderText = jQuery(select).attr("name").includes("_fr") ? "-- Sélectionner --" : "-- Select --";

        toggleFranceRegion(nation);
        jQuery(select).next(".select2-container").find(".select2-search__field").attr("placeholder", placeholderText);
      }

      authorizingAgencyData[lng] = {
        data: jsonRec["stdyDscr/studyAuthorization/authorizingAgency_" + lng],
        additional: jsonRec["additional/obtainedAuthorization/otherAuthorizingAgency_" + lng]
      };

      let rawData = jsonRec["stdyDscr/citation/rspStmt/AuthEnty_" + lng];
      // Séparer investigators et affiliations
      investigatorData[lng] = {
        investigators: rawData
      };
      // Mettre à jour investigatorsNumber avec le max du nombre d’investigators
      investigatorsNumber = Math.max(investigatorsNumber, investigatorData[lng].investigators.length);

      contributorData[lng] = jsonRec["stdyDscr/citation/rspStmt/othId_" + lng] ?? [];
      contactData[lng] = jsonRec["stdyDscr/citation/distStmt/contact_" + lng];

      fundingAgenciesData[lng] = {
        data: jsonRec["stdyDscr/citation/prodStmt/fundAg/values_" + lng],
        additional: jsonRec["additional/fundingAgent/fundingAgentType_" + lng],
        additionalOFAT: jsonRec["additional/fundingAgent/otherFundingAgentType_" + lng]
      };

      prodStmtProducerData[lng] = {
        data: jsonRec["stdyDscr/citation/prodStmt/producer/values_" + lng],
        additional: jsonRec["additional/sponsor/sponsorType_" + lng],
        additionalOST: jsonRec["additional/sponsor/otherSponsorType_" + lng]
      };

      additionalGovernanceCommittee = jsonRec["additional/governance/committee_" + lng];
      if (additionalGovernanceCommittee) {
        let radioCommittee = jQuery(`[name="additional/governance/committee_${lng}"][value="${additionalGovernanceCommittee}"]`);
        radioCommittee.prop("disabled", false).prop("checked", true).trigger("change");
      }

      additionalNetworkConsortium = jsonRec["additional/collaborations/networkConsortium_" + lng];
      if (additionalNetworkConsortium) {
        let radioNetworkConsortium = jQuery(`[name="additional/collaborations/networkConsortium_${lng}"][value="${additionalNetworkConsortium}"]`);
        radioNetworkConsortium.prop("disabled", false).prop("checked", true).trigger("change");
      }

      let stdyClas = jsonRec["stdyDscr/method/stdyClas_" + lng];
      jQuery(`select[name="stdyDscr/method/stdyClas_${lng}"]`).val(stdyClas).change();

      // Fonction pour remplir les champs checkbox ou select-multiple
      topcClasTopic = jsonRec["stdyDscr/stdyInfo/subject/topcClas_" + lng];

      if (topcClasTopic && Array.isArray(topcClasTopic)) {
        // normalisation des chaînes pour comparaison
        let norm = (s) =>
          String(s || "")
            .toLowerCase()
            .replaceAll(/[-_]/g, " ")
            .replaceAll(/\s+/g, " ")
            .trim();

        // définitions des mappings entre vocabulaire et champs du formulaire
        let mappings = [
          // checkboxes
          {
            vocab: "health theme",
            name: `stdyDscr/stdyInfo/subject/topcClas[]/value/health theme_${lng}[]`,
            type: "checkbox"
          },
          {
            vocab: "health determinant",
            name: `stdyDscr/stdyInfo/subject/topcClas[]/value/health determinant_${lng}[]`,
            type: "checkbox"
          },
          // single text
          {
            vocab: "other health theme",
            name: `stdyDscr/stdyInfo/subject/topcClas[]/value/other health theme_${lng}`,
            type: "text"
          },
          {
            vocab: "other socio demographic determinant",
            name: `stdyDscr/stdyInfo/subject/topcClas[]/value/other socio demographic determinant_${lng}`,
            type: "text"
          },
          {
            vocab: "other environmental determinant",
            name: `stdyDscr/stdyInfo/subject/topcClas[]/value/other environmental determinant_${lng}`,
            type: "text"
          },
          {
            vocab: "other healthcare determinant",
            name: `stdyDscr/stdyInfo/subject/topcClas[]/value/other healthcare determinant_${lng}`,
            type: "text"
          },
          {
            vocab: "other behavioural determinant",
            name: `stdyDscr/stdyInfo/subject/topcClas[]/value/other behavioural determinant_${lng}`,
            type: "text"
          },
          {
            vocab: "other biological determinant",
            name: `stdyDscr/stdyInfo/subject/topcClas[]/value/other biological determinant_${lng}`,
            type: "text"
          },
          {
            vocab: "other determinant",
            name: `stdyDscr/stdyInfo/subject/topcClas[]/value/other determinant_${lng}`,
            type: "text"
          },

          // select
          {
            vocab: "cim-11",
            name: `stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11_${lng}`,
            type: "select"
          }
        ];
        // parcourir les mappings et remplir les champs correspondants selon le type
        for (const map of mappings) {
          let found = topcClasTopic.filter((item) => norm(item.vocab) === norm(map.vocab));
          if (!found.length) continue;

          let topics = found.map((i) => (i.topic ? i.topic.trim() : "")).filter(Boolean);
          if (!topics.length) continue;

          if (map.type === "checkbox") {
            let checkboxes = jQuery(`input[name="${map.name}"]`);
            let checkedCount = 0;
            checkboxes.each(function () {
              if (topics.includes(jQuery(this).val())) {
                jQuery(this).prop("checked", true).trigger("change");
                checkedCount++;
              }
            });
          } else if (map.type === "select") {
            createOptionCim11(found, lng);
          } else {
            // GESTION DES INPUTS TEXTES
            let val = topics[0];
            let el = jQuery(`[name="${map.name}"]`);

            if (el.length) {
              el.val(val).trigger("change");
            }
          }
        }
      }
      // Fin topcClas

      let additionalRareDiseases = jsonRec["additional/theme/RareDiseases_" + lng];
      let radioRareDiseases = jQuery(`[name="additional/theme/RareDiseases_${lng}"][value="${additionalRareDiseases}"]`);
      radioRareDiseases.prop("disabled", false).prop("checked", true).trigger("change");

      let additionalIsHealthTheme = jsonRec["additional/isHealthTheme_" + lng];
      let radioIsHealthTheme = jQuery(`[name="additional/isHealthTheme_${lng}"][value="${additionalIsHealthTheme}"]`);
      radioIsHealthTheme.prop("disabled", false).prop("checked", true).trigger("change");

      let additionalIsContributorPI = jsonRec["additional/isContributorPI_" + lng];
      let radioIsContributorPI = jQuery(`[name="additional/isContributorPI_${lng}"][value="${additionalIsContributorPI}"]`);
      radioIsContributorPI.prop("disabled", false).prop("checked", true).trigger("change");

      let additionalAddTeamMember = jsonRec["additional/addTeamMember_" + lng];
      let radioAddTeamMember = jQuery(`[name="additional/addTeamMember_${lng}"][value="${additionalAddTeamMember}"]`);
      radioAddTeamMember.prop("disabled", false).prop("checked", true).trigger("change");

      let additionalIsPIContact = jsonRec["additional/primaryInvestigator/isPIContact_" + lng];
      let radioIsPIContact = jQuery(`[name="additional/primaryInvestigator/isPIContact_${lng}"][value="${additionalIsPIContact}"]`);
      radioIsPIContact.prop("disabled", false).prop("checked", true).trigger("change");

      /** step 2 */
      // Désactiver la select anlyUnitFake
      jQuery(`select[name="stdyDscr/stdyInfo/sumDscr/anlyUnitFake_${lng}"]`).prop("disabled", true);

      //Remplissage checkbox
      let researchPurpose = jsonRec["additional/interventionalStudy/researchPurpose_" + lng];
      if (researchPurpose?.length > 0) {
        setFieldValues(researchPurpose, `additional/interventionalStudy/researchPurpose_${lng}[]`, "checkbox");
      }

      let trialPhase = JSON.stringify(jsonRec[`additional/interventionalStudy/trialPhase_${lng}`]);

      if (trialPhase?.length > 0) {
        setFieldValues(trialPhase, `additional/interventionalStudy/trialPhase_${lng}[]`, "checkbox");
      }

      let interventionalStudyShema = jsonRec["additional/interventionalStudy/interventionalStudyModel_" + lng];
      if (interventionalStudyShema) {
        jQuery(`select[name="additional/interventionalStudy/interventionalStudyModel_${lng}"]`).val(interventionalStudyShema).change();
      }

      let allocationMode = jsonRec["additional/allocation/allocationMode_" + lng];
      jQuery(`select[name="additional/allocation/allocationMode_${lng}"]`).val(allocationMode).change();

      let allocationUnit = jsonRec["additional/allocation/allocationUnit_" + lng];
      jQuery(`select[name="additional/allocation/allocationUnit_${lng}"]`).val(allocationUnit).change();

      let maskingType = jsonRec["additional/masking/maskingType_" + lng];
      jQuery(`select[name="additional/masking/maskingType_${lng}"]`).val(maskingType).change();

      let blindedMaskingDetails = JSON.stringify(jsonRec[`additional/masking/blindedMaskingDetails_${lng}`]);
      if (blindedMaskingDetails?.length > 0) {
        setFieldValues(blindedMaskingDetails, `additional/masking/blindedMaskingDetails_${lng}[]`, "checkbox");
      }

      /* Start Repeater Arms */
      armsData[lng] = jsonRec?.[`additional/arms_${lng}`] ?? [];
      /* Start Repeater Intervention/Exposure */
      interventionData[lng] = jsonRec?.[`additional/intervention_${lng}`];
      datasetPIDData[lng] = jsonRec[`additional/fileDscr/fileTxt/fileCitation/titlStmt/IDno/values_${lng}`];

      /* End Repeater Intervention/Exposure */
      inclusionGroupsData[lng] = jsonRec?.[`additional/inclusionGroups_${lng}`] ?? [];

      /***** STEP 3 */
      let targetSampleSize = jsonRec["stdyDscr/method/dataColl/targetSampleSize_" + lng];
      jQuery(`select[name="stdyDscr/method/dataColl/targetSampleSize_${lng}"]`).val(targetSampleSize).change();

      let timeMeth = JSON.stringify(jsonRec["stdyDscr/method/dataColl/timeMeth_" + lng]);
      if (timeMeth) {
        jQuery('select[name="stdyDscr/method/dataColl/timeMeth"]').val(timeMeth).change();
      }

      let recrutementTiming = JSON.stringify(jsonRec["additional/cohortLongitudinal/recrutementTiming_" + lng]);
      if (recrutementTiming?.length > 0) {
        setFieldValues(recrutementTiming, `additional/cohortLongitudinal/recrutementTiming_${lng}[]`, "checkbox");
      }

      let collMode = JSON.stringify(jsonRec["stdyDscr/method/dataColl/collMode_" + lng]);
      if (collMode?.length > 0) {
        setFieldValues(collMode, `stdyDscr/method/dataColl/collMode_${lng}[]`, "checkbox");
      }

        let sampProc = jsonRec["stdyDscr/method/dataColl/sampProc_" + lng];
        if (Array.isArray(sampProc) && sampProc.length > 0) {
            setFieldValues(sampProc, `stdyDscr/method/dataColl/sampProc_${lng}[]`, "checkbox");
        }
      let frameUnitType = jsonRec["stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_" + lng];
      if (frameUnitType?.length > 0) {
        frameUnitType = parsePseudoArray(frameUnitType);
        setFieldValues(frameUnitType, `stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_${lng}[]`, "checkbox");
      }

      let collDates = jsonRec["stdyDscr/stdyInfo/sumDscr/collDate_" + lng];
      if (collDates?.length > 0) {
        let eventStart = collDates[0]?.start || "";
        let eventEnd = collDates[0]?.end || "";

        // si date par défaut 3000-01-01, on vide le champ
        if (eventEnd === "3000-01-01") {
          eventEnd = "";
        }

        jQuery('[name="stdyDscr/stdyInfo/sumDscr/collDate/event_start"]').val(eventStart).change();
        jQuery('[name="stdyDscr/stdyInfo/sumDscr/collDate/event_end"]').val(eventEnd).change();
      }

      let isActiveFollowUp = jsonRec["additional/activeFollowUp/isActiveFollowUp_" + lng];

      if (isActiveFollowUp) {
        let radioActiveFollowUp = jQuery(`[name="additional/activeFollowUp/isActiveFollowUp_${lng}"][value="${isActiveFollowUp}"]`);
        radioActiveFollowUp.prop("disabled", false).prop("checked", true).trigger("change");
      }

      let notesData = jsonRec["stdyDscr/method/notes_" + lng];
      if (Array.isArray(notesData)) {
        let foundFollowUp = notesData.find((n) => n.subject?.toLowerCase().trim() === "follow-up");

        if (foundFollowUp && Array.isArray(foundFollowUp.values) && foundFollowUp.values.length) {
          setFieldValues(foundFollowUp.values, `stdyDscr/method/notes/subject_followUP_${lng}[]`, "checkbox");
        }

        let foundResearchType = notesData.find((n) => n.subject?.toLowerCase().trim() === "research type");

        if (foundResearchType?.values?.length) {
          let researchType = foundResearchType.values[0];
          jQuery(`select[name="stdyDscr/method/notes/subject_researchType_${lng}"]`).val(researchType).change();

          if (researchType === "Etude observationnelle" || researchType === "Observational Study") {
            autoRepeatClickInerExpo2("#add-intervExpo2", interventionData);
            autoRepeatClickInclusionObservational("#add-observational-inclusion-group", inclusionGroupsData);
          } else {
            autoRepeatClickInerExpo("#add-intervExpo", interventionData);
            autoRepeatClickInclusionInterventional("#add-interventional-inclusion-group", inclusionGroupsData);
          }
        }
        let foundObservationalStudyMethod = notesData.find((n) => n.subject?.toLowerCase().trim() === "observational study method");
        if (foundObservationalStudyMethod?.values?.length) {
          jQuery(`select[name="stdyDscr/method/notes_${lng}"]`).val(foundObservationalStudyMethod.values[0]).change();
        }
      }

      let isDataIntegration = jsonRec["additional/dataCollectionIntegration/isDataIntegration_" + lng];
      if (isDataIntegration) {
        let radioisDataIntegration = jQuery(`[name="additional/dataCollectionIntegration/isDataIntegration_${lng}"][value="${isDataIntegration}"]`);
        radioisDataIntegration.prop("disabled", false).prop("checked", true).trigger("change");
      }
      let ddiSources = jsonRec["stdyDscr/method/dataColl/sources/values_" + lng] || [];
      let otherSources = jsonRec["additional/thirdPartySource/otherSourceType_" + lng] || [];
      sourcesData[lng] = ddiSources.map((source, index) => {
        return {
          ...source,
          otherSourceType: otherSources[index] || ""
        };
      });

      let geogCoverage = jsonRec["stdyDscr/stdyInfo/sumDscr/geogCover_" + lng];
      if (geogCoverage) {
        geogCoverage = parsePseudoArray(geogCoverage);
        setFieldValues(geogCoverage, `stdyDscr/stdyInfo/sumDscr/geogCover_${lng}[]`, "checkbox");
      }

      // universe logic
      let level_type = jsonRec["stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I_" + lng];
      if (level_type?.length > 0) {
        jQuery(`select[name="stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I_${lng}"]`).val(level_type).change();
      }

      let level_age_clusion_I = jsonRec["stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I_" + lng];
      if (level_age_clusion_I?.length > 0) {
        setFieldValues(level_age_clusion_I, `stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I_${lng}[]`, "checkbox");
      }

      let level_sex_clusion_I = jsonRec["stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I_" + lng];
      if (level_sex_clusion_I?.length > 0) {
        setFieldValues(level_sex_clusion_I, `stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I_${lng}[]`, "checkbox");
      }

      let avlStatus = jsonRec["stdyDscr/dataAccs/setAvail/avlStatus_" + lng];
      jQuery(`select[name="stdyDscr/dataAccs/setAvail/avlStatus_${lng}"]`).val(avlStatus).change();

      // Caractéristiques données coll -> Identifiant pérenne du jeu de données -> Type de données
      let dataKind = jsonRec["stdyDscr/stdyInfo/sumDscr/dataKind_" + lng];
      if (dataKind) {
        dataKind = parsePseudoArray(dataKind);
        setFieldValues(dataKind, `stdyDscr/stdyInfo/sumDscr/dataKind_${lng}[]`, "checkbox");
      }

      let isDataInBiobank = jsonRec["additional/dataTypes/isDataInBiobank_" + lng];
      if (isDataInBiobank) {
        let radioisDataInBiobank = jQuery(`[name="additional/dataTypes/isDataInBiobank_${lng}"][value="${isDataInBiobank}"]`);
        radioisDataInBiobank.prop("disabled", false).prop("checked", true).trigger("change");
      }

      let biobankContent = jsonRec["additional/dataTypes/biobankContent_" + lng];
      if (biobankContent?.length > 0) {
        setFieldValues(biobankContent, `additional/dataTypes/biobankContent_${lng}[]`, "checkbox");
      }

      let isSpecPermRequired = jsonRec["stdyDscr/dataAccs/useStmt/specPerm/required_yes_" + lng];
      if (isSpecPermRequired) {
        let radioSpecPermRequired = jQuery(`[name="stdyDscr/dataAccs/useStmt/specPerm/required_yes_${lng}"][value="${isSpecPermRequired}"]`);
        radioSpecPermRequired.prop("disabled", false).prop("checked", true).trigger("change");
      }

      let mockSampleAvailable = jsonRec["additional/mockSample/mockSampleAvailable_" + lng];
      if (mockSampleAvailable) {
        let radioMockSampleAvailable = jQuery(`[name="additional/mockSample/mockSampleAvailable_${lng}"][value="${mockSampleAvailable}"]`);
        radioMockSampleAvailable.prop("disabled", false).prop("checked", true).trigger("change");
      }
      let variableDictionnaryAvailable = jsonRec["additional/variableDictionnary/variableDictionnaryAvailable_" + lng];
      if (variableDictionnaryAvailable) {
        let radioVDA = jQuery(`[name="additional/variableDictionnary/variableDictionnaryAvailable_${lng}"][value="${variableDictionnaryAvailable}"]`);
        radioVDA.prop("disabled", false).prop("checked", true).trigger("change");
      }

      contactUseStmtData[lng] = jsonRec["stdyDscr/dataAccs/useStmt/contact_" + lng] ?? [];

      let dataStandardName = jsonRec["stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName/values_" + lng];
      if (dataStandardName?.length > 0) {
        standardNameData[lng] = parsePseudoArray(dataStandardName);
      }

      let otherQualityStatement = jsonRec["stdyDscr/stdyInfo/qualityStatement/otherQualityStatement_" + lng];
      if (otherQualityStatement?.length > 0) {
        otherQualityStatementData[lng] = parsePseudoArray(otherQualityStatement);
      }

      IDnoData[lng] = jsonRec["stdyDscr/citation/titlStmt/IDNo/values_" + lng] ?? [];
      let additional_conformityDeclaration = jsonRec["additional/regulatoryRequirements/conformityDeclaration_" + lng];
      jQuery(`select[name="additional/regulatoryRequirements/conformityDeclaration_${lng}"]`).val(additional_conformityDeclaration).change();

      let isClinicalTrial = jsonRec["additional/interventionalStudy/isClinicalTrial_" + lng];
      if (isClinicalTrial) {
        let radioIsClinicalTrial = jQuery(`[name="additional/interventionalStudy/isClinicalTrial_${lng}"][value="${isClinicalTrial}"]`);
        radioIsClinicalTrial.prop("disabled", false).prop("checked", true).trigger("change");
      }

      let isInclusionGroupsInt = jsonRec["additional/interventionalStudy/isInclusionGroups_" + lng];
      if (isInclusionGroupsInt) {
        let radioIsInclusionGroupsInt = jQuery(`[name="additional/interventionalStudy/isInclusionGroups_${lng}"][value="${isInclusionGroupsInt}"]`);
        radioIsInclusionGroupsInt.prop("disabled", false).prop("checked", true).trigger("change");
      }

      let isInclusionGroupsObs = jsonRec["additional/observationalStudy/isInclusionGroups_" + lng];
      if (isInclusionGroupsObs) {
        let radioIsInclusionGroupsObs = jQuery(`[name="additional/observationalStudy/isInclusionGroups_${lng}"][value="${isInclusionGroupsObs}"]`);
        radioIsInclusionGroupsObs.prop("disabled", false).prop("checked", true).trigger("change");
      }

      docsData[lng] = jsonRec["additional/relatedDocument_" + lng] ?? [];

      /*** les champs autres traité à la fin */

      let otherResearchPurpose = jsonRec["additional/interventionalStudy/otherResearchPurpose_" + lng];
      if (otherResearchPurpose) {
        jQuery(`[name="additional/interventionalStudy/otherResearchPurpose_${lng}"]`).val(otherResearchPurpose).change();
      }
      let recruitmentSourceOther = jsonRec["additional/dataCollection/recruitmentSourceOther_" + lng];
      if (recruitmentSourceOther) {
        jQuery(`[name="additional/dataCollection/recruitmentSourceOther_${lng}"]`).val(recruitmentSourceOther).change();
      }
      let followUpModeOther = jsonRec["additional/activeFollowUp/followUpModeOther_" + lng];
      if (followUpModeOther) {
        jQuery(`[name="additional/activeFollowUp/followUpModeOther_${lng}"]`).val(followUpModeOther).change();
      }
      let dataTypeOther = jsonRec["additional/dataTypes/dataTypeOther_" + lng];
      if (dataTypeOther) {
        jQuery(`[name="additional/dataTypes/dataTypeOther_${lng}"]`).val(dataTypeOther).change();
      }
      let paraclinicalDataOther = jsonRec["additional/dataTypes/paraclinicalDataOther_" + lng];
      if (paraclinicalDataOther) {
        jQuery(`[name="additional/dataTypes/paraclinicalDataOther_${lng}"]`).val(paraclinicalDataOther).change();
      }
      let biologicalDataDetails = jsonRec["additional/dataTypes/biologicalDataDetails_" + lng];
      if (biologicalDataDetails) {
        jQuery(`[name="additional/dataTypes/biologicalDataDetails_${lng}"]`).val(biologicalDataDetails).change();
      }
      let biobankContentOther = jsonRec["additional/dataTypes/biobankContentOther_" + lng];
      if (biobankContentOther) {
        jQuery(`[name="additional/dataTypes/biobankContentOther_${lng}"]`).val(biobankContentOther).change();
      }
      let otherLiquidsDetails = jsonRec["additional/dataTypes/otherLiquidsDetails_" + lng];
      if (otherLiquidsDetails) {
        jQuery(`[name="additional/dataTypes/otherLiquidsDetails_${lng}"]`).val(otherLiquidsDetails).change();
      }
    }

    autoRepeatClickObtainedAuthorization("#add-ObtainedAuthorization", authorizingAgencyData);
    autoRepeatClickPrimaryInvestigator("#add-PrimaryInvestigator", investigatorsNumber, investigatorData);
    autoRepeatClickFundingAgent("#add-fundingAgent", fundingAgenciesData);
    autoRepeatClickSponsor("#add-sponsor", prodStmtProducerData);
    autoRepeatClickContactPoint("#add-ContactPoint", contactData);
    autoRepeatClickContributor("#add-TeamMember", contributorData);
    autoRepeatClickArms("#add-arm", armsData);
    autoRepeatClickSources("#add-sources", sourcesData);
    autoRepeatClickDatasetPID("#add-DatasetPID", datasetPIDData);
    autoRepeatClickInformationContact("#add-DataInformationContact", contactUseStmtData);
    autoRepeatClickStandardName("#add-standard", standardNameData);
    autoRepeatClickOtherQualityStatement("#add-DataQuality", otherQualityStatementData);
    autoRepeatClickIDno("#add-OtherStudyId", IDnoData);
    autoRepeatClickDocs("#add-RelatedDocument", docsData);
  }
}

// =========  init.js - Initialisations globales =========//
/**
 * Fonctions d’initialisation générales
 * Setup global behaviors (animations, tooltips, etc.)
 */

import { toggleFranceRegion } from "./helpers.js";

export function initJs() {
  jQuery(document).ready(function ($) {
    function toggleInvestigatorBlocks(isPI, type = "changePI") {
      const $repeaters = $("#repeater-PrimaryInvestigator .repeater-item");

      $repeaters.each(function (index) {
        const $bloc = $(this);
        const $details = $bloc.find("#showinvestigatordetails");
        const $detailsLastname = $bloc.find("#showinvestigatordetailslastname");
        const $orcid = $bloc.find("#showinvestigatorOrcid");
        const $affiliation = $bloc.find("#showinvestigatorPiaffiliation");
        const $piemailshow = $bloc.find("#showemailpi");

        if (isPI === true) {
          //  "Oui / Yes"
          if (index === 0) {
            // First repeater — stays hidden (keep d-none)
            $details.addClass("d-none");
            $detailsLastname.addClass("d-none");
            $piemailshow.addClass("d-none");
            fillFirstRepeaterWithConnectedUser();
          } else {
            // 2️ Next repeaters — always show
            $details.removeClass("d-none");
            $detailsLastname.removeClass("d-none");
            $orcid.removeClass("d-none");
            $affiliation.removeClass("d-none");
            $piemailshow.removeClass("d-none");
          }
        } else {
          if (index === 0) {
            //a cause du probléme dans edit (si j'ajoute un nouveau block , la premiere block vider)
            //j'ai ajouter cette condition

            if (type == "changePI") {
              clearFirstRepeater();
            }
          }

          //  "Non / No" — show all repeaters completely
          $details.removeClass("d-none");
          $detailsLastname.removeClass("d-none");
          $orcid.removeClass("d-none");
          $affiliation.removeClass("d-none");
          $piemailshow.removeClass("d-none");
        }
      });
    }

    function fillFirstRepeaterWithConnectedUser() {
      if (typeof wpUserData === "undefined") return;

      const firstName = wpUserData.first_name || "";
      const lastName = wpUserData.last_name || "";
      const email = wpUserData.email || "";

      const $firstRepeater = $("#repeater-PrimaryInvestigator .repeater-item").first();

      // Adjust selectors to your real input names/classes
      $firstRepeater.find('input[name^="stdyDscr/citation/rspStmt/AuthEnty/firstname"]').val(firstName);
      $firstRepeater.find('input[name^="stdyDscr/citation/rspStmt/AuthEnty/lastname"]').val(lastName);
      $firstRepeater.find('input[name^="additional/primaryInvestigator/piMail"]').val(email);
    }

    function clearFirstRepeater() {
      const $firstRepeater = $("#repeater-PrimaryInvestigator .repeater-item").first();

      $firstRepeater.find('input[name^="stdyDscr/citation/rspStmt/AuthEnty/firstname"]').val("");
      $firstRepeater.find('input[name^="stdyDscr/citation/rspStmt/AuthEnty/lastname"]').val("");
      $firstRepeater.find('input[name^="additional/primaryInvestigator/piMail"]').val("");

      // unlock fields
      $firstRepeater.find("input").prop("readonly", false);
    }

    $(document).on(
      "change",
      'input[name="stdyDscr/method/dataColl/sources/srcOrig_fr[]"], input[name="stdyDscr/method/dataColl/sources/srcOrig_en[]"]',
      function () {
        const $item = $(this).closest(".repeater-item");
        let checked = $item
          .find(
            'input[name="stdyDscr/method/dataColl/sources/srcOrig_fr[]"]:checked, input[name="stdyDscr/method/dataColl/sources/srcOrig_en[]"]:checked'
          )
          .map(function () {
            return $(this).val();
          })
          .get();

        const $otherBloc = $item.find("#OtherSourceTypeBloc");

        if (checked.includes("Autre") || checked.includes("Other")) {
          $otherBloc.removeClass("d-none");
        } else {
          resetContainerFields("OtherSourceTypeBloc");
        }
      }
    );

    $(document).on("change", 'input[name="additional/governance/committee_fr"], input[name="additional/governance/committee_en"]', function () {
      $("#committeeDetailBloc, #committeeDetailBlocOthers").addClass("d-none");
      const val = $(this).val();

      if ((val === "Yes" || val === "Oui") && $(this).is(":checked")) {
        resetContainerFields("committeeDetailBlocOthers");
        $("#committeeDetailBloc").removeClass("d-none");
      } else if ((val === "Other" || val === "Autre") && $(this).is(":checked")) {
        resetContainerFields("committeeDetailBloc");
        $("#committeeDetailBlocOthers").removeClass("d-none");
      } else {
        resetContainerFields("committeeDetailBloc");
        resetContainerFields("committeeDetailBlocOthers");
      }
    });

    $(document).on(
      "change",
      'input[name="additional/collaborations/networkConsortium_fr"], input[name="additional/collaborations/networkConsortium_en"]',
      function () {
        const val = $(this).val(); // Oui / Yes

        // Toujours tout cacher avant
        $("#CollaborationDetailsPrecision").addClass("d-none");

        if ((val === "Oui" || val === "Yes") && $(this).is(":checked")) {
          $("#CollaborationDetailsPrecision").removeClass("d-none");
        } else {
          resetContainerFields("CollaborationDetailsPrecision");
        }
      }
    );

    $(document).on("change", 'input[name="additional/addTeamMember_fr"]', function () {
      if ($(this).val() === "Oui" && $(this).is(":checked")) {
        $("#teamMemberCollaps").removeClass("d-none");
      } else {
        resetContainerFields("teamMemberCollaps");
      }
    });

    $(document).on("change", 'input[name="additional/isContributorPI_fr"] , input[name="additional/isContributorPI_en"]', function () {
      const val = $(this).val();
      const isPI = (val === "Oui" || val === "Yes") && $(this).is(":checked");
      toggleInvestigatorBlocks(isPI, "changePI");
    });

    //  Reapply logic when adding new repeaters dynamically
    $(document).on("click", "#add-PrimaryInvestigator", function () {
      setTimeout(() => {
        const initFR = $('input[name="additional/isContributorPI_fr"]:checked').val();
        const initEN = $('input[name="additional/isContributorPI_en"]:checked').val();
        if (initFR) toggleInvestigatorBlocks(initFR === "Oui", "AddBlock");
        else if (initEN) toggleInvestigatorBlocks(initEN === "Yes", "AddBlock");
      }, 200);
    });

    $(document).on(
      "change",
      'select[name="stdyDscr/method/notes/subject_researchType_fr"], select[name="stdyDscr/method/notes/subject_researchType_en"]',
      function () {
        let value = $(this).val() || "";
        if (!value || value.trim() === "") {
          return;
        }
        // Interventional
        if (value.includes("Etude interventionnelle") || value === "Interventional Study") {
          $("#interventionalStudyBloc").removeClass("d-none");
          $("#observationalStudyBloc").addClass("d-none");
          resetContainerFields("observationalStudyBloc");
        }

        // Observational
        else if (value === "Etude observationnelle" || value === "Observational Study") {
          $("#observationalStudyBloc").removeClass("d-none");
          $("#interventionalStudyBloc").addClass("d-none");
          resetContainerFields("interventionalStudyBloc");
        }
        // None
        else {
          $("#interventionalStudyBloc").addClass("d-none");
          $("#observationalStudyBloc").addClass("d-none");
          resetContainerFields("interventionalStudyBloc");
          resetContainerFields("observationalStudyBloc");
        }
      }
    );

    $(document).on(
      "change",
      'input[name="additional/interventionalStudy/isClinicalTrial_fr"] , input[name="additional/interventionalStudy/isClinicalTrial_en"]',
      function () {
        if ($(this).val() === "Oui" || $(this).val() === "Yes") {
          $("#trialPhase").removeClass("d-none");
        } else {
          resetContainerFields("trialPhase");
        }
      }
    );

    $(document).on(
      "change",
      'input[name="additional/interventionalStudy/isInclusionGroups_fr"], input[name="additional/interventionalStudy/isInclusionGroups_en"]',
      function () {
        if ($(this).val() === "Oui" || $(this).val() === "Yes") {
          $("#interventional-inclusion-group").removeClass("d-none");
          $("#title-interventional-inclusion-group").removeClass("d-none");
          $("#add-interventional-inclusion-group").removeClass("d-none");
        } else {
          resetContainerFields("interventional-inclusion-group");
          $("#title-interventional-inclusion-group").addClass("d-none");
          $("#add-interventional-inclusion-group").addClass("d-none");
        }
      }
    );

    $(document).on(
      "change",
      'input[name="additional/observationalStudy/isInclusionGroups_fr"], input[name="additional/observationalStudy/isInclusionGroups_en"]',
      function () {
        if ($(this).val() === "Oui" || $(this).val() === "Yes") {
          $("#observational-inclusion-group").removeClass("d-none");
          $("#title-observational-inclusion-group").removeClass("d-none");
          $("#add-observational-inclusion-group").removeClass("d-none");
        } else {
          resetContainerFields("observational-inclusion-group");
          $("#title-observational-inclusion-group").addClass("d-none");
          $("#add-observational-inclusion-group").addClass("d-none");
        }
      }
    );

    $(document).on("change", 'input[name^="additional/interventionalStudy/researchPurpose_"]', function () {
      let name = $(this).attr("name");
      let checked = $(`input[name="${name}"]:checked`)
        .map(function () {
          return $(this).val();
        })
        .get();

      if (checked.includes("Autre") || checked.includes("Other")) {
        $("#otherResearchPurpose").removeClass("d-none");
      } else {
        resetContainerFields("otherResearchPurpose");
      }
    });

    $(document).on("change", 'select[name="additional/arms/armsType_fr"], select[name="additional/arms/armsType_en"]', function () {
      if ($(this).val() == "Autre" || $(this).val() == "Other") {
        $("#ArmTypeOtherBloc").removeClass("d-none");
      } else {
        resetContainerFields("ArmTypeOtherBloc");
      }
    });

    // Autre mode de collecte
    $(document).on("change", 'input[name^="stdyDscr/method/dataColl/collMode_"]', function () {
      let name = $(this).attr("name");
      let checked = $(`input[name="${name}"]:checked`)
        .map(function () {
          return $(this).val();
        })
        .get();

      if (checked.includes("Autre") || checked.includes("Other")) {
        $("#CollectionFrequencyBloc").removeClass("d-none");
      } else {
        resetContainerFields("CollectionFrequencyBloc");
      }
    });

    $(document).on("change", 'select[name="stdyDscr/method/notes_fr"],select[name="stdyDscr/method/notes_en"]', function () {
      if ($(this).val() === "Autre" || $(this).val() === "Other") {
        $("#OtherResearchTypeBloc").removeClass("d-none");
      } else {
        resetContainerFields("OtherResearchTypeBloc");
      }
    });

    // arms bloc
    const container_armsBloc = jQuery(`[data-containerToDuplicate="armsBloc"]`);
    const armsBloc = container_armsBloc.find(`[data-inputToDuplicate="armsBloc"] input`);

    const wasDisabled = armsBloc.prop("disabled");
    armsBloc.prop("disabled", false);
    // Déclencher le change
    armsBloc.trigger("input").trigger("change"); // si tu veux les deux
    // Rétablir l'état disabled
    armsBloc.prop("disabled", wasDisabled);

    const container_InclusionGroups = jQuery(`[data-containerToDuplicate="inclusionGroupsBloc"]`);
    const InclusionGroups = container_InclusionGroups.find(`[data-inputToDuplicate="inclusionGroupsBloc"] input`);

    const wasDisabled_InclusionGroups = InclusionGroups.prop("disabled");
    InclusionGroups.prop("disabled", false);
    InclusionGroups.trigger("input").trigger("change");
    InclusionGroups.prop("disabled", wasDisabled_InclusionGroups);

    const container_InclusionGroups2 = jQuery(`[data-containerToDuplicate="inclusionGroupsBloc2"]`);
    const InclusionGroups2 = container_InclusionGroups2.find(`[data-inputToDuplicate="inclusionGroupsBloc2"] input`);

    const wasDisabled_InclusionGroups2 = InclusionGroups2.prop("disabled");
    InclusionGroups2.prop("disabled", false);
    InclusionGroups2.trigger("input").trigger("change");
    InclusionGroups2.prop("disabled", wasDisabled_InclusionGroups2);

    $(document).on(
      "change",
      'input[name="stdyDscr/method/dataColl/collMode_fr[]"], input[name="stdyDscr/method/dataColl/collMode_en[]"]',
      function () {
        let checked = $('input[name^="stdyDscr/method/dataColl/collMode_"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();

        if (checked.includes("Autre") || checked.includes("Other")) {
          $("#collectionModeOther").removeClass("d-none");
        } else {
          resetContainerFields("collectionModeOther");
        }
      }
    );

    //SamplingModeOther
    $(document).on(
      "change",
      'input[name="stdyDscr/method/dataColl/sampProc_fr[]"] , input[name="stdyDscr/method/dataColl/sampProc_en[]"]',
      function () {
        let checked = $('input[name^="stdyDscr/method/dataColl/sampProc_"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();

        if (checked.includes("Autre") || checked.includes("Other")) {
          $("#SamplingModeOther").removeClass("d-none");
        } else {
          resetContainerFields("SamplingModeOther");
        }
      }
    );

    //RecruitmentSourceOther
    $(document).on(
      "change",
      'input[name="stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_fr[]"] , input[name="stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_en[]"]',
      function () {
        let checked = $('input[name^="stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();
        if (checked.includes("Autre") || checked.includes("Other")) {
          $("#RecruitmentSourceOther").removeClass("d-none");
        } else {
          resetContainerFields("RecruitmentSourceOther");
        }
      }
    );

    //FollowUpMode
    $(document).on(
      "change",
      'input[name="additional/activeFollowUp/isActiveFollowUp_en"] , input[name="additional/activeFollowUp/isActiveFollowUp_fr"]',
      function () {
        const val = $(this).val();
        if ((val === "Oui" || val === "Yes") && $(this).is(":checked")) {
          $("#FollowUpMode").removeClass("d-none");
        } else {
          resetContainerFields("FollowUpMode");
          resetContainerFields("FollowUpModeOther");

          $('input[name="stdyDscr/method/notes/subject_followUP_fr[]"]').prop("checked", false);
        }
      }
    );

    //FollowUpModeOther
    $(document).on(
      "change",
      'input[name="stdyDscr/method/notes/subject_followUP_en[]"], input[name="stdyDscr/method/notes/subject_followUP_fr[]"]',
      function () {
        let checked = $(
          'input[name="stdyDscr/method/notes/subject_followUP_en[]"]:checked, input[name="stdyDscr/method/notes/subject_followUP_fr[]"]:checked'
        )
          .map(function () {
            return $(this).val();
          })
          .get();

        if (checked.includes("Autre") || checked.includes("Other")) {
          $("#FollowUpModeOther").removeClass("d-none");
        } else {
          resetContainerFields("FollowUpModeOther");
        }
      }
    );

    //DataTypeOther
    $(document).on(
      "change",
      'input[name="stdyDscr/stdyInfo/sumDscr/dataKind_fr[]"], input[name="stdyDscr/stdyInfo/sumDscr/dataKind_en[]"]',
      function () {
        let checked = $('input[name^="stdyDscr/stdyInfo/sumDscr/dataKind_"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();

        if (checked.includes("Autre") || checked.includes("Other")) {
          $("#DataTypeOther").removeClass("d-none");
        } else {
          resetContainerFields("DataTypeOther");
        }

        if (checked.includes("Données cliniques") || checked.includes("Clinical data")) {
          $("#ClinicalDataDetails").removeClass("d-none");
        } else {
          resetContainerFields("ClinicalDataDetails");
        }

        if (checked.includes("Données paracliniques (hors biologiques) : Autre") || checked.includes("Paraclinical data (non-biological) : Other")) {
          $("#ParaclinicalDataOther").removeClass("d-none");
          // re remplir le champs : additional/dataTypes/paraclinicalDataOther
          // OC : j'ai commenter ce code car il est unitile
          /*nadaSetFieldValue('additional/dataTypes/paraclinicalDataOther_en',jsonRec['additional/dataTypes/paraclinicalDataOther_en'],'text')
                    nadaSetFieldValue('additional/dataTypes/paraclinicalDataOther_fr',jsonRec['additional/dataTypes/paraclinicalDataOther_fr'],'text')*/
        } else {
          resetContainerFields("ParaclinicalDataOther");
        }

        if (checked.includes("Données biologiques") || checked.includes("Biological data")) {
          $("#BiologicalDataDetails").removeClass("d-none");
          $("#isDataInBiobank").removeClass("d-none");
        } else {
          $("#BiologicalDataDetails").addClass("d-none").find("input").val("");
          $("#isDataInBiobank").addClass("d-none").find("select").val("");
          resetContainerFields("BiologicalDataDetails");
          resetContainerFields("isDataInBiobank");
          resetContainerFields("BiobankContent");
        }
      }
    );

    $(document).on(
      "change",
      'input[name="additional/dataTypes/isDataInBiobank_fr"], input[name="additional/dataTypes/isDataInBiobank_en"]',
      function () {
        if ($(this).val() === "Oui" || $(this).val() === "Yes") {
          $("#BiobankContent").removeClass("d-none");
        } else {
          resetContainerFields("BiobankContent");
          resetContainerFields("BiobankContentOther");
          resetContainerFields("OtherLiquidsDetails");
        }
      }
    );

    $(document).on(
      "change",
      'input[name="additional/dataTypes/biobankContent_fr[]"], input[name="additional/dataTypes/biobankContent_en[]"]',
      function () {
        let checked = $('input[name^="additional/dataTypes/biobankContent_"]:checked')
          .map(function () {
            return $(this).val();
          })
          .get();

        if (checked.includes("Autres") || checked.includes("Others")) {
          $("#BiobankContentOther").removeClass("d-none");
        } else {
          resetContainerFields("BiobankContentOther");
        }
        if (checked.includes("Autres liquides ou sécretions biologiques") || checked.includes("Other fluids and secretions")) {
          $("#OtherLiquidsDetails").removeClass("d-none");
        } else {
          resetContainerFields("OtherLiquidsDetails");
        }
      }
    );

    $(document).on(
      "change",
      'input[name="additional/dataCollectionIntegration/isDataIntegration_fr"], input[name="additional/dataCollectionIntegration/isDataIntegration_en"]',
      function () {
        if ($(this).val() === "Oui" || $(this).val() === "Yes") {
          $("#DataIntegration").removeClass("d-none");
        } else {
          resetContainerFields("DataIntegration");
        }
      }
    );

    $(document).on(
      "change",
      'input[name="stdyDscr/dataAccs/useStmt/specPerm/required_yes_fr"], input[name="stdyDscr/dataAccs/useStmt/specPerm/required_yes_en"]',
      function () {
        if ($(this).val() === "Oui" || $(this).val() === "Yes") {
          $("#DataAccessRequestToolLocation").removeClass("d-none");
        } else {
          resetContainerFields("DataAccessRequestToolLocation");
        }
      }
    );

    $(document).on(
      "change",
      'input[name="additional/variableDictionnary/variableDictionnaryAvailable_fr"], input[name="additional/variableDictionnary/variableDictionnaryAvailable_en"]',
      function () {
        if ($(this).val() === "Oui" || $(this).val() === "Yes") {
          $("#VariableDictionnaryLink").removeClass("d-none");
        } else {
          resetContainerFields("VariableDictionnaryLink");
        }
      }
    );

    $(document).on(
      "change",
      'input[name="additional/mockSample/mockSampleAvailable_fr"], input[name="additional/mockSample/mockSampleAvailable_en"]',
      function () {
        if ($(this).val() === "Oui" || $(this).val() === "Yes") {
          $("#MockSampleLocation").removeClass("d-none");
        } else {
          resetContainerFields("MockSampleLocation");
        }
      }
    );

    $(document).on("change", 'input[name^="stdyDscr/stdyInfo/subject/topcClas[]/value/health determinant_fr"]', function () {
      let checked = $('input[name^="stdyDscr/stdyInfo/subject/topcClas[]/value/health determinant_fr"]:checked')
        .map(function () {
          return $(this).val();
        })
        .get();

      const toggles = [
        {
          text: "Déterminants socio-démographiques et économiques : Autre",
          id: "OtherSocioDemoDeterminant"
        },
        {
          text: "Déterminants environnementaux : Autre",
          id: "OtherEnvironmentalDeterminant"
        },
        {
          text: "Déterminants liés au système de santé : Autre",
          id: "OtherHealthcarSystemDeterminant"
        },
        {
          text: "Déterminants comportementaux : Autre",
          id: "OtherBehavioralDeterminant"
        },
        {
          text: "Déterminants biologiques : Autre",
          id: "OtherBiologicalDeterminant"
        },
        {
          text: "Autre",
          id: "OtherDeterminant"
        }
      ];

      const decodeHtml = (str) => $("<textarea/>").html(str).text();

      for (const t of toggles) {
        const decodedText = decodeHtml(t.text).trim();

        if (checked.includes(decodedText)) {
          $(`#${t.id}`).removeClass("d-none");
        } else {
          resetContainerFields(t.id);
        }
      }
    });

    $(document).on("change", 'input[name^="stdyDscr/stdyInfo/subject/topcClas[]/value/health determinant_en"]', function () {
      let checked = $('input[name^="stdyDscr/stdyInfo/subject/topcClas[]/value/health determinant_en"]:checked')
        .map(function () {
          return $(this).val();
        })
        .get();

      const toggles = [
        {
          text: "Socio-demographic and economic determinants: Other",
          id: "OtherSocioDemoDeterminant"
        },
        {
          text: "Environmental determinants: Other",
          id: "OtherEnvironmentalDeterminant"
        },
        {
          text: "Healthcare system determinants: Other",
          id: "OtherHealthcarSystemDeterminant"
        },
        {
          text: "Behavioral determinants: Other",
          id: "OtherBehavioralDeterminant"
        },
        {
          text: "Biological determinants: Other",
          id: "OtherBiologicalDeterminant"
        },
        {
          text: "Other",
          id: "OtherDeterminant"
        }
      ];

      for (const t of toggles) {
        if (checked.includes(t.text)) {
          $(`#${t.id}`).removeClass("d-none");
        } else {
          resetContainerFields(t.id);
        }
      }
    });

    $(document).on(
      "change",
      'select[name="stdyDscr/stdyInfo/sumDscr/nation_fr[]"], select[name="stdyDscr/stdyInfo/sumDscr/nation_en[]"]',
      function () {
        toggleFranceRegion($(this).val());

        let placeholderText = $(this).attr("name").includes("_fr") ? "-- Sélectionner --" : "-- Select --";

        $(this).next(".select2-container").find(".select2-search__field").attr("placeholder", placeholderText);
      }
    );

    // ORGANISATION DES ELEMENTS DANS LE DOM
    $(".parent-checkbox").each(function () {
      let $parentWrapper = $(this);
      let $parentInput = $parentWrapper.find(".form-check-input");

      let parentName = $parentInput.attr("data-parent") ? $parentInput.attr("data-parent").trim() : "";
      let parentUri = $parentInput.attr("data-uri") ? $parentInput.attr("data-uri").trim() : "";
      let lng = $parentInput.attr("attr-lng"); // Récupère 'fr' ou 'en'

      // Sécurité pour éviter les bugs si le texte contient des guillemets
      parentName = parentName.replaceAll('"', '\\"');
      parentUri = parentUri.replaceAll(/"/g, '\\"');

      let selectors = [];
      // On ajoute attr-lng pour ne chercher que les enfants de la meme langue
      if (parentName) selectors.push('.child-checkbox .form-check-input[attr-lng="' + lng + '"][data-child="' + parentName + '"]');
      if (parentUri) selectors.push('.child-checkbox .form-check-input[attr-lng="' + lng + '"][data-child="' + parentUri + '"]');

      if (selectors.length > 0) {
        let $childrenInputs = $(selectors.join(", "));
        let $lastElement = $parentWrapper;

        $childrenInputs.each(function () {
          let $childWrapper = $(this).closest(".child-checkbox");
          $childWrapper.insertAfter($lastElement);
          $lastElement = $childWrapper;
        });
      }
    });

    // GESTION DE L'AFFICHAGE LORS DU CLIC
    $(document).on("change", ".parent-checkbox .form-check-input", function () {
      let $parent = $(this);
      let parentName = $parent.attr("data-parent") ? $parent.attr("data-parent").trim() : "";
      let parentUri = $parent.attr("data-uri") ? $parent.attr("data-uri").trim() : "";
      let lng = $parent.attr("attr-lng");
      let isChecked = $parent.is(":checked");

      parentName = parentName.replaceAll('"', '\\"');
      parentUri = parentUri.replaceAll('"', '\\"');

      let selectors = [];
      if (parentName) selectors.push('.child-checkbox .form-check-input[attr-lng="' + lng + '"][data-child="' + parentName + '"]');
      if (parentUri) selectors.push('.child-checkbox .form-check-input[attr-lng="' + lng + '"][data-child="' + parentUri + '"]');

      if (selectors.length > 0) {
        $(selectors.join(", ")).each(function () {
          let wrapper = $(this).closest(".child-checkbox");
          if (isChecked) {
            wrapper.show(); // Affiche l'enfant
          } else {
            $(this).prop("checked", false);
            wrapper.hide(); // Masque l'enfant
          }
        });
      }
    });

    // INITIALISATION AU CHARGEMENT DE LA PAGE
    $(".child-checkbox").hide(); // On masque tout par défaut
    $(".parent-checkbox .form-check-input:checked").trigger("change");

    // Modifier le comportement des compteurs de caractères
    $(document).on("input", "input[maxlength], textarea[maxlength]", function () {
      const $field = $(this);
      const $counter = $field.siblings(".char-counter");
      if (!$counter.length) return;

      const max = parseInt($counter.data("max"), 10);
      const len = $field.val().length;
      const remaining = max - len;

      const $remainingSpan = $counter.find(".remaining");
      $remainingSpan.text(remaining);

      // Changer la couleur en fonction du nombre de caractères restants
      if (remaining < 0) {
        $remainingSpan.css("color", "#fe5442");
      } else if (remaining < max * 0.1) {
        $remainingSpan.css("color", "#fe5442");
      } else {
        $remainingSpan.css("color", "#005eb8");
      }
    });

    // Initial trigger pour mettre à jour les compteurs au chargement
    $("input[maxlength], textarea[maxlength]").trigger("input");

    /** fonction 1  */
    $("body").on("input", "textarea.same-data-textarea", function (e, triggeredBySync) {
      if (triggeredBySync) return;

      const $field = $(this);

      const lng = $field.attr("attr-lng"); // "fr" ou "en"
      const otherLng = lng === "fr" ? "en" : "fr";

      // Remonter au bloc répété courant
      const $item = $field.closest(".repeater-item");

      // baseName = name without suffix _fr / _en (and optional [])
      const baseName = $field.attr("name").replace(/_(fr|en)(\[\])?$/, "");

      // Chercher uniquement dans CE repeater-item
      const selector = `textarea[attr-lng="${otherLng}"][name="${baseName}_${otherLng}"]`;
      const $other = $item.find(selector);

      if ($other.length) {
        $other.val($field.val()).trigger("input", [true]);
      }
    });

    /** fonction 2 : il faut factoriser avec la fonction 1 */
    $("body").on("input change", "input.same-data-autocomplete", function (e, triggeredBySync) {
      if (triggeredBySync) return;

      const $field = $(this);

      const lng = $field.attr("attr-lng"); // "fr" or "en"
      const otherLng = lng === "fr" ? "en" : "fr";

      // current repeater-item
      const $item = $field.closest(".repeater-item");

      // base name
      const baseName = $field.attr("name").replace(/_(fr|en)(\[\])?$/, "");

      // other field (same group)
      const selector = `input[attr-lng="${otherLng}"][name="${baseName}_${otherLng}"]`;
      const $other = $item.find(selector);

      if ($other.length) {
        // always read latest value
        const finalValue = $field.val();
        $other.val(finalValue).trigger("input", [true]);
      }
    });

    // Gestion des boutons radio customisés
    $(document).on("click", ".BtnRadio", function () {
      const $btn = $(this);
      const $langContainer = $btn.closest(".lang-input");
      const currentLang = $langContainer.attr("attr-lng");
      const index = $langContainer.find(".BtnRadio").index($btn); // index du bouton (0 ou 1)

      // Trouver le bloc "répétiteur" parent
      const repeaterItem = $btn.closest(".repeater-item");

      // Met à jour le hidden dans la langue actuelle
      const value = $btn.attr("attr-value");
      const $hidden = $langContainer.find('input[type="hidden"]');
      $hidden.val(value).trigger("change");

      // Met à jour le style du bouton actif (dans la langue actuelle)
      $langContainer.find(".BtnRadio").removeClass("active btn-primary").addClass("btn-outline-primary");
      $btn.addClass("active btn-primary").removeClass("btn-outline-primary");

      // Trouver le conteneur de l’autre langue dans le même répéteur
      const $otherLangContainer = repeaterItem.find(".lang-input").filter(function () {
        return $(this).attr("attr-lng") !== currentLang;
      });

      // Trouver le bouton correspondant (même index)
      const $otherBtn = $otherLangContainer.find(".BtnRadio").eq(index);

      const $otherHidden = $otherLangContainer.find('input[type="hidden"]');
      const otherValue = $otherBtn.attr("attr-value");

      // Mettre à jour le bouton et le hidden dans l’autre langue
      $otherLangContainer.find(".BtnRadio").removeClass("active btn-primary").addClass("btn-outline-primary");
      $otherBtn.addClass("active btn-primary").removeClass("btn-outline-primary");
      $otherHidden.val(otherValue);
    });

    $(document).on("change", ".lang-input input[type='radio']", function () {
      const $current = $(this);
      const $group = $current.closest(".lang-input"); // le bloc courant
      // désélectionner seulement les autres radios dans ce bloc
      $group.find("input[type='radio']").not($current).prop("checked", false);
    });

    function formatDateDMY(dateStr) {
      const parts = dateStr.split("-"); // [yyyy, mm, dd]
      return parts[2] + "-" + parts[1] + "-" + parts[0]; // dd-mm-yyyy
    }

    $('input[name="stdyDscr/stdyInfo/sumDscr/collDate/event_start"]').on("change", function () {
      const startDate = $(this).val();
      $('input[name="stdyDscr/stdyInfo/sumDscr/collDate/event_end"]').attr("min", startDate).trigger("change");
    });

    $(".date-check").on("change keyup blur", function () {
      const min = $(this).attr("min");

      const val = $(this).val();

      if (val !== "" && val < min) {
        const minFormatted = formatDateDMY(min);
        $(this).addClass("is-invalid");
        $(this).next(".error-date").show().html(`La date doit être supérieure ou égale à ${minFormatted}`);
      } else {
        $(this).removeClass("is-invalid");
        $(this).next(".error-date").hide();
      }
    });

    //function pour rafraichir les tags
    function refreshTags($select) {
      const id = $select.data("tags-id");
      const $wrapper = $('.tags-wrapper[data-tags-id="' + id + '"]');
      $wrapper.empty();

      ($select.val() || []).forEach(function (val) {
        const text = $select.find('option[value="' + val.replaceAll('"', '\\"') + '"]').text();
        if (!text) return;
        const $chip = $('<span class="tag-chip" data-value="' + val + '"></span>');
        $chip.append(document.createTextNode(text));
        $chip.append('<span class="tag-chip-remove">&times;</span>');
        $wrapper.append($chip);
      });
    }

    // Initialisation des select2
    $("select.js-nada-multi").each(function () {
      const $select = $(this);
      $select.select2({
        width: "100%",
        placeholder: $select.data("placeholder")
      });
      refreshTags($select);
    });

    jQuery(document).on("change", "select.js-nada-multi", function (e, triggeredBySync) {
      if (triggeredBySync) return;

      const $this = jQuery(this);
      const lang = $this.attr("attr-lng");
      const otherLang = lang === "fr" ? "en" : "fr";

      // Recuperer le name de base sans le suffixe de langue
      const nameBase = $this.attr("name").replace(/_(fr|en)(\[\])?$/, "");

      // Trouver le select de l'autre langue correspondant
      const selector = `select.js-nada-multi[attr-lng="${otherLang}"][name^="${nameBase}_"]`;
      const $otherSelect = jQuery(selector);
      if (!$otherSelect.length) return;

      // recupérer les indexes des options sélectionnées dans le select actuel
      const selectedIndexes = [];
      $this.find("option").each(function (i) {
        if (this.selected) selectedIndexes.push(i);
      });

      // Appliquer la sélection au select de l'autre langue
      $otherSelect.find("option").each(function (i) {
        this.selected = selectedIndexes.includes(i);
      });

      // Déclencher le changement sur l'autre select pour mettre à jour les tags
      $otherSelect.trigger("change", [true]);
    });

    $(document).on("change", "select.js-nada-multi", function () {
      refreshTags($(this));
    });

    $(document).on("click", ".tags-select-container .tag-chip-remove", function () {
      const $chip = $(this).closest(".tag-chip");
      const val = $chip.data("value");
      const $container = $chip.closest(".tags-select-container");
      const id = $container.data("tags-id");
      const $select = $('select.js-nada-multi[data-tags-id="' + id + '"]');
      const selected = ($select.val() || []).filter((v) => v != val);
      $select.val(selected).trigger("change");
    });

    function toggleOtherField($container) {
      const key = $container.data("parent") || $container.data("section");
      if (!key) return;

      const $item = $container.closest(".repeater-item");

      const $other = $item.find(`[data-item="${key}"]`);
      if (!$other.length) return;

      let show = false;

      // 1) Si un select a la valeur "Autre"
      $container.find("select").each(function () {
        const val = String($(this).val() || "")
          .trim()
          .toLowerCase();
        if (val === "autre" || val === "autres") show = true;
      });

      // 2) Si une checkbox ou radio cochée a la valeur "Autre"
      if (!show) {
        $container
          .find('input[type="checkbox"], input[type="radio"]')
          .filter(":checked")
          .each(function () {
            const val = String($(this).val() || "")
              .trim()
              .toLowerCase();
            if (val === "autre" || val === "autres") show = true;
          });
      }

      if (show) {
        $other.removeClass("d-none");
      } else {
        $other.addClass("d-none");
        $other.find("input, textarea").val("");
      }
    }

    $(document).on("change", "[data-parent] input, [data-parent] select, [data-section] input, [data-section] select", function () {
      const $container = $(this).closest("[data-parent], [data-section]");
      toggleOtherField($container);
    });

    $(function () {
      $("div[data-parent], section[data-parent], div[data-section], section[data-section]").each(function () {
        toggleOtherField($(this));
      });
    });

    $(document).on(
      "change",
      'select[name="stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I_fr"],select[name = "stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I_en"]',
      function () {
        const name = $(this).attr("name");
        const value = $(this).val();
        if (name.endsWith("_fr")) {
          if (value === "Autre") {
            $("#OtherPopulationType").removeClass("d-none");
          } else {
            resetContainerFields("OtherPopulationType");
          }
        }
        if (name.endsWith("_en")) {
          if (value === "Other") {
            $("#OtherPopulationTypeBloc").removeClass("d-none");
          } else {
            resetContainerFields("OtherPopulationTypeBloc");
          }
        }
      }
    );

    //HealthTheme
    $(document).on("change", 'input[name="additional/isHealthTheme_fr"]', function () {
      if ($(this).val() === "Oui") {
        $("#HealthTheme").removeClass("d-none");
      } else {
        resetContainerFields("HealthTheme");
        resetContainerFields("OtherHealthTheme");
      }
    });

    $(document).on("change", 'input[name="additional/isHealthTheme_en"]', function () {
      if ($(this).val() === "Yes") {
        $("#HealthTheme").removeClass("d-none");
      } else {
        resetContainerFields("HealthTheme");
        resetContainerFields("OtherHealthTheme");
      }
    });

    //OtherHealthTheme
    $(document).on("change", 'input[name^="stdyDscr/stdyInfo/subject/topcClas[]/value/health theme_fr"]', function () {
      let checked = $('input[name^="stdyDscr/stdyInfo/subject/topcClas[]/value/health theme_fr"]:checked')
        .map(function () {
          return $(this).val();
        })
        .get();

      if (checked.includes("Autre")) {
        $("#OtherHealthTheme").removeClass("d-none");
      } else {
        resetContainerFields("OtherHealthTheme");
      }
    });

    $(document).on("change", 'input[name^="stdyDscr/stdyInfo/subject/topcClas[]/value/health theme_en"]', function () {
      let checked = $('input[name^="stdyDscr/stdyInfo/subject/topcClas[]/value/health theme_en"]:checked')
        .map(function () {
          return $(this).val();
        })
        .get();

      if (checked.includes("Other")) {
        $("#OtherHealthTheme").removeClass("d-none");
      } else {
        resetContainerFields("OtherHealthTheme");
      }
    });

    $(document).on(
      "change",
      'input[name="additional/dataCollectionIntegration/isDataIntegration_fr"], input[name="additional/dataCollectionIntegration/isDataIntegration_en"]',
      function () {
        const val = $(this).val();

        if ((val === "Oui" || val === "Yes") && $(this).is(":checked")) {
          $("#ThirdPartySourceBloc").removeClass("d-none");
        } else {
          resetContainerFields("ThirdPartySourceBloc");
        }
      }
    );

    $(document).on(
      "change",
      'select[name="additional/intervention/interventionType_fr"], select[name="additional/intervention/interventionType_en"]',
      function () {
        let $select = $(this);
        let $repeaterItem = $select.closest(".repeater-item"); // scope to this block
        let $otherBloc = $repeaterItem.find(".intervention-type-other");

        if ($select.val() === "Autre" || $select.val() === "Other") {
          $otherBloc.removeClass("d-none");
        } else {
          $otherBloc.addClass("d-none");
        }
      }
    );

    $(document).on("change", 'select[name="additional/arms/armsType_fr"], select[name="additional/arms/armsType_en"]', function () {
      let $select = $(this);
      let $repeaterItem = $select.closest(".repeater-item"); // scope to this block
      let $otherBloc = $repeaterItem.find(".arm-type-other");

      if ($select.val() === "Autre" || $select.val() === "Other") {
        $otherBloc.removeClass("d-none");
      } else {
        $otherBloc.addClass("d-none");
      }
    });
  });
}

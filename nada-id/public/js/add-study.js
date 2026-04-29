const autoSaveIntervalMs = nada_global_vars.auto_save_interval * 1000; // en millisecondes
let formDirty = false;
let isTyping = false;
let isSaving = false;
let counter = 0; // nombre de sauvegarde

jQuery(document).ready(function ($) {
  // Marquer le formulaire comme modifié
  $("#form-add-study").on("input change", "input, textarea, select", function () {
    formDirty = true;
  });

  // Détecter que l’utilisateur est en train d'écrire (debounce)
  const typingDebounce = debounce(() => {
    isTyping = false;
  }, 1000);

  $("#form-add-study").on("input", "input, textarea", function () {
    isTyping = true;
    typingDebounce(); // isTyping devient false 1s après la dernière frappe
  });

  setInterval(function () {
    counter++;
    if (isSaving) return;
    if (!formDirty) return;
    if (isTyping) return;
    if (counter == 1) return;

    //  Minimum le titre doit etre remplir pour déclencher l'autosave
    const title = $('textarea[name="stdyDscr/citation/titlStmt/titl_fr"]').val();
    if (!title || title.trim() === "") return;

    formDirty = false;

    saveStudy($, "", "", "", true);
  }, autoSaveIntervalMs);

  $(document).on(
    "input",
    'input[name$="/lastname_fr"], \
   input[name$="/lastname_en"], \
   textarea[name$="/lastname_fr"], \
   textarea[name$="/lastname_en"]',
    function () {
      const pos = this.selectionStart;
      this.value = this.value.toUpperCase();
      this.setSelectionRange(pos, pos);
    }
  );

  $(".email-study-btn-nada").on("click", function (e) {
    e.preventDefault();

    let email = $(this).attr("data-bs-value");
    $("#emailStudyModal .modal-body.study-title").text(email);
    $("#emailStudyModal").modal("show");
  });

  $(".cancel-show-modal-email").on("click", function () {
    $("#emailStudyModal").modal("hide");
  });

  function openCimModal(lng) {
    $("input[name='langueSearchCIM']").val(lng);

    if (lng === "en") {
      $("#detailCIMLabel").text("Pathologies, conditions or diagnoses targeted by the study");
      $(".modalCIM .lang-label").text("Search");
      $("input[name='searchCIM']").attr("placeholder", "Enter at least 3 characters");
      $("#listPathologies").html(`
      <div class="text-center my-4">
        <span class="dashicons dashicons-info" style="font-size:32px; color:#555;"></span>
        <p class="mt-2 mb-0">Start the search by entering one or more keywords.</p>
      </div>
    `);
      $("#detailCIM .cancel-show-modal-cim").text("Close");
      $("#detailCIM .save-show-modal-cim").text("Validate");
    } else {
      $("#detailCIMLabel").text("Pathologies, affections ou diagnostics ciblés par l’étude");
      $(".modalCIM .lang-label").text("Rechercher");
      $("input[name='searchCIM']").attr("placeholder", "Entrez un minimum de 3 caractères");
      $("#listPathologies").html(`
      <div class="text-center my-4">
        <span class="dashicons dashicons-info" style="font-size:32px; color:#555;"></span>
        <p class="mt-2 mb-0">Lancer la recherche en saisissant un ou plusieurs mot-clés.</p>
      </div>
    `);
      $("#detailCIM .cancel-show-modal-cim").text("Fermer");
      $("#detailCIM .save-show-modal-cim").text("Valider");
    }

    $("#detailCIM").modal("show");
  }

  $(
    "select[name='stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11_fr[]'], select[name='stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11_en[]']"
  ).on("select2:open", function (e) {
    const $select = $(this);

    e.preventDefault();

    // Vérifie si le select contient uniquement des options vides
    const hasOnlyEmptyOptions =
      $select.find("option").filter(function () {
        return $(this).val() !== ""; // garde seulement les options qui ont une vraie valeur
      }).length === 0;

    if (hasOnlyEmptyOptions) {
      // Ferme immédiatement le dropdown Select2
      $select.select2("close");

      // Ouvrir modal
      const name = $select.attr("name") || "";
      const lng = name.indexOf("_en") !== -1 ? "en" : "fr";

      openCimModal(lng);
    }
  });

  $(document)
    .off("click", ".openModalCim")
    .on("click", ".openModalCim", function (e) {
      e.preventDefault();

      const lng = $(this).attr("attr-lng");

      // Ouvrir modal
      openCimModal(lng);
    });

  $(".cancel-show-modal-cim").on("click", function () {
    $("#detailCIM").modal("hide");
  });
});

function debounce(fn, delay) {
  let timer;
  return function (...args) {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, args), delay);
  };
}

let lastFormData = null;
let lastSubmitter = null;
let lastBtnText = null;
let lastSpinner = null;
let enableTranslation = false;
jQuery(".cancel-show-modal-study").on("click", function () {
  jQuery("#translationConfirmModal").modal("hide");

  lastSubmitter.disabled = false;
  let spinner = lastSubmitter.querySelector(".spinner-border");
  if (spinner) spinner.classList.add("d-none");
  const lang = jQuery(".nav-item.langue .nav-link.active").data("lang");

  // Change le texte
  let btnText = lastSubmitter.querySelector(".btn-text");
  if (btnText) btnText.textContent = lang === "fr" ? "Terminer" : "Finish";
});

jQuery("#declineTranslationBtn").on("click", function () {
  enableTranslation = false;
  if (!lastFormData) return;

  lastFormData.append("is_submit", 1);
  lastFormData.append("status_key", jQuery('input[name="status-key"]').val());
  lastFormData.append("enable_translation", enableTranslation);

  sendAjax(lastFormData, lastSubmitter, lastBtnText, lastSpinner);

  lastFormData = null;
});

jQuery("#confirmTranslationBtn").on("click", function () {
  enableTranslation = true;
  if (!lastFormData) return;

  lastFormData.append("is_submit", 1);
  lastFormData.append("status_key", jQuery('input[name="status-key"]').val());
  lastFormData.append("enable_translation", enableTranslation);

  sendAjax(lastFormData, lastSubmitter, lastBtnText, lastSpinner);

  lastFormData = null;
});

jQuery("#translationConfirmModal").on("hidden.bs.modal", function () {
  if (!lastSubmitter) return;

  lastSubmitter.disabled = false;
  let spinner = lastSubmitter.querySelector(".spinner-border");
  if (spinner) spinner.classList.add("d-none");

  const btnText = lastSubmitter.querySelector(".btn-text");
  const lang = jQuery(".nav-item.langue .nav-link.active").data("lang");
  if (btnText) {
    btnText.textContent = lang === "fr" ? "Terminer" : "Finish";
  }
});

/*** FIN change mltlgue */

jQuery(document).ready(function ($) {
  let currentStep = 1;
  const totalSteps = 4;

  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const finishBtn = document.getElementById("finishBtn");
  const steps = [...document.querySelectorAll(".tab-pane")];
  const tabs = [...document.querySelectorAll("#stepperTabs .nav-link")];
  //init step if is daraft
  const savedDraftStep = localStorage.getItem("draft_step");

  const stepFromURL = getStepFromURL();

  if (savedDraftStep) {
    currentStep = Number.parseInt(savedDraftStep);

    const url = new URL(globalThis.location);
    url.searchParams.set("step", currentStep);
    history.replaceState(null, document.title, url);
  } else {
    currentStep = stepFromURL;
  }

  // delete draft_step when doc has fully loaded
  setTimeout(() => {
    localStorage.removeItem("draft_step");
  }, 0);

  history.scrollRestoration = "manual";

  // init step from url
  function getStepFromURL() {
    const url = new URL(globalThis.location);
    const step = Number.parseInt(url.searchParams.get("step"));

    return step && step >= 1 && step <= totalSteps ? step : 1;
  }

  // step
  function showStep(step, updateUrl = true) {
    currentStep = step;

    steps.forEach((s, i) => {
      s.classList.toggle("show", i === step - 1);
      s.classList.toggle("active", i === step - 1);
    });

    tabs.forEach((t, i) => {
      t.classList.toggle("active", i === step - 1);
    });

    if (updateUrl) {
      const url = new URL(globalThis.location);
      url.searchParams.set("step", step);
      history.replaceState(null, document.title, url);
    }

    prevBtn.disabled = step === 1;
    nextBtn.classList.toggle("d-none", step === totalSteps);
    finishBtn.classList.toggle("d-none", step !== totalSteps);

    document.activeElement?.blur();
    window.scrollTo(0, 0);
  }

  //click tab
  tabs.forEach((tab, i) => {
    tab.addEventListener("click", (e) => {
      e.preventDefault();
      showStep(i + 1);
    });
  });

  // next btn
  nextBtn.addEventListener("click", () => {
    if (currentStep < totalSteps) {
      showStep(currentStep + 1);
    }
  });

  // prev btn
  prevBtn.addEventListener("click", () => {
    if (currentStep > 1) {
      showStep(currentStep - 1);
    }
  });

  // init first step
  showStep(currentStep, false);

  function validateEmailField($field) {
    const value = $field.val().trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let $error = $field.next(".invalid-feedback.email-error");

    if (value === "") {
      $field.removeClass("is-invalid is-valid");
      $error.remove();
      return true;
    }

    if (!emailRegex.test(value)) {
      $field.removeClass("is-valid").addClass("is-invalid");
      if (!$error.length) {
        $field.after('<div class="invalid-feedback email-error">exemple@domaine.com</div>');
      }
      return false;
    } else {
      $field.removeClass("is-invalid").addClass("is-valid");
      $error.remove();
      return true;
    }
  }

  $(document).on("input", 'input[data-validator="email"]', function () {
    validateEmailField($(this));
  });

  jQuery("select[ddiShema='stdyDscr/stdyInfo/sumDscr/nation']").select2({
    placeholder: "Rechercher un pays...",
    allowClear: true,
    width: "100%"
  });

  function toggleSubmitLoading(button, loading) {
    if (!button || button.tagName !== "BUTTON") return;

    button.disabled = loading;

    const spinner = button.querySelector(".spinner-border");
    const btnText = button.querySelector(".btn-text");
    const lang = jQuery(".nav-item.langue .nav-link.active").data("lang");

    if (spinner) spinner.classList.toggle("d-none", !loading);

    if (btnText) {
      btnText.textContent = loading
        ? lang === "fr"
          ? "Chargement..."
          : "Loading..."
        : button.id === "finishBtn"
          ? lang === "fr"
            ? "Terminer"
            : "Finish"
          : lang === "fr"
            ? "Enregistrer brouillon"
            : "Save draft";
    }
  }

  function goToInvalidStep($field, submitter) {
    if (!$field) return;

    const $step = $field.closest(".tab-pane");
    const stepId = $step.attr("id");

    if (stepId) {
      $(`#stepperTabs button[data-bs-target="#${stepId}"]`).tab("show");
    }

    $("html, body").animate({ scrollTop: $field.offset().top - 200 }, 400);
    $field.focus();

    toggleSubmitLoading(submitter, false);
  }

  // Soumission du formulaire "Ajouter une étude"
  $(document)
    .off("submit", "#form-add-study")
    .on("submit", "#form-add-study", function (e) {
      e.preventDefault();

      const submitter = e.originalEvent.submitter;
      if (!submitter || !["finishBtn", "saveAsDraft"].includes(submitter.id)) return;

      if (submitter?.id === "saveAsDraft") {
        localStorage.setItem("draft_step", currentStep);
      }

      toggleSubmitLoading(submitter, true);

      // validation persannalisée
      if (submitter.id === "finishBtn") {
        const steps = $(".tab-pane");
        let firstInvalidStep = null;
        let firstInvalidField = null;

        $(".is-invalid").removeClass("is-invalid");

        // validation email
        let emailValid = true;
        let firstInvalidEmailField = null;

        $('input[data-validator="email"]').each(function () {
          const $field = $(this);
          if ($field.closest(".d-none, [hidden]").length > 0) return;

          const valid = validateEmailField($field);
          if (!valid) {
            emailValid = false;
            if (!firstInvalidEmailField) firstInvalidEmailField = $field;
          }
        });

        if (!emailValid) {
          goToInvalidStep(firstInvalidEmailField, submitter);
          return;
        }

        steps.each(function () {
          const $step = $(this);
          const $requiredFields = $step.find("[required]");
          let stepValid = true;

          $requiredFields.each(function () {
            const $field = $(this);
            let value = "";

            if ($field.closest(".d-none, [hidden]").length > 0) return;

            // SELECT2
            if ($field.hasClass("select2-hidden-accessible")) {
              value = $.trim($field.val());
              const $select2 = $field.next(".select2").find(".select2-selection");

              if (!value) {
                stepValid = false;
                $select2.addClass("is-invalid");
                if (!firstInvalidField) firstInvalidField = $field;
              } else {
                $select2.removeClass("is-invalid");
              }
              return;
            }

            // CHECKBOX / RADIO
            if ($field.is(":checkbox") || $field.is(":radio")) {
              value = $step.find(`[name="${$field.attr("name")}"]:checked`).length;

              if (!value) {
                stepValid = false;

                if ($field.hasClass("btn-check")) {
                  $field.closest(".lang-input").find("label.btn").addClass("is-invalid");
                } else {
                  $field.addClass("is-invalid");
                }

                if (!firstInvalidField) firstInvalidField = $field;
              } else {
                if ($field.hasClass("btn-check")) {
                  $field.closest(".lang-input").find("label.btn").removeClass("is-invalid");
                } else {
                  $field.removeClass("is-invalid");
                }
              }
              return;
            }

            // DATE
            if ($field.is('[type="date"]')) {
              value = $field.val();
              const $errorDate = $field.siblings(".error-date");

              if (!value) {
                stepValid = false;
                $field.addClass("is-invalid");
                if (!firstInvalidField) firstInvalidField = $field;
              } else {
                $field.removeClass("is-invalid");
                if ($errorDate.length) $errorDate.hide();
              }
              return;
            }

            // TEXTE
            value = $.trim($field.val());
            if (!value) {
              stepValid = false;
              $field.addClass("is-invalid");
              if (!firstInvalidField) firstInvalidField = $field;
            } else {
              $field.removeClass("is-invalid");
            }
          });

          if (!stepValid && !firstInvalidStep) firstInvalidStep = $step;
        });

        if (firstInvalidStep) {
          goToInvalidStep(firstInvalidField, submitter);

          return;
        }
      }
      // end validation persannalisée

      const btnText = submitter.querySelector(".btn-text");
      const spinner = submitter.querySelector(".spinner-border");

      saveStudy($, submitter, btnText, spinner, false);
    });

  $(document).on("change", '[name^="creatorIsPi"]', function () {
    const val = $(this).val();
    if ((val === "No" || val === "Non") && $(this).is(":checked")) {
      $("#creatorIsPiEmail").removeClass("d-none");
    } else {
      $("#creatorIsPiEmail").addClass("d-none");
    }
  });
});

jQuery(function ($) {
  // validation dynamique du champs type d'identifiant
  jQuery(".controlChampsIdentifinat").each(function () {
    const $container = jQuery(this);
    const $selectFR = $container.find("select[name$='_fr']");
    const $selectEN = $container.find("select[name$='_en']");
    const $inputFR = $container.find(".lang-input  input[name$='_fr']");
    const $inputEN = $container.find(".lang-input  input[name$='_en']");

    if (!$selectFR.length || !$selectEN.length || !$inputFR.length || !$inputEN.length) return;

    function applyValidation($input, selectedType, lang) {
      const type = (selectedType || "").toUpperCase();

      const value = $input.val().trim();

      // Pas de saisie → rien
      if (value === "") {
        $input.removeClass("is-invalid is-valid");
        $input.next(".invalid-feedback.org-id-error").remove();
        return;
      }

      let regex = null;
      let msg = "";

      switch (type) {
        case "SIREN":
          regex = /^\d{9}$/;
          msg = lang === "fr" ? "Le SIREN doit contenir exactement 9 chiffres." : "The SIREN must contain exactly 9 digits.";
          break;

        case "RNSR":
          regex = /^[0-9]{4}[A-Za-z0-9]{5}[A-Z]$/;
          msg =
            lang === "fr"
              ? "10 caractères dont les 4 premières sont obligatoirement des chiffres et le dernier une lettre majuscule"
              : "10 characters where the first 4 must be digits and the last an uppercase letter.";
          break;

        case "DOI":
          regex = /^10\.\d{4,9}\/[-._;()/:A-Z0-9]+$/i;
          msg = lang === "fr" ? "DOI invalide. Exemple : 10.5281/zenodo.1234567" : "Invalid DOI. Example: 10.5281/zenodo.1234567";
          break;

        case "HANDLE":
          regex = /^\d+(\.\d+)*\/.+$/;
          msg = lang === "fr" ? "Handle invalide. Exemple : 20.500.12345/ABC" : "Invalid Handle. Example: 20.500.12345/ABC";
          break;

        default:
          $input.removeClass("is-invalid is-valid");
          $input.next(".invalid-feedback.org-id-error").remove();
          return;
      }

      toggleValidation($input, regex.test(value), msg);
    }

    // Initialisation
    applyValidation($inputFR, $selectFR.val(), "fr");
    applyValidation($inputEN, $selectEN.val(), "en");

    $selectFR.on("change", function () {
      setTimeout(() => {
        applyValidation($inputFR, $selectFR.val(), "fr");
      }, 300);
    });

    $inputFR.on("input", function () {
      applyValidation($inputFR, $selectFR.val(), "fr");
    });

    $selectEN.on("change", function () {
      setTimeout(() => {
        applyValidation($inputEN, $selectEN.val(), "en");
      }, 300);
    });

    $inputEN.on("input", function () {
      applyValidation($inputEN, $selectEN.val(), "en");
    });
  });

  // IDREF
  const idrefRegex = /^\d{8}.$/;
  $(document).on("input blur", 'input[name*="IdRef"]', function () {
    const $input = $(this);
    const value = $input.val().trim();
    toggleValidation(
      $input,
      idrefRegex.test(value),
      lang === "fr" ? "Contenir 9 caractères dont les 8 premiers sont des chiffres." : "Contain 9 characters, where the first 8 are digits."
    );
  });

  //input RNSR
  const rnsrRegex = /^[0-9]{4}[A-Za-z0-9]{5}[A-Z]$/;
  $(document).on("input blur", 'input[name*="RNSR"]', function () {
    const $input = $(this);
    const value = $input.val().trim();

    toggleValidation(
      $input,
      rnsrRegex.test(value),
      lang === "fr"
        ? "10 caractères dont les 4 premières sont obligatoirement des chiffres et le dernier une lettre majuscule"
        : "10 characters where the first 4 must be digits and the last an uppercase letter."
    );
  });

  function toggleValidation($input, isValid, msg) {
    const $error = $input.siblings(".invalid-feedback.org-id-error");

    if (!isValid) {
      $input.removeClass("is-valid").addClass("is-invalid");

      if (!$error.length) {
        $input.after(`<div class="invalid-feedback org-id-error">${msg}</div>`);
      } else {
        $error.text(msg);
      }
    } else {
      $input.removeClass("is-invalid").addClass("is-valid");
      $error.remove();
    }
  }

  const $selFr = $('select[name="stdyDscr/stdyInfo/sumDscr/anlyUnitFake_fr"]');
  const $selEn = $('select[name="stdyDscr/stdyInfo/sumDscr/anlyUnitFake_en"]');

  [$selFr, $selEn].forEach(($sel) => {
    if ($sel.length && (!$sel.val() || $sel.val() === "")) {
      $sel.val("Individus").trigger("change");
    }
    $sel.prop("disabled", true);
  });
});

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".tags-input-container").forEach((container) => {
    const tagName = container.dataset.tags;
    const input = container.querySelector(`.tags-input[data-tags="${tagName}"]`);
    const wrapper = container.querySelector(`.tags-wrapper[data-tags="${tagName}"]`);
    const hidden = container.querySelector(`.tags-hidden[data-tags="${tagName}"]`);

    let tags = [];

    function updateHidden() {
      hidden.value = tags.join(";");
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

    // Mode édition — vider le wrapper et reconstruire les tags depuis le hidden
    wrapper.innerHTML = ""; // vide les anciens tags
    if (hidden.value) {
      hidden.value.split(";").forEach((tag) => addTag(tag.trim()));
    }

    input.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === ";") {
        e.preventDefault();
        const tagText = input.value.trim();
        if (tagText) addTag(tagText);
        input.value = "";
      }
    });
  });
});

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
      $el
        .filter(function () {
            return String(jQuery(this).val()) === String(val);
        })
        .prop("checked", true)
    });
    $el.trigger("change")
  }
}

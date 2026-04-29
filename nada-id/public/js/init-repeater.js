jQuery(document).ready(function ($) {
  // change type identifiant -- email
  function enableUrlMode($input) {
    if ($input.closest(".input-group-prefix").length) return;

    $input.attr({ placeholder: "https://ror.org/3yrm5c26" }).addClass("prefixInput ror-input");
  }
  // change type identifiant -- text
  function disableUrlMode($input) {
    const $parent = $input.closest(".input-group-prefix");

    const name = $input.attr("name"); // récupère le name

    if (name.includes("_fr")) {
      $input.attr({
        placeholder: "Identifiant de l’organisation d’affiliation principale"
      });
    } else if (name.includes("_en")) {
      $input.attr({ placeholder: "Main affiliated organisation identifier" });
    }
  }

  $(document).on("change select2:select select2:clear", '.controlChampsIdentifinat select[name*="ExtLink/title"]', function () {
    const $select = $(this);
    const value = $select.val();
    const $container = $select.closest(".controlChampsIdentifinat");
    const $uriInput = $container.find('input[name*="ExtLink/URI"]');

    if (!$uriInput.length) return;

    $uriInput.each(function (index, el) {
      if (value === "ROR") {
        enableUrlMode($(el));
      } else {
        disableUrlMode($(el));
      }
    });
  });

  $('.controlChampsIdentifinat select[name*="ExtLink/title"]').each(function () {
    $(this).trigger("change");
  });

  /* Gestion des tabs de langue */
  function initLangTabs(container = document) {
    const groups = container.querySelectorAll(".input-group");

    groups.forEach((group) => {
      const inputs = group.querySelectorAll(".lang-input");
      const labels = group.querySelectorAll(".lang-label");
      const tabs = group.querySelectorAll(".nav-link");
      const statusTag = group.querySelector(".badge");

      tabs.forEach((tab) => {
        tab.addEventListener("click", (e) => {
          e.preventDefault();
          tabs.forEach((t) => t.classList.remove("active"));
          tab.classList.add("active");

          const lang = tab.getAttribute("data-lang");

          inputs.forEach((input) => (input.getAttribute("attr-lng") === lang ? input.classList.remove("d-none") : input.classList.add("d-none")));

          labels.forEach((label) => (label.getAttribute("attr-lng") === lang ? label.classList.remove("d-none") : label.classList.add("d-none")));
        });
      });

      const updateStatus = () => {
        const allFilled = Array.from(inputs).every((i) => {
          if (!i) return false;
          i.value.trim() !== "";
        });
        if (statusTag) {
          statusTag.textContent = allFilled ? "Completed" : "Incomplet";
          statusTag.classList.remove("bg-success", "bg-warning");
          statusTag.classList.add(allFilled ? "bg-success" : "bg-warning");
        }
      };
    });
  }

  /* fonction type identifiant */
  function attachOrgIdValidation(items) {
    jQuery(items)
      .find(".controlChampsIdentifinat")
      .each(function () {
        const $container = jQuery(this);
        const $selectFR = $container.find("select[name$='_fr']");
        const $selectEN = $container.find("select[name$='_en']");
        const $inputFR = $container.find(".lang-input input[name$='_fr']");
        const $inputEN = $container.find(".lang-input input[name$='_en']");

        if (!$selectFR.length || !$selectEN.length || !$inputFR.length || !$inputEN.length) return;

        function applyValidation($input, selectedType, lang) {
          const type = (selectedType || "").toUpperCase();
          const value = $input.val().trim();

          if (!value) {
            $input.removeClass("is-invalid is-valid");
            $input.next(".invalid-feedback.org-id-error").remove();
            return;
          }

          let regex = null,
            msg = "";

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

          const $error = $input.siblings(".invalid-feedback.org-id-error");

          if (!regex.test(value)) {
            $input.removeClass("is-valid").addClass("is-invalid");

            if (!$error.length) {
              $input.after('<div class="invalid-feedback org-id-error">' + msg + "</div>");
            } else {
              $error.text(msg);
            }
          } else {
            $input.removeClass("is-invalid").addClass("is-valid");
            $error.remove();
          }
        }

        // Init
        applyValidation($inputFR, $selectFR.val(), "fr");
        applyValidation($inputEN, $selectEN.val(), "en");

        $selectFR.off(".orgid").on("change.orgid", function () {
          applyValidation($inputFR, $selectFR.val(), "fr");
        });

        $inputFR.off(".orgid").on("input.orgid", function () {
          applyValidation($inputFR, $selectFR.val(), "fr");
        });

        $selectEN.off(".orgid").on("change.orgid", function () {
          applyValidation($inputEN, $selectEN.val(), "en");
        });

        $inputEN.off(".orgid").on("input.orgid", function () {
          applyValidation($inputEN, $selectEN.val(), "en");
        });
      });
  }

  /** Fonction repeater **/
  function initRepeater(repeaterId, addBtnId) {
    const repeater = document.getElementById(repeaterId);
    const addBtn = document.getElementById(addBtnId);
    if (!repeater || !addBtn) return;

    function attachRemoveHandler(btn) {
      btn.addEventListener("click", function () {
        const item = btn.closest(".repeater-item");

        if (item && repeater.querySelectorAll(".repeater-item").length > 1) {
          item.remove();
        } else {
          const firstBtn = item.querySelector(".btn-remove");
          if (firstBtn) firstBtn.style.display = "none";

          item.style.display = "none";
          item.setAttribute("data-reapeter-hidden", "first");
          item.setAttribute("data-hidden-model", "true");
        }

        // --> after removing/hiding, update title visibility
        // updateTitleVisibility();
      });
    }

    function attachHiddenHandler(btn, firstRepeater) {
      btn.addEventListener("click", function () {
        if (firstRepeater) {
          const firstBtn = firstRepeater.querySelector(".btn-remove");
          if (firstBtn) firstBtn.style.display = "none";

          firstRepeater.style.display = "none";
          firstRepeater.setAttribute("data-reapeter-hidden", "first");
          firstRepeater.setAttribute("data-hidden-model", "true");

          const parent = $(firstRepeater).parent().attr("id");
          resetContainerFields(parent);

          // $(parent).find('input[type="text"], input[type="number"], input[type="email"], textarea')
          //     .val('');

          // $(parent).find('input[type="radio"], input[type="checkbox"]')
          //     .prop('checked', false)
          //     .removeClass('active');

          // $(parent).find('select').prop('selectedIndex', 0).trigger('change');
          // $(parent).find('select').val('').trigger('change');
        }
      });
    }
    // Normalize string for accent-insensitive comparison
    function normalizeString(str) {
      return str
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
    }

    function initAutocomplete(input, script) {
      if ($(input).data("autocomplete-initialized")) return;

      $(input).data("autocomplete-initialized", true);

      if (script && script.classList.contains("dataList")) {
        const dataList = JSON.parse(script.textContent);

        $(input)
          .autocomplete({
            source: function (request, response) {
              const term = normalizeString(request.term || "");
              response(dataList.filter((item) => normalizeString(item.label).includes(term)));
            },
            minLength: 0,
            select: function (event, ui) {
              const $currentInput = $(this);
              const $repeaterItem = $currentInput.closest(".repeater-item");
              const item = ui.item;
              const currentName = $currentInput.attr("name") || "";
              let basePath = currentName.replace(/_(fr|en)$/i, "");
              basePath = basePath.replace(/\/(affiliation|ExtLink\/.*)$/i, "");
              const jsonInputName = basePath + "/institution_json";

              let finalJsonArray = [];
              if (item.siren && item.siren.trim() !== "") {
                finalJsonArray.push({
                  title: "SIREN",
                  uri: item.siren,
                  role: ""
                });
              }
              if (item.uri && item.uri.trim() !== "") {
                finalJsonArray.push({ title: "ROR", uri: item.uri, role: "" });
              }

              let $hiddenInput = $repeaterItem.find('input[name="' + jsonInputName + '"]');
              if (!$hiddenInput.length) {
                $hiddenInput = $('<input type="hidden" class="institution-json-data" name="' + jsonInputName + '">');
                $repeaterItem.append($hiddenInput);
              }
              $hiddenInput.val(JSON.stringify(finalJsonArray));
              if (currentName.includes("_fr")) {
                $repeaterItem.find('input[name="' + currentName.replace("_fr", "_en") + '"]').val(item.label);
              } else if (currentName.includes("_en")) {
                $repeaterItem.find('input[name="' + currentName.replace("_en", "_fr") + '"]').val(item.label);
              }

              $repeaterItem
                .find('[name^="additional/sponsor/sponsorType_"], [name^="additional/fundingAgent/fundingAgentType_"]')
                .val(item.status || "")
                .trigger("change");
            }
          })
          .data("ui-autocomplete")._resizeMenu = function () {
          this.menu.element.outerWidth(this.element.outerWidth());
        };

        $(input).on("focus click", function () {
          $(this).autocomplete("search", $(this).val());
        });

        $(input).on("click", function () {
          $(this).autocomplete("widget").css("visibility", "visible");
        });

        $(input)
          .off("input")
          .on("input", function () {
            const $currentInput = $(this);
            const $repeaterItem = $currentInput.closest(".repeater-item");

            const currentName = $currentInput.attr("name") || "";

            if (currentName.includes("_fr")) {
              // vider l champs
              $repeaterItem.find('[name*="/ExtLink/title_"]').val("").trigger("change");
              $repeaterItem.find('[name*="/ExtLink/URI_"]').val("").trigger("change");
              $repeaterItem
                .find('[name^="additional/sponsor/sponsorType_"], [name^="additional/fundingAgent/fundingAgentType_"]')
                .val("")
                .trigger("change");
            }
          });
      }
    }

    const firstItem = repeater.querySelector(".repeater-item");
    if (firstItem) {
      const firstBtn = firstItem.querySelector(".btn-remove");
      if (firstBtn) firstBtn.style.display = "none";

      const isHiddenModel = firstItem.hasAttribute("data-reapeter-hidden") || firstItem.getAttribute("data-reapeter-hidden") === "first";

      if (isHiddenModel) {
        firstItem.style.display = "none";
        firstItem.setAttribute("data-hidden-model", "true");
      }

      firstItem.querySelectorAll("input.autocomplete").forEach((input) => {
        const wrapper = input.closest(".lang-input");
        const jsonTag = wrapper.querySelector(".dataList");
        initAutocomplete($(input), jsonTag);
      });
    }

    repeater.querySelectorAll(".btn-remove").forEach((btn) => attachRemoveHandler(btn));

    addBtn.addEventListener("click", function () {
      const hiddenModel = repeater.querySelector('.repeater-item[data-reapeter-hidden="first"], .repeater-item[data-hidden-model="true"]');
      if (hiddenModel && hiddenModel.style.display === "none") {
        hiddenModel.style.display = "";
        hiddenModel.removeAttribute("data-reapeter-hidden");
        hiddenModel.removeAttribute("data-hidden-model");

        // afficher le bouton supprimer
        const btnRemove = hiddenModel.querySelector(".btn-remove");
        if (btnRemove) {
          btnRemove.style.display = "inline-flex";
          attachHiddenHandler(btnRemove, hiddenModel);
        }
        // --> show title when first item becomes visible
        // updateTitleVisibility();
        return;
      }

      const model = repeater.querySelector('.repeater-item[data-reapeter-hidden="first"]') || repeater.querySelector(".repeater-item");
      if (!model) return;

      const clone = model.cloneNode(true);
      clone.removeAttribute("data-reapeter-hidden");
      clone.style.display = "";
      clone.dataset.clone = "true";
      clone.querySelectorAll(".same-data").forEach((el) => {
        el.classList.remove("same-data");
      });

      clone.querySelectorAll(".is-valid, .is-invalid").forEach((el) => {
        el.classList.remove("is-valid", "is-invalid");
      });

      // Supprimer les spans Select2
      clone.querySelectorAll("span.select2").forEach((span) => span.remove());

      // Réinitialiser selects
      $(clone)
        .find("select")
        .each(function () {
          $(this).val("").select2();
        });

      jQuery("#form-add-study select").select2();

      let repeaterCounter = 0;

      // Réinitialiser les champs
      clone.querySelectorAll("input").forEach((input, index) => {
        if (input.type === "radio" || input.type === "checkbox") {
          input.checked = false;

          if (input.id && input.id.trim() !== "") {
            const newId = input.id + "_clone_" + Date.now() + "_" + index;

            const oldId = input.id;
            input.id = newId;

            const label = clone.querySelector(`label[for="${oldId}"]`);
            if (label) {
              label.setAttribute("for", newId);
            }
          }
        } else {
          input.value = "";
        }

        if (input.classList.contains("autocomplete")) {
          const script = input.nextElementSibling;
          initAutocomplete(input, script);
        }
      });

      // Réinitialiser uniquement les groupes de boutons "radio"
      clone.querySelectorAll(".blockBtnRadio.lang-input").forEach((block) => {
        // vider le hidden lié à ce groupe
        block.querySelectorAll('input[type="hidden"]').forEach((input) => {
          input.value = "";
        });

        // désactiver les boutons visuels
        block.querySelectorAll(".BtnRadio").forEach((btn) => {
          btn.classList.remove("active", "btn-primary");
          btn.classList.add("btn-outline-primary");
        });
      });

      clone.querySelectorAll("textarea").forEach((textarea) => {
        textarea.value = "";
      });

      // Masquer les champs "Autre, précision" dans le clone
      clone
        .querySelectorAll(
          '[data-item="FundingAgent"], [data-item="armOtherBloc"], [data-item="otherAuthorizingAgency"],[data-item="OtherSourceType"], [data-item="ThirdPartySource"], [data-item="SchemapidValue"], [data-item="InterventionTypeOtherBloc"],[data-item="Sponsor"]'
        )
        .forEach((el) => {
          el.classList.add("d-none");
          const input = el.querySelector("input");
          if (input) input.value = "";
        });

      const fields = clone.querySelectorAll("input[maxlength], textarea[maxlength]");
      fields.forEach((field) => {
        if (field._charCounterHandler) {
          field.removeEventListener("input", field._charCounterHandler);
        }
        const handler = () => updateCharCounter($(field));
        field._charCounterHandler = handler;
        field.addEventListener("input", handler);
        handler();
      });

      // --- Réinitialiser les repeaters enfants ---
      clone.querySelectorAll(".repeaterBlock").forEach((childRepeater) => {
        const childItems = childRepeater.querySelectorAll(".repeater-item");
        childItems.forEach((item, index) => {
          if (index > 0) {
            item.remove();
          } else {
            item.querySelectorAll("input").forEach((input) => (input.value = ""));
            $(item)
              .find("select")
              .each(function () {
                $(this).val("").select2();
              });
            const btnRemove = item.querySelector(".btn-remove");
            if (btnRemove) btnRemove.style.display = "none";
          }
        });
      });
      clone.querySelectorAll(".institution-json-data").forEach((input) => (input.value = ""));
      // Bouton supprimer
      const btn = clone.querySelector(".btn-remove");
      if (btn) {
        btn.style.display = "inline-flex";
        attachRemoveHandler(btn);
      }

      repeater.appendChild(clone);

      // --> after adding a clone, ensure title is visible
      // updateTitleVisibility();

      // Ré-initialiser tabs uniquement pour ce clone
      initLangTabs(clone);

      // --- REATTACH ORG ID VALIDATION ---
      attachOrgIdValidation(clone);

      // --- REATTACH ORCID VALIDATION ---
      $(clone)
        .find('input[data-validator="orcid"]')
        .removeClass("is-invalid is-valid")
        .val("")
        .off("input")
        .on("input", function () {
          const $field = $(this);
          let value = $field.val().replace(/[^0-9Xx]/g, "");
          let formatted = "";
          for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0 && i < 16) formatted += "-";
            formatted += value[i];
          }
          formatted = formatted.substring(0, 19);
          $field.val(formatted);

          const orcidRegex = /^\d{4}-\d{4}-\d{4}-\d{3}[\dXx]$/;
          const $wrapper = $field.closest(".lang-input");
          let $error = $wrapper.find(".invalid-feedback.orcid-error");
          if (formatted === "") {
            $field.removeClass("is-invalid is-valid");
            $error.remove();
            return;
          }

          if (!orcidRegex.test(formatted)) {
            $field.removeClass("is-valid").addClass("is-invalid");
            // On ajoute l'erreur SEULEMENT si elle n'existe pas déjà
            if ($error.length === 0) {
              $wrapper.append(
                $("<div/>", {
                  class: "invalid-feedback orcid-error",
                  text: "0000-0002-1825-0097 // 0000-0002-1825-009X"
                })
              );
            }
          } else {
            $field.removeClass("is-invalid").addClass("is-valid");
            $error.remove();
          }
        });
      $(clone).find(".invalid-feedback.orcid-error").remove();
    });
  }

  function updateCharCounter($field) {
    const $wrapper = $field.closest(".input-group");
    const $counter = $wrapper.find(".char-counter");
    if (!$counter.length) return;

    const max = parseInt($counter.data("max"), 10);
    const len = $field.val().length;
    const remaining = max - len;

    const $remainingSpan = $counter.find(".remaining");
    $remainingSpan.text(remaining);

    if (remaining < 0) {
      $remainingSpan.css("color", "#fe5442");
    } else if (remaining < max * 0.1) {
      $remainingSpan.css("color", "#fe5442");
    } else {
      $remainingSpan.css("color", "#005eb8");
    }
  }

  initLangTabs();

  // --- Initialisation du premier bloc existant  ---
  jQuery(".repeaterBlock").each(function () {
    const $block = jQuery(this);
    const $firstItem = $block.find(".repeater-item").first();
    if (!$firstItem.length) return;

    $firstItem.off("input.charCounter").on("input.charCounter", "input[maxlength], textarea[maxlength]", function () {
      updateCharCounter(jQuery(this));
    });

    $firstItem.find("input[maxlength], textarea[maxlength]").each(function () {
      updateCharCounter(jQuery(this));
    });
  });

  // Appel repeater
  initRepeater("repeater-standards", "add-standard");
  initRepeater("repeater-fundingAgent", "add-fundingAgent");
  initRepeater("repeater-arms", "add-arm");
  initRepeater("repeater-RelatedDocument", "add-RelatedDocument");
  initRepeater("repeater-interventional-inclusion-group", "add-interventional-inclusion-group");
  initRepeater("repeater-observational-inclusion-group", "add-observational-inclusion-group");
  initRepeater("repeater-teamMember", "add-TeamMember");

  initRepeater("repeater-intervExpo", "add-intervExpo");
  initRepeater("repeater-intervExpo2", "add-intervExpo2");

  initRepeater("repeater-inclusionGroups", "add-inclusionGroups");
  initRepeater("repeater-inclusionGroups2", "add-inclusionGroups2");

  initRepeater("repeater-sources", "add-sources");
  initRepeater("repeater-PrimaryInvestigator", "add-PrimaryInvestigator");
  initRepeater("repeater-PersonPIDContributor", "add-PersonPIDContributor");
  initRepeater("repeater-ContactPoint", "add-ContactPoint");
  initRepeater("repeater-Contributor", "add-Contributor");
  initRepeater("repeater-DataQuality", "add-DataQuality");
  initRepeater("repeater-DataInformationContact", "add-DataInformationContact");
  initRepeater("repeater-DatasetPID", "add-DatasetPID");
  initRepeater("repeater-ObtainedAuthorization", "add-ObtainedAuthorization");
  initRepeater("repeater-OtherStudyId", "add-OtherStudyId");
  initRepeater("repeater-sponsor", "add-sponsor");

  attachOrgIdValidation(document);
});

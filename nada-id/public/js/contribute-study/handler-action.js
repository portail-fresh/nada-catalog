/**
 * Extrait les identifiants d'organisation et les injecte dans l'input hidden JSON
 */
function updateHiddenJson($item, extlinks, hiddenName) {
  if (!extlinks || !Array.isArray(extlinks)) return;

  const orgData = extlinks
    .filter((link) => link.title === "ROR" || link.title === "SIREN")
    .map((link) => ({
      title: link.title,
      uri: link.uri,
      role: link.role || ""
    }));

  if (orgData.length > 0) {
    let $hiddenInput = $item.find(`input[name="${hiddenName}"]`);
    if (!$hiddenInput.length) {
      $hiddenInput = jQuery('<input type="hidden" class="institution-json-data">').attr("name", hiddenName);
      $item.append($hiddenInput);
    }
    $hiddenInput.val(JSON.stringify(orgData)).change();
  }
}
export function fillItem($item, idx, data, additional, lang) {
  if (!data[idx]) return;

  $item
    .find(`[name="stdyDscr/studyAuthorization/authorizingAgency_${lang}"]`)
    .val(data[idx]["name"] || "")
    .trigger("change");

  $item
    .find(`[name="additional/obtainedAuthorization/otherAuthorizingAgency_${lang}"]`)
    .val(additional[idx] || "")
    .trigger("change");
}

// Helper: check if an object has at least one non-empty field
export function hasData(obj) {
  return (
    obj &&
    Object.values(obj).some((v) => {
      if (typeof v === "object") return hasData(v);
      return String(v ?? "").trim() !== "";
    })
  );
}

export function autoRepeatClickObtainedAuthorization(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // On prend la langue avec le plus grand nombre de blocs pour calculer n
  const n = Math.max(...Object.values(allData).map((lang) => lang.data.length)) - 1;

  let i = 0;
  const interval = setInterval(() => {
    if (i < n) {
      $btn.trigger("click");

      const $last = jQuery("#repeater-ObtainedAuthorization .repeater-item").last();

      // remplir tous les champs pour toutes les langues
      Object.keys(allData).forEach((lang) => {
        const data = allData[lang].data;
        const add = allData[lang].additional;
        fillItem($last, i + 1, data, add, lang);
      });

      i++;
    } else {
      clearInterval(interval);

      const $first = jQuery("#repeater-ObtainedAuthorization .repeater-item").first();

      Object.keys(allData).forEach((lang) => {
        fillItem($first, 0, allData[lang].data, allData[lang].additional, lang);
      });

      if (typeof callback === "function") callback();
    }
  }, 300);
}

export function autoRepeatClickPrimaryInvestigator(selector, n, primaryInvestigatorData) {
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
        const { investigators = [] } = primaryInvestigatorData[lang];

        investigators.forEach((inv, idx) => {
          let $item = jQuery("#repeater-PrimaryInvestigator .repeater-item").eq(idx);
          if (!$item.length) return;
          const rawName = inv.name || "";
          const firstPart = rawName.split(";")[0]?.trim() || "";
          const lastPart = rawName.split(";")[1]?.trim() || "";
          const firstname = inv.firstname && inv.firstname.trim() !== "" ? inv.firstname : firstPart;
          const lastname = inv.lastname && inv.lastname.trim() !== "" ? inv.lastname : lastPart;
          // Investigator
          $item.find(`[name="stdyDscr/citation/rspStmt/AuthEnty/firstname_${lang}"]`).val(firstname).change();
          $item.find(`[name="stdyDscr/citation/rspStmt/AuthEnty/lastname_${lang}"]`).val(lastname).change();
          $item.find(`[name="additional/primaryInvestigator/piMail_${lang}"]`).val(inv.email).change();
          $item.find(`[name="stdyDscr/citation/rspStmt/AuthEnty/affiliation_${lang}"]`).val(inv.affiliationName).change();

          $item.find(`[name="additional/primaryInvestigator/piAffiliation/piLabo_${lang}"]`).val(inv.PILabo).change();

          // btn radio
          const isContact = inv.isContact === "" ? "" : inv.isContact ? (lang === "fr" ? "Oui" : "Yes") : lang === "fr" ? "Non" : "No";
          $item.find(`[name="additional/primaryInvestigator/isPIContact_${lang}"]`).val(isContact).change();
          $item.find(`.BtnRadio[attr-value="${isContact}"]`).addClass("active btn-primary").removeClass("btn-outline-primary");

          if (inv.extlink) {
            let orgsForJson = [];

            inv.extlink.forEach((extLink) => {
              switch (extLink.title) {
                case "ORCID":
                  $item.find(`[name="stdyDscr/citation/rspStmt/AuthEnty/ExtLink/ORCID_${lang}"]`).val(extLink.uri).change();
                  break;
                case "IdRef":
                  $item.find(`[name="stdyDscr/citation/rspStmt/AuthEnty/ExtLink/IdRef_${lang}"]`).val(extLink.uri).change();
                  break;
                case "SIREN":
                case "ROR":
                  if (extLink.role === "organisation id") {
                    orgsForJson.push({
                      title: extLink.title,
                      uri: extLink.uri,
                      role: ""
                    });
                  }
                  break;
                case "RNSR":
                  if (extLink.role === "labo id") {
                    $item.find(`[name="stdyDscr/citation/rspStmt/AuthEnty/ExtLink/RNSR_${lang}"]`).val(extLink.uri).change();
                  }
                  break;
              }
            });

            if (orgsForJson.length > 0) {
              const jsonName = "stdyDscr/citation/rspStmt/AuthEnty/institution_json";
              let $hInput = $item.find(`input[name="${jsonName}"]`);
              if (!$hInput.length) {
                $hInput = jQuery(`<input type="hidden" class="institution-json-data" name="${jsonName}">`);
                $item.append($hInput);
              }
              $hInput.val(JSON.stringify(orgsForJson)).change();
            }
          }
        });
      });
    }
  }, 3000);
}

// auto-clicks to create team member blocks and fill them
export function autoRepeatClickContributor(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Filter valid languages and prepare data
  const validLangsData = Object.entries(allData)
    .filter(([_, data]) => Array.isArray(data) && data.length > 0)
    .map(([lang, data]) => ({
      lang,
      contributors: data.filter((item) => item.type === "contributor" && (item.name || item.isContact || item.extlink[0].uri || item.extlink[1].uri)),
      affiliations: data.filter(
        (item) =>
          item.type === "affiliation" && (item.name || item.teamMemberLabo || item.extlink[0].title || item.extlink[0].uri || item.extlink[1].uri)
      )
    }));

  if (validLangsData.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Calculate total blocks needed
  const totalBlocks = Math.max(validLangsData[0].contributors.length, validLangsData[0].affiliations.length);

  // Handle single block case - fill existing first item
  if (totalBlocks === 1) {
    const $firstItem = jQuery("#repeater-teamMember .repeater-item").first();
    validLangsData.forEach(({ lang, contributors, affiliations }) => {
      fillContributorBlock($firstItem, contributors[0], affiliations[0], lang);
    });
    if (typeof callback === "function") callback();
    return;
  }

  // Handle multiple blocks - create additional items
  let currentBlock = 1;
  const interval = setInterval(() => {
    if (currentBlock < totalBlocks) {
      $btn.trigger("click");
      const $lastItem = jQuery("#repeater-teamMember .repeater-item").last();
      validLangsData.forEach(({ lang, contributors, affiliations }) => {
        fillContributorBlock($lastItem, contributors[currentBlock], affiliations[currentBlock], lang);
      });

      currentBlock++;
    } else {
      clearInterval(interval);
      const $firstItem = jQuery("#repeater-teamMember .repeater-item").first();
      validLangsData.forEach(({ lang, contributors, affiliations }) => {
        fillContributorBlock($firstItem, contributors[0], affiliations[0], lang);
      });

      if (typeof callback === "function") callback();
    }
  }, 300);
}
// Fill all contributor fields
export function fillContributorBlock($block, contributor, affiliation, lang) {
  if (!contributor && !affiliation) return;

  // ---- Contributor ----
  if (contributor) {
    const rawName = contributor.name || "";
    const firstPart = rawName.split(";")[0]?.trim() || "";
    const lastPart = rawName.split(";")[1]?.trim() || "";

    const firstname = contributor.firstname && contributor.firstname.trim() !== "" ? contributor.firstname : firstPart;

    const lastname = contributor.lastname && contributor.lastname.trim() !== "" ? contributor.lastname : lastPart;

    $block.find(`[name="stdyDscr/citation/rspStmt/othId/type_contributor_${lang}"]`).val(firstname).change();

    $block.find(`[name="stdyDscr/citation/rspStmt/othId/type_contributor/lastname_${lang}"]`).val(lastname).change();

    // ORCID
    $block
      .find(`[name="stdyDscr/citation/rspStmt/othId/ExtLink/ORCID_${lang}"]`)
      .val(contributor.extlink[0]?.uri || "")
      .change();

    // IdRef
    $block
      .find(`[name="stdyDscr/citation/rspStmt/othId/ExtLink/IdRef_${lang}"]`)
      .val(contributor.extlink[1]?.uri || "")
      .change();

    // Contact point radio
    const isTeamMemberContact =
      contributor.isContact === "" ? "" : contributor.isContact ? (lang === "fr" ? "Oui" : "Yes") : lang === "fr" ? "Non" : "No";
    $block.find(`[name="additional/TeamMember/IsTeamMemberContact_${lang}"]`).val(isTeamMemberContact).change();
    $block.find(`.BtnRadio[attr-value="${isTeamMemberContact}"]`).addClass("active btn-primary").removeClass("btn-outline-primary");
  }

  // ---- Affiliation (organization) ----
  if (affiliation) {
    $block
      .find(`[name="stdyDscr/citation/rspStmt/othId/affiliation_${lang}"]`)
      .val(affiliation.name || "")
      .change();
    $block
      .find(`[name="additional/TeamMember/TeamMemberAffiliation/TeamMemberLabo_${lang}"]`)
      .val(affiliation.teamMemberLabo || "")
      .change();

    if (affiliation.extlink) {
      updateHiddenJson($block, affiliation.extlink, "stdyDscr/citation/rspStmt/othId/institution_json");

      affiliation.extlink.forEach((extLink) => {
        if (extLink.role === "labo id") {
          $block.find(`[name="stdyDscr/citation/rspStmt/othId/ExtLink/RNSR_${lang}"]`).val(extLink.uri).change();
        }
      });
    }
  }
}

export function autoRepeatClickContactPoint(selector, contactData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Validation data
  const validLangs = Object.keys(contactData).filter((lang) => contactData[lang] && Array.isArray(contactData[lang]) && contactData[lang].length > 0);

  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Fonction utilitaire pour vérifier si l’objet contient des données significatives
  function hasData(item) {
    if (!item) return false;
    if (item.type === "contact") {
      return !!(
        (item.name && item.name.trim()) ||
        (item.firstname && item.firstname.trim()) ||
        (item.lastname && item.lastname.trim()) ||
        (item.email && item.email.trim()) ||
        (item.affiliationName && item.affiliationName.trim()) ||
        (item.contactPointLabo && item.contactPointLabo.trim())
      );
    }
    return false;
  }

  // Calculer le nombre maximal de blocs nécessaires (utiliser la première langue valide pour déterminer la structure)
  const firstLang = validLangs[0];
  const firstData = contactData[firstLang];
  const contacts = firstData.filter((item) => item.type === "contact" && (item.name || item.email || item.affiliationName || item.contactPointLabo));
  const totalBlocks = contacts.length;

  // Créer des blocs supplémentaires si nécessaire
  if (totalBlocks > 1) {
    let i = 1;
    const interval = setInterval(() => {
      if (i < totalBlocks) {
        $btn.trigger("click");

        const $lastItem = jQuery("#repeater-ContactPoint .repeater-item").last();

        // Remplir pour toutes les langues
        validLangs.forEach((lang) => {
          const data = contactData[lang];
          const langContacts = data.filter((item) => item.type === "contact" && hasData(item));
          fillContactBlock($lastItem, langContacts[i], lang);
        });

        i++;
      } else {
        clearInterval(interval);

        // Remplir le premier bloc après la création de tous les blocs
        const $firstItem = jQuery("#repeater-ContactPoint .repeater-item").first();

        validLangs.forEach((lang) => {
          const data = contactData[lang];
          const langContacts = data.filter((item) => item.type === "contact" && hasData(item));
          fillContactBlock($firstItem, langContacts[0], lang);
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
        (item) => item.type === "contact" && (item.name || item.email || item.affiliationName || item.contactPointLabo)
      );
      fillContactBlock($firstItem, langContacts[0], lang);
    });

    if (typeof callback === "function") callback();
  }
}

export function fillContactBlock($block, contact, lang) {
  if (contact) {
    const rawName = contact.name || "";
    const firstPart = rawName.split(";")[0]?.trim() || "";
    const lastPart = rawName.split(";")[1]?.trim() || "";

    const firstname = contact.firstname && contact.firstname.trim() !== "" ? contact.firstname : firstPart;

    const lastname = contact.lastname && contact.lastname.trim() !== "" ? contact.lastname : lastPart;

    $block.find(`input[name="stdyDscr/citation/distStmt/contact_${lang}"]`).val(firstname).change();
    $block.find(`input[name="stdyDscr/citation/distStmt/contact/lastname_${lang}"]`).val(lastname).change();

    $block
      .find(`[name="stdyDscr/citation/distStmt/contact/email"]`)
      .val(contact.email || "")
      .change();
    $block
      .find(`[name="stdyDscr/citation/distStmt/contact/affiliation_${lang}"]`)
      .val(contact.affiliationName || "")
      .change();
    $block
      .find(`[name="additional/contactPointLabo_${lang}"]`)
      .val(contact.contactPointLabo || "")
      .change();
    if (contact.extlink) {
      updateHiddenJson($block, contact.extlink, "stdyDscr/citation/distStmt/contact/institution_json");

      contact.extlink.forEach((extLink) => {
        if (extLink.role === "labo id") {
          $block.find(`[name="stdyDscr/citation/distStmt/contact/ExtLink/RNSR_${lang}"]`).val(extLink.uri).change();
        }
      });
    }
  }
}

export async function autoRepeatClickFundingAgent(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  const validLangs = Object.keys(allData || {}).filter(
    (lang) => allData[lang]?.data && Array.isArray(allData[lang].data) && allData[lang].data.length > 0
  );

  if (!validLangs.length) {
    callback?.();
    return;
  }

  const n = Math.max(...validLangs.map((lang) => allData[lang].data.length)) - 1;

  for (let i = 0; i < n; i++) {
    $btn.trigger("click");

    await wait(100);

    const $lastItem = jQuery("#repeater-fundingAgent .repeater-item").last();

    validLangs.forEach((lang) => {
      const data = allData[lang]?.data ?? [];
      const additional = allData[lang]?.additional ?? [];

      if (data[i + 1]) {
        $lastItem.find(`[name="stdyDscr/citation/prodStmt/fundAg_${lang}"]`).val(data[i + 1]?.name || "");

        $lastItem.find(`select[name="additional/fundingAgent/fundingAgentType_${lang}"]`).val(additional[i + 1] || "");

        updateHiddenJson($lastItem, data[i + 1]?.extlink, "stdyDscr/citation/prodStmt/fundAg/institution_json");
      }
    });

    validLangs.forEach((lang) => {
      $lastItem.find(`select[name="additional/fundingAgent/fundingAgentType_${lang}"]`).trigger("change");
    });

    await wait(50);

    validLangs.forEach((lang) => {
      let value = allData[lang]?.additionalOFAT?.[i + 1];

      if (!value && lang !== "fr") {
        value = allData["fr"]?.additionalOFAT?.[i + 1];
      }

      if (value) {
        $lastItem.find(`[name="additional/fundingAgent/otherFundingAgentType_${lang}"]`).val(value.trim());
      }
    });
  }

  const $firstItem = jQuery("#repeater-fundingAgent .repeater-item").first();

  validLangs.forEach((lang) => {
    const data = allData[lang]?.data?.[0];
    const additional = allData[lang]?.additional?.[0];

    if (!data) return;

    if (data?.extlink) {
      updateHiddenJson($firstItem, data.extlink, "stdyDscr/citation/prodStmt/fundAg/institution_json");
    }

    $firstItem.find(`[name="stdyDscr/citation/prodStmt/fundAg_${lang}"]`).val(data?.name || "");

    $firstItem
      .find(`select[name="additional/fundingAgent/fundingAgentType_${lang}"]`)
      .val(additional || "")
      .trigger("change");
  });

  await wait(50);

  validLangs.forEach((lang) => {
    const value = allData[lang]?.additionalOFAT?.[0];

    if (value) {
      $firstItem.find(`[name="additional/fundingAgent/otherFundingAgentType_${lang}"]`).val(value);
    }
  });

  callback?.();
}

function wait(ms = 50) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

export async function autoRepeatClickSponsor(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  const validLangs = Object.keys(allData).filter((lang) => allData[lang]?.data && Array.isArray(allData[lang].data) && allData[lang].data.length > 0);

  if (!validLangs.length) {
    callback?.();
    return;
  }

  const n = Math.max(...validLangs.map((lang) => allData[lang].data.length)) - 1;

  for (let i = 0; i < n; i++) {
    $btn.trigger("click");

    await wait(100);

    const $lastItem = jQuery("#repeater-sponsor .repeater-item").last();

    validLangs.forEach((lang) => {
      const data = allData[lang]?.data ?? [];
      const data_additional = allData[lang]?.additional ?? [];

      if (data[i + 1]) {
        $lastItem.find(`[name="stdyDscr/citation/prodStmt/producer_${lang}"]`).val(data[i + 1]?.name || "");

        $lastItem.find(`select[name="additional/sponsor/sponsorType_${lang}"]`).val(data_additional[i + 1] || "");

        updateHiddenJson($lastItem, data[i + 1].extlink, "stdyDscr/citation/prodStmt/producer/institution_json");
      }
    });

    validLangs.forEach((lang) => {
      $lastItem.find(`select[name="additional/sponsor/sponsorType_${lang}"]`).trigger("change");
    });

    await wait(50);

    validLangs.forEach((lang) => {
      const additionalOST = allData[lang]?.additionalOST ?? [];

      let value = additionalOST[i + 1];

      if (!value && lang !== "fr") {
        value = allData["fr"]?.additionalOST?.[i + 1];
      }

      if (value) {
        $lastItem.find(`[name="additional/sponsor/otherSponsorType_${lang}"]`).val(value.trim());
      }
    });
  }

  const $firstItem = jQuery("#repeater-sponsor .repeater-item").first();

  validLangs.forEach((lang) => {
    const data = allData[lang]?.data?.[0];
    const additional = allData[lang]?.additional?.[0];

    if (!data) return;

    if (data?.extlink) {
      updateHiddenJson($firstItem, data.extlink, "stdyDscr/citation/prodStmt/producer/institution_json");
    }

    $firstItem.find(`[name="stdyDscr/citation/prodStmt/producer_${lang}"]`).val(data?.name || "");

    $firstItem
      .find(`select[name="additional/sponsor/sponsorType_${lang}"]`)
      .val(additional || "")
      .trigger("change");
  });

  await wait(50);

  validLangs.forEach((lang) => {
    const additionalOST = allData[lang]?.additionalOST?.[0];

    if (additionalOST) {
      $firstItem.find(`[name="additional/sponsor/otherSponsorType_${lang}"]`).val(additionalOST);
    }
  });

  callback?.();
}

// ----- Interventional exposures (repeater-intervExpo) -----
export function autoRepeatClickInerExpo(selectorAddBtn, allData, callback) {
  const $btn = jQuery(selectorAddBtn);
  if (!$btn.length) return;

  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  const validLangs = Object.keys(allData).filter((lang) => Array.isArray(allData[lang]) && allData[lang].length > 0);
  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  const total = Math.max(...validLangs.map((lang) => allData[lang].length));
  const $repeater = jQuery("#repeater-intervExpo");
  const existing = $repeater.find(".repeater-item").length;
  const needed = Math.max(0, total - existing);

  for (let i = 0; i < needed; i++) $btn.trigger("click");

  const $items = $repeater.find(".repeater-item");

  for (let i = 0; i < total; i++) {
    const $item = $items.eq(i);

    validLangs.forEach((lang) => {
      const data = allData[lang];
      if (!data || !data[i]) return;
      const row = data[i];

      const hasData = ["name", "type", "typeOther", "description"].some((k) => String(row[k] || "").trim() !== "");
      if (!hasData) return;

      // fill inputs
      $item.find(`[name="additional/intervention/interventionName_${lang}"]`).val(row.name || "");
      $item.find(`select[name="additional/intervention/interventionType_${lang}"]`).val(row.type || "");
      const $typeOther = $item.find(`[name="additional/intervention/interventionTypeOther_${lang}"]`);
      const typeVal = (row.type || "").toLowerCase().trim();
      if (typeVal === "autre" || typeVal === "other") {
        $typeOther.val(row.typeOther || "");
      } else {
        $typeOther.val("");
      }

      //trigger once per item
      $item.find("select, textarea").trigger("change");
    });
  }

  if (typeof callback === "function") callback();
}

// ----- Observational exposures (repeater-intervExpo2) -----
export function autoRepeatClickInerExpo2(selectorAddBtn, allData, callback) {
  const $btn = jQuery(selectorAddBtn);
  if (!$btn.length) return;

  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  const validLangs = Object.keys(allData).filter((lang) => Array.isArray(allData[lang]) && allData[lang].length > 0);
  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  const total = Math.max(...validLangs.map((lang) => allData[lang].length));
  const $repeater = jQuery("#repeater-intervExpo2");
  const existing = $repeater.find(".repeater-item").length;
  const needed = Math.max(0, total - existing);

  for (let i = 0; i < needed; i++) $btn.trigger("click");

  const $items = $repeater.find(".repeater-item");

  for (let i = 0; i < total; i++) {
    const $item = $items.eq(i);

    validLangs.forEach((lang) => {
      const data = allData[lang];
      if (!data || !data[i]) return;
      const row = data[i];

      const hasData = ["name", "type", "typeOther", "description"].some((k) => String(row[k] || "").trim() !== "");
      if (!hasData) return;

      $item.find(`[name="additional/intervention/interventionName_${lang}"]`).val(row.name || "");
      $item.find(`select[name="additional/intervention/interventionType_${lang}"]`).val(row.type || "");

      const $typeOther = $item.find(`[name="additional/intervention/interventionTypeOther_${lang}"]`);
      const typeVal = (row.type || "").toLowerCase().trim();
      if (typeVal === "autre" || typeVal === "other") {
        $typeOther.val(row.typeOther || "");
      } else {
        $typeOther.val("");
      }

      // single trigger per repeater item
      $item.find("select, textarea").trigger("change");
    });
  }

  if (typeof callback === "function") callback();
}

export function autoRepeatClickStandardName(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Vérifier que allData existe et n'est pas vide
  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Filtrer les langues qui ont des données valides
  const validLangs = Object.keys(allData).filter((lang) => allData[lang] && Array.isArray(allData[lang]) && allData[lang].length > 0);

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
            .find(`[name="stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName_${lang}"]`)
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
          $firstItem.find(`[name="stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName_${lang}"]`).val(data[0]).change();
        }
      });

      if (typeof callback === "function") callback();
    }
  }, 300);
}

export function autoRepeatClickOtherQualityStatement(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Vérifier que allData existe et n'est pas vide
  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Filtrer les langues qui ont des données valides
  const validLangs = Object.keys(allData).filter((lang) => allData[lang] && Array.isArray(allData[lang]) && allData[lang].length > 0);

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
            .find(`[name="stdyDscr/stdyInfo/qualityStatement/otherQualityStatement_${lang}"]`)
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
          $firstItem.find(`[name="stdyDscr/stdyInfo/qualityStatement/otherQualityStatement_${lang}"]`).val(data[0]).change();
        }
      });

      if (typeof callback === "function") callback();
    }
  }, 300);
}

export function autoRepeatClickSources(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  if (!allData || Object.keys(allData).length === 0) {
    callback?.();
    return;
  }

  const validLangs = Object.keys(allData).filter((lang) => Array.isArray(allData[lang]) && allData[lang].length > 0);

  if (validLangs.length === 0) {
    callback?.();
    return;
  }

  const totalItems = Math.max(...validLangs.map((lang) => allData[lang].length));

  // FILL FIRST BLOCK
  const $firstItem = jQuery("#repeater-sources .repeater-item").first();

  validLangs.forEach((lang) => fillSourcesItem($firstItem, 0, allData[lang], lang));

  if (totalItems === 1) {
    callback?.();
    return;
  }
  // CREATE + FILL REMAINING BLOCKS SEQUENTIALLY
  let i = 1; // because first block already filled

  function addNextBlock() {
    if (i >= totalItems) {
      callback?.();
      return;
    }
    $btn.trigger("click");
    // Wait until new block exists in DOM
    const check = setInterval(() => {
      const $items = jQuery("#repeater-sources .repeater-item");
      if ($items.length > i) {
        clearInterval(check);
        const $newItem = $items.eq(i);
        validLangs.forEach((lang) => fillSourcesItem($newItem, i, allData[lang], lang));
        i++;
        addNextBlock(); // call next iteration
      }
    }, 50);
  }
  addNextBlock();
}

export function fillSourcesItem($item, idx, data, lang) {
  if (!data || !data[idx]) return;
  const item = data[idx];

  // citation
  $item
    .find(`[name="stdyDscr/method/dataColl/sources/sourceCitation_${lang}"]`)
    .val(item.sourceCitation.titlStmt.titl || "")
    .change();

  // holdings
  $item
    .find(`[name="stdyDscr/method/dataColl/sources/sourceCitation/holdings_${lang}"]`)
    .val(item.sourceCitation.holdings || "")
    .change();

  // purpose select
  $item
    .find(`select[name="stdyDscr/method/dataColl/sources/sourceCitation/notes/subject_sourcePurpose_${lang}"]`)
    .val(item.sourceCitation.notes.value || "")
    .change();

  // checkboxes srcOrig
  if (Array.isArray(item.srcOrig)) {
    setFieldValues(item.srcOrig, `stdyDscr/method/dataColl/sources/srcOrig_${lang}[]`, "checkbox", $item);
  }

  // otherSourceType
  $item.find(`textarea[name="additional/thirdPartySource/otherSourceType_${lang}"]`).val(item.otherSourceType).change();
}

export function autoRepeatClickDocs(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  const validLangs = Object.keys(allData).filter((lang) => allData[lang] && Array.isArray(allData[lang]) && allData[lang].length > 0);

  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }
  const n = Math.max(...validLangs.map((lang) => allData[lang].length)) - 1;
  let i = 0;

  const interval = setInterval(() => {
    if (i == 0) {
      clearInterval(interval);
      const $firstItem = jQuery("#repeater-RelatedDocument .repeater-item").first();
      validLangs.forEach((lang) => {
        fillDocsItem($firstItem, 0, allData[lang], lang);
      });
      if (typeof callback === "function") callback();
    }

    if (i < n) {
      $btn.trigger("click");
      const $lastItem = jQuery("#repeater-RelatedDocument .repeater-item").last();
      validLangs.forEach((lang) => {
        const data = allData[lang];
        fillDocsItem($lastItem, i + 1, data, lang);
      });

      i++;
    }
  }, 0);
}

export function fillDocsItem($item, idx, data, lang) {
  if (!data || !data[idx]) {
    return;
  }
  const item = data[idx];
  const $documentType = $item.find(`select[name="additional/relatedDocument/documentType_${lang}"]`);
  $documentType.val(item.type || "").change();

  const $documentTitle = $item.find(`textarea[name="additional/relatedDocument/documentTitle_${lang}"]`);
  $documentTitle.val(item.title || "").change();
  const $documentLink = $item.find(`input[name="additional/relatedDocument/documentLink_${lang}"]`);
  $documentLink.val(item.link || "").change();
}

export function autoRepeatClickBras(selector, n, data, data_additional) {
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
            .find('select[name="stdyDscr/studyAuthorization/authorizingAgency_fr"]')
            .val(data[i + 1]["name"])
            .change();
          $lastItem
            .find('[name="additional/obtainedAuthorization/otherAuthorizingAgency_fr"]')
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
      .find('[name="additional/obtainedAuthorization/otherAuthorizingAgency_fr"]')
      .val(data_additional[0])
      .change();
  }
}

export function autoRepeatClickInclusionInterventional(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  const validLangs = Object.keys(allData).filter((lang) => allData[lang] && Array.isArray(allData[lang]) && allData[lang].length > 0);

  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Calculate total number of blocks needed
  const total = Math.max(...validLangs.map((lang) => allData[lang].length));

  const $repeater = jQuery("#repeater-interventional-inclusion-group");
  if (!$repeater.length) return;

  const $firstItem = $repeater.find(".repeater-item").first();
  const isModelHidden =
    $firstItem.attr("data-reapeter-hidden") === "first" || $firstItem.attr("data-hidden-model") === "true" || $firstItem.css("display") === "none";

  const fillAllItems = () => {
    const existing = $repeater.find(".repeater-item").length;
    const needed = Math.max(0, total - existing);

    // Créer les blocs manquants
    for (let i = 0; i < needed; i++) {
      $btn.trigger("click");
    }

    const $items = $repeater.find(".repeater-item");
    for (let i = 0; i < total; i++) {
      const $item = $items.eq(i);
      validLangs.forEach((lang) => {
        const data = allData[lang];
        if (!data || !data[i]) return;
        const group = data[i];

        // Ignorer les blocs vides
        if (!group.name && !group.description) {
          return;
        }

        const $name = $item.find(`textarea[name="additional/inclusionGroups/groupName_${lang}"]`);
        if ($name.length) $name.val(group.name || "").change();

        const $desc = $item.find(`textarea[name="additional/inclusionGroups/groupDescription_${lang}"]`);
        if ($desc.length) $desc.val(group.description || "").change();
      });
    }

    if (typeof callback === "function") callback();
  };

  if (isModelHidden) {
    $btn.trigger("click");

    // attendre que le bloc soit rendu avant de remplir
    setTimeout(() => {
      fillAllItems();
    }, 400);
  } else {
    // sinon, remplir directement (modèle 1)
    fillAllItems();
  }
}

export function autoRepeatClickInclusionObservational(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  const validLangs = Object.keys(allData).filter((lang) => allData[lang] && Array.isArray(allData[lang]) && allData[lang].length > 0);

  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Get number of items to create
  const total = Math.max(...validLangs.map((lang) => allData[lang].length));
  const $repeater = jQuery("#repeater-observational-inclusion-group");
  if (!$repeater.length) return;

  const $firstItem = $repeater.find(".repeater-item").first();
  const isModelHidden =
    $firstItem.attr("data-reapeter-hidden") === "first" || $firstItem.attr("data-hidden-model") === "true" || $firstItem.css("display") === "none";
  const fillAllItems = () => {
    const existing = $repeater.find(".repeater-item").length;
    const needed = Math.max(0, total - existing);
    const $currentBtn = jQuery(selector);
    // Create missing items
    for (let i = 0; i < needed; i++) {
      $currentBtn.trigger("click");
    }

    // Fill all items once
    const $items = $repeater.find(".repeater-item");
    for (let i = 0; i < total; i++) {
      const $item = $items.eq(i);
      validLangs.forEach((lang) => {
        const data = allData[lang];
        if (!data || !data[i]) return;
        const group = data[i];

        // Skip if all empty
        if (!group.name && !group.description) {
          return;
        }
        const $name = $item.find(`textarea[name="additional/inclusionGroups/groupName_${lang}"]`);
        if ($name.length) $name.val(group.name || "").change();

        const $desc = $item.find(`textarea[name="additional/inclusionGroups/groupDescription_${lang}"]`);
        if ($desc.length) $desc.val(group.description || "").change();
      });
    }

    if (typeof callback === "function") callback();
  };
  if (isModelHidden) {
    $btn.trigger("click");

    // attendre que le bloc soit rendu avant de remplir
    setTimeout(() => {
      fillAllItems();
    }, 400);
  } else {
    // sinon, remplir directement (modèle 1)
    fillAllItems();
  }
}

export function autoRepeatClickDatasetPID(selector, allData, callback) {
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
        fillDatasetPIDItem($lastItem, i + 1, allData[lang], lang);
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

export function fillDatasetPIDItem($item, idx, data, lang) {
  if (!Array.isArray(data) || !data[idx]) return;
  const item = data[idx];
  $item
    .find(`select[name="additional/fileDscr/fileTxt/fileCitation/titlStmt/IDno/agent_${lang}"]`)
    .val(item.type || "")
    .change();

  $item
    .find(`[name="additional/fileDscr/fileTxt/fileCitation/titlStmt/IDno_${lang}"]`)
    .val(item.uri || "")
    .change();
}

export function autoRepeatClickArms(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;
  const $repeater = jQuery("#repeater-arms");
  if (!$repeater.length) return;

  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  const validLangs = Object.keys(allData).filter((lang) => Array.isArray(allData[lang]) && allData[lang].length > 0);

  if (validLangs.length === 0) {
    $repeater.find(".repeater-item").first().hide();
    if (typeof callback === "function") callback();
    return;
  }

  const total = Math.max(...validLangs.map((lang) => allData[lang].length));

  const $firstItem = $repeater.find(".repeater-item").first();
  const isModelHidden =
    $firstItem.attr("data-reapeter-hidden") === "first" || $firstItem.attr("data-hidden-model") === "true" || $firstItem.css("display") === "none";

  const fillAllItems = () => {
    const existing = $repeater.find(".repeater-item").length;
    const needed = Math.max(0, total - existing);

    for (let i = 0; i < needed; i++) {
      $btn.trigger("click");
    }

    const $items = $repeater.find(".repeater-item");
    for (let i = 0; i < total; i++) {
      const $item = $items.eq(i);
      validLangs.forEach((lang) => {
        const data = allData[lang];
        if (!data || !data[i]) return;
        fillArmsItem($item, i, data, lang);
      });
    }

    if (typeof callback === "function") callback();
  };

  if (isModelHidden) {
    $btn.trigger("click");
    setTimeout(() => fillAllItems(), 400);
  } else {
    fillAllItems();
  }
}

export function fillArmsItem($item, idx, data, lang) {
  if (!data || !data[idx]) return;
  const item = data[idx];

  const hasData = ["name", "type", "typeOther", "description"].some((k) => String(item[k] || "").trim() !== "");
  if (!hasData) return;

  // armsName
  const $armsName = $item.find(`textarea[name="additional/arms/armsName_${lang}"]`);
  if ($armsName.length) $armsName.val(item.name || "");

  // armsType
  const $armsType = $item.find(`select[name="additional/arms/armsType_${lang}"]`);
  if ($armsType.length) $armsType.val(item.type || "");

  // armsDescription
  const $desc = $item.find(`textarea[name="additional/arms/armsDescription_${lang}"]`);
  if ($desc.length) $desc.val(item.description || "");

  // armsTypeOther
  const $typeOther = $item.find(`textarea[name="additional/arms/armsTypeOther_${lang}"]`);
  if ($typeOther.length) {
    const typeVal = (item.type || "").toLowerCase().trim();
    if (typeVal === "autre" || typeVal === "other") {
      $typeOther.val(item.typeOther || "");
    } else {
      $typeOther.val("");
    }
  }

  // trigger change only once per item, not per field
  $item.find("select, textarea").trigger("change");
}

// fill block repeater autres identifiants
export function autoRepeatClickIDno(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  const validLangs = Object.keys(allData).filter((lang) => Array.isArray(allData[lang]) && allData[lang].length > 0);
  if (validLangs.length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  const total = Math.max(...validLangs.map((lang) => allData[lang].length));

  const $repeater = jQuery("#repeater-OtherStudyId");
  if (!$repeater.length) return;

  const $firstItem = $repeater.find(".repeater-item").first();
  const isModelHidden =
    $firstItem.attr("data-reapeter-hidden") === "first" || $firstItem.attr("data-hidden-model") === "true" || $firstItem.css("display") === "none";

  const fillAllItems = () => {
    const existing = $repeater.find(".repeater-item").length;
    const needed = Math.max(0, total - existing);

    for (let i = 0; i < needed; i++) {
      $btn.trigger("click");
    }

    const $items = $repeater.find(".repeater-item");
    for (let i = 0; i < total; i++) {
      const $item = $items.eq(i);
      validLangs.forEach((lang) => {
        const data = allData[lang];
        if (!data || !data[i]) return;
        fillOtherStudyIdItem($item, i, data, lang);
      });
    }

    if (typeof callback === "function") callback();
  };

  if (isModelHidden) {
    $btn.trigger("click");
    setTimeout(() => fillAllItems(), 400);
  } else {
    fillAllItems();
  }
}

export function fillOtherStudyIdItem($item, idx, data, lang) {
  if (!data || !data[idx]) return;
  const item = data[idx];

  const hasData = ["agency", "code"].some((k) => String(item[k] || "").trim() !== "");
  if (!hasData) return;

  const $agency = $item.find(`[name="stdyDscr/citation/titlStmt/IDNo/agency_${lang}"]`);
  if ($agency.length) $agency.val(item.agency || "");

  const $code = $item.find(`[name="stdyDscr/citation/titlStmt/IDNo_${lang}"]`);
  if ($code.length) $code.val(item.code || "");

  $item.find("input, select, textarea").trigger("change");
}

// --- Fill repeater: Information Contact ---
export function autoRepeatClickInformationContact(selector, allData, callback) {
  const $btn = jQuery(selector);
  if (!$btn.length) return;

  // Exit early if data is missing
  if (!allData || Object.keys(allData).length === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  // Determine the max number of blocks to create
  const maxLength = Math.max(...Object.values(allData).map((lang) => (Array.isArray(lang) ? lang.length : 0)));

  if (maxLength === 0) {
    if (typeof callback === "function") callback();
    return;
  }

  let i = 0;
  const interval = setInterval(() => {
    const $items = jQuery("#repeater-DataInformationContact .repeater-item");
    const $lastItem = $items.last();

    // Fill data for each language (FR/EN)
    Object.keys(allData).forEach((lang) => {
      const data = allData[lang];
      if (data && data[i]) {
        fillInformationContactItem($lastItem, i, data, lang);
      }
    });

    i++;

    if (i >= maxLength) {
      clearInterval(interval);
      if (typeof callback === "function") callback();
    } else {
      $btn.trigger("click");
    }
  }, 400);
}

export function fillInformationContactItem($item, idx, data, lang) {
  if (!data || !data[idx]) return;
  const item = data[idx];
  const rawName = item.name || "";
  const firstPart = rawName.split(";")[0]?.trim() || "";
  const lastPart = rawName.split(";")[1]?.trim() || "";

  const firstname = item.firstname && item.firstname.trim() !== "" ? item.firstname : firstPart;

  const lastname = item.lastname && item.lastname.trim() !== "" ? item.lastname : lastPart;
  $item.find(`[name="stdyDscr/dataAccs/useStmt/contact_${lang}"]`).val(firstname).change();
  $item.find(`[name="stdyDscr/dataAccs/useStmt/contact/lastname_${lang}"]`).val(lastname).change();
  $item
    .find(`[name="stdyDscr/dataAccs/useStmt/contact/mail"]`)
    .val(item.email || "")
    .change();
}

// ========= main.js - Point d'entrée principal =========//

/**
 * Orchestrer l’application
 * Importer tous les modules
 */

// Imports des modules
import { cim11 } from "./cim11.js";
import { helpersJs } from "./helpers.js";
import { initJs } from "./init.js";
import { multilangJs } from "./multilang.js";

import { submitJs } from "./submit.js";
import { validationForm } from "./validation.js";

import { contributeStudyConfig } from "./config.js";
import { formHandler } from "./form-handler.js";

const { mode, jsonRec, jsonParentStudy, studyDetails, studyId, currentUser } = contributeStudyConfig;
// Point d'entrée principal
jQuery(function ($) {
  initJs();
  multilangJs();
  submitJs();
  helpersJs();
  validationForm();
  cim11();

  setTimeout(() => {
    if (mode === "edit") {
      formHandler(jsonRec, jsonParentStudy, studyDetails, currentUser);
    }
  }, 0);
});

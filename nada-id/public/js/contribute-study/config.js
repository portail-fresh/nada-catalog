// =========== config.js - Variables globales et constantes ======= //
/**
 * Stocker les valeurs fixes (lang, temps de frappe, version, etc.)
 * Stocker l’état global partagé (dernier formulaire, dernier bouton, etc.)
 * Exporter ces valeurs pour les autres modules
 */

//Extraire les donnés
// config.js
const container = document.getElementById("nada_contribute_study_data");

export const contributeStudyConfig = container
  ? JSON.parse(container.dataset.config || "{}")
  : {};

export const mode = contributeStudyConfig.mode;
export const jsonRec = contributeStudyConfig.jsonRec;
export const compareStudy = contributeStudyConfig.compareStudy;
export const jsonParentStudy = contributeStudyConfig.jsonParentStudy;
export const currentUser = contributeStudyConfig.currentUser;


export const lang = nada_global_vars.lang || "fr";
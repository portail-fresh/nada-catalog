/**
 * Ce fichier regroupe l’ensemble du JavaScript global du plugin Nada ID
 */
jQuery(document).ready(function ($) {
  const SESSION_KEY = "studies_context";
  const clearAllFilters = () => {
    localStorage.removeItem("catalog_filters");
    localStorage.removeItem("catalog_advanced_filters");
    localStorage.removeItem("catalog_search_mode");
    localStorage.removeItem("myspace_filters");
    localStorage.removeItem("myspace_advanced_filters");
    localStorage.removeItem("myspace_search_mode");
  };

  // suppression des filtres hors contexte
  if (
    window.CONTEXT === "catalog" ||
    window.CONTEXT === "study" ||
    window.CONTEXT === "studies"
  ) {
    sessionStorage.setItem(SESSION_KEY, "1");
  } else {
    // Hors contexte catalog/study/ list studies  =>  effacer les filtres
    sessionStorage.removeItem(SESSION_KEY);
    clearAllFilters();
    return;
  }

  window.addEventListener("beforeunload", function () {
    // Si on quitte vers une page hors contexte => effacer les filtres
    if (!sessionStorage.getItem(SESSION_KEY)) {
      clearAllFilters();
    }
  });
});

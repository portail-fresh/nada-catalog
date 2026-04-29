// Fonction pour récupérer un paramètre dans l'URL
function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}

// function pour récupérer listes des etudes
function chargerCatalogues(
  filtres = {},
  search = "",
  sortBy = "",
  sortOrder = "desc",
  limit = "",
  lang = ""
) {
  jQuery.ajax({
    url: catalog_vars.ajax_url,
    method: "POST",
    data: {
      action: "nada_load_catalogs",
      filtres: filtres,
      lang: lang,
      search: search,
      sortBy: sortBy,
      sortOrder: sortOrder,
      limit: limit,
      _ajax_nonce: catalog_vars.nonce,
    },
    beforeSend: function () {
      if (lang && lang == "en") {
        jQuery("#nada-resultats").html("<p>Loading...</p>");
      } else {
        jQuery("#nada-resultats").html("<p>Chargement...</p>");
      }
    },
    success: function (res) {
      jQuery("#nada-resultats").html(res.data.html);
    },
    error: function () {
      jQuery("#nada-resultats").html("<p>Erreur lors du chargement.</p>");
    },
  });
}

function buildQueryStringFromFormArray(formArray) {
  const params = new URLSearchParams();
  formArray.forEach(({ name, value }) => {
    params.append(name, value);
  });
  return params.toString();
}

// Fonction pour récupérer les paramètres du formulaire de recherche et tri
function getSearchFormParams() {
  // recuperation du order et sortby Exemple: dans l'option value= value-order
  const displaySort = jQuery("#nada-display-sort").val();
  const [sortBy, sortOrder] = displaySort.split("-");

  return {
    search: jQuery("#nada-search-input").val(),
    sortBy: sortBy,
    sortOrder: sortOrder,
    limit: jQuery("#nada-limit").val(),
    lang: jQuery("input[name='language-radio']:checked").val(),
  };
}

// Function pour modifier language
function updateLanguageUI(selectedLang) {
  // Mettre le correct button checked
  jQuery(`input[name='language-radio'][value='${selectedLang}']`).prop(
    "checked",
    true
  );
}

// Function pour charger  les facets selon language
function chargerFacets(lang = "fr") {
  // initialisation vide emplacement du facete
  jQuery("#facet-en").html("");
  jQuery("#facet-fr").html("");
  // ajax pour recuperer les facete
  jQuery.ajax({
    url: catalog_vars.ajax_url,
    method: "POST",
    data: {
      action: "nada_load_facets",
      lang: lang,
      _ajax_nonce: catalog_vars.nonce,
    },
    beforeSend: function () {
      // Test pour affiche le message selon la language
      if (lang && lang == "en") {
        jQuery("#facet-en").html("<p>Loading facets...</p>");
        jQuery("#facet-fr").html("");
      } else {
        jQuery("#facet-fr").html("<p>Chargement des facettes...</p>");
        jQuery("#facet-en").html("");
      }
    },
    success: function (res) {
      if (res.success) {
        jQuery("#facet-en").html(res.data.html);
        jQuery("#facet-fr").html(res.data.html);
        // Remplace seulement le contenu du bon facet
        // Afficher la bonne div et cacher l'autre
        if (lang === "en") {
          jQuery("#facet-en").removeClass("d-none"); // montre EN
          jQuery("#facet-fr").addClass("d-none"); // cache FR
        } else {
          jQuery("#facet-fr").removeClass("d-none"); // montre FR
          jQuery("#facet-en").addClass("d-none"); // cache EN
        }
      }
    },
    error: function () {
      jQuery("#facet-fr").html(
        "<p>Erreur lors du chargement des facettes.</p>"
      );
    },
  });
}

jQuery(document).ready(function ($) {
  // Initialiser la langue à partir du paramètre URL ou définir par défaut « fr »
  const langParam = getQueryParam("lang") || "fr";
  updateLanguageUI(langParam);

  // Chargement initial
  const searchParam = getQueryParam("search");
  if (searchParam) {
    $("#nada-search-input").val(searchParam);
  }

  // Charger les facettes dynamiquement au démarrage
  chargerFacets(langParam);

  // Charger avec les paramètres par défaut
  const initialParams = getSearchFormParams();
  chargerCatalogues(
    {},
    initialParams.search,
    initialParams.sortBy,
    initialParams.sortOrder,
    initialParams.limit,
    langParam ?? initialParams.lang
  );

  // Gérer les changements de langue des boutons radio
  $(document).on("change", "input[name='language-radio']", function () {
    const selectedLang = $(this).val();
    chargerFacets(selectedLang); // Recharge les facettes dynamiquement

    // Obtenir les paramètres de recherche et de filtrage actuels
    const params = getSearchFormParams();
    const filtreData = $("#nada-filter-form").serializeArray();
    const queryString = buildQueryStringFromFormArray(filtreData);

    // Recharger les catalogues avec une nouvelle langue selectionee
    chargerCatalogues(
      queryString,
      params.search,
      params.sortBy,
      params.sortOrder,
      params.limit,
      selectedLang
    );
  });

  // Gérer les changements dans le formulaire de tri/recherche (auto-submit)
  $("#nada-search-form").on("change", "select", function () {
    const params = getSearchFormParams();
    const filtreData = $("#nada-filter-form").serializeArray();
    const queryString = buildQueryStringFromFormArray(filtreData);
    chargerCatalogues(
      queryString,
      params.search,
      params.sortBy,
      params.sortOrder,
      params.limit,
      params.lang
    );
  });

  // Gérer le formulaire de filtre (auto-submit sur changement)
  $("#nada-filter-form").on("change", "input, select", function () {
    const params = getSearchFormParams();
    const formData = $("#nada-filter-form").serializeArray();
    const queryString = buildQueryStringFromFormArray(formData);
    chargerCatalogues(
      queryString,
      params.search,
      params.sortBy,
      params.sortOrder,
      params.limit,
      params.lang
    );
  });

  // Gérer la soumission du formulaire de recherche (pour la recherche libre)
  $("#nada-search-form").on("submit", function (e) {
    e.preventDefault();
    const params = getSearchFormParams();
    const filtreData = $("#nada-filter-form").serializeArray();
    const queryString = buildQueryStringFromFormArray(filtreData);
    chargerCatalogues(
      queryString,
      params.search,
      params.sortBy,
      params.sortOrder,
      params.limit,
      params.lang
    );
  });
});

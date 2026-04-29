// referentiel.js
jQuery(document).ready(function ($) {
  // Initialize DataTable
  const table = $("#ref-list-admin").DataTable({
    destroy: true,
    language: {
      url:
        nada_global_vars.lang === "fr"
          ? "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
          : "//cdn.datatables.net/plug-ins/1.13.6/i18n/en-EN.json",
    },
  });
});

function resetContainerFields(containerId) {
  const $container = jQuery("#" + containerId);
  const first = $container.find("[data-reapeter-hidden]");
  // Cacher la section
  if (!first.length) {
    $container.addClass("d-none");
  }

  // Vider tous les champs texte, nombre, email et textarea
  $container.find('input[type="text"], input[type="number"], input[type="email"], textarea').val("");

  // Décocher tous les radio et checkbox
  $container.find('input[type="radio"], input[type="checkbox"]').prop("checked", false).removeClass("active"); // pour Bootstrap .btn-check

  // Réinitialiser les listes déroulantes
  $container.find("select").prop("selectedIndex", 0).trigger("change");
  $container.find("select").val("").trigger("change");

  $container.find("input, textarea, select").each(function () {
    const $field = jQuery(this);
    $field.removeAttr("required");
  });
}

function nadaSetFieldValue(name, value, type) {
  jQuery("[name='" + name + "']")
    .val(value)
    .trigger("change");
}

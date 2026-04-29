jQuery(document).ready(function ($) {
  let currentLang = "fr";
  const url = window.location.href;
  if (url.includes("-fr")) currentLang = "fr";
  else if (url.includes("-en")) currentLang = "en";

  function applyLang() {
    $(".nav-item.langue .nav-link").removeClass("active");
    $(`.nav-item.langue .nav-link[data-lang='${currentLang}']`).addClass(
      "active"
    );

    $(".lang-text").each(function () {
      const $el = $(this);
      const raw = $el.data(currentLang);
      $el.text(formatValue(raw, currentLang));
    });

    $(".lang-row").each(function () {
      const $row = $(this);
      $row.find("[data-field]").each(function () {
        const $cell = $(this);
        const field = $cell.data("field"); // ex: "title" ou "uri"
        const value = $row.data(currentLang + "-" + field);
        $cell.text(value || "-");
      });
    });

    // Masquer/Afficher selon la langue
    if (currentLang === "en") {
      $(".details-study-fr").hide();
      $(".details-study-en").show();
    } else {
      $(".details-study-en").hide();
      $(".details-study-fr").show();
    }
  }

  function formatValue(value, lang) {
    if (value === undefined || value === null) return "-";

    const val = String(value).toLowerCase();

    if (val === "true" || val === "1" || val === "yes") {
      return lang === "fr" ? "Oui" : "Yes";
    }
    if (val === "false" || val === "0" || val === "no") {
      return lang === "fr" ? "Non" : "No";
    }

    if (val === "other" || val === "others") {
      return lang === "fr" ? "Autre(s)" : "Other(s)";
    }

    return value;
  }

  applyLang();

  $(document).on("click", ".nav-item.langue .nav-link", function (e) {
    e.preventDefault();
    currentLang = $(this).data("lang");

    $(this).closest(".nav-pills").find(".nav-link").removeClass("active");
    $(this).addClass("active");

    applyLang();
  });

  const links = jQuery(".navStickyBar .nav-item a");
  const sections = jQuery(".submenu-study");

  function setActiveLinkAndContent(hash) {
    links.removeClass("active");
    sections.hide();

    const activeLink = jQuery(`.navStickyBar .nav-item a[href="${hash}"]`);
    const activeSection = jQuery(`.submenu-study[id="${hash.substring(1)}"]`);

    activeLink.addClass("active");
    activeSection.show();
  }

  links.on("click", function (e) {
    e.preventDefault();
    const hash = jQuery(this).attr("href");
    if (!hash) return;

    setActiveLinkAndContent(hash);
  });

  const firstSection = sections.first();
  if (firstSection.length) {
    setActiveLinkAndContent("#" + firstSection.attr("id"));
  }

});

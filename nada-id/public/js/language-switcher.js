// ** Gestion de la langue ** //
jQuery(document).ready(function ($) {
  // Appliquer la langue sélectionnée
  function applyLang(lang, $trigger = null) {
    if (lang === "fr") {
      $("input.lang-input[name^='stdyDscr/method/dataColl/respRate_en']").next(".error-msg").remove();
    } else {
      $("input.lang-input[name^='stdyDscr/method/dataColl/respRate_fr']").next(".error-msg").remove();
    }

    $(".nav-item.langue .nav-link").removeClass("active");
    if ($trigger) $trigger.addClass("active");

    $(".lang-label, .lang-input").addClass("d-none");

    $(".one-input input").each(function () {
      const placeholder = $(this).attr("attr-placeholder-" + lang);
      $(this).attr("placeholder", placeholder);
    });

    $(`.lang-label[attr-lng='${lang}'], .lang-input[attr-lng='${lang}']`).removeClass("d-none");

    $(".lang-text").each(function () {
      const text = $(this).data(lang);
      const icon = $(this).data("icon");

      let html = "";

      if (icon) {
        html += `<i class="${icon}"></i>`;
      }

      if (text) {
        html += `<span class="label-text">${text}</span>`;
      }

      $(this).html(html);
    });

    $(".lang-text-tooltip").each(function () {
      const text = $(this).data(lang);
      const tooltip = $(this)
        .attr("tooltip-" + lang)
        ?.trim();
      const $info = $(this).find(".info-bulle");

      if (text) $(this).find(".contentSection").text(text);

      if ($info.length) {
        tooltip ? $info.attr("data-text", tooltip).show() : $info.hide();
      }
    });

    $("html").attr("lang", lang);
  }

  // Appliquer la langue initiale
  const lang = language_vars?.lang || "fr";
  const $link = $(`.nav-item.langue .nav-link[data-lang='${lang}']`);
  applyLang(lang, $link);

  // Gestion du clic sur les liens de langue
  $(document).on("click", ".nav-item.langue .nav-link", function (e) {
    e.preventDefault();

    const lang = $(this).data("lang");
    applyLang(lang, $(this));
  });
});

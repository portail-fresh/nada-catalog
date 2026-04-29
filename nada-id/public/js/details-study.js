// Contexte de l'application
window.CONTEXT = "study";

jQuery(function ($) {
  function applyLang() {
    $(".nav-item.langue .nav-link").removeClass("active");
    $(`.nav-item.langue .nav-link[data-lang='${lang}']`).addClass("active");

    $(".lang-text").each(function () {
      const $el = $(this);
      const raw = $el.data(lang);
      $el.html(formatValue(raw, lang));
    });

    $(".lang-row").each(function () {
      const $row = $(this);
      $row.find("[data-field]").each(function () {
        const $cell = $(this);
        const field = $cell.data("field"); // ex: "title" ou "uri"
        const value = $row.data(lang + "-" + field);
        $cell.text(value || "-");
      });
    });

    // Masquer/Afficher selon la langue
    if (lang === "en") {
      $(".details-study-fr").hide();
      $(".details-study-en").show();
    } else {
      $(".details-study-en").hide();
      $(".details-study-fr").show();
    }

    // Mise à jour des onglets du tab ---
    $("#stepperTabs .section-tab").each(function () {
      const $tab = $(this);
      const text = $tab.data(lang); // récupère data-en ou data-fr
      if (text) {
        $tab.text(text);
      }
    });
  }

  function formatValue(value, lang) {
    if (value === undefined || value === null) return "-";

    const val = String(value).toLowerCase();

    if (val === "true" || val === "yes") {
      return lang === "fr" ? "Oui" : "Yes";
    }
    if (val === "false" || val === "no") {
      return lang === "fr" ? "Non" : "No";
    }

    if (val === "other" || val === "others") {
      return lang === "fr" ? "Autre(s)" : "Other(s)";
    }

    return value;
  }

  function updateVisibility() {
    // 1) field-bloc
    $(".field-bloc").each(function () {
      const $bloc = $(this);
      const $value = $bloc.find(".field-bloc__value");

      // prioritize data attribute
      const dataVal = ($value.attr("data-" + lang) || "").trim();
      // fallback to visible text
      const textVal = $value.text().trim();
      // detect HTML children (images/links...) that are visible
      const hasVisibleChildren =
        $value.children().filter(function () {
          return (
            $(this).is(":visible") &&
            ($(this).text().trim() !== "" || $(this).is("img,a"))
          );
        }).length > 0;

      const hasContent = dataVal !== "" || textVal !== "" || hasVisibleChildren;

      $bloc.toggle(hasContent);
    });

    // 4) Hide empty <li> inside .authorization-detail
    $(".authorization-detail li").each(function () {
      const $li = $(this);
      const $val = $li.find(".field-bloc__value");

      // value from data attribute
      const dataVal = ($val.attr("data-" + lang) || "").trim();

      // fallback to visible text
      const textVal = $val.text().trim();

      // any visible children?
      const hasVisibleChildren =
        $val.children().filter(function () {
          return (
            $(this).is(":visible") &&
            ($(this).text().trim() !== "" || $(this).is("img,a"))
          );
        }).length > 0;

      const hasContent = dataVal !== "" || textVal !== "" || hasVisibleChildren;

      $li.toggle(hasContent);
    });

    // 2) field-card
    $(".field-card").each(function () {
      const $card = $(this);
      const $body = $card.find(".field-card__body");

      // find any field-bloc inside that has non-empty data-<lang> or non-empty text or visible children
      const hasContent =
        $body.find(".field-bloc").filter(function () {
          const $val = $(this).find(".field-bloc__value");
          const dataVal = ($val.attr("data-" + lang) || "").trim();
          const textVal = $val.text().trim();
          const hasVisibleChildren =
            $val.children().filter(function () {
              return (
                $(this).is(":visible") &&
                ($(this).text().trim() !== "" || $(this).is("img,a"))
              );
            }).length > 0;
          return dataVal !== "" || textVal !== "" || hasVisibleChildren;
        }).length > 0;

      $card.toggle(hasContent);
    });

    // 3) submenu-study
    $(".submenu-study").each(function () {
      const $submenu = $(this);
      const hasContent =
        $submenu.find(".field-bloc").filter(function () {
          const $val = $(this).find(".field-bloc__value");
          const dataVal = ($val.attr("data-" + lang) || "").trim();
          const textVal = $val.text().trim();
          const hasVisibleChildren =
            $val.children().filter(function () {
              return (
                $(this).is(":visible") &&
                ($(this).text().trim() !== "" || $(this).is("img,a"))
              );
            }).length > 0;
          return dataVal !== "" || textVal !== "" || hasVisibleChildren;
        }).length > 0;

      $submenu.toggle(hasContent);
    });

    updateSubmenuVisibility(lang);
    updateStickyNav();
  }

  //upadate stickybar
  function updateStickyNav() {
    $(".navStickyBar li").hide();

    $(".submenu-study:visible").each(function () {
      const id = this.id;
      if (!id) return;

      const $link = $('.navStickyBar a[href="#' + id + '"]');
      if ($link.length) {
        $link.closest("li").show();
      }
    });
  }

  function updateSubmenuVisibility(lang) {
    $(".submenu-study-table").each(function () {
      const $section = $(this);

      const hasContent =
        $section.find(".lang-text").filter(function () {
          const val = ($(this).attr("data-" + lang) || "").trim();
          return val !== "";
        }).length > 0;

      $section.toggle(hasContent);
    });
  }

  // change lang
  $(document).on("click", ".nav-item.langue .nav-link", function (e) {
    e.preventDefault();
    lang = $(this).data("lang");

    $(this).closest(".nav-pills").find(".nav-link").removeClass("active");
    $(this).addClass("active");

    localStorage.setItem("selectedLang", lang); // la stocke dans le localStorage

    applyLang();
    updateVisibility();
  });

  // return to calogue page
  $(document).on("click", ".return-btn", function (e) {
    e.preventDefault();

    const referrer = document.referrer;
    const currentOrigin = window.location.origin;

    if (referrer && referrer.startsWith(currentOrigin)) {
      window.history.back();
      return;
    }

    const routes = {
      fr: "/catalogue/",
      en: "/en/catalog/",
    };

    window.location.href = routes[lang] || routes.fr;
  });

  //hide le bloc qui contient seulment des titres
  jQuery(".submenu-study").each(function () {
    const $submenu = jQuery(this);

    const nonHeadingElements = $submenu.children().not("h2, h3, h4");

    if (nonHeadingElements.length === 0) {
      $submenu.hide();
    }
  });

  jQuery(".leftsidebar .nav-item").each(function () {
    const $li = jQuery(this);
    const $link = $li.find("a");
    const targetId = $link.attr("href");

    if (targetId && targetId.startsWith("#")) {
      const $target = jQuery(targetId);

      // Skip if section doesn't exist
      if ($target.length === 0) {
        $li.hide();
        return;
      }

      // Filter children other than headings or empty elements
      const $contentChildren = $target.children().not("h1,h2,h3,h4,br");

      if ($target.is(":empty") || $contentChildren.length === 0) {
        $li.hide();
      }
    }
  });

  jQuery(".leftsidebar .nav-item a").on("click", function (e) {
    e.preventDefault();

    const targetId = jQuery(this).attr("href");
    const $target = jQuery(targetId);

    if ($target.length) {
      jQuery(".leftsidebar .nav-item").removeClass("active");

      jQuery(this).closest(".nav-item").addClass("active");

      jQuery("html, body").animate(
        {
          scrollTop: $target.offset().top - 180,
        },
        500,
      );
    }
  });

  // toggle field card content
  jQuery(".field-card__body").show();
  jQuery(".field-card__header").addClass("open");

  jQuery(".field-card__header").on("click", function () {
    const $body = jQuery(this).next(".field-card__body");

    $body.slideToggle(200);
    jQuery(this).toggleClass("open");
  });

  // toggle sidebar
  $(document).on("click", "#sidebarToggleBtn", function () {
    $("#sidebarToggleContent").toggleClass("opened");
    $(".leftsidebar.toggle-container").toggleClass("is-open");
  });

  //init
  applyLang();
});

// Window LOAD
jQuery(window).on("load", function () {
  const currentUrl = window.location.href;
  if (currentUrl.includes("/catalogue-detail/")) {
    jQuery("body").addClass("is-catalogue-detail");
  }
});

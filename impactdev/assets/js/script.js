/** Corriger erreur dans console mediaplayer */
jQuery(document).ready(function ($) {
  // Initialise tous les AudioPlayer après que Tooltip a fini son initialisation
  $(".cmtt-audio-shortcode").each(function () {
    if (typeof $(this).mediaelementplayer === "function") {
      $(this).mediaelementplayer({
        audioWidth: "95.5%",
        audioHeight: 30,
        startVolume: 0.8,
        features: ["playpause", "progress", "current", "duration", "tracks", "volume", "fullscreen"],
        pauseOtherPlayers: true
      });
    }
  });
});

/** Corriger erreur double liste dans glossaire */
jQuery(document).ready(function ($) {
  // Attendre un petit délai pour s'assurer que le plugin a créé les éléments
  setTimeout(function () {
    var letters = $("#glossaryList-nav .ln-letters");
    if (letters.length > 1) {
      letters.eq(1).hide(); // cacher le 2ème
    }
  }, 50); // 50ms après le rendu
});

jQuery(document).ready(function ($) {
  var settings = {
    audioWidth: "95.5%",
    audioHeight: 30,
    startVolume: 0.8,
    features: ["playpause", "progress", "current", "duration", "tracks", "volume", "fullscreen"],
    pauseOtherPlayers: true
  };

  $(".glossary-item-audio")
    .find(".cmtt-audio-shortcode")
    .each(function () {
      if (typeof $(this).mediaelementplayer === "function") {
        $(this).mediaelementplayer(settings);
      }
    });
});

jQuery(document).ready(function ($) {
  $(".mdf_button").on("click", function (e) {
    // Sélecteur de l'input
    var $input = $('input[name="mdf[medafi_685483438edd2]"]');

    // Récupérer la valeur encodée (ex: &lt;span class=...&gt;mot&lt;/span&gt;)
    var encodedValue = $input.val();

    // 1. Décoder les entités HTML
    var decoded = $("<textarea/>").html(encodedValue).text();

    // 2. Supprimer les balises HTML (comme <span ...>)
    var cleaned = decoded.replace(/<[^>]*>/g, "");

    // 3. Mettre à jour le champ input avec le texte nettoyé
    $input.val(cleaned);
  });
});

/* liste des langues */
document.addEventListener("DOMContentLoaded", () => {
  // Sélectionne le <li> dans la liste qui a la classe current-lang
  const currentLangItem = document.querySelector("ul.sub-menu li.current-lang");

  if (currentLangItem) {
    // Cache cet élément
    currentLangItem.style.display = "none";
  }
});

function applySelect2Styles() {
  document.querySelectorAll(".frm_form_7 .select2-selection--single").forEach((el) => {
    el.style.borderRadius = "25px";
    el.style.height = "40px";
    el.style.lineHeight = "40px";
    el.style.padding = "0 10px";
    el.style.display = "flex";
    el.style.alignItems = "center";
    el.style.boxSizing = "border-box";
  });
}

/* traduction newsletter */
document.addEventListener("DOMContentLoaded", function () {
  // Vérifier si la langue est anglaise
  const lang = document.documentElement.lang || "";
  if (!lang.startsWith("en")) return;

  // Fonction pour traduire le formulaire newsletter
  function translateNewsletter(newsletter) {
    if (!newsletter) return;

    const descriptions = newsletter.querySelectorAll(".newsletter-description");
    if (descriptions.length >= 2) {
      descriptions[0].textContent = "Newsletter";
      descriptions[1].textContent = "Subscribe to our newsletter to receive the latest news and resources.";
    }

    const emailInput = newsletter.querySelector("input[type='email']");
    if (emailInput) {
      emailInput.placeholder = "Your email address";
    }

    const submitButton = newsletter.querySelector("input[type='submit']");
    if (submitButton) {
      submitButton.value = "→";
    }
  }

  // Traduire le message de succès et masquer le bloc email après soumission
  document.addEventListener("wpcf7mailsent", function (event) {
    const form = event.target;
    const newsletter = form.closest(".newsletter-form"); // Sécurisé

    if (newsletter) {
      // Traduire le message
      const successMessage = newsletter.querySelector(".wpcf7-response-output");
      if (successMessage) {
        successMessage.textContent = "Thank you for subscribing. You will receive our latest news soon.";
      }

      // Masquer le bloc email + bouton
      const formFields = newsletter.querySelector(".form-field-wrapper");
      if (formFields) {
        formFields.style.display = "none";
      }
    }
  });

  // Traduire directement au chargement (au cas où le formulaire est affiché dès le début)
  document.querySelectorAll(".newsletter-form").forEach(translateNewsletter);
});

/* submit  button touche pas lactualité */
/* si categorie vide dans page actualité */
/* les deux boutons filtrage et reset page actualité */
document.addEventListener("DOMContentLoaded", function () {
  const submitBtn = document.querySelector("#mdf_search_form_11052 .mdf_shortcode_submit_button");
  const resetBtn = document.querySelector("#mdf_search_form_11052 .mdf_shortcode_reset_button");
  const resultsContainer = document.querySelector("#mdf_results_by_ajax");

  if (!submitBtn || !resetBtn || !resultsContainer) return;

  // Vérification périodique pour détecter les résultats injectés via AJAX
  const checkResults = setInterval(() => {
    const hasResults = resultsContainer.querySelector(".tm-box-col-wrapper") !== null;

    if (hasResults) {
      submitBtn.style.display = "none";
      resetBtn.style.display = "inline-block";
    } else {
      submitBtn.style.display = "inline-block";
      resetBtn.style.display = "none";
    }
  }, 100); // toutes les 100ms
});

/*Recherche dans glossaire*/
jQuery(document).ready(function ($) {
  $("#glossary-search").on("keyup", function () {
    var filter = $(this).val().toLowerCase();
    var anyVisible = false;

    $(".glossaryList li").each(function () {
      // On ignore la ligne "Nothing found"
      if ($(this).hasClass("ln-no-match")) return;

      var termText = $(this).text().toLowerCase();
      if (termText.indexOf(filter) > -1) {
        $(this).show();
        anyVisible = true;
      } else {
        $(this).hide();
      }
    });

    // Afficher "Nothing found" si aucun terme visible
    if (!anyVisible) {
      $(".glossaryList li.ln-no-match").show();
    } else {
      $(".glossaryList li.ln-no-match").hide();
    }
  });
});

/*Traduire message aucun resultat glossaire*/
jQuery(function ($) {
  $("#glossary-search").on("input", function () {
    // Vérifie si l'élément .ln-no-match existe
    var $noMatch = $(".ln-no-match");
    if ($noMatch.length) {
      $noMatch.each(function () {
        var text = $(this).text().trim();
        if (text === "Nothing found. Please change the filters.") {
          $(this).text("Aucun résultat trouvé.");
        }
      });
    }
  });
});

/*Cocher 1er category dans formulaire proposer une actualité*/
jQuery(document).ready(function ($) {
  $('input[name="categorie-article"]').first().prop("checked", true);
});

/*Scroll vers NL si message*/
jQuery(window).on("load", function () {
  var $messages = jQuery(".wpcf7-response-output:visible, .wpcf7-not-valid-tip:visible, .newsletter-success.wpcf7-response-output");

  if ($messages.length) {
    var targetOffset = $messages.first().offset().top;
    var headerHeight = jQuery("header").outerHeight() || 0;

    jQuery("html, body").animate(
      {
        scrollTop: targetOffset - headerHeight - 140
      },
      500
    );
  }
});

/*Scroll inscription vers message*/
jQuery(document).ready(function ($) {
  // On récupère tous les messages d'inscrit visibles
  var $messages = $(".wpcf7-response-output:visible, .wpcf7-not-valid-tip:visible");

  if ($messages.length) {
    // On prend le premier message visible pour scroller
    var targetOffset = $messages.first().offset().top;

    // Ajuste si tu as un header sticky
    var headerHeight = $("header").outerHeight() || 0;
    $("html, body").scrollTop(targetOffset - headerHeight - 20);
  }
});

/*Ajouter 0 avant date ex:01*/
jQuery(document).ready(function ($) {
  $(".themetechmount-entry-date .entry-date").each(function () {
    // Récupérer le texte complet du <time>
    var $time = $(this);
    var dayText = $time
      .contents()
      .filter(function () {
        return this.nodeType === 3; // texte uniquement
      })
      .first()
      .text()
      .trim();

    // Ajouter un zéro si nécessaire
    if (dayText.length === 1) {
      $time
        .contents()
        .filter(function () {
          return this.nodeType === 3;
        })
        .first()
        .replaceWith("0" + dayText);
    }
  });
});

/*tag categories home page*/

jQuery(document).ready(function ($) {
  // Étape 1 : clic sur une catégorie dans .tm-meta-line.cat-links
  $(document).on("click", ".bloc-actualites .tm-meta-line.cat-links, .single-post .tm-meta-line.cat-links", function () {
    // Récupérer le texte du dernier span (ex: "Article")
    var cat = $(this).find("span:not(.screen-reader-text)").last().text().trim().toLowerCase();

    // Nettoyer en slug (sans accents ni espaces)
    cat = cat.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    cat = cat.replace(/\s+/g, "-").replace(/[^a-z0-9\-]/g, "");

    // Redirection vers /actualites?cat
    window.location.href = "/actualites?" + cat;
  });

  // Étape 2 : quand on arrive sur /actualites?cat → activer le filtre
  var query = window.location.search.substring(1); // récupère tout après "?"
  if (query) {
    var cat = query.toLowerCase();

    var $targetLink = $('.tm-sortable-list a[data-filter=".' + cat + '"]');
    if ($targetLink.length) {
      // Retire sélection précédente
      $(".tm-sortable-list a").removeClass("selected");
      // Ajoute sélection sur le bon
      $targetLink.addClass("selected");
      // Déclenche le clic
      $targetLink.trigger("click");
    }
  }
});

/*Hide lien dans equipe*/
jQuery(document).ready(function ($) {
  $(".themetechmount-icon-box.themetechmount-box-link a").each(function () {
    $(this).replaceWith($(this).contents());
  });
  $(".themetechmount-box-title h4 a").each(function () {
    $(this).replaceWith($(this).contents());
  });
});

/* Ignorer lien vers glossaire pour page glossaire */
jQuery(document).ready(function ($) {
  if ($("ul").hasClass("glossaryList")) {
    $(".glossaryLink").css({
      "pointer-events": "none",
      cursor: "default",
      "border-bottom": "unset"
    });
  }

  $(
    ".themetechmount-box-title h4 .glossaryLink, .themetechmount-box-desc-text .glossaryLink , .NotGlossaire .glossaryLink, .technical-documentation-row .glossaryLink,.bloc-article .wpr-grid-item-title,.bloc-article .wpr-grid-item-excerpt"
  ).css({
    "pointer-events": "none",
    cursor: "default",
    "border-bottom": "unset",
    "font-weight": "initial !important"
  });
});

/*Changer identifiant par email */
jQuery(document).ready(function ($) {
  $('label[for="user_login"]')
    .contents()
    .filter(function () {
      return this.nodeType === 3; // sélectionne uniquement le texte
    })
    .first()
    .replaceWith("Email ");
});
/* traduire dans glossaire tous par all */
jQuery(function ($) {
  setTimeout(function () {
    // Traduction du lien "Tous/All"
    var $allLink = $("a.ln-all");
    if ($allLink.length) {
      var lang = $("html").attr("lang") || "fr";
      $allLink.text(lang.startsWith("en") ? "All" : "Tous");
    }

    // Traduction du message "Aucun résultat trouvé."
    var $noMatch = $("li.ln-no-match");
    if ($noMatch.length) {
      var lang = $("html").attr("lang") || "fr";
      $noMatch.each(function () {
        $(this).text(lang.startsWith("en") ? "Nothing found." : "Aucun résultat trouvé.");
      });
    }
  }, 500); // délai pour laisser le temps au plugin de générer les éléments
});

jQuery(function ($) {
  //   var lang = $("html").attr("lang") || "fr";

  // Formulaire newsletter uniquement
  var $newsletterForm = $(".newsletter-submit").closest("form"); // trouve le formulaire parent
  if ($newsletterForm.length) {
    var $cf7Msg = $newsletterForm.find(".wpcf7-response-output");
    $cf7Msg.css("visibility", "hidden"); // masquer avant traduction
    if ($cf7Msg.length) {
      $cf7Msg.text(
        lang.startsWith("en")
          ? "Thank you! Please check your email to confirm your request."
          : "Merci ! Vérifiez votre email pour valider votre demande."
      );
      $cf7Msg.css("visibility", "visible"); // montrer après traduction
    }
  }
});

jQuery(function ($) {
  var lang = $("html").attr("lang") || "fr";
  var $btn = $(".footer-contact-btn").css("visibility", "hidden");
  $btn.text(lang.startsWith("en") ? "Contact us" : "Contactez-nous").css("visibility", "visible");
});

/* login english */
jQuery(function ($) {
  var lang = $("html").attr("lang") || "fr";

  if (lang.startsWith("en")) {
    var $loginForm = $(".ur-form-grid");

    if ($loginForm.length) {
      // Champ Email (username)
      var $usernameField = $loginForm.find('input[name="username"]');
      var $usernameLabel = $loginForm.find('label[for="username"]');
      if ($usernameField.length && $usernameLabel.length) {
        $usernameField.attr("placeholder", "Please enter your email");
        $usernameLabel
          .contents()
          .filter(function () {
            return this.nodeType === 3;
          })
          .first()
          .replaceWith("Email ");
      }

      // Champ Password : juste le placeholder
      var $passwordField = $loginForm.find('input[name="password"]');
      if ($passwordField.length) {
        $passwordField.attr("placeholder", "Please enter your password");
      }
    }
  }
});
// poir all plus loin
jQuery(function ($) {
  setTimeout(function () {
    var lang = $("html").attr("lang") || "fr";

    // Traduction du titre YARPP
    var $yarppTitle = $(".yarpp h3");
    if ($yarppTitle.length) {
      $yarppTitle.text(lang.startsWith("en") ? "Learn more" : "Pour aller plus loin");
    }

    // Traduction du message YARPP "pas d'entrée similaire"
    var $yarppMessage = $(".yarpp p");
    if ($yarppMessage.length) {
      $yarppMessage.each(function () {
        var text = $(this).text().trim();
        if (text === "Il n’y a pas d’entrée similaire.") {
          $(this).text(lang.startsWith("en") ? "No related posts found." : "Il n’y a pas d’entrée similaire.");
        }
      });
    }
  }, 500); // délai pour laisser le temps au plugin YARPP de générer le contenu
});
// learn more dans les article extraits
jQuery(function ($) {
  if ($("html").attr("lang").startsWith("en")) {
    $("a")
      .filter(function () {
        return $(this).text().trim() === "Lire plus";
      })
      .text("Learn more");
  }
});
// badge proposition
jQuery(function ($) {
  if ($("html").attr("lang").startsWith("en")) {
    $(".cf7-badge.bloc-badge").text("Talk to us");
  }
});
// dans les single posts pages
jQuery(function ($) {
  if ($("html").attr("lang").startsWith("en")) {
    $(".cf7-badge").text("Talk to us");
  }
});

// proposer une actualité dnas les singles posts
jQuery(function ($) {
  if ($("html").attr("lang").startsWith("en")) {
    $("a.bouton-post").text("Submit news");
  }
});

// newsletter succes message si champ vide

jQuery(function ($) {
  $(".wpcf7-form").each(function () {
    var $form = $(this);
    var $success = $form.find(".wpcf7-response-output");

    // Vérifier toutes les 100ms si un message d'erreur est visible
    setInterval(function () {
      if ($form.find(".wpcf7-not-valid-tip:visible").length) {
        $success.hide();
      }
    }, 100);

    // Afficher le succès quand le formulaire est envoyé correctement
    $form.on("wpcf7mailsent", function () {
      $success.show();
    });
  });
});

// message NL
jQuery(function ($) {
  const lang = $("html").attr("lang") || "fr"; // par défaut français

  $(".wpcf7-not-valid-tip").each(function () {
    const text = $(this).text().trim();

    if (lang.startsWith("en") && text === "Veuillez renseigner ce champ.") {
      $(this).text("Please enter your email address.");
    }

    if (lang.startsWith("fr") && text === "Please enter your email address.") {
      $(this).text("Veuillez renseigner votre adresse email.");
    }
  });
});

// reset
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector(".user-registration-ResetPassword");
  if (!form) return;

  const emailInput = form.querySelector('input[name="user_login"]');
  const submitBtn = form.querySelector('input[type="submit"]');

  // Créer un conteneur pour le message d'erreur
  let errorMsg = document.createElement("div");
  errorMsg.style.color = "red";
  errorMsg.style.fontSize = "14px";
  errorMsg.style.marginTop = "5px";
  errorMsg.style.paddingBottom = "15px"; // remplacer marginBottom par paddingBottom
  errorMsg.style.display = "none";
  emailInput.parentNode.appendChild(errorMsg);

  // Détection langue
  const lang = document.documentElement.lang || "fr";
  const isEnglish = lang.indexOf("en") === 0;

  function getEmailError(value) {
    if (!value) return isEnglish ? "Please enter your email address." : "Veuillez entrer votre adresse email.";
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) return isEnglish ? "Please enter a valid email address." : "Veuillez entrer une adresse email valide.";
    return "";
  }

  function validateEmail() {
    const message = getEmailError(emailInput.value.trim());
    if (message) {
      errorMsg.textContent = message;
      errorMsg.style.display = "block";
      submitBtn.disabled = false; // <-- force toujours cliquable
      return false;
    } else {
      errorMsg.style.display = "none";
      submitBtn.disabled = false; // <-- force toujours cliquable
      return true;
    }
  }

  // Validation instantanée à la saisie
  emailInput.addEventListener("input", validateEmail);

  // Validation au submit
  form.addEventListener("submit", function (e) {
    if (!validateEmail()) {
      e.preventDefault(); // bloque submit si email invalide
    }
  });
});

// champs requis
jQuery(function ($) {
  var lang = $("html").attr("lang") || "fr";
  var msgRequired = lang.startsWith("en") ? "This field is required." : "Ce champ est requis.";

  // Formulaire User Registration
  var $form = $("#user-registration-form-14712"); // remplace par l'ID correct si besoin
  if ($form.length) {
    // Observer pour détecter les messages d'erreur dynamiques
    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        $(mutation.addedNodes).each(function () {
          var $node = $(this);
          if ($node.hasClass && $node.hasClass("user-registration-error")) {
            $node.text(msgRequired).css("visibility", "visible");
          }
          $node.find(".user-registration-error").each(function () {
            $(this).text(msgRequired).css("visibility", "visible");
          });
        });
      });
    });

    observer.observe($form[0], { childList: true, subtree: true });
  }
});
// remplacer les champs dans inscription eglish
jQuery(function ($) {
  // Vérifie si la page est en anglais
  if ($("html").attr("lang") && $("html").attr("lang").startsWith("en")) {
    var $form = $("#user-registration-form-14712");

    // Mapping labels et placeholders avec "Please"
    var fields = {
      first_name: {
        label: "First name",
        placeholder: "Please enter your first name"
      },
      last_name: {
        label: "Last name",
        placeholder: "Please enter your last name"
      },
      user_email: { label: "Email", placeholder: "Please enter your email" },
      user_pass: {
        label: "Password",
        placeholder: "Please enter your password"
      },
      user_confirm_password: {
        label: "Confirm password",
        placeholder: "Please confirm your password"
      },
      // Nouveau champ ajouté
      input_box_1762110056: {
        label: "Phone Number",
        placeholder: "Please enter your phone number"
      }
    };

    $.each(fields, function (id, data) {
      // Modifier le label en gardant l'étoile si elle existe
      var $label = $form.find('label[for="' + id + '"]');
      var abbr = $label.find("abbr.required").prop("outerHTML") || "";
      $label.html(data.label + " " + abbr);

      // Modifier le placeholder
      $form.find("#" + id).attr("placeholder", data.placeholder);

      // Mettre à jour data-label si utilisé pour la validation
      $form.find("#" + id).attr("data-label", data.label);
    });
  }
});

// bouton inscription
jQuery(function ($) {
  var lang = $("html").attr("lang") || "fr";
  var $form = $("#user-registration-form-14712"); // Formulaire d'inscription

  if ($form.length) {
    var $submitBtn = $form.find('button[type="submit"]');
    if (lang.startsWith("en")) {
      $submitBtn.text("Register");
    } else {
      $submitBtn.text("S'inscrire");
    }
  }
});

//Redirection vers mot dans glossaire multi-langue (Polylang + Bootstrap collapse)
jQuery(document).ready(function ($) {
  function normalizeText(str) {
    return String(str || "")
      .trim()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "") // accents
      .replace(/\s+/g, "-") // espaces → tirets
      .replace(/[^\w\-]/g, ""); // garder lettres/chiffres/tirets
  }

  function getCurrentLang() {
    var path = window.location.pathname;
    var langMatch = path.match(/^\/([a-z]{2})\//);
    if (langMatch && langMatch[1]) return langMatch[1];
    var htmlLang = document.documentElement.getAttribute("lang");
    if (htmlLang) return htmlLang.substring(0, 2).toLowerCase();
    return "fr";
  }

  function getGlossaryBaseUrl() {
    var lang = getCurrentLang();
    switch (lang) {
      case "en":
        return "/en/glossary/";
      case "fr":
        return "/glossaire/";
      default:
        return "/" + lang + "/glossary/";
    }
  }

  // Trouver le lien/élément correspondant au terme
  function findGlossaryTermEl(term) {
    term = normalizeText(term);
    // 1) Elément qui a directement l'ID = term
    var $byId = $("#" + term);
    if ($byId.length) return $byId.eq(0);

    // 2) .glossaryLink dont le texte normalisé ou data-term correspond
    var $candidates = $(".accordion-header span.glossaryLink").filter(function () {
      var $t = $(this);
      var fromText = normalizeText($t.text());
      var fromData = normalizeText($t.data("term"));
      return fromText === term || (fromData && fromData === term);
    });

    if ($candidates.length) return $candidates.eq(0);

    // 3) Titre d'accordéon (si ce n’est pas le lien lui-même)
    var $accBtn = $(".accordion-button").filter(function () {
      return normalizeText($(this).text()) === term;
    });

    if ($accBtn.length) return $accBtn.eq(0);

    return $(); // vide
  }

  // Scroll avec offset (header sticky)
  function scrollToEl($el, offset) {
    if (!$el || !$el.length) return;
    var top = $el.offset().top - (offset || 180); // ajuste l’offset ici (ex: 120px)
    $("html, body").stop(true).animate({ scrollTop: top }, 500);
  }

  // Ouvrir l’accordéon parent si besoin puis scroller
  function openAndScroll($target) {
    if (!$target || !$target.length) return;

    var $item = $target.closest(".accordion-item");
    var $collapse = $item.find(".accordion-collapse").eq(0);
    var $btn = $item.find(".accordion-button").eq(0);

    // Si pas d’accordéon autour -> scroll direct
    if (!$collapse.length || !$btn.length) {
      scrollToEl($target);
      return;
    }

    // Si déjà ouvert -> scroll direct
    if ($collapse.hasClass("show")) {
      scrollToEl($target);
      return;
    }

    // Ouvre et attends l’animation Bootstrap
    $collapse.one("shown.bs.collapse", function () {
      // petit délai pour laisser le layout se stabiliser
      setTimeout(function () {
        scrollToEl($target);
      }, 50);
    });

    // Déclenche l’ouverture (équivaut à cliquer)
    $btn.trigger("click");
  }

  // Traiter le hash actuel (avec retry si le contenu arrive tard)
  function handleHash(maxWaitMs) {
    var raw = window.location.hash ? window.location.hash.substring(1) : "";
    if (!raw) return;

    try {
      raw = decodeURIComponent(raw);
    } catch (e) {}
    var term = normalizeText(raw);
    if (!term) return;

    var start = Date.now();
    (function tryFind() {
      var $target = findGlossaryTermEl(term);
      if ($target.length) {
        openAndScroll($target);
        return;
      }
      if (Date.now() - start < (maxWaitMs || 5000)) {
        // 5s max
        setTimeout(tryFind, 150);
      }
    })();
  }

  // --- Click sur un mot du glossaire (depuis n’importe quelle page) ---
  $(document).on("click", ".glossaryLink", function (e) {
    // Si on n’est PAS sur la page glossaire, on ouvre un nouvel onglet
    if (!$("#glossaryList").length) {
      e.preventDefault();
      var term = normalizeText($(this).data("term") || $(this).text());
      var url = getGlossaryBaseUrl() + "#" + term;
      window.open(url, "_blank");
    }
    // Si on est déjà sur la page du glossaire, laisser le hash se mettre
    // puis handleHash() s’en charge via hashchange ci-dessous.
  });

  // --- Au chargement : si hash présent, gérer le scroll ---
  handleHash(5000);

  // --- Si le hash change (ex: clic interne), re-gérer ---
  $(window).on("hashchange", function () {
    handleHash(1000);
  });

  // BONUS: améliore le scroll si CSS supporté (en plus du fallback jQuery)
  try {
    document.documentElement.style.scrollBehavior = "smooth";
  } catch (e) {}
});

document.addEventListener("DOMContentLoaded", function () {
  function getLabel(inputName) {
    const input = document.querySelector(`input[name="${inputName}"], input[type="date"][name="${inputName}"]`);
    if (!input) return null;
    let parent = input.parentElement;
    while (parent && parent.tagName !== "LABEL") {
      parent = parent.parentElement;
    }
    return parent;
  }

  const fieldsData = [
    {
      label: getLabel("date_debut"),
      input: document.querySelector('input[name="date_debut"]')
    },
    {
      label: getLabel("date_fin"),
      input: document.querySelector('input[name="date_fin"]')
    },
    {
      label: getLabel("lieu"),
      input: document.querySelector('input[name="lieu"]')
    }
  ];

  const showCats = ["190", "192", "203", "201"];

  function toggleFields() {
    const selectedRadio = document.querySelector('input[name="categorie-article"]:checked');
    const selectedValue = selectedRadio ? selectedRadio.value : null;
    const show = showCats.includes(selectedValue);

    fieldsData.forEach((f) => {
      if (!f.label || !f.input) return;

      // Affichage
      f.label.style.display = show ? "block" : "none";

      // Gestion de l'obligation
      if (show) {
        f.input.setAttribute("required", "required");
      } else {
        f.input.removeAttribute("required");
        f.input.value = ""; // optionnel : vide le champ si la catégorie ne correspond pas
      }
    });
  }

  // Masquer et rendre non obligatoire par défaut
  setTimeout(() => {
    fieldsData.forEach((f) => {
      if (!f.label || !f.input) return;
      f.label.style.display = "none";
      f.input.removeAttribute("required");
    });
  }, 200);

  // Listener sur les radios
  const radios = document.querySelectorAll('input[name="categorie-article"]');
  radios.forEach((r) => r.addEventListener("change", toggleFields));
});

// inscription

// TRADUCTION RESET PASSWORD
jQuery(function ($) {
  const lang = $("html").attr("lang") || "fr";

  function traduireResetForm() {
    $("#lostpasswordform,#resetpasswordform")
      .find("legend, label, p, span, input, button")
      .each(function () {
        const text = $(this).text().trim();

        if (text === "Reset Password") {
          $(this).text("Réinitialisation du mot de passe");
        }

        if (text === "Please enter your email address or username. You will receive a link to create a new password via email.") {
          $(this).text(
            "Veuillez saisir votre adresse e-mail ou votre nom d’utilisateur. Vous recevrez un lien par e-mail pour créer un nouveau mot de passe."
          );
        }

        if (text === "Please enter a new password.") {
          $(this).text("Nouveau mot de passe");
        }
        if (text === "Re-enter Password") {
          $(this).text("Retapez le mot de passe");
        }
      });
  }

  function traduireMessagesDynamiques() {
    // Exemple : messages affichés après soumission
    $("#lostpasswordform .message, #lostpasswordform .wpcf7-response-output, #lostpasswordform .login-error, #lostpasswordform p").each(function () {
      const text = $(this).text().trim();

      if (text === "That username is not recognised.") {
        $(this).text("Nom d’utilisateur non reconnu.");
      }

      if (text === "An email has been sent. Please check your inbox.") {
        $(this).text("Un e-mail a été envoyé. Vérifiez votre boîte de réception.");
      }
    });
  }

  if (lang.startsWith("fr")) {
    // Traduire au chargement de la page
    traduireResetForm();

    // Traduire après clic
    $("#reset-pass-submit").on("click", function () {
      setTimeout(function () {
        traduireResetForm();
        traduireMessagesDynamiques(); // traduction des messages dynamiques
      }, 50); // délai pour laisser le plugin injecter les messages
    });
  }
});
// message erreur reset mdp
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector(".user-registration-ResetPassword");
  if (!form) return;

  const pass1 = form.querySelector('input[name="password_1"]');
  const pass2 = form.querySelector('input[name="password_2"]');

  // Ajouter les placeholders
  const lang = document.documentElement.lang || "fr";
  const isEnglish = lang.startsWith("en");

  pass1.placeholder = isEnglish ? "Please enter your new password" : "Veuillez saisir votre nouveau mot de passe";
  pass2.placeholder = isEnglish ? "Please confirm your new password" : "Veuillez confirmer votre nouveau mot de passe";

  // Message d'erreur
  let passError = document.createElement("div");
  passError.style.color = "red";
  passError.style.fontSize = "14px";
  passError.style.marginTop = "5px";
  passError.style.paddingBottom = "15px";
  passError.style.display = "none";
  pass2.parentNode.appendChild(passError);

  // Message de succès
  let successMsg = document.createElement("div");
  successMsg.style.background = "#f7fdf8";
  successMsg.style.padding = "12px 12px 12px 16px";
  successMsg.style.borderRadius = "4px";
  successMsg.style.border = "none";
  successMsg.style.borderLeft = "4px solid #49c85f";
  successMsg.style.display = "none";
  successMsg.style.alignItems = "center";
  successMsg.style.gap = "12px";
  successMsg.style.color = "#222";
  successMsg.style.fontSize = "14px";
  successMsg.style.fontWeight = "400";
  successMsg.style.lineHeight = "21px";
  successMsg.style.letterSpacing = ".15px";
  successMsg.style.borderColor = "#46b450";
  form.parentNode.insertBefore(successMsg, form);

  const loginURL = isEnglish ? "/en/login/" : "/connexion/";

  function validatePasswords() {
    if (pass1.value && pass2.value && pass1.value !== pass2.value) {
      passError.textContent = isEnglish ? "Passwords do not match." : "Les mots de passe ne correspondent pas.";
      passError.style.display = "block";
      return false;
    }
    passError.style.display = "none";
    return true;
  }

  pass1.addEventListener("input", validatePasswords);
  pass2.addEventListener("input", validatePasswords);

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    if (!validatePasswords()) return;

    const formData = new FormData(form);

    fetch(form.action, {
      method: "POST",
      body: formData
    })
      .then(() => {
        form.style.display = "none";
        successMsg.textContent = isEnglish ? "Your password has been successfully changed." : "Votre mot de passe a été modifié avec succès.";
        successMsg.style.display = "block";

        setTimeout(() => {
          window.location.href = loginURL;
        }, 2000);
      })
      .catch(() => {
        passError.textContent = isEnglish ? "An error occurred. Please try again." : "Une erreur est survenue. Veuillez réessayer.";
        passError.style.display = "block";
      });
  });

  // Cacher le premier "Nouveau mot de passe"
  const pElements = document.querySelectorAll(".user-registration-ResetPassword .ur-form-grid > p");
  pElements.forEach((p) => {
    if (p.textContent.trim() === "Nouveau mot de passe") {
      p.style.display = "none";
    }
  });
});

//Traduction input upload proposer une actualité

jQuery(document).ready(function ($) {
  // Détecter la langue
  var lang = $("html").attr("lang"); // ex: "en-US" ou "fr-FR"
  var isEnglish = lang.startsWith("en");

  // Parcourir tous les inputs type file de CF7
  $(".wpcf7-form-control.wpcf7-file").each(function () {
    var $input = $(this);

    // Créer un label personnalisé
    var $label = $('<span class="custom-file-label"></span>');
    var $nameFileHtml = $('<div class="custom-file-name"></div>');
    $label.text(isEnglish ? "Choose a file" : "Choisir un fichier");

    // Insérer le label après l'input
    $input.after($label);
    $label.after($nameFileHtml);

    // Quand un fichier est sélectionné
    $input.on("change", function () {
      var fileName = $input.val().split("\\").pop(); // récupérer le nom du fichier
      if (fileName) {
        $nameFileHtml.text(fileName);
      } else {
        $nameFileHtml.text("");
      }
    });

    // Cacher le texte natif du navigateur
    $input.css({
      opacity: 0,
      position: "absolute",
      width: "100%",
      height: "100%",
      cursor: "pointer"
    });

    // Styliser le label comme un bouton
    $label.css({
      display: "inline-block",
      padding: "6px 12px",
      border: "1px solid #ccc",
      "border-radius": "8px",
      cursor: "pointer",
      background: "#ffffff",
      "font-size": "13px",
      "font-weight": "100!important"
    });

    // Cliquer sur le label déclenche l'input file
    $label.on("click", function () {
      $input.trigger("click");
    });
  });
});

/* accueil par home */

document.addEventListener("DOMContentLoaded", function () {
  const link = document.querySelector(".home");
  if (!link) return;

  if (document.documentElement.lang.startsWith("en")) {
    link.innerHTML = link.innerHTML.replace("Accueil", "Home");
    link.setAttribute("href", "/en/home");
  }
});

/* les champs de proposition */

document.addEventListener("DOMContentLoaded", function () {
  if (document.documentElement.lang.startsWith("en")) {
    const metaCards = document.querySelectorAll(".cf7-meta .meta-card strong");
    metaCards.forEach((card) => {
      switch (card.textContent.trim()) {
        case "ORCID :":
          card.textContent = "ORCID :";
          break;
        case "Institution :":
          card.textContent = "Institution :";
          break;
        case "Date début :":
          card.textContent = "Start date :";
          break;
        case "Date fin :":
          card.textContent = "End date :";
          break;
        case "Lieu :":
          card.textContent = "Location :";
          break;
      }
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  if (document.documentElement.lang.startsWith("en")) {
    const relatedTitle = document.querySelector(".yarpp-related h3");
    if (relatedTitle) {
      relatedTitle.textContent = "Learn more";
    }
  }
});

jQuery(function ($) {
  // Vérifie si l'URL contient /en/catalog/
  if (window.location.pathname === "/en/catalog/") {
    // Attendre que le DOM soit prêt
    $(document).ready(function () {
      // Déclenche le clic sur le label du bouton anglais
      $('#lang-switcher label[for="lang-en"]').trigger("click");
    });
  }
});

//code menu
document.addEventListener("DOMContentLoaded", function () {
  const menuItem = document.querySelector(".menu-item.menu-user");
  if (!menuItem) return;

  const userMenu = menuItem.querySelector("a");

  userMenu.addEventListener("click", function (e) {
    e.preventDefault();
    e.stopPropagation();
    menuItem.classList.toggle("active");
  });

  document.addEventListener("click", function (e) {
    if (!menuItem.contains(e.target)) {
      menuItem.classList.remove("active");
    }
  });
});

//NL
jQuery(document).ready(function ($) {
  $("#second-footer").insertBefore("#first-footer");
});

/* rest password */
document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get("lang") === "en") {
    const translations = {
      "Nouveau mot de passe": "New Password",
      "Re-saisir le nouveau mot de passe.": "Confirm Password",
      "Veuillez saisir votre nouveau mot de passe": "Please enter your new password",
      "Veuillez confirmer votre nouveau mot de passe": "Please confirm your new password",
      Enregistrer: "Save"
    };

    document.querySelectorAll("#user-registration label").forEach((label) => {
      let mainText = Array.from(label.childNodes)
        .filter((n) => n.nodeType === Node.TEXT_NODE)
        .map((n) => n.textContent.trim())
        .join(" ");
      if (translations[mainText]) {
        label.childNodes.forEach((n) => {
          if (n.nodeType === Node.TEXT_NODE) n.textContent = translations[mainText] + " ";
        });
      }
    });

    document.querySelectorAll("#user-registration input").forEach((input) => {
      const placeholder = input.getAttribute("placeholder");
      if (placeholder && translations[placeholder]) input.setAttribute("placeholder", translations[placeholder]);
    });

    document.querySelectorAll('#user-registration input[type="submit"], #user-registration button').forEach((btn) => {
      const val = btn.value ? btn.value.trim() : btn.textContent.trim();
      if (translations[val]) {
        if (btn.value) btn.value = translations[val];
        else btn.textContent = translations[val];
      }
    });
  }
});
/* success message inscription english */
jQuery(document).ready(function ($) {
  // Fonction pour traduire le message
  function translateSuccessMessage() {
    var messageDiv = $("#ur-submit-message-node");
    if (messageDiv.length) {
      var text = messageDiv.text().trim();

      // Vérifie si le site est en anglais
      if ($("html").attr("lang") === "en" || window.location.href.indexOf("/en") > -1) {
        if (text === "Utilisateur inscrit avec succès.") {
          messageDiv.find("ul").text("User registered successfully.");
        }
      }
    }
  }

  // Surveille l'apparition du message après soumission
  const observer = new MutationObserver(translateSuccessMessage);
  observer.observe(document.body, { childList: true, subtree: true });

  // Et au cas où le message est déjà là
  translateSuccessMessage();
});
/* champ portable */
jQuery(document).ready(function ($) {
  $('input[name="input_box_1762110056"], .input_box_1762110056').on("input", function () {
    this.value = this.value.replace(/[^0-9+]/g, "");
  });
});
// Détecte la langue de l'utilisateur
const userLang = navigator.language || navigator.userLanguage; // ex: "fr-FR" ou "en-US"

// Si la langue commence par 'en', on traduit le champ
if (userLang.startsWith("en")) {
  // Sélection du label
  const label = document.querySelector("#input_box_1762110056_field label");
  if (label) label.textContent = "Phone Number";

  // Sélection du champ input
  const input = document.querySelector("#input_box_1762110056");
  if (input) {
    input.setAttribute("placeholder", "Please enter your phone number");
    input.setAttribute("data-label", "Phone Number"); // pour la validation ou scripts existants
  }
}

/* Messagepar defaut nl */
document.addEventListener("DOMContentLoaded", () => {
  const tips = document.querySelectorAll(".wpcf7-not-valid-tip");

  tips.forEach((tip) => {
    const text = tip.textContent.trim();
    if (
      text === "Merci ! Vérifiez votre email pour valider votre demande." ||
      text === "Thank you! Please check your email to confirm your request."
    ) {
      tip.classList.add("cf7-newsletter-msg");
    }
  });
});

/*test*/
document.addEventListener("DOMContentLoaded", function () {
  // Vérifier si la langue est anglaise
  const lang = document.documentElement.lang || "";

  if (lang.startsWith("en")) {
    jQuery(document).ready(function ($) {
      $('#homepage-search-form button[type="submit"]').on("click", function (e) {
        e.preventDefault();

        var term = $("#homepage-search-input").val().trim();

        if (term) {
          // Rediriger uniquement pour la version anglaise
          window.location.href = "/en/catalog/?search=" + encodeURIComponent(term);
        }
      });
    });
  }
});

/*Ignorer glossary sur home page, tous les titres   */
jQuery(document).ready(function ($) {
  $(
    ".page-id-16218 .glossaryLink , .page-id-9905 .glossaryLink,.page-id-10487 .glossaryLink,.tm-element-content-heading .glossaryLink,.e-n-accordion-item-title-text .glossaryLink,.tm-element-content-heading .glossaryLink,.elementor-heading-title .glossaryLink,.titre-fresh .glossaryLink,.titre-no-glossaire .glossaryLink, .bloc-mon-espace .glossaryLink"
  ).each(function () {
    $(this).replaceWith($(this).text());
  });
});

jQuery(document).ready(function ($) {
  $("#user-registration-form-14712").on("submit", function (e) {
    setTimeout(function () {
      // Désactive uniquement les événements du plugin
      $(document).off("ur_form_submission_success");
      $(document).off("ur_form_submission_error");

      // Neutralise seulement window.scrollTo si le plugin l'utilise
      const originalScrollTo = window.scrollTo;
      window.scrollTo = function (x, y) {
        // ignore les appels du plugin
      };

      // Scroll manuel de +100px après la soumission
      $("html, body").animate(
        {
          scrollTop: $(window).scrollTop() + 100
        },
        400
      );
    }, 200); // petit délai pour laisser la requête AJAX démarrer
  });
});

//affichage dans page message activation
document.addEventListener("DOMContentLoaded", function () {
  // Fonction pour récupérer un paramètre GET
  function getQueryParam(name) {
    let params = new URLSearchParams(window.location.search);
    return params.get(name);
  }

  let message = getQueryParam("activation_msg");
  if (message) {
    let container = document.querySelector(".message-activation");
    if (container) {
      // Décoder les caractères spéciaux et afficher dans le bloc
      container.innerHTML =
        '<div class="activation-success" style=" border:1px solid #46b450; text-align:center; color:#351F65;    margin-bottom: 15px;">' +
        decodeURIComponent(message) +
        "</div>";
    }
  }
});

//
jQuery(document).ready(function ($) {
  jQuery(".wpr-promo-box").each(function () {
    const $parent = jQuery(this);

    const $overlay = $parent.find(".wpr-promo-box-bg-overlay");
    const $content = $parent.find(".wpr-promo-box-content");

    if (!$overlay.length || !$content.length) return;

    const bgColor = $overlay.css("background-color");

    // appliquer la couleur au content
    $content.css("background-color", bgColor);
  });

  $("#login").on("click", function (e) {
    e.preventDefault(); // empêche l'action par défaut si c'est un lien
    window.location.hash = "login"; // ajoute #login à l’URL
  });

  $("#register").on("click", function (e) {
    e.preventDefault();
    window.location.hash = "register"; // ajoute #register à l’URL
  });
});

// desactiver gloossaires dans les pages video
document.addEventListener("DOMContentLoaded", function () {
  const path = window.location.pathname;
  if (path === "/videos/" || path === "/en/videos-2/") {
    const glossaryLinks = document.querySelectorAll(".glossaryLink");

    glossaryLinks.forEach((link) => {
      // Remplacer l'élément par son texte uniquement
      const textNode = document.createTextNode(link.textContent);
      link.parentNode.replaceChild(textNode, link);
    });
  }
});

// lien complet au lieu justement lire plus
document.addEventListener("DOMContentLoaded", function () {
  const path = window.location.pathname;
  if (path === "/videos/" || path === "/en/videos-2/") {
    // Sélectionner tous les blocs post-item
    const posts = document.querySelectorAll(".post-item");

    posts.forEach((post) => {
      // Trouver le lien "Lire plus / Learn more"
      const link = post.querySelector(".themetechmount-blogbox-footer-left a");
      if (link) {
        const url = link.href;

        // Rendre tout le bloc cliquable
        post.style.cursor = "pointer";
        post.addEventListener("click", function (e) {
          // Eviter de double cliquer sur un lien déjà existant
          if (e.target.tagName.toLowerCase() !== "a") {
            window.location.href = url;
          }
        });
      }
    });
  }
});

// remplacement lien categories vers son page
document.addEventListener("DOMContentLoaded", function () {
  // Sélectionne tous les spans de catégorie visibles
  document.querySelectorAll(".tm-meta-line.cat-links span:not(.screen-reader-text)").forEach(function (catSpan) {
    const catText = catSpan.textContent.trim();

    // Vérifie si c'est "Vidéo" ou "Video"
    if (catText === "Vidéo" || catText === "Video") {
      // Détermine l'URL selon la langue
      const url = catText === "Video" ? "/en/videos-2/" : "/videos/";

      // Crée le lien
      const link = document.createElement("a");
      link.href = url;
      link.textContent = catText;

      // Remplace le texte par le lien
      catSpan.textContent = "";
      catSpan.appendChild(link);
    }
  });
});
// cache icone genially
document.addEventListener("DOMContentLoaded", function () {
  const hideWatermark = () => {
    const wm = document.querySelector('a[href*="genially.com?from=watermark-powered"], .Watermarkstyled__StyledWatermark-sc-11r3nbg-2');
    if (wm) wm.style.display = "none";
  };
  hideWatermark();
  setInterval(hideWatermark, 500); // répète toutes les 0.5s pour les injections dynamiques
});

// scroll vers glossiere interne

window.onload = function () {
  const offset = 330;

  if (window.location.hash) {
    history.scrollRestoration = "manual";
    window.scrollTo(0, 0);
  }

  function slugify(text) {
    return text
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/\s+/g, "-")
      .replace(/[^\w\-]+/g, "")
      .replace(/\-\-+/g, "-")
      .replace(/^-+/, "")
      .replace(/-+$/, "");
  }

  function doScroll(target) {
    const y = target.getBoundingClientRect().top + window.pageYOffset - offset;

    window.scrollTo({
      top: y,
      behavior: "smooth"
    });
  }

  function scrollToGlossaryTerm(hash) {
    if (!hash) return;

    const headings = document.querySelectorAll(".accordion-header");
    let targetHeading = null;

    headings.forEach((h) => {
      const span = h.querySelector(".glossaryLink");
      if (span && slugify(span.textContent.trim()) === hash.toLowerCase()) {
        targetHeading = h;
      }
    });

    if (!targetHeading) return;

    const button = targetHeading.querySelector(".accordion-button");
    const collapseId = button.getAttribute("aria-controls");
    const collapseEl = document.getElementById(collapseId);

    if (collapseEl && !collapseEl.classList.contains("show")) {
      collapseEl.addEventListener("shown.bs.collapse", function handler() {
        collapseEl.removeEventListener("shown.bs.collapse", handler);
        requestAnimationFrame(() => {
          doScroll(targetHeading);
        });
      });

      new bootstrap.Collapse(collapseEl, { toggle: true });
    } else {
      requestAnimationFrame(() => {
        doScroll(targetHeading);
      });
    }
  }

  if (window.location.hash) {
    const hash = decodeURIComponent(window.location.hash.substring(1));
    scrollToGlossaryTerm(hash);
  }
};

document.addEventListener("click", function (e) {
  const toggle = e.target.closest(".toggle-password");
  if (!toggle) return;

  const wrapper = toggle.closest(".password-wrapper");
  const input = wrapper.querySelector("input");
  const icon = toggle.querySelector("i");

  if (input.type === "password") {
    input.type = "text";
    icon.className = "fa-solid fa-eye";
  } else {
    input.type = "password";
    icon.className = "fa-solid fa-eye-slash";
  }
});

// menu

//js modal reset password
jQuery(function ($) {
  $(".edit-password-btn").on("click", (e) => (e.preventDefault(), $("#modal-reset-password").show()));
  $(".modal-close").on("click", (e) => $(e.target).is("#modal-reset-password, .modal-close") && $("#modal-reset-password").hide());
  // Clic sur annuler modal
  $(".cancel-edit-password,.btn-close").on("click", function () {
    $("#modal-reset-password").hide();
  });

  // Fermer le modal en cliquant en dehors
  $(window).on("click", function (e) {
    if ($(e.target).is("#modal-reset-password")) {
      $("#addEditItemModal").hide();
      resetForm();
    }
  });
});

jQuery(document).ready(function ($) {
  $(document).on("submit", "#resetPasswordForm", function (e) {
    e.preventDefault();

    $.ajax({
      url: "/wp-admin/admin-ajax.php",
      type: "POST",
      data: $(this).serialize(),
      success: function (res) {
        if (res.success) {
          $("#password-response").html('<p class="cep-success">' + res.data.message + "</p>");
          $("#resetPasswordForm")[0].reset();

          if (res.data.redirect_url) {
            setTimeout(function () {
              window.location.href = res.data.redirect_url;
            }, 3000);
          }
        } else {
          let html = "";
          res.data.errors.forEach((err) => {
            html += '<p class="cep-error">' + err + "</p>";
          });
          $("#password-response").html(html);
        }
      },
      error: function () {
        $("#password-response").html('<p class="cep-error">Erreur serveur</p>');
      }
    });
  });
});

//conversion du format du date du post liste des articles
jQuery(document).ready(function ($) {
  const moisFR = {
    janvier: "Jan",
    février: "Féb",
    mars: "Mar",
    avril: "Avr",
    mai: "Mai",
    juin: "Juin",
    juillet: "Juil",
    août: "Août",
    septembre: "Sep",
    octobre: "Oct",
    novembre: "Nov",
    décembre: "Déc"
  };

  const moisEN = {
    january: "Jan",
    february: "Feb",
    march: "Mar",
    april: "Apr",
    may: "May",
    june: "Jun",
    july: "Jul",
    august: "Aug",
    september: "Sep",
    october: "Oct",
    november: "Nov",
    december: "Dec"
  };
  let lang = $("html").attr("lang") || "en";

  function formatDates(scope) {
    $(scope)
      .find(".wpr-grid-item-date span")
      .each(function () {
        // éviter double traitement
        if ($(this).find(".custom-date-month").length) return;

        let text = $(this).text().trim().toLowerCase();
        let parts = text.split(" ");

        if (parts.length === 3) {
          let mois = parts[1];
          let annee = parts[2];

          let moisFormat = lang.startsWith("fr") ? moisFR[mois] : moisEN[mois];

          if (moisFormat) {
            $(this).html('<span class="custom-date-month">' + moisFormat + "</span> " + '<span class="custom-date-year">' + annee + "</span>");
          }
        }
      });
  }

  // Initial
  formatDates(document);

  // Observer pour détecter nouveaux éléments (Load More AJAX)
  const observer = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      mutation.addedNodes.forEach(function (node) {
        // Vérifier que c’est un élément HTML
        if (node.nodeType === 1) {
          formatDates(node);
        }
      });
    });
  });

  // Cibler le container principal (important)
  const target = document.querySelector(".wpr-grid-wrap") || document.body;

  observer.observe(target, {
    childList: true,
    subtree: true
  });
});

/** Add badge in page acualite */
jQuery(window).on("elementor/frontend/init", function () {
  function addBadge() {
    // Vérifie si l'élément existe et si je suis dans la page actualites
    if (jQuery(".bloc-article").length) {
      jQuery.ajax({
        url: "/wp-admin/admin-ajax.php",
        type: "POST",
        data: {
          action: "get_custom_posts" // le nom du hook PHP
        },
        success: function (response) {
          if (Array.isArray(response.ids)) {
            response.ids.forEach(function (postId) {
              // Sélectionne l'élément avec la classe correspondant à l'ID
              var target = jQuery(".wpr-grid-item.post-" + postId);
              if (target.length) {
                target.append('<div class="cf7-badge">' + response.badge + "</div>");
              }
            });
          }
        },
        error: function (err) {
          console.log("Erreur AJAX:", err);
        }
      });
    }
  }

  jQuery(".wpr-load-more-btn").on("click", function () {
    addBadge(); // appelle ta fonction
  });

  // initiale
  addBadge();
});

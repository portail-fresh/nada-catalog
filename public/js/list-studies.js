jQuery(document).ready(function ($) {
  // Récupère la langue depuis PHP
  const lang = nada_vars.lang || "fr";

  let langUrl = "";
  if (lang === "fr") {
    langUrl = "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json";
  } else {
    langUrl = "//cdn.datatables.net/plug-ins/1.13.6/i18n/en-EN.json";
  }
  var table = $("#list-etude-admin").DataTable({
    destroy: true, // évite l'erreur "Cannot reinitialise"
    language: { url: langUrl },
    columnDefs: [
      {
        targets: 1, // deuxieme colonne
        type: "date-eu", // format jour/mois/année
      },
    ],
    order: [[1, "desc"]],
  });

  let studyIdno = null;
  let studyTitle = "";

  // Clic sur l’icône supprimer → ouvre le modal
  $(".delete-study-btn-nada").on("click", function () {
    studyIdno = $(this).data("study-idno");
    studyTitle = $(this).data("study-title");

    // Mets à jour le texte dans le modal
    $("#deleteStudyModal .modal-body .study-title").text(studyTitle);
    $("#deleteStudyModal .confirm-delete-study").attr(
      "data-study-idno",
      studyIdno
    );

    // Ouvre le modal avec Bootstrap
    $("#deleteStudyModal").show();
  });

  $(".cancel-delete-study").on("click", function () {
    $("#deleteStudyModal").hide();
    studyIdno = null;
  });

  // Clic sur confirmer la suppression
  $(".confirm-delete-study").on("click", function () {
    if (!studyIdno) return;

    const $btn = $(this);
    const $spinner = $btn.find(".spinner-border");
    const $btnText = $btn.find(".btn-text");

    $spinner.removeClass("d-none");
    $btnText.text("Suppression...");

    $.ajax({
      url: study_vars.ajax_url,
      method: "POST",
      data: {
        action: "nada_delete_study",
        study_idno: studyIdno,
        _ajax_nonce: ajax_url.nonce,
      },
      success: function (response) {
        if (response.success) {
          // Supprime la ligne dans le tableau
          showToast("Étude supprimée avec succès !", "success");

          // Récupérer le TR via data-study-idno
          const $row = $(
            `.delete-study-btn-nada[data-study-idno='${studyIdno}']`
          ).closest("tr");

          // Vérifier que la ligne existe
          if ($row.length) {
            table.row($row).remove().draw(false); // draw(false) évite de tout redraw
          } else {
            console.warn(
              "Impossible de trouver la ligne pour studyIdno:",
              studyIdno
            );
          }
        } else {
          showToast(
            response.data?.message || "Erreur lors de la suppression.",
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Erreur AJAX :", error);
        showToast("Problème de connexion au serveur.", "error");
      },
      complete: function () {
        // Ferme le modal
        $("#deleteStudyModal").hide();
        $spinner.addClass("d-none");
        $btnText.text("Supprimer");
      },
    });
  });

  // Clic sur l'icone suitch → ouvre le modal
  $(".publish-study-suitch").on("click", function () {
    const checkbox = $(this);
    const newState = checkbox.is(":checked") ? 1 : 0;
    const actionText = newState == 1 ? "publier" : "dépublier";

    studyIdno = $(this).data("study-idno");
    studyTitle = $(this).data("study-title");

    // Mets à jour le texte dans le modal
    $("#publishStudyModal .modal-body .study-title").text(studyTitle);
    $("#publishStudyModal .modal-body .study-action").text(actionText);
    $("#publishStudyModal .confirm-action-study").attr({
      "data-study-idno": studyIdno,
      "data-state": newState,
    });
    // Ouvre le modal avec Bootstrap
    $("#publishStudyModal").show();
  });

  $(".cancel-action-study").on("click", function () {
    $("#publishStudyModal").hide();
    studyIdno = null;
  });

  $(".confirm-action-study").on("click", function () {
    if (!studyIdno) return;

    const $btn = $(this);
    const $spinner = $btn.find(".spinner-border");
    const $btnText = $btn.find(".btn-text");

    newState = $(this).data("state");

    $spinner.removeClass("d-none");
    $btnText.text(newState == 0 ? "Dépublier..." : "Publier...");

    $.ajax({
      url: study_vars.ajax_url,
      method: "POST",
      data: {
        action: "publish_unpublish_study",
        study_idno: studyIdno,
        study_title: studyTitle,
        published: newState,
        _ajax_nonce: study_vars.nonce,
      },
      success: function (response) {
        if (response.success) {
          const $row = $(
            `.publish-study-suitch[data-study-idno="${studyIdno}"]`
          ).closest("tr");
          const row = table.row($row);
          const $statusCell = $row.find("td").eq(2);

          if (newState == 0) {
    
            // Mettre à jour le statut en "Dépubliée"
            $statusCell.html('<span class="badge bg-danger">Dépubliée</span>');
          } else {
            // Mettre à jour le statut en "Publiée"
            $statusCell.html('<span class="badge bg-success">Publiée</span>');
            $row.find(".reject-study-btn-nada").remove();
          }

          $row.find(".require-study-modifications-btn").remove();

          row.invalidate().draw(false);
          showToast(
            newState
              ? "Étude publiée avec succès !"
              : "Étude dépubliée avec succès !"
          );
        } else {
          showToast(
            response.data?.message || "Erreur lors de la modification.",
            "error"
          );
        }
      },
      error: function () {
        showToast("Problème de connexion au serveur.", "error");
      },
      complete: function () {
        // Ferme le modal
        $("#publishStudyModal").hide();
        $spinner.addClass("d-none");
        $btnText.text("Confirmer");
      },
    });
  });

  // Clic sur l’icône decision → ouvre le modal
  $(".make-study-decision-btn").on("click", function () {
    studyIdno = $(this).data("study-idno");
    studyTitle = $(this).data("study-title");

    // Mets à jour le texte dans le modal
    $("#decisionStudyModal .modal-body .study-title").text(studyTitle);
    $("#decisionStudyModal .confirm-delete-study").attr(
      "data-study-idno",
      studyIdno
    );

    // Ouvre le modal avec Bootstrap
    $("#decisionStudyModal").show();
  });

  $(".cancel-approve-study").on("click", function () {
    $("#decisionStudyModal").hide();
    studyIdno = null;
  });

  // Clic sur approuver l'étude
  $(".approve-disapprove-survey-btn").on("click", function () {
    if (!studyIdno) return;

    const $btn = $(this);
    const $spinner = $btn.find(".spinner-border");
    const $btnText = $btn.find(".btn-text");
    const piDecision = $(this).data("decision");

    $spinner.removeClass("d-none");
    $btnText.text(piDecision == "approve" ? "Approuver..." : "Désapprouver...");

    $.ajax({
      url: study_vars.ajax_url,
      method: "POST",
      data: {
        action: "make_decision_study",
        study_idno: studyIdno,
        decision: piDecision,
        study_title: studyTitle,
        _ajax_nonce: study_vars.nonce,
      },
      success: function (response) {
        if (response.success) {
          // Trouve la ligne correspondant à l'étude
          const $row = $(
            `.make-study-decision-btn[data-study-idno="${studyIdno}"]`
          ).closest("tr");
          const row = table.row($row);

          // Met à jour le badge
          const badgeHtml =
            piDecision === "approve"
              ? '<span class="badge bg-success"><i class="fa fa-check-circle" style="margin-right:2px;"></i> Approuvé</span>'
              : '<span class="badge bg-danger"><i class="fa fa-times-circle" style="margin-right:2px;"></i> Désapprouvé</span>';

          const $cell = $row.find("td").eq(5); // index 0-based
          $cell.html(badgeHtml);
          row.invalidate().draw(false);
          showToast("Decision prise avec succès !");
        } else {
          showToast(
            response.data?.message || "Erreur lors de la prise de décision.",
            "error"
          );
        }
      },
      error: function () {
        showToast("Problème de connexion au serveur.", "error");
      },
      complete: function () {
        // Ferme le modal
        $("#decisionStudyModal").hide();
        $spinner.addClass("d-none");
        $btnText.text(piDecision == "approve" ? "Approuver" : "Désapprouver");
      },
    });
  });

  //click sur l'iccone demende modifications → ouvre le modal
  $(".require-study-modifications-btn").on("click", function () {
    studyIdno = $(this).data("study-idno");
    studyTitle = $(this).data("study-title");

    $("#requestChangesModal .modal-body .study-title").text(studyTitle);
    // Ouvre le modal avec Bootstrap
    $("#requestChangesModal").show();
  });

  // click sur demande de modifications
  $(".confirm-request-changes-study").on("click", function () {
    if (!studyIdno) return;

    const $btn = $(this);
    const $spinner = $btn.find(".spinner-border");
    const $btnText = $btn.find(".btn-text");

    $spinner.removeClass("d-none");
    $btnText.text("Demande...");

    $.ajax({
      url: study_vars.ajax_url,
      method: "POST",
      data: {
        action: "request_changes_study",
        study_idno: studyIdno,
        study_title: studyTitle,
        _ajax_nonce: study_vars.nonce,
      },
      success: function (response) {
        if (response.success) {
          // Trouve la ligne correspondant à l'étude
          const $row = $(
            `.require-study-modifications-btn[data-study-idno="${studyIdno}"]`
          ).closest("tr");
          const row = table.row($row);

          // Mettre à jour le statut (colonne 3 -> index 2)
          const $statusCell = $row.find("td").eq(2);
          $statusCell.html(
            '<span class="badge bg-warning">Modifications requises</span>'
          );

          // Supprimer l’icône "request modifications"
          $row.find(".require-study-modifications-btn").remove();
          row.invalidate().draw(false);
          showToast("Modifications demandés avec succès !");
        } else {
          showToast(
            response.data?.message ||
              "Erreur lors de demande des modifications.",
            "error"
          );
        }
      },
      error: function () {
        showToast("Problème de connexion au serveur.", "error");
      },
      complete: function () {
        // Ferme le modal
        $("#requestChangesModal").hide();
        $spinner.addClass("d-none");
      },
    });
  });

  $(".cancel-modal").on("click", function () {
    $("#requestChangesModal").hide();
    studyIdno = null;
  });

  // Clic sur rejeter une étude
  $(".reject-study-btn-nada").on("click", function () {
    studyIdno = $(this).data("study-idno");
    studyTitle = $(this).data("study-title");

    // Mets à jour le texte dans le modal
    $("#rejectStudyModal .modal-body .study-title").text(studyTitle);

    // Ouvre le modal avec Bootstrap
    $("#rejectStudyModal").show();
  });

  // click sur rejeter une demande
  $(".confirm-reject-study").on("click", function () {
    if (!studyIdno) return;

    const $btn = $(this);
    const $spinner = $btn.find(".spinner-border");
    const $btnText = $btn.find(".btn-text");

    $spinner.removeClass("d-none");
    $btnText.text("Rejet...");

    $.ajax({
      url: study_vars.ajax_url,
      method: "POST",
      data: {
        action: "reject_study",
        study_idno: studyIdno,
        study_title: studyTitle,
        _ajax_nonce: study_vars.nonce,
      },
      success: function (response) {
        if (response.success) {
          // Trouve la ligne correspondant à l'étude
          const $row = $(
            `.reject-study-btn-nada[data-study-idno="${studyIdno}"]`
          ).closest("tr");
          const row = table.row($row);

          // Mettre à jour le statut (colonne 3 -> index 2)
          const $statusCell = $row.find("td").eq(2);
          $statusCell.html('<span class="badge bg-danger">Rejetée</span>');

          // Supprimer l’icône "request modifications"
          $row.find(".reject-study-btn-nada").remove();
          // Supprimer l'icone "publier"
          $row.find(".publish-study-suitch").remove();
          row.invalidate().draw(false);
          showToast("Etude rejetée avec succès !");
        } else {
          showToast(
            response.data?.message || "Erreur lors de rejet d'une etude.",
            "error"
          );
        }
      },
      error: function () {
        showToast("Problème de connexion au serveur.", "error");
      },
      complete: function () {
        // Ferme le modal
        $("#rejectStudyModal").hide();
        $spinner.addClass("d-none");
      },
    });
  });

  // Clic sur rejeter une étude
  $(".cancel-reject-study").on("click", function () {
    $("#rejectStudyModal").hide();
    studyIdno = null;
  });
  // Fonction générique pour afficher un toast
  function showToast(message, type = "success") {
    const toastEl = document.getElementById("study-action-toast");
    const toastBody = document.getElementById("study-action-toast-body");

    if (!toastEl || !toastBody) {
      console.error("Toast non trouvé dans le DOM !");
      return;
    }

    toastBody.textContent = message;
    toastEl.classList.remove("text-bg-success", "text-bg-danger");

    if (type === "success") {
      toastEl.classList.add("text-bg-success");
    } else if (type === "error") {
      toastEl.classList.add("text-bg-danger");
    }

    // Bootstrap doit être chargé et disponible
    if (typeof bootstrap !== "undefined") {
      const bsToast = new bootstrap.Toast(toastEl);
      bsToast.show();
    } 
  }
});

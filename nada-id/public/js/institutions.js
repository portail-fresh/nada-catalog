(function ($) {
    "use strict";

    let currentAction = "add";
    let currentInstitutionId = 0;
    let lang = institutions_vars.lang || "fr";

    function loadTable(wrapper, paged) {
        const searchKey = wrapper.data("search-key");
        const searchTerm = wrapper.find(".dt-search-input").val();
        const contentDiv = wrapper.find(".nada-table-content");
        const perPage = wrapper.find(".dt-per-page-select").val();

        contentDiv.css("opacity", "0.5");

        $.ajax({
            url: institutions_vars.ajax_url,
            type: "POST",
            data: {
                action: "nada_fetch_table",
                search_key: searchKey,
                term: searchTerm,
                paged: paged,
                per_page: perPage,
            },
            success: function (response) {
                contentDiv.html(response);
                contentDiv.css("opacity", "1");
                contentDiv.find(".search-box").remove();
            },
            error: function (xhr, status, error) {
                contentDiv.css("opacity", "1");
            },
        });
    }

    $(document).ready(function () {
        $(document).on("click", ".institution-search-button", function (e) {
            e.preventDefault();
            const wrapper = $(this).closest(".institution-wrapper");
            loadTable(wrapper, 1);
        });

        $(document).on("keypress", ".dt-search-input", function (e) {
            if (e.which === 13 || e.keyCode === 13) {
                e.preventDefault();
                const wrapper = $(this).closest(".institution-wrapper");
                loadTable(wrapper, 1);
            }
        });

        $(document).on("change", ".dt-per-page-select", function (e) {
            const wrapper = $(this).closest(".institution-wrapper");
            loadTable(wrapper, 1);
        });

        $(document).on(
            "click",
            ".institution-wrapper .pagination-links a",
            function (e) {
                e.preventDefault();
                const wrapper = $(this).closest(".institution-wrapper");
                const url = $(this).attr("href");
                let paged = 1;
                const match = url.match(/paged=(\d+)/);

                if (match?.[1]) {
                    paged = parseInt(match[1], 10);
                }
                loadTable(wrapper, paged);
            },
        );

        /** Ouvrir le modal de confirmation pour approuver ou rejeter une institution */
        $(document).on("click", ".btn-action-trigger", function (e) {
            e.preventDefault();
            const $element = $(this);

            currentInstitutionId = $element.data("id");
            currentAction = $element.data("action");
            const nameFr = $element.data("name-fr") || "";
            const nameEn = $element.data("name-en") || "";
            const displayName = nameFr || nameEn || "cette institution";
            const action =
                currentAction === "approve"
                    ? lang === "fr"
                        ? "Approuver"
                        : "Approve"
                    : lang === "fr"
                        ? "Rejeter"
                        : "Reject";

            $("#validateActionModal .institution-name").text(displayName);
            $("#validateActionModal .institution-action").text(action);

            $("#validateActionModal").fadeIn(300);
            $("body").addClass("nada-modal-open");
        });

        /** Fermer le modal de confirmation */
        $(document).on(
            "click",
            ".cancel-action, .nada-modal-close, .nada-modal-overlay",
            function (e) {
                $("#validateActionModal").fadeOut(300);
                $("body").removeClass("nada-modal-open");

                setTimeout(function () {
                    $("#confirmation-content").show();
                    $("#loader").hide();
                    $("#success-message").hide();
                }, 300);

                currentInstitutionId = null;
                currentAction = null;
            },
        );

        /** Confirmer ou rejet une institution */
        $(document).on("click", ".confirm-action", function (e) {
            e.preventDefault();
            if (!currentInstitutionId || !currentAction) return false;

            const $btn = $(this);
            const $spinner = $btn.find(".spinner-border");

            $btn.prop("disabled", true);
            $spinner.removeClass("d-none");
            $("#confirmation-content").hide();
            $("#loader").show();

            $.ajax({
                url: institutions_vars.ajax_url,
                method: "POST",
                data: {
                    action: "nadaUpdateStateInstitution",
                    id: currentInstitutionId,
                    state: currentAction === "approve" ? "approved" : "rejected",
                    _ajax_nonce: institutions_vars.nonce,
                },
                success: function (response) {
                    if (response.success) {
                        $("#loader").hide();
                        $(".message-success-response").text(response.data.message);
                        $("#success-message").show();
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    } else {
                        $("#loader").hide();
                        $("#confirmation-content").show();
                        $btn.prop("disabled", false);
                        $spinner.addClass("d-none");
                    }
                },
                error: function (xhr, status, error) {
                    $("#loader").hide();
                    $("#confirmation-content").show();
                    $btn.prop("disabled", false);
                    $spinner.addClass("d-none");
                },
                complete: function () {
                    currentInstitutionId = null;
                    currentAction = null;
                },
            });
        });

        // ========== AJOUTER / MODIFIER INSTITUTION ==========

        const editableFields = ["descFrIns", "descEnIns", "uriIns", "sirenIns"];
        const readOnlyInEditMode = [
            "labelFrIns",
            "labelEnIns",
            "identifierIns",
            "statusIns",
        ];

        /** Basculer la visibilité et l'état des champs en fonction du mode (ajout ou édition) */
        function toggleFieldsVisibility(isEditMode) {
            readOnlyInEditMode.forEach((fieldName) => {
                const $field = $(`#${fieldName}`);
                if (isEditMode) {
                    $field.prop("readonly", true).prop("required", false);
                } else {
                    $field.prop("readonly", false).prop("required", true);
                }
            });

            editableFields.forEach((fieldName) => {
                const $field = $(`#${fieldName}`);
                $field.prop("readonly", false);
            });
        }

        /**Ajout une institution */
        $(document).on("click", ".btn-add-institution", function (e) {
            e.preventDefault();
            currentAction = "add";
            currentInstitutionId = 0;
            resetForm(); // Réinitialiser tous les champs
            toggleFieldsVisibility(false); // En mode ajout, tous les champs sont éditables

            // Changer le titre du modal
            $("#addEditInstitutionModal .nada-modal-header h3").text(
                lang === "fr" ? "Ajouter une institution" : "Add institution",
            );

            $("#addEditInstitutionModal").fadeIn(300);
            $("body").addClass("nada-modal-open");
        });

        /**Éditer une institution */
        $(document).on("click", ".btn-edit-institution", function (e) {
            e.preventDefault();
            currentAction = "edit";
            currentInstitutionId = $(this).data("id");
            // Remplir tous les champs avec les données
            $("#item-id").val(currentInstitutionId);
            $("#labelFrIns").val($(this).data("label-fr") || "");
            $("#labelEnIns").val($(this).data("label-en") || "");
            $("#descFrIns").val($(this).data("desc-fr") || "");
            $("#descEnIns").val($(this).data("desc-en") || "");
            $("#uriIns").val($(this).data("uri") || "");
            $("#sirenIns").val($(this).data("siren") || "");
            $("#identifierIns").val($(this).data("identifier") || "");
            $("#statusIns").val($(this).data("status") || "");
            toggleFieldsVisibility(true); // En mode édition, certains champs sont en lecture seule

            // Changer le titre du modal
            $("#addEditInstitutionModal .nada-modal-header h3").text(
                lang === "fr" ? "Éditer l'institution" : "Edit institution",
            );

            $("#addEditInstitutionModal").fadeIn(300);
            $("body").addClass("nada-modal-open");
        });

        // Annuler / Fermer le modal
        $(document).on(
            "click",
            ".cancel-action, #addEditInstitutionModal .nada-modal-close",
            function (e) {
                e.preventDefault();
                $("#addEditInstitutionModal").fadeOut(300);
                $("body").removeClass("nada-modal-open");

                setTimeout(function () {
                    $("#form-content").show();
                    $("#loader-add-edit").hide();
                    $("#success-message-edit").hide();
                    resetForm();
                }, 300);
            },
        );

        $(document).on(
            "click",
            "#addEditInstitutionModal .nada-modal-overlay",
            function (e) {
                if (e.target === this) {
                    $("#addEditInstitutionModal").fadeOut(300);
                    $("body").removeClass("nada-modal-open");

                    setTimeout(function () {
                        $("#form-content").show();
                        $("#loader-add-edit").hide();
                        $("#success-message-edit").hide();
                        resetForm();
                    }, 300);
                }
            },
        );

        /** Réinitialiser tous les champs du formulaire et les messages d'erreur */
        function resetForm() {
            $("#item-id").val(0);
            $("#labelFrIns").val("");
            $("#labelEnIns").val("");
            $("#descFrIns").val("");
            $("#descEnIns").val("");
            $("#uriIns").val("");
            $("#sirenIns").val("");
            $("#identifierIns").val("");
            $("#statusIns").val("");
            $(".form-control").removeClass("is-invalid");
            clearErrorText();
        }

        /** Confirmer l'ajout ou la modification d'une institution */

        $(document).on("click", ".confirm-add-edit-ins", function (e) {
            e.preventDefault();
            const $button = $(this);
            const $spinner = $button.find(".spinner-border");

            clearErrorText();

            $button.prop("disabled", true);
            $spinner.removeClass("d-none");

            $("#form-content").hide();
            $("#loader-add-edit").show();

            const formData = {
                action: "nadaSaveInstitution",
                id: $("#item-id").val(), // 0 pour ajout, ID pour édition
                label_fr: $("#labelFrIns").val(),
                label_en: $("#labelEnIns").val(),
                desc_fr: $("#descFrIns").val(),
                desc_en: $("#descEnIns").val(),
                uri: $("#uriIns").val(),
                siren: $("#sirenIns").val(),
                identifier: $("#identifierIns").val(),
                status: $("#statusIns").val(),
                nonce: institutions_vars.nonce,
            };

            $.ajax({
                url: institutions_vars.ajax_url,
                type: "POST",
                data: formData,
                success: function (response) {
                    if (response.success) {
                        $("#loader-add-edit").hide();
                        $(".message-success-response").html(response.data.message);
                        $("#success-message-edit").show();
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    } else {
                        $("#loader-add-edit").hide();
                        $("#form-content").show();
                        showErrorText(response.data.message || "Erreur inconnue");
                        $button.prop("disabled", false);
                        $spinner.addClass("d-none");
                    }
                },
                error: function (xhr, status, error) {
                    $("#loader-add-edit").hide();
                    $("#form-content").show();
                    showErrorText("Erreur : " + error);
                    $button.prop("disabled", false);
                    $spinner.addClass("d-none");
                },
            });
        });

        function showErrorText(message) {
            $("#item-form-error").html(message).removeClass("d-none");
        }

        function clearErrorText() {
            $("#item-form-error").addClass("d-none").html("");
        }
    });
})(jQuery);

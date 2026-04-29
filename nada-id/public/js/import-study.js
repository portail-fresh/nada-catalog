jQuery(document).ready(function ($) {
  $("#idForm").validate({
    submitHandler: function (form, event) {
      event.preventDefault();
      // const fileInput = form.querySelector('input[type="file"]');
      // const file = fileInput.files[0];
      // const fileName = file.name.toLowerCase();
      const $button = $("#submitBtn");
      const $spinner = $button.find(".spinner-border");
      const $btnText = $button.find(".btn-text");
      $button.prop("disabled", true);
      $spinner.removeClass("d-none");
      $btnText.text("Envoi...");
      let formData = new FormData(form);
      formData.append("action", "nada_import_study");
      // Envoi AJAX
      $.ajax({
        url: nada_import_ajax.ajax_url,
        type: "POST",
        dataType: "json",
        processData: false,
        contentType: false,
        data: formData,
        success: function (response) {
          if (response.success) {
            $("#response-success").text(response.message).removeClass("d-none");
            $("#response-error").addClass("d-none");
          } else {
            $("#response-error").text(response.message).removeClass("d-none");
            $("#response-success").addClass("d-none");
          }
        },
        error: function (xhr) {
          $("#response-error")
            .text("Impossible de contacter le serveur.")
            .removeClass("d-none");
          $("#response-success").addClass("d-none");
        },
        complete: function () {
          $button.prop("disabled", false);
        },
      });
      return false; //  bloquer le submit classique
    },
  });
});

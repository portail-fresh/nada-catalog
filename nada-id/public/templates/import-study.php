<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<div class="row mb-4">
  <div class="col-12 d-flex justify-content-end">
    <a href="<?php echo ($current_language === 'fr') ? '/mon-espace' : '/en/my-space/'; ?>" class="link-with-icon link-add">
      <i class="fa fa-arrow-left"></i> <?php echo __('btnBack', 'nada-id'); ?>
    </a>
  </div>
</div>
<form id="idForm" novalidate="novalidate">
  <h2 class="lang-text" data-fr="Import d'un XML / JSON" data-en="Import of an XML / JSON">
    Import d'un XML / JSON
  </h2>

  <input type="file" id="xmlJsonFile" name="xmlJsonFile" accept=".xml,.json" required style="display:none;">

  <label for="xmlJsonFile" class="custom-file-upload lang-text"
    data-fr=" Choisir un fichier"
    data-en="Choose a file">
    Choisir un fichier
  </label>
  <span id="file-name" class="file-name lang-text"
    data-fr="Aucun fichier sélectionné"
    data-en="No file selected">
    Aucun fichier sélectionné
  </span>

  <br><br>

  <button type="submit" class="btn btn-secondary mb-5 mt-5" id="submitBtn" style="border-radius:5px!important">
    <span class="lang-text"
      data-fr="Envoyer"
      data-en="Send">
      Envoyer
    </span>
  </button>
  <div id="form-message" class="mt-3">
  </div>
</form>
<div id="response-success" class="alert alert-success mt-3 d-none"></div>
<div id="response-error" class="alert alert-danger mt-3 d-none"></div>

<script>
  jQuery(document).ready(function($) {
    const lang = (typeof nada_global_vars !== "undefined" && nada_global_vars.lang) ? nada_global_vars.lang : "fr";

    $(".lang-text").each(function() {
      const $el = $(this);
      const fr = $el.attr("data-fr") || "";
      const en = $el.attr("data-en") || "";
      $el.text(lang === "fr" ? fr : en);
    });

    $("#xmlJsonFile").on("change", function() {
      const fileName = this.files.length ? this.files[0].name : "";
      const $fileNameSpan = $("#file-name");

      if (fileName) {
        $fileNameSpan.text(fileName);
      } else {
        const defaultText = lang === "fr" ?
          $fileNameSpan.attr("data-fr") :
          $fileNameSpan.attr("data-en");
        $fileNameSpan.text(defaultText);
      }
    });
  });
</script>
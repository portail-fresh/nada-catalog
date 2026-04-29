<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<form  id="idForm" novalidate="novalidate">
    <h2>Import d'un XML / JSON</h2>
    <input type="file" name="xmlFile" accept=".xml,.json" required>
    <button type="submit" class="btn btn-secondary mb-5 mt-5" id="submitBtn" style="border-radius:5px!important">
        Envoyer
    </button>
    <div id="form-message" class="mt-3">
    </div>
</form>


<div id="response-success" class="alert alert-success mt-3 d-none"></div>
<div id="response-error" class="alert alert-danger mt-3 d-none"></div>
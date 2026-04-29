/**
 * Ce fichier regroupe l’ensemble du JavaScript global du plugin Nada ID
 */
jQuery(document).ready(function ($) {
    $("#page-liste-etude-admin .glossaryLink").removeClass("glossaryLink");
    $("#form-add-study .glossaryLink").removeClass("glossaryLink");
    $(".details-study-fr .glossaryLink").removeClass("glossaryLink");
    $(".details-study-en .glossaryLink").removeClass("glossaryLink");
    $("#page-ref-list-admin .glossaryLink").removeClass("glossaryLink");
    $("#page-ref-details .glossaryLink").removeClass("glossaryLink");

    $("#form-add-study textarea").each(function () {
        let val = $(this).val();
        // Supprimer les balises <span class="glossaryLink">...</span>
        val = val.replaceAll(
            /<span[^>]*class="[^"]*glossaryLink[^"]*"[^>]*>(.*?)<\/span>/gi,
            "$1"
        );
        $(this).val(val);
    });
});

<?php
/*
Template Name: Add Study
*/
?>

<?php
include('add-study-input.php');
?>
<?php
// Code ajouté par la DG afin de détecter le mode et cacher les boutons en bas en consultatiuon
// Détecter le mode depuis plusieurs sources possibles
$mode = $mode
    ?? (get_query_var('mode') ?: '')
    ?? (isset($_GET['mode']) ? sanitize_text_field($_GET['mode']) : '');

$is_detail = (trim(strtolower((string)$mode)) === 'detail');
?>

<div class="modal" tabindex="-1" id="emailStudyModal" tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Adresse email</h5>
                <button type="button" class="btn-close cancel-show-modal-email" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body study-title"></div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-show-modal-email" data-bs-dismiss="modal">Fermer</button>
            </div>

        </div>
    </div>
</div>


<div class="container my-4 form-nada-add w-100">
    <form id="form-add-study">
        <input type="hidden" name="idno" value="<?php echo esc_attr(get_query_var('idno') ?? ''); ?>">
        <div id="stepper">
            <ul class="nav nav-pills m-0 nav nav-pills m-0 d-flex justify-content-end">
                <li class="nav-item langue">
                    <a class="nav-link active" data-lang="fr" href="#">FR</a>
                </li>
                <li class="nav-item langue">
                    <a class="nav-link" data-lang="en" href="#">EN</a>
                </li>
            </ul>
            <!-- Step indicators -->
            <ul class="nav nav-tabs" id="stepperTabs">
                <button class="nav-link active lang-text"
                    id="step-1-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#step-1"
                    type="button"
                    role="tab"
                    aria-controls="step-1"
                    aria-selected="true"
                    data-fr="Renseignements sur le contexte de la collecte des données"
                    data-en="Information on the context of data collection">
                    Renseignements sur le contexte de la collecte des données
                </button>
                <button class="nav-link lang-text"
                    id="step-2-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#step-2"
                    type="button"
                    role="tab"
                    aria-controls="step-2"
                    aria-selected="true"
                    data-fr="Méthodologie de l'étude"
                    data-en="Study methodology">
                    Méthodologie de l'étude
                </button>

                <button class="nav-link lang-text"
                    id="step-3-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#step-3"
                    type="button"
                    role="tab"
                    aria-controls="step-3"
                    aria-selected="true"
                    data-fr="Caractéristiques des données collectées"
                    data-en="Characteristics of collected data">
                    Caractéristiques des données collectées
                </button>

            </ul>

            <!-- Step contents -->
            <div class="tab-content" id="stepperContent">

                <?php
                include('section1.php');
                ?>
                <?php
                include('section2.php');
                ?>
                <?php
                include('section3.php');
                ?>

            </div>

            <!-- Navigation buttons -->
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-secondary lang-text"
                    id="prevBtn"
                    disabled
                    data-fr="Précédent"
                    data-en="Previous">
                    Précédent
                </button>

                <button class="btn btn-primary" type="submit" id="saveAsDraft">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text lang-text"
                        data-fr="Enregistrer brouillon"
                        data-en="Save draft">
                        Enregistrer brouillon
                    </span>

                </button>


                <button class="btn btn-primary lang-text"
                    id="nextBtn"
                    data-fr="Suivant"
                    data-en="Next">
                    Suivant
                </button>

                <!-- <button class="btn btn-success d-none" type="submit" id="finishBtn">Terminer</button> -->
                <button class="btn btn-secondary d-none" type="submit" id="finishBtn" style="border-radius: 8px;">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text lang-text "
                        data-fr="Terminer"
                        data-en="Finish">
                        Terminer
                    </span>
                </button>
            </div>
        </div>
    </form>

    <div id="form-message" class="mt-3"></div>
    <div id="response-error" class="nada-id-error d-none"></div>
    <div id="response-success" class="nada-id-success d-none"></div>
</div>

<!-- condition sur les champs  -->
<script>
    jQuery(document).ready(function($) {
        function toggleOtherField($container) {
            const key = $container.data('parent') || $container.data('section');
            if (!key) return;

            const $other = $container.find(`[data-item="${key}"]`);
            if (!$other.length) return;

            let show = false;

            // 1) Si un select a la valeur "Autre"
            $container.find('select').each(function() {
                const val = String($(this).val() || '').trim().toLowerCase();
                if (val === 'autre' || val === 'autres') show = true;
            });

            // 2) Si une checkbox ou radio cochée a la valeur "Autre"
            if (!show) {
                $container.find('input[type="checkbox"], input[type="radio"]').filter(':checked').each(function() {
                    const val = String($(this).val() || '').trim().toLowerCase();
                    if (val === 'autre' || val === 'autres') show = true;
                });
            }

            if (show) {
                $other.removeClass('d-none');
            } else {
                $other.addClass('d-none');
                $other.find('input, textarea').val('');
            }
        }

        $(document).on('change', '[data-parent] input, [data-parent] select, [data-section] input, [data-section] select', function() {
            const $container = $(this).closest('[data-parent], [data-section]');
            toggleOtherField($container);
        });

        $(function() {
            $('[data-parent], [data-section]').each(function() {
                toggleOtherField($(this));
            });
        });



        $(document).on('change', 'input[name="additional/collaborations/networkConsortium_fr"]', function() {
            if ($(this).val() === 'Oui' && $(this).is(':checked')) {
                $('#CollaborationDetailsPrecision').removeClass('d-none');
            } else {
                $('#CollaborationDetailsPrecision').addClass('d-none');
            }
        });

        $(document).on('change', 'input[name="additional/collaborations/networkConsortium_en"]', function() {
            if ($(this).val() === 'Non' && $(this).is(':checked')) {
                $('#CollaborationDetailsPrecision').removeClass('d-none');
            } else {
                $('#CollaborationDetailsPrecision').addClass('d-none');
            }
        });

        $(document).on('change', 'select[name="stdyDscr/method/notes/subject_researchType_fr"]', function() {
            if ($(this).val() === 'interventionnelle') {
                $('#interventionalStudyBloc').removeClass('d-none');
                $('#observationalStudyBloc').addClass('d-none');
            } else if ($(this).val() === 'observationnelle') {
                $('#observationalStudyBloc').removeClass('d-none');
                $('#interventionalStudyBloc').addClass('d-none');
            } else {
                $('#interventionalStudyBloc').addClass('d-none');
                $('#observationalStudyBloc').addClass('d-none');
            }
        });

        $(document).on('change', 'select[name^="additional/arms/armsType"]', function() {
            const $currentRepeater = $(this).closest('.repeater-item');
            const $otherBloc = $currentRepeater.find('.arm-type-other-bloc');

            if ($(this).val() === 'Autre') {
                $otherBloc.removeClass('d-none');
            } else {
                $otherBloc.addClass('d-none');
                $otherBloc.find('input').val('');
            }
        });




        $(document).on('change', 'select[name="stdyDscr/method/dataColl/timeMeth_fr"]', function() {
            if ($(this).val() === 'Autre') {
                $('#OtherResearchTypeBloc').removeClass('d-none');
            } else {
                $('#OtherResearchTypeBloc').addClass('d-none');
            }

        });

        // Autre mode de collecte 
        $(document).on('change', 'input[name="stdyDscr/method/dataColl/collMode_fr[]"], input[name="stdyDscr/method/dataColl/collMode_en[]"]', function() {
            let checked = $('input[name="stdyDscr/method/dataColl/collMode_fr[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Autre')) {
                $('#CollectionFrequencyBloc').removeClass('d-none');
            } else {
                $('#CollectionFrequencyBloc').addClass('d-none');
            }
        });

        $(document).on('change', 'input[name="stdyDscr/method/dataColl/sampProc_fr[]"]', function() {
            let checked = $('input[name="stdyDscr/method/dataColl/sampProc_fr[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Autre')) {
                $('#stdyDscr_method_dataColl_sampProc_other').removeClass('d-none');
            } else {
                $('#stdyDscr_method_dataColl_sampProc_other').addClass('d-none');
            }
        });

        // Research type EN
        $(document).on('change', 'select[name="stdyDscr/method/notes/subject_researchType_en"]', function() {
            if ($(this).val() === 'interventional') {
                $('#interventionalStudyBloc').removeClass('d-none');
                $('#observationalStudyBloc').addClass('d-none');
            } else if ($(this).val() === 'observational') {
                $('#observationalStudyBloc').removeClass('d-none');
                $('#interventionalStudyBloc').addClass('d-none');
            } else {
                $('#interventionalStudyBloc').addClass('d-none');
                $('#observationalStudyBloc').addClass('d-none');
            }
        });

        // Arms type EN
        $(document).on('change', 'select[name="additional/arms/armsType_en"]', function() {
            if ($(this).val() === 'Other') {
                $('#ArmTypeOtherBloc').removeClass('d-none');
            } else {
                $('#ArmTypeOtherBloc').addClass('d-none');
            }
        });

        // Sampling procedure EN
        $(document).on('change', 'input[name="stdyDscr/method/dataColl/sampProc_en[]"]', function() {
            let checked = $('input[name="stdyDscr/method/dataColl/sampProc_en[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Other')) {
                $('#stdyDscr_method_dataColl_sampProc_other').removeClass('d-none');
            } else {
                $('#stdyDscr_method_dataColl_sampProc_other').addClass('d-none');
            }
        });

        $(document).on('change', 'input[name="stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_fr[]"]', function() {
            let checked = $('input[name="stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_fr[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();




            if (checked.includes('Autre')) {
                $('#RecruitmentSourceOtherBloc').removeClass('d-none');
            } else {
                $('#RecruitmentSourceOtherBloc').addClass('d-none');
            }
        });

        $(document).on('change', 'input[name="additional/activeFollowUp/isActiveFollowUp_fr"]', function() {
            const val = $(this).val();
            if ((val === "Oui" || val === "Yes") && $(this).is(':checked')) {
                $('#FollowUpModalitiesBloc').removeClass('d-none');
            } else {
                $('#FollowUpModalitiesBloc').addClass('d-none');
                $('input[name="stdyDscr/method/notes/subject_followUP_fr[]"]').prop('checked', false);
            }
        });

        $(document).on('change', 'input[name="stdyDscr/method/notes/subject_followUP_fr[]"]', function() {
            let checked = $('input[name="stdyDscr/method/notes/subject_followUP_fr[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Autre')) {
                $('#FollowUpModeOtherBloc').removeClass('d-none');
            } else {
                $('#FollowUpModeOtherBloc').addClass('d-none');
            }
        });

        $(document).on('change', 'select[name="stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I_fr"]', function() {
            if ($(this).val() === 'Autre') {
                $('#OtherPopulationTypeBloc').removeClass('d-none');
            } else {
                $('#OtherPopulationTypeBloc').addClass('d-none');
            }
        });

        // Unit type EN
        $(document).on('change', 'input[name="stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_en[]"]', function() {
            let checked = $('input[name="stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_en[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Other')) {
                $('#RecruitmentSourceOtherBloc').removeClass('d-none');
            } else {
                $('#RecruitmentSourceOtherBloc').addClass('d-none');
            }
        });

        // Active follow-up EN
        $(document).on('change', 'input[name="additional/activeFollowUp/isActiveFollowUp_en"]', function() {
            if ($(this).val() === 'true' && $(this).is(':checked')) {
                $('#FollowUpModalitiesBloc').removeClass('d-none');
            } else {
                $('#FollowUpModalitiesBloc').addClass('d-none');
            }
        });

        // Follow-up subject EN
        $(document).on('change', 'input[name="stdyDscr/method/notes/subject_followUP_en[]"]', function() {
            let checked = $('input[name="stdyDscr/method/notes/subject_followUP_en[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Other')) {
                $('#FollowUpModeOtherBloc').removeClass('d-none');
            } else {
                $('#FollowUpModeOtherBloc').addClass('d-none');
            }
        });

        // Population type EN
        $(document).on('change', 'select[name="stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I_en"]', function() {
            if ($(this).val() === 'Other') {
                $('#OtherPopulationTypeBloc').removeClass('d-none');
            } else {
                $('#OtherPopulationTypeBloc').addClass('d-none');
            }
        });

        $(document).on('change', 'input[name="stdyDscr/method/dataColl/sources/srcOrig_fr[]"], input[name="stdyDscr/method/dataColl/sources/srcOrig_en[]"]', function() {
            const $item = $(this).closest(".repeater-item");

            let checked = $item
                .find('input[name="stdyDscr/method/dataColl/sources/srcOrig_fr[]"]:checked, input[name="stdyDscr/method/dataColl/sources/srcOrig_en[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            const $otherBloc = $item.find('#OtherSourceTypeBloc');

            if (checked.includes('Autre')) {
                $otherBloc.removeClass('d-none');
            } else {
                $otherBloc.addClass('d-none');
            }
        });

        $(document).on('change', 'input[name="stdyDscr/stdyInfo/sumDscr/dataKind_fr[]"]', function() {
            let checked = $('input[name="stdyDscr/stdyInfo/sumDscr/dataKind_fr[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Autre')) {
                $('#dataTypeOtherBloc').removeClass('d-none');
            } else {
                $('#dataTypeOtherBloc').addClass('d-none');
            }
        });

        // Data kind EN
        $(document).on('change', 'input[name="stdyDscr/stdyInfo/sumDscr/dataKind_en[]"]', function() {
            let checked = $('input[name="stdyDscr/stdyInfo/sumDscr/dataKind_en[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Other')) {
                $('#dataTypeOtherBloc').removeClass('d-none');
            } else {
                $('#dataTypeOtherBloc').addClass('d-none');
            }
        });

        $(document).on('change', 'select[name="stdyDscr/citation/IDno/agentSchema_en"]', function() {
            if ($(this).val() === 'Other') {
                $('#pidValue').removeClass('d-none');
            } else {
                $('#pidValue').addClass('d-none');
            }
        });

        $(document).on('change', 'select[name="stdyDscr/citation/IDno/agentSchema_fr"]', function() {
            if ($(this).val() === 'Autre') {
                $('#pidValue').removeClass('d-none');
            } else {
                $('#pidValue').addClass('d-none');
            }
        });


        // FR
        $(document).on('change', 'input[name="additional/dataCollection/inclusionStrategy_fr[]"]', function() {
            let checked = $('input[name="additional/dataCollection/inclusionStrategy_fr[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Autre')) {
                $('#inclusionStrategyOtherBloc').removeClass('d-none');
            } else {
                $('#inclusionStrategyOtherBloc').addClass('d-none');
            }
        });

        // EN
        $(document).on('change', 'input[name="additional/dataCollection/inclusionStrategy_en[]"]', function() {
            let checked = $('input[name="additional/dataCollection/inclusionStrategy_en[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Other')) {
                $('#inclusionStrategyOtherBloc').removeClass('d-none');
            } else {
                $('#inclusionStrategyOtherBloc').addClass('d-none');
            }
        });



        $(document).on('change', 'input[name="additional/dataCollectionIntegration/isDataIntegration_fr"], input[name="additional/dataCollectionIntegration/isDataIntegration_en"]', function() {
            const val = $(this).val();

            if ((val === "Oui" || val === "Yes") && $(this).is(':checked')) {
                $('#ThirdPartySourceBloc').removeClass('d-none');
            } else {
                $('#ThirdPartySourceBloc').addClass('d-none');
            }
        });

        $(document).on('change', 'input[name="stdyDscr/stdyInfo/sumDscr/dataKind_fr[]"]', function() {
            let checked = $('input[name="stdyDscr/stdyInfo/sumDscr/dataKind_fr[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Données cliniques')) {
                $('#ClinicalDataDetailsBloc').removeClass('d-none');
            } else {
                $('#ClinicalDataDetailsBloc').addClass('d-none');
            }
        });


        $(document).on('change', 'input[name="stdyDscr/stdyInfo/sumDscr/dataKind_en[]"]', function() {
            let checked = $('input[name="stdyDscr/stdyInfo/sumDscr/dataKind_en[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Clinical data')) {
                $('#ClinicalDataDetailsBloc').removeClass('d-none');
            } else {
                $('#ClinicalDataDetailsBloc').addClass('d-none');
            }
        });


        $(document).on('change', 'input[name="stdyDscr/stdyInfo/sumDscr/dataKind_fr[]"]', function() {
            let checked = $('input[name="stdyDscr/stdyInfo/sumDscr/dataKind_fr[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Données biologiques')) {
                $('#BiologicalDataDetailsBloc').removeClass('d-none');
                $('#IsDataInBiobankBloc').removeClass('d-none');
            } else {
                $('#BiologicalDataDetailsBloc').addClass('d-none').find('input').val('');
                $('#IsDataInBiobankBloc').addClass('d-none').find('select').val('');
                $('#BiobankContentBloc').addClass('d-none')
                    .find('input[type="checkbox"]').prop('checked', false);
            }
        });


        $(document).on('change', 'input[name="stdyDscr/stdyInfo/sumDscr/dataKind_en[]"]', function() {
            let checked = $('input[name="stdyDscr/stdyInfo/sumDscr/dataKind_en[]"]:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();

            if (checked.includes('Biological data')) {
                $('#BiologicalDataDetailsBloc').removeClass('d-none');
                $('#IsDataInBiobankBloc').removeClass('d-none');
            } else {
                $('#BiologicalDataDetailsBloc').addClass('d-none').find('input').val('');
                $('#IsDataInBiobankBloc').addClass('d-none').find('select').val('');
                $('#BiobankContentBloc').addClass('d-none')
                    .find('input[type="checkbox"]').prop('checked', false);
            }
        });



        $(document).on('change', 'select[name="additional/intervention/interventionType_fr"]', function() {
            if ($(this).val() === 'Autre') {
                $('#InterventionTypeOtherBloc').removeClass('d-none');
            } else {
                $('#InterventionTypeOtherBloc').addClass('d-none');
            }
        });

        $(document).on('change', 'select[name="additional/intervention/interventionType_en"]', function() {
            if ($(this).val() === 'Other') {
                $('#InterventionTypeOtherBloc').removeClass('d-none');
            } else {
                $('#InterventionTypeOtherBloc').addClass('d-none');
            }
        });
        $(document).on('change', 'select[name="additional/intervention/interventionType_fr"], select[name="additional/intervention/interventionType_en"]', function() {
            let $select = $(this);
            let $repeaterItem = $select.closest('.repeater-item'); // scope to this block
            let $otherBloc = $repeaterItem.find('.intervention-type-other');

            if ($select.val() === 'Autre' || $select.val() === 'Other') {
                $otherBloc.removeClass('d-none');
            } else {
                $otherBloc.addClass('d-none');
            }
        });

        $(document).on('change', 'select[name="additional/dataTypes/isDataInBiobank_fr"]', function() {
            if ($(this).val() === 'true') {
                $('#BiobankContentBloc').removeClass('d-none');
            } else {
                $('#BiobankContentBloc').addClass('d-none');
            }
        });

        $(document).on('change', 'select[name="additional/dataTypes/isDataInBiobank_en"]', function() {
            if ($(this).val() === 'true') {
                $('#BiobankContentBloc').removeClass('d-none');
            } else {
                $('#BiobankContentBloc').addClass('d-none');
            }
        });







    });
</script>
<!-- repeater -->
<script>
    jQuery(document).ready(function($) {

        /** ==========================
         *  Gestion des champs multilingues
         *  (attaché une seule fois pour tout le document)
         * ========================== */
        $("body").on("change", ".lang-input input[type=radio], .lang-input input[type=text]", function(e, triggeredBySync) {
            if (triggeredBySync) return;

            const $field = $(this);
            const $container = $field.closest(".repeater-item, .lang-input");
            const lng = $container.attr("attr-lng");
            const otherLng = lng === "fr" ? "en" : "fr";

            const baseName = $field.attr("name").replace(/_(fr|en)(\[\])?$/, "");
            const selector = `.lang-input[attr-lng="${otherLng}"] [name="${baseName}_${otherLng}${$field.prop("multiple") ? "[]" : ""}"]`;
            const $other = $container.parent().find(selector);

            if ($other.length) {
                if ($field.is("select")) {
                    //$other.val($field.val()).trigger("change", [true]);
                } else if ($field.is("input[type=radio]")) {
                    const val = $field.val();
                    const $other = $(selector + `[value="${val}"]`);
                    if ($other.length) {
                        $other.prop("checked", true).trigger("change", [true]);
                    }
                } else {
                    $other.val($field.val());
                }
            }
        });


        // same text comme nom et prenom 
        // on a ajouter la classe same-data pour connaitre les champs a traiter
        $("body").on("input", "input[type=text].lang-input.same-data", function(e, triggeredBySync) {
            if (triggeredBySync) return;

            const $field = $(this);
            const lng = $field.attr("attr-lng"); // l'input lui-même a attr-lng="fr"
            const otherLng = lng === "fr" ? "en" : "fr";

            // on enlève le suffixe _fr ou _en pour retrouver le "baseName"
            const baseName = $field.attr("name").replace(/_(fr|en)(\[\])?$/, "");

            // construire le sélecteur du champ "autre langue"
            const selector = `input[type=text].lang-input[attr-lng="${otherLng}"][name="${baseName}_${otherLng}"]`;
            const $other = $(selector);

            if ($other.length) {
                $other.val($field.val()).trigger("input", [true]);
            }
        });

        //checkbox : on va travailler avec la position du name.
        $("body").on("change", ".input-group-prefix.lang-input input[type=checkbox]", function(e, triggeredBySync) {
            if (triggeredBySync) return;

            const $checkbox = $(this);
            const $container = $checkbox.closest(".input-group-prefix.lang-input");
            const lng = $container.attr("attr-lng");
            const otherLng = lng === "fr" ? "en" : "fr";

            // trouver toutes les checkbox du même groupe (FR ou EN)
            const $all = $container.find("input[type=checkbox]");
            const index = $all.index($checkbox); // position de la checkbox sélectionnée

            // trouver le container de l'autre langue
            const $otherContainer = $container.siblings(`.input-group-prefix.lang-input[attr-lng="${otherLng}"]`);
            const $otherCheckboxes = $otherContainer.find("input[type=checkbox]");

            const $other = $otherCheckboxes.eq(index); // checkbox à la même position

            if ($other.length) {
                $other.prop("checked", $checkbox.prop("checked")).trigger("change", [true]);
            }
        });


        $("body").on("change", ".lang-input select", function(e, triggeredBySync) {
            if (triggeredBySync) return;

            const $select = $(this);

            const $container = $select.closest(".lang-input");
            const lng = $container.attr("attr-lng");
            const otherLng = lng === "fr" ? "en" : "fr";

            // trouver le container de l'autre langue
            const $otherContainer = $container.siblings(`.lang-input[attr-lng="${otherLng}"]`);
            const $otherSelects = $otherContainer.find("select");

            // position du select dans le container
            const index = $container.find("select").index($select);
            const $otherSelect = $otherSelects.eq(index);

            if ($otherSelect.length) {
                // récupérer l'index sélectionné
                const selectedIndex = $select.prop("selectedIndex");

                if (selectedIndex >= 0) {
                    $otherSelect.prop("selectedIndex", selectedIndex).trigger("change", [true]);
                }
            }
        });

        // Synchronisation radios multilangues par position
        $("body").on("change", ".lang-input input[type=radio]", function(e, triggeredBySync) {
            if (triggeredBySync) return;

            const $radio = $(this);
            const $container = $radio.closest(".lang-input");
            const lng = $container.attr("attr-lng");
            const otherLng = lng === "fr" ? "en" : "fr";

            // trouver le container de l'autre langue
            const $otherContainer = $container.closest(".input-group").find(`.lang-input[attr-lng="${otherLng}"]`);
            if (!$otherContainer.length) return;

            // récupérer toutes les radios dans le même container
            const $allRadios = $container.find('input[type=radio]');
            const index = $allRadios.index($radio);

            // récupérer la radio correspondante à la même position
            const $otherRadios = $otherContainer.find('input[type=radio]');
            const $otherRadio = $otherRadios.eq(index);

            if ($otherRadio.length) {
                $otherRadio.prop("checked", $radio.prop("checked")).trigger("change", [true]);
            }
        });

        /** ==========================
         *  Gestion des tabs de langue
         * ========================== */
        function initLangTabs(container = document) {
            const groups = container.querySelectorAll(".input-group");


            groups.forEach((group) => {
                const inputs = group.querySelectorAll(".lang-input");
                const labels = group.querySelectorAll(".lang-label");
                const tabs = group.querySelectorAll(".nav-link");
                const statusTag = group.querySelector(".badge");


                tabs.forEach((tab) => {
                    tab.addEventListener("click", (e) => {
                        e.preventDefault();
                        tabs.forEach((t) => t.classList.remove("active"));
                        tab.classList.add("active");

                        const lang = tab.getAttribute("data-lang");
                        inputs.forEach((input) => input.getAttribute("attr-lng") === lang ?
                            input.classList.remove("d-none") :
                            input.classList.add("d-none"));

                        labels.forEach((label) => label.getAttribute("attr-lng") === lang ?
                            label.classList.remove("d-none") :
                            label.classList.add("d-none"));
                    });
                });

                const updateStatus = () => {
                    const allFilled = Array.from(inputs).every((i) => {
                        if (!i) return false;
                        i.value.trim() !== ""
                    });
                    if (statusTag) {
                        statusTag.textContent = allFilled ? "Completed" : "Incomplet";
                        statusTag.classList.remove("bg-success", "bg-warning");
                        statusTag.classList.add(allFilled ? "bg-success" : "bg-warning");
                    }

                };

            });
        }

        initLangTabs();


        /** ==========================
         *  Gestion des repeaters
         * ========================== */
        function initRepeater(repeaterId, addBtnId) {
            const repeater = document.getElementById(repeaterId);
            const addBtn = document.getElementById(addBtnId);
            if (!repeater || !addBtn) return;

            function attachRemoveHandler(btn) {
                btn.addEventListener("click", function() {
                    const item = btn.closest(".repeater-item");
                    if (item && repeater.querySelectorAll(".repeater-item").length > 1) {
                        item.remove();
                    }
                });
            }

            const firstItem = repeater.querySelector(".repeater-item");
            if (firstItem) {
                const firstBtn = firstItem.querySelector(".btn-remove");
                if (firstBtn) firstBtn.style.display = "none";
            }

            repeater.querySelectorAll(".btn-remove").forEach(btn => attachRemoveHandler(btn));

            addBtn.addEventListener("click", function() {
                const firstItem = repeater.querySelector(".repeater-item");
                if (!firstItem) return;

                const clone = firstItem.cloneNode(true);
                clone.dataset.clone = "true";

                // Supprimer les spans Select2
                clone.querySelectorAll("span.select2").forEach(span => span.remove());

                // Réinitialiser selects
                $(clone).find("select").each(function() {
                    $(this).val("").select2();
                });

                let repeaterCounter = 0;


                // Réinitialiser les champs 
                clone.querySelectorAll("input").forEach((input, index) => {

                    if (input.type === "radio" || input.type === "checkbox") {
                        input.checked = false;

                        if (input.id && input.id.trim() !== "") {
                            const newId = input.id + "_clone_" + Date.now() + "_" + index;

                            const oldId = input.id;
                            input.id = newId;

                            const label = clone.querySelector(`label[for="${oldId}"]`);
                            if (label) {
                                label.setAttribute("for", newId);
                            }
                        }
                    } else {
                        input.value = "";
                    }
                });

                clone.querySelectorAll("textarea").forEach(textarea => {
                    textarea.value = "";
                });

                // Masquer les champs "Autre, précision" dans le clone
                clone.querySelectorAll('[data-item="otherAuthorizingAgency"], [data-item="ThirdPartySource"], [data-item="SchemapidValue"], [data-item="InterventionTypeOtherBloc"]').forEach(el => {
                    el.classList.add("d-none");
                    const input = el.querySelector("input");
                    if (input) input.value = "";
                });

                // Réinitialiser tous les repeaters enfants à l'intérieur du clone
                clone.querySelectorAll(".repeaterBlock").forEach(childRepeater => {
                    const childItems = childRepeater.querySelectorAll(".repeater-item");

                    childItems.forEach((item, index) => {
                        if (index > 0) {
                            item.remove();
                        } else {
                            item.querySelectorAll("input").forEach(input => input.value = "");
                            $(item).find("select").each(function() {
                                $(this).val("").select2();
                            });

                            const btnRemove = item.querySelector(".btn-remove");
                            if (btnRemove) btnRemove.style.display = "none";
                        }
                    });
                });

                // Bouton supprimer
                const btn = clone.querySelector(".btn-remove");
                if (btn) {
                    btn.style.display = "inline-flex";
                    attachRemoveHandler(btn);
                }

                repeater.appendChild(clone);

                // Ré-initialiser tabs uniquement pour ce clone
                initLangTabs(clone);
            });
        }

        // Appel
        initRepeater("repeater-standards", "add-standard");
        initRepeater("repeater-fundingAgent", "add-fundingAgent");
        initRepeater("repeater-ObtainedAuthorization", "add-ObtainedAuthorization");
        initRepeater("repeater-brasArm", "add-brasArm");
        initRepeater("repeater-intervExpo", "add-intervExpo");
        initRepeater("repeater-inclusionGroups", "add-inclusionGroups");
        initRepeater("repeater-sources", "add-sources");
        initRepeater("repeater-PrimaryInvestigator", "add-PrimaryInvestigator");
        initRepeater("repeater-PersonPIDContributor", "add-PersonPIDContributor");
        initRepeater("repeater-ContactPoint", "add-ContactPoint");
        initRepeater("repeater-Contributor", "add-Contributor");
        initRepeater("repeater-DataQuality", "add-DataQuality");
        initRepeater("repeater-DataInformationContact", "add-DataInformationContact");
        initRepeater("repeater-DatasetPID", "add-DatasetPID");
    });
    // Exemple d'utilisation pour plusieurs repeaters
</script>


<script>
    jQuery(document).ready(function($) {
        $("#nextBtn, #prevBtn").on("click", function() {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    });
</script>

<?php if ($is_detail): ?>
    <style>
        /*Code ajouté par la DG afin de détecter le mode et cacher les boutons en bas en consultatiuon*/
        #nextBtn,
        #finishBtn {
            display: none !important;
        }
    </style>
    <script>
        jQuery(function($) {
            $('#nextBtn, #finishBtn, #prevBtn, #saveAsDraft').addClass('d-none').prop('disabled', true).hide();
        });
    </script>
<?php endif; ?>
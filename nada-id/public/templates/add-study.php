<?php
/*
Template Name: Add Study
*/
?>

<?php
include('add-study-input.php');


$data = get_query_var('nada_contribute_study_data', []);
?>

<div id="nada_contribute_study_data"
    data-config="<?php echo esc_attr(wp_json_encode($data)); ?>">
</div>

<div class="my-4 form-nada-add w-100">
    <form id="form-add-study" novalidate>
        <input type="hidden" name="idno" value="<?php echo esc_attr(get_query_var('idno') ?? ''); ?>">
        <input type="hidden" name="status-key" value="<?php echo $statusKey ?? "draft"; ?>">
        <input type="hidden" name="created-by" value="<?php echo $contributorCreatedBy ?? ""; ?>">
        <input type="hidden" name="published" value="<?php echo $published ?? 0; ?>">
        <div id="stepper">
            <ul class="nav nav-pills m-0 nav nav-pills d-flex justify-content-end">
                <li class="nav-item langue">
                    <a class="nav-link <?php echo $current_language === 'fr' ? 'active' : ''; ?>" data-lang="fr" href="#">FR</a>
                </li>
                <li class="nav-item langue">
                    <a class="nav-link <?php echo $current_language === 'en' ? 'active' : ''; ?>" data-lang="en" href="#">EN</a>
                </li>
            </ul>
            <!-- Step indicators -->
            <div class="tabs-container">
                <ul class="nav nav-tabs flex-nowrap" id="stepperTabs">
                    <button class="nav-link active lang-text"
                        id="step-1-tab"
                        data-bs-target="#step-1"
                        type="button"

                        data-fr="Informations relatives à l'étude"
                        data-en="Study related informations"
                        data-icon="fa fa-file-text">
                        <span class="label-text">Informations relatives à l'étude</span>
                    </button>
                    <button class="nav-link lang-text"
                        id="step-2-tab"
                        data-bs-target="#step-2"
                        type="button"

                        data-fr="Renseignements administratifs"
                        data-en="Administrative information"
                        data-icon="fa fa-building">
                        <span class="label-text">Renseignements administratifs</span>
                    </button>

                    <button class="nav-link lang-text"
                        id="step-3-tab"
                        data-bs-target="#step-3"
                        type="button"

                        data-fr="Méthodologie de l'étude"
                        data-en="Study Methodology"
                        data-icon="fa fa-cogs">
                        <span class="label-text">Méthodologie de l'étude</span>
                    </button>

                    <button class="nav-link lang-text"
                        id="step-4-tab"
                        data-bs-target="#step-4"
                        type="button"

                        data-fr="Collecte et accès aux données"
                        data-en="Data collection and access"
                        data-icon="fa fa-database">
                        <span class="label-text">Collecte et accès aux données</span>
                    </button>

                </ul>
            </div>

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
                <?php
                include('section4.php');
                ?>
            </div>
            <!-- Navigation buttons -->
            <div class="d-flex justify-content-between mt-3 displayBtnSmall">
                <button class="btn btn-secondary lang-text"
                    id="prevBtn"
                    type="button"
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
                    type="button"
                    data-fr="Suivant"
                    data-en="Next">
                    Suivant
                </button>
                <button class="btn btn-secondary d-none" type="submit" id="finishBtn" style="border-radius: 8px;">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text lang-text "
                        data-fr="Terminer"
                        data-en="Finish">
                        Terminer
                    </span>
            </div>
        </div>
    </form>

    <div id="form-message" class="mt-3"></div>
    <div id="response-error" class="nada-id-error d-none"></div>
    <div id="response-success" class="nada-id-success d-none"></div>
</div>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
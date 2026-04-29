<?php
/*
Template Name: Add Study section 1
*/

// init options boolean
$options_boolean = [
    "fr" => [
        "Oui" => "Oui",
        "Non" => "Non",
    ],
    "en" => [
        "Yes" => "Yes",
        "No" => "No",
    ],
];
?>


<div class="tab-pane show active" id="step-1">
    <section key="CollectContext">
        <h2 class="lang-text" data-fr="Informations relatives à l'étude" data-en="Study related informations">
            Informations relatives à l'étude</h2>

        <section key="StudyOverview">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                    aria-controls="collapseOne" data-fr="Présentation de l'étude" data-en="Study overview">
                    Présentation de l'étude
                </button>
                <div class="collapse show" id="collapseOne">
                    <div class="card card-body">
                        <!-- Section General -->
                        <section key="General">
                            <?php
                            $tooltipFr = getTooltipByName('StudyOverview', 'fr', 'StudyRelatedInfo');
                            $tooltipEn = getTooltipByName('StudyOverview', 'en', 'StudyRelatedInfo'); ?>
                            <h3 class="lang-text-tooltip"
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Présentation de l'étude"
                                data-en="Study overview">
                                <span class="contentSection">Présentation de l'étude</span>
                                <span class="info-bulle"
                                    attr-lng="fr"
                                    data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                            </h3>
                            <div class="row">
                                <div class="col-md-6 col-sm-12 mb-3 relative">
                                    <?php
                                    $tooltipFr = getTooltipByName('Title', 'fr', 'StudyOverview');
                                    $tooltipEn = getTooltipByName('Title', 'en', 'StudyOverview');
                                    nada_renderInputGroup(
                                        "Titre de l'étude",
                                        "Study title",
                                        "stdyDscr/citation/titlStmt/titl",
                                        "textarea",
                                        [],
                                        true,
                                        true,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        500
                                    ); ?>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <?php
                                    $tooltipFr = getTooltipByName('Acronym', 'fr', 'StudyOverview');
                                    $tooltipEn = getTooltipByName('Acronym', 'en', 'StudyOverview');
                                    nada_renderInputGroup(
                                        "Acronyme de l'étude",
                                        "Study acronym",
                                        "stdyDscr/citation/titlStmt/altTitl",
                                        "textarea",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        200
                                    ); ?>
                                </div>
                                <div class="col-md-12 mb-3" data-key="StudyStatus">
                                    <?php
                                    $StudyStatus = getReferentielByName('StudyStatus');
                                    $tooltipFr = getTooltipByName('StudyStatus', 'fr', 'StudyOverview');
                                    $tooltipEn = getTooltipByName('StudyStatus', 'en', 'StudyOverview');
                                    nada_renderInputGroup(
                                        "Statut actuel de l'étude",
                                        "Current study status",
                                        "stdyDscr/method/stdyClas",
                                        "select",
                                        $StudyStatus,
                                        true,
                                        true,
                                        $tooltipFr,
                                        $tooltipEn
                                    );
                                    ?>
                                </div>
                                <div class="col-md-12 mb-3 allow-enter">
                                    <?php
                                    $tooltipFr = getTooltipByName('Purpose', 'fr', 'StudyOverview');
                                    $tooltipEn = getTooltipByName('Purpose', 'en', 'StudyOverview');
                                    nada_renderInputGroup(
                                        "Objectifs de l'étude",
                                        "Study objectives",
                                        "stdyDscr/stdyInfo/purpose/value",
                                        "textarea",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        5000
                                    ); ?>
                                </div>

                                <div class="col-md-12 mb-3 allow-enter">
                                    <?php
                                    $tooltipFr = getTooltipByName('Summary', 'fr', 'StudyOverview');
                                    $tooltipEn = getTooltipByName('Summary', 'en', 'StudyOverview');
                                    nada_renderInputGroup(
                                        "Résumé de l'étude",
                                        "Study summary",
                                        "stdyDscr/stdyInfo/abstract/value",
                                        "textarea",
                                        [],
                                        true,
                                        true,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        5000
                                    ); ?>
                                </div>


                                <div class="col-md-12 mb-3">
                                    <span></span>
                                    <?php
                                    $tooltipFr = getTooltipByName('Keyword', 'fr', 'StudyOverview');
                                    $tooltipEn = getTooltipByName('Keyword', 'en', 'StudyOverview');
                                    nada_renderInputGroup(
                                        "Mots-clés libres",
                                        "Free keywords",
                                        "stdyDscr/stdyInfo/subject/keyword",
                                        "tags",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        "A séparer par un point-virgule, ou entrée",
                                        "To separate by a semicolon",
                                        500
                                    );
                                    ?>
                                </div>


                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>

        <section key="Theme">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseTwo"
                    aria-expanded="true"
                    aria-controls="collapseTwo"
                    data-fr="Thématique"
                    data-en="Theme">
                    Thématique
                </button>

                <div class="collapse show" id="collapseTwo">
                    <div class="card card-body">
                        <section key="Theme">
                            <?php
                            $tooltipFr = getTooltipByName('Theme', 'fr', 'StudyRelatedInfo');
                            $tooltipEn = getTooltipByName('Theme', 'en', 'StudyRelatedInfo'); ?>
                            <h3 class="lang-text-tooltip"
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Thématique"
                                data-en="Theme">
                                <span class="contentSection">Thématique</span>
                                <span class="info-bulle"
                                    attr-lng="fr"
                                    data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                            </h3>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <?php
                                    $tooltipFr = getTooltipByName('isHealthTheme', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('isHealthTheme', 'en', 'Theme');
                                    nada_renderInputGroup(
                                        "L'étude porte-t-elle sur une ou plusieurs spécialité(s) médicale(s) ?",
                                        "Does the study cover one or more medical speciality(ies)?",
                                        "additional/isHealthTheme",
                                        "radio",
                                        $options_boolean,
                                        true,
                                        true,
                                        $tooltipFr,
                                        $tooltipEn
                                    );
                                    ?>
                                </div>
                                <div class="col-md-12 mb-3 d-none" id="HealthTheme" data-key="HealthTheme">
                                    <?php
                                    $HealthTheme = getReferentielByName('HealthTheme', true);
                                    $tooltipFr = getTooltipByName('HealthTheme', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('HealthTheme', 'en', 'Theme');
                                    nada_renderInputGroup2(
                                        "Spécialité(s) médicale(s) concernée(s)",
                                        "Medical speciality(ies) concerned:",
                                        "stdyDscr/stdyInfo/subject/topcClas[]/value/health theme",
                                        "checkbox",
                                        $HealthTheme,
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn
                                    );
                                    ?>
                                    <input type="hidden" name="stdyDscr/stdyInfo/subject/topcClas[]/vocab/health theme"
                                        value="health theme" />
                                </div>

                                <div class="col-md-12 mb-3 d-none" id="OtherHealthTheme">
                                    <?php
                                    $tooltipFr = getTooltipByName('OtherHealthTheme', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('OtherHealthTheme', 'en', 'Theme');
                                    nada_renderInputGroup(
                                        "Autre spécialité médicale, précisions",
                                        "Other medical specialty, details",
                                        "stdyDscr/stdyInfo/subject/topcClas[]/value/other health theme",
                                        "textarea",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        1000
                                    );
                                    ?>
                                    <input type="hidden"
                                        name="stdyDscr/stdyInfo/subject/topcClas[]/vocab/other health theme"
                                        value="other health theme" />
                                </div>

                                <div class="col-md-12 mb-3" data-key="HealthDeterminant">
                                    <?php
                                    //options to change
                                    $HealthDeterminant = getReferentielByName('HealthDeterminant');
                                    $tooltipFr = getTooltipByName('HealthDeterminant', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('HealthDeterminant', 'en', 'Theme');
                                    nada_renderInputGroup(
                                        "Déterminants de santé",
                                        "Health determinants",
                                        "stdyDscr/stdyInfo/subject/topcClas[]/value/health determinant",
                                        "checkbox",
                                        $HealthDeterminant,
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn
                                    );
                                    ?>
                                    <input type="hidden"
                                        name="stdyDscr/stdyInfo/subject/topcClas[]/vocab/health determinant"
                                        value="health determinant" />
                                </div>

                                <div class="col-md-12 mb-3 d-none" id="OtherSocioDemoDeterminant">
                                    <?php
                                    $tooltipFr = getTooltipByName('OtherSocioDemoDeterminant', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('OtherSocioDemoDeterminant', 'en', 'Theme');
                                    nada_renderInputGroup(
                                        "Autres déterminants socio-démographiques et économiques, précisions",
                                        "Other socio-demographic and economic determinants, details",
                                        "stdyDscr/stdyInfo/subject/topcClas[]/value/other socio demographic determinant",
                                        "textarea",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        1000
                                    );
                                    ?>
                                    <input type="hidden"
                                        name="stdyDscr/stdyInfo/subject/topcClas[]/vocab/other socio demographic determinant"
                                        value="other socio demographic determinant" />
                                </div>


                                <div class="col-md-12 mb-3 d-none" id="OtherEnvironmentalDeterminant">
                                    <?php
                                    $tooltipFr = getTooltipByName('OtherEnvironmentalDeterminant', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('OtherEnvironmentalDeterminant', 'en', 'Theme');
                                    nada_renderInputGroup(
                                        "Autres déterminants environnementaux, précisions",
                                        "Other environmental determinants, details",
                                        "stdyDscr/stdyInfo/subject/topcClas[]/value/other environmental determinant",
                                        "textarea",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        1000
                                    );
                                    ?>
                                    <input type="hidden"
                                        name="stdyDscr/stdyInfo/subject/topcClas[]/vocab/other environmental determinant"
                                        value="other environmental determinant" />
                                </div>

                                <div class="col-md-12 mb-3 d-none" id="OtherHealthcarSystemDeterminant">
                                    <?php
                                    $tooltipFr = getTooltipByName('OtherHealthcarSystemDeterminant', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('OtherHealthcarSystemDeterminant', 'en', 'Theme');
                                    nada_renderInputGroup(
                                        "Autres déterminants liés au système de santé, précisions",
                                        "Other healthcare system determinants, details",
                                        "stdyDscr/stdyInfo/subject/topcClas[]/value/other healthcare determinant",
                                        "textarea",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        1000
                                    );
                                    ?>
                                    <input type="hidden"
                                        name="stdyDscr/stdyInfo/subject/topcClas[]/vocab/other healthcare determinant"
                                        value="other healthcare determinant" />
                                </div>

                                <div class="col-md-12 mb-3 d-none" id="OtherBehavioralDeterminant">
                                    <?php
                                    $tooltipFr = getTooltipByName('OtherBehavioralDeterminant', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('OtherBehavioralDeterminant', 'en', 'Theme');
                                    nada_renderInputGroup(
                                        "Autres déterminants comportementaux, précisions",
                                        "Other behavioral determinants, details",
                                        "stdyDscr/stdyInfo/subject/topcClas[]/value/other behavioural determinant",
                                        "textarea",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        1000
                                    );
                                    ?>
                                    <input type="hidden"
                                        name="stdyDscr/stdyInfo/subject/topcClas[]/vocab/other behavioural determinant"
                                        value="other behavioural determinant" />
                                </div>

                                <div class="col-md-12 mb-3 d-none" id="OtherBiologicalDeterminant">
                                    <?php
                                    $tooltipFr = getTooltipByName('OtherBiologicalDeterminant', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('OtherBiologicalDeterminant', 'en', 'Theme');
                                    nada_renderInputGroup(
                                        "Autres déterminants biologiques, précisions",
                                        "Other biological determinants, details",
                                        "stdyDscr/stdyInfo/subject/topcClas[]/value/other biological determinant",
                                        "textarea",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        1000
                                    );
                                    ?>
                                    <input type="hidden"
                                        name="stdyDscr/stdyInfo/subject/topcClas[]/vocab/other biological determinant"
                                        value="other biological determinant" />
                                </div>


                                <div class="col-md-12 mb-3 d-none" id="OtherDeterminant">
                                    <?php
                                    $tooltipFr = getTooltipByName('OtherDeterminant', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('OtherDeterminant', 'en', 'Theme');
                                    nada_renderInputGroup(
                                        "Autres déterminants, précisions",
                                        "Other determinants, details",
                                        "stdyDscr/stdyInfo/subject/topcClas[]/value/other determinant",
                                        "textarea",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        1000,

                                    );
                                    ?>
                                    <input type="hidden"
                                        name="stdyDscr/stdyInfo/subject/topcClas[]/vocab/other determinant"
                                        value="other determinant" />
                                </div>


                                <div class="col-md-12 mb-3 ">

                                    <?php
                                    $options_pathologies = [
                                        "CIM-11" => "CIM-11"
                                    ];
                                    $tooltipFr = getTooltipByName('Pathology', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('Pathology', 'en', 'Theme');
                                    nada_renderInputGroup(
                                        "Pathologie(s), affection(s) ou diagnostic(s) ciblé(s) par l’étude",
                                        "Pathology(ies), condition(s) or diagnose(s) targeted by the study",
                                        "stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11",
                                        "select-multiple-cim11",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        null,
                                        true
                                    );
                                    ?>

                                    <input type="hidden" name="stdyDscr/stdyInfo/subject/topcClas[]/vocab/cim-11"
                                        value="cim-11" />

                                </div>

                                <div class="col-md-12 mb-3" id="">
                                    <?php
                                    $tooltipFr = getTooltipByName('RareDiseases', 'fr', 'Theme');
                                    $tooltipEn = getTooltipByName('RareDiseases', 'en', 'Theme');
                                    nada_renderInputGroup(
                                        "L'étude porte-t-elle sur une ou plusieurs maladie(s) rare(s) ? ",
                                        "Does the study cover one or more rare disease(s)?",
                                        "additional/theme/RareDiseases",
                                        "radio",
                                        $options_boolean,
                                        true,
                                        true,
                                        $tooltipFr,
                                        $tooltipEn,
                                        null,
                                    );
                                    ?>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>
            </div>
        </section>

        <section key="Population">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapsethirteen"
                    aria-expanded="true"
                    aria-controls="collapsethirteen"
                    data-fr="Population de l'étude"
                    data-en="Study population">
                    Population de l'étude
                </button>

                <div class="collapse show" id="collapsethirteen">
                    <div class="card card-body">
                        <section key="Population">
                            <?php
                            $tooltipFr = getTooltipByName('Population', 'fr', 'StudyRelatedInfo');
                            $tooltipEn = getTooltipByName('Population', 'en', 'StudyRelatedInfo'); ?>
                            <h3 class="lang-text-tooltip"
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Population de l'étude"
                                data-en="Study population">
                                <span class="contentSection">Population de l'étude</span>
                                <span class="info-bulle "
                                    attr-lng="fr"
                                    data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                            </h3>

                            <div class="row">
                                <div class="col-md-12 mb-3" data-key="PopulationType">
                                    <?php
                                    $PopulationType = getReferentielByName('PopulationType');
                                    $tooltipFr = getTooltipByName('PopulationType', 'fr', 'Population');
                                    $tooltipEn = getTooltipByName('PopulationType', 'en', 'Population');
                                    nada_renderInputGroup(
                                        "Type de population",
                                        "Population Type",
                                        "stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I",
                                        "select",
                                        $PopulationType,
                                        true,
                                        true,
                                        $tooltipFr,
                                        $tooltipEn
                                    );
                                    ?>
                                </div>
                                <div class="col-md-12 mb-3 d-none" id="OtherPopulationType">
                                    <?php
                                    $tooltipFr = getTooltipByName('OtherPopulationType', 'fr', 'Population');
                                    $tooltipEn = getTooltipByName('OtherPopulationType', 'en', 'Population');
                                    nada_renderInputGroup(
                                        "Autre type de population, précisions",
                                        "Other population type, details",
                                        "additional/OtherPopulationType",
                                        "textarea",
                                        [],
                                        true,
                                        false,
                                        $tooltipFr,
                                        $tooltipEn,
                                        false,
                                        null,
                                        null,
                                        1000
                                    ); ?>
                                </div>
                            </div>

                            <section key="DemographicInfo">
                                <?php
                                $tooltipFr = getTooltipByName('DemographicInfo', 'fr', 'Population');
                                $tooltipEn = getTooltipByName('DemographicInfo', 'en', 'Population'); ?>
                                <h3 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Caractéristiques démographiques"
                                    data-en="Demographic characteristics">
                                    <span class="contentSection">Caractéristiques démographiques</span>
                                    <span class="info-bulle "
                                        attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h3>

                                <div class="row">
                                    <div class="col-md-12 mb-3" data-key="Sex">
                                        <?php
                                        $Sex = getReferentielByName('Sex', true);
                                        $tooltipFr = getTooltipByName('Sex', 'fr', 'DemographicInfo');
                                        $tooltipEn = getTooltipByName('Sex', 'en', 'DemographicInfo');
                                        nada_renderInputGroup2(
                                            "Sexe",
                                            "Sex",
                                            "stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I",
                                            "checkbox",
                                            $Sex,
                                            true,
                                            true,
                                            $tooltipFr,
                                            $tooltipEn
                                        );
                                        ?>
                                    </div>
                                    <div class="col-md-12 mb-3" data-key="Age">
                                        <?php
                                        $Age = getReferentielByName('Age', true);
                                        $tooltipFr = getTooltipByName('Age', 'fr', 'DemographicInfo');
                                        $tooltipEn = getTooltipByName('Age', 'en', 'DemographicInfo');
                                        nada_renderInputGroup2(
                                            "Age",
                                            "Age",
                                            "stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I",
                                            "checkbox",
                                            $Age,
                                            true,
                                            true,
                                            $tooltipFr,
                                            $tooltipEn
                                        );
                                        ?>
                                    </div>
                                </div>
                            </section>

                            <section key="OtherClusion">
                                <?php
                                $tooltipFr = getTooltipByName('OtherClusion', 'fr', 'Population');
                                $tooltipEn = getTooltipByName('OtherClusion', 'en', 'Population'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Autres critères d'éligibilité"
                                    data-en="Other eligibility criteria">
                                    <span class="contentSection">Autres critères d'éligibilité</span>
                                    <span class="info-bulle "
                                        attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>

                                <div class="row">
                                    <div class="col-md-6 col-sm-12 mb-3 allow-enter">
                                        <?php
                                        $tooltipFr = getTooltipByName('InclusionCriterion', 'fr', 'OtherClusion');
                                        $tooltipEn = getTooltipByName('InclusionCriterion', 'en', 'OtherClusion');
                                        nada_renderInputGroup(
                                            "Critères inclusion",
                                            "Inclusion Criteria",
                                            "stdyDscr/stdyInfo/sumDscr/universe/Clusion_I",
                                            "textarea",
                                            [],
                                            true,
                                            false,
                                            $tooltipFr,
                                            $tooltipEn,
                                            false,
                                            null,
                                            null,
                                            5000
                                        ); ?>

                                    </div>
                                    <div class="col-md-6 col-sm-12 mb-3 allow-enter">
                                        <?php
                                        $tooltipFr = getTooltipByName('ExclusionCriterion', 'fr', 'OtherClusion');
                                        $tooltipEn = getTooltipByName('ExclusionCriterion', 'en', 'OtherClusion');
                                        nada_renderInputGroup(
                                            "Critères exclusion",
                                            "Exclusion Criteria",
                                            "stdyDscr/stdyInfo/sumDscr/universe/Clusion_E",
                                            "textarea",
                                            [],
                                            true,
                                            false,
                                            $tooltipFr,
                                            $tooltipEn,
                                            false,
                                            null,
                                            null,
                                            5000
                                        ); ?>

                                    </div>
                                </div>
                            </section>

                            <section key="GeographicalCoverage">
                                <?php
                                $tooltipFr = getTooltipByName('GeographicalCoverage', 'fr', 'Population');
                                $tooltipEn = getTooltipByName('GeographicalCoverage', 'en', 'Population'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Champ géographique"
                                    data-en="Geographical scope">
                                    <span class="contentSection">Champ géographique</span>
                                    <span class="info-bulle "
                                        attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>


                                <div class="row">
                                    <div class="col-md-12 mb-3" data-key="GeographicalCoverage">
                                        <?php
                                        $GeographicalCoverage = getReferentielByName('GeographicalCoverage', true);
                                        $tooltipFr = getTooltipByName('Nation', 'fr', 'GeographicalCoverage');
                                        $tooltipEn = getTooltipByName('Nation', 'en', 'GeographicalCoverage');
                                        nada_renderInputGroup2(
                                            "Pays concerné(s)",
                                            "Country(ies) concerned",
                                            "stdyDscr/stdyInfo/sumDscr/nation",
                                            "select-multiple",
                                            $GeographicalCoverage,
                                            true,
                                            true,
                                            $tooltipFr,
                                            $tooltipEn

                                        );
                                        ?>
                                    </div>
                                    <div class="col-md-12 mb-3 d-none" id="FranceRegion" data-key="FranceRegion">
                                        <?php
                                        $FranceRegion = getReferentielByName('FranceRegion');
                                        $tooltipFr = getTooltipByName('FranceRegion', 'fr', 'GeographicalCoverage');
                                        $tooltipEn = getTooltipByName('FranceRegion', 'en', 'GeographicalCoverage');
                                        nada_renderInputGroup(
                                            "Région(s) française(s) concernée(s)",
                                            "French region(s) concerned",
                                            "stdyDscr/stdyInfo/sumDscr/geogCover",
                                            "checkbox",
                                            $FranceRegion,
                                            true,
                                            false,
                                            $tooltipFr,
                                            $tooltipEn
                                        );
                                        ?>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <?php
                                        $tooltipFr = getTooltipByName('GeoDetail', 'fr', 'GeographicalCoverage');
                                        $tooltipEn = getTooltipByName('GeoDetail', 'en', 'GeographicalCoverage');
                                        nada_renderInputGroup(
                                            "Champ géographique, précisions",
                                            "Geographical coverage, details",
                                            "additional/geographicalCoverage/geoDetail",
                                            "textarea",
                                            [],
                                            true,
                                            false,
                                            $tooltipFr,
                                            $tooltipEn,
                                            false,
                                            null,
                                            null,
                                            1500
                                        ); ?>
                                    </div>
                                </div>
                            </section>
                        </section>
                    </div>
                </div>
            </div>
        </section>

    </section>
</div>
<?php
/*
Template Name: Add Study section 4
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

global $compareStudy;
$current_lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';

?>

<div class="tab-pane show active" id="step-4">
    <section key="DataCollectionAccess">
        <h2 class="lang-text"
            data-fr="Collecte et accès aux données"
            data-en="Data collection and access">Collecte et accès aux données</h2>

        <section key="DataCollectionIntegration">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"
                    data-fr="Collecte et réutilisation de données" data-en="Data collection and reuse">
                    Collecte et réutilisation de données
                </button>

                <div class="collapse show" id="collapseOne">
                    <div class="card card-body">
                        <section key="DataCollectionIntegration">
                            <?php
                            $tooltipFr = getTooltipByName('DataCollectionIntegration', 'fr', 'DataCollectionAccess');
                            $tooltipEn = getTooltipByName('DataCollectionIntegration', 'en', 'DataCollectionAccess'); ?>
                            <h3 class="lang-text-tooltip"
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Collecte et réutilisation de données"
                                data-en="Data collection and reuse">
                                <span class="contentSection">Collecte et réutilisation de données</span>
                                <span class="info-bulle"
                                    attr-lng="fr"
                                    data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                            </h3>

                            <section key="SampleSize">
                                <?php
                                $tooltipFr = getTooltipByName('SampleSize', 'fr', 'DataCollectionIntegration');
                                $tooltipEn = getTooltipByName('SampleSize', 'en', 'DataCollectionIntegration'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Nombre de participants"
                                    data-en="Number of participants">
                                    <span class="contentSection">Nombre de participants</span>
                                    <span class="info-bulle"
                                        attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>

                                <div class="row">
                                    <div class="col-md-12 mb-3" data-key="PlannedSampleSize">
                                        <?php
                                        $PlannedSampleSize = getReferentielByName('PlannedSampleSize');
                                        $tooltipFr = getTooltipByName('PlannedSampleSize', 'fr', 'SampleSize');
                                        $tooltipEn = getTooltipByName('PlannedSampleSize', 'en', 'SampleSize');
                                        nada_renderInputGroup(
                                            "Nombre prévu de participants",
                                            "Target number of participants",
                                            "stdyDscr/method/dataColl/targetSampleSize",
                                            "select",
                                            $PlannedSampleSize,
                                            true,
                                            true,
                                            $tooltipFr,
                                            $tooltipEn
                                        );
                                        ?>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <?php
                                        $tooltipFr = getTooltipByName('FinalSampleSize', 'fr', 'SampleSize');
                                        $tooltipEn = getTooltipByName('FinalSampleSize', 'en', 'SampleSize');
                                        nada_renderInputGroup(
                                            "Nombre actuel de participants",
                                            "Actual number of participants",
                                            "stdyDscr/method/dataColl/respRate",
                                            "text",
                                            [],
                                            true,
                                            false,
                                            $tooltipFr,
                                            $tooltipEn,
                                            true,
                                            null,
                                            null,
                                            50
                                        ); ?>
                                    </div>
                                </div>
                            </section>

                            <section key="CollectionChronology">
                                <?php
                                $tooltipFr = getTooltipByName('CollectionChronology', 'fr', 'DataCollectionIntegration');
                                $tooltipEn = getTooltipByName('CollectionChronology', 'en', 'DataCollectionIntegration'); ?>
                                <?php
                                $statusKey = "stdyDscr/stdyInfo/sumDscr/collDate_{$current_lang}_status";
                                $isChanged = $compareStudy[$statusKey] ?? false;
                                ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Chronologie de la collecte" data-en="Collection chronology">
                                    <span class="contentSection">Chronologie de la collecte</span>
                                    <span class="info-bulle " attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                    <?php if ($isChanged): ?>
                                        <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                            <i class="dashicons dashicons-edit"></i>
                                        </span>
                                    <?php endif; ?>
                                </h4>

                                <div class="row">
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <?php
                                        $tooltipFr = getTooltipByName('CollectionStart', 'fr', 'CollectionChronology');
                                        $tooltipEn = getTooltipByName('CollectionStart', 'en', 'CollectionChronology');
                                        nada_renderInputGroup(
                                            "Date de début de la collecte (recrutement du premier participant)",
                                            "Collection start date (recruitment of the first participant)",
                                            "stdyDscr/stdyInfo/sumDscr/collDate/event_start",
                                            "date",
                                            [],
                                            true,
                                            true,
                                            $tooltipFr,
                                            $tooltipEn
                                        ); ?>
                                        <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/collDate/event_start/event_fr" value="début" />
                                        <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/collDate/event_start/event_en" value="start" />

                                    </div>
                                    <div class="col-md-6 col-sm-12 mb-3">
                                        <?php
                                        $tooltipFr = getTooltipByName('CollectionEnd', 'fr', 'CollectionChronology');
                                        $tooltipEn = getTooltipByName('CollectionEnd', 'en', 'CollectionChronology');
                                        nada_renderInputGroup(
                                            "Date de fin de la collecte (dernier suivi du dernier participant)",
                                            "Collection end date (last follow-up of the last participant)",
                                            "stdyDscr/stdyInfo/sumDscr/collDate/event_end",
                                            "date",
                                            [],
                                            true,
                                            false,
                                            $tooltipFr,
                                            $tooltipEn
                                        ); ?>
                                        <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/collDate/event_end/event_fr" value="fin" />
                                        <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/collDate/event_end/event_en" value="end" />

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <?php
                                        $tooltipFr = getTooltipByName('CollectionFrequency', 'fr', 'CollectionChronology');
                                        $tooltipEn = getTooltipByName('CollectionFrequency', 'en', 'CollectionChronology'); ?>
                                        <?php nada_renderInputGroup(
                                            "Fréquence de la collecte",
                                            "Collection frequency",
                                            "stdyDscr/method/dataColl/frequenc",
                                            "textarea",
                                            [],
                                            true,
                                            true,
                                            $tooltipFr,
                                            $tooltipEn,
                                            false,
                                            null,
                                            null,
                                            1000
                                        ); ?>
                                    </div>
                                </div>
                            </section>

                            <section key="DataCollection">
                                <?php
                                $tooltipFr = getTooltipByName('DataCollection', 'fr', 'DataCollectionIntegration');
                                $tooltipEn = getTooltipByName('DataCollection', 'en', 'DataCollectionIntegration'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Informations concernant les données collectées auprès des participants dans le cadre de l'étude"
                                    data-en="Information concerning data collected from participants during the study">
                                    <span class="contentSection">Informations concernant les données collectées auprès des participants dans le cadre de l'étude</span>
                                    <span class="info-bulle"
                                        attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>
                                <i>
                                    <span class="lang-text subTitleSection"
                                        data-fr="Les informations concernant la réutilisation de données existantes seront à renseigner dans la prochaine section"
                                        data-en=" Information concerning the reuse of existing data will be provided in the next section.">
                                        Les informations concernant la réutilisation de données existantes seront à renseigner dans la prochaine section
                                    </span>
                                </i>
                                <section key="CollectionProcess">
                                    <?php
                                    $tooltipFr = getTooltipByName('CollectionProcess', 'fr', 'DataCollection');
                                    $tooltipEn = getTooltipByName('CollectionProcess', 'en', 'DataCollection'); ?>
                                    <h5 class="lang-text-tooltip"
                                        tooltip-fr="<?php echo $tooltipFr; ?>"
                                        tooltip-en="<?php echo $tooltipEn; ?>"
                                        data-fr="Procédure de collecte"
                                        data-en="Collection procedure">
                                        <span class="contentSection">Procédure de collecte</span>
                                        <span class="info-bulle"
                                            attr-lng="fr"
                                            data-text="<?php echo $tooltipFr; ?>">
                                            <span class="dashicons dashicons-info"></span>
                                        </span>
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-12 mb-3" data-key="CollectionMode">
                                            <?php
                                            $CollectionMode = getReferentielByName('CollectionMode', true);
                                            $tooltipFr = getTooltipByName('CollectionMode', 'fr', 'CollectionProcess');
                                            $tooltipEn = getTooltipByName('CollectionMode', 'en', 'CollectionProcess');
                                            nada_renderInputGroup2(
                                                "Mode de collecte",
                                                "Collection mode",
                                                "stdyDscr/method/dataColl/collMode",
                                                "checkbox",
                                                $CollectionMode,
                                                true,
                                                true,
                                                $tooltipFr,
                                                $tooltipEn,
                                            );
                                            ?>
                                        </div>

                                        <div class="col-md-12 mb-3 d-none" id="collectionModeOther">
                                            <?php
                                            $tooltipFr = getTooltipByName('CollectionModeOther', 'fr', 'CollectionProcess');
                                            $tooltipEn = getTooltipByName('CollectionModeOther', 'en', 'CollectionProcess');
                                            nada_renderInputGroup(
                                                "Autre mode de collecte, précisions",
                                                "Other collection mode, details",
                                                "additional/collectionProcess/collectionModeOther",
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

                                        <div class="col-md-12 mb-3 allow-enter" id="CollectionModeDetails">
                                            <?php
                                            $tooltipFr = getTooltipByName('CollectionModeDetails', 'fr', 'CollectionProcess');
                                            $tooltipEn = getTooltipByName('CollectionModeDetails', 'en', 'CollectionProcess'); ?>
                                            <?php nada_renderInputGroup(
                                                "Mode de collecte, précisions",
                                                "Collection mode, details",
                                                "additional/collectionProcess/collectionModeDetails",
                                                "textarea",
                                                [],
                                                true,
                                                false,
                                                $tooltipFr,
                                                $tooltipEn,
                                                false,
                                                null,
                                                null,
                                                2000
                                            ); ?>
                                        </div>

                                        <div class="col-md-12 mb-3" id="SamplingMode" data-key="SamplingMode">
                                            <?php
                                            $SamplingMode = getReferentielByName('SamplingMode', true);
                                            $tooltipFr = getTooltipByName('SamplingMode', 'fr', 'CollectionProcess');
                                            $tooltipEn = getTooltipByName('SamplingMode', 'en', 'CollectionProcess');
                                            nada_renderInputGroup2(
                                                "Procédure d'échantillonage à l'inclusion",
                                                "Sampling procedure at inclusion",
                                                "stdyDscr/method/dataColl/sampProc",
                                                "checkbox",
                                                $SamplingMode,
                                                true,
                                                true,
                                                $tooltipFr,
                                                $tooltipEn
                                            );
                                            ?>
                                        </div>

                                        <div class="col-md-12 mb-3 d-none" id="SamplingModeOther">
                                            <?php
                                            $tooltipFr = getTooltipByName('SamplingModeOther', 'fr', 'CollectionProcess');
                                            $tooltipEn = getTooltipByName('SamplingModeOther', 'en', 'CollectionProcess');
                                            nada_renderInputGroup(
                                                "Autre procédure d'échantillonnage, précisions",
                                                "Other sampling mode, details",
                                                "additional/dataCollection/samplingModeOther",
                                                "textarea",
                                                [],
                                                true,
                                                false,
                                                $tooltipFr,
                                                $tooltipEn,
                                                false,
                                                null,
                                                null,
                                                2000
                                            ); ?>
                                        </div>

                                        <div class="col-md-12 mb-3" id="RecruitmentSource" data-key="RecruitmentSource">
                                            <?php
                                            $RecruitmentSource = getReferentielByName('RecruitmentSource');
                                            $tooltipFr = getTooltipByName('RecruitmentSource', 'fr', 'DataCollection');
                                            $tooltipEn = getTooltipByName('RecruitmentSource', 'en', 'DataCollection');
                                            nada_renderInputGroup(
                                                "Source de recrutement des participants",
                                                "Participants recruitment source",
                                                "stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType",
                                                "checkbox",
                                                $RecruitmentSource,
                                                true,
                                                true,
                                                $tooltipFr,
                                                $tooltipEn
                                            );
                                            ?>
                                        </div>

                                        <div class="col-md-12 mb-3 d-none" id="RecruitmentSourceOther">
                                            <?php
                                            $tooltipFr = getTooltipByName('RecruitmentSourceOther', 'fr', 'DataCollection');
                                            $tooltipEn = getTooltipByName('RecruitmentSourceOther', 'en', 'DataCollection');
                                            nada_renderInputGroup(
                                                "Autre source de recrutement, précisions",
                                                "Other recruitment source, details",
                                                "additional/dataCollection/recruitmentSourceOther",
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
                                </section>


                                <section key="ActiveFollowUp">
                                    <?php
                                    $tooltipFr = getTooltipByName('ActiveFollowUp', 'fr', 'DataCollection');
                                    $tooltipEn = getTooltipByName('ActiveFollowUp', 'en', 'DataCollection'); ?>
                                    <h5 class="lang-text-tooltip"
                                        tooltip-fr="<?php echo $tooltipFr; ?>"
                                        tooltip-en="<?php echo $tooltipEn; ?>"
                                        data-fr="Suivi actif des participants"
                                        data-en="Active follow-up">
                                        <span class="contentSection">Suivi actif des participants</span>
                                        <span class="info-bulle"
                                            attr-lng="fr"
                                            data-text="<?php echo $tooltipFr; ?>">
                                            <span class="dashicons dashicons-info"></span>
                                        </span>
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-12 mb-3" id="IsActiveFollowUp">
                                            <?php
                                            $tooltipFr = getTooltipByName('IsActiveFollowUp', 'fr', 'ActiveFollowUp');
                                            $tooltipEn = getTooltipByName('IsActiveFollowUp', 'en', 'ActiveFollowUp');
                                            nada_renderInputGroup(
                                                "Un suivi actif des participants est-il réalisé ?",
                                                "It is an active follow-up?",
                                                "additional/activeFollowUp/isActiveFollowUp",
                                                "radio",
                                                $options_boolean,
                                                true,
                                                true,
                                                $tooltipFr,
                                                $tooltipEn
                                            );
                                            ?>
                                        </div>

                                        <div class="col-md-12 mb-3 d-none" id="FollowUpMode" data-key="FollowUpMode">
                                            <?php
                                            $FollowUpMode = getReferentielByName('FollowUpMode');
                                            $tooltipFr = getTooltipByName('FollowUpMode', 'fr', 'ActiveFollowUp');
                                            $tooltipEn = getTooltipByName('FollowUpMode', 'en', 'ActiveFollowUp');
                                            nada_renderInputGroup(
                                                "Modalités du suivi actif",
                                                "Follow-up method",
                                                "stdyDscr/method/notes/subject_followUP",
                                                "checkbox",
                                                $FollowUpMode,
                                                true,
                                                true,
                                                $tooltipFr,
                                                $tooltipEn
                                            );
                                            ?>
                                            <input type="hidden" name="stdyDscr/method/notes/subject_followUP/subject_fr" value="suivi" />
                                            <input type="hidden" name="stdyDscr/method/notes/subject_followUP/subject_en" value="follow-up" />

                                        </div>

                                        <div class="col-md-12 mb-3 d-none" id="FollowUpModeOther">
                                            <?php
                                            $tooltipFr = getTooltipByName('FollowUpModeOther', 'fr', 'ActiveFollowUp');
                                            $tooltipEn = getTooltipByName('FollowUpModeOther', 'en', 'ActiveFollowUp');
                                            nada_renderInputGroup(
                                                "Autre modalité de suivi, précisions",
                                                "Other follow-up method, details",
                                                "additional/activeFollowUp/followUpModeOther",
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

                                </section>

                                <section key="DataTypes">
                                    <?php
                                    $tooltipFr = getTooltipByName('DataTypes', 'fr', 'DataCollection');
                                    $tooltipEn = getTooltipByName('DataTypes', 'en', 'DataCollection'); ?>
                                    <h5 class="lang-text-tooltip"
                                        tooltip-fr="<?php echo $tooltipFr; ?>"
                                        tooltip-en="<?php echo $tooltipEn; ?>"
                                        data-fr="Types de données" data-en="Data types">
                                        <span class="contentSection">Types de données</span>
                                        <span class="info-bulle " attr-lng="fr"
                                            data-text="<?php echo $tooltipFr; ?>">
                                            <span class="dashicons dashicons-info"></span>
                                        </span>
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-12 mb-3" data-key="Datatype">
                                            <?php
                                            $Datatype = getReferentielByName('Datatype');
                                            $tooltipFr = getTooltipByName('DataType', 'fr', 'DataTypes');
                                            $tooltipEn = getTooltipByName('DataType', 'en', 'DataTypes');
                                            nada_renderInputGroup(
                                                "Type de données",
                                                "Data type",
                                                "stdyDscr/stdyInfo/sumDscr/dataKind",
                                                "checkbox",
                                                $Datatype,
                                                true,
                                                true,
                                                $tooltipFr,
                                                $tooltipEn
                                            );
                                            ?>
                                        </div>

                                        <div class="col-md-12 mb-3 d-none" id="DataTypeOther">
                                            <?php
                                            $tooltipFr = getTooltipByName('DataTypeOther', 'fr', 'DataTypes');
                                            $tooltipEn = getTooltipByName('DataTypeOther', 'en', 'DataTypes');
                                            nada_renderInputGroup(
                                                "Autre type de données, précisions",
                                                "Other data type, details",
                                                "additional/dataTypes/dataTypeOther",
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

                                        <div class="col-md-12 mb-3 d-none" id="ClinicalDataDetails">
                                            <?php
                                            $tooltipFr = getTooltipByName('ClinicalDataDetails', 'fr', 'DataTypes');
                                            $tooltipEn = getTooltipByName('ClinicalDataDetails', 'en', 'DataTypes');
                                            nada_renderInputGroup(
                                                "Données cliniques, précisions",
                                                "Clinical data, details",
                                                "additional/dataTypes/clinicalDataDetails",
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


                                        <div class="col-md-12 mb-3 d-none" id="ParaclinicalDataOther">
                                            <?php
                                            $tooltipFr = getTooltipByName('ParaclinicalDataOther', 'fr', 'DataTypes');
                                            $tooltipEn = getTooltipByName('ParaclinicalDataOther', 'en', 'DataTypes');
                                            nada_renderInputGroup(
                                                "Autres données paracliniques (hors biologiques), précisions",
                                                "Other paraclinical data (non-biological), details",
                                                "additional/dataTypes/paraclinicalDataOther",
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

                                        <div class="col-md-12 mb-3 d-none" id="BiologicalDataDetails">
                                            <?php
                                            $tooltipFr = getTooltipByName('BiologicalDataDetails', 'fr', 'DataTypes');
                                            $tooltipEn = getTooltipByName('BiologicalDataDetails', 'en', 'DataTypes');
                                            nada_renderInputGroup(
                                                "Données biologiques, précisions",
                                                "Biological data, details",
                                                "additional/dataTypes/biologicalDataDetails",
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


                                        <div class="col-md-12 mb-3 d-none" id="isDataInBiobank">
                                            <?php
                                            $options_dataInBioBank = [
                                                "fr" => [
                                                    "Oui" => "Oui",
                                                    "Non" => "Non",
                                                ],
                                                "en" => [
                                                    "Yes" => "Yes",
                                                    "No" => "No",
                                                ]
                                            ];
                                            $tooltipFr = getTooltipByName('isDataInBiobank', 'fr', 'DataTypes');
                                            $tooltipEn = getTooltipByName('isDataInBiobank', 'en', 'DataTypes');
                                            nada_renderInputGroup(
                                                    "Présence d'échantillons biologiques dans une biobanque ?",
                                                    "Presence of biological samples in a biobank ?",
                                                "additional/dataTypes/isDataInBiobank",
                                                "radio",
                                                $options_dataInBioBank,
                                                true,
                                                false,
                                                $tooltipFr,
                                                $tooltipEn
                                            );
                                            ?>
                                        </div>

                                        <div class="col-md-12 mb-3 d-none" id="BiobankContent"
                                            data-key="BiobankContent">
                                            <?php
                                            $BiobankContent = getReferentielByName('BiobankContent');
                                            $tooltipFr = getTooltipByName('BiobankContent', 'fr', 'DataTypes');
                                            $tooltipEn = getTooltipByName('BiobankContent', 'en', 'DataTypes');
                                            nada_renderInputGroup(
                                                "Nature des échantillons",
                                                "Nature of samples",
                                                "additional/dataTypes/biobankContent",
                                                "checkbox",
                                                $BiobankContent,
                                                true,
                                                true,
                                                $tooltipFr,
                                                $tooltipEn
                                            );
                                            ?>
                                        </div>

                                        <div class="col-md-12 mb-3 d-none" id="BiobankContentOther">
                                            <?php
                                            $tooltipFr = getTooltipByName('BiobankContentOther', 'fr', 'DataTypes');
                                            $tooltipEn = getTooltipByName('BiobankContentOther', 'en', 'DataTypes');
                                            nada_renderInputGroup(
                                                "Autre échantillon, précisions",
                                                "Other sample, details",
                                                "additional/dataTypes/biobankContentOther",
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
                                        </div>

                                        <div class="col-md-12 mb-3 d-none" id="OtherLiquidsDetails">
                                            <?php
                                            $tooltipFr = getTooltipByName('OtherLiquidsDetails', 'fr', 'DataTypes');
                                            $tooltipEn = getTooltipByName('OtherLiquidsDetails', 'en', 'DataTypes');
                                            nada_renderInputGroup(
                                                "Autres liquides ou sécrétions biologiques, précisions",
                                                "Other fluids and secretions, details",
                                                "additional/dataTypes/otherLiquidsDetails",
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
                                        </div>
                                    </div>
                                </section>

                            </section>

                            <div class="row">
                                <div class="col-md-12 mb-3" id="IsDataIntegration">
                                    <?php
                                    $tooltipFr = getTooltipByName('IsDataIntegration', 'fr', 'DataCollectionIntegration');
                                    $tooltipEn = getTooltipByName('IsDataIntegration', 'en', 'DataCollectionIntegration');
                                    nada_renderInputGroup(
                                        "Des données individuelles provenant d'autres sources sont-elles réutilisées dans le cadre de cette étude ?",
                                        "Are individual data from other sources reused in this study?",
                                        "additional/dataCollectionIntegration/isDataIntegration",
                                        "radio",
                                        $options_boolean,
                                        true,
                                        true,
                                        $tooltipFr,
                                        $tooltipEn
                                    );
                                    ?>
                                </div>
                            </div>

                            <section class="d-none" key="DataIntegration" id="DataIntegration">
                                <?php
                                $tooltipFr = getTooltipByName('DataIntegration', 'fr', 'DataCollectionIntegration');
                                $tooltipEn = getTooltipByName('DataIntegration', 'en', 'DataCollectionIntegration'); ?>
                                <h3 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Réutilisation de données existantes"
                                    data-en="Reuse of existing data">
                                    <span class="contentSection">Réutilisation de données existantes</span>
                                    <span class="info-bulle"
                                        attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h3>

                                <div class="row">
                                    <div class="col-md-12 mb-3" id="ConformityDeclaration"
                                        data-key="ConformityDeclaration">
                                        <?php
                                        $ConformityDeclaration = getReferentielByName('ConformityDeclaration');
                                        $tooltipFr = getTooltipByName('ConformityDeclaration', 'fr', 'DataIntegration');
                                        $tooltipEn = getTooltipByName('ConformityDeclaration', 'en', 'DataIntegration');
                                        nada_renderInputGroup(
                                            "Déclaration de conformité",
                                            "Declaration of conformity",
                                            "additional/regulatoryRequirements/conformityDeclaration",
                                            "select",
                                            $ConformityDeclaration,
                                            true,
                                            false,
                                            $tooltipFr,
                                            $tooltipEn
                                        );
                                        ?>
                                    </div>

                                    <section key="ThirdPartySource" id="ThirdPartySourceBloc" class="d-none">
                                        <?php
                                        $tooltipFr = getTooltipByName('ThirdPartySource', 'fr', 'DataIntegration');
                                        $tooltipEn = getTooltipByName('ThirdPartySource', 'en', 'DataIntegration'); ?>
                                        <?php
                                        $statusKey = "stdyDscr/method/dataColl/sources/values_{$current_lang}_status";
                                        $isChanged = $compareStudy[$statusKey] ?? false;
                                        ?>
                                        <h4 class="lang-text-tooltip"
                                            tooltip-fr="<?php echo $tooltipFr; ?>"
                                            tooltip-en="<?php echo $tooltipEn; ?>"
                                            data-fr="Informations concernant les données réutilisées pour l'étude"
                                            data-en="Information concerning data reused for the study">
                                            <span class="contentSection">Informations concernant les données réutilisées pour l'étude</span>
                                            <span class="info-bulle " attr-lng="fr"
                                                data-text="<?php echo $tooltipFr; ?>">
                                                <span class="dashicons dashicons-info"></span>
                                            </span>
                                            <?php if ($isChanged): ?>
                                                <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                                    <i class="dashicons dashicons-edit"></i>
                                                </span>
                                            <?php endif; ?>
                                        </h4>
                                        <i>
                                            <span class="lang-text subTitleSection" data-fr="Plusieurs sources possibles"
                                                data-en="Several possible sources">
                                                Plusieurs sources possibles
                                            </span>
                                        </i>
                                        <div id="repeater-sources" class="repeaterBlock">


                                            <div class="mb-4 repeater-item">
                                                <button type="button" class="btn-remove btn-remove-section ">
                                                    <span class="dashicons dashicons-trash"></span>
                                                    <span class="lang-text" data-fr="Supprimer" data-en="Delete">
                                                        Supprimer
                                                    </span>
                                                </button>
                                                <div class="row w-100">

                                                    <div class="col-md-6 col-sm-12 mb-3" id="SourceName">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('SourceName', 'fr', 'ThirdPartySource');
                                                        $tooltipEn = getTooltipByName('SourceName', 'en', 'ThirdPartySource');
                                                        nada_renderInputGroup(
                                                            "Description de la source",
                                                            "Source description",
                                                            "stdyDscr/method/dataColl/sources/sourceCitation",
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

                                                    <div class="col-md-6 col-sm-12 mb-3">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('SourceId', 'fr', 'ThirdPartySource');
                                                        $tooltipEn = getTooltipByName('SourceId', 'en', 'ThirdPartySource');
                                                        nada_renderInputGroup(
                                                            "Identifiant de la source",
                                                            "Source identifier",
                                                            "stdyDscr/method/dataColl/sources/sourceCitation/holdings",
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
                                                    <div>
                                                        <div class="col-md-12 mb-3" data-parent="OtherSourceType" data-key="SourceType">
                                                            <?php
                                                            $SourceType = getReferentielByName('SourceType');
                                                            $tooltipFr = getTooltipByName('SourceType', 'fr', 'ThirdPartySource');
                                                            $tooltipEn = getTooltipByName('SourceType', 'en', 'ThirdPartySource');
                                                            nada_renderInputGroup(
                                                                "Type de source",
                                                                "Source type",
                                                                "stdyDscr/method/dataColl/sources/srcOrig",
                                                                "checkbox",
                                                                $SourceType,
                                                                true,
                                                                true,
                                                                $tooltipFr,
                                                                $tooltipEn
                                                            );
                                                            ?>
                                                        </div>

                                                        <div class="col-md-12 mb-3 d-none"
                                                            data-item="OtherSourceType">
                                                            <?php
                                                            $tooltipFr = getTooltipByName('OtherSourceType', 'fr', 'ThirdPartySource');
                                                            $tooltipEn = getTooltipByName('OtherSourceType', 'en', 'ThirdPartySource');
                                                            nada_renderInputGroup(
                                                                "Autre type de source, précisions",
                                                                "Other source type, details",
                                                                "additional/thirdPartySource/otherSourceType",
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

                                                    <div class="col-md-12 mb-3" data-key="SourcePurpose">
                                                        <?php
                                                        $SourcePurpose = getReferentielByName('SourcePurpose');
                                                        $tooltipFr = getTooltipByName('SourcePurpose', 'fr', 'ThirdPartySource');
                                                        $tooltipEn = getTooltipByName('SourcePurpose', 'en', 'ThirdPartySource');
                                                        nada_renderInputGroup(
                                                            "Objectif de l'integration de la source",
                                                            "Source integration purpose",
                                                            "stdyDscr/method/dataColl/sources/sourceCitation/notes/subject_sourcePurpose",
                                                            "select",
                                                            $SourcePurpose,
                                                            true,
                                                            true,
                                                            $tooltipFr,
                                                            $tooltipEn
                                                        );
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex btnBlockRepeater">
                                            <button type="button" class="btn-add mt-2" id="add-sources">
                                                <span class="dashicons dashicons-plus"></span>
                                                <span class="lang-text" data-fr="Ajouter" data-en="Add">
                                                    Ajouter
                                                </span>
                                            </button>
                                        </div>
                                    </section>


                                </div>
                            </section>

                        </section>
                    </div>
                </div>
            </div>
        </section>

        <section key="DataAccess">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo"
                    data-fr="Accès aux données"
                    data-en="Data access">
                    Accès aux données
                </button>

                <div class="collapse show" id="collapseTwo">
                    <div class="card card-body">
                        <section key="DataAccess">
                            <?php
                            $tooltipFr = getTooltipByName('DataAccess', 'fr', 'DataCollectionAccess');
                            $tooltipEn = getTooltipByName('DataAccess', 'en', 'DataCollectionAccess'); ?>
                            <h3 class="lang-text-tooltip"
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Accès aux données" data-en="Data access">
                                <span class="contentSection">Accès aux données</span>
                                <span class="info-bulle " attr-lng="fr"
                                    data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                            </h3>

                            <section key="DataQuality">
                                <?php
                                $tooltipFr = getTooltipByName('DataQuality', 'fr', 'DataAccess');
                                $tooltipEn = getTooltipByName('DataQuality', 'en', 'DataAccess'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Qualité des données" data-en="Data quality">
                                    <span class="contentSection">Qualité des données</span>
                                    <span class="info-bulle " attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <?php
                                        $statusKey = "stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName/values_{$current_lang}_status";
                                        $isChanged = $compareStudy[$statusKey] ?? false;
                                        ?>
                                        <h5 class="lang-text-tooltip"
                                            data-fr="Qualité des données : standard(s)" data-en="Data quality : standard(s)">
                                            <span class="contentSection">Qualité des données : standard(s)</span>
                                            <?php if ($isChanged): ?>
                                                <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                                    <i class="dashicons dashicons-edit"></i>
                                                </span>
                                            <?php endif; ?>
                                        </h5>
                                        <div id="repeater-standards" class="repeaterBlock">
                                            <div class="mb-2 repeater-item">
                                                <button type="button" class="btn-remove btn-remove-section ">
                                                    <span class="dashicons dashicons-trash"></span>
                                                    <span class="lang-text" data-fr="Supprimer" data-en="Delete">
                                                        Supprimer
                                                    </span>
                                                </button>
                                                <div class="row w-100">
                                                    <div class="col-md-12 mb-3">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('UsedStandards', 'fr', 'DataQuality');
                                                        $tooltipEn = getTooltipByName('UsedStandards', 'en', 'DataQuality');
                                                        nada_renderInputGroup(
                                                            "Standard(s) ou nomenclature(s) employé(s)",
                                                            "Standard(s) or nomenclature(s) used",
                                                            "stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName",
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
                                                        );
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex btnBlockRepeater">
                                            <button type="button" class="btn-add mt-2" id="add-standard">
                                                <span class="dashicons dashicons-plus"></span>
                                                <span class="lang-text" data-fr="Ajouter" data-en="Add">
                                                    Ajouter
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <?php
                                        $statusKey = "stdyDscr/stdyInfo/qualityStatement/otherQualityStatement/values_{$current_lang}_status";
                                        $isChanged = $compareStudy[$statusKey] ?? false;
                                        ?>
                                        <h5 class="lang-text-tooltip"
                                            data-fr="Qualité des données : procédure" data-en="Data quality : procedure">
                                            <span class="contentSection">Qualité des données : procédure</span>
                                            <?php if ($isChanged): ?>
                                                <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                                    <i class="dashicons dashicons-edit"></i>
                                                </span>
                                            <?php endif; ?>
                                        </h5>
                                        <div id="repeater-DataQuality" class="repeaterBlock">
                                            <div class="mb-2 repeater-item">

                                                <button type="button" class="btn-remove btn-remove-section ">
                                                    <span class="dashicons dashicons-trash"></span>
                                                    <span class="lang-text" data-fr="Supprimer" data-en="Delete">
                                                        Supprimer
                                                    </span>
                                                </button>

                                                <div class="row w-100">
                                                    <div class="col-md-12 mb-3">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('QualityProcedure', 'fr', 'DataQuality');
                                                        $tooltipEn = getTooltipByName('QualityProcedure', 'en', 'DataQuality');
                                                        nada_renderInputGroup(
                                                            "Procédure qualité utilisée",
                                                            "Quality procedure used",
                                                            "stdyDscr/stdyInfo/qualityStatement/otherQualityStatement",
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
                                            </div>
                                        </div>
                                        <div class="d-flex btnBlockRepeater">
                                            <button type="button" class="btn-add mt-2" id="add-DataQuality">
                                                <span class="dashicons dashicons-plus"></span>
                                                <span class="lang-text" data-fr="Ajouter" data-en="Add">
                                                    Ajouter
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>


                            </section>

                            <section key="DataAvailability">
                                <?php
                                $tooltipFr = getTooltipByName('DataAvailability', 'fr', 'DataAccess');
                                $tooltipEn = getTooltipByName('DataAvailability', 'en', 'DataAccess'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Disponibilité du jeu de données"
                                    data-en="Data availability">
                                    <span class="contentSection">Disponibilité du jeu de données</span>
                                    <span class="info-bulle"
                                        attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>
                                <div class="row">

                                    <div class="col-md-12 mb-3" id="IndividualDataAccess"
                                        data-key="IndividualDataAccess">
                                        <?php
                                        $IndividualDataAccess = getReferentielByName('IndividualDataAccess', true);
                                        $tooltipEn = getTooltipByName('IndividualDataAccess', 'en', 'DataAvailability');
                                        $tooltipFr = getTooltipByName('IndividualDataAccess', 'fr', 'DataAvailability');
                                        nada_renderInputGroup2(
                                            "Accès aux données individuelles",
                                            "Access to individual data",
                                            "stdyDscr/dataAccs/setAvail/avlStatus",
                                            "select",
                                            $IndividualDataAccess,
                                            true,
                                            true,
                                            $tooltipFr,
                                            $tooltipEn
                                        );
                                        ?>
                                    </div>


                                    <div class="col-md-12 mb-3" id="AggregatedDataAccess">
                                        <?php
                                        $tooltipFr = getTooltipByName('AggregatedDataAccess', 'fr', 'DataAvailability');
                                        $tooltipEn = getTooltipByName('AggregatedDataAccess', 'en', 'DataAvailability');
                                        nada_renderInputGroup(
                                            "Modalités de mise à disposition des données agrégées",
                                            "Conditions under which the aggregated data are made available",
                                            "stdyDscr/othStdMat/relMat",
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


                                    <section key="DataAccessRequestTool">
                                        <?php
                                        $tooltipFr = getTooltipByName('DataAccessRequestTool', 'fr', 'DataAvailability');
                                        $tooltipEn = getTooltipByName('DataAccessRequestTool', 'en', 'DataAvailability'); ?>
                                        <h4 class="lang-text-tooltip"
                                            tooltip-fr="<?php echo $tooltipFr; ?>"
                                            tooltip-en="<?php echo $tooltipEn; ?>"
                                            data-fr="Outil de demande d'accès aux données"
                                            data-en="Data access request tool">
                                            <span class="contentSection">Outil de demande d'accès aux données</span>
                                            <span class="info-bulle"
                                                attr-lng="fr"
                                                data-text="<?php echo $tooltipFr; ?>">
                                                <span class="dashicons dashicons-info"></span>
                                            </span>
                                        </h4>

                                        <div class="row">
                                            <div class="col-md-12 mb-3" id="DataAccessRequestToolAvailable">
                                                <?php
                                                $tooltipFr = getTooltipByName('DataAccessRequestToolAvailable', 'fr', 'DataAccessRequestTool');
                                                $tooltipEn = getTooltipByName('DataAccessRequestToolAvailable', 'en', 'DataAccessRequestTool');
                                                nada_renderInputGroup(
                                                    "Existe-t-il un outil de demande d'accès aux données ?",
                                                    "A data access request tool is it available ?",
                                                    "stdyDscr/dataAccs/useStmt/specPerm/required_yes",
                                                    "radio",
                                                    $options_boolean,
                                                    true,
                                                    false,
                                                    $tooltipFr,
                                                    $tooltipEn
                                                );
                                                ?>
                                            </div>

                                            <div class="col-md-12 mb-3 d-none" id="DataAccessRequestToolLocation">
                                                <?php
                                                $tooltipFr = getTooltipByName('DataAccessRequestToolLocation', 'fr', 'DataAccessRequestTool');
                                                $tooltipEn = getTooltipByName('DataAccessRequestToolLocation', 'en', 'DataAccessRequestTool');
                                                nada_renderInputGroup(
                                                    "Lien vers l'outil de demande d'accès",
                                                    "Link to the data access request tool",
                                                    "stdyDscr/dataAccs/useStmt/specPerm",
                                                    "textarea",
                                                    [],
                                                    true,
                                                    false,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    true,
                                                    null,
                                                    null,
                                                    1000
                                                ); ?>
                                            </div>

                                        </div>
                                    </section>

                                    <div class="row">

                                        <div class="col-md-12 mb-3">
                                            <?php
                                            $tooltipFr = getTooltipByName('DataFileCompleteness', 'fr', 'DataAvailability');
                                            $tooltipEn = getTooltipByName('DataFileCompleteness', 'en', 'DataAvailability');
                                            nada_renderInputGroup(
                                                "Complétude des fichiers des données",
                                                "Data file completeness",
                                                "stdyDscr/dataAccs/setAvail/complete",
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

                                        <div class="col-md-12 mb-3">
                                            <?php
                                            $tooltipFr = getTooltipByName('DataLocation', 'fr', 'DataAvailability');
                                            $tooltipEn = getTooltipByName('DataLocation', 'en', 'DataAvailability');
                                            nada_renderInputGroup(
                                                "Localisation des données",
                                                "Data location",
                                                "stdyDscr/dataAccs/setAvail/accsPlac",
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
                                </div>
                            </section>

                            <section key="UseStatement">
                                <?php
                                $tooltipFr = getTooltipByName('UseStatement', 'fr', 'DataAccess');
                                $tooltipEn = getTooltipByName('UseStatement', 'en', 'DataAccess'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Conditions d'usage"
                                    data-en="Use conditions">
                                    <span class="contentSection">Conditions d'usage</span>
                                    <span class="info-bulle"
                                        attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <?php
                                        $tooltipFr = getTooltipByName('AccessConditions', 'fr', 'UseStatement');
                                        $tooltipEn = getTooltipByName('AccessConditions', 'en', 'UseStatement');
                                        nada_renderInputGroup(
                                            "Conditions d'accès aux données",
                                            "Data access conditions",
                                            "stdyDscr/dataAccs/setAvail/conditions",
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

                                    <div class="col-md-12 mb-3">
                                        <?php
                                        $tooltipFr = getTooltipByName('AccessRestrictions', 'fr', 'UseStatement');
                                        $tooltipEn = getTooltipByName('AccessRestrictions', 'en', 'UseStatement');
                                        nada_renderInputGroup(
                                            "Restrictions d'accès",
                                            "Access restrictions",
                                            "stdyDscr/dataAccs/useStmt/restrctn",
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

                                    <div class="col-md-12 mb-3">
                                        <?php
                                        $tooltipFr = getTooltipByName('AdditionalDataAccessLink', 'fr', 'UseStatement');
                                        $tooltipEn = getTooltipByName('AdditionalDataAccessLink', 'en', 'UseStatement');
                                        nada_renderInputGroup(
                                            "Lien vers le plan de partage des données ou vers une documentation relative à l'accès aux données",
                                            "Link to sharing plan or to documentation relating to data access.",
                                            "stdyDscr/dataAccs/setAvail/notes",
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

                                    <div class="col-md-12 mb-3">
                                        <?php
                                        $tooltipFr = getTooltipByName('NonDisclosureAgreement', 'fr', 'UseStatement');
                                        $tooltipEn = getTooltipByName('NonDisclosureAgreement', 'en', 'UseStatement');
                                        nada_renderInputGroup(
                                            "Accord de confidentialité",
                                            "Non-disclosure agreement",
                                            "stdyDscr/dataAccs/useStmt/confDec",
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

                                <section key="DataInformationContact">
                                    <?php
                                    $tooltipFr = getTooltipByName('DataInformationContact', 'fr', 'UseStatement');
                                    $tooltipEn = getTooltipByName('DataInformationContact', 'en', 'UseStatement'); ?>
                                    <?php
                                    $statusKey = "stdyDscr/dataAccs/useStmt/contact/values_{$current_lang}_status";
                                    $isChanged = $compareStudy[$statusKey] ?? false;
                                    ?>
                                    <h4 class="lang-text-tooltip"
                                        tooltip-fr="<?php echo $tooltipFr; ?>"
                                        tooltip-en="<?php echo $tooltipEn; ?>"
                                        data-fr="Personne à contacter pour des renseignements concernant les données"
                                        data-en="Contact person for data information">
                                        <span class="contentSection">Personne à contacter pour des renseignements concernant les données</span>
                                        <span class="info-bulle " attr-lng="fr"
                                            data-text="<?php echo $tooltipFr; ?>">
                                            <span class="dashicons dashicons-info"></span>
                                        </span>
                                        <?php if ($isChanged): ?>
                                            <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                                <i class="dashicons dashicons-edit"></i>
                                            </span>
                                        <?php endif; ?>
                                    </h4>
                                    <div class="col-md-12 mb-3">
                                        <div id="repeater-DataInformationContact" class="repeaterBlock">
                                            <div class="mb-2 repeater-item">
                                                <button type="button" class="btn-remove btn-remove-section ">
                                                    <span class="dashicons dashicons-trash"></span>
                                                    <span class="lang-text" data-fr="Supprimer" data-en="Delete">
                                                        Supprimer
                                                    </span>
                                                </button>

                                                <div class="row w-100">
                                                    <div class="col-md-6 col-sm-12 mb-3">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('DIContactName', 'fr', 'DataInformationContact');
                                                        $tooltipEn = getTooltipByName('DIContactName', 'en', 'DataInformationContact');
                                                        nada_renderInputGroup(
                                                            "Prénom du contact",
                                                            "Contact first name",
                                                            "stdyDscr/dataAccs/useStmt/contact",
                                                            "text",
                                                            [],
                                                            true,
                                                            true,
                                                            $tooltipFr,
                                                            $tooltipEn
                                                        ); ?>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12 mb-3">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('DIContactName', 'fr', 'DataInformationContact');
                                                        $tooltipEn = getTooltipByName('DIContactName', 'en', 'DataInformationContact');
                                                        nada_renderInputGroup(
                                                            "NOM du contact",
                                                            "LAST NAME of the contact",
                                                            "stdyDscr/dataAccs/useStmt/contact/lastname",
                                                            "text",
                                                            [],
                                                            true,
                                                            true,
                                                            $tooltipFr,
                                                            $tooltipEn
                                                        ); ?>
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('DIContactMail', 'fr', 'DataInformationContact');
                                                        $tooltipEn = getTooltipByName('DIContactMail', 'en', 'DataInformationContact');
                                                        nada_renderInputGroup(
                                                            "Email du contact",
                                                            "Contact email",
                                                            "stdyDscr/dataAccs/useStmt/contact/mail",
                                                            "email",
                                                            [],
                                                            true,
                                                            true,
                                                            $tooltipFr,
                                                            $tooltipEn,
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            "email"
                                                        ); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex btnBlockRepeater">
                                            <button type="button" class="btn-add mt-2" id="add-DataInformationContact">
                                                <span class="dashicons dashicons-plus"></span>
                                                <span class="lang-text" data-fr="Ajouter" data-en="Add">
                                                    Ajouter
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </section>
                            </section>

                            <section key="DataCitation">
                                <?php
                                $tooltipFr = getTooltipByName('DataCitation', 'fr', 'DataAccess');
                                $tooltipEn = getTooltipByName('DataCitation', 'en', 'DataAccess'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Obligations liées à l’usage des données"
                                    data-en="Obligations related to data use">
                                    <span class="contentSection">Obligations liées à l’usage des données</span>
                                    <span class="info-bulle " attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>


                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <?php
                                        $tooltipFr = getTooltipByName('DataCitationRequirement', 'fr', 'DataCitation');
                                        $tooltipEn = getTooltipByName('DataCitationRequirement', 'en', 'DataCitation');
                                        nada_renderInputGroup(
                                            "Obligation de transmission des travaux",
                                            "Reporting requirement",
                                            "stdyDscr/dataAccs/useStmt/deposReq",
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

                                    <div class="col-md-12 mb-3">
                                        <?php
                                        $tooltipFr = getTooltipByName('DataCitationStatement', 'fr', 'DataCitation');
                                        $tooltipEn = getTooltipByName('DataCitationStatement', 'en', 'DataCitation');
                                        nada_renderInputGroup(
                                            "Obligation de citation",
                                            "Citation requirement",
                                            "stdyDscr/dataAccs/useStmt/citReq",
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


                            </section>

                            <section key="VariableDictionnary">
                                <?php
                                $tooltipFr = getTooltipByName('VariableDictionnary', 'fr', 'DataAccess');
                                $tooltipEn = getTooltipByName('VariableDictionnary', 'en', 'DataAccess'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Dictionnaire des variables"
                                    data-en="Data dictionary">
                                    <span class="contentSection">Dictionnaire des variables</span>
                                    <span class="info-bulle " attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>

                                <div class="row">
                                    <div class="col-md-12 mb-3" id="VariableDictionnaryAvailable">
                                        <?php
                                        $tooltipFr = getTooltipByName('VariableDictionnaryAvailable', 'fr', 'VariableDictionnary');
                                        $tooltipEn = getTooltipByName('VariableDictionnaryAvailable', 'en', 'VariableDictionnary');
                                        nada_renderInputGroup(
                                            "Existe-t-il un dictionnaire des variables ?",
                                            "A data dictionary is it available ?",
                                            "additional/variableDictionnary/variableDictionnaryAvailable",
                                            "radio",
                                            $options_boolean,
                                            true,
                                            true,
                                            $tooltipFr,
                                            $tooltipEn
                                        );
                                        ?>
                                    </div>

                                    <div class="col-md-12 mb-3 d-none" id="VariableDictionnaryLink">
                                        <?php
                                        $tooltipFr = getTooltipByName('VariableDictionnaryLink', 'fr', 'VariableDictionnary');
                                        $tooltipEn = getTooltipByName('VariableDictionnaryLink', 'en', 'VariableDictionnary');
                                        nada_renderInputGroup(
                                            "Lien vers le dictionnaire des variables",
                                            "Link to the data dictionary",
                                            "additional/variableDictionnary/variableDictionnaryLink",
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
                            </section>

                            <section key="MockSample">
                                <?php
                                $tooltipFr = getTooltipByName('MockSample', 'fr', 'DataAccess');
                                $tooltipEn = getTooltipByName('MockSample', 'en', 'DataAccess'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Echantillon fictif"
                                    data-en="Mock sample">
                                    <span class="contentSection">Echantillon fictif</span>
                                    <span class="info-bulle " attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>

                                <div class="row">
                                    <div class="col-md-12 mb-3" id="MockSampleAvailable">
                                        <?php
                                        $tooltipFr = getTooltipByName('MockSampleAvailable', 'fr', 'MockSample');
                                        $tooltipEn = getTooltipByName('MockSampleAvailable', 'en', 'MockSample');
                                        nada_renderInputGroup(
                                            "Un échantillon fictif du jeu de données est-il disponible ?",
                                            "A mock sample is it available ?",
                                            "additional/mockSample/mockSampleAvailable",
                                            "radio",
                                            $options_boolean,
                                            true,
                                            false,
                                            $tooltipFr,
                                            $tooltipEn
                                        );
                                        ?>
                                    </div>

                                    <div class="col-md-12 mb-3 d-none" id="MockSampleLocation">
                                        <?php
                                        $tooltipFr = getTooltipByName('MockSampleLocation', 'fr', 'MockSample');
                                        $tooltipEn = getTooltipByName('MockSampleLocation', 'en', 'MockSample');
                                        nada_renderInputGroup(
                                            "Lien vers l'échantillon fictif ou vers les renseignements pour y accéder",
                                            "Link to the sample of to information on how to access it",
                                            "additional/mockSample/mockSampleLocation",
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
                            </section>

                            <div class="row" id="OtherDocumentation">
                                <div class="col-md-12 mb-3">
                                    <?php
                                    $tooltipFr = getTooltipByName('OtherDocumentation', 'fr', 'DataAccess');
                                    $tooltipEn = getTooltipByName('OtherDocumentation', 'en', 'DataAccess');
                                    nada_renderInputGroup(
                                        "Documentation complémentaire sur les données",
                                        "Additional documentation on data",
                                        "additional/dataCollection/otherDocumentation",
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
                                </div>
                            </div>

                            <section key="DatasetPID">
                                <?php
                                $tooltipFr = getTooltipByName('DatasetPID', 'fr', 'DataAccess');
                                $tooltipEn = getTooltipByName('DatasetPID', 'en', 'DataAccess'); ?>
                                <?php
                                $statusKey = "additional/fileDscr/fileTxt/fileCitation/titlStmt/IDno/values_{$current_lang}_status";
                                $isChanged = $compareStudy[$statusKey] ?? false;
                                ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Identification pérenne du jeu de données"
                                    data-en="Persistent identifier of the dataset">
                                    <span class="contentSection">Identification pérenne du jeu de données</span>
                                    <span class="info-bulle " attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                    <?php if ($isChanged): ?>
                                        <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                            <i class="dashicons dashicons-edit"></i>
                                        </span>
                                    <?php endif; ?>
                                </h4>

                                <div id="repeater-DatasetPID" class="repeaterBlock">
                                    <div class="mb-2 repeater-item" data-section="SchemapidValue">
                                        <button type="button" class="btn-remove btn-remove-section">
                                            <span class="dashicons dashicons-trash"></span>
                                            <span class="lang-text" data-fr="Supprimer"
                                                data-en="Delete">Supprimer</span>
                                        </button>

                                        <div class="row w-100 controlChampsIdentifinat" data-parent="SchemapidValue">
                                            <div class="col-md-6 col-sm-12 mb-3" data-key="DatasetPIDSchema">
                                                <?php
                                                $DatasetPIDSchema = getReferentielByName('DatasetPIDSchema');
                                                $tooltipFr = getTooltipByName('PIDSchema', 'fr', 'DatasetPID');
                                                $tooltipEn = getTooltipByName('PIDSchema', 'en', 'DatasetPID');
                                                nada_renderInputGroup(
                                                    "Type d'identifiant du jeu de données",
                                                    "Identifier type of the dataset",
                                                    "additional/fileDscr/fileTxt/fileCitation/titlStmt/IDno/agent",
                                                    "select",
                                                    $DatasetPIDSchema,
                                                    true,
                                                    false,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    false,
                                                    null,
                                                    null,
                                                    400
                                                );
                                                ?>
                                            </div>

                                            <div class="col-md-6 col-sm-12 mb-3">
                                                <?php
                                                $tooltipFr = getTooltipByName('URI', 'fr', 'DatasetPID');
                                                $tooltipEn = getTooltipByName('URI', 'en', 'DatasetPID');
                                                nada_renderInputGroup(
                                                    "Identifiant du jeu de données",
                                                    "Dataset identifier",
                                                    "additional/fileDscr/fileTxt/fileCitation/titlStmt/IDno",
                                                    "text",
                                                    [],
                                                    true,
                                                    false,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    true,
                                                    null,
                                                    null,
                                                    null
                                                );
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex btnBlockRepeater">
                                    <button type="button" class="btn-add mt-2" id="add-DatasetPID">
                                        <span class="dashicons dashicons-plus"></span>
                                        <span class="lang-text"
                                            data-fr="Ajouter"
                                            data-en="Add">
                                            Ajouter
                                        </span>
                                    </button>
                                </div>

                            </section>

                        </section>
                    </div>
                </div>
            </div>
        </section>

        <section key="RelatedDocument">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree"
                    data-fr="Documents connexes"
                    data-en="Related Documents">
                    Documents connexes
                </button>

                <div class="collapse show" id="collapseThree">
                    <div class="card card-body">
                        <section key="RelatedDocument">
                            <?php
                            $tooltipFr = getTooltipByName('RelatedDocument', 'fr', 'DataCollectionAccess');
                            $tooltipEn = getTooltipByName('RelatedDocument', 'en', 'DataCollectionAccess'); ?>
                            <?php
                            $statusKey = "additional/relatedDocument_{$current_lang}_status";
                            $isChanged = $compareStudy[$statusKey] ?? false;
                            ?>
                            <h3 class="lang-text-tooltip"
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Documents connexes" data-en="Related Documents">
                                <span class="contentSection">Documents connexes</span>
                                <span class="info-bulle " attr-lng="fr"
                                    data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                                <?php if ($isChanged): ?>
                                    <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                        <i class="dashicons dashicons-edit"></i>
                                    </span>
                                <?php endif; ?>
                            </h3>


                            <div id="repeater-RelatedDocument" class="repeaterBlock">
                                <div class="mb-2 repeater-item" data-section="SchemapidValue">
                                    <button type="button" class="btn-remove btn-remove-section ">
                                        <span class="dashicons dashicons-trash"></span>
                                        <span class="lang-text"
                                            data-fr="Supprimer"
                                            data-en="Delete">
                                            Supprimer
                                        </span>
                                    </button>

                                    <div class="row w-100">
                                        <div class="col-md-12 mb-3" data-key="DocumentType">
                                            <?php
                                            $DocumentType = getReferentielByName('DocumentType');
                                            $tooltipFr = getTooltipByName('DocumentType', 'fr', 'RelatedDocument');
                                            $tooltipEn = getTooltipByName('DocumentType', 'en', 'RelatedDocument');
                                            nada_renderInputGroup(
                                                "Type de document",
                                                "Document type",
                                                "additional/relatedDocument/documentType",
                                                "select",
                                                $DocumentType,
                                                true,
                                                false,
                                                $tooltipFr,
                                                $tooltipEn
                                            );
                                            ?>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <?php
                                            $tooltipFr = getTooltipByName('DocumentTitle', 'fr', 'RelatedDocument');
                                            $tooltipEn = getTooltipByName('DocumentTitle', 'en', 'RelatedDocument');
                                            nada_renderInputGroup(
                                                "Titre du document",
                                                "Document title",
                                                "additional/relatedDocument/documentTitle",
                                                "textarea",
                                                [],
                                                true,
                                                false,
                                                $tooltipFr,
                                                $tooltipEn,
                                                false,
                                                null,
                                                null,
                                                400
                                            );
                                            ?>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <?php
                                            $tooltipFr = getTooltipByName('DocumentLink', 'fr', 'RelatedDocument');
                                            $tooltipEn = getTooltipByName('DocumentLink', 'en', 'RelatedDocument');
                                            nada_renderInputGroup(
                                                "Lien vers le document",
                                                "Document link",
                                                "additional/relatedDocument/documentLink",
                                                "text",
                                                [],
                                                true,
                                                false,
                                                $tooltipFr,
                                                $tooltipEn,
                                                true,
                                                null,
                                                null,
                                                null
                                            );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex btnBlockRepeater">
                                <button type="button" class="btn-add mt-2" id="add-RelatedDocument">
                                    <span class="dashicons dashicons-plus"></span>
                                    <span class="lang-text"
                                        data-fr="Ajouter"
                                        data-en="Add">
                                        Ajouter
                                    </span>
                                </button>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>

    </section>
</div>
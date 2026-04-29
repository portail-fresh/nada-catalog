<?php
/*
Template Name: Add Study section 3
*/

// init options boolean
$options_boolean = [
  "fr" => [
    "Oui" => "Oui",
    "Non" => "Non",
  ],
  "en" => [
    "Yes" => "Yes",
    "No"  => "No",
  ],
];

global $compareStudy;
$current_lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';

?>
<div class="tab-pane" id="step-3">
  <section key="CollectContext">
    <h2 class="lang-text"
      data-fr="Méthodologie de l'étude"
      data-en="Study Methodology">
      Méthodologie de l'étude
    </h2>
    <div class="card card-body">
      <section key="StudySchema">

        <section key="AnalysisUnit" class="mb-5" id="analysisUnitBloc">
          <div class="row">
            <div ddiShema="stdyDscr/stdyInfo/sumDscr/anlyUnit">
              <?php
              $options = [
                "fr" => [
                  "Individus" => "Individus"
                ],
                "en" => [
                  "Individuals" => "Individuals"
                ]
              ];
              $tooltipFr = getTooltipByName('AnalysisUnit', 'fr', 'StudyMethodology');
              $tooltipEn = getTooltipByName('AnalysisUnit', 'en', 'StudyMethodology');
              nada_renderInputGroup(
                "Unité de collecte",
                "Collection unit",
                "stdyDscr/stdyInfo/sumDscr/anlyUnitFake",
                "select",
                $options,
                true,
                true,
                $tooltipFr,
                $tooltipEn
              );
              ?>
              <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/anlyUnit_fr" value="Individus">
              <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/anlyUnit_en" value="Individus">
            </div>
          </div>
        </section>

        <section key="ResearchType" class="mb-5" id="researchTypeBloc">
          <div class="row">
            <div ddiShema="stdyDscr/method/notes" data-key="ResearchType">
              <?php
              $options = getReferentielByName('ResearchType');
              $tooltipFr = getTooltipByName('ResearchType', 'fr', 'StudyMethodology');
              $tooltipEn = getTooltipByName('ResearchType', 'en', 'StudyMethodology');
              nada_renderInputGroup(
                "Modèle d'étude",
                "Study Type",
                "stdyDscr/method/notes/subject_researchType",
                "select",
                $options,
                true,
                true,
                $tooltipFr,
                $tooltipEn
              );
              ?>
            </div>
          </div>
        </section>

        <section key="InterventionalStudy" class="mb-5 d-none" id="interventionalStudyBloc">
          <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#collapseOne"
            aria-expanded="true"
            aria-controls="collapseOne"
            data-fr="Etude interventionnelle (expérimentale)"
            data-en="Interventional Study">
            Etude interventionnelle (expérimentale)
          </button>

          <div class="collapse show" id="collapseOne">
            <div class="mb-4">
              <div class="card card-body">
                <?php
                $tooltipFr = getTooltipByName('InterventionalStudy', 'fr', 'StudyMethodology');
                $tooltipEn = getTooltipByName('InterventionalStudy', 'en', 'StudyMethodology'); ?>
                <h3
                  class="lang-text-tooltip"
                  tooltip-fr="<?php echo $tooltipFr; ?>"
                  tooltip-en="<?php echo $tooltipEn; ?>"
                  data-fr="Etude interventionnelle (expérimentale)"
                  data-en="Interventional Study">
                  <span class="contentSection">Etude interventionnelle (expérimentale)</span>
                  <span class="info-bulle "
                    attr-lng="fr"
                    data-text="<?php echo $tooltipFr; ?>">
                    <span class="dashicons dashicons-info"></span>
                  </span>
                </h3>
                <section key="InterventionalStudy">
                  <div class="row mb-3">
                    <div class="col-md-12 mb-3" ddiShema="_custom">
                      <?php
                      $tooltipFr = getTooltipByName('IsClinicalTrial', 'fr', 'InterventionalStudy');
                      $tooltipEn = getTooltipByName('IsClinicalTrial', 'en', 'InterventionalStudy');
                      nada_renderInputGroup(
                        "S'agit-il d'un essai clinique?",
                        "Is this a clinical trial?",
                        "additional/interventionalStudy/isClinicalTrial",
                        "radio",
                        $options_boolean,
                        true,
                        true,
                        $tooltipFr,
                        $tooltipEn
                      );
                      ?>
                    </div>
                    <div class="col-md-12 mb-3 d-none" id="trialPhase" data-key="TrialPhase">
                      <?php
                      $options_phases = getReferentielByName('TrialPhase');
                      $tooltipFr = getTooltipByName('TrialPhase', 'fr', 'InterventionalStudy');
                      $tooltipEn = getTooltipByName('TrialPhase', 'en', 'InterventionalStudy');
                      nada_renderInputGroup(
                        "Phase de l'essai",
                        "Study Phase",
                        "additional/interventionalStudy/trialPhase",
                        "checkbox",
                        $options_phases,
                        true,
                        true,
                        $tooltipFr,
                        $tooltipEn,
                        false,
                        "Pour les essais à phases combinées, cochez plusieurs cases.",
                        "For combined phases, check several options."
                      );
                      ?>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-12 mb-3" data-key="ResearchPurpose">
                      <?php
                      $options_researchTypes = getReferentielByName('ResearchPurpose');
                      $tooltipFr = getTooltipByName('ResearchPurpose', 'fr', 'InterventionalStudy');
                      $tooltipEn = getTooltipByName('ResearchPurpose', 'en', 'InterventionalStudy');
                      nada_renderInputGroup(
                        "Objectif principal de la recherche",
                        "Primary research purpose",
                        "additional/interventionalStudy/researchPurpose",
                        "checkbox",
                        $options_researchTypes,
                        true,
                        true,
                        $tooltipFr,
                        $tooltipEn
                      );
                      ?>
                    </div>
                    <div class="col-md-12 mb-3 d-none" data-item="otherResearchPurpose" ddiShema="(otherResearchPurpose)_custom" id="otherResearchPurpose">
                      <?php
                      $tooltipFr = getTooltipByName('OtherResearchPurpose', 'fr', 'InterventionalStudy');
                      $tooltipEn = getTooltipByName('OtherResearchPurpose', 'en', 'InterventionalStudy');
                      nada_renderInputGroup(
                        "Autre objectif principal, précisions",
                        "Other research purpose, details",
                        "additional/interventionalStudy/otherResearchPurpose",
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

                  <section key="interventionalStudyInclusionGroups" class="mb-3">
                    <div class="row">
                      <div class="col-md-12 mb-3">
                        <?php
                        $tooltipFr = getTooltipByName('IsInclusionGroups', 'fr', 'InterventionalStudy');
                        $tooltipEn = getTooltipByName('IsInclusionGroups', 'en', 'InterventionalStudy');
                        nada_renderInputGroup(
                          "L'étude comporte-t-elle plusieurs groupes définis dès l'inclusion ?",
                          "Does the study include several groups defined at the time of inclusion ?",
                          "additional/interventionalStudy/isInclusionGroups",
                          "radio",
                          $options_boolean,
                          true,
                          true,
                          $tooltipFr,
                          $tooltipEn,
                          false,
                          "Si oui, remplir les champs suivants",
                          "If yes, complete the following fields",
                          null
                        );
                        ?>
                      </div>
                      <div class="col-md-12 d-none" id="interventional-inclusion-group">
                        <?php
                        $tooltipFr = getTooltipByName('InclusionGroup', 'fr', 'InterventionalStudy');
                        $tooltipEn = getTooltipByName('InclusionGroup', 'en', 'InterventionalStudy'); ?>
                        <h4 id="title-interventional-inclusion-group" class="lang-text-tooltip d-none"
                          tooltip-fr="<?php echo $tooltipFr; ?>"
                          tooltip-en="<?php echo $tooltipEn; ?>"
                          data-fr="Groupes à l'inclusion"
                          data-en="Inclusion group">
                          <span class="contentSection">Groupes à l'inclusion</span>
                          <span class="info-bulle"
                            attr-lng="fr"
                            data-text="<?php echo $tooltipFr; ?>">
                            <span class="dashicons dashicons-info"></span>
                          </span>
                        </h4>
                        <div id="repeater-interventional-inclusion-group" class="repeaterBlock">
                          <div class="mb-4 repeater-item" data-reapeter-hidden="first">
                            <button type="button" class="btn-remove btn-remove-section ">
                              <span class="dashicons dashicons-trash"></span>
                              <span class="lang-text"
                                data-fr="Supprimer"
                                data-en="Delete">
                                Supprimer
                              </span>
                            </button>
                            <div class="row w-100">
                              <div class="col-md-12 mb-3">
                                <?php
                                $tooltipFr = getTooltipByName('GroupName', 'fr', 'InclusionGroup');
                                $tooltipEn = getTooltipByName('GroupName', 'en', 'InclusionGroup');
                                nada_renderInputGroup(
                                  "Nom du groupe",
                                  "Group name",
                                  "additional/inclusionGroups/groupName",
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

                                ); ?>
                              </div>
                              <div class="col-md-12 mb-3">
                                <?php
                                $tooltipFr = getTooltipByName('GroupDescription', 'fr', 'InclusionGroup');
                                $tooltipEn = getTooltipByName('GroupDescription', 'en', 'InclusionGroup');
                                nada_renderInputGroup(
                                  "Description du groupe",
                                  "Group description",
                                  "additional/inclusionGroups/groupDescription",
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
                        </div>
                        <div class="d-flex btnBlockRepeater">
                          <button type="button" class="btn-add mt-2 d-none" id="add-interventional-inclusion-group">
                            <span class="dashicons dashicons-plus"></span>
                            <span class="lang-text"
                              data-fr="Ajouter"
                              data-en="Add">
                              Ajouter
                            </span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </section>

                  <div class="row mb-3">
                    <div class="col-md-12 mb-3" data-key="InterventionalStudyModel">
                      <?php
                      $options_studyDesigns = getReferentielByName('InterventionalStudyModel');
                      $tooltipFr = getTooltipByName('InterventionalStudyModel', 'fr', 'InterventionalStudy');
                      $tooltipEn = getTooltipByName('InterventionalStudyModel', 'en', 'InterventionalStudy');
                      nada_renderInputGroup(
                        "Schéma d’intervention",
                        "Intervention model",
                        "additional/interventionalStudy/interventionalStudyModel",
                        "select",
                        $options_studyDesigns,
                        true,
                        true,
                        $tooltipFr,
                        $tooltipEn
                      );
                      ?>
                    </div>
                  </div>
                  <section key="Allocation" class="mb-3">
                    <?php
                    $tooltipFr = getTooltipByName('Allocation', 'fr', 'InterventionalStudy');
                    $tooltipEn = getTooltipByName('Allocation', 'en', 'InterventionalStudy'); ?>
                    <h4 class="lang-text-tooltip"
                      tooltip-fr="<?php echo $tooltipFr; ?>"
                      tooltip-en="<?php echo $tooltipEn; ?>"
                      data-fr="Allocation"
                      data-en="Allocation">
                      <span class="contentSection">Allocation</span>
                      <span class="info-bulle "
                        attr-lng="fr"
                        data-text="<?php echo $tooltipFr; ?>">
                        <span class="dashicons dashicons-info"></span>
                      </span>
                    </h4>
                    <div class="row">
                      <div class="col-md-6 col-sm-12 mb-3" ddiShema="_custom" data-key="AllocationMode">
                        <?php
                        $options_randomisation = getReferentielByName('AllocationMode');
                        $tooltipFr = getTooltipByName('AllocationMode', 'fr', 'Allocation');
                        $tooltipEn = getTooltipByName('AllocationMode', 'en', 'Allocation');
                        nada_renderInputGroup(
                          "Mode d'allocation",
                          "Allocation Type",
                          "additional/allocation/allocationMode",
                          "select",
                          $options_randomisation,
                          true,
                          true,
                          $tooltipFr,
                          $tooltipEn
                        );
                        ?>
                      </div>
                      <div class="col-md-6 col-sm-12 mb-3" ddiShema="_custom" data-key="AllocationUnit">
                        <?php
                        $options_allocation = getReferentielByName('AllocationUnit');
                        $tooltipFr = getTooltipByName('AllocationUnit', 'fr', 'Allocation');
                        $tooltipEn = getTooltipByName('AllocationUnit', 'en', 'Allocation');
                        nada_renderInputGroup(
                          "Unité d'allocation",
                          "Allocation Unit",
                          "additional/allocation/allocationUnit",
                          "select",
                          $options_allocation,
                          true,
                          true,
                          $tooltipFr,
                          $tooltipEn
                        );
                        ?>
                      </div>
                    </div>
                  </section>
                  <section key="Masking" class="mb-3">
                    <?php
                    $tooltipFr = getTooltipByName('Masking', 'fr', 'InterventionalStudy');
                    $tooltipEn = getTooltipByName('Masking', 'en', 'InterventionalStudy'); ?>
                    <h4 class="lang-text-tooltip"
                      tooltip-fr="<?php echo $tooltipFr; ?>"
                      tooltip-en="<?php echo $tooltipEn; ?>"
                      data-fr="Insu"
                      data-en="Masking">
                      <span class="contentSection">Insu</span>
                      <span class="info-bulle "
                        attr-lng="fr"
                        data-text="<?php echo $tooltipFr; ?>">
                        <span class="dashicons dashicons-info"></span>
                      </span>
                    </h4>
                    <div class="row">
                      <div class="col-md-12 mb-3" ddiShema="additional/masking/maskingType" data-key="MaskingType">
                        <?php
                        $options_masking = getReferentielByName('MaskingType');
                        $tooltipFr = getTooltipByName('MaskingType', 'fr', 'Masking');
                        $tooltipEn = getTooltipByName('MaskingType', 'en', 'Masking');
                        nada_renderInputGroup(
                          "Mode d'insu",
                          "Masking type",
                          "additional/masking/maskingType",
                          "select",
                          $options_masking,
                          true,
                          true,
                          $tooltipFr,
                          $tooltipEn
                        );
                        ?>
                      </div>
                      <div class="col-md-12 mb-3" ddiShema="_custom" data-key="BlindedMaskingDetails">
                        <?php
                        $options_roles = getReferentielByName('BlindedMaskingDetails');
                        $tooltipFr = getTooltipByName('BlindedMaskingDetails', 'fr', 'Masking');
                        $tooltipEn = getTooltipByName('BlindedMaskingDetails', 'en', 'Masking');
                        nada_renderInputGroup(
                          "Groupe(s) avec insu (en aveugle)",
                          "Blinded masking group(s)",
                          "additional/masking/blindedMaskingDetails",
                          "checkbox",
                          $options_roles,
                          true,
                          false,
                          $tooltipFr,
                          $tooltipEn
                        );
                        ?>
                      </div>
                    </div>
                  </section>
                  <section key="Arms" class="mb-3">
                    <?php
                    $tooltipFr = getTooltipByName('Arm', 'fr', 'InterventionalStudy');
                    $tooltipEn = getTooltipByName('Arm', 'en', 'InterventionalStudy'); ?>
                    <?php
                    $statusKey = "additional/arms_{$current_lang}_status";
                    $isChanged = $compareStudy[$statusKey] ?? false;
                    ?>
                    <h4 class="lang-text-tooltip"
                      tooltip-fr="<?php echo $tooltipFr; ?>"
                      tooltip-en="<?php echo $tooltipEn; ?>"
                      data-fr="Bras (ou sous-groupe de participants)"
                      data-en="Arm (or subgroup of participants)">
                      <span class="contentSection">Bras ou sous-groupe de participants</span>
                      <span class="info-bulle "
                        attr-lng="fr"
                        data-text="<?php echo $tooltipFr; ?>">
                        <span class="dashicons dashicons-info"></span>
                      </span>
                      <?php if ($isChanged): ?>
                        <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                          <i class="dashicons dashicons-edit"></i>
                        </span>
                      <?php endif; ?>
                    </h4>
                    <div id="repeater-arms" class="repeaterBlock">
                      <div class="mb-4 repeater-item" data-section="arms" data-reapeter-hidden="first">
                        <button type="button" class="btn-remove btn-remove-section ">
                          <span class="dashicons dashicons-trash"></span>
                          <span class="lang-text"
                            data-fr="Supprimer"
                            data-en="Delete">
                            Supprimer
                          </span>
                        </button>

                        <div class="row w-100" data-section="armOtherBloc">
                          <div class="col-md-12 mb-3">
                            <?php
                            $tooltipFr = getTooltipByName('ArmName', 'fr', 'Arm');
                            $tooltipEn = getTooltipByName('ArmName', 'en', 'Arm');
                            nada_renderInputGroup(
                              "Nom du bras",
                              "Arm name",
                              "additional/arms/armsName",
                              "textarea",
                              [],
                              true,
                              false,
                              $tooltipFr,
                              $tooltipEn,
                              false,
                              null,
                              null,
                              400,
                            ); ?>
                          </div>
                          <div class="col-md-12 mb-3" ddiShema="_custom" data-parent="armOtherBloc" data-key="ArmType">
                            <?php
                            $options_interventions = getReferentielByName('ArmType');
                            $tooltipFr = getTooltipByName('ArmType', 'fr', 'Arm');
                            $tooltipEn = getTooltipByName('ArmType', 'en', 'Arm');
                            nada_renderInputGroup(
                              "Type de bras",
                              "Arm type",
                              "additional/arms/armsType",
                              "select",
                              $options_interventions,
                              true,
                              false,
                              $tooltipFr,
                              $tooltipEn
                            );
                            ?>
                          </div>
                          <div class="col-md-12 mb-3 d-none arm-type-other" data-item="armOtherBloc">
                            <?php
                            $tooltipFr = getTooltipByName('ArmTypeOther', 'fr', 'Arm');
                            $tooltipEn = getTooltipByName('ArmTypeOthe', 'en', 'Arm');
                            nada_renderInputGroup(
                              "Autre type de bras, précisions",
                              "Other arm type, details",
                              "additional/arms/armsTypeOther",
                              "textarea",
                              [],
                              true,
                              true,
                              $tooltipFr,
                              $tooltipEn,
                              null,
                              null,
                              null,
                              1000
                            ); ?>
                          </div>

                          <div class="col-md-12 mb-3">
                            <?php
                            $tooltipFr = getTooltipByName('ArmDescription', 'fr', 'Arm');
                            $tooltipEn = getTooltipByName('ArmDescription', 'en', 'Arm');
                            nada_renderInputGroup(
                              "Description du bras",
                              "Arm description",
                              "additional/arms/armsDescription",
                              "textarea",
                              [],
                              true,
                              false,
                              $tooltipFr,
                              $tooltipEn,
                              null,
                              null,
                              null,
                              1000
                            ); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex btnBlockRepeater">
                      <button type="button" class="btn-add mt-2" id="add-arm">
                        <span class="dashicons dashicons-plus"></span>
                        <span class="lang-text"
                          data-fr="Ajouter"
                          data-en="Add">
                          Ajouter
                        </span>
                      </button>
                    </div>
                  </section>
                  <section key="Intervention" class="mb-3">
                    <?php
                    $tooltipFr = getTooltipByName('Intervention', 'fr', 'InterventionalStudy');
                    $tooltipEn = getTooltipByName('Intervention', 'en', 'InterventionalStudy'); ?>
                    <?php
                    $statusKey = "additional/intervention_{$current_lang}_status";
                    $isChanged = $compareStudy[$statusKey] ?? false;
                    ?>
                    <h4 class="lang-text-tooltip"
                      tooltip-fr="<?= $tooltipFr; ?>"
                      tooltip-en="<?= $tooltipEn; ?>"
                      data-fr="Facteur, processus ou exposition étudié"
                      data-en="Studied factor, process, or exposure">
                      <span class="contentSection">Facteur, processus ou exposition étudié</span>
                      <span class="info-bulle "
                        attr-lng="fr"
                        data-text="<?= $tooltipFr; ?>">
                        <span class="dashicons dashicons-info"></span>
                      </span>
                      <?php if ($isChanged): ?>
                        <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                          <i class="dashicons dashicons-edit"></i>
                        </span>
                      <?php endif; ?>
                    </h4>
                    <div id="repeater-intervExpo" class="repeaterBlock">
                      <div class="mb-4 repeater-item" data-section="intervExpo">
                        <button type="button" class="btn-remove btn-remove-section ">
                          <span class="dashicons dashicons-trash"></span>
                          <span class="lang-text"
                            data-fr="Supprimer"
                            data-en="Delete">
                            Supprimer
                          </span>
                        </button>

                        <div class="row w-100" data-section="InterventionTypeOtherBloc">
                          <div class="col-md-12 mb-3">
                            <?php
                            $tooltipFr = getTooltipByName('InterventionName', 'fr', 'Intervention');
                            $tooltipEn = getTooltipByName('InterventionName', 'en', 'Intervention');
                            nada_renderInputGroup(
                              "Nom du facteur ou de l'exposition",
                              "Factor/exposure name",
                              "additional/intervention/interventionName",
                              "textarea",
                              [],
                              true,
                              false,
                              $tooltipFr,
                              $tooltipEn,
                              true,
                              null,
                              null,
                              400
                            ); ?>
                          </div>
                          <div class="col-md-12 mb-3" ddiShema="_custom" data-parent="InterventionTypeOtherBloc" data-key="InterventionType">
                            <?php
                            $options_interventionType = getReferentielByName('InterventionType');
                            $tooltipFr = getTooltipByName('InterventionType', 'fr', 'Intervention');
                            $tooltipEn = getTooltipByName('InterventionType', 'en', 'Intervention');
                            nada_renderInputGroup(
                              "Type de facteur ou d'exposition",
                              "Factor/exposure type",
                              "additional/intervention/interventionType",
                              "select",
                              $options_interventionType,
                              true,
                              false,
                              $tooltipFr,
                              $tooltipEn
                            );
                            ?>
                          </div>
                          <div class="col-md-12 mb-3 d-none intervention-type-other" data-item="InterventionTypeOtherBloc">
                            <?php
                            $tooltipFr = getTooltipByName('InterventionTypeOther', 'fr', 'Intervention');
                            $tooltipEn = getTooltipByName('InterventionTypeOther', 'en', 'Intervention');
                            nada_renderInputGroup(
                              "Autre type de facteur ou d'exposition, précisions",
                              "Other factor/exposure type, details",
                              "additional/intervention/interventionTypeOther",
                              "textarea",
                              [],
                              true,
                              false,
                              $tooltipFr,
                              $tooltipEn,
                              null,
                              null,
                              null,
                              1000
                            ); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex btnBlockRepeater">
                      <button type="button" class="btn-add mt-2" id="add-intervExpo">
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

        <section key="ObservationalStudy" class="mb-5 d-none" id="observationalStudyBloc">
          <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#collapseTwo"
            aria-expanded="true"
            aria-controls="collapseTwo"
            data-fr="Observationnelle"
            data-en="Observational Study">
            Observationnelle
          </button>

          <div class="collapse show" id="collapseTwo">
            <div class="mb-4">
              <div class="card card-body">
                <?php
                $tooltipFr = getTooltipByName('ObservationalStudy', 'fr', 'StudySchema');
                $tooltipEn = getTooltipByName('ObservationalStudy', 'en', 'StudySchema'); ?>
                <h3 class="lang-text-tooltip"
                  tooltip-fr="<?php echo $tooltipFr; ?>"
                  tooltip-en="<?php echo $tooltipEn; ?>"
                  data-fr="Etude observationnelle"
                  data-en="Observational study">
                  <span class="contentSection">Etude observationnelle</span>
                  <span class="info-bulle "
                    attr-lng="fr"
                    data-text="<?php echo $tooltipFr; ?>">
                    <span class="dashicons dashicons-info"></span>
                  </span>
                </h3>
                <section key="ObservationalStudy">
                  <div class="row">
                    <div class="col-md-12 mb-3" data-key="ObservationalStudy">
                      <?php
                      $options_observationalType = getReferentielByName('ObservationalStudy');
                      $tooltipFr = getTooltipByName('ObservationalStudyDesign', 'fr', 'ObservationalStudy');
                      $tooltipEn = getTooltipByName('ObservationalStudyDesign', 'en', 'ObservationalStudy');
                      nada_renderInputGroup(
                        "Modèle de l’étude observationnelle",
                        "Observational study  design",
                        "stdyDscr/method/notes",
                        "select",
                        $options_observationalType,
                        true,
                        true,
                        $tooltipFr,
                        $tooltipEn
                      );
                      ?>
                    </div>
                    <div class="col-md-12 mb-3">
                      <div key="OtherResearchType" class="d-none" id="OtherResearchTypeBloc">
                        <?php
                        $tooltipFr = getTooltipByName('OtherResearchTypeDetails', 'fr', 'ObservationalStudy');
                        $tooltipEn = getTooltipByName('OtherResearchTypeDetails', 'en', 'ObservationalStudy');
                        nada_renderInputGroup(
                          "Autre type d'étude observationnelle, précisions",
                          "Other research type, details",
                          "additional/otherResearchType/otherResearchTypeDetails",
                          "textarea",
                          [],
                          true,
                          true,
                          $tooltipFr,
                          $tooltipEn,
                          null,
                          null,
                          null,
                          1000
                        ); ?>
                      </div>
                    </div>
                    <div class="col-md-12 mb-3" data-key="RecrutementTiming">
                      <?php
                      $options = getReferentielByName('RecrutementTiming');
                      $tooltipFr = getTooltipByName('TimePerspective', 'fr', 'ObservationalStudy');
                      $tooltipEn = getTooltipByName('TimePerspective', 'en', 'ObservationalStudy');
                      nada_renderInputGroup(
                        "Perspective temporelle",
                        "Time perspective",
                        "stdyDscr/method/dataColl/timeMeth",
                        "select",
                        $options,
                        true,
                        false,
                        $tooltipFr,
                        $tooltipEn,
                        null,
                        null,
                        null,
                        400
                      );
                      ?>
                    </div>

                  </div>
                  <section key="observationalStudyInclusionGroups" class="mb-3">
                    <div class="col-md-12 mb-3">
                      <?php
                      $tooltipFr = getTooltipByName('IsInclusionGroups', 'fr', 'ObservationalStudy');
                      $tooltipEn = getTooltipByName('IsInclusionGroups', 'en', 'ObservationalStudy');
                      nada_renderInputGroup(
                        "L'étude comporte-t-elle plusieurs groupes définis dès l'inclusion ?",
                        "Does the study include several groups defined at enrollment ?",
                        "additional/observationalStudy/isInclusionGroups",
                        "radio",
                        $options_boolean,
                        true,
                        true,
                        $tooltipFr,
                        $tooltipEn,
                        null,
                        "Si oui, remplir les champs suivants",
                        "If yes, complete the following fields",
                        null
                      );
                      ?>
                    </div>
                    <div id="observational-inclusion-group" class="d-none">
                      <?php
                      $tooltipFr = getTooltipByName('InclusionGroup', 'fr', 'ObservationalStudy');
                      $tooltipEn = getTooltipByName('InclusionGroup', 'en', 'ObservationalStudy'); ?>
                      <h4 id="title-observational-inclusion-group" class="lang-text-tooltip d-none"
                        tooltip-fr="<?php echo $tooltipFr; ?>"
                        tooltip-en="<?php echo $tooltipEn; ?>"
                        data-fr="Groupes à l'inclusion"
                        data-en="Inclusion group">
                        <span class="contentSection">Groupes à l'inclusion</span>
                        <span class="info-bulle"
                          attr-lng="fr"
                          data-text="<?php echo $tooltipFr; ?>">
                          <span class="dashicons dashicons-info"></span>
                        </span>
                      </h4>
                      <div id="repeater-observational-inclusion-group" class="repeaterBlock">
                        <div class="mb-4 repeater-item" data-reapeter-hidden="first">
                          <button type="button" class="btn-remove btn-remove-section ">
                            <span class="dashicons dashicons-trash"></span>
                            <span class="lang-text"
                              data-fr="Supprimer"
                              data-en="Delete">
                              Supprimer
                            </span>
                          </button>
                          <div class="row w-100">
                            <div class="col-md-12 mb-3">
                              <?php
                              $tooltipFr = getTooltipByName('GroupName', 'fr', 'InclusionGroup');
                              $tooltipEn = getTooltipByName('GroupName', 'en', 'InclusionGroup');
                              nada_renderInputGroup(
                                "Nom du groupe",
                                "Group name",
                                "additional/inclusionGroups/groupName",
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

                              ); ?>
                            </div>
                            <div class="col-md-12 mb-3">
                              <?php
                              $tooltipFr = getTooltipByName('GroupDescription', 'fr', 'InclusionGroup');
                              $tooltipEn = getTooltipByName('GroupDescription', 'en', 'InclusionGroup');
                              nada_renderInputGroup(
                                "Description du groupe",
                                "Group description",
                                "additional/inclusionGroups/groupDescription",
                                "textarea",
                                [],
                                true,
                                false,
                                $tooltipFr,
                                $tooltipEn,
                                null,
                                null,
                                null,
                                1000
                              ); ?>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="d-flex btnBlockRepeater">
                        <button type="button" class="btn-add mt-2 d-none" id="add-observational-inclusion-group">
                          <span class="dashicons dashicons-plus"></span>
                          <span class="lang-text"
                            data-fr="Ajouter"
                            data-en="Add">
                            Ajouter
                          </span>
                        </button>
                      </div>
                    </div>
                  </section>
                  <section key="Intervention2" class="mb-3">
                    <?php
                    $tooltipFr = getTooltipByName('Intervention', 'fr', 'ObservationalStudy');
                    $tooltipEn = getTooltipByName('Intervention', 'en', 'ObservationalStudy'); ?>
                    <?php
                    $statusKey = "additional/intervention_{$current_lang}_status";
                    $isChanged = $compareStudy[$statusKey] ?? false;
                    ?>
                    <h3 class="lang-text-tooltip"
                      tooltip-fr="<?php echo $tooltipFr; ?>"
                      tooltip-en="<?php echo $tooltipEn; ?>"
                      data-fr="Facteur, processus ou exposition étudié"
                      data-en="Studied factor, process, or exposure">
                      <span class="contentSection">Facteur, processus ou exposition étudié</span>
                      <span class="info-bulle "
                        attr-lng="fr"
                        data-text="<?php echo $tooltipFr; ?>">
                        <span class="dashicons dashicons-info"></span>
                      </span>
                      <?php if ($isChanged): ?>
                        <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                          <i class="dashicons dashicons-edit"></i>
                        </span>
                      <?php endif; ?>
                    </h3>
                    <div id="repeater-intervExpo2" class="repeaterBlock">
                      <div class="mb-4 repeater-item" data-section="intervExpo">
                        <button type="button" class="btn-remove btn-remove-section ">
                          <span class="dashicons dashicons-trash"></span>
                          <span class="lang-text"
                            data-fr="Supprimer"
                            data-en="Delete">
                            Supprimer
                          </span>
                        </button>

                        <div class="row w-100" data-section="InterventionTypeOtherBloc">
                          <div class="col-md-12 mb-3">
                            <?php
                            $tooltipFr = getTooltipByName('InterventionName', 'fr', 'Intervention');
                            $tooltipEn = getTooltipByName('InterventionName', 'en', 'Intervention');
                            nada_renderInputGroup(
                              "Nom du facteur ou de l'exposition",
                              "Factor/exposure name",
                              "additional/intervention/interventionName",
                              "textarea",
                              [],
                              true,
                              true,
                              $tooltipFr,
                              $tooltipEn,
                              false,
                              null,
                              null,
                              400
                            ); ?>
                          </div>
                          <div class="col-md-12 mb-3" ddiShema="_custom" data-parent="InterventionTypeOtherBloc" data-key="InterventionType">
                            <?php
                            $options_interventionType = getReferentielByName('InterventionType');
                            $tooltipFr = getTooltipByName('InterventionType', 'fr', 'Intervention');
                            $tooltipEn = getTooltipByName('InterventionType', 'en', 'Intervention');
                            nada_renderInputGroup(
                              "Type de facteur ou d'exposition",
                              "Factor/exposure type",
                              "additional/intervention/interventionType",
                              "select",
                              $options_interventionType,
                              true,
                              false,
                              $tooltipFr,
                              $tooltipEn
                            );
                            ?>
                          </div>
                          <div class="col-md-12 mb-3 d-none intervention-type-other" data-item="InterventionTypeOtherBloc">
                            <?php
                            $tooltipFr = getTooltipByName('InterventionTypeOther', 'fr', 'Intervention');
                            $tooltipEn = getTooltipByName('InterventionTypeOther', 'en', 'Intervention');
                            nada_renderInputGroup(
                              "Autre type de facteur ou d'exposition, précisions",
                              "Other factor/exposure type, details",
                              "additional/intervention/interventionTypeOther",
                              "textarea",
                              [],
                              true,
                              false,
                              $tooltipFr,
                              $tooltipEn,
                              null,
                              null,
                              null,
                              1000
                            ); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex btnBlockRepeater">
                      <button type="button" class="btn-add mt-2" id="add-intervExpo2">
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

        <section key="HealthParameters" class="mb-5" id="healthParametersBloc">
          <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#collapseThree"
            aria-expanded="true"
            aria-controls="collapseThree"
            data-fr="Critères d'évaluation (de jugement)"
            data-en="Primary outcome(s)">
            Critères d'évaluation (de jugement)
          </button>
          <div class="collapse show" id="collapseThree">
            <div class="mb-4">
              <div class="card card-body">
                <?php
                $tooltipFr = getTooltipByName('Outcomes', 'fr', 'StudyMethodology');
                $tooltipEn = getTooltipByName('Outcomes', 'en', 'StudyMethodology'); ?>
                <h3 class="lang-text-tooltip"
                  tooltip-fr="<?php echo $tooltipFr; ?>"
                  tooltip-en="<?php echo $tooltipEn; ?>"
                  data-fr="Critères d'évaluation (de jugement)"
                  data-en="Health-related outcomes">
                  <span class="contentSection">Critères d'évaluation (de jugement)</span>
                  <span class="info-bulle "
                    attr-lng="fr"
                    data-text="<?php echo $tooltipFr; ?>">
                    <span class="dashicons dashicons-info"></span>
                  </span>
                </h3>
                <section key="HealthParameters">
                  <div class="row">
                    <div class="col-md-6 col-sm-12 mb-3  allow-enter">
                      <?php
                      $tooltipFr = getTooltipByName('PrimaryOutcomes', 'fr', 'Outcomes');
                      $tooltipEn = getTooltipByName('PrimaryOutcomes', 'en', 'Outcomes');
                      nada_renderInputGroup(
                        "Critère d'évaluation (de jugement) principal",
                        "Primary Outcome",
                        "stdyDscr/studyDevelopment/developmentActivity/type_primaryEvaluation/description",
                        "textarea",
                        [],
                        true,
                        true,
                        $tooltipFr,
                        $tooltipEn,
                        null,
                        null,
                        null,
                        5000
                      ); ?>
                      <input type="hidden" name="stdyDscr/studyDevelopment/developmentActivity/type_primaryEvaluation/type_fr" value="primary evaluation" />
                      <input type="hidden" name="stdyDscr/studyDevelopment/developmentActivity/type_primaryEvaluation/type_en" value="primary evaluation" />
                    </div>
                    <div class=" col-md-6 col-sm-12 mb-3  allow-enter">
                      <?php
                      $tooltipFr = getTooltipByName('SecondaryOutcomes', 'fr', 'Outcomes');
                      $tooltipEn = getTooltipByName('SecondaryOutcomes', 'en', 'Outcomes');
                      nada_renderInputGroup(
                        "Critère(s) d'évaluation (de jugement) secondaire(s)",
                        "Secondary Outcome(s)",
                        "stdyDscr/studyDevelopment/developmentActivity/type_secondaryEvaluation/description",
                        "textarea",
                        [],
                        true,
                        false,
                        $tooltipFr,
                        $tooltipEn,
                        null,
                        null,
                        null,
                        5000
                      ); ?>
                      <input type="hidden" name="stdyDscr/studyDevelopment/developmentActivity/type_secondaryEvaluation/type_fr" value="secondary evaluation" />
                      <input type="hidden" name="stdyDscr/studyDevelopment/developmentActivity/type_secondaryEvaluation/type_en" value="secondary evaluation" />

                    </div>
                  </div>
                </section>
              </div>
            </div>
          </div>
        </section>

      </section>
    </div>
  </section>
</div>
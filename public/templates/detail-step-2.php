<div class="tab-pane " id="detaill-2">
  <div class="row">
    <div class="col-md-4 leftsidebar">
      <div class="navStickyBar">
        <ul class="navbar-nav flex-column wb--full-width">
          <li class="nav-item">
            <a href="#studyschema" class="lang-text" data-fr="Schéma d'étude" data-en="Administrative information">Schéma d'étude</a>
          </li>
          <li class="nav-item">
            <a href="#collectereutilisation" class="lang-text" data-fr="Collecte et réutilisation de données" data-en="Data collection and reuse">Collecte et réutilisation de données</a>
          </li>
          <li class="nav-item">
            <a href="#population" class="lang-text" data-fr="Population" data-en="Population">Population</a>
          </li>
          <li class="nav-item">
            <a href="#studiedhealthparams" class="lang-text" data-fr="Paramètres de santé étudiés" data-en="Health-related Outcomes">Paramètres de santé étudiés</a>
          </li>
        </ul>
      </div>
    </div>

    <div class="col-md-8">
      <div class="submenu-study" id="studyschema">


        <h2 class="field-content__h1 lang-text"
          data-fr="Schéma d'étude"
          data-en="Study Design">
          Schéma d'étude
        </h2>

        <?php
        $analysisUnitFr  = $jsonRec['fr']['study_desc']['study_info']['analysis_unit'] ?? '[-]';
        $analysisUnitEn  = $jsonRec['en']['study_desc']['study_info']['analysis_unit'] ?? '[-]';

        $studyTypeFr     = $jsonRec['fr']['study_desc']['method']['method_notes'] ?? '[-]';
        $studyTypeEn     = $jsonRec['en']['study_desc']['method']['method_notes'] ?? '[-]';

        $researchPurposeFr = $jsonRec['fr']['additional']['interventionalStudy']['researchPurpose'] ?? [];
        $researchPurposeEn = $jsonRec['en']['additional']['interventionalStudy']['researchPurpose'] ?? [];

        $trialPhaseFr = $jsonRec['fr']['additional']['interventionalStudy']['trialPhase'] ?? [];
        $trialPhaseEn = $jsonRec['en']['additional']['interventionalStudy']['trialPhase'] ?? [];

        $interventionModelFr = $jsonRec['fr']['additional']['interventionalStudy']['interventionalStudyModel'] ?? '[-]';
        $interventionModelEn = $jsonRec['en']['additional']['interventionalStudy']['interventionalStudyModel'] ?? '[-]';

        $allocationModeFr = $jsonRec['fr']['additional']['allocation']['allocationMode'] ?? '[-]';
        $allocationModeEn = $jsonRec['en']['additional']['allocation']['allocationMode'] ?? '[-]';

        $allocationUnitFr = $jsonRec['fr']['additional']['allocation']['allocationUnit'] ?? '[-]';
        $allocationUnitEn = $jsonRec['en']['additional']['allocation']['allocationUnit'] ?? '[-]';

        $maskingTypeFr = $jsonRec['fr']['additional']['masking']['maskingType'] ?? '[-]';
        $maskingTypeEn = $jsonRec['en']['additional']['masking']['maskingType'] ?? '[-]';

        $blindedDetailsFr = $jsonRec['fr']['additional']['masking']['blindedMaskingDetails'] ?? [];
        $blindedDetailsEn = $jsonRec['en']['additional']['masking']['blindedMaskingDetails'] ?? [];

        if (is_array($researchPurposeFr)) $researchPurposeFr = implode(', ', $researchPurposeFr);
        if (is_array($researchPurposeEn)) $researchPurposeEn = implode(', ', $researchPurposeEn);
        if (is_array($trialPhaseFr)) $trialPhaseFr = implode(', ', $trialPhaseFr);
        if (is_array($trialPhaseEn)) $trialPhaseEn = implode(', ', $trialPhaseEn);
        if (is_array($blindedDetailsFr)) $blindedDetailsFr = implode(', ', $blindedDetailsFr);
        if (is_array($blindedDetailsEn)) $blindedDetailsEn = implode(', ', $blindedDetailsEn);

        $showStudyTypeBloc = ($studyTypeFr === 'interventionnelle' || $studyTypeEn === 'interventionnelle');
        $showStudyTypeBlocObs = ($studyTypeFr === 'Observationnelle' || $studyTypeEn === 'Observationnelle');


        ?>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Unité d'analyse" data-en="Analysis Unit">Unité d'analyse</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_attr($analysisUnitFr ?: '[-]'); ?>"
            data-en="<?php echo esc_attr($analysisUnitEn ?: '[-]'); ?>">
            <?php echo esc_html($analysisUnitFr  ?: '[-]'); ?>
          </div>
        </div>

        <div class="field-bloc">
          <div class="field-bloc__title lang-text" data-fr="Type de recherche" data-en="Study Type">Type de recherche</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_attr($studyTypeFr ?: '[-]'); ?>"
            data-en="<?php echo esc_attr($studyTypeEn ?: '[-]'); ?>">
            <?php echo esc_html($studyTypeFr  ?: '[-]'); ?>
          </div>
        </div>

        <?php if ($showStudyTypeBloc): ?>

          <h2 class="lang-text"
            data-fr="Étude interventionnelle"
            data-en="Interventional Study">
            Étude interventionnelle
          </h2>

          <div class="field-bloc">
            <div class="field-bloc__title lang-text" data-fr="Object principal de la recherche" data-en="Research Purpose">Object principal de la recherche</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_attr($researchPurposeFr ?: '[-]'); ?>"
              data-en="<?php echo esc_attr($researchPurposeEn ?: '[-]'); ?>">
              <?php echo esc_html($researchPurposeFr  ?: '[-]'); ?>
            </div>
          </div>

          <div class="field-bloc">
            <div class="field-bloc__title lang-text" data-fr="Phase de l'essai" data-en="Study Phase">Phase de l'essai</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_attr($trialPhaseFr ?: '[-]'); ?>"
              data-en="<?php echo esc_attr($trialPhaseEn ?: '[-]'); ?>">
              <?php echo esc_html($trialPhaseFr  ?: '[-]'); ?>
            </div>
          </div>

          <div class="field-bloc">
            <div class="field-bloc__title lang-text" data-fr="Schéma d’intervention" data-en="Intervention Model">Schéma d’intervention</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_attr($interventionModelFr ?: '[-]'); ?>"
              data-en="<?php echo esc_attr($interventionModelEn ?: '[-]'); ?>">
              <?php echo esc_html($interventionModelFr  ?: '[-]'); ?>
            </div>
          </div>

          <h2 class="field-content__h2 lang-text"
            data-fr="Allocation"
            data-en="Allocation">
            Allocation
          </h2>

          <div class="field-bloc">
            <div class="field-bloc__title lang-text" data-fr="Mode d'allocation" data-en="Allocation Type">Mode d'allocation</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_attr($allocationModeFr ?: '[-]'); ?>"
              data-en="<?php echo esc_attr($allocationModeEn ?: '[-]'); ?>">
              <?php echo esc_html($allocationModeFr  ?: '[-]'); ?>
            </div>
          </div>

          <div class="field-bloc">
            <div class="field-bloc__title lang-text" data-fr="Unité d'allocation" data-en="Allocation Unit">Unité d'allocation</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_attr($allocationUnitFr ?: '[-]'); ?>"
              data-en="<?php echo esc_attr($allocationUnitEn ?: '[-]'); ?>">
              <?php echo esc_html($allocationUnitFr  ?: '[-]'); ?>
            </div>
          </div>

          <h2 class="field-content__h2 lang-text"
            data-fr="Insu"
            data-en="Masking">
            Insu
          </h2>

          <div class="field-bloc">
            <div class="field-bloc__title lang-text" data-fr="Type Insu" data-en="Masking Type">Type Insu</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_attr($maskingTypeFr ?: '[-]'); ?>"
              data-en="<?php echo esc_attr($maskingTypeEn ?: '[-]'); ?>">
              <?php echo esc_html($maskingTypeFr ?: '[-]'); ?>
            </div>
          </div>

          <div class="field-bloc">
            <div class="field-bloc__title lang-text" data-fr="Insu en aveugle, précisions" data-en="Blinded Masking Details">Insu en aveugle, précisions</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_attr($blindedDetailsFr ?: '[-]'); ?>"
              data-en="<?php echo esc_attr($blindedDetailsEn ?: '[-]'); ?>">
              <?php echo esc_html($blindedDetailsFr ?: '[-]'); ?>
            </div>
          </div>



          <h2 class="field-content__h2 lang-text"
            data-fr="Bras / Groupes"
            data-en="Arms / Groups">
            Bras / Groupes
          </h2>

          <?php
          $armsFr = $jsonRec['fr']['additional']['arms'] ?? [];
          $armsEn = $jsonRec['en']['additional']['arms'] ?? [];

          // echo '<pre>'; print_r($armsFr); echo '</pre>';

          if (!is_array($armsFr)) $armsFr = [];
          if (!is_array($armsEn)) $armsEn = [];

          $maxArms = max(count($armsFr), count($armsEn));

          $showOtherColumn = false;

          foreach ($armsFr as $arm) {
            $type = mb_strtolower(trim($arm['type'] ?? ''));
            if ($type === 'autre' || $type === 'other') {
              $showOtherColumn = true;
              break;
            }
          }

          if (!$showOtherColumn) {
            foreach ($armsEn as $arm) {
              $type = mb_strtolower(trim($arm['type'] ?? ''));
              if ($type === 'autre' || $type === 'other') {
                $showOtherColumn = true;
                break;
              }
            }
          }
          ?>

          <table class="field-table">
            <thead>
              <tr>
                <th class="lang-text" data-fr="Type de bras" data-en="Arm Type">Type de bras</th>
                <?php if ($showOtherColumn): ?>
                  <th class="lang-text" data-fr="Autre type de bras, précisions" data-en="Other arm type, details">
                    Autre type de bras, précisions
                  </th>
                <?php endif; ?>
                <th class="lang-text" data-fr="Nom du bras" data-en="Arm Name">Nom du bras</th>
                <th class="lang-text" data-fr="Description du bras" data-en="Arm Description">Description du bras</th>
              </tr>
            </thead>

            <tbody>
              <?php if ($maxArms > 0): ?>
                <?php for ($i = 0; $i < $maxArms; $i++): ?>
                  <?php
                  $frItem = $armsFr[$i] ?? [];
                  $enItem = $armsEn[$i] ?? [];

                  $frType  = $frItem['type']        ?? '[-]';
                  $frOther = $frItem['typeOther']   ?? '[-]';
                  $frName  = $frItem['name']        ?? '[-]';
                  $frDesc  = $frItem['description'] ?? '[-]';

                  $enType  = $enItem['type']        ?? '[-]';
                  $enOther = $enItem['typeOther']   ?? '[-]';
                  $enName  = $enItem['name']        ?? '[-]';
                  $enDesc  = $enItem['description'] ?? '[-]';
                  ?>
                  <tr class="lang-row"
                    data-fr-type="<?php echo esc_attr($frType); ?>"
                    data-en-type="<?php echo esc_attr($enType); ?>"
                    data-fr-other="<?php echo esc_attr($frOther); ?>"
                    data-en-other="<?php echo esc_attr($enOther); ?>"
                    data-fr-name="<?php echo esc_attr($frName); ?>"
                    data-en-name="<?php echo esc_attr($enName); ?>"
                    data-fr-desc="<?php echo esc_attr($frDesc); ?>"
                    data-en-desc="<?php echo esc_attr($enDesc); ?>">

                    <td>
                      <span class="lang-text"
                        data-fr="<?php echo esc_attr($frType ?: '[-]'); ?>"
                        data-en="<?php echo esc_attr($enType ?: '[-]'); ?>">
                        <?php echo esc_html($frType ?: '[-]'); ?>
                      </span>
                    </td>

                    <?php if ($showOtherColumn): ?>
                      <td>
                        <span class="lang-text"
                          data-fr="<?php echo esc_attr($frOther ?: '[-]'); ?>"
                          data-en="<?php echo esc_attr($enOther ?: '[-]'); ?>">
                          <?php echo esc_html($frOther ?: '[-]'); ?>
                        </span>
                      </td>
                    <?php endif; ?>

                    <td>
                      <span class="lang-text"
                        data-fr="<?php echo esc_attr($frName ?: '[-]'); ?>"
                        data-en="<?php echo esc_attr($enName ?: '[-]'); ?>">
                        <?php echo esc_html($frName ?: '[-]'); ?>
                      </span>
                    </td>

                    <td>
                      <span class="lang-text"
                        data-fr="<?php echo esc_attr($frDesc ?: '[-]'); ?>"
                        data-en="<?php echo esc_attr($enDesc ?: '[-]'); ?>">
                        <?php echo esc_html($frDesc ?: '[-]'); ?>
                      </span>
                    </td>
                  </tr>
                <?php endfor; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4">-</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>

        <?php endif; ?>

        <h2 class="lang-text"
          data-fr="Intervention/exposition"
          data-en="Intervention/Exposure">
          Intervention/exposition
        </h2>
        <?php
        $intervArrFr = $jsonRec['fr']['additional']['intervention'] ?? [];
        $intervArrEn = $jsonRec['en']['additional']['intervention'] ?? [];

        if (!is_array($intervArrFr)) $intervArrFr = [];
        if (!is_array($intervArrEn)) $intervArrEn = [];

        $maxInterv = max(count($intervArrFr), count($intervArrEn));

        $showOtherColumn = false;

        foreach ($intervArrFr as $item) {
          $type = mb_strtolower(trim($item['type'] ?? ''));
          if ($type === 'autres' || $type === 'other') {
            $showOtherColumn = true;
            break;
          }
        }

        if (!$showOtherColumn) {
          foreach ($intervArrEn as $item) {
            $type = mb_strtolower(trim($item['type'] ?? ''));
            if ($type === 'autres' || $type === 'other') {
              $showOtherColumn = true;
              break;
            }
          }
        }
        ?>

        <table class="field-table">
          <thead>
            <tr>
              <th class="lang-text" data-fr="Nom de l'intervention/exposition" data-en="Intervention/Exposure Name">
                Nom
              </th>
              <th class="lang-text" data-fr="Type d'intervention/exposition" data-en="Intervention/Exposure Type">
                Type
              </th>
              <?php if ($showOtherColumn): ?>
                <th class="lang-text" data-fr="Autre type" data-en="Other Type">
                  Autre type
                </th>
              <?php endif; ?>
              <th class="lang-text" data-fr="Description" data-en="Description">
                Description
              </th>
            </tr>
          </thead>

          <tbody>
            <?php if ($maxInterv > 0): ?>
              <?php for ($i = 0; $i < $maxInterv; $i++): ?>
                <?php
                $frItem = $intervArrFr[$i] ?? [];
                $enItem = $intervArrEn[$i] ?? [];

                $frName = $frItem['name'] ?? '[-]';
                $enName = $enItem['name'] ?? '[-]';

                $frType = $frItem['type'] ?? '[-]';
                $enType = $enItem['type'] ?? '[-]';

                $frTypeOther = $frItem['typeOther'] ?? '[-]';
                $enTypeOther = $enItem['typeOther'] ?? '[-]';

                $frDesc = $frItem['description'] ?? '[-]';
                $enDesc = $enItem['description'] ?? '[-]';
                ?>
                <tr class="lang-row"
                  data-fr-name="<?php echo esc_attr($frName); ?>"
                  data-en-name="<?php echo esc_attr($enName); ?>"
                  data-fr-type="<?php echo esc_attr($frType); ?>"
                  data-en-type="<?php echo esc_attr($enType); ?>"
                  data-fr-typeother="<?php echo esc_attr($frTypeOther); ?>"
                  data-en-typeother="<?php echo esc_attr($enTypeOther); ?>"
                  data-fr-desc="<?php echo esc_attr($frDesc); ?>"
                  data-en-desc="<?php echo esc_attr($enDesc); ?>">

                  <!-- Name -->
                  <td>
                    <span class="lang-text"
                      data-fr="<?php echo esc_attr($frName ?: '[-]'); ?>"
                      data-en="<?php echo esc_attr($enName ?: '[-]'); ?>">
                      <?php echo esc_html($frName ?: '[-]'); ?>
                    </span>
                  </td>

                  <td>
                    <span class="lang-text"
                      data-fr="<?php echo esc_attr($frType ?: '[-]'); ?>"
                      data-en="<?php echo esc_attr($enType ?: '[-]'); ?>">
                      <?php echo esc_html($frType ?: '[-]'); ?>
                    </span>
                  </td>

                  <?php if ($showOtherColumn): ?>
                    <td>
                      <span class="lang-text"
                        data-fr="<?php echo esc_attr($frTypeOther ?: '[-]'); ?>"
                        data-en="<?php echo esc_attr($enTypeOther ?: '[-]'); ?>">
                        <?php echo esc_html($frTypeOther ?: '-'); ?>
                      </span>
                    </td>
                  <?php endif; ?>

                  <td>
                    <span class="lang-text"
                      data-fr="<?php echo esc_attr($frDesc ?: '[-]'); ?>"
                      data-en="<?php echo esc_attr($enDesc ?: '[-]'); ?>">
                      <?php echo esc_html($frDesc ?: '[-]'); ?>
                    </span>
                  </td>

                </tr>
              <?php endfor; ?>
            <?php else: ?>
              <tr>
                <td colspan="4">-</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>



        <?php if ($showStudyTypeBlocObs): ?>
          <h2 class="field-content__h2 lang-text"
            data-fr="Observationnelle"
            data-en="Observational">
            Observationnelle
          </h2>


          <?php
          $observationalModelFr = $jsonRec['fr']['study_desc']['method']['data_collection']['time_method'] ?? '[-]';
          $observationalModelEn = $jsonRec['en']['study_desc']['method']['data_collection']['time_method'] ?? '[-]';

          $otherResearchTypeFr = $jsonRec['fr']['additional']['otherResearchType']['otherResearchTypeDetails'] ?? '[-]';
          $otherResearchTypeEn = $jsonRec['en']['additional']['otherResearchType']['otherResearchTypeDetails'] ?? '[-]';
          ?>

          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Modèle de l’étude observationnelle"
              data-en="Observational Study Design">
              Modèle de l’étude observationnelle
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($observationalModelFr ?: '[-]'); ?>"
              data-en="<?php echo esc_html($observationalModelEn ?: '[-]'); ?>">
              <?php echo esc_html($observationalModelFr ?: '[-]'); ?>
            </div>
          </div>

          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Autre type de recherche, précisions"
              data-en="Other Research Type Details">
              Autre type de recherche, précisions
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($otherResearchTypeFr  ?: '[-]'); ?>"
              data-en="<?php echo esc_html($otherResearchTypeEn  ?: '[-]'); ?>">
              <?php echo esc_html($otherResearchTypeFr ?: '[-]'); ?>
            </div>
          </div>



          <h3 class="field-content__h2 lang-text"
            data-fr="Étude longitudinale ou cohorte"
            data-en="Longitudinal or cohort study">
            Étude longitudinale ou cohorte
          </h3>

          <?php
          $recruitmentTimingFr = $jsonRec['fr']['additional']['cohortLongitudinal']['recrutementTiming'] ?? '[-]';
          $recruitmentTimingEn = $jsonRec['en']['additional']['cohortLongitudinal']['recrutementTiming'] ?? '[-]';

          if (is_string($recruitmentTimingFr) && str_starts_with(trim($recruitmentTimingFr), '[')) {
            $decodedFr = json_decode($recruitmentTimingFr, true);
            if (is_array($decodedFr)) {
              $recruitmentTimingFr = implode(', ', $decodedFr);
            }
          } elseif (is_array($recruitmentTimingFr)) {
            $recruitmentTimingFr = implode(', ', $recruitmentTimingFr);
          }

          if (is_string($recruitmentTimingEn) && str_starts_with(trim($recruitmentTimingEn), '[')) {
            $decodedEn = json_decode($recruitmentTimingEn, true);
            if (is_array($decodedEn)) {
              $recruitmentTimingEn = implode(', ', $decodedEn);
            }
          } elseif (is_array($recruitmentTimingEn)) {
            $recruitmentTimingEn = implode(', ', $recruitmentTimingEn);
          }
          ?>

          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Temporalité du recrutement"
              data-en="Recruitment Timing">
              Temporalité du recrutement
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($recruitmentTimingFr); ?>"
              data-en="<?php echo esc_html($recruitmentTimingEn); ?>">
              <?php echo esc_html($recruitmentTimingFr ?: '-'); ?>
            </div>
          </div>
        <?php endif; ?>


        <h2 class="field-content__h2 lang-text"
          data-fr="Groupes à l'inclusion"
          data-en="Inclusion Groups">
          Groupes à l'inclusion
        </h2>

        <?php
        $inclusionGroupsFr = $jsonRec['fr']['additional']['inclusionGroups'] ?? [];
        $inclusionGroupsEn = $jsonRec['en']['additional']['inclusionGroups'] ?? [];

        // Ensure array format
        if (!is_array($inclusionGroupsFr)) $inclusionGroupsFr = [];
        if (!is_array($inclusionGroupsEn)) $inclusionGroupsEn = [];

        $maxInclusionGroups = max(count($inclusionGroupsFr), count($inclusionGroupsEn));
        ?>

        <table class="field-table">
          <thead>
            <tr>
              <th class="lang-text" data-fr="Nom du groupe" data-en="Group name">Nom du groupe</th>
              <th class="lang-text" data-fr="Description du groupe" data-en="Group description">Description du groupe</th>
              <th class="lang-text" data-fr="Exposition associée au groupe" data-en="Exposition associated to the inclusion group">Exposition associée au groupe</th>
            </tr>
          </thead>

          <tbody>
            <?php if ($maxInclusionGroups > 0): ?>
              <?php for ($i = 0; $i < $maxInclusionGroups; $i++): ?>
                <?php
                $frItem = $inclusionGroupsFr[$i] ?? [];
                $enItem = $inclusionGroupsEn[$i] ?? [];

                $frName = $frItem['name'] ?? '[-]';
                $enName = $enItem['name'] ?? '[-]';

                $frDescription = $frItem['description'] ?? '[-]';
                $enDescription = $enItem['description'] ?? '[-]';

                $frInterExpo = $frItem['interventionExposition'] ?? '[-]';
                $enInterExpo = $enItem['interventionExposition'] ?? '[-]';
                ?>
                <tr class="lang-row"
                  data-fr-name="<?php echo esc_attr($frName); ?>"
                  data-en-name="<?php echo esc_attr($enName); ?>"
                  data-fr-description="<?php echo esc_attr($frDescription); ?>"
                  data-en-description="<?php echo esc_attr($enDescription); ?>"
                  data-fr-interexpo="<?php echo esc_attr($frInterExpo); ?>"
                  data-en-interexpo="<?php echo esc_attr($enInterExpo); ?>">

                  <td>
                    <span class="lang-text"
                      data-fr="<?php echo esc_attr($frName ?: '[-]'); ?>"
                      data-en="<?php echo esc_attr($enName ?: '[-]'); ?>">
                      <?php echo esc_html($frName ?: '[-]'); ?>
                    </span>
                  </td>

                  <td>
                    <span class="lang-text"
                      data-fr="<?php echo esc_attr($frDescription ?: '[-]'); ?>"
                      data-en="<?php echo esc_attr($enDescription ?: '[-]'); ?>">
                      <?php echo esc_html($frDescription ?: '[-]'); ?>
                    </span>
                  </td>

                  <td>
                    <span class="lang-text"
                      data-fr="<?php echo esc_attr($frInterExpo ?: '[-]'); ?>"
                      data-en="<?php echo esc_attr($enInterExpo ?: '[-]'); ?>">
                      <?php echo esc_html($frInterExpo ?: '[-]'); ?>
                    </span>
                  </td>

                </tr>
              <?php endfor; ?>
            <?php else: ?>
              <tr>
                <td colspan="3">-</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>


      </div>

      <div class="submenu-study" id="collectereutilisation">
        <h2 class="field-content__h1 lang-text"
          data-fr="Collecte et réutilisation de données"
          data-en="Data collected or produced specifically for the study.">
          Collecte et réutilisation de données
        </h2>

        <?php
        $frequencyFr = $jsonRec['fr']['study_desc']['method']['data_collection']['frequency'] ?? '[-]';
        $frequencyEn = $jsonRec['en']['study_desc']['method']['data_collection']['frequency'] ?? '[-]';

        $collModeFr = $jsonRec['fr']['study_desc']['method']['data_collection']['coll_mode'] ?? [];
        $collModeEn = $jsonRec['en']['study_desc']['method']['data_collection']['coll_mode'] ?? [];

        $modeOtherFr = $jsonRec['fr']['additional']['collectionProcess']['collectionModeOther'] ?? '[-]';
        $modeOtherEn = $jsonRec['en']['additional']['collectionProcess']['collectionModeOther'] ?? '[-]';

        $modeDetailsFr = $jsonRec['fr']['additional']['collectionProcess']['collectionModeDetails'] ?? '[-]';
        $modeDetailsEn = $jsonRec['en']['additional']['collectionProcess']['collectionModeDetails'] ?? '[-]';

        if (is_array($collModeFr)) $collModeFr = implode(', ', $collModeFr);
        if (is_array($collModeEn)) $collModeEn = implode(', ', $collModeEn);


        $showOtherMode = false;

        $collModeFrLower = mb_strtolower($collModeFr);
        $collModeEnLower = mb_strtolower($collModeEn);

        if (str_contains($collModeFrLower, 'autre') || str_contains($collModeEnLower, 'other')) {
          $showOtherMode = true;
        }
        ?>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
            data-fr="Fréquence de la collecte"
            data-en="Collection Frequency">
            Fréquence de la collecte
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($frequencyFr ?: '[-]'); ?>"
            data-en="<?php echo esc_html($frequencyEn ?: '[-]'); ?>">
            <?php echo esc_html($frequencyFr ?: '[-]'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
            data-fr="Mode de collecte"
            data-en="Collection Mode">
            Mode de collecte
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($collModeFr ?: '[-]'); ?>"
            data-en="<?php echo esc_html($collModeEn ?: '[-]'); ?>">
            <?php echo esc_html($collModeFr ?: '[-]'); ?>
          </div>
        </div>
        <?php if ($showOtherMode): ?>

          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Autre mode de collecte, précisions"
              data-en="Other collection mode, details">
              Autre mode de collecte, précisions
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($modeOtherFr ?: '[-]'); ?>"
              data-en="<?php echo esc_html($modeOtherEn ?: '[-]'); ?>">
              <?php echo esc_html($modeOtherFr ?: '[-]'); ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
            data-fr="Mode de collecte, précisions"
            data-en="Collection mode, details">
            Mode de collecte, précisions
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($modeDetailsFr ?: '[-]'); ?>"
            data-en="<?php echo esc_html($modeDetailsEn ?: '[-]'); ?>">
            <?php echo esc_html($modeDetailsFr ?: '[-]'); ?>
          </div>
        </div>

        <?php

        if (!function_exists('parseStringArrayNormalized')) {
          function parseStringArrayNormalized($str)
          {
            if (is_array($str)) return $str;
            if (empty($str)) return [];
            // remove brackets and spaces
            $clean = trim($str, "[] \t\n\r\0\x0B'\"");
            // split by commas
            $parts = preg_split('/\s*,\s*/', $clean);
            // clean quotes and spaces
            $parts = array_map(fn($p) => trim($p, "'\" "), $parts);
            // remove empty values
            return array_values(array_filter($parts, fn($v) => $v !== ''));
          }
        }


        $inclusionStrategyFr = $jsonRec['fr']['additional']['dataCollection']['inclusionStrategy'] ?? [];
        $inclusionStrategyEn = $jsonRec['en']['additional']['dataCollection']['inclusionStrategy'] ?? [];

        $inclusionStrategyOtherFr = $jsonRec['fr']['additional']['dataCollection']['inclusionStrategyOther'] ?? '[-]';
        $inclusionStrategyOtherEn = $jsonRec['en']['additional']['dataCollection']['inclusionStrategyOther'] ?? '[-]';

        $samplingProcFr = $jsonRec['fr']['study_desc']['method']['data_collection']['sampling_procedure'] ?? [];
        $samplingProcEn = $jsonRec['en']['study_desc']['method']['data_collection']['sampling_procedure'] ?? [];

        $samplingModeOtherFr = $jsonRec['fr']['additional']['dataCollection']['samplingModeOther'] ?? '[-]';
        $samplingModeOtherEn = $jsonRec['en']['additional']['dataCollection']['samplingModeOther'] ?? '[-]';

        $recruitmentSourceFr = $jsonRec['fr']['study_desc']['method']['data_collection']['sample_frame']['frame_unit']['unit_type'] ?? [];
        $recruitmentSourceEn = $jsonRec['en']['study_desc']['method']['data_collection']['sample_frame']['frame_unit']['unit_type'] ?? [];

        $recruitmentSourceOtherFr = $jsonRec['fr']['additional']['dataCollection']['recruitmentSourceOther'] ?? '[-]';
        $recruitmentSourceOtherEn = $jsonRec['en']['additional']['dataCollection']['recruitmentSourceOther'] ?? '[-]';

        if (is_array($inclusionStrategyFr)) $inclusionStrategyFr = implode(', ', $inclusionStrategyFr);
        if (is_array($inclusionStrategyEn)) $inclusionStrategyEn = implode(', ', $inclusionStrategyEn);

        $samplingProcFr = parseStringArrayNormalized($samplingProcFr);
        $samplingProcFr = implode(', ', $samplingProcFr);
        $samplingProcEn = parseStringArrayNormalized($samplingProcEn);
        $samplingProcEn = implode(', ', $samplingProcEn);

        $recruitmentSourceFr = parseStringArrayNormalized($recruitmentSourceFr);
        $recruitmentSourceFr = implode(', ', $recruitmentSourceFr);
        $recruitmentSourceEn = parseStringArrayNormalized($recruitmentSourceEn);
        $recruitmentSourceEn = implode(', ', $recruitmentSourceEn);

        $showInclusionOther = false;

        $lowerFr = mb_strtolower($inclusionStrategyFr);
        $lowerEn = mb_strtolower($inclusionStrategyEn);

        if (str_contains($lowerFr, 'autre') || str_contains($lowerEn, 'other')) {
          $showInclusionOther = true;
        }


        $showSamplingOther = false;

        $lowerSamplingFr = mb_strtolower($samplingProcFr);
        $lowerSamplingEn = mb_strtolower($samplingProcEn);

        if (str_contains($lowerSamplingFr, 'autre') || str_contains($lowerSamplingEn, 'other')) {
          $showSamplingOther = true;
        }

        $showRecruitmentOther = false;

        $lowerRecruitFr = mb_strtolower($recruitmentSourceFr);
        $lowerRecruitEn = mb_strtolower($recruitmentSourceEn);

        if (str_contains($lowerRecruitFr, 'autre') || str_contains($lowerRecruitEn, 'other')) {
          $showRecruitmentOther = true;
        }
        ?>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
            data-fr="Stratégie d'inclusion"
            data-en="Inclusion Strategy">
            Stratégie d'inclusion
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($inclusionStrategyFr ?: '[-]'); ?>"
            data-en="<?php echo esc_html($inclusionStrategyEn ?: '[-]'); ?>">
            <?php echo esc_html($inclusionStrategyFr ?: '[-]'); ?>
          </div>
        </div>
        <?php if ($showInclusionOther): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Autre stratégie d'inclusion, précisions"
              data-en="Other inclusion strategy, details">
              Autre stratégie d'inclusion, précisions
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($inclusionStrategyOtherFr ?: '[-]'); ?>"
              data-en="<?php echo esc_html($inclusionStrategyOtherEn ?: '[-]'); ?>">
              <?php echo esc_html($inclusionStrategyOtherFr ?: '[-]'); ?>
            </div>
          </div>
        <?php endif; ?>


        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
            data-fr="Procédure d'échantillonnage à l'inclusion"
            data-en="Sampling Procedure at Inclusion">
            Procédure d'échantillonnage à l'inclusion
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($samplingProcFr ?: '[-]'); ?>"
            data-en="<?php echo esc_html($samplingProcEn ?: '[-]'); ?>">
            <?php echo esc_html($samplingProcFr ?: '[-]'); ?>
          </div>
        </div>
        <?php if ($showSamplingOther): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Autre procédure d'échantillonnage, précisions"
              data-en="Other sampling mode, details">
              Autre procédure d'échantillonnage, précisions
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($samplingModeOtherFr ?: '[-]'); ?>"
              data-en="<?php echo esc_html($samplingModeOtherEn ?: '[-]'); ?>">
              <?php echo esc_html($samplingModeOtherFr ?: '[-]'); ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
            data-fr="Source de recrutement des participants"
            data-en="Participant Recruitment Source">
            Source de recrutement des participants
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($recruitmentSourceFr ?: '[-]'); ?>"
            data-en="<?php echo esc_html($recruitmentSourceEn ?: '[-]'); ?>">
            <?php echo esc_html($recruitmentSourceFr  ?: '[-]'); ?>
          </div>
        </div>
        <?php if ($showRecruitmentOther): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Autre source de recrutement, précisions"
              data-en="Other recruitment source, details">
              Autre source de recrutement, précisions
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($recruitmentSourceOtherFr ?: '[-]'); ?>"
              data-en="<?php echo esc_html($recruitmentSourceOtherEn ?: '[-]'); ?>">
              <?php echo esc_html($recruitmentSourceOtherFr  ?: '[-]'); ?>
            </div>
          </div>
        <?php endif; ?>


        <h2 class="field-content__h2 lang-text"
          data-fr="Dates de la collecte"
          data-en="Collection Dates">
          Dates de la collecte
        </h2>

        <?php
        $collDatesFr = $jsonRec['fr']['study_desc']['study_info']['coll_dates'][0] ?? [];
        $collDatesEn = $jsonRec['en']['study_desc']['study_info']['coll_dates'][0] ?? [];

        $collectionStartDateFr = $collDatesFr['start'] ?? '[-]';
        $collectionEndDateFr   = $collDatesFr['end'] ?? '[-]';

        $collectionStartDateEn = $collDatesEn['start'] ?? '[-]';
        $collectionEndDateEn   = $collDatesEn['end'] ?? '[-]';
        ?>

        <table class="field-table">
          <thead>
            <tr>
              <th class="lang-text"
                data-fr="Date de début de la collecte (recrutement du 1er participant)"
                data-en="Collection Start Date">
                Date de début de la collecte (recrutement du 1er participant)
              </th>
              <th class="lang-text"
                data-fr="Date de fin de la collecte (dernier suivi du dernier participant)"
                data-en="Collection End Date">
                Date de fin de la collecte (dernier suivi du dernier participant)
              </th>
            </tr>
          </thead>

          <tbody>
            <tr class="lang-row"
              data-fr-start="<?php echo esc_attr($collectionStartDateFr); ?>"
              data-en-start="<?php echo esc_attr($collectionStartDateEn); ?>"
              data-fr-end="<?php echo esc_attr($collectionEndDateFr); ?>"
              data-en-end="<?php echo esc_attr($collectionEndDateEn); ?>">

              <td>
                <span class="lang-text"
                  data-fr="<?php echo esc_attr($collectionStartDateFr ?: '[-]'); ?>"
                  data-en="<?php echo esc_attr($collectionStartDateEn ?: '[-]'); ?>">
                  <?php echo esc_html($collectionStartDateFr ?: '[-]'); ?>
                </span>
              </td>

              <td>
                <span class="lang-text"
                  data-fr="<?php echo esc_attr($collectionEndDateFr ?: '[-]'); ?>"
                  data-en="<?php echo esc_attr($collectionEndDateEn ?: '[-]'); ?>">
                  <?php echo esc_html($collectionEndDateFr ?: '[-]'); ?>
                </span>
              </td>

            </tr>
          </tbody>
        </table>


        <h2 class="field-content__h2 lang-text"
          data-fr="Suivi actif des participants"
          data-en="Active follow-up">
          Suivi actif des participants
        </h2>

        <?php
        $isActiveFollowUpFr = $jsonRec['fr']['additional']['activeFollowUp']['isActiveFollowUp'] ?? '[-]';
        $isActiveFollowUpEn = $jsonRec['en']['additional']['activeFollowUp']['isActiveFollowUp'] ?? '[-]';

        $followUpMethodFr = $jsonRec['fr']['study_desc']['method']['notes'] ?? [];
        $followUpMethodEn = $jsonRec['en']['study_desc']['method']['notes'] ?? [];

        $followUpModeOtherFr = $jsonRec['fr']['additional']['activeFollowUp']['followUpModeOther'] ?? '[-]';
        $followUpModeOtherEn = $jsonRec['en']['additional']['activeFollowUp']['followUpModeOther'] ?? '[-]';

        $isDataIntegrationFr = $jsonRec['fr']['additional']['dataCollectionIntegration']['isDataIntegration'] ?? '[-]';
        $isDataIntegrationEn = $jsonRec['en']['additional']['dataCollectionIntegration']['isDataIntegration'] ?? '[-]';

        if (is_array($followUpMethodFr)) $followUpMethodFr = implode(', ', $followUpMethodFr);
        if (is_array($followUpMethodEn)) $followUpMethodEn = implode(', ', $followUpMethodEn);

        $flagFr = mb_strtolower(trim($isActiveFollowUpFr));
        $flagEn = mb_strtolower(trim($isActiveFollowUpEn));
        $showFollowUpDetails = in_array($flagFr, ['oui', 'yes', 'true', 'vrai'], true)
          || in_array($flagEn, ['oui', 'yes', 'true', 'vrai'], true);

        $lowerFollowUpFr = mb_strtolower($followUpMethodFr);
        $lowerFollowUpEn = mb_strtolower($followUpMethodEn);
        $showFollowUpOther = str_contains($lowerFollowUpFr, 'autre') || str_contains($lowerFollowUpEn, 'other');

        $flagFr = mb_strtolower(trim($isDataIntegrationFr));
        $flagEn = mb_strtolower(trim($isDataIntegrationEn));

        $showDataIntegrationBloc = in_array($flagFr, ['oui', 'yes', 'true', 'vrai'], true)
          || in_array($flagEn, ['oui', 'yes', 'true', 'vrai'], true);


        $showOtherCol = false;
        foreach ($sourcesFr as $item) {
          $srcType = is_array($item['srcOrig'] ?? null) ? implode(', ', $item['srcOrig']) : ($item['srcOrig'] ?? '');
          if (str_contains(mb_strtolower($srcType), 'autre') || str_contains(mb_strtolower($srcType), 'other')) {
            $showOtherCol = true;
            break;
          }
        }
        if (!$showOtherCol) {
          foreach ($sourcesEn as $item) {
            $srcType = is_array($item['srcOrig'] ?? null) ? implode(', ', $item['srcOrig']) : ($item['srcOrig'] ?? '');
            if (str_contains(mb_strtolower($srcType), 'autre') || str_contains(mb_strtolower($srcType), 'other')) {
              $showOtherCol = true;
              break;
            }
          }
        }
        ?>

        <table class="field-table">
          <thead>
            <tr>
              <th class="lang-text" data-fr="Suivi actif des participants ?" data-en="Active Follow-up?">Suivi actif des participants ?</th>
              <?php if ($showFollowUpDetails): ?>
                <th class="lang-text" data-fr="Modalités de suivi" data-en="Follow-up Method">
                  Modalités de suivi
                </th>
                <?php if ($showFollowUpOther): ?>
                  <th class="lang-text" data-fr="Autre modalités de suivi, précisions" data-en="Other follow-up method, details">
                    Autre modalités de suivi, précisions
                  </th>
                <?php endif; ?>
              <?php endif; ?>
              <th class="lang-text" data-fr="Utilisation des données individuelles issues d'autres sources des données" data-en="Use of Individual Data from Other Data Sources">
                Utilisation des données individuelles issues d'autres sources des données
              </th>
            </tr>
          </thead>
          <tbody>
            <tr class="lang-row"
              data-fr-active="<?php echo esc_attr($isActiveFollowUpFr); ?>"
              data-en-active="<?php echo esc_attr($isActiveFollowUpEn); ?>"
              data-fr-method="<?php echo esc_attr($followUpMethodFr); ?>"
              data-en-method="<?php echo esc_attr($followUpMethodEn); ?>"
              data-fr-other="<?php echo esc_attr($followUpModeOtherFr); ?>"
              data-en-other="<?php echo esc_attr($followUpModeOtherEn); ?>"
              data-fr-dataint="<?php echo esc_attr($isDataIntegrationFr); ?>"
              data-en-dataint="<?php echo esc_attr($isDataIntegrationEn); ?>">
              <td>
                <span class="lang-text"
                  data-fr="<?php echo esc_attr($isActiveFollowUpFr ?: '[-]'); ?>"
                  data-en="<?php echo esc_attr($isActiveFollowUpEn ?: '[-]'); ?>">
                  <?php echo esc_html($isActiveFollowUpFr ?: '[-]'); ?>
                </span>
              </td>
              <?php if ($showFollowUpDetails): ?>
                <td><span class="lang-text"
                    data-fr="<?php echo esc_attr($followUpMethodFr ?: '[-]'); ?>"
                    data-en="<?php echo esc_attr($followUpMethodEn ?: '[-]'); ?>">
                    <?php echo esc_html($followUpMethodFr ?: '[-]'); ?>
                  </span>
                </td>
                <?php if ($showFollowUpOther): ?>
                  <td><span class="lang-text"
                      data-fr="<?php echo esc_attr($followUpModeOtherFr ?: '[-]'); ?>"
                      data-en="<?php echo esc_attr($followUpModeOtherEn ?: '[-]'); ?>">
                      <?php echo esc_html($followUpModeOtherFr ?: '-'); ?>
                    </span></td>
                <?php endif; ?>
              <?php endif; ?>
              <td><span class="lang-text"
                  data-fr="<?php echo esc_attr($isDataIntegrationFr ?: '[-]'); ?>"
                  data-en="<?php echo esc_attr($isDataIntegrationEn ?: '[-]'); ?>">
                  <?php echo esc_html($isDataIntegrationFr ?: '[-]'); ?>
                </span></td>
            </tr>
          </tbody>
        </table>

        <?php if ($showDataIntegrationBloc): ?>
          <h2 class="field-content__h2 lang-text"
            data-fr="Sources tierces"
            data-en="Third-Party Sources">
            Sources tierces
          </h2>

          <?php
          $sourcesFr = $jsonRec['fr']['study_desc']['method']['data_collection']['sources'] ?? [];
          $sourcesEn = $jsonRec['en']['study_desc']['method']['data_collection']['sources'] ?? [];

          if (!is_array($sourcesFr)) $sourcesFr = [];
          if (!is_array($sourcesEn)) $sourcesEn = [];

          $maxSources = max(count($sourcesFr), count($sourcesEn));
          ?>

          <table class="field-table">
            <thead>
              <tr>
                <th class="lang-text" data-fr="Description de la source" data-en="Source Description">
                  Description de la source
                </th>
                <th class="lang-text" data-fr="Type de source" data-en="Source Type">
                  Type de source
                </th>
                <th class="lang-text" data-fr="Objectif de l'intégration de la source" data-en="Source Integration Purpose">
                  Objectif de l'intégration de la source
                </th>
                <?php if ($showOtherCol): ?>
                  <th class="lang-text" data-fr="Autre, précisions" data-en="Other, details">
                    Autre, précisions
                  </th>
                <?php endif; ?>
              </tr>
            </thead>

            <tbody>
              <?php if ($maxSources > 0): ?>
                <?php for ($i = 0; $i < $maxSources; $i++): ?>
                  <?php
                  $frItem = $sourcesFr[$i] ?? [];
                  $enItem = $sourcesEn[$i] ?? [];

                  $frCitation = $frItem['citation'] ?? '[-]';
                  $enCitation = $enItem['citation'] ?? '[-]';

                  $frSrcOrig = $frItem['srcOrig'] ?? [];
                  $enSrcOrig = $enItem['srcOrig'] ?? [];

                  if (is_array($frSrcOrig)) $frSrcOrig = implode(', ', $frSrcOrig);
                  if (is_array($enSrcOrig)) $enSrcOrig = implode(', ', $enSrcOrig);

                  $frPurpose = $frItem['notes']['subject_sourcePurpose'] ?? '[-]';
                  $enPurpose = $enItem['notes']['subject_sourcePurpose'] ?? '[-]';

                  $frOther = $frItem['otherSourceType'] ?? '[-]';
                  $enOther = $enItem['otherSourceType'] ?? '[-]';
                  ?>
                  <tr class="lang-row"
                    data-fr-citation="<?php echo esc_attr($frCitation); ?>"
                    data-en-citation="<?php echo esc_attr($enCitation); ?>"
                    data-fr-src="<?php echo esc_attr($frSrcOrig); ?>"
                    data-en-src="<?php echo esc_attr($enSrcOrig); ?>"
                    data-fr-purpose="<?php echo esc_attr($frPurpose); ?>"
                    data-en-purpose="<?php echo esc_attr($enPurpose); ?>"
                    data-fr-other="<?php echo esc_attr($frOther); ?>"
                    data-en-other="<?php echo esc_attr($enOther); ?>">

                    <td>
                      <span class="lang-text"
                        data-fr="<?php echo esc_attr($frCitation ?: '[-]'); ?>"
                        data-en="<?php echo esc_attr($enCitation ?: '[-]'); ?>">
                        <?php echo esc_html($frCitation ?: '[-]'); ?>
                      </span>
                    </td>

                    <td>
                      <span class="lang-text"
                        data-fr="<?php echo esc_attr($frSrcOrig ?: '[-]'); ?>"
                        data-en="<?php echo esc_attr($enSrcOrig ?: '[-]'); ?>">
                        <?php echo esc_html($frSrcOrig ?: '[-]'); ?>
                      </span>
                    </td>

                    <td>
                      <span class="lang-text"
                        data-fr="<?php echo esc_attr($frPurpose ?: '[-]'); ?>"
                        data-en="<?php echo esc_attr($enPurpose ?: '[-]'); ?>">
                        <?php echo esc_html($frPurpose ?: '-'); ?>
                      </span>
                    </td>

                    <?php if ($showOtherCol): ?>
                      <td>
                        <span class="lang-text"
                          data-fr="<?php echo esc_attr($frOther ?: '[-]'); ?>"
                          data-en="<?php echo esc_attr($enOther ?: '[-]'); ?>">
                          <?php echo esc_html($frOther ?: '[-]'); ?>
                        </span>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endfor; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4">-</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>

        <?php endif; ?>


      </div>

      <div class="submenu-study" id="population">
        <h2 class="field-content__h1 lang-text"
          data-fr="Population"
          data-en="Population">
          Population
        </h2>

        <h2 class="field-content__h2 lang-text"
          data-fr="Caractéristiques démographiques"
          data-en="Demographic Characteristics">
          Caractéristiques démographiques
        </h2>

        <?php
        $universeFr = $jsonRec['fr']['additional']['universe'] ?? [];
        $universeEn = $jsonRec['en']['additional']['universe'] ?? [];

        $frSexe = $universeFr['sexe'] ?? [];
        $enSexe = $universeEn['sexe'] ?? [];

        $frAge = $universeFr['level_age_Clusion_I'] ?? [];
        $enAge = $universeEn['level_age_Clusion_I'] ?? [];

        $frType = $universeFr['level_type_clusion_I'] ?? '[-]';
        $enType = $universeEn['level_type_clusion_I'] ?? '[-]';

        $frTypeAutre = $universeFr['type_inclusion_autre'] ?? '[-]';
        $enTypeAutre = $universeEn['type_inclusion_autre'] ?? '[-]';

        if (is_array($frSexe)) $frSexe = implode(', ', $frSexe);
        if (is_array($enSexe)) $enSexe = implode(', ', $enSexe);

        if (is_array($frAge)) $frAge = implode(', ', $frAge);
        if (is_array($enAge)) $enAge = implode(', ', $enAge);

        $flagFr = mb_strtolower(trim($frType));
        $flagEn = mb_strtolower(trim($enType));

        $showTypeAutre = in_array($flagFr, ['autre', 'other'], true)
          || in_array($flagEn, ['autre', 'other'], true);
        ?>

        <div class="field-bloc">
          <div class="field-bloc__title lang-text" data-fr="Sexe" data-en="Sex">Sexe</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_attr($frSexe ?: '[-]'); ?>"
            data-en="<?php echo esc_attr($enSexe ?: '[-]'); ?>">
            <?php echo esc_html($frSexe ?: '[-]'); ?>
          </div>
        </div>

        <div class="field-bloc">
          <div class="field-bloc__title lang-text" data-fr="Âge" data-en="Age">Âge</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_attr($frAge ?: '[-]'); ?>"
            data-en="<?php echo esc_attr($enAge ?: '[-]'); ?>">
            <?php echo esc_html($frAge ?: '[-]'); ?>
          </div>
        </div>

        <div class="field-bloc">
          <div class="field-bloc__title lang-text" data-fr="Type de population" data-en="Population Type">Type de population</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_attr($frType ?: '[-]'); ?>"
            data-en="<?php echo esc_attr($enType ?: '[-]'); ?>">
            <?php echo esc_html($frType ?: '[-]'); ?>
          </div>
        </div>
        <?php if ($showTypeAutre): ?>
          <div class="field-bloc">
            <div class="field-bloc__title lang-text"
              data-fr="Autre type de population, précisions"
              data-en="Other Population Type (details)">
              Autre type de population, précisions
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_attr($frTypeAutre ?: '[-]'); ?>"
              data-en="<?php echo esc_attr($enTypeAutre ?: '[-]'); ?>">
              <?php echo esc_html($frTypeAutre ?: '[-]'); ?>
            </div>
          </div>
        <?php endif; ?>

        <h2 class="field-content__h2 lang-text"
          data-fr="Autres critères d'éligibilité"
          data-en="Other eligibility criteria">
          Autres critères d'éligibilité
        </h2>

        <?php
        $universeFr = $jsonRec['fr']['additional']['universe'] ?? [];
        $universeEn = $jsonRec['en']['additional']['universe'] ?? [];

        $clusionIFr = $universeFr['clusion_I'] ?? '[-]';
        $clusionIEn = $universeEn['clusion_I'] ?? '[-]';

        $clusionEFr = $universeFr['clusion_E'] ?? '[-]';
        $clusionEEn = $universeEn['clusion_E'] ?? '[-]';
        ?>

        <table class="field-table">
          <thead>
            <tr>
              <th class="lang-text" data-fr="Critères d'inclusion" data-en="Inclusion Criteria">
                Critères d'inclusion
              </th>
              <th class="lang-text" data-fr="Critères d'exclusion" data-en="Exclusion Criteria">
                Critères d'exclusion
              </th>
            </tr>
          </thead>

          <tbody>
            <tr class="lang-row"
              data-fr-inclusion="<?php echo esc_attr($clusionIFr); ?>"
              data-en-inclusion="<?php echo esc_attr($clusionIEn); ?>"
              data-fr-exclusion="<?php echo esc_attr($clusionEFr); ?>"
              data-en-exclusion="<?php echo esc_attr($clusionEEn); ?>">

              <td>
                <span class="lang-text"
                  data-fr="<?php echo esc_attr($clusionIFr ?: '[-]'); ?>"
                  data-en="<?php echo esc_attr($clusionIEn ?: '[-]'); ?>">
                  <?php echo esc_html($clusionIFr ?: '[-]'); ?>
                </span>
              </td>

              <td>
                <span class="lang-text"
                  data-fr="<?php echo esc_attr($clusionEFr ?: '[-]'); ?>"
                  data-en="<?php echo esc_attr($clusionEEn ?: '[-]'); ?>">
                  <?php echo esc_html($clusionEFr ?: '[-]'); ?>
                </span>
              </td>
            </tr>
          </tbody>
        </table>



        <h2 class="field-content__h2 lang-text"
          data-fr="Champ géographique"
          data-en="Geographical Coverage">
          Champ géographique
        </h2>

        <?php

        if (!function_exists('parseStringArrayNormalized')) {
          function parseStringArrayNormalized($str)
          {
            if (is_array($str)) return $str;
            if (empty($str)) return [];
            // Remove square brackets and quotes
            $clean = trim($str, "[] \t\n\r\0\x0B'\"");
            // Split by comma
            $parts = preg_split('/\s*,\s*/', $clean);
            // Clean up each part
            $parts = array_map(fn($p) => trim($p, "'\" "), $parts);
            // Filter out empties
            return array_values(array_filter($parts, fn($v) => $v !== ''));
          }
        }
        $nationFr = $jsonRec['fr']['study_desc']['study_info']['nation'] ?? [];
        $nationEn = $jsonRec['en']['study_desc']['study_info']['nation'] ?? [];

        $geoCoverageFr = $jsonRec['fr']['study_desc']['study_info']['geog_coverage'] ?? [];
        $geoCoverageEn = $jsonRec['en']['study_desc']['study_info']['geog_coverage'] ?? [];

        $geoDetailFr = $jsonRec['fr']['additional']['geographicalCoverage']['geoDetail'] ?? '[-]';
        $geoDetailEn = $jsonRec['en']['additional']['geographicalCoverage']['geoDetail'] ?? '[-]';


        if (is_array($nationFr) && isset($nationFr[0]['name'])) {
          $nationFr = implode(', ', array_filter(array_map(fn($n) => $n['name'] ?? '[-]', $nationFr)));
        } elseif (!empty($nationFr)) {
          $nationFr = implode(', ', parseStringArrayNormalized($nationFr));
        } else {
          $nationFr = '[-]';
        }

        if (is_array($nationEn) && isset($nationEn[0]['name'])) {
          $nationEn = implode(', ', array_filter(array_map(fn($n) => $n['name'] ?? '[-]', $nationEn)));
        } elseif (!empty($nationEn)) {
          $nationEn = implode(', ', parseStringArrayNormalized($nationEn));
        } else {
          $nationEn = '[-]';
        }

        $geoCoverageFr = implode(', ', parseStringArrayNormalized($geoCoverageFr));
        $geoCoverageEn = implode(', ', parseStringArrayNormalized($geoCoverageEn));
        ?>

        <div class="field-bloc">
          <div class="field-bloc__title lang-text"
            data-fr="Pays concernées"
            data-en="Countries Concerned">
            Pays concernées
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_attr($nationFr ?: '[-]'); ?>"
            data-en="<?php echo esc_attr($nationEn ?: '[-]'); ?>">
            <?php echo esc_html($nationFr  ?: '[-]'); ?>
          </div>
        </div>

        <div class="field-bloc">
          <div class="field-bloc__title lang-text"
            data-fr="Régions concernées (en France)"
            data-en="Regions Concerned (France)">
            Régions concernées (en France)
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_attr($geoCoverageFr ?: '[-]'); ?>"
            data-en="<?php echo esc_attr($geoCoverageEn ?: '[-]'); ?>">
            <?php echo esc_html($geoCoverageFr  ?: '[-]'); ?>
          </div>
        </div>

        <div class="field-bloc">
          <div class="field-bloc__title lang-text"
            data-fr="Détail du champ géographique"
            data-en="Geographical Coverage Details">
            Détail du champ géographique
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_attr($geoDetailFr ?: '[-]'); ?>"
            data-en="<?php echo esc_attr($geoDetailEn ?: '[-]'); ?>">
            <?php echo esc_html($geoDetailFr ?: '[-]'); ?>
          </div>
        </div>
      </div>


      <div class="submenu-study" id="studiedhealthparams">
        <h2 class="field-content__h1 lang-text"
          data-fr="Paramètres de santé étudiés"
          data-en="Studied Health Parameters">
          Paramètres de santé étudiés
        </h2>
        <?php
        $activitiesFr = $jsonRec['fr']['study_desc']['study_development']['development_activity'] ?? [];
        $activitiesEn = $jsonRec['en']['study_desc']['study_development']['development_activity'] ?? [];

        $primaryEvalFr = $primaryEvalEn = '';
        $secondaryEvalFr = $secondaryEvalEn = '';

        foreach ($activitiesFr as $activity) {
          if (($activity['activity_type'] ?? '') === 'évaluation primaire') {
            $primaryEvalFr = $activity['activity_description'] ?? '[-]';
          }
          if (($activity['activity_type'] ?? '') === 'évaluation secondaire') {
            $secondaryEvalFr = $activity['activity_description'] ?? '[-]';
          }
        }
        foreach ($activitiesEn as $activity) {
          if (($activity['activity_type'] ?? '') === 'primary evaluation') {
            $primaryEvalEn = $activity['activity_description'] ?? '[-]';
          }
          if (($activity['activity_type'] ?? '') === 'secondary evaluation') {
            $secondaryEvalEn = $activity['activity_description'] ?? '[-]';
          }
        }


        ?>

        <div class="field-bloc">
          <div class="field-bloc__title lang-text"
            data-fr="Critères d’évaluation (de jugement) principaux"
            data-en="Primary Outcome">
            Critères d’évaluation (de jugement) principaux
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_attr($primaryEvalFr ?: '[-]'); ?>"
            data-en="<?php echo esc_attr($primaryEvalEn ?: '[-]'); ?>">
            <?php echo esc_html($primaryEvalFr  ?: '[-]'); ?>
          </div>
        </div>

        <div class="field-bloc">
          <div class="field-bloc__title lang-text"
            data-fr="Critères d’évaluation (de jugement) secondaires"
            data-en="Secondary Outcomes">
            Critères d’évaluation (de jugement) secondaires
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_attr($secondaryEvalFr  ?: '[-]'); ?>"
            data-en="<?php echo esc_attr($secondaryEvalEn  ?: '[-]'); ?>">
            <?php echo esc_html($secondaryEvalFr  ?: '[-]'); ?>
          </div>
        </div>

      </div>


    </div>

  </div>
</div>
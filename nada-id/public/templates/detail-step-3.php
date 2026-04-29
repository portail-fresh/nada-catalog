  <?php
  if (!function_exists('buildTopicsHtml3')) {
    function buildTopicsHtml3(array $items, string $class = 'checkbox-item', ?string $key = null): string
    {
      $html = '';


      foreach ($items as $item) {
        // Récupère la valeur selon le type d'élément
        if (is_array($item)) {
          $value = $key && isset($item[$key]) ? trim($item[$key]) : '';
        } else {
          $value = trim((string)$item);
        }
        // Ajoute à la sortie si non vide
        if ($value !== '') {
          $html .= '<span class="' . htmlspecialchars($class) . '">' . htmlspecialchars($value) . '</span> ';
        }
      }

      return trim($html);
    }
  }
  if (!function_exists('splitOutsideParentheses')) {
    function splitOutsideParentheses(string $str): array
    {
      $result = [];
      $bracketLevel = 0;
      $current = '';

      $len = mb_strlen($str);
      for ($i = 0; $i < $len; $i++) {
        $char = mb_substr($str, $i, 1);

        if ($char === '(') {
          $bracketLevel++;
        } elseif ($char === ')') {
          $bracketLevel--;
        }

        if ($char === ',' && $bracketLevel === 0) {
          $result[] = trim($current);
          $current = '';
        } else {
          $current .= $char;
        }
      }

      if (trim($current) !== '') {
        $result[] = trim($current);
      }

      return $result;
    }
  }
  ?>

  <?php
  $langs = ['fr', 'en'];
  $currentLang = 'fr';


  if (!function_exists('parseStringArrayNormalized')) {
    function parseStringArrayNormalized($str)
    {
      if (is_array($str)) return $str;
      if (empty($str)) return [];

      // Try decoding JSON if possible
      $jsonTry = json_decode($str, true);
      if (is_array($jsonTry)) {
        return array_values(array_filter(array_map('trim', $jsonTry)));
      }

      // Clean up manual string like "['aaa','bbb']"
      $clean = trim($str, "[] \t\n\r\0\x0B'\"");
      $clean = str_replace(["'", '"'], '', $clean);
      $parts = preg_split('/\s*,\s*/', $clean);
      return array_values(array_filter($parts, fn($v) => $v !== ''));
    }
  }


  $targetSampleFr = $jsonRec['fr']['study_desc']['method']['data_collection']['target_sample_size'] ?? '';
  $targetSampleEn = $jsonRec['en']['study_desc']['method']['data_collection']['target_sample_size'] ?? '';

  $responseRateFrArr = $jsonRec['fr']['study_desc']['method']['data_collection']['response_rate'] ?? [];
  $responseRateEnArr = $jsonRec['en']['study_desc']['method']['data_collection']['response_rate'] ?? [];

  $responseRateFr = is_array($responseRateFrArr) ? ($responseRateFrArr[0] ?? '') : $responseRateFrArr;
  $responseRateEn = is_array($responseRateEnArr) ? ($responseRateEnArr[0] ?? '') : $responseRateEnArr;



  $clinicalDetailsFr = $jsonRec['fr']['additional']['dataTypes']['clinicalDataDetails'] ?? '';
  $clinicalDetailsEn = $jsonRec['en']['additional']['dataTypes']['clinicalDataDetails'] ?? '';

  $biologicalDetailsFr = $jsonRec['fr']['additional']['dataTypes']['biologicalDataDetails'] ?? '';
  $biologicalDetailsEn = $jsonRec['en']['additional']['dataTypes']['biologicalDataDetails'] ?? '';

  $isDataInBiobankFr = $jsonRec['fr']['additional']['dataTypes']['isDataInBiobank'] ?? '';
  $isDataInBiobankEn = $jsonRec['en']['additional']['dataTypes']['isDataInBiobank'] ?? '';

  $biobankContentFr = $jsonRec['fr']['additional']['dataTypes']['biobankContent'] ?? [];
  $biobankContentEn = $jsonRec['en']['additional']['dataTypes']['biobankContent'] ?? [];

  $dataTypeOtherFr = $jsonRec['fr']['additional']['dataTypes']['dataTypeOther'] ?? '';
  $dataTypeOtherEn = $jsonRec['en']['additional']['dataTypes']['dataTypeOther'] ?? '';

  $dataKindsRawFr = $jsonRec['fr']['study_desc']['study_info']['data_kind'] ?? '';
  $dataKindsRawEn = $jsonRec['en']['study_desc']['study_info']['data_kind'] ?? '';

  $dataKindsListFr = parseStringArrayNormalized($dataKindsRawFr);
  $dataKindsListEn = parseStringArrayNormalized($dataKindsRawEn);

  $dataKindsFr = implode(', ', $dataKindsListFr);
  $dataKindsEn = implode(', ', $dataKindsListEn);


  if (is_array($biobankContentFr)) {
    $biobankContentFr = implode(', ', $biobankContentFr);
  }
  if (is_array($biobankContentEn)) {
    $biobankContentEn = implode(', ', $biobankContentEn);
  }

  $hasClinicalData = in_array('Données cliniques', $dataKindsListFr, true) || in_array('Données cliniques', $dataKindsListEn, true);

  $hasBiologicalData = in_array('Données biologiques', $dataKindsListFr, true) || in_array('Données biologiques', $dataKindsListEn, true);


  $biobankFlag = mb_strtolower(trim($isDataInBiobankFr));
  $showBiobankContent = in_array($biobankFlag, ['oui', 'yes', 'true', 'vrai'], true);


  $lowerFr = array_map('mb_strtolower', $dataKindsListFr);
  $lowerEn = array_map('mb_strtolower', $dataKindsListEn);
  $hasOtherDataType = in_array('autre', $lowerFr, true)
    || in_array('other', $lowerFr, true)
    || in_array('autre', $lowerEn, true)
    || in_array('other', $lowerEn, true);

  ?>

  <?php
  $analysisUnitFr = trim($jsonRec['fr']['study_desc']['study_info']['analysis_unit'] ?? '');
  $analysisUnitEn = trim($jsonRec['en']['study_desc']['study_info']['analysis_unit'] ?? '');

  $hasAnalysisUnit = $analysisUnitFr || $analysisUnitEn;

  if ($hasAnalysisUnit):
  ?>
    <div class="submenu-study" id="analysisunit">

      <?php if ($hasAnalysisUnit): ?>
        <h2 class="field-content__h1 lang-text"
          data-fr="Unité de collecte"
          data-en="Collection unit">
          Unité de collecte
        </h2>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
            data-fr="Unité de collecte"
            data-en="Collection unit">
            Unité de collecte
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($analysisUnitFr); ?>"
            data-en="<?php echo esc_html($analysisUnitEn); ?>">
            <?php echo esc_html($currentLang === 'fr' ? $analysisUnitFr : $analysisUnitEn); ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php
  $studyTypeFr = trim($jsonRec['fr']['study_desc']['method']['method_notes'] ?? '');
  $studyTypeEn = trim($jsonRec['en']['study_desc']['method']['method_notes'] ?? '');

  $hasStudyType = $studyTypeFr || $studyTypeEn;

  if ($hasStudyType):
  ?>
    <div class="submenu-study" id="modelEtude">
      <?php if ($hasStudyType): ?>
        <h2 class="field-content__h1 lang-text"
          data-fr="Modèle d'étude"
          data-en="Study Type">
          Modèle d'étude
        </h2>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
            data-fr="Modèle d'étude"
            data-en="Study type">
            Modèle d'étude
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($studyTypeFr); ?>"
            data-en="<?php echo esc_html($studyTypeEn); ?>">
            <?php echo esc_html($currentLang === 'fr' ? $studyTypeFr : $studyTypeEn); ?>
          </div>
        </div>
      <?php endif; ?>

    </div>
  <?php endif; ?>
  <?php
  $notesFr = $jsonRec['fr']['study_desc']['method']['notes'] ?? [];
  $notesEn = $jsonRec['en']['study_desc']['method']['notes'] ?? [];

  $observationalModelFr = '';
  $observationalModelEn = '';

  $otherResearchTypeFr = $jsonRec['fr']['additional']['otherResearchType']['otherResearchTypeDetails'] ?? '';
  $otherResearchTypeEn = $jsonRec['en']['additional']['otherResearchType']['otherResearchTypeDetails'] ?? '';

  $timePerspectiveFr = $jsonRec['fr']['study_desc']['method']['data_collection']['time_method'] ?? '';
  $timePerspectiveEn = $jsonRec['en']['study_desc']['method']['data_collection']['time_method'] ?? '';

  $isInclusionGroupsFr = normalizeBooleanValue($jsonRec['fr']['additional']['isInclusionGroups'] ?? '', 'fr');
  $isInclusionGroupsEn = normalizeBooleanValue($jsonRec['en']['additional']['isInclusionGroups'] ?? '', 'en');

  $hasInclusionGroups = $isInclusionGroupsFr !== '' || $isInclusionGroupsEn !== '';
  if (is_array($notesFr)) {
    foreach ($notesFr as $note) {
      if (isset($note['subject']) && trim(strtolower($note['subject'])) === 'observational study method') {
        if (!empty($note['values']) && is_array($note['values'])) {
          $observationalModelFr = implode(', ', array_filter($note['values']));
        }
        break;
      }
    }
  }

  if (is_array($notesEn)) {
    foreach ($notesEn as $note) {
      if (isset($note['subject']) && trim(strtolower($note['subject'])) === 'observational study method') {
        if (!empty($note['values']) && is_array($note['values'])) {
          $observationalModelEn = implode(', ', array_filter($note['values']));
        }
        break;
      }
    }
  }
  // Inclusion groups
  $inclusionGroupsFr = $jsonRec['fr']['additional']['inclusionGroups'] ?? [];
  $inclusionGroupsEn = $jsonRec['en']['additional']['inclusionGroups'] ?? [];
  $maxInclusionGroups = max(count($inclusionGroupsFr), count($inclusionGroupsEn));

  // Number of inclusion groups
  $nrInclusionGroupsFr = trim((string) ($jsonRec['fr']['additional']['nrInclusionGroups'] ?? ''));
  $nrInclusionGroupsEn = trim((string) ($jsonRec['en']['additional']['nrInclusionGroups'] ?? ''));

  // Intervention data
  $interventionFr = $jsonRec['fr']['additional']['intervention'] ?? [];
  $interventionEn = $jsonRec['en']['additional']['intervention'] ?? [];
  $maxIntervention = max(count($interventionFr), count($interventionEn));

  $hasValidInclusionGroup = false;
  for ($i = 0; $i < $maxInclusionGroups; $i++) {
    $frItem = $inclusionGroupsFr[$i] ?? [];
    $enItem = $inclusionGroupsEn[$i] ?? [];
    $fields = [
      trim($frItem['name'] ?? ''),
      trim($frItem['description'] ?? ''),
      trim($enItem['name'] ?? ''),
      trim($enItem['description'] ?? ''),
    ];
    if (implode('', $fields) !== '') {
      $hasValidInclusionGroup = true;
      break;
    }
  }

  $hasValidIntervention = false;
  for ($i = 0; $i < $maxIntervention; $i++) {
    $frItem = $interventionFr[$i] ?? [];
    $enItem = $interventionEn[$i] ?? [];
    $fields = [
      trim($frItem['name'] ?? ''),
      trim($frItem['type'] ?? ''),
      trim($frItem['typeOther'] ?? ''),
      trim($frItem['description'] ?? ''),
      trim($enItem['name'] ?? ''),
      trim($enItem['type'] ?? ''),
      trim($enItem['typeOther'] ?? ''),
      trim($enItem['description'] ?? '')
    ];
    if (implode('', $fields) !== '') {
      $hasValidIntervention = true;
      break;
    }
  }

  $hasObservationalData =
    trim($observationalModelFr) !== '' ||
    trim($observationalModelEn) !== '' ||
    trim($otherResearchTypeFr) !== '' ||
    trim($otherResearchTypeEn) !== '' ||
    trim($timePerspectiveFr) !== '' ||
    trim($timePerspectiveEn) !== '' ||
    trim($nrInclusionGroupsFr) !== '' ||
    trim($nrInclusionGroupsEn) !== '' ||
    $hasValidInclusionGroup ||
    $hasValidIntervention;
  ?>

  <?php if ($showObservationalStudy): ?>
    <?php if ($hasObservationalData): ?>
      <div class="submenu-study" id="observationalstudy">
        <h2 class="field-content__h1 lang-text"
          data-fr="Étude observationnelle"
          data-en="Observational study">
          Étude observationnelle
        </h2>

        <?php if ($observationalModelFr || $observationalModelEn): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Modèle de l’étude observationnelle"
              data-en="Observational study design">
              Modèle de l’étude observationnelle
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?= esc_html($observationalModelFr); ?>"
              data-en="<?= esc_html($observationalModelEn); ?>">
              <?= esc_html($currentLang === 'fr' ? $observationalModelFr : $observationalModelEn); ?>
            </div>
          </div>
        <?php endif; ?>


        <?php if ($otherResearchTypeFr || $otherResearchTypeEn): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Autre type d’étude observationnelle, précisions"
              data-en="Other research type, details">
              Autre type d’étude observationnelle, précisions
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?= esc_html($otherResearchTypeFr); ?>"
              data-en="<?= esc_html($otherResearchTypeEn); ?>">
              <?= esc_html($currentLang === 'fr' ? $otherResearchTypeFr : $otherResearchTypeEn); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($timePerspectiveFr || $timePerspectiveEn): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Perspective temporelle"
              data-en="Time perspective">
              Perspective temporelle
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?= esc_html($timePerspectiveFr); ?>"
              data-en="<?= esc_html($timePerspectiveEn); ?>">
              <?= esc_html($currentLang === 'fr' ? $timePerspectiveFr : $timePerspectiveEn); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($hasInclusionGroups): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="L'étude comporte-t-elle plusieurs groupes définis dès l'inclusion ?"
              data-en="Does the study include several groups defined at the time of inclusion ?">
              L'étude comporte-t-elle plusieurs groupes définis dès l'inclusion ?
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?= esc_html($isInclusionGroupsFr); ?>"
              data-en="<?= esc_html($isInclusionGroupsEn); ?>">
              <?= esc_html($currentLang === 'fr' ? $isInclusionGroupsFr : $isInclusionGroupsEn); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php
        // Detect if any valid inclusion group exists
        $hasValidInclusionGroup = false;
        for ($i = 0; $i < $maxInclusionGroups; $i++) {
          $frItem = $inclusionGroupsFr[$i] ?? [];
          $enItem = $inclusionGroupsEn[$i] ?? [];

          $fields = [
            trim($frItem['name'] ?? ''),
            trim($frItem['description'] ?? ''),
            trim($enItem['name'] ?? ''),
            trim($enItem['description'] ?? ''),
          ];

          if (implode('', $fields) !== '') {
            $hasValidInclusionGroup = true;
            break;
          }
        }

        $hasInclusionSection = $hasValidInclusionGroup || $nrInclusionGroupsFr || $nrInclusionGroupsEn;
        ?>

        <?php if ($hasInclusionSection): ?>
          <div>
            <h3 class="field-content__h2 lang-text"
              data-fr="Groupes à l'inclusion"
              data-en="Enrollment groups">
              Groupes à l'inclusion
            </h3>

            <?php if ($nrInclusionGroupsFr || $nrInclusionGroupsEn): ?>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text"
                  data-fr="Nombre de groupes à l'inclusion"
                  data-en="Inclusion groups number">
                  Nombre de groupes à l'inclusion
                </div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?= esc_html($nrInclusionGroupsFr); ?>"
                  data-en="<?= esc_html($nrInclusionGroupsEn); ?>">
                  <?= esc_html($currentLang === 'fr' ? $nrInclusionGroupsFr : $nrInclusionGroupsEn); ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($hasValidInclusionGroup): ?>
              <?php for ($i = 0; $i < $maxInclusionGroups; $i++):
                $num = $i + 1;
                $frItem = $inclusionGroupsFr[$i] ?? [];
                $enItem = $inclusionGroupsEn[$i] ?? [];

                $frName = trim($frItem['name'] ?? '');
                $frDesc = trim($frItem['description'] ?? '');
                $enName = trim($enItem['name'] ?? '');
                $enDesc = trim($enItem['description'] ?? '');

                if (!$frName && !$frDesc && !$enName && !$enDesc) continue;
              ?>
                <div class="field-card mb-3">
                  <div class="field-card__header lang-text"
                    data-fr="Groupe à l'inclusion <?= $num; ?>"
                    data-en="Inclusion group <?= $num; ?>">
                    Groupe à l'inclusion <?= $num; ?>
                  </div>

                  <div class="field-card__body">
                    <?php if ($frName || $enName): ?>
                      <div class="field-bloc mb-2">
                        <div class="field-bloc__title lang-text"
                          data-fr="Nom du groupe"
                          data-en="Group name">
                          Nom du groupe
                        </div>
                        <div class="field-bloc__value lang-text"
                          data-fr="<?= nl2br(esc_html($frName)); ?>"
                          data-en="<?= nl2br(esc_html($enName)); ?>">
                          <?= nl2br(esc_html($currentLang === 'fr' ? $frName : $enName)); ?>
                        </div>
                      </div>
                    <?php endif; ?>

                    <?php if ($frDesc || $enDesc): ?>
                      <div class="field-bloc mb-2">
                        <div class="field-bloc__title lang-text"
                          data-fr="Description du groupe"
                          data-en="Group description">
                          Description du groupe
                        </div>
                        <div class="field-bloc__value lang-text"
                          data-fr="<?= nl2br(esc_html($frDesc)); ?>"
                          data-en="<?= nl2br(esc_html($enDesc)); ?>">
                          <?= nl2br(esc_html($currentLang === 'fr' ? $frDesc : $enDesc)); ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endfor; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>




        <?php
        $hasValidIntervention = false;

        for ($i = 0; $i < $maxIntervention; $i++) {
          $frItem = $interventionFr[$i] ?? [];
          $enItem = $interventionEn[$i] ?? [];
          $fields = [
            trim($frItem['name'] ?? ''),
            trim($frItem['type'] ?? ''),
            trim($frItem['typeOther'] ?? ''),
            trim($frItem['description'] ?? ''),
            trim($enItem['name'] ?? ''),
            trim($enItem['type'] ?? ''),
            trim($enItem['typeOther'] ?? ''),
            trim($enItem['description'] ?? ''),
          ];
          if (implode('', $fields) !== '') {
            $hasValidIntervention = true;
            break;
          }
        }
        ?>

        <?php if ($hasValidIntervention): ?>
          <div>
            <h3 class="field-content__h2 lang-text"
              data-fr="Facteur, processus ou exposition étudié(e)"
              data-en="Studied factor, process, or exposure">
              Facteur, processus ou exposition étudié(e)
            </h3>

            <?php for ($i = 0; $i < $maxIntervention; $i++):
              $num = $i + 1;
              $frItem = $interventionFr[$i] ?? [];
              $enItem = $interventionEn[$i] ?? [];

              $frName = trim($frItem['name'] ?? '');
              $frType = trim($frItem['type'] ?? '');
              $frTypeOther = trim($frItem['typeOther'] ?? '');
              $frDesc = trim($frItem['description'] ?? '');

              $enName = trim($enItem['name'] ?? '');
              $enType = trim($enItem['type'] ?? '');
              $enTypeOther = trim($enItem['typeOther'] ?? '');
              $enDesc = trim($enItem['description'] ?? '');

              if (!$frName && !$frType && !$frTypeOther && !$frDesc && !$enName && !$enType && !$enTypeOther && !$enDesc) continue;
            ?>

              <div class="field-card mb-3">
                <div class="field-card__header lang-text"
                  data-fr="Facteur, processus ou exposition étudié <?= $num; ?>"
                  data-en="Studied factor, process, or exposure <?= $num; ?>">
                  Facteur, processus ou exposition étudié <?= $num; ?>
                </div>

                <div class="field-card__body">

                  <?php if ($frName || $enName): ?>
                    <div class="field-bloc mb-2">
                      <div class="field-bloc__title lang-text"
                        data-fr="Nom du facteur ou de l'exposition"
                        data-en="Factor/exposure name">
                        Nom du facteur ou de l'exposition
                      </div>
                      <div class="field-bloc__value lang-text"
                        data-fr="<?= nl2br(esc_html($frName)); ?>"
                        data-en="<?= nl2br(esc_html($enName)); ?>">
                        <?= nl2br(esc_html($currentLang === 'fr' ? $frName : $enName)); ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <?php if ($frType || $enType): ?>
                    <div class="field-bloc mb-2">
                      <div class="field-bloc__title lang-text"
                        data-fr="Type de facteur ou d'exposition"
                        data-en="Factor/exposure type">
                        Type de facteur ou d'exposition
                      </div>
                      <div class="field-bloc__value lang-text"
                        data-fr="<?= nl2br(esc_html($frType)); ?>"
                        data-en="<?= nl2br(esc_html($enType)); ?>">
                        <?= nl2br(esc_html($currentLang === 'fr' ? $frType : $enType)); ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <?php if ($frTypeOther || $enTypeOther): ?>
                    <div class="field-bloc mb-2">
                      <div class="field-bloc__title lang-text"
                        data-fr="Autre type"
                        data-en="Other type">
                        Autre type
                      </div>
                      <div class="field-bloc__value lang-text"
                        data-fr="<?= nl2br(esc_html($frTypeOther)); ?>"
                        data-en="<?= nl2br(esc_html($enTypeOther)); ?>">
                        <?= nl2br(esc_html($currentLang === 'fr' ? $frTypeOther : $enTypeOther)); ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <?php if ($frDesc || $enDesc): ?>
                    <div class="field-bloc mb-2">
                      <div class="field-bloc__title lang-text"
                        data-fr="Description"
                        data-en="Description">
                        Description
                      </div>
                      <div class="field-bloc__value lang-text"
                        data-fr="<?= nl2br(esc_html($frDesc)); ?>"
                        data-en="<?= nl2br(esc_html($enDesc)); ?>">
                        <?= nl2br(esc_html($currentLang === 'fr' ? $frDesc : $enDesc)); ?>
                      </div>
                    </div>
                  <?php endif; ?>

                </div>
              </div>
            <?php endfor; ?>
          </div>
        <?php endif; ?>




      </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php
  //  Helper to deeply check if an array has non-empty values
  if (!function_exists('hasNonEmptyData')) {
    function hasNonEmptyData($arr)
    {
      if (!is_array($arr)) return !empty(trim((string)$arr));
      foreach ($arr as $v) {
        if (hasNonEmptyData($v)) return true;
      }
      return false;
    }
  }

  $hasInterventionalStudy =
    hasNonEmptyData($jsonRec['fr']['additional']['interventionalStudy']['IsClinicalTrial'] ?? '') ||
    hasNonEmptyData($jsonRec['en']['additional']['interventionalStudy']['IsClinicalTrial'] ?? '') ||
    hasNonEmptyData($jsonRec['fr']['additional']['interventionalStudy']['trialPhase'] ?? []) ||
    hasNonEmptyData($jsonRec['en']['additional']['interventionalStudy']['trialPhase'] ?? []) ||
    hasNonEmptyData($jsonRec['fr']['additional']['interventionalStudy']['researchPurpose'] ?? []) ||
    hasNonEmptyData($jsonRec['en']['additional']['interventionalStudy']['researchPurpose'] ?? []) ||
    hasNonEmptyData($jsonRec['fr']['additional']['interventionalStudy']['otherResearchPurpose'] ?? '') ||
    hasNonEmptyData($jsonRec['en']['additional']['interventionalStudy']['otherResearchPurpose'] ?? '') ||
    hasNonEmptyData($jsonRec['fr']['additional']['inclusionGroups'] ?? []) ||
    hasNonEmptyData($jsonRec['en']['additional']['inclusionGroups'] ?? []) ||
    hasNonEmptyData($jsonRec['fr']['additional']['nrInclusionGroups'] ?? '') ||
    hasNonEmptyData($jsonRec['en']['additional']['nrInclusionGroups'] ?? '') ||
    hasNonEmptyData($jsonRec['fr']['additional']['interventionalStudy']['interventionalStudyModel'] ?? '') ||
    hasNonEmptyData($jsonRec['en']['additional']['interventionalStudy']['interventionalStudyModel'] ?? '') ||
    hasNonEmptyData($jsonRec['fr']['additional']['allocation'] ?? []) ||
    hasNonEmptyData($jsonRec['en']['additional']['allocation'] ?? []) ||
    hasNonEmptyData($jsonRec['fr']['additional']['masking'] ?? []) ||
    hasNonEmptyData($jsonRec['en']['additional']['masking'] ?? []) ||
    hasNonEmptyData($jsonRec['fr']['additional']['armsNumber'] ?? '') ||
    hasNonEmptyData($jsonRec['en']['additional']['armsNumber'] ?? '') ||
    hasNonEmptyData($jsonRec['fr']['additional']['arms'] ?? []) ||
    hasNonEmptyData($jsonRec['fr']['additional']['intervention'] ?? []);
  ?>
  <?php if ($showInterventionalStudy): ?>
    <?php if ($hasInterventionalStudy): ?>
      <div class="submenu-study" id="interventionalstudy">
        <h2 class="field-content__h1 lang-text"
          data-fr="Étude interventionnelle (expérimentale)"
          data-en="Interventional study">
          Étude interventionnelle (expérimentale)
        </h2>
        <?php
        $isClinicalTrialFr = $jsonRec['fr']['additional']['interventionalStudy']['isClinicalTrial'] ?? '';
        $isClinicalTrialEn = $jsonRec['en']['additional']['interventionalStudy']['isClinicalTrial'] ?? '';


        $isClinicalTrialFr = normalizeBooleanValue($isClinicalTrialFr, 'fr');
        $isClinicalTrialEn =  normalizeBooleanValue($isClinicalTrialEn, 'en');

        $hasClinicalTrial = $isClinicalTrialFr !== '' || $isClinicalTrialEn !== '';
        $isInclusionGroupsFr = normalizeBooleanValue($jsonRec['fr']['additional']['isInclusionGroups'] ?? '', 'fr');
        $isInclusionGroupsEn = normalizeBooleanValue($jsonRec['en']['additional']['isInclusionGroups'] ?? '', 'en');


        $hasInclusionGroups = $isInclusionGroupsFr !== '' || $isInclusionGroupsEn !== '';
        ?>

        <?php if ($hasClinicalTrial): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="S'agit-il d'un essai clinique ?"
              data-en="Is this a clinical trial?">
              S'agit-il d'un essai clinique ?
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?= esc_attr($isClinicalTrialFr); ?>"
              data-en="<?= esc_attr($isClinicalTrialEn); ?>">
              <?= esc_html($currentLang === 'fr' ? $isClinicalTrialFr : $isClinicalTrialEn); ?>
            </div>
          </div>
        <?php endif; ?>


        <?php
        $trialPhaseFr = $jsonRec['fr']['additional']['interventionalStudy']['trialPhase'] ?? [];
        $trialPhaseEn = $jsonRec['en']['additional']['interventionalStudy']['trialPhase'] ?? [];

        if (!is_array($trialPhaseFr)) $trialPhaseFr = !empty($trialPhaseFr) ? [$trialPhaseFr] : [];
        if (!is_array($trialPhaseEn)) $trialPhaseEn = !empty($trialPhaseEn) ? [$trialPhaseEn] : [];

        $trialPhaseFr = array_filter(array_map('trim', $trialPhaseFr));
        $trialPhaseEn = array_filter(array_map('trim', $trialPhaseEn));

        $hasTrialPhase = !empty($trialPhaseFr) || !empty($trialPhaseEn);
        ?>

        <?php if ($hasTrialPhase): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Phase de l'essai"
              data-en="Study Phase">
              Phase de l'essai
            </div>

            <div class="field-bloc__value lang-text"
              data-fr="<?= esc_attr(buildTopicsHtml3($trialPhaseFr)); ?>"
              data-en="<?= esc_attr(buildTopicsHtml3($trialPhaseEn)); ?>">
              <?= $currentLang === 'fr'
                ? buildTopicsHtml3($trialPhaseFr)
                : buildTopicsHtml3($trialPhaseEn); ?>
            </div>

          </div>
        <?php endif; ?>

        <?php
        $researchPurposeFr = $jsonRec['fr']['additional']['interventionalStudy']['researchPurpose'] ?? [];
        $researchPurposeEn = $jsonRec['en']['additional']['interventionalStudy']['researchPurpose'] ?? [];

        // Normalize to arrays
        if (!is_array($researchPurposeFr)) $researchPurposeFr = !empty($researchPurposeFr) ? [$researchPurposeFr] : [];
        if (!is_array($researchPurposeEn)) $researchPurposeEn = !empty($researchPurposeEn) ? [$researchPurposeEn] : [];

        // Clean values
        $researchPurposeFr = array_filter(array_map('trim', $researchPurposeFr));
        $researchPurposeEn = array_filter(array_map('trim', $researchPurposeEn));

        $hasResearchPurpose = !empty($researchPurposeFr) || !empty($researchPurposeEn);
        ?>

        <?php if ($hasResearchPurpose): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Objectif principal de la recherche"
              data-en="Primary research purpose">
              Objectif principal de la recherche
            </div>

            <div class="field-bloc__value lang-text"
              data-fr="<?= esc_attr(buildTopicsHtml3($researchPurposeFr)); ?>"
              data-en="<?= esc_attr(buildTopicsHtml3($researchPurposeEn)); ?>">
              <?= $currentLang === 'fr'
                ? buildTopicsHtml3($researchPurposeFr)
                : buildTopicsHtml3($researchPurposeEn); ?>
            </div>
          </div>
        <?php endif; ?>



        <?php
        $researchPurposeFr = $jsonRec['fr']['additional']['interventionalStudy']['researchPurpose'] ?? [];
        $researchPurposeEn = $jsonRec['en']['additional']['interventionalStudy']['researchPurpose'] ?? [];

        if (!is_array($researchPurposeFr)) {
          $researchPurposeFr = !empty($researchPurposeFr) ? [$researchPurposeFr] : [];
        }
        if (!is_array($researchPurposeEn)) {
          $researchPurposeEn = !empty($researchPurposeEn) ? [$researchPurposeEn] : [];
        }

        $hasOther = false;
        foreach ([$researchPurposeFr, $researchPurposeEn] as $purposes) {
          foreach ($purposes as $p) {
            $p = strtolower(trim($p));
            if (in_array($p, ['autre', 'other'])) {
              $hasOther = true;
              break 2;
            }
          }
        }

        $otherPurposeFr = $jsonRec['fr']['additional']['interventionalStudy']['otherResearchPurpose'] ?? '';
        $otherPurposeEn = $jsonRec['en']['additional']['interventionalStudy']['otherResearchPurpose'] ?? '';

        $otherPurposeFr = !empty(trim((string)$otherPurposeFr)) ? trim((string)$otherPurposeFr) : '';
        $otherPurposeEn = !empty(trim((string)$otherPurposeEn)) ? trim((string)$otherPurposeEn) : '';
        ?>
        <?php if ($hasOther && ($otherPurposeFr !== '' || $otherPurposeEn !== '')): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Autre objectif principal, précisions"
              data-en="Other research purpose, details">
              Autre objectif principal, précisions
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo nl2br(esc_attr($otherPurposeFr)); ?>"
              data-en="<?php echo nl2br(esc_attr($otherPurposeEn)); ?>">
              <?php echo nl2br(esc_html($otherPurposeFr)); ?>
            </div>
          </div>
        <?php endif; ?>


        <?php
        $inclusionGroupsFr = $jsonRec['fr']['additional']['inclusionGroups'] ?? [];
        $inclusionGroupsEn = $jsonRec['en']['additional']['inclusionGroups'] ?? [];

        $maxInclusionGroups = max(count($inclusionGroupsFr), count($inclusionGroupsEn));

        $nrInclusionGroupsFr = trim((string)($jsonRec['fr']['additional']['nrInclusionGroups'] ?? ''));
        $nrInclusionGroupsEn = trim((string)($jsonRec['en']['additional']['nrInclusionGroups'] ?? ''));

        $hasInclusionGroups = false;
        for ($i = 0; $i < $maxInclusionGroups; $i++) {
          $frItem = $inclusionGroupsFr[$i] ?? [];
          $enItem = $inclusionGroupsEn[$i] ?? [];

          $frName = trim($frItem['name'] ?? '');
          $enName = trim($enItem['name'] ?? '');
          $frDesc = trim($frItem['description'] ?? '');
          $enDesc = trim($enItem['description'] ?? '');

          if ($frName || $enName || $frDesc || $enDesc) {
            $hasInclusionGroups = true;
            break;
          }
        }

        $hasInclusionData = $nrInclusionGroupsFr || $nrInclusionGroupsEn || $hasInclusionGroups;
        ?>
        <?php if ($hasInclusionGroups): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="L'étude comporte-t-elle plusieurs groupes définis dès l'inclusion ?"
              data-en="Does the study include several groups defined at the time of inclusion ?">
              L'étude comporte-t-elle plusieurs groupes définis dès l'inclusion ?
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?= esc_html($isInclusionGroupsFr); ?>"
              data-en="<?= esc_html($isInclusionGroupsEn); ?>">
              <?= esc_html($currentLang === 'fr' ? $isInclusionGroupsFr : $isInclusionGroupsEn); ?>
            </div>
          </div>
        <?php endif; ?>
        <?php if ($hasInclusionData): ?>
          <div>
            <h3 class="field-content__h2 lang-text"
              data-fr="Groupes à l'inclusion"
              data-en="Enrollment groups">
              Groupes à l'inclusion
            </h3>

            <?php if ($nrInclusionGroupsFr || $nrInclusionGroupsEn): ?>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text"
                  data-fr="Nombre de groupes à l'inclusion"
                  data-en="Inclusion groups number">
                  Nombre de groupes à l'inclusion
                </div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?= esc_html($nrInclusionGroupsFr); ?>"
                  data-en="<?= esc_html($nrInclusionGroupsEn); ?>">
                  <?= esc_html($currentLang === 'fr' ? $nrInclusionGroupsFr : $nrInclusionGroupsEn); ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($hasInclusionGroups): ?>
              <?php for ($i = 0; $i < $maxInclusionGroups; $i++):
                $num = $i + 1;
                $frItem = $inclusionGroupsFr[$i] ?? [];
                $enItem = $inclusionGroupsEn[$i] ?? [];

                $frName = trim($frItem['name'] ?? '');
                $enName = trim($enItem['name'] ?? '');
                $frDesc = trim($frItem['description'] ?? '');
                $enDesc = trim($enItem['description'] ?? '');

                if (!$frName && !$enName && !$frDesc && !$enDesc) continue;
              ?>
                <div class="field-card mb-3">
                  <div class="field-card__header lang-text"
                    data-fr="Groupe à l'inclusion <?= $num; ?>"
                    data-en="Inclusion group <?= $num; ?>">
                    Groupe à l'inclusion <?= $num; ?>
                  </div>

                  <div class="field-card__body">
                    <?php if ($frName || $enName): ?>
                      <div class="field-bloc mb-2">
                        <div class="field-bloc__title lang-text"
                          data-fr="Nom du groupe"
                          data-en="Group name">
                          Nom du groupe
                        </div>
                        <div class="field-bloc__value lang-text"
                          data-fr="<?= nl2br(esc_html($frName)); ?>"
                          data-en="<?= nl2br(esc_html($enName)); ?>">
                          <?= nl2br(esc_html($currentLang === 'fr' ? $frName : $enName)); ?>
                        </div>
                      </div>
                    <?php endif; ?>

                    <?php if ($frDesc || $enDesc): ?>
                      <div class="field-bloc mb-2">
                        <div class="field-bloc__title lang-text"
                          data-fr="Description du groupe"
                          data-en="Group description">
                          Description du groupe
                        </div>
                        <div class="field-bloc__value lang-text"
                          data-fr="<?= nl2br(esc_html($frDesc)); ?>"
                          data-en="<?= nl2br(esc_html($enDesc)); ?>">
                          <?= nl2br(esc_html($currentLang === 'fr' ? $frDesc : $enDesc)); ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endfor; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>




        <?php
        $interventionModelFr = $jsonRec['fr']['additional']['interventionalStudy']['interventionalStudyModel'] ?? '';
        $interventionModelEn = $jsonRec['en']['additional']['interventionalStudy']['interventionalStudyModel'] ?? '';

        $interventionModelFr = trim((string)$interventionModelFr);
        $interventionModelEn = trim((string)$interventionModelEn);

        $hasInterventionModel = $interventionModelFr !== '' || $interventionModelEn !== '';
        ?>
        <?php if ($hasInterventionModel): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text"
              data-fr="Schéma d’intervention"
              data-en="Intervention model">
              Schéma d’intervention
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?= esc_attr($interventionModelFr); ?>"
              data-en="<?= esc_attr($interventionModelEn); ?>">
              <?= esc_html($currentLang === 'fr' ? $interventionModelFr : $interventionModelEn); ?>
            </div>
          </div>
        <?php endif; ?>


        <?php
        $allocationModeFr = $jsonRec['fr']['additional']['allocation']['allocationMode'] ?? '';
        $allocationModeEn = $jsonRec['en']['additional']['allocation']['allocationMode'] ?? '';
        $allocationUnitFr = $jsonRec['fr']['additional']['allocation']['allocationUnit'] ?? '';
        $allocationUnitEn = $jsonRec['en']['additional']['allocation']['allocationUnit'] ?? '';

        // Clean and trim values
        $allocationModeFr = trim((string)$allocationModeFr);
        $allocationModeEn = trim((string)$allocationModeEn);
        $allocationUnitFr = trim((string)$allocationUnitFr);
        $allocationUnitEn = trim((string)$allocationUnitEn);

        $hasAllocationData =
          $allocationModeFr !== '' ||
          $allocationModeEn !== '' ||
          $allocationUnitFr !== '' ||
          $allocationUnitEn !== '';
        ?>
        <?php if ($hasAllocationData): ?>
          <div>
            <h3 class="field-content__h2 lang-text"
              data-fr="Allocation"
              data-en="Allocation">
              Allocation
            </h3>

            <?php if ($allocationModeFr || $allocationModeEn): ?>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text"
                  data-fr="Mode d’allocation"
                  data-en="Allocation type">
                  Mode d’allocation
                </div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?= esc_attr($allocationModeFr); ?>"
                  data-en="<?= esc_attr($allocationModeEn); ?>">
                  <?= esc_html($currentLang === 'fr' ? $allocationModeFr : $allocationModeEn); ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($allocationUnitFr || $allocationUnitEn): ?>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text"
                  data-fr="Unité d’allocation"
                  data-en="Allocation unit">
                  Unité d’allocation
                </div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?= esc_attr($allocationUnitFr); ?>"
                  data-en="<?= esc_attr($allocationUnitEn); ?>">
                  <?= esc_html($currentLang === 'fr' ? $allocationUnitFr : $allocationUnitEn); ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>


        <?php

        $maskingTypeFr = $jsonRec['fr']['additional']['masking']['maskingType'] ?? '';
        $maskingTypeEn = $jsonRec['en']['additional']['masking']['maskingType'] ?? '';

        $blindedDetailsFr = $jsonRec['fr']['additional']['masking']['blindedMaskingDetails'] ?? [];
        $blindedDetailsEn = $jsonRec['en']['additional']['masking']['blindedMaskingDetails'] ?? [];

        // Normalize to arrays
        if (!is_array($blindedDetailsFr)) $blindedDetailsFr = !empty($blindedDetailsFr) ? [$blindedDetailsFr] : [];
        if (!is_array($blindedDetailsEn)) $blindedDetailsEn = !empty($blindedDetailsEn) ? [$blindedDetailsEn] : [];

        // Clean array values
        $blindedDetailsFr = array_filter(array_map('trim', $blindedDetailsFr));
        $blindedDetailsEn = array_filter(array_map('trim', $blindedDetailsEn));

        // Clean text values
        $maskingTypeFr = trim((string)$maskingTypeFr);
        $maskingTypeEn = trim((string)$maskingTypeEn);

        $hasMaskingData = $maskingTypeFr !== '' || $maskingTypeEn !== '' || !empty($blindedDetailsFr) || !empty($blindedDetailsEn);
        ?>

        <?php if ($hasMaskingData): ?>
          <div>
            <h3 class="field-content__h2 lang-text"
              data-fr="Insu"
              data-en="Masking">
              Insu
            </h3>

            <?php if ($maskingTypeFr || $maskingTypeEn): ?>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text"
                  data-fr="Mode d'insu"
                  data-en="Masking type">
                  Mode d'insu
                </div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?= esc_attr($maskingTypeFr); ?>"
                  data-en="<?= esc_attr($maskingTypeEn); ?>">
                  <?= $currentLang === 'fr' ? $maskingTypeFr : $maskingTypeEn; ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if (!empty($blindedDetailsFr) || !empty($blindedDetailsEn)): ?>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text"
                  data-fr="Groupe(s) avec insu (en aveugle)"
                  data-en="Blinded masking group(s)">
                  Groupe(s) avec insu (en aveugle)
                </div>

                <div class="field-bloc__value lang-text"
                  data-fr="<?= esc_attr(buildTopicsHtml3($blindedDetailsFr)); ?>"
                  data-en="<?= esc_attr(buildTopicsHtml3($blindedDetailsEn)); ?>">
                  <?= $currentLang === 'fr'
                    ? buildTopicsHtml3($blindedDetailsFr)
                    : buildTopicsHtml3($blindedDetailsEn); ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>



        <?php
        $armsFr = $jsonRec['fr']['additional']['arms'] ?? [];
        $armsEn = $jsonRec['en']['additional']['arms'] ?? [];

        if (!is_array($armsFr)) $armsFr = [];
        if (!is_array($armsEn)) $armsEn = [];

        // Fonction pour vérifier si un arm a au moins un champ non vide
        function hasArmData($arm)
        {
          if (!is_array($arm)) return false;
          return !empty(array_filter($arm, fn($v) => trim((string)$v) !== ''));
        }
        $armsFr = array_filter($armsFr, 'hasArmData');
        $armsEn = array_filter($armsEn, 'hasArmData');
        $maxArms = max(count($armsFr), count($armsEn));
        $hasArmsData = !empty($armsFr) || !empty($armsEn);
        ?>

        <?php if ($hasArmsData): ?>
          <div>
            <h3 class="field-content__h2 lang-text"
              data-fr="Bras de l'étude"
              data-en="Study arms">
              Bras de l'étude
            </h3>

            <?php for ($index = 0; $index < $maxArms; $index++): ?>
              <?php
              $num = $index + 1;
              $armFr = $armsFr[$index] ?? [];
              $armEn = $armsEn[$index] ?? [];

              $nameFr = trim($armFr['name'] ?? '');
              $typeFr = trim($armFr['type'] ?? '');
              $typeOtherFr = trim($armFr['typeOther'] ?? '');
              $descriptionFr = trim($armFr['description'] ?? '');

              $nameEn = trim($armEn['name'] ?? '');
              $typeEn = trim($armEn['type'] ?? '');
              $typeOtherEn = trim($armEn['typeOther'] ?? '');
              $descriptionEn = trim($armEn['description'] ?? '');

              // Skip si tous les champs sont vides dans les deux langues
              if (
                !$nameFr && !$typeFr && !$typeOtherFr && !$descriptionFr &&
                !$nameEn && !$typeEn && !$typeOtherEn && !$descriptionEn
              ) {
                continue;
              }
              ?>

              <div class="field-card mb-3">
                <div class="field-card__header lang-text"
                  data-fr="Bras <?= $num; ?>"
                  data-en="Arm <?= $num; ?>">
                  Bras <?= $num; ?>
                </div>

                <div class="field-card__body">

                  <?php if ($nameFr || $nameEn): ?>
                    <div class="field-bloc mb-2">
                      <div class="field-bloc__title lang-text"
                        data-fr="Nom du bras"
                        data-en="Arm name">
                        Nom du bras
                      </div>
                      <div class="field-bloc__value lang-text"
                        data-fr="<?= nl2br(esc_html($nameFr)); ?>"
                        data-en="<?= nl2br(esc_html($nameEn)); ?>">
                        <?= nl2br(esc_html($currentLang === 'fr' ? $nameFr : $nameEn)); ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <?php if ($typeFr || $typeEn): ?>
                    <div class="field-bloc mb-2">
                      <div class="field-bloc__title lang-text"
                        data-fr="Type de bras"
                        data-en="Arm type">
                        Type de bras
                      </div>
                      <div class="field-bloc__value lang-text"
                        data-fr="<?= esc_html($typeFr); ?>"
                        data-en="<?= esc_html($typeEn); ?>">
                        <?= esc_html($currentLang === 'fr' ? $typeFr : $typeEn); ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <?php if ($typeOtherFr || $typeOtherEn): ?>
                    <div class="field-bloc mb-2">
                      <div class="field-bloc__title lang-text"
                        data-fr="Autre type de bras, précisions"
                        data-en="Other arm type, details">
                        Autre type de bras, précisions
                      </div>
                      <div class="field-bloc__value lang-text"
                        data-fr="<?= nl2br(esc_html($typeOtherFr)); ?>"
                        data-en="<?= nl2br(esc_html($typeOtherEn)); ?>">
                        <?= nl2br(esc_html($currentLang === 'fr' ? $typeOtherFr : $typeOtherEn)); ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <?php if ($descriptionFr || $descriptionEn): ?>
                    <div class="field-bloc mb-2">
                      <div class="field-bloc__title lang-text"
                        data-fr="Description du bras"
                        data-en="Arm description">
                        Description du bras
                      </div>
                      <div class="field-bloc__value lang-text"
                        data-fr="<?= nl2br(esc_html($descriptionFr)); ?>"
                        data-en="<?= nl2br(esc_html($descriptionEn)); ?>">
                        <?= nl2br(esc_html($currentLang === 'fr' ? $descriptionFr : $descriptionEn)); ?>
                      </div>
                    </div>
                  <?php endif; ?>

                </div>
              </div>
            <?php endfor; ?>
          </div>
        <?php endif; ?>




        <div>
          <?php
          $interventionsFr = $jsonRec['fr']['additional']['intervention'] ?? [];
          $interventionsEn = $jsonRec['en']['additional']['intervention'] ?? [];

          if (!is_array($interventionsFr)) $interventionsFr = [];
          if (!is_array($interventionsEn)) $interventionsEn = [];

          $interventionsFr = array_filter($interventionsFr, function ($item) {
            return !empty(array_filter($item, fn($v) => trim((string)$v) !== ''));
          });
          ?>

          <?php if (!empty($interventionsFr)): ?>
            <div>
              <h3 class="field-content__h2 lang-text"
                data-fr="Facteur, processus ou exposition étudié(e)"
                data-en="Studied factor, process, or exposure">
                Facteur, processus ou exposition étudié(e)
              </h3>

              <?php foreach ($interventionsFr as $index => $item): ?>
                <?php
                $num = $index + 1;
                $itemEn = $interventionsEn[$index] ?? [];

                $nameFr = trim($item['name'] ?? '');
                $typeFr = trim($item['type'] ?? '');
                $typeOtherFr = trim($item['typeOther'] ?? '');
                $descriptionFr = trim($item['description'] ?? '');

                $nameEn = trim($itemEn['name'] ?? '');
                $typeEn = trim($itemEn['type'] ?? '');
                $typeOtherEn = trim($itemEn['typeOther'] ?? '');
                $descriptionEn = trim($itemEn['description'] ?? '');

                // Skip empty entries
                if (!$nameFr && !$typeFr && !$typeOtherFr && !$descriptionFr && !$nameEn && !$typeEn && !$typeOtherEn && !$descriptionEn) continue;
                ?>

                <div class="field-card mb-3">
                  <div class="field-card__header lang-text"
                    data-fr="Facteur / Exposition <?= $num; ?>"
                    data-en="Factor / Exposure <?= $num; ?>">
                    Facteur / Exposition <?= $num; ?>
                  </div>

                  <div class="field-card__body">

                    <?php if ($nameFr || $nameEn): ?>
                      <div class="field-bloc mb-2">
                        <div class="field-bloc__title lang-text"
                          data-fr="Nom du facteur ou de l'exposition"
                          data-en="Factor/exposure name">
                          Nom du facteur ou de l'exposition
                        </div>
                        <div class="field-bloc__value lang-text"
                          data-fr="<?= nl2br(esc_html($nameFr)); ?>"
                          data-en="<?= nl2br(esc_html($nameEn)); ?>">
                          <?= nl2br(esc_html($currentLang === 'fr' ? $nameFr : $nameEn)); ?>
                        </div>
                      </div>
                    <?php endif; ?>

                    <?php if ($typeFr || $typeEn): ?>
                      <div class="field-bloc mb-2">
                        <div class="field-bloc__title lang-text"
                          data-fr="Type de facteur ou d'exposition"
                          data-en="Factor/exposure type">
                          Type de facteur ou d'exposition
                        </div>
                        <div class="field-bloc__value lang-text"
                          data-fr="<?= esc_html($typeFr); ?>"
                          data-en="<?= esc_html($typeEn); ?>">
                          <?= esc_html($currentLang === 'fr' ? $typeFr : $typeEn); ?>
                        </div>
                      </div>
                    <?php endif; ?>

                    <?php if ($typeOtherFr || $typeOtherEn): ?>
                      <div class="field-bloc mb-2">
                        <div class="field-bloc__title lang-text"
                          data-fr="Autre type de facteur ou d'exposition, précisions"
                          data-en="Other factor/exposure type, details">
                          Autre type de facteur ou d'exposition, précisions
                        </div>
                        <div class="field-bloc__value lang-text"
                          data-fr="<?= nl2br(esc_html($typeOtherFr)); ?>"
                          data-en="<?= nl2br(esc_html($typeOtherEn)); ?>">
                          <?= nl2br(esc_html($currentLang === 'fr' ? $typeOtherFr : $typeOtherEn)); ?>
                        </div>
                      </div>
                    <?php endif; ?>



                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

        </div>

      </div>
    <?php endif; ?>
  <?php endif; ?>


  <?php
  $activitiesFr = $jsonRec['fr']['study_desc']['study_development']['development_activity'] ?? [];
  $activitiesEn = $jsonRec['en']['study_desc']['study_development']['development_activity'] ?? [];

  if (!function_exists('findActivityByType')) {
    function findActivityByType($activities, $types)
    {
      foreach ($activities as $act) {
        $type = strtolower(trim($act['activity_type'] ?? ''));
        if (in_array($type, array_map('strtolower', $types))) {
          return trim($act['activity_description'] ?? '');
        }
      }
      return '';
    }
  }

  $primaryFr = findActivityByType($activitiesFr, ['évaluation primaire', 'primary evaluation']);
  $primaryEn = findActivityByType($activitiesEn, ['évaluation primaire', 'primary evaluation']);
  $secondaryFr = findActivityByType($activitiesFr, ['évaluation secondaire', 'secondary evaluation']);
  $secondaryEn = findActivityByType($activitiesEn, ['évaluation secondaire', 'secondary evaluation']);

  $hasHealthParams = $primaryFr || $primaryEn || $secondaryFr || $secondaryEn;
  ?>

  <?php if ($hasHealthParams): ?>
    <div class="submenu-study" id="healthparameters">
      <h2 class="field-content__h1 lang-text"
        data-fr="Critères d'évaluation (de jugement)"
        data-en="Primary outcome(s)">
        Critères d'évaluation (de jugement)
      </h2>

      <?php if ($primaryFr || $primaryEn): ?>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
            data-fr="Critère d'évaluation (de jugement) principal"
            data-en="Primary Outcome">
            Critère d'évaluation (de jugement) principal
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo nl2br(esc_attr($primaryFr)); ?>"
            data-en="<?php echo nl2br(esc_attr($primaryEn)); ?>">
            <?php echo nl2br(esc_html($currentLang === 'fr' ? $primaryFr : $primaryEn)); ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($secondaryFr || $secondaryEn): ?>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
            data-fr="Critère(s) d'évaluation (de jugement) secondaire(s)"
            data-en="Secondary Outcome(s)">
            Critère(s) d'évaluation (de jugement) secondaire(s)
          </div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo nl2br(esc_attr($secondaryFr)); ?>"
            data-en="<?php echo nl2br(esc_attr($secondaryEn)); ?>">
            <?php echo nl2br(esc_html($currentLang === 'fr' ? $secondaryFr : $secondaryEn)); ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
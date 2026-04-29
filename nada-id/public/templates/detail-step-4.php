<?php
if (!function_exists('buildTopicsHtml4')) {
    function buildTopicsHtml4(?array $items, string $class = 'checkbox-item', ?string $key = null): string
    {
        $html = '';

        foreach ($items as $item) {
            // Récupère la valeur selon le type d'élément
            if (is_array($item)) {
                $value = $key && isset($item[$key]) ? trim($item[$key]) : '';
            } else {
                $value = trim((string)$item);
            }

            // Supprime tout ce qui est entre parenthèses (et les parenthèses elles-mêmes)
            //      if ($key !== 'unitType') {
            //        $value = preg_replace('/\([^)]*\)/u', '', $value);
            //      }

            // Nettoie les doubles espaces ou virgules inutiles
            $value = trim(preg_replace('/\s*,\s*/u', ', ', $value));
            $value = preg_replace('/\s{2,}/u', ' ', $value);
            $value = trim($value, " ,");

            // Ajoute à la sortie si non vide
            if ($value !== '') {
                $html .= '<span class="' . htmlspecialchars($class) . '">' . htmlspecialchars($value) . '</span> ';
            }
        }

        return trim($html);
    }
}

if (!function_exists('buildTopicsHtmlLink')) {
    function buildTopicsHtmlLink($link)
    {

        if (filter_var($link, FILTER_VALIDATE_URL)) {
            $html = '<a href="' . htmlspecialchars($link) . '" target="_blank">' . htmlspecialchars($link) . '</a>';
        } else {
            $html = $link;
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
$unitTypeFr = $jsonRec['fr']['study_desc']['method']['data_collection']['sample_frame']['frame_unit']['unit_type'] ?? '';
$unitTypeEn = $jsonRec['en']['study_desc']['method']['data_collection']['sample_frame']['frame_unit']['unit_type'] ?? '';
$recruitmentSourceOtherFr = trim((string)($jsonRec['fr']['additional']['dataCollection']['recruitmentSourceOther'] ?? ''));
$recruitmentSourceOtherEn = trim((string)($jsonRec['en']['additional']['dataCollection']['recruitmentSourceOther'] ?? ''));

//  Helper to parse pseudo arrays like "['text1','text2','Autre']"
$parsePseudoArray = function ($value) {
    if (empty($value)) return [];
    if (is_array($value)) return array_values(array_filter(array_map('trim', $value), 'strlen'));

    $s = trim((string)$value);
    // remove surrounding [ ]
    if (str_starts_with($s, '[') && str_ends_with($s, ']')) {
        $s = substr($s, 1, -1);
    }

    // split on ',' that separates single-quoted items
    $parts = preg_split("/'\\s*,\\s*'/u", trim($s, "'\" "));
    $parts = array_map(function ($p) {
        $p = trim($p);
        $p = str_replace(['\\"', "\\'"], ['"', "'"], $p);
        return $p;
    }, $parts);

    return array_values(array_filter($parts, fn($v) => $v !== ''));
};

// Parse and join
$unitTypeFr = $parsePseudoArray($unitTypeFr);
$unitTypeEn = $parsePseudoArray($unitTypeEn);
$unitTypeFrStr = implode(', ', $unitTypeFr);
$unitTypeEnStr = implode(', ', $unitTypeEn);

// Detect presence
$hasUnitType = $unitTypeFrStr !== '' || $unitTypeEnStr !== '';
$hasRecruitmentSourceOther = $recruitmentSourceOtherFr !== '' || $recruitmentSourceOtherEn !== '';
?>

<div class="submenu-study" id="regulatoryCollecte">
    <h2 class="field-content__h1 lang-text"
        data-fr="Collecte et réutilisation de données"
        data-en="Data collection and reuse">
        Collecte et réutilisation de données
    </h2>
    <?php

    $plannedSampleFr = trim((string)($jsonRec['fr']['study_desc']['method']['data_collection']['target_sample_size'] ?? ''));
    $plannedSampleEn = trim((string)($jsonRec['en']['study_desc']['method']['data_collection']['target_sample_size'] ?? ''));
    $actualSampleFr = trim((string)($jsonRec['fr']['study_desc']['method']['data_collection']['response_rate'] ?? ''));
    $actualSampleEn = trim((string)($jsonRec['en']['study_desc']['method']['data_collection']['response_rate'] ?? ''));

    $hasPlannedSample = $plannedSampleFr !== '' || $plannedSampleEn !== '';
    $hasActualSample = $actualSampleFr !== '' || $actualSampleEn !== '';

    $hasSampleSection = $hasPlannedSample || $hasActualSample;
    ?>
    <?php if ($hasSampleSection): ?>

        <h3 class="lang-text-tooltip "
            tooltip-fr="Nombre de participants prévus et effectifs"
            tooltip-en="Planned and actual number of participants"
            data-fr="Nombre actuel de participants"
            data-en="Number of participants">
            <span class="contentSection lang-text" data-fr="Nombre actuel de participants"
                data-en="Number of participants">Nombre de participants</span>
            <!-- <span class="info-bulle" attr-lng="fr"
                  data-text="Nombre de participants prévus et effectifs">
                  <span class="dashicons dashicons-info"></span>
                </span> -->
        </h3>
        <div class="row">
            <?php if ($hasPlannedSample): ?>
                <div class="col-md-6 mb-3">
                    <div class="field-bloc mb-2">
                        <div class="field-bloc__title lang-text"
                            data-fr="Nombre prévu de participants"
                            data-en="Expected number of participants">
                            Nombre prévu de participants
                        </div>
                        <div class="field-bloc__value lang-text"
                            data-fr="<?= esc_attr($plannedSampleFr); ?>"
                            data-en="<?= esc_attr($plannedSampleEn); ?>">
                            <?= esc_html($currentLang === 'fr' ? $plannedSampleFr : $plannedSampleEn); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($hasActualSample): ?>
                <div class="col-md-6 mb-3">
                    <div class="field-bloc mb-2">
                        <div class="field-bloc__title lang-text"
                            data-fr="Nombre réel de participants"
                            data-en="Actual number of participants">
                            Nombre réel de participants
                        </div>
                        <div class="field-bloc__value lang-text"
                            data-fr="<?= nl2br(esc_attr($actualSampleFr)); ?>"
                            data-en="<?= nl2br(esc_attr($actualSampleEn)); ?>">
                            <?= nl2br(esc_html($currentLang === 'fr' ? $actualSampleFr : $actualSampleEn)); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>




    <?php

    $collDatesFr = $jsonRec['fr']['study_desc']['study_info']['coll_dates'] ?? [];
    $collDatesEn = $jsonRec['en']['study_desc']['study_info']['coll_dates'] ?? [];
    $frequencyFr = trim((string)($jsonRec['fr']['study_desc']['method']['data_collection']['frequency'] ?? ''));
    $frequencyEn = trim((string)($jsonRec['en']['study_desc']['method']['data_collection']['frequency'] ?? ''));

    if (!is_array($collDatesFr)) $collDatesFr = [];
    if (!is_array($collDatesEn)) $collDatesEn = [];

    // Extract first period (if exists)
    $startFr = trim($collDatesFr[0]['start'] ?? '');
    $endFr = trim($collDatesFr[0]['end'] ?? '');
    $startEn = trim($collDatesEn[0]['start'] ?? '');
    $endEn = trim($collDatesEn[0]['end'] ?? '');

    // Remove default end date if it's 3000-01-01
    if ($endFr === '3000-01-01') $endFr = '';
    if ($endEn === '3000-01-01') $endEn = '';

    // Determine if fields have data
    $hasStartDate = $startFr !== '' || $startEn !== '';
    $hasEndDate = $endFr !== '' || $endEn !== '';
    $hasFrequency = $frequencyFr !== '' || $frequencyEn !== '';

    // Show the entire section only if something is filled
    $hasCollectionChronology = $hasStartDate || $hasEndDate || $hasFrequency;
    ?>
    <?php if ($hasCollectionChronology): ?>
        <div class="submenu-study" id="collectionChronology">
            <h3 class="lang-text-tooltip"
                tooltip-fr="Indique la période pendant laquelle les données de l’étude ont été recueillies."
                tooltip-en="Indicates the period during which the study data were collected."
                data-fr="Chronologie de la collecte"
                data-en="Collection Chronology">
                <span class="contentSection lang-text" data-fr="Chronologie de la collecte"
                    data-en="Collection Chronology">Chronologie de la collecte</span>
                <!-- <span class="info-bulle" attr-lng="fr"
                      data-text="Indique la période pendant laquelle les données de l’étude ont été recueillies.">
                      <span class="dashicons dashicons-info"></span>
                    </span> -->
            </h3>

            <div class="row">
                <?php
                if ($hasStartDate):
                    // Convertir AAAA-mm-jj → JJ/MM/AAAA
                    $startFrFormatted = date("d/m/Y", strtotime($startFr));
                    $startEnFormatted = date("d/m/Y", strtotime($startEn));
                ?>
                    <div class="col-md-6 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Date de début de la collecte (recrutement du premier participant)"
                                data-en="Collection start date (recruitment of the first participant)">
                                Date de début de la collecte (recrutement du premier participant)
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_attr($startFrFormatted); ?>"
                                data-en="<?= esc_attr($startEnFormatted); ?>">
                                <?= esc_html($currentLang === 'fr' ? $startFrFormatted : $startEnFormatted); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                if ($hasEndDate):
                    $endFrFormatted = date("d/m/Y", strtotime($endFr));
                    $endEnFormatted = date("d/m/Y", strtotime($endEn));
                ?>
                    <div class="col-md-6 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Date de fin de la collecte (dernier suivi du dernier participant)"
                                data-en="Collection end date (last follow-up of the last participant)">
                                Date de fin de la collecte (dernier suivi du dernier participant)
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_attr($endFrFormatted); ?>"
                                data-en="<?= esc_attr($endEnFormatted); ?>">
                                <?= esc_html($currentLang === 'fr' ? $endFrFormatted : $endEnFormatted); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($hasFrequency): ?>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Fréquence de la collecte"
                                data-en="Collection frequency">
                                Fréquence de la collecte
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr($frequencyFr)); ?>"
                                data-en="<?= nl2br(esc_attr($frequencyEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $frequencyFr : $frequencyEn)); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>


    <?php
    // Fetch data (FR + EN)
    $collModeFr = $jsonRec['fr']['study_desc']['method']['data_collection']['coll_mode'] ?? [];
    $collModeEn = $jsonRec['en']['study_desc']['method']['data_collection']['coll_mode'] ?? [];

    $collectionModeOtherFr = trim((string)($jsonRec['fr']['additional']['collectionProcess']['collectionModeOther'] ?? ''));
    $collectionModeOtherEn = trim((string)($jsonRec['en']['additional']['collectionProcess']['collectionModeOther'] ?? ''));

    $collectionModeDetailsFr = trim((string)($jsonRec['fr']['additional']['collectionProcess']['collectionModeDetails'] ?? ''));
    $collectionModeDetailsEn = trim((string)($jsonRec['en']['additional']['collectionProcess']['collectionModeDetails'] ?? ''));

    $samplingProcFr = $jsonRec['fr']['study_desc']['method']['data_collection']['sampling_procedure'] ?? '';
    $samplingProcEn = $jsonRec['en']['study_desc']['method']['data_collection']['sampling_procedure'] ?? '';

    $samplingModeOtherFr = trim((string)($jsonRec['fr']['additional']['dataCollection']['samplingModeOther'] ?? ''));
    $samplingModeOtherEn = trim((string)($jsonRec['en']['additional']['dataCollection']['samplingModeOther'] ?? ''));

    // Normalize arrays if needed
    $values = [];

    if (is_array($collModeFr)) {
        foreach ($collModeFr as $item) {
            $decoded = json_decode($item, true);
            if (is_array($decoded) && isset($decoded['value'])) {
                $values[] = $decoded['value'];
            }
        }
    } else {
        $decoded = json_decode($collModeFr, true);
        if (is_array($decoded) && isset($decoded['value'])) {
            $values[] = $decoded['value'];
        } elseif (!empty($collModeFr)) {
            $values[] = $collModeFr;
        }
    }

    $collModeFr = $values;
    $values = [];
    if (is_array($collModeEn)) {
        foreach ($collModeEn as $item) {
            $decoded = json_decode($item, true);
            if (is_array($decoded) && isset($decoded['value'])) {
                $values[] = $decoded['value'];
            }
        }
    } else {
        $decoded = json_decode($collModeEn, true);
        if (is_array($decoded) && isset($decoded['value'])) {
            $values[] = $decoded['value'];
        } elseif (!empty($collModeEn)) {
            $values[] = $collModeEn;
        }
    }
    $collModeEn = $values;

    // Join as comma-separated strings
    $collModeFrStr = $collModeFr !== [] ? trim(implode(', ', array_filter($collModeFr))) : '';
    $collModeEnStr = $collModeEn !== [] ? trim(implode(', ', array_filter($collModeEn))) : '';
    // Decode JSON-like sampling procedure strings (handle malformed single-quote JSON safely)
    $fixJsonString = function ($str) {
        if (empty($str)) return [];

        // Nettoyage basique
        $clean = trim($str);
        if (str_starts_with($clean, '[') && str_ends_with($clean, ']')) {
            $clean = substr($clean, 1, -1);
        }

        // On découpe seulement sur les virgules qui NE SONT PAS dans des parenthèses
        $result = [];
        $buffer = '';
        $depth = 0; // Compte les parenthèses ouvertes

        for ($i = 0; $i < strlen($clean); $i++) {
            $char = $clean[$i];

            if ($char === '(') {
                $depth++;
                $buffer .= $char;
            } elseif ($char === ')') {
                $depth--;
                $buffer .= $char;
            } elseif ($char === ',' && $depth === 0) {
                // Virgule de séparation principale
                $trimmed = trim($buffer);
                if ($trimmed !== '') $result[] = preg_replace('/^["\']|["\']$/', '', $trimmed);
                $buffer = '';
            } else {
                $buffer .= $char;
            }
        }

        // Dernier élément
        $trimmed = trim($buffer);
        if ($trimmed !== '') $result[] = preg_replace('/^["\']|["\']$/', '', $trimmed);

        return array_map('stripslashes', $result);
    };


    //Apply decoding for FR/EN
    $samplingProcFrForCondition = is_array($samplingProcFr) ? $samplingProcFr : $fixJsonString($samplingProcFr);
    $samplingProcEnForCondition = is_array($samplingProcEn) ? $samplingProcEn : $fixJsonString($samplingProcEn);


    //Join them safely
    $samplingProcFrStr = trim(implode(', ', array_filter($samplingProcFrForCondition)));
    $samplingProcEnStr = trim(implode(', ', array_filter($samplingProcEnForCondition)));
    preg_match_all('/value":"([^"]+)"/', $samplingProcFrStr, $matchesFr);
    $samplingProcValuesFr = $matchesFr[1]; // array containing all extracted values
    preg_match_all('/value":"([^"]+)"/', $samplingProcEnStr, $matchesEn);
    $samplingProcValuesEn = $matchesEn[1]; // array containing all extracted values

    // Determine what has data
    $hasCollMode = $collModeFrStr !== '' || $collModeEnStr !== '';
    $hasCollModeOther = $collectionModeOtherFr !== '' || $collectionModeOtherEn !== '';
    $hasCollModeDetails = $collectionModeDetailsFr !== '' || $collectionModeDetailsEn !== '';
    $hasSamplingProc = $samplingProcFrStr !== '' || $samplingProcEnStr !== '';
    $hasSamplingModeOther = $samplingModeOtherFr !== '' || $samplingModeOtherEn !== '';

    // Show whole section only if something is filled
    $hasCollectionProcess = $hasCollMode || $hasCollModeOther || $hasCollModeDetails || $hasSamplingProc || $hasSamplingModeOther || $hasUnitType || $hasRecruitmentSourceOther;
    ?>
    <?php if ($hasCollectionProcess): ?>
        <div class="submenu-study" id="collectionProcess">
            <h3 class="lang-text-tooltip"
                tooltip-fr="Section décrivant la manière dont les données sont recueillies dans le cadre de l’étude."
                tooltip-en="Section describing how data are collected in the study."
                data-fr="Procédure de collecte"
                data-en="Collection Procedure">
                <span class="contentSection lang-text" data-fr="Procédure de collecte"
                    data-en="Collection Procedure">Procédure de collecte</span>
                <!-- <span class="info-bulle" attr-lng="fr"
                      data-text="Section décrivant la manière dont les données sont recueillies dans le cadre de l’étude.">
                      <span class="dashicons dashicons-info"></span>
                    </span> -->
            </h3>

            <div class="row">
                <?php if ($hasCollMode): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Mode de collecte"
                                data-en="Collection Mode">
                                Mode de collecte
                            </div>

                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_attr(buildTopicsHtml4($collModeFr)); ?>"
                                data-en="<?= esc_attr(buildTopicsHtml4($collModeEn)); ?>">
                                <?= $currentLang === 'fr'
                                    ? buildTopicsHtml4($collModeFr)
                                    : buildTopicsHtml4($collModeEn); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                $hasAutreCollModeFr = in_array('Autre', array_map('trim', $collModeFr), true)
                    || in_array('Autre', array_map('trim', $collModeFr), true);

                $hasAutreCollModeEn = in_array('Autre', array_map('trim', $collModeEn), true)
                    || in_array('Other', array_map('trim', $collModeEn), true);

                $showAutreCollMode = ($currentLang === 'fr' && $hasAutreCollModeFr)
                    || ($currentLang === 'en' && $hasAutreCollModeEn);
                ?>

                <?php if ($showAutreCollMode && $hasCollModeOther): ?>

                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Autre mode de collecte, précisions"
                                data-en="Other collection mode, details">
                                Autre mode de collecte, précisions
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr($collectionModeOtherFr)); ?>"
                                data-en="<?= nl2br(esc_attr($collectionModeOtherEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $collectionModeOtherFr : $collectionModeOtherEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($hasCollModeDetails): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Mode de collecte, précisions"
                                data-en="Collection mode, details">
                                Mode de collecte, précisions
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr($collectionModeDetailsFr)); ?>"
                                data-en="<?= nl2br(esc_attr($collectionModeDetailsEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $collectionModeDetailsFr : $collectionModeDetailsEn)); ?>
                            </div>

                        </div>
                    </div>
                <?php endif; ?>

                <?php
                $valuesFr = [];
                $valuesEn = [];
                // Step 1: Remove outer brackets
                $inputFr = trim($samplingProcFr, "[]");

                // Step 2: Split by the pattern `','` (escaped single quotes)
                $parts = preg_split("/','/", $inputFr);

                // Step 3: Remove leftover single quotes
                $parts = array_map(fn($p) => trim($p, "'"), $parts);


                // Step 4: Decode each JSON string
                $objects = array_map(fn($p) => json_decode($p, true), $parts);

                $valuesEn = [];
                foreach ($objects as $object) {
                    if ($object && $object["value"]) {
                        $valuesFr[] = ($object["value"]);
                    } else {
                        continue;
                    }
                }
                // Step 1: Remove outer brackets
                $inputEn = trim($samplingProcEn, "[]");

                // Step 2: Split by the pattern `','` (escaped single quotes)
                $parts = preg_split("/','/", $inputEn);

                // Step 3: Remove leftover single quotes
                $parts = array_map(fn($p) => trim($p, "'"), $parts);

                // Step 4: Decode each JSON string
                $objects = array_map(fn($p) => json_decode($p, true), $parts);

                $valuesEn = [];
                foreach ($objects as $object) {
                    if ($object && $object["value"]) {
                        $valuesEn[] = ($object["value"]);
                    } else {
                        continue;
                    }
                }


                // Vérifie si "Autre" (fr) ou "Other" (en) est présent dans la procédure d’échantillonnage
                $hasAutreSamplingFr = in_array('Autre', $valuesFr, true);

                $hasAutreSamplingEn = in_array('Other', $valuesEn, true);

                // Décide si on affiche le champ selon la langue actuelle
                $showAutreSampling = ($currentLang === 'fr' && $hasAutreSamplingFr)
                    || ($currentLang === 'en' && $hasAutreSamplingEn);
                ?>

                <?php

                if ($hasSamplingProc): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Procédure d’échantillonnage à l’inclusion"
                                data-en="Sampling procedure at inclusion">
                                Procédure d’échantillonnage à l’inclusion
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_attr(buildTopicsHtml4($samplingProcValuesFr)); ?>"
                                data-en="<?= esc_attr(buildTopicsHtml4($samplingProcValuesEn)); ?>">
                                <?= $currentLang === 'fr'
                                    ? buildTopicsHtml4($samplingProcValuesFr)
                                    : buildTopicsHtml4($samplingProcValuesEn); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($showAutreSampling && $hasSamplingModeOther): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Autre procédure d’échantillonnage, précisions"
                                data-en="Other sampling mode, details">
                                Autre procédure d’échantillonnage, précisions
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr($samplingModeOtherFr)); ?>"
                                data-en="<?= nl2br(esc_attr($samplingModeOtherEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $samplingModeOtherFr : $samplingModeOtherEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                $unitTypeFrStrr = is_array($unitTypeFrStr) ? $unitTypeFrStr : $fixJsonString($unitTypeFrStr);
                $unitTypeEnStrr = is_array($unitTypeEnStr) ? $unitTypeEnStr : $fixJsonString($unitTypeEnStr);

                // $unitTypeFrStrr et $unitTypeEnStrr sont les tableaux ou chaînes des sources de recrutement
                $unitTypeFrArr = is_array($unitTypeFrStrr) ? $unitTypeFrStrr : array_filter(array_map('trim', explode(';', $unitTypeFrStrr)));
                $unitTypeEnArr = is_array($unitTypeEnStrr) ? $unitTypeEnStrr : array_filter(array_map('trim', explode(';', $unitTypeEnStrr)));

                // Vérifie si "Autre" (fr) ou "Other" (en) est présent
                $hasAutreRecruitmentFr = in_array('Autre', $unitTypeFrArr, true) || in_array('Other', $unitTypeFrArr, true);
                $hasAutreRecruitmentEn = in_array('Autre', $unitTypeEnArr, true) || in_array('Other', $unitTypeEnArr, true);

                // Décide si on affiche le champ selon la langue
                $showAutreRecruitment = ($currentLang === 'fr' && $hasAutreRecruitmentFr)
                    || ($currentLang === 'en' && $hasAutreRecruitmentEn);

                ?>

                <?php if ($hasUnitType || $hasRecruitmentSourceOther): ?>
                    <?php if ($hasUnitType): ?>
                        <div class="col-md-12 mb-3">
                            <div class="field-bloc mb-2">
                                <div class="field-bloc__title lang-text"
                                    data-fr="Source de recrutement des participants"
                                    data-en="Participants recruitment source">
                                    Source de recrutement des participants
                                </div>
                                <div class="field-bloc__value lang-text"
                                    data-fr="<?= esc_attr(buildTopicsHtml4($unitTypeFrStrr, 'checkbox-item', 'unitType')); ?>"
                                    data-en="<?= esc_attr(buildTopicsHtml4($unitTypeEnStrr, 'checkbox-item', 'unitType')); ?>">
                                    <?= $currentLang === 'fr'
                                        ? buildTopicsHtml4($unitTypeFrStrr, 'checkbox-item', 'unitType')
                                        : buildTopicsHtml4($unitTypeEnStrr, 'checkbox-item', 'unitType'); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($showAutreRecruitment && $hasRecruitmentSourceOther): ?>
                        <div class="col-md-12 mb-3">
                            <div class="field-bloc mb-2">
                                <div class="field-bloc__title lang-text"
                                    data-fr="Autre source de recrutement, précisions"
                                    data-en="Other recruitment source, details">
                                    Autre source de recrutement, précisions
                                </div>
                                <div class="field-bloc__value lang-text"
                                    data-fr="<?= nl2br(esc_attr($recruitmentSourceOtherFr)); ?>"
                                    data-en="<?= nl2br(esc_attr($recruitmentSourceOtherEn)); ?>">
                                    <?= nl2br(esc_html($currentLang === 'fr' ? $recruitmentSourceOtherFr : $recruitmentSourceOtherEn)); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>


    <?php
    $isActiveFollowUpFr = $jsonRec['fr']['additional']['activeFollowUp']['isActiveFollowUp'] ?? '';
    $isActiveFollowUpEn = $jsonRec['en']['additional']['activeFollowUp']['isActiveFollowUp'] ?? '';

    $notesFr = $jsonRec['fr']['study_desc']['method']['notes'] ?? [];
    $notesEn = $jsonRec['en']['study_desc']['method']['notes'] ?? [];

    // Extract values only where subject == "follow-up"
    $followUpModeFrValues = [];
    foreach ($notesFr as $note) {
        if (
            isset($note['subject']) &&
            strtolower(trim($note['subject'])) === 'follow-up' &&
            !empty($note['values'])
        ) {
            $followUpModeFrValues = array_merge($followUpModeFrValues, $note['values']);
        }
    }

    $followUpModeEnValues = [];
    foreach ($notesEn as $note) {
        if (
            isset($note['subject']) &&
            strtolower(trim($note['subject'])) === 'follow-up' &&
            !empty($note['values'])
        ) {
            $followUpModeEnValues = array_merge($followUpModeEnValues, $note['values']);
        }
    }

    // Convert to strings
    $followUpModeFrStr = implode(', ', $followUpModeFrValues);
    $followUpModeEnStr = implode(', ', $followUpModeEnValues);

    // Additional optional values
    $followUpModeOtherFr = trim((string)($jsonRec['fr']['additional']['activeFollowUp']['followUpModeOther'] ?? ''));
    $followUpModeOtherEn = trim((string)($jsonRec['en']['additional']['activeFollowUp']['followUpModeOther'] ?? ''));


    $isActiveFollowUpFr =  normalizeBooleanValue($isActiveFollowUpFr, 'fr');
    $isActiveFollowUpEn = normalizeBooleanValue($isActiveFollowUpEn, 'en');

    // Section visibility
    $hasIsActiveFollowUp = $isActiveFollowUpFr !== '' || $isActiveFollowUpEn !== '';
    $hasFollowUpMode = $followUpModeFrStr !== '' || $followUpModeEnStr !== '';
    $hasFollowUpModeOther = $followUpModeOtherFr !== '' || $followUpModeOtherEn !== '';

    $hasActiveFollowUpSection = $hasIsActiveFollowUp || $hasFollowUpMode || $hasFollowUpModeOther;
    ?>

    <?php if ($hasActiveFollowUpSection): ?>
        <div class="submenu-study" id="activeFollowUp">
            <h3 class="lang-text-tooltip"
                tooltip-fr="Le suivi actif désigne une procédure systématique de collecte d’informations auprès des participants après leur inclusion dans l’étude, à l’initiative de l’équipe de recherche."
                tooltip-en="Active follow-up refers to a systematic process of collecting information from participants after their inclusion in the study, initiated by the research team."
                data-fr="Suivi actif des participants"
                data-en="Active follow-up">
                <span class="contentSection lang-text" data-fr="Suivi actif des participants"
                    data-en="Active follow-up">Suivi actif des participants</span>
                <!-- <span class="info-bulle" attr-lng="fr"
                      data-text="Le suivi actif désigne une procédure systématique de collecte d’informations auprès des participants après leur inclusion dans l’étude, à l’initiative de l’équipe de recherche.">
                      <span class="dashicons dashicons-info"></span>
                    </span> -->
            </h3>

            <div class="row">
                <?php if ($hasIsActiveFollowUp): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Un suivi actif des participants est-il réalisé ?"
                                data-en="It is an active follow-up?">
                                Un suivi actif des participants est-il réalisé ?
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_attr($isActiveFollowUpFr); ?>"
                                data-en="<?= esc_attr($isActiveFollowUpEn); ?>">
                                <?= esc_html($currentLang === 'fr' ? $isActiveFollowUpFr : $isActiveFollowUpEn); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
                $followUpModeFrArr = is_array($followUpModeFrStr) ? $followUpModeFrStr : array_filter(array_map('trim', explode(';', $followUpModeFrStr)));
                $followUpModeEnArr = is_array($followUpModeEnStr) ? $followUpModeEnStr : array_filter(array_map('trim', explode(';', $followUpModeEnStr)));

                $followUpModeFrArr = is_array($followUpModeFrStr)
                    ? $followUpModeFrStr
                    : splitOutsideParentheses($followUpModeFrStr);

                $followUpModeEnArr = is_array($followUpModeEnStr)
                    ? $followUpModeEnStr
                    : splitOutsideParentheses($followUpModeEnStr);

                $containsAutreFollowUpModeFr = in_array('Autre', array_map('trim', $followUpModeFrArr), true)
                    || in_array('Other', array_map('trim', $followUpModeFrArr), true);

                $containsAutreFollowUpModeEn = in_array('Autre', array_map('trim', $followUpModeEnArr), true)
                    || in_array('Other', array_map('trim', $followUpModeEnArr), true);

                $showAutreFollowUpMode = ($currentLang === 'fr' && $containsAutreFollowUpModeFr)
                    || ($currentLang === 'en' && $containsAutreFollowUpModeEn);

                $isFollowUpInactive =
                    strtolower(trim($isActiveFollowUpFr)) === 'non' ||
                    strtolower(trim($isActiveFollowUpEn)) === 'no';
                ?>

                <?php if ($hasFollowUpMode && !$isFollowUpInactive): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Modalités du suivi actif"
                                data-en="Follow-up method">
                                Modalités du suivi actif
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_attr(buildTopicsHtml4($followUpModeFrArr)); ?>"
                                data-en="<?= esc_attr(buildTopicsHtml4($followUpModeEnArr)); ?>">
                                <?= $currentLang === 'fr'
                                    ? buildTopicsHtml4($followUpModeFrArr)
                                    : buildTopicsHtml4($followUpModeEnArr); ?>
                            </div>

                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($showAutreFollowUpMode && $hasFollowUpModeOther && !$isFollowUpInactive): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Autre modalités de suivi, précisions"
                                data-en="Other follow-up method, details">
                                Autre modalité de suivi, précisions
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr($followUpModeOtherFr)); ?>"
                                data-en="<?= nl2br(esc_attr($followUpModeOtherEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $followUpModeOtherFr : $followUpModeOtherEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>


    <?php
    $dataKindFr = $jsonRec['fr']['study_desc']['study_info']['data_kind'] ?? '';
    $dataKindEn = $jsonRec['en']['study_desc']['study_info']['data_kind'] ?? '';

    $dataTypeOtherFr = trim((string)($jsonRec['fr']['additional']['dataTypes']['dataTypeOther'] ?? ''));
    $dataTypeOtherEn = trim((string)($jsonRec['en']['additional']['dataTypes']['dataTypeOther'] ?? ''));

    $clinicalDataDetailsFr = trim((string)($jsonRec['fr']['additional']['dataTypes']['clinicalDataDetails'] ?? ''));
    $clinicalDataDetailsEn = trim((string)($jsonRec['en']['additional']['dataTypes']['clinicalDataDetails'] ?? ''));

    $paraclinicalDataOtherFr = trim((string)($jsonRec['fr']['additional']['dataTypes']['paraclinicalDataOther'] ?? ''));
    $paraclinicalDataOtherEn = trim((string)($jsonRec['en']['additional']['dataTypes']['paraclinicalDataOther'] ?? ''));

    $biologicalDataDetailsFr = trim((string)($jsonRec['fr']['additional']['dataTypes']['biologicalDataDetails'] ?? ''));
    $biologicalDataDetailsEn = trim((string)($jsonRec['en']['additional']['dataTypes']['biologicalDataDetails'] ?? ''));

    $isDataInBiobankFr = trim((string)($jsonRec['fr']['additional']['dataTypes']['isDataInBiobank'] ?? ''));
    $isDataInBiobankEn = trim((string)($jsonRec['en']['additional']['dataTypes']['isDataInBiobank'] ?? ''));
    $biobankContentFr = $jsonRec['fr']['additional']['dataTypes']['biobankContent'] ?? [];
    $biobankContentEn = $jsonRec['en']['additional']['dataTypes']['biobankContent'] ?? [];

    $biobankContentOtherFr = trim((string)($jsonRec['fr']['additional']['dataTypes']['biobankContentOther'] ?? ''));
    $biobankContentOtherEn = trim((string)($jsonRec['en']['additional']['dataTypes']['biobankContentOther'] ?? ''));

    $otherLiquidsDetailsFr = trim((string)($jsonRec['fr']['additional']['dataTypes']['otherLiquidsDetails'] ?? ''));
    $otherLiquidsDetailsEn = trim((string)($jsonRec['en']['additional']['dataTypes']['otherLiquidsDetails'] ?? ''));

    $parsePseudoArray = function ($value) {
        if (empty($value)) return [];
        if (is_array($value)) return array_values(array_filter(array_map('trim', $value), 'strlen'));

        $s = trim((string)$value);
        if (str_starts_with($s, '[') && str_ends_with($s, ']')) {
            $s = substr($s, 1, -1);
        }

        $parts = preg_split("/'\\s*,\\s*'/u", trim($s, "'\" "));
        $parts = array_map(function ($p) {
            $p = trim($p);
            $p = str_replace(['\\"', "\\'"], ['"', "'"], $p);
            return $p;
        }, $parts);

        return array_values(array_filter($parts, fn($v) => $v !== ''));
    };

    $dataKindFr = $parsePseudoArray($dataKindFr);
    $dataKindEn = $parsePseudoArray($dataKindEn);
    $biobankContentFr = $parsePseudoArray($biobankContentFr);
    $biobankContentEn = $parsePseudoArray($biobankContentEn);

    $dataKindFrStr = implode(', ', $dataKindFr);
    $dataKindEnStr = implode(', ', $dataKindEn);
    $biobankContentFrStr = implode(', ', $biobankContentFr);
    $biobankContentEnStr = implode(', ', $biobankContentEn);

    // Determine presence of data
    $hasDataKind = $dataKindFrStr !== '' || $dataKindEnStr !== '';
    $hasDataTypeOther = $dataTypeOtherFr !== '' || $dataTypeOtherEn !== '';
    $hasClinicalDataDetails = $clinicalDataDetailsFr !== '' || $clinicalDataDetailsEn !== '';
    $hasParaclinicalDataOther = $paraclinicalDataOtherFr !== '' || $paraclinicalDataOtherEn !== '';
    $hasBiologicalDataDetails = $biologicalDataDetailsFr !== '' || $biologicalDataDetailsEn !== '';
    $hasBiobankContent = $biobankContentFrStr !== '' || $biobankContentEnStr !== '';
    $hasBiobankContentOther = $biobankContentOtherFr !== '' || $biobankContentOtherEn !== '';
    $hasOtherLiquidsDetails = $otherLiquidsDetailsFr !== '' || $otherLiquidsDetailsEn !== '';


    $isDataIntegrationFr = trim((string)($jsonRec['fr']['additional']['dataCollectionIntegration']['isDataIntegration'] ?? ''));
    $isDataIntegrationEn = trim((string)($jsonRec['en']['additional']['dataCollectionIntegration']['isDataIntegration'] ?? ''));

    // Normalization to always show Oui/Non or Yes/No properly
    $isDataIntegrationFr = normalizeBooleanValue($isDataIntegrationFr, 'fr');
    $isDataIntegrationEn = normalizeBooleanValue($isDataIntegrationEn, 'en');

    $hasIsDataIntegration = $isDataIntegrationFr !== '' || $isDataIntegrationEn !== '';

    $hasDataTypesSection =
        $hasDataKind || $hasDataTypeOther || $hasClinicalDataDetails || $hasParaclinicalDataOther ||
        $hasBiologicalDataDetails || $hasBiobankContent ||
        $hasBiobankContentOther || $hasOtherLiquidsDetails || $hasIsDataIntegration;
    ?>
    <?php if ($hasDataTypesSection): ?>
        <div class="submenu-study" id="dataTypes">
            <h3 class="lang-text-tooltip"
                tooltip-fr="Décrit les types de données recueillies au cours de l'étude auprès des participants."
                tooltip-en="Describes the types of data collected directly from study participants during the study."
                data-fr="Types de données"
                data-en="Data types">
                <span class="contentSection lang-text" data-fr="Types de données"
                    data-en="Data types">Types de données</span>
                <!-- <span class="info-bulle" attr-lng="fr"
                      data-text="Décrit les types de données recueillies au cours de l'étude auprès des participants.">
                      <span class="dashicons dashicons-info"></span>
                    </span> -->
            </h3>

            <?php
            $containsAutreDataKindFr = in_array('Autre', array_map('trim', $dataKindFr), true)
                || in_array('Other', array_map('trim', $dataKindFr), true);

            $containsAutreDataKindEn = in_array('Autre', array_map('trim', $dataKindEn), true)
                || in_array('Other', array_map('trim', $dataKindEn), true);

            $showAutreDataKind = ($currentLang === 'fr' && $containsAutreDataKindFr)
                || ($currentLang === 'en' && $containsAutreDataKindEn);

            // --- Conditions de présence selon la langue ---
            $containsClinicalFr = in_array('Données cliniques', array_map('trim', $dataKindFr), true);
            $containsClinicalEn = in_array('Clinical data', array_map('trim', $dataKindEn), true);

            $containsParaclinicalFr = in_array('Données paracliniques (hors biologiques)', array_map('trim', $dataKindFr), true);
            $containsParaclinicalEn = in_array('Paraclinical data (non-biological)', array_map('trim', $dataKindEn), true);

            $containsBiologicalFr = in_array('Données biologiques', array_map('trim', $dataKindFr), true);
            $containsBiologicalEn = in_array('Biological data', array_map('trim', $dataKindEn), true);

            // --- Affichage conditionnel des sections ---
            $showClinicalDetails = ($currentLang === 'fr' && $containsClinicalFr)
                || ($currentLang === 'en' && $containsClinicalEn);

            $showParaclinicalDetails = ($currentLang === 'fr' && $containsParaclinicalFr)
                || ($currentLang === 'en' && $containsParaclinicalEn);

            $showBiologicalDetails = ($currentLang === 'fr' && $containsBiologicalFr)
                || ($currentLang === 'en' && $containsBiologicalEn);
            ?>

            <div class="row">
                <?php if ($hasDataKind): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text" data-fr="Type de données" data-en="Data type">
                                Type de données
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_attr(buildTopicsHtml4($dataKindFr)); ?>"
                                data-en="<?= esc_attr(buildTopicsHtml4($dataKindEn)); ?>">
                                <?= $currentLang === 'fr'
                                    ? buildTopicsHtml4($dataKindFr)
                                    : buildTopicsHtml4($dataKindEn); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($showAutreDataKind && $hasDataTypeOther): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Autre type de données, précisions"
                                data-en="Other data type, details">
                                Autre type de données, précisions
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr($dataTypeOtherFr)); ?>"
                                data-en="<?= nl2br(esc_attr($dataTypeOtherEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $dataTypeOtherFr : $dataTypeOtherEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($hasClinicalDataDetails && $showClinicalDetails): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Données cliniques, précisions"
                                data-en="Clinical data, details">
                                Données cliniques, précisions
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr($clinicalDataDetailsFr)); ?>"
                                data-en="<?= nl2br(esc_attr($clinicalDataDetailsEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $clinicalDataDetailsFr : $clinicalDataDetailsEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($hasParaclinicalDataOther && $showParaclinicalDetails): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Autres données paracliniques (hors biologiques), précisions"
                                data-en="Other paraclinical data (non-biological), details">
                                Autres données paracliniques (hors biologiques), précisions
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr($paraclinicalDataOtherFr)); ?>"
                                data-en="<?= nl2br(esc_attr($paraclinicalDataOtherEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $paraclinicalDataOtherFr : $paraclinicalDataOtherEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($hasBiologicalDataDetails && $showBiologicalDetails): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Données biologiques, précisions"
                                data-en="Biological data, details">
                                Données biologiques, précisions
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr($biologicalDataDetailsFr)); ?>"
                                data-en="<?= nl2br(esc_attr($biologicalDataDetailsEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $biologicalDataDetailsFr : $biologicalDataDetailsEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>


                <?php
                $biobankContentFrArray = is_array($biobankContentFrStr)
                    ? $biobankContentFrStr
                    : array_map('trim', explode(',', $biobankContentFrStr));

                $biobankContentEnArray = is_array($biobankContentEnStr)
                    ? $biobankContentEnStr
                    : array_map('trim', explode(',', $biobankContentEnStr));
                ?>

                <?php if ($hasBiobankContent && $showBiologicalDetails): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Nature des échantillons"
                                data-en="Nature of samples">
                                Nature des échantillons
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_attr(buildTopicsHtml4($biobankContentFrArray)); ?>"
                                data-en="<?= esc_attr(buildTopicsHtml4($biobankContentEnArray)); ?>">
                                <?= $currentLang === 'fr'
                                    ? buildTopicsHtml4($biobankContentFrArray)
                                    : buildTopicsHtml4($biobankContentEnArray); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($hasBiobankContentOther): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Autre échantillon, précisions"
                                data-en="Other sample, details">
                                Autre échantillon, précisions
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr($biobankContentOtherFr)); ?>"
                                data-en="<?= nl2br(esc_attr($biobankContentOtherEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $biobankContentOtherFr : $biobankContentOtherEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($hasOtherLiquidsDetails): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Autres liquides ou sécrétions biologiques, précisions"
                                data-en="Other fluids or secretions, details">
                                Autres liquides ou sécrétions biologiques, précisions
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr($otherLiquidsDetailsFr)); ?>"
                                data-en="<?= nl2br(esc_attr($otherLiquidsDetailsEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $otherLiquidsDetailsFr : $otherLiquidsDetailsEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($hasIsDataIntegration): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Des données individuelles provenant d'autres sources sont-elles réutilisées dans le cadre de cette étude ?"
                                data-en="Are individual data from other sources reused in this study ?">
                                Des données individuelles provenant d'autres sources sont-elles réutilisées dans le
                                cadre de cette étude ?
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_attr($isDataIntegrationFr); ?>"
                                data-en="<?= esc_attr($isDataIntegrationEn); ?>">
                                <?= esc_html($currentLang === 'fr' ? $isDataIntegrationFr : $isDataIntegrationEn); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>


            </div>
        </div>
    <?php endif; ?>




    <?php

    $conformityDeclarationFr = ($jsonRec['fr']['additional']['regulatoryRequirements']['conformityDeclaration'] ?? '');
    $conformityDeclarationEn = ($jsonRec['en']['additional']['regulatoryRequirements']['conformityDeclaration'] ?? '');

    $sourcesFr = $jsonRec['fr']['study_desc']['method']['data_collection']['sources'] ?? [];
    $sourcesEn = $jsonRec['en']['study_desc']['method']['data_collection']['sources'] ?? [];

    $otherSourceTypeFr = $jsonRec['fr']['additional']['thirdPartySource']['otherSourceType'] ?? [];
    $otherSourceTypeEn = $jsonRec['en']['additional']['thirdPartySource']['otherSourceType'] ?? [];


    $conformityDeclarationFr = normalizeBooleanValue($conformityDeclarationEn, 'fr');
    $conformityDeclarationEn = normalizeBooleanValue($conformityDeclarationEn, 'en');

    function has_nonempty_data($data)
    {
        if (is_array($data)) {
            foreach ($data as $v) {
                if (has_nonempty_data($v)) return true;
            }
            return false;
        }
        return trim((string)$data) !== '';
    }

    $hasConformity = $conformityDeclarationFr !== '' || $conformityDeclarationEn !== '';
    $hasSources = has_nonempty_data($sourcesFr) || has_nonempty_data($sourcesEn);
    $hasDataIntegration = $hasConformity || $hasSources;

    ?>
    <?php if ($hasDataIntegration): ?>
        <div class="submenu-study" id="dataIntegration">
            <h3 class="lang-text"
                data-fr="Réutilisation de données existantes"
                data-en="Reuse of existing data">
                Réutilisation de données existantes
            </h3>


            <?php if ($hasSources): ?>
                <h4 class="field-content__h4 lang-text"
                    data-fr="Informations concernant les données collectées auprès des participants dans le cadre de l'étude"
                    data-en="Information concerning data collected from participants during the study">
                    Informations concernant les données collectées auprès des participants dans le cadre de l'étude
                </h4>

                <?php
                foreach ($sourcesFr as $i => $srcFr):
                    $srcEn = $sourcesEn[$i] ?? [];

                    $num = $i + 1;

                    // FR
                    $citationFr = trim((string)($srcFr['sourceCitation']['titlStmt']['titl'] ?? ''));
                    $holdingsFr = trim((string)($srcFr['sourceCitation']['holdings'] ?? ''));
                    $srcOrigArrFr = isset($srcFr['srcOrig']) && is_array($srcFr['srcOrig']) ? array_filter($srcFr['srcOrig']) : [];
                    $purposeFr = trim((string)($srcFr['sourceCitation']['notes']['value'] ?? ''));
                    $otherSourceTypeValFr = trim((string)($otherSourceTypeFr[$i] ?? ''));

                    // EN
                    $citationEn = trim((string)($srcEn['sourceCitation']['titlStmt']['titl'] ?? ''));
                    $holdingsEn = trim((string)($srcEn['sourceCitation']['holdings'] ?? ''));
                    $srcOrigArrEn = isset($srcEn['srcOrig']) && is_array($srcEn['srcOrig']) ? array_filter($srcEn['srcOrig']) : [];
                    $purposeEn = trim((string)($srcEn['sourceCitation']['notes']['value'] ?? ''));
                    $otherSourceTypeValEn = trim((string)($otherSourceTypeEn[$i] ?? ''));
                    // Autre / Other
                    $hasOtherFr = in_array('Autre', array_map('trim', $srcOrigArrFr), true);
                    $hasOtherEn = in_array('Other', array_map('trim', $srcOrigArrEn), true);

                    // Skip si vide dans les deux langues
                    if (
                        !$citationFr && !$citationEn &&
                        !$holdingsFr && !$holdingsEn &&
                        empty($srcOrigArrFr) && empty($srcOrigArrEn) &&
                        !$purposeFr && !$purposeEn &&
                        !$otherSourceTypeValFr && !$otherSourceTypeValEn
                    ) continue;
                ?>
                    <div class="field-card mb-3">
                        <div class="field-card__header lang-text"
                            data-fr="Source <?= $num; ?>"
                            data-en="Source <?= $num; ?>">
                            Source <?= $num; ?>
                        </div>

                        <div class="field-card__body">

                            <?php if ($citationFr || $citationEn): ?>
                                <div class="field-bloc mb-2">
                                    <div class="field-bloc__title lang-text"
                                        data-fr="Description de la source"
                                        data-en="Source description">
                                        Description de la source
                                    </div>
                                    <div class="field-bloc__value lang-text"
                                        data-fr="<?= esc_attr(nl2br(esc_html($citationFr))); ?>"
                                        data-en="<?= esc_attr(nl2br(esc_html($citationEn))); ?>">
                                        <?= $currentLang === 'fr'
                                            ? nl2br(esc_html($citationFr))
                                            : nl2br(esc_html($citationEn)); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($holdingsFr || $holdingsEn): ?>
                                <div class="field-bloc mb-2">
                                    <div class="field-bloc__title lang-text"
                                        data-fr="Identifiant de la source"
                                        data-en="Source identifier">
                                        Identifiant de la source
                                    </div>
                                    <div class="field-bloc__value lang-text"
                                        data-fr="<?= esc_attr(esc_html($holdingsFr)); ?>"
                                        data-en="<?= esc_attr(esc_html($holdingsEn)); ?>">
                                        <?= $currentLang === 'fr'
                                            ? esc_html($holdingsFr)
                                            : esc_html($holdingsEn); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($srcOrigArrFr) || !empty($srcOrigArrEn)): ?>
                                <div class="field-bloc mb-2">
                                    <div class="field-bloc__title lang-text"
                                        data-fr="Type de source"
                                        data-en="Source type">
                                        Type de source
                                    </div>
                                    <div class="field-bloc__value lang-text"
                                        data-fr="<?= esc_attr(buildTopicsHtml4($srcOrigArrFr)); ?>"
                                        data-en="<?= esc_attr(buildTopicsHtml4($srcOrigArrEn)); ?>">
                                        <?= $currentLang === 'fr'
                                            ? buildTopicsHtml4($srcOrigArrFr)
                                            : buildTopicsHtml4($srcOrigArrEn); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (
                                ($otherSourceTypeValFr && $hasOtherFr) ||
                                ($otherSourceTypeValEn && $hasOtherEn)
                            ): ?>
                                <div class="field-bloc mb-2">
                                    <div class="field-bloc__title lang-text"
                                        data-fr="Autre type de source, précisions"
                                        data-en="Other source type, details">
                                        Autre type de source, précisions
                                    </div>
                                    <div class="field-bloc__value lang-text"
                                        data-fr="<?= esc_attr(nl2br(esc_html($otherSourceTypeValFr))); ?>"
                                        data-en="<?= esc_attr(nl2br(esc_html($otherSourceTypeValEn))); ?>">
                                        <?= $currentLang === 'fr'
                                            ? nl2br(esc_html($otherSourceTypeValFr))
                                            : nl2br(esc_html($otherSourceTypeValEn)); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($purposeFr || $purposeEn): ?>
                                <div class="field-bloc mb-2">
                                    <div class="field-bloc__title lang-text"
                                        data-fr="Objectif de l'intégration de la source"
                                        data-en="Source integration purpose">
                                        Objectif de l'intégration de la source
                                    </div>
                                    <div class="field-bloc__value lang-text"
                                        data-fr="<?= esc_attr(esc_html($purposeFr)); ?>"
                                        data-en="<?= esc_attr(esc_html($purposeEn)); ?>">
                                        <?= $currentLang === 'fr'
                                            ? esc_html($purposeFr)
                                            : esc_html($purposeEn); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    <?php endif; ?>


</div>

<div class="submenu-study" id="dateAccess">
    <h2 class="field-content__h1 lang-text"
        data-fr="Accès aux données"
        data-en="Data access">
        Accès aux données

    </h2>

    <?php
    $standardsFr = $jsonRec['fr']['study_desc']['study_info']['quality_statement']['standards'] ?? [];
    $standardsEn = $jsonRec['en']['study_desc']['study_info']['quality_statement']['standards'] ?? [];

    $otherQualityFr = $jsonRec['fr']['study_desc']['study_info']['quality_statement']['other_quality_statement'] ?? '';
    $otherQualityEn = $jsonRec['en']['study_desc']['study_info']['quality_statement']['other_quality_statement'] ?? '';

    $parsePseudoArray = function ($value) {
        if (empty($value)) return [];
        if (is_array($value)) return array_values(array_filter(array_map('trim', $value), 'strlen'));

        $s = trim((string)$value);
        if (str_starts_with($s, '[') && str_ends_with($s, ']')) {
            $s = substr($s, 1, -1);
        }

        $parts = preg_split("/'\\s*,\\s*'/u", trim($s, "'\" "));
        $parts = array_map(function ($p) {
            $p = trim($p);
            $p = str_replace(['\\"', "\\'"], ['"', "'"], $p);
            return $p;
        }, $parts);

        return array_values(array_filter($parts, fn($v) => $v !== ''));
    };

    $standardsListFR = [];
    $standardsListEN = [];

    foreach ($standardsFr as $std) {
        $name = trim((string)($std['name'] ?? ''));
        if ($name !== '') {
            $parsed = $parsePseudoArray($name);
            $standardsListFR = array_merge($standardsListFR, $parsed ?: [$name]);
        }
    }

    foreach ($standardsEn as $std) {
        $name = trim((string)($std['name'] ?? ''));
        if ($name !== '') {
            $parsed = $parsePseudoArray($name);
            $standardsListEN = array_merge($standardsListEN, $parsed ?: [$name]);
        }
    }

    $otherQualityFrList = $parsePseudoArray($otherQualityFr);
    $otherQualityEnList = $parsePseudoArray($otherQualityEn);

    $hasStandards = !empty($standardsListFR) || !empty($standardsListEN);
    $hasOtherQuality = !empty($otherQualityFrList) || !empty($otherQualityEnList);
    $hasDataQuality = $hasStandards || $hasOtherQuality;
    ?>

    <?php if ($hasDataQuality): ?>
        <div class="submenu-study" id="dataQuality">
            <h3 class="lang-text"
                data-fr="Qualité des données"
                data-en="Data quality">
                <!-- <span class="contentSection">Qualité des données</span> -->
                <!-- <span class="info-bulle" attr-lng="fr"
                      data-text="Informations sur les procédures et outils mis en place pour assurer la qualité, la cohérence et la traçabilité des données collectées dans l’étude.">
                      <span class="dashicons dashicons-info"></span>
                    </span> -->
            </h3>

            <div class="row">
                <?php if ($hasStandards): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Standard(s) ou nomenclature(s) employé(s)"
                                data-en="Standard(s) or nomenclature(s) used">
                                Standard(s) ou nomenclature(s) employé(s)
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr(buildTopicsHtml4($standardsListFR))); ?>"
                                data-en="<?= nl2br(esc_attr(buildTopicsHtml4($standardsListEN))); ?>">
                                <?= $currentLang === 'fr'
                                    ? nl2br(buildTopicsHtml4($standardsListFR))
                                    : nl2br(buildTopicsHtml4($standardsListEN)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($hasOtherQuality): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Procédure qualité utilisée"
                                data-en="Quality procedure used">
                                Procédure qualité utilisée
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_attr(buildTopicsHtml4($otherQualityFrList))); ?>"
                                data-en="<?= nl2br(esc_attr(buildTopicsHtml4($otherQualityEnList))); ?>">
                                <?= $currentLang === 'fr'
                                    ? nl2br(buildTopicsHtml4($otherQualityFrList))
                                    : nl2br(buildTopicsHtml4($otherQualityEnList)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>






















    <?php

    $individualDataFr = trim((string)($jsonRec['fr']['study_desc']['data_access']['dataset_availability']['status'] ?? ''));

    $individualDataEn = trim((string)($jsonRec['en']['study_desc']['data_access']['dataset_availability']['status'] ?? ''));

    $aggregatedDataFr = trim((string)($jsonRec['fr']['study_desc']['method']['data_collection']['research_instrument'] ?? ''));
    $aggregatedDataEn = trim((string)($jsonRec['en']['study_desc']['method']['data_collection']['research_instrument'] ?? ''));

    $dataToolRequiredFr = trim((string)($jsonRec['fr']['study_desc']['data_access']['dataset_use']['spec_perm'][0]['required'] ?? ''));
    $dataToolRequiredEn = trim((string)($jsonRec['en']['study_desc']['data_access']['dataset_use']['spec_perm'][0]['required'] ?? ''));

    $dataToolLinkFr = trim((string)($jsonRec['fr']['study_desc']['data_access']['dataset_use']['spec_perm'][0]['txt'] ?? ''));
    $dataToolLinkEn = trim((string)($jsonRec['en']['study_desc']['data_access']['dataset_use']['spec_perm'][0]['txt'] ?? ''));

    $dataCompletenessFr = trim((string)($jsonRec['fr']['study_desc']['data_access']['dataset_availability']['complete'] ?? ''));
    $dataCompletenessEn = trim((string)($jsonRec['en']['study_desc']['data_access']['dataset_availability']['complete'] ?? ''));

    $dataLocationFr = trim((string)($jsonRec['fr']['study_desc']['data_access']['dataset_availability']['access_place'] ?? ''));
    $dataLocationEn = trim((string)($jsonRec['en']['study_desc']['data_access']['dataset_availability']['access_place'] ?? ''));

    $normalizeFr = fn($v) => in_array(strtolower($v), ['yes', 'oui']) ? 'Oui' : (in_array(strtolower($v), ['no', 'non']) ? 'Non' : $v);
    $normalizeEn = fn($v) => in_array(strtolower($v), ['yes', 'oui']) ? 'Yes' : (in_array(strtolower($v), ['no', 'non']) ? 'No' : $v);

    $dataToolRequiredFr = $normalizeFr($dataToolRequiredFr);
    $dataToolRequiredEn = $normalizeEn($dataToolRequiredEn);

    $hasIndividualData = $individualDataFr !== '' || $individualDataEn !== '';
    $hasAggregatedData = $aggregatedDataFr !== '' || $aggregatedDataEn !== '';
    $hasToolRequired = $dataToolRequiredFr !== '' || $dataToolRequiredEn !== '';
    $hasToolLink = $dataToolLinkFr !== '' || $dataToolLinkEn !== '';
    $hasCompleteness = $dataCompletenessFr !== '' || $dataCompletenessEn !== '';
    $hasLocation = $dataLocationFr !== '' || $dataLocationEn !== '';

    $hasDataAvailability =
        $hasIndividualData || $hasAggregatedData || $hasToolRequired ||
        $hasToolLink || $hasCompleteness || $hasLocation;
    ?>

    <?php if ($hasDataAvailability):
        $individualDataFrJson = json_decode($individualDataFr, true);
        $individualDataFrValue = $individualDataFrJson["value"] ?? '';
        $individualDataEnJson = json_decode($individualDataEn, true);
        $individualDataEnValue = $individualDataEnJson["value"] ?? '';
    ?>
        <div class="submenu-study" id="dataAvailability">
            <h3 class="lang-text"
                data-fr="Disponibilité du jeu de données"
                data-en="Data Availability">
                Disponibilité du jeu de données
            </h3>

            <div class="row">
                <?php if ($hasIndividualData): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Accès aux données individuelles"
                                data-en="Access to individual data">
                                Accès aux données individuelles
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_html($individualDataFrValue); ?>"
                                data-en="<?= esc_html($individualDataEnValue); ?>">
                                <?= esc_html($currentLang === 'fr' ? $individualDataFrValue : $individualDataEnValue); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($hasAggregatedData): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Modalités de mise à disposition des données agrégées"
                                data-en="Conditions under which aggregated data are made available">
                                Modalités de mise à disposition des données agrégées
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_html($aggregatedDataFr)); ?>"
                                data-en="<?= nl2br(esc_html($aggregatedDataEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $aggregatedDataFr : $aggregatedDataEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($hasToolRequired || $hasToolLink): ?>
                <div class="subsection" id="dataAccessTool">
                    <h4 class="lang-text-tooltip"
                        tooltip-fr="Cette section regroupe les informations relatives à l’existence éventuelle d’un outil d'accès aux données"
                        tooltip-en="This section contains information about the possible presence of a data access request tool"
                        data-fr="Outil de demande d'accès aux données"
                        data-en="Data access request tool">
                        <span class="contentSection lang-text" data-fr="Outil de demande d'accès aux données"
                            data-en="Data access request tool">Outil de demande d'accès aux données</span>
                        <!-- <span class="info-bulle" attr-lng="fr"
                              data-text="Cette section regroupe les informations relatives à l’existence éventuelle d’un outil d'accès aux données">
                              <span class="dashicons dashicons-info"></span>
                            </span> -->
                    </h4>

                    <div class="row">
                        <?php if ($hasToolRequired): ?>
                            <div class="col-md-12 mb-3">
                                <div class="field-bloc mb-2">
                                    <div class="field-bloc__title lang-text"
                                        data-fr="Existe-t-il un outil de demande d'accès aux données ?"
                                        data-en="A data access request tool is it available ?">
                                        Existe-t-il un outil de demande d'accès aux données ?
                                    </div>
                                    <div class="field-bloc__value lang-text"
                                        data-fr="<?= esc_html($dataToolRequiredFr); ?>"
                                        data-en="<?= esc_html($dataToolRequiredEn); ?>">
                                        <?= esc_html($currentLang === 'fr' ? $dataToolRequiredFr : $dataToolRequiredEn); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasToolLink): ?>
                            <div class="col-md-12 mb-3">
                                <div class="field-bloc mb-2">
                                    <div class="field-bloc__title lang-text"
                                        data-fr="Lien vers l'outil de demande d'accès"
                                        data-en="Link to the data access request tool">
                                        Lien vers l'outil de demande d'accès
                                    </div>
                                    <?php $link = $currentLang === 'fr' ? $dataToolLinkFr : $dataToolLinkEn; ?>
                                    <div class="field-bloc__value">
                                        <a href="<?= esc_url($link) ?>" target="_blank">
                                            <?= nl2br(esc_html($link)); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <?php if ($hasCompleteness): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Complétude des fichiers des données"
                                data-en="Data file completeness">
                                Complétude des fichiers des données
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_html($dataCompletenessFr)); ?>"
                                data-en="<?= nl2br(esc_html($dataCompletenessEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $dataCompletenessFr : $dataCompletenessEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($hasLocation): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Localisation des données"
                                data-en="Data location">
                                Localisation des données
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_html($dataLocationFr)); ?>"
                                data-en="<?= nl2br(esc_html($dataLocationEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $dataLocationFr : $dataLocationEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>


    <?php
    $conditionsFr = trim((string)($jsonRec['fr']['study_desc']['data_access']['dataset_use']['conditions'] ?? ''));
    $conditionsEn = trim((string)($jsonRec['en']['study_desc']['data_access']['dataset_use']['conditions'] ?? ''));

    $restrictionsFr = trim((string)($jsonRec['fr']['study_desc']['data_access']['dataset_use']['restrictions'] ?? ''));
    $restrictionsEn = trim((string)($jsonRec['en']['study_desc']['data_access']['dataset_use']['restrictions'] ?? ''));

    $sharingLinkFr = trim((string)($jsonRec['fr']['study_desc']['data_access']['notes'] ?? ''));
    $sharingLinkEn = trim((string)($jsonRec['en']['study_desc']['data_access']['notes'] ?? ''));

    $confDecFr = $jsonRec['fr']['study_desc']['data_access']['dataset_use']['conf_dec'] ?? [];
    $confDecEn = $jsonRec['en']['study_desc']['data_access']['dataset_use']['conf_dec'] ?? [];
    $confDecFrText = isset($confDecFr[0]['txt']) ? trim($confDecFr[0]['txt']) : '';
    $confDecEnText = isset($confDecEn[0]['txt']) ? trim($confDecEn[0]['txt']) : '';

    $contactsFr = $jsonRec['fr']['study_desc']['data_access']['dataset_use']['contact'] ?? [];
    $contactsEn = $jsonRec['en']['study_desc']['data_access']['dataset_use']['contact'] ?? [];

    $hasValidContactsFr = false;
    $hasValidContactsEn = false;
    foreach ($contactsFr as $c) {
        if (!empty(trim($c['name'] ?? '')) || !empty(trim($c['email'] ?? ''))) {
            $hasValidContactsFr = true;
            break;
        }
    }
    foreach ($contactsEn as $c) {
        if (!empty(trim($c['name'] ?? '')) || !empty(trim($c['email'] ?? ''))) {
            $hasValidContactsEn = true;
            break;
        }
    }

    $hasUseStatementFr =
        $conditionsFr !== '' ||
        $restrictionsFr !== '' ||
        $sharingLinkFr !== '' ||
        $confDecFrText !== '' ||
        $hasValidContactsFr;

    $hasUseStatementEn =
        $conditionsEn !== '' ||
        $restrictionsEn !== '' ||
        $sharingLinkEn !== '' ||
        $confDecEnText !== '' ||
        $hasValidContactsEn;
    ?>

    <?php if ($hasUseStatementFr || $hasUseStatementEn): ?>
        <div class="submenu-study" id="useStatement">
            <h3 class="lang-text"
                data-fr="Conditions d’usage"
                data-en="Use conditions">
                Conditions d’usage
            </h3>

            <div class="row">
                <?php if ($conditionsFr !== '' || $conditionsEn !== ''): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Conditions d'accès aux données"
                                data-en="Data access conditions">
                                Conditions d'accès aux données
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_html($conditionsFr)); ?>"
                                data-en="<?= nl2br(esc_html($conditionsEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $conditionsFr : $conditionsEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($restrictionsFr !== '' || $restrictionsEn !== ''): ?>
                    <div class="col-md-6 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Restrictions d'accès"
                                data-en="Access restrictions">
                                Restrictions d'accès
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_html($restrictionsFr)); ?>"
                                data-en="<?= nl2br(esc_html($restrictionsEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $restrictionsFr : $restrictionsEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($sharingLinkFr !== '' || $sharingLinkEn !== ''): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Lien vers le plan de partage des données ou vers une documentation relative à l'accès aux données"
                                data-en="Link to sharing plan or to documentation relating to data access">
                                Lien vers le plan de partage des données ou vers une documentation relative à
                                l'accès aux données
                            </div>
                            <?php $sharingLink = $currentLang === 'fr' ? $sharingLinkFr : $sharingLinkEn; ?>
                            <div class="field-bloc__value">
                                <a href="<?= nl2br(esc_html($sharingLink)); ?>">
                                    <?= nl2br(esc_html($sharingLink)); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($confDecFrText !== '' || $confDecEnText !== ''): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Accord de confidentialité"
                                data-en="Non-disclosure agreement">
                                Accord de confidentialité
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_html($confDecFrText)); ?>"
                                data-en="<?= nl2br(esc_html($confDecEnText)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $confDecFrText : $confDecEnText)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($hasValidContactsFr || $hasValidContactsEn): ?>
                <div class="field-bloc mt-4 submenu-study-table">

                    <div class="field-bloc__title lang-text"
                        data-fr="Personne à contacter pour des renseignements concernant les données"
                        data-en="Contact person for data information">
                        Personne à contacter pour des renseignements concernant les données
                    </div>

                    <div class="field-bloc__value">
                        <table class="field-table">
                            <thead>
                                <tr>
                                    <th class="lang-text"
                                        data-fr="Prénom NOM du contact"
                                        data-en="First name LAST NAME of the contact">
                                        First name LAST NAME of the contact
                                    </th>
                                    <th class="lang-text"
                                        data-fr="Email du contact"
                                        data-en="Contact email">
                                        Email du contact
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contactsFr as $index => $contactFr): ?>
                                    <?php
                                    $contactEn = $contactsEn[$index] ?? [];
                                    $nameFr = trim(str_replace(';', ' ', $contactFr['name']) ?? '');
                                    $emailFr = trim($contactFr['email'] ?? '');
                                    $nameEn = trim(str_replace(';', ' ', $contactEn['name']) ?? '');
                                    $emailEn = trim($contactEn['email'] ?? $emailFr);
                                    if ($nameFr === '' && $emailFr === '') continue;
                                    ?>
                                    <tr>
                                        <td class="lang-text"
                                            data-fr="<?= esc_attr($nameFr); ?>"
                                            data-en="<?= esc_attr($nameEn); ?>">
                                            <?= esc_html($nameFr); ?>
                                        </td>
                                        <td class="lang-text"
                                            data-fr="<?= esc_attr($emailFr); ?>"
                                            data-en="<?= esc_attr($emailEn); ?>">
                                            <?php if (filter_var($emailFr, FILTER_VALIDATE_EMAIL)): ?>
                                                <a href="mailto:<?= esc_attr($emailFr); ?>">
                                                    <?= esc_html($emailFr); ?>
                                                </a>
                                            <?php else: ?>
                                                <?= esc_html($emailFr); ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            <?php endif; ?>


        </div>
    <?php endif; ?>



    <?php
    $depositReqFr = trim((string)($jsonRec['fr']['study_desc']['data_access']['dataset_use']['deposit_req'] ?? ''));
    $depositReqEn = trim((string)($jsonRec['en']['study_desc']['data_access']['dataset_use']['deposit_req'] ?? ''));

    $citReqFr = trim((string)($jsonRec['fr']['study_desc']['data_access']['dataset_use']['cit_req'] ?? ''));
    $citReqEn = trim((string)($jsonRec['en']['study_desc']['data_access']['dataset_use']['cit_req'] ?? ''));

    $hasDataCitation = $depositReqFr !== '' || $depositReqEn !== '' || $citReqFr !== '' || $citReqEn !== '';
    ?>

    <?php if ($hasDataCitation): ?>
        <div class="submenu-study" id="dataCitation">
            <h3 class="lang-text-tooltip"
                tooltip-fr="Indique les responsabilités de l’utilisateur vis-à-vis des données"
                tooltip-en="Indicates the user's responsibilities with regard to data"
                data-fr="Obligations liées à l’usage des données"
                data-en="Obligations related to data use">
                <span class="contentSection lang-text" data-fr="Obligations liées à l’usage des données"
                    data-en="Obligations related to data use">Obligations liées à l’usage des données</span>
                <!-- <span class="info-bulle" attr-lng="fr"
                      data-text="Indique les responsabilités de l’utilisateur vis-à-vis des données">
                      <span class="dashicons dashicons-info"></span>
                    </span> -->
            </h3>

            <div class="row">
                <?php if ($depositReqFr !== '' || $depositReqEn !== ''): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Obligation de transmission des travaux"
                                data-en="Reporting requirement">
                                Obligation de transmission des travaux
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_html($depositReqFr)); ?>"
                                data-en="<?= nl2br(esc_html($depositReqEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $depositReqFr : $depositReqEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($citReqFr !== '' || $citReqEn !== ''): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Obligation de citation"
                                data-en="Citation requirement">
                                Obligation de citation
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_html($citReqFr)); ?>"
                                data-en="<?= nl2br(esc_html($citReqEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $citReqFr : $citReqEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>


    <?php

    $varDictFr = $jsonRec['fr']['additional']['variableDictionnary'] ?? [];
    $varDictEn = $jsonRec['en']['additional']['variableDictionnary'] ?? [];

    $variableAvailableFr = $varDictFr['variableDictionnaryAvailable'] ?? '';
    $variableAvailableEn = $varDictEn['variableDictionnaryAvailable'] ?? '';
    $variableLinkFr = $varDictFr['variableDictionnaryLink'] ?? '';
    $variableLinkEn = $varDictEn['variableDictionnaryLink'] ?? '';


    $variableAvailableFr = normalizeBooleanValue($variableAvailableFr, 'fr');
    $variableAvailableEn = normalizeBooleanValue($variableAvailableEn, 'en');

    $hasVariableDictionary = $variableAvailableFr !== '' || $variableAvailableEn !== '' || $variableLinkFr !== '' || $variableLinkEn !== '';
    ?>

    <?php if ($hasVariableDictionary): ?>
        <div class="submenu-study" id="variableDictionary">
            <h3 class="lang-text-tooltip"
                tooltip-fr="Document listant et décrivant toutes les variables collectées, leur format, unités et modalités de codage."
                tooltip-en="Document listing and describing all collected variables, their format, units, and coding modalities."
                data-fr="Dictionnaire des variables"
                data-en="Data dictionary">
                <span class="contentSection lang-text" data-fr="Dictionnaire des variables"
                    data-en="Data dictionary">Dictionnaire des variables</span>
                <!-- <span class="info-bulle" attr-lng="fr"
                      data-text="Document listant et décrivant toutes les variables collectées, leur format, unités et modalités de codage.">
                      <span class="dashicons dashicons-info"></span>
                    </span> -->
            </h3>

            <div class="row">
                <?php if ($variableAvailableFr !== '' || $variableAvailableEn !== ''): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Existe-t-il un dictionnaire des variables ?"
                                data-en="A data dictionary is it available ?">
                                Existe-t-il un dictionnaire des variables ?
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_html($variableAvailableFr); ?>"
                                data-en="<?= esc_html($variableAvailableEn); ?>">
                                <?= esc_html($currentLang === 'fr' ? $variableAvailableFr : $variableAvailableEn); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($variableLinkFr !== '' || $variableLinkEn !== ''): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Lien vers le dictionnaire des variables"
                                data-en="Link to the data dictionary">
                                Lien vers le dictionnaire des variables
                            </div>
                            <?php $variableLink = $currentLang === 'fr' ? $variableLinkFr : $variableLinkEn; ?>
                            <div class="field-bloc__value">
                                <a href="<?= esc_url($variableLink); ?>"
                                    target="_blank" rel="noopener">
                                    <?= nl2br(esc_html($variableLink)); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>




    <?php

    $mockSampleFr = $jsonRec['fr']['additional']['mockSample'] ?? [];
    $mockSampleEn = $jsonRec['en']['additional']['mockSample'] ?? [];

    $mockSampleAvailableFr = $mockSampleFr['mockSampleAvailable'] ?? '';
    $mockSampleAvailableEn = $mockSampleEn['mockSampleAvailable'] ?? '';
    $mockSampleLocationFr = trim((string)($mockSampleFr['mockSampleLocation'] ?? ''));
    $mockSampleLocationEn = trim((string)($mockSampleEn['mockSampleLocation'] ?? ''));

    $otherDocFr = trim((string)($jsonRec['fr']['additional']['dataCollection']['otherDocumentation'] ?? ''));
    $otherDocEn = trim((string)($jsonRec['en']['additional']['dataCollection']['otherDocumentation'] ?? ''));

    $mockSampleAvailableFr = normalizeBooleanValue($mockSampleAvailableFr, 'fr');
    $mockSampleAvailableEn =  normalizeBooleanValue($mockSampleAvailableEn, 'en');

    $hasMockSampleSection =
        $mockSampleAvailableFr !== '' ||
        $mockSampleAvailableEn !== '' ||
        $mockSampleLocationFr !== '' ||
        $mockSampleLocationEn !== '' ||
        $otherDocFr !== '' ||
        $otherDocEn !== '';
    ?>

    <?php if ($hasMockSampleSection): ?>
        <div class="submenu-study" id="mockSample">
            <h3 class="lang-text"
                data-fr="Echantillon fictif"
                data-en="Mock sample">
            </h3>

            <div class="row">
                <?php if ($mockSampleAvailableFr !== '' || $mockSampleAvailableEn !== ''): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Un échantillon fictif du jeu de données est-il disponible ?"
                                data-en="A mock sample is it available ?">
                                Un échantillon fictif du jeu de données est-il disponible ?
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_html($mockSampleAvailableFr); ?>"
                                data-en="<?= esc_html($mockSampleAvailableEn); ?>">
                                <?= esc_html($currentLang === 'fr' ? $mockSampleAvailableFr : $mockSampleAvailableEn); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($mockSampleLocationFr !== '' || $mockSampleLocationEn !== ''): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Lien vers l'échantillon fictif ou vers les renseignements pour y accéder"
                                data-en="Link to the sample of to information on how to access it">
                                Lien vers l'échantillon fictif ou vers les renseignements pour y accéder
                            </div>
                            <?php $linkValue = $currentLang === 'fr' ? $mockSampleLocationFr : $mockSampleLocationEn; ?>
                            <div class="field-bloc__value">
                                <a href="<?= esc_url($linkValue)?>" target="_blank">
                                    <?= nl2br(esc_html($linkValue)); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($otherDocFr !== '' || $otherDocEn !== ''): ?>
                    <div class="col-md-12 mb-3">
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Documentation complémentaire sur les données"
                                data-en="Additional documentation on data">
                                Documentation complémentaire sur les données
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_html($otherDocFr)); ?>"
                                data-en="<?= nl2br(esc_html($otherDocEn)); ?>">
                                <?= nl2br(esc_html($currentLang === 'fr' ? $otherDocFr : $otherDocEn)); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>


            </div>
        </div>
    <?php endif; ?>


    <?php
    $datasetIdsFr = $jsonRec['fr']['additional']['fileDscr']['fileTxt']['fileCitation']['titlStmt']['IDno'] ?? [];
    $datasetIdsEn = $jsonRec['en']['additional']['fileDscr']['fileTxt']['fileCitation']['titlStmt']['IDno'] ?? [];

    if (!is_array($datasetIdsFr)) $datasetIdsFr = [];
    if (!is_array($datasetIdsEn)) $datasetIdsEn = [];

    $datasetIdsFr = array_values(array_filter($datasetIdsFr, function ($item) {
        return !empty(trim($item['type'] ?? '')) || !empty(trim($item['uri'] ?? ''));
    }));

    $hasDatasetIds = count($datasetIdsFr) > 0;
    ?>

    <?php if ($hasDatasetIds): ?>

        <div class="submenu-study-table" id="datasetPID">
            <h3 class="lang-text"
                data-fr="Identification pérenne du jeu de données"
                data-en="Persistent identifier of the dataset">
                Identification pérenne du jeu de données
            </h3>

            <table class="field-table">
                <thead>
                    <tr>
                        <th class="lang-text"
                            data-fr="Type d'identifiant du jeu de données"
                            data-en="Dataset identifier type">
                            Type d'identifiant du jeu de données
                        </th>
                        <th class="lang-text"
                            data-fr="Identifiant du jeu de données"
                            data-en="Dataset identifier">
                            Identifiant du jeu de données
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datasetIdsFr as $index => $idInfoFr): ?>
                        <?php
                        $idInfoEn = $datasetIdsEn[$index] ?? [];
                        $typeFr = trim($idInfoFr['type'] ?? '');
                        $idFr = trim($idInfoFr['uri'] ?? '');
                        $typeEn = trim($idInfoEn['type'] ?? $typeFr);
                        $idEn = trim($idInfoEn['uri'] ?? $idFr);

                        if ($typeFr === '' && $idFr === '') continue;
                        ?>
                        <tr>
                            <td class="lang-text"
                                data-fr="<?= esc_html($typeFr); ?>"
                                data-en="<?= esc_html($typeEn); ?>">
                                <?= esc_html($typeFr); ?>
                            </td>
                            <td class="lang-text"
                                data-fr="<?= esc_html($idFr); ?>"
                                data-en="<?= esc_html($idEn); ?>">
                                <?php if (filter_var($idFr, FILTER_VALIDATE_URL)): ?>
                                    <a href="<?= esc_url($idFr); ?>" target="_blank"><?= esc_html($idFr); ?></a>
                                <?php else: ?>
                                    <?= esc_html($idFr); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<?php
// ================================================
// SECTION: Documents connexes
// ================================================

// Get FR & EN arrays
$relatedDocsFr = $jsonRec['fr']['additional']['relatedDocument'] ?? [];
$relatedDocsEn = $jsonRec['en']['additional']['relatedDocument'] ?? [];

// Normalize arrays
if (!is_array($relatedDocsFr)) $relatedDocsFr = [];
if (!is_array($relatedDocsEn)) $relatedDocsEn = [];

// Extract only numeric keys (0,1,2,…)
$relatedDocsFr = array_filter($relatedDocsFr, 'is_array');
$relatedDocsEn = array_filter($relatedDocsEn, 'is_array');

// Reindex arrays
$relatedDocsFr = array_values($relatedDocsFr);
$relatedDocsEn = array_values($relatedDocsEn);

$hasData = !empty($relatedDocsFr) || !empty($relatedDocsEn);
?>

<?php if ($hasData): ?>
    <div class="submenu-study" id="RelatedDocument">
        <h2 class="field-content__h1 lang-text"
            data-fr="Documents connexes"
            data-en="Related Documents">
            Documents connexes
        </h2>

        <?php foreach ($relatedDocsFr as $index => $docFr): ?>
            <?php
            $docEn = $relatedDocsEn[$index] ?? [];

            $typeFr = trim((string)($docFr['type'] ?? ''));
            $typeEn = trim((string)($docEn['type'] ?? ''));

            $titleFr = trim((string)($docFr['title'] ?? ''));
            $titleEn = trim((string)($docEn['title'] ?? ''));

            $linkFr = trim((string)($docFr['link'] ?? ''));
            $linkEn = trim((string)($docEn['link'] ?? ''));

            if ($typeFr === '' && $titleFr === '' && $linkFr === '' && $typeEn === '' && $titleEn === '' && $linkEn === '') continue;
            ?>

            <div class="field-card" style="margin-bottom: 12px;">
                <div class="field-card__header lang-text"
                    data-fr="Document connexe <?= $index + 1; ?>"
                    data-en="Related document <?= $index + 1; ?>">
                    Document connexe <?= $index + 1; ?>
                </div>

                <div class="field-card__body">
                    <?php if ($typeFr): ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Type de document"
                                data-en="Document type">Type de document
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_html($typeFr); ?>"
                                data-en="<?= esc_html($typeEn); ?>">
                                <?= esc_html($typeFr); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($titleFr): ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Titre du document"
                                data-en="Document title">Titre du document
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= nl2br(esc_html($titleFr)); ?>"
                                data-en="<?= nl2br(esc_html($titleEn)); ?>">
                                <?= nl2br(esc_html($titleFr)); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($linkFr): ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                data-fr="Lien vers le document"
                                data-en="Document link">Lien vers le document
                            </div>
                            <div class="field-bloc__value lang-text"
                                data-fr="<?= esc_html($linkFr); ?>"
                                data-en="<?= esc_html($linkEn); ?>">
                                <?php
                                $linkValue = $currentLang === 'fr' ? $linkFr : $linkEn; ?>
                                <a href="<?= esc_url($linkValue); ?>" target="_blank"
                                    rel="noopener"><?= nl2br(esc_html($linkValue)); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
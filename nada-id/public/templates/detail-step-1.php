<?php $langs = ['fr', 'en']; ?>

<?php
$titleFr = $jsonRec["fr"]['study_desc']['title_statement']['title'] ?? '';
$titleEn = $jsonRec["en"]['study_desc']['title_statement']['title'] ?? '';
$acronymeFr = $jsonRec["fr"]['study_desc']['title_statement']['alternate_title'] ?? '';
$acronymeEn = $jsonRec["en"]['study_desc']['title_statement']['alternate_title'] ?? '';
$studyClassFr = $jsonRec["fr"]["study_desc"]["method"]["study_class"] ?? '';
$studyClassEn = $jsonRec["en"]["study_desc"]["method"]["study_class"] ?? '';
$purposeFr = $jsonRec["fr"]["study_desc"]["study_info"]["purpose"] ?? '';
$purposeEn = $jsonRec["en"]["study_desc"]["study_info"]["purpose"] ?? '';
$abstractFr = $jsonRec["fr"]["study_desc"]["study_info"]["abstract"] ?? '';
$abstractEn = $jsonRec["en"]["study_desc"]["study_info"]["abstract"] ?? '';
$KeywordsFr = $jsonRec["fr"]["study_desc"]["study_info"]["keywords"] ?? [];
$KeywordsEn = $jsonRec["en"]["study_desc"]["study_info"]["keywords"] ?? [];
$KeywordsFrList = array_column($KeywordsFr, 'keyword');
$KeywordsEnList = array_column($KeywordsEn, 'keyword');
$KeywordsFrText = implode('; ', $KeywordsFrList);
$KeywordsEnText = implode('; ', $KeywordsEnList);

$hasStudyInfo =
  $titleFr || $titleEn ||
  $acronymeFr || $acronymeEn ||
  $studyClassFr || $studyClassEn ||
  $purposeFr || $purposeEn ||
  $abstractFr || $abstractEn ||
  $KeywordsFrText || $KeywordsEnText;
?>

<?php if ($hasStudyInfo): ?>
  <div class="submenu-study" id="studyInformations">
    <div class="field-content__h1 lang-text" data-fr="Présentation de l'étude" data-en="Study overview">
      Présentation de l'étude
    </div>
    <?php
    // Get title values
    $titleFr = $jsonRec["fr"]['study_desc']['title_statement']['title'] ?? '';
    $titleEn = $jsonRec["en"]['study_desc']['title_statement']['title'] ?? '';
    if ($titleFr || $titleEn): ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text" data-fr="Titre de l'étude" data-en="Study title">Titre
          de l'étude
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo nl2br(esc_html($titleFr)); ?>"
          data-en="<?php echo nl2br(esc_html($titleEn)); ?>">
          <?php echo nl2br(esc_html($currentLang === 'fr' ? $titleFr : $titleEn)); ?>
        </div>
      </div>
    <?php endif; ?>
    <?php
    // Get acronym values
    $acronymeFr = $jsonRec["fr"]['study_desc']['title_statement']['alternate_title'] ?? '';
    $acronymeEn = $jsonRec["en"]['study_desc']['title_statement']['alternate_title'] ?? '';
    if ($acronymeFr || $acronymeEn): ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text" data-fr="Acronyme de l'étude" data-en="Study acronym">
          Acronyme de l'étude
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo nl2br(esc_html($acronymeFr)); ?>"
          data-en="<?php echo nl2br(esc_html($acronymeEn)); ?>">
          <?php echo nl2br(esc_html($currentLang === 'fr' ? $acronymeFr : $acronymeEn)); ?>
        </div>
      </div>
    <?php endif; ?>
    <?php
    // Get studyClass values
    $studyClassFr = $jsonRec["fr"]["study_desc"]["method"]["study_class"] ?? '';
    $studyClassEn = $jsonRec["en"]["study_desc"]["method"]["study_class"] ?? '';
    if ($studyClassFr || $studyClassEn): ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text" data-fr="Statut actuel de l'étude" data-en="Current study status">
          Statut actuel de l'étude
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo esc_html($studyClassFr); ?>"
          data-en="<?php echo esc_html($studyClassEn); ?>">
          <?php echo esc_html($currentLang === 'fr' ? $studyClassFr : $studyClassEn); ?>
        </div>
      </div>
    <?php endif; ?>
    <?php
    // Get studyClass values
    $purposeFr = $jsonRec["fr"]["study_desc"]["study_info"]["purpose"] ?? '';
    $purposeEn = $jsonRec["en"]["study_desc"]["study_info"]["purpose"] ?? '';
    if ($purposeFr || $purposeEn):  ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text" data-fr="Objectifs de l'étude"
          data-en="Study objectives">Objectifs de l'étude
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo nl2br(esc_html($purposeFr)); ?>"
          data-en="<?php echo nl2br(esc_html($purposeEn)); ?>">
          <?php echo nl2br(esc_html($currentLang === 'fr' ? $purposeFr : $purposeEn)); ?>
        </div>
      </div>
    <?php endif; ?>
    <?php
    // Get abstract values
    $abstractFr = $jsonRec["fr"]["study_desc"]["study_info"]["abstract"] ?? '';
    $abstractEn = $jsonRec["en"]["study_desc"]["study_info"]["abstract"] ?? '';
    if ($abstractFr || $abstractEn): ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text" data-fr="Résumé de l'étude" data-en="Study summary">
          Résumé de l'étude
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo nl2br(esc_html($abstractFr)); ?>"
          data-en="<?php echo nl2br(esc_html($abstractEn)); ?>">
          <?php echo nl2br(esc_html($currentLang === 'fr' ? $abstractFr : $abstractEn)); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php
    $KeywordsFr = $jsonRec["fr"]["study_desc"]["study_info"]["keywords"] ?? [];
    $KeywordsEn = $jsonRec["en"]["study_desc"]["study_info"]["keywords"] ?? [];

    $KeywordsFrList = array_column($KeywordsFr, 'keyword');
    $KeywordsEnList = array_column($KeywordsEn, 'keyword');

    $KeywordsFrHtml = '';
    foreach ($KeywordsFrList as $word) {
      $word = trim($word);
      if ($word !== '') {
        $KeywordsFrHtml .= "<span class='keyword-item'>" . htmlspecialchars($word) . "</span> ";
      }
    }

    $KeywordsEnHtml = '';
    foreach ($KeywordsEnList as $word) {
      $word = trim($word);
      if ($word !== '') {
        $KeywordsEnHtml .= "<span class='keyword-item'>" . htmlspecialchars($word) . "</span> ";
      }
    }

    if (!empty($KeywordsFrHtml) || !empty($KeywordsEnHtml)): ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text" data-fr="Mots-clés libres" data-en="Free keywords">Mots-clés libres
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo esc_attr($KeywordsFrHtml); ?>"
          data-en="<?php echo esc_attr($KeywordsEnHtml); ?>">
          <?php echo $currentLang === 'fr' ? $KeywordsFrHtml : $KeywordsEnHtml; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php
$isHealthThemeFr = $jsonRec['fr']['additional']['isHealthTheme'] ?? '';
$isHealthThemeEn = $jsonRec['en']['additional']['isHealthTheme'] ?? '';

$topicsFr = $jsonRec['fr']['study_desc']['study_info']['topics'] ?? [];
$topicsEn = $jsonRec['en']['study_desc']['study_info']['topics'] ?? [];

$rareDiseasesFr = $jsonRec["fr"]["additional"]["theme"]["RareDiseases"] ?? '';
$rareDiseasesEn = $jsonRec["en"]["additional"]["theme"]["RareDiseases"] ?? '';

$hasThematique = false;

// Check if isHealthTheme has data
if ($isHealthThemeFr || $isHealthThemeEn) {
  $hasThematique = true;
}

// Check if topics contain any data
foreach (array_merge($topicsFr, $topicsEn) as $topic) {
  if (!empty($topic['topic'])) {
    $hasThematique = true;
    break;
  }
}

// Check if RareDiseases has data
if ($rareDiseasesFr || $rareDiseasesEn) {
  $hasThematique = true;
}
?>

<?php if ($hasThematique): ?>

  <div class="submenu-study" id="thematique">
    <div class="field-content__h1 lang-text" data-fr="Thématique" data-en="Theme">
      Thématique
    </div>


    <?php
    // Configuration: Define vocabulary types and their labels
    $vocabConfig = [
      'health theme' => [
        'type' => 'topics',
        'fr' => 'Spécialité(s) médicale(s) concernée(s)',
        'en' => 'Medical speciality/specialities represented'
      ],
      'other health theme' => [
        'type' => 'topics',
        'fr' => 'Autre spécialité médicale, précisions',
        'en' => 'Other medical specialty, details'
      ],
      'cim-11' => [
        'type' => 'topics',
        'fr' => 'Pathologie(s), affection(s) ou diagnostic(s) ciblé(s) par l’étude',
        'en' => 'Pathology(ies), condition(s) or diagnose(s) targeted by the study'
      ],
      'health determinant' => [
        'type' => 'topics',
        'fr' => 'Déterminants de santé',
        'en' => 'Health determinants'
      ],
      'other socio demographic determinant' => [
        'type' => 'string',
        'fr' => 'Autres déterminants socio-démographiques et économiques, précisions',
        'en' => 'Other socio-demographic and economic determinants, details'
      ],
      'other environmental determinant' => [
        'type' => 'string',
        'fr' => 'Autres déterminants environnementaux, précisions',
        'en' => 'Other environmental determinants, details'
      ],
      'other healthcare determinant' => [
        'type' => 'string',
        'fr' => 'Autres déterminants liés au système de santé, précisions',
        'en' => 'Other healthcare system determinants, details'
      ],
      'other behavioural determinant' => [
        'type' => 'string',
        'fr' => 'Autres déterminants comportementaux, précisions',
        'en' => 'Other behavioral determinants, details'
      ],
      'other biological determinant' => [
        'type' => 'string',
        'fr' => 'Autres déterminants biologiques, précisions',
        'en' => 'Other biological determinants, details'
      ],
      'other determinant' => [
        'type' => 'string',
        'fr' => 'Autres déterminants, précisions',
        'en' => 'Other determinants, details'
      ]
    ];

    $topicsFr = $jsonRec['fr']['study_desc']['study_info']['topics'] ?? [];
    $topicsEn = $jsonRec['en']['study_desc']['study_info']['topics'] ?? [];


    if (!function_exists('buildTopicsHtml5')) {
      function buildTopicsHtml5($topics)
      {
        $html = '';
        foreach ($topics as $t) {

          $uri = '';
          // Cas 1 : extLink['uri']
          if (!empty($t['extLink']['uri'])) {
            $uri = trim($t['extLink']['uri']);
          }
          // Cas 2 : extLink[0]['uri']
          elseif (!empty($t['extLink'][0]['uri'])) {
            $uri = trim($t['extLink'][0]['uri']);
          }

          // Valeur du topic
          $value = trim($t['topic'] ?? '');

          if ($uri && $value) {
            // Les deux existent → lien
            $label = '<a href="' . htmlspecialchars($uri, ENT_QUOTES, 'UTF-8') . '" target="_blank">'
              . htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
              . '</a>';
          } elseif ($value) {
            // Seulement value
            $label = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
          } elseif ($uri) {
            // Seulement topic
            $label = htmlspecialchars($uri, ENT_QUOTES, 'UTF-8');
          } else {
            continue;
          }

          $html .= '<span class="checkbox-item">' . $label . '</span> ';
        }
        return trim($html);
      }
    }


    if (!function_exists('buildTopicsHtml')) {
      function buildTopicsHtml($topics)
      {
        $html = '';
        foreach ($topics as $t) {
          $topic = trim($t['topic'] ?? '');
          $value = trim($t['value'] ?? '');

          if ($topic && $value) {
            // Les deux existent → lien
            $label = '<a href="' . htmlspecialchars($topic) . '" target="_blank">' . htmlspecialchars($value) . '</a>';
          } elseif ($value) {
            // Seulement value
            $label = htmlspecialchars($value);
          } elseif ($topic) {
            // Seulement topic
            $label = htmlspecialchars($topic);
          } else {
            continue;
          }

          $html .= '<span class="checkbox-item">' . $label . '</span> ';
        }
        return trim($html);
      }
    }

    // Extract all data
    $extractedData = [];

    foreach ($vocabConfig as $vocabType => $config) {
      if ($config['type'] === 'topics') {
        // Multiple topics - filter and implode
        $filteredFr = array_filter($topicsFr, fn($t) => ($t['vocab'] ?? '') === $vocabType);
        $filteredEn = array_filter($topicsEn, fn($t) => ($t['vocab'] ?? '') === $vocabType);

        // $frValue = implode(', ', array_column($filteredFr, 'topic'));
        // $enValue = implode(', ', array_column($filteredEn, 'topic'));


        if ($vocabType == 'cim-11') {
          $frValue = buildTopicsHtml5($filteredFr);
          $enValue = buildTopicsHtml5($filteredEn);
        } else {
          $frValue = buildTopicsHtml($filteredFr);
          $enValue = buildTopicsHtml($filteredEn);
        }
      } else {
        // Single string value - find first occurrence
        $frValue = '';
        $enValue = '';

        foreach ($topicsFr as $topic) {
          if (($topic['vocab'] ?? '') === $vocabType) {
            $frValue = $topic['topic'] ?? '';
            break;
          }
        }

        foreach ($topicsEn as $topic) {
          if (($topic['vocab'] ?? '') === $vocabType) {
            $enValue = $topic['topic'] ?? '';
            break;
          }
        }
      }

      $extractedData[$vocabType] = [
        'fr' => $frValue,
        'en' => $enValue,
        'labelFr' => $config['fr'],
        'labelEn' => $config['en']
      ];
    }

    // Render all field blocks
    foreach ($extractedData as $data) {
      if (empty($data['fr']) && empty($data['en'])) continue;
    ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text"
          data-fr="<?php echo esc_attr($data['labelFr']); ?>"
          data-en="<?php echo esc_attr($data['labelEn']); ?>">
          <?php echo esc_html($currentLang === 'fr' ? $data['labelFr'] : $data['labelEn']); ?>
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo nl2br(esc_attr($data['fr'])); ?>"
          data-en="<?php echo nl2br(esc_attr($data['en'])); ?>">
          <?php
          if (str_contains($data['fr'], '<span') || str_contains($data['en'], '<span')) {
            echo $currentLang === 'fr' ? $data['fr'] : $data['en'];
          } else {
            echo esc_html($currentLang === 'fr' ? $data['fr'] : $data['en']);
          }
          ?>
        </div>
      </div>
    <?php
    }
    ?>

    <?php
    // Get rareDiseases values
    $rareDiseasesFr = normalizeBooleanValue($jsonRec["fr"]["additional"]["theme"]["RareDiseases"] ?? '', 'fr');   
    $rareDiseasesEn =  normalizeBooleanValue($jsonRec["en"]["additional"]["theme"]["RareDiseases"] ?? '', 'en');
    if ($rareDiseasesFr || $rareDiseasesEn):  ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text" data-fr="L'étude porte-t-elle sur une ou plusieurs maladie(s) rare(s) ?"
          data-en="Does the study cover one or more rare disease(s)?">
          L'étude porte-t-elle sur une ou plusieures maladies rares ?
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo esc_html($rareDiseasesFr); ?>"
          data-en="<?php echo esc_html($rareDiseasesEn); ?>">
          <?php echo esc_html($currentLang === 'fr' ? $rareDiseasesFr : $rareDiseasesEn); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>


<?php
$universeFr = $jsonRec["fr"]['additional']['universe'] ?? [];
$universeEn = $jsonRec["en"]['additional']['universe'] ?? [];

$frSex = $universeFr['sexe'] ?? [];
$enSex = $universeEn['sexe'] ?? [];

$frAge = $universeFr['level_age_clusion_I'] ?? [];
$enAge = $universeEn['level_age_clusion_I'] ?? [];

$frType = $universeFr['level_type_clusion_I'] ?? [];
$enType = $universeEn['level_type_clusion_I'] ?? [];

$frOtherType = $universeFr['type_inclusion_autre'] ?? '';
$enOtherType = $universeEn['type_inclusion_autre'] ?? '';

$clusionIFr = $universeFr['clusion_I'] ?? '';
$clusionIEn = $universeEn['clusion_I'] ?? '';

$clusionEFr = $universeFr['clusion_E'] ?? '';
$clusionEEn = $universeEn['clusion_E'] ?? '';

$nationFr = $jsonRec["fr"]['study_desc']['study_info']['nation'] ?? [];
$nationEn = $jsonRec["en"]['study_desc']['study_info']['nation'] ?? [];

$geoCoverageFr = $jsonRec["fr"]['study_desc']['study_info']['geog_coverage'] ?? [];
$geoCoverageEn = $jsonRec["en"]['study_desc']['study_info']['geog_coverage'] ?? [];

$geoDetailFr = $jsonRec["fr"]['additional']['geographicalCoverage']['geoDetail'] ?? '';
$geoDetailEn = $jsonRec["en"]['additional']['geographicalCoverage']['geoDetail'] ?? '';

// Normalize arrays to strings
if (is_array($frSex)) $frSex = implode(', ', $frSex);
if (is_array($enSex)) $enSex = implode(', ', $enSex);
if (is_array($frAge)) $frAge = implode(', ', $frAge);
if (is_array($enAge)) $enAge = implode(', ', $enAge);
if (is_array($frType)) $frType = implode(', ', $frType);
if (is_array($enType)) $enType = implode(', ', $enType);

if (!function_exists('parseStringArrayNormalized')) {
  function parseStringArrayNormalized($str)
  {
    if (is_array($str)) return $str;
    if (empty($str)) return [];
    $clean = trim($str, "[] \t\n\r\0\x0B'\"");
    $parts = preg_split('/\s*,\s*/', $clean);
    $parts = array_map(fn($p) => trim($p, "'\" "), $parts);
    return array_values(array_filter($parts, fn($v) => $v !== ''));
  }
}

if (is_array($nationFr) && isset($nationFr[0]['name'])) {
  $nationFr = implode(', ', array_filter(array_map(fn($n) => $n['name'] ?? '', $nationFr)));
} elseif (!empty($nationFr)) {
  $nationFr = implode(', ', parseStringArrayNormalized($nationFr));
} else {
  $nationFr = '';
}

if (is_array($nationEn) && isset($nationEn[0]['name'])) {
  $nationEn = implode(', ', array_filter(array_map(fn($n) => $n['name'] ?? '', $nationEn)));
} elseif (!empty($nationEn)) {
  $nationEn = implode(', ', parseStringArrayNormalized($nationEn));
} else {
  $nationEn = '';
}

$geoCoverageFr = implode(', ', parseStringArrayNormalized($geoCoverageFr));
$geoCoverageEn = implode(', ', parseStringArrayNormalized($geoCoverageEn));

// Determine if any population-related data exists
$hasPopulation =
  $frSex || $enSex ||
  $frAge || $enAge ||
  $frType || $enType ||
  $frOtherType || $enOtherType ||
  $clusionIFr || $clusionIEn ||
  $clusionEFr || $clusionEEn ||
  $nationFr || $nationEn ||
  $geoCoverageFr || $geoCoverageEn ||
  $geoDetailFr || $geoDetailEn;

?>

<?php if ($hasPopulation): ?>
  <div class="submenu-study" id="population">
    <h2 class="field-content__h1 lang-text"
      data-fr="Population de l'étude"
      data-en="Study population">
      Population de l'étude
    </h2>

    <?php
    $universeFr = $jsonRec["fr"]['additional']['universe'] ?? [];
    $universeEn = $jsonRec["en"]['additional']['universe'] ?? [];

    $frSex = $universeFr['sexe'] ?? [];
    $enSex = $universeEn['sexe'] ?? [];

    $frAge = $universeFr['level_age_clusion_I'] ?? [];
    $enAge = $universeEn['level_age_clusion_I'] ?? [];

    $frType = $universeFr['level_type_clusion_I'] ?? [];
    $enType = $universeEn['level_type_clusion_I'] ?? [];

    $frOtherType = $universeFr['type_inclusion_autre'] ?? '';
    $enOtherType = $universeEn['type_inclusion_autre'] ?? '';

    $clusionIFr = $universeFr['clusion_I'] ?? '';
    $clusionIEn = $universeEn['clusion_I'] ?? '';

    $clusionEFr = $universeFr['clusion_E'] ?? '';
    $clusionEEn = $universeEn['clusion_E'] ?? '';

    if (is_array($frSex)) $frSex = implode(', ', $frSex);
    if (is_array($enSex)) $enSex = implode(', ', $enSex);

    if (is_array($frAge)) $frAge = implode(', ', $frAge);
    if (is_array($enAge)) $enAge = implode(', ', $enAge);

    if (is_array($frType)) $frType = implode(', ', $frType);
    if (is_array($enType)) $enType = implode(', ', $enType);

    $showOtherType = in_array($frType, ['Autre', 'autre', 'other', 'Other'], true)
      || in_array($enType, ['Autre', 'autre', 'other', 'Other'], true);
    ?>
    <?php if ($frType || $enType): ?>
      <div class="field-bloc">
        <div class="field-bloc__title lang-text" data-fr="Type de population" data-en="Population Type">Type de population</div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo esc_attr($frType); ?>"
          data-en="<?php echo esc_attr($enType); ?>">
          <?php echo esc_html($currentLang === 'fr' ? $frType : $enType); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($showOtherType): ?>
      <div class="field-bloc">
        <div class="field-bloc__title lang-text"
          data-fr="Autre type de population, précisions"
          data-en="Other Population Type (details)">
          Autre type de population, précisions
        </div>

        <div class="field-bloc__value lang-text"
          data-fr="<?php echo esc_attr($frOtherType); ?>"
          data-en="<?php echo esc_attr($enOtherType); ?>">
          <?php echo esc_html($currentLang === 'fr' ? $frOtherType : $enOtherType); ?>
        </div>
      </div>

    <?php endif; ?>
    <?php if ($frSex || $enSex): ?>
      <?php

      if (!function_exists('buildTopicsHtml')) {
        function buildTopicsHtml($topics)
        {
          $html = '';
          foreach ($topics as $t) {
            $topic = trim($t['topic'] ?? '');
            $value = trim($t['value'] ?? '');

            if ($topic && $value) {
              // Les deux existent → lien
              $label = '<a href="' . htmlspecialchars($topic) . '" target="_blank">' . htmlspecialchars($value) . '</a>';
            } elseif ($value) {
              // Seulement value
              $label = htmlspecialchars($value);
            } elseif ($topic) {
              // Seulement topic
              $label = htmlspecialchars($topic);
            } else {
              continue;
            }

            $html .= '<span class="checkbox-item">' . $label . '</span> ';
          }
          return trim($html);
        }
      }
      $frSexArray = is_array($frSex)
        ? $frSex
        : array_filter(array_map('trim', preg_split('/[,;]+/', (string)$frSex)));

      $enSexArray = is_array($enSex)
        ? $enSex
        : array_filter(array_map('trim', preg_split('/[,;]+/', (string)$enSex)));

      $frSexTopics = array_map(fn($v) => ['topic' => '', 'value' => $v], $frSexArray);
      $enSexTopics = array_map(fn($v) => ['topic' => '', 'value' => $v], $enSexArray);
      ?>

      <div class="field-bloc">
        <div class="field-bloc__title lang-text" data-fr="Sexe" data-en="Sex">Sexe</div>
        <div class="field-bloc__value lang-text"
          data-fr="<?= esc_attr(buildTopicsHtml($frSexTopics)); ?>"
          data-en="<?= esc_attr(buildTopicsHtml($enSexTopics)); ?>">
          <?= $currentLang === 'fr'
            ? buildTopicsHtml($frSexTopics)
            : buildTopicsHtml($enSexTopics); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($frAge || $enAge): ?>
      <?php
      if (!function_exists('buildTopicsHtml')) {
        function buildTopicsHtml($topics)
        {
          $html = '';
          foreach ($topics as $t) {
            $topic = trim($t['topic'] ?? '');
            $value = trim($t['value'] ?? '');

            if ($topic && $value) {
              // Les deux existent → lien
              $label = '<a href="' . htmlspecialchars($topic) . '" target="_blank">' . htmlspecialchars($value) . '</a>';
            } elseif ($value) {
              // Seulement value
              $label = htmlspecialchars($value);
            } elseif ($topic) {
              // Seulement topic
              $label = htmlspecialchars($topic);
            } else {
              continue;
            }

            $html .= '<span class="checkbox-item">' . $label . '</span> ';
          }
          return trim($html);
        }
      }
      $frAgeArray = is_array($frAge)
        ? $frAge
        : array_filter(array_map('trim', preg_split('/[,;]+/', (string)$frAge)));

      $enAgeArray = is_array($enAge)
        ? $enAge
        : array_filter(array_map('trim', preg_split('/[,;]+/', (string)$enAge)));

      $frAgeTopics = array_map(fn($v) => ['topic' => '', 'value' => $v], $frAgeArray);
      $enAgeTopics = array_map(fn($v) => ['topic' => '', 'value' => $v], $enAgeArray);
      ?>

      <div class="field-bloc">
        <div class="field-bloc__title lang-text" data-fr="Âge" data-en="Age">Âge</div>
        <div class="field-bloc__value lang-text"
          data-fr="<?= esc_attr(buildTopicsHtml($frAgeTopics)); ?>"
          data-en="<?= esc_attr(buildTopicsHtml($enAgeTopics)); ?>">
          <?= $currentLang === 'fr'
            ? buildTopicsHtml($frAgeTopics)
            : buildTopicsHtml($enAgeTopics); ?>
        </div>
      </div>
    <?php endif; ?>


    <?php if ($clusionIFr || $clusionIEn): ?>
      <div class="field-bloc">
        <div class="field-bloc__title lang-text" data-fr="Critères d'inclusion" data-en="Inclusion Criteria">Critères d'inclusion</div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo nl2br(esc_attr($clusionIFr)); ?>"
          data-en="<?php echo nl2br(esc_attr($clusionIEn)); ?>">
          <?php echo esc_html($currentLang === 'fr' ? $clusionIFr : $clusionIEn); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($clusionEFr || $clusionEEn): ?>
      <div class="field-bloc">
        <div class="field-bloc__title lang-text" data-fr="Critères d'exclusion" data-en="Exclusion Criteria">Critères d'exclusion</div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo nl2br(esc_attr($clusionEFr)); ?>"
          data-en="<?php echo nl2br(esc_attr($clusionEEn)); ?>">
          <?php echo nl2br(esc_html($currentLang === 'fr' ? $clusionEFr : $clusionEEn)); ?>
        </div>
      </div>
    <?php endif; ?>

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
    $nationFr = $jsonRec["fr"]['study_desc']['study_info']['nation'] ?? [];
    $nationEn = $jsonRec["en"]['study_desc']['study_info']['nation'] ?? [];


    if (is_array($nationFr) && isset($nationFr[0]['name'])) {
      $nationFrList = array_filter(array_map(fn($n) => $n['name'] ?? '', $nationFr));
    } elseif (!empty($nationFr)) {
      $nationFrList = parseStringArrayNormalized($nationFr);
    } else {
      $nationFrList = [];
    }

    if (is_array($nationEn) && isset($nationEn[0]['name'])) {
      $nationEnList = array_filter(array_map(fn($n) => $n['name'] ?? '', $nationEn));
    } elseif (!empty($nationEn)) {
      $nationEnList = parseStringArrayNormalized($nationEn);
    } else {
      $nationEnList = [];
    }

    $nationFrHtml = '';
    foreach ($nationFrList as $country) {
      $country = trim($country);
      if ($country !== '') {
        $nationFrHtml .= "<span class='keyword-item'>" . htmlspecialchars($country) . "</span> ";
      }
    }

    $nationEnHtml = '';
    foreach ($nationEnList as $country) {
      $country = trim($country);
      if ($country !== '') {
        $nationEnHtml .= "<span class='keyword-item'>" . htmlspecialchars($country) . "</span> ";
      }
    }


    $geoCoverageFr = $jsonRec["fr"]['study_desc']['study_info']['geog_coverage'] ?? [];
    $geoCoverageEn = $jsonRec["en"]['study_desc']['study_info']['geog_coverage'] ?? [];

    $geoDetailFr = $jsonRec["fr"]['additional']['geographicalCoverage']['geoDetail'] ?? '';
    $geoDetailEn = $jsonRec["en"]['additional']['geographicalCoverage']['geoDetail'] ?? '';



    $geoCoverageFr = implode(', ', parseStringArrayNormalized($geoCoverageFr));
    $geoCoverageEn = implode(', ', parseStringArrayNormalized($geoCoverageEn));
    ?>

    <?php if (!empty($nationFrHtml) || !empty($nationEnHtml)): ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text"
          data-fr="Pays concerné(s)"
          data-en="Country(ies) concerned">
          Pays concerné(s)
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo esc_attr($nationFrHtml); ?>"
          data-en="<?php echo esc_attr($nationEnHtml); ?>">
          <?php echo $currentLang === 'fr' ? $nationFrHtml : $nationEnHtml; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if ($geoCoverageFr || $geoCoverageEn): ?>
      <div class="field-bloc">
        <div class="field-bloc__title lang-text"
          data-fr="Région(s) française(s) concernée(s)"
          data-en="French region(s) concerned">
          Région(s) française(s) concernée(s)
        </div>
        <?php

        if (!function_exists('buildTopicsHtml')) {
          function buildTopicsHtml($topics)
          {
            $html = '';
            foreach ($topics as $t) {
              $topic = trim($t['topic'] ?? '');
              $value = trim($t['value'] ?? '');

              if ($topic && $value) {
                // Les deux existent → lien
                $label = '<a href="' . htmlspecialchars($topic) . '" target="_blank">' . htmlspecialchars($value) . '</a>';
              } elseif ($value) {
                // Seulement value
                $label = htmlspecialchars($value);
              } elseif ($topic) {
                // Seulement topic
                $label = htmlspecialchars($topic);
              } else {
                continue;
              }

              $html .= '<span class="checkbox-item">' . $label . '</span> ';
            }
            return trim($html);
          }
        }
        $geoCoverageFrArray = is_array($geoCoverageFr)
          ? $geoCoverageFr
          : array_filter(array_map('trim', preg_split('/[,;]+/', (string)$geoCoverageFr)));

        $geoCoverageEnArray = is_array($geoCoverageEn)
          ? $geoCoverageEn
          : array_filter(array_map('trim', preg_split('/[,;]+/', (string)$geoCoverageEn)));

        $geoCoverageFrTopics = array_map(fn($v) => ['topic' => '', 'value' => $v], $geoCoverageFrArray);
        $geoCoverageEnTopics = array_map(fn($v) => ['topic' => '', 'value' => $v], $geoCoverageEnArray);
        ?>

        <div class="field-bloc__value lang-text"
          data-fr="<?= esc_attr(buildTopicsHtml($geoCoverageFrTopics)); ?>"
          data-en="<?= esc_attr(buildTopicsHtml($geoCoverageEnTopics)); ?>">
          <?= $currentLang === 'fr'
            ? buildTopicsHtml($geoCoverageFrTopics)
            : buildTopicsHtml($geoCoverageEnTopics); ?>
        </div>

      </div>
    <?php endif; ?>
    <?php if ($geoDetailFr || $geoDetailEn): ?>
      <div class="field-bloc">
        <div class="field-bloc__title lang-text"
          data-fr="Champ géographique, précisions"
          data-en="Geographical coverage, details">
          Champ géographique, précisions
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?php echo nl2br(esc_attr($geoDetailFr)); ?>"
          data-en="<?php echo nl2br(esc_attr($geoDetailEn)); ?>">
          <?php echo esc_html($currentLang === 'fr' ? $geoDetailFr : $geoDetailEn); ?>
        </div>
      </div>

    <?php endif; ?>
  </div>
<?php endif; ?>
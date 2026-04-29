<?php

if (!defined('ABSPATH')) {
  exit;
}

$current_language = function_exists('pll_current_language') ? pll_current_language() : 'fr';
?>

<?php
$contactsFr = $jsonRec["fr"]["study_desc"]["distribution_statement"]["contact"] ?? [];
$contactsEn = $jsonRec["en"]["study_desc"]["distribution_statement"]["contact"] ?? [];

if (!is_array($contactsFr)) $contactsFr = [];
if (!is_array($contactsEn)) $contactsEn = [];

// Filter out completely empty contacts
$contactsFr = array_values(array_filter($contactsFr, function ($c) {
  return !empty(trim($c['name'] ?? '')) ||
    !empty(trim($c['email'] ?? '')) ||
    !empty(trim($c['affiliationName'] ?? ''));
}));
$contactsEn = array_values(array_filter($contactsEn, function ($c) {
  return !empty(trim($c['name'] ?? '')) ||
    !empty(trim($c['email'] ?? '')) ||
    !empty(trim($c['affiliationName'] ?? ''));
}));

$hasContactsfr = count($contactsFr) > 0;
$hasContactsen = count($contactsEn) > 0;

$dataToolLinkFr = trim((string)($jsonRec['fr']['study_desc']['data_access']['dataset_use']['spec_perm'][0]['txt'] ?? ''));
$dataToolLinkEn = trim((string)($jsonRec['en']['study_desc']['data_access']['dataset_use']['spec_perm'][0]['txt'] ?? ''));

$hasToolLink = false;

$url_pdad = get_nada_pdad_url();

if (
  (!empty($dataToolLinkFr) && str_contains($dataToolLinkFr, $url_pdad)) ||
  (!empty($dataToolLinkEn) && str_contains($dataToolLinkEn, $url_pdad))
) {
  $hasToolLink = true;
}
?>

<ul class="nav nav-pills m-0 nav nav-pills m-0 d-flex justify-content-end">
  <li class="nav-item langue">
    <a class="nav-link active" data-lang="fr" href="#">FR</a>
  </li>
  <li class="nav-item langue">
    <a class="nav-link" data-lang="en" href="#">EN</a>
  </li>
</ul>

<div class="details-study-fr" style="display: none;">
  <?php include('partials/details-study-data-fr.php'); ?>
</div>

<div class="details-study-en" style="display: none;">
  <?php include('partials/details-study-data-en.php'); ?>
</div>

<div class="detail-container form-nada-add">

  <div class="d-flex justify-content-end mb-3">
    <button class="btn btn-secondary return-btn lang-text" id="returnDetailsBtn" data-fr="Retour" data-en="Return">
      Retour
    </button>
  </div>

  <?php $langs = ['fr', 'en']; ?>
  <div class="">
    <div class="row">
      <div class="col-md-4 leftsidebar toggle-container">
        <div class="toggle-item d-md-none" id="sidebarToggleBtn">
          <span class="toggle-text"><?php echo ($current_language === 'fr') ? 'Sommaire' : 'Summary'; ?></span>
          <i class="arrow-down"></i>
        </div>
        <div class="toggle-content h-100" id="sidebarToggleContent">
          <div class="navStickyBar">
            <ul class="navbar-nav flex-column wb--full-width">
              <!-- step 1  -->
              <li class="nav-item">
                <a href="#studyInformations" class="lang-text" data-fr="Présentation de l'étude"
                  data-en="Study overview">Présentation de l'étude</a>
              </li>
              <li class="nav-item">
                <a href="#thematique" class="lang-text" data-fr="Thématique" data-en="Theme">Thématique</a>
              </li>
              <li class="nav-item">
                <a href="#population" class="lang-text" data-fr="Population de l'étude" data-en="Study population">Population de l'étude</a>
              </li>
              <!-- step2 -->
              <li class="nav-item">
                <a href="#regulatoryRequirements" class="lang-text" data-fr="Pré-requis réglementaires"
                  data-en="Regulatory requirements">Pré-requis réglementaires</a>
              </li>
              <li class="nav-item">
                <a href="#principalInvestigator" class="lang-text" data-fr="Responsable scientifique"
                  data-en="Principal investigator">Responsable scientifique</a>
              </li>
              <li class="nav-item">
                <a href="#researchTeamMember" class="lang-text" data-fr="Membre de l'équipe de recherche"
                  data-en="Research team member">Membre de l'équipe de recherche</a>
              </li>
              <li class="nav-item">
                <a href="#contact" class="lang-text" data-fr="Points de contact" data-en="Contact points">Points
                  de contact</a>
              </li>
              <li class="nav-item">
                <a href="#financeur" class="lang-text" data-fr="Financeur" data-en="Funder">Financeur</a>
              </li>
              <li class="nav-item">
                <a href="#organisation" class="lang-text" data-fr="Organisation et gouvernance"
                  data-en="Organization and governance">Organisation et gouvernance</a>
              </li>
              <li class="nav-item">
                <a href="#otherStudy" class="lang-text" data-fr="Autre(s) identifiant(s) de l'étude"
                  data-en="Other(s) ID for the study">Autre(s) identifiant(s) de l'étude</a>
              </li>
              <!-- step3 -->
              <li class="nav-item">
                <a href="#analysisunit" class="lang-text" data-fr="Unité d'analyse" data-en="Analysis unit">
                  Unité d'analyse
                </a>
              </li>
              <li class="nav-item">
                <a href="#modelEtude" class="lang-text" data-fr="Modèle d'étude" data-en="Study Type">
                  Modèle d'étude
                </a>
              </li>
              <?php
              $analysisUnitFr = $jsonRec['fr']['study_desc']['method']['method_notes'] ?? '';
              $analysisUnitEn = $jsonRec['en']['study_desc']['method']['method_notes'] ?? '';

              if (!function_exists('normalizeText')) {
                function normalizeText($text)
                {
                  $text = trim(mb_strtolower($text));
                  $text = preg_replace('/\s+/', ' ', $text);
                  $text = str_replace(['é', 'è', 'ê', 'ë'], 'e', $text);
                  return $text;
                }
              }

              $frNorm = normalizeText($analysisUnitFr);
              $enNorm = normalizeText($analysisUnitEn);

              $showObservationalStudy = (
                str_contains($frNorm, 'etude observationnelle') ||
                str_contains($enNorm, 'observational study')
              );

              $showInterventionalStudy = (
                str_contains($frNorm, 'etude interventionnelle') ||
                str_contains($enNorm, 'interventional study')
              );
              ?>
              <?php if ($showObservationalStudy): ?>
                <li class="nav-item">
                  <a href="#observationalstudy"
                    class="lang-text"
                    data-fr="Etude observationnelle"
                    data-en="Observational study">
                    Etude observationnelle
                  </a>
                </li>
              <?php endif; ?>
              <?php if ($showInterventionalStudy): ?>
                <li class="nav-item">
                  <a href="#interventionalstudy"
                    class="lang-text"
                    data-fr="Étude interventionnelle (expérimentale)"
                    data-en="Interventional study">
                    Étude interventionnelle (expérimentale)
                  </a>
                </li>
              <?php endif; ?>
              <li class="nav-item">
                <a href="#healthparameters"
                  class="lang-text"
                  data-fr="Critères d'évaluation (de jugement)"
                  data-en="Primary outcome(s)">
                  Critères d'évaluation (de jugement)
                </a>
              </li>
              <!-- step4 -->
              <li class="nav-item">
                <a href="#regulatoryCollecte" class="lang-text" data-fr="Collecte et réutilisation de données"
                  data-en="Data collection and reuse">Collecte et réutilisation de données</a>
              </li>
              <li class="nav-item">
                <a href="#dateAccess" class="lang-text" data-fr="Accès aux données"
                  data-en="Data access">Accès aux données</a>
              </li>
              <li class="nav-item">
                <a href="#RelatedDocument" class="lang-text" data-fr="Documents connexes"
                  data-en="Related documents">Documents connexes</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-8 px-4 mt-4 mt-md-0">
        <?php
        include('detail-step-1.php');
        ?>
        <?php
        include('detail-step-2.php');
        ?>
        <?php
        include('detail-step-3.php');
        ?>
        <?php
        include('detail-step-4.php');
        ?>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-end mb-3">
    <button class="btn btn-secondary return-btn lang-text" id="returnDetailsBtn" data-fr="Retour" data-en="Return">
      Retour
    </button>
  </div>
</div>
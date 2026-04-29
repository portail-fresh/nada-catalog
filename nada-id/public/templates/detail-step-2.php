<?php
// Data preparation
$agenciesFr = $jsonRec['fr']['study_desc']['study_authorization']['agency'] ?? [];
$agenciesEn = $jsonRec['en']['study_desc']['study_authorization']['agency'] ?? [];
$otherFr = $jsonRec['fr']['additional']['obtainedAuthorization']['otherAuthorizingAgency'] ?? [];
$otherEn = $jsonRec['en']['additional']['obtainedAuthorization']['otherAuthorizingAgency'] ?? [];

if (!is_array($agenciesFr)) $agenciesFr = [];
if (!is_array($agenciesEn)) $agenciesEn = [];
if (!is_array($otherFr)) $otherFr = [];
if (!is_array($otherEn)) $otherEn = [];

$max = max(count($agenciesFr), count($agenciesEn));

// Determine if section should be shown
$hasAuthorities = false;
for ($i = 0; $i < $max; $i++) {
  $nameFr = trim($agenciesFr[$i]['name'] ?? '');
  $nameEn = trim($agenciesEn[$i]['name'] ?? '');
  $otherFrVal = trim($otherFr[$i] ?? '');
  $otherEnVal = trim($otherEn[$i] ?? '');
  if ($nameFr || $nameEn || $otherFrVal || $otherEnVal) {
    $hasAuthorities = true;
    break;
  }
}
?>

<?php
if (!function_exists('normalizeUrl')) {

  function normalizeUrl($url)
  {
    if (!empty($url) && !preg_match('#^https?://#', $url)) {
      return 'http://' . $url;
    }
    return $url;
  }
}
?>

<?php if ($hasAuthorities): ?>
  <div class="submenu-study" id="regulatoryRequirements">

    <h2 class="field-content__h1 lang-text"
      data-fr="Pré-requis réglementaires"
      data-en="Regulatory requirements">
      Pré-requis réglementaires
    </h2>

    <ul class="authorization-list">

      <?php for ($i = 0; $i < $max; $i++):
        $nameFr = trim($agenciesFr[$i]['name'] ?? '');
        $nameEn = trim($agenciesEn[$i]['name'] ?? '');
        $otherFrVal = trim($otherFr[$i] ?? '');
        $otherEnVal = trim($otherEn[$i] ?? '');

        if (!$nameFr && !$nameEn && !$otherFrVal && !$otherEnVal) continue;

        $num = $i + 1;
      ?>

        <li class="authorization-item">

          <strong class="lang-text"
            data-fr="Autorisation <?= $num; ?>"
            data-en="Authorization <?= $num; ?>">
            Autorisation <?= $num ?>
          </strong>

          <ul class="authorization-detail">

            <?php if ($nameFr || $nameEn): ?>
              <li>
                <div class="field-bloc">

                  <div class="field-bloc__title lang-text"
                    data-fr="Autorité compétente ayant délivré l'autorisation ou l'avis de validité de l'étude"
                    data-en="Competent authority that issued the authorisation or notice of validity for the study">
                  </div>

                  <div class="field-bloc__value lang-text"
                    data-fr="<?= esc_html($nameFr); ?>"
                    data-en="<?= esc_html($nameEn); ?>">
                  </div>

                </div>
              </li>
            <?php endif; ?>

            <?php if ($otherFrVal || $otherEnVal): ?>
              <li>
                <div class="field-bloc">

                  <div class="field-bloc__title lang-text"
                    data-fr="Autre autorité compétente, précisions"
                    data-en="Other competent authority, details">
                  </div>

                  <div class="field-bloc__value lang-text"
                    data-fr="<?= nl2br(esc_html($otherFrVal)); ?>"
                    data-en="<?= nl2br(esc_html($otherEnVal)); ?>">
                  </div>

                </div>
              </li>
            <?php endif; ?>

          </ul>

        </li>

      <?php endfor; ?>

    </ul>

  </div>
<?php endif; ?>

<?php
if (!function_exists('hasNonEmptyExtlink')) {
  function hasNonEmptyExtlink(array $extlinks): bool
  {
    foreach ($extlinks as $item) {
      if (!is_array($item)) {
        if (trim((string)$item) !== '') return true;
        continue;
      }
      foreach ($item as $k => $v) {
        if ($k === 'role' || $k === 'title') continue; // ignore "role" and "title" fields
        if (trim((string)$v) !== '') return true;
      }
    }
    return false;
  }
}

if (!function_exists('hasAnyContent')) {
  function hasAnyContent(array $entity): bool
  {
    $fields = ['firstname', 'lastname', 'name', 'email', 'affiliationName', 'PILabo'];

    foreach ($fields as $field) {
      if (isset($entity[$field]) && trim((string)$entity[$field]) !== '') {
        return true;
      }
    }

    // Check isContact specifically
    if (isset($entity['isContact']) && trim((string)$entity['isContact']) !== '') {
      return true;
    }

    // Check extlink array
    if (isset($entity['extlink']) && is_array($entity['extlink'])) {
      if (hasNonEmptyExtlink($entity['extlink'])) {
        return true;
      }
    }

    return false;
  }
}

$entitiesFR = $jsonRec["fr"]["study_desc"]["authoring_entity"] ?? [];
$entitiesEN = $jsonRec["en"]["study_desc"]["authoring_entity"] ?? [];

if (!is_array($entitiesFR)) $entitiesFR = [];
if (!is_array($entitiesEN)) $entitiesEN = [];

// Check if ANY investigator has content (FR or EN)
$hasAnyInvestigator = false;


foreach ($entitiesFR as $i => $entityFR) {
  $entityEN = $entitiesEN[$i] ?? [];
  if (hasAnyContent($entityFR) || hasAnyContent($entityEN)) {
    $hasAnyInvestigator = true;
    break;
  }
}
foreach ($entitiesFR as $i => $entityFR) {
  $entityEN = $entitiesEN[$i] ?? [];
  if (!hasAnyContent($entityFR) && !hasAnyContent($entityEN)) {
    unset($entitiesFR[$i]);
    break;
  }
}
$entitiesFR = array_values($entitiesFR);
$entitiesEN = array_values($entitiesEN);

?>

<?php if ($hasAnyInvestigator): ?>
  <div class="submenu-study" id="principalInvestigator">
    <div class="field-content__h1 lang-text"
      data-fr="Responsable scientifique"
      data-en="Principal investigator">
      Responsable scientifique
    </div>

    <?php foreach ($entitiesFR as $i => $investigatorFR): ?>
      <?php
      $num = $i + 1;
      $investigatorEN = $entitiesEN[$i] ?? [];
      $hasContent = hasAnyContent($investigatorFR) || hasAnyContent($investigatorEN);

      if (!$hasContent) continue;

      $firstnameFr  = trim($investigatorFR['firstname'] ?? '');
      $firstnameEn  = trim($investigatorEN['firstname'] ?? '');
      $lastnameFr  = trim($investigatorFR['lastname'] ?? '');
      $lastnameEn  = trim($investigatorEN['lastname'] ?? '');
      $nameFr  = trim($investigatorFR['name'] ?? '');
      $nameEn  = trim($investigatorEN['name'] ?? '');
      if (empty($firstnameFr) && empty($lastnameFr) && !empty($nameFr) && (strpos($nameFr, ';') || strpos($nameFr, ' ')) !== false) {
        $parts = array_map('trim', explode(';', $nameFr, 2));
        $firstnameFr = $parts[0] ?? '';
        $lastnameFr = $parts[1] ?? '';
      }

      if (empty($firstnameEn) && empty($lastnameEn) && !empty($nameEn) && strpos($nameEn, ';') !== false) {
        $parts = array_map('trim', explode(';', $nameEn, 2));
        $firstnameEn = $parts[0] ?? '';
        $lastnameEn = $parts[1] ?? '';
      }
      $emailFr = trim($investigatorFR['email'] ?? '');
      $emailEn = trim($investigatorEN['email'] ?? '');
      $affFr   = trim($investigatorFR['affiliationName'] ?? '');
      $affEn   = trim($investigatorEN['affiliationName'] ?? '');

      // Get all extlinks (support both single object and array)
      $extlinkFR = $investigatorFR['extlink'] ?? [];
      $extlinkEN = $investigatorEN['extlink'] ?? [];

      // Convert single extlink object to array
      if (is_array($extlinkFR) && isset($extlinkFR['title']) && isset($extlinkFR['uri'])) {
        $extlinkFR = [$extlinkFR];
      } elseif (!is_array($extlinkFR)) {
        $extlinkFR = [];
      }

      if (is_array($extlinkEN) && isset($extlinkEN['title']) && isset($extlinkEN['uri'])) {
        $extlinkEN = [$extlinkEN];
      } elseif (!is_array($extlinkEN)) {
        $extlinkEN = [];
      }
      $piLaboFR = $investigatorFR['PILabo'] ?? null;
      $piLaboEN = $investigatorEN['PILabo'] ?? null;
      ?>

      <div class="field-card mb-3">
        <div class="field-card__header lang-text"
          data-fr="Responsable scientifique <?= $num; ?>"
          data-en="Principal investigator <?= $num; ?>">
          Responsable scientifique <?= $num; ?>
        </div>

        <div class="field-card__body">

          <?php if ($firstnameFr || $firstnameEn): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="Prénom du responsable scientifique"
                data-en="First name of the principal investigator">
                Prénom du responsable scientifique
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= nl2br(esc_html($firstnameFr)); ?>"
                data-en="<?= nl2br(esc_html($firstnameEn)); ?>">
                <?= nl2br(esc_html($firstnameFr)); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($lastnameFr || $lastnameEn): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="NOM du responsable scientifique"
                data-en="LAST NAME of the principal investigator">
                NOM du responsable scientifique
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= nl2br(esc_html($lastnameFr)); ?>"
                data-en="<?= nl2br(esc_html($lastnameEn)); ?>">
                <?= nl2br(esc_html($lastnameFr)); ?>
              </div>
            </div>
          <?php endif; ?>

            <?php if ($emailFr || $emailEn): ?>
                <div class="field-bloc mb-2">
                    <div class="field-bloc__title lang-text"
                         data-fr="Email du responsable scientifique"
                         data-en="Principal investigator's email">
                        Email du responsable scientifique
                    </div>
                    <div class="field-bloc__value lang-text"
                         data-fr="<?= esc_html($emailFr); ?>"
                         data-en="<?= esc_html($emailEn); ?>">
                        <?= esc_html($emailFr); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            if (!empty($extlinkFR)):
                foreach ($extlinkFR as $linkIndex => $linkFR):
                    $linkEN = $extlinkEN[$linkIndex] ?? [];
                    $title = trim($linkFR['title'] ?? '');
                    $uri = trim($linkFR['uri'] ?? '');
                    $titleEN = trim($linkEN['title'] ?? '');
                    $uriEN = trim($linkEN['uri'] ?? '');

                    if (!$title || !$uri) continue;

                    $isORCID = strtoupper($title) === 'ORCID';
                    $isIdRef = strtoupper($title) === 'IDREF';

                    if ($isORCID):
                        ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                 data-fr="ORCID du responsable scientifique"
                                 data-en="Principal investigator's ORCID">
                                ORCID du responsable scientifique
                            </div>
                            <div class="field-bloc__value">
                                <a href="https://orcid.org/<?= esc_attr($uri); ?>" target="_blank"><?= esc_html($uri); ?></a>
                            </div>
                        </div>
                    <?php
                    elseif ($isIdRef):
                        ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                 data-fr="Identifiant IdRef"
                                 data-en="IdRef identifier">
                                Identifiant IdRef
                            </div>
                            <div class="field-bloc__value">
                                <a href="http://www.idref.fr/<?= esc_html($uri); ?>/id" target="_blank"><?= esc_html($uri); ?></a>
                            </div>
                        </div>
                    <?php
                    endif;
                endforeach;
            endif;
            ?>

            <?php if (!empty($affFr) || !empty($affEn)): ?>
                <div class="field-bloc mb-2">
                    <div class="field-bloc__title lang-text"
                         data-fr="Nom de l'organisation d'affiliation principale"
                         data-en="Main affiliated organisation name">
                        Nom de l'organisation d'affiliation principale
                    </div>
                    <div class="field-bloc__value lang-text"
                         data-fr="<?= nl2br(esc_html($affFr)); ?>"
                         data-en="<?= nl2br(esc_html($affEn)); ?>">
                        <?= nl2br(esc_html($affFr)); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            if (!empty($extlinkFR)):
                $orgIdentifiersPI = [];

                foreach ($extlinkFR as $linkIndex => $linkFR):
                    $linkEN = $extlinkEN[$linkIndex] ?? [];
                    $title = trim($linkFR['title'] ?? '');
                    $uri = trim($linkFR['uri'] ?? '');
                    $titleEN = trim($linkEN['title'] ?? '');
                    $uriEN = trim($linkEN['uri'] ?? '');

                    if (!$title || !$uri) continue;

                    $isROR = strtoupper($title) === 'ROR';
                    $isSIREN = strtoupper($title) === 'SIREN';

                    if ($isROR || $isSIREN){
                        $orgIdentifiersPI[] = [
                                'title' => $title,
                                'titleEN' => $titleEN,
                                'uri' => $uri,
                                'uriEN' => $uriEN,
                                'isROR' => $isROR,
                                'isSIREN' => $isSIREN,
                                'linkIndex' => $linkIndex
                        ];
                    }
                endforeach;

                if (!empty($orgIdentifiersPI)):
                    ?>
                    <div class="field-bloc mb-2">
                        <div class="field-bloc__title lang-text"
                             data-fr="Identifiant de l'organisation d'affiliation principale"
                             data-en="Main affiliated organisation identifier">
                            Identifiant de l'organisation d'affiliation principale
                        </div>
                        <div class="field-bloc__value">
                            <?php foreach ($orgIdentifiersPI as $org): ?>
                                <div style="margin-bottom: 8px;">
                                    <span class="identifier-type"><?= esc_html($org['title']); ?>:</span>

                                    <?php if ($org['isROR']): ?>
                                        <a href="<?= esc_attr($org['uri']); ?>" target="_blank"><?= esc_html($org['uri']); ?></a>
                                    <?php elseif ($org['isSIREN']): ?>
                                        <span id="siren-value-<?= $i; ?>-<?= $org['linkIndex']; ?>"><?= esc_html($org['uri']); ?></span>
                                        <span onclick="copySiren('siren-value-<?= $i; ?>-<?= $org['linkIndex']; ?>')" title="Copier" style="cursor:pointer;">🗐</span>
                                    <?php else: ?>
                                        <span class="lang-text"
                                              data-fr="<?= esc_html($org['uri']); ?>"
                                              data-en="<?= esc_html($org['uriEN']); ?>">
                          <?= esc_html($currentLang === 'fr' ? $org['uri'] : $org['uriEN']); ?>
                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php
                endif;
            endif;
            ?>

            <?php if (!empty($piLaboFR) || !empty($piLaboEN)): ?>
                <div class="field-bloc mb-2">
                    <div class="field-bloc__title lang-text"
                         data-fr="Affiliation(s), précisions"
                         data-en="Affiliation(s), details">
                        Affiliation(s), précisions
                    </div>
                    <div class="field-bloc__value lang-text"
                         data-fr="<?php echo nl2br(esc_attr($piLaboFR)); ?>"
                         data-en="<?php echo nl2br(esc_attr($piLaboEN)); ?>">
                        <?= nl2br(esc_html($currentLang === 'fr' ? $piLaboFR : $piLaboEN)); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            if (!empty($extlinkFR)):
                $rnsr = null;

                foreach ($extlinkFR as $linkIndex => $linkFR):
                    $linkEN = $extlinkEN[$linkIndex] ?? [];
                    $title = trim($linkFR['title'] ?? '');
                    $uri = trim($linkFR['uri'] ?? '');
                    $titleEN = trim($linkEN['title'] ?? '');
                    $uriEN = trim($linkEN['uri'] ?? '');

                    if (!$title || !$uri) continue;

                    $isRNSR = strtoupper($title) === 'RNSR';

                    if ($isRNSR) {
                        $rnsr = [
                                'title' => $title,
                                'titleEN' => $titleEN,
                                'uri' => $uri,
                                'uriEN' => $uriEN,
                                'linkIndex' => $linkIndex
                        ];
                    }
                endforeach;

                if ($rnsr):
                    ?>
                    <div class="field-bloc mb-2">
                        <div class="field-bloc__title lang-text"
                             data-fr="Identifiant RNSR du laboratoire d'appartenance"
                             data-en="Affiliated laboratory RNSR identifier">
                            Identifiant RNSR du laboratoire d'appartenance
                        </div>
                        <div class="field-bloc__value">
                            <a href="https://rnsr.adc.education.fr/structure/<?= esc_attr($rnsr['uri']); ?>" target="_blank">
                                <?= esc_html($rnsr['uri']); ?>
                            </a>
                        </div>
                    </div>
                <?php
                endif;
            endif;
            ?>

        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php
$membersFr = $jsonRec["fr"]["study_desc"]["oth_id"] ?? [];
$membersEn = $jsonRec["en"]["study_desc"]["oth_id"] ?? [];

if (!is_array($membersFr)) $membersFr = [];
if (!is_array($membersEn)) $membersEn = [];

$membersFr = array_values(array_filter($membersFr, function ($item) {
    return ($item['type'] ?? '') !== 'collaboration';
}));

$membersEn = array_values(array_filter($membersEn, function ($item) {
    return ($item['type'] ?? '') !== 'collaboration';
}));

$chunksFr = array_chunk($membersFr, 2);
$chunksEn = array_chunk($membersEn, 2);

$chunksFr = array_values(array_filter($chunksFr, function ($pair) {
    $contrib = $pair[0] ?? [];
    $aff     = $pair[1] ?? [];
    $name = trim($contrib['name'] ?? '', " ;");
    $hasOrgLink = !empty(array_filter(
            $aff['extlink'] ?? [],
            fn($l) => ($l['role'] ?? '') === 'organisation id' && !empty(trim($l['uri'] ?? ''))
    ));

    $hasLaboLink = !empty(array_filter(
            $aff['extlink'] ?? [],
            fn($l) => ($l['role'] ?? '') === 'labo id' && !empty(trim($l['uri'] ?? ''))
    ));

    return !empty($contrib['firstname'])
            || !empty($contrib['lastname'])
            || !empty($name)
            || !empty($aff['name'])
            || !empty($aff['teamMemberLabo'])
            || $hasOrgLink
            || $hasLaboLink
            || !empty($contrib['extlink'][0]['uri'])
            || !empty($contrib['extlink'][1]['uri']);
}));

$chunksEn = array_values(array_filter($chunksEn, function ($pair) {
    $contrib = $pair[0] ?? [];
    $aff     = $pair[1] ?? [];
    $name = trim($contrib['name'] ?? '', " ;");
    $hasOrgLink = !empty(array_filter(
            $aff['extlink'] ?? [],
            fn($l) => ($l['role'] ?? '') === 'organisation id' && !empty(trim($l['uri'] ?? ''))
    ));

    $hasLaboLink = !empty(array_filter(
            $aff['extlink'] ?? [],
            fn($l) => ($l['role'] ?? '') === 'labo id' && !empty(trim($l['uri'] ?? ''))
    ));

    return !empty($contrib['firstname'])
            || !empty($contrib['lastname'])
            || !empty($name)
            || !empty($aff['name'])
            || !empty($aff['teamMemberLabo'])
            || $hasOrgLink
            || $hasLaboLink
            || !empty($contrib['extlink'][0]['uri'])
            || !empty($contrib['extlink'][1]['uri']);
}));

$hasMembersfr = count($chunksFr) > 0;
$hasMembersen = count($chunksEn) > 0;
?>

<?php if ($hasMembersfr || $hasMembersen): ?>
    <div class="submenu-study" id="researchTeamMember">
        <div class="field-content__h1 lang-text"
             data-fr="Membre de l'équipe de recherche"
             data-en="Research team member">
            Membre de l'équipe de recherche
        </div>

        <?php foreach ($chunksFr as $i => $pairFR): ?>
            <?php
            $num  = $i + 1;
            $pairEN = $chunksEn[$i] ?? [];

            $contributorFR = $pairFR[0] ?? [];
            $affiliationFR = $pairFR[1] ?? [];
            $contributorEN = $pairEN[0] ?? [];
            $affiliationEN = $pairEN[1] ?? [];

            $firstnameFr = $contributorFR['firstname'] ?? '';
            $firstnameEn = $contributorEN['firstname'] ?? '';
            $lastnameFr  = $contributorFR['lastname']  ?? '';
            $lastnameEn  = $contributorEN['lastname']  ?? '';
            $nameFr      = $contributorFR['name'] ?? '';
            $nameEn      = $contributorEN['name'] ?? '';

            if (empty($firstnameFr) && empty($lastnameFr) && !empty($nameFr) && strpos($nameFr, ';') !== false) {
                $parts       = array_map('trim', explode(';', $nameFr, 2));
                $firstnameFr = $parts[0] ?? '';
                $lastnameFr  = $parts[1] ?? '';
            }
            if (empty($firstnameEn) && empty($lastnameEn) && !empty($nameEn) && strpos($nameEn, ';') !== false) {
                $parts       = array_map('trim', explode(';', $nameEn, 2));
                $firstnameEn = $parts[0] ?? '';
                $lastnameEn  = $parts[1] ?? '';
            }
            if (empty($firstnameEn)) $firstnameEn = $firstnameFr;
            if (empty($lastnameEn))  $lastnameEn  = $lastnameFr;

            $teamMemberLaboFr = trim($affiliationFR['teamMemberLabo'] ?? '');
            $teamMemberLaboEn = trim($affiliationEN['teamMemberLabo'] ?? $teamMemberLaboFr);

            $contribExtFR = $contributorFR['extlink'] ?? [];
            $contribExtEN = $contributorEN['extlink'] ?? [];

            $orcidFR  = '';
            $orcidEN  = '';
            $idrefFR  = '';
            $idrefEN  = '';

            foreach ($contribExtFR as $cl) {
                if (strtoupper($cl['title'] ?? '') === 'ORCID' && empty($orcidFR)) $orcidFR = trim($cl['uri'] ?? '');
                if (strtoupper($cl['title'] ?? '') === 'IDREF' && empty($idrefFR)) $idrefFR = trim($cl['uri'] ?? '');
            }
            foreach ($contribExtEN as $cl) {
                if (strtoupper($cl['title'] ?? '') === 'ORCID' && empty($orcidEN)) $orcidEN = trim($cl['uri'] ?? '');
                if (strtoupper($cl['title'] ?? '') === 'IDREF' && empty($idrefEN)) $idrefEN = trim($cl['uri'] ?? '');
            }
            if (empty($orcidEN)) $orcidEN = $orcidFR;
            if (empty($idrefEN)) $idrefEN = $idrefFR;

            //Extlinks affiliation séparés par rôle
            $orgLinksFR  = array_values(array_filter($affiliationFR['extlink'] ?? [], fn($l) => ($l['role'] ?? '') === 'organisation id'));
            $laboLinksFR = array_values(array_filter($affiliationFR['extlink'] ?? [], fn($l) => ($l['role'] ?? '') === 'labo id'));
            $orgLinksEN  = array_values(array_filter($affiliationEN['extlink'] ?? [], fn($l) => ($l['role'] ?? '') === 'organisation id'));
            $laboLinksEN = array_values(array_filter($affiliationEN['extlink'] ?? [], fn($l) => ($l['role'] ?? '') === 'labo id'));

            // Vérification données
            $hasData =
                    !empty($firstnameFr)
                    || !empty($lastnameFr)
                    || !empty($affiliationFR['name'])
                    || !empty($teamMemberLaboFr)
                    || !empty($orcidFR)
                    || !empty($idrefFR)
                    || !empty($orgLinksFR)
                    || !empty($laboLinksFR)
                    || !empty(trim((string)($contributorFR['isContact'] ?? '')));

            if (!$hasData) continue;
            ?>

            <div class="field-card mb-3">
                <div class="field-card__header lang-text"
                     data-fr="Responsable de l'équipe partenaire <?= $num; ?>"
                     data-en="Partner team leader <?= $num; ?>">
                    Responsable de l'équipe partenaire <?= $num; ?>
                </div>

                <div class="field-card__body">

                    <?php
                    if (!empty($firstnameFr) || !empty($firstnameEn)): ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                 data-fr="Prénom du membre de l'équipe"
                                 data-en="First name of the team leader">
                                Prénom du membre de l'équipe
                            </div>
                            <div class="field-bloc__value lang-text"
                                 data-fr="<?= esc_html($firstnameFr); ?>"
                                 data-en="<?= esc_html($firstnameEn); ?>">
                                <?= esc_html($firstnameFr); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    if (!empty($lastnameFr) || !empty($lastnameEn)): ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                 data-fr="NOM du membre de l'équipe"
                                 data-en="LAST NAME of the team leader">
                                NOM du membre de l'équipe
                            </div>
                            <div class="field-bloc__value lang-text"
                                 data-fr="<?= esc_html($lastnameFr); ?>"
                                 data-en="<?= esc_html($lastnameEn); ?>">
                                <?= esc_html($lastnameFr); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    if (!empty($orcidFR) || !empty($orcidEN)):
                        $orcidDisplay = $currentLang === 'fr' ? $orcidFR : $orcidEN;
                        ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                 data-fr="ORCID du responsable de l'équipe"
                                 data-en="Team leader's ORCID">
                                ORCID du responsable de l'équipe
                            </div>
                            <div class="field-bloc__value">
                                <a href="https://orcid.org/<?= esc_attr($orcidDisplay); ?>" target="_blank">
                                    <?= esc_html($orcidDisplay); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    if (!empty($idrefFR) || !empty($idrefEN)):
                        $idrefDisplay = $currentLang === 'fr' ? $idrefFR : $idrefEN;
                        ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                 data-fr="Identifiant IdRef"
                                 data-en="IdRef identifier">
                                Identifiant IdRef
                            </div>
                            <div class="field-bloc__value">
                                <a href="http://www.idref.fr/<?= esc_attr($idrefDisplay); ?>/id" target="_blank">
                                    <?= esc_html($idrefDisplay); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    if (!empty($affiliationFR['name']) || !empty($affiliationEN['name'])): ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                 data-fr="Nom de l'organisation d'affiliation principale"
                                 data-en="Main affiliated organisation name">
                                Nom de l'organisation d'affiliation principale
                            </div>
                            <div class="field-bloc__value lang-text"
                                 data-fr="<?= nl2br(esc_html($affiliationFR['name'] ?? '')); ?>"
                                 data-en="<?= nl2br(esc_html($affiliationEN['name'] ?? '')); ?>">
                                <?= nl2br(esc_html($affiliationFR['name'] ?? '')); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    $orgIdentifiersRT = [];
                    foreach ($orgLinksFR as $oi => $orgLinkFR):
                        $orgLinkEN = $orgLinksEN[$oi] ?? $orgLinkFR;
                        $titleFr   = trim($orgLinkFR['title'] ?? '');
                        $titleEn   = trim($orgLinkEN['title'] ?? $titleFr);
                        $uriFr     = trim($orgLinkFR['uri']   ?? '');
                        $uriEn     = trim($orgLinkEN['uri']   ?? $uriFr);

                        // Ne pas ajouter si titre ET uri sont vides
                        if (empty($titleFr) && empty($uriFr)) continue;

                        $isROR     = strtoupper($titleFr) === 'ROR';
                        $isSIREN   = strtoupper($titleFr) === 'SIREN';

                        $orgIdentifiersRT[] = [
                                'titleFr' => $titleFr,
                                'titleEn' => $titleEn,
                                'uriFr' => $uriFr,
                                'uriEn' => $uriEn,
                                'isROR' => $isROR,
                                'isSIREN' => $isSIREN,
                                'oi' => $oi
                        ];

                    endforeach;

                    if (!empty($orgIdentifiersRT)):
                        ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                 data-fr="Identifiant de l'organisation d'affiliation principale"
                                 data-en="Main affiliated organisation identifier">
                                Identifiant de l'organisation d'affiliation principale
                            </div>
                            <div class="field-bloc__value">
                                <?php foreach ($orgIdentifiersRT as $org): ?>
                                    <div style="margin-bottom: 8px;">
                                        <span class="identifier-type"><?= esc_html($org['titleFr']); ?>:</span>

                                        <?php if ($org['isROR']): ?>
                                            <a href="https://ror.org/<?= esc_attr($org['uriFr']); ?>" target="_blank">
                                                <?= esc_html($org['uriFr']); ?>
                                            </a>
                                        <?php elseif ($org['isSIREN']): ?>
                                            <span id="siren-value-<?= $i; ?>-<?= $org['oi']; ?>"><?= esc_html($org['uriFr']); ?></span>
                                            <span onclick="copySiren('siren-value-<?= $i; ?>-<?= $org['oi']; ?>')" title="Copier" style="cursor:pointer;">🗐</span>
                                        <?php else: ?>
                                            <span class="lang-text"
                                                  data-fr="<?= esc_html($org['uriFr']); ?>"
                                                  data-en="<?= esc_html($org['uriEn']); ?>">
                        <?= esc_html($currentLang === 'fr' ? $org['uriFr'] : $org['uriEn']); ?>
                      </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php
                    endif;
                    ?>

                    <?php
                    if (!empty($teamMemberLaboFr) || !empty($teamMemberLaboEn)): ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                 data-fr="Affiliation(s), précisions"
                                 data-en="Affiliation(s), details">
                                Affiliation(s), précisions
                            </div>
                            <div class="field-bloc__value">
                <span class="lang-text"
                      data-fr="<?= nl2br(esc_attr($teamMemberLaboFr)); ?>"
                      data-en="<?= nl2br(esc_attr($teamMemberLaboEn)); ?>">
                  <?= nl2br(esc_html($currentLang === 'fr' ? $teamMemberLaboFr : $teamMemberLaboEn)); ?>
                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php

                    foreach ($laboLinksFR as $li => $laboLinkFR):
                        $laboLinkEN  = $laboLinksEN[$li] ?? $laboLinkFR;
                        $laboTitleFr = trim($laboLinkFR['title'] ?? '');
                        $laboUriFr   = trim($laboLinkFR['uri']   ?? '');
                        $laboUriEn   = trim($laboLinkEN['uri']   ?? $laboUriFr);
                        ?>
                        <?php if (!empty($laboUriFr) || !empty($laboUriEn)): ?>
                        <div class="field-bloc mb-2">
                            <div class="field-bloc__title lang-text"
                                 data-fr="Identifiant RNSR du laboratoire d'appartenance"
                                 data-en="Affiliated laboratory RNSR identifier">
                                Identifiant RNSR du laboratoire d'appartenance
                            </div>
                            <div class="field-bloc__value">
                                <?php if (strtoupper($laboTitleFr) === 'RNSR'): ?>
                                    <a href="https://rnsr.adc.education.fr/structure/<?= esc_attr($laboUriFr); ?>" target="_blank">
                                        <?= esc_html($laboUriFr); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="lang-text"
                                          data-fr="<?= esc_html($laboUriFr); ?>"
                                          data-en="<?= esc_html($laboUriEn); ?>">
                      <?= esc_html($currentLang === 'fr' ? $laboUriFr : $laboUriEn); ?>
                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php
$contactsFr = $jsonRec["fr"]["study_desc"]["distribution_statement"]["contact"] ?? [];
$contactsEn = $jsonRec["en"]["study_desc"]["distribution_statement"]["contact"] ?? [];

if (!is_array($contactsFr)) $contactsFr = [];
if (!is_array($contactsEn)) $contactsEn = [];

// Filter out completely empty contacts
$contactsFr = array_values(array_filter($contactsFr, function ($c) {
  return !empty(trim($c['name'] ?? '')) ||
    !empty(trim($c['lastname'] ?? '')) ||
    !empty(trim($c['firstname'] ?? '')) ||
    !empty(trim($c['email'] ?? '')) ||
    !empty(trim($c['affiliationName'] ?? '')) ||
    !empty(trim($c['contactPointLabo'] ?? '')) ||
    !empty(trim($c['extlink'][0]['uri'] ?? '')) ||
    !empty(trim($c['extlink'][0]['title'] ?? '')) ||
    !empty(trim($c['extlink'][1]['uri'] ?? ''));
}));
$contactsEn = array_values(array_filter($contactsEn, function ($c) {
  return !empty(trim($c['name'] ?? '')) ||
    !empty(trim($c['firstname'] ?? '')) ||
    !empty(trim($c['lastname'] ?? '')) ||
    !empty(trim($c['email'] ?? '')) ||
    !empty(trim($c['affiliationName'] ?? '')) ||
    !empty(trim($c['contactPointLabo'] ?? '')) ||
    !empty(trim($c['extlink'][0]['uri'] ?? '')) ||
    !empty(trim($c['extlink'][0]['title'] ?? '')) ||
    !empty(trim($c['extlink'][1]['uri'] ?? ''));
}));

$hasContactsfr = count($contactsFr) > 0;
$hasContactsen = count($contactsEn) > 0;
?>

<?php if ($hasContactsfr || $hasContactsen): ?>
  <div class="submenu-study" id="contact">
    <div class="field-content__h1 lang-text"
      data-fr="Point(s) de contact"
      data-en="Contact point(s)">
      Point(s) de contact
    </div>

    <?php foreach ($contactsFr as $i => $contactFR): ?>
      <?php
      $num = $i + 1;
      $contactEN = $contactsEn[$i] ?? [];

      // Extract values safely
      $nameFr = trim($contactFR['name'] ?? '');
      $nameEn = trim($contactEN['name'] ?? '');
      $firstnameFr = trim($contactFR['firstname'] ?? '');
      $firstnameEn = trim($contactEN['firstname'] ?? '');
      $lastnameFr = trim($contactFR['lastname'] ?? '');
      $lastnameEn = trim($contactEN['lastname'] ?? '');

      // If firstname and lastname are empty but name has data with separator, split it
      if (empty($firstnameFr) && empty($lastnameFr) && !empty($nameFr) && strpos($nameFr, ';') !== false) {
        $parts = array_map('trim', explode(';', $nameFr, 2));
        $firstnameFr = $parts[0] ?? '';
        $lastnameFr = $parts[1] ?? '';
      }

      if (empty($firstnameEn) && empty($lastnameEn) && !empty($nameEn) && strpos($nameEn, ';') !== false) {
        $parts = array_map('trim', explode(';', $nameEn, 2));
        $firstnameEn = $parts[0] ?? '';
        $lastnameEn = $parts[1] ?? '';
      }
      if (empty($firstnameEn)) $firstnameEn = $firstnameFr;
      if (empty($lastnameEn)) $lastnameEn = $lastnameFr;

      $emailFr = trim($contactFR['email'] ?? '');
      $emailEn = trim($contactEN['email'] ?? $emailFr);

      $affiliationFr = trim($contactFR['affiliationName'] ?? '');
      $affiliationEn = trim($contactEN['affiliationName'] ?? $affiliationFr);

      $contactPointLaboFr = trim($contactFR['contactPointLabo'] ?? '');
      $contactPointLaboEn = trim($contactEN['contactPointLabo'] ?? '');

      // Get all extlinks (support both single object and array)
      $extlinkFr = $contactFR['extlink'] ?? [];
      $extlinkEn = $contactEN['extlink'] ?? [];

      // Convert single extlink object to array
      if (is_array($extlinkFr) && isset($extlinkFr['title']) && isset($extlinkFr['uri'])) {
        $extlinkFr = [$extlinkFr];
      } elseif (!is_array($extlinkFr)) {
        $extlinkFr = [];
      }

      if (is_array($extlinkEn) && isset($extlinkEn['title']) && isset($extlinkEn['uri'])) {
        $extlinkEn = [$extlinkEn];
      } elseif (!is_array($extlinkEn)) {
        $extlinkEn = [];
      }

      // Skip card if absolutely nothing is filled
      if (
        !($firstnameFr || $lastnameFr || $emailFr || $affiliationFr || $extlinkFr || $contactPointLaboFr) &&
        !($firstnameEn || $lastnameEn || $emailEn || $affiliationEn || $extlinkEn || $contactPointLaboEn)
      ) continue;
      ?>

      <div class="field-card" style="margin-bottom: 15px;">
        <div class="field-card__header lang-text"
          data-fr="Point de contact <?= $num; ?>"
          data-en="Contact point <?= $num; ?>">
          Point de contact <?= $num; ?>
        </div>

        <div class="field-card__body">
          <?php if ($firstnameFr || $firstnameEn): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="Prénom du contact"
                data-en="First name of the contact">
                Prénom du contact
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= esc_html($firstnameFr); ?>"
                data-en="<?= esc_html($firstnameEn); ?>">
                <?= esc_html($firstnameFr); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($lastnameFr || $lastnameEn): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="NOM du contact"
                data-en="LAST NAME of the contact">
                NOM du contact
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= esc_html($lastnameFr); ?>"
                data-en="<?= esc_html($lastnameEn); ?>">
                <?= esc_html($lastnameFr); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($emailFr || $emailEn): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="Email du contact"
                data-en="Contact email">
                Email du contact
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= esc_html($emailFr); ?>"
                data-en="<?= esc_html($emailEn); ?>">
                <?php if (filter_var($emailFr, FILTER_VALIDATE_EMAIL)): ?>
                  <a href="mailto:<?= esc_attr($emailFr); ?>"><?= esc_html($emailFr); ?></a>
                <?php else: ?>
                  <?= esc_html($emailFr); ?>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($affiliationFr || $affiliationEn): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="Nom de l'organisation d'affiliation principale"
                data-en="Main affiliated organisation name">
                Nom de l'organisation d'affiliation principale
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= nl2br(esc_html($affiliationFr)); ?>"
                data-en="<?= nl2br(esc_html($affiliationEn)); ?>">
                <?= nl2br(esc_html($affiliationFr)); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php
          // Display all extlinks
          if (!empty($extlinkFr)):
            $orgIdentifiersPC = [];
            $rnsr = null;

            foreach ($extlinkFr as $linkIndex => $linkFR):
              $linkEN = $extlinkEn[$linkIndex] ?? [];

              $titleFr = trim($linkFR['title'] ?? '');
              $titleEn = trim($linkEN['title'] ?? '');

              $uriFr = trim($linkFR['uri'] ?? '');
              $uriEn = trim($linkEN['uri'] ?? '');

              if (!$titleFr || !$uriFr) continue;

              $isROR = (strtoupper(trim((string) $titleFr)) === 'ROR')
                || (strtoupper(trim((string) $titleEn)) === 'ROR');
              $isSIREN = (strtoupper(trim((string) $titleFr)) === 'SIREN')
                || (strtoupper(trim((string) $titleEn)) === 'SIREN');
              $isRNSR = (strtoupper(trim((string) $titleFr)) === 'RNSR')
                || (strtoupper(trim((string) $titleEn)) === 'RNSR');

              if ($isRNSR) {
                $rnsr = [
                  'titleFr' => $titleFr,
                  'titleEn' => $titleEn,
                  'uriFr' => $uriFr,
                  'uriEn' => $uriEn,
                  'linkIndex' => $linkIndex
                ];
              } else {
                $orgIdentifiersPC[] = [
                  'titleFr' => $titleFr,
                  'titleEn' => $titleEn,
                  'uriFr' => $uriFr,
                  'uriEn' => $uriEn,
                  'isROR' => $isROR,
                  'isSIREN' => $isSIREN,
                  'linkIndex' => $linkIndex
                ];
              }
            endforeach;
              if (!empty($orgIdentifiersPC)):
          ?>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text"
                  data-fr="Identifiant de l'organisation d'affiliation principale"
                  data-en="Main affiliated organisation identifier">
                  Identifiant de l'organisation d'affiliation principale
                </div>
                <div class="field-bloc__value">
                  <?php foreach ($orgIdentifiersPC as $org): ?>
                    <div style="margin-bottom: 8px;">
                      <span class="identifier-type"><?= esc_html($org['titleFr']); ?>:</span>

                      <?php if ($org['isROR']): ?>
                        <a href="<?= esc_attr($org['uriFr']); ?>" target="_blank"><?= esc_html($org['uriFr']); ?></a>
                      <?php elseif ($org['isSIREN']): ?>
                          <span id="siren-value-<?= $i; ?>-<?= $org['linkIndex']; ?>"><?= esc_html($org['uriFr']); ?></span>
                          <span onclick="copySiren('siren-value-<?= $i; ?>-<?= $org['linkIndex']; ?>')" title="Copier" style="cursor:pointer;">🗐</span>
                      <?php else: ?>
                        <span class="lang-text"
                          data-fr="<?= esc_html($org['uriFr']); ?>"
                          data-en="<?= esc_html($org['uriEn']); ?>">
                          <?= esc_html($currentLang === 'fr' ? $org['uriFr'] : $org['uriEn']); ?>
                        </span>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
          <?php
            endif;
            if ($rnsr):
          ?>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text"
                  data-fr="Identifiant RNSR du laboratoire d'appartenance"
                  data-en="Affiliated laboratory RNSR identifier">
                  Identifiant RNSR du laboratoire d'appartenance
                </div>
                <div class="field-bloc__value">
                  <a href="https://rnsr.adc.education.fr/structure/<?= esc_attr($rnsr['uriFr']); ?>" target="_blank">
                    <?= esc_html($rnsr['uriFr']); ?>
                  </a>
                </div>
              </div>
          <?php
            endif;
          endif;
          ?>

          <?php if ($contactPointLaboFr || $contactPointLaboEn): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="Affiliation(s), précisions"
                data-en="Affiliation(s), details">
                Affiliation(s), précisions
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?php echo nl2br(esc_attr($contactPointLaboFr)); ?>"
                data-en="<?php echo nl2br(esc_attr($contactPointLaboEn)); ?>">
                <?= nl2br(esc_html($currentLang === 'fr' ? $contactPointLaboFr : $contactPointLaboEn)); ?>
              </div>
            </div>
          <?php endif; ?>

        </div><!-- /.field-card__body -->
      </div><!-- /.field-card -->
    <?php endforeach; ?>
  </div><!-- /.submenu-study -->
<?php endif; ?>



<?php
$entitiesFR = $jsonRec["fr"]["study_desc"]["production_statement"]["funding_agencies"] ?? [];
$entitiesEN = $jsonRec["en"]["study_desc"]["production_statement"]["funding_agencies"] ?? [];

$additionalFR    = $jsonRec["fr"]["additional"]["fundingAgent"]["fundingAgentType"]      ?? [];
$additionalEN    = $jsonRec["en"]["additional"]["fundingAgent"]["fundingAgentType"]      ?? [];
$additionalOFATFR = $jsonRec["fr"]["additional"]["fundingAgent"]["otherFundingAgentType"] ?? [];
$additionalOFATEN = $jsonRec["en"]["additional"]["fundingAgent"]["otherFundingAgentType"] ?? [];

// Normalize arrays
if (!is_array($entitiesFR))    $entitiesFR    = [];
if (!is_array($entitiesEN))    $entitiesEN    = [];
if (!is_array($additionalOFATFR)) $additionalOFATFR = [];
if (!is_array($additionalOFATEN)) $additionalOFATEN = [];

if (is_array($additionalFR)) {
  $additionalFR = array_values(array_filter($additionalFR, fn($v) => trim((string)$v) !== ''));
} else {
  $additionalFR = [];
}
if (is_array($additionalEN)) {
  $additionalEN = array_values(array_filter($additionalEN, fn($v) => trim((string)$v) !== ''));
} else {
  $additionalEN = [];
}

$entitiesFR = array_values($entitiesFR);

function normalizeExtLinks(array $entry): array
{
  $raw = $entry['extlinks'] ?? $entry['extlink'] ?? [];
  if (empty($raw)) return [];
  if (isset($raw[0]) && is_array($raw[0])) return $raw;
  if (isset($raw['title']) || isset($raw['uri'])) return [$raw];
  return $raw;
}

$hasData = false;
foreach ($entitiesFR as $i => $funderFR) {
  $funderEN = $entitiesEN[$i] ?? [];
  $nameFR   = trim($funderFR['name'] ?? '');
  $nameEN   = trim($funderEN['name'] ?? '');
  $typeFR   = trim($additionalFR[$i] ?? '');
  $typeEN   = trim($additionalEN[$i] ?? '');
  $linksFR  = normalizeExtLinks($funderFR);
  $linksEN  = normalizeExtLinks($funderEN);

  $hasLinks = !empty(array_filter(
    array_merge($linksFR, $linksEN),
    fn($l) =>
    !empty(trim($l['uri'] ?? '')) || !empty(trim($l['title'] ?? ''))
  ));

  if ($nameFR || $nameEN || $typeFR || $typeEN || $hasLinks) {
    $hasData = true;
    break;
  }
}
?>

<?php
if (!function_exists('normalizeUrl')) {
  function normalizeUrl($url)
  {
    if (!empty($url) && !preg_match('#^https?://#', $url)) {
      return 'http://' . $url;
    }
    return $url;
  }
}
?>

<?php if ($hasData): ?>
  <div class="submenu-study" id="financeur">
    <h2 class="field-content__h1 lang-text"
      data-fr="Financeur"
      data-en="Funder">
      Financeur
    </h2>

    <?php
    $maxCards = max(
      count($entitiesFR),
      count($entitiesEN),
      count($additionalFR),
      count($additionalEN),
      count($additionalOFATFR),
      count($additionalOFATEN)
    );

    for ($i = 0; $i < $maxCards; $i++):
      $num     = $i + 1;
      $funderFR = $entitiesFR[$i] ?? [];
      $funderEN = $entitiesEN[$i] ?? [];

      $nameFR      = trim($funderFR['name'] ?? '');
      $nameEN      = trim($funderEN['name'] ?? '');
      $typeFR      = trim($additionalFR[$i] ?? '');
      $typeEN      = trim($additionalEN[$i] ?? '');
      $otherTypeFR = trim($additionalOFATFR[$i] ?? '');
      $otherTypeEN = trim($additionalOFATEN[$i] ?? '');

      // ── Tous les extlinks
      $extLinksFR = normalizeExtLinks($funderFR);
      $extLinksEN = normalizeExtLinks($funderEN);

      $hasLinks = !empty(array_filter(
        array_merge($extLinksFR, $extLinksEN),
        fn($l) =>
        !empty(trim($l['uri'] ?? '')) || !empty(trim($l['title'] ?? ''))
      ));

      if (!($nameFR || $nameEN || $typeFR || $typeEN || $hasLinks)) continue;
    ?>

      <div class="field-card" style="margin-bottom: 10px;">
        <div class="field-card__header lang-text"
          data-fr="Financeur <?= $num; ?>"
          data-en="Funder <?= $num; ?>">
          Financeur <?= $num; ?>
        </div>

        <div class="field-card__body">

          <?php
          if ($nameFR || $nameEN): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="Nom du financeur"
                data-en="Funder name">
                Nom du financeur
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= nl2br(esc_html($nameFR)); ?>"
                data-en="<?= nl2br(esc_html($nameEN)); ?>">
                <?= nl2br(esc_html($nameFR)); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php
          if ($typeFR || $typeEN): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="Type de financeur"
                data-en="Funder type">
                Type de financeur
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= esc_html($typeFR); ?>"
                data-en="<?= esc_html($typeEN); ?>">
                <?= esc_html($typeFR); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php
          $showOther = (strtolower($typeFR) === 'autre' && $otherTypeFR)
            || (strtolower($typeEN) === 'other' && $otherTypeEN);
          if ($showOther): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="Autre type de financeur, précisions"
                data-en="Other funder type, details">
                Autre type de financeur, précisions
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= esc_html($otherTypeFR); ?>"
                data-en="<?= esc_html($otherTypeEN); ?>">
                <?= esc_html($otherTypeFR ?: $otherTypeEN); ?>
              </div>
            </div>
          <?php endif; ?>

            <?php
            $maxLinks = max(count($extLinksFR), count($extLinksEN));

            $hasIdentifiers = false;
            $identifiers = [];

            for ($ei = 0; $ei < $maxLinks; $ei++):
                $lFr     = $extLinksFR[$ei] ?? [];
                $lEn     = $extLinksEN[$ei] ?? [];
                $titleFr = trim($lFr['title'] ?? ($lEn['title'] ?? ''));
                $titleEn = trim($lEn['title'] ?? $titleFr);
                $uriFr   = trim($lFr['uri']   ?? ($lEn['uri'] ?? ''));
                $uriEn   = trim($lEn['uri']   ?? $uriFr);

                if (!$titleFr && !$titleEn && !$uriFr && !$uriEn) continue;

                $identifiers[] = [
                        'titleFr' => $titleFr,
                        'titleEn' => $titleEn,
                        'uriFr'   => $uriFr,
                        'uriEn'   => $uriEn,
                        'index'   => $ei
                ];
                $hasIdentifiers = true;
            endfor;
            ?>

            <?php if ($hasIdentifiers): ?>
                <div class="field-bloc mb-2">
                    <div class="field-bloc__title lang-text"
                         data-fr="Identifiant du financeur"
                         data-en="Funder identifier">
                        Identifiant du financeur
                    </div>
                    <div class="field-bloc__value">
                        <?php foreach ($identifiers as $identifier):
                            $titleFr = $identifier['titleFr'];
                            $titleEn = $identifier['titleEn'];
                            $uriFr   = $identifier['uriFr'];
                            $uriEn   = $identifier['uriEn'];
                            $ei      = $identifier['index'];

                            $isROR   = strtoupper($titleFr) === 'ROR';
                            $isSIREN = strtoupper($titleFr) === 'SIREN';
                            ?>
                            <div style="margin-bottom: 8px;">
                              <span class="identifier-type lang-text"
                                    data-fr="<?= esc_html($titleFr); ?>"
                                    data-en="<?= esc_html($titleEn); ?>">
                                <?= esc_html($currentLang === 'fr' ? $titleFr : $titleEn); ?>
                              </span>:

                                <?php if ($isROR): ?>
                                    <a href="<?= esc_attr(normalizeUrl($uriFr)); ?>" target="_blank">
                                        <?= esc_html($uriFr); ?>
                                    </a>
                                <?php elseif ($isSIREN): ?>
                                    <span id="siren-value-<?= $i; ?>-<?= $ei; ?>"><?= esc_html($uriFr); ?></span>
                                    <span onclick="copySiren('siren-value-<?= $i; ?>-<?= $ei; ?>')" title="Copier" style="cursor:pointer;">🗐</span>
                                <?php else: ?>
                                    <span class="lang-text"
                                          data-fr="<?= esc_html($uriFr); ?>"
                                          data-en="<?= esc_html($uriEn); ?>">
              <?= esc_html($currentLang === 'fr' ? $uriFr : $uriEn); ?>
            </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
      </div>
    <?php endfor; ?>
  </div>
<?php endif; ?>

<?php
// Extract sponsor data
$producersFR = $jsonRec["fr"]["study_desc"]["production_statement"]["producers"] ?? [];
$producersEN = $jsonRec["en"]["study_desc"]["production_statement"]["producers"] ?? [];

$sponsorTypeFR = $jsonRec["fr"]["additional"]["sponsor"]["sponsorType"] ?? [];
$sponsorTypeEN = $jsonRec["en"]["additional"]["sponsor"]["sponsorType"] ?? [];

$otherSponsorTypeFR = $jsonRec["fr"]["additional"]["sponsor"]["otherSponsorType"] ?? [];
$otherSponsorTypeEN = $jsonRec["en"]["additional"]["sponsor"]["otherSponsorType"] ?? [];

// Filter arrays
if (is_array($sponsorTypeFR)) {
  $sponsorTypeFR = array_values(array_filter($sponsorTypeFR, fn($v) => trim((string)$v) !== ''));
} else {
  $sponsorTypeFR = [];
}

if (is_array($sponsorTypeEN)) {
  $sponsorTypeEN = array_values(array_filter($sponsorTypeEN, fn($v) => trim((string)$v) !== ''));
} else {
  $sponsorTypeEN = [];
}

// Ensure arrays
if (!is_array($producersFR)) $producersFR = [];
if (!is_array($producersEN)) $producersEN = [];
if (!is_array($sponsorTypeFR)) $sponsorTypeFR = [];
if (!is_array($sponsorTypeEN)) $sponsorTypeEN = [];
if (!is_array($otherSponsorTypeFR)) $otherSponsorTypeFR = [];
if (!is_array($otherSponsorTypeEN)) $otherSponsorTypeEN = [];

// Filter producers
$producersFR = array_values(array_filter($producersFR, function ($item) {
  return !empty($item['name']) || !empty($item['extlink']['title']) || !empty($item['extlink']['uri']);
}));

// Pre-check for data presence
$hasSponsorData = count($producersFR) > 0;
foreach ($producersFR as $i => $producerFR) {
  $producerEN = $producersEN[$i] ?? [];

  $nameFR  = trim($producerFR['name'] ?? '');
  $typeFR  = trim($sponsorTypeFR[$i] ?? '');
  $titleFR = trim($producerFR['extlink']['title'] ?? '');
  $uriFR   = trim($producerFR['extlink']['uri'] ?? '');

  $nameEN  = trim($producerEN['name'] ?? '');
  $typeEN  = trim($sponsorTypeEN[$i] ?? '');
  $titleEN = trim($producerEN['extlink']['title'] ?? '');
  $uriEN   = trim($producerEN['extlink']['uri'] ?? '');

  if ($nameFR || $typeFR || $titleFR || $uriFR || $nameEN || $typeEN || $titleEN || $uriEN) {
    $hasSponsorData = true;
    break;
  }
}

// Committee logic
$committeeRaw = strtolower(trim($jsonRec[$currentLang]["additional"]["governance"]["committee"] ?? ''));
$showCommitteeYes = in_array($committeeRaw, ['yes', 'oui']);
$showCommitteeOther = in_array($committeeRaw, ['autre', 'other']);

// Committee details
$committeeDetailsFr = trim($jsonRec["fr"]["study_desc"]["study_info"]["quality_statement"]["standards"][0]["committee"] ?? '');
$committeeDetailsEn = trim($jsonRec["en"]["study_desc"]["study_info"]["quality_statement"]["standards"][0]["committee"] ?? '');
$committeeOtherFr = trim($jsonRec["fr"]["study_desc"]["study_info"]["quality_statement"]["standards"][0]["governance"] ?? '');
$committeeOtherEn = trim($jsonRec["en"]["study_desc"]["study_info"]["quality_statement"]["standards"][0]["governance"] ?? '');

// Network/consortium logic
$networkConsortiumRaw = strtolower(trim($jsonRec[$currentLang]["additional"]["collaborations"]["networkConsortium"] ?? ''));
$showConsortium = in_array($networkConsortiumRaw, ['true', '1', 'yes', 'oui']);
// Collaboration
$othIdFr = $jsonRec["fr"]["study_desc"]["oth_id"] ?? [];
$othIdEn = $jsonRec["en"]["study_desc"]["oth_id"] ?? [];
// Fonction pour extraire text collaboration du tableau oth_id
function extractCollaborations($othIdArray)
{
  $collaborations = '';
  if (is_array($othIdArray)) {
    foreach ($othIdArray as $item) {
      if (isset($item['type']) && $item['type'] === 'collaboration') {
        if (!empty($item['name'])) {
          $collaborations = trim($item['name']);
        }
      }
    }
  }
  return $collaborations;
}

// Extraire collaborations text fr et en
$collaborationFr = extractCollaborations($othIdFr);
$collaborationEn = extractCollaborations($othIdEn);

// Check if we have any data at all before showing section
$hasData = $hasSponsorData || $committeeRaw || $networkConsortiumRaw;
?>

<?php if ($hasData): ?>
  <div class="submenu-study" id="organisation">
    <h2 class="field-content__h1 lang-text"
      data-fr="Organisation et gouvernance"
      data-en="Organization and governance">
      Organisation et gouvernance
    </h2>

    <?php
    $maxCards = max(
      count($producersFR),
      count($producersEN),
      count($sponsorTypeFR),
      count($sponsorTypeEN),
      count($otherSponsorTypeFR),
      count($otherSponsorTypeEN)
    );

    for ($i = 0; $i < $maxCards; $i++):
      $num = $i + 1;
      $producerFR = $producersFR[$i] ?? [];
      $producerEN = $producersEN[$i] ?? [];

      $nameFR = trim($producerFR['name'] ?? '');
      $nameEN = trim($producerEN['name'] ?? '');

      $typeFR = trim($sponsorTypeFR[$i] ?? '');
      $typeEN = trim($sponsorTypeEN[$i] ?? '');

      $otherTypeFR = trim($otherSponsorTypeFR[$i] ?? '');
      $otherTypeEN = trim($otherSponsorTypeEN[$i] ?? '');

      $extlinksFR = $producerFR['extlink'] ?? [];
      $extlinksEN = $producerEN['extlink'] ?? [];

      // Convert single extlink object to array
      if (is_array($extlinksFR) && isset($extlinksFR['title']) && isset($extlinksFR['uri'])) {
        $extlinksFR = [$extlinksFR];
      } elseif (!is_array($extlinksFR)) {
        $extlinksFR = [];
      }

      if (is_array($extlinksEN) && isset($extlinksEN['title']) && isset($extlinksEN['uri'])) {
        $extlinksEN = [$extlinksEN];
      } elseif (!is_array($extlinksEN)) {
        $extlinksEN = [];
      }

      // Ignorer si tout est vide
      if (!($nameFR || $typeFR || (!empty($extlinksFR)) || $nameEN || $typeEN || (!empty($extlinksEN)))) continue;
    ?>

      <div class="field-card" style="margin-bottom: 10px;">
        <div class="field-card__header lang-text"
          data-fr="Promoteur <?= $num; ?>"
          data-en="Sponsor <?= $num; ?>">
          Promoteur <?= $num; ?>
        </div>

        <div class="field-card__body">

          <?php if ($nameFR): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="Nom du promoteur"
                data-en="Sponsor name">
                Nom du promoteur
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= nl2br(esc_html($nameFR)); ?>"
                data-en="<?= nl2br(esc_html($nameEN)); ?>">
                <?= nl2br(esc_html($nameFR)); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($typeFR): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="Statut du promoteur"
                data-en="Sponsor type">
                Statut du promoteur
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= esc_html($typeFR); ?>"
                data-en="<?= esc_html($typeEN); ?>">
                <?= esc_html($typeFR); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php
          $showOther = (strtolower($typeFR) === 'autre' && $otherTypeFR)
            || (strtolower($typeEN) === 'other' && $otherTypeEN);
          if ($showOther): ?>
            <div class="field-bloc mb-2">
              <div class="field-bloc__title lang-text"
                data-fr="Autre statut du promoteur, précisions"
                data-en="Other sponsor type, details">
                Autre statut du promoteur, précisions
              </div>
              <div class="field-bloc__value lang-text"
                data-fr="<?= esc_html($otherTypeFR); ?>"
                data-en="<?= esc_html($otherTypeEN); ?>">
                <?= esc_html($otherTypeFR ?: $otherTypeEN); ?>
              </div>
            </div>
          <?php endif; ?>

            <?php
            // Display all extlinks
            if (!empty($extlinksFR)):
                ?>
                <div class="field-bloc mb-2">
                    <div class="field-bloc__title lang-text"
                         data-fr="Identifiant du promoteur"
                         data-en="Sponsor identifier">
                        Identifiant du promoteur
                    </div>
                    <div class="field-bloc__value">
                        <?php
                        foreach ($extlinksFR as $linkIndex => $linkFR):
                            $linkEN = $extlinksEN[$linkIndex] ?? [];

                            $titleFR = trim($linkFR['title'] ?? '');
                            $titleEN = trim($linkEN['title'] ?? '');

                            $uriFR = trim($linkFR['uri'] ?? '');
                            $uriEN = trim($linkEN['uri'] ?? '');

                            if (!$titleFR || !$uriFR) continue;
                            ?>
                            <div style="margin-bottom: 8px;">
                                <span class="identifier-type"><?= esc_html($titleFR); ?>:</span>

                                <?php if ($titleFR === 'ROR'): ?>
                                    <a href="<?= esc_attr($uriFR); ?>" target="_blank"><?= esc_html($uriFR); ?></a>
                                <?php elseif ($titleFR === 'SIREN'): ?>
                                    <span id="siren-value-<?= $i; ?>-<?= $linkIndex; ?>"><?= esc_html($uriFR); ?></span>
                                    <span onclick="copySiren('siren-value-<?= $i; ?>-<?= $linkIndex; ?>')" title="Copier" style="cursor:pointer;">🗐</span>
                                <?php else: ?>
                                    <span><?= esc_html($uriFR); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php
                        endforeach;
                        ?>
                    </div>
                </div>
            <?php
            endif;
            ?>

        </div>
      </div>
    <?php endfor; ?>

    <?php if ($committeeRaw): ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text"
          data-fr="Comité scientifique ou de pilotage"
          data-en="Scientific or steering committee">
          Comité scientifique ou de pilotage
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?= $showCommitteeYes ? 'Oui' : ($showCommitteeOther ? 'Autre' : 'Non'); ?>"
          data-en="<?= $showCommitteeYes ? 'Yes' : ($showCommitteeOther ? 'Other' : 'No'); ?>">
          <?= $showCommitteeYes ? 'Oui' : ($showCommitteeOther ? 'Autre' : 'Non'); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php
    // Afficher "Comité, précisions" si Oui / Yes
    if ($showCommitteeYes && !empty($committeeDetailsFr)) : ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text"
          data-fr="Comité, précisions"
          data-en="Committee details">
          Comité, précisions
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?= nl2br(esc_html($committeeDetailsFr)); ?>"
          data-en="<?= nl2br(esc_html($committeeDetailsEn)); ?>">
          <?= nl2br(esc_html($committeeDetailsFr)); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php
    // Afficher "Autre, précisions" si valeur = Autre / Other
    if ($showCommitteeOther && !empty($committeeOtherFr)) : ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text"
          data-fr="Autre, précisions"
          data-en="Other details">
          Autre, précisions
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?= nl2br(esc_html($committeeOtherFr)); ?>"
          data-en="<?= nl2br(esc_html($committeeOtherEn)); ?>">
          <?= nl2br(esc_html($committeeOtherFr)); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($networkConsortiumRaw): ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text"
          data-fr="Réseaux, consortiums"
          data-en="Networks, consortia">
          Réseaux, consortiums
        </div>
        <div class="field-bloc__value lang-text"
          data-fr="<?= $showConsortium ? 'Oui' : 'Non'; ?>"
          data-en="<?= $showConsortium ? 'Yes' : 'No'; ?>">
          <?= $showConsortium ? 'Oui' : 'Non'; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($showConsortium): ?>
      <div class="field-bloc mb-2">
        <div class="field-bloc__title lang-text"
          data-fr="Collaboration(s) impliquée(s), précisions"
          data-en="Collaboration(s) involved, details">Collaboration(s) impliquée(s), précisions</div>
        <div class="field-bloc__value lang-text"
          data-fr="<?= esc_html($collaborationFr); ?>"
          data-en="<?= esc_html($collaborationEn); ?>">
          <?= $collaborationFr; ?>
        </div>
      </div>
    <?php endif; ?>
  </div><!-- /.submenu-study -->
<?php endif; ?>
<?php
$datasetIdnoArrFr = $jsonRec['fr']['study_desc']['title_statement']['IDno']['metadata_no'] ?? [];
$datasetIdnoArrEn = $jsonRec['en']['study_desc']['title_statement']['IDno']['metadata_no'] ?? [];

if (!is_array($datasetIdnoArrFr)) $datasetIdnoArrFr = [];
if (!is_array($datasetIdnoArrEn)) $datasetIdnoArrEn = [];

$datasetIdnoArrFr = array_values(array_filter($datasetIdnoArrFr, function ($item) {
  return !empty($item['agency']) || !empty($item['code']);
}));
$datasetIdnoArrEn = array_values(array_filter($datasetIdnoArrEn, function ($item) {
  return !empty($item['agency']) || !empty($item['code']);
}));

$maxDataset = max(count($datasetIdnoArrFr), count($datasetIdnoArrEn));
?>

<?php if ($maxDataset > 0): ?>
  <div class="submenu-study" id="otherStudy">
    <h2 class="field-content__h1 lang-text"
      data-fr="Autre(s) identifiant(s) de l'étude"
      data-en="Other(s) identifier(s) for the study">
      Autre(s) identifiant(s) de l'étude
    </h2>

    <div class="field-bloc submenu-study-table">

      <div class="field-bloc__value">
        <table class="field-table">
          <thead>
            <tr>
              <th class="lang-text"
                data-fr="Type d'identifiant de l'étude"
                data-en="Study identifier type">
                Type d'identifiant de l'étude
              </th>
              <th class="lang-text"
                data-fr="Identifiant de l'étude"
                data-en="Study identifier">
                Identifiant de l'étude
              </th>
            </tr>
          </thead>

          <tbody>
            <?php for ($i = 0; $i < $maxDataset; $i++): ?>
              <?php
              $frItem = $datasetIdnoArrFr[$i] ?? [];
              $enItem = $datasetIdnoArrEn[$i] ?? [];

              $frAgency = trim($frItem['agency'] ?? '');
              $enAgency = trim($enItem['agency'] ?? '');

              $frCode = trim($frItem['code'] ?? '');
              $enCode = trim($enItem['code'] ?? '');

              if ($frAgency === '' && $frCode === '' && $enAgency === '' && $enCode === '') {
                continue;
              }
              ?>
              <tr>
                <td class="lang-text"
                  data-fr="<?= nl2br(esc_attr($frAgency)); ?>"
                  data-en="<?= nl2br(esc_attr($enAgency)); ?>">
                  <?= nl2br(esc_html($frAgency)); ?>
                </td>
                <td class="lang-text"
                  data-fr="<?= nl2br(esc_attr($frCode)); ?>"
                  data-en="<?= nl2br(esc_attr($enCode)); ?>">
                  <?= nl2br(esc_html($frCode)); ?>
                </td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<script>
    function copySiren(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            navigator.clipboard.writeText(element.innerText);
        }
    }
</script>
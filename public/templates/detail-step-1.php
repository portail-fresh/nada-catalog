<div class="tab-pane show active" id="detaill-1">
  <div class="row">
    <div class="col-md-4 leftsidebar">
      <div class="navStickyBar">
        <ul class="navbar-nav flex-column wb--full-width">
          <li class="nav-item">
            <a href="#administrative" class="lang-text" data-fr="Renseignements administratifs" data-en="Administrative information">Renseignements administratifs</a>
          </li>
          <li class="nav-item">
            <a href="#investigateur" class="lang-text" data-fr="Investigateur principal" data-en="Principal investigator">Investigateur principal</a>
          </li>
          <li class="nav-item">
            <a href="#membre" class="lang-text" data-fr="Membre de l'équipe de recherche" data-en="Research team member">Membre de l'équipe de recherche</a>
          </li>
          <li class="nav-item">
            <a href="#contact" class="lang-text" data-fr="Points de contact" data-en="Contact points">Points de contact</a>
          </li>
          <li class="nav-item">
            <a href="#financeur" class="lang-text" data-fr="Financeur" data-en="Funder">Financeur</a>
          </li>
          <li class="nav-item">
            <a href="#organisation" class="lang-text" data-fr="Organisation et gouvernance" data-en="Organization and governance">Organisation et gouvernance</a>
          </li>
          <li class="nav-item">
            <a href="#statut" class="lang-text" data-fr="Statut de l'étude" data-en="Study status">Statut de l'étude</a>
          </li>
          <li class="nav-item">
            <a href="#thematique" class="lang-text" data-fr="Thématique" data-en="Theme">Thématique</a>
          </li>
        </ul>
      </div>
    </div>
    <?php $langs = ['fr', 'en']; ?>
    <div class="col-md-8">
      <div class="submenu-study" id="administrative">
        <div class="field-content__h1 lang-text" data-fr="Général" data-en="General">
          Général
        </div>
        <?php
        // Get title values
        $titleFr = $jsonRec['fr']['study_desc']['title_statement']['title'] ?? '';
        $titleEn = $jsonRec['en']['study_desc']['title_statement']['title'] ?? '';
        if (($titleFr) || ($titleEn)): 
        ?>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Titre de l'étude" data-en="Title">Titre de l'étude</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($titleFr); ?>"
            data-en="<?php echo esc_html($titleEn); ?>">
            <?php echo esc_html($jsonRec[$currentLang]['study_desc']['title_statement']['title']); ?>
          </div>
        </div>
        <?php endif; ?>
        <?php
        // Get acronym values
        $acronymeFr = $jsonRec['fr']['study_desc']['title_statement']['alternate_title'] ?? '';
        $acronymeEn = $jsonRec['en']['study_desc']['title_statement']['alternate_title'] ?? '';
        if (($acronymeFr) || ($acronymeEn)): 
        ?>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Acronyme" data-en="Acronym">Acronyme</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($acronymeFr); ?>"
            data-en="<?php echo esc_html($acronymeEn); ?>">
            <?php echo esc_html($jsonRec[$currentLang]['study_desc']['title_statement']['alternate_title']); ?>
          </div>
        </div>
        <?php endif; ?>
        <?php
        // Préparer les données pour les autorités
        $agenciesFr = $jsonRec['fr']['study_desc']['study_authorization']['agency'] ?? [];
        $agenciesEn = $jsonRec['en']['study_desc']['study_authorization']['agency'] ?? [];
        $otherFr = $jsonRec['fr']['additional']['obtainedAuthorization']['otherAuthorizingAgency'] ?? [];
        $otherEn = $jsonRec['en']['additional']['obtainedAuthorization']['otherAuthorizingAgency'] ?? [];

        $max = max(count($agenciesFr), count($agenciesEn));

        // Vérifier si on a au moins une autorité valide
        $hasAuthorities = false;
        for ($i = 0; $i < $max; $i++) {
          $nameFr = $agenciesFr[$i]['name'] ?? '';
          $nameEn = $agenciesEn[$i]['name'] ?? '';
          $otherFrVal = $otherFr[$i] ?? '';
          $otherEnVal = $otherEn[$i] ?? '';
          
          if (!empty($nameFr) || !empty($nameEn) || !empty($otherFrVal) || !empty($otherEnVal)) {
            $hasAuthorities = true;
            break;
          }
        }
        ?>

        <?php if ($hasAuthorities): ?>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" 
              data-fr="Autorités compétentes" 
              data-en="Competent authorities">
            Autorités compétentes
          </div>
          <div class="field-bloc__value">
            <ul class="authorities-list">
              <?php
              for ($i = 0; $i < $max; $i++) {
                $nameFr = $agenciesFr[$i]['name'] ?? '';
                $nameEn = $agenciesEn[$i]['name'] ?? '';
                $otherFrVal = $otherFr[$i] ?? '';
                $otherEnVal = $otherEn[$i] ?? '';
                
                // Afficher seulement si au moins une valeur existe
                if (!empty($nameFr) || !empty($nameEn) || !empty($otherFrVal) || !empty($otherEnVal)) {
                  $currentName = $jsonRec[$currentLang]['study_desc']['study_authorization']['agency'][$i]['name'] ?? '';
                  $currentOther = $jsonRec[$currentLang]['additional']['obtainedAuthorization']['otherAuthorizingAgency'][$i] ?? '';
                  
                  echo "<li class='lang-row' 
                          data-fr-name='" . esc_attr($nameFr) . "' 
                          data-en-name='" . esc_attr($nameEn) . "' 
                          data-fr-other='" . esc_attr($otherFrVal) . "' 
                          data-en-other='" . esc_attr($otherEnVal) . "'>";
                  
                  // Afficher le nom de l'autorité
                  if (!empty($currentName)) {
                    echo esc_html($currentName);
                  }      
                  echo "</li>";
                }
              }
              ?>
            </ul>
          </div>
        </div>
        <?php endif; ?>
        <!-- <h2 class="field-content__h2 lang-text" data-fr="Pré-requis réglementaires" data-en="Regulatory requirements">
          Pré-requis réglementaires
        </h2>
        <h3 class="field-content__h3 lang-text" data-fr="Autorisations ou avis obtenus" data-en="Authorizations or approvals obtained">
          Autorisations ou avis obtenus
        </h3>
        <table class="field-table">
          <thead>
            <tr>
              <th class="lang-text" data-fr="Autorité competente" data-en="Competent authority">Autorité competente</th>
              <th class="lang-text" data-fr="Autre, précision" data-en="Others">Autre, précision</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $agenciesFr = $jsonRec['fr']['study_desc']['study_authorization']['agency'] ?? [];
            $agenciesEn = $jsonRec['en']['study_desc']['study_authorization']['agency'] ?? [];
            $otherFr = $jsonRec['fr']['additional']['obtainedAuthorization']['otherAuthorizingAgency'] ?? [];
            $otherEn = $jsonRec['en']['additional']['obtainedAuthorization']['otherAuthorizingAgency'] ?? [];

            $max = max(count($agenciesFr), count($agenciesEn));

            for ($i = 0; $i < $max; $i++) {
              $nameFr = $agenciesFr[$i]['name'] ?? '-';
              $nameEn = $agenciesEn[$i]['name'] ?? '-';
              $otherFrVal = $otherFr[$i] ?? '-';
              $otherEnVal = $otherEn[$i] ?? '-';

              echo "<tr class='lang-row'
              data-fr-name='" . esc_attr($nameFr) . "'
              data-en-name='" . esc_attr($nameEn) . "'
              data-fr-other='" . esc_attr($otherFrVal) . "'
              data-en-other='" . esc_attr($otherEnVal) . "'>";
              echo "<td>" . esc_html($jsonRec[$currentLang]['study_desc']['study_authorization']['agency'][$i]['name'] ?? '-') . "</td>";
              echo "<td>" . esc_html($jsonRec[$currentLang]['additional']['obtainedAuthorization']['otherAuthorizingAgency'][$i] ?? '-') . "</td>";
              echo "</tr>";
            }
            ?>
          </tbody>
        </table> -->
        <?php
        // Get conformity values
        $conformityFr = $jsonRec['fr']['additional']['regulatoryRequirements']['conformityDeclaration'] ?? '';
        $conformityEn = $jsonRec['en']['additional']['regulatoryRequirements']['conformityDeclaration'] ?? '';
        // Show block only if has a value
        if (($conformityFr) || ($conformityEn)): 
        ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text" 
                data-fr="Déclaration de conformité" 
                data-en="Declaration of conformity">
              Déclaration de conformité
            </div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($conformityFr); ?>"
              data-en="<?php echo esc_html($conformityEn); ?>">
              <?php echo esc_html($jsonRec[$currentLang]['additional']['regulatoryRequirements']['conformityDeclaration'] ?? ''); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="submenu-study" id="investigateur">
        <div class="field-content__h1 lang-text" data-fr="Investigateur principal" data-en="Principal investigator">
          Investigateur principal
        </div>

        <?php
        $currentuserpi = strtolower(trim($isCurrentUserPi ?? ''));
        $isNo = in_array($currentuserpi, ['false', '0', 'no', 'non']);
        $valueFr = $isNo ? 'Non' : 'Oui';
        $valueEn = $isNo ? 'No'  : 'Yes';
        ?>
       

        <?php if ($isNo): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text" data-fr="Email PI" data-en="PI mail">Email PI</div>
            <div class="field-bloc__value">
              <?php echo esc_html($piEmail); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php
        $entitiesFR = $jsonRec["fr"]["study_desc"]["authoring_entity"];
        $entitiesEN = $jsonRec["en"]["study_desc"]["authoring_entity"];

        $chunksFR = array_chunk($entitiesFR, 2);
        $chunksEN = array_chunk($entitiesEN, 2);
        foreach ($chunksFR as $i => $pairFR):
          $num = $i + 1;
          $investigatorFR = $pairFR[0];
          $affiliationFR  = $pairFR[1] ?? null;

          $pairEN = $chunksEN[$i] ?? [];
          $investigatorEN = $pairEN[0] ?? [];
          $affiliationEN  = $pairEN[1] ?? null;
        ?>
          <div class="field-card" data-toggle-bloc="collapse-<?php echo $num; ?>" style="margin-bottom: 10px;">
            <div class="field-card__header  lang-text" data-toggle-item="collapse-<?php echo $num; ?>" data-fr="Investigateur principal <?php echo $num; ?>" data-en="Principal investigator <?php echo $num; ?>">
              Investigateur principal <?php echo $num; ?>
            </div>

            <div class="field-card__body" data-toggle-content="collapse-<?php echo $num; ?>">
              <!-- Investigator -->
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Prénom NOM de l'investigateur principal" data-en="PI name">Prénom NOM de l'investigateur principal</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($investigatorFR['name']); ?>"
                  data-en="<?php echo esc_html($investigatorEN['name']); ?>">
                  <?php echo esc_html($investigatorFR['name']); ?>
                </div>
              </div>
              <h4 class="field-content__h4 lang-text" data-fr="Identification de l'investigateur principal" data-en="Identification of the principal investigator">
                Identification de l'investigateur principal
              </h4>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Type d'identifiant de l'investigateur principal" data-en="Investigator identifier type">Type d'identifiant de l'investigateur principal </div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($investigatorFR['extlink']['title']); ?>"
                  data-en="<?php echo esc_html($investigatorEN['extlink']['title']); ?>">
                  <?php echo esc_html($investigatorFR['extlink']['title']); ?>
                </div>
              </div>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Identifiant de l'investigateur principal" data-en="Investigator identifier">Identifiant de l'investigateur principal</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_url($investigatorFR['extlink']['uri']); ?>"
                  data-en="<?php echo esc_url($investigatorEN['extlink']['uri']); ?>">
                  <?php echo esc_url($investigatorFR['extlink']['uri']); ?>
                </div>
              </div>

              <!-- Affiliation -->
              <h4 class="field-content__h4 lang-text" data-fr="Affiliation de l'investigateur principal" data-en="Affiliation of the principal investigator">
                Affiliation de l'investigateur principal
              </h4>

              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Nom de l'organisation" data-en="Organisation name">Nom de l'organisation</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($affiliationFR['name']); ?>"
                  data-en="<?php echo esc_html($affiliationEN['name']); ?>">
                  <?php echo esc_html($affiliationFR['name']); ?>
                </div>
              </div>

              <h5 class="field-content__h5 lang-text" data-fr="Identification de l'organisme" data-en="Identification of the organization">
                Identification de l'organisme
              </h5>

              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Type d'identifiant organisation" data-en="Type of identification">Type d'identifiant organisation</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($affiliationFR['extlink']['title']); ?>"
                  data-en="<?php echo esc_html($affiliationEN['extlink']['title']); ?>">
                  <?php echo esc_html($affiliationFR['extlink']['title']); ?>
                </div>
              </div>

              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="URI de l'organisation" data-en="Organisation URI">URI de l'organisation</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_url($affiliationFR['extlink']['uri']); ?>"
                  data-en="<?php echo esc_url($affiliationEN['extlink']['uri']); ?>">
                  <?php echo esc_url($affiliationFR['extlink']['uri']); ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="submenu-study" id="membre">
        <div class="field-content__h1 lang-text" data-fr="Membre de l'équipe de recherche" data-en="Research team member">
          Membre de l'équipe de recherche
        </div>
        <?php
        $entitiesFR = $jsonRec["fr"]["study_desc"]["oth_id"];
        $entitiesEN = $jsonRec["en"]["study_desc"]["oth_id"];

        $chunksFR = array_chunk($entitiesFR, 2);
        $chunksEN = array_chunk($entitiesEN, 2);

        foreach ($chunksFR as $i => $pairFR):
          $num = $i + 1;
          $contributorFR = $pairFR[0];
          $affiliationFR  = $pairFR[1] ?? null;

          $pairEN = $chunksEN[$i] ?? [];
          $contributorEN = $pairEN[0] ?? [];
          $affiliationEN  = $pairEN[1] ?? null;
        ?>
          <div class="field-card" data-toggle-bloc="collapse-<?php echo $num; ?>" style="margin-bottom: 10px;">
            <div class="field-card__header  lang-text" data-toggle-item="collapse-<?php echo $num; ?>" data-fr="Membre de l'équipe de recherche <?php echo $num; ?>" data-en="Research team member <?php echo $num; ?>">
              Membre de l'équipe de recherche <?php echo $num; ?>
            </div>

            <div class="field-card__body" data-toggle-content="collapse-<?php echo $num; ?>">
              <!-- contributor -->
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Prénom NOM du membre de l’équipe" data-en="Last name – First name">Prénom NOM du membre de l’équipe</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($contributorFR['name']); ?>"
                  data-en="<?php echo esc_html($contributorEN['name']); ?>">
                  <?php echo esc_html($contributorFR['name']); ?>
                </div>
              </div>
              <h4 class="field-content__h4 lang-text" data-fr="Identification du membre de l’équipe" data-en="Identification of the team member">
                Identification du membre de l’équipe
              </h4>

              <?php
              $extlinksFr = $contributorFR['extlinks'] ?? [];
              $extlinksEn = $contributorEN['extlinks'] ?? [];
              $max = max(count($extlinksFr), count($extlinksEn));
              ?>

              <table class="field-table">
                <thead>
                  <tr>
                    <th class="lang-text" data-fr="Type d'identifiant du membre de l’équipe" data-en="Person identifier type">
                      Type d'identifiant du membre de l’équipe
                    </th>
                    <th class="lang-text" data-fr="Identifiant du membre de l’équipe" data-en="Person identifier">
                      Identifiant du membre de l’équipe
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <?php for ($j = 0; $j < $max; $j++):
                    $linkFr = $extlinksFr[$j] ?? [];
                    $linkEn = $extlinksEn[$j] ?? [];

                    $titleFr = $linkFr['title'] ?? '-';
                    $titleEn = $linkEn['title'] ?? '-';
                    $uriFr   = $linkFr['uri'] ?? '-';
                    $uriEn   = $linkEn['uri'] ?? '-';
                  ?>
                    <tr class="lang-row"
                      data-fr-title="<?php echo esc_attr($titleFr); ?>"
                      data-en-title="<?php echo esc_attr($titleEn); ?>"
                      data-fr-uri="<?php echo esc_attr($uriFr); ?>"
                      data-en-uri="<?php echo esc_attr($uriEn); ?>">
                      <td><?php echo esc_html($jsonRec[$currentLang]['study_desc']['oth_id'][$i * 2]['extlinks'][$j]['title'] ?? '-'); ?></td>
                      <td><?php echo esc_html($jsonRec[$currentLang]['study_desc']['oth_id'][$i * 2]['extlinks'][$j]['uri'] ?? '-'); ?></td>
                    </tr>
                  <?php endfor; ?>
                </tbody>
              </table>

              <!-- Affiliation -->
              <h4 class="field-content__h4 lang-text" data-fr="Affiliation du membre de l’équipe" data-en="Affiliation of the team member">
                Affiliation du membre de l’équipe
              </h4>

              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Nom de l'organisation" data-en="Organisation name">Nom de l'organisation</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($affiliationFR['name']); ?>"
                  data-en="<?php echo esc_html($affiliationEN['name']); ?>">
                  <?php echo esc_html($affiliationFR['name']); ?>
                </div>
              </div>

              <h5 class="field-content__h5 lang-text" data-fr="Identification de l'organisme" data-en="Identification of the organization">
                Identification de l'organisme
              </h5>

              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Type d'identifiant organisation" data-en="Type of identification">Type d'identifiant organisation</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($affiliationFR['extlink']['title']); ?>"
                  data-en="<?php echo esc_html($affiliationEN['extlink']['title']); ?>">
                  <?php echo esc_html($affiliationFR['extlink']['title']); ?>
                </div>
              </div>

              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="URI de l'organisation" data-en="Organisation URI">URI de l'organisation</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_url($affiliationFR['extlink']['uri']); ?>"
                  data-en="<?php echo esc_url($affiliationEN['extlink']['uri']); ?>">
                  <?php echo esc_url($affiliationFR['extlink']['uri']); ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="submenu-study" id="contact">
        <div class="field-content__h1 lang-text" data-fr="Points de contact" data-en="Contact points">
          Points de contact
        </div>

        <?php
        $entitiesFR = $jsonRec["fr"]["study_desc"]["distribution_statement"]["contact"] ?? [];
        $entitiesEN = $jsonRec["en"]["study_desc"]["distribution_statement"]["contact"] ?? [];

        $chunksFR = array_chunk($entitiesFR, 2);
        $chunksEN = array_chunk($entitiesEN, 2);


        foreach ($chunksFR as $i => $pairFR):
          $num = $i + 1;
          $contactFR = $pairFR[0] ?? null;
          $affiliationFR  = $pairFR[1] ?? null;

          $pairEN = $chunksEN[$i] ?? [];
          $contactEN = $pairEN[0] ?? null;
          $affiliationEN  = $pairEN[1] ?? null;
        ?>
          <div class="field-card" data-toggle-bloc="collapse-<?php echo $num; ?>" style="margin-bottom: 10px;">
            <div class="field-card__header  lang-text" data-toggle-item="collapse-<?php echo $num; ?>" data-fr="Point de contact <?php echo $num; ?>" data-en="Contact point <?php echo $num; ?>">
              Point de contact <?php echo $num; ?>
            </div>
            <div class="field-card__body" data-toggle-content="collapse-<?php echo $num; ?>">
              <!-- contact -->
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Prénom NOM du contact" data-en="Contact name">Prénom NOM du contact</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($contactFR['name']); ?>"
                  data-en="<?php echo esc_html($contactEN['name']); ?>">
                  <?php echo esc_html($contactFR['name']); ?>
                </div>
              </div>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Email du contact" data-en="Contact email">Email du contact</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($contactFR['email']); ?>"
                  data-en="<?php echo esc_html($contactEN['email']); ?>">
                  <?php echo esc_html($contactFR['email']); ?>
                </div>
              </div>

              <!-- Affiliation -->
              <h4 class="field-content__h4 lang-text" data-fr="Affiliation du contact" data-en="Affiliation of the contact">
                Affiliation du contact
              </h4>

              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Nom de l'organisation" data-en="Organisation name">Nom de l'organisation</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($affiliationFR['name']); ?>"
                  data-en="<?php echo esc_html($affiliationEN['name']); ?>">
                  <?php echo esc_html($affiliationFR['name']); ?>
                </div>
              </div>

              <h5 class="field-content__h5 lang-text" data-fr="Identification de l'organisme" data-en="Identification of the organization">
                Identification de l'organisme
              </h5>

              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Type d'identifiant organisation" data-en="Type of identification">Type d'identifiant organisation</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($affiliationFR['extlink']['title']); ?>"
                  data-en="<?php echo esc_html($affiliationEN['extlink']['title']); ?>">
                  <?php echo esc_html($affiliationFR['extlink']['title']); ?>
                </div>
              </div>

              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="URI de l'organisation" data-en="Organisation URI">URI de l'organisation</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_url($affiliationFR['extlink']['uri']); ?>"
                  data-en="<?php echo esc_url($affiliationEN['extlink']['uri']); ?>">
                  <?php echo esc_url($affiliationFR['extlink']['uri']); ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="submenu-study" id="financeur">
        <div class="field-content__h1 lang-text" data-fr="Financeur" data-en="Funder">
          Financeur
        </div>

        <?php
        $entitiesFR = $jsonRec["fr"]["study_desc"]["production_statement"]["funding_agencies"] ?? [];
        $entitiesEN = $jsonRec["en"]["study_desc"]["production_statement"]["funding_agencies"] ?? [];

        $additionalFR = $jsonRec["fr"]["additional"]["fundingAgent"]["fundingAgentType"] ?? [];
        $additionalEN = $jsonRec["en"]["additional"]["fundingAgent"]["fundingAgentType"] ?? [];

        foreach ($entitiesFR as $i => $funderFR):
          $num = $i + 1;
          $funderEN = $entitiesEN[$i] ?? [];

          $typeFR   = $additionalFR[$i] ?? '';
          $typeEN   = $additionalEN[$i] ?? '';
        ?>
          <div class="field-card" data-toggle-bloc="collapse-<?php echo $num; ?>" style="margin-bottom: 10px;">
            <div class="field-card__header  lang-text" data-toggle-item="collapse-<?php echo $num; ?>" data-fr="Financeur <?php echo $num; ?>" data-en="Funder <?php echo $num; ?>">
              Financeur <?php echo $num; ?>
            </div>
            <div class="field-card__body" data-toggle-content="collapse-<?php echo $num; ?>">
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Nom du financeur" data-en="Funder name">Nom du financeur</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($funderFR['name']); ?>"
                  data-en="<?php echo esc_html($funderEN['name']); ?>">
                  <?php echo esc_html($funderFR['name']); ?>
                </div>
              </div>

              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Type de financeur" data-en="Funder type">Type de financeur</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($typeFR); ?>"
                  data-en="<?php echo esc_html($typeEN); ?>">
                  <?php echo esc_html($typeFR); ?>
                </div>
              </div>

              <h4 class="field-content__h4 lang-text" data-fr="Identification du financeur" data-en="Identification of the funder">
                Identification du financeur
              </h4>

              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="Type d'identifiant du financeur" data-en="Funder identifier type">Type d'identifiant du financeur</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($funderFR['title']); ?>"
                  data-en="<?php echo esc_html($funderEN['title']); ?>">
                  <?php echo esc_html($funderFR['title']); ?>
                </div>
              </div>
              <div class="field-bloc mb-2">
                <div class="field-bloc__title lang-text" data-fr="URI du financeur" data-en="Funder URI">URI du financeur</div>
                <div class="field-bloc__value lang-text"
                  data-fr="<?php echo esc_html($funderFR['uri']); ?>"
                  data-en="<?php echo esc_html($funderEN['uri']); ?>">
                  <?php echo esc_html($funderFR['uri']); ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="submenu-study" id="organisation">
        <div class="field-content__h1 lang-text" data-fr="Organisation et gouvernance" data-en="Organization and governance">
          Organisation et gouvernance
        </div>
        <h4 class="field-content__h3 lang-text" data-fr="Promoteur/Organisme responsable" data-en="Sponsor/Responsible organization">
          Promoteur/Organisme responsable
        </h4>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Nom du promoteur" data-en="Sponsor name">Nom du promoteur</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($jsonRec["fr"]["study_desc"]["production_statement"]["producers"][0]["name"]); ?>"
            data-en="<?php echo esc_html($jsonRec["en"]["study_desc"]["production_statement"]["producers"][0]["name"]); ?>">
            <?php echo esc_html($jsonRec[$currentLang]["study_desc"]["production_statement"]["producers"][0]["name"]); ?>
          </div>
        </div>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Statut du promoteur" data-en="Sponsor type">Statut du promoteur</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($jsonRec["fr"]["additional"]["sponsor"]["sponsorType"]); ?>"
            data-en="<?php echo esc_html($jsonRec["en"]["additional"]["sponsor"]["sponsorType"]); ?>">
            <?php echo esc_html($jsonRec[$currentLang]["additional"]["sponsor"]["sponsorType"]); ?>
          </div>
        </div>
        <h4 class="field-content__h4 lang-text" data-fr="Identification du promoteur" data-en="Sponsor ID">
          Identification du promoteur
        </h4>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Type d'identifiant du promoteur" data-en="Sponsor identifier type">Type d'identifiant du promoteur</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($jsonRec["fr"]["study_desc"]["production_statement"]["producers"][0]["extlink"]["title"]); ?>"
            data-en="<?php echo esc_html($jsonRec["en"]["study_desc"]["production_statement"]["producers"][0]["extlink"]["title"]); ?>">
            <?php echo esc_html($jsonRec[$currentLang]["study_desc"]["production_statement"]["producers"][0]["extlink"]["title"]); ?>
          </div>
        </div>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="URI du promoteur" data-en="Sponsor URI">URI du promoteur</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_url($jsonRec["fr"]["study_desc"]["production_statement"]["producers"][0]["extlink"]["uri"]); ?>"
            data-en="<?php echo esc_url($jsonRec["en"]["study_desc"]["production_statement"]["producers"][0]["extlink"]["uri"]); ?>">
            <?php echo esc_url($jsonRec[$currentLang]["study_desc"]["production_statement"]["producers"][0]["extlink"]["uri"]); ?>
          </div>
        </div>
        <h4 class="field-content__h3 lang-text" data-fr="Gouvernance" data-en="Governance">
          Gouvernance
        </h4>
        <?php
        $committee = strtolower($jsonRec[$currentLang]["additional"]["governance"]["committee"] ?? '');

        $showSecond = in_array($committee, ['true', '1', 'yes']);
        $showThird  = in_array($committee, ['other', 'others']);
        ?>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Comité scientifique ou de pilotage" data-en="Scientific or steering committee">Comité scientifique ou de pilotage</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($committee); ?>"
            data-en="<?php echo esc_html($committee); ?>">
            <?php
            if ($showSecond) echo $currentLang === 'fr' ? 'Oui' : 'Yes';
            elseif ($showThird) echo $currentLang === 'fr' ? 'Autre' : 'Other';
            else echo $currentLang === 'fr' ? 'Non' : 'No';
            ?>
          </div>
        </div>
        <?php if ($showSecond): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text" data-fr="Comité, précisions" data-en="Committee details">Comité, précisions</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($jsonRec["fr"]["study_desc"]["study_info"]["quality_statement"]["standards"][0]["producer"]); ?>"
              data-en="<?php echo esc_html($jsonRec["en"]["study_desc"]["study_info"]["quality_statement"]["standards"][0]["producer"]); ?>">
              <?php echo esc_html($jsonRec[$currentLang]["study_desc"]["study_info"]["quality_statement"]["standards"][0]["producer"]); ?>
            </div>
          </div>
        <?php endif; ?>
        <?php if ($showThird): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text" data-fr="Autre, précisions" data-en="Others details">Autre, précisions</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($jsonRec["fr"]["study_desc"]["study_info"]["quality_statement"]["other_quality_statement"]); ?>"
              data-en="<?php echo esc_html($jsonRec["en"]["study_desc"]["study_info"]["quality_statement"]["other_quality_statement"]); ?>">
              <?php echo esc_html($jsonRec[$currentLang]["study_desc"]["study_info"]["quality_statement"]["other_quality_statement"]); ?>
            </div>
          </div>
        <?php endif; ?>
        <h4 class="field-content__h3 lang-text" data-fr="Collaborations" data-en="Collaborations">
          Collaborations
        </h4>
        <?php
        $committee = strtolower($jsonRec[$currentLang]["additional"]["collaborations"]["networkConsortium"] ?? '');
        $showSecond = in_array($committee, ['true', '1', 'yes']);
        ?>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Réseaux, consortiums" data-en="Networks, consortia">Réseaux, consortiums</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($committee); ?>"
            data-en="<?php echo esc_html($committee); ?>">
            <?php echo esc_html($committee); ?>
          </div>
        </div>
        <?php if ($showSecond): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text" data-fr="Précisions " data-en="Details">Précisions</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($jsonRec["fr"]["additional"]["collaborations"]["collaboration"]); ?>"
              data-en="<?php echo esc_html($jsonRec["en"]["additional"]["collaborations"]["collaboration"]); ?>">
              <?php echo esc_html($jsonRec[$currentLang]["additional"]["collaborations"]["collaboration"]); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="submenu-study" id="statut">
        <div class="field-content__h1 lang-text" data-fr="Statut de l'étude" data-en="Study status">
          Statut de l'étude
        </div>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Statut de l'étude" data-en="Study status">Statut de l'étude</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($jsonRec['fr']["study_desc"]["method"]["study_class"]); ?>"
            data-en="<?php echo esc_html($jsonRec['en']["study_desc"]["method"]["study_class"]); ?>">
            <?php echo esc_html($jsonRec[$currentLang]["study_desc"]["method"]["study_class"]); ?>
          </div>
        </div>
      </div>

      <div class="submenu-study" id="thematique">
        <div class="field-content__h1 lang-text" data-fr="Thématique" data-en="Theme">
          Thématique
        </div>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Objectifs" data-en="Objectives">Objectifs</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($jsonRec['fr']["study_desc"]["study_info"]["purpose"]); ?>"
            data-en="<?php echo esc_html($jsonRec['en']["study_desc"]["study_info"]["purpose"]); ?>">
            <?php echo esc_html($jsonRec[$currentLang]["study_desc"]["study_info"]["purpose"]); ?>
          </div>
        </div>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Résumé" data-en="Summary">Résumé</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($jsonRec['fr']["study_desc"]["study_info"]["abstract"]); ?>"
            data-en="<?php echo esc_html($jsonRec['en']["study_desc"]["study_info"]["abstract"]); ?>">
            <?php echo esc_html($jsonRec[$currentLang]["study_desc"]["study_info"]["abstract"]); ?>
          </div>
        </div>

        <?php
        $topicsFr = $jsonRec["fr"]["study_desc"]["study_info"]["topics"] ?? [];
        $topicsEn = $jsonRec['en']["study_desc"]["study_info"]["topics"] ?? [];

        // --- Spécialité médicale (health theme)
        $topicsFrHealth = array_filter($topicsFr, function ($t) {
          return isset($t['vocab']) && $t['vocab'] === "health theme";
        });
        $topicsEnHealth = array_filter($topicsEn, function ($t) {
          return isset($t['vocab']) && $t['vocab'] === "health theme";
        });

        $vocabListFrHealth = array_column($topicsFrHealth, 'topic');
        $vocabListEnHealth = array_column($topicsEnHealth, 'topic');

        $vocabFrHealth = implode(', ', $vocabListFrHealth);
        $vocabEnHealth = implode(', ', $vocabListEnHealth);

        // --- Groupe de pathologies (cim-11)
        $topicsFrCim = array_filter($topicsFr, function ($t) {
          return isset($t['vocab']) && $t['vocab'] === "cim-11";
        });
        $topicsEnCim = array_filter($topicsEn, function ($t) {
          return isset($t['vocab']) && $t['vocab'] === "cim-11";
        });

        $vocabListFrCim = array_column($topicsFrCim, 'topic');
        $vocabListEnCim = array_column($topicsEnCim, 'topic');

        $vocabFrCim = implode(', ', $vocabListFrCim);
        $vocabEnCim = implode(', ', $vocabListEnCim);

        // --- Groupe de pathologies (cim-11)
        $topicsFrDeter = array_filter($topicsFr, function ($t) {
          return isset($t['vocab']) && $t['vocab'] === "health determinant";
        });
        $topicsEnDeter = array_filter($topicsEn, function ($t) {
          return isset($t['vocab']) && $t['vocab'] === "health determinant";
        });

        $vocabListFrDeter = array_column($topicsFrDeter, 'topic');
        $vocabListEnDeter = array_column($topicsEnDeter, 'topic');

        $vocabFrDeter = implode(', ', $vocabListFrDeter);
        $vocabEnDeter = implode(', ', $vocabListEnDeter);
        ?>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Spécialité médicale" data-en="Medical field">Spécialité médicale</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($vocabFrHealth); ?>"
            data-en="<?php echo esc_html($vocabEnHealth); ?>">
            <?php echo esc_html($vocabFrHealth); ?>
          </div>
        </div>
        <!-- <?php print_r($jsonRec["fr"]) ?> -->
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Groupe de pathologies" data-en="Pathology group">Groupe de pathologies</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($vocabFrCim); ?>"
            data-en="<?php echo esc_html($vocabEnCim); ?>">
            <?php echo esc_html($vocabFrCim); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Mots-clés libres" data-en="Keywords">Mots-clés libres</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($jsonRec["fr"]["study_desc"]["study_info"]["keywords"][0]["keyword"]); ?>"
            data-en="<?php echo esc_html($jsonRec["en"]["study_desc"]["study_info"]["keywords"][0]["keyword"]); ?>">
            <?php echo esc_html($jsonRec[$currentLang]["study_desc"]["study_info"]["keywords"][0]["keyword"]); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Déterminants de santé" data-en="Health determinants">Déterminants de santé</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($vocabFrDeter); ?>"
            data-en="<?php echo esc_html($vocabEnDeter); ?>">
            <?php echo esc_html($vocabFrDeter); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Information complémentaire précisant l'étude" data-en="Additional Study Information">Information complémentaire précisant l'étude</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($jsonRec["fr"]["additional"]["theme"]["complementaryInformation"]); ?>"
            data-en="<?php echo esc_html($jsonRec["en"]["additional"]["theme"]["complementaryInformation"]); ?>">
            <?php echo esc_html($jsonRec[$currentLang]["additional"]["theme"]["complementaryInformation"]); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Maladies rares" data-en="Rare diseases">Maladies rares</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($jsonRec["fr"]["additional"]["theme"]["RareDiseases"]); ?>"
            data-en="<?php echo esc_html($jsonRec["en"]["additional"]["theme"]["RareDiseases"]); ?>">
            <?php echo esc_html($jsonRec[$currentLang]["additional"]["theme"]["RareDiseases"]); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="tab-pane " id="detaill-3">
  <div class="row">
    <div class="col-md-4 leftsidebar">
      <div class="navStickyBar">
        <ul class="navbar-nav flex-column wb--full-width">
          <li class="nav-item">
            <a href="#participant"  class="lang-text" data-fr="Nombre de participants" data-en="Number of participants">Nombre de participants</a>
          </li>
          <li class="nav-item">
            <a href="#datatype" class="lang-text" data-fr="Types de données" data-en="Types of data">Types de données</a>
          </li>
          <li class="nav-item">
            <a href="#dataaccess"  class="lang-text" data-fr="Accès aux données" data-en="Data access">Accès aux données</a>
          </li>
          <li class="nav-item"> 
            <a href="#dataquality"  class="lang-text" data-fr="Qualité des données" data-en="Data quality">Qualité des données</a>
          </li>
          <!-- <li class="nav-item">
            <a href="#datasetid"  class="lang-text" data-fr="Identifiant pérenne du jeu de données" data-en="Persistent identifier of the dataset">Identifiant pérenne du jeu de données</a>
          </li> -->
        </ul>
      </div>
    </div>

    <?php
    $langs = ['fr', 'en'];
    $currentLang = 'fr';


    if (!function_exists('parseStringArrayNormalized')) {
      function parseStringArrayNormalized($str) {
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

    $hasClinicalData = in_array('Données cliniques', $dataKindsListFr, true)|| in_array('Données cliniques', $dataKindsListEn, true);

    $hasBiologicalData = in_array('Données biologiques', $dataKindsListFr, true)|| in_array('Données biologiques', $dataKindsListEn, true);


    $biobankFlag = mb_strtolower(trim($isDataInBiobankFr));
    $showBiobankContent = in_array($biobankFlag, ['oui', 'yes', 'true', 'vrai'], true);


    $lowerFr = array_map('mb_strtolower', $dataKindsListFr);
    $lowerEn = array_map('mb_strtolower', $dataKindsListEn);
    $hasOtherDataType = in_array('autre', $lowerFr, true)
                    || in_array('other', $lowerFr, true)
                    || in_array('autre', $lowerEn, true)
                    || in_array('other', $lowerEn, true);

    ?>

    <div class="col-md-8">

     
      <div class="submenu-study" id="participant">
        <h2 class="field-content__h1 lang-text" data-fr="Participants" data-en="Participants">Participants</h2>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Nombre prévu" data-en="Planned number">Nombre prévu</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($targetSampleFr); ?>"
            data-en="<?php echo esc_html($targetSampleEn); ?>">
            <?php echo esc_html($targetSampleFr ?: '-'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Effectif réel" data-en="Actual number">Effectif réel</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($responseRateFr); ?>"
            data-en="<?php echo esc_html($responseRateEn); ?>">
            <?php echo esc_html($responseRateFr ?: '-'); ?>
          </div>
        </div>
      </div>


      <div class="submenu-study" id="datatype">
        <h2 class="field-content__h1 lang-text" data-fr="Types de données" data-en="Types of data">Types de données</h2>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" data-fr="Type de données" data-en="Type of data">Type de données</div>
          <div class="field-bloc__value lang-text"
            data-fr="<?php echo esc_html($dataKindsFr); ?>"
            data-en="<?php echo esc_html($dataKindsEn); ?>">
            <?php echo esc_html($dataKindsFr ?: '-'); ?>
          </div>
        </div>
        <?php if ($hasClinicalData): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text" data-fr="Données cliniques, précisions" data-en="Clinical data, details">Données cliniques, précisions</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($clinicalDetailsFr); ?>"
              data-en="<?php echo esc_html($clinicalDetailsEn); ?>">
              <?php echo esc_html($clinicalDetailsFr ?: '-'); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($hasBiologicalData): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text" data-fr="Données biologiques, précisions" data-en="Biological data, details">Données biologiques, précisions</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($biologicalDetailsFr); ?>"
              data-en="<?php echo esc_html($biologicalDetailsEn); ?>">
              <?php echo esc_html($biologicalDetailsFr ?: '-'); ?>
            </div>
          </div>

          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text" data-fr="Présence des échantillons dans une biobanque" data-en="Presence of samples in a biobank">Présence des échantillons dans une biobanque</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($isDataInBiobankFr); ?>"
              data-en="<?php echo esc_html($isDataInBiobankEn); ?>">
              <?php echo esc_html($isDataInBiobankFr ?: '-'); ?>
            </div>
          </div>
        <?php endif; ?>
        
        <?php if ($showBiobankContent): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text" data-fr="Contenu de la biobanque" data-en="Content of the biobank">Contenu de la biobanque</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($biobankContentFr); ?>"
              data-en="<?php echo esc_html($biobankContentEn); ?>">
              <?php echo esc_html($biobankContentFr ?: '-'); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php  if ($hasOtherDataType): ?>
          <div class="field-bloc mb-2">
            <div class="field-bloc__title lang-text" data-fr="Autre type de données, précisions" data-en="Other data types, details">Autre type de données, précisions</div>
            <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($dataTypeOtherFr); ?>"
              data-en="<?php echo esc_html($dataTypeOtherEn); ?>">
              <?php echo esc_html($dataTypeOtherFr ?: '-'); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

    
      <div class="submenu-study" id="dataaccess">
        <h2 class="field-content__h1 lang-text"
            data-fr="Accès aux données"
            data-en="Data access">
          Accès aux données
        </h2>

        <?php
        $indivAccessFr = $jsonRec['fr']['study_desc']['data_access']['dataset_availability']['status'] ?? '';
        $aggregAccessFr = $jsonRec['fr']['study_desc']['method']['data_collection']['research_instrument'] ?? '';
        $hasAccessToolFr = $jsonRec['fr']['study_desc']['data_access']['dataset_use']['spec_perm'][0]['required'] ?? '';
        $accessToolLinkFr = $jsonRec['fr']['study_desc']['data_access']['dataset_use']['spec_perm'][0]['txt'] ?? '';
        $conditionsFr = $jsonRec['fr']['study_desc']['data_access']['dataset_use']['conditions'] ?? '';
        $restrictionsFr = $jsonRec['fr']['study_desc']['data_access']['dataset_use']['restrictions'] ?? '';
        $extraInfoLinkFr = $jsonRec['fr']['study_desc']['data_access']['notes'] ?? '';
        $depositReqFr = $jsonRec['fr']['study_desc']['data_access']['dataset_use']['deposit_req'] ?? '';
        $citReqFr = $jsonRec['fr']['study_desc']['data_access']['dataset_use']['cit_req'] ?? '';
        $completenessFr = $jsonRec['fr']['study_desc']['data_access']['dataset_availability']['complete'] ?? '';
        $dataLocationFr = $jsonRec['fr']['study_desc']['data_access']['dataset_availability']['access_place'] ?? '';
        $confAgreementFr = $jsonRec['fr']['study_desc']['data_access']['dataset_use']['conf_dec'][0]['txt'] ?? '';
        $mockSampleFr = $jsonRec['fr']['additional']['mockSample']['mockSampleAvailable'] ?? '';
        $mockSampleLocationFr = $jsonRec['fr']['additional']['mockSample']['mockSampleLocation'] ?? '';

        $indivAccessEn = $jsonRec['en']['study_desc']['data_access']['dataset_availability']['status'] ?? '';
        $aggregAccessEn = $jsonRec['en']['study_desc']['method']['data_collection']['research_instrument'] ?? '';
        $hasAccessToolEn = $jsonRec['en']['study_desc']['data_access']['dataset_use']['spec_perm'][0]['required'] ?? '';
        $accessToolLinkEn = $jsonRec['en']['study_desc']['data_access']['dataset_use']['spec_perm'][0]['txt'] ?? '';
        $conditionsEn = $jsonRec['en']['study_desc']['data_access']['dataset_use']['conditions'] ?? '';
        $restrictionsEn = $jsonRec['en']['study_desc']['data_access']['dataset_use']['restrictions'] ?? '';
        $extraInfoLinkEn = $jsonRec['en']['study_desc']['data_access']['notes'] ?? '';
        $depositReqEn = $jsonRec['en']['study_desc']['data_access']['dataset_use']['deposit_req'] ?? '';
        $citReqEn = $jsonRec['en']['study_desc']['data_access']['dataset_use']['cit_req'] ?? '';
        $completenessEn = $jsonRec['en']['study_desc']['data_access']['dataset_availability']['complete'] ?? '';
        $dataLocationEn = $jsonRec['en']['study_desc']['data_access']['dataset_availability']['access_place'] ?? '';
        $confAgreementEn = $jsonRec['en']['study_desc']['data_access']['dataset_use']['conf_dec'][0]['txt'] ?? '';
        $mockSampleEn = $jsonRec['en']['additional']['mockSample']['mockSampleAvailable'] ?? '';
        $mockSampleLocationEn = $jsonRec['en']['additional']['mockSample']['mockSampleLocation'] ?? '';



        ?>


     

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text" 
              data-fr="Accès aux données individuelles"
              data-en="Access to individual data">
            Accès aux données individuelles
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($indivAccessFr); ?>"
              data-en="<?php echo esc_html($indivAccessEn); ?>">
            <?php echo esc_html($indivAccessFr ?: '-'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Accès aux données agrégées"
              data-en="Access to aggregated data">
            Accès aux données agrégées
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($aggregAccessFr); ?>"
              data-en="<?php echo esc_html($aggregAccessEn); ?>">
            <?php echo esc_html($aggregAccessFr ?: '-'); ?>
          </div>
        </div>

        <h3 class="field-content__h2 lang-text"
            data-fr="Outil de demande d'accès aux données"
            data-en="Data access request tool">
          Outil de demande d'accès aux données
        </h3>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Existence d'un outil de demande d'accès aux données"
              data-en="Availability of a data access request tool">
            Existence d'un outil de demande d'accès aux données
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($hasAccessToolFr); ?>"
              data-en="<?php echo esc_html($hasAccessToolEn); ?>">
            <?php echo esc_html($hasAccessToolFr ?: '-'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Lien vers l'outil de demande d'accès"
              data-en="Link to the data access request tool">
            Lien vers l'outil de demande d'accès
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($accessToolLinkFr); ?>"
              data-en="<?php echo esc_html($accessToolLinkEn); ?>">
            <?php echo esc_html($accessToolLinkFr ?: '-'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Conditions d'accès aux données"
              data-en="Data access conditions">
            Conditions d'accès aux données
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($conditionsFr); ?>"
              data-en="<?php echo esc_html($conditionsEn); ?>">
            <?php echo esc_html($conditionsFr ?: '-'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Restrictions d'accès"
              data-en="Access restrictions">
            Restrictions d'accès
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($restrictionsFr); ?>"
              data-en="<?php echo esc_html($restrictionsEn); ?>">
            <?php echo esc_html($restrictionsFr ?: '-'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Lien vers informations complémentaires relatives à l'accès aux données"
              data-en="Link to additional information on data access">
            Lien vers informations complémentaires relatives à l'accès aux données
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($extraInfoLinkFr); ?>"
              data-en="<?php echo esc_html($extraInfoLinkEn); ?>">
            <?php echo esc_html($extraInfoLinkFr ?: '-'); ?>
          </div>
        </div>

        

        

        <h3 class="field-content__h2 lang-text"
            data-fr="Obligations liées à l’usage des données"
            data-en="Obligations related to data use">
          Obligations liées à l’usage des données
        </h3>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Obligation de transmission des travaux"
              data-en="Reporting requirement">
            Obligation de transmission des travaux
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($depositReqFr); ?>"
              data-en="<?php echo esc_html($depositReqEn); ?>">
            <?php echo esc_html($depositReqFr ?: '-'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Obligation de citation"
              data-en="Citation requirement">
            Obligation de citation
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($citReqFr); ?>"
              data-en="<?php echo esc_html($citReqEn); ?>">
            <?php echo esc_html($citReqFr ?: '-'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Complétude des fichiers des données"
              data-en="Data file completeness">
            Complétude des fichiers des données
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($completenessFr); ?>"
              data-en="<?php echo esc_html($completenessEn); ?>">
            <?php echo esc_html($completenessFr ?: '-'); ?>
          </div>

          <?php
            $contactRawFr = $jsonRec['fr']['study_desc']['data_access']['dataset_use']['contact'][0]['name'] ?? '';
            $contactRawEn = $jsonRec['en']['study_desc']['data_access']['dataset_use']['contact'][0]['name'] ?? '';

            if (!function_exists('parseStringArrayNormalized')) {
              function parseStringArrayNormalized($str) {
                if (is_array($str)) return $str;
                if (empty($str)) return [];
                // remove [] and quotes
                $clean = trim($str, "[] \t\n\r\0\x0B'\"");
                // split by commas
                $parts = preg_split('/\s*,\s*/', $clean);
                // Remove any leftover quotes or spaces around each value
                $parts = array_map(fn($p) => trim($p, "'\" "), $parts);

                // remove empty entries
                return array_values(array_filter($parts, fn($v) => $v !== ''));
              }
            }

            $contactListFr = parseStringArrayNormalized($contactRawFr);
            $contactListEn = parseStringArrayNormalized($contactRawEn);
            $max = max(count($contactListFr), count($contactListEn));
          ?>

          <h3 class="field-content__h2 lang-text"
              data-fr="Personnes à contacter"
              data-en="Contact persons">
            Personnes à contacter
          </h3>

          <table class="field-table">
            <thead>
              <tr>
                <th class="lang-text" data-fr="Nom du contact" data-en="Contact name">
                  Nom du contact
                </th>
              </tr>
            </thead>
            <tbody>
              <?php if ($max > 0): ?>
                <?php for ($i = 0; $i < $max; $i++): ?>
                  <?php
                    $frVal = $contactListFr[$i] ?? '-';
                    $enVal = $contactListEn[$i] ?? '-';
                  ?>
                  <tr class="lang-row"
                      data-fr-name="<?php echo esc_attr($frVal); ?>"
                      data-en-name="<?php echo esc_attr($enVal); ?>">
                    <td>
                      <span class="lang-text"
                            data-fr="<?php echo esc_attr($frVal ?: '-'); ?>"
                            data-en="<?php echo esc_attr($enVal ?: '-'); ?>">
                        <?php echo esc_html($frVal ?: '-'); ?>
                      </span>
                    </td>
                  </tr>
                <?php endfor; ?>
              <?php else: ?>
                <tr><td>-</td></tr>
              <?php endif; ?>
            </tbody>
          </table>


        </div>
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Localisation des données"
              data-en="Data location">
            Localisation des données
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($dataLocationFr); ?>"
              data-en="<?php echo esc_html($dataLocationEn); ?>">
            <?php echo esc_html($dataLocationFr ?: '-'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Accord de confidentialité"
              data-en="Non-disclosure agreement">
            Accord de confidentialité
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($confAgreementFr); ?>"
              data-en="<?php echo esc_html($confAgreementEn); ?>">
            <?php echo esc_html($confAgreementFr ?: '-'); ?>
          </div>
        </div>

        <h3 class="field-content__h2 lang-text"
            data-fr="Échantillon fictif"
            data-en="Mock sample">
          Échantillon fictif
        </h3>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Existence d'un échantillon fictif"
              data-en="Availability of a mock sample">
            Existence d'un échantillon fictif
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($mockSampleFr); ?>"
              data-en="<?php echo esc_html($mockSampleEn); ?>">
            <?php echo esc_html($mockSampleFr ?: '-'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Lien ou précisions"
              data-en="Link or specify">
            Lien ou précisions
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($mockSampleLocationFr); ?>"
              data-en="<?php echo esc_html($mockSampleLocationEn); ?>">
            <?php echo esc_html($mockSampleLocationFr ?: '-'); ?>
          </div>
        </div>
      </div>



      <div class="submenu-study" id="dataquality">
        <h2 class="field-content__h1 lang-text"
            data-fr="Qualité des données"
            data-en="Data quality">
          Qualité des données
        </h2>

        <?php

        $standardsRawFr = $jsonRec['fr']['study_desc']['study_info']['quality_statement']['standards'][0]['name'] ?? '';
        $standardsRawEn = $jsonRec['en']['study_desc']['study_info']['quality_statement']['standards'][0]['name'] ?? '';

        $qualityProcRawFr = $jsonRec['fr']['study_desc']['study_info']['quality_statement']['compliance_description'] ?? '';
        $qualityProcRawEn = $jsonRec['en']['study_desc']['study_info']['quality_statement']['compliance_description'] ?? '';

        $variableDictAvailableFr = $jsonRec['fr']['additional']['variableDictionnary']['variableDictionnaryAvailable'] ?? '';
        $variableDictAvailableEn = $jsonRec['en']['additional']['variableDictionnary']['variableDictionnaryAvailable'] ?? '';

        $variableDictLinkFr = $jsonRec['fr']['additional']['variableDictionnary']['variableDictionnaryLink'] ?? '';
        $variableDictLinkEn = $jsonRec['en']['additional']['variableDictionnary']['variableDictionnaryLink'] ?? '';

        $otherDocFr = $jsonRec['fr']['additional']['dataQuality']['otherDocumentation'] ?? '';
        $otherDocEn = $jsonRec['en']['additional']['dataQuality']['otherDocumentation'] ?? '';

          if (!function_exists('parseStringArrayNormalized')) {
            function parseStringArrayNormalized($str) {
              if (is_array($str)) return $str;
              if (empty($str)) return [];
              $clean = trim($str, "[] \t\n\r\0\x0B'\"");
              $parts = preg_split('/\s*,\s*/', $clean);
              $parts = array_map(fn($p) => trim($p, "'\" "), $parts);
              return array_values(array_filter($parts, fn($v) => $v !== ''));
            }
          }
          



        $standardsListFr = parseStringArrayNormalized($standardsRawFr);
        $standardsListEn = parseStringArrayNormalized($standardsRawEn);

        $qualityProcListFr = parseStringArrayNormalized($qualityProcRawFr);
        $qualityProcListEn = parseStringArrayNormalized($qualityProcRawEn);

        $maxStandards = max(count($standardsListFr), count($standardsListEn));
        $maxQualityProc = max(count($qualityProcListFr), count($qualityProcListEn));

        ?>

     
        <h3 class="field-content__h2 lang-text"
            data-fr="Standards ou nomenclatures employés"
            data-en="Standards or nomenclatures used">
          Standards ou nomenclatures employés
        </h3>

        <table class="field-table">
          <thead>
            <tr>
              <th class="lang-text" data-fr="Nom" data-en="Name">Nom</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($maxStandards > 0): ?>
              <?php for ($i = 0; $i < $maxStandards; $i++): ?>
                <?php
                  $frVal = $standardsListFr[$i] ?? '-';
                  $enVal = $standardsListEn[$i] ?? '-';
                ?>
                <tr class="lang-row"
                    data-fr-name="<?php echo esc_attr($frVal); ?>"
                    data-en-name="<?php echo esc_attr($enVal); ?>">
                  <td>
                    <span class="lang-text"
                          data-fr="<?php echo esc_attr($frVal ?: '-'); ?>"
                          data-en="<?php echo esc_attr($enVal ?: '-'); ?>">
                      <?php echo esc_html($frVal ?: '-'); ?>
                    </span>
                  </td>
                </tr>
              <?php endfor; ?>
            <?php else: ?>
              <tr><td>-</td></tr>
            <?php endif; ?>
          </tbody>
        </table>


     
        <h3 class="field-content__h2 lang-text"
            data-fr="Procédure qualité utilisée"
            data-en="Quality procedures used">
          Procédure qualité utilisée
        </h3>
       
        <table class="field-table">
          <thead>
            <tr>
              <th class="lang-text" data-fr="Procédure" data-en="Procedure">Procédure</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($maxQualityProc > 0): ?>
              <?php for ($i = 0; $i < $maxQualityProc; $i++): ?>
                <?php
                  $frVal = $qualityProcListFr[$i] ?? '-';
                  $enVal = $qualityProcListEn[$i] ?? '-';
                ?>
                <tr class="lang-row"
                    data-fr-name="<?php echo esc_attr($frVal); ?>"
                    data-en-name="<?php echo esc_attr($enVal); ?>">
                  <td>
                    <span class="lang-text"
                          data-fr="<?php echo esc_attr($frVal ?: '-'); ?>"
                          data-en="<?php echo esc_attr($enVal ?: '-'); ?>">
                      <?php echo esc_html($frVal ?: '-'); ?>
                    </span>
                  </td>
                </tr>
              <?php endfor; ?>
            <?php else: ?>
              <tr><td>-</td></tr>
            <?php endif; ?>
          </tbody>
        </table>


   
        <h3 class="field-content__h2 lang-text"
            data-fr="Dictionnaire des variables"
            data-en="Variable dictionary">
          Dictionnaire des variables
        </h3>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Existence d’un dictionnaire des variables"
              data-en="Presence of a data dictionary">
            Existence d’un dictionnaire des variables
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($variableDictAvailableFr); ?>"
              data-en="<?php echo esc_html($variableDictAvailableEn); ?>">
            <?php echo esc_html($variableDictAvailableFr ?: '-'); ?>
          </div>
        </div>

        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Lien vers le dictionnaire des variables (si accessible)"
              data-en="Link to the data dictionary (if accessible)">
            Lien vers le dictionnaire des variables (si accessible)
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($variableDictLinkFr); ?>"
              data-en="<?php echo esc_html($variableDictLinkEn); ?>">
            <?php echo esc_html($variableDictLinkFr ?: '-'); ?>
          </div>
        </div>

      
        <div class="field-bloc mb-2">
          <div class="field-bloc__title lang-text"
              data-fr="Autres documentations sur les données"
              data-en="Other data documentation">
            Autres documentations sur les données
          </div>
          <div class="field-bloc__value lang-text"
              data-fr="<?php echo esc_html($otherDocFr); ?>"
              data-en="<?php echo esc_html($otherDocEn); ?>">
            <?php echo esc_html($otherDocFr ?: '-'); ?>
          </div>
        </div>
      </div>          

      


      <div class="submenu-study" id="datasetid">
        <h2 class="field-content__h1 lang-text"
            data-fr="Identifiant pérenne du jeu de données"
            data-en="Persistent identifier of the dataset">
          Identifiant pérenne du jeu de données
        </h2>

        <?php
        $datasetIdnoArrFr = $jsonRec['fr']['study_desc']['title_statement']['IDno'] ?? [];
        $datasetIdnoArrEn = $jsonRec['en']['study_desc']['title_statement']['IDno'] ?? [];

        if (!is_array($datasetIdnoArrFr)) $datasetIdnoArrFr = [];
        if (!is_array($datasetIdnoArrEn)) $datasetIdnoArrEn = [];




        $maxDataset = max(count($datasetIdnoArrFr), count($datasetIdnoArrEn));


        $showOtherColumn = false;
        foreach ($datasetIdnoArrFr as $item) {
          if (!empty(trim($item['agentOther'] ?? ''))) {
            $showOtherColumn = true;
            break;
          }
        }
        if (!$showOtherColumn) {
          foreach ($datasetIdnoArrEn as $item) {
            if (!empty(trim($item['agentOther'] ?? ''))) {
              $showOtherColumn = true;
              break;
            }
          }
        }
        ?>

        <table class="field-table">
          <thead>
            <tr>
              <th class="lang-text" data-fr="URI" data-en="URI">URI</th>
              <th class="lang-text" data-fr="Type" data-en="Schema">Type</th>
              <?php if ($showOtherColumn): ?>
                <th class="lang-text" data-fr="Autre type" data-en="Other PID schema">Autre type</th>
              <?php endif; ?>
              <th class="lang-text" data-fr="Producteur" data-en="Producer">Producteur</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($maxDataset > 0): ?>
              <?php for ($i = 0; $i < $maxDataset; $i++): ?>
                <?php
                  $frItem = $datasetIdnoArrFr[$i] ?? [];
                  $enItem = $datasetIdnoArrEn[$i] ?? [];

                  $frUri     = $frItem['uri']         ?? '-';
                  $enUri     = $enItem['uri']         ?? '-';

                  $frSchema  = $frItem['agentSchema'] ?? '-';
                  $enSchema  = $enItem['agentSchema'] ?? '-';

                  $frOther   = $frItem['agentOther']  ?? '-';
                  $enOther   = $enItem['agentOther']  ?? '-';

                  $frProd    = $frItem['producer']    ?? '-';
                  $enProd    = $enItem['producer']    ?? '-';
                ?>
                <tr class="lang-row"
                    data-fr-uri="<?php echo esc_attr($frUri); ?>"
                    data-en-uri="<?php echo esc_attr($enUri); ?>"
                    data-fr-schema="<?php echo esc_attr($frSchema); ?>"
                    data-en-schema="<?php echo esc_attr($enSchema); ?>"
                    data-fr-other="<?php echo esc_attr($frOther); ?>"
                    data-en-other="<?php echo esc_attr($enOther); ?>"
                    data-fr-prod="<?php echo esc_attr($frProd); ?>"
                    data-en-prod="<?php echo esc_attr($enProd); ?>">
                  <td class="lang-text"
                      data-fr="<?php echo esc_attr($frUri ?: '-'); ?>"
                      data-en="<?php echo esc_attr($enUri ?: '-'); ?>">
                      <?php echo esc_html($frUri ?: '-'); ?>
                  </td>
                  <td class="lang-text"
                      data-fr="<?php echo esc_attr($frSchema ?: '-'); ?>"
                      data-en="<?php echo esc_attr($enSchema ?: '-'); ?>">
                      <?php echo esc_html($frSchema ?: '-'); ?>
                  </td>
                  <?php if ($showOtherColumn): ?>
                    <td class="lang-text"
                        data-fr="<?php echo esc_attr($frOther ?: '-'); ?>"
                        data-en="<?php echo esc_attr($enOther ?: '-'); ?>">
                        <?php echo esc_html($frOther ?: '-'); ?>
                    </td>
                  <?php endif; ?>
                  <td class="lang-text"
                      data-fr="<?php echo esc_attr($frProd ?: '-'); ?>"
                      data-en="<?php echo esc_attr($enProd ?: '-'); ?>">
                      <?php echo esc_html($frProd ?: '-'); ?>
                  </td>
                </tr>
              <?php endfor; ?>
            <?php else: ?>
              <tr><td colspan="4">-</td></tr>
            <?php endif; ?>
          </tbody>
        </table>

      </div>


    </div>
  </div>
</div>
   


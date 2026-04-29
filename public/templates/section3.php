<?php
/*
Template Name: Add Study section 3
*/
?>

<?php
include('add-study-input.php');
?>

<div class="tab-pane" id="step-3">
  <section key="CollectContext">
    <h1 class="lang-text"
      data-fr="Caractéristique des données collectées"
      data-en="Characteristics of collected data">
      Caractéristique des données collectées
    </h1>
    <section key="DataCharacteristics">
      <section key="SampleSize">
        <div class="mb-4">
          <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#collapsefour"
            aria-expanded="true"
            aria-controls="collapsefour"
            data-fr="Nombre de participants"
            data-en="Number of participants">
            Nombre de participants
          </button>

          <div class="collapse show" id="collapsefour">
            <div class="card card-body">
              <section key="SampleSize">
                <h3 class="lang-text"
                  data-fr="Nombre de participants"
                  data-en="Number of participants">
                  Nombre de participants
                </h3>

                <div class="row">
                  <div class="col-md-6 mb-3" ddiShema="stdyDscr/method/dataColl/targetSampleSize">
                    <?php
                    $options_PredictedNumber = [
                      "fr" => [
                        "< 500 individus"        => "< 500 individus",
                        "[500-1000[ individus"   => "[500-1000[ individus",
                        "[1000-10000[ individus" => "[1000-10000[ individus",
                        "[10000-20000[ individus" => "[10000-20000[ individus",
                        ">= 20000 individus"     => ">= 20000 individus"
                      ],
                      "en" => [
                        "< 500 individuals"        => "< 500 individuals",
                        "[500-1000[ individuals"   => "[500-1000[ individuals",
                        "[1000-10000[ individuals" => "[1000-10000[ individuals",
                        "[10000-20000[ individuals" => "[10000-20000[ individuals",
                        ">= 20000 individuals"     => ">= 20000 individuals"
                      ]
                    ];

                    nada_renderInputGroup("Nombre prévu", "Number planned", "stdyDscr/method/dataColl/targetSampleSize", "select", $options_PredictedNumber, true, true);
                    ?>
                  </div>
                  <div class="col-md-6 mb-3">
                    <?php nada_renderInputGroup("Effectif réél", "Actual number", "stdyDscr/method/dataColl/respRate", "text", [], true, false, null, null, true); ?>
                  </div>
                </div>
              </section>
            </div>
          </div>
        </div>
      </section>

      <section key="DataTypes">
        <div class="mb-4">
          <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#collapseseven"
            aria-expanded="true"
            aria-controls="collapseseven"
            data-fr="Types de données"
            data-en="Types of data">
            Types de données
          </button>

          <div class="collapse show" id="collapseseven">
            <div class="card card-body">
              <section key="DataTypes">
                <h3 class="lang-text"
                  data-fr="Types de données"
                  data-en="Data types">
                  Types de données
                </h3>

                <div class="row">
                  <div class="col-md-12 mb-3" ddiShema="stdyDscr/stdyInfo/sumDscr/dataKind">
                    <?php
                    $options_dataTypes = [
                      "fr" => [
                        "Données cliniques" => "Données cliniques",
                        "Données paracliniques (hors biologiques)" => "Données paracliniques (hors biologiques)",
                        "Données paracliniques (hors biologiques) : Imagerie" => "Données paracliniques (hors biologiques) : Imagerie",
                        "Données paracliniques (hors biologiques) : Anthropométrie" => "Données paracliniques (hors biologiques) : Anthropométrie",
                        "Données paracliniques (hors biologiques) : Exploration fonctionnelle" => "Données paracliniques (hors biologiques) : Exploration fonctionnelle",
                        "Données paracliniques (hors biologiques) : Autre" => "Données paracliniques (hors biologiques) : Autre",
                        "Données biologiques" => "Données biologiques",
                        "Données socio-démographiques" => "Données socio-démographiques",
                        "Données environnementales-d'exposition" => "Données environnementales-d'exposition",
                        "Données comportementales" => "Données comportementales",
                        "Données géospatiales" => "Données géospatiales",
                        "Données économiques" => "Données économiques",
                        "Données génétiques-génomiques" => "Données génétiques-génomiques",
                        "Données de santé rapportées par les participants de l'étude" => "Données de santé rapportées par les participants de l'étude",
                        "Autre" => "Autre"
                      ],
                      "en" => [
                        "Clinical data" => "Clinical data",
                        "Paraclinical data (non-biological)" => "Paraclinical data (non-biological)",
                        "Paraclinical data (non-biological): Imaging" => "Paraclinical data (non-biological): Imaging",
                        "Paraclinical data (non-biological): Anthropometry" => "Paraclinical data (non-biological): Anthropometry",
                        "Paraclinical data (non-biological): Functional exploration" => "Paraclinical data (non-biological): Functional exploration",
                        "Paraclinical data (non-biological): Other" => "Paraclinical data (non-biological): Other",
                        "Biological data" => "Biological data",
                        "Socio-demographic data" => "Socio-demographic data",
                        "Environmental-exposure data" => "Environmental-exposure data",
                        "Behavioral data" => "Behavioral data",
                        "Geospatial data" => "Geospatial data",
                        "Economic data" => "Economic data",
                        "Genetic-genomic data" => "Genetic-genomic data",
                        "Health data reported by study participants" => "Health data reported by study participants",
                        "Other" => "Other"
                      ]
                    ];


                    nada_renderInputGroup("Type de données", "Data type", "stdyDscr/stdyInfo/sumDscr/dataKind", "checkbox", $options_dataTypes, true, true);
                    ?>
                  </div>

                  <div class="col-md-12 mb-3 d-none" id="ClinicalDataDetailsBloc">
                    <?php nada_renderInputGroup("Données cliniques, précisions", "Clinical data details", "additional/dataTypes/clinicalDataDetails", "text", [], true, false); ?>
                  </div>
                  <div class="col-md-12 mb-3 d-none" id="BiologicalDataDetailsBloc">
                    <?php nada_renderInputGroup("Données biologiques précisions", "Biological data details", "additional/dataTypes/biologicalDataDetails", "text", [], true, false); ?>
                  </div>
                  <div class="col-md-6 mb-3 d-none" id="IsDataInBiobankBloc" ddiShema="_custom">
                    <?php
                    $options_dataInBioBank = [
                      "true"  => "Oui",
                      "false" => "Non",
                    ];
                    nada_renderInputGroup("Présence des échantillons dans une biobanque", "Presence of samples in a biobank", "additional/dataTypes/isDataInBiobank", "select", $options_dataInBioBank, true, false);
                    ?>
                  </div>
                  <div class="col-md-12 mb-3 d-none" id="BiobankContentBloc" ddiShema="_custom">
                    <?php
                    $options_dataInBioBankContent = [
                      "fr" => [
                        "Tissus" => "Tissus",
                        "Cellules" => "Cellules",
                        "Acides nucléiques" => "Acides nucléiques",
                        "Sang total" => "Sang total",
                        "Plasma" => "Plasma",
                        "Sang fœtal" => "Sang fœtal",
                        "Sérum" => "Sérum",
                        "Cellules sanguines" => "Cellules sanguines",
                        "Autres liquides ou sécrétions biologiques" => "Autres liquides ou sécrétions biologiques",
                        "Autres" => "Autres"
                      ],
                      "en" => [
                        "Tissues" => "Tissues",
                        "Cells" => "Cells",
                        "Nucleic acids" => "Nucleic acids",
                        "Whole blood" => "Whole blood",
                        "Plasma" => "Plasma",
                        "Fetal blood" => "Fetal blood",
                        "Serum" => "Serum",
                        "Blood cells" => "Blood cells",
                        "Other biological fluids or secretions" => "Other biological fluids or secretions",
                        "Other" => "Other"
                      ]
                    ];

                    nada_renderInputGroup("Contenu de la biobanque", "Biobank contents", "additional/dataTypes/biobankContent", "checkbox", $options_dataInBioBankContent, true, true);
                    ?>
                  </div>
                  <div class="col-md-12 mb-3 d-none" id="dataTypeOtherBloc">
                    <?php nada_renderInputGroup("Autre type de données, précisions", "Other data type, précisions", "additional/dataTypes/dataTypeOther", "textarea", [], true, false); ?>
                  </div>

                </div>
              </section>
            </div>
          </div>
        </div>
      </section>

      <section key="DataAccess">
        <div class="mb-4">
          <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#collapsesix"
            aria-expanded="true"
            aria-controls="collapsesix"
            data-fr="Accès aux données"
            data-en="Data access">
            Accès aux données
          </button>

          <div class="collapse show" id="collapsesix">
            <div class="card card-body">
              <section key="DataAccess">
                <h3 class="lang-text"
                  data-fr="Accès aux données"
                  data-en="Data access">
                  Accès aux données
                </h3>

                <div class="row">
                  <div class="col-md-6 mb-3" ddiShema="stdyDscr/dataAccs/setAvail/avlStatus">
                    <?php
                    $options_IndividualDataAccess = [
                      "fr" => [
                        "Libre accès"              => "Libre accès",
                        "Accès réservé"            => "Accès réservé",
                        "Accès aux seules métadonnées" => "Accès aux seules métadonnées",
                        "Sous embargo"             => "Sous embargo",
                        "A définir"                => "A définir"
                      ],
                      "en" => [
                        "Open access"              => "Open access",
                        "Restricted access"        => "Restricted access",
                        "Metadata-only access"     => "Metadata-only access",
                        "Under embargo"            => "Under embargo",
                        "To be defined"            => "To be defined"
                      ]
                    ];

                    nada_renderInputGroup("Accès aux données individuelles", "Access to individual data", "stdyDscr/dataAccs/setAvail/avlStatus", "select", $options_IndividualDataAccess, true, true);
                    ?>
                  </div>
                  <div class="col-md-6 mb-3">
                    <?php nada_renderInputGroup("Accès aux données agregées ", "Access to aggregated data", "stdyDscr/othStdMat/relMat", "text", [], true, false); ?>
                  </div>


                  <section key="DataAccessRequestTool">
                    <h4 class="lang-text"
                      data-fr="Outil de demande d'accès aux données"
                      data-en="Data access request tool">
                      Outil de demande d'accès aux données
                    </h4>

                    <div class="row">
                      <div class="col-md-12 mb-3" ddiShema="stdyDscr/dataAccs/useStmt/specPerm">
                        <input type="hidden" name="stdyDscr/dataAccs/useStmt/specPerm/required_fr" value="yes" />
                        <input type="hidden" name="stdyDscr/dataAccs/useStmt/specPerm/required_en" value="yes" />
                        <?php
                        $options_boolean = [
                          "fr" => [
                            "Oui"  => "Oui",
                            "Non" => "Non",
                          ],
                          "en" => [
                            "Yes"  => "Yes",
                            "No" => "No",
                          ]
                        ];
                        nada_renderInputGroup("Existence d'un outil de demande d'accès aux données", "Availability of a data access request tool", "stdyDscr/dataAccs/useStmt/specPerm/required_yes", "radio", $options_boolean, true, false);
                        ?>
                      </div>
                      <div class="col-md-6 mb-3">
                        <?php nada_renderInputGroup("Lien vers l'outil de demande d'accès", "Link to the data access request tool", "stdyDscr/dataAccs/useStmt/specPerm", "text", [], true, false); ?>
                      </div>
                    </div>
                  </section>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <?php nada_renderInputGroup("Conditions d'accès aux données", "Data access conditions", "stdyDscr/dataAccs/setAvail/conditions", "text", [], true, false); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                      <?php nada_renderInputGroup("Restrictions d'accès", "Access restrictions", "stdyDscr/dataAccs/useStmt/restrctn", "text", [], true, false); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                      <?php nada_renderInputGroup("Lien vers informations complémentaires relatives à l'accès aux données", "Link to additional information on data access", "stdyDscr/dataAccs/setAvail/notes", "text", [], true, false); ?>
                    </div>
                  </div>

                  <section key="DataCitation">
                    <h4 class="lang-text"
                      data-fr="Obligations liées à l’usage des données"
                      data-en="Obligations related to data use">
                      Obligations liées à l’usage des données
                    </h4>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <?php nada_renderInputGroup("Obligation de transmission des travaux ", "Reporting requirement", "stdyDscr/dataAccs/useStmt/deposReq", "text", [], true, false); ?>
                      </div>
                      <div class="col-md-12 mb-3">
                        <?php nada_renderInputGroup("Obligation de citation", "Citation requirement", "stdyDscr/dataAccs/useStmt/citReq", "text", [], true, false); ?>
                      </div>
                    </div>
                  </section>


                  <div class="col-md-12 mb-3">
                    <?php nada_renderInputGroup("Complétude des fichiers des données", "Data file completeness", "stdyDscr/dataAccs/setAvail/complete", "text", [], true, false); ?>
                  </div>
                  <div class="col-md-12 mb-3">
                    <div id="repeater-DataInformationContact" class="repeaterBlock">
                      <div class="mb-2 repeater-item">
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
                            <?php nada_renderInputGroup("Personnes à contacter pour obtenir les renseignements concernant les données", "Contact person for data information", "stdyDscr/dataAccs/useStmt/contact", "text", [], true, false,); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex btnBlockRepeater">
                      <button type="button" class="btn-add mt-2" id="add-DataInformationContact">
                        <span class="dashicons dashicons-plus"></span>
                        <span class="lang-text"
                          data-fr="Ajouter"
                          data-en="Add">
                          Ajouter
                        </span>
                      </button>
                    </div>
                  </div>
                  <div class="col-md-12 mb-3">
                    <?php nada_renderInputGroup("Localisation des données", "Data location", "stdyDscr/dataAccs/setAvail/accsPlac", "text", [], true, false); ?>
                  </div>
                  <div class="col-md-6 mb-3">
                    <?php nada_renderInputGroup("Accord de confidentialité", "Non-disclosure agreement", "stdyDscr/dataAccs/useStmt/confDec", "text", [], true, false); ?>
                  </div>


                  <section key="MockSample">
                    <h4 class="lang-text"
                      data-fr="Échantillon fictif"
                      data-en="Mock sample">
                      Échantillon fictif
                    </h4>

                    <div class="col-md-6 mb-3" ddiShema="_custom">
                      <?php
                      $options_boolean = [
                        "fr" => [
                          "Oui"  => "Oui",
                          "Non" => "Non",
                        ],
                        "en" => [
                          "Yes"  => "Yes",
                          "No" => "No",
                        ]
                      ];
                      nada_renderInputGroup("Existence d'un échantillon fictif", "Availability of a mock sample", "additional/mockSample/mockSampleAvailable", "radio", $options_boolean, true, false);
                      ?>
                    </div>
                    <div class="col-md-6 mb-3">
                      <?php nada_renderInputGroup("Lien ou précisions", "Link or specify", "additional/mockSample/mockSampleLocation", "text", [], true, false); ?>
                    </div>
                  </section>
                  <?php nada_renderInputGroup("Identifiant", "Identifier", "stdyDscr/citation/IDno/identifiant", "text", [], true, false); ?>

                </div>
              </section>
            </div>
          </div>
        </div>
      </section>

      <section key="DataQuality">
        <div class="mb-4">
          <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#collapsefive"
            aria-expanded="true"
            aria-controls="collapsefive"
            data-fr="Qualité des données"
            data-en="Data quality">
            Qualité des données
          </button>

          <div class="collapse show" id="collapsefive">

            <div class="card card-body">

              <section key="DataQuality">
                <h3 class="lang-text"
                  data-fr="Qualité des données"
                  data-en="Data quality">
                  Qualité des données
                </h3>


                <div class="row">
                  <div class="col-md-12 mb-3">
                    <div id="repeater-standards" class="repeaterBlock">
                      <div class="mb-2 repeater-item">
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
                            nada_renderInputGroup(
                              "Standards ou nomenclatures employés",
                              "Standards or nomenclatures used",
                              "stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName",
                              "text",
                              [],
                              true,
                              false
                            );
                            ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex btnBlockRepeater">
                      <button type="button" class="btn-add mt-2" id="add-standard">
                        <span class="dashicons dashicons-plus"></span>
                        <span class="lang-text"
                          data-fr="Ajouter"
                          data-en="Add">
                          Ajouter
                        </span>
                      </button>
                    </div>
                  </div>
                  <div class="col-md-12 mb-3">
                    <div id="repeater-DataQuality" class="repeaterBlock">
                      <div class="mb-2 repeater-item">
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
                            <?php nada_renderInputGroup("Procédure qualité utilisée", "Quality procedures used", "stdyDscr/stdyInfo/qualityStatement/complianceDescription", "text", [], true, false); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex btnBlockRepeater">
                      <button type="button" class="btn-add mt-2" id="add-DataQuality">
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
                <section key="VariableDictionnary">
                  <h4 class="lang-text"
                    data-fr="Dictionnaire des variables"
                    data-en="Variable dictionary">
                    Dictionnaire des variables
                  </h4>

                  <div class="row">
                    <div class="col-md-6 mb-3" ddiShema="_custom">
                      <?php
                      $options_boolean = [
                        "fr" => [
                          "Oui"  => "Oui",
                          "Non" => "Non",
                        ],
                        "en" => [
                          "Yes"  => "Yes",
                          "No" => "No",
                        ]
                      ];
                      nada_renderInputGroup("Existence d’un dictionnaire des variables", "Presence of a data dictionary", "additional/variableDictionnary/variableDictionnaryAvailable", "radio", $options_boolean, true, true);
                      ?>
                    </div>
                    <div class="col-md-12 mb-3">
                      <?php nada_renderInputGroup("Lien vers le dictionnaire des variables (si accessible)", "Link to the data dictionary (if accessible)", "additional/variableDictionnary/variableDictionnaryLink", "text", [], true, false); ?>
                    </div>
                  </div>
                </section>
                <div class="col-md-6 mb-3">
                  <?php nada_renderInputGroup("Autres documentations sur les données", "Other data documentation", "additional/dataQuality/otherDocumentation", "text", [], true, true); ?>
                </div>
              </section>
            </div>
          </div>
        </div>
      </section>

      <section key="DatasetPID">
        <div class="mb-4">
          <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#collapsetwelve"
            aria-expanded="true"
            aria-controls="collapsetwelve"
            data-fr="Identifiant pérenne du jeu de données"
            data-en="Persistent identifier of the dataset">
            Identifiant pérenne du jeu de données
          </button>

          <div class="collapse show" id="collapsetwelve">
            <div class="card card-body">
              <section key="DatasetPID">
                <h3 class="lang-text" data-fr="Identifiant pérenne du jeu de données" data-en="Persistent identifier of the dataset">Identifiant pérenne du jeu de données</h3>
                <div id="repeater-DatasetPID" class="repeaterBlock">
                  <div class="mb-2 repeater-item" data-section="SchemapidValue">
                    <button type="button" class="btn-remove btn-remove-section ">
                      <span class="dashicons dashicons-trash"></span>
                      <span class="lang-text"
                        data-fr="Supprimer"
                        data-en="Delete">
                        Supprimer
                      </span>
                    </button>
                    <div class="row w-100" data-parent="SchemapidValue">
                      <div class="col-md-12 mb-3">
                        <?php nada_renderInputGroup("Identifiant du jeu de données", "Identifier", "stdyDscr/citation/IDno/identifiant", "text", [], true, false); ?>
                      </div>
                      <div class="col-md-12 mb-3 ">
                        <?php nada_renderInputGroup("URI du jeu de données", "URI", "stdyDscr/citation/IDno/uri", "url", [], true, false); ?>
                      </div>
                      <div class="col-md-6 mb-3" ddiShema="_custom">
                        <?php
                        $options_boolean = [
                          "fr" => [
                            "DOI"    => "DOI",
                            "Handle" => "Handle",
                            "Autre"  => "Autre"
                          ],
                          "en" => [
                            "DOI"    => "DOI",
                            "Handle" => "Handle",
                            "Other"  => "Other"
                          ]
                        ];

                        nada_renderInputGroup("Type d'identifiant", "Schema", "stdyDscr/citation/IDno/agentSchema", "select", $options_boolean, true, false);
                        ?>
                      </div>
                      <div class="col-md-6 mb-3 d-none " id="pidValue" data-item="SchemapidValue">
                        <?php nada_renderInputGroup("Autre type d'identifiant", "Other PID schema", "stdyDscr/citation/IDno/otherAgent", "textarea", [], true, false); ?>
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
            </div>
          </div>
        </div>
      </section>
    </section>
  </section>
</div>

<script>
  jQuery(document).ready(function() {

    function setupValidation(lang) {
      const $select = jQuery("select[name='stdyDscr/method/dataColl/targetSampleSize_" + lang + "']");
      const $input = jQuery("input.lang-input[name^='stdyDscr/method/dataColl/respRate_" + lang + "']");

      const initialAttrValue = $input.attr("value") || "";

      function updateInputLimits() {
        const selected = $select.val();

        let min = 0;
        let max = null;

        switch (selected) {
          case "< 500 individus":
          case "< 500 individuals":
            min = 0;
            max = 499;
            break;

          case "[500-1000[ individus":
          case "[500-1000[ individuals":
            min = 500;
            max = 999;
            break;

          case "[1000-10000[ individus":
          case "[1000-10000[ individuals":
            min = 1000;
            max = 9999;
            break;

          case "[10000-20000[ individus":
          case "[10000-20000[ individuals":
            min = 10000;
            max = 19999;
            break;

          case ">= 20000 individus":
          case ">= 20000 individuals":
            min = 20000;
            max = null;
            break;

          default:
            min = 0;
            max = null;
        }

        $input.attr("min", min);
        if (max !== null) {
          $input.attr("max", max);
        } else {
          $input.removeAttr("max");
        }
      }

      jQuery(document).on("blur", "input.lang-input[name^='stdyDscr/method/dataColl/respRate_" + lang + "']", function() {
        const $this = jQuery(this);
        const val = $this.val().trim();
        const min = parseInt(jQuery(this).attr("min"));
        const max = parseInt(jQuery(this).attr("max"));

        $this.next(".error-msg").remove();


        if (val !== "" && !/^[0-9]+$/.test(val)) {
          const msg = lang === "fr" ?
            "Veuillez entrer uniquement des chiffres (pas de lettres ni de caractères spéciaux)." :
            "Please enter numbers only (no letters or special characters).";
          $this.after(jQuery('<div>').addClass('error-msg text-danger').text(msg));
          return;
        }

        const value = parseInt(val);

        jQuery(this).next(".error-msg").remove();

        if (jQuery(this).val().trim() === "" || isNaN(value)) return;

        if (jQuery(this).val().trim() === "") return;
        if ((!isNaN(min) && value < min) || (!isNaN(max) && value > max)) {
          const msg = lang === "fr" ?
            "Veuillez entrer un nombre entre " + min + (max ? " et " + max : " et plus") + "." :
            "Please enter a number between " + min + (max ? " and " + max : " or more") + ".";
          jQuery(this).after(jQuery('<div>').addClass('error-msg text-danger').text(msg));
        }
      });

      updateInputLimits();

      $select.on("change", function() {
        updateInputLimits();
        if (initialAttrValue !== "") {
          $input.val(initialAttrValue);
        } else {
          $input.val("");
        }
        $input.next(".error-msg").remove();
      });
    }

    setupValidation("fr");
    setupValidation("en");

  });
</script>
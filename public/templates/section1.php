<?php
/*
Template Name: Add Study section 1
*/
?>

<?php
include('add-study-input.php');
?>

<div class="tab-pane show active" id="step-1">

  <section key="CollectContext">
    <h1 class="lang-text" data-fr="Renseignements sur le contexte de la collecte des données" data-en="Context of data collection">Renseignements sur le contexte de la collecte des données</h1>

    <!-- Section 1 -->
    <section key="AdministrativeInformation">
      <div class="mb-4">
        <button class="btn btn-primary w-100 text-start btnCollpase lang-text" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" data-fr="Renseignements administratifs" data-en="Administrative information">
          Renseignements administratifs
        </button>
        <div class="collapse show" id="collapseOne">
          <div class="card card-body">
            <!-- Section General -->
            <section key="General">
              <h3 class="lang-text" data-fr="Général" data-en="General">Général</h3>
              <div class="row">
                <div class="col-md-6 mb-3 relative" ddiShema="stdyDscr/citation/titlStmt/titl">
                  <?php nada_renderInputGroup("Titre de l'étude", "Title", "stdyDscr/citation/titlStmt/titl", "textarea", [], true, true, "Titre de l'étude", "Study title"); ?>
                </div>
                <div class="col-md-6 mb-3" ddiShema="stdyDscr/citation/titlStmt/altTitl">
                  <?php nada_renderInputGroup("Acronyme", "Acronym", "stdyDscr/citation/titlStmt/altTitl", "text", [], true, false, "Acronyme de l'étude", "Study acronym"); ?>
                </div>
              </div>
              <section key="RegulatoryRequirements">
                <h4 class="lang-text"
                  data-fr="Pré-requis réglementaires"
                  data-en="Regulatory requirements">
                  Pré-requis réglementaires
                </h4>

                <section key="ObtainedAuthorization">
                  <h5 class="lang-text"
                    data-fr="Autorisations ou avis obtenus"
                    data-en="Authorizations or approvals obtained">
                    Autorisations ou avis obtenus
                  </h5>


                  <div id="repeater-ObtainedAuthorization" class="repeaterBlock">
                    <div class="mb-4 repeater-item" data-section="otherAuthorizingAgency">
                      <?php if ($mode != "detail") :  ?>
                        <button type="button" class="btn-remove btn-remove-section ">
                          <span class="dashicons dashicons-trash"></span>
                          <span class="lang-text"
                            data-fr="Supprimer"
                            data-en="Delete">
                            Supprimer
                          </span>

                        </button>
                      <?php endif ?>


                      <div class="row w-100" data-parent="otherAuthorizingAgency">
                        <div class="col-md-6 mb-3">
                          <?php
                          $options_authorizingAgency = [
                            "fr" => [
                              "ANSM"    => "ANSM",
                              "CNIL"    => "CNIL",
                              "CESREES" => "CESREES",
                              "CPP"     => "CPP",
                              "Autre"   => "Autre"
                            ],
                            "en" => [
                              "ANSM"    => "ANSM",
                              "CNIL"    => "CNIL",
                              "CESREES" => "CESREES",
                              "CPP"     => "CPP",
                              "Other"   => "Other"
                            ]
                          ];

                          nada_renderInputGroup("Autorité competente", "Competent authority", "stdyDscr/studyAuthorization/authorizingAgency", "select", $options_authorizingAgency, true, true, "Instance ayant donné l’autorisation ou l’avis", "Authority/Body granting the authorization or approval");
                          ?>
                        </div>

                        <div class="col-md-6 mb-3 d-none " data-item="otherAuthorizingAgency" ddiShema="(authorizingAgency) _custom">
                          <?php nada_renderInputGroup("Autre, précision", "Other (specify)", "additional/obtainedAuthorization/otherAuthorizingAgency", "textarea", [], true, false);  ?>
                        </div>
                      </div>


                    </div>
                  </div>

                  <div class="d-flex btnBlockRepeater">
                    <button type="button" class="btn-add mt-2" id="add-ObtainedAuthorization">
                      <span class="dashicons dashicons-plus"></span>
                      <span class="lang-text"
                        data-fr="Ajouter"
                        data-en="Add">
                        Ajouter
                      </span>
                    </button>
                  </div>
                </section>

                <div class="row">
                  <div class="col-md-6 mb-3" ddiShema="_custom">
                    <?php
                    $options_authorizingAgency = [
                      "MR-001"    => "MR-001",
                      "MR-002"    => "MR-002",
                      "MR-003"    => "MR-003",
                      "MR-004"    => "MR-004"
                    ];
                    nada_renderInputGroup("Déclaration de conformité", "Declaration of conformity", "additional/regulatoryRequirements/conformityDeclaration", "select", $options_authorizingAgency, true, false);
                    ?>
                  </div>
                </div>
              </section>
            </section>


            <!-- Section PrimaryInvestigator -->
            <section key="PrimaryInvestigator">
              <h3 class="lang-text"
                data-fr="Investigateur principal"
                data-en="Principal investigator">
                Investigateur principal
              </h3>
              <?php if ($mode != "detail") {   ?>
                <div class="row">
                  <div class="col-md-12 mb-3">
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
                    nada_renderInputGroup("Je suis le PI responsable (créateur de l'étude)", "I am the responsible PI (creator of the study)", "creatorIsPi", "radio", $options_boolean, true, true); ?>
                  </div>
                  <div class="col-md-6 mb-3 d-none" id="creatorIsPiEmail">
                    <?php nada_renderInputGroup("Email PI", "Email PI", "pi-email", "email", [], true, true); ?>
                  </div>
                </div>
              <?php }  ?>

              <div class="row">
                <div id="repeater-PrimaryInvestigator" class="repeaterBlock">


                  <div class="mb-4 repeater-item">
                    <button type="button" class="btn-remove btn-remove-section ">
                      <span class="dashicons dashicons-trash"></span>
                      <span class="lang-text"
                        data-fr="Supprimer"
                        data-en="Delete">
                        Supprimer
                      </span>
                    </button>

                    <div class="col-md-6 mb-3" ddiShema="stdyDscr/citation/rspStmt/AuthEnty">
                      <?php nada_renderInputGroup("Prénom NOM de l'investigateur principal", "PI name", "stdyDscr/citation/rspStmt/AuthEnty", "text", [], true, true, null, null, true); ?>
                    </div>
                    <section key="PersonPID">
                      <h4 class="lang-text"
                        data-fr="Identification de l'investigateur principal"
                        data-en="Identification of the principal investigator">
                        Identification de l'investigateur principal
                      </h4>

                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <?php
                          $options_PIDSchema = [
                            "ORCID"    => "ORCID",
                            "IDref"    => "IDref"
                          ];
                          nada_renderInputGroup("Type d'identifiant de l'investigateur principal", "Person identifier type", "stdyDscr/citation/rspStmt/AuthEnty/ExtLink/title", "select", $options_PIDSchema, true, true);
                          ?>
                        </div>
                        <div class="col-md-6 mb-3">
                          <?php nada_renderInputGroup("Identifiant de l'investigateur principal", "Person identifier URI", "stdyDscr/citation/rspStmt/AuthEnty/ExtLink/ExtLink/URI", "url", [], true, true); ?>
                        </div>
                      </div>
                    </section>

                    <section key="Affiliation">
                      <h4 class="lang-text"
                        data-fr="Affiliation de l'investigateur principal"
                        data-en="Affiliation of the principal investigator">
                        Affiliation de l'investigateur principal
                      </h4>
                      <div class="row">
                        <div class="col-md-12 mb-3" ddiShema="stdyDscr/citation/rspStmt/othId/affiliation">
                          <?php nada_renderInputGroup("Nom de l'organisation", "Organisation name", "stdyDscr/citation/rspStmt/AuthEnty/affiliation", "text", [], true, true, null, null, true); ?>
                        </div>
                      </div>
                      <section key="OrganisationPID">
                        <h5 class="lang-text"
                          data-fr="Identification de l'organisme"
                          data-en="Identification of the organization">
                          Identification de l'organisme
                        </h5>
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <?php
                            $options_PIDSchema = [
                              "ROR"    => "ROR",
                              "RNSR"    => "RNSR",
                              "SIRENE"    => "SIRENE"
                            ];
                            nada_renderInputGroup("Type d'identifiant organisation", "Organisation identifier type", "stdyDscr/citation/rspStmt/AuthEnty/affiliation/ExtLink/title", "select", $options_PIDSchema, true, true);
                            ?>
                          </div>
                          <div class="col-md-6 mb-3">
                            <?php nada_renderInputGroup("URI de l'organisation", "Organisation identifier URI", "stdyDscr/citation/rspStmt/AuthEnty/affiliation/ExtLink/URI", "url", [], true, true); ?>
                          </div>
                        </div>
                      </section>
                    </section>
                  </div>
                </div>
                <div class="d-flex btnBlockRepeater">
                  <button type="button" class="btn-add mt-2" id="add-PrimaryInvestigator">
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

            <!-- Section Contributor -->
            <section key="Contributor">
              <h3 class="lang-text"
                data-fr="Membre de l'équipe de recherche"
                data-en="Research team member">
                Membre de l'équipe de recherche
              </h3>

              <div id="repeater-Contributor" class="repeaterBlock">
                <div class="mb-4 repeater-item">
                  <button type="button" class="btn-remove btn-remove-section ">
                    <span class="dashicons dashicons-trash"></span>
                    <span class="lang-text"
                      data-fr="Supprimer"
                      data-en="Delete">
                      Supprimer
                    </span>

                  </button>
                  <div class="row w-100">
                    <div class="col-md-6 mb-3">
                      <input type="hidden" name="stdyDscr/citation/rspStmt/othId/type" value="contributor" />
                      <?php nada_renderInputGroup("Prénom NOM du membre de l’équipe", "Last name – First name", "stdyDscr/citation/rspStmt/othId/type_contributor", "text", [], true, true, null, null, true); ?>
                    </div>
                  </div>

                  <section key="PersonPID">
                    <h4 class="lang-text"
                      data-fr="Identification du membre de l’équipe"
                      data-en="Identification of the team member">
                      Identification du membre de l’équipe
                    </h4>

                    <div id="repeater-PersonPIDContributor" class="repeaterBlock">
                      <div class="mb-4 repeater-item">
                        <button type="button" class="btn-remove btn-remove-section ">
                          <span class="dashicons dashicons-trash"></span>
                          <span class="lang-text"
                            data-fr="Supprimer"
                            data-en="Delete">
                            Supprimer
                          </span>
                        </button>


                        <div class="row w-100">
                          <div class="col-md-6 mb-3">
                            <?php
                            $options_PIDSchema = [
                              "ORCID"    => "ORCID",
                              "IDref"    => "IDref"
                            ];
                            nada_renderInputGroup("Type d'identifiant du membre de l’équipe", "Person identifier type", "stdyDscr/citation/rspStmt/othId/type_contributor/ExtLink/title", "select", $options_PIDSchema, true, true);
                            ?>
                          </div>
                          <div class="col-md-6 mb-3">
                            <?php nada_renderInputGroup("Identifiant du membre de l’équipe", "Person identifier URI", "stdyDscr/citation/rspStmt/othId/type_contributor/ExtLink/URI", "url", [], true, true); ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex btnBlockRepeater">
                      <button type="button" class="btn-add mt-2" id="add-PersonPIDContributor">
                        <span class="dashicons dashicons-plus"></span>
                        <span class="lang-text"
                          data-fr="Ajouter"
                          data-en="Add">
                          Ajouter
                        </span>

                      </button>
                    </div>
                  </section>

                  <section key="Affiliation">
                    <h4 class="lang-text"
                      data-fr="Affiliation du membre de l’équipe"
                      data-en="Affiliation of the team member">
                      Affiliation du membre de l’équipe
                    </h4>
                    <div class="row">
                      <div class="col-md-12 mb-3" ddiShema="stdyDscr/citation/rspStmt/othId/affiliation">
                        <?php nada_renderInputGroup("Nom de l'organisation", "Organisation name", "stdyDscr/citation/rspStmt/othId/affiliation", "text", [], true, true, null, null, true); ?>
                      </div>
                    </div>
                    <section key="OrganisationPID">
                      <h5 class="lang-text"
                        data-fr="Identification de l'organisme"
                        data-en="Identification of the organization">
                        Identification de l'organisme
                      </h5>
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <?php
                          $options_PIDSchema = [
                            "ROR"    => "ROR",
                            "RNSR"    => "RNSR",
                            "SIRENE"    => "SIRENE"
                          ];
                          nada_renderInputGroup("Type d'identifiant de l'organisation", "Organisation identifier type", "stdyDscr/citation/rspStmt/othId/affiliation/ExtLink/title", "select", $options_PIDSchema, true, true);
                          ?>
                        </div>
                        <div class="col-md-6 mb-3">
                          <?php nada_renderInputGroup("URI de l'organisation", "Organisation identifier URI", "stdyDscr/citation/rspStmt/othId/affiliation/ExtLink/URI", "url", [], true, true); ?>
                        </div>
                      </div>
                    </section>
                  </section>
                </div>
              </div>
              <div class="d-flex btnBlockRepeater">
                <button type="button" class="btn-add mt-2" id="add-Contributor">
                  <span class="dashicons dashicons-plus"></span>
                  <span class="lang-text"
                    data-fr="Ajouter"
                    data-en="Add">
                    Ajouter
                  </span>

                </button>
              </div>
            </section>

            <!-- Section ContactPoint -->
            <section key="ContactPoint">
              <h3 class="lang-text"
                data-fr="Points de contact"
                data-en="Contact points">
                Points de contact
              </h3>

              <div id="repeater-ContactPoint" class="repeaterBlock">



                <div class="mb-4 repeater-item">
                  <button type="button" class="btn-remove btn-remove-section ">
                    <span class="dashicons dashicons-trash"></span>
                    <span class="lang-text"
                      data-fr="Supprimer"
                      data-en="Delete">
                      Supprimer
                    </span>
                  </button>
                  <div class="row w-100">
                    <div class="col-md-6 mb-3" ddiShema="stdyDscr/citation/distStmt/contact/name">
                      <?php nada_renderInputGroup("Prénom NOM du contact", "Contact name", "stdyDscr/citation/distStmt/contact", "text", [], true, true, null, null, true); ?>
                    </div>
                    <div class="col-md-6 mb-3" ddiShema="stdyDscr/citation/distStmt/contact/email">
                      <?php nada_renderInputGroup("Email du contact", "Email", "stdyDscr/citation/distStmt/contact/email", "email", [], true, false); ?>
                    </div>
                  </div>

                  <section key="Affiliation">
                    <h4 class="lang-text"
                      data-fr="Affiliation du contact"
                      data-en="Affiliation of the contact">
                      Affiliation du contact
                    </h4>

                    <div class="row">
                      <div class="col-md-12 mb-3" ddiShema="stdyDscr/citation/distStmt/contact/affiliation">
                        <?php nada_renderInputGroup("Nom de l'organisation", "Organisation name", "stdyDscr/citation/distStmt/contact/affiliation", "text", [], true, true, null, null, true); ?>
                      </div>
                    </div>
                    <section key="OrganisationPID">
                      <h5 class="lang-text"
                        data-fr="Identification de l'organisme"
                        data-en="Identification of the organization">
                        Identification de l'organisme
                      </h5>

                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <?php
                          $options_PIDSchema = [
                            "ROR"    => "ROR",
                            "RNSR"    => "RNSR",
                            "SIRENE"    => "SIRENE"
                          ];
                          nada_renderInputGroup("Type d'identifiant de l'organisation", "Organisation identifier type", "stdyDscr/citation/distStmt/contact/affiliation/ExtLink/title", "select", $options_PIDSchema, true, true);
                          ?>
                        </div>
                        <div class="col-md-6 mb-3">
                          <?php nada_renderInputGroup("URI de l'organisation", "Organisation identifier URI", "stdyDscr/citation/distStmt/contact/affiliation/ExtLink/URI", "url", [], true, true); ?>
                        </div>
                      </div>
                    </section>
                  </section>
                </div>
              </div>
              <div class="d-flex btnBlockRepeater">
                <button type="button" class="btn-add mt-2" id="add-ContactPoint">
                  <span class="dashicons dashicons-plus"></span>
                  <span class="lang-text"
                    data-fr="Ajouter"
                    data-en="Add">
                    Ajouter
                  </span>
                </button>
              </div>
            </section>

            <!-- Section FundingAgent -->
            <section key="FundingAgent">
              <h3 class="lang-text"
                data-fr="Financeur"
                data-en="Funder">
                Financeur
              </h3>

              <div id="repeater-fundingAgent" class="repeaterBlock">



                <div class="mb-4 repeater-item">
                  <button type="button" class="btn-remove btn-remove-section ">
                    <span class="dashicons dashicons-trash"></span>
                    <span class="lang-text"
                      data-fr="Supprimer"
                      data-en="Delete">
                      Supprimer
                    </span>
                  </button>

                  <div class="row w-100">
                    <div class="col-md-6 mb-3" ddiShema="stdyDscr/citation/prodStmt/fundAg">
                      <?php nada_renderInputGroup("Nom du financeur", "Funder name", "stdyDscr/citation/prodStmt/fundAg", "text", [], true, true, null, null, true); ?>
                    </div>

                    <div class="col-md-6 mb-3">
                      <?php
                      $options_PIDSchema = [
                        "fr" => [
                          "Public (France)"          => "Public (France)",
                          "Public (Europe)"          => "Public (Europe)",
                          "Industrie"                => "Industrie",
                          "Privé à but non lucratif" => "Privé à but non lucratif",
                          "Autre"                    => "Autre"
                        ],
                        "en" => [
                          "Public (France)"          => "Public (France)",
                          "Public (Europe)"          => "Public (Europe)",
                          "Industry"                 => "Industry",
                          "Private non-profit"       => "Private non-profit",
                          "Other"                    => "Other"
                        ]
                      ];
                      nada_renderInputGroup("Type de financeur", "Funder type", "additional/fundingAgent/fundingAgentType", "select", $options_PIDSchema, true, true);
                      ?>
                    </div>
                  </div>

                  <section key="FundingAgentPID">
                    <h4 class="lang-text"
                      data-fr="Identification du financeur"
                      data-en="Identification of the funder">
                      Identification du financeur
                    </h4>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <?php
                        $options_PIDSchema = [
                          "ROR"    => "ROR",
                          "RNSR"    => "RNSR",
                          "SIRENE"    => "SIRENE"
                        ];
                        nada_renderInputGroup("Type d'identifiant du financeur", "Funder identifier type", "stdyDscr/citation/prodStmt/fundAg/ExtLink/title", "select", $options_PIDSchema, true, true);
                        ?>
                      </div>
                      <div class="col-md-6 mb-3">
                        <?php nada_renderInputGroup("URI du financeur", "Funder identifier URI", "stdyDscr/citation/prodStmt/fundAg/ExtLink/URI", "url", [], true, true); ?>
                      </div>
                    </div>
                  </section>
                </div>
              </div>
              <div class="d-flex btnBlockRepeater">
                <button type="button" class="btn-add mt-2" id="add-fundingAgent">
                  <span class="dashicons dashicons-plus"></span>
                  <span class="lang-text"
                    data-fr="Ajouter"
                    data-en="Add">
                    Ajouter
                  </span>
                </button>
              </div>
            </section>

            <!-- Section OrganisationGovernance -->
            <section key="OrganisationGovernance">
              <h3 class="lang-text"
                data-fr="Organisation et gouvernance"
                data-en="Organization and governance">
                Organisation et gouvernance
              </h3>


              <section key="Sponsor">
                <h4 class="lang-text"
                  data-fr="Promoteur/Organisme responsable"
                  data-en="Sponsor/Responsible organization">
                  Promoteur/Organisme responsable
                </h4>

                <div class="row">
                  <div class="col-md-6 mb-3" ddiShema="stdyDscr/citation/prodStmt/producer">
                    <input type="hidden" name="stdyDscr/citation/prodStmt/producer/role_fr" value="sponsor" />
                    <input type="hidden" name="stdyDscr/citation/prodStmt/producer/role_en" value="sponsor" />
                    <?php nada_renderInputGroup("Nom du promoteur", "Sponsor name", "stdyDscr/citation/prodStmt/producer", "text", [], true, true, null, null, true); ?>
                  </div>

                  <div class="col-md-6 mb-3" ddiShema="_custom">
                    <?php
                    $options = [
                      "fr" => [
                        "Public (France)"          => "Public (France)",
                        "Public (Europe)"          => "Public (Europe)",
                        "Industrie"                => "Industrie",
                        "Privé à but non lucratif" => "Privé à but non lucratif",
                        "Autre"                    => "Autre"
                      ],
                      "en" => [
                        "Public (France)"          => "Public (France)",
                        "Public (Europe)"          => "Public (Europe)",
                        "Industry"                 => "Industry",
                        "Private non-profit"       => "Private non-profit",
                        "Other"                    => "Other"
                      ]
                    ];
                    nada_renderInputGroup("Statut du promoteur", "Sponsor type", "additional/sponsor/sponsorType", "select", $options, true, true);
                    ?>
                  </div>
                </div>

                <section key="SponsorPID">
                  <h5 class="lang-text"
                    data-fr="Identification du promoteur"
                    data-en="Sponsor ID">
                    Identification du promoteur
                  </h5>

                  <div class="row">
                    <div class="col-md-6 mb-3" ddiShema="stdyDscr/citation/prodStmt/producer/ExtLink/PIDSchema">
                      <input type="hidden" name="stdyDscr/citation/prodStmt/producer/ExtLink/PIDSchema/role" value="sponsor" />
                      <?php
                      $options = [
                        'ROR' => 'ROR',
                        'RNSR' => 'RNSR',
                        'SIRENE' => 'SIRENE'
                      ];
                      nada_renderInputGroup("Type d'identifiant du promoteur", "Sponsor identifier type", "stdyDscr/citation/prodStmt/producer/ExtLink/title", "select", $options, true, true);
                      ?>
                    </div>

                    <div class="col-md-6 mb-3" ddiShema="stdyDscr/citation/prodStmt/producer/ExtLink/URI">
                      <?php nada_renderInputGroup("URI du promoteur", "Sponsor identifier URL", "stdyDscr/citation/prodStmt/producer/ExtLink/uri", "url", [], true, true); ?>
                    </div>
                    <div class="col-md-12 mb-3" ddiShema="stdyDscr/citation/prodStmt/producer/ExtLink/URI">
                      <input type="hidden" name="producer/role" value="sponsor" />
                      <input type="hidden" name="producer/role" value="sponsor" />
                    </div>
                  </div>
                </section>
              </section>

              <section key="Governance">
                <h4 class="lang-text"
                  data-fr="Gouvernance"
                  data-en="Governance">
                  Gouvernance
                </h4>

                <div class="row">
                  <div class="col-md-12 mb-3" ddiShema="_custom">
                    <?php
                    $options_boolean = [
                      "fr" => [
                        "Oui"  => "Oui",
                        "Non" => "Non",
                        "Autre" => "Autre"
                      ],
                      "en" => [
                        "Yes"  => "Yes",
                        "No" => "No",
                        "Other" => "Other"
                      ]
                    ];
                    nada_renderInputGroup("Comité scientifique ou de pilotage", "Scientific or steering committee", "additional/governance/committee", "radio", $options_boolean, true, true);
                    ?>
                  </div>

                  <div class="col-md-6 mb-3 d-none" id="committeeDetailBloc">
                    <?php nada_renderInputGroup("Comité, précisions", "Committee details", "stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/value", "text", [], true, false); ?>
                    <input type="hidden" name="stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/role_fr" value="comité" />
                    <input type="hidden" name="stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/role_en" value="committee" />
                  </div>

                  <div class="col-md-6 mb-3 d-none" id="committeeDetailBlocOthers" ddiShema="stdyDscr/stdyInfo/qualityStatement/otherQualityStatement">
                    <?php nada_renderInputGroup("Autre, précisions", "Other (specify)", "stdyDscr/stdyInfo/qualityStatement/otherQualityStatement", "textarea", [], true, false); ?>
                  </div>
                </div>
              </section>

              <section key="Collaborations">
                <h4 class="lang-text"
                  data-fr="Collaborations"
                  data-en="Collaborations">
                  Collaborations
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
                    nada_renderInputGroup("Réseaux, consortiums", "Networks, consortia", "additional/collaborations/networkConsortium", "radio", $options_boolean, true, true);
                    ?>
                  </div>
                  <div class="col-md-6 mb-3 d-none" id="CollaborationDetailsPrecision">
                    <?php nada_renderInputGroup("Précisions", "Details", "stdyDscr/citation/rspStmt/othId/collaboration", "text", [], true, true); ?>
                    <input type="hidden" name="stdyDscr/citation/rspStmt/othId/type_fr" value="contributeur" />
                    <input type="hidden" name="stdyDscr/citation/rspStmt/othId/type_en" value="contributor" />
                  </div>
                </div>
              </section>
            </section>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 2 -->
    <div class="card card-body mb-4">
      <section key="StudyStatus">
        <h3 class="lang-text"
          data-fr="Statut de l'étude"
          data-en="Study status">
          Statut de l'étude
        </h3>

        <div class="row">
          <div class="col-md-6 mb-3">
            <?php
            $options_PIDSchema = [
              "fr" => [
                "Etude en cours"   => "Etude en cours",
                "Etude complétée"  => "Etude complétée",
                "Etude arrêtée"    => "Etude arrêtée",
                "Inconnu"          => "Inconnu"
              ],
              "en" => [
                "Ongoing study"    => "Ongoing study",
                "Completed study"  => "Completed study",
                "Stopped study"    => "Stopped study",
                "Unknown"          => "Unknown"
              ]
            ];
            nada_renderInputGroup("Statut de l'étude", "Study status", "stdyDscr/method/stdyClas", "select", $options_PIDSchema, true, true);
            ?>
          </div>
        </div>
      </section>
    </div>

    <!-- Section 3 -->
    <section key="Theme">
      <div class="mb-4">
        <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#collapseTwo"
          aria-expanded="true"
          aria-controls="collapseTwo"
          data-fr="Thématique"
          data-en="Theme">
          Thématique
        </button>

        <div class="collapse show" id="collapseTwo">
          <div class="card card-body">
            <section key="Theme">
              <h3 class="lang-text"
                data-fr="Thématique"
                data-en="Theme">
                Thématique
              </h3>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <input type="hidden" name="stdyDscr/stdyInfo/abstract/contentType_fr" value="but" />
                  <input type="hidden" name="stdyDscr/stdyInfo/abstract/contentType_en" value="purpose" />
                  <?php nada_renderInputGroup("Objectifs", "Objectives", "stdyDscr/stdyInfo/purpose/value", "textarea", [], true, false); ?>
                </div>

                <div class="col-md-6 mb-3">
                  <input type="hidden" name="stdyDscr/stdyInfo/abstract/contentType_fr" value="résumé" />
                  <input type="hidden" name="stdyDscr/stdyInfo/abstract/contentType_en" value="abstract" />
                  <?php nada_renderInputGroup("Résumé", "Summary", "stdyDscr/stdyInfo/abstract/value", "textarea", [], true, true); ?>
                </div>

                <div class="col-md-12 mb-3">
                  <?php
                  $options_specialites = [
                    "fr" => [
                      'Addictologie' => 'Addictologie',
                      'Allergologie' => 'Allergologie',
                      'Andrologie et urologie' => 'Andrologie et urologie',
                      'Anesthésiologie' => 'Anesthésiologie',
                      'Cardiologie et médecine vasculaire' => 'Cardiologie et médecine vasculaire',
                      'Chirurgie' => 'Chirurgie',
                      'Dermatologie' => 'Dermatologie',
                      'Endocrinologie' => 'Endocrinologie',
                      'Ethique médicale' => 'Ethique médicale',
                      'Gastroentérologie' => 'Gastroentérologie',
                      'Génétique médicale' => 'Génétique médicale',
                      'Gériatrie' => 'Gériatrie',
                      'Gynécologie et obstétrique' => 'Gynécologie et obstétrique',
                      'Hématologie' => 'Hématologie',
                      'Hépatologie' => 'Hépatologie',
                      'Immunologie médicale' => 'Immunologie médicale',
                      'Infectiologie' => 'Infectiologie',
                      'Médecine complémentaire et intégrative' => 'Médecine complémentaire et intégrative',
                      'Médecine d’urgence' => 'Médecine d’urgence',
                      'Médecine générale' => 'Médecine générale',
                      'Médecine interne' => 'Médecine interne',
                      'Médecine physique et de réadaptation' => 'Médecine physique et de réadaptation',
                      'Médecine régénérative' => 'Médecine régénérative',
                      'Médecine tropicale' => 'Médecine tropicale',
                      'Néphrologie' => 'Néphrologie',
                      'Neurologie' => 'Neurologie',
                      'Nutrition et diététique' => 'Nutrition et diététique',
                      'Odontologie' => 'Odontologie',
                      'Oncologie' => 'Oncologie',
                      'Ophtalmologie' => 'Ophtalmologie',
                      'Orthopédie et traumatologie' => 'Orthopédie et traumatologie',
                      'Oto-rhino-laryngologie' => 'Oto-rhino-laryngologie',
                      'Pédiatrie' => 'Pédiatrie',
                      'Pneumologie' => 'Pneumologie',
                      'Psychiatrie' => 'Psychiatrie',
                      'Radiologie et médecine nucléaire' => 'Radiologie et médecine nucléaire',
                      'Rhumatologie' => 'Rhumatologie',
                      'Santé publique, environnementale et du travail' => 'Santé publique, environnementale et du travail',
                      'Sciences du sport et de la condition physique' => 'Sciences du sport et de la condition physique',
                      'Sciences et services de santé' => 'Sciences et services de santé',
                      'Sciences infirmières' => 'Sciences infirmières',
                      'Transplantation' => 'Transplantation',
                      'Vénérologie' => 'Vénérologie',
                      'Pas de spécialité médicale spécifique' => 'Pas de spécialité médicale spécifique'
                    ],
                    "en" => [
                      'Addictology' => 'Addictology',
                      'Allergology' => 'Allergology',
                      'Andrology and Urology' => 'Andrology and Urology',
                      'Anesthesiology' => 'Anesthesiology',
                      'Cardiology and Vascular Medicine' => 'Cardiology and Vascular Medicine',
                      'Surgery' => 'Surgery',
                      'Dermatology' => 'Dermatology',
                      'Endocrinology' => 'Endocrinology',
                      'Medical Ethics' => 'Medical Ethics',
                      'Gastroenterology' => 'Gastroenterology',
                      'Medical Genetics' => 'Medical Genetics',
                      'Geriatrics' => 'Geriatrics',
                      'Gynecology and Obstetrics' => 'Gynecology and Obstetrics',
                      'Hematology' => 'Hematology',
                      'Hepatology' => 'Hepatology',
                      'Medical Immunology' => 'Medical Immunology',
                      'Infectiology' => 'Infectiology',
                      'Complementary and Integrative Medicine' => 'Complementary and Integrative Medicine',
                      'Emergency Medicine' => 'Emergency Medicine',
                      'General Medicine' => 'General Medicine',
                      'Internal Medicine' => 'Internal Medicine',
                      'Physical and Rehabilitation Medicine' => 'Physical and Rehabilitation Medicine',
                      'Regenerative Medicine' => 'Regenerative Medicine',
                      'Tropical Medicine' => 'Tropical Medicine',
                      'Nephrology' => 'Nephrology',
                      'Neurology' => 'Neurology',
                      'Nutrition and Dietetics' => 'Nutrition and Dietetics',
                      'Dentistry' => 'Dentistry',
                      'Oncology' => 'Oncology',
                      'Ophthalmology' => 'Ophthalmology',
                      'Orthopedics and Traumatology' => 'Orthopedics and Traumatology',
                      'Otorhinolaryngology' => 'Otorhinolaryngology',
                      'Pediatrics' => 'Pediatrics',
                      'Pulmonology' => 'Pulmonology',
                      'Psychiatry' => 'Psychiatry',
                      'Radiology and Nuclear Medicine' => 'Radiology and Nuclear Medicine',
                      'Rheumatology' => 'Rheumatology',
                      'Public, Environmental and Occupational Health' => 'Public, Environmental and Occupational Health',
                      'Sports and Exercise Science' => 'Sports and Exercise Science',
                      'Health Sciences and Services' => 'Health Sciences and Services',
                      'Nursing Sciences' => 'Nursing Sciences',
                      'Transplantation' => 'Transplantation',
                      'Venereology' => 'Venereology',
                      'No specific medical specialty' => 'No specific medical specialty'
                    ]
                  ];

                  nada_renderInputGroup("Spécialité médicale", "Medical field", "stdyDscr/stdyInfo/subject/topcClas[]/value", "checkbox", $options_specialites, true, true);
                  ?>
                  <input type="hidden" name="stdyDscr/stdyInfo/subject/topcClas[]/vocab" value="health theme" />
                </div>

                <div class="col-md-6 mb-3" ddiShema="stdyDscr/stdyInfo/subject/topcClas">
                  <?php
                  $options_pathologies = [
                    "CIM-11" => "CIM-11"
                  ];

                  nada_renderInputGroup("Groupe de pathologies", "Pathology group", "stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11", "select-multiple", $options_pathologies, true, true);
                  ?>
                  <input type="hidden" name="stdyDscr/stdyInfo/subject/topcClas[]/vocab/cim-11" value="cim-11" />
                </div>

                <div class="col-md-6 mb-3" ddiShema="stdyDscr/stdyInfo/subject/keyword">
                  <?php nada_renderInputGroup("Mots-clés libres", "Keywords", "stdyDscr/stdyInfo/subject/keyword", "textarea", [], true, true); ?>
                </div>

                <div class="col-md-12 mb-3" ddiShema="stdyDscr/stdyInfo/subject/topcClas">
                  <?php
                  $options_determinants = [
                    "fr" => [
                      "Déterminants socio-démographiques et économiques" => "Déterminants socio-démographiques et économiques",
                      "Déterminants socio-démographiques et économiques : Emploi" => "Déterminants socio-démographiques et économiques : Emploi",
                      "Déterminants socio-démographiques et économiques : Revenu" => "Déterminants socio-démographiques et économiques : Revenu",
                      "Déterminants socio-démographiques et économiques : Logement" => "Déterminants socio-démographiques et économiques : Logement",
                      "Déterminants socio-démographiques et économiques : Niveau d'études" => "Déterminants socio-démographiques et économiques : Niveau d'études",
                      "Déterminants socio-démographiques et économiques : Situation familiale" => "Déterminants socio-démographiques et économiques : Situation familiale",
                      "Déterminants socio-démographiques et économiques : Genre" => "Déterminants socio-démographiques et économiques : Genre",
                      "Déterminants socio-démographiques et économiques : Âge" => "Déterminants socio-démographiques et économiques : Âge",
                      "Déterminants socio-démographiques et économiques : Autre" => "Déterminants socio-démographiques et économiques : Autre",

                      "Déterminants environnementaux" => "Déterminants environnementaux",
                      "Déterminants environnementaux : Qualité de l'air" => "Déterminants environnementaux : Qualité de l'air",
                      "Déterminants environnementaux : Qualité de l'eau" => "Déterminants environnementaux : Qualité de l'eau",
                      "Déterminants environnementaux : Climat" => "Déterminants environnementaux : Climat",
                      "Déterminants environnementaux : Autre" => "Déterminants environnementaux : Autre",

                      "Déterminants liés au système de santé" => "Déterminants liés au système de santé",
                      "Déterminants liés au système de santé : Accès aux soins" => "Déterminants liés au système de santé : Accès aux soins",
                      "Déterminants liés au système de santé : Qualité des soins" => "Déterminants liés au système de santé : Qualité des soins",
                      "Déterminants liés au système de santé : Consommation des soins" => "Déterminants liés au système de santé : Consommation des soins",
                      "Déterminants liés au système de santé : Autre" => "Déterminants liés au système de santé : Autre",

                      "Déterminants comportementaux" => "Déterminants comportementaux",
                      "Déterminants comportementaux : Addiction" => "Déterminants comportementaux : Addiction",
                      "Déterminants comportementaux : Alimentation" => "Déterminants comportementaux : Alimentation",
                      "Déterminants comportementaux : Activité physique" => "Déterminants comportementaux : Activité physique",
                      "Déterminants comportementaux : Littératie en santé" => "Déterminants comportementaux : Littératie en santé",
                      "Déterminants comportementaux : Hygiène" => "Déterminants comportementaux : Hygiène",
                      "Déterminants comportementaux : Pratiques sexuelles" => "Déterminants comportementaux : Pratiques sexuelles",
                      "Déterminants comportementaux : Comportements à risque" => "Déterminants comportementaux : Comportements à risque",
                      "Déterminants comportementaux : Autre" => "Déterminants comportementaux : Autre",

                      "Déterminants biologiques" => "Déterminants biologiques",
                      "Déterminants biologiques : Prédisposition génétique" => "Déterminants biologiques : Prédisposition génétique",
                      "Déterminants biologiques : Vieillissement" => "Déterminants biologiques : Vieillissement",
                      "Déterminants biologiques : Sexe" => "Déterminants biologiques : Sexe",
                      "Déterminants biologiques : Autre" => "Déterminants biologiques : Autre",

                      "Autre" => "Autre"
                    ],
                    "en" => [
                      "Socio-demographic and economic determinants" => "Socio-demographic and economic determinants",
                      "Socio-demographic and economic determinants: Employment" => "Socio-demographic and economic determinants: Employment",
                      "Socio-demographic and economic determinants: Income" => "Socio-demographic and economic determinants: Income",
                      "Socio-demographic and economic determinants: Housing" => "Socio-demographic and economic determinants: Housing",
                      "Socio-demographic and economic determinants: Education level" => "Socio-demographic and economic determinants: Education level",
                      "Socio-demographic and economic determinants: Family situation" => "Socio-demographic and economic determinants: Family situation",
                      "Socio-demographic and economic determinants: Gender" => "Socio-demographic and economic determinants: Gender",
                      "Socio-demographic and economic determinants: Age" => "Socio-demographic and economic determinants: Age",
                      "Socio-demographic and economic determinants: Other" => "Socio-demographic and economic determinants: Other",

                      "Environmental determinants" => "Environmental determinants",
                      "Environmental determinants: Air quality" => "Environmental determinants: Air quality",
                      "Environmental determinants: Water quality" => "Environmental determinants: Water quality",
                      "Environmental determinants: Climate" => "Environmental determinants: Climate",
                      "Environmental determinants: Other" => "Environmental determinants: Other",

                      "Health system determinants" => "Health system determinants",
                      "Health system determinants: Access to care" => "Health system determinants: Access to care",
                      "Health system determinants: Quality of care" => "Health system determinants: Quality of care",
                      "Health system determinants: Health care use" => "Health system determinants: Health care use",
                      "Health system determinants: Other" => "Health system determinants: Other",

                      "Behavioral determinants" => "Behavioral determinants",
                      "Behavioral determinants: Addiction" => "Behavioral determinants: Addiction",
                      "Behavioral determinants: Diet" => "Behavioral determinants: Diet",
                      "Behavioral determinants: Physical activity" => "Behavioral determinants: Physical activity",
                      "Behavioral determinants: Health literacy" => "Behavioral determinants: Health literacy",
                      "Behavioral determinants: Hygiene" => "Behavioral determinants: Hygiene",
                      "Behavioral determinants: Sexual practices" => "Behavioral determinants: Sexual practices",
                      "Behavioral determinants: Risk behaviors" => "Behavioral determinants: Risk behaviors",
                      "Behavioral determinants: Other" => "Behavioral determinants: Other",

                      "Biological determinants" => "Biological determinants",
                      "Biological determinants: Genetic predisposition" => "Biological determinants: Genetic predisposition",
                      "Biological determinants: Aging" => "Biological determinants: Aging",
                      "Biological determinants: Sex" => "Biological determinants: Sex",
                      "Biological determinants: Other" => "Biological determinants: Other",

                      "Other" => "Other"
                    ]
                  ];

                  nada_renderInputGroup("Déterminants de santé", "Health determinants", "stdyDscr/stdyInfo/subject/topcClas", "checkbox", $options_determinants, true, true);
                  ?>
                  <input type="hidden" name="stdyDscr/stdyInfo/subject/topcClas/vocab" value="health determinant" />
                </div>

                <div class="col-md-6 mb-3" ddiShema="_custom">
                  <?php nada_renderInputGroup("Information complémentaire précisant l'étude", "Additional Study Information", "additional/theme/complementaryInformation", "textarea", [], true, false); ?>
                </div>

                <div class="col-md-12 mb-3" ddiShema="_custom">
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
                  nada_renderInputGroup("Maladies rares", "Rare diseases", "additional/theme/RareDiseases", "radio", $options_boolean, true, true);
                  ?>
                </div>
              </div>
            </section>
          </div>
        </div>
      </div>
    </section>
  </section>
</div>
<?php
/*
Template Name: Add Study section 2
*/
?>

<?php
include('add-study-input.php');
?>

<div class="tab-pane" id="step-2">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <section key="StudyMethodology">
    <h1 class="lang-text"
      data-fr="Méthodologie de l'étude"
      data-en="Study methodology">
      Méthodologie de l'étude
    </h1>

    <!-- section StudyMethodology -->
    <section key="StudyMethodology">
      <div class="mb-4">
        <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#collapseThree"
          aria-expanded="true"
          aria-controls="collapseThree"
          data-fr="Schéma d'étude"
          data-en="Study design">
          Schéma d'étude
        </button>

        <div class="collapse show" id="collapseThree">
          <div class="card card-body">
            <section key="StudySchema">
              <h3 class="lang-text"
                data-fr="Schéma d'étude"
                data-en="Study design">
                Schéma d'étude
              </h3>

              <div class="row">
                <div class="col-md-6 mb-3" ddiShema="stdyDscr/stdyInfo/sumDscr/anlyUnit">
                  <?php
                  $options = [
                    "fr" => [
                      "Individus" => "Individus"
                    ],
                    "en" => [
                      "Individuals" => "Individuals"
                    ]
                  ];
                  nada_renderInputGroup("Unité d'analyse", "Analysis unit", "stdyDscr/stdyInfo/sumDscr/anlyUnitFake", "select", $options, true, true);

                  ?>
                  <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/anlyUnit_fr" value="Individus">
                  <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/anlyUnit_en" value="Individus">
                </div>
                <div class="col-md-6 mb-3" ddiShema="stdyDscr/method/notes">
                  <?php
                  $options = [
                    "fr" => [
                      "observationnelle"   => "Observationnelle",
                      "interventionnelle"  => "Interventionnelle"
                    ],
                    "en" => [
                      "observational"      => "Observational",
                      "interventional"     => "Interventional"
                    ]
                  ];
                  nada_renderInputGroup("Type de recherche", "Study Type", "stdyDscr/method/notes/subject_researchType", "select", $options, true, true);
                  ?>
                  <input type="hidden" name="stdyDscr/method/notes/subject_researchType/subject_fr" value="type de recherche" />
                  <input type="hidden" name="stdyDscr/method/notes/subject_researchType/subject_en" value="research type" />
                </div>
              </div>

              <section key="InterventionalStudy" class="d-none mb-5" id="interventionalStudyBloc">
                <h3 class="lang-text"
                  data-fr="Étude interventionnelle (expérimentale)"
                  data-en="Interventional study">
                  Étude interventionnelle (expérimentale)
                </h3>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <?php
                    $options_researchTypes = [
                      "fr" => [
                        "Traitement" => "Traitement",
                        "Recherche sur les services de santé" => "Recherche sur les services de santé",
                        "Prévention" => "Prévention",
                        "Recherche fondamentale" => "Recherche fondamentale",
                        "Diagnostic" => "Diagnostic",
                        "Faisabilité des dispositifs" => "Faisabilité des dispositifs",
                        "Soins de soutien" => "Soins de soutien",
                        "Dépistage" => "Dépistage",
                        "Autre" => "Autre"
                      ],
                      "en" => [
                        "Treatment" => "Treatment",
                        "Health Services Research" => "Health Services Research",
                        "Prevention" => "Prevention",
                        "Basic Research" => "Basic Research",
                        "Diagnosis" => "Diagnosis",
                        "Device Feasibility" => "Device Feasibility",
                        "Supportive Care" => "Supportive Care",
                        "Screening" => "Screening",
                        "Other" => "Other"
                      ]
                    ];
                    nada_renderInputGroup("Object principal de la recherche", "Research Purpose", "additional/interventionalStudy/researchPurpose", "checkbox", $options_researchTypes, true, true);
                    ?>
                  </div>
                  <div class="col-md-6 mb-3">
                    <?php
                    $options_phases = [
                      "fr" => [
                        'Phase 1' => 'Phase 1',
                        'Phase 2' => 'Phase 2',
                        'Phase 3' => 'Phase 3',
                        'Phase 4' => 'Phase 4',
                        'N/A'     => 'N/A'
                      ],
                      "en" => [
                        'Phase 1' => 'Phase 1',
                        'Phase 2' => 'Phase 2',
                        'Phase 3' => 'Phase 3',
                        'Phase 4' => 'Phase 4',
                        'N/A'     => 'N/A'
                      ]
                    ];

                    nada_renderInputGroup("Phase de l'essai", "Study Phase", "additional/interventionalStudy/trialPhase", "checkbox", $options_phases, true, true);
                    ?>
                  </div>
                  <div class="col-md-6 mb-3">
                    <?php
                    $options_studyDesigns = [
                      "fr" => [
                        'Simple bras'        => 'Simple bras',
                        'Groupes parallèles' => 'Groupes parallèles',
                        'Schéma croisé'      => 'Schéma croisé',
                        'Plan factoriel'     => 'Plan factoriel',
                        'Séquentiel'         => 'Séquentiel'
                      ],
                      "en" => [
                        'Single Arm'         => 'Single Arm',
                        'Parallel Groups'    => 'Parallel Groups',
                        'Crossover Design'   => 'Crossover Design',
                        'Factorial Design'   => 'Factorial Design',
                        'Sequential'         => 'Sequential'
                      ]
                    ];
                    nada_renderInputGroup("Schéma d’intervention", "Intervention model", "additional/interventionalStudy/interventionalStudyModel", "select", $options_studyDesigns, true, true);
                    ?>
                  </div>
                </div>
                <section key="Allocation">
                  <h4 class="lang-text"
                    data-fr="Allocation"
                    data-en="Allocation">
                    Allocation
                  </h4>
                  <div class="row">
                    <div class="col-md-6 mb-3" ddiShema="_custom">
                      <?php
                      $options_randomisation = [
                        "fr" => [
                          'Randomisé'      => 'Randomisé',
                          'Non randomisé'  => 'Non randomisé',
                          'N/A'            => 'N/A'
                        ],
                        "en" => [
                          'Randomized'     => 'Randomized',
                          'Non-randomized' => 'Non-randomized',
                          'N/A'            => 'N/A'
                        ]
                      ];

                      nada_renderInputGroup("Mode d'allocation", "Allocation Type", "additional/allocation/allocationMode", "select", $options_randomisation, true, true);
                      ?>
                    </div>
                    <div class="col-md-6 mb-3" ddiShema="_custom">
                      <?php
                      $options_allocation = [
                        "fr" => [
                          'Individuelle' => 'Individuelle',
                          'Cluster'      => 'Cluster'
                        ],
                        "en" => [
                          'Individual'   => 'Individual',
                          'Cluster'      => 'Cluster'
                        ]
                      ];
                      nada_renderInputGroup("Unité d'allocation", "Allocation Unit", "additional/allocation/allocationUnit", "select", $options_allocation, true, true);
                      ?>
                    </div>
                  </div>
                </section>
                <section key="Masking">
                  <h4 class="lang-text"
                    data-fr="Insu"
                    data-en="Masking">
                    Insu
                  </h4>
                  <div class="row">
                    <div class="col-md-6 mb-3" ddiShema="additional/masking/maskingType">
                      <?php
                      $options_masking = [
                        "fr" => [
                          'Avec insu' => 'Avec insu',
                          'Ouvert'    => 'Ouvert'
                        ],
                        "en" => [
                          'Blinded'   => 'Blinded',
                          'Open label' => 'Open label'
                        ]
                      ];

                      nada_renderInputGroup("Type Insu", "Masking Type", "additional/masking/maskingType", "select", $options_masking, true, true);
                      ?>
                    </div>
                    <div class="col-md-6 mb-3" ddiShema="_custom">
                      <?php
                      $options_roles = [
                        "fr" => [
                          'Participant'                   => 'Participant',
                          'Personnel soignant'            => 'Personnel soignant',
                          "Membre de l'équipe de recherche" => "Membre de l'équipe de recherche",
                          'Evaluateur des résultats'      => 'Evaluateur des résultats'
                        ],
                        "en" => [
                          'Participant'                   => 'Participant',
                          'Healthcare Provider'           => 'Healthcare Provider',
                          "Research Team Member"          => "Research Team Member",
                          'Outcome Assessor'              => 'Outcome Assessor'
                        ]
                      ];
                      nada_renderInputGroup("Insu en aveugle, précisions", "Blinded Masking Details", "additional/masking/blindedMaskingDetails", "checkbox", $options_roles, true, true);
                      ?>
                    </div>
                  </div>
                </section>
                <section key="Arms" data-containerToDuplicate="armsBloc">
                  <h4 class="lang-text"
                    data-fr="Bras / Groupe"
                    data-en="Arms / Groups">
                    Bras / Groupe
                  </h4>

                  <div class="row">
                    <div class="col-md-12 mb-1" data-inputToDuplicate="armsBloc">
                      <?php nada_renderInputGroup("Nombre des bras", "Number of Arms", "additional/arms/armsNumber", "text", [], true, true, null, null, true); ?>
                    </div>
                    <section class="card card-body mx-4">
                      <section class="repeater-item mb-2" key="Arm" data-contentToDuplicate="armsBloc">
                        <h5 class="lang-text"
                          data-fr="Ajouter un bras"
                          data-en="Add an arm">
                          Ajouter un bras <span class="number"></span>
                        </h5>
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <?php
                            $options_interventions = [
                              "fr" => [
                                'Expérimental'         => 'Expérimental',
                                'Comparateur actif'    => 'Comparateur actif',
                                'Comparateur placebo'  => 'Comparateur placebo',
                                'Comparateur fictif'   => 'Comparateur fictif',
                                'Sans intervention'    => 'Sans intervention',
                                'Autre'                => 'Autre'
                              ],
                              "en" => [
                                'Experimental'         => 'Experimental',
                                'Active Comparator'    => 'Active Comparator',
                                'Placebo Comparator'   => 'Placebo Comparator',
                                'Sham Comparator'      => 'Sham Comparator',
                                'No Intervention'      => 'No Intervention',
                                'Other'                => 'Other'
                              ]
                            ];
                            nada_renderInputGroup("Type de bras", "Arm Type", "additional/arms/armsType", "select", $options_interventions, true, false);
                            ?>
                          </div>
                          <div class="col-md-6 mb-3 d-none arm-type-other-bloc" id="ArmTypeOtherBloc">
                            <?php nada_renderInputGroup("Autre type de bras, précisions", "Other arm type, details", "additional/arms/armsTypeOther", "textarea", [], true, true); ?>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <?php nada_renderInputGroup("Nom du bras", "Arm Name", "additional/arms/armsName", "text", [], true, true, null, null, true); ?>
                          </div>
                          <div class="col-md-6 mb-3">
                            <?php nada_renderInputGroup("Description du bras", "Arm Description", "additional/arms/armsDescription", "text", [], true, false); ?>
                          </div>
                        </div>
                      </section>
                    </section>
                  </div>
                </section>
              </section>

              <section key="Intervention" class="mb-5">
                <h3 class="lang-text"
                  data-fr="Intervention/exposition"
                  data-en="Intervention/Exposure">
                  Intervention/exposition
                </h3>

                <div id="repeater-intervExpo" class="repeaterBlock">



                  <div class="mb-4 repeater-item" data-section="intervExpo">
                    <button type="button" class="btn-remove btn-remove-section ">
                      <span class="dashicons dashicons-trash"></span>
                      <span class="lang-text"
                        data-fr="Supprimer"
                        data-en="Delete">
                        Supprimer
                      </span>
                    </button>

                    <div class="row w-100" data-section="InterventionTypeOtherBloc">
                      <div class="col-md-12 mb-3">
                        <?php nada_renderInputGroup("Nom de l'intervention/exposition", "Intervention/exposition Name", "additional/intervention/interventionName", "text", [], true, true, null, null, true); ?>
                      </div>
                      <div class="col-md-6 mb-3" ddiShema="_custom" data-parent="InterventionTypeOtherBloc">
                        <?php
                        $options_interventionType = [
                          "fr" => [
                            'Médicament'             => 'Médicament',
                            'Dispositif médical'     => 'Dispositif médical',
                            'Biologique / vaccin'    => 'Biologique / vaccin',
                            'Procédure / chirurgie'  => 'Procédure / chirurgie',
                            'Radiation'              => 'Radiation',
                            'Comportementale'        => 'Comportementale',
                            'Génétique'              => 'Génétique',
                            'Complément alimentaire' => 'Complément alimentaire',
                            'Test diagnostique'      => 'Test diagnostique',
                            'Combinaison'            => 'Combinaison',
                            'Santé publique'         => 'Santé publique',
                            'Autre'                  => 'Autre'
                          ],
                          "en" => [
                            'Drug'                   => 'Drug',
                            'Medical Device'         => 'Medical Device',
                            'Biological / Vaccine'   => 'Biological / Vaccine',
                            'Procedure / Surgery'    => 'Procedure / Surgery',
                            'Radiation'              => 'Radiation',
                            'Behavioral'             => 'Behavioral',
                            'Genetic'                => 'Genetic',
                            'Dietary Supplement'     => 'Dietary Supplement',
                            'Diagnostic Test'        => 'Diagnostic Test',
                            'Combination'            => 'Combination',
                            'Public Health'          => 'Public Health',
                            'Other'                  => 'Other'
                          ]
                        ];
                        nada_renderInputGroup("Type d'intervention/exposition", "Intervention/Exposition Type", "additional/intervention/interventionType", "select", $options_interventionType, true, false);
                        ?>
                      </div>
                      <div class="col-md-12 mb-3 d-none intervention-type-other" data-item="InterventionTypeOtherBloc">
                        <?php nada_renderInputGroup(
                          "Autre type d'intervention/exposition, précisions",
                          "Other intervention/exposition type, details",
                          "additional/intervention/interventionTypeOther",
                          "textarea",
                          [],
                          true,
                          false
                        ); ?>
                      </div>

                      <div class="col-md-12 mb-3">
                        <?php nada_renderInputGroup("Description d'intervention/exposition", "Intervention/Exposition Description", "additional/intervention/interventionDescription", "textarea", [], true, false); ?>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="d-flex btnBlockRepeater">
                  <button type="button" class="btn-add mt-2" id="add-intervExpo">
                    <span class="dashicons dashicons-plus"></span>
                    <span class="lang-text"
                      data-fr="Ajouter"
                      data-en="Add">
                      Ajouter
                    </span>
                  </button>
                </div>

                <section key="ObservationalStudy" class="d-none mb-5" id="observationalStudyBloc">
                  <h4 class="lang-text"
                    data-fr="Observationnelle"
                    data-en="Observational">
                    Observationnelle
                  </h4>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <?php
                      $options_observationalType = [
                        "fr" => [
                          'Étude cas-témoins'              => 'Étude cas-témoins',
                          'Étude longitudinale ou cohorte' => 'Étude longitudinale ou cohorte',
                          'Registres de morbidité'         => 'Registres de morbidité',
                          'Registres des actes'            => 'Registres des actes',
                          'Transversale : répétée'         => 'Transversale : répétée',
                          'Transversale : non répétée'     => 'Transversale : non répétée',
                          'Étude de cas'                   => 'Étude de cas',
                          'Autre'                          => 'Autre'
                        ],
                        "en" => [
                          'Case-Control Study'             => 'Case-Control Study',
                          'Longitudinal / Cohort Study'    => 'Longitudinal / Cohort Study',
                          'Morbidity Registry'             => 'Morbidity Registry',
                          'Procedure Registry'             => 'Procedure Registry',
                          'Cross-sectional: Repeated'      => 'Cross-sectional: Repeated',
                          'Cross-sectional: Non-repeated'  => 'Cross-sectional: Non-repeated',
                          'Case Study'                     => 'Case Study',
                          'Other'                          => 'Other'
                        ]
                      ];
                      nada_renderInputGroup("Modèle de l’étude observationnelle", "Observational Study  Design", "stdyDscr/method/dataColl/timeMeth", "select", $options_observationalType, true, true);
                      ?>
                    </div>

                    <div class="col-md-6 mb-3">
                      <div key="OtherResearchType" class="d-none" id="OtherResearchTypeBloc">
                        <?php nada_renderInputGroup("Autre type de recherche, précisions", "Other Research Type Details", "additional/otherResearchType/otherResearchTypeDetails", "textarea", [], true, true); ?>
                      </div>
                    </div>

                  </div>
                  <section key="CohortLongitudinal">
                    <h5 class="lang-text"
                      data-fr="Étude longitudinale ou cohorte"
                      data-en="Longitudinal or cohort study">
                      Étude longitudinale ou cohorte
                    </h5>
                    <div class="row">
                      <div class="col-md-6 mb-3" ddiShema="_custom">
                        <?php
                        $options_recruitment = [
                          "fr" => [
                            'Avec recrutement rétrospectif' => 'Avec recrutement rétrospectif',
                            'Avec recrutement prospectif'   => 'Avec recrutement prospectif',
                            'Autre'                         => 'Autre'
                          ],
                          "en" => [
                            'With Retrospective Recruitment' => 'With Retrospective Recruitment',
                            'With Prospective Recruitment'   => 'With Prospective Recruitment',
                            'Other'                          => 'Other'
                          ]
                        ];
                        nada_renderInputGroup("Temporalité du recrutement", "Recruitment Timing", "additional/cohortLongitudinal/recrutementTiming", "checkbox", $options_recruitment, true, true);
                        ?>
                      </div>
                    </div>
                  </section>


                </section>
              </section>

              <section key="InclusionGroups" data-containerToDuplicate="inclusionGroupsBloc">
                <h3 class="lang-text"
                  data-fr="Groupes à l'inclusion"
                  data-en="Inclusion Groups">
                  Groupes à l'inclusion
                </h3>

                <div class="row">
                  <div class="col-md-12 mb-3" ddiShema="_custom" data-inputToDuplicate="inclusionGroupsBloc">
                    <?php nada_renderInputGroup("Nombre de groupes à l'inclusion", "Inclusion groups number", "additional/inclusionGroups/nrInclusionGroups", "text", [], true, true, null, null, true); ?>
                  </div>
                </div>
                <section class=" card card-body mx-4">
                  <section class="repeater-item mb-2" key="InclusionGroup" data-contentToDuplicate="inclusionGroupsBloc">
                    <h5 class="lang-text"
                      data-fr="Groupes à l'inclusion"
                      data-en="Inclusion Groups">Groupe à l'inclusion<span class="number"></span></h5>
                    <div class="row">
                      <div class="col-md-12 mb-3" ddiShema="_custom">
                        <?php nada_renderInputGroup("Nom du groupe", "Group name", "additional/inclusionGroups/groupName", "text", [], true, true, null, null, true); ?>
                      </div>
                      <div class="col-md-12 mb-3" ddiShema="_custom">
                        <?php nada_renderInputGroup("Description du groupe", "Group description", "additional/inclusionGroups/groupDescription", "textarea", [], true, false); ?>
                      </div>
                      <div class="col-md-12 mb-3" ddiShema="_custom">
                        <?php nada_renderInputGroup("Exposition associée au groupe", "Exposition associated to the inclusion group", "additional/inclusionGroups/groupInterventionExposition", "textarea", [], true, false); ?>
                      </div>
                    </div>
                  </section>
                </section>
              </section>
            </section>
          </div>
        </div>
      </div>
    </section>

    <!-- section DataCollectionIntegration -->
    <section key="DataCollectionIntegration">
      <div class="mb-4">
        <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#collapsefourteen"
          aria-expanded="true"
          aria-controls="collapsefourteen"
          data-fr="Collecte et réutilisation de données"
          data-en="Data collection and reuse">
          Collecte et réutilisation de données
        </button>
        <div class="collapse show" id="collapsefourteen">
          <div class="card card-body">
            <section key="DataCollectionIntegration">
              <h3 class="lang-text"
                data-fr="Collecte et réutilisation de données"
                data-en="Data collected or produced specifically for the study.">Collecte et réutilisation de données</h3>
              <section key="DataCollection">
                <h4 class="lang-text"
                  data-fr="Données individuelles produites ou collectées dans le cadre de l'étude"
                  data-en="Individual data produced or collected as part of the study">
                  Données individuelles produites ou collectées dans le cadre de l'étude
                </h4>
                <section key="CollectionProcess">


                  <div class="row">
                    <div class="col-md-12 mb-3" ddiShema="_custom">
                      <?php nada_renderInputGroup("Fréquence de la collecte", "Collection Frequency", "stdyDscr/method/dataColl/frequenc", "textarea", [], true, true, null, null, true); ?>
                    </div>
                    <div class="col-md-12 mb-3" ddiShema="stdyDscr/method/dataColl/collMode">
                      <?php
                      $options_dataCollectionMethods = [
                        "fr" => [
                          'Mesures et tests' => 'Mesures et tests',
                          'Mesures et tests : Connaissances / compétences / aptitudes' => 'Mesures et tests : Connaissances / compétences / aptitudes',
                          'Mesures et tests : Mesures cliniques et tests laboratoires' => 'Mesures et tests : Mesures cliniques et tests laboratoires',
                          'Mesures et tests : Psychologiques' => 'Mesures et tests : Psychologiques',
                          'Entretien ou questionnaire administré par enquêteur' => 'Entretien ou questionnaire administré par enquêteur',
                          'Questionnaire auto-administré' => 'Questionnaire auto-administré',
                          'Expérimentation' => 'Expérimentation',
                          'Expérimentation : En laboratoire' => 'Expérimentation : En laboratoire',
                          'Expérimentation : Sur le terrain (y compris en clinique)' => 'Expérimentation : Sur le terrain (y compris en clinique)',
                          'Expérimentation : Par Internet' => 'Expérimentation : Par Internet',
                          'Enregistrement / Prise d\'image / Captation' => 'Enregistrement / Prise d\'image / Captation',
                          'Observation' => 'Observation',
                          'Transcription et saisie d’informations dans un enregistrement structuré' => 'Transcription et saisie d’informations dans un enregistrement structuré',
                          'Autre' => 'Autre'
                        ],
                        "en" => [
                          'Measurements and Tests' => 'Measurements and Tests',
                          'Measurements and Tests: Knowledge / Skills / Abilities' => 'Measurements and Tests: Knowledge / Skills / Abilities',
                          'Measurements and Tests: Clinical Measurements and Laboratory Tests' => 'Measurements and Tests: Clinical Measurements and Laboratory Tests',
                          'Measurements and Tests: Psychological' => 'Measurements and Tests: Psychological',
                          'Interview or Investigator-administered Questionnaire' => 'Interview or Investigator-administered Questionnaire',
                          'Self-administered Questionnaire' => 'Self-administered Questionnaire',
                          'Experimentation' => 'Experimentation',
                          'Experimentation: In Laboratory' => 'Experimentation: In Laboratory',
                          'Experimentation: In the Field (including Clinical Setting)' => 'Experimentation: In the Field (including Clinical Setting)',
                          'Experimentation: Online' => 'Experimentation: Online',
                          'Recording / Imaging / Capture' => 'Recording / Imaging / Capture',
                          'Observation' => 'Observation',
                          'Transcription and Entry of Information into a Structured Record' => 'Transcription and Entry of Information into a Structured Record',
                          'Other' => 'Other'
                        ]
                      ];
                      nada_renderInputGroup("Mode de collecte", "Collection Mode", "stdyDscr/method/dataColl/collMode", "checkbox", $options_dataCollectionMethods, true, true);
                      ?>
                    </div>
                    <div class="col-md-12 mb-3 d-none" ddiShema="_custom" id="CollectionFrequencyBloc">
                      <?php nada_renderInputGroup("Autre mode de collecte, précisions", "Other collection mode, details", "additional/collectionProcess/collectionModeOther", "textarea", [], true, false); ?>
                    </div>
                    <div class="col-md-12 mb-3" ddiShema="_custom">
                      <?php nada_renderInputGroup("Mode de collecte, précisions", "Collection mode, details", "additional/collectionProcess/collectionModeDetails", "textarea", [], true, false); ?>
                    </div>

                  </div>
                </section>


                <div class="row">
                  <div class="col-md-12 mb-3" ddiShema="_custom">
                    <?php
                    $options_inclusionCriteria = [
                      "fr" => [
                        "Inclusion par exposition-facteur (selon une ou des variables individuelles d'intérêt)" =>
                        "Inclusion par exposition-facteur (selon une ou des variables individuelles d'intérêt)",
                        "Inclusion basée sur une caractéristique individuelle (critère clinique, comportemental ou socio-démographique)" =>
                        "Inclusion basée sur une caractéristique individuelle (critère clinique, comportemental ou socio-démographique)",
                        "Inclusion fondée sur un événement passé (de santé, de parcours, d'une étape de vie...) ou critère de jugement (outcome-based)" =>
                        "Inclusion fondée sur un événement passé (de santé, de parcours, d'une étape de vie...) ou critère de jugement (outcome-based)",
                        "Inclusion par appartenance à un cadre externe : contexte organisationnel ou territorial (Personnes vivant dans un lieu, appartenant à une situation organisationnelle donnée, usagers d’un service)" =>
                        "Inclusion par appartenance à un cadre externe : contexte organisationnel ou territorial (Personnes vivant dans un lieu, appartenant à une situation organisationnelle donnée, usagers d’un service)",
                        "Inclusion par participation volontaire" =>
                        "Inclusion par participation volontaire",
                        "Autre" => "Autre"
                      ],
                      "en" => [
                        "Inclusion by exposure factor (according to one or more individual variables of interest)" =>
                        "Inclusion by exposure factor (according to one or more individual variables of interest)",
                        "Inclusion based on an individual characteristic (clinical, behavioral, or sociodemographic criterion)" =>
                        "Inclusion based on an individual characteristic (clinical, behavioral, or sociodemographic criterion)",
                        "Inclusion based on a past event (health, life course, life stage...) or outcome-based criterion" =>
                        "Inclusion based on a past event (health, life course, life stage...) or outcome-based criterion",
                        "Inclusion by belonging to an external framework: organizational or territorial context (People living in a specific place, belonging to a given organizational situation, or users of a service)" =>
                        "Inclusion by belonging to an external framework: organizational or territorial context (People living in a specific place, belonging to a given organizational situation, or users of a service)",
                        "Inclusion by voluntary participation" =>
                        "Inclusion by voluntary participation",
                        "Other" => "Other"
                      ]
                    ];
                    nada_renderInputGroup("Stratégie d'inclusion", "Inclusion Strategy", "additional/dataCollection/inclusionStrategy", "checkbox", $options_inclusionCriteria, true, true);
                    ?>
                  </div>
                  <div class="col-md-12 mb-3 d-none" id="inclusionStrategyOtherBloc">
                    <?php nada_renderInputGroup("Autre stratégie d'inclusion, précisions", "Other inclusion strategy, details", "additional/dataCollection/inclusionStrategyOther", "textarea", [], true, false); ?>
                  </div>
                  <div class="col-md-12 mb-3" ddiShema="stdyDscr/method/dataColl/sampProc">
                    <?php
                    $options_samplingMethod = [
                      "fr" => [
                        "Dénombrement complet" => "Dénombrement complet",
                        "Probabiliste" => "Probabiliste",
                        "Probabiliste : aléatoire simple" => "Probabiliste : aléatoire simple",
                        "Probabiliste : aléatoire systématique ou Échantillonnage par intervalle" => "Probabiliste : aléatoire systématique ou Échantillonnage par intervalle",
                        "Probabiliste : stratifié" => "Probabiliste : stratifié",
                        "Probabiliste : grappe" => "Probabiliste : grappe",
                        "Probabiliste : multi-étapes" => "Probabiliste : multi-étapes",
                        "Non probabiliste" => "Non probabiliste",
                        "Non probabiliste : disponibilité (de 'convenance' ou d''opportunité')" => "Non probabiliste : disponibilité (de convenance ou d'opportunité)",
                        "Non probabiliste : raisonné ou par 'jugement'" => "Non probabiliste : raisonné ou par jugement",
                        "Non probabiliste : quota" => "Non probabiliste : quota",
                        "Non probabiliste : échantillonnage par les répondants ou 'boule de neige'" => "Non probabiliste : échantillonnage par les répondants ou boule de neige",
                        "Non probabiliste : par saturation" => "Non probabiliste : par saturation",
                        "Mixte probabiliste et non probabiliste" => "Mixte probabiliste et non probabiliste",
                        "Autre" => "Autre"
                      ],
                      "en" => [
                        "Complete enumeration" => "Complete enumeration",
                        "Probabilistic" => "Probabilistic",
                        "Probabilistic: Simple random" => "Probabilistic: Simple random",
                        "Probabilistic: Systematic random or Interval sampling" => "Probabilistic: Systematic random or Interval sampling",
                        "Probabilistic: Stratified" => "Probabilistic: Stratified",
                        "Probabilistic: Cluster" => "Probabilistic: Cluster",
                        "Probabilistic: Multi-stage" => "Probabilistic: Multi-stage",
                        "Non-probabilistic" => "Non-probabilistic",
                        "Non-probabilistic: Convenience (or opportunity) sampling" => "Non-probabilistic: Convenience (or opportunity) sampling",
                        "Non-probabilistic: Judgmental or purposive" => "Non-probabilistic: Judgmental or purposive",
                        "Non-probabilistic: Quota" => "Non-probabilistic: Quota",
                        "Non-probabilistic: Respondent-driven or Snowball sampling" => "Non-probabilistic: Respondent-driven or Snowball sampling",
                        "Non-probabilistic: Saturation" => "Non-probabilistic: Saturation",
                        "Mixed probabilistic and non-probabilistic" => "Mixed probabilistic and non-probabilistic",
                        "Other" => "Other"
                      ]
                    ];
                    nada_renderInputGroup("Procédure d'échantillonnage à l'inclusion", "Sampling Procedure at Inclusion", "stdyDscr/method/dataColl/sampProc", "checkbox", $options_samplingMethod, true, true);
                    ?>
                  </div>
                  <div class="col-md-6 mb-3 d-none" id="stdyDscr_method_dataColl_sampProc_other" ddiShema="_custom">
                    <?php nada_renderInputGroup("Autre procedure d'échantillonnage, précisions", "Other sampling mode, details", "additional/dataCollection/samplingModeOther", "textarea", [], true, false); ?>
                  </div>
                  <div class="col-md-12 mb-3" ddiShema="stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType">
                    <?php
                    $options_recruitmentSource = [
                      "fr" => [
                        "Via les professionnels de santé d'exercice libéral" => "Via les professionnels de santé d'exercice libéral",
                        "Base de population à finalité statistique" => "Base de population à finalité statistique",
                        "Registre de maladies, de décès" => "Registre de maladies, de décès",
                        "Base médico-administrative (de patients, Assurance Maladie/Mutuelle)" => "Base médico-administrative (de patients, Assurance Maladie/Mutuelle)",
                        "Base administrative (d'employés, d'élèves, d'étudiants, etc…)" => "Base administrative (d'employés, d'élèves, d'étudiants, etc…)",
                        "Via des structures (services ou établissements de santé, écoles, entreprises…)" => "Via des structures (services ou établissements de santé, écoles, entreprises…)",
                        "Autre" => "Autre"
                      ],
                      "en" => [
                        "Through private healthcare professionals" => "Through private healthcare professionals",
                        "Population database for statistical purposes" => "Population database for statistical purposes",
                        "Disease or mortality registry" => "Disease or mortality registry",
                        "Medico-administrative database (patients, Health Insurance/Mutual Fund)" => "Medico-administrative database (patients, Health Insurance/Mutual Fund)",
                        "Administrative database (employees, pupils, students, etc.)" => "Administrative database (employees, pupils, students, etc.)",
                        "Through institutions (healthcare services or facilities, schools, companies, etc.)" => "Through institutions (healthcare services or facilities, schools, companies, etc.)",
                        "Other" => "Other"
                      ]
                    ];
                    nada_renderInputGroup("Source de recrutement des participants ", "Participant Recruitment Source", "stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType", "checkbox", $options_recruitmentSource, true, true);
                    ?>
                  </div>
                  <div class="col-md-6 mb-3 d-none" id="RecruitmentSourceOtherBloc" ddiShema="_custom">
                    <?php nada_renderInputGroup("Autre source de recrutement, précisions", "Other recruitment source, details", "additional/dataCollection/recruitmentSourceOther", "textarea", [], true, true); ?>
                  </div>
                </div>

                <section key="CollectionChronology">
                  <h4 class="lang-text"
                    data-fr="Chronologie de la collecte"
                    data-en="Collection Chronology">
                    Chronologie de la collecte
                  </h4>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <?php nada_renderInputGroup("Date de debut de la collecte (recrutement du 1er participant)", "Collection Start Date", "stdyDscr/stdyInfo/sumDscr/collDate/event_start", "date", [], true, true); ?>
                      <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/collDate/event_start/event_fr" value="début" />
                      <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/collDate/event_start/event_en" value="start" />

                    </div>
                    <div class="col-md-6 mb-3">
                      <?php nada_renderInputGroup("Date de fin de la collecte (dernier suivi du dernier participant)", "Collection End Date", "stdyDscr/stdyInfo/sumDscr/collDate/event_end", "date", [], true, true); ?>
                      <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/collDate/event_end/event_fr" value="fin" />
                      <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/collDate/event_end/event_en" value="end" />
                    </div>
                  </div>
                </section>

                <section key="ActiveFollowUp">
                  <h4 class="lang-text"
                    data-fr="Suivi actif des participants"
                    data-en="Active follow-up">
                    Suivi actif des participants
                  </h4>

                  <div class="row">
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
                      nada_renderInputGroup("Suivi actif des participants?", "Active Follow-up?", "additional/activeFollowUp/isActiveFollowUp", "radio", $options_boolean, true, true);
                      ?>
                    </div>
                    <div class="col-md-6 mb-3 d-none" id="FollowUpModalitiesBloc" ddiShema="stdyDscr/method/notes">
                      <?php
                      $options_followUp = [
                        "fr" => [
                          "Suivi par contact avec le participant (téléphone, e-mail, courrier...)" => "Suivi par contact avec le participant (téléphone, e-mail, courrier...)",
                          "Suivi par visite du participant au centre investigateur" => "Suivi par visite du participant au centre investigateur",
                          "Autre" => "Autre"
                        ],
                        "en" => [
                          "Follow-up through contact with the participant (phone, email, mail...)" => "Follow-up through contact with the participant (phone, email, mail...)",
                          "Follow-up through participant visit to the investigator’s site" => "Follow-up through participant visit to the investigator’s site",
                          "Other" => "Other"
                        ]
                      ];
                      nada_renderInputGroup("Modalités de suivi", "Follow-up Method", "stdyDscr/method/notes/subject_followUP", "checkbox", $options_followUp, true, false);
                      ?>
                      <input type="hidden" name="stdyDscr/method/notes/subject_followUP/subject_fr" value="suivi" />
                      <input type="hidden" name="stdyDscr/method/notes/subject_followUP/subject_en" value="follow-up" />

                    </div>
                    <div class="col-md-6 mb-3 d-none" id="FollowUpModeOtherBloc" ddiShema="_custom">
                      <?php nada_renderInputGroup("Autre modalités de suivi, précisions", "Other follow-up method, details", "additional/activeFollowUp/followUpModeOther", "textarea", [], true, false); ?>
                    </div>
                  </div>
                </section>
              </section>
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
                  nada_renderInputGroup("Utilisation des données individuelles issues d'autres sources des données", "Use of Individual Data from Other Data Sources", "additional/dataCollectionIntegration/isDataIntegration", "radio", $options_boolean, true, true);
                  ?>
                </div>
              </div>

              <section key="ThirdPartySource" id="ThirdPartySourceBloc" class="d-none">
                <h3 class="lang-text"
                  data-fr="Sources tierces"
                  data-en="Third-party sources">
                  Sources tierces
                </h3>

                <div id="repeater-sources" class="repeaterBlock">


                  <div class="mb-4 repeater-item" data-section="sources">
                    <button type="button" class="btn-remove btn-remove-section ">
                      <span class="dashicons dashicons-trash"></span>
                      <span class="lang-text"
                        data-fr="Supprimer"
                        data-en="Delete">
                        Supprimer
                      </span>
                    </button>
                    <div class="row w-100" data-section="ThirdPartySource">
                      <div class="col-md-6 mb-3">
                        <?php nada_renderInputGroup("Description de la source", "Source Description", "stdyDscr/method/dataColl/sources/sourceCitation", "textarea", [], true, true); ?>
                      </div>
                      <div class="col-md-6 mb-3" ddiShema="stdyDscr/method/dataColl/sources/sourceCitation/notes">
                        <?php
                        $options_dataEnrichment = [
                          "fr" => [
                            "Ajout d'individus"              => "Ajout d'individus",
                            "Enrichissement par croisement"  => "Enrichissement par croisement",
                            "Suivi passif des participants"  => "Suivi passif des participants"
                          ],
                          "en" => [
                            "Addition of individuals"        => "Addition of individuals",
                            "Enrichment through data linkage" => "Enrichment through data linkage",
                            "Passive follow-up of participants" => "Passive follow-up of participants"
                          ]
                        ];

                        nada_renderInputGroup("Objectif de l'integration de la source", "Source Integration Purpose", "stdyDscr/method/dataColl/sources/sourceCitation/notes/subject_sourcePurpose", "select", $options_dataEnrichment, true, true);
                        ?>
                        <input type="hidden" name="stdyDscr/method/dataColl/sources/sourceCitation/notes/subject_sourcePurpose/subject_fr" value="but de la source" />
                        <input type="hidden" name="stdyDscr/method/dataColl/sources/sourceCitation/notes/subject_sourcePurpose/subject_en" value="source purpose" />
                      </div>
                      <div data-parent="ThirdPartySource">
                        <div class="col-md-12 mb-3" ddiShema="stdyDscr/method/dataColl/sources/srcOrig">
                          <?php
                          $options_dataSources = [
                            "fr" => [
                              "Dossier du patient" => "Dossier du patient",
                              "Source des données paracliniques" => "Source des données paracliniques",
                              "Biobanque" => "Biobanque",
                              "Registre" => "Registre",
                              "Base administrative" => "Base administrative",
                              "Base médico-administrative" => "Base médico-administrative",
                              "Données collectées dans le cadre d'une recherche" => "Données collectées dans le cadre d'une recherche",
                              "Autre" => "Autre"
                            ],
                            "en" => [
                              "Patient record" => "Patient record",
                              "Source of paraclinical data" => "Source of paraclinical data",
                              "Biobank" => "Biobank",
                              "Registry" => "Registry",
                              "Administrative database" => "Administrative database",
                              "Medico-administrative database" => "Medico-administrative database",
                              "Data collected as part of a research study" => "Data collected as part of a research study",
                              "Other" => "Other"
                            ]
                          ];
                          nada_renderInputGroup("Type de source", "Source Type", "stdyDscr/method/dataColl/sources/srcOrig", "checkbox", $options_dataSources, true, false);
                          ?>
                        </div>
                        <div class="col-md-12 mb-3 d-none" id="OtherSourceTypeBloc" data-item="ThirdPartySource">
                          <?php nada_renderInputGroup("Autre, précisions", "Other Source Type (details)", "additional/thirdPartySource/otherSourceType", "textarea", [], true, true); ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="d-flex btnBlockRepeater">
                  <button type="button" class="btn-add mt-2" id="add-sources">
                    <span class="dashicons dashicons-plus"></span>
                    <span class="lang-text"
                      data-fr="Ajouter"
                      data-en="Add">
                      Ajouter
                    </span>
                  </button>
                </div>
              </section>
            </section>
          </div>
        </div>
      </div>
    </section>

    <section key="Population">
      <div class="mb-4">
        <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#collapsethirteen"
          aria-expanded="true"
          aria-controls="collapsethirteen"
          data-fr="Population"
          data-en="Population">
          Population
        </button>

        <div class="collapse show" id="collapsethirteen">
          <div class="card card-body">
            <section key="Population">
              <h3 class="lang-text"
                data-fr="Population"
                data-en="Population">
                Population
              </h3>

              <section key="DemographicInfo">
                <h4 class="lang-text"
                  data-fr="Caractéristiques démographiques"
                  data-en="Demographic characteristics">
                  Caractéristiques démographiques
                </h4>

                <div class="row">
                  <div class="col-md-6 mb-3" ddiShema="stdyDscr/stdyInfo/sumDscr/universe/value">
                    <?php
                    $options_sex = [
                      "fr" => [
                        "Masculin" => "Masculin",
                        "Féminin"  => "Féminin",
                        "Autre"    => "Autre"
                      ],
                      "en" => [
                        "Male"     => "Male",
                        "Female"   => "Female",
                        "Other"    => "Other"
                      ]
                    ];
                    nada_renderInputGroup("Sexe", "Sex", "stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I", "checkbox", $options_sex, true, true);
                    ?>
                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I/level_fr" value="sexe" />
                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I/level_en" value="sex" />

                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I/clusion_fr" value="I" />
                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I/clusion_en" value="I" />


                  </div>
                  <div class="col-md-12 mb-3" ddiShema="stdyDscr/stdyInfo/sumDscr/universe">
                    <?php
                    $options_ageGroup = [
                      "fr" => [
                        "Prénatal" => "Prénatal",
                        "Nouveau-né ( naissance à 28j)" => "Nouveau-né ( naissance à 28j)",
                        "Nourrisson (28j à 2 ans)" => "Nourrisson (28j à 2 ans)",
                        "Petite enfance (2 à 5 ans)" => "Petite enfance (2 à 5 ans)",
                        "Enfance (6 à 12 ans)" => "Enfance (6 à 12 ans)",
                        "Adolescence (13 à 18 ans)" => "Adolescence (13 à 18 ans)",
                        "Adulte (19 à 24 ans)" => "Adulte (19 à 24 ans)",
                        "Adulte (25 à 44 ans)" => "Adulte (25 à 44 ans)",
                        "Adulte (45 à 64 ans)" => "Adulte (45 à 64 ans)",
                        "Personne âgée (65 à 79 ans)" => "Personne âgée (65 à 79 ans)",
                        "Grand âge (80 ans et plus)" => "Grand âge (80 ans et plus)"
                      ],
                      "en" => [
                        "Prenatal" => "Prenatal",
                        "Newborn (birth to 28 days)" => "Newborn (birth to 28 days)",
                        "Infant (28 days to 2 years)" => "Infant (28 days to 2 years)",
                        "Early childhood (2 to 5 years)" => "Early childhood (2 to 5 years)",
                        "Childhood (6 to 12 years)" => "Childhood (6 to 12 years)",
                        "Adolescence (13 to 18 years)" => "Adolescence (13 to 18 years)",
                        "Adult (19 to 24 years)" => "Adult (19 to 24 years)",
                        "Adult (25 to 44 years)" => "Adult (25 to 44 years)",
                        "Adult (45 to 64 years)" => "Adult (45 to 64 years)",
                        "Older adult (65 to 79 years)" => "Older adult (65 to 79 years)",
                        "Elderly (80 years and over)" => "Elderly (80 years and over)"
                      ]
                    ];

                    nada_renderInputGroup("Age", "Age", "stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I", "checkbox", $options_ageGroup, true, true);
                    ?>
                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I/level_fr" value="sexe" />
                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I/level_en" value="sex" />

                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I/clusion_fr" value="I" />
                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I/clusion_en" value="I" />

                  </div>

                </div>
              </section>
              <div class="row">
                <div class="col-md-6 mb-3" ddiShema="stdyDscr/stdyInfo/sumDscr/universe/value">
                  <?php
                  $options_targetPopulation = [
                    "fr" => [
                      "Population générale" => "Population générale",
                      "Patients" => "Patients",
                      "Personnes en situation de handicap" => "Personnes en situation de handicap",
                      "Autre" => "Autre"
                    ],
                    "en" => [
                      "General population" => "General population",
                      "Patients" => "Patients",
                      "People with disabilities" => "People with disabilities",
                      "Other" => "Other"
                    ]
                  ];

                  nada_renderInputGroup("Type de population", "Population Type", "stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I", "select", $options_targetPopulation, true, true);
                  ?>
                  <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I/level_fr" value="sexe" />
                  <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I/level_en" value="sex" />

                  <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I/clusion_fr" value="I" />
                  <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I/clusion_en" value="I" />

                </div>
                <div class="col-md-6 mb-3 d-none" id="OtherPopulationTypeBloc">
                  <?php nada_renderInputGroup("Autre type de population, précisions", "Population Type", "additional/OtherPopulationType", "textarea", [], true, false); ?>
                </div>
              </div>
              <section key="OtherClusion">
                <h4 class="lang-text"
                  data-fr="Autres critères d'éligibilité"
                  data-en="Other eligibility criteria">
                  Autres critères d'éligibilité
                </h4>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <?php nada_renderInputGroup("Critères inclusion", "Inclusion Criteria", "stdyDscr/stdyInfo/sumDscr/universe/Clusion_I", "textarea", [], true, false); ?>

                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/Clusion_I/clusion_fr" value="I" />
                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/Clusion_I/clusion_en" value="I" />

                  </div>
                  <div class="col-md-6 mb-3">
                    <?php nada_renderInputGroup("Critères exclusion", "Exclusion Criteria", "stdyDscr/stdyInfo/sumDscr/universe/Clusion_E", "textarea", [], true, false); ?>
                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/Clusion_E/clusion_fr" value="E" />
                    <input type="hidden" name="stdyDscr/stdyInfo/sumDscr/universe/Clusion_E/clusion_en" value="E" />

                  </div>
                </div>
              </section>
              <section key="GeographicalCoverage">
                <h4 class="lang-text"
                  data-fr="Champ géographique"
                  data-en="Geographical scope">
                  Champ géographique
                </h4>
                <div class="row">
                  <div class="col-md-12 mb-3" ddiShema="stdyDscr/stdyInfo/sumDscr/nation">
                    <?php
                    $options_regions = [
                      "fr" => [
                        "France" => "France",
                        "Afghanistan" => "Afghanistan",
                        "Albanie" => "Albanie",
                        "Algérie" => "Algérie",
                        "Samoa américaines" => "Samoa américaines",
                        "Andorre" => "Andorre",
                        "Angola" => "Angola",
                        "Anguilla" => "Anguilla",
                        "Antarctique" => "Antarctique",
                        "Antigua-et-Barbuda" => "Antigua-et-Barbuda",
                        "Argentine" => "Argentine",
                        "Arménie" => "Arménie",
                        "Aruba" => "Aruba",
                        "Australie" => "Australie",
                        "Autriche" => "Autriche",
                        "Azerbaïdjan" => "Azerbaïdjan",
                        "Bahamas" => "Bahamas",
                        "Bahreïn" => "Bahreïn",
                        "Bangladesh" => "Bangladesh",
                        "Barbade" => "Barbade",
                        "Biélorussie" => "Biélorussie",
                        "Belgique" => "Belgique",
                        "Belize" => "Belize",
                        "Bénin" => "Bénin",
                        "Bermudes" => "Bermudes",
                        "Bhoutan" => "Bhoutan",
                        "Bolivie" => "Bolivie",
                        "Bosnie-Herzégovine" => "Bosnie-Herzégovine",
                        "Botswana" => "Botswana",
                        "Brésil" => "Brésil",
                        "Brunei Darussalam" => "Brunei Darussalam",
                        "Bulgarie" => "Bulgarie",
                        "Burkina Faso" => "Burkina Faso",
                        "Burundi" => "Burundi",
                        "Cap-Vert" => "Cap-Vert",
                        "Cambodge" => "Cambodge",
                        "Cameroun" => "Cameroun",
                        "Canada" => "Canada",
                        "Îles Caïmans" => "Îles Caïmans",
                        "République centrafricaine" => "République centrafricaine",
                        "Tchad" => "Tchad",
                        "Chili" => "Chili",
                        "Chine" => "Chine",
                        "Colombie" => "Colombie",
                        "Comores" => "Comores",
                        "Congo" => "Congo",
                        "République démocratique du Congo" => "République démocratique du Congo",
                        "Costa Rica" => "Costa Rica",
                        "Côte d'Ivoire" => "Côte d'Ivoire",
                        "Croatie" => "Croatie",
                        "Cuba" => "Cuba",
                        "Chypre" => "Chypre",
                        "Tchéquie" => "Tchéquie",
                        "Danemark" => "Danemark",
                        "Djibouti" => "Djibouti",
                        "Dominique" => "Dominique",
                        "République dominicaine" => "République dominicaine",
                        "Équateur" => "Équateur",
                        "Égypte" => "Égypte",
                        "El Salvador" => "El Salvador",
                        "Guinée équatoriale" => "Guinée équatoriale",
                        "Érythrée" => "Érythrée",
                        "Estonie" => "Estonie",
                        "Eswatini" => "Eswatini",
                        "Éthiopie" => "Éthiopie",
                        "Fidji" => "Fidji",
                        "Finlande" => "Finlande",
                        "Gabon" => "Gabon",
                        "Gambie" => "Gambie",
                        "Géorgie" => "Géorgie",
                        "Allemagne" => "Allemagne",
                        "Ghana" => "Ghana",
                        "Grèce" => "Grèce",
                        "Grenade" => "Grenade",
                        "Guatemala" => "Guatemala",
                        "Guinée" => "Guinée",
                        "Guinée-Bissau" => "Guinée-Bissau",
                        "Guyana" => "Guyana",
                        "Haïti" => "Haïti",
                        "Saint-Siège" => "Saint-Siège",
                        "Honduras" => "Honduras",
                        "Hongrie" => "Hongrie",
                        "Islande" => "Islande",
                        "Inde" => "Inde",
                        "Indonésie" => "Indonésie",
                        "Iran" => "Iran",
                        "Irak" => "Irak",
                        "Irlande" => "Irlande",
                        "Israël" => "Israël",
                        "Italie" => "Italie",
                        "Jamaïque" => "Jamaïque",
                        "Japon" => "Japon",
                        "Jordanie" => "Jordanie",
                        "Kazakhstan" => "Kazakhstan",
                        "Kenya" => "Kenya",
                        "Kiribati" => "Kiribati",
                        "Corée du Sud" => "Corée du Sud",
                        "Koweït" => "Koweït",
                        "Kirghizistan" => "Kirghizistan",
                        "République populaire démocratique du Laos" => "République populaire démocratique du Laos",
                        "Lettonie" => "Lettonie",
                        "Liban" => "Liban",
                        "Lesotho" => "Lesotho",
                        "Libéria" => "Libéria",
                        "Libye" => "Libye",
                        "Liechtenstein" => "Liechtenstein",
                        "Lituanie" => "Lituanie",
                        "Luxembourg" => "Luxembourg",
                        "Madagascar" => "Madagascar",
                        "Malawi" => "Malawi",
                        "Malaisie" => "Malaisie",
                        "Maldives" => "Maldives",
                        "Mali" => "Mali",
                        "Malte" => "Malte",
                        "Îles Marshall" => "Îles Marshall",
                        "Mauritanie" => "Mauritanie",
                        "Maurice" => "Maurice",
                        "Mexique" => "Mexique",
                        "Moldavie" => "Moldavie",
                        "Monaco" => "Monaco",
                        "Mongolie" => "Mongolie",
                        "Monténégro" => "Monténégro",
                        "Maroc" => "Maroc",
                        "Mozambique" => "Mozambique",
                        "Myanmar" => "Myanmar",
                        "Namibie" => "Namibie",
                        "Nauru" => "Nauru",
                        "Népal" => "Népal",
                        "Pays-Bas" => "Pays-Bas",
                        "Nouvelle-Zélande" => "Nouvelle-Zélande",
                        "Nicaragua" => "Nicaragua",
                        "Niger" => "Niger",
                        "Nigéria" => "Nigéria",
                        "Norvège" => "Norvège",
                        "Oman" => "Oman",
                        "Pakistan" => "Pakistan",
                        "Palaos" => "Palaos",
                        "Panama" => "Panama",
                        "Papouasie-Nouvelle-Guinée" => "Papouasie-Nouvelle-Guinée",
                        "Paraguay" => "Paraguay",
                        "Pérou" => "Pérou",
                        "Philippines" => "Philippines",
                        "Pologne" => "Pologne",
                        "Portugal" => "Portugal",
                        "Qatar" => "Qatar",
                        "Roumanie" => "Roumanie",
                        "Fédération de Russie" => "Fédération de Russie",
                        "Rwanda" => "Rwanda",
                        "Arabie saoudite" => "Arabie saoudite",
                        "Sénégal" => "Sénégal",
                        "Serbie" => "Serbie",
                        "Seychelles" => "Seychelles",
                        "Sierra Leone" => "Sierra Leone",
                        "Singapour" => "Singapour",
                        "Slovaquie" => "Slovaquie",
                        "Slovénie" => "Slovénie",
                        "Îles Salomon" => "Îles Salomon",
                        "Somalie" => "Somalie",
                        "Afrique du Sud" => "Afrique du Sud",
                        "Espagne" => "Espagne",
                        "Sri Lanka" => "Sri Lanka",
                        "Soudan" => "Soudan",
                        "Suède" => "Suède",
                        "Suisse" => "Suisse",
                        "République arabe syrienne" => "République arabe syrienne",
                        "Taïwan" => "Taïwan",
                        "Tadjikistan" => "Tadjikistan",
                        "Tanzanie" => "Tanzanie",
                        "Thaïlande" => "Thaïlande",
                        "Timor-Leste" => "Timor-Leste",
                        "Togo" => "Togo",
                        "Tonga" => "Tonga",
                        "Trinité-et-Tobago" => "Trinité-et-Tobago",
                        "Tunisie" => "Tunisie",
                        "Turquie" => "Turquie",
                        "Turkménistan" => "Turkménistan",
                        "Ouganda" => "Ouganda",
                        "Ukraine" => "Ukraine",
                        "Émirats arabes unis" => "Émirats arabes unis",
                        "Royaume-Uni" => "Royaume-Uni",
                        "États-Unis" => "États-Unis",
                        "Uruguay" => "Uruguay",
                        "Ouzbékistan" => "Ouzbékistan",
                        "Vanuatu" => "Vanuatu",
                        "Venezuela" => "Venezuela",
                        "Viêt Nam" => "Viêt Nam",
                        "Yémen" => "Yémen",
                        "Zambie" => "Zambie",
                        "Zimbabwe" => "Zimbabwe"
                      ],
                      "en" => [
                        "France" => "France",
                        "Afghanistan" => "Afghanistan",
                        "Albania" => "Albania",
                        "Algeria" => "Algeria",
                        "American Samoa" => "American Samoa",
                        "Andorra" => "Andorra",
                        "Angola" => "Angola",
                        "Anguilla" => "Anguilla",
                        "Antarctica" => "Antarctica",
                        "Antigua and Barbuda" => "Antigua and Barbuda",
                        "Argentina" => "Argentina",
                        "Armenia" => "Armenia",
                        "Aruba" => "Aruba",
                        "Australia" => "Australia",
                        "Austria" => "Austria",
                        "Azerbaijan" => "Azerbaijan",
                        "Bahamas" => "Bahamas",
                        "Bahrain" => "Bahrain",
                        "Bangladesh" => "Bangladesh",
                        "Barbados" => "Barbados",
                        "Belarus" => "Belarus",
                        "Belgium" => "Belgium",
                        "Belize" => "Belize",
                        "Benin" => "Benin",
                        "Bermuda" => "Bermuda",
                        "Bhutan" => "Bhutan",
                        "Bolivia" => "Bolivia",
                        "Bosnia and Herzegovina" => "Bosnia and Herzegovina",
                        "Botswana" => "Botswana",
                        "Brazil" => "Brazil",
                        "Brunei Darussalam" => "Brunei Darussalam",
                        "Bulgaria" => "Bulgaria",
                        "Burkina Faso" => "Burkina Faso",
                        "Burundi" => "Burundi",
                        "Cabo Verde" => "Cabo Verde",
                        "Cambodia" => "Cambodia",
                        "Cameroon" => "Cameroon",
                        "Canada" => "Canada",
                        "Cayman Islands" => "Cayman Islands",
                        "Central African Republic" => "Central African Republic",
                        "Chad" => "Chad",
                        "Chile" => "Chile",
                        "China" => "China",
                        "Colombia" => "Colombia",
                        "Comoros" => "Comoros",
                        "Congo" => "Congo",
                        "Congo (Democratic Republic)" => "Congo (Democratic Republic)",
                        "Costa Rica" => "Costa Rica",
                        "Côte d'Ivoire" => "Côte d'Ivoire",
                        "Croatia" => "Croatia",
                        "Cuba" => "Cuba",
                        "Cyprus" => "Cyprus",
                        "Czechia" => "Czechia",
                        "Denmark" => "Denmark",
                        "Djibouti" => "Djibouti",
                        "Dominica" => "Dominica",
                        "Dominican Republic" => "Dominican Republic",
                        "Ecuador" => "Ecuador",
                        "Egypt" => "Egypt",
                        "El Salvador" => "El Salvador",
                        "Equatorial Guinea" => "Equatorial Guinea",
                        "Eritrea" => "Eritrea",
                        "Estonia" => "Estonia",
                        "Eswatini" => "Eswatini",
                        "Ethiopia" => "Ethiopia",
                        "Fiji" => "Fiji",
                        "Finland" => "Finland",
                        "Gabon" => "Gabon",
                        "Gambia" => "Gambia",
                        "Georgia" => "Georgia",
                        "Germany" => "Germany",
                        "Ghana" => "Ghana",
                        "Greece" => "Greece",
                        "Grenada" => "Grenada",
                        "Guatemala" => "Guatemala",
                        "Guinea" => "Guinea",
                        "Guinea-Bissau" => "Guinea-Bissau",
                        "Guyana" => "Guyana",
                        "Haiti" => "Haiti",
                        "Holy See" => "Holy See",
                        "Honduras" => "Honduras",
                        "Hungary" => "Hungary",
                        "Iceland" => "Iceland",
                        "India" => "India",
                        "Indonesia" => "Indonesia",
                        "Iran" => "Iran",
                        "Iraq" => "Iraq",
                        "Ireland" => "Ireland",
                        "Israel" => "Israel",
                        "Italy" => "Italy",
                        "Jamaica" => "Jamaica",
                        "Japan" => "Japan",
                        "Jordan" => "Jordan",
                        "Kazakhstan" => "Kazakhstan",
                        "Kenya" => "Kenya",
                        "Kiribati" => "Kiribati",
                        "Korea (Republic of)" => "Korea (Republic of)",
                        "Kuwait" => "Kuwait",
                        "Kyrgyzstan" => "Kyrgyzstan",
                        "Lao People's Democratic Republic" => "Lao People's Democratic Republic",
                        "Latvia" => "Latvia",
                        "Lebanon" => "Lebanon",
                        "Lesotho" => "Lesotho",
                        "Liberia" => "Liberia",
                        "Libya" => "Libya",
                        "Liechtenstein" => "Liechtenstein",
                        "Lithuania" => "Lithuania",
                        "Luxembourg" => "Luxembourg",
                        "Madagascar" => "Madagascar",
                        "Malawi" => "Malawi",
                        "Malaysia" => "Malaysia",
                        "Maldives" => "Maldives",
                        "Mali" => "Mali",
                        "Malta" => "Malta",
                        "Marshall Islands" => "Marshall Islands",
                        "Mauritania" => "Mauritania",
                        "Mauritius" => "Mauritius",
                        "Mexico" => "Mexico",
                        "Moldova" => "Moldova",
                        "Monaco" => "Monaco",
                        "Mongolia" => "Mongolia",
                        "Montenegro" => "Montenegro",
                        "Morocco" => "Morocco",
                        "Mozambique" => "Mozambique",
                        "Myanmar" => "Myanmar",
                        "Namibia" => "Namibia",
                        "Nauru" => "Nauru",
                        "Nepal" => "Nepal",
                        "Netherlands" => "Netherlands",
                        "New Zealand" => "New Zealand",
                        "Nicaragua" => "Nicaragua",
                        "Niger" => "Niger",
                        "Nigeria" => "Nigeria",
                        "Norway" => "Norway",
                        "Oman" => "Oman",
                        "Pakistan" => "Pakistan",
                        "Palau" => "Palau",
                        "Panama" => "Panama",
                        "Papua New Guinea" => "Papua New Guinea",
                        "Paraguay" => "Paraguay",
                        "Peru" => "Peru",
                        "Philippines" => "Philippines",
                        "Poland" => "Poland",
                        "Portugal" => "Portugal",
                        "Qatar" => "Qatar",
                        "Romania" => "Romania",
                        "Russian Federation" => "Russian Federation",
                        "Rwanda" => "Rwanda",
                        "Saudi Arabia" => "Saudi Arabia",
                        "Senegal" => "Senegal",
                        "Serbia" => "Serbia",
                        "Seychelles" => "Seychelles",
                        "Sierra Leone" => "Sierra Leone",
                        "Singapore" => "Singapore",
                        "Slovakia" => "Slovakia",
                        "Slovenia" => "Slovenia",
                        "Solomon Islands" => "Solomon Islands",
                        "Somalia" => "Somalia",
                        "South Africa" => "South Africa",
                        "Spain" => "Spain",
                        "Sri Lanka" => "Sri Lanka",
                        "Sudan" => "Sudan",
                        "Sweden" => "Sweden",
                        "Switzerland" => "Switzerland",
                        "Syrian Arab Republic" => "Syrian Arab Republic",
                        "Taiwan" => "Taiwan",
                        "Tajikistan" => "Tajikistan",
                        "Tanzania" => "Tanzania",
                        "Thailand" => "Thailand",
                        "Timor-Leste" => "Timor-Leste",
                        "Togo" => "Togo",
                        "Tonga" => "Tonga",
                        "Trinidad and Tobago" => "Trinidad and Tobago",
                        "Tunisia" => "Tunisia",
                        "Türkiye" => "Türkiye",
                        "Turkmenistan" => "Turkmenistan",
                        "Uganda" => "Uganda",
                        "Ukraine" => "Ukraine",
                        "United Arab Emirates" => "United Arab Emirates",
                        "United Kingdom" => "United Kingdom",
                        "United States" => "United States",
                        "Uruguay" => "Uruguay",
                        "Uzbekistan" => "Uzbekistan",
                        "Vanuatu" => "Vanuatu",
                        "Venezuela" => "Venezuela",
                        "Viet Nam" => "Viet Nam",
                        "Yemen" => "Yemen",
                        "Zambia" => "Zambia",
                        "Zimbabwe" => "Zimbabwe"
                      ]
                    ];
                    nada_renderInputGroup("Pays concernées ", "Countries Concerned", "stdyDscr/stdyInfo/sumDscr/nation", "select", $options_regions, true, true);
                    ?>
                  </div>
                  <div class="col-md-12 mb-3">
                    <?php
                    $options_dataTypes = [
                      "fr" => [
                        "Auvergne-Rhône-Alpes"       => "Auvergne-Rhône-Alpes",
                        "Bourgogne-Franche-Comté"    => "Bourgogne-Franche-Comté",
                        "Bretagne"                   => "Bretagne",
                        "Centre-Val de Loire"        => "Centre-Val de Loire",
                        "Corse"                      => "Corse",
                        "Grand Est"                  => "Grand Est",
                        "Hauts-de-France"            => "Hauts-de-France",
                        "Île-de-France"              => "Île-de-France",
                        "Normandie"                  => "Normandie",
                        "Nouvelle-Aquitaine"         => "Nouvelle-Aquitaine",
                        "Occitanie"                  => "Occitanie",
                        "Pays de la Loire"           => "Pays de la Loire",
                        "Provence-Alpes-Côte d’Azur" => "Provence-Alpes-Côte d’Azur",
                        "Guadeloupe"                 => "Guadeloupe",
                        "Martinique"                 => "Martinique",
                        "Guyane"                     => "Guyane",
                        "La Réunion"                 => "La Réunion",
                        "Mayotte"                    => "Mayotte"
                      ],
                      "en" => [
                        "Auvergne-Rhône-Alpes"       => "Auvergne-Rhône-Alpes",
                        "Bourgogne-Franche-Comté"    => "Bourgogne-Franche-Comté",
                        "Bretagne"                   => "Bretagne",
                        "Centre-Val de Loire"        => "Centre-Val de Loire",
                        "Corse"                      => "Corse",
                        "Grand Est"                  => "Grand Est",
                        "Hauts-de-France"            => "Hauts-de-France",
                        "Île-de-France"              => "Île-de-France",
                        "Normandie"                  => "Normandie",
                        "Nouvelle-Aquitaine"         => "Nouvelle-Aquitaine",
                        "Occitanie"                  => "Occitanie",
                        "Pays de la Loire"           => "Pays de la Loire",
                        "Provence-Alpes-Côte d’Azur" => "Provence-Alpes-Côte d’Azur",
                        "Guadeloupe"                 => "Guadeloupe",
                        "Martinique"                 => "Martinique",
                        "Guyane"                     => "Guyane",
                        "La Réunion"                 => "La Réunion",
                        "Mayotte"                    => "Mayotte"
                      ]
                    ];
                    nada_renderInputGroup("Régions concernées (en France)", "Regions Concerned (France)", "stdyDscr/stdyInfo/sumDscr/geogCover", "checkbox", $options_dataTypes, true, true);
                    ?>
                  </div>
                  <div class="col-md-6 mb-3">
                    <?php nada_renderInputGroup("Détail du champ géographique", "Geographical Coverage Details", "additional/geographicalCoverage/geoDetail", "textarea", [], true, false); ?>
                  </div>
                </div>
              </section>
            </section>
          </div>
        </div>
      </div>
    </section>

    <section key="HealthParameters">
      <div class="mb-4">
        <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#collapseeight"
          aria-expanded="true"
          aria-controls="collapseeight"
          data-fr="Paramètres de santé étudiés"
          data-en="Health-related Outcomes">
          Paramètres de santé étudiés
        </button>

        <div class="collapse show" id="collapseeight">
          <div class="card card-body">
            <section key="HealthParameters">
              <h3 class="lang-text"
                data-fr="Paramètres de santé étudiés"
                data-en="Health-related Outcomes">
                Paramètres de santé étudiés
              </h3>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <?php nada_renderInputGroup("Critères d'évaluation (de jugement) principaux", "Primary Outcome", "stdyDscr/studyDevelopment/developmentActivity/type_primaryEvaluation/description", "textarea", [], true, true); ?>
                  <input type="hidden" name="stdyDscr/studyDevelopment/developmentActivity/type_primaryEvaluation/type_fr" value="évaluation primaire" />
                  <input type="hidden" name="stdyDscr/studyDevelopment/developmentActivity/type_primaryEvaluation/type_en" value="primary evaluation" />

                </div>
                <div class=" col-md-6 mb-3">
                  <?php nada_renderInputGroup("Critères d'évaluation (de jugement) secondaire", "Secondary Outcomes", "stdyDscr/studyDevelopment/developmentActivity/type_secondaryEvaluation/description", "textarea", [], true, false); ?>
                  <input type="hidden" name="stdyDscr/studyDevelopment/developmentActivity/type_secondaryEvaluation/type_fr" value="évaluation secondaire" />
                  <input type="hidden" name="stdyDscr/studyDevelopment/developmentActivity/type_secondaryEvaluation/type_en" value="secondary evaluation" />

                </div>
              </div>
            </section>
          </div>
        </div>
      </div>
    </section>

  </section>
</div>
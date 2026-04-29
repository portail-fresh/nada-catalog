<?php
if (!defined('ABSPATH')) exit; // Sécurité

/**
 * Prépare le payload Nada à partir d'un array $langData et du code langue
 *
 * @param array $langData Données du formulaire pour la langue $lang
 * @param string $lang 'fr' ou 'en'
 * @param string $idno optionnel, sinon généré automatiquement
 * @return array
 */



function prepare_nada_payload(array $langData, string $lang, string $idno = '', string $mode, string $respValidation = '', string $contactEmail = ''): array
{
    // time zone
    $tz = new DateTimeZone('Europe/Paris');
    $now = new DateTimeImmutable('now', $tz);
    $currentDate = $now->format('Y/m/d');

    // nom contribiteur
    $user_id = get_current_user_id();
    $first_name = sanitize_text_field(get_user_meta($user_id, 'first_name', true) ?: '');
    $last_name  = sanitize_text_field(get_user_meta($user_id, 'last_name', true)  ?: '');
    $contributorFullName = $first_name . ' ' . $last_name;

    $additionalBase = [
        "IsImport" => false,
        "provenance" => "Contribution",
        "versionLang" => $lang,
        "originLang" => $lang,
        "lastUpdatedManual" => $currentDate,
        "respValidation" => $respValidation,
        "contactEmail" =>  [ 
            "export" => false,
            "value" => $contactEmail // Oui ou Non
        ],
        "obtainedAuthorization" => [
            "otherAuthorizingAgency" => $langData['otherAgencies'] ?? []
        ],
        "regulatoryRequirements" => [
            "conformityDeclaration" => $langData['additional/regulatoryRequirements/conformityDeclaration'] ?? ''
        ],
        "fundingAgent" => [
            "fundingAgentType" => $langData['fundingAgentTypes'] ?? ''
        ],
        "sponsor" => [
            "sponsorType" => $langData['additional/sponsor/sponsorType'] ?? ''
        ],
        "governance" => [
            "committee" => $langData['additional/governance/committee'] ?? ''
        ],
        "collaborations" => [
            "networkConsortium" => $langData['additional/collaborations/networkConsortium'] ?? '',
            "collaboration" => $langData['stdyDscr/citation/rspStmt/othId/collaboration'] ?? ''
        ],
        "theme" => [
            "complementaryInformation" => $langData['additional/theme/complementaryInformation'] ?? '',
            "RareDiseases" => $langData['additional/theme/RareDiseases'] ?? ''
        ],
        "interventionalStudy" => [
            "researchPurpose" => $langData['researchPurposes'],
            "trialPhase" => $langData['trialPhases'],
            "interventionalStudyModel" => $langData['additional/interventionalStudy/interventionalStudyModel'],
        ],
        "allocation" => [
            "allocationMode" => $langData['additional/allocation/allocationMode'],
            "allocationUnit" => $langData['additional/allocation/allocationUnit']
        ],
        "masking" => [
            "maskingType" => $langData['additional/masking/maskingType'],
            "blindedMaskingDetails" => $langData['blindedMaskingDetails']
        ],
        "arms" => $langData['arms'],
        "armsNumber" => $langData['additional/arms/armsNumber'],
        "intervention" => $langData['interventions'],
        "cohortLongitudinal" => [ 
            "recrutementTiming" => $langData['recrutementTiming'] 
        ],
        "otherResearchType" => [
            "otherResearchTypeDetails" => $langData['additional/otherResearchType/otherResearchTypeDetails']
        ],
        "inclusionGroups" => $langData['inclusionGroups'],
        "nrInclusionGroups" => $langData['additional/inclusionGroups/nrInclusionGroups'],
        "collectionProcess" => [
            "collectionModeDetails" => $langData['additional/collectionProcess/collectionModeDetails'],
            "collectionModeOther" => $langData['additional/collectionProcess/collectionModeOther']
        ],
        "dataCollection" => [
            "inclusionStrategy" => $langData['inclusionStrategy'],
            "inclusionStrategyOther" => $langData['additional/dataCollection/inclusionStrategyOther'],
            "samplingModeOther" => $langData['additional/dataCollection/samplingModeOther'],
            "recruitmentSourceOther" => $langData['additional/dataCollection/recruitmentSourceOther']
        ],
        "activeFollowUp" => [
            "isActiveFollowUp" => $langData['additional/activeFollowUp/isActiveFollowUp'],
            "followUpModeOther" => $langData['additional/activeFollowUp/followUpModeOther'],
        ],
        "dataCollectionIntegration" => [
            "isDataIntegration" => $langData['additional/dataCollectionIntegration/isDataIntegration']
        ],
        "thirdPartySource" => [
            "otherSourceType" =>  $langData['additional/thirdPartySource/otherSourceType']
        ],
        "geographicalCoverage" => [
            "geoDetail" => $langData['additional/geographicalCoverage/geoDetail']
        ],
        "dataTypes" => [
            "clinicalDataDetails" => $langData['additional/dataTypes/clinicalDataDetails'],
            "biologicalDataDetails" => $langData['additional/dataTypes/biologicalDataDetails'],
            "isDataInBiobank" => $langData['additional/dataTypes/isDataInBiobank'],
            "biobankContent" => $langData['biobankContent'],
            "dataTypeOther" => $langData['additional/dataTypes/dataTypeOther'],
        ],
        "variableDictionnary" => [
            "variableDictionnaryAvailable" => $langData['additional/variableDictionnary/variableDictionnaryAvailable'], 
            "variableDictionnaryLink" => $langData['additional/variableDictionnary/variableDictionnaryLink']
        ],
        "dataQuality" => [
            "otherDocumentation" => $langData['additional/dataQuality/otherDocumentation']
        ],
        "mockSample" => [
            "mockSampleAvailable" => $langData['additional/mockSample/mockSampleAvailable'],
            "mockSampleLocation" => $langData['additional/mockSample/mockSampleLocation']
        ],
        "universe" => [
            "export" => false,
            "sexe" => $langData['level_sex_Clusion_I'] ?? [],
            "level_age_Clusion_I" => $langData['level_age_Clusion_I'] ?? [],
            "level_type_clusion_I" => $langData['stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I'] ?? '',
            "type_inclusion_autre" => $langData['additional/OtherPopulationType'] ?? '',
            "clusion_I" => $langData['stdyDscr/stdyInfo/sumDscr/universe/Clusion_I'] ?? '',
            "clusion_E" => $langData['stdyDscr/stdyInfo/sumDscr/universe/Clusion_E'] ?? ''
        ],
        "dataKind" => [
            "export" => false,
            "values" => $langData['dataKind'] ? convertJsonToArray($langData['dataKind']) : [],
        ],
        "geogCoverage" => [
            "export" => false,
            "values" => $langData['geogCover'] ? convertJsonToArray($langData['geogCover']) : []
        ],
        "sourceOrigine" => [
            "export" => false,
            "values" => $langData['sourcesOrigines']
        ]
    ];

    // conditional data if mode === 'add'
    $additionalWhenAdd = [];
    if ($mode === 'add') {
        $additionalWhenAdd = [
            "creationDate" => $currentDate,
            "contributorName" => $contributorFullName,
            "contributorAffiliation" => '',
        ];
    }

    $additional = array_merge($additionalBase, $additionalWhenAdd);

    // final payload     
    $payload = [
        "repositoryid" => "FReSH",
        "published" => 0,
        "doc_desc" => [
            "title" => $langData['stdyDscr/citation/titlStmt/titl'],
            "idno" => $idno
        ],
        "study_desc" => [
            "title_statement" => [
                "idno" => $idno,
                "IDno" => $langData['idnos'],
                "agent_schema" => $langData['stdyDscr/citation/IDno/agentSchema'],
                "other_agent" => $langData['stdyDscr/citation/IDno/otherAgent'],
                "uri" => $langData['stdyDscr/citation/IDno/uri'],
                "title" => $langData['stdyDscr/citation/titlStmt/titl'],
                "alternate_title" => $langData['stdyDscr/citation/titlStmt/altTitl'],
            ],
            "study_authorization" => $langData['agencies'],
            "authoring_entity" => $langData['authEntities'],
            "oth_id" => $langData['othIds'],
            "production_statement" => [
                "producers" => [
                    [
                        "name" => $langData['stdyDscr/citation/prodStmt/producer'],
                        "extlink" => [
                            "title" => $langData['stdyDscr/citation/prodStmt/producer/ExtLink/title'] ?? '',
                            "uri"   => $langData['stdyDscr/citation/prodStmt/producer/ExtLink/uri'] ?? ''
                        ],
                    ]
                ],
                "funding_agencies" => $langData['fundingAgencies']
            ],
            "distribution_statement" => [
                "contact" => $langData['contacts'] ??  []
            ],
            "study_info" => [
                "keywords" => $langData['keywords'] ?? [],
                "topics" => $langData['topics'],
                "purpose" => $langData['stdyDscr/stdyInfo/purpose/value'],
                "abstract" => $langData['stdyDscr/stdyInfo/abstract/value'],
                "coll_dates" => [
                    [
                        "start" => $langData['stdyDscr/stdyInfo/sumDscr/collDate/event_start'],
                        "end" => $langData['stdyDscr/stdyInfo/sumDscr/collDate/event_end'],
                    ]
                ],
                "nation" => $langData['nation'],
                "geog_coverage" => $langData['geogCover'] ?? '',
                "analysis_unit" => $langData['stdyDscr/stdyInfo/sumDscr/anlyUnit'],
                "universe" => json_encode([
                    "level_sex_clusion_I" => $langData['level_sex_Clusion_I'],
                    "level_age_clusion_I" => $langData['level_age_Clusion_I'],
                    "level_type_clusion_I" => $langData['stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I'],
                    "level_type_clusion_other" => $langData['additional/OtherPopulationType'],
                    "clusion_I" => $langData['stdyDscr/stdyInfo/sumDscr/universe/Clusion_I'],
                    "clusion_E" => $langData['stdyDscr/stdyInfo/sumDscr/universe/Clusion_E'],
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                "data_kind" => $langData['dataKind'],
                "quality_statement" =>
                [
                    "compliance_description" => $langData['otherQualityStatements'],
                    "standards" => [
                        [
                            "name" => $langData['standardsCompliance'],
                            "producer" => $langData['stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/value']
                        ]
                    ],
                    "other_quality_statement" => $langData['stdyDscr/stdyInfo/qualityStatement/otherQualityStatement']
                ]
            ],
            "study_development" => [
                "development_activity" => [ 
                    [
                        "activity_type" => $langData['stdyDscr/studyDevelopment/developmentActivity/type_primaryEvaluation/type'],
                        "activity_description" => $langData['stdyDscr/studyDevelopment/developmentActivity/type_primaryEvaluation/description']
                    ],
                    [
                        "activity_type" => $langData['stdyDscr/studyDevelopment/developmentActivity/type_secondaryEvaluation/type'],
                        "activity_description" => $langData['stdyDscr/studyDevelopment/developmentActivity/type_secondaryEvaluation/description']
                    ]
                ]
            ],
            "method" => [
                "data_collection" => [
                    "time_method" => $langData['stdyDscr/method/dataColl/timeMeth'],
                    "frequency" => $langData["stdyDscr/method/dataColl/frequenc"],
                    "sampling_procedure" => $langData["sampProc"],
                    "sample_frame" => [
                        "frame_unit" => [
                            "unit_type" => $langData['unitType']
                        ]
                    ],
                    "coll_mode" => $langData['callModes'],
                    "research_instrument" => $langData['stdyDscr/othStdMat/relMat'],
                    "sources" => $langData['sources'],
                    "target_sample_size" => $langData['stdyDscr/method/dataColl/targetSampleSize'],
                    "response_rate" => $langData['stdyDscr/method/dataColl/respRate'],
                ],
                "method_notes" => $langData['stdyDscr/method/notes/subject_researchType'],
                "study_class" => $langData['stdyDscr/method/stdyClas'],    
                "notes" => $langData['subject_followUP']
            ],
            "data_access" => [
                "dataset_availability" => [
                    "access_place"     => $langData['stdyDscr/dataAccs/setAvail/accsPlac'],
                    "complete"         => $langData['stdyDscr/dataAccs/setAvail/complete'],
                    "status"           => $langData['stdyDscr/dataAccs/setAvail/avlStatus'],
                ],
                "dataset_use" => [
                    "restrictions" => $langData['stdyDscr/dataAccs/useStmt/restrctn'],
                    "conditions"   => $langData['stdyDscr/dataAccs/setAvail/conditions'],
                    "conf_dec" => [
                        [
                            "txt"      => $langData['stdyDscr/dataAccs/useStmt/confDec']
                        ],
                    ],
                    "spec_perm" => [
                        [
                            "txt"      => $langData['stdyDscr/dataAccs/useStmt/specPerm'] ?? '',
                            "required" => $langData['stdyDscr/dataAccs/useStmt/specPerm/required_yes'] ?? ''
                        ],
                    ],
                    "contact" => [
                        [
                            "name" => $langData['useStmtContacts']
                        ]
                    ],
                    "deposit_req"  => $langData['stdyDscr/dataAccs/useStmt/deposReq'],
                    "cit_req"      => $langData['stdyDscr/dataAccs/useStmt/citReq'],
                ],
                "notes" => $langData['stdyDscr/dataAccs/setAvail/notes'],
            ],
        ],
    ];
    $payload['additional'] = $additional;


    return $payload;
}


function convertJsonToArray(string $str)
{
    // Convertir en vrai JSON
    $json = str_replace("'", '"', $str);

    // Décoder en tableau PHP
    $array = json_decode($json);
    return $array;
}

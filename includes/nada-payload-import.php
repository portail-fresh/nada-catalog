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

function prepare_nada_import_payload(array $langData, string $lang, string $idno = ''): array
{
    $payload = [
        "repositoryid" => "FReSH",
       "published" => 0,
        "doc_desc" => [
            "title" => (is_array($t = ($langData['stdyDscr/citation/titlStmt/titl'] ?? '')) ? ($t[0] ?? '') : $t),
            "idno" => $idno,
        ],
        "study_desc" => [
            "title_statement" => [
                "idno" => $idno,
                "IDno" => [],
                "title" => (is_array($t = ($langData['stdyDscr/citation/titlStmt/titl'] ?? '')) ? ($t[0] ?? '') : $t),
                "alternate_title" =>  (is_array($t = ($langData['stdyDscr/citation/titlStmt/altTitl'] ?? '')) ? ($t[0] ?? '') : $t),
            ],
            "study_authorization" => [
                "agency" => array_map(
                    fn($value) => [
                        "name" => $value,
                        "affiliation" => "",
                        "abbr" => ""
                    ],
                    array_values(array_filter(
                        $langData,
                        fn($v, $k) =>
                            str_starts_with($k, 'stdyDscr/studyAuthorization')
                            && str_ends_with($k, 'authorizingAgency'),
                        ARRAY_FILTER_USE_BOTH
                    ))
                )
            ],
            "authoring_entity" => [
                [
                    "name" => $langData['stdyDscr/citation/rspStmt/AuthEnty/name'] ?? '',
                    "affiliation" => $langData['stdyDscr/citation/rspStmt/AuthEnty/affiliation'] ?? '',
                    "type" => "investigator",
                    "extlink" => [
                        "title" => $langData['stdyDscr/citation/rspStmt/AuthEnty/ExtLink/pidSchema'] ?? '',
                        "uri" => $langData['stdyDscr/citation/rspStmt/AuthEnty/ExtLink/uri'] ?? ''
                    ],
                ]
            ],
            "oth_id" => $langData['stdyDscr/citation/rspStmt/othId'] ?? [],

            "production_statement" => [
                "producers" => [
                    [
                        "role" => $langData['stdyDscr/citation/prodStmt/producer/role'] ?? '', // add name/titl
                        "name" => (is_array($t = ($langData['stdyDscr/citation/prodStmt/producer/name/titl'] ?? '')) ? ($t[0] ?? '') : $t), // add name/titl
                        "extlink" => [
                            "role" => $langData['stdyDscr/citation/prodStmt/producer/role'] ?? '',
                            "title" => $langData['stdyDscr/citation/prodStmt/producer/ExtLink/title'] ?? '',
                            "uri" => $langData['stdyDscr/citation/prodStmt/producer/ExtLink/uri'] ?? ''
                        ],
                    ]
                ],
                "funding_agencies" => [
                    ["name" => (is_array($t = ($langData['stdyDscr/citation/prodStmt/fundAg'] ?? '')) ? ($t[0] ?? '') : $t)]
                ],
            ],
            "distribution_statement" => [
                "contact" => [
                    [
                        'type' => "contact",
                        'name' => $langData['stdyDscr/citation/distStmt/contact/name'] ?? '', 
                        'email' => $langData['stdyDscr/citation/distStmt/contact/email'] ?? '',
                    ]
                ],
            ],
            "study_info" => [
                "keywords" => array_map(
                                fn($k) => ["keyword" => $k], 
                                (array)($langData['stdyDscr/stdyInfo/subject/keyword'] ?? [])
                ),
                "topics" => $langData['stdyDscr/stdyInfo/subject/topcClas'],
                "abstract" => $langData['stdyDscr/stdyInfo/abstract'][0] ?? '', 
                "coll_dates" => [
                    [
                        "start" => $langData['stdyDscr/stdyInfo/sumDscr/collDate/event_start'] ?? '', 
                        "end" => $langData['stdyDscr/stdyInfo/sumDscr/collDate/event_end'] ?? ''
                    ]
                ],
                "nation" => $langData['stdyDscr/stdyInfo/sumDscr/nation'] ?? [],  
                "geog_coverage" =>(is_array($t = ($langData['stdyDscr/stdyInfo/sumDscr/geogCover'] ?? '')) ? ($t[0] ?? '') : $t), 
                "analysis_unit" => (is_array($t = ($langData['stdyDscr/stdyInfo/sumDscr/anlyUnit'] ?? '')) ? ($t[0] ?? '') : $t),
                "universe" => json_encode([
                    "level_sex_clusion_I" => $langData['level_sex_clusion_I'] ?? [],
                    "level_age_clusion_I" => $langData['level_age_clusion_I'] ?? [],
                    "level_type_clusion_I" => $langData['level_type_clusion_I'] ?? [],
                    "level_type_clusion_other" => $langData['additional/OtherPopulationType'] ?? '',
                    "clusion_I" => $langData['clusion_I'][0] ?? '',
                    "clusion_E" => $langData['clusion_E'][0] ?? '',
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                 "data_kind" => !empty($langData['stdyDscr/stdyInfo/sumDscr/dataKind'] ?? [])
                                        ? "['" . implode("','", (array)$langData['stdyDscr/stdyInfo/sumDscr/dataKind']) . "']"
                                        : '',
                "quality_statement" =>
                    [
                        "standards" => [
                            [
                                 "name" => !empty($langData['stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/' .
                                               ($lang === "fr" ? '0' : '1') . '/standardName'])
                                            ? "['" . $langData['stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/' .
                                                ($lang === "fr" ? '0' : '1') . '/standardName'] . "']"
                                            : (!empty($langData['stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName'])
                                                ? "['" . $langData['stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName'] . "']"
                                                : ''),


                            ]
                        ],
                        "other_quality_statement" => (is_array($t = ($langData['stdyDscr/stdyInfo/qualityStatement/otherQualityStatement'] ?? '')) ? ($t[0] ?? '') : $t)
                    ]
            ],
            "study_development" => [
                "development_activity" => $langData['stdyDscr/studyDevelopment/developmentActivity'] ?? []
            ],
            "method" => [
                "data_collection" => [
                   "time_method" => (is_array($t = ($langData['stdyDscr/method/dataColl/timeMeth'] ?? '')) ? ($t[0] ?? '') : $t),
                    "frequency" => (is_array($t = ($langData['stdyDscr/method/dataColl/frequenc'] ?? '')) ? ($t[0] ?? '') : $t), // frequenc not found in all doc
                   "sampling_procedure" => $langData['stdyDscr/method/dataColl/sampProc'] ?? '',
                    "sample_frame" => [
                        "frame_unit" => [
                            "unit_type" => !empty($langData['stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType'] ?? [])
                                            ? "['" . implode("','", (array)$langData['stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType']) . "']"
                                            : ''
                        ]
                    ],
                    "coll_mode" => $langData['stdyDscr/method/dataColl/collMode'] ?? [] ,
                    "research_instrument" => (is_array($t = ($langData['stdyDscr/othStdMat/relMat'] ?? '')) ? ($t[0] ?? '') : $t),
                    "sources" => (function() use ($langData) {
                        $sources = [];
                        $base = 'stdyDscr/method/dataColl/sources/';
                        $i = 0;
                        
                        // Check for indexed sources (0, 1, 2...)
                        while (isset($langData[$base . $i . '/sourceCitation']) || isset($langData[$base . $i . '/srcOrig'])) {
                            $srcOrig = $langData[$base . $i . '/srcOrig'] ?? [];
                            $sources[] = [
                                "citation" => $langData[$base . $i . '/sourceCitation'][0] ?? '',
                                "srcOrig" => !empty($srcOrig ?? []) ? "['" . implode("','", (array)$srcOrig) . "']" : '',
                                "notes" => array_filter(["subject_sourcePurpose" => $langData[$base . $i . '/notes/subject_sourcePurpose'][0] ?? '']),
                                "otherSourceType" => $langData[$base . $i . '/otherSourceType'][0] ?? ''
                            ];
                            $i++;
                        }
                        
                        // If no indexed sources, try non-indexed (single source)
                        if (empty($sources) && (isset($langData[$base . 'sourceCitation']) || isset($langData[$base . 'srcOrig']))) {
                            $srcOrig = $langData[$base . 'srcOrig'] ?? [];
                            $sources[] = [
                                "citation" => $langData[$base . 'sourceCitation'][0] ?? '',
                                "srcOrig" => !empty($srcOrig ?? []) ? "['" . implode("','", (array)$srcOrig) . "']" : '',
                                "notes" => array_filter(["subject_sourcePurpose" => $langData[$base . 'notes/subject_sourcePurpose'][0] ?? '']),
                                "otherSourceType" => $langData[$base . 'otherSourceType'][0] ?? ''
                            ];
                        }
                        
                        return $sources;
                    })(),
                    "target_sample_size" => (is_array($t = ($langData['stdyDscr/method/dataColl/targetSampleSize'] ?? '')) ? ($t[0] ?? '') : $t),
                    "response_rate" => (is_array($t = ($langData['stdyDscr/method/dataColl/respRate'] ?? '')) ? ($t[0] ?? '') : $t),
                ],
                "method_notes" => (is_array($t = ($langData['stdyDscr/method/notes'] ?? '')) ? ($t[0] ?? '') : $t), 
                "study_class" => is_array($langData['stdyDscr/method/stdyClas'] ?? null)
                    ? ($langData['stdyDscr/method/stdyClas'][0] ?? '') : ($langData['stdyDscr/method/stdyClas'] ?? ''),
                "notes" => (is_array($t = ($langData['stdyDscr/method/notes'] ?? '')) ? ($t[0] ?? '') : $t) 
            ],
            "data_access" => [
                "dataset_availability" => [                    
                    "access_place" =>(function($c) {
                                if (is_array($c)) {
                                    return isset($c[0]) && is_string($c[0]) ? $c[0] : '';
                                }
                                return is_string($c) ? $c : '';
                            })($langData['stdyDscr/dataAccs/setAvail/accsPlac'] ?? null) ,
                    "complete" =>(function($c) {
                                if (is_array($c)) {
                                    return isset($c[0]) && is_string($c[0]) ? $c[0] : '';
                                }
                                return is_string($c) ? $c : '';
                            })($langData['stdyDscr/dataAccs/setAvail/complete'] ?? null) ,
                    "status" =>(function($c) {
                                if (is_array($c)) {
                                    return isset($c[0]) && is_string($c[0]) ? $c[0] : '';
                                }
                                return is_string($c) ? $c : '';
                            })($langData['stdyDscr/dataAccs/setAvail/avlStatus'] ?? null) ,
                ],
                "dataset_use" => [
                    "restrictions" =>(function($c) {
                                if (is_array($c)) {
                                    return isset($c[0]) && is_string($c[0]) ? $c[0] : '';
                                }
                                return is_string($c) ? $c : '';
                            })($langData['stdyDscr/dataAccs/useStmt/restrctn'] ?? null) ,
                     "conditions"=> (function($c) {
                                if (is_array($c)) {
                                    return isset($c[0]) && is_string($c[0]) ? $c[0] : '';
                                }
                                return is_string($c) ? $c : '';
                            })($langData['stdyDscr/dataAccs/setAvail/conditions'] ?? null),
                    "conf_dec" => [
                        [
                            "txt" => (is_array($t = ($langData['stdyDscr/dataAccs/useStmt/confDec'] ?? '')) ? ($t[0] ?? '') : $t)
                        ],
                    ],
                    "spec_perm" => [
                        [
                            "txt" => (is_array($t = ($langData['stdyDscr/dataAccs/useStmt/specPerm'] ?? '')) ? ($t[0] ?? '') : $t), 
                            "required" => (is_array($t = ($langData['stdyDscr/dataAccs/useStmt/specPerm/required'] ?? '')) ? ($t[0] ?? '') : $t)
                        ],
                    ],
                    "contact" => [
                        [
                            "name" => (is_array($t = ($langData['stdyDscr/dataAccs/useStmt/contact'] ?? '')) ? ($t[0] ?? '') : $t) 
                        ]
                    ],
                    "deposit_req" => (is_array($t = ($langData['stdyDscr/dataAccs/useStmt/deposReq'] ?? '')) ? ($t[0] ?? '') : $t),
                    "cit_req" => (is_array($t = ($langData['stdyDscr/dataAccs/useStmt/citReq'] ?? '')) ? ($t[0] ?? '') : $t),
                ],
                "notes" => isset($langData['stdyDscr/dataAccs/setAvail/notes'])
                    ? (is_array($langData['stdyDscr/dataAccs/setAvail/notes'])
                        ? implode(' ', (array)$langData['stdyDscr/dataAccs/setAvail/notes'])
                        : $langData['stdyDscr/dataAccs/setAvail/notes'])
                    : '',

            ],
        ],
        "additional" => [
            "IsImport" => true,
            "provenance" => "PEF",
            "versionLang" => $lang,
            "originLang" => $lang,
            "creationDate" => $langData['additional/creationDate'][0] ?? '',
            "lastUpdatedManual" => $langData['additional/lastUpdatedManual'][0] ?? '',
            "contactEmail" =>  [ 
                "export" => false,
                "value" => $langData['stdyDscr/citation/distStmt/contact/email'] ? ($lang === 'fr' ? 'Oui' : 'Yes') : ($lang === 'fr' ? 'Non' : 'No'), 
            ],
            "obtainedAuthorization" => [
                "otherAuthorizingAgency" => $langData['stdyDscr/studyAuthorization/authorizingAgency'] ?? [] 
            ],
            "regulatoryRequirements" => [
                "conformityDeclaration" => $langData['additional/regulatoryRequirements/conformityDeclaration'] ?? '' 
            ],
            "fundingAgent" => [
                "fundingAgentType" => $langData['additional/fundingAgent/fundingAgentType'] ?? ''
            ],
            "sponsor" => [
                "sponsorType" => (is_array($t = ($langData['additional/sponsor/sponsorType'] ?? '')) ? ($t[0] ?? '') : $t)
            ],
            "governance" => [
                "committee" => $langData['additional/governance/committee'] ?? ''
            ],
            "collaborations" => [
                "networkConsortium" => $langData['additional/collaborations/networkConsortium'] ?? '', 
                "collaboration" => $langData['additional/collaborations/collaboration'][0]  ?? '' //
            ],
            "theme" => [
                "complementaryInformation" => $langData['additional/theme/complementaryInformation'] ?? '', 
                "RareDiseases" => $langData['additional/theme/RareDiseases'] ?? ''
            ],
            "collectionProcess" => [
                "collectionModeDetails" => (is_array($t = ($langData['additional/collectionProcess/collectionModeDetails'] ?? '')) ? ($t[0] ?? '') : $t),
                "collectionModeOther" => $langData['additional/collectionProcess/collectionModeOther'] 
            ],
            "dataCollection" => [
                "inclusionStrategyOther" => $langData['additional/dataCollection/inclusionStrategyOther'] ?? '',
                "samplingModeOther" => $langData['additional/dataCollection/samplingModeOther'] ?? '',
                "recruitmentSourceOther" => $langData['additional/dataCollection/recruitmentSourceOther'] ?? ''
            ],
            "activeFollowUp" => [
                "isActiveFollowUp" => $langData['additional/activeFollowUp/isActiveFollowUp'],
                "followUpModeOther" => $langData['additional/activeFollowUp/followUpModeOther'], 
            ],
            "dataCollectionIntegration" => [
                "isDataIntegration" => $langData['additional/dataCollectionIntegration/isDataIntegration']
            ],
            "thirdPartySource" => [
                "otherSourceType" => $langData['additional/thirdPartySource/otherSourceType'] 
            ],
            "geographicalCoverage" => [
                "geoDetail" => (is_array($t = ($langData['additional/geographicalCoverage/geoDetail'] ?? '')) ? ($t[0] ?? '') : $t)
            ],
            "dataTypes" => [
                "clinicalDataDetails" => (is_array($t = ($langData['additional/dataTypes/clinicalDataDetails'] ?? '')) ? ($t[0] ?? '') : $t),
                "biologicalDataDetails" => (is_array($t = ($langData['additional/dataTypes/biologicalDataDetails'] ?? '')) ? ($t[0] ?? '') : $t),
                "isDataInBiobank" => $langData['additional/dataTypes/isDataInBiobank'],
                "dataTypeOther" => $langData['additional/dataTypes/dataTypeOther'], 
            ],
            "variableDictionnary" => [
                "variableDictionnaryAvailable" => $langData['additional/variableDictionnary/variableDictionnaryAvailable'], 
                "variableDictionnaryLink" => (is_array($t = ($langData['additional/variableDictionnary/variableDictionnaryLink'] ?? '')) ? ($t[0] ?? '') : $t)
            ],
            "dataQuality" => [
                "otherDocumentation" => $langData['additional/dataQuality/otherDocumentation'] 
            ],
            "mockSample" => [
                "mockSampleAvailable" => $langData['additional/mockSample/mockSampleAvailable'], 
                "mockSampleLocation" => $langData['additional/mockSample/mockSampleLocation']
            ]
        ]
    ];

    return $payload;
}
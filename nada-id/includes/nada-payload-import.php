<?php
if (!defined('ABSPATH')) exit; // Sécurité

/**
 * Prépare le payload complet pour l'import NADA    
 * @param array
 * @return array
 */
function prepare_nada_import_payload(array $payload): array
{

    $d = $payload["data"];  // Données aplaties spécifiques à la langue
    $lang = $payload["lang"]; // Code langue ('fr' ou 'en')
    $idno = $payload["idno"];
    $authors = $payload["authors"]; // Identifiant unique généré
    $othIds = $payload["oth_ids"];  // Tableau traité des auteurs
    $distContacts = $payload["dist_contacts"];  // Tableau traité des autres IDs
    $useContacts = $payload["use_contacts"]; // Tableau traité des contacts
    $agencies = $payload["agencies"]; // Tableau traité des agences de financement
    $producers = $payload["producers"]; // Tableau traité des producteurs
    $hasEmailBooleanString = $payload["has_email_boolean_string"]; // Valeur 'Oui'/'Non' ou 'true'/'false'
    $facets = $payload["facets"]; // Tableau des valeurs par défaut pour les filtres à facettes
    $linkReport = $payload["link_report"]; // email du PI
    $linkTechnical = $payload["link_technical"]; // fullname du PI
    $linkStudy = $payload["link_study"]; // base idno
    $status = $payload["status"]; // is_published

    // Helper local pour extraire une valeur simple (premier élément si tableau)
    $val = fn($key) => isset($d[$key]) ? (is_array($d[$key]) ? $d[$key][0] : $d[$key]) : '';

    // Helper local pour formater les tableaux de chaînes en string JS "['a','b']" pour NADA
    $strArr = function ($arr) {
        if (empty($arr)) return "";
        if (!is_array($arr)) $arr = [$arr];
        // Échappement des quotes pour éviter de casser le JSON NADA
        $clean = array_map(fn($v) => str_replace("'", "\\'", trim($v)), $arr);
        return "['" . implode("','", $clean) . "']";
    };

    return [
        "repositoryid" => "FReSH",
        "published"    => $status,
        "link_study" => $linkStudy,
        "link_report" => $linkReport,
        "link_technical" => $linkTechnical,
        "link_indicator" => "imported",
        "merge_options" => "replace",
        "doc_desc" => [
            "title" => $val('stdyDscr/citation/titlStmt/titl'),
            "idno"  => $idno,
            "producers" => [
                [
                    "name" => $d['docDscr/citation/rspStmt/AuthEnty'] ?? "",
                    "affiliation" => $d['docDscr/citation/prodStmt/producer/0/affiliation'] ?? "",
                ]
            ],
        ],
        "study_desc" => [
            "title_statement" => [
                "idno" => $idno,
                "IDno" => ["metadata_no" => $d['stdyDscr/titlStmt/idnos'] ?? []],
                "title" => $val('stdyDscr/citation/titlStmt/titl'),
                "alternate_title" => $val('stdyDscr/citation/titlStmt/altTitl'),
            ],
            "study_authorization" => $d['study_authorization'] ?? [],
            "authoring_entity"     => $authors,
            "oth_id"               => $othIds,
            "production_statement" => [
                "prod_place"       => $lang === 'fr' ? 'Portail Epidémiologie France (PEF)' : 'Epidemiology France Portal (PEF)',
                "producers"        => $producers['list'],
                "funding_agencies" => $agencies['list'],
            ],
            "distribution_statement" => [
                "contact" => $distContacts,
            ],
            "study_info" => [
                "keywords" => array_map(fn($k) => ["keyword" => $k], (array)($d['stdyDscr/stdyInfo/subject/keyword'] ?? [])),
                "topics"   => $d['stdyDscr/stdyInfo/subject/topcClas'] ?? [],
                "purpose"  => $val('stdyDscr/stdyInfo/abstract/purpose'),
                "abstract" => $val('stdyDscr/stdyInfo/abstract/abstract'),
                "coll_dates" => [[
                    "start" => $d['stdyDscr/stdyInfo/sumDscr/collDate/event_start'] ?? '',
                    "end"   => $d['stdyDscr/stdyInfo/sumDscr/collDate/event_end'] ?? '',
                ]],
                "nation" => $d['stdyDscr/stdyInfo/sumDscr/nation'] ?? [],
                "geog_coverage" => $strArr($d['stdyDscr/stdyInfo/sumDscr/geogCover'] ?? []),
                "analysis_unit" => $val('stdyDscr/stdyInfo/sumDscr/anlyUnit'),
                "universe" => json_encode([
                    "level_sex_clusion_I"  => $d['level_sex_clusion_I'] ?? [],
                    "level_age_clusion_I"  => $d['level_age_clusion_I'] ?? [],
                    "level_type_clusion_I" => $d['level_type_clusion_I'] ?? [],
                    "level_type_clusion_other" => $val('additional/otherPopulationType'),
                    "clusion_I" => $d['clusion_I'] ?? '',
                    "clusion_E" => $d['clusion_E'] ?? '',
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),

                "data_kind" => $strArr($d['stdyDscr/stdyInfo/sumDscr/dataKind'] ?? []),

                "quality_statement" => [
                    "standards" => [[
                        "name" => $strArr($d['stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName'] ?? []),
                        "committee"  => $val('stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/committee'),
                        "governance" => $val('stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/governance')
                    ]],
                    "other_quality_statement" => $strArr($d['stdyDscr/stdyInfo/qualityStatement/otherQualityStatement'] ?? []),
                ]
            ],
            "study_development" => [
                "development_activity" => $d['stdyDscr/studyDevelopment/developmentActivity'] ?? []
            ],
            "method" => [
                "data_collection" => [
                    "time_method" => $val('stdyDscr/method/dataColl/timeMeth'),
                    "frequency"   => $val("stdyDscr/method/dataColl/frequenc"),
                    "sampling_procedure" => $strArr($d['stdyDscr/method/dataColl/sampProc'] ?? []),
                    "sample_frame" => [
                        "frame_unit" => [
                            "unit_type" => $strArr($d['stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType'] ?? []),
                        ],
                    ],
                    "coll_mode" => $d['stdyDscr/method/dataColl/collMode'] ?? [],
                    "research_instrument" => $val('stdyDscr/othStdMat/relMat'),
                    "sources" => $d['stdyDscr/method/dataColl/sources'] ?? [],
                    "target_sample_size" => cleanUnicodeSequences($val('stdyDscr/method/dataColl/targetSampleSize')),
                    "response_rate" => $val('stdyDscr/method/dataColl/respRate'),
                ],
                "method_notes" => $val('stdyDscr/method/notes/subject_researchType'),
                "study_class"  => $val('stdyDscr/method/stdyClas'),
                "notes"        => $d['stdyDscr/method/notes'] ?? [],
            ],
            "data_access" => [
                "dataset_availability" => [
                    "access_place" => $val('stdyDscr/dataAccs/setAvail/accsPlac'),
                    "complete"     => $val('stdyDscr/dataAccs/setAvail/complete'),
                    "status"       => $d['stdyDscr/dataAccs/setAvail/avlStatus'] ?? null,
                ],
                "dataset_use" => [
                    "restrictions" => $val('stdyDscr/dataAccs/useStmt/restrctn'),
                    "conditions"   => $val('stdyDscr/dataAccs/setAvail/conditions'),
                    "conf_dec"     => [["txt" => $val('stdyDscr/dataAccs/useStmt/confDec')]],
                    "spec_perm"    => [[
                        "txt" => $d['stdyDscr/dataAccs/useStmt/specPerm'] ?? '',
                        "required" => normalizeBooleanValue($d['stdyDscr/dataAccs/useStmt/specPerm/required'] ?? '', $lang)
                    ]],
                    "contact"      => $useContacts,
                    "deposit_req"  => $val('stdyDscr/dataAccs/useStmt/deposReq'),
                    "cit_req"      => $val('stdyDscr/dataAccs/useStmt/citReq'),
                ],
                "notes" => is_array($d['stdyDscr/dataAccs/setAvail/notes'] ?? '')
                    ? implode(' ', $d['stdyDscr/dataAccs/setAvail/notes'])
                    : ($d['stdyDscr/dataAccs/setAvail/notes'] ?? ''),
            ],
        ],
        "additional" => [
            "IsImport" => true,
            "versionLang" => $lang,
            "originLang" => $lang,
            "autoTranslation" => false,
            "status" => "imported",
            "creationDate" => $d['additional/creationDate'] ?? null,
            "lastUpdatedAuto" => $d['additional/lastUpdatedAuto'] ?? null,
            "lastUpdatedManual" => $d['additional/lastUpdatedManual'] ?? null,
            "isContributorPI" => $d['additional/isContributorPI'] ?? false,
            "contributorName" => $d['additional/contributorName'] ?? '',
            "contributorAffiliation" => $d['additional/contributorAffiliation'] ?? '',
            "addTeamMember" => $d['additional/addTeamMember'] ?? '',
            "contactEmail" =>  [
                "export" => false,
                "value" =>  normalizeBooleanValue($hasEmailBooleanString, $lang), // Oui/Non
            ],
            "obtainedAuthorization" => [
                "otherAuthorizingAgency" => $d['additional/obtainedAuthorization/otherAuthorizingAgency'] ?? []
            ],
            "relatedDocument" => $d['additional/relatedDocument'] ?? [],
            "regulatoryRequirements" => [
                "conformityDeclaration" => $val('additional/regulatoryRequirements/conformityDeclaration')
            ],
            "fundingAgent" => [
                "fundingAgentType"      => $agencies['types'],
                "otherFundingAgentType" => $agencies['otherTypes']
            ],
            "sponsor" => [
                "sponsorType"      => $producers['types'],
                "otherSponsorType" => $producers['otherTypes'],
            ],
            "governance" => [
                "committee" => $d['additional/governance/committee'] ?? '',
            ],
            "collaborations" => [
                "networkConsortium" => $d['additional/collaborations/networkConsortium'] ?? ''
            ],
            "theme" => [
                "complementaryInformation" => $d['additional/theme/complementaryInformation'] ?? '',
                "RareDiseases" => $d['additional/theme/RareDiseases'] ?? ''
            ],

            // Mapping des champs additionnels standards
            "activeFollowUp" => [
                "isActiveFollowUp" => $d['additional/activeFollowUp/isActiveFollowUp'] ?? '',
                "followUpModeOther" => $val('additional/activeFollowUp/followUpModeOther'),
            ],
            "interventionalStudy" => [
                "researchPurpose" =>  $d['additional/interventionalStudy/researchPurpose'] ?? [],
                "trialPhase" => $d['additional/interventionalStudy/trialPhase'] ?? [],
                "interventionalStudyModel" => $val('additional/interventionalStudy/interventionalStudyModel'),
                "isClinicalTrial" => $d['additional/interventionalStudy/isClinicalTrial'] ?? '',
                "otherResearchPurpose" =>  $val('additional/interventionalStudy/otherResearchPurpose')
            ],
            "isInclusionGroups" => $d['additional/isInclusionGroups'] ?? '',
            "allocation" => [
                "allocationMode" => $val('additional/allocation/allocationMode'),
                "allocationUnit" => $val('additional/allocation/allocationUnit')
            ],
            "masking" => [
                "maskingType" => $val('additional/masking/maskingType'),
                "blindedMaskingDetails" => $d['additional/masking/blindedMaskingDetails'] ?? []
            ],
            "arms" => $d['additional/arms'] ?? [],
            "intervention" => $d['additional/intervention'] ?? [],
            "inclusionGroups" => $d['additional/inclusionGroups'] ?? [],
            "collectionProcess" => [
                "collectionModeDetails" => isset($d['additional/collectionProcess/collectionModeDetails'])
                    ? (is_array($d['additional/collectionProcess/collectionModeDetails'])
                        ? ($d['additional/collectionProcess/collectionModeDetails'][0] ?? '')
                        : $d['additional/collectionProcess/collectionModeDetails'])
                    : '',
                "collectionModeOther" => $val('additional/collectionProcess/collectionModeOther'),
            ],
            "dataCollection" => [
                "inclusionStrategyOther" => $d['additional/dataCollection/inclusionStrategyOther'] ?? '',
                "samplingModeOther" => $val('additional/dataCollection/samplingModeOther'),
                "recruitmentSourceOther" => $val('additional/dataCollection/recruitmentSourceOther'),
                "otherDocumentation" => $val('additional/dataCollection/otherDocumentation')
            ],
            "dataCollectionIntegration" => [
                "isDataIntegration" => $d['additional/dataCollectionIntegration/isDataIntegration'] ?? '',
            ],
            "geographicalCoverage" => [
                "geoDetail" => $val('additional/geographicalCoverage/geoDetail')
            ],
            "dataTypes" => [
                "clinicalDataDetails" => isset($d['additional/dataTypes/clinicalDataDetails'])
                    ? (is_array($d['additional/dataTypes/clinicalDataDetails'])
                        ? ($d['additional/dataTypes/clinicalDataDetails'][0] ?? '')
                        : $d['additional/dataTypes/clinicalDataDetails'])
                    : '',
                "biologicalDataDetails" => $val('additional/dataTypes/biologicalDataDetails'),
                "isDataInBiobank" => $d['additional/dataTypes/isDataInBiobank'] ?? '',
                "biobankContent" => $d['additional/dataTypes/biobankContent'] ?? [],
                "biobankContentOther" => $val('additional/dataTypes/biobankContentOther'),
                "dataTypeOther" => $val('additional/dataTypes/dataTypeOther'),
            ],
            "variableDictionnary" => [
                "variableDictionnaryAvailable" => $d['additional/variableDictionnary/variableDictionnaryAvailable'] ?? '',
                "variableDictionnaryLink" => $val('additional/variableDictionnary/variableDictionnaryLink')
            ],
            "dataQuality" => [
                "otherDocumentation" => $val('additional/dataQuality/otherDocumentation')
            ],
            "mockSample" => [
                "mockSampleAvailable" => $d['additional/mockSample/mockSampleAvailable'] ?? '',
                "mockSampleLocation" => $val('additional/mockSample/mockSampleLocation')
            ],
            "thirdPartySource" => [
                "otherSourceType" => $d['additional/thirdPartySource/otherSourceType'] ?? [],
            ],
            "universe" => [
                "export" => false,
                "sexe" => $d['level_sex_clusion_I_filter'] ?? [],
                "level_age_clusion_I" => $d['level_age_clusion_I_filter'] ?? [],
                "level_type_clusion_I" => $d['level_type_clusion_I'] ?? [],
                "type_inclusion_autre" => $val('additional/otherPopulationType'),
                "clusion_I" => $val('clusion_I'),
                "clusion_E" => $val('clusion_E'),
            ],
            "observationalStudy" => ["export" => false, "values" => $facets['observationalStudy']],
            "avlStatus"          => ["export" => false, "values" => $facets['avlStatus']],
            "geogCoverage"       => ["export" => false, "values" => is_array($facets['geogCover']) ? array_unique($facets['geogCover']) : $facets['geogCover']],
            "dataKind"           => ["export" => false, "values" => is_array($facets['dataKind']) ? array_unique($facets['dataKind']) : $facets['dataKind']],
            "sourceOrigine"      => ["export" => false, "values" => is_array($facets['sourceOrigine']) ? array_unique($facets['sourceOrigine']) : $facets['sourceOrigine']],
            "researchType"       => ["export" => false, "values" => $facets['researchType']],
            "collectionStartDate" => ["export" => false, "values" => $facets['startDate']],
            "collectionEndDate"  => ["export" => false, "values" => $facets['endDate']],
            "targetSampleSize"   => ["export" => false, "values" => $facets['targetSampleSize']],
            "topicsHealthTheme"  => ["export" => false, "values" => is_array($facets['healthTheme']) ? array_unique($facets['healthTheme']) : $facets['healthTheme']],
            "topicsHealthDeterminant" => ["export" => false, "values" => is_array($facets['healthDeterminant']) ? array_unique($facets['healthDeterminant']) : $facets['healthDeterminant']],
            "age"                => ["export" => false, "values" => is_array($facets['age']) ? array_unique($facets['age']) : $facets['age']],
            "sex"                => ["export" => false, "values" => is_array($facets['sex']) ? array_unique($facets['sex']) : $facets['sex']],
            "populationType"     => ["export" => false, "values" => $facets['popType']],
            "specPermRequired"   => ["export" => false, "values" => $facets['specPermRequired']],
            "prodPlace"          => ["export" => false, "values" => $lang === 'fr' ? 'Portail Epidémiologie France (PEF)' : 'Epidemiology France Portal (PEF)'],
            "sponsorName" => [
                "export" => false,
                "values" => !empty($producers['list'])
                    ? array_unique(array_column($producers['list'], 'name'))
                    : ($lang == 'fr' ? 'Non renseigné' : 'Not specified'),
            ],
            "sponsorType" => [
                "export" => false,
                "values" => !empty($producers['types']) && is_array($producers['types'])
                    ? array_unique($producers['types'])
                    : ($producers['types'] ?? ($lang == 'fr' ? 'Non renseigné' : 'Not specified')),
            ],
            "isHealthTheme" => $d['additional/isHealthTheme'] ?? '',
            "trialPhase" => [
                "export" => false,
                "values" => $facets['trialPhase'] ?? ($lang === 'fr' ? 'Non renseigné' : 'Not specified'),
            ],
            "allocationMode" => [
                "export" => false,
                "values" => $val('additional/allocation/allocationMode') ?: ($lang === 'fr' ? 'Non renseigné' : 'Not specified'),
            ],
            "rareDiseases" => [
                "export" => false,
                "values" => isset($d['additional/theme/RareDiseases'])
                    ? ($d['additional/theme/RareDiseases']
                        ? ($lang === 'fr' ? 'Oui' : 'Yes')
                        : ($lang === 'fr' ? 'Non' : 'No'))
                    : ($lang === 'fr' ? 'Non renseigné' : 'Not specified'),
            ],
            "fileDscr" => [
                "fileTxt" => [
                    "fileCitation" => [
                        "titlStmt" => [
                            "IDno" => $d['additional/fileDscr/fileTxt/fileCitation/titlStmt/IDno'] ?? []
                        ]
                    ]
                ]
            ]
        ]
    ];
}

<?php

class Nada_Study_Api_Mapper
{
    /**
     * Prépare le payload Nada à partir d'un array $langData et du code langue
     *
     * @param array $langData Données du formulaire pour la langue $lang
     * @param string $lang 'fr' ou 'en'
     * @param string $idno optionnel, sinon généré automatiquement
     * @return array
     */


    public function prepare_nada_payload(array $langData, string $lang, string $mode, string $idno): array
    {
        // time zone
        $tz = new DateTimeZone('Europe/Paris');
        $now = new DateTimeImmutable('now', $tz);
        $currentDate = $now->format('d-m-Y');

        $prodPlace = 'France Recherche en Santé Humaine (FReSH)';
        if (strpos($idno, 'PEF')) {
            $prodPlace = $lang === 'fr' ? 'Portail Epidémiologie France (PEF)' : 'Epidemiology France Portal (PEF)';
        }

        $additionalBase = [
            "IsImport" => false,
            "versionLang" => $lang,
            "originLang" => $lang,
            "lastUpdatedAuto" => $currentDate,
            "lastUpdatedManual" => $currentDate,
            "autoTranslation" => $langData['additional/autoTranslation'],
            "status" => $langData['status'],
            "respValidation" => $langData['additional/respValidation'] ?? '',
            "isHealthTheme" => convertToBool($langData['additional/isHealthTheme'] ?? ''),
            "isContributorPI" => convertToBool($langData['additional/isContributorPI'] ?? ''),
            "addTeamMember" => convertToBool($langData['additional/addTeamMember'] ?? ''),
            "primaryInvestigator" => [
                "isPIContact" => convertToBool($langData['additional/primaryInvestigator/isPIContact'] ?? ''),
                "piMail" => $langData['additional/primaryInvestigator/piMail'] ?? '',
                "piAffiliation" => [
                    "piLabo" => $langData['additional/primaryInvestigator/piAffiliation/piLabo'] ?? '',
                ]
            ],
            "relatedDocument" => $langData['relatedDocument'] ?? [],
            "contactEmail" => [
                "export" => false,
                "value" => $langData['additional/contactEmail']
            ],
            "piEmail" => [
                "export" => false,
                "value" => $langData['additional/pi_email'] ?? ''
            ],
            "contactPointLabo" => $langData['additional/contactPointLabo'] ?? [],
            "obtainedAuthorization" => [
                "otherAuthorizingAgency" => $langData['otherAgencies'] ?? []
            ],
            "regulatoryRequirements" => [
                "conformityDeclaration" => $langData['additional/regulatoryRequirements/conformityDeclaration'] ?? ''
            ],
            "fundingAgent" => [
                "fundingAgentType" => $langData['fundingAgentTypes'] ?? '',
                "otherFundingAgentType" => $langData['otherFundingAgentTypes'] ?? ''
            ],
            "sponsor" => [
                "sponsorType" => $langData['sponsorType'] ?? '',
                "otherSponsorType" => $langData['otherSponsorType'] ?? '',

            ],
            "governance" => [
                "committee" => convertToBool($langData['additional/governance/committee'] ?? '')
            ],
            "collaborations" => [
                "networkConsortium" => convertToBool($langData['additional/collaborations/networkConsortium'] ?? '')
            ],
            "theme" => [
                "complementaryInformation" => $langData['additional/theme/complementaryInformation'] ?? '',
                "RareDiseases" => convertToBool($langData['additional/theme/RareDiseases'] ?? '')
            ],
            "interventionalStudy" => [
                "researchPurpose" => $langData['researchPurposes'],
                "trialPhase" => $langData['trialPhases'],
                "interventionalStudyModel" => $langData['additional/interventionalStudy/interventionalStudyModel'],
                "isClinicalTrial" => convertToBool($langData['additional/interventionalStudy/isClinicalTrial'] ?? ''),
                "otherResearchPurpose" => $langData['additional/interventionalStudy/otherResearchPurpose'],
            ],
            "isInclusionGroups" => convertToBool(($langData['additional/interventionalStudy/isInclusionGroups'] ?? $langData['additional/observationalStudy/isInclusionGroups']) ?? ''),
            "allocation" => [
                "allocationMode" => $langData['additional/allocation/allocationMode'],
                "allocationUnit" => $langData['additional/allocation/allocationUnit']
            ],
            "masking" => [
                "maskingType" => $langData['additional/masking/maskingType'],
                "blindedMaskingDetails" => $langData['blindedMaskingDetails']
            ],
            "arms" => $langData['arms'],
            "intervention" => $langData['interventions'],
            "cohortLongitudinal" => [
                "recrutementTiming" => $langData['recrutementTiming']
            ],
            "otherResearchType" => [
                "otherResearchTypeDetails" => $langData['additional/otherResearchType/otherResearchTypeDetails']
            ],
            "inclusionGroups" => $langData['inclusionGroups'],
            "nrInclusionGroups" => isset($langData['additional/inclusionGroups/nrInclusionGroups']) ?? null,
            "collectionProcess" => [
                "collectionModeDetails" => $langData['additional/collectionProcess/collectionModeDetails'],
                "collectionModeOther" => $langData['additional/collectionProcess/collectionModeOther']
            ],
            "dataCollection" => [
                "inclusionStrategy" => $langData['inclusionStrategy'],
                "inclusionStrategyOther" => $langData['additional/dataCollection/inclusionStrategyOther'] ?? null,
                "samplingModeOther" => $langData['additional/dataCollection/samplingModeOther'] ?? null,
                "recruitmentSourceOther" => $langData['additional/dataCollection/recruitmentSourceOther'],
                "otherDocumentation" => $langData['additional/dataCollection/otherDocumentation'],
            ],
            "activeFollowUp" => [
                "isActiveFollowUp" => convertToBool($langData['additional/activeFollowUp/isActiveFollowUp']),
                "followUpModeOther" => $langData['additional/activeFollowUp/followUpModeOther'],
            ],
            "dataCollectionIntegration" => [
                "isDataIntegration" =>  convertToBool($langData['additional/dataCollectionIntegration/isDataIntegration'] ?? null)
            ],
            "geographicalCoverage" => [
                "geoDetail" => $langData['additional/geographicalCoverage/geoDetail']
            ],
            "dataTypes" => [
                "clinicalDataDetails" => $langData['additional/dataTypes/clinicalDataDetails'],
                "biologicalDataDetails" => $langData['additional/dataTypes/biologicalDataDetails'],
                "isDataInBiobank" => convertToBool($langData['additional/dataTypes/isDataInBiobank'] ?? null),
                "biobankContent" => $langData['biobankContent'],
                "biobankContentOther" => $langData['additional/dataTypes/biobankContentOther'],
                "dataTypeOther" => $langData['additional/dataTypes/dataTypeOther'],
                "paraclinicalDataOther" => $langData['additional/dataTypes/paraclinicalDataOther'],
                "otherLiquidsDetails" => $langData['additional/dataTypes/otherLiquidsDetails'],
            ],
            "variableDictionnary" => [
                "variableDictionnaryAvailable" => convertToBool($langData['additional/variableDictionnary/variableDictionnaryAvailable'] ?? null),
                "variableDictionnaryLink" => $langData['additional/variableDictionnary/variableDictionnaryLink']
            ],
            "dataQuality" => [
                "otherDocumentation" => $langData['additional/dataQuality/otherDocumentation'] ?? ""
            ],
            "mockSample" => [
                "mockSampleAvailable" => convertToBool($langData['additional/mockSample/mockSampleAvailable'] ?? null),
                "mockSampleLocation" => $langData['additional/mockSample/mockSampleLocation']
            ],
            "thirdPartySource" => [
                "otherSourceType" => $langData['otherSourceType'],
            ],
            "universe" => [
                "export" => false,
                "sexe" => $this->extract_values($langData['level_sex_Clusion_I'] ?? []),
                "level_age_clusion_I" => $this->extract_values($langData['level_age_Clusion_I'] ?? []),
                "level_type_clusion_I" => $langData['stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I'] ?? '',
                "type_inclusion_autre" => $langData['additional/OtherPopulationType'] ?? '',
                "clusion_I" => $langData['stdyDscr/stdyInfo/sumDscr/universe/Clusion_I'] ?? '',
                "clusion_E" => $langData['stdyDscr/stdyInfo/sumDscr/universe/Clusion_E'] ?? ''
            ],
            "dataKind" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue(
                    array_unique(
                        !empty($langData['dataKind']) ? (array)$this->convertJsonToArray($langData['dataKind']) : []
                    ),
                    $lang
                ),
            ],
            "geogCoverage" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue(
                    array_unique(
                        !empty($langData['geogCover']) ? (array)$this->convertJsonToArray($langData['geogCover']) : []
                    ),
                    $lang
                ),
            ],
            "avlStatus" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($langData['stdyDscr/dataAccs/setAvail/avlStatus'] ?? '', $lang),
            ],
            "sourceOrigine" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue(
                    !empty($langData['sourcesOrigines'])
                        ? array_unique((array)$langData['sourcesOrigines'])
                        : [],
                    $lang
                ),
            ],
            "observationalStudy" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($langData['stdyDscr/method/notes'] ?? '', $lang),
            ],
            "prodPlace" => [
                "export" => false,
                "values" => $prodPlace,
            ],
            "researchType" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($langData['stdyDscr/method/notes/subject_researchType'] ?? '', $lang),
            ],
            "collectionStartDate" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($langData['stdyDscr/stdyInfo/sumDscr/collDate/event_start'] ?? '', $lang),
            ],
            "collectionEndDate" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($langData['stdyDscr/stdyInfo/sumDscr/collDate/event_end'] ?? '', $lang),
            ],
            "targetSampleSize" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue(isset($langData['stdyDscr/method/dataColl/targetSampleSize']) ? cleanUnicodeSequences(get_lang_value($langData['stdyDscr/method/dataColl/targetSampleSize'])) : '', $lang),
            ],
            "sponsorName" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue(!empty($langData['producers']) ? array_unique((array)$langData['producers']) : [], $lang),
            ],
            "sponsorType" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue(!empty($langData['sponsorType']) ? array_unique((array)$langData['sponsorType']) : [], $lang),
            ],
            "topicsHealthTheme" => [
                "export" => false,
                "values" => !empty($langData['additional/topicsHealthTheme']) ? array_unique((array)$langData['additional/topicsHealthTheme']) : ($lang == 'fr' ? 'Pas de spécialité médicale spécifique' : 'No specific medical speciality'),
            ],
            "topicsHealthDeterminant" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue(!empty($langData['additional/topicsHealthDeterminant']) ? array_unique((array)$langData['additional/topicsHealthDeterminant']) : [], $lang),
            ],
            "age" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($this->extract_values($langData['level_age_Clusion_I'] ?? []), $lang),
            ],
            "sex" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($this->extract_values($langData['level_sex_Clusion_I'] ?? []), $lang),
            ],
            "populationType" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($langData['level_type_clusion_I'] ?? '', $lang),
            ],
            "trialPhase" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($langData['additional/interventionalStudy/trialPhase'] ?? [], $lang),
            ],
            "allocationMode" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($langData['additional/allocation/allocationMode'] ?? '', $lang),
            ],
            "rareDiseases" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($langData['additional/theme/RareDiseases'] ?? '', $lang),
            ],
            "specPermRequired" => [
                "export" => false,
                "values" => $this->checkIfEmptyValue($langData['stdyDscr/dataAccs/useStmt/specPerm/required_yes'] ?? '', $lang),
            ],
            "fileDscr" => [
                "fileTxt" => [
                    "fileCitation" => [
                        "titlStmt" => [
                            "IDno" => $langData['datasetPIDs']
                        ]
                    ]
                ]
            ],
        ];

        // conditional data if mode === 'add'
        $additionalWhenAdd = [];
        if ($mode === 'add') {
            $additionalWhenAdd = [
                "creationDate" => $currentDate,
                "contributorName" => $langData['contributorFullName'],
                "contributorAffiliation" => $langData['contributorAffiliation'] ?? '',
            ];
        }

        $additional = array_merge($additionalBase, $additionalWhenAdd);
        $payload = [
            "repositoryid" => "FReSH",
            "published" => 0,
            "merge_options" => "replace",
            "link_study" => $langData['link_study'] ?? '',
            "link_indicator" => $langData['status_key'],
            "link_report" => $langData['link_report'] ?? '',
            "link_technical" => $langData['link_technical'] ?? '',
            "doc_desc" => [
                "title" => $langData['stdyDscr/citation/titlStmt/titl'],
                "idno" => $idno,
                "producers" => [
                    [
                        "name" => $langData['contributorFullName'],
                        "affiliation" => $langData['contributorAffiliation'] ?? '',
                    ]
                ],
            ],
            "study_desc" => [
                "title_statement" => [
                    "idno" => $idno,
                    "IDno" => [
                        'metadata_no' => $langData['other_idnos'],
                        'metadata_yes' => [["code" => "FReSH", "agency" => "FReSH"]]
                    ],
                    "uri" => $langData['stdyDscr/citation/IDno/uri'],
                    "title" => $langData['stdyDscr/citation/titlStmt/titl'],
                    "alternate_title" => $langData['stdyDscr/citation/titlStmt/altTitl'],
                ],
                "study_authorization" => $langData['agencies'],
                "authoring_entity" => $langData['authEntities'],
                "oth_id" => $langData['othIds'],
                "production_statement" => [
                    "prod_place" => $prodPlace,
                    "producers" => $langData['producers'] ?? [],
                    "funding_agencies" => $langData['fundingAgencies']
                ],
                "distribution_statement" => [
                    "contact" => $langData['contacts'] ?? []
                ],
                "study_info" => [
                    "keywords" => $langData['keywords'] ?? [],
                    "topics" => $langData['topics'],
                    "purpose" => $langData['stdyDscr/stdyInfo/purpose/value'],
                    "abstract" => $langData['stdyDscr/stdyInfo/abstract/value'],
                    "coll_dates" => [
                        [
                            "start" => $langData['stdyDscr/stdyInfo/sumDscr/collDate/event_start'] ?? '',
                            "end" => !empty($langData['stdyDscr/stdyInfo/sumDscr/collDate/event_end'])
                                ? $langData['stdyDscr/stdyInfo/sumDscr/collDate/event_end']
                                : (!empty($langData['stdyDscr/stdyInfo/sumDscr/collDate/event_start']) ? '3000-01-01' : ''),
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
                        "standards" => [
                            [
                                "name" => $langData['standardsCompliance'],
                                "committee" => $langData['stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/committee'],
                                "governance" => $langData['stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/governance']

                            ]
                        ],
                        "other_quality_statement" => $langData['otherQualityStatements']
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
                    "notes" => $langData['methodNotes']
                ],
                "data_access" => [
                    "dataset_availability" => [
                        "access_place" => $langData['stdyDscr/dataAccs/setAvail/accsPlac'],
                        "complete" => $langData['stdyDscr/dataAccs/setAvail/complete'],
                        "status" => $langData['individual_data_access'] ?? '',
                    ],
                    "dataset_use" => [
                        "restrictions" => $langData['stdyDscr/dataAccs/useStmt/restrctn'],
                        "conditions" => $langData['stdyDscr/dataAccs/setAvail/conditions'],
                        "conf_dec" => [
                            [
                                "txt" => $langData['stdyDscr/dataAccs/useStmt/confDec']
                            ],
                        ],
                        "spec_perm" => [
                            [
                                "txt" => $langData['stdyDscr/dataAccs/useStmt/specPerm'] ?? '',
                                "required" => $langData['stdyDscr/dataAccs/useStmt/specPerm/required_yes'] ?? ''
                            ],
                        ],
                        "contact" => $langData['useStmtContacts'] ?? [],
                        "deposit_req" => $langData['stdyDscr/dataAccs/useStmt/deposReq'],
                        "cit_req" => $langData['stdyDscr/dataAccs/useStmt/citReq'],
                    ],
                    "notes" => $langData['stdyDscr/dataAccs/setAvail/notes'],
                ],
            ],
        ];
        $payload['additional'] = $additional;


        return $payload;
    }


    private function convertJsonToArray(string $str)
    {
        $json = str_replace("'", '"', $str);
        return json_decode($json);
    }
    // Fonction utilitaire pour extraire uniquement les valeurs d'un tableau
    // d'objets contenant une clé "value" (par exemple : [{"concept": {...}, "value": "Texte"}])
    private function extract_values($items, $key = 'value')
    {
        if (!is_array($items)) {
            return [];
        }
        $values = [];
        foreach ($items as $item) {
            if (is_array($item) && isset($item[$key])) {
                $values[] = $item[$key];
            } elseif (is_string($item)) {
                $values[] = $item;
            }
        }
        return array_unique($values);
    }

    /* Fonction pour vérifier si une valeur est vide en retourne une valeur par défaut pour le filtre du facette */
    private function checkIfEmptyValue($value, string $lang, string $fr = 'Non renseigné', string $en = 'Not specified')
    {
        $default = $lang === 'fr' ? $fr : $en;
        // Cas null / vide simple
        if ($value === null || $value === '' || $value === []) {
            return $default;
        }

        // Cas string
        if (is_string($value)) {
            return trim($value) !== '' ? $value : $default;
        }

        // Cas tableau
        if (is_array($value)) {
            $filtered = array_filter(array_map(function ($item) {
                if (is_string($item)) {
                    return trim($item) !== '' ? $item : null;
                }
                if (is_array($item) && isset($item['name']) && trim($item['name']) !== '') {
                    return $item['name'];
                }
                return null;
            }, $value), fn($v) => $v !== null);

            return !empty($filtered) ? array_values($filtered) : $default;
        }

        return $default;
    }
}

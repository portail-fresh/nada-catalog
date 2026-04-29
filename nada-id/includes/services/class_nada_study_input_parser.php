<?php
class Nada_Study_Input_Parser
{
    public function parse(array  $postData, array $userInfo, string $studySource, array $translatableFields, string $mode): array
    {
        // Tableaux pour stocker les champs qui ont besoin de traduction
        $frNeedsTranslation = [];
        $enNeedsTranslation = [];
        $statusKey = $postData['status_key'];
        $isSubmit = $postData['is_submit']; //Cliquer sur le bouton terminer ou non
        $translateEnabled = $this->is_enable_translate($postData['enable_translation']);
        // Tableau pour stocker les données parsés
        $result    = [
            'fr' => [],
            'en' => [],
        ];

        // Parcourir chaque clé envoyée et séparer par langue
        foreach ($postData as $key => $value) {
            if (preg_match('/^(.*)_(fr|en)$/', $key, $matches)) {
                $baseKey     = $matches[1]; // ex: stdyDscr/citation/titlStmt/titl
                $lang        = $matches[2]; // fr ou en
                $result[$lang][$baseKey] = wp_unslash($value);
                // Si traduction activée et valeur remplie
                if ($translateEnabled && !empty($value) && in_array($baseKey, $translatableFields)) {
                    // Construire les clés pour les deux langues
                    $key_fr = $baseKey . '_fr';
                    $key_en = $baseKey . '_en';

                    // Vérifier si les deux langues ont des valeurs
                    $has_fr = !empty($postData[$key_fr]);
                    $has_en = !empty($postData[$key_en]);

                    // Ne pas ajouter à la traduction si les deux langues sont déjà remplies
                    if (!($has_fr && $has_en)) {
                        if ($lang === 'fr') {
                            $frNeedsTranslation[$baseKey] = wp_unslash($value);
                        } elseif ($lang === 'en') {
                            $enNeedsTranslation[$baseKey] = wp_unslash($value);
                        }
                    }
                }
            } elseif ($key == "complexData") {
                $complexData = json_decode(stripslashes($value), true);
                $result['fr']['keywords'] = $complexData['keywords_fr'] ?? [];
                $result['en']['keywords'] = $complexData['keywords_en'] ?? [];

                $result['en']['relatedDocument'] = $complexData['relatedDocument_en'];
                $result['fr']['relatedDocument'] = $complexData['relatedDocument_fr'];

                $result['en']['producers'] = $complexData['producers_en'];
                $result['fr']['producers'] = $complexData['producers_fr'];
                $result['fr']['sponsorType'] = $complexData['producersTypes_fr'];
                $result['en']['sponsorType'] = $complexData['producersTypes_en'];
                $result['fr']['otherSponsorType'] = $complexData['otherProducersTypes_fr'];
                $result['en']['otherSponsorType'] = $complexData['otherProducersTypes_en'];

                $result['fr']['fundingAgencies'] = $complexData['fundingAgencies_fr'];
                $result['en']['fundingAgencies'] = $complexData['fundingAgencies_en'];
                $result['fr']['fundingAgentTypes'] = $complexData['fundingAgentTypes_fr'];
                $result['en']['fundingAgentTypes'] = $complexData['fundingAgentTypes_en'];
                $result['fr']['otherFundingAgentTypes'] = $complexData['otherFundingAgentTypes_fr'];
                $result['en']['otherFundingAgentTypes'] = $complexData['otherFundingAgentTypes_en'];
                $result['fr']['callModes'] = $complexData['callModes_fr'];
                $result['en']['callModes'] = $complexData['callModes_en'];
                $result['fr']['agencies'] = [
                    "agency" => array_map(
                        fn($agency) => ["name" => wp_unslash($agency)],
                        $complexData['agencies_fr'] ?? []
                    )
                ];
                $result['en']['agencies'] = [
                    "agency" => array_map(
                        fn($agency) => ["name" => wp_unslash($agency)],
                        $complexData['agencies_en'] ?? []
                    )
                ];

                $result['en']['otherAgencies'] = $complexData['otherAgencies_en'];
                $result['fr']['otherAgencies'] = $complexData['otherAgencies_fr'];
                $result['en']['authEntities'] = $complexData['authEntities_en'];
                $result['fr']['authEntities'] = $complexData['authEntities_fr'];
                $result['en']['othIds'] = $complexData['othIds_en'];
                $result['fr']['othIds'] = $complexData['othIds_fr'];


                $result['en']['contacts'] = $complexData['distContacts_en'];
                $result['fr']['contacts'] = $complexData['distContacts_fr'];
                $result['en']['topics'] = $complexData['topics_en'];
                $result['fr']['topics'] = $complexData['topics_fr'];


                // Interventions
                $result['en']['interventions'] = array_values(array_filter(
                    $complexData['interventions_en'],
                    function ($item) {
                        return !empty(trim($item['name'] ?? '')) ||
                            !empty(trim($item['type'] ?? '')) ||
                            !empty(trim($item['typeOther'] ?? ''));
                    }
                ));

                $result['fr']['interventions'] = array_values(array_filter(
                    $complexData['interventions_fr'],
                    function ($item) {
                        return !empty(trim($item['name'] ?? '')) ||
                            !empty(trim($item['type'] ?? '')) ||
                            !empty(trim($item['typeOther'] ?? ''));
                    }
                ));

                // recrutementTiming
                $result['en']['recrutementTiming'] = $complexData['recrutementTiming_en'];
                $result['fr']['recrutementTiming'] = $complexData['recrutementTiming_fr'];


                // Research Purposes
                $result['en']['researchPurposes'] = $complexData['researchPurposes_en'];
                $result['fr']['researchPurposes'] = $complexData['researchPurposes_fr'];

                // Trial Phases
                $result['en']['trialPhases'] = $complexData['trialPhases_en'];
                $result['fr']['trialPhases'] = $complexData['trialPhases_fr'];

                // Blinded Masking Details
                $result['en']['blindedMaskingDetails'] = $complexData['blindedMaskingDetails_en'];
                $result['fr']['blindedMaskingDetails'] = $complexData['blindedMaskingDetails_fr'];

                // Inclusion Strategy
                $result['en']['inclusionStrategy']   = $complexData['inclusionStrategy_en'];
                $result['fr']['inclusionStrategy']   = $complexData['inclusionStrategy_fr'];

                // Sampling Procedure
                $result['en']['sampProc'] = empty($complexData['sampProc_en']) ? '' : "['" . implode("','", array_values($complexData['sampProc_en'])) . "']";
                $result['fr']['sampProc'] = empty($complexData['sampProc_fr']) ? '' : "['" . implode("','", array_values($complexData['sampProc_fr'])) . "']";

                // Unit Type
                $result['en']['unitType'] = empty($complexData['unitType_en']) ? '' : "['" . implode("','", array_values($complexData['unitType_en'])) . "']";
                $result['fr']['unitType'] = empty($complexData['unitType_fr']) ? '' : "['" . implode("','", array_values($complexData['unitType_fr'])) . "']";

                // Age Inclusion Level I
                $result['en']['level_age_Clusion_I'] = $complexData['level_age_Clusion_I_en'];
                $result['fr']['level_age_Clusion_I'] = $complexData['level_age_Clusion_I_fr'];

                // FR
                $result['fr']['nation'] = array_map(
                    fn($code) => [
                        'name' => (string)$code["value"],
                        'abbreviation' => (string) $code["concept"]["vocabURI"],
                        'extLink' => $code["concept"]
                    ],
                    (array) ($complexData['nation_fr'] ?? [])
                );

                // EN
                $result['en']['nation'] = array_map(
                    fn($code) => [
                        'name' => (string)$code["value"],
                        'abbreviation' => (string) $code["concept"]["vocabURI"],
                        'extLink' => $code["concept"]
                    ],
                    (array) ($complexData['nation_en'] ?? [])
                );


                // Geographic Coverage
                $result['en']['geogCover'] = empty($complexData['geogCover_en']) ? '' : "['" . implode("','", array_values($complexData['geogCover_en'])) . "']";
                $result['fr']['geogCover'] = empty($complexData['geogCover_fr']) ? '' : "['" . implode("','", array_values($complexData['geogCover_fr'])) . "']";

                // Subject Follow-Up
                $result['en']['subject_followUP'] = $complexData['subject_followUP_en'];
                $result['fr']['subject_followUP'] = $complexData['subject_followUP_fr'];

                // Sources
                $sourcesFr = $complexData['sources_fr'];
                $sourcesEn =  $complexData['sources_en'];
                $result['en']['sources'] = $sourcesEn;
                $result['fr']['sources']  = $sourcesFr;

                // Récupère tous les srcOrig, fusionne et enlève les doublons
                $uniqueSrcOrigFr = array_values(array_unique(array_merge(...array_column($sourcesFr, 'srcOrig'))));
                $uniqueSrcOrigEn = array_values(array_unique(array_merge(...array_column($sourcesEn, 'srcOrig'))));

                $result['fr']['otherSourceType'] = $complexData['otherSourceType_fr'];
                $result['en']['otherSourceType'] =  $complexData['otherSourceType_en'];

                // Sources origines
                $result['en']['sourcesOrigines'] = $uniqueSrcOrigEn;
                $result['fr']['sourcesOrigines'] = $uniqueSrcOrigFr;

                // Sex Inclusion Level I
                $result['en']['level_sex_Clusion_I'] = $complexData['level_sex_Clusion_I_en'];
                $result['fr']['level_sex_Clusion_I'] = $complexData['level_sex_Clusion_I_fr'];

                //standardsCompliance
                $result['en']['standardsCompliance'] = empty($complexData['standardsCompliance_en']) ? '' : "['" . implode("','", array_values($complexData['standardsCompliance_en'])) . "']";
                $result['fr']['standardsCompliance'] = empty($complexData['standardsCompliance_fr']) ? '' : "['" . implode("','", array_values($complexData['standardsCompliance_fr'])) . "']";

                //otherQualityStatements
                $result['en']['otherQualityStatements'] = empty($complexData['otherQualityStatements_en']) ? '' : "['" . implode("','", array_values($complexData['otherQualityStatements_en'])) . "']";
                $result['fr']['otherQualityStatements'] = empty($complexData['otherQualityStatements_fr']) ? '' : "['" . implode("','", array_values($complexData['otherQualityStatements_fr'])) . "']";


                //useStmtContacts
                $result['en']['useStmtContacts'] = $complexData['useStmtContacts_en'];
                $result['fr']['useStmtContacts'] = $complexData['useStmtContacts_fr'];


                //idnos
                $result['en']['idnos'] = $complexData['idnos_en'];
                $result['fr']['idnos'] = $complexData['idnos_fr'];

                //biobankContent
                $result['en']['biobankContent'] = $complexData['biobankContent_en'];
                $result['fr']['biobankContent'] = $complexData['biobankContent_fr'];

                //dataKind
                $result['en']['dataKind'] = empty($complexData['dataKind_en']) ? '' : "['" . implode("','", array_values($complexData['dataKind_en'])) . "']";
                $result['fr']['dataKind'] = empty($complexData['dataKind_fr']) ? '' : "['" . implode("','", array_values($complexData['dataKind_fr'])) . "']";

                //inclusionGroups
                $result['en']['inclusionGroups'] = array_values(array_filter(
                    $complexData['inclusionGroups_en'],
                    function ($item) {
                        return !empty(trim($item['name'] ?? '')) ||
                            !empty(trim($item['description'] ?? ''));
                    }
                ));

                $result['fr']['inclusionGroups'] = array_values(array_filter(
                    $complexData['inclusionGroups_fr'],
                    function ($item) {
                        return !empty(trim($item['name'] ?? '')) ||
                            !empty(trim($item['description'] ?? ''));
                    }
                ));
                //arms
                $result['en']['arms'] = array_values(array_filter(
                    $complexData['arms_en'],
                    function ($item) {
                        return !empty(trim($item['name'] ?? '')) ||
                            !empty(trim($item['type'] ?? '')) ||
                            !empty(trim($item['typeOther'] ?? '')) ||
                            !empty(trim($item['description'] ?? ''));
                    }
                ));

                $result['fr']['arms'] = array_values(array_filter(
                    $complexData['arms_fr'],
                    function ($item) {
                        return !empty(trim($item['name'] ?? '')) ||
                            !empty(trim($item['type'] ?? '')) ||
                            !empty(trim($item['typeOther'] ?? '')) ||
                            !empty(trim($item['description'] ?? ''));
                    }
                ));

                //arms
                $result['en']['other_idnos'] = $complexData['other_idnos_en'];
                $result['fr']['other_idnos'] = $complexData['other_idnos_fr'];

                $result['en']['methodNotes'] = $complexData['methodNotes_en'];
                $result['fr']['methodNotes'] = $complexData['methodNotes_fr'];

                $result['en']['datasetPIDs'] = $complexData['datasetPIDs_en'];
                $result['fr']['datasetPIDs'] = $complexData['datasetPIDs_fr'];

                $result['en']['individual_data_access'] = $complexData['individual_data_access_en'];
                $result['fr']['individual_data_access'] = $complexData['individual_data_access_fr'];
            } else {
                // Cas sans suffixe -> copier dans les deux langues
                $result['fr'][$key] = wp_unslash($value);
                $result['en'][$key] = wp_unslash($value);
            }

            // Auto-translation flag
            $result['fr']['additional/autoTranslation'] = $translateEnabled;
            $result['en']['additional/autoTranslation'] = $translateEnabled;

            if ($isSubmit == 1 || $statusKey == "pending") {
                $statusKey = "pending";
            } elseif ($statusKey == "imported" && $mode == 'edit') {
                $statusKey = "imported";
            } else {
                $statusKey = "draft";
            }


            $firstContact =  isset($result['fr']['contacts']) ? $result['fr']['contacts'][0] : [];

            $firstInvestigator = isset($result['fr']['authEntities']) ? $result['fr']['authEntities'][0] : [];

            $firstInvestigatorEmail = $firstInvestigator['email'] ?? '';
            $firstInvestigatorFullname = '';

            if (!empty($firstInvestigator)) {
                $firstname = $firstInvestigator['firstname'] ?? '';
                $lastname  = $firstInvestigator['lastname'] ?? '';

                $firstInvestigatorFullname = trim($firstname . ' ' . $lastname);
            }

            $result['fr']['link_study'] =  $studySource;
            $result['en']['link_study'] = $studySource;
            $result['en']['additional/pi_email'] = $firstInvestigatorEmail;
            $result['fr']['additional/pi_email'] =   $firstInvestigatorEmail;
            $result['en']['link_report'] = $firstInvestigatorEmail;
            $result['fr']['link_report'] =   $firstInvestigatorEmail;
            $result['en']['link_technical'] = $firstInvestigatorFullname;
            $result['fr']['link_technical'] =   $firstInvestigatorFullname;

            // verification si le utilisateur connecte est Pi:  si oui:respValidation= true sinon =''
            $respValidation = '';

            if (isset($postData['additional/isContributorPI_fr']) && $postData['additional/isContributorPI_fr'] === 'Oui') {
                $respValidation = 'true';
            }
            $result['fr']['respValidation'] = $respValidation;
            $result['en']['respValidation'] = $respValidation;
            // Status
            $statusValues = get_translated_value_in_both_languages($statusKey);
            $result['fr']['status'] =  $statusValues['fr'];
            $result['en']['status'] = $statusValues['en'];
            $result['fr']['status_key'] = $statusKey;
            $result['en']['status_key'] = $statusKey;
            $result['fr']['additional/contactEmail'] = isset($result['fr']['contacts']) ? $this->check_contacts_has_email($result['fr']['contacts'], 'fr') : '';
            $result['en']['additional/contactEmail'] =  isset($result['fr']['contacts']) ? $this->check_contacts_has_email($result['en']['contacts'], 'en') : '';
            $result['fr']['contributorFullName'] = $userInfo['contributor_fullname'];
            $result['en']['contributorFullName'] = $userInfo['contributor_fullname'];
            $result['fr']['contributorAffiliation'] = $userInfo['contributor_affiliation'];
            $result['en']['contributorAffiliation'] = $userInfo['contributor_affiliation'];
        }

        return [
            'structured'   => $result,
            'to_translate' => [
                "fr" => $frNeedsTranslation,
                "en" => $enNeedsTranslation
            ],
            'status_key' => $statusKey,
            'pi_data' => [
                "first_pi_email" => $firstInvestigatorEmail,
                "first_pi_name" => $firstInvestigator ? $firstInvestigator['name'] : '',
            ],
            'contact_mail' => $firstContact ? $firstContact['email'] : '',
            'translate_enabled' => $translateEnabled
        ];
    }

    /** Vérifie si au moins un contact a une adresse e-mail valide */
    private function check_contacts_has_email(array $contacts, $lang): string
    {
        // 1) le tableau doit exister et ne pas être vide
        if (empty($contacts) || !is_array($contacts)) {
            return $lang === 'fr' ? 'Non' : 'No';
        }
        // 2) au moins un élément de type 'contact' avec un email valide
        foreach ($contacts as $item) {
            if (!empty($item['type']) && $item['type'] === 'contact' && !empty($item['email']) && filter_var($item['email'], FILTER_VALIDATE_EMAIL)) {
                return  $lang === 'fr' ? 'Oui' : 'Yes';
            }
        }

        return $lang === 'fr' ? 'Non' : 'No';
    }

    private function is_enable_translate($value): bool
    {
        $translate_enabled = false;
        // Convertir string/number en booléen
        if (is_string($value)) {
            $value = strtolower($value); // normaliser
            $translate_enabled = ($value === 'true' || $value === '1');
        } else {
            // si déjà bool ou int
            $translate_enabled = (bool) $value;
        }

        return $translate_enabled;
    }
}

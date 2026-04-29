<?php

/**
 * Ce fichier gère le téléchargement de fichiers JSON/XML,
 * le traitement logique des données et l'envoi vers l'API NADA.
 */

use Dom\XPath;

/**
 * Fonction de rappel pour gérer l'importation de fichiers.
 */
function nada_import_study_callback(): void
{
    $lang = pll_current_language() ?? 'fr';
    try {
        if (!isset($_FILES['xmlJsonFile']) || empty($_FILES['xmlJsonFile']['tmp_name'])) {
            $msgerr = __("uploadProvidFileMsg", 'nada-id');
            throw new Exception($msgerr);
        }

        $filePath = $_FILES['xmlJsonFile']['tmp_name'];

        if (!file_exists($filePath) || !is_readable($filePath)) {
            $msgerr = __("uploadReadableFileMsg", 'nada-id');
            throw new Exception($msgerr);
        }

        $originalName = $_FILES['xmlJsonFile']['name'];
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $text_extensions = ['json', 'xml'];

        if (!in_array(strtolower($ext), $text_extensions)) {
            $expected = implode(', ', $text_extensions);
            $msgerr = __("uploadAcceptExtMsg", 'nada-id');
            throw new Exception("$msgerr $expected.");
        }

        if ($ext == 'xml') {
            import_xml_study($filePath);
        } else {
            $fileContent = file_get_contents($filePath);
            save_survey($fileContent, true, null, $originalName);
        }
    } catch (Exception $e) {
        nada_pef_log('Error nada_import_study_callback: ' . $e->getMessage());
        wp_send_json([
            'success' => false,
            'message' => $lang === 'fr' ?
                'Une erreur technique est survenue. Veuillez réessayer plus tard.' :
                'A technical error occurred. Please try again later.',
            'response' => 'KO',
        ]);
    }
}

// ----------------------------------------------------------------------
// FONCTIONS UTILITAIRES DE TRAITEMENT LOGIQUE (HELPERS)
// ----------------------------------------------------------------------

/**
 * Traite et normalise les auteurs/investigateurs (AuthEnty)
 */
function process_authors($authEnty, $lang)
{
    if (empty($authEnty)) return [];

    // Si un seul auteur
    if (isset($authEnty['name'])) {
        $authEnty = [$authEnty];
    }

    $result = [];

    foreach ($authEnty as $entry) {

        $affiliation = '';
        foreach ($entry['affiliation'] ?? [] as $aff) {
            if (($aff['lang'] ?? '') === $lang) {
                $affiliation = $aff['value'] ?? '';
                break;
            }
        }
        $links = $entry['extLink'] ?? [];
        $orgLinks = [];
        foreach ($links as $link) {
            $title = $link['title'] ?? '';
            $role  = $link['role'] ?? '';
            $uri   = $link['uri'] ?? '';
            if ($role === 'organisation id') {
                $orgLinks[] = [
                    'title' => $title,
                    'uri'   => $uri,
                    'role'  => 'organisation id'
                ];
            }
        }
        if (empty($orgLinks)) {
            $orgLinks[] = [
                'title' => '',
                'uri'   => '',
                'role'  => 'organisation id'
            ];
        }
        $fullName = $entry['name'] ?? "";
        $firstname = '';
        $lastname = '';
        if (!empty($fullName) && str_contains($fullName, ';')) {
            [$firstname, $lastname] = explode(';', $fullName, 2);
        }
        $result[] = [
            "name" => $fullName,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "type" => "investigator",
            "extlink" => $orgLinks,
            "email" => $entry['email'] ?? "",
            "affiliationName" => $affiliation,
            "PILabo" => $entry['PILabo'] ?? "",
            "isContact" => $entry['isContact'] ?? ""
        ];
    }
    return $result;
}
/**
 * Traite les Other IDs (Contributeurs, Affiliations complexes...)
 */
function process_oth_ids($othIds, $lang)
{
    if (empty($othIds)) return [['name' => '']];

    $result = [];
    foreach ($othIds as $entry) {
        $type = $entry['type'] ?? '';
        if ($type === 'collaboration') {
            // collaboration multilingue
            $coll = '';
            foreach ($entry['affiliation'] ?? [] as $a) {
                if (($a['lang'] ?? '') === $lang) {
                    $coll = $a['value'];
                    break;
                }
            }
            $result[] = [
                'name' => $coll,
                'type' => $type
            ];
        } elseif ($type === 'contributor') {
            // Extraction ORCID/IdRef pour les contributeurs
            $links = [];
            foreach ($entry['extLink'] ?? [] as $l) {
                if (in_array($l['title'] ?? '', ['ORCID', 'IdRef'])) {
                    $links[] = ['title' => $l['title'], 'uri' => $l['uri'] ?? '', 'role' => 'team member id'];
                }
            }
            $result[] = [
                'name' => $entry['name'] ?? '',
                'type' => $type,
                'isContact' => $entry['isContact'] ?? '',
                'extlink' => $links
            ];
        } elseif ($type === 'affiliation') {
            // Affiliation multilingue
            $aff = '';
            foreach ($entry['affiliation'] ?? [] as $a) {
                if (($a['lang'] ?? '') === $lang) {
                    $aff = $a['value'];
                    break;
                }
            }

            // Liens externes organisation/labo
            $extLinkData = isset($entry['extLink'][0]) ? $entry['extLink'] : (isset($entry['extLink']) ? [$entry['extLink']] : []);
            // On s'assure d'avoir la structure [Organisation, Labo]
            $orgLinks = [];
            $laboLinks = [];

            foreach ($extLinkData as $ext) {
                if (($ext['role'] ?? '') === 'organisation id') {
                    $orgLinks[] = ['title' => $ext['title'] ?? '', 'uri' => $ext['uri'] ?? '', 'role' => 'organisation id'];
                }
                if (($ext['role'] ?? '') === 'labo id') {
                    $laboLinks[] = ['title' => $ext['title'] ?? '', 'uri' => $ext['uri'] ?? '', 'role' => 'labo id'];
                }
            }

            $result[] = [
                'name' => $aff,
                'teamMemberLabo' => $entry['teamMemberLabo'] ?? '',
                'type' => $type,
                'extlink' => array_merge($orgLinks, $laboLinks)
            ];
        } else {
            // Cas par défaut pour les autres types
            $result[] = ['name' => $entry['name'] ?? '', 'type' => $type];
        }
    }
    return empty($result) ? [['name' => '']] : $result;
}

/**
 * Traite les entités (Agences de financement, Producteurs)
 */
function process_entities($entries, $lang, $typeKey, $otherKey, $isProducer = false)
{
    $out = ['list' => [], 'types' => [], 'otherTypes' => []];

    // Si vide ou null, on renvoie une liste avec un élément name vide
    if (empty($entries) || !is_array($entries)) {
        $out['list'][] = ['name' => ''];
        return $out;
    }

    $hasValidEntry = false;

    foreach ($entries as $entry) {
        if (!is_array($entry)) continue;

        // Nom
        $name = '';
        if (!empty($entry['name']) && is_array($entry['name'])) {
            foreach ($entry['name'] as $n) {
                if (isset($n['lang'], $n['value']) && $n['lang'] === $lang) {
                    $name = $n['value'];
                    break;
                }
            }
        }

        $rawLinks = $entry['extLink'] ?? [];
        // Normaliser en tableau de tableaux
        if (!empty($rawLinks) && !array_is_list($rawLinks)) {
            $rawLinks = [$rawLinks];
        }

        $extlinks = [];
        foreach ($rawLinks as $l) {
            $link = [
                'title' => $l['title'] ?? '',
                'uri' => $l['uri'] ?? ''
            ];
            if ($isProducer) {
                $link['role'] = $l['role'] ?? '';
            }
            $extlinks[] = $link;
        }

        // Construction de l'élément de la liste
        $listItem = [
            'name' => $name ?: '',
        ];

        if (!empty($extlinks)) {
            $listItem['extlink'] = $extlinks;
        }

        if ($isProducer) {
            $listItem['role'] = 'sponsor';
        }

        $out['list'][] = $listItem;

        if ($name !== '') {
            $hasValidEntry = true;
        }

        // Type principal
        $tVal = '';
        if (!empty($entry[$typeKey]) && is_array($entry[$typeKey])) {
            foreach ($entry[$typeKey] as $t) {
                if (isset($t['lang'], $t['value']) && $t['lang'] === $lang) {
                    $tVal = $t['value'];
                    break;
                }
            }
        }
        $out['types'][] = $tVal;

        // Autre type
        $oVal = '';
        if (!empty($entry[$otherKey]) && is_array($entry[$otherKey])) {
            foreach ($entry[$otherKey] as $o) {
                if (isset($o['lang'], $o['value']) && $o['lang'] === $lang) {
                    $oVal = $o['value'];
                    break;
                }
            }
        }
        $out['otherTypes'][] = $oVal;
    }

    if (!$hasValidEntry) {
        $out['list'] = [['name' => '']];
        $out['types'] = [];
        $out['otherTypes'] = [];
    }

    return $out;
}

/**
 * Traite les contacts dans DistStmt
 */
function process_dist_contacts($contacts, $lang)
{
    $res = [];
    if (empty($contacts)) return [['name' => '', 'type' => 'contact']];

    foreach ($contacts as $c) {
        $aff = '';
        foreach ($c['affiliation'] ?? [] as $a) {
            if (($a['lang'] ?? '') === $lang) $aff = $a['value'];
        }

        $ext = $c['extLink'] ?? [];

        $fullName = $c['name'] ?? "";
        $firstname = '';
        $lastname = '';
        if (!empty($fullName) && str_contains($fullName, ';')) {
            [$firstname, $lastname] = explode(';', $fullName, 2);
        }
        $res[] = [
            'name' => $fullName,
            'lastname' => $lastname,
            'firstname' => $firstname,
            'type' => 'contact',
            'email' => $c['email'] ?? '',
            'affiliationName' => $aff,
            'contactPointLabo' => $c['contactPointLabo'] ?? '',
            'extlink' => $ext
        ];
    }
    return $res;
}

/**
 * Traite les contacts dans UseStmt
 */
function process_use_contacts($langData)
{
    $res = [];
    $basePath = 'stdyDscr/dataAccs/useStmt/contact';

    $i = 0;
    while (true) {
        $nk = "$basePath/$i/name";
        $ek = "$basePath/$i/email";

        if (!isset($langData[$nk]) && !isset($langData[$ek])) break;

        $res[] = [
            'name' => $langData[$nk] ?? '',
            'email' => $langData[$ek] ?? '',
            'type' => 'contact'
        ];
        $i++;
    }

    // Si vide, renvoyer un tableau avec un élément vide selon besoin NADA
    if (empty($res)) $res[] = ['name' => ''];

    return $res;
}

/**
 * Retourne une valeur par défaut pour les filtres à facettes si vide
 */
function defaultFacet($val, $lang)
{
    if (!empty($val)) return $val;
    return $lang === 'fr' ? 'Non renseigné' : 'Not specified';
}

function defaultFacetSourceOrigin($val, $lang)
{
    if (!empty($val)) return $val;
    return $lang === 'fr' ? 'Non applicable' : 'Not applicable';
}

function defaultFacetHealthTheme($val, $lang)
{
    if (!empty($val)) return $val;
    return $lang === 'fr' ? 'Pas de spécialité médicale spécifique' : 'No specific medical speciality';
}

/**
 * Sépare les données JSON par langue (fr/en)
 * @param array $jsonData Données JSON brutes
 * @return array Tableau avec clés 'fr' et 'en' contenant les données respectives
 */
function splitJsonByLang(array $jsonData): array
{
    $result = ['fr' => [], 'en' => []];
    foreach (['fr', 'en'] as $lang) {
        $result[$lang]['study_authorization'] = ['agency' => []];
        $result[$lang]['additional/topicsHealthTheme'] = [];
        $result[$lang]['additional/topicsHealthDeterminant'] = [];
        $result[$lang]['additional/sourceOrigine/values'] = [];
        $result[$lang]['stdyDscr/studyDevelopment/developmentActivity'] = [];
    }
    // récuperation du isHealthTheme variable
    $isHealthTheme = $jsonData['additional']['isHealthTheme'] ?? '';
    // Normalisation en booléen
    $isHealthThemeEnabled = in_array(strtolower($isHealthTheme), ['true', 'oui', 'yes', '1']);
    $process = function ($data, string $path = '') use (&$process, &$result, $isHealthThemeEnabled) {
        $endDate = '3000-01-01';
        // --- Cas 1: Valeur scalaire (Fin de branche) ---
        if (!is_array($data)) {
            $result['fr'][$path] = $data;
            $result['en'][$path] = $data;
            return;
        }

        // --- Détection de liste (Tableau indexé numériquement) ---
        $keys = array_keys($data);
        $isList = $keys === range(0, count($data) - 1) || $keys === array_map('strval', range(0, count($data) - 1));

        if (str_ends_with($path, '/sumDscr/collDate') || str_contains($path, '/sumDscr/collDate')) {
            // Sous-cas A : Objet unique (associatif avec clés 'event' et 'value')
            if (isset($data['event']) && isset($data['value'])) {
                $event = strtolower($data['event']);
                $keyMap = ['start' => 'event_start', 'end' => 'event_end'];

                if (isset($keyMap[$event])) {
                    $finalKey = "stdyDscr/stdyInfo/sumDscr/collDate/{$keyMap[$event]}";
                    $result['fr'][$finalKey] = $data['value'];
                    $result['en'][$finalKey] = $data['value'];
                }
                $startKey = "stdyDscr/stdyInfo/sumDscr/collDate/event_start";
                $endKey = "stdyDscr/stdyInfo/sumDscr/collDate/event_end";

                $hasStartDate = isset($result['fr'][$startKey]) && !empty($result['fr'][$startKey]);
                $hasEndDate = isset($result['fr'][$endKey]) && !empty($result['fr'][$endKey]);
                // On met la date par défaut seulement si start existe et end n'existe pas
                if ($hasStartDate && !$hasEndDate) {
                    $result['fr'][$endKey] = $endDate;
                    $result['en'][$endKey] = $endDate;
                }
                return;
            }

            // Sous-cas B : Liste d'objets
            if ($isList) {
                foreach ($data as $dateItem) {
                    if (!isset($dateItem['event']) || !isset($dateItem['value'])) continue;

                    $event = strtolower($dateItem['event']);
                    $keyMap = ['start' => 'event_start', 'end' => 'event_end'];

                    if (isset($keyMap[$event])) {
                        $finalKey = "stdyDscr/stdyInfo/sumDscr/collDate/{$keyMap[$event]}";
                        $result['fr'][$finalKey] = $dateItem['value'];
                        $result['en'][$finalKey] = $dateItem['value'];
                    }
                }
                $startKey = "stdyDscr/stdyInfo/sumDscr/collDate/event_start";
                $endKey = "stdyDscr/stdyInfo/sumDscr/collDate/event_end";
                $hasStartDate = isset($result['fr'][$startKey]) && !empty($result['fr'][$startKey]);
                $hasEndDate = isset($result['fr'][$endKey]) && !empty($result['fr'][$endKey]);
                // On met la date par défaut seulement si start existe et end n'existe pas
                if ($hasStartDate && !$hasEndDate) {
                    $result['fr'][$endKey] = $endDate;
                    $result['en'][$endKey] = $endDate;
                }

                return;
            }
        }
        // --- Cas Spécial : IDNo (Liste sans 'lang') ---
        if ($isList && str_contains($path, 'IDNo') && !isset($data[0]['lang'])) {
            $idnos = [];
            foreach ($data as $item) {
                if (is_array($item)) {
                    $idnos[] = [
                        'agency' => $item['agency'] ?? '',
                        'code' => $item['value'] ?? ''
                    ];
                }
            }
            $result['fr']['stdyDscr/titlStmt/idnos'] = $idnos;
            $result['en']['stdyDscr/titlStmt/idnos'] = $idnos;
            return;
        }

        // --- Traitement des listes multilingues (avec clé 'lang') ---
        if ($isList && (isset($data[0]['lang']))) {
            foreach ($data as $item) {
                $lang = $item['lang'] ?? null;
                if (!$lang || !isset($result[$lang])) continue;

                // 1. Universe (Population)
                if (str_contains($path, 'universe')) {
                    $level = $item['level'] ?? null;
                    $clusion = $item['clusion'] ?? null;
                    $value = $item['value'] ?? null;

                    if ($clusion) {
                        $clusionKey = $clusion === 'I' ? 'clusion_I' : ($clusion === 'E' ? 'clusion_E' : $clusion);

                        // Structure avec Concept
                        $dataWithConcept = $value;
                        if (isset($item['concept']['vocab'], $item['concept']['vocabURI'])) {
                            $dataWithConcept = [
                                'value' => $value,
                                'concept' => $item['concept']
                            ];
                        }

                        if ($level) {
                            $key = "level_{$level}_clusion_{$clusion}";
                            if ($level === 'type') {
                                $result[$lang][$key] = $value; // Liste simple pour type
                            } else {
                                $result[$lang][$key][] = $dataWithConcept; // Liste avec concept
                            }
                            // Facette (filtre) : Valeur brute uniquement
                            $result[$lang][$key . '_filter'][] = $value;
                        } else {
                            $result[$lang][$clusionKey] = $value;
                        }
                    }
                    continue;
                }

                if (str_contains($path, 'studyDevelopment')) {
                    $langGroups = [];
                    foreach ($data as $item) {
                        $lang = $item['lang'] ?? null;
                        if (!$lang || !isset($result[$lang])) {
                            continue;
                        }
                        if (!isset($langGroups[$lang])) {
                            $langGroups[$lang] = [];
                        }
                        $langGroups[$lang][] = $item;
                    }

                    foreach ($langGroups as $lang => $items) {
                        if (!isset($result[$lang][$path])) {
                            $result[$lang][$path] = [];
                        }

                        $typeGroups = [];
                        foreach ($items as $item) {
                            $type = isset($item['type']) ? trim($item['type']) : '';
                            $description = isset($item['description']) ? trim($item['description']) : '';
                            if (empty($type) || empty($description)) {
                                continue;
                            }
                            if (!isset($typeGroups[$type])) {
                                $typeGroups[$type] = [];
                            }
                            $typeGroups[$type][] = $description;
                        }

                        // This loop will now correctly run for "primary evaluation" AND "secondary evaluation"
                        foreach ($typeGroups as $type => $descriptions) {
                            $result[$lang][$path][] = [
                                'activity_type' => $type,
                                'activity_description' => implode("\r\n", $descriptions)
                            ];
                        }
                    }
                    return;
                }

                if (str_contains($path, 'nation')) {
                    if (!isset($result[$lang][$path]) || !is_array($result[$lang][$path])) {
                        $result[$lang][$path] = [];
                    }

                    if (is_array($item)) {
                        $nation = [
                            'name' => $item['value'] ?? ($item['name'] ?? ''),
                            'abbreviation' => $item['abbr'] ?? ''
                        ];

                        // Simplified from NEW
                        if (isset($item['concept']['vocab'], $item['concept']['vocabURI'])) {
                            $nation['extLink'] = $item['concept'];
                        }
                    } else {
                        $nation = [
                            'name' => (string)$item,
                            'abbreviation' => ''
                        ];
                    }

                    $result[$lang][$path][] = $nation;
                    continue;
                }

                // 4. Topics
                if (str_contains($path, 'topcClas')) {
                    if (!isset($result[$lang][$path]) || !is_array($result[$lang][$path])) {
                        $result[$lang][$path] = [];
                    }

                    $vocab = isset($item['vocab']) ? trim((string)$item['vocab']) : '';
                    $val = isset($item['value']) ? trim((string)$item['value']) : '';

                    $topic = [
                        'topic' => $val,
                        'vocab' => $vocab,
                    ];
                    if ($vocab === 'health theme') {
                        if ($isHealthThemeEnabled) {
                            $emptyExtlink = [
                                ["title" => "ESV", "uri" => ""],
                                ["title" => "MeSH", "uri" => ""]
                            ];
                            $topic['extLink'] = isset($item['extLink']) ? $item['extLink'] : $emptyExtlink;
                            $result[$lang]['additional/topicsHealthTheme'][] = $val;
                        } else {
                            // Ignorer health theme si isHealthThemeEnabled est faux
                            continue;
                        }
                    } elseif ($vocab === 'health determinant') {
                        $result[$lang]['additional/topicsHealthDeterminant'][] = $val;
                    } elseif ($vocab === 'cim-11') {
                        $emptyExtlink = [
                            "title" => "",
                            "uri" => ""
                        ];

                        $topic['extLink'] = isset($item['extLink'])
                            ? $item['extLink']
                            : $emptyExtlink;
                    }

                    $result[$lang][$path][] = $topic;
                    continue;
                }

                if (str_contains($path, 'othId')) {
                    $othIdRaw = $item ?? [];
                    if (!is_array($othIdRaw) || isset($othIdRaw['value'])) {
                        $othIdRaw = [$othIdRaw];
                    }

                    $result[$lang][$path] = array_map(function ($item) {
                        return [
                            'name' => '',
                            'type' => $item['type'] ?? '',
                        ];
                    }, $othIdRaw);

                    $names = array_filter(array_map(function ($it) {
                        return is_array($it) ? trim($it['value'] ?? '') : (is_string($it) ? trim($it) : '');
                    }, $othIdRaw));

                    if (count($names) > 0) {
                        $result[$lang]['additional/collaborations/networkConsortium'] = true;
                    }
                    continue;
                }

                if (str_contains($path, 'method/notes')) {
                    $subject = $item['subject'] ?? null;
                    $value = $item['value'] ?? null;

                    if ($subject && $value) {
                        // Initialize notes array if not exists
                        if (!isset($result[$lang][$path])) {
                            $result[$lang][$path] = [];
                        }

                        // Check if subject already exists
                        $subjectIndex = null;
                        foreach ($result[$lang][$path] as $index => $note) {
                            if (isset($note['subject']) && $note['subject'] === $subject) {
                                $subjectIndex = $index;
                                break;
                            }
                        }

                        // If subject exists, add value
                        if ($subjectIndex !== null) {
                            $result[$lang][$path][$subjectIndex]['values'][] = $value;
                        } else {
                            // Otherwise, create new entry
                            $result[$lang][$path][] = [
                                'subject' => $subject,
                                'values' => [$value]
                            ];
                        }

                        if ($subject === 'observational study method') {
                            $result[$lang]['additional/observationalStudy'] = $value ?? '';
                        }
                        if ($subject === 'research type') {
                            $result[$lang]['stdyDscr/method/notes/subject_researchType'] = $value ?? '';
                        }
                    }

                    continue;
                }

                if (str_contains($path, 'stdyInfo/abstract')) {
                    $type = $item['contentType'] ?? null;
                    $value = $item['value'] ?? '';

                    if (!$lang || !$type || !isset($result[$lang])) {
                        continue;
                    }

                    if (!isset($result[$lang]["$path/$type"])) {
                        $result[$lang]["$path/$type"] = [];
                    }

                    $result[$lang]["$path/$type"][] = $value;
                    continue;
                }

                // 8. CollMode / SampProc
                if ((str_contains($path, 'collMode')) || (str_contains($path, 'sampProc'))) {
                    if (!isset($result[$lang][$path]) || !is_array($result[$lang][$path])) {
                        $result[$lang][$path] = [];
                    }
                    $concept = [];

                    if (isset($item['concept']) && is_array($item['concept']) && !empty($item['concept'])) {
                        $concept = $item['concept'];
                    } else {
                        $concept = [
                            "vocab" => "",
                            "vocabURI" => ""
                        ];
                    }

                    $value = trim((string)($item['value'] ?? ($concept["value"] ?? "")));

                    $outObj = [
                        'concept' => $concept,
                        'value' => $value
                    ];
                    $result[$lang][$path][] =
                        json_encode($outObj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    continue;
                }

                // 9. AvlStatus
                if (str_contains($path, 'setAvail/avlStatus')) {
                    if (!isset($result[$lang][$path]) || !is_array($result[$lang][$path])) {
                        $result[$lang][$path] = [];
                    }

                    $value = $item['value'] ?? '';
                    $extLink = $item['extLink'] ?? [];

                    $out = [
                        'value' => $value,
                        'extLink' => $extLink
                    ];

                    $result[$lang]['additional/avlStatus'] = $value;

                    $result[$lang][$path] =
                        json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    continue;
                }

                // 10. FileDscr
                if (str_contains($path, 'fileDscr/fileTxt/fileCitation/titlStmt/IDno')) {
                    if (!is_array($item)) continue;

                    $obj = [
                        'type' => $item['type'] ?? '',
                        'uri' => $item['uri'] ?? ''
                    ];

                    $result[$lang][$path][] = $obj;
                    continue;
                }

                // Default: simple value
                $value = isset($item['value']) ? $item['value'] : '';
                $result[$lang][$path][] = $value;
            }
            return;
        }

        // --- Cas 2: Objet unique avec 'lang' (et 'value') ---
        if (!$isList && isset($data['lang']) && isset($data['value'])) {
            $lang = $data['lang'];
            $item = $data['value'];
            if (isset($result[$lang])) {
                $result[$lang][$path] = $item;
            }

            // Gestion Authorizing Agency
            if (str_contains($path, 'stdyDscr/studyAuthorization')) {
                if ($lang === 'fr') {
                    if (str_starts_with($item, 'Autre')) {
                        $name = 'Autre';
                    } else {
                        $name = $item;
                    }
                } else { // en
                    if (str_starts_with($item, 'Other')) {
                        $name = 'Other';
                    } else {
                        $name = $item;
                    }
                }

                $result[$lang]['study_authorization']['agency'][] = [
                    'name' => $name
                ];

                $cleanFr = $cleanEn = "";

                if ($lang === 'fr' && str_starts_with($item, 'Autre :')) {
                    $cleanFr = trim(substr($item, strlen("Autre :")));
                }

                if ($lang === 'en' && str_starts_with($item, 'Other :')) {
                    $cleanEn = trim(substr($item, strlen("Other :")));
                }

                if ($lang === 'fr') {
                    $result['fr']['additional/obtainedAuthorization/otherAuthorizingAgency'][] = $cleanFr;
                }

                if ($lang === 'en') {
                    $result['en']['additional/obtainedAuthorization/otherAuthorizingAgency'][] = $cleanEn;
                }

                return;
            }

            // Gestion othId
            if (str_contains($path, 'othId')) {
                $result[$lang][$path] =
                    [
                        [
                            'name' => '',
                            'type' => $data['type'] ?? '',
                        ]
                    ];

                $val = is_string($data['value']) ? trim($data['value']) : '';
                if ($val !== '') {
                    $result[$lang]['additional/collaborations/networkConsortium'] = true;
                }

                return;
            }
            return;
        }

        // --- Cas 3: Listes spécifiques sans 'lang' interne ---

        // Related Documents
        if (str_contains($path, 'relatedDocuments')) {
            // version liste ou unique si non liste on met en tableau
            $documents = $isList ? $data : [$data];
            foreach ($documents as $document) {
                if (!is_array($document)) continue;

                foreach (['fr', 'en'] as $lang) {
                    if (!isset($result[$lang]['additional/relatedDocument'])) {
                        $result[$lang]['additional/relatedDocument'] = [];
                    }

                    $doc = [
                        'type' => '',
                        'title' => '',
                        'link' => ''
                    ];

                    // Mapping des champs
                    $fieldMapping = [
                        'documentType' => 'type',
                        'documentTitle' => 'title',
                        'documentLink' => 'link'
                    ];

                    foreach ($fieldMapping as $field => $key) {
                        if (isset($document[$field])) {
                            // Si c'est un tableau avec lang
                            if (is_array($document[$field])) {
                                foreach ($document[$field] as $item) {
                                    if (
                                        is_array($item) &&
                                        isset($item['lang']) &&
                                        $item['lang'] === $lang
                                    ) {
                                        // Récupérer la valeur (peut être vide)
                                        $doc[$key] = isset($item['value']) ? trim($item['value']) : '';
                                        break;
                                    }
                                }
                            } // Si c'est une valeur directe (string)
                            else {
                                $doc[$key] = trim($document[$field]);
                            }
                        }
                    }
                    $result[$lang]['additional/relatedDocument'][] = $doc;
                }
            }

            return;
        }

        // SpecPerm
        if (str_contains($path, 'stdyDscr/dataAccs/useStmt/specPerm') && $isList) {
            foreach ($data as $spec) {
                $required = $spec['required'] ?? '';
                $values = $spec['values'] ?? [];

                foreach (['fr', 'en'] as $lang) {
                    $txt = '';
                    foreach ($values as $v) {
                        if (($v['lang'] ?? '') === $lang) {
                            $txt = trim($v['value'] ?? '');
                            break;
                        }
                    }
                    $result[$lang]['stdyDscr/dataAccs/useStmt/specPerm'] = $txt;
                    $result[$lang]['stdyDscr/dataAccs/useStmt/specPerm/required'] = $required;
                }
            }

            return;
        }

        // Sources
        if (str_contains($path, 'sources') && $isList) {
            $langs = ['fr', 'en'];
            $otherSourceTypes = [];
            $uniqueSrcOrig = [];

            foreach ($langs as $lang) {
                $result[$lang][$path] = [];
                $otherSourceTypes[$lang] = [];
                $uniqueSrcOrig[$lang] = [];
            }

            $baseDdiObj = [
                'sourceCitation' => [
                    'titlStmt' => ['titl' => ''],
                    'holdings' => '',
                    'notes' => [
                        'subject' => 'source purpose',
                        'value' => ''
                    ]
                ],
                'srcOrig' => []
            ];

            foreach ($data as $source) {
                if (!is_array($source)) continue;
                $objs = ['fr' => $baseDdiObj, 'en' => $baseDdiObj];
                $currentOther = ['fr' => '', 'en' => ''];
                foreach ($source as $field => $items) {
                    if (!is_array($items)) continue;
                    foreach ($items as $item) {
                        $val = trim($item['value'] ?? '');
                        if ($val === '') continue;

                        $lang = $item['lang'] ?? '';
                        if (!isset($objs[$lang])) continue;
                        if ($field == 'sourceCitation') {
                            $objs[$lang]['sourceCitation']['titlStmt']['titl'] = $val;
                        } elseif ($field == 'holdings') {
                            $objs[$lang]['sourceCitation']['holdings'] = $val;
                        } elseif ($field == 'notes') {
                            $objs[$lang]['sourceCitation']['notes']['value'] = $val;
                        } elseif ($field == 'srcOrig') {
                            $objs[$lang]['srcOrig'][] = $val;
                            $uniqueSrcOrig[$lang][] = $val;
                        } elseif ($field == 'otherSourceType') {
                            $currentOther[$lang] = $val;
                        }
                    }
                }

                foreach ($langs as $lang) {
                    $isEmpty = empty($objs[$lang]['sourceCitation']['titlStmt']['titl'])
                        && empty($objs[$lang]['sourceCitation']['holdings'])
                        && empty($objs[$lang]['sourceCitation']['notes']['value'])
                        && empty($objs[$lang]['srcOrig']);

                    if (!$isEmpty) {
                        $result[$lang][$path][] = $objs[$lang];
                        $otherSourceTypes[$lang][] = $currentOther[$lang];
                    }
                }
            }
            foreach ($langs as $lang) {
                $result[$lang]['additional/sourceOrigine/values'] = array_values(array_unique($uniqueSrcOrig[$lang]));
                $result[$lang]['additional/thirdPartySource/otherSourceType'] = $otherSourceTypes[$lang];
            }
            return;
        }

        // Listes simples multilingues
        if ((str_contains($path, 'inclusionGroups') || str_contains($path, 'arms') || (str_contains($path, 'intervention'))) && $isList) {
            $result['fr'][$path] = [];
            $result['en'][$path] = [];

            foreach ($data as $group) {
                if (!is_array($group)) continue;

                $objFr = [];
                $objEn = [];

                foreach ($group as $field => $items) {
                    if (!is_array($items)) continue;

                    $fr = '';
                    $en = '';

                    foreach ($items as $item) {
                        $val = trim($item['value'] ?? '');
                        if ($val === '') continue;

                        if (($item['lang'] ?? '') === 'fr') {
                            $fr = $val;
                        }
                        if (($item['lang'] ?? '') === 'en') {
                            $en = $val;
                        }
                    }

                    $objFr[$field] = $fr;
                    $objEn[$field] = $en;
                }

                $result['fr'][$path][] = $objFr;
                $result['en'][$path][] = $objEn;
            }

            return;
        }
        foreach ($data as $key => $value) {
            $currentPath = $path ? "$path/$key" : $key;
            $process($value, $currentPath);
        }
    };

    $process($jsonData);
    return $result;
}

function import_xml_study($filePath): void
{
    $lang = pll_current_language() ?? 'fr';
    if (!file_exists($filePath) || !is_readable($filePath)) {
        nada_pef_log("Fichier XML introuvable ou non lisible: {$filePath}", 'error');
        wp_send_json([
            'success' => false,
            'message' => $lang === "fr" ? "Fichier introuvable ou non lisible." : "File not found or unreadable."
        ]);
        return;
    }

    libxml_use_internal_errors(true);

    try {
        // Charger le XML
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;


        if (!$dom->load($filePath)) {
            $errs = libxml_get_errors();
            libxml_clear_errors();
            throw new Exception("Impossible de charger le XML source. Erreurs: " . json_encode($errs));
        }
        $study_type = extract_study_type_from_xml($dom);
        // Récupérer la langue originale si présente (xml:lang), sinon fallback sur 'fr'
        $root = $dom->documentElement;
        $originalLang = $root->getAttributeNS('http://www.w3.org/XML/1998/namespace', 'lang');
        if (empty($originalLang)) {
            $originalLang = 'fr';
        }

        // Préparer l'API et les variables de résultat
        $nada_api = get_nada_api_instance();
        $newId = getSurveyNewId();
        $languages = ['fr', 'en'];
        $responses = [];
        $resultMultiplePEF = [];

        // Boucle par langue : clone du DOM, modifs, sauvegarde temporaire, import
        foreach ($languages as $lang) {
            $tempFile = null;
            try {
                // Nouvel idno pour la langue
                $newIdno = "FReSH_PEF-{$newId}-{$lang}";

                // Cloner le DOM pour ne pas altérer l'original
                $modifiedDom = clone $dom;
                $modifiedRoot = $modifiedDom->documentElement;
                $ddiNs = $root->namespaceURI;

                // XPath pour manipuler les noeuds en namespace DDI
                $xpath = new DOMXPath($modifiedDom);
                // enregistrer le namespace (valeur conservée telle quelle)
                $xpath->registerNamespace('ddi', 'ddi:codebook:2_5');

                // Mettre à jour l'attribut xml:lang sur la racine
                $modifiedRoot->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:lang', $lang);

                // Mettre à jour l'attribut ID du codeBook (racine)
                $modifiedRoot->setAttribute('ID', $newIdno);


                // Ensure stdyDscr IDNo exists
                $stdyIdNo = $xpath->query(
                    '//ddi:stdyDscr/ddi:citation/ddi:titlStmt/ddi:IDNo'
                );

                if ($stdyIdNo && $stdyIdNo->length > 0) {
                    $stdyNode = $stdyIdNo->item(0);
                    $stdyNode->nodeValue = $newIdno;
                } else {
                    $titlStmt = $xpath->query(
                        '//ddi:stdyDscr/ddi:citation/ddi:titlStmt'
                    )->item(0);

                    if (!$titlStmt) {
                        throw new Exception('titlStmt introuvable dans le XML.');
                    }

                    $stdyNode = $modifiedDom->createElementNS(
                        $ddiNs,
                        'ddi:IDNo',
                        $newIdno
                    );
                    $titlStmt->appendChild($stdyNode);
                }

                // Remove ALL other IDNo
                $allIdNos = $xpath->query('//ddi:IDNo');
                foreach ($allIdNos as $node) {
                    if ($node !== $stdyNode) {
                        $node->parentNode->removeChild($node);
                    }
                }

                // Sauvegarder le DOM modifié dans un fichier temporaire
                $tempFile = tempnam(sys_get_temp_dir(), 'xml_');
                if ($tempFile === false) {
                    throw new Exception("Impossible de créer un fichier temporaire.");
                }

                // Sauvegarder le DOM modifié
                $saveOk = $modifiedDom->save($tempFile);
                if ($saveOk === false) {
                    throw new Exception("Impossible d'enregistrer le XML modifié dans {$tempFile}");
                }

                // Préparer les paramètres pour l'import NADA
                $params = [
                    'file_path' => $tempFile,
                    'repositoryid' => 'FReSH',
                    'type' => 'survey',
                    'overwrite' => 'no',
                    'published' => '0'
                ];

                // Appel API NADA
                $response = $nada_api->import_ddi($params);
                // Nettoyage du fichier temporaire
                if (empty($response['success']) || $response['success'] != 1 || ($response['data']['status'] ?? '') != 'success') {
                    $errMsg = $response['data']['message'] ?? 'Erreur inconnue lors de l\'API NADA';
                    nada_pef_log("Erreur API NADA pour langue {$lang} : {$errMsg}", 'error');
                    throw new Exception($errMsg);
                }

                // Succès : stocker la réponse
                $responses[$lang] = $response;
                $resultMultiplePEF[] = $response['data']['survey']['idno'] ?? ($response['data']['dataset']['idno'] ?? $newIdno);
            } catch (\Throwable $e) {
                // Log d'erreur par langue et on continue (ne pas interrompre la boucle globale)
                nada_pef_log("Erreur pour la langue {$lang} : " . $e->getMessage(), 'error');
            } finally {
                // Toujours supprimer le fichier temporaire s'il existe
                if (!empty($tempFile) && file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }
        }

        // Vérifier les réponses : au moins une réussite requise
        if (empty($responses)) {
            nada_pef_log("Aucune étude n'a pu être créée pour aucune langue", 'error');
            wp_send_json([
                'success' => false,
                'message' => "Aucune étude n'a pu être créée pour aucune langue. Voir logs."
            ]);
            return;
        }

        // Insérer en base WP
        try {
            $idno_fr = $responses['fr']['data']['survey']['idno'] ?? null;
            $sid_fr = $responses['fr']['data']['survey']['sid'];
            $sid_en = $responses['en']['data']['survey']['sid'];

            if ($idno_fr && $sid_fr) {
                add_study_to_wp($idno_fr, $sid_fr, $sid_en, '', ' ', $filePath, $study_type, ""); //TODO recuperer le pi a partir fichier xml
            } else {
                nada_pef_log("Données manquantes pour insertion WP : idno_fr={$idno_fr} sid_fr={$sid_fr} sid_en={$sid_en}", 'warning');
            }
        } catch (\Throwable $e) {
            nada_pef_log("Erreur insertion WP : " . $e->getMessage(), 'error');
        }

        // Réponse AJAX finale
        wp_send_json([
            'success' => true,
            'message' => $lang === 'fr' ? 'Import XML terminé' : 'XML import complete',
            'responses' => $responses,
            'studies' => $resultMultiplePEF
        ]);
    } catch (\Exception $e) {
        nada_pef_log('Erreur import_xml_study : ' . $e->getMessage(), 'error');
        wp_send_json([
            'success' => false,
            'message' => $lang === 'fr' ?
                'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                'A technical error occurred while generating the view. Please try again later or contact support.',
            'response' => 'KO',
        ]);
        return;
    }
}

/**
 * Extraire le study_type depuis le XML
 *
 * @param DOMDocument $dom
 * @return string
 */
function extract_study_type_from_xml($dom)
{
    try {
        $notesList = $dom->getElementsByTagName('notes');

        /** @var DOMElement $note */
        foreach ($notesList as $note) {
            if ($note instanceof DOMElement) {
                $subject = $note->getAttribute('subject');

                if ($subject === 'research type') {
                    $value = trim($note->nodeValue);
                    return trim($value);
                }
            }
        }

        return '';
    } catch (Exception $e) {
        nada_pef_log('Erreur extraction study_type : ' . $e->getMessage(), 'error');
        return '';
    }
}

// Fonction utilitaire
function get_lang_value($data): string
{
    if ($data === null) return '';
    if (is_array($data)) return !empty($data[0]) ? (string)$data[0] : '';
    return (string)$data;
}

function cleanUnicodeSequences(string $text): string
{
    $decoded = json_decode('"' . $text . '"');
    return $decoded !== null ? $decoded : $text;
}

// Fonction utilitaire pour logger
function nada_pef_log($message, $type = 'info')
{
    $log_dir = WP_PLUGIN_DIR . '/nada-id/logs-PEFF';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $filename = $type === 'error' ? 'error.log' : 'success.log';
    $filepath = $log_dir . '/' . $filename;

    $time = date('Y-m-d H:i:s');
    file_put_contents($filepath, "[$time] $message\n", FILE_APPEND);
}

// Fonction pour vider les logs avant chaque lancement
function nada_clear_logs()
{
    $log_dir = WP_PLUGIN_DIR . '/nada-id/logs-PEFF';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $files = ['success.log', 'error.log', 'import.log'];
    foreach ($files as $file) {
        $filepath = $log_dir . '/' . $file;
        if (file_exists($filepath)) {
            file_put_contents($filepath, ""); // vide le fichier
        }
    }
}

/* Fonction pour ajouter une étude dans la base WordPress */
function add_study_to_wp($idno, $nada_study_id_fr, $nada_study_id_en, $nada_study_fr, $nada_study_en, $filename, $study_type, $pi_email): void
{

    global $wpdb;
    $table_name = $wpdb->prefix . "nada_list_studies";
    $current_user_id = get_current_user_id();

    // Sécuriser get_base_idno
    if (!$idno) {
        throw new Exception("Impossible de récupérer l’idno (fichier : $filename)");
    }

    $base_idno = get_base_idno($idno);

    if (!$base_idno) {
        throw new Exception("get_base_idno() a retourné une valeur vide (fichier : $filename)");
    }

    try {
        if ($idno) {
            $base_idno = get_base_idno($idno);
        } else {
            throw new Exception("Impossible de récupérer idno pour le fichier $filename");
        }
    } catch (\Throwable $e) {
        nada_pef_log("Erreur get_base_idno pour le fichier $filename : " . $e->getMessage(), 'error');
    }


    $insert_data = [
        'nada_study_idno' => $base_idno,
        'nada_study_id_fr' => $nada_study_id_fr,
        'nada_study_id_en' => $nada_study_id_en,
        'status' => 'imported',
        'study_type' => sanitize_text_field($study_type),
        'created_by' => $current_user_id,
        'created_at' => current_time('mysql'),
        'nada_study_title_fr' => $nada_study_fr,
        'nada_study_title_en' => $nada_study_en,
        'PEFF_filename' => $filename,
        'pi_email' => $pi_email,
        'added_by' => $current_user_id,
    ];

    $result = $wpdb->insert($table_name, $insert_data);

    if ($result === false) {
        throw new Exception('Erreur lors de la création dans WP');
    }
}

/** Expose les fichiers du dossier PEF via l'API REST Wordpress */
add_action('rest_api_init', function () {
    register_rest_route('nada/v1', '/pef-files(?:/(?P<filename>[^/]+))?', [
        'methods' => 'GET',
        'callback' => 'nada_get_pef_file_content',
        'permission_callback' => '__return_true', // accès public
    ]);
});

add_action('rest_api_init', function () {
    register_rest_route('nada/v1', '/delete', [
        'methods' => 'DELETE',
        'callback' => 'nada_delete_all_peff_files',
        'permission_callback' => '__return_true', // accès public
    ]);
});


/**
 * Callback pour lire tous les fichiers et continuer malgré les erreurs
 */
function nada_get_pef_file_content(WP_REST_Request $request)
{
    // Vider les logs au début
    nada_clear_logs();

    $nada_token = authentificate($request);

    $dir = WP_PLUGIN_DIR . '/nada-id/public/PEF/files';
    $filename = $request->get_param('filename');
    $results = [];
    $errors = [];

    if (!is_dir($dir)) {
        $errorMsg = 'Le dossier PEF est introuvable';
        nada_pef_log($errorMsg, 'error');
        return new WP_Error('no_dir', $errorMsg, ['status' => 404]);
    }

    if (empty($filename)) {
        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;

            if (!is_file($path)) continue;

            try {
                $fileContent = nada_read_file($path, $file);

                if (!$fileContent) {
                    $msg = "Le fichier $file est vide ou illisible";
                    $errors[] = $msg;
                    nada_pef_log($msg, 'error');
                    continue;
                }

                $res = save_survey($fileContent, false, $nada_token, $file);
                $results[] = $res;
                nada_pef_log("Fichier $file traité avec succès : " . json_encode($res));
            } catch (\Exception $e) {
                $msg = "Erreur avec le fichier $file : " . $e->getMessage();
                $errors[] = $msg;
                nada_pef_log($msg, 'error');
                continue; // continuer malgré l'erreur
            }
        }
    } else {
        // Lire un seul fichier
        $filename = sanitize_file_name($filename);
        $path = $dir . '/' . $filename;
        if (!file_exists($path)) {
            $msg = "Fichier introuvable: $filename";
            nada_pef_log($msg, 'error');
            return new WP_Error('no_file', $msg, ['status' => 404]);
        }

        try {
            $fileContent = nada_read_file($path, $filename);
            if ($fileContent) {
                $res = save_survey($fileContent, false, $nada_token, $filename);
                $results[] = $res;
                nada_pef_log("Fichier $filename traité avec succès : " . json_encode($res));
            }
        } catch (\Exception $e) {
            $msg = "Erreur avec le fichier $filename : " . $e->getMessage();
            $errors[] = $msg;
            nada_pef_log($msg, 'error');
        }
    }

    wp_send_json([
        'success' => true,
        'message' => 'Parcours terminé, certaines erreurs peuvent être présentes',
        'response' => $results,
        'errors' => $errors
    ]);
}

/**
 * Fonction utilitaire pour lire un fichier (texte ou base64)
 */
function nada_read_file($path, $originalName)
{
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    $text_extensions = ['json', 'xml'];
    $content = null;
    if (in_array(strtolower($ext), $text_extensions)) {
        $content = file_get_contents($path);
    }
    return $content;
}

/* Fonction d'authentification et récupération du token NADA */
function authentificate($request)
{
    $username = null;
    $password = null;
    $body = $request->get_json_params();
    if (empty($body)) {
        // fallback to form-data / params
        $body = $request->get_params();
    }
    if (!empty($body['username'])) {
        $username = $body['username'];
    }
    if (!empty($body['password'])) {
        $password = $body['password'];
    }

    if (empty($username) || empty($password)) {
        wp_send_json([
            'success' => false,
            'status' => 400,
            'message' => 'Username et password requis !',
        ]);
    }

    $user = wp_authenticate(sanitize_user($username), $password);
    if (is_wp_error($user)) {
        wp_send_json([
            'success' => false,
            'status' => 401,
            'message' => 'Authentification échouée !',
        ]);
    }
    return get_user_meta($user->ID, 'nada_token', true);
}

function nada_delete_all_peff_files(WP_REST_Request $request)
{
    // Initialisation API
    return [
        'success' => true,
        'message' => 'Delete action executed'
    ];
}

function validate_required_fields($jsonData)
{
    $validation = [
        'fr' => ['valid' => true, 'missing' => []],
        'en' => ['valid' => true, 'missing' => []]
    ];
    $currentStudyType = detectStudyType($jsonData);
    $fieldsToValidate = [
        //SECTION 1
        ['name' => 'title', 'path' => 'stdyDscr/citation/titlStmt/titl', 'type' => 'localized_value'],
        ['name' => 'abstract', 'path' => 'stdyDscr/stdyInfo/abstract', 'type' => 'localized_value_by_type'],
        ['name' => 'studyStatus', 'path' => 'stdyDscr/method/stdyClas', 'type' => 'localized_value'],
        ['name' => 'nation', 'path' => 'stdyDscr/stdyInfo/sumDscr/nation', 'type' => 'localized_value'],
        ['name' => 'isHealthTheme', 'path' => 'additional/isHealthTheme', 'type' => 'simple_value'],
        ['name' => 'RareDiseases', 'path' => 'additional/theme/RareDiseases', 'type' => 'simple_value'],
        ['name' => 'universe_sex', 'path' => 'stdyDscr/stdyInfo/sumDscr/universe', 'type' => 'array_filter_check', 'filter_key' => 'level', 'filter_val' => 'sex'],
        ['name' => 'universe_age', 'path' => 'stdyDscr/stdyInfo/sumDscr/universe', 'type' => 'array_filter_check', 'filter_key' => 'level', 'filter_val' => 'age'],
        ['name' => 'universe_type', 'path' => 'stdyDscr/stdyInfo/sumDscr/universe', 'type' => 'array_filter_check', 'filter_key' => 'level', 'filter_val' => 'type'],
        //SECTION 2
        ['name' => 'authorizingAgency', 'path' => 'stdyDscr/studyAuthorization', 'sub_path' => 'authorizingAgency', 'type' => 'array_wrapper_check'],
        ['name' => 'isContributorPI', 'path' => 'additional/isContributorPI', 'type' => 'simple_value'],
        ['name' => 'addTeamMember', 'path' => 'additional/addTeamMember', 'type' => 'simple_value'],
        ['name' => 'author_name', 'path' => 'stdyDscr/citation/rspStmt/AuthEnty', 'sub_path' => 'name', 'type' => 'array_check_universal'],
        ['name' => 'author_affiliation', 'path' => 'stdyDscr/citation/rspStmt/AuthEnty', 'sub_path' => 'affiliation', 'type' => 'array_check_localized'],
        ['name' => 'contributor_name', 'path' => 'stdyDscr/citation/rspStmt/othId', 'sub_path' => 'name', 'type' => 'array_check_universal', 'dependency' => ['path' => 'additional/addTeamMember', 'expected_value' => true]],
        ['name' => 'contributor_affiliation', 'path' => 'stdyDscr/citation/rspStmt/othId', 'sub_path' => 'affiliation', 'type' => 'array_check_localized'],

        ['name' => 'collaborations', 'path' => 'stdyDscr/citation/rspStmt/othId', 'type' => 'array_filter_check', 'filter_key' => 'type', 'filter_val' => 'collaboration', 'sub_path' => 'affiliation'],
        ['name' => 'contact_name', 'path' => 'stdyDscr/citation/distStmt/contact', 'sub_path' => 'name', 'type' => 'array_check_universal'],
        ['name' => 'contact_email', 'path' => 'stdyDscr/citation/distStmt/contact', 'sub_path' => 'email', 'type' => 'array_check_universal'],
        ['name' => 'contact_affiliation', 'path' => 'stdyDscr/citation/distStmt/contact', 'sub_path' => 'affiliation', 'type' => 'array_check_localized'],

        ['name' => 'funding_agency', 'path' => 'stdyDscr/citation/prodStmt/fundAg', 'sub_path' => 'name', 'type' => 'array_check_localized'],
        ['name' => 'funding_agency_type', 'path' => 'stdyDscr/citation/prodStmt/fundAg', 'sub_path' => 'type', 'type' => 'array_check_localized'],
        ['name' => 'producer_name', 'path' => 'stdyDscr/citation/prodStmt/producer', 'sub_path' => 'name', 'type' => 'array_check_localized'],
        ['name' => 'producer_sponsorType', 'path' => 'stdyDscr/citation/prodStmt/producer', 'sub_path' => 'sponsorType', 'type' => 'array_check_localized'],

        ['name' => 'committee', 'path' => 'additional/governance/committee', 'type' => 'simple_value'],
        ['name' => 'networkConsortium', 'path' => 'additional/collaborations/networkConsortium', 'type' => 'simple_value'],

        // SECTION 3
        ['name' => 'anlyUnit', 'path' => 'stdyDscr/stdyInfo/sumDscr/anlyUnit', 'type' => 'localized_value'],
        ['name' => 'researchType', 'path' => 'stdyDscr/method/notes', 'type' => 'array_filter_check', 'filter_key' => 'subject', 'filter_val' => 'research type'],
        ['name' => 'developmentActivity', 'path' => 'stdyDscr/studyDevelopment/developmentActivity', 'sub_path' => 'description', 'type' => 'flat_localized_array'],
        ['name' => 'isInclusionGroups', 'path' => 'additional/isInclusionGroups', 'type' => 'simple_value'],
        ['name' => 'intervention_name', 'path' => 'additional/intervention', 'sub_path' => 'name', 'type' => 'array_check_localized'],
        ['name' => 'isClinicalTrial', 'path' => 'additional/interventionalStudy/isClinicalTrial', 'type' => 'simple_value', 'study_condition' => 'interventional'],
        ['name' => 'trialPhase', 'path' => 'additional/interventionalStudy/trialPhase', 'type' => 'localized_value', 'study_condition' => 'interventional'],
        ['name' => 'researchPurpose', 'path' => 'additional/interventionalStudy/researchPurpose', 'type' => 'localized_value', 'study_condition' => 'interventional'],
        ['name' => 'interventionalStudyModel', 'path' => 'additional/interventionalStudy/interventionalStudyModel', 'type' => 'localized_value', 'study_condition' => 'interventional'],
        ['name' => 'allocationMode', 'path' => 'additional/allocation/allocationMode', 'type' => 'localized_value', 'study_condition' => 'interventional'],
        ['name' => 'allocationUnit', 'path' => 'additional/allocation/allocationUnit', 'type' => 'localized_value', 'study_condition' => 'interventional'],
        ['name' => 'maskingType', 'path' => 'additional/masking/maskingType', 'type' => 'localized_value', 'study_condition' => 'interventional'],
        // OBSERVATIONNELLE
        ['name' => 'observationalStudyMethod', 'path' => 'stdyDscr/method/notes', 'type' => 'array_filter_check', 'filter_key' => 'subject', 'filter_val' => 'observational study method', 'study_condition' => 'observational'],
        // SECTION 4
        ['name' => 'targetSampleSize', 'path' => 'stdyDscr/method/dataColl/targetSampleSize', 'type' => 'localized_value'],
        ['name' => 'frequency', 'path' => 'stdyDscr/method/dataColl/frequenc', 'type' => 'localized_value'],
        ['name' => 'collMode', 'path' => 'stdyDscr/method/dataColl/collMode', 'type' => 'localized_value'],
        ['name' => 'sampProc', 'path' => 'stdyDscr/method/dataColl/sampProc', 'type' => 'localized_value'],
        ['name' => 'unitType', 'path' => 'stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType', 'type' => 'localized_value'],
        ['name' => 'isActiveFollowUp', 'path' => 'additional/activeFollowUp/isActiveFollowUp', 'type' => 'simple_value'],
        ['name' => 'followUp', 'path' => 'stdyDscr/method/notes', 'type' => 'array_filter_check', 'filter_key' => 'subject', 'filter_val' => 'follow-up', 'dependency' => ['path' => 'additional/activeFollowUp/isActiveFollowUp', 'expected_value' => true]],
        ['name' => 'dataKind', 'path' => 'stdyDscr/stdyInfo/sumDscr/dataKind', 'type' => 'localized_value'],
        ['name' => 'sources', 'path' => 'stdyDscr/method/dataColl/sources', 'type' => 'array_check_integrity', 'required_sub_fields' => ['srcOrig', 'notes'], 'dependency' => ['path' => 'additional/dataCollectionIntegration/isDataIntegration', 'expected_value' => true]],
        ['name' => 'isDataIntegration', 'path' => 'additional/dataCollectionIntegration/isDataIntegration', 'type' => 'simple_value'],
        ['name' => 'avlStatus', 'path' => 'stdyDscr/dataAccs/setAvail/avlStatus', 'type' => 'localized_value'],
        ['name' => 'useStmt_contact_name', 'path' => 'stdyDscr/dataAccs/useStmt/contact', 'sub_path' => 'name', 'type' => 'array_check_universal'],
        ['name' => 'useStmt_contact_email', 'path' => 'stdyDscr/dataAccs/useStmt/contact', 'sub_path' => 'email', 'type' => 'array_check_universal'],
        ['name' => 'variableDictionnaryAvailable', 'path' => 'additional/variableDictionnary/variableDictionnaryAvailable', 'type' => 'simple_value'],
    ];

    foreach (['fr', 'en'] as $lang) {
        foreach ($fieldsToValidate as $field) {
            if (isset($field['study_condition']) && $currentStudyType !== $field['study_condition']) {
                continue;
            }
            if (isset($field['dependency'])) {
                $actualValue = getDataAtPath($jsonData, $field['dependency']['path']);
                if ($actualValue !== $field['dependency']['expected_value']) {
                    continue;
                }
            }
            $isValid = false;
            $path = $field['path'];

            if ($field['type'] == 'array_check_integrity') {
                $array = getDataAtPath($jsonData, $path);
                $isValid = checkArrayObjectsIntegrity($array, $field['required_sub_fields'], $lang);
            } elseif ($field['type'] == 'simple_value') {
                $val = getDataAtPath($jsonData, $path);
                $isValid = ($val !== null && $val !== '');
            } elseif ($field['type'] == 'localized_value' || $field['type'] == 'localized_value_by_type') {
                $data = getDataAtPath($jsonData, $path);
                $isValid = hasLocalizedEntry($data, $lang);
            } elseif ($field['type'] == 'array_check_localized') {
                $array = getDataAtPath($jsonData, $path);
                $isValid = checkArrayObjectsLocalized($array, $field['sub_path'], $lang);
            } elseif ($field['type'] == 'array_check_universal') {
                $array = getDataAtPath($jsonData, $path);
                $isValid = checkArrayObjectsUniversal($array, $field['sub_path']);
            } elseif ($field['type'] == 'array_filter_check') {
                $array = getDataAtPath($jsonData, $path);
                $subPath = $field['sub_path'] ?? null;
                $isValid = checkFilteredArray($array, $field['filter_key'], $field['filter_val'], $lang, $subPath);
            } elseif ($field['type'] == 'array_wrapper_check') {
                $array = getDataAtPath($jsonData, $path);
                $isValid = checkWrapperArray($array, $field['sub_path'], $lang);
            } elseif ($field['type'] == 'flat_localized_array') {
                $array = getDataAtPath($jsonData, $path);
                $subKey = $field['sub_path'];
                $isValid = checkFlatLocalizedArray($array, $subKey, $lang);
            }

            if (!$isValid) {
                $validation[$lang]['valid'] = false;
                $validation[$lang]['missing'][] = $field['name'];
            }
        }
    }

    if (!checkColldateField($jsonData)) {
        $validation['fr']['valid'] = false;
        $validation['fr']['missing'][] = 'collDate';
        $validation['en']['valid'] = false;
        $validation['en']['missing'][] = 'collDate';
    }

    return $validation;
}

/**
 * Vérifie un tableau d'objets. TOUS les objets doivent avoir le champ traduit.
 */
function checkArrayObjectsLocalized($array, $subKey, $lang)
{
    if (!is_array($array) || empty($array)) {
        return false;
    }
    foreach ($array as $item) {
        if (!isset($item[$subKey])) {
            return false;
        }
        if (!hasLocalizedEntry($item[$subKey], $lang)) {
            return false;
        }
    }
    return true;
}

/**
 * Vérifie un tableau d'objets universels. TOUS les objets doivent avoir le champ rempli.
 */
function checkArrayObjectsUniversal($array, $subKey)
{
    if (!is_array($array) || empty($array)) {
        return false;
    }

    foreach ($array as $item) {
        if (!isset($item[$subKey]) || trim($item[$subKey]) === '') {
            return false;
        }
    }
    return true;
}


function checkArrayObjectsIntegrity($array, $requiredSubFields, $lang)
{
    if (!is_array($array) || empty($array)) return false;
    foreach ($array as $item) {
        foreach ($requiredSubFields as $fieldKey) {
            if (!isset($item[$fieldKey])) return false;
            if (!hasLocalizedEntry($item[$fieldKey], $lang)) return false;
        }
    }
    return true;
}

function detectStudyType($jsonData)
{
    $notes = getDataAtPath($jsonData, 'stdyDscr/method/notes');
    if (!is_array($notes)) return '';
    foreach ($notes as $note) {
        if (isset($note['subject']) && strtolower(trim($note['subject'])) === 'research type') {
            $value = strtolower($note['value'] ?? '');
            if (strpos($value, 'intervention') !== false) return 'interventional';
            if (strpos($value, 'observation') !== false) return 'observational';
        }
    }
    return '';
}

function checkFlatLocalizedArray($array, $valueKey, $lang)
{
    if (!is_array($array) || empty($array)) {
        return false;
    }

    foreach ($array as $item) {
        if (isset($item['lang']) && $item['lang'] === $lang && isset($item[$valueKey]) && !empty(trim($item[$valueKey]))) {
            return true;
        }
    }

    return false;
}

function getDataAtPath($data, $path)
{
    $parts = explode('/', $path);
    foreach ($parts as $part) {
        if (!isset($data[$part])) return null;
        $data = $data[$part];
    }
    return $data;
}

function hasLocalizedEntry($array, $lang)
{
    if (!is_array($array) || empty($array)) return false;
    foreach ($array as $item) {
        if (isset($item['lang']) && $item['lang'] === $lang && !empty(trim($item['value'] ?? ''))) return true;
    }
    return false;
}

function checkFilteredArray($array, $filterKey, $filterVal, $lang, $subPath = null)
{
    if (!is_array($array) || empty($array)) return false;

    foreach ($array as $item) {
        if (isset($item[$filterKey]) && strtolower($item[$filterKey]) === strtolower($filterVal)) {
            if ($subPath !== null) {
                if (isset($item[$subPath]) && hasLocalizedEntry($item[$subPath], $lang)) {
                    return true;
                }
            } else {
                if (isset($item['lang']) && $item['lang'] === $lang && !empty($item['value'])) {
                    return true;
                }
            }
        }
    }
    return false;
}

function checkWrapperArray($array, $subKey, $lang)
{
    if (!is_array($array) || empty($array)) return false;
    foreach ($array as $wrapper) {
        if (isset($wrapper[$subKey])) {
            $obj = $wrapper[$subKey];
            if (isset($obj['lang']) && $obj['lang'] === $lang && !empty($obj['value'])) return true;
        }
    }
    return false;
}

function checkColldateField($jsonData)
{
    $data = getDataAtPath($jsonData, 'stdyDscr/stdyInfo/sumDscr/collDate');
    if (!$data) return false;
    $entries = isset($data['event']) ? [$data] : $data;
    foreach ($entries as $entry) {
        if (isset($entry['event']) && $entry['event'] === 'start' && !empty($entry['value'])) return true;
    }
    return false;
}


/**
 * Génère un message de validation pour l'utilisateur
 */
function generate_validation_message($validation, $currentLang)
{
    $frValid = $validation['fr']['valid'];
    $enValid = $validation['en']['valid'];

    $messages = [];

    // Aucune langue valide
    if (!$frValid && !$enValid) {
        $messages[] = $currentLang === 'fr'
            ? "Aucune langue n'est valide. L'import ne peut pas être effectué."
            : "No language is valid. Import cannot be performed.";

        $messages[] = $currentLang === 'fr'
            ? "Champs manquants (FR) : " . implode(', ', $validation['fr']['missing'])
            : "Missing fields (FR): " . implode(', ', $validation['fr']['missing']);

        $messages[] = $currentLang === 'fr'
            ? "Champs manquants (EN) : " . implode(', ', $validation['en']['missing'])
            : "Missing fields (EN): " . implode(', ', $validation['en']['missing']);

        return [
            'success' => false,
            'canProceed' => false,
            'message' => implode("\n", $messages)
        ];
    }

    // Les deux langues valides
    if ($frValid && $enValid) {
        $messages[] = $currentLang === 'fr'
            ? "Les deux langues (FR et EN) sont valides. Les deux versions seront sauvegardées."
            : "Both languages (FR and EN) are valid. Both versions will be saved.";

        return [
            'success' => true,
            'canProceed' => true,
            'message' => implode("\n", $messages),
            'languages' => ['fr', 'en']
        ];
    }

    // Seul FR valide
    if ($frValid && !$enValid) {
        $messages[] = $currentLang === 'fr'
            ? "Seule la version française est valide. Seule la version FR sera sauvegardée."
            : "Only the French version is valid. Only the FR version will be saved.";

        $messages[] = $currentLang === 'fr'
            ? "Champs manquants (EN) : " . implode(', ', $validation['en']['missing'])
            : "Missing fields (EN): " . implode(', ', $validation['en']['missing']);

        return [
            'success' => true,
            'canProceed' => true,
            'message' => implode("\n", $messages),
            'languages' => ['fr']
        ];
    }

    // Seul EN valide
    if (!$frValid && $enValid) {
        $messages[] = $currentLang === 'fr'
            ? "Seule la version anglaise est valide. Seule la version EN sera sauvegardée."
            : "Only the English version is valid. Only the EN version will be saved.";

        $messages[] = $currentLang === 'fr'
            ? "Champs manquants (FR) : " . implode(', ', $validation['fr']['missing'])
            : "Missing fields (FR): " . implode(', ', $validation['fr']['missing']);

        return [
            'success' => true,
            'canProceed' => true,
            'message' => implode("\n", $messages),
            'languages' => ['en']
        ];
    }
}

/**
 * Sauvegarde une étude dans NADA et WP à partir du contenu JSON.
 */
function save_survey($fileContent, $isTeleversment = true, ?string $nada_token = null, ?string $filename = null)
{
    $currentLang = pll_current_language() ?? 'fr';
    global $wpdb;
    $table = $wpdb->prefix . "nada_contributor_pef";

    try {
        // 1. Décodage du JSON
        $jsonData = json_decode($fileContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON file: " . json_last_error_msg());
        }

        $languagesToProcess = [];
        if ($isTeleversment) {
            // 2. VALIDATION DES CHAMPS REQUIS
            $validation = validate_required_fields($jsonData);
            $validationMessage = generate_validation_message($validation, $currentLang);

            // Si aucune langue n'est valide, on arrête l'import
            if (!$validationMessage['canProceed']) {
                wp_send_json([
                    'success' => false,
                    'message' => $validationMessage['message'],
                    'response' => 'VALIDATION_FAILED'
                ]);
            }
            // Langues à traiter uniquement celles qui sont valides
            $languagesToProcess = $validationMessage['languages'];
        } else {
            $languagesToProcess = ['fr', 'en'];
        }

        // Récupération de l'ID de base
        $baseIdno = array_column($jsonData['stdyDscr']['citation']['titlStmt']['IDNo'] ?? [], 'value', 'agency')['FReSH'] ?? null;
        if (!$baseIdno) {
            // Fallback si l'IDNo FReSH est manquant on génère un IDNo
            $baseIdno = "FRESH-PEF" . getSurveyNewId();
        }

        // verification pour lier le fichier PEF avec son contrubitor si le contributor a un compte
        $result = $wpdb->get_row(
            $wpdb->prepare("SELECT first_name, last_name, affiliation, email FROM $table WHERE fresh_id = %s", $baseIdno.'-fr')
        );
        $contributorName = '';
        $contributorAffiliation = '';
        $createdBy = '';
        if ($result) {
          $contributorAffiliation = $result->affiliation;
          $contributorName = $result->first_name . ' ' . $result->last_name;
            $email = $result->email;
            $user = get_user_by('email', $email);
            if ($user) {
                $nada_token = get_user_meta($user->ID, 'nada_token', true);
                $createdBy = get_user_meta($user->ID, 'nada_user_id', true);
            }
        }

        // Extraction des données brutes communes (avant séparation par langue)
        $rawAuthEntities = $jsonData['stdyDscr']['citation']['rspStmt']['AuthEnty'] ?? [];
        $rawOthIds = $jsonData['stdyDscr']['citation']['rspStmt']['othId'] ?? [];
        $rawContacts = $jsonData['stdyDscr']['citation']['distStmt']['contact'] ?? [];
        $rawAgencies = (array)($jsonData['stdyDscr']['citation']['prodStmt']['fundAg'] ?? []);
        $rawProducers = (array)($jsonData['stdyDscr']['citation']['prodStmt']['producer'] ?? []);

        // Séparation des données par langue (fr/en)
        $langData = splitJsonByLang($jsonData);

        // Initialisation API
        $nada_api = get_nada_api_instance();
        $responses = [];
        $resultMultiplePEF = [];

        // Boucle de traitement par langue (UNIQUEMENT LES LANGUES VALIDES)
        foreach ($languagesToProcess as $lang) {
            // Si pas de données pour cette langue, on passe
            if (empty($langData[$lang])) continue;

            try {
                // Construction de l'ID unique par langue
                $idno = "{$baseIdno}-{$lang}";

                //affectitation data contributor
                $langData[$lang]['additional/contributorName'] = $contributorName;
                $langData[$lang]['additional/contributorAffiliation'] = $contributorAffiliation;

                //  TRAITEMENT LOGIQUE AVANT PAYLOAD ---
                $cleanAuthors = process_authors($rawAuthEntities, $lang);
                $cleanOthIds = process_oth_ids($rawOthIds, $lang);
                $cleanAgencies = process_entities($rawAgencies, $lang, 'type', 'otherType', false);
                $cleanProducers = process_entities($rawProducers, $lang, 'sponsorType', 'otherSponsorType', true);
                $cleanDistContacts = process_dist_contacts($rawContacts, $lang);
                $cleanUseContacts = process_use_contacts($langData[$lang]);

                // Calcul booléen pour "Has Email"
                $hasEmail = 'false';
                foreach ($cleanDistContacts as $c) {
                    if (!empty($c['email']) && filter_var($c['email'], FILTER_VALIDATE_EMAIL)) {
                        $hasEmail = 'true';
                        break;
                    }
                }
                // Extraction de l'email, nom prénom du premier investigateur (PI)
                $linkReport = '';
                $linkTechnical = '';
                if (!empty($cleanAuthors) && is_array($cleanAuthors)) {
                    $firstAuthor = reset($cleanAuthors);
                    $linkReport = $firstAuthor['email'] ?? '';
                    $linkTechnical = $firstAuthor['name'] ? str_replace(';', ' ', $firstAuthor['name']) : '';
                }

                // Pré-calcul des valeurs par défaut pour les Facettes (Filtres)
                $facets = [
                    'observationalStudy' => defaultFacet($langData[$lang]['additional/observationalStudy'] ?? '', $lang),
                    'avlStatus' => defaultFacet($langData[$lang]['additional/avlStatus'] ?? '', $lang),
                    'sourceOrigine' => defaultFacetSourceOrigin($langData[$lang]['additional/sourceOrigine/values'] ?? [], $lang),
                    'geogCover' => defaultFacet($langData[$lang]['stdyDscr/stdyInfo/sumDscr/geogCover'] ?? [], $lang),
                    'dataKind' => defaultFacet($langData[$lang]['stdyDscr/stdyInfo/sumDscr/dataKind'] ?? [], $lang),
                    'researchType' => defaultFacet($langData[$lang]['stdyDscr/method/notes/subject_researchType'] ?? '', $lang),
                    'startDate' => defaultFacet($langData[$lang]['stdyDscr/stdyInfo/sumDscr/collDate/event_start'] ?? '', $lang),
                    'endDate' => defaultFacet($langData[$lang]['stdyDscr/stdyInfo/sumDscr/collDate/event_end'] ?? '', $lang),
                    'targetSampleSize' => defaultFacet(cleanUnicodeSequences(get_lang_value($langData[$lang]['stdyDscr/method/dataColl/targetSampleSize'] ?? '')), $lang),
                    'healthTheme' => defaultFacetHealthTheme($langData[$lang]['additional/topicsHealthTheme'] ?? [], $lang),
                    'healthDeterminant' => defaultFacet($langData[$lang]['additional/topicsHealthDeterminant'] ?? [], $lang),
                    'age' => defaultFacet($langData[$lang]['level_age_clusion_I_filter'] ?? [], $lang),
                    'sex' => defaultFacet($langData[$lang]['level_sex_clusion_I_filter'] ?? '', $lang),
                    'popType' => defaultFacet($langData[$lang]['level_type_clusion_I'] ?? '', $lang),
                    'specPermRequired' => defaultFacet(normalizeBooleanValue($langData[$lang]['stdyDscr/dataAccs/useStmt/specPerm/required'] ?? '', $lang), $lang),
                    'trialPhase' => defaultFacet($langData[$lang]['additional/interventionalStudy/trialPhase'] ?? [], $lang)
                ];

                $payload = [
                    "data" => $langData[$lang],
                    "lang" => $lang,
                    "idno" => $idno,
                    "authors" => $cleanAuthors,
                    "oth_ids" => $cleanOthIds,
                    "dist_contacts" => $cleanDistContacts,
                    "use_contacts" => $cleanUseContacts,
                    "agencies" => $cleanAgencies,
                    "producers" => $cleanProducers,
                    "has_email_boolean_string" => $hasEmail,
                    "facets" => $facets,
                    "link_report" => $linkReport,
                    "link_technical" => $linkTechnical,
                    "link_study" => $baseIdno,
                    "status" => $isTeleversment ? '0' : '1' // Téléversement = BROUILLON (0) , IMPORTER PUBLIÉ (1)
                ];

                // 7. Création du Payload
                $studyPayload = prepare_nada_import_payload($payload);

                // 8. Appel API NADA
                $response = $nada_api->create_study(json_encode($studyPayload), $nada_token, $createdBy, $createdBy);

                if (empty($response['success']) || $response['success'] != 1 || ($response['data']['status'] ?? '') != 'success') {

                    foreach ($response['details'] as $error) {
                        if (strpos($error, 'IDNO already exists') !== false) {
                            throw new Exception('IDNO-exists');
                        }
                    }
                    
                    throw new Exception($response['data']['message'] ?? 'Erreur API NADA inconnue');
                }

                $responses[$lang] = $response;
                $resultMultiplePEF[] = $response['data']['dataset']['idno'];
            } catch (\Throwable $e) {
                // Log l'erreur mais continue pour l'autre langue
                nada_pef_log("Erreur Import JSON ($lang) - $filename: " . $e->getMessage(), 'error');
                if ($isTeleversment) {
                    if ($e->getMessage() == 'IDNO-exists') {
                        wp_send_json([
                            'success' => false,
                            'message' => $lang === 'fr' ?
                                'Une étude avec cet IDNO existe déjà.' :
                                'A study with this IDNO already exists.',
                            'response' => $e->getMessage()
                        ]);
                    } else {
                        wp_send_json([
                            'success' => false,
                            'message' => $lang === 'fr' ?
                                'Une erreur technique est survenue lors de la génération de la vue. Veuillez réessayer plus tard ou contacter le support' :
                                'A technical error occurred while generating the view. Please try again later or contact support.',
                            'response' => $e->getMessage()
                        ]);
                    }

                    return; // Arrêt en cas d'erreur Ajax
                }
            }
        }

        // 9. Insertion en base de données WordPress
        // On vérifie si au moins une langue a été importée avec succès
        if (isset($responses['fr']['data']['dataset']['id']) || isset($responses['en']['data']['dataset']['id'])) {
            try {
                add_study_to_wp(
                    $responses['fr']['data']['dataset']['idno'] ?? $responses['en']['data']['dataset']['idno'],
                    $responses['fr']['data']['dataset']['id'] ?? '',
                    $responses['en']['data']['dataset']['id'] ?? '',
                    $responses['fr']['data']['dataset']['title'] ?? '',
                    $responses['en']['data']['dataset']['title'] ?? '',
                    $filename,
                    $langData['fr']['stdyDscr/method/notes/subject_researchType'] ?? $langData['en']['stdyDscr/method/notes/subject_researchType'] ?? '',
                    $linkReport
                );
            } catch (\Throwable $e) {
                nada_pef_log("Erreur insertion DB WP: " . $e->getMessage(), 'error');
            }
        }

        // 10. Réponse finale avec message de validation
        if ($isTeleversment) {
            wp_send_json([
                'success' => true,
                'message' => $validationMessage['message'],
                'response' => $resultMultiplePEF,
                'data' => $responses
            ]);
        } else {
            return $resultMultiplePEF;
        }
    } catch (Exception $e) {
        nada_pef_log('Error save_survey: ' . $e->getMessage(), 'error');
        if ($isTeleversment) {
            wp_send_json([
                'success' => false,
                'message' => $currentLang === 'fr' ?
                    'Une erreur technique est survenue lors de l\'import.' :
                    'A technical error occurred during import.',
                'response' => $e->getMessage(),
            ]);
        } else {
            throw $e;
        }
    }
}

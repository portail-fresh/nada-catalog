<?php

class Nada_Deepl_Service
{
    private NADA_DeepL $deepl;
    public function __construct(
        NADA_DeepL $deepl,
    ) {
        $this->deepl = $deepl;
    }

    /** Traduction des etudes */
    public function translate_studies(array $data): array
    {
        try {
            if (!$this->deepl->isConfigured()) {
                throw new Exception('DeepL is not configured.');
            }
            $result = $data['structured'];
            $fr_needs_translation = $data['to_translate']['fr'];
            $en_needs_translation =  $data['to_translate']['en'];
            // Champs REPEATERS autorisés à être traduits
            $translatable_repeater_fields = [
                'interventions' => ['name', 'type', 'typeOther'],
                'inclusionGroups' => ['name', 'description'],
                'arms' => ['name', 'type', 'typeOther', 'description'],
                'relatedDocument' => ['type', 'title', 'link'],
                'othIds' => ['teamMemberLabo', 'name'],
                'contacts' => ['contactPointLabo'],
                'authEntities' => ['PILabo'],
            ];

            // Vocabulaires des topics à traduire
            $translatable_topic_vocabs = [
                'other determinant',
                'other health theme',
                'other biological determinant',
                'other behavioural determinant',
                'other healthcare determinant',
                'other environmental determinant',
                'other socio demographic determinant'
            ];

            // Champs à ignorer pour la traduction (clés imbriquées)
            $skip_translation_fields = [
                'type',
                'link'
            ];

            // Champs sérialisés à traduire
            $serialized_fields = [
                'standardsCompliance',
                'otherQualityStatements'
            ];


            // Gestion des REPEATERS pour la traduction
            foreach ($translatable_repeater_fields as $repeater => $fields) {
                $items_fr = $result['fr'][$repeater] ?? [];
                $items_en = $result['en'][$repeater] ?? [];

                foreach ($items_fr as $index => $item_fr) {
                    if (!isset($items_en[$index])) {
                        $items_en[$index] = [];
                    }
                    foreach ($fields as $field) {
                        if (in_array($field, $skip_translation_fields, true)) {
                            continue;
                        }

                        if (str_contains($field, '.')) {
                            list($parent, $child) = explode('.', $field, 2);
                            if (in_array("$parent.$child", $skip_translation_fields)) {
                                continue; // NE PAS TRADUIRE
                            }

                            $fr_val = $item_fr[$parent][$child] ?? '';
                            $en_val = $items_en[$index][$parent][$child] ?? '';
                        } else {
                            $fr_val = $item_fr[$field] ?? '';
                            $en_val = $items_en[$index][$field] ?? '';
                        }

                        // Si FR rempli et EN vide → traduire FR → EN
                        if (!empty($fr_val) && empty($en_val)) {
                            $fr_needs_translation["$repeater/$index/$field"] = $fr_val;
                        }

                        // Si EN rempli et FR vide → traduire EN → FR
                        if (!empty($en_val) && empty($fr_val)) {
                            $en_needs_translation["$repeater/$index/$field"] = $en_val;
                        }
                    }
                }
            }

            // Traduire FR -> EN
            if (!empty($fr_needs_translation)) {
                $texts_fr = array_values($fr_needs_translation);
                $keys_fr = array_keys($fr_needs_translation);
                $translations_en = $this->deepl->translate($texts_fr, 'FR', 'EN-US');

                if ($translations_en !== false) {
                    foreach ($keys_fr as $index => $baseKey) {
                        if (isset($translations_en[$index])) {
                            $translation_key = $keys_fr[$index];
                            if (preg_match('#^(.*)/(\d+)/(.*)$#', $translation_key, $m)) {
                                // REPEATER : interventions/0/name
                                $rep = $m[1];
                                $i   = (int)$m[2];
                                $field = $m[3];
                                $result['en'][$rep][$i][$field] = $translations_en[$index];
                            } else {
                                // CHAMP SIMPLE
                                $result['en'][$translation_key] = $translations_en[$index];
                            }
                        }
                    }
                }
            }

            // Traduire EN -> FR
            if (!empty($en_needs_translation)) {
                $texts_en = array_values($en_needs_translation);
                $keys_en = array_keys($en_needs_translation);
                $translations_fr = $this->deepl->translate($texts_en, 'EN', 'FR');

                if ($translations_fr !== false) {
                    foreach ($keys_en as $index => $baseKey) {
                        if (isset($translations_fr[$index])) {
                            $translation_key = $keys_en[$index];
                            if (preg_match('#^(.*)/(\d+)/(.*)$#', $translation_key, $m)) {
                                // REPEATER : interventions/0/name
                                $rep = $m[1];
                                $i   = (int)$m[2];
                                $field = $m[3];
                                $result['fr'][$rep][$i][$field] = $translations_fr[$index];
                            } else {
                                // CHAMP SIMPLE
                                $result['fr'][$translation_key] = $translations_fr[$index];
                            }
                        }
                    }
                }
            }

            // Traduire FR -> EN
            if (!empty($result['fr']['topics'])) {
                $topics_fr = $result['fr']['topics'] ?? [];
                $topics_en = $result['en']['topics'] ?? [];

                $topics_to_translate = [];
                $topic_indices = [];

                foreach ($topics_fr as $index => $topic_fr) {
                    $vocab = strtolower(trim($topic_fr['vocab'] ?? ''));

                    if (in_array($vocab, array_map('strtolower', $translatable_topic_vocabs))) {
                        $topic_text = trim($topic_fr['topic'] ?? '');
                        $topic_text_en = trim($topics_en[$index]['topic'] ?? '');

                        // Traduire seulement si FR non vide ET EN vide
                        if (!empty($topic_text) && empty($topic_text_en)) {
                            $topics_to_translate[] = $topic_text;
                            $topic_indices[] = $index;
                        }
                    } else {
                        if (!isset($topics_en[$index])) {
                            $topics_en[$index] = $topic_fr;
                        }
                    }
                }

                if (!empty($topics_to_translate)) {
                    $translated_topics = $this->deepl->translate($topics_to_translate, 'FR', 'EN-US');
                    if ($translated_topics !== false) {
                        foreach ($topic_indices as $batch_index => $topic_index) {
                            if (isset($translated_topics[$batch_index])) {
                                if (!isset($topics_en[$topic_index])) {
                                    $topics_en[$topic_index] = [];
                                }

                                $topics_en[$topic_index]['topic'] = $translated_topics[$batch_index];
                                $topics_en[$topic_index]['vocab'] = $topics_fr[$topic_index]['vocab'];

                                if (isset($topics_fr[$topic_index]['extLink'])) {
                                    $topics_en[$topic_index]['extLink'] = $topics_fr[$topic_index]['extLink'];
                                }
                            }
                        }
                        $result['en']['topics'] = $topics_en;
                    }
                }
            }

            // Traduire EN -> FR
            if (!empty($result['en']['topics'])) {
                $topics_en = $result['en']['topics'] ?? [];
                $topics_fr = $result['fr']['topics'] ?? [];

                $topics_to_translate = [];
                $topic_indices = [];

                foreach ($topics_en as $index => $topic_en) {
                    $vocab = strtolower(trim($topic_en['vocab'] ?? ''));

                    if (in_array($vocab, array_map('strtolower', $translatable_topic_vocabs))) {
                        $topic_text = trim($topic_en['topic'] ?? '');
                        $topic_text_fr = trim($topics_fr[$index]['topic'] ?? '');
                        if (!empty($topic_text) && empty($topic_text_fr)) {
                            $topics_to_translate[] = $topic_text;
                            $topic_indices[] = $index;
                        }
                    } else {
                        if (!isset($topics_fr[$index])) {
                            $topics_fr[$index] = $topic_en;
                        }
                    }
                }

                if (!empty($topics_to_translate)) {
                    $translated_topics = $this->deepl->translate($topics_to_translate, 'EN', 'FR');

                    if ($translated_topics !== false) {
                        foreach ($topic_indices as $batch_index => $topic_index) {
                            if (isset($translated_topics[$batch_index])) {
                                if (!isset($topics_fr[$topic_index])) {
                                    $topics_fr[$topic_index] = [];
                                }

                                $topics_fr[$topic_index]['topic'] = $translated_topics[$batch_index];
                                $topics_fr[$topic_index]['vocab'] = $topics_en[$topic_index]['vocab'];

                                if (isset($topics_en[$topic_index]['extLink'])) {
                                    $topics_fr[$topic_index]['extLink'] = $topics_en[$topic_index]['extLink'];
                                }
                            }
                        }

                        $result['fr']['topics'] = $topics_fr;
                    }
                }
            }

            // Traduire FR -> EN
            if (!empty($result['fr']['keywords']) && empty($result['en']['keywords'])) {
                $keywords_fr = array_map(function ($kw) {
                    return is_array($kw) && isset($kw['keyword']) ? $kw['keyword'] : $kw;
                }, $result['fr']['keywords']);

                $translated_keywords = $this->deepl->translate($keywords_fr, 'FR', 'EN-US');
                if ($translated_keywords !== false) {
                    $result['en']['keywords'] = array_map(function ($translation) {
                        return ['keyword' => $translation];
                    }, $translated_keywords);
                }
            }

            // Traduire EN -> FR
            if (!empty($result['en']['keywords']) && empty($result['fr']['keywords'])) {
                $keywords_en = array_map(function ($kw) {
                    return is_array($kw) && isset($kw['keyword']) ? $kw['keyword'] : $kw;
                }, $result['en']['keywords']);

                $translated_keywords = $this->deepl->translate($keywords_en, 'EN', 'FR');

                if ($translated_keywords !== false) {
                    $result['fr']['keywords'] = array_map(function ($translation) {
                        return ['keyword' => $translation];
                    }, $translated_keywords);
                }
            }

            // Traduction des champs sérialisés
            foreach ($serialized_fields as $field) {
                // FR -> EN
                if (!empty($result['fr'][$field]) && empty($result['en'][$field])) {
                    $source_items = nada_parse_serialized_array($result['fr'][$field]);
                    if (!empty($source_items)) {
                        $translated_items = $this->deepl->translate($source_items, 'FR', 'EN-US');
                        if ($translated_items !== false) {
                            $result['en'][$field] = nada_serialize_to_array_string($translated_items);
                        }
                    }
                }

                // EN -> FR
                if (!empty($result['en'][$field]) && empty($result['fr'][$field])) {
                    $source_items = nada_parse_serialized_array($result['en'][$field]);
                    if (!empty($source_items)) {
                        $translated_items = $this->deepl->translate($source_items, 'EN', 'FR');
                        if ($translated_items !== false) {
                            $result['fr'][$field] = nada_serialize_to_array_string($translated_items);
                        }
                    }
                }
            }


            // FR -> EN
            if (!empty($result['fr']['otherAgencies']) && isset($result['en']['otherAgencies'])) {
                $source_array = $result['fr']['otherAgencies'];
                $target_array = $result['en']['otherAgencies'];
                $fr_has_content = false;
                foreach ($source_array as $value) {
                    if (!empty(trim($value))) {
                        $fr_has_content = true;
                        break;
                    }
                }
                $en_is_empty = true;
                foreach ($target_array as $value) {
                    if (!empty(trim($value))) {
                        $en_is_empty = false;
                        break;
                    }
                }
                if ($fr_has_content && $en_is_empty) {
                    $texts_to_translate = [];
                    $indices_map = [];
                    foreach ($source_array as $index => $value) {
                        if (!empty(trim($value))) {
                            $texts_to_translate[] = $value;
                            $indices_map[] = $index;
                        }
                    }

                    if (!empty($texts_to_translate)) {
                        $translated_texts = $this->deepl->translate($texts_to_translate, 'FR', 'EN-US');

                        if ($translated_texts !== false) {
                            // Créer un tableau avec la même structure (copier toutes les valeurs y compris vides)
                            $result['en']['otherAgencies'] = $source_array;
                            // Remplacer uniquement les valeurs traduites
                            foreach ($indices_map as $batch_index => $original_index) {
                                if (isset($translated_texts[$batch_index])) {
                                    $result['en']['otherAgencies'][$original_index] = $translated_texts[$batch_index];
                                }
                            }
                        }
                    }
                }
            }

            // EN -> FR
            if (!empty($result['en']['otherAgencies']) && isset($result['fr']['otherAgencies'])) {
                $source_array = $result['en']['otherAgencies'];
                $target_array = $result['fr']['otherAgencies'];
                $en_has_content = false;
                foreach ($source_array as $value) {
                    if (!empty(trim($value))) {
                        $en_has_content = true;
                        break;
                    }
                }
                $fr_is_empty = true;
                foreach ($target_array as $value) {
                    if (!empty(trim($value))) {
                        $fr_is_empty = false;
                        break;
                    }
                }
                if ($en_has_content && $fr_is_empty) {
                    // Filtrer les valeurs vides pour la traduction (mais conserver les indices)
                    $texts_to_translate = [];
                    $indices_map = [];
                    foreach ($source_array as $index => $value) {
                        if (!empty(trim($value))) {
                            $texts_to_translate[] = $value;
                            $indices_map[] = $index;
                        }
                    }

                    if (!empty($texts_to_translate)) {
                        $translated_texts = $this->deepl->translate($texts_to_translate, 'EN', 'FR');
                        if ($translated_texts !== false) {
                            // Créer un tableau avec la même structure (copier toutes les valeurs y compris vides)
                            $result['fr']['otherAgencies'] = $source_array;
                            // Remplacer uniquement les valeurs traduites
                            foreach ($indices_map as $batch_index => $original_index) {
                                if (isset($translated_texts[$batch_index])) {
                                    $result['fr']['otherAgencies'][$original_index] = $translated_texts[$batch_index];
                                }
                            }
                        }
                    }
                }
            }

            // FR -> EN
            if (!empty($result['fr']['otherSponsorType']) && isset($result['en']['otherSponsorType'])) {
                $source_array = $result['fr']['otherSponsorType'];
                $target_array = $result['en']['otherSponsorType'];
                $fr_has_content = false;
                foreach ($source_array as $value) {
                    if (!empty(trim($value))) {
                        $fr_has_content = true;
                        break;
                    }
                }
                $en_is_empty = true;
                foreach ($target_array as $value) {
                    if (!empty(trim($value))) {
                        $en_is_empty = false;
                        break;
                    }
                }
                if ($fr_has_content && $en_is_empty) {
                    $texts_to_translate = [];
                    $indices_map = [];
                    foreach ($source_array as $index => $value) {
                        if (!empty(trim($value))) {
                            $texts_to_translate[] = $value;
                            $indices_map[] = $index;
                        }
                    }

                    if (!empty($texts_to_translate)) {
                        $translated_texts = $this->deepl->translate($texts_to_translate, 'FR', 'EN-US');

                        if ($translated_texts !== false) {
                            // Créer un tableau avec la même structure (copier toutes les valeurs y compris vides)
                            $result['en']['otherSponsorType'] = $source_array;
                            // Remplacer uniquement les valeurs traduites
                            foreach ($indices_map as $batch_index => $original_index) {
                                if (isset($translated_texts[$batch_index])) {
                                    $result['en']['otherSponsorType'][$original_index] = $translated_texts[$batch_index];
                                }
                            }
                        }
                    }
                }
            }

            // EN -> FR
            if (!empty($result['en']['otherSponsorType']) && isset($result['fr']['otherSponsorType'])) {
                $source_array = $result['en']['otherSponsorType'];
                $target_array = $result['fr']['otherSponsorType'];
                $en_has_content = false;
                foreach ($source_array as $value) {
                    if (!empty(trim($value))) {
                        $en_has_content = true;
                        break;
                    }
                }
                $fr_is_empty = true;
                foreach ($target_array as $value) {
                    if (!empty(trim($value))) {
                        $fr_is_empty = false;
                        break;
                    }
                }
                if ($en_has_content && $fr_is_empty) {
                    // Filtrer les valeurs vides pour la traduction (mais conserver les indices)
                    $texts_to_translate = [];
                    $indices_map = [];
                    foreach ($source_array as $index => $value) {
                        if (!empty(trim($value))) {
                            $texts_to_translate[] = $value;
                            $indices_map[] = $index;
                        }
                    }

                    if (!empty($texts_to_translate)) {
                        $translated_texts = $this->deepl->translate($texts_to_translate, 'EN', 'FR');
                        if ($translated_texts !== false) {
                            // Créer un tableau avec la même structure (copier toutes les valeurs y compris vides)
                            $result['fr']['otherSponsorType'] = $source_array;
                            // Remplacer uniquement les valeurs traduites
                            foreach ($indices_map as $batch_index => $original_index) {
                                if (isset($translated_texts[$batch_index])) {
                                    $result['fr']['otherSponsorType'][$original_index] = $translated_texts[$batch_index];
                                }
                            }
                        }
                    }
                }
            }

            // FR -> EN
            if (!empty($result['fr']['otherFundingAgentTypes']) && isset($result['en']['otherFundingAgentTypes'])) {
                $source_array = $result['fr']['otherFundingAgentTypes'];
                $target_array = $result['en']['otherFundingAgentTypes'];
                $fr_has_content = false;
                foreach ($source_array as $value) {
                    if (!empty(trim($value))) {
                        $fr_has_content = true;
                        break;
                    }
                }
                $en_is_empty = true;
                foreach ($target_array as $value) {
                    if (!empty(trim($value))) {
                        $en_is_empty = false;
                        break;
                    }
                }
                if ($fr_has_content && $en_is_empty) {
                    $texts_to_translate = [];
                    $indices_map = [];
                    foreach ($source_array as $index => $value) {
                        if (!empty(trim($value))) {
                            $texts_to_translate[] = $value;
                            $indices_map[] = $index;
                        }
                    }

                    if (!empty($texts_to_translate)) {
                        $translated_texts = $this->deepl->translate($texts_to_translate, 'FR', 'EN-US');

                        if ($translated_texts !== false) {
                            // Créer un tableau avec la même structure (copier toutes les valeurs y compris vides)
                            $result['en']['otherFundingAgentTypes'] = $source_array;
                            // Remplacer uniquement les valeurs traduites
                            foreach ($indices_map as $batch_index => $original_index) {
                                if (isset($translated_texts[$batch_index])) {
                                    $result['en']['otherFundingAgentTypes'][$original_index] = $translated_texts[$batch_index];
                                }
                            }
                        }
                    }
                }
            }

            // EN -> FR
            if (!empty($result['en']['otherFundingAgentTypes']) && isset($result['fr']['otherFundingAgentTypes'])) {
                $source_array = $result['en']['otherFundingAgentTypes'];
                $target_array = $result['fr']['otherFundingAgentTypes'];
                $en_has_content = false;
                foreach ($source_array as $value) {
                    if (!empty(trim($value))) {
                        $en_has_content = true;
                        break;
                    }
                }
                $fr_is_empty = true;
                foreach ($target_array as $value) {
                    if (!empty(trim($value))) {
                        $fr_is_empty = false;
                        break;
                    }
                }
                if ($en_has_content && $fr_is_empty) {
                    // Filtrer les valeurs vides pour la traduction (mais conserver les indices)
                    $texts_to_translate = [];
                    $indices_map = [];
                    foreach ($source_array as $index => $value) {
                        if (!empty(trim($value))) {
                            $texts_to_translate[] = $value;
                            $indices_map[] = $index;
                        }
                    }

                    if (!empty($texts_to_translate)) {
                        $translated_texts = $this->deepl->translate($texts_to_translate, 'EN', 'FR');
                        if ($translated_texts !== false) {
                            // Créer un tableau avec la même structure (copier toutes les valeurs y compris vides)
                            $result['fr']['otherFundingAgentTypes'] = $source_array;
                            // Remplacer uniquement les valeurs traduites
                            foreach ($indices_map as $batch_index => $original_index) {
                                if (isset($translated_texts[$batch_index])) {
                                    $result['fr']['otherFundingAgentTypes'][$original_index] = $translated_texts[$batch_index];
                                }
                            }
                        }
                    }
                }
            }


            // sources
            if (isset($result['fr']['sources']) || isset($result['en']['sources'])) {
                $src_fr = $result['fr']['sources'] ?? [];
                $src_en = $result['en']['sources'] ?? [];

                $sources_to_en = [];
                $sources_to_fr = [];

                $max_count = max(count($src_fr), count($src_en));

                for ($i = 0; $i < $max_count; $i++) {
                    // 1. Champs textes simples
                    $titl_fr = $src_fr[$i]['sourceCitation']['titlStmt']['titl'] ?? '';
                    $titl_en = $src_en[$i]['sourceCitation']['titlStmt']['titl'] ?? '';
                    if ($titl_fr !== '' && $titl_en === '') $sources_to_en["$i|titl"] = $titl_fr;
                    if ($titl_en !== '' && $titl_fr === '') $sources_to_fr["$i|titl"] = $titl_en;

                    $hold_fr = $src_fr[$i]['sourceCitation']['holdings'] ?? '';
                    $hold_en = $src_en[$i]['sourceCitation']['holdings'] ?? '';
                    if ($hold_fr !== '' && $hold_en === '') $sources_to_en["$i|hold"] = $hold_fr;
                    if ($hold_en !== '' && $hold_fr === '') $sources_to_fr["$i|hold"] = $hold_en;

                    $note_fr = $src_fr[$i]['sourceCitation']['notes']['value'] ?? '';
                    $note_en = $src_en[$i]['sourceCitation']['notes']['value'] ?? '';
                    if ($note_fr !== '' && $note_en === '') $sources_to_en["$i|note"] = $note_fr;
                    if ($note_en !== '' && $note_fr === '') $sources_to_fr["$i|note"] = $note_en;

                    // 2. Champ tableau : srcOrig
                    $orig_fr = $src_fr[$i]['srcOrig'] ?? [];
                    $orig_en = $src_en[$i]['srcOrig'] ?? [];

                    if (!empty($orig_fr) && empty($orig_en)) {
                        foreach ($orig_fr as $j => $val) {
                            if (trim($val) !== '') $sources_to_en["$i|srcOrig|$j"] = $val;
                        }
                    }
                    if (!empty($orig_en) && empty($orig_fr)) {
                        foreach ($orig_en as $j => $val) {
                            if (trim($val) !== '') $sources_to_fr["$i|srcOrig|$j"] = $val;
                        }
                    }
                }

                // --- Traduction FR -> EN ---
                if (!empty($sources_to_en)) {
                    $texts = array_values($sources_to_en);
                    $keys = array_keys($sources_to_en);
                    $translated = $this->deepl->translate($texts, 'FR', 'EN-US');

                    if ($translated !== false) {
                        foreach ($keys as $idx => $key) {
                            $parts = explode('|', $key);
                            $i = (int)$parts[0];
                            $field = $parts[1];

                            // Création de la structure DDI si manquante
                            if (!isset($result['en']['sources'][$i]['sourceCitation'])) {
                                $result['en']['sources'][$i]['sourceCitation'] = [
                                    'titlStmt' => ['titl' => ''], 'holdings' => '', 'notes' => ['subject' => 'source purpose', 'value' => '']
                                ];
                            }
                            if (!isset($result['en']['sources'][$i]['srcOrig'])) {
                                $result['en']['sources'][$i]['srcOrig'] = [];
                            }

                            if ($field === 'titl') $result['en']['sources'][$i]['sourceCitation']['titlStmt']['titl'] = $translated[$idx];
                            elseif ($field === 'hold') $result['en']['sources'][$i]['sourceCitation']['holdings'] = $translated[$idx];
                            elseif ($field === 'note') $result['en']['sources'][$i]['sourceCitation']['notes']['value'] = $translated[$idx];
                            elseif ($field === 'srcOrig') {
                                $j = (int)$parts[2];
                                $result['en']['sources'][$i]['srcOrig'][$j] = $translated[$idx];
                            }
                        }
                    }
                }

                // --- Traduction EN -> FR ---
                if (!empty($sources_to_fr)) {
                    $texts = array_values($sources_to_fr);
                    $keys = array_keys($sources_to_fr);
                    $translated = $this->deepl->translate($texts, 'EN', 'FR');

                    if ($translated !== false) {
                        foreach ($keys as $idx => $key) {
                            $parts = explode('|', $key);
                            $i = (int)$parts[0];
                            $field = $parts[1];

                            if (!isset($result['fr']['sources'][$i]['sourceCitation'])) {
                                $result['fr']['sources'][$i]['sourceCitation'] = [
                                    'titlStmt' => ['titl' => ''], 'holdings' => '', 'notes' => ['subject' => 'source purpose', 'value' => '']
                                ];
                            }
                            if (!isset($result['fr']['sources'][$i]['srcOrig'])) {
                                $result['fr']['sources'][$i]['srcOrig'] = [];
                            }

                            if ($field === 'titl') $result['fr']['sources'][$i]['sourceCitation']['titlStmt']['titl'] = $translated[$idx];
                            elseif ($field === 'hold') $result['fr']['sources'][$i]['sourceCitation']['holdings'] = $translated[$idx];
                            elseif ($field === 'note') $result['fr']['sources'][$i]['sourceCitation']['notes']['value'] = $translated[$idx];
                            elseif ($field === 'srcOrig') {
                                $j = (int)$parts[2];
                                $result['fr']['sources'][$i]['srcOrig'][$j] = $translated[$idx];
                            }
                        }
                    }
                }
            }


            // otherSourceType
            $other_fr = $result['fr']['otherSourceType'] ?? [];
            $other_en = $result['en']['otherSourceType'] ?? [];

            if (!is_array($other_fr)) $other_fr = [];
            if (!is_array($other_en)) $other_en = [];

            $max_other = max(count($other_fr), count($other_en));

            $other_to_en = [];
            $other_to_fr = [];

            for ($i = 0; $i < $max_other; $i++) {
                $val_fr = trim((string)($other_fr[$i] ?? ''));
                $val_en = trim((string)($other_en[$i] ?? ''));

                if ($val_fr !== '' && $val_en === '') {
                    $other_to_en[$i] = $val_fr;
                }
                if ($val_en !== '' && $val_fr === '') {
                    $other_to_fr[$i] = $val_en;
                }
            }

            // 3. Traduction FR -> EN
            if (!empty($other_to_en)) {
                $texts = array_values($other_to_en);
                $keys = array_keys($other_to_en);
                $translated = $this->deepl->translate($texts, 'FR', 'EN-US');

                if ($translated !== false) {
                    if (!isset($result['en']['otherSourceType']) || !is_array($result['en']['otherSourceType'])) {
                        $result['en']['otherSourceType'] = array_fill(0, count($other_fr), "");
                    }
                    foreach ($keys as $idx => $original_index) {
                        $result['en']['otherSourceType'][$original_index] = $translated[$idx];
                    }
                }
            }

            // 4. Traduction EN -> FR
            if (!empty($other_to_fr)) {
                $texts = array_values($other_to_fr);
                $keys = array_keys($other_to_fr);
                $translated = $this->deepl->translate($texts, 'EN', 'FR');

                if ($translated !== false) {
                    if (!isset($result['fr']['otherSourceType']) || !is_array($result['fr']['otherSourceType'])) {
                        $result['fr']['otherSourceType'] = array_fill(0, count($other_en), "");
                    }
                    foreach ($keys as $idx => $original_index) {
                        $result['fr']['otherSourceType'][$original_index] = $translated[$idx];
                    }
                }
            }
            return $result;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function get_translatable_fields(): array
    {
        return [
            //step 1
            'stdyDscr/citation/titlStmt/titl',
            'stdyDscr/citation/titlStmt/altTitl',
            'stdyDscr/stdyInfo/purpose/value',
            'stdyDscr/stdyInfo/abstract/value',
            'additional/OtherPopulationType',
            'stdyDscr/stdyInfo/sumDscr/universe/Clusion_I',
            'stdyDscr/stdyInfo/sumDscr/universe/Clusion_E',
            'additional/geographicalCoverage/geoDetail',
            // step 2
            'stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/committee',
            'stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/governance',
            'stdyDscr/citation/rspStmt/othId/collaboration',
            // step 3
            'additional/otherResearchType/otherResearchTypeDetails',
            'stdyDscr/studyDevelopment/developmentActivity/type_primaryEvaluation/description',
            'stdyDscr/studyDevelopment/developmentActivity/type_secondaryEvaluation/description',
            'additional/interventionalStudy/otherResearchPurpose',
            // step 4
            'stdyDscr/method/dataColl/frequenc',
            'additional/collectionProcess/collectionModeOther',
            'additional/collectionProcess/collectionModeDetails',
            'additional/dataCollection/samplingModeOther',
            'additional/dataCollection/recruitmentSourceOther',
            'additional/activeFollowUp/followUpModeOther',
            'additional/dataTypes/dataTypeOther',
            'additional/dataTypes/clinicalDataDetails',
            'additional/dataTypes/paraclinicalDataOther',
            'additional/dataTypes/biologicalDataDetails',
            'additional/dataTypes/biobankContentOther',
            'additional/dataTypes/otherLiquidsDetails',
            'stdyDscr/othStdMat/relMat',
            'stdyDscr/dataAccs/useStmt/specPerm',
            'stdyDscr/dataAccs/setAvail/complete',
            'stdyDscr/dataAccs/setAvail/accsPlac',
            'stdyDscr/dataAccs/setAvail/conditions',
            'stdyDscr/dataAccs/useStmt/restrctn',
            'stdyDscr/dataAccs/setAvail/notes',
            'stdyDscr/dataAccs/useStmt/confDec',
            'stdyDscr/dataAccs/useStmt/deposReq',
            'stdyDscr/dataAccs/useStmt/citReq',
            'additional/variableDictionnary/variableDictionnaryLink',
            'additional/mockSample/mockSampleLocation',
            'additional/dataCollection/otherDocumentation'
        ];
    }
}

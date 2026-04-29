<?php

if (! function_exists('splitIntoVerticalColumns')) {
    function splitIntoVerticalColumns($items,  $columns = 3)
    {
        $total = count($items);
        $perColumn = (int) ceil($total / $columns);
        $result = array_fill(0, $columns, []);

        $i = 0;
        foreach ($items as $key => $value) {
            $col = (int) floor($i / $perColumn);
            if ($col >= $columns) {
                $col = $columns - 1;
            }
            $result[$col][$key] = $value;
            $i++;
        }

        return $result;
    }
}

// fonction pour recupere la liste des institutions
if (!function_exists('getListInstitutions')) {
    function getListInstitutions(): array
    {
        global $listInstitutions;
        if (empty($listInstitutions) || !is_array($listInstitutions)) {
            return ['fr' => [], 'en' => []];
        }

        $options = ['fr' => [], 'en' => []];

        foreach ($listInstitutions as $item) {
            $options['fr'][] = [
                'id' => $item['id'],
                'label' => $item['label_fr'] ?? '',
                'uri' => $item['uri'] ?? '',
                'identifier' => $item['identifier'] ?? '',
                'siren' => $item['siren'] ?? '',
                'status' => $item['status'] ?? '',
            ];
            $options['en'][] = [
                'id' => $item['id'],
                'label' => $item['label_en'] ?? '',
                'uri' => $item['uri'] ?? '',
                'identifier' => $item['identifier'] ?? '',
                'siren' => $item['siren'] ?? '',
                'status' => $item['status'] ?? '',
            ];
        }

        return $options;
    }
}

if (! function_exists('getReferentielByName')) {
    function getReferentielByName(string $name, bool $hasExtlink = false)
    {
        global $list_referentiels;

        foreach ($list_referentiels as $ref) {
            // Gestion tableau ou objet
            if ((is_array($ref) && isset($ref['name']) && $ref['name'] === $name) ||
                (is_object($ref) && isset($ref->name) && $ref->name === $name)
            ) {

                // Récupération des items
                $items = is_array($ref) ? $ref['items'] : $ref->items;

                // Construction du tableau final
                $options = ['fr' => [], 'en' => []];
                foreach ($items as $item) {
                    if ($hasExtlink) {
                        $options['fr'][] = [
                            'id' => $item->id,
                            'label' => $item->label_fr ?? '',
                            'uri' => $item->uri ?? '',
                            'uri_esv' => $item->uri_esv ?? '',
                            'uri_mesh' => $item->uri_mesh ?? '',
                            'identifier'  => $item->identifier ?? '',
                            'siren'  => $item->siren ?? '',
                            'status'  => $item->status ?? '',
                        ];
                        $options['en'][] = [
                            'id' => $item->id,
                            'label' => $item->label_en ?? '',
                            'uri' => $item->uri ?? '',
                            'uri_esv' => $item->uri_esv ?? '',
                            'uri_mesh' => $item->uri_mesh ?? '',
                            'identifier'  => $item->identifier ?? '',
                            'siren'  => $item->siren ?? '',
                            'status'  => $item->status ?? '',
                        ];
                    } else {
                        if ($name === 'RecruitmentSource') {
                            $keyId = $item->id;
                            $options['fr'][$keyId] = $item->label_fr;
                            $options['en'][$keyId] = $item->label_en;
                        }else{
                            $options['fr'][$item->label_fr] = $item->label_fr;
                            $options['en'][$item->label_en] = $item->label_en;
                        }
                    }
                }

                /* tri alphabétique par label */
                if (in_array($name, ['HealthTheme', 'CollectionMode', 'FranceRegion', 'RecruitmentSource'])) {
                    $options =  triOptionsByName($options);
                }

                return $options;
            }
        }

        return ['fr' => [], 'en' => []]; // si non trouvé, retourne tableau vide
    }
}

if (! function_exists('getTooltipByName')) {
    function getTooltipByName(string $variableName, string $lang = 'fr', string $parent = ''): string
    {
        $list_tooltips = get_list_metadata_tooltips_wp();
        // Créer la clé avec parent si fourni
        $key = !empty($parent) ? $parent . '.' . $variableName : $variableName;

        if (
            !isset($list_tooltips[$key]) ||
            !isset($list_tooltips[$key][$lang])
        ) {
            return '';
        }

        return $list_tooltips[$key][$lang];
    }
}

if (!function_exists('triOptionsByName')) {
    function triOptionsByName(array $options): array
    {
        $forceLast = ['autre', 'other'];

        foreach ($options as $lang => $values) {

            if (!is_array($values)) {
                continue;
            }

            uasort($values, function ($a, $b) use ($forceLast) {

                // Extraction du label
                $labelA = is_array($a) && isset($a['label'])
                    ? mb_strtolower($a['label'])
                    : mb_strtolower((string) $a);

                $labelB = is_array($b) && isset($b['label'])
                    ? mb_strtolower($b['label'])
                    : mb_strtolower((string) $b);

                // Forcer en dernier
                if (in_array($labelA, $forceLast, true)) {
                    return 1;
                }

                if (in_array($labelB, $forceLast, true)) {
                    return -1;
                }

                // Tri alphabétique normal
                return strcasecmp($labelA, $labelB);
            });

            $options[$lang] = $values;
        }

        return $options;
    }
}

if (!function_exists('json_values_equal')) {
    function json_values_equal($a, $b): bool
    {
        // Si ce sont des tableaux
        if (is_array($a) && is_array($b)) {
            // trier les tableaux indexés pour ignorer l’ordre
            $a_sorted = $a;
            $b_sorted = $b;

            if (array_keys($a_sorted) === range(0, count($a_sorted) - 1)) sort($a_sorted);
            if (array_keys($b_sorted) === range(0, count($b_sorted) - 1)) sort($b_sorted);

            // comparer récursivement chaque élément
            if (count($a_sorted) !== count($b_sorted)) return false;
            foreach ($a_sorted as $k => $v) {
                if (!array_key_exists($k, $b_sorted)) return false;
                if (!json_values_equal($v, $b_sorted[$k])) return false;
            }
            return true;
        }

        // Si l’un est tableau et l’autre pas
        if (is_array($a) xor is_array($b)) return false;

        // comparaison simple
        return $a === $b;
    }
}

if (! function_exists('normalizeNameForStatus')) {
    function normalizeNameForStatus($name)
    {
        $map = [
            'stdyDscr/method/notes' => 'additional/observationalStudy',
            'stdyDscr/method/notes/subject_followUP' => 'followupNotes',
        ];

        if (isset($map[$name])) {
            return $map[$name];
        }

        $name = preg_replace('/\[\]/', '', $name);

        if (preg_match('#/value/(.+)$#', $name, $matches)) {
            return $matches[1];
        }

        $name = preg_replace('#/value/.*$#', '', $name);

        return $name;
    }
}

if (! function_exists('nada_renderInputGroup2')) {
    /**
     * Génère un bloc input group multilingue ou non
     *
     * @param string $label_fr     Label du champ en Fr
     * @param string $label_en     Label du champ en EN
     * @param string $tooltip_fr     description du champ en Fr
     * @param string $tooltip_en     description du champ en EN
     * @param string $name      Nom du champ
     * @param string $type     checkbox | select-multiple | autocomplete | select
     * @param array  $options   Options pour select/radio/checkbox (['valeur' => 'Libellé'])
     * @param bool   $multilang Multilingue (FR/EN) ou non
     * @param bool   $required  Champ obligatoire ?
     */
    function nada_renderInputGroup2($label_fr, $label_en, $name, $type = 'text', $options = [], $multilang = true, $required = false, $tooltip_fr = null, $tooltip_en = null, $same = false, $subtitle_fr = null, $subtitle_en = null, $maxChars = null, $displayBtn = false)
    {

        //$required = false;
        global $mode;
        global $jsonRec; // tableau associatif JSON (payload)

        global $studyDetails;
        global $jsonParentStudy;
        global $compareStudy;

        // Helper: normaliser une valeur en tableau (pour checkbox/select-multiple/tags)
        $to_array = static function ($v): array {
            if ($v === null || $v === '') return [];
            if (is_array($v)) return $v;
            return [$v];
        };
        // Helper: escape attr & html
        $e = static fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
        // Expect V2: $options = ['fr' => [ ['label','uri','uri_esv','uri_mesh'], ... ], 'en' => [...]]
        $opts_v2 = static function (array $options, string $lng): array {
            if (!isset($options[$lng]) || !is_array($options[$lng])) return [];
            $out = [];
            foreach ($options[$lng] as $it) {
                $out[] = [
                    'id'    => (string)($it['id']    ?? ''),
                    'label'    => (string)($it['label']    ?? ''),
                    'uri'      => (string)($it['uri']      ?? ''),
                    'uri_esv'  => (string)($it['uri_esv']  ?? ''),
                    'uri_mesh' => (string)($it['uri_mesh'] ?? ''),
                    'identifier' => (string)($it['identifier'] ?? ''),
                    'siren' => (string)($it['siren'] ?? ''),
                    'status' => (string)($it['status'] ?? ''),
                ];
            }
            return $out;
        };
        // langues
        $langs = $multilang ? ['fr' => 'Français', 'en' => 'English'] : ['' => ''];


?>
        <div class="card">
            <div class="input-group">

                <!--A vérifier -->
                <div class="d-flex justify-content-between">
                    <div class="d-flex hidden">
                        <?php if ($type == 'text' || $type == 'textarea' || $type == 'tags') { ?>
                            <span class="badge bg-warning mt-2">Incomplet</span>
                        <?php } ?>
                    </div>
                    <div class="d-flex hidden">
                        <?php if ($multilang): ?>
                            <ul class="nav nav-pills m-0">
                                <li class="nav-item langue">
                                    <a class="nav-link " data-lang="fr" href="#">FR</a>
                                </li>
                                <li class="nav-item langue">
                                    <a class="nav-link" data-lang="en" href="#">EN</a>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <?php

                foreach ($langs as $lng => $labelLng):
                    $suffix = $lng ? "_$lng" : "";
                    $hideClass = ($lng === 'en' && $multilang) ? 'd-none' : '';
                    $label = $lng == 'en' ? $label_en : $label_fr;
                    $placeholderSelect = $lng == 'en' ? "Select" : "Sélectionner";
                    $rawValue = $jsonRec[$name . $suffix] ?? null;

                    $reqAttr = $required ? 'required' : '';
                    $rawValueParent = $jsonParentStudy[$name . $suffix] ?? null;

                    $baseName = normalizeNameForStatus($name);
                    $statusKey = $baseName . $suffix . '_status';

                    $isChanged = $compareStudy[$statusKey] ?? false;

                    $opts_select   = $opts_v2($options, $lng);
                    $opts_radio    = $opts_v2($options, $lng);
                    $opts_checkbox = $opts_v2($options, $lng);
                    $opts_autocomplete = $opts_v2($options, $lng);
                ?>

                    <div class="d-flex justify-content-between">
                        <label class="form-label mb-2 ">
                            <span class="lang-label <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <?php echo ($lng == 'en') ? $e($label_en) : $e($label_fr); ?>
                                <?php if ($required): ?>
                                    <span class="text-danger">*</span>
                                <?php endif; ?>
                            </span>


                            <?php if (!empty($tooltip_fr) || !empty($tooltip_en)): ?>
                                <!-- Info-bulle -->
                                <span class="info-bulle lang-label <?php echo $hideClass; ?>"
                                    <?php if (!empty($options[$lng])) : ?>
                                    data-options='<?php echo esc_attr(json_encode($opts_v2($options, $lng))); ?>'
                                    <?php endif; ?>
                                    attr-lng="<?php echo $e($lng); ?>"
                                    data-text="<?php echo ($lng === 'en') ? $e($tooltip_en) : $e($tooltip_fr); ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                            <?php endif; ?>

                            <?php
                            $subtitleText = ($lng == 'en') ? $subtitle_en : $subtitle_fr;
                            if (!empty($subtitleText)): ?>

                                <span class="d-block st-subtitle lang-label <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                    <?php echo $e($subtitleText); ?>
                                </span>

                            <?php endif; ?>

                            <?php if ($isChanged): ?>
                                <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $e($lng); ?>">
                                    <i class="dashicons dashicons-edit"></i>
                                </span>
                            <?php endif; ?>
                        </label>
                        <?php
                        $normalizeValue = static function ($v) use (&$normalizeValue) {
                            if (is_array($v)) {
                                $res = [];
                                foreach ($v as $x) {
                                    $norm = $normalizeValue($x);
                                    if ($norm !== null && $norm !== '') $res[] = $norm;
                                }
                                return $res;
                            }
                            return $v;
                        }; ?>

                        <!-- changed value - compare -->
                        <?php
                        $val1 = $normalizeValue($rawValue);
                        $val2 = $normalizeValue($rawValueParent);

                        if (!json_values_equal($val1, $val2)):
                        ?>
                        <?php endif; ?>
                    </div>

                    <?php
                    switch ($type):

                        case 'select': ?>
                            <div class="lang-input <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <select
                                    class="form-select "

                                    name="<?php echo $e($name . $suffix); ?>">
                                    <option value="">-- <?php echo $placeholderSelect; ?> --</option>
                                    <?php
                                    $selected = (string)($rawValue ?? '');
                                    foreach ($opts_select as $opt):
                                        $val = $opt['label']; // submit label; switch to $opt['uri'] if you prefer
                                        $sel = ($val === $selected) ? 'selected' : '';
                                    ?>
                                        <option
                                            value="<?php echo $e($val); ?>" <?php echo $sel; ?>
                                            data-uri="<?php echo $e($opt['uri']); ?>"
                                            data-uri-esv="<?php echo $e($opt['uri_esv']); ?>"
                                            data-uri-mesh="<?php echo $e($opt['uri_mesh']); ?>">
                                            <?php echo $e($opt['label']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        <?php break;

                        case 'autocomplete':
                        ?>
                            <div class="lang-input autoSaisie <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <input type="autocomplete" class="form-control autocomplete <?php if ($same) echo 'same-data-autocomplete'; ?>"
                                    <?php echo $reqAttr  ?>
                                    attr-lng="<?php echo $e($lng); ?>"
                                    name="<?php echo $e($name . $suffix); ?>"
                                    placeholder="<?php echo $label; ?>">
                                <script type="application/json" class="dataList">
                                    <?php echo json_encode($opts_autocomplete, JSON_UNESCAPED_UNICODE); ?>
                                </script>

                            </div>


                        <?php break;

                        case 'select-multiple':
                            $selectedVals = $to_array($rawValue);
                            $selectedVals = array_map('strval', $selectedVals);
                            $uid = $name . $suffix . '_' . $lng;
                        ?>
                            <div class="lang-input select2-searchable tags-select-container <?php echo $hideClass; ?>"
                                attr-lng="<?php echo $e($lng); ?>"
                                data-tags-id="<?php echo $e($uid); ?>">

                                <!--  Selected items -->
                                <div class="tags-wrapper" data-tags-id="<?php echo $e($uid); ?>">
                                    <?php foreach ($opts_select as $opt):
                                        $val = $opt['label'];
                                        if (!in_array($val, $selectedVals, true)) continue;
                                    ?>
                                        <span class="tag-chip" data-value="<?php echo $e($val); ?>">
                                            <?php echo $e($opt['label']); ?>
                                            <span class="tag-chip-remove">&times;</span>
                                        </span>
                                    <?php endforeach; ?>
                                </div>

                                <!--  Searchable select (Select2) -->
                                <select
                                    <?php echo $reqAttr;  ?>
                                    class="form-select select2 select2-searchable js-nada-multi "

                                    attr-lng="<?php echo $e($lng); ?>"
                                    name="<?php echo $e($name . $suffix); ?>[]"
                                    multiple="multiple"
                                    data-tags-id="<?php echo $e($uid); ?>"
                                    data-placeholder="-- <?php echo $placeholderSelect; ?> --">
                                    <option value=""></option>
                                    <?php foreach ($opts_select as $opt):
                                        $val = $opt['label'];
                                        $sel = in_array($val, $selectedVals, true) ? 'selected' : '';
                                    ?>
                                        <option
                                            value="<?php echo $e($val); ?>" <?php echo $sel; ?>
                                            data-uri="<?php echo $e($opt['uri']); ?>"
                                            data-uri-esv="<?php echo $e($opt['uri_esv']); ?>"
                                            data-uri-mesh="<?php echo $e($opt['uri_mesh']); ?>">
                                            <?php echo $e($opt['label']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php
                            break;

                        case 'checkbox':
                        ?>
                            <div class="input-group-prefix lang-input row <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <?php
                                $selectedVals = array_map('strval', $to_array($rawValue));
                                $useUriLogic = ($name === 'stdyDscr/method/dataColl/collMode');

                                $hasParent = false;
                                foreach ($opts_checkbox as $opt) {
                                    if ($useUriLogic) {
                                        // Nouvelle logique basée sur l'URI pour le champ spécifique
                                        if (str_contains((string)$opt['uri'], '.')) {
                                            $hasParent = true;
                                            break;
                                        }
                                    } else {
                                        // Ancienne logique basée sur le label pour tous les autres champs
                                        if (str_contains($opt['label'], ':')) {
                                            $hasParent = true;
                                            break;
                                        }
                                    }
                                }

                                $firstCheckbox = true;
                                if (!$hasParent) {
                                    $columns = splitIntoVerticalColumns($opts_checkbox, 3);

                                    foreach ($columns as $column) {
                                        echo '<div class="col-md-4">';
                                        foreach ($column as $opt):
                                            $text = $opt['label'];
                                            $val  = $opt['label'];
                                            $id   = $name . '_' . $lng . '_' . preg_replace('~[^a-z0-9]+~i', '-', $text);
                                            $checked = in_array($val, $selectedVals, true) ? 'checked' : '';
                                ?>
                                            <div class="form-check ">
                                                <input class="form-check-input lang-input"

                                                    type="checkbox"
                                                    attr-lng="<?php echo $e($lng); ?>"
                                                    name="<?php echo $e($name . $suffix); ?>[]"
                                                    id="<?php echo $e($id); ?>"
                                                    value="<?php echo $e($val); ?>"
                                                    <?php echo $checked; ?>
                                                    data-id="<?php echo $e($opt['id']); ?>"
                                                    data-uri="<?php echo $e($opt['uri']); ?>"
                                                    data-uri-esv="<?php echo $e($opt['uri_esv']); ?>"
                                                    data-uri-mesh="<?php echo $e($opt['uri_mesh']); ?>"
                                                    <?php echo ($required && $firstCheckbox) ? 'required' : ''; ?>>
                                                <label class="form-check-label" for="<?php echo $e($id); ?>"><?php echo $e($text); ?></label>
                                            </div>
                                        <?php
                                            $firstCheckbox = false;
                                        endforeach;
                                        echo '</div>';
                                    }
                                } else {
                                    foreach ($opts_checkbox as $opt):
                                        $text = $opt['label'];
                                        $val  = $opt['label'];
                                        $uri  = (string)$opt['uri'];
                                        $id   = $name . '_' . $lng . '_' . preg_replace('~[^a-z0-9]+~i', '-', $text);
                                        $checked = in_array($val, $selectedVals, true) ? 'checked' : '';

                                        if ($useUriLogic) {
                                            // Traitement via l'URI pour le nouveau champ
                                            $isChild   = str_contains($uri, '.');
                                            $parentKey = $isChild ? explode('.', $uri)[0] : $uri;

                                            $dataAttributes = $isChild ? ' data-child="' . trim($e($parentKey)) . '"' : ' data-parent="' . trim($e($parentKey)) . '"';
                                        } else {
                                            // Traitement original via le label (conservé intact)
                                            $isChild   = str_contains($text, ':');
                                            $parentKey = $isChild ? explode(':', $text)[0] : null;

                                            $dataAttributes = $isChild ? ' data-child="' . trim($e($parentKey)) . '"' : ' data-parent="' . trim($e($text)) . '"';
                                        }

                                        $indentClass = $isChild ? 'col-md-12 child-checkbox' : 'col-md-12 parent-checkbox';
                                        ?>
                                        <div class="form-check <?php echo $indentClass; ?>">
                                            <input class="form-check-input lang-input"

                                                type="checkbox"
                                                attr-lng="<?php echo $e($lng); ?>"
                                                name="<?php echo $e($name . $suffix); ?>[]"
                                                id="<?php echo $e($id); ?>"
                                                value="<?php echo $e($val); ?>"
                                                <?php echo $checked; ?>
                                                <?php echo $isChild ? ' data-child="' . trim($e($parentKey)) . '"' : ' data-parent="' . trim($e($text)) . '"'; ?>
                                                data-id="<?php echo $e($opt['id']); ?>"
                                                data-uri="<?php echo $e($opt['uri']); ?>"
                                                data-uri-esv="<?php echo $e($opt['uri_esv']); ?>"
                                                data-uri-mesh="<?php echo $e($opt['uri_mesh']); ?>"
                                                <?php echo ($required && $firstCheckbox) ? 'required' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $e($id); ?>"><?php echo $e($text); ?></label>
                                        </div>
                                <?php
                                        $firstCheckbox = false;
                                    endforeach;
                                } ?>
                            </div>
                        <?php
                            break;


                        default: // text
                        ?>
                            <div class="lang-input <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">

                            </div>

                <?php
                    endswitch;

                endforeach; ?>
            </div>
        </div>
    <?php
    }
}



if (! function_exists('nada_renderInputGroup')) {
    /**
     * Génère un bloc input group multilingue ou non
     *
     * @param string $label_fr     Label du champ en Fr
     * @param string $label_en     Label du champ en EN
     * @param string $tooltip_fr     description du champ en Fr
     * @param string $tooltip_en     description du champ en EN
     * @param string $name      Nom du champ
     * @param string $type      text | textarea | select | select-multiple | date | radio | checkbox | email | tags | url | BtnRadio
     * @param array  $options   Options pour select/radio/checkbox (['valeur' => 'Libellé'])
     * @param bool   $multilang Multilingue (FR/EN) ou non
     * @param bool   $required  Champ obligatoire ?
     */
    function nada_renderInputGroup($label_fr, $label_en, $name, $type = 'text', $options = [], $multilang = true, $required = false, $tooltip_fr = null, $tooltip_en = null, $same = false, $subtitle_fr = null, $subtitle_en = null, $maxChars = null, $displayBtn = false, $validatorType = null)
    {

        // a mettre en comment
        //$required = false;
        global $jsonRec; // tableau associatif JSON (payload)
        global $studyDetails;
        global $jsonParentStudy;
        global $compareStudy;


        // Helper: normaliser une valeur en tableau (pour checkbox/select-multiple/tags)
        $to_array = static function ($v): array {
            if ($v === null || $v === '') return [];
            if (is_array($v)) return $v;
            return [$v];
        };
        // Helper: escape attr & html
        $e = static fn($s) => is_array($s)  ? '' : htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

        // langues
        $langs = $multilang ? ['fr' => 'Français', 'en' => 'English'] : ['' => ''];
    ?>
        <div class="card">
            <div class="input-group">
                <div class="d-flex justify-content-between">
                    <div class="d-flex hidden">
                        <?php if ($type == 'text' || $type == 'textarea' || $type == 'tags') { ?>
                            <span class="badge bg-warning mt-2">Incomplet</span>
                        <?php } ?>
                    </div>
                    <div class="d-flex hidden">
                        <?php if ($multilang): ?>
                            <ul class="nav nav-pills m-0">
                                <li class="nav-item langue">
                                    <a class="nav-link active" data-lang="fr" href="#">FR</a>
                                </li>
                                <li class="nav-item langue">
                                    <a class="nav-link" data-lang="en" href="#">EN</a>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <?php

                foreach ($langs as $lng => $labelLng):
                    $suffix = $lng ? "_$lng" : "";
                    $reqAttr = $required ? 'required' : '';
                    $hideClass = ($lng === 'en' && $multilang) ? 'd-none' : '';
                    $label = $lng == 'en' ? $label_en : $label_fr;
                    $placeholderSelect = $lng == 'en' ? "Select" : "Sélectionner";
                    $rawValue = $jsonRec[$name . $suffix] ?? null;

                    $baseName = normalizeNameForStatus($name);
                    $statusKey = $baseName . $suffix . '_status';

                    $isChanged = $compareStudy[$statusKey] ?? false;

                    if ($type == 'checkbox' && $options['fr']) {
                        $options_checkbox = $options[$lng];
                    } else {
                        $options_checkbox = $options;
                    }

                    if ($type == 'select' && $options['fr']) {
                        $options_select = $options[$lng];
                    } else {
                        $options_select = $options;
                    }

                    if (($type == 'radio' || $type == 'BtnRadio') && $options['fr']) {
                        $options_radio = $options[$lng];
                    } else {
                        $options_radio = $options;
                    }

                ?>

                    <div class="d-flex justify-content-between">
                        <label class="form-label mb-2 ">
                            <span class="lang-label <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <?php echo ($lng == 'en') ? $e($label_en) : $e($label_fr); ?>
                                <?php if ($required): ?>
                                    <span class="text-danger">*</span>
                                <?php endif; ?>
                            </span>

                            <?php if (!empty($tooltip_fr) || !empty($tooltip_en)): ?>
                                <!-- Info-bulle -->
                                <span class="info-bulle lang-label <?php echo $hideClass; ?>"
                                    <?php if (!empty($options[$lng])) : ?>
                                    data-options='<?php echo esc_attr(json_encode($options[$lng])); ?>'
                                    <?php endif; ?>
                                    attr-lng="<?php echo $e($lng); ?>"
                                    data-text="<?php echo ($lng === 'en') ? $e($tooltip_en) : $e($tooltip_fr); ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                            <?php endif; ?>

                            <?php
                            $subtitleText = ($lng == 'en') ? $subtitle_en : $subtitle_fr;
                            if (!empty($subtitleText)): ?>

                                <span class="d-block st-subtitle lang-label text-nowrap <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                    <?php echo $e($subtitleText); ?>
                                </span>

                            <?php endif; ?>

                            <?php if ($isChanged): ?>
                                <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $e($lng); ?>">
                                    <i class="dashicons dashicons-edit"></i>
                                </span>
                            <?php endif; ?>
                        </label>


                    </div>

                    <?php
                    switch ($type):
                        case 'textarea': ?>
                            <div class="lang-input <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <textarea
                                    <?php echo $reqAttr  ?>
                                    class="form-control <?php if ($same) echo 'same-data-textarea'; ?> "

                                    attr-lng="<?php echo $e($lng); ?>"
                                    name="<?php echo $e($name . $suffix); ?>"
                                    <?php echo $maxChars ? 'maxlength="' . (int)$maxChars . '"' : ''; ?>
                                    placeholder="<?php echo $label; ?>"><?php if ($jsonRec  && isset($jsonRec[$name . $suffix])) {
                                                                            echo $jsonRec[$name . $suffix];
                                                                        } ?></textarea>

                                <?php if ($maxChars): ?>
                                    <small class="text-muted d-block text-end mt-1 char-counter"
                                        attr-lng="<?php echo htmlspecialchars(string: $lng); ?>"
                                        data-max="<?php echo (int)$maxChars; ?>">
                                        <span class="remaining"><?php echo (int)$maxChars; ?></span>
                                        <?php echo ($lng === 'en') ? 'characters remaining' : 'caractères restants'; ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                        <?php break;

                        case 'select': ?>
                            <div class="lang-input <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <select
                                    <?php echo $reqAttr  ?>
                                    class="form-select "

                                    name="<?php echo $e($name . $suffix); ?>">

                                    <?php
                                    $selected = (string)($rawValue ?? '');
                                    // Auto-select si une seule option disponible et champ requis
                                    if (count($options_select) === 1 && $selected === '' && $required) {
                                        $selected = (string) array_key_first($options_select);
                                    }
                                    // Option vide si plusieurs choix possibles
                                    if (count($options_select) > 1):
                                    ?>
                                        <option value="">-- <?php echo $placeholderSelect; ?> --</option>
                                    <?php endif; ?>

                                    <?php foreach ($options_select as $val => $text): ?>
                                        <option value="<?php echo $e($val); ?>"
                                            <?php echo ((string)$val === $selected) ? 'selected' : ''; ?>>
                                            <?php echo $e($text); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        <?php break;

                        case 'select-multiple-cim11':
                            $selectedVals = $to_array($rawValue); ?>
                            <div class="blockCIM lang-input <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <div class="InputCIM">
                                    <select
                                        <?php echo $reqAttr  ?>
                                        class="form-select lang-input select2 <?php echo $hideClass; ?> "

                                        attr-lng="<?php echo $e($lng); ?>"
                                        name="<?php echo $e($name . $suffix); ?>[]"
                                        multiple="multiple"
                                        data-placeholder="-- Sélectionner --">
                                        <option value=""></option>
                                        <?php foreach ($options as $val => $text):
                                            $sel = in_array((string)$val, array_map('strval', $selectedVals), true) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $e($val); ?>" <?php echo $sel; ?>>
                                                <?php echo $e($text); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="">
                                    <div class="openModalBtn openModalCim" id="openModalCim" attr-lng="<?php echo $e($lng); ?>"><?= $lng == 'fr' ? 'Rechercher' : 'Search'; ?></div>
                                </div>

                            </div>
                        <?php break;


                        case 'tags':

                            $tags_value = null;
                            if ($jsonRec) {
                                $tags_value = $jsonRec[$name . $suffix];
                            }

                        ?>
                            <div class="tags-input-container lang-input <?php echo $hideClass; ?>" data-tags="<?php echo $e($name . $suffix); ?>" attr-lng="<?php echo $e($lng); ?>">
                                <div class="tags-wrapper" data-tags="<?php echo $e($name . $suffix); ?>"></div>
                                <input <?php echo $reqAttr  ?> type="text" class="tags-input " data-tags="<?php echo $e($name . $suffix); ?>"
                                    placeholder="<?= $lng == 'fr' ? 'Tapez et appuyez sur entrée ou point virgule' : 'Type and press Enter or semicolon'; ?>">
                                <input type="hidden" name="<?php echo $e(s: $name . $suffix); ?>[]" class="tags-hidden" data-tags="<?php echo $e($name . $suffix); ?>" value="<?php echo $e($tags_value); ?>">
                            </div>
                        <?php break;

                        case 'radio':

                            if (is_bool($rawValue)) {
                                $current = $rawValue ? "true" : "false";
                            } else {
                                $current = (string)($rawValue ?? '');
                            }

                        ?>
                            <div class="lang-input <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <?php
                                $firstRadio = true;
                                foreach ($options_radio as $val => $text):
                                    $id = $name . '_' . $lng . '_' . preg_replace('~[^a-z0-9]+~i', '-', (string)$val);
                                    $checked = ((string)$val === $current) ? 'checked' : '';
                                ?>
                                    <input type="radio" class="btn-check  <?php echo $hideClass; ?> "
                                        <?php echo ($required && $firstRadio) ? 'required' : ''; ?>
                                        attr-lng="<?php echo $e($lng); ?>"
                                        name="<?php echo $e($name . $suffix); ?>"
                                        id="<?php echo $e($id); ?>"
                                        value="<?php echo $e($val); ?>"
                                        <?php echo $checked; ?>>

                                    <label class="btn btn-outline-primary me-2" for="<?php echo $e($id); ?>">
                                        <?php echo $e($text); ?>
                                    </label>
                                <?php
                                    $firstRadio = false;
                                endforeach;
                                ?>
                            </div>
                        <?php
                            break;

                        case 'BtnRadio':
                        ?>
                            <div class="lang-input blockBtnRadio <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <input type="hidden" name="<?php echo $e($name . $suffix); ?>" value="">
                                <?php
                                foreach ($options_radio as $val => $text):
                                ?>
                                    <label class="btn btn-outline-primary me-2 pointer BtnRadio " attr-value="<?php echo $e($val); ?>">
                                        <?php echo $e($text); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php
                            break;

                        case 'email': ?>
                        <?php break;

                        case 'date': ?>

                        <?php break;
                        case 'checkbox':
                        ?>
                            <div class="input-group-prefix lang-input row <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <?php
                                $selectedVals = $to_array($rawValue);

                                $hasParent = false;

                                foreach ($options_checkbox as $text) {
                                    $text = preg_replace('/\s*:/u', ':', $text);
                                    if (str_contains($text, ':')) {
                                        $hasParent = true;
                                        break;
                                    }
                                }
                                $firstCheckbox = true;
                                $isRecruitmentSource = ($name === "stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType");
                                if (!$hasParent) {
                                    $columns = splitIntoVerticalColumns($options_checkbox, 3);
                                    foreach ($columns as $column) {
                                        echo '<div class="col-md-4">';
                                        foreach ($column as $val => $text) {
                                            $id = $name . '_' . $lng . '_' . preg_replace('~[^a-z0-9]+~i', '-', (string)$text);

                                            if ($isRecruitmentSource) {
                                                $checked = in_array((string)$text, array_map('strval', $selectedVals), true) ? 'checked' : '';
                                                ?>
                                                <div class="form-check ">
                                                    <input class="form-check-input lang-input" type="checkbox"
                                                           attr-lng="<?php echo $e($lng); ?>"
                                                           name="<?php echo $e($name . $suffix); ?>[]"
                                                           id="<?php echo $e($id); ?>"
                                                           value="<?php echo $e($text); ?>"
                                                           data-id="<?php echo $e($val); ?>" <?php echo $checked; ?>
                                                            <?php echo ($required && $firstCheckbox) ? 'required' : ''; ?>>
                                                    <label class="form-check-label" for="<?php echo $e($id); ?>">
                                                        <?php echo $e($text); ?>
                                                    </label>
                                                </div>
                                                <?php
                                            } else {
                                                $checked = '';
                                                foreach ($selectedVals as $selected) {
                                                    if (trim((string)$text) === trim((string)$selected)) {
                                                        $checked = 'checked';
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <div class="form-check ">
                                                    <input class="form-check-input lang-input" type="checkbox"
                                                           attr-lng="<?php echo $e($lng); ?>"
                                                           name="<?php echo $e($name . $suffix); ?>[]"
                                                           id="<?php echo $e($id); ?>"
                                                           value="<?php echo $e($val); ?>" <?php echo $checked; ?>
                                                            <?php echo ($required && $firstCheckbox) ? 'required' : ''; ?>>
                                                    <label class="form-check-label" for="<?php echo $e($id); ?>">
                                                        <?php echo $e($text); ?>
                                                    </label>
                                                </div>
                                                <?php
                                            }
                                            $firstCheckbox = false;
                                        }
                                        echo '</div>';
                                    }
                                } else {
                                    foreach ($options_checkbox as $val => $text):
                                        $id = $name . '_' . $lng . '_' . preg_replace('~[^a-z0-9]+~i', '-', (string)$text);
                                        $checked = in_array((string)$val, array_map('strval', $selectedVals), true) ? 'checked' : '';

                                        $isChild = str_contains($text, ':');
                                        $indentClass = $isChild ? 'col-md-12 child-checkbox' : 'col-md-12 parent-checkbox';
                                        $parentKey = $isChild ? explode(':', $text)[0] : null;
                                        ?>
                                        <div class="form-check <?php echo $indentClass; ?>">
                                            <input class="form-check-input lang-input" type="checkbox"
                                                   attr-lng="<?php echo $e($lng); ?>"
                                                   name="<?php echo $e($name . $suffix); ?>[]"
                                                   id="<?php echo $e($id); ?>"
                                                   value="<?php echo $e($val); ?>" <?php echo $checked; ?> <?php echo $isChild
                                                    ? ' data-child="' . trim($e($parentKey)) . '"'
                                                    : ' data-parent="' . trim($e($text)) . '"'; ?>
                                                    <?php echo ($required && $firstCheckbox) ? 'required' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $e($id); ?>">
                                                <?php echo $e($text); ?>
                                            </label>
                                        </div>
                                        <?php
                                        $firstCheckbox = false;
                                    endforeach;
                                }
                                ?>
                            </div>
                            <?php
                            break;
                        case 'checkbox':
                            ?>
                            <div class="input-group-prefix lang-input row <?php echo $hideClass; ?>"
                                 attr-lng="<?php echo $e($lng); ?>">
                                <?php
                                $selectedVals = $to_array($rawValue);

                                $hasParent = false;

                                foreach ($options_checkbox as $text) {
                                    $text = preg_replace('/\s*:/u', ':', $text);
                                    if (str_contains($text, ':')) {
                                        $hasParent = true;
                                        break;
                                    }
                                }
                                $firstCheckbox = true;

                                if (!$hasParent) {
                                    $columns = splitIntoVerticalColumns($options_checkbox, 3);
                                    foreach ($columns as $column) {
                                        echo '<div class="col-md-4">';
                                        foreach ($column as $val => $text) {
                                            $id = $name . '_' . $lng . '_' . preg_replace('~[^a-z0-9]+~i', '-', (string)$text);
                                            $checked = '';
                                            foreach ($selectedVals as $selected) {
                                                if (trim((string)$text) === trim((string)$selected)) {
                                                    $checked = 'checked';
                                                    break;
                                                }
                                            } ?>
                                            <div class="form-check ">
                                                <input class="form-check-input lang-input" type="checkbox"
                                                       attr-lng="<?php echo $e($lng); ?>"
                                                       name="<?php echo $e($name . $suffix); ?>[]"
                                                       id="<?php echo $e($id); ?>"
                                                       value="<?php echo $e($val); ?>" <?php echo $checked; ?>
                                                        <?php echo ($required && $firstCheckbox) ? 'required' : ''; ?>>
                                                <label class="form-check-label" for="<?php echo $e($id); ?>">
                                                    <?php echo $e($text); ?>
                                                </label>
                                            </div>
                                            <?php
                                            $firstCheckbox = false;
                                        }
                                        echo '</div>';
                                    }
                                } else {
                                    foreach ($options_checkbox as $val => $text):
                                        $id = $name . '_' . $lng . '_' . preg_replace('~[^a-z0-9]+~i', '-', (string)$text);
                                        $checked = in_array((string)$val, array_map('strval', $selectedVals), true) ? 'checked' : '';

                                        $isChild = str_contains($text, ':');
                                        $indentClass = $isChild ? 'col-md-12 child-checkbox' : 'col-md-12 parent-checkbox';
                                        $parentKey = $isChild ? explode(':', $text)[0] : null;
                                        ?>
                                        <div class="form-check <?php echo $indentClass; ?>">
                                            <input class="form-check-input lang-input" type="checkbox"
                                                   attr-lng="<?php echo $e($lng); ?>"
                                                   name="<?php echo $e($name . $suffix); ?>[]"
                                                   id="<?php echo $e($id); ?>"
                                                   value="<?php echo $e($val); ?>" <?php echo $checked; ?> <?php echo $isChild
                                                    ? ' data-child="' . trim($e($parentKey)) . '"'
                                                    : ' data-parent="' . trim($e($text)) . '"'; ?>
                                                    <?php echo ($required && $firstCheckbox) ? 'required' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $e($id); ?>">
                                                <?php echo $e($text); ?>
                                            </label>
                                        </div>
                                <?php
                                        $firstCheckbox = false;
                                    endforeach;
                                }
                                ?>
                            </div>
                        <?php
                            break;



                        case 'checkbox-multi':
                        ?>
                            <div class="input-group-prefix lang-input row <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <?php
                                $firstCheckbox = true;
                                foreach ($options as $val => $text):
                                    $id = $name . '_' . $lng . '_' . preg_replace('~[^a-z0-9]+~i', '-', (string)$text[$lng]);
                                    ?>
                                    <div class="form-check col-md-4  ">
                                        <input class="form-check-input lang-input"
                                            <?php echo ($required && $firstCheckbox) ? 'required' : ''; ?>

                                            type="checkbox"
                                            attr-lng="<?php echo $e($lng); ?>"
                                            name="<?php echo $e($name . $suffix); ?>[]"
                                            id="<?php echo $e($id); ?>"
                                            value="<?php echo $e($val); ?>">
                                        <label class="form-check-label" for="<?php echo $e($id); ?>">
                                            <?php echo $e($text[$lng]); ?>
                                        </label>
                                    </div>
                                <?php
                                    $firstCheckbox = false;
                                endforeach;
                                ?>
                            </div>
                        <?php
                            break;

                        case 'url':
                            // rien à faire ici
                            break;


                        default: // text
                        ?>
                            <div class="lang-input <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <input type="text"
                                    <?php
                                    if (!empty($validatorType)) {
                                        echo 'data-validator="' . htmlspecialchars($validatorType, ENT_QUOTES) . '"';
                                    }


                                    $value = null;

                                    // Vérifie que $jsonRec existe et que l'élément demandé existe
                                    if ($jsonRec && isset($jsonRec[$name . $suffix]) && !is_array($jsonRec[$name . $suffix])) {
                                        $value = $jsonRec[$name . $suffix];
                                    }

                                    ?>
                                    <?php echo $reqAttr  ?>
                                    class="form-control <?php if ($same) echo 'same-data'; ?>  "

                                    attr-lng="<?php echo $e($lng); ?>"
                                    name="<?php echo $e($name . $suffix); ?>"
                                    placeholder="<?php echo $label; ?>"
                                    value="<?= htmlspecialchars($value ?? '', ENT_QUOTES) ?>"
                                    <?php echo $maxChars ? 'maxlength="' . (int)$maxChars . '"' : ''; ?>>

                                <?php if ($maxChars): ?>
                                    <small class="text-muted d-block text-end mt-1 char-counter"
                                        attr-lng="<?php echo htmlspecialchars($lng); ?>"
                                        data-max="<?php echo (int)$maxChars; ?>">
                                        <span class="remaining"><?php echo (int)$maxChars; ?></span>
                                        <?php echo ($lng === 'en') ? 'characters remaining' : 'caractères restants'; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                <?php
                    endswitch;
                endforeach; ?>

                <?php

                if ($type == 'url') {
                    $cleanHost = '';
                    if (!empty($jsonRec)) {
                        $key = $name . ($multilang ? '_fr' : '');
                        $rawVal = $jsonRec[$key] ?? '';
                        $cleanHost = preg_replace('~^https?://~i', '', $rawVal);
                    }
                ?>
                    <div class="input-group-prefix one-input">
                        <span class="input-group-text">http://</span>
                        <input type="text"
                            attr-placeholder-fr="<?php echo $label_fr; ?>"
                            attr-placeholder-en="<?php echo $label_en; ?>"
                            class="form-control prefixInput"
                            placeholder="<?php echo $label_fr; ?>"
                            <?php echo $reqAttr;  ?>
                            name="<?php echo $e($name); ?>"
                            value="<?php echo $e($cleanHost); ?>">
                    </div>
                <?php }
                if ($type == 'email') { ?>
                    <div class="one-input">
                        <input type="email"
                            <?php echo $reqAttr;  ?>
                            <?php
                            if (!empty($validatorType)) {
                                echo 'data-validator="' . htmlspecialchars($validatorType, ENT_QUOTES) . '"';
                            }
                            ?>

                            attr-placeholder-fr="<?php echo $label_fr; ?>"
                            attr-placeholder-en="<?php echo $label_en; ?>"
                            class="form-control"
                            placeholder="<?php echo $label_fr; ?>"
                            name="<?php echo $e($name); ?>"
                            value>
                    </div>
                <?php }
                if ($type == 'date') { ?>
                    <div class="one-input">
                        <input type="date"
                            <?php echo $reqAttr  ?>
                            attr-placeholder-fr="<?php echo $label_fr; ?>"
                            attr-placeholder-en="<?php echo $label_en; ?>"
                            class="form-control date-check"
                            placeholder="<?php echo $label_fr; ?>"
                            name="<?php echo $e($name); ?>">
                        <span class="error-date text-danger" style="display:none;"><?php echo "Date invalide."; ?></span>
                    </div>
                <?php }
                ?>
            </div>
        </div>
<?php
    }
}
?>
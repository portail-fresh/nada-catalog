<?php

$current_language = pll_current_language() ?? 'fr';

function formatCreatedDate($createdRaw, $lang, ?string $type = 'datetime')
{
    if (empty($createdRaw)) {
        return '';
    }

    try {
        return format_date($createdRaw, $lang, $type);
    } catch (Exception $e) {
        return '';
    }
}

function formatLangText($lang, $currentLanguage)
{
    $map = [
        'fr' => ['fr' => 'français', 'en' => 'anglais'],
        'en' => ['fr' => 'French',   'en' => 'English']
    ];

    return $map[$currentLanguage][$lang] ?? $lang;
}

function formatBooleanText($value, $currentLanguage)
{
    $map = [
        'fr' => [true => 'oui', false => 'non'],
        'en' => [true => 'yes', false => 'no'],
    ];

    $boolValue = (bool) $value;

    return $map[$currentLanguage][$boolValue] ?? ($currentLanguage === 'fr' ? 'non' : 'no');
}
if (!empty($datasets)) {
    foreach ($datasets as $dataset) {

        // variables claires pour le template
        $title  = esc_html($dataset['title'] ?? '');
        $abbreviation  = esc_html($dataset['abbreviation'] ?? '');
        $status = esc_html($dataset['status_key'] ?? '');

        $createdFullname = esc_html($dataset['user_fullname'] ?? '');
        $modificatorFullname = esc_html($dataset['modificator_fullname'] ?? '');

        $pi_name = esc_html($dataset['link_technical'] ?? '');
        $identifier = esc_html($dataset['idno'] ?? '');

        $hasChildren = is_array($dataset['children']) && count($dataset['children']) > 0;

        //Si l’étude publiée a une version étude 'en attente de publication', le collapse doit être ouvert par défaut.
        $show = $dataset['has_pending_Child'] ? 'show' : '';
?>
        <div class="col-md-12 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body accordion">
                    <div class=" accordion-item d-block">
                        <div class="accordion-header">
                            <div class="row parent-item">
                                <div class="col-md-12 mb-4 contentHeaderCard align-items-center">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <button class="accordion-button collapsed <?= !$hasChildren ? 'no-child' : '' ?>"
                                                title="<?= htmlspecialchars($title) ?>"
                                                <?= $hasChildren ? 'data-bs-toggle="collapse" data-bs-target="#collapse' . $dataset['id'] . '"' : '' ?>>

                                                <span class="titleBtnCollapse d-flex align-items-center w-100 fw-bold"
                                                    data-bs-placement="top"
                                                    title=" <?= __('technicalMetadataInfoBulle', 'nada-id'); ?>">

                                                    <span class="text-truncate"
                                                        data-bs-placement="top"
                                                        title="<?= htmlspecialchars_decode($title, ENT_QUOTES) ?>">
                                                        <?= $title ?>
                                                    </span>

                                                    <span class="open-metadata"
                                                        data-meta='<?= json_encode([
                                                                        'identifier'           => $dataset['technical_data']['identifier'] ?? '',
                                                                        'iDSchema'             => $dataset['technical_data']['iDSchema'] ?? '',
                                                                        'provenance'           => $dataset['technical_data']['provenance'] ?? '',
                                                                        'versionLang'          => formatLangText($dataset['technical_data']['versionLang'] ?? '', $current_language),
                                                                        'originLang'           => formatLangText($dataset['technical_data']['originLang'] ?? '', $current_language),
                                                                        'creationDate'         => formatCreatedDate($dataset['technical_data']['creationDate'] ?? '', $current_language),
                                                                        'lastUpdatedAuto'      => formatCreatedDate($dataset['technical_data']['lastUpdatedAuto'] ?? '', $current_language, 'date_only'),
                                                                        'lastUpdatedManual'    => formatCreatedDate($dataset['technical_data']['lastUpdatedManual'] ?? '', $current_language, 'date_only'),
                                                                        'respValidation'       => $dataset['technical_data']['respValidation'] ?? '',
                                                                        'autoTranslation'      => formatBooleanText($dataset['technical_data']['autoTranslation'] ?? false, $current_language),
                                                                        'status'               => strip_tags(render_status_html($dataset['link_indicator']  ?? 'draft', $lang)), //$dataset['technical_data']['status'] ?? '',
                                                                        'contributorName'      => $dataset['technical_data']['contributorName'] ?? '',
                                                                        'contributorAffiliation' => $dataset['technical_data']['contributorAffiliation'] ?? ''
                                                                    ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                                        <span class="dashicons dashicons-info"></span>
                                                    </span>
                                                </span>

                                            </button>
                                            <!-- abbreviation -->
                                            <span class="titleBtnCollapse text-truncate p-0"
                                                data-bs-placement="top"
                                                title="<?= htmlspecialchars_decode($abbreviation, ENT_QUOTES) ?>">
                                                <?= $abbreviation ?>
                                            </span>
                                        </div>
                                        <div class="col-md-4 statusHtml">
                                            <span class=""><?= render_status_html($status ?? 'draft', $lang); ?></span>
                                        </div>
                                        <div class="col-md-1">
                                            <span class="d-flex justify-content-end align-items-center h-100">
                                                <span class="mb-0"><?= generate_publish_switch_inline($dataset); ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 contentBodyCard">
                                    <div class="row">
                                        <div class="col-md-5 mb-2">
                                            <span class="fw-bold"><?php echo __('listUserKey', 'nada-id'); ?>: </span>
                                            <?= $createdFullname ?>
                                        </div>
                                        <div class="col-md-5  mb-2">
                                            <span class="fw-bold"><?php echo __('listLastModificator', 'nada-id'); ?>: </span>
                                            <?= $modificatorFullname ?>
                                        </div>
                                        <div class="col-md-5 mb-2">
                                            <span class="fw-bold"><?php echo __('catalogPI', 'nada-id'); ?>: </span>
                                            <?= $pi_name ?>
                                        </div>

                                        <div class="col-md-5 mb-2">
                                            <span class="fw-bold"><?php echo __('refListIdentifier', 'nada-id'); ?>: </span>
                                            <?= $identifier ?>
                                        </div>

                                        <div class="col-md-5 mb-2">
                                            <span class="fw-bold"><?php echo __('listCreationDateKey', 'nada-id'); ?>: </span>
                                            <?= formatCreatedDate($dataset['created'], $current_language) ?>
                                        </div>

                                        <div class="col-md-5 mb-2">
                                            <span class="fw-bold"><?php echo __('listModificationDateKey', 'nada-id'); ?>: </span>
                                            <?= formatCreatedDate($dataset['modificationDate'], $current_language) ?>
                                        </div>
                                        <div class="col-md-12">

                                            <div class="actionListcard">
                                                <span><?= generate_actions_inline($dataset); ?> </span>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="actionListcard">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="collapse<?= $dataset['id'] ?>" class="accordion-collapse collapse <?= htmlspecialchars($show) ?> py-4">
                            <div class="accordion-body p-0">
                                <?php if ($hasChildren) { ?>
                                    <div class="childs-item">
                                        <?php foreach ($dataset['children'] as $index => $child) { ?>

                                            <?php
                                            $totalChildren = count($dataset['children']);
                                            $reversedIndex = $totalChildren - $index;
                                            ?>

                                            <div class="child-item">
                                                <div class='card row m-0 card-etude-liste'>

                                                    <div class="header-etude-liste">
                                                        <div class="">
                                                            <strong>
                                                                <span class="badge">V-<?= $reversedIndex ?></span>
                                                                <?php echo esc_html($child['title'] ?? '') ?>
                                                            </strong>

                                                            <span class="titleBtnCollapse fw-bold cursor-pointer"
                                                                data-bs-placement="top"
                                                                title=" <?= __('technicalMetadata', 'nada-id'); ?>">
                                                                <span class="open-metadata"
                                                                    data-meta='<?= json_encode([
                                                                                    'identifier'           => $child['technical_data']['identifier'] ?? '',
                                                                                    'iDSchema'             => $child['technical_data']['iDSchema'] ?? '',
                                                                                    'provenance'           => $child['technical_data']['provenance'] ?? '',
                                                                                    'versionLang'          => formatLangText($child['technical_data']['versionLang'] ?? '', $current_language),
                                                                                    'originLang'           => formatLangText($child['technical_data']['originLang'] ?? '', $current_language),
                                                                                    'creationDate'         => formatCreatedDate($child['technical_data']['creationDate'] ?? '', $current_language),
                                                                                    'lastUpdatedAuto'      => formatCreatedDate($child['technical_data']['lastUpdatedAuto'] ?? '', $current_language, 'date_only'),
                                                                                    'lastUpdatedManual'    => formatCreatedDate($child['technical_data']['lastUpdatedManual'] ?? '', $current_language, 'date_only'),
                                                                                    'respValidation'       => $child['technical_data']['respValidation'] ?? '',
                                                                                    'autoTranslation'      => formatBooleanText($child['technical_data']['autoTranslation'] ?? false, $current_language),
                                                                                    'status'               => strip_tags(render_status_html($child['link_indicator']  ?? 'draft', $lang)), //$dataset['technical_data']['status'] ?? '',
                                                                                    'contributorName'      => $child['technical_data']['contributorName'] ?? '',
                                                                                    'contributorAffiliation' => $child['technical_data']['contributorAffiliation'] ?? ''
                                                                                ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                                                    <span class="dashicons dashicons-info"></span>
                                                                </span>

                                                            </span>
                                                            <!-- abbreviation -->
                                                            <span class="titleBtnCollapse text-truncate p-0 d-block">
                                                                <?php echo esc_html($child['abbreviation'] ?? '') ?>
                                                            </span>
                                                        </div>

                                                        <div class=""><?= render_status_html($child['status_key']  ?? 'draft', $lang); ?></div>
                                                    </div>

                                                    <div class="col-md-12 body-etude-liste">
                                                        <div class="row">
                                                            <div class="col-md-10">
                                                                <div class="row">
                                                                    <div class=" col-md-6">
                                                                        <strong><?php echo __('listUserKey', 'nada-id'); ?></strong>
                                                                        <?= esc_html($child['user_fullname']) ?>
                                                                    </div>

                                                                    <div class=" col-md-6">
                                                                        <strong><?php echo __('listLastModificator', 'nada-id'); ?></strong>
                                                                        <?= esc_html($child['modificator_fullname']) ?>
                                                                    </div>

                                                                    <div class=" col-md-6">
                                                                        <strong><?php echo __('catalogPI', 'nada-id'); ?></strong>
                                                                        <?= esc_html($child['pi_data']['pi_name']) ?>
                                                                    </div>

                                                                    <div class=" col-md-6">
                                                                        <strong><?php echo __('refListIdentifier', 'nada-id'); ?></strong>
                                                                        <?= esc_html($child['idno']) ?>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <strong><?php echo __('listCreationDateKey', 'nada-id'); ?></strong>
                                                                        <?php echo formatCreatedDate($child['created'] ?? '', $current_language) ?>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <strong><?php echo __('listModificationDateKey', 'nada-id'); ?></strong>
                                                                        <?php echo formatCreatedDate($child['modificationDate'] ?? '', $current_language) ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 d-flex align-items-end flex-column">
                                                                <?= generate_publish_switch_inline($child); ?>
                                                            </div>


                                                            <div class="col-md-12 py-2">
                                                                <div class="d-flex justify-content-end"><?= generate_actions_inline($child); ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
<?php
    }
} else {
    echo '<p class="text-center">Aucun résultat</p>';
}
?>
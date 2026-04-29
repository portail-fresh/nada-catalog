<?php

/**
 * Template: referentiel-details.php
 * Affiche les détails d'un référentiel et ses items
 */
if (!defined('ABSPATH')) exit;
$referentiel_id = $referentiel['id'] ?? 0;
?>

<div id="page-ref-details">
    <div class="ref-details-header">
        <div class="header-content-wrapper">
            <h1><?php echo esc_html($referentiel['referentiel_name']); ?></h1>
            <div class="header-actions">
                <button type="button"
                    class="link-with-icon link-add"
                    id="add-edit-item-btn"
                    data-ref-id="<?php echo esc_attr($referentiel_id); ?>">
                    <i class="fa fa-plus"></i> <?php echo __('titleModelAddItem', 'nada-id'); ?>
                </button>
                <a href="javascript:history.back()" class="link-with-icon link-add">
                    <i class="fa fa-arrow-left"></i> <?php echo __('btnBack', 'nada-id'); ?>
                </a>
            </div>
        </div>
    </div>

    <?php
    $columnStatus = [
        'hasUri' => false,
        'hasUriEsv' => false,
        'hasUriMesh' => false,
        'hasIdentifier' => false,
        'hasSiren' => false,
        'hasStatus' => false,
    ];

    if (!empty($items)) {
        foreach ($items as $item) {
            if (!$columnStatus['hasUri'] && !empty($item['uri'])) $columnStatus['hasUri'] = true;
            if (!$columnStatus['hasUriEsv'] && !empty($item['uri_esv'])) $columnStatus['hasUriEsv'] = true;
            if (!$columnStatus['hasUriMesh'] && !empty($item['uri_mesh'])) $columnStatus['hasUriMesh'] = true;
            if (!$columnStatus['hasIdentifier'] && !empty($item['identifier'])) $columnStatus['hasIdentifier'] = true;
            if (!$columnStatus['hasSiren'] && !empty($item['siren'])) $columnStatus['hasSiren'] = true;
            if (!$columnStatus['hasStatus'] && !empty($item['status'])) $columnStatus['hasStatus'] = true;

            // Si toutes les colonnes sont trouvées, arrête la boucle
            if (array_sum($columnStatus) === 6) break;
        }
    }

    $totalColumns = 5;
    if ($columnStatus['hasUri']) $totalColumns++;
    if ($columnStatus['hasUriEsv']) $totalColumns++;
    if ($columnStatus['hasUriMesh']) $totalColumns++;
    if ($columnStatus['hasIdentifier']) $totalColumns++;
    if ($columnStatus['hasSiren']) $totalColumns++;
    if ($columnStatus['hasStatus']) $totalColumns++;
    ?>

    <div class="ref-items-section">
        <table border="1" cellpadding="5" class="table" cellspacing="0" id="ref-items-table">
            <thead>
                <tr>
                    <th><?php echo __('refListNameFr', 'nada-id'); ?></th>
                    <th><?php echo __('refListNameEn', 'nada-id'); ?></th>
                    <th><?php echo __('refListDescriptionFr', 'nada-id'); ?></th>
                    <th><?php echo __('refListDescriptionEn', 'nada-id'); ?></th>
                    <?php if ($columnStatus['hasUri']): ?>
                        <th>URI</th>
                    <?php endif; ?>
                    <?php if ($columnStatus['hasUriEsv']): ?>
                        <th>URI_ESV</th>
                    <?php endif; ?>
                    <?php if ($columnStatus['hasUriMesh']): ?>
                        <th>URI_MESH</th>
                    <?php endif; ?>
                    <?php if ($columnStatus['hasIdentifier']): ?>
                        <th><?php echo __('refListIdentifier', 'nada-id'); ?></th>
                    <?php endif; ?>
                    <?php if ($columnStatus['hasSiren']): ?>
                        <th>SIREN</th>
                    <?php endif; ?>
                    <?php if ($columnStatus['hasStatus']): ?>
                        <th><?php echo __('refListStatus', 'nada-id'); ?></th>
                    <?php endif; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="<?php echo $totalColumns; ?>" class="nada-no-results">
                            <?php echo __('emptyRefData', 'nada-id'); ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr data-item-id="<?php echo esc_attr($item['id']); ?>">
                            <td><?php echo esc_html($item['label_fr'] ?? ''); ?></td>
                            <td><?php echo esc_html($item['label_en'] ?? ''); ?></td>
                            <td><?php echo esc_html($item['desc_fr'] ?? ''); ?></td>
                            <td><?php echo esc_html($item['desc_en'] ?? ''); ?></td>

                            <?php if ($columnStatus['hasUri']): ?>
                                <td><?php echo esc_html($item['uri'] ?? ''); ?></td>
                            <?php endif; ?>

                            <?php if ($columnStatus['hasUriEsv']): ?>
                                <td><?php echo esc_html($item['uri_esv'] ?? ''); ?></td>
                            <?php endif; ?>

                            <?php if ($columnStatus['hasUriMesh']): ?>
                                <td><?php echo esc_html($item['uri_mesh'] ?? ''); ?></td>
                            <?php endif; ?>

                            <?php if ($columnStatus['hasIdentifier']): ?>
                                <td><?php echo esc_html($item['identifier'] ?? ''); ?></td>
                            <?php endif; ?>

                            <?php if ($columnStatus['hasSiren']): ?>
                                <td><?php echo esc_html($item['siren'] ?? ''); ?></td>
                            <?php endif; ?>

                            <?php if ($columnStatus['hasStatus']): ?>
                                <td><?php echo esc_html($item['status'] ?? ''); ?></td>
                            <?php endif; ?>

                            <td class="nada-actions">
                                <span class="nada-edit"
                                    data-ref-id="<?php echo esc_attr($referentiel_id); ?>"
                                    data-id="<?php echo esc_attr($item['id']); ?>"
                                    data-label-fr="<?php echo esc_attr($item['label_fr'] ?? ''); ?>"
                                    data-label-en="<?php echo esc_attr($item['label_en'] ?? ''); ?>"
                                    data-desc-fr="<?php echo esc_attr($item['desc_fr'] ?? ''); ?>"
                                    data-desc-en="<?php echo esc_attr($item['desc_en'] ?? ''); ?>"
                                    data-uri="<?php echo esc_attr($item['uri'] ?? ''); ?>"
                                    data-uri-esv="<?php echo esc_attr($item['uri_esv'] ?? ''); ?>"
                                    data-uri-mesh="<?php echo esc_attr($item['uri_mesh'] ?? ''); ?>"
                                    data-identifier="<?php echo esc_attr($item['identifier'] ?? ''); ?>"
                                    data-siren="<?php echo esc_attr($item['siren'] ?? ''); ?>"
                                    data-status="<?php echo esc_attr($item['status'] ?? ''); ?>"
                                    style="cursor:pointer; color:#2980b9; margin-right:10px;">
                                    <i class="fa fa-edit"></i>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
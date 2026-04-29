<?php
if (!defined('ABSPATH')) exit;
//vérifier si l'utilisateur est admin
$is_admin_fresh = current_user_can('admin_fresh');
?>
<div class="technical-documentation-article">
    <header class="technical-documentation-article-header">
        <h1>
            <?php echo esc_html($vocab_title); ?>
        </h1>
    </header>
    <p class="technical-documentation-content">
        <?php echo wp_kses_post($vocab_description); ?>
        <?php

        $v_obj = (object) $vocab;
        // Modification : Bouton visible uniquement pour les admins
        if ($is_admin_fresh):
        ?>
            <button type="button"
                class="btn-edit-vd-field"
                title="<?php echo __('editActionVD', 'nada-id'); ?>"
                data-id="<?php echo esc_attr($v_obj->id); ?>"
                data-ref-id="<?php echo esc_attr($v_obj->id ?? ''); ?>"
                data-label-fr="<?php echo esc_attr($v_obj->label_fr); ?>"
                data-label-en="<?php echo esc_attr($v_obj->label_en); ?>"
                data-desc-fr="<?php echo esc_attr($v_obj->description_fr); ?>"
                data-desc-en="<?php echo esc_attr($v_obj->description_en); ?>">
                <i class="fa fa-edit"></i> <?php echo __('editActionVD', 'nada-id'); ?>
            </button>
        <?php endif; ?>

    </p>

    <?php if (!empty($items)): ?>
        <?php
        $has_uri = false;
        $has_uri_mesh = false;
        $has_uri_esv = false;

        foreach ($items as $item) {
            if (!empty($item['uri']) && $item['uri'] !== 'NULL') $has_uri = true;
            if (!empty($item['uri_mesh']) && $item['uri_mesh'] !== 'NULL') $has_uri_mesh = true;
            if (!empty($item['uri_esv']) && $item['uri_esv'] !== 'NULL') $has_uri_esv = true;
        }
        ?>

        <div class="technical-documentation-table-container table-container">
            <table class="technical-documentation-vocab-table vocab-table td-table">
                <thead>
                    <tr>
                        <th><?php echo __('frenchTermVD', 'nada-id'); ?></th>
                        <th><?php echo __('englishTermVD', 'nada-id'); ?></th>
                        <th><?php echo __('frenchDescVD', 'nada-id'); ?></th>
                        <th><?php echo __('englishDescVD', 'nada-id'); ?></th>
                        <?php if ($has_uri): ?><th>URI</th><?php endif; ?>
                        <?php if ($has_uri_mesh): ?><th>URI MeSH</th><?php endif; ?>
                        <?php if ($has_uri_esv): ?><th>URI ESV</th><?php endif; ?>
                        <?php if ($is_admin_fresh): ?>
                            <th class="col-actions">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><strong><?php echo esc_html($item['label_fr'] ?? ''); ?></strong></td>
                            <td><strong><?php echo esc_html($item['label_en'] ?? ''); ?></strong></td>
                            <td><?php echo wp_kses_post($item['desc_fr'] ?? ''); ?></td>
                            <td><?php echo wp_kses_post($item['desc_en'] ?? ''); ?></td>
                            <?php if ($has_uri): ?>
                                <td><?php render_uri_link($item['uri'] ?? ''); ?></td>
                            <?php endif; ?>

                            <?php if ($has_uri_mesh): ?>
                                <td><?php render_uri_link($item['uri_mesh'] ?? ''); ?></td>
                            <?php endif; ?>

                            <?php if ($has_uri_esv): ?>
                                <td><?php render_uri_link($item['uri_esv'] ?? ''); ?></td>
                            <?php endif; ?>
                            <?php if ($is_admin_fresh): ?>
                                <td class="col-actions nada-actions">
                                    <span class="nada-edit"
                                        data-id="<?php echo esc_attr($item['id']); ?>"
                                        data-ref-id="<?php echo esc_attr($v_obj->id ?? ''); ?>"
                                        data-label-fr="<?php echo esc_attr($item['label_fr'] ?? ''); ?>"
                                        data-label-en="<?php echo esc_attr($item['label_en'] ?? ''); ?>"
                                        data-desc-fr="<?php echo esc_attr($item['desc_fr'] ?? ''); ?>"
                                        data-desc-en="<?php echo esc_attr($item['desc_en'] ?? ''); ?>"
                                        style="cursor:pointer; color:#2980b9; margin-right:10px;">
                                        <i class="fa fa-edit"></i>
                                    </span>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>
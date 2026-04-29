<?php
if (!defined('ABSPATH')) exit;
//vérifier si l'utilisateur est admin
$is_admin_fresh = current_user_can('admin_fresh');
?>
<div class="technical-documentation-article">
    <header class="technical-documentation-article-header">
        <h1><?php echo esc_html($section_title); ?></h1>
    </header>

    <div class="td-table-container table-container">
        <table class="technical-documentation-metadata-table td-table">
            <thead>
                <tr>
                    <th><?php echo __('elementIdVDSchema', 'nada-id'); ?></th>
                    <th><?php echo __('variableNameVDSchema', 'nada-id'); ?></th>
                    <th><?php echo __('labelFRVDSchema', 'nada-id'); ?></th>
                    <th><?php echo __('descFRVDSchema', 'nada-id'); ?></th>
                    <th><?php echo __('labelENVDSchema', 'nada-id'); ?></th>
                    <th><?php echo __('descENVDSchema', 'nada-id'); ?></th>
                    <th><?php echo __('variableTypeVDSchema', 'nada-id'); ?></th>
                    <th>Min Occur</th>
                    <th>Max Occur</th>
                    <?php if ($is_admin_fresh): ?>
                        <th class="col-actions">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($metadata_fields as $field): ?>
                    <tr>
                        <td><?php echo esc_html($field['element_id']); ?></td>
                        <td><strong><?php echo esc_html($field['variable_name']); ?></strong></td>
                        <td><?php echo esc_html($field['label_fr'] ?: ''); ?></td>
                        <td><?php echo wp_kses_post($field['description_fr'] ?: ''); ?></td>
                        <td><?php echo esc_html($field['label_en'] ?: ''); ?></td>
                        <td><?php echo wp_kses_post($field['description_en'] ?: ''); ?></td>
                        <td><?php echo esc_html($field['variable_type']); ?></td>
                        <td><?php echo esc_html($field['min_occur']); ?></td>
                        <td><?php echo esc_html($field['max_occur']); ?></td>
                        <?php if ($is_admin_fresh): ?>
                            <td class="col-actions">
                                <button class="btn-edit-vd-field"
                                    data-id="<?php echo esc_attr($field['id']); ?>"
                                    data-label-fr="<?php echo esc_attr($field['label_fr'] ?? ''); ?>"
                                    data-label-en="<?php echo esc_attr($field['label_en'] ?? ''); ?>"
                                    data-desc-fr="<?php echo esc_attr($field['description_fr'] ?? ''); ?>"
                                    data-desc-en="<?php echo esc_attr($field['description_en'] ?? ''); ?>">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
if (!defined('ABSPATH')) exit;
?>
<div id="page-ref-list-admin">
    <table border="1" cellpadding="2" class="table" cellspacing="0" id="ref-list-admin">
        <thead>
            <tr>
                <th><?php echo __('refName', 'nada-id'); ?></th>
                <th>Actions</th>
        </thead>
        <tbody>
            <?php if (empty($referentiels)): ?>
                <tr>
                    <td colspan="2" class="nada-no-results"><?php echo __('emptyRefData', 'nada-id'); ?> </td>
                </tr>
            <?php else: ?>
                <?php foreach ($referentiels as $ref): ?>
                    <tr class="<?php echo !$ref['is_enabled'] ? 'nada-archived' : ''; ?>">
                        <td><?php echo esc_html($ref['referentiel_name']); ?></td>
                        <td class="nada-actions">
                            <span class="nada-edit"
                                data-id="<?php echo esc_attr($ref['id']); ?>">
                                <a href="<?php echo ($current_language === 'fr')
                                                ? '/referentiel-detail/' . esc_html($ref['id'])
                                                : '/en/repository-detail/' . esc_html($ref['id']); ?>">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
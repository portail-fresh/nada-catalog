<!-- Modal de confirmation -->
<div id="validateActionModal" class="nada-modal" style="display: none;">
    <div class="nada-modal-overlay"></div>
    <div class="nada-modal-wrapper">
        <!-- Loader -->
        <div id="loader" class="nada-modal-content" style="display:none">
            <div class="nada-modal-body text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-3"><?php echo __('loading', 'nada-id'); ?></div>
            </div>
        </div>

        <!-- Message de succès -->
        <div id="success-message" class="nada-modal-content" style="display:none">
            <div class="nada-modal-body text-center">
                <span class="dashicons dashicons-yes-alt success-icon" style="font-size: 48px; color: #28a745;"></span>
                <div class="message-success-response mt-3"></div>
            </div>
        </div>

        <!-- Contenu principal de confirmation -->
        <div class="nada-modal-content" id="confirmation-content">
            <div class="nada-modal-header">
                <h3>Confirmation</h3>
                <button type="button" class="nada-modal-close cancel-action">&times;</button>
            </div>
            <div class="nada-modal-body">
                <p><?php echo __('textConfirmInsModal', 'nada-id'); ?> <strong><span class="institution-name"></span></strong> ?</p>
            </div>
            <div class="nada-modal-footer">
                <button type="button" class="button cancel-action"><?php echo __('btnCancel', 'nada-id'); ?></button>
                <button type="button" class="button button-primary confirm-action">
                    <span class="spinner-border spinner-border-sm d-none"></span>
                    <span class="btn-text"><?php echo __('btnConfirm', 'nada-id'); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>
<div id="addEditInstitutionModal" class="nada-modal" style="display: none;">
    <div class="nada-modal-overlay"></div>
    <div class="nada-modal-wrapper">
        <!-- Loader -->
        <div id="loader-add-edit" class="nada-modal-content" style="display:none">
            <div class="nada-modal-body text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-3"><?php echo __('loading', 'nada-id'); ?></div>
            </div>
        </div>

        <!-- Message de succès -->
        <div id="success-message-edit" class="nada-modal-content" style="display:none">
            <div class="nada-modal-body text-center">
                <span class="dashicons dashicons-yes-alt success-icon" style="font-size: 48px; color: #28a745;"></span>
                <div class="message-success-response mt-3"></div>
            </div>
        </div>

        <!-- Contenu principal du formulaire -->
        <div class="nada-modal-content modal-lg" id="form-content">
            <div class="nada-modal-header">
                <h3><?php echo __('Éditer l\'institution', 'nada-id'); ?></h3>
                <button type="button" class="nada-modal-close cancel-action">&times;</button>
            </div>

            <div class="nada-modal-body">
                <div id="item-form-error" class="alert alert-danger d-none" role="alert"></div>

                <form id="item-form">
                    <input type="hidden" name="action" value="nadaSaveInstitution">
                    <input type="hidden" name="id" id="item-id" value="0">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="labelFrIns" class="form-label"><?php echo __('refListNameFr', 'nada-id'); ?> *</label>
                            <input type="text" class="form-control" id="labelFrIns" name="labelFrIns" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="labelEnIns" class="form-label"><?php echo __('refListNameEn', 'nada-id'); ?> *</label>
                            <input type="text" class="form-control" id="labelEnIns" name="labelEnIns" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="descFrIns" class="form-label"><?php echo __('refListDescriptionFr', 'nada-id'); ?></label>
                            <textarea class="form-control" id="descFrIns" name="descFrIns" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="descEnIns" class="form-label"><?php echo __('refListDescriptionEn', 'nada-id'); ?></label>
                            <textarea class="form-control" id="descEnIns" name="descEnIns" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="uriIns" class="form-label">URI</label>
                            <input type="text" class="form-control" id="uriIns" name="uriIns" placeholder="0abcdef12">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sirenIns" class="form-label">SIREN</label>
                            <input type="text" class="form-control" id="sirenIns" name="sirenIns">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="identifierIns" class="form-label">Identifiant</label>
                            <input type="text" class="form-control" id="identifierIns" name="identifierIns">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="statusIns" class="form-label">Statut</label>
                            <input type="text" class="form-control" id="statusIns" name="statusIns">
                        </div>
                    </div>
                </form>
            </div>

            <div class="nada-modal-footer">
                <button type="button" class="button cancel-action"><?php echo __('btnCancel', 'nada-id'); ?></button>
                <button type="button" class="button button-primary confirm-add-edit-ins">
                    <span class="spinner-border spinner-border-sm d-none"></span>
                    <span class="btn-text"><?php echo __('btnConfirm', 'nada-id'); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>
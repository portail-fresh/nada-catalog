<div id="addEditItemModal" class="nada-modal" style="display: none;">
    <div class="nada-modal-overlay"></div>
    <div class="nada-modal-wrapper">
        <!-- Loader -->
        <div id="loader-add-edit-ref" class="nada-modal-content" style="display:none">
            <div class="nada-modal-body text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-3"><?php echo __('loading', 'nada-id'); ?></div>
            </div>
        </div>

        <!-- Message de succès -->
        <div id="success-message-ref" class="nada-modal-content" style="display:none">
            <div class="nada-modal-body text-center">
                <span class="dashicons dashicons-yes-alt success-icon" style="font-size: 48px; color: #28a745;"></span>
                <div class="message-success-response mt-3"></div>
            </div>
        </div>

        <!-- Contenu principal du formulaire -->
        <div class="nada-modal-content modal-lg" id="form-content">
            <div class="nada-modal-header">
                <h3 class="modal-title" id="addEditItemLabel"><?php echo __('titleModelAddItem', 'nada-id'); ?></h5>
                    <button type="button" class="nada-modal-close cancel-action">&times;</button>
            </div>

            <div class="nada-modal-body">
                <div id="item-form-error" class="alert alert-danger d-none" role="alert"></div>

                <form id="item-form">
                    <input type="hidden" name="action" value="nada_save_referentiel_item">
                    <input type="hidden" name="referentiel_id" id="referentiel_id" value>
                    <input type="hidden" name="item_id" id="item-id" value="0">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="label_fr" class="form-label"><?php echo __('refListNameFr', 'nada-id'); ?> *</label>
                            <input type="text" class="form-control" id="label_fr" name="label_fr" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="label_en" class="form-label"><?php echo __('refListNameEn', 'nada-id'); ?> *</label>
                            <input type="text" class="form-control" id="label_en" name="label_en" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="desc_fr" class="form-label"><?php echo __('refListDescriptionFr', 'nada-id'); ?> </label>
                            <textarea class="form-control" id="desc_fr" name="desc_fr" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="desc_en" class="form-label"><?php echo __('refListDescriptionEn', 'nada-id'); ?></label>
                            <textarea class="form-control" id="desc_en" name="desc_en" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="uri" class="form-label">URI</label>
                            <input type="text" class="form-control" id="uri" name="uri">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="uri_esv" class="form-label">URI_ESV</label>
                            <input type="text" class="form-control" id="uri_esv" name="uri_esv">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="uri_mesh" class="form-label">URI_MESH</label>
                            <input type="text" class="form-control" id="uri_mesh" name="uri_mesh">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="identifier" class="form-label">Identifiant</label>
                            <input type="text" class="form-control" id="identifier" name="identifier">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="siren" class="form-label">SIREN</label>
                            <input type="text" class="form-control" id="siren" name="siren">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <input type="text" class="form-control" id="status" name="status">
                        </div>
                    </div>


                </form>
            </div>

            <div class="nada-modal-footer">
                <button type="button" class="button cancel-action"><?php echo __('btnCancel', 'nada-id'); ?></button>
                <button type="button" class="button button-primary confirm-add-edit-item">
                    <span class="spinner-border spinner-border-sm d-none"></span>
                    <span class="btn-text"><?php echo __('btnConfirm', 'nada-id'); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>
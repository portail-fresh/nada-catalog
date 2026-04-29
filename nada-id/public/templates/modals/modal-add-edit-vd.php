<div class="modal" tabindex="-1" id="editVDModal" aria-labelledby="editVDLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVDLabel">
                    <?php echo __('titleVDModel', 'nada-id'); ?>
                </h5>
                <button type="button" class="btn-close cancel-edit-vd-field" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <div id="vd-form-error" class="alert alert-danger d-none" role="alert"></div>

                <form id="vd-form">
                    <input type="hidden" name="action" value="nada_update_vd_description_field">
                    <input type="hidden" name="field_id" id="field-id" value="">

                    <div class="mb-3">
                        <label for="meta_label_fr" class="form-label">
                            <?php echo __('labelFRVDSchema', 'nada-id'); ?>
                        </label>
                        <input type="text" class="form-control bg-light" id="meta_label_fr" name="label_fr" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="meta_label_en" class="form-label">
                            <?php echo __('labelENVDSchema', 'nada-id'); ?>
                        </label>
                        <input type="text" class="form-control bg-light" id="meta_label_en" name="label_en" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="meta_desc_fr" class="form-label">
                            <?php echo __('descFRVDSchema', 'nada-id'); ?>
                        </label>
                        <textarea class="form-control" id="meta_desc_fr" name="description_fr" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="meta_desc_en" class="form-label">
                            <?php echo __('descENVDSchema', 'nada-id'); ?>
                        </label>
                        <textarea class="form-control" id="meta_desc_en" name="description_en" rows="4"></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="button cancel-edit-vd-field">
                    <?php echo __('btnCancel', 'nada-id'); ?>
                </button>
                <button type="button" class="button confirm-edit-vd-field">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text"><?php echo __('btnConfirm', 'nada-id'); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal metadonnées -->
<div id="metadata" class="nada-modal" style="display:none">
    <div class="nada-modal-overlay"></div>
    <div class="nada-modal-wrapper">
        <div class="nada-modal-content" id="confirmation-content">
            <div class="nada-modal-header">
                <h3>
                    <?= __('technicalMetadata', 'nada-id'); ?>
                </h3>
                <button type="button" class="nada-modal-close cancel-metadata">&times;</button>
            </div>
            <div class="nada-modal-body">
                <div class="metadata-content">
                    <p><strong><?= __('metadataIdentifier', 'nada-id'); ?> :</strong> <span id="meta-identifier"></span></p>
                    <p><strong><?= __('metadataIDSchema', 'nada-id'); ?> :</strong> <span id="meta-iDSchema"></span></p>
                    <p><strong><?= __('metadataProvenance', 'nada-id'); ?> :</strong> <span id="meta-provenance"></span></p>
                    <p><strong><?= __('metadataVersionLang', 'nada-id'); ?> :</strong> <span id="meta-versionLang"></span></p>
                    <p><strong><?= __('metadataOriginLang', 'nada-id'); ?> :</strong> <span id="meta-originLang"></span></p>
                    <p><strong><?= __('metadataCreationDate', 'nada-id'); ?> :</strong> <span id="meta-creationDate"></span></p>
                    <p><strong><?= __('metadataLastUpdatedAuto', 'nada-id'); ?> :</strong> <span id="meta-lastUpdatedAuto"></span></p>
                    <p><strong><?= __('metadataLastUpdatedManual', 'nada-id'); ?> :</strong> <span id="meta-lastUpdatedManual"></span></p>
                    <p><strong><?= __('metadataRespValidation', 'nada-id'); ?> :</strong> <span id="meta-respValidation"></span></p>
                    <p><strong><?= __('metadataAutoTranslation', 'nada-id'); ?> :</strong> <span id="meta-autoTranslation"></span></p>
                    <p><strong><?= __('metadataStatus', 'nada-id'); ?> :</strong> <span id="meta-status"></span></p>
                    <p><strong><?= __('metadataContributorName', 'nada-id'); ?> :</strong> <span id="meta-contributorName"></span></p>
                    <p><strong><?= __('metadataContributorAffiliation', 'nada-id'); ?> :</strong> <span id="meta-contributorAffiliation"></span></p>
                </div>
            </div>
            <div class="nada-modal-footer">
                <button type="button" class="button cancel-metadata">
                    <?php echo __('btnBack', 'nada-id'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal delete study -->
<div class="modal" tabindex="-1" id="deleteStudyModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <?php echo __('deleteStudy', 'nada-id'); ?></h5>
                <button type="button" class="btn-close cancel-delete-study" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <p class="delete-text text-dark">
                    <?php echo __('textConfirmDeleteStudy', 'nada-id'); ?>
                    <strong class="study-title">
                    </strong> ? <br> <span class="text-danger fw-bold">
                        <?php echo __('subTextConfirmDeleteStudy', 'nada-id'); ?></span>
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-delete-study" data-bs-dismiss="modal">
                    <?php echo __('btnCancel', 'nada-id'); ?>
                </button>
                <button type="button" class="btn btn-danger confirm-delete-study">
                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>

                    <span class="btn-text">
                        <?php echo __('btnConfirm', 'nada-id'); ?>
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Modal Confirmation publish/unpublish study -->
<div class="modal" id="publishStudyModal" tabindex="-1" aria-labelledby="confirmationModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="confirmationModalLabel"><?php echo __('titlePublishUnpublishedStudy', 'nada-id'); ?></h5>
                <button type="button" class="btn-close cancel-action-study" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="text-dark"><?php echo __('textPublishUnpublishedStudy', 'nada-id'); ?> <strong
                        class="study-title"></strong> ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-action-study" data-bs-dismiss="modal">
                    <?php echo __('btnCancel', 'nada-id'); ?>
                </button>
                <button type="button" class="btn btn-success confirm-action-study">
                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                    <span class="btn-text"><?php echo __('btnConfirm', 'nada-id'); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal request edit study -->
<div class="modal" id="requestChangesModal" tabindex="-1" aria-labelledby="requestChangesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestChangesLabel"><?php echo __('requestChanges', 'nada-id'); ?></h5>
                <button type="button" class="btn-close requestChanges-cancel-modal" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="text-dark">
                    <?php echo __('textRequestChanges', 'nada-id'); ?>
                    <strong class="study-title"></strong> ?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary requestChanges-cancel-modal"
                    data-bs-dismiss="modal"><?php echo __('btnCancel', 'nada-id'); ?></button>
                <button type="button" class="btn btn-warning confirm-request-changes-study">
                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                    <span class="btn-text"><?php echo __('btnConfirm', 'nada-id'); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal reject study -->
<div class="modal" tabindex="-1" id="rejectStudyModal" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel"><?php echo __('rejectionStudy', 'nada-id'); ?></h5>
                <button type="button" class="btn-close cancel-reject-study" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <p class="reject-text text-dark">
                    <?php echo __('textConfirmRejectionStudy', 'nada-id'); ?>
                    <strong class="study-title"></strong> ? <br>
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-reject-study" data-bs-dismiss="modal">
                    <?php echo __('btnCancel', 'nada-id'); ?></button>
                <button type="button" class="btn btn-danger confirm-reject-study">
                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                    <span class="btn-text"><?php echo __('btnConfirm', 'nada-id'); ?></span>
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Modal cancel study -->
<div class="modal" id="cancelStudyModal" tabindex="-1" aria-labelledby="cancelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelLabel"><?php echo __('cancelModalTitle', 'nada-id'); ?></h5>
                <button type="button" class="btn-close cancel-modal" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="text-dark">
                    <?php echo __('cancelModalTxt', 'nada-id'); ?>
                    <strong class="study-title"></strong> ?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-modal"
                    data-bs-dismiss="modal"><?php echo __('btnCancel', 'nada-id'); ?></button>
                <button type="button" class="btn btn-danger confirm-cancel-study">
                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                    <span class="btn-text"><?php echo __('btnConfirm', 'nada-id'); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>
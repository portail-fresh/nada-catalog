<?php
if (!defined(constant_name: 'ABSPATH')) exit;
?>
<div class="btnSutdy">
    <?php if ($current_language === 'fr') : ?>
        <a href="/alimentation-du-catalogue" class="link-with-icon link-add">
            <i class="fa fa-database"></i>
            Contribuer au catalogue
        </a>
    <?php else: ?>
        <a href="/en/contribute" class="link-with-icon link-add">
            <i class="fa fa-database"></i>
            Contribute to the catalog
        </a>
    <?php endif; ?>
    <?php if (current_user_can('admin_fresh')) : ?>
        <a href="/televersement-catalogue" class="link-with-icon link-import">
            <i class="fa fa-upload"></i>
            <?php echo ($current_language === 'fr') ? 'Téléversement catalogue' : 'Upload catalog'; ?>
        </a>
    <?php endif; ?>
</div>
<div id="page-liste-etude-admin">
    <table border="1" cellpadding="5" class="table" cellspacing="0" id="list-etude-admin">
        <thead>
            <tr>
                <th><?php echo __('listTitleKey', 'nada-id'); ?></th>
                <th><?php echo __('listCreationDateKey', 'nada-id'); ?></th>
                <th><?php echo __('listStatusKey', 'nada-id'); ?></th>
                <th><?php echo __('listUserKey', 'nada-id'); ?></th>
                <th>Actions</th>
                <th><?php echo __('listDecisionKey', 'nada-id'); ?></th>
                <th><?php echo __('listPublishKey', 'nada-id'); ?></th>
        </thead>
        <tbody>
            <?php foreach ($user_studies as $study): ?>
                <tr id="<?php echo esc_html($study['idno']); ?>">
                    <td class="text-truncate" style="max-width: 200px;">
                        <?php echo esc_html($study['title']); ?></span>
                    </td>
                    <td style="max-width: 120px;" data-order="<?php echo esc_attr($study['created']); ?>">
                        <?php echo esc_html(format_date_fr($study['created'])); ?>
                    </td>
                    <td>
                        <?php
                        $status_key = esc_html($study['status_key']);

                        // Associer chaque statut à une couleur
                        $badge_class = match ($status_key) {
                            'draft'            => 'secondary',
                            'pending'          => 'primary',       // jaune
                            'approved'         => 'success',       // vert
                            'rejected'         => 'danger',        // rouge
                            'changes_requested' => 'warning',
                            'published' => 'success',         // bleu clair
                            'unpublished' => 'danger',
                            default            => 'primary'
                        };
                        ?>
                        <span class="badge bg-<?php echo $badge_class; ?>">
                            <?php echo $study['status']; ?>
                        </span>
                    </td>
                    <td><?php echo esc_html($study['user']['first_name']); ?></td>
                    <td>
                        <span class="icon edit edit-study-btn-nada"
                            data-action="info"
                            data-study-idno="<?php echo $study['idno']; ?>"
                            data-study-title="<?php echo esc_html($study['title']); ?>"
                            title=<?php echo __('actionDetails', 'nada-id'); ?>
                            style="cursor:pointer; color:#2980b9; margin-right:10px;">
                            <a href="/catalogue-detail/<?php echo esc_html($study['idno']); ?>">
                                <i class="fa fa-eye"></i>
                            </a>
                        </span>

                        <?php if ($study['can_edit']): ?>
                            <span class="icon edit edit-study-btn-nada"
                                data-action="edit"
                                data-study-id="<?php echo $study['idno']; ?>"
                                data-study-name="<?php echo esc_html($study['title']); ?>"
                                title=<?php echo __('actionEdit', 'nada-id'); ?>
                                style="cursor:pointer; color:#2980b9; margin-right:10px;">
                                <a href="/alimentation-du-catalogue/<?php echo esc_html($study['idno']); ?>">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </span>
                        <?php endif; ?>

                        <?php if ($study['can_reject']): ?>
                            <span class="icon reject reject-study-btn-nada" data-action="reject"
                                data-study-idno="<?php echo $study['idno']; ?>"
                                data-study-title="<?php echo esc_html($study['title']); ?>"
                                title=<?php echo __('actionReject', 'nada-id'); ?> style="cursor:pointer; color:#e74c3c; margin-right:10px;">
                                <i class="fas fa-backspace"></i> </span>
                        <?php endif; ?>

                        <?php if ($study['can_delete']): ?>
                            <span class="icon supprimer delete-study-btn-nada" data-action="supprimer"
                                data-study-idno="<?php echo $study['idno']; ?>"
                                data-study-title="<?php echo esc_html($study['title']); ?>"
                                title=<?php echo __('actionDelete', 'nada-id'); ?> style="cursor:pointer; color:#e74c3c; margin-right:10px;">
                                <i class="fa fa-trash"></i>
                            </span>
                        <?php endif; ?>

                        <?php if ($study['can_decide']): ?>
                            <span class="icon edit make-study-decision-btn"
                                data-action="decider"
                                data-study-idno="<?php echo $study['idno']; ?>"
                                data-study-title="<?php echo esc_html($study['title']); ?>"
                                title=<?php echo __('actionMakeDecision', 'nada-id'); ?>
                                style="cursor:pointer; color:#2980b9; margin-right:10px;">
                                <i class="fa fa-check"></i>
                            </span>
                        <?php endif; ?>

                        <?php if ($study['can_require_modifications']): ?>
                            <span class="icon supprimer require-study-modifications-btn"
                                data-action="Modifications requises"
                                data-study-idno="<?php echo $study['idno']; ?>"
                                data-study-title="<?php echo esc_html($study['title']); ?>"
                                title=<?php echo __('actionRequiredChanges', 'nada-id'); ?>
                                style="cursor:pointer; color:#e74c3c; margin-right:10px;">
                                <i class="fa fa-repeat"></i>
                            </span>
                        <?php endif; ?>

                    </td>
                    <td>
                        <?php if (!is_null($study['is_approved'])): ?>
                            <?php if ($study['is_approved']): ?>
                                <span class="badge bg-success">
                                    <i class="fa fa-check-circle" style="margin-right:2px;"></i> 
                                    <?php echo __('decisionApprove', 'nada-id'); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger">
                                    <i class="fa fa-times-circle" style="margin-right:2px;"></i>
                                    <?php echo __('decisionDisapprove', 'nada-id'); ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>

                    </td>
                    <td>
                        <?php if ($study['can_publish']): ?>
                            <div class="form-check formNada form-switch p-0">
                                <input class="form-check-input publish-study-suitch"
                                    type="checkbox"
                                    role="switch"
                                    id="switch-[<?php echo $study['idno']; ?>]"
                                    data-study-idno="<?php echo $study['idno']; ?>"
                                    data-study-title="<?php echo esc_html($study['title']); ?>"
                                    aria-checked="<?php echo $study['published'] == "1" ? 'true' : 'false'; ?>"
                                    <?php echo esc_html($study['published']  == "1" ? 'checked' : ''); ?>>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modals -->
<div class="modal" tabindex="-1" id="deleteStudyModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Suppression d'étude</h5>
                <button type="button" class="btn-close cancel-delete-study" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <p class="delete-text text-dark">Voulez-vous vraiment <strong>supprimer</strong> l’étude
                    <strong class="study-title">
                    </strong> ? <br> <span class="text-danger fw-bold">Cette action est irréversible. </span>
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-delete-study" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger confirm-delete-study">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="false"></span>
                    <span class="btn-text">Supprimer</span>
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal" id="publishStudyModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                <button type="button" class="btn-close cancel-action-study" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="text-dark">Voulez-vous vraiment <strong class="study-action"></strong> l’étude <strong class="study-title"></strong> ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-action-study" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success confirm-action-study">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Confirmer</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="decisionStudyModal" tabindex="-1" aria-labelledby="decisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="decisionModalLabel">Approuver l'étude</h5>
                <button type="button" class="btn-close cancel-approve-study" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <p class="text-dark">Voulez-vous vraiment <strong>approuver/désapprouver</strong> l’étude <strong></strong> ? </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-approve-study" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success approve-disapprove-survey-btn" data-decision="approve">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Approuver</span>
                </button>
                <button type="button" class="btn btn-danger approve-disapprove-survey-btn" data-decision="disapprove">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Désapprouver</span>
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal" id="requestChangesModal" tabindex="-1" aria-labelledby="requestChangesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestChangesLabel">Demande de modifications</h5>
                <button type="button" class="btn-close cancel-modal" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="text-dark">
                    Voulez-vous vraiment envoyer une demande de <strong>modifications</strong> pour l’étude
                    <strong class="study-title"></strong> ?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-modal" data-bs-dismiss="modal">Non</button>
                <button type="button" class="btn btn-warning confirm-request-changes-study">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Oui</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="rejectStudyModal" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Rejet de l’étude</h5>
                <button type="button" class="btn-close cancel-reject-study" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <p class="reject-text text-dark">
                    Voulez-vous vraiment <strong>rejeter</strong> l’étude
                    <strong class="study-title"></strong> ? <br>
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-reject-study" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger confirm-reject-study">
                    <i class="fas fa-ban me-1"></i>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    <span class="btn-text">Rejeter</span>
                </button>
            </div>

        </div>
    </div>
</div>



<div class="position-fixed top-50 start-50 translate-middle p-3" style="z-index: 9999">
    <div id="study-action-toast" class="toast align-items-center border-0 bg-dark"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
        data-bs-delay="8000"
        data-bs-autohide="true">
        <div class="d-flex">
            <div id="study-action-toast-body" class="toast-body">
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
        </div>
    </div>
</div>
<script src="https://cdn.datatables.net/plug-ins/1.13.6/sorting/date-eu.js"></script>
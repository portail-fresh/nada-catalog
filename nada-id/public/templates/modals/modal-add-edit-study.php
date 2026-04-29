<div class="modal" tabindex="-1" id="detailCIM" tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailCIMLabel"><b>Pathologies, affections ou diagnostics ciblés par l’étude</b></h5>
                <button type="button" class="btn-close cancel-show-modal-cim" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body study-title listCIM">
                <div class="modalCIM searchInput form-nada-add mb-4">
                    <div class="card">
                        <div class="input-group">
                            <div class="d-flex">
                                <label class="form-label mb-2 ">
                                    <span class="lang-label">
                                        Rechercher
                                    </span>
                                </label>
                            </div>
                            <input type="text" class="form-control" name="searchCIM" placeholder="Entrez un minimum de 3 caractères">
                            <input type="hidden" name="langueSearchCIM" value="fr">
                        </div>
                    </div>

                </div>

                <div id="listPathologies">
                    <div class="text-center  my-4">

                        <span class="dashicons dashicons-info" style="font-size:32px; color:#555; margin-bottom:15px;"></span>
                        <p class="mt-2 mb-0">Lancer la recherche en saisissant un ou plusieurs mot-clés.</p>
                    </div>
                </div>



            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-show-modal-cim" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary  save-show-modal-cim">Valider</button>
            </div>

        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="emailStudyModal" tabindex="-1" aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Adresse email</h5>
                <button type="button" class="btn-close cancel-show-modal-email" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body study-title"></div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel-show-modal-email" data-bs-dismiss="modal">Fermer</button>
            </div>

        </div>
    </div>
</div>

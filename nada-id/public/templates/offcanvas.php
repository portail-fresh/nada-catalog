<div class="offcanvas offcanvas-end" tabindex="-1" id="basicDrawer">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Dictionnaire</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <div class="basicDrawerTitle">
            <span class="textlabel">Nom du champ <span class="data-url"></span></span>
            <div class="data-title"></div>
        </div>

        <div class="basicDrawerDescription">
            <span class="textlabel">Description</span>
            <div class="data-description"></div>
        </div>

        <div class="basicDrawerVP">
            <span class="textlabel">Valeurs possibles</span>
            <div class="data-vc"></div>
        </div>
    </div>

</div>

<div class="modal" id="translationConfirmModal" tabindex="-1" aria-labelledby="translationConfirmLabel" aria-hidden="false" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">
            <div id="loader" class="modal-body  text-center" style="display:none">
                <div class="spinner-border text-primary" role="status"></div>
                <div>Chargement...</div>
            </div>

            <div id="succes-message" class="modal-body text-center alert mb-0" style="display:none">
                <span class="dashicons dashicons-yes-alt success-icon fs-3"></span>
                <div class="message-succes-response"></div>
            </div>

            <div class="modalContent">
                <div class="modal-header">
                    <h5 class="modal-title lang-text" id="translationConfirmLabel"
                        data-fr="Confirmation de traduction"
                        data-en="Translation Confirmation">
                        Confirmation de traduction
                    </h5>
                    <button type="button" class="btn-close cancel-show-modal-study" id="closeTranslationModal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info" role="alert">
                        <i class="dashicons dashicons-translation" style="font-size: 24px; float: left; margin-right: 10px;"></i>
                        <p class="lang-text mb-0"
                            data-fr="Merci pour votre contribution au catalogue FReSH ! <br>
                                    L’administrateur a été informé de la création de votre fiche et procédera à sa publication. <br>
                                    Vous pouvez dès à présent créer une version anglaise de votre fiche. <br>
                                    La traduction sera appliquée uniquement aux champs non renseignés.
                                    Les champs déjà complétés dans la langue correspondante resteront inchangés et ne seront pas écrasés. <br>
                                    Souhaitez-vous le faire maintenant ?<br>
                                    (Le cas échéant, une traduction automatique sera générée, que vous pourrez ensuite modifier si nécessaire.)"
                            data-en="Thank you for your contribution to the FReSH catalogue! <br>
                                    The administrator has been notified of the creation of your entry and will proceed to publish it. <br>
                                    You can now create a French version of your entry. <br>
                                    The translation will only be applied to fields that have not been filled in.
                                    Fields that have already been completed in the corresponding language will remain unchanged and will not be overwritten. <br>
                                    Would you like to do so now?<br>
                                    (Otherwise, an automatic translation will be generated, which you can then modify if necessary.)">
                            Merci pour votre contribution au catalogue FReSH ! <br>
                            L’administrateur a été informé de la création de votre fiche et procédera à sa publication. <br>
                            Vous pouvez dès à présent créer une version anglaise de votre fiche. <br>
                            La traduction sera appliquée uniquement aux champs non renseignés.
                            Les champs déjà complétés dans la langue correspondante resteront inchangés et ne seront pas écrasés. <br>
                            Souhaitez-vous le faire maintenant ?<br>
                            (Le cas échéant, une traduction automatique sera générée, que vous pourrez ensuite modifier si nécessaire.)
                        </p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary lang-text" data-bs-dismiss="modal" id="declineTranslationBtn"
                        data-fr="Non, je ne veux pas traduire"
                        data-en="No, I do not want to translate">
                        Non, je ne veux pas traduire
                    </button>
                    <button type="button" class="btn btn-primary lang-text" id="confirmTranslationBtn"
                        data-fr="Oui, j’accepte"
                        data-en="Yes, I accept">
                        Oui, j’accepte
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
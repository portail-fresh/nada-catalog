<?php
/*
Template Name: Add Study section 2
*/

// init options boolean
$options_boolean = [
    "fr" => [
        "Oui" => "Oui",
        "Non" => "Non",
    ],
    "en" => [
        "Yes" => "Yes",
        "No" => "No",
    ],
];
// text des input repetitive
$labelOrgFr = "Nom de l'organisation d'affiliation principale";
$labelOrgEn = "Main affiliated organisation name";
$labelIdeOrgFr = "Identifiant de l’organisation d’affiliation principale";
$labelIdeOrgEn = "Main affiliated organisation identifier";
$labelTypeOrgFr = "Type d'identifiant de l'organisation d'affiliation principale";
$labelTypeOrgEn = "Main affiliated organisation identifier type";
$labelAffFr = "Affiliation(s), précisions";
$labelAffEn = "Affiliation(s), details";
$labelRNSRFr = "Identifiant RNSR du laboratoire d'appartenance";
$labelRNSREn = "Affiliated laboratory RNSR identifier";

global $compareStudy;
$current_lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';
?>

<div class="tab-pane" id="step-2">
    <section key="StudyMethodology">
        <h2 class="lang-text"
            data-fr="Renseignements administratifs"
            data-en="Administrative information">
            Renseignements administratifs
        </h2>

        <section key="RegulatoryRequirements">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseThreee"
                    aria-expanded="true"
                    aria-controls="collapseThreee"
                    data-fr="Pré-requis réglementaires"
                    data-en="Regulatory requirements">
                    Pré-requis réglementaires
                </button>

                <div class="collapse show" id="collapseThreee">
                    <div class="card card-body">
                        <?php
                        $tooltipFr = getTooltipByName('RegulatoryRequirements', 'fr', 'AdministrativeInformation');
                        $tooltipEn = getTooltipByName('RegulatoryRequirements', 'en', 'AdministrativeInformation'); ?>
                        <section key="RegulatoryRequirements">
                            <h3 class="lang-text-tooltip"
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Pré-requis réglementaires"
                                data-en="Regulatory requirements">
                                <span class="contentSection">Pré-requis réglementaires</span>
                                <span class="info-bulle"
                                    attr-lng="fr"
                                    data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                            </h3>
                            <section key="ObtainedAuthorization">
                                <?php
                                $tooltipFr = getTooltipByName('ObtainedAuthorization', 'fr', 'RegulatoryRequirements');
                                $tooltipEn = getTooltipByName('ObtainedAuthorization', 'en', 'RegulatoryRequirements'); ?>
                                <?php
                                $statusKey = "stdyDscr/studyAuthorization/authorizingAgency/values_{$current_lang}_status";
                                $isChanged = $compareStudy[$statusKey] ?? false;
                                ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Autorisation(s) ou avi(s) obtenu(s)"
                                    data-en="Authorisation(s) or approval(s) obtained">
                                    <span class="contentSection">Autorisation(s) ou avi(s) obtenu(s)</span>
                                    <span class="info-bulle"
                                        attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                    <?php if ($isChanged): ?>
                                        <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                            <i class="dashicons dashicons-edit"></i>
                                        </span>
                                    <?php endif; ?>
                                </h4>


                                <div id="repeater-ObtainedAuthorization" class="repeaterBlock">
                                    <div class="mb-4 repeater-item" data-section="otherAuthorizingAgency">

                                        <div class="row">
                                            <div class="justify-content-end">
                                                <button type="button" class="btn-remove btn-remove-section">
                                                    <span class="dashicons dashicons-trash"></span>
                                                    <span class="lang-text" data-fr="Supprimer" data-en="Delete">Supprimer</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row" data-parent="otherAuthorizingAgency">
                                            <div class="col-md-12 mb-3" data-key="AuthorizationAgency">
                                                <?php
                                                $tooltipFr = getTooltipByName('AuthorizingAgency', 'fr', 'ObtainedAuthorization');
                                                $tooltipEn = getTooltipByName('AuthorizingAgency', 'en', 'ObtainedAuthorization');
                                                $options_authorizingAgency = getReferentielByName('AuthorizationAgency');
                                                nada_renderInputGroup(
                                                    "Autorité compétente ayant délivré l'autorisation ou l'avis de validité de l'étude",
                                                    "Competent authority that issued the authorisation or notice of validity for the study",
                                                    "stdyDscr/studyAuthorization/authorizingAgency",
                                                    "select",
                                                    $options_authorizingAgency,
                                                    true,
                                                    true,
                                                    $tooltipFr,
                                                    $tooltipEn
                                                );
                                                ?>
                                            </div>

                                            <div class="col-md-12 mb-3 d-none" data-item="otherAuthorizingAgency"
                                                ddiShema="(authorizingAgency)_custom">
                                                <?php
                                                $tooltipFr = getTooltipByName('otherAuthorizingAgency', 'fr', 'ObtainedAuthorization');
                                                $tooltipEn = getTooltipByName('otherAuthorizingAgency', 'en', 'ObtainedAuthorization');
                                                nada_renderInputGroup(
                                                    "Autre autorité compétente, précisions",
                                                    "Other competent authority, details",
                                                    "additional/obtainedAuthorization/otherAuthorizingAgency",
                                                    "textarea",
                                                    [],
                                                    true,
                                                    false,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    false,
                                                    null,
                                                    null,
                                                    1000
                                                );
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex btnBlockRepeater">
                                    <button type="button" class="btn-add mt-2" id="add-ObtainedAuthorization">
                                        <span class="dashicons dashicons-plus"></span>
                                        <span class="lang-text" data-fr="Ajouter" data-en="Add">Ajouter</span>
                                    </button>
                                </div>
                            </section>


                        </section>
                    </div>
                </div>
            </div>
        </section>


        <div class="card card-body mb-4">
            <div class="row">
                <div class="col-md-12 mb-3" ddiShema="additional/isContributorPI">
                    <?php
                    $tooltipFr = getTooltipByName('IsContributorPI', 'fr', 'AdministrativeInformation');
                    $tooltipEn = getTooltipByName('IsContributorPI', 'en', 'AdministrativeInformation');
                    nada_renderInputGroup(
                        "Etes-vous le responsable scientifique de l'étude ?",
                        "Are you the principal investigator of the study ?",
                        "additional/isContributorPI",
                        "radio",
                        $options_boolean,
                        true,
                        true,
                        $tooltipFr,
                        $tooltipEn
                    );
                    ?>
                </div>
            </div>
        </div>


        <section key="PrimaryInvestigator">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapsefourr"
                    aria-expanded="true"
                    aria-controls="collapsefourr"
                    data-fr="Responsable scientifique"
                    data-en="Principal investigator">
                    Responsable scientifique
                </button>

                <div class="collapse show" id="collapsefourr">
                    <div class="card card-body">
                        <section key="PrimaryInvestigator">
                            <?php
                            $tooltipFr = getTooltipByName('PrimaryInvestigator', 'fr', 'AdministrativeInformation');
                            $tooltipEn = getTooltipByName('PrimaryInvestigator', 'en', 'AdministrativeInformation'); ?>
                            <?php
                            $statusKey = "stdyDscr/citation/rspStmt/AuthEnty_{$current_lang}_status";
                            $isChanged = $compareStudy[$statusKey] ?? false;
                            ?>
                            <h3 class="lang-text-tooltip "
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Responsable scientifique"
                                data-en="Principal investigator">
                                <span class="contentSection">Responsable scientifique</span>
                                <span class="info-bulle"
                                    attr-lng="fr"
                                    data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                                <?php if ($isChanged): ?>
                                    <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                        <i class="dashicons dashicons-edit"></i>
                                    </span>
                                <?php endif; ?>
                            </h3>
                            <div class="row">
                                <div id="repeater-PrimaryInvestigator" class="repeaterBlock">
                                    <div class="mb-4 repeater-item" data-section="PrimaryInvestigator">
                                        <div class="row">
                                            <div class="justify-content-end">
                                                <button type="button" class="btn-remove btn-remove-section">
                                                    <span class="dashicons dashicons-trash"></span>
                                                    <span class="lang-text"
                                                        data-fr="Supprimer"
                                                        data-en="Delete">
                                                        Supprimer
                                                    </span>
                                                </button>
                                            </div>
                                        </div>

                                        <section key="PrimaryInvestigator">

                                            <div class="row">

                                                <div class="col-md-6 col-sm-12 mb-3 d-none"
                                                    id="showinvestigatordetails">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('PIName', 'fr', 'PrimaryInvestigator');
                                                    $tooltipEn = getTooltipByName('PIName', 'en', 'PrimaryInvestigator');
                                                    nada_renderInputGroup(
                                                        "Prénom du responsable scientifique",
                                                        "First name of the principal investigator",
                                                        "stdyDscr/citation/rspStmt/AuthEnty/firstname",
                                                        "text",
                                                        [],
                                                        true,
                                                        true,
                                                        $tooltipFr,
                                                        $tooltipEn,
                                                        true,
                                                        null,
                                                        null,
                                                        null
                                                    ); ?>
                                                </div>
                                                <div class="col-md-6 col-sm-12 mb-3 d-none"
                                                    id="showinvestigatordetailslastname">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('PIName', 'fr', 'PrimaryInvestigator');
                                                    $tooltipEn = getTooltipByName('PIName', 'en', 'PrimaryInvestigator');
                                                    nada_renderInputGroup(
                                                        "NOM du responsable scientifique",
                                                        "LAST NAME of the principal investigator",
                                                        "stdyDscr/citation/rspStmt/AuthEnty/lastname",
                                                        "text",
                                                        [],
                                                        true,
                                                        true,
                                                        $tooltipFr,
                                                        $tooltipEn,
                                                        true,
                                                        null,
                                                        null,
                                                        null
                                                    ); ?>
                                                </div>
                                                <div class="col-md-12 mb-3 d-none"
                                                    ddiShema="additional/primaryInvestigator/piMail" id="showemailpi">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('PIMail', 'fr', 'PrimaryInvestigator');
                                                    $tooltipEn = getTooltipByName('PIMail', 'en', 'PrimaryInvestigator');
                                                    nada_renderInputGroup(
                                                        "Email du responsable scientifique",
                                                        "Principal investigator's email",
                                                        "additional/primaryInvestigator/piMail",
                                                        "text",
                                                        [],
                                                        true,
                                                        true,
                                                        $tooltipFr,
                                                        $tooltipEn,
                                                        true,
                                                        null,
                                                        null,
                                                        null,
                                                        null,
                                                        "email",
                                                    );
                                                    ?>

                                                </div>

                                                <div class="col-md-6 col-sm-12  mb-3" data-tooltip-url="true"
                                                    id="showinvestigatorOrcid">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('PIORCID', 'fr', 'PrimaryInvestigator');
                                                    $tooltipEn = getTooltipByName('PIORCID', 'en', 'PrimaryInvestigator');
                                                    nada_renderInputGroup(
                                                        "ORCID du responsable scientifique",
                                                        "Principal investigator's ORCID",
                                                        "stdyDscr/citation/rspStmt/AuthEnty/ExtLink/ORCID",
                                                        "text",
                                                        [],
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn,
                                                        false,
                                                        null,
                                                        null,
                                                        null,
                                                        false,
                                                        "orcid"
                                                    );
                                                    ?>
                                                </div>


                                                <div class="col-md-6 col-sm-12 mb-3" data-tooltip-url="true"
                                                    ddiShema="stdyDscr/citation/rspStmt/AuthEnty/ExtLink/IdRef">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('PIIdRef', 'fr', 'PrimaryInvestigator');
                                                    $tooltipEn = getTooltipByName('PIIdRef', 'en', 'PrimaryInvestigator');
                                                    nada_renderInputGroup(
                                                        "Identifiant IdRef",
                                                        "IdRef Identifier",
                                                        "stdyDscr/citation/rspStmt/AuthEnty/ExtLink/IdRef",
                                                        "text",
                                                        [],
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn
                                                    );
                                                    ?>
                                                </div>


                                            </div>

                                            <section key="PIAffiliation" id="showinvestigatorPiaffiliation">
                                                <?php
                                                $tooltipFr = getTooltipByName('PIAffiliation', 'fr', 'PrimaryInvestigator');
                                                $tooltipEn = getTooltipByName('PIAffiliation', 'en', 'PrimaryInvestigator'); ?>
                                                <h4 class="lang-text-tooltip"
                                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                                    data-fr="Affiliation(s) du responsable scientifique"
                                                    data-en="Principal investigator affiliation(s)">
                                                    <span class="contentSection">Affiliation(s) du responsable scientifique</span>
                                                    <span class="info-bulle"
                                                        attr-lng="fr"
                                                        data-text="<?php echo $tooltipFr; ?>">
                                                        <span class="dashicons dashicons-info"></span>
                                                    </span>
                                                </h4>
                                                <?php $options = getListInstitutions(); ?>
                                                <div class="row">
                                                    <div class="col-md-12 mb-3 "
                                                        ddiShema="stdyDscr/citation/rspStmt/othId/affiliation"
                                                        data-key="Institutions">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('OrganisationName', 'fr', 'PIAffiliation');
                                                        $tooltipEn = getTooltipByName('OrganisationName', 'en', 'PIAffiliation');
                                                        nada_renderInputGroup2(
                                                            $labelOrgFr,
                                                            $labelOrgEn,
                                                            "stdyDscr/citation/rspStmt/AuthEnty/affiliation",
                                                            "autocomplete",
                                                            $options,
                                                            true,
                                                            true,
                                                            $tooltipFr,
                                                            $tooltipEn,
                                                            true,
                                                            null,
                                                            null,
                                                            400
                                                        ); ?>
                                                        <input type="hidden" class="institution-json-data" name="stdyDscr/citation/rspStmt/AuthEnty/institution_json" value="">
                                                    </div>
                                                </div>
                                                <section key="OrganisationPID">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('OrganisationPID', 'fr', 'PIAffiliation');
                                                    $tooltipEn = getTooltipByName('OrganisationPID', 'en', 'PIAffiliation'); ?>
                                                    <h5 class="lang-text-tooltip"
                                                        tooltip-fr="<?php echo $tooltipFr; ?>"
                                                        tooltip-en="<?php echo $tooltipEn; ?>"
                                                        data-fr="Identification de l'organisation d'affiliation principale"
                                                        data-en="Main affiliated organisation identification">
                                                        <span class="contentSection">Identification de l'organisation d'affiliation principale</span>
                                                        <span class="info-bulle"
                                                            attr-lng="fr"
                                                            data-text="<?php echo $tooltipFr; ?>">
                                                            <span class="dashicons dashicons-info"></span>
                                                        </span>
                                                    </h5>

                                                    <div class="row controlChampsIdentifinat hidden-bloc">
                                                        <div class="col-md-6 col-sm-12 mb-3"
                                                            data-key="OrganisationPIDSchema">
                                                            <?php
                                                            $options_PIDSchema = getReferentielByName('OrganisationPIDSchema');
                                                            $tooltipFr = getTooltipByName('PIDSchema', 'fr', 'OrganisationPID');
                                                            $tooltipEn = getTooltipByName('PIDSchema', 'en', 'OrganisationPID');
                                                            nada_renderInputGroup(
                                                                $labelTypeOrgFr,
                                                                $labelTypeOrgEn,
                                                                "stdyDscr/citation/rspStmt/AuthEnty/ExtLink/title",
                                                                "select",
                                                                $options_PIDSchema,
                                                                true,
                                                                false,
                                                                $tooltipFr,
                                                                $tooltipEn
                                                            );
                                                            ?>
                                                        </div>

                                                        <div class="col-md-6 col-sm-12 mb-3">
                                                            <?php
                                                            $tooltipFr = getTooltipByName('URI', 'fr', 'OrganisationPID');
                                                            $tooltipEn = getTooltipByName('URI', 'en', 'OrganisationPID');
                                                            nada_renderInputGroup(
                                                                $labelIdeOrgFr,
                                                                $labelIdeOrgEn,
                                                                "stdyDscr/citation/rspStmt/AuthEnty/affiliation/ExtLink/URI",
                                                                "text",
                                                                [],
                                                                true,
                                                                false,
                                                                $tooltipFr,
                                                                $tooltipEn
                                                            ); ?>
                                                        </div>
                                                    </div>
                                                </section>

                                                <div class="row">

                                                    <div class="col-md-6 col-sm-12 mb-3 allow-enter"
                                                        ddiShema="additional/piLabo">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('PILabo', 'fr', 'PIAffiliation');
                                                        $tooltipEn = getTooltipByName('PILabo', 'en', 'PIAffiliation');
                                                        nada_renderInputGroup(
                                                            $labelAffFr,
                                                            $labelAffEn,
                                                            "additional/primaryInvestigator/piAffiliation/piLabo",
                                                            "textarea",
                                                            [],
                                                            true,
                                                            false,
                                                            $tooltipFr,
                                                            $tooltipEn,
                                                            false
                                                        );
                                                        ?>

                                                    </div>

                                                    <div class="col-md-6 col-sm-12 mb-3" data-tooltip-url="true"
                                                        ddiShema="stdyDscr/citation/rspStmt/AuthEnty/ExtLink/RNSR">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('PILaboId', 'fr', 'PIAffiliation');
                                                        $tooltipEn = getTooltipByName('PILaboId', 'en', 'PIAffiliation');
                                                        nada_renderInputGroup(
                                                            $labelRNSRFr,
                                                            $labelRNSREn,
                                                            "stdyDscr/citation/rspStmt/AuthEnty/ExtLink/RNSR",
                                                            "text",
                                                            [],
                                                            true,
                                                            false,
                                                            $tooltipFr,
                                                            $tooltipEn,
                                                        );
                                                        ?>
                                                    </div>


                                                </div>


                                            </section>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('IsPIContact', 'fr', 'PrimaryInvestigator');
                                                    $tooltipEn = getTooltipByName('IsPIContact', 'en', 'PrimaryInvestigator');
                                                    nada_renderInputGroup(
                                                        "Le responsable scientifique est-il un point de contact ?",
                                                        "Is the principal investigator a contact point?",
                                                        "additional/primaryInvestigator/isPIContact",
                                                        "BtnRadio",
                                                        $options_boolean,
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn
                                                    );
                                                    ?>
                                                </div>
                                            </div>

                                        </section>
                                    </div>
                                </div>

                                <div class="d-flex btnBlockRepeater">
                                    <button type="button" class="btn-add mt-2" id="add-PrimaryInvestigator">
                                        <span class="dashicons dashicons-plus"></span>
                                        <span class="lang-text"
                                            data-fr="Ajouter "
                                            data-en="Add">
                                            Ajouter
                                        </span>
                                    </button>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>
            </div>
        </section>

        <div class="card card-body mb-4">
            <div class="row">
                <div class="col-md-12 mb-3" ddiShema="additional/addTeamMember">
                    <?php
                    $tooltipFr = getTooltipByName('AddTeamMember', 'fr', 'AdministrativeInformation');
                    $tooltipEn = getTooltipByName('AddTeamMember', 'en', 'AdministrativeInformation');
                    nada_renderInputGroup(
                        "D'autres équipes sont-elles impliquées dans cette étude ? ",
                        "Are other teams involved in this study?",
                        "additional/addTeamMember",
                        "radio",
                        $options_boolean,
                        true,
                        false,
                        $tooltipFr,
                        $tooltipEn
                    );
                    ?>
                </div>
            </div>
        </div>

        <section key="TeamMember" id="teamMemberCollaps" class="d-none">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapsefivee"
                    aria-expanded="true"
                    aria-controls="collapsefivee"
                    data-fr="Membre de l'équipe de recherche"
                    data-en="Research team member">
                    Membre de l'équipe de recherche
                </button>

                <div class="collapse show" id="collapsefivee">
                    <div class="card card-body">
                        <section key="TeamMember">

                            <div class="row">
                                <?php
                                $tooltipFr = getTooltipByName('TeamMember', 'fr', 'AdministrativeInformation');
                                $tooltipEn = getTooltipByName('TeamMember', 'en', 'AdministrativeInformation'); ?>
                                <?php
                                $statusKey = "stdyDscr/citation/rspStmt/othId/collaboration_{$current_lang}_status";
                                $isChanged = $compareStudy[$statusKey] ?? false;
                                ?>
                                <h3 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Responsable de l'équipe partenaire"
                                    data-en="Partner team leader">
                                    <span class="contentSection">Responsable de l'équipe partenaire</span>
                                    <span class="info-bulle"
                                        attr-lng="fr"
                                        data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                    <?php if ($isChanged): ?>
                                        <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                            <i class="dashicons dashicons-edit"></i>
                                        </span>
                                    <?php endif; ?>
                                </h3>
                                <div id="repeater-teamMember" class="repeaterBlock">
                                    <div class="mb-4 repeater-item" data-section="TeamMember">
                                        <div class="row">
                                            <div class="justify-content-end">
                                                <button type="button" class="btn-remove btn-remove-section">
                                                    <span class="dashicons dashicons-trash"></span>
                                                    <span class="lang-text"
                                                        data-fr="Supprimer"
                                                        data-en="Delete">
                                                        Supprimer
                                                    </span>
                                                </button>
                                            </div>
                                        </div>

                                        <section key="TeamMember">

                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 mb-3">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('TeamMemberName', 'fr', 'TeamMember');
                                                    $tooltipEn = getTooltipByName('TeamMemberName', 'en', 'TeamMember');
                                                    nada_renderInputGroup(
                                                        "Prénom du responsable de l'équipe",
                                                        "First name of the team leader",
                                                        "stdyDscr/citation/rspStmt/othId/type_contributor",
                                                        "textarea",
                                                        [],
                                                        true,
                                                        true,
                                                        $tooltipFr,
                                                        $tooltipEn,
                                                        true,
                                                        null,
                                                        null,
                                                        100
                                                    ); ?>
                                                </div>
                                                <div class="col-md-6 col-sm-12  mb-3">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('TeamMemberName', 'fr', 'TeamMember');
                                                    $tooltipEn = getTooltipByName('TeamMemberName', 'en', 'TeamMember');
                                                    nada_renderInputGroup(
                                                        "NOM du responsable de l'équipe",
                                                        "LAST NAME of the team leader",
                                                        "stdyDscr/citation/rspStmt/othId/type_contributor/lastname",
                                                        "textarea",
                                                        [],
                                                        true,
                                                        true,
                                                        $tooltipFr,
                                                        $tooltipEn,
                                                        true,
                                                        null,
                                                        null,
                                                        100
                                                    ); ?>
                                                </div>
                                                <div class="col-md-6 col-sm-12 mb-3" data-tooltip-url="true"
                                                    ddiShema="stdyDscr/citation/rspStmt/othId/ExtLink/ORCID">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('TeamMemberORCID', 'fr', 'TeamMember');
                                                    $tooltipEn = getTooltipByName('TeamMemberORCID', 'en', 'TeamMember');
                                                    nada_renderInputGroup(
                                                        "ORCID du responsable de l'équipe",
                                                        "Team leader's ORCID",
                                                        "stdyDscr/citation/rspStmt/othId/ExtLink/ORCID",
                                                        "text",
                                                        [],
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn,
                                                        false,
                                                        null,
                                                        null,
                                                        null,
                                                        false,
                                                        "orcid"
                                                    );
                                                    ?>
                                                </div>

                                                <div class="col-md-6 col-sm-12 mb-3" data-tooltip-url="true"
                                                    ddiShema="stdyDscr/citation/rspStmt/othId/ExtLink/IdRef">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('TeamMemberIdRef', 'fr', 'TeamMember');
                                                    $tooltipEn = getTooltipByName('TeamMemberIdRef', 'en', 'TeamMember');
                                                    nada_renderInputGroup(
                                                        "Identifiant IdRef",
                                                        "IdRef ID",
                                                        "stdyDscr/citation/rspStmt/othId/ExtLink/IdRef",
                                                        "text",
                                                        [],
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn
                                                    );
                                                    ?>
                                                </div>


                                                <section key="PIAffiliation">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('TeamMemberAffiliation', 'fr', 'TeamMember');
                                                    $tooltipEn = getTooltipByName('TeamMemberAffiliation', 'en', 'TeamMember'); ?>
                                                    <h4 class="lang-text-tooltip"
                                                        tooltip-fr="<?php echo $tooltipFr; ?>"
                                                        tooltip-en="<?php echo $tooltipEn; ?>"
                                                        data-fr="Affiliation(s) du responsable de l'équipe"
                                                        data-en="Team leader affiliation(s)">
                                                        <span class="contentSection">Affiliation(s) du responsable de l'équipe</span>
                                                        <span class="info-bulle"
                                                            attr-lng="fr"
                                                            data-text="<?php echo $tooltipFr; ?>">
                                                            <span class="dashicons dashicons-info"></span>
                                                        </span>
                                                    </h4>
                                                    <?php $options = getListInstitutions(); ?>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3"
                                                            ddiShema="stdyDscr/citation/rspStmt/othId/affiliation"
                                                            data-key="Institutions">
                                                            <?php
                                                            $tooltipFr = getTooltipByName('OrganisationName', 'fr', 'TeamMemberAffiliation');
                                                            $tooltipEn = getTooltipByName('OrganisationName', 'en', 'TeamMemberAffiliation');
                                                            nada_renderInputGroup2(
                                                                $labelOrgFr,
                                                                $labelOrgEn,
                                                                "stdyDscr/citation/rspStmt/othId/affiliation",
                                                                "autocomplete",
                                                                $options,
                                                                true,
                                                                true,
                                                                $tooltipFr,
                                                                $tooltipEn,
                                                                true,
                                                                null,
                                                                null,
                                                                400
                                                            ); ?>
                                                            <input type="hidden" class="institution-json-data" name="stdyDscr/citation/rspStmt/othId/institution_json" value="">

                                                        </div>
                                                    </div>
                                                    <section key="OrganisationPID">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('OrganisationPID', 'fr', 'TeamMemberAffiliation');
                                                        $tooltipEn = getTooltipByName('OrganisationPID', 'en', 'TeamMemberAffiliation'); ?>
                                                        <h5 class="lang-text-tooltip"
                                                            tooltip-fr="<?php echo $tooltipFr; ?>"
                                                            tooltip-en="<?php echo $tooltipEn; ?>"
                                                            data-fr="Identification de l'organisation d'affiliation principale"
                                                            data-en="Main affiliated organisation identification">
                                                            <span class="contentSection">Identification de l'organisation d'affiliation principale</span>
                                                            <span class="info-bulle"
                                                                attr-lng="fr"
                                                                data-text="<?php echo $tooltipFr; ?>">
                                                                <span class="dashicons dashicons-info"></span>
                                                            </span>
                                                        </h5>
                                                        <div class="row controlChampsIdentifinat hidden-bloc">
                                                            <div class="col-md-6 col-sm-12 mb-3"
                                                                data-key="OrganisationPIDSchema">
                                                                <?php
                                                                $options_PIDSchema = getReferentielByName('OrganisationPIDSchema');
                                                                $tooltipFr = getTooltipByName('PIDSchema', 'fr', 'OrganisationPID');
                                                                $tooltipEn = getTooltipByName('PIDSchema', 'en', 'OrganisationPID');
                                                                nada_renderInputGroup(
                                                                    $labelTypeOrgFr,
                                                                    $labelTypeOrgEn,
                                                                    "stdyDscr/citation/rspStmt/othId/ExtLink/title",
                                                                    "select",
                                                                    $options_PIDSchema,
                                                                    true,
                                                                    false,
                                                                    $tooltipFr,
                                                                    $tooltipEn
                                                                );
                                                                ?>
                                                            </div>
                                                            <div class="col-md-6 col-sm-12  mb-3">
                                                                <?php
                                                                $tooltipFr = getTooltipByName('URI', 'fr', 'OrganisationPID');
                                                                $tooltipEn = getTooltipByName('URI', 'en', 'OrganisationPID');
                                                                nada_renderInputGroup(
                                                                    $labelIdeOrgFr,
                                                                    $labelIdeOrgEn,
                                                                    "stdyDscr/citation/rspStmt/othId/ExtLink/URI",
                                                                    "text",
                                                                    [],
                                                                    true,
                                                                    false,
                                                                    $tooltipFr,
                                                                    $tooltipEn
                                                                ); ?>
                                                            </div>
                                                        </div>
                                                    </section>

                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-12 mb-3 allow-enter"
                                                            ddiShema="additional/TeamMember/TeamMemberAffiliation/TeamMemberLabo">
                                                            <?php
                                                            $tooltipFr = getTooltipByName('TeamMemberLabo', 'fr', 'TeamMemberAffiliation');
                                                            $tooltipEn = getTooltipByName('TeamMemberLabo', 'en', 'TeamMemberAffiliation');
                                                            nada_renderInputGroup(
                                                                $labelAffFr,
                                                                $labelAffEn,
                                                                "additional/TeamMember/TeamMemberAffiliation/TeamMemberLabo",
                                                                "textarea",
                                                                [],
                                                                true,
                                                                false,
                                                                $tooltipFr,
                                                                $tooltipEn,
                                                                false
                                                            ); ?>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12 mb-3"
                                                            ddiShema="stdyDscr/citation/rspStmt/othId/ExtLink/RNSR"
                                                            data-tooltip-url="true">
                                                            <?php
                                                            $tooltipFr = getTooltipByName('TeamMemberLaboId', 'fr', 'TeamMemberAffiliation');
                                                            $tooltipEn = getTooltipByName('TeamMemberLaboId', 'en', 'TeamMemberAffiliation');
                                                            nada_renderInputGroup(
                                                                $labelRNSRFr,
                                                                $labelRNSREn,
                                                                "stdyDscr/citation/rspStmt/othId/ExtLink/RNSR",
                                                                "text",
                                                                [],
                                                                true,
                                                                false,
                                                                $tooltipFr,
                                                                $tooltipEn,
                                                                true
                                                            ); ?>

                                                        </div>
                                                    </div>


                                                </section>

                                                <div class="col-md-12 mb-3"
                                                    ddiShema="additional/TeamMember/IsTeamMemberContact">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('IsTeamMemberContact', 'fr', 'TeamMember');
                                                    $tooltipEn = getTooltipByName('IsTeamMemberContact', 'en', 'TeamMember');
                                                    nada_renderInputGroup(
                                                        "Le responsable de l'équipe est-il un point de contact ?",
                                                        "Is the team leader a contact point ?",
                                                        "additional/TeamMember/IsTeamMemberContact",
                                                        "BtnRadio",
                                                        $options_boolean,
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn
                                                    );
                                                    ?>
                                                </div>

                                            </div>

                                        </section>
                                    </div>
                                </div>

                                <div class="d-flex btnBlockRepeater">
                                    <button type="button" class="btn-add mt-2" id="add-TeamMember">
                                        <span class="dashicons dashicons-plus"></span>
                                        <span class="lang-text"
                                            data-fr="Ajouter"
                                            data-en="Add">
                                            Ajouter
                                        </span>
                                    </button>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>
            </div>
        </section>


        <section key="ContactPoint">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapsesixx"
                    aria-expanded="true"
                    aria-controls="collapsesixx"
                    data-fr="Point de contact"
                    data-en="Contact point">
                    Point de contact
                </button>

                <div class="collapse show" id="collapsesixx">
                    <div class="card card-body">
                        <section key="ContactPoint">
                            <?php
                            $tooltipFr = getTooltipByName('ContactPoint', 'fr', 'AdministrativeInformation');
                            $tooltipEn = getTooltipByName('ContactPoint', 'en', 'AdministrativeInformation'); ?>
                            <?php
                            $statusKey = "stdyDscr/citation/distStmt/contact/values_{$current_lang}_status";
                            $isChanged = $compareStudy[$statusKey] ?? false;
                            ?>
                            <h3 class="lang-text-tooltip"
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Point(s) de contact"
                                data-en="Contact point(s)">
                                <span class="contentSection">Point(s) de contact</span>
                                <span class="info-bulle" attr-lng="fr" data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                                <?php if ($isChanged): ?>
                                    <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                        <i class="dashicons dashicons-edit"></i>
                                    </span>
                                <?php endif; ?>
                            </h3>


                            <div id="repeater-ContactPoint" class="repeaterBlock">

                                <div class="mb-4 repeater-item" data-section="ContactPoint">
                                    <div class="row">
                                        <div class="justify-content-end">
                                            <button type="button" class="btn-remove btn-remove-section">
                                                <span class="dashicons dashicons-trash"></span>
                                                <span class="lang-text" data-fr="Supprimer"
                                                    data-en="Delete">Supprimer</span>
                                            </button>
                                        </div>
                                    </div>


                                    <section key="ContactPoint">


                                        <div class="row">
                                            <div class="col-md-6 col-sm-12  mb-3"
                                                ddiShema="stdyDscr/citation/distStmt/contact">
                                                <?php
                                                $tooltipFr = getTooltipByName('ContactName', 'fr', 'ContactPoint');
                                                $tooltipEn = getTooltipByName('ContactName', 'en', 'ContactPoint');
                                                nada_renderInputGroup(
                                                    "Prénom du contact",
                                                    "First name of the contact",
                                                    "stdyDscr/citation/distStmt/contact",
                                                    "text",
                                                    [],
                                                    true,
                                                    true,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    true
                                                ); ?>
                                            </div>
                                            <div class="col-md-6 col-sm-12 mb-3"
                                                ddiShema="stdyDscr/citation/distStmt/contact/lastname">
                                                <?php
                                                $tooltipFr = getTooltipByName('ContactName', 'fr', 'ContactPoint');
                                                $tooltipEn = getTooltipByName('ContactName', 'en', 'ContactPoint');
                                                nada_renderInputGroup(
                                                    "NOM du contact",
                                                    "LAST NAME of the contact",
                                                    "stdyDscr/citation/distStmt/contact/lastname",
                                                    "text",
                                                    [],
                                                    true,
                                                    true,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    true
                                                ); ?>
                                            </div>
                                            <div class="col-md-12 mb-3"
                                                ddiShema="stdyDscr/citation/distStmt/contact/email">
                                                <?php
                                                $tooltipFr = getTooltipByName('EMail', 'fr', 'ContactPoint');
                                                $tooltipEn = getTooltipByName('EMail', 'en', 'ContactPoint');
                                                nada_renderInputGroup(
                                                    "Email du contact",
                                                    "Email",
                                                    "stdyDscr/citation/distStmt/contact/email",
                                                    "email",
                                                    [],
                                                    true,
                                                    true,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    false,
                                                    null,
                                                    null,
                                                    null,
                                                    false,
                                                    "email"
                                                ); ?>


                                            </div>
                                        </div>

                                        <section key="Affiliation">
                                            <?php
                                            $tooltipFr = getTooltipByName('ContactPointAffiliation', 'fr', 'ContactPoint');
                                            $tooltipEn = getTooltipByName('ContactPointAffiliation', 'en', 'ContactPoint'); ?>
                                            <h4 class="lang-text-tooltip"
                                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                                tooltip-en="<?php echo $tooltipEn; ?>"
                                                data-fr="Affiliation(s) du contact"
                                                data-en="Contact affiliation(s)">
                                                <span class="contentSection">Affiliation(s) du contact</span>
                                                <span class="info-bulle" attr-lng="fr"
                                                    data-text="<?php echo $tooltipFr; ?>">
                                                    <span class="dashicons dashicons-info"></span>
                                                </span>
                                            </h4>
                                            <?php $options = getListInstitutions(); ?>
                                            <div class="row">
                                                <div class="col-md-12 mb-3"
                                                    ddiShema="stdyDscr/citation/distStmt/contact/affiliation"
                                                    data-key="Institutions">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('OrganisationName', 'fr', 'ContactPointAffiliation');
                                                    $tooltipEn = getTooltipByName('OrganisationName', 'en', 'ContactPointAffiliation');
                                                    nada_renderInputGroup2(
                                                        $labelOrgFr,
                                                        $labelOrgEn,
                                                        "stdyDscr/citation/distStmt/contact/affiliation",
                                                        "autocomplete",
                                                        $options,
                                                        true,
                                                        true,
                                                        $tooltipFr,
                                                        $tooltipEn,
                                                        true,
                                                        null,
                                                        null,
                                                        400
                                                    ); ?>
                                                    <input type="hidden" class="institution-json-data" name="stdyDscr/citation/distStmt/contact/institution_json" value="">
                                                </div>
                                            </div>
                                            <section key="OrganisationPID">
                                                <?php
                                                $tooltipFr = getTooltipByName('OrganisationPID', 'fr', 'ContactPointAffiliation');
                                                $tooltipEn = getTooltipByName('OrganisationPID', 'en', 'ContactPointAffiliation'); ?>
                                                <h5 class="lang-text-tooltip"
                                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                                    data-fr="Identification de l'organisation d'affiliation principale"
                                                    data-en="Main affiliated organisation identification">
                                                    <span class="contentSection">Identification de l'organisation d'affiliation principale</span>
                                                    <span class="info-bulle"
                                                        attr-lng="fr"
                                                        data-text="<?php echo $tooltipFr; ?>">
                                                        <span class="dashicons dashicons-info"></span>
                                                    </span>
                                                </h5>
                                                <div class="row controlChampsIdentifinat hidden-bloc">
                                                    <div class="col-md-6 col-sm-12 mb-3"
                                                        data-key="OrganisationPIDSchema">
                                                        <?php
                                                        $options_PIDSchema = getReferentielByName('OrganisationPIDSchema');
                                                        $tooltipFr = getTooltipByName('PIDSchema', 'fr', 'OrganisationPID');
                                                        $tooltipEn = getTooltipByName('PIDSchema', 'en', 'OrganisationPID');
                                                        nada_renderInputGroup(
                                                            $labelTypeOrgFr,
                                                            $labelTypeOrgEn,
                                                            "stdyDscr/citation/distStmt/contact/ExtLink/title",
                                                            "select",
                                                            $options_PIDSchema,
                                                            true,
                                                            false,
                                                            $tooltipFr,
                                                            $tooltipEn
                                                        );
                                                        ?>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12 mb-3">
                                                        <?php
                                                        $tooltipFr = getTooltipByName('URI', 'fr', 'OrganisationPID');
                                                        $tooltipEn = getTooltipByName('URI', 'en', 'OrganisationPID');
                                                        nada_renderInputGroup(
                                                            $labelIdeOrgFr,
                                                            $labelIdeOrgEn,
                                                            "stdyDscr/citation/distStmt/contact/ExtLink/URI",
                                                            "text",
                                                            [],
                                                            true,
                                                            false,
                                                            $tooltipFr,
                                                            $tooltipEn
                                                        ); ?>
                                                    </div>
                                                </div>
                                            </section>

                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 mb-3 allow-enter"
                                                    ddiShema="additional/contactPointLabo">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('ContactPointLabo', 'fr', 'ContactPointAffiliation');
                                                    $tooltipEn = getTooltipByName('ContactPointLabo', 'en', 'ContactPointAffiliation');
                                                    nada_renderInputGroup(
                                                        $labelAffFr,
                                                        $labelAffEn,
                                                        "additional/contactPointLabo",
                                                        "textarea",
                                                        [],
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn,
                                                        false
                                                    ); ?>
                                                </div>
                                                <div class="col-md-6 col-sm-12  mb-3" data-tooltip-url="true"
                                                    ddiShema="stdyDscr/citation/distStmt/contact/affiliation">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('ContactPointLaboId', 'fr', 'ContactPointAffiliation');
                                                    $tooltipEn = getTooltipByName('ContactPointLaboId', 'en', 'ContactPointAffiliation');
                                                    nada_renderInputGroup(
                                                        $labelRNSRFr,
                                                        $labelRNSREn,
                                                        "stdyDscr/citation/distStmt/contact/ExtLink/RNSR",
                                                        "text",
                                                        [],
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn,
                                                        true
                                                    ); ?>
                                                </div>
                                            </div>

                                        </section>

                                    </section>


                                </div>
                            </div>

                            <div class="d-flex btnBlockRepeater">
                                <button type="button" class="btn-add mt-2" id="add-ContactPoint">
                                    <span class="dashicons dashicons-plus"></span>
                                    <span class="lang-text" data-fr="Ajouter" data-en="Add">Ajouter</span>
                                </button>
                            </div>


                        </section>
                    </div>
                </div>
            </div>
        </section>

        <section key="FundingAgent">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseFundingAgent"
                    aria-expanded="true"
                    aria-controls="collapseFundingAgent"
                    data-fr="Financeur"
                    data-en="Funder">
                    Financeur
                </button>

                <div class="collapse show" id="collapseFundingAgent">
                    <div class="card card-body">
                        <section key="FundingAgent">
                            <?php
                            $tooltipFr = getTooltipByName('FundingAgent', 'fr', 'AdministrativeInformation');
                            $tooltipEn = getTooltipByName('FundingAgent', 'en', 'AdministrativeInformation'); ?>
                            <?php
                            $statusKey = "stdyDscr/citation/prodStmt/fundAg/values_{$current_lang}_status";
                            $isChanged = $compareStudy[$statusKey] ?? false;
                            ?>
                            <h3 class="lang-text-tooltip"
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Financeur"
                                data-en="Funder">
                                <span class="contentSection">Financeur</span>
                                <span class="info-bulle" attr-lng="fr"
                                    data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                                <?php if ($isChanged): ?>
                                    <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                        <i class="dashicons dashicons-edit"></i>
                                    </span>
                                <?php endif; ?>
                            </h3>
                            <div id="repeater-fundingAgent" class="repeaterBlock">
                                <div class="mb-4 repeater-item" data-section="FundingAgent">
                                    <div class="row">
                                        <div class="justify-content-end">
                                            <button type="button" class="btn-remove btn-remove-section">
                                                <span class="dashicons dashicons-trash"></span>
                                                <span class="lang-text"
                                                    data-fr="Supprimer"
                                                    data-en="Delete">
                                                    Supprimer
                                                </span>
                                            </button>
                                        </div>
                                    </div>


                                    <section key="FundingAgent">
                                        <?php $options = getListInstitutions(); ?>
                                        <div class="row" data-parent="FundingAgent">
                                            <div class="col-md-12 mb-3" ddiShema="stdyDscr/citation/prodStmt/fundAg"
                                                data-key="Institutions">
                                                <?php
                                                $tooltipFr = getTooltipByName('FundingAgentName', 'fr', 'FundingAgent');
                                                $tooltipEn = getTooltipByName('FundingAgentName', 'en', 'FundingAgent');
                                                nada_renderInputGroup2(
                                                    "Nom du financeur",
                                                    "Funder name",
                                                    "stdyDscr/citation/prodStmt/fundAg",
                                                    "autocomplete",
                                                    $options,
                                                    true,
                                                    true,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    true,
                                                    null,
                                                    null,
                                                    400
                                                );
                                                ?>
                                                <input type="hidden" class="institution-json-data" name="stdyDscr/citation/prodStmt/fundAg/institution_json" value="">
                                            </div>

                                            <div class="col-md-12 mb-3" data-key="FundingAgentType">
                                                <?php
                                                $options_FunderType = getReferentielByName('FundingAgentType');
                                                $tooltipFr = getTooltipByName('FundingAgentType', 'fr', 'FundingAgent');
                                                $tooltipEn = getTooltipByName('FundingAgentType', 'en', 'FundingAgent');
                                                nada_renderInputGroup(
                                                    "Type de financeur",
                                                    "Funder type",
                                                    "additional/fundingAgent/fundingAgentType",
                                                    "select",
                                                    $options_FunderType,
                                                    true,
                                                    true,
                                                    $tooltipFr,
                                                    $tooltipEn
                                                );
                                                ?>
                                            </div>
                                            <div class="col-md-12 mb-3 d-none" data-item="FundingAgent"
                                                id="showOtherFundingAgentType">
                                                <?php
                                                $tooltipFr = getTooltipByName('OtherFundingAgentType', 'fr', 'FundingAgent');
                                                $tooltipEn = getTooltipByName('OtherFundingAgentType', 'en', 'FundingAgent');
                                                nada_renderInputGroup(
                                                    "Autre type de financeur, précisions",
                                                    "Other funder type, details",
                                                    "additional/fundingAgent/otherFundingAgentType",
                                                    "textarea",
                                                    [],
                                                    true,
                                                    false,
                                                    $tooltipFr,
                                                    $tooltipEn
                                                );
                                                ?>
                                            </div>

                                        </div>

                                        <section key="FundingAgentPID" class="hidden-bloc">
                                            <?php
                                            $tooltipFr = getTooltipByName('FundingAgentPID', 'fr', 'FundingAgent');
                                            $tooltipEn = getTooltipByName('FundingAgentPID', 'en', 'FundingAgent'); ?>
                                            <h4 class="lang-text-tooltip"
                                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                                tooltip-en="<?php echo $tooltipEn; ?>"
                                                data-fr="Identification du financeur"
                                                data-en="Funder identification">
                                                <span class="contentSection">Identification du financeur</span>
                                                <span class="info-bulle"
                                                    attr-lng="fr"
                                                    data-text="<?php echo $tooltipFr; ?>">
                                                    <span class="dashicons dashicons-info"></span>
                                                </span>
                                            </h4>
                                            <div class="row controlChampsIdentifinat">
                                                <div class="col-md-6 col-sm-12 mb-3" data-key="FundingAgentPIDSchema">
                                                    <?php
                                                    $options_PIDSchema = getReferentielByName('FundingAgentPIDSchema');
                                                    $tooltipFr = getTooltipByName('PIDSchema', 'fr', 'FundingAgentPID');
                                                    $tooltipEn = getTooltipByName('PIDSchema', 'en', 'FundingAgentPID');
                                                    nada_renderInputGroup(
                                                        "Type d'identifiant du financeur",
                                                        "Funder identifier type",
                                                        "stdyDscr/citation/prodStmt/fundAg/ExtLink/title",
                                                        "select",
                                                        $options_PIDSchema,
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn
                                                    );
                                                    ?>

                                                </div>

                                                <div class="col-md-6 col-sm-12 mb-3">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('URI', 'fr', 'FundingAgentPID');
                                                    $tooltipEn = getTooltipByName('URI', 'en', 'FundingAgentPID');
                                                    nada_renderInputGroup(
                                                        "Identifiant du financeur",
                                                        "Funder identifier",
                                                        "stdyDscr/citation/prodStmt/fundAg/ExtLink/URI",
                                                        "text",
                                                        [],
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn
                                                    );
                                                    ?>
                                                </div>
                                            </div>
                                        </section>
                                    </section>
                                </div>
                            </div>

                            <div class="d-flex btnBlockRepeater">
                                <button type="button" class="btn-add mt-2" id="add-fundingAgent">
                                    <span class="dashicons dashicons-plus"></span>
                                    <span class="lang-text"
                                        data-fr="Ajouter"
                                        data-en="Add">
                                        Ajouter
                                    </span>
                                </button>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>

        <section key="OrganisationGovernance">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseOrganisationGovernance"
                    aria-expanded="true"
                    aria-controls="collapseOrganisationGovernance"
                    data-fr="Organisation et gouvernance"
                    data-en="Organisation and governance">
                    Organisation et gouvernance
                </button>
                <div class="collapse show" id="collapseOrganisationGovernance">
                    <div class="card card-body">
                        <section key="OrganisationGovernance">
                            <?php
                            $tooltipFr = getTooltipByName('OrganisationGovernance', 'fr', 'AdministrativeInformation');
                            $tooltipEn = getTooltipByName('OrganisationGovernance', 'en', 'AdministrativeInformation'); ?>
                            <h3 class="lang-text-tooltip"
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Organisation et gouvernance"
                                data-en="Organisation and governance">
                                <span class="contentSection">Organisation et gouvernance</span>
                                <span class="info-bulle" attr-lng="fr" data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                            </h3>


                            <section key="Sponsor">
                                <?php
                                $tooltipFr = getTooltipByName('Sponsor', 'fr', 'OrganisationGovernance');
                                $tooltipEn = getTooltipByName('Sponsor', 'en', 'OrganisationGovernance'); ?>
                                <?php
                                $statusKey = "stdyDscr/citation/prodStmt/producer/values_{$current_lang}_status";
                                $isChanged = $compareStudy[$statusKey] ?? false;
                                ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Promoteur de l'étude (Organisation responsable)"
                                    data-en="Sponsor (Responsible organisation)">

                                    <span class="contentSection">Promoteur de l'étude (Organisation responsable)</span>
                                    <span class="info-bulle" attr-lng="fr" data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                    <?php if ($isChanged): ?>
                                        <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                            <i class="dashicons dashicons-edit"></i>
                                        </span>
                                    <?php endif; ?>
                                </h4>
                                <i>
                                    <span class="lang-text subTitleSection"
                                        data-fr="Organisme responsable du traitement des données."
                                        data-en="Organisation responsible for data processing.">
                                        Organisme responsable du traitement des données.
                                    </span>
                                </i>
                                <div id="repeater-sponsor" class="repeaterBlock">
                                    <div class="mb-4 repeater-item" data-section="Sponsor">

                                        <div class="row">
                                            <div class="justify-content-end">
                                                <button type="button" class="btn-remove btn-remove-section ">
                                                    <span class="dashicons dashicons-trash"></span>
                                                    <span class="lang-text"
                                                        data-fr="Supprimer"
                                                        data-en="Delete">
                                                        Supprimer
                                                    </span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row" data-parent="Sponsor">

                                            <div class="col-md-12 mb-3" ddiShema="stdyDscr/citation/prodStmt/producer"
                                                data-key="Institutions">
                                                <?php
                                                $options = getListInstitutions();
                                                $tooltipFr = getTooltipByName('SponsorName', 'fr', 'Sponsor');
                                                $tooltipEn = getTooltipByName('SponsorName', 'en', 'Sponsor');
                                                nada_renderInputGroup2(
                                                    "Nom du promoteur",
                                                    "Sponsor name",
                                                    "stdyDscr/citation/prodStmt/producer",
                                                    "autocomplete",
                                                    $options,
                                                    true,
                                                    true,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    true
                                                ); ?>
                                                <input type="hidden" class="institution-json-data" name="stdyDscr/citation/prodStmt/producer/institution_json" value="">
                                            </div>

                                            <div class="col-md-12 mb-3" ddiShema="_custom" data-key="SponsorType">
                                                <?php
                                                $options = getReferentielByName('SponsorType');
                                                $tooltipFr = getTooltipByName('SponsorType', 'fr', 'Sponsor');
                                                $tooltipEn = getTooltipByName('SponsorType', 'en', 'Sponsor');
                                                nada_renderInputGroup(
                                                    "Statut du promoteur",
                                                    "Sponsor type",
                                                    "additional/sponsor/sponsorType",
                                                    "select",
                                                    $options,
                                                    true,
                                                    true,
                                                    $tooltipFr,
                                                    $tooltipEn
                                                );
                                                ?>
                                            </div>

                                            <div class="col-md-12 mb-3 d-none" data-item="Sponsor"
                                                id="showOtherSponsorType">
                                                <?php
                                                $tooltipFr = getTooltipByName('OtherSponsorType', 'fr', 'Sponsor');
                                                $tooltipEn = getTooltipByName('OtherSponsorType', 'en', 'Sponsor');
                                                nada_renderInputGroup(
                                                    "Autre statut du promoteur, précisions",
                                                    "Other sponsor type, details",
                                                    "additional/sponsor/otherSponsorType",
                                                    "textarea",
                                                    [],
                                                    true,
                                                    false,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    false,
                                                    null,
                                                    null,
                                                    400
                                                ); ?>
                                            </div>
                                        </div>

                                        <section key="SponsorPID" class="hidden-bloc">
                                            <?php
                                            $tooltipFr = getTooltipByName('SponsorPID', 'fr', 'Sponsor');
                                            $tooltipEn = getTooltipByName('SponsorPID', 'en', 'Sponsor'); ?>
                                            <h5 class="lang-text-tooltip"
                                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                                tooltip-en="<?php echo $tooltipEn; ?>"
                                                data-fr="Identification du promoteur"
                                                data-en="Sponsor identification">
                                                <span class="contentSection">Identification du promoteur</span>
                                                <span class="info-bulle"
                                                    attr-lng="fr"
                                                    data-text="<?php echo $tooltipFr; ?>">
                                                    <span class="dashicons dashicons-info"></span>
                                                </span>
                                            </h5>
                                            <div class="row controlChampsIdentifinat">
                                                <div class="col-md-6 col-sm-12 mb-3"
                                                    ddiShema="stdyDscr/citation/prodStmt/producer/ExtLink/PIDSchema"
                                                    data-key="SponsorPIDSchema">
                                                    <?php
                                                    $options = getReferentielByName('SponsorPIDSchema');
                                                    $tooltipFr = getTooltipByName('PIDSchema', 'fr', 'SponsorPID');
                                                    $tooltipEn = getTooltipByName('PIDSchema', 'en', 'SponsorPID');
                                                    nada_renderInputGroup(
                                                        "Type d'identifiant du promoteur",
                                                        "Sponsor identifier type",
                                                        "stdyDscr/citation/prodStmt/producer/ExtLink/title",
                                                        "select",
                                                        $options,
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn
                                                    );
                                                    ?>
                                                </div>

                                                <div class="col-md-6 col-sm-12 mb-3"
                                                    ddiShema="stdyDscr/citation/prodStmt/producer/ExtLink/URI">
                                                    <?php
                                                    $tooltipFr = getTooltipByName('URL', 'fr', 'SponsorPID');
                                                    $tooltipEn = getTooltipByName('URL', 'en', 'SponsorPID');
                                                    nada_renderInputGroup(
                                                        "Identifiant du promoteur",
                                                        "Sponsor identifier",
                                                        "stdyDscr/citation/prodStmt/producer/ExtLink/URI",
                                                        "text",
                                                        [],
                                                        true,
                                                        false,
                                                        $tooltipFr,
                                                        $tooltipEn
                                                    ); ?>
                                                </div>
                                            </div>

                                        </section>
                                    </div>
                                </div>
                                <div class="d-flex btnBlockRepeater">
                                    <button type="button" class="btn-add mt-2" id="add-sponsor">
                                        <span class="dashicons dashicons-plus"></span>
                                        <span class="lang-text" data-fr="Ajouter" data-en="Add">Ajouter</span>
                                    </button>
                                </div>
                            </section>

                            <section key="Governance">
                                <?php
                                $tooltipFr = getTooltipByName('Governance', 'fr', 'OrganisationGovernance');
                                $tooltipEn = getTooltipByName('Governance', 'en', 'OrganisationGovernance'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Gouvernance"
                                    data-en="Governance">
                                    <span class="contentSection">Gouvernance</span>
                                    <span class="info-bulle" attr-lng="fr" data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>


                                <div class="row">
                                    <div class="col-md-12 mb-3" ddiShema="_custom">
                                        <?php
                                        $options = [
                                            "fr" => [
                                                "Oui"  => "Oui",
                                                "Non" => "Non",
                                                "Autre" => "Autre"
                                            ],
                                            "en" => [
                                                "Yes"  => "Yes",
                                                "No" => "No",
                                                "Other" => "Other"
                                            ]
                                        ];
                                        $tooltipFr = getTooltipByName('Committee', 'fr', 'Governance');
                                        $tooltipEn = getTooltipByName('Committee', 'en', 'Governance');
                                        nada_renderInputGroup(
                                            "Comité scientifique ou de pilotage",
                                            "Scientific or steering committee",
                                            "additional/governance/committee",
                                            "radio",
                                            $options,
                                            true,
                                            true,
                                            $tooltipFr,
                                            $tooltipEn,
                                        );
                                        ?>
                                    </div>

                                    <div class="col-md-12 mb-3 d-none" id="committeeDetailBloc">
                                        <?php $tooltipFr = getTooltipByName('CommitteeDetail', 'fr', 'Governance');
                                        $tooltipEn = getTooltipByName('CommitteeDetail', 'en', 'Governance');
                                        nada_renderInputGroup(
                                            "Comité, précisions",
                                            "Committee details",
                                            "stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/committee",
                                            "textarea",
                                            [],
                                            true,
                                            false,
                                            $tooltipFr,
                                            $tooltipEn,
                                            false,
                                            null,
                                            null,
                                            1000
                                        ); ?>

                                    </div>

                                    <div class="col-md-12 mb-3 d-none" id="committeeDetailBlocOthers">
                                        <?php
                                        $tooltipFr = getTooltipByName('OtherGovernance', 'fr', 'Governance');
                                        $tooltipEn = getTooltipByName('OtherGovernance', 'en', 'Governance');
                                        nada_renderInputGroup(
                                            "Autre modalité de gouvernance, précisions",
                                            "Other form of gouvernance, details",
                                            "stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/producer/governance",
                                            "textarea",
                                            [],
                                            true,
                                            false,
                                            $tooltipFr,
                                            $tooltipEn,
                                            false,
                                            null,
                                            null,
                                            1000
                                        ); ?>
                                    </div>
                                </div>
                            </section>

                            <section key="Collaborations">
                                <?php
                                $tooltipFr = getTooltipByName('Collaborations', 'fr', 'OrganisationGovernance');
                                $tooltipEn = getTooltipByName('Collaborations', 'en', 'OrganisationGovernance'); ?>
                                <h4 class="lang-text-tooltip"
                                    tooltip-fr="<?php echo $tooltipFr; ?>"
                                    tooltip-en="<?php echo $tooltipEn; ?>"
                                    data-fr="Collaborations"
                                    data-en="Collaborations">
                                    <span class="contentSection">Collaborations</span>
                                    <span class="info-bulle" attr-lng="fr" data-text="<?php echo $tooltipFr; ?>">
                                        <span class="dashicons dashicons-info"></span>
                                    </span>
                                </h4>


                                <div class="row">
                                    <div class="col-md-12 mb-3" ddiShema="_custom">
                                        <?php
                                        $tooltipFr = getTooltipByName('NetworkConsortium', 'fr', 'Collaborations');
                                        $tooltipEn = getTooltipByName('NetworkConsortium', 'en', 'Collaborations');
                                        nada_renderInputGroup(
                                            "Réseaux, consortiums",
                                            "Networks, consortia",
                                            "additional/collaborations/networkConsortium",
                                            "radio",
                                            $options_boolean,
                                            true,
                                            true,
                                            $tooltipFr,
                                            $tooltipEn,
                                            true,
                                            null,
                                            null,
                                            400
                                        );
                                        ?>
                                    </div>
                                    <div class="col-md-12 mb-3 d-none" id="CollaborationDetailsPrecision">
                                        <?php
                                        $tooltipFr = getTooltipByName('CollaborationsDetails', 'fr', 'Collaborations');
                                        $tooltipEn = getTooltipByName('CollaborationsDetails', 'en', 'Collaborations');
                                        nada_renderInputGroup(
                                            "Collaboration(s) impliquée(s), précisions",
                                            "Collaboration(s) involved, details",
                                            "stdyDscr/citation/rspStmt/othId/collaboration",
                                            "textarea",
                                            [],
                                            true,
                                            true,
                                            $tooltipFr,
                                            $tooltipEn,
                                            false,
                                            null,
                                            null,
                                            1000
                                        ); ?>
                                    </div>
                                </div>
                            </section>
                        </section>
                    </div>
                </div>
            </div>
        </section>

        <section key="OtherStudyId">
            <div class="mb-4">
                <button class="btn btn-primary w-100 text-start btnCollpase lang-text"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseOtherStudyId"
                    aria-expanded="true"
                    aria-controls="collapseOtherStudyId"
                    data-fr="Autre(s) identifiant(s) de l'étude"
                    data-en="Other(s) ID for the study">
                    Autre(s) identifiant(s) de l'étude
                </button>

                <div class="collapse show" id="collapseOtherStudyId">
                    <div class="card card-body">


                        <section key="OtherStudyId">
                            <?php
                            $tooltipFr = getTooltipByName('OtherStudyId', 'fr', 'AdministrativeInformation');
                            $tooltipEn = getTooltipByName('OtherStudyId', 'en', 'AdministrativeInformation'); ?>
                            <?php
                            $statusKey = "stdyDscr/citation/titlStmt/IDNo/values_{$current_lang}_status";
                            $isChanged = $compareStudy[$statusKey] ?? false;
                            ?>
                            <h3 class="lang-text-tooltip"
                                tooltip-fr="<?php echo $tooltipFr; ?>"
                                tooltip-en="<?php echo $tooltipEn; ?>"
                                data-fr="Autre(s) identifiant(s) de l'étude"
                                data-en="Other(s) identifier(s) for the study">
                                <span class="contentSection">Autre(s) identifiant(s) de l'étude</span>
                                <span class="info-bulle"
                                    attr-lng="fr"
                                    data-text="<?php echo $tooltipFr; ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                                <?php if ($isChanged): ?>
                                    <span class="lang-label ms-2 text-warning d-none" title="Changement détecté" attr-lng="<?php echo $current_lang; ?>">
                                        <i class="dashicons dashicons-edit"></i>
                                    </span>
                                <?php endif; ?>
                            </h3>
                            <div id="repeater-OtherStudyId" class="repeaterBlock">
                                <div class="mb-4 repeater-item" data-section="OtherStudyId">
                                    <div class="row">
                                        <div class="justify-content-end">
                                            <button type="button" class="btn-remove btn-remove-section">
                                                <span class="dashicons dashicons-trash"></span>
                                                <span class="lang-text"
                                                    data-fr="Supprimer"
                                                    data-en="Delete">
                                                    Supprimer
                                                </span>
                                            </button>
                                        </div>
                                    </div>


                                    <section class="row" key="OtherStudyId">
                                        <div class="row">
                                            <div class="col-md-12 mb-3" ddiShema="additional/contactPointLabo">
                                                <?php $tooltipFr = getTooltipByName('IDSchema', 'fr', 'OtherStudyId');
                                                $tooltipEn = getTooltipByName('IDSchema', 'en', 'OtherStudyId');
                                                nada_renderInputGroup(
                                                    "Type d'identifiant de l'étude",
                                                    "Study identifier type",
                                                    "stdyDscr/citation/titlStmt/IDNo/agency",
                                                    "textarea",
                                                    [],
                                                    true,
                                                    false,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    true,
                                                    null,
                                                    null,
                                                    400
                                                ); ?>
                                            </div>

                                            <div class="col-md-12 mb-3" ddiShema="additional/contactPointLabo">
                                                <?php
                                                $tooltipFr = getTooltipByName('Identifier', 'fr', 'OtherStudyId');
                                                $tooltipEn = getTooltipByName('Identifier', 'en', 'OtherStudyId');
                                                nada_renderInputGroup(
                                                    "Identifiant de l'étude",
                                                    "Study identifier",
                                                    "stdyDscr/citation/titlStmt/IDNo",
                                                    "textarea",
                                                    [],
                                                    true,
                                                    false,
                                                    $tooltipFr,
                                                    $tooltipEn,
                                                    true,
                                                    null,
                                                    null,
                                                    400
                                                ); ?>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>

                            <div class="d-flex btnBlockRepeater">
                                <button type="button" class="btn-add mt-2" id="add-OtherStudyId">
                                    <span class="dashicons dashicons-plus"></span>
                                    <span class="lang-text"
                                        data-fr="Ajouter"
                                        data-en="Add">
                                        Ajouter
                                    </span>
                                </button>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>

    </section>
</div>
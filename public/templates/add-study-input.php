<?php

if (! function_exists('nada_renderInputGroup')) {
    /**
     * Génère un bloc input group multilingue ou non
     *
     * @param string $label_fr     Label du champ en Fr
     * @param string $label_en     Label du champ en EN
     * @param string $tooltip_fr     description du champ en Fr
     * @param string $tooltip_en     description du champ en EN
     * @param string $name      Nom du champ
     * @param string $type      text | textarea | select | select-multiple | date | radio | checkbox | email | tags | url
     * @param array  $options   Options pour select/radio/checkbox (['valeur' => 'Libellé'])
     * @param bool   $multilang Multilingue (FR/EN) ou non
     * @param bool   $required  Champ obligatoire ?
     */
    function nada_renderInputGroup($label_fr, $label_en, $name, $type = 'text', $options = [], $multilang = true, $required = false, $tooltip_fr = null, $tooltip_en = null, $same = false)
    {

        // $required = false;
        global $mode;
        global $jsonRec; // tableau associatif JSON (payload)
        global $studyDetails;

        // Helper: normaliser une valeur en tableau (pour checkbox/select-multiple/tags)
        $to_array = static function ($v): array {
            if ($v === null || $v === '') return [];
            if (is_array($v)) return $v;
            return [$v];
        };
        // Helper: escape attr & html
        $e = static fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

        // langues
        $langs = $multilang ? ['fr' => 'Français', 'en' => 'English'] : ['' => ''];
        //var_dump($langs);

        $allData = [];
        $readClass = "";
        if ($mode == 'detail') {
            $readClass = "readInput";
        }

?>
        <div class="card">
            <div class="input-group">
                <?php if ($mode != 'detail'): ?>

                    <div class="d-flex justify-content-between">
                        <div class="d-flex hidden">
                            <?php if ($type == 'text' || $type == 'textarea' || $type == 'tags') { ?>
                                <span class="badge bg-warning mt-2">Incomplet</span>
                            <?php } ?>
                        </div>
                        <div class="d-flex hidden">
                            <?php if ($multilang): ?>
                                <ul class="nav nav-pills m-0">
                                    <li class="nav-item langue">
                                        <a class="nav-link active" data-lang="fr" href="#">FR</a>
                                    </li>
                                    <li class="nav-item langue">
                                        <a class="nav-link" data-lang="en" href="#">EN</a>
                                    </li>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php

                foreach ($langs as $lng => $labelLng):
                    $suffix = $lng ? "_$lng" : "";
                    $reqAttr = $required ? 'required' : '';
                    $hideClass = ($lng === 'en' && $multilang) ? 'd-none' : '';
                    $label = $lng == 'en' ? $label_en : $label_fr;
                    $placeholderSelect = $lng == 'en' ? "Select" : "Sélectionner";


                    if ($type == 'checkbox' && $options['fr']) {
                        $options_checkbox = $options[$lng];
                    } else {
                        $options_checkbox = $options;
                    }

                    if ($type == 'select' && $options['fr']) {
                        $options_select = $options[$lng];
                    } else {
                        $options_select = $options;
                    }

                    if ($type == 'radio' && $options['fr']) {
                        $options_radio = $options[$lng];
                    } else {
                        $options_radio = $options;
                    }

                ?>

                    <div class="d-flex">
                        <label class="form-label mb-2 ">
                            <span class="lang-label <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <?php echo ($lng == 'en') ? $e($label_en) : $e($label_fr); ?>
                                <?php if ($required): ?>
                                    <span class="text-danger">*</span>
                                <?php endif; ?>
                            </span>

                            <?php if (!empty($tooltip_fr) || !empty($tooltip_en)): ?>
                                <!-- Info-bulle -->
                                <span class="info-bulle lang-label <?php echo $hideClass; ?>"
                                    attr-lng="<?php echo $e($lng); ?>"
                                    data-text="<?php echo ($lng === 'en') ? $e($tooltip_en) : $e($tooltip_fr); ?>">
                                    <span class="dashicons dashicons-info"></span>
                                </span>
                            <?php endif; ?>
                        </label>
                    </div>
                    <?php
                    //echo $reqAttr;

                    switch ($type):
                        case 'textarea': ?>
                            <textarea
                                class="form-control lang-input <?php echo $hideClass; ?> <?php echo $readClass; ?>"
                                <?php echo ($mode == "detail" ? 'disabled' : ''); ?>
                                attr-lng="<?php echo $e($lng); ?>"
                                name="<?php echo $e($name . $suffix); ?>"
                                placeholder="<?php echo $label; ?>"><?php echo $jsonRec[$name . $suffix]; ?></textarea>
                        <?php break;

                        case 'select': ?>
                            <div class="lang-input <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <select
                                    class="form-select <?php echo $readClass; ?>"
                                    <?php echo ($mode == "detail" ? 'disabled' : ''); ?>
                                    name="<?php echo $e($name . $suffix); ?>">
                                    <option value="">-- <?php echo $placeholderSelect; ?> --</option>
                                    <?php
                                    $selected = (string)($rawValue ?? '');
                                    foreach ($options_select as $val => $text):
                                        $sel = ((string)$val === $selected) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $e($val); ?>" <?php echo $sel; ?>>
                                            <?php echo $e($text); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        <?php break;

                        case 'select-multiple':
                            $selectedVals = $to_array($rawValue); ?>
                            <div class="lang-input <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <select
                                    class="form-select lang-input select2 <?php echo $hideClass; ?> <?php echo $readClass; ?>"
                                    <?php echo ($mode == "detail" ? 'disabled' : ''); ?>
                                    attr-lng="<?php echo $e($lng); ?>"
                                    name="<?php echo $e($name . $suffix); ?>[]"
                                    multiple="multiple"
                                    data-placeholder="-- Sélectionner --">
                                    <option value=""></option>
                                    <?php foreach ($options as $val => $text):
                                        $sel = in_array((string)$val, array_map('strval', $selectedVals), true) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $e($val); ?>" <?php echo $sel; ?>>
                                            <?php echo $e($text); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php break;

                        case 'tags':
                            // $rawValue peut être tableau de tags ou chaîne (séparée par virgule)
                            $tags = $to_array($rawValue);
                        ?>
                            <div class="tags-input-container <?php echo $hideClass; ?>" data-tags="<?php echo $e($name . $suffix); ?>">
                                <div class="tags-wrapper" data-tags="<?php echo $e($name . $suffix); ?>">
                                    <?php foreach ($tags as $t): if ($t === '' || $t === null) continue; ?>
                                        <span class="tag"><?php echo $e($t); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <input type="text" class="tags-input <?php echo $readClass; ?>" data-tags="<?php echo $e($name . $suffix); ?>" placeholder="Tapez et appuyez sur Enter ou virgule">
                                <?php foreach ($tags as $t): ?>
                                    <input type="hidden" name="<?php echo $e($name . $suffix); ?>[]" class="tags-hidden" data-tags="<?php echo $e($name . $suffix); ?>" value="<?php echo $e($t); ?>">
                                <?php endforeach; ?>
                            </div>
                        <?php break;

                          case 'radio':
                            // Normaliser les booléens en string
                            if (is_bool($rawValue)) {
                                $current = $rawValue ? "true" : "false";
                            } else {
                                $current = (string)($rawValue ?? '');
                            }

                        ?>
                            <div class="lang-input <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <?php
                                foreach ($options_radio as $val => $text):
                                    $id = $name . '_' . $lng . '_' . preg_replace('~[^a-z0-9]+~i', '-', (string)$val);
                                    $checked = ((string)$val === $current) ? 'checked' : '';
                                ?>
                                    <input type="radio" class="btn-check  <?php echo $hideClass; ?> <?php echo $readClass; ?>"
                                        <?php echo ($mode == "detail" ? 'disabled' : ''); ?>
                                        attr-lng="<?php echo $e($lng); ?>"
                                        name="<?php echo $e($name . $suffix); ?>"
                                        id="<?php echo $e($id); ?>"
                                        value="<?php echo $e($val); ?>"
                                        <?php echo $checked; ?>>
                                    <label class="btn btn-outline-primary me-2" for="<?php echo $e($id); ?>">
                                        <?php echo $e($text); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php
                            break;

                        case 'email': ?>
                            <?php
                            if ($mode == "detail") { ?>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#emailModal" class="email-study-btn-nada" data-bs-value="<?php echo $jsonRec[$name]; ?>">
                                    Afficher l’email
                                </a>

                            <?php } else { ?>

                            <?php } ?>



                        <?php break;

                        case 'date': ?>

                        <?php break;

                        case 'checkbox':
                        ?>
                            <div class="input-group-prefix lang-input row <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <?php
                                $selectedVals = $to_array($rawValue);

                                $hasParent = false;

                                foreach ($options_checkbox as $text) {
                                    if (str_contains($text, ':')) {
                                        $hasParent = true;
                                        break;
                                    }
                                }

                                foreach ($options_checkbox as $val => $text):
                                    $id = $name . '_' . $lng . '_' . preg_replace('~[^a-z0-9]+~i', '-', (string)$text);
                                    $checked = in_array((string)$val, array_map('strval', $selectedVals), true) ? 'checked' : '';

                                    if (!$hasParent) {
                                        $indentClass = 'col-4';
                                    } else {
                                        if (str_contains($text, ':')) {
                                            $indentClass = 'col-12 child-checkbox';
                                        } else {
                                            $indentClass = 'col-12 parent-checkbox';
                                        }
                                    }

                                    $isChild = str_contains($text, ':');
                                    $parentKey = $isChild ? explode(':', $text)[0] : null;

                                ?>
                                    <div class="form-check <?php echo $indentClass . ' ' . $readClass; ?>">
                                        <input class="form-check-input lang-input"
                                            <?php echo ($mode == "detail" ? 'disabled' : ''); ?>
                                            type="checkbox"
                                            attr-lng="<?php echo $e($lng); ?>"
                                            name="<?php echo $e($name . $suffix); ?>[]"
                                            id="<?php echo $e($id); ?>"
                                            value="<?php echo $e($val); ?>"
                                            <?php echo $checked; ?>
                                            <?php echo $isChild
                                                ? ' data-child="' . trim($e($parentKey)) . '"'
                                                : ' data-parent="' . trim($e($text)) . '"'; ?>>

                                        <label class="form-check-label" for="<?php echo $e($id); ?>">
                                            <?php echo $e($text); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php
                            break;

                        case 'checkbox-multi':
                        ?>
                            <div class="input-group-prefix lang-input row <?php echo $hideClass; ?>" attr-lng="<?php echo $e($lng); ?>">
                                <?php

                                foreach ($options as $val => $text):
                                    $id = $name . '_' . $lng . '_' . preg_replace('~[^a-z0-9]+~i', '-', (string)$text[$lng]);
                                ?>
                                    <div class="form-check col-md-4 <?php echo $readClass; ?>">
                                        <input class="form-check-input lang-input"
                                            <?php echo ($mode == "detail" ? 'disabled' : ''); ?>
                                            type="checkbox"
                                            attr-lng="<?php echo $e($lng); ?>"
                                            name="<?php echo $e($name . $suffix); ?>[]"
                                            id="<?php echo $e($id); ?>"
                                            value="<?php echo $e($val); ?>">
                                        <label class="form-check-label" for="<?php echo $e($id); ?>">
                                            <?php echo $e($text[$lng]); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php
                            break;

                        case 'url':
                            // stocké en clair, sans gestion protocol dans la valeur
                            $val = (string)($rawValue ?? '');
                            // si la valeur commence par http(s)://, on peut l’afficher sans le répéter après le préfixe

                        ?>

                        <?php break;

                        default: // text
                        ?>
                            <input type="text" class="form-control lang-input <?php if ($same) {
                                                                                    echo 'same-data';
                                                                                }; ?> <?php echo $hideClass; ?> <?php echo $readClass; ?>"
                                <?php echo ($mode == "detail" ? 'disabled' : ''); ?>
                                attr-lng="<?php echo $e($lng); ?>"
                                name="<?php echo $e($name . $suffix); ?>"
                                placeholder="<?php echo $label; ?>"
                                value="<?php echo $jsonRec[$name . $suffix]; ?>">
                <?php
                    endswitch;
                endforeach; ?>

                <?php
                $cleanHost = preg_replace('~^https?://~i', '', $val);
                if ($type == 'url') {
                ?>
                    <div class="input-group-prefix one-input">
                        <span class="input-group-text">http://</span>
                        <input type="text"
                            attr-placeholder-fr="<?php echo $label_fr; ?>"
                            attr-placeholder-en="<?php echo $label_en; ?>"
                            class="form-control prefixInput"
                            placeholder="<?php echo $label_fr; ?>"
                            name="<?php echo $e($name); ?>"
                            value="<?php echo $e($cleanHost); ?>">
                    </div>
                <?php }
                if ($type == 'email') { ?>
                    <div class="one-input">
                        <input type="email"
                            attr-placeholder-fr="<?php echo $label_fr; ?>"
                            attr-placeholder-en="<?php echo $label_en; ?>"
                            class="form-control"
                            placeholder="<?php echo $label_fr; ?>"
                            name="<?php echo $e($name); ?>"
                            value>
                    </div>
                <?php }
                if ($type == 'date') { ?>
                    <div class="one-input">
                        <input type="date"
                            attr-placeholder-fr="<?php echo $label_fr; ?>"
                            attr-placeholder-en="<?php echo $label_en; ?>"
                            class="form-control"
                            placeholder="<?php echo $label_fr; ?>"
                            name="<?php echo $e($name); ?>">
                    </div>
                <?php }
                ?>
            </div>
        </div>
<?php
    }
}
?>
<script>
    jQuery(window).on("load", function() {

        <?php if ($mode == "detail" || $mode == "edit") { ?>
            if (window._autoClickExecuted) return;
            window._autoClickExecuted = true;

            <?php if ($mode == "detail") { ?>
                jQuery('.btn-add').css('visibility', 'hidden');
                jQuery('.btn-remove').css('display', 'none');
                jQuery('.btn-remove').css('margin-bottom', '0px');
            <?php } ?>

            <?php if ($mode == "edit" && $studyDetails) { ?>
                let piEmail = "<?php echo $studyDetails['pi_email']; ?>";
                // email du user connecté
                let userEmail = "<?php echo $current_user->user_email; ?>";

                // Initialisation PI au chargement
                if (piEmail === userEmail) {
                    let $radioCreatorIsPiValue = jQuery(`[name^="creatorIsPi"][value="Yes"]`);
                    $radioCreatorIsPiValue.prop("disabled", false).prop("checked", true).trigger("change")
                } else {
                    let $radioCreatorIsPiValue = jQuery(`[name^="creatorIsPi"][value="No"]`);
                    $radioCreatorIsPiValue.prop("disabled", false).prop("checked", true).trigger("change")
                    let creatorIsPiValueEmail = "<?php echo addslashes($studyDetails['pi_email']); ?>";
                    if (creatorIsPiValueEmail) {
                        jQuery('[name="pi-email"]').val(creatorIsPiValueEmail).change();
                    }

                }

            <?php } ?>

            // arms bloc
            const container_armsBloc = jQuery(`[data-containerToDuplicate="armsBloc"]`);
            const armsBloc = container_armsBloc.find(`[data-inputToDuplicate="armsBloc"] input`);

            const wasDisabled = armsBloc.prop('disabled');
            armsBloc.prop('disabled', false);
            // Déclencher le change
            armsBloc.trigger('input').trigger('change'); // si tu veux les deux
            // Rétablir l'état disabled
            armsBloc.prop('disabled', wasDisabled);


            const container_InclusionGroups = jQuery(`[data-containerToDuplicate="inclusionGroupsBloc"]`);
            const InclusionGroups = container_InclusionGroups.find(`[data-inputToDuplicate="inclusionGroupsBloc"] input`);

            const wasDisabled_InclusionGroups = InclusionGroups.prop('disabled');
            InclusionGroups.prop('disabled', false);
            // Déclencher le change
            InclusionGroups.trigger('input').trigger('change'); // si tu veux les deux
            // Rétablir l'état disabled
            InclusionGroups.prop('disabled', wasDisabled_InclusionGroups);

            /***** STEP 1 */

            <?php $langs = ['fr', 'en']; ?>
            let lang = '';
            let authorizingAgencyData = {}; // objet et non tableau
            let investigatorData = {};
            let contributorData = {};
            let contributorData_fr = {};
            let contactData = {};
            let fundingAgenciesData = {};
            let investigatorsNumber = 0;
            let contributorsNumber = 0;
            let prodStmtProducer = {};
            let additionalGovernanceCommittee = '';
            let additionalNetworkConsortium = '';
            let topcClasTopic = '';
            let interventionData = {};
            let sourcesData = {};
            let contactUseStmtData = {};
            let standardNameData = {};
            let complianceDescriptionData = {};
            let IDnoData = {};

            <?php foreach ($langs as $lng): ?>

                lang = '<?= $lng ?>';

                authorizingAgencyData['<?= $lng ?>'] = {
                    data: <?= json_encode($jsonRec["stdyDscr/studyAuthorization/authorizingAgency_$lng"]) ?>,
                    additional: <?= json_encode($jsonRec["additional/obtainedAuthorization/otherAuthorizingAgency_$lng"]) ?>
                };

                const rawData_<?= $lng ?> = <?= json_encode($jsonRec["stdyDscr/citation/rspStmt/AuthEnty_$lng"] ?? []) ?>;

                // Séparer investigators et affiliations
                investigatorData['<?= $lng ?>'] = {
                    investigators: rawData_<?= $lng ?>.filter(item => item.type === "investigator"),
                    affiliations: rawData_<?= $lng ?>.filter(item => item.type === "affiliation")
                };

                // Mettre à jour investigatorsNumber avec le max du nombre d’investigators
                investigatorsNumber = Math.max(
                    investigatorsNumber,
                    investigatorData['<?= $lng ?>'].investigators.length
                );

                contributorData['<?= $lng ?>'] = <?= json_encode($jsonRec["stdyDscr/citation/rspStmt/othId_$lng"] ?? []) ?>;
                contactData['<?= $lng ?>'] = <?= json_encode($jsonRec["stdyDscr/citation/distStmt/contact_$lng"]) ?>;
                fundingAgenciesData['<?= $lng ?>'] = {
                    data: <?= json_encode($jsonRec["stdyDscr/citation/prodStmt/fundAg_$lng"]) ?>,
                    additional: <?php echo json_encode($jsonRec["additional/fundingAgent/fundingAgentType_$lng"]); ?>
                }

                prodStmtProducer = <?= json_encode($jsonRec["stdyDscr/citation/prodStmt/producer_$lng"] ?? []) ?>;
                if (prodStmtProducer.length) {
                    jQuery(`[name="stdyDscr/citation/prodStmt/producer_<?= $lng ?>"]`).val(prodStmtProducer[0].name).change();
                    jQuery(`select[name="stdyDscr/citation/prodStmt/producer/ExtLink/title_<?= $lng ?>"]`).val(prodStmtProducer[0].extlink.title).change();
                    jQuery('[name="stdyDscr/citation/prodStmt/producer/ExtLink/uri"]').val(prodStmtProducer[0].extlink.uri).change();

                    const additionalSponsorType_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/sponsor/sponsorType_$lng"]); ?>;
                    jQuery(`select[name="additional/sponsor/sponsorType_<?= $lng ?>"]`).val(additionalSponsorType_<?= $lng ?>).change();
                }

                additionalGovernanceCommittee = <?php echo json_encode($jsonRec["additional/governance/committee_$lng"]); ?>;
                if (additionalGovernanceCommittee) {
                    const $radioCommittee_<?= $lng ?> = jQuery(`[name="additional/governance/committee_<?= $lng ?>"][value="${additionalGovernanceCommittee}"]`);
                    $radioCommittee_<?= $lng ?>.prop("disabled", false).prop("checked", true).trigger("change");
                }

                additionalNetworkConsortium = <?php echo json_encode($jsonRec["additional/collaborations/networkConsortium_$lng"]); ?>;
                if (additionalGovernanceCommittee) {
                    const $radioNetworkConsortium_<?= $lng ?> = jQuery(`[name="additional/collaborations/networkConsortium_<?= $lng ?>"][value="${additionalNetworkConsortium}"]`);
                    $radioNetworkConsortium_<?= $lng ?>.prop("disabled", false).prop("checked", true).trigger("change")
                }

                const stdyClas_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/method/stdyClas_$lng"]); ?>;
                jQuery(`select[name="stdyDscr/method/stdyClas_<?= $lng ?>"]`).val(stdyClas_<?= $lng ?>).change();

                topcClasTopic = <?php echo json_encode($jsonRec["stdyDscr/stdyInfo/subject/topcClas_$lng"]); ?>;
                if (topcClasTopic) {
                    const healthTheme_<?= $lng ?> = topcClasTopic.filter(item => item.vocab === "health theme").map(item => item.topic);
                    if (healthTheme_<?= $lng ?>.length) {
                        setFieldValues(healthTheme_<?= $lng ?>, "stdyDscr/stdyInfo/subject/topcClas[]/value_<?= $lng ?>[]", "checkbox");
                    }

                    const cim11_<?= $lng ?> = topcClasTopic.filter(item => item.vocab === "cim-11").map(item => item.topic);
                    if (cim11_<?= $lng ?>.length) {
                        setFieldValues(cim11_<?= $lng ?>, "stdyDscr/stdyInfo/subject/topcClas[]/value/cim-11[]", "select");
                    }

                    const healthDeterminant_<?= $lng ?> = topcClasTopic.filter(item => item.vocab === "health determinant").map(item => item.topic);
                    if (healthDeterminant_<?= $lng ?>.length) {
                        setFieldValues(healthDeterminant_<?= $lng ?>, "stdyDscr/stdyInfo/subject/topcClas_<?= $lng ?>[]", "checkbox");
                    }
                }



                const stdyInfoKeyword_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/stdyInfo/subject/keyword_$lng"]); ?>;
                if (stdyInfoKeyword_<?= $lng ?> && stdyInfoKeyword_<?= $lng ?>.length > 0 && stdyInfoKeyword_<?= $lng ?>[0].keyword) {
                    jQuery('[name="stdyDscr/stdyInfo/subject/keyword_<?= $lng ?>"]')
                        .val(stdyInfoKeyword_<?= $lng ?>[0].keyword)
                        .change();
                }

                const additionalRareDiseases_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/theme/RareDiseases_$lng"]); ?>;
                const $radioRareDiseases_<?= $lng ?> = jQuery(`[name="additional/theme/RareDiseases_<?= $lng ?>"][value="${additionalRareDiseases_<?= $lng ?>}"]`);
                $radioRareDiseases_<?= $lng ?>.prop("disabled", false).prop("checked", true).trigger("change");

                /** step 2 */
                jQuery('select[name="stdyDscr/stdyInfo/sumDscr/anlyUnitFake_<?= $lng ?>"]').prop('disabled', true);

                const researchTypeSubject_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/method/notes/subject_researchType_$lng"]); ?>;
                if (researchTypeSubject_<?= $lng ?>) {
                    jQuery('select[name="stdyDscr/method/notes/subject_researchType_<?= $lng ?>"]').val(researchTypeSubject_<?= $lng ?>).change();
                }

                //Remplissage checkbox
                const researchPurpose_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/interventionalStudy/researchPurpose_$lng"]); ?>;
                if (researchPurpose_<?= $lng ?> && researchPurpose_<?= $lng ?>.length) {
                    setFieldValues(researchPurpose_<?= $lng ?>, `additional/interventionalStudy/researchPurpose_${lang}[]`, "checkbox");
                }

                const trialPhase_<?= $lng ?> = <?php echo json_encode(value: $jsonRec["additional/interventionalStudy/trialPhase_$lng"]); ?>;
                if (trialPhase_<?= $lng ?> && trialPhase_<?= $lng ?>.length) {
                    setFieldValues(trialPhase_<?= $lng ?>, `additional/interventionalStudy/trialPhase_${lang}[]`, "checkbox");
                }

                const interventionalStudyShema_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/interventionalStudy/interventionalStudyModel_$lng"]); ?>;
                if (interventionalStudyShema_<?= $lng ?>) {
                    jQuery(`select[name="additional/interventionalStudy/interventionalStudyModel_${lang}"]`).val(interventionalStudyShema_<?= $lng ?>).change();
                }

                const allocationMode_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/allocation/allocationMode_$lng"]); ?>;
                jQuery(`select[name="additional/allocation/allocationMode_${lang}"]`).val(allocationMode_<?= $lng ?>).change();

                const allocationUnit_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/allocation/allocationUnit_$lng"]); ?>;
                jQuery(`select[name="additional/allocation/allocationUnit_${lang}"]`).val(allocationUnit_<?= $lng ?>).change();

                const maskingType_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/masking/maskingType_$lng"]); ?>;
                jQuery(`select[name="additional/masking/maskingType_${lang}"]`).val(maskingType_<?= $lng ?>).change();

                const blindedMaskingDetails_<?= $lng ?> = <?php echo json_encode(value: $jsonRec["additional/masking/blindedMaskingDetails_$lng"]); ?>;
                if (blindedMaskingDetails_<?= $lng ?> && blindedMaskingDetails_<?= $lng ?>.length) {
                    setFieldValues(blindedMaskingDetails_<?= $lng ?>, `additional/masking/blindedMaskingDetails_${lang}[]`, "checkbox");
                }

                const dataArms_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/arms_$lng"]); ?>;
                if (dataArms_<?= $lng ?> && dataArms_<?= $lng ?>.length) {
                    autoFillArms(dataArms_<?= $lng ?>, lang);
                }
                /* Start Repeater Intervention/Exposure */
                interventionData['<?= $lng ?>'] = <?= json_encode($jsonRec["additional/intervention_$lng"] ?? []) ?>;
                /* End Repeater Intervention/Exposure */


                const dataInclusionGroups_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/inclusionGroups_$lng"]) ?>;
                if (dataInclusionGroups_<?= $lng ?> && dataInclusionGroups_<?= $lng ?>.length) {
                    autoFillInclusionGroups(dataInclusionGroups_<?= $lng ?>, lang);
                }
                /***** STEP 3 */

                const targetSampleSize_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/method/dataColl/targetSampleSize_$lng"]); ?>;
                jQuery(`select[name="stdyDscr/method/dataColl/targetSampleSize_${lang}"]`).val(targetSampleSize_<?= $lng ?>).change();
                jQuery(`[name="stdyDscr/method/dataColl/respRate_${lang}"]`).value;

                const timeMeth_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/method/dataColl/timeMeth_$lng"]); ?>;
                if (timeMeth_<?= $lng ?>) {
                    jQuery('select[name="stdyDscr/method/dataColl/timeMeth_<?= $lng ?>"]').val(timeMeth_<?= $lng ?>).change();
                }

                const recrutementTiming_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/cohortLongitudinal/recrutementTiming_$lng"]); ?>;
                if (recrutementTiming_<?= $lng ?> && recrutementTiming_<?= $lng ?>.length) {
                    setFieldValues(recrutementTiming_<?= $lng ?>, `additional/cohortLongitudinal/recrutementTiming_${lang}[]`, "checkbox");
                }

                const collMode_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/method/dataColl/collMode_$lng"]); ?>;
                if (collMode_<?= $lng ?> && collMode_<?= $lng ?>.length) {
                    setFieldValues(collMode_<?= $lng ?>, `stdyDscr/method/dataColl/collMode_${lang}[]`, "checkbox");
                }

                const inclusionStrategy_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/dataCollection/inclusionStrategy_$lng"]); ?>;
                if (inclusionStrategy_<?= $lng ?> && inclusionStrategy_<?= $lng ?>.length) {
                    setFieldValues(inclusionStrategy_<?= $lng ?>, `additional/dataCollection/inclusionStrategy_${lang}[]`, "checkbox");
                }

                let sampProc_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/method/dataColl/sampProc_$lng"]); ?>;
                sampProc_<?= $lng ?> = parsePseudoArray(sampProc_<?= $lng ?>)
                setFieldValues(sampProc_<?= $lng ?>, `stdyDscr/method/dataColl/sampProc_${lang}[]`, "checkbox");

                let frameUnitType_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_$lng"]); ?>;
                if (frameUnitType_<?= $lng ?> && frameUnitType_<?= $lng ?>.length) {
                    frameUnitType_<?= $lng ?> = parsePseudoArray(frameUnitType_<?= $lng ?>)
                    setFieldValues(frameUnitType_<?= $lng ?>, `stdyDscr/method/dataColl/sampleFrame/frameUnit/unitType_${lang}[]`, "checkbox");
                }

                // let typeSource_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/method/dataColl/sources/srcOrig_$lng"]); ?>;
                // typeSource_<?= $lng ?> = parsePseudoArray(typeSource_<?= $lng ?>)
                // setFieldValues(typeSource_<?= $lng ?>, `stdyDscr/method/dataColl/sources/srcOrig_${lang}[]`, "checkbox");



                const collDates_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/stdyInfo/sumDscr/collDate_$lng"]); ?>;
                if (collDates_<?= $lng ?> && collDates_<?= $lng ?>.length > 0) {
                    let eventStart = collDates_<?= $lng ?>[0]?.start || '';
                    let eventEnd = collDates_<?= $lng ?>[0]?.end || '';
                    jQuery('[name="stdyDscr/stdyInfo/sumDscr/collDate/event_start"]').val(eventStart).change();
                    jQuery('[name="stdyDscr/stdyInfo/sumDscr/collDate/event_end"]').val(eventEnd).change();
                }

                isActiveFollowUp = <?php echo json_encode($jsonRec["additional/activeFollowUp/isActiveFollowUp_$lng"]); ?>;
                if (isActiveFollowUp) {
                    const $radioActiveFollowUp_<?= $lng ?> = jQuery(`[name="additional/activeFollowUp/isActiveFollowUp_<?= $lng ?>"][value="${isActiveFollowUp}"]`);
                    $radioActiveFollowUp_<?= $lng ?>.prop("disabled", false).prop("checked", true).trigger("change");
                }

                const subject_followUP_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/method/notes/subject_followUP_$lng"]); ?>;
                if (subject_followUP_<?= $lng ?> && subject_followUP_<?= $lng ?>.length) {
                    setFieldValues(subject_followUP_<?= $lng ?>, `stdyDscr/method/notes/subject_followUP_${lang}[]`, "checkbox");
                }

                isDataIntegration = <?php echo json_encode($jsonRec["additional/dataCollectionIntegration/isDataIntegration_$lng"]); ?>;
                if (isDataIntegration) {
                    const $radioisDataIntegration_<?= $lng ?> = jQuery(`[name="additional/dataCollectionIntegration/isDataIntegration_<?= $lng ?>"][value="${isDataIntegration}"]`);
                    $radioisDataIntegration_<?= $lng ?>.prop("disabled", false).prop("checked", true).trigger("change");
                }

                sourcesData['<?= $lng ?>'] = <?= json_encode($jsonRec["stdyDscr/method/dataColl/sources_$lng"] ?? []) ?>;


                const nation_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/stdyInfo/sumDscr/nation_$lng"]); ?>;
                if (nation_<?= $lng ?> && nation_<?= $lng ?>.length) {
                    jQuery(`select[name="stdyDscr/stdyInfo/sumDscr/nation_${lang}"]`).val(nation_<?= $lng ?>[0].name ?? '').change();
                }

                let geogCoverage_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/stdyInfo/sumDscr/geogCover_$lng"]); ?>;
                if (geogCoverage_<?= $lng ?>) {
                    geogCoverage_<?= $lng ?> = parsePseudoArray(geogCoverage_<?= $lng ?>)
                    setFieldValues(geogCoverage_<?= $lng ?>, `stdyDscr/stdyInfo/sumDscr/geogCover_${lang}[]`, "checkbox");
                }

                // universe logic
                const level_type_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I_$lng"]); ?>;
                if (level_type_<?= $lng ?> && level_type_<?= $lng ?>.length) {
                    jQuery(`select[name="stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I_${lang}"]`).val(level_type_<?= $lng ?>).change();
                }

                const level_age_clusion_I_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I_$lng"]); ?>;
                if (level_age_clusion_I_<?= $lng ?> && level_age_clusion_I_<?= $lng ?>.length) {
                    setFieldValues(level_age_clusion_I_<?= $lng ?>, `stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I_${lang}[]`, "checkbox");
                }

                const level_sex_clusion_I_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I_$lng"]); ?>;
                if (level_sex_clusion_I_<?= $lng ?> && level_sex_clusion_I_<?= $lng ?>.length) {
                    setFieldValues(level_sex_clusion_I_<?= $lng ?>, `stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I_${lang}[]`, "checkbox");
                }
                //End unuverse

                // --- step3 ---

                const avlStatus_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/dataAccs/setAvail/avlStatus_$lng"]); ?>;
                jQuery(`select[name="stdyDscr/dataAccs/setAvail/avlStatus_${lang}"]`).val(avlStatus_<?= $lng ?>).change();

                // Caractéristiques données coll -> Identifiant pérenne du jeu de données -> Type de données
                let dataKind_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/stdyInfo/sumDscr/dataKind_$lng"]); ?>;
                if (dataKind_<?= $lng ?>) {
                    dataKind_<?= $lng ?> = parsePseudoArray(dataKind_<?= $lng ?>)
                    setFieldValues(dataKind_<?= $lng ?>, `stdyDscr/stdyInfo/sumDscr/dataKind_${lang}[]`, "checkbox");
                }
                const isDataInBiobank_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/dataTypes/isDataInBiobank_$lng"]); ?>;
                jQuery(`select[name="additional/dataTypes/isDataInBiobank_${lang}"]`).val(isDataInBiobank_<?= $lng ?>).change();

                //Caractéristiques données coll -> Types de données -> Contenu de la biobanque
                const biobankContent_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/dataTypes/biobankContent_$lng"]); ?>;
                if (biobankContent_<?= $lng ?> && biobankContent_<?= $lng ?>.length) {
                    setFieldValues(biobankContent_<?= $lng ?>, `additional/dataTypes/biobankContent_${lang}[]`, "checkbox");
                }
                isSpecPermRequired = <?php echo json_encode($jsonRec["stdyDscr/dataAccs/useStmt/specPerm/required_yes_$lng"]); ?>;
                if (isSpecPermRequired) {
                    const $radioSpecPermRequired_<?= $lng ?> = jQuery(`[name="stdyDscr/dataAccs/useStmt/specPerm/required_yes_<?= $lng ?>"][value="${isSpecPermRequired}"]`);
                    $radioSpecPermRequired_<?= $lng ?>.prop("disabled", false).prop("checked", true).trigger("change");
                }

                mockSampleAvailable = <?php echo json_encode($jsonRec["additional/mockSample/mockSampleAvailable_$lng"]); ?>;
                if (mockSampleAvailable) {
                    const $radioMockSampleAvailable_<?= $lng ?> = jQuery(`[name="additional/mockSample/mockSampleAvailable_<?= $lng ?>"][value="${mockSampleAvailable}"]`);
                    $radioMockSampleAvailable_<?= $lng ?>.prop("disabled", false).prop("checked", true).trigger("change");
                }
                variableDictionnaryAvailable = <?php echo json_encode($jsonRec["additional/variableDictionnary/variableDictionnaryAvailable_$lng"]); ?>;
                if (variableDictionnaryAvailable) {
                    const $radioVDA_<?= $lng ?> = jQuery(`[name="additional/variableDictionnary/variableDictionnaryAvailable_<?= $lng ?>"][value="${variableDictionnaryAvailable}"]`);
                    $radioVDA_<?= $lng ?>.prop("disabled", false).prop("checked", true).trigger("change");
                }

                let contactUseStmt_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/dataAccs/useStmt/contact_$lng"]); ?>;
                if (contactUseStmt_<?= $lng ?> && contactUseStmt_<?= $lng ?>.length) {
                    contactUseStmtData['<?= $lng ?>'] = parsePseudoArray(contactUseStmt_<?= $lng ?>);
                }

                let dataStandardName_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName_$lng"]); ?>;
                if (dataStandardName_<?= $lng ?> && dataStandardName_<?= $lng ?>.length) {
                    standardNameData['<?= $lng ?>'] = parsePseudoArray(dataStandardName_<?= $lng ?>)
                }

                let dataComplianceDescription_<?= $lng ?> = <?php echo json_encode($jsonRec["stdyDscr/stdyInfo/qualityStatement/complianceDescription_$lng"]); ?>;
                if (dataComplianceDescription_<?= $lng ?> && dataComplianceDescription_<?= $lng ?>.length) {
                    complianceDescriptionData['<?= $lng ?>'] = parsePseudoArray(dataComplianceDescription_<?= $lng ?>)
                }
                IDnoData['<?= $lng ?>'] = <?= json_encode($jsonRec["stdyDscr/citation/IDno_$lng"] ?? []) ?>;

                const additional_conformityDeclaration_<?= $lng ?> = <?php echo json_encode($jsonRec["additional/regulatoryRequirements/conformityDeclaration_$lng"]); ?>;
                jQuery(`select[name="additional/regulatoryRequirements/conformityDeclaration_${lang}"]`).val(additional_conformityDeclaration_<?= $lng ?>).change();

            <?php endforeach; ?>



            autoRepeatClickObtainedAuthorization("#add-ObtainedAuthorization", authorizingAgencyData);
            autoRepeatClickPrimaryInvestigator("#add-PrimaryInvestigator", investigatorsNumber, investigatorData);

            autoRepeatClickFundingAgent("#add-fundingAgent", fundingAgenciesData);

            autoRepeatClickContactPoint("#add-ContactPoint", contactData);

            autoRepeatClickContributor("#add-Contributor", contributorData);

            autoRepeatClickInerExpo("#add-intervExpo", interventionData);

            autoRepeatClickSources("#add-sources", sourcesData);


            autoRepeatClickInformationContact("#add-DataInformationContact", contactUseStmtData);

            autoRepeatClickStandardName("#add-standard", standardNameData);

            autoRepeatClickComplianceDescription("#add-DataQuality", complianceDescriptionData);



            autoRepeatClickDatasetPID("#add-DatasetPID", IDnoData);

            // /***** STEP 2 */


            // //Remplissage select => par Rania
            // let analyseUnit = <?php echo json_encode($jsonRec['stdyDscr/stdyInfo/sumDscr/anlyUnit']); ?>;
            // jQuery('select[name="stdyDscr/stdyInfo/sumDscr/anlyUnit_fr"]').val(analyseUnit).change();
            // jQuery('select[name="stdyDscr/stdyInfo/sumDscr/anlyUnit_en"]').val(analyseUnit).change();

            // let universe = <?php echo json_encode($jsonRec['stdyDscr/stdyInfo/sumDscr/universe']); ?>;

            // let universeObj = JSON.parse(universe);

            // setFieldValues(universeObj['level_sex_clusion_I'], "stdyDscr/stdyInfo/sumDscr/universe/level_sex-Clusion_I_fr[]", "checkbox");
            // setFieldValues(universeObj['level_age_clusion_I'], "stdyDscr/stdyInfo/sumDscr/universe/level_age-Clusion_I_fr[]", "checkbox");


            // jQuery('select[name="stdyDscr/stdyInfo/sumDscr/universe/level_type-Clusion_I_fr"]').val(universeObj['level_type_clusion_I']).change();
            // jQuery('[name="additional/OtherPopulationType_fr"]').val(universeObj['level_type_clusion_other']).change(); // A corriger apres la MEPP du 10-09-025
            // jQuery('[name="stdyDscr/stdyInfo/sumDscr/universe/Clusion_I_fr"]').val(universeObj['clusion_I']).change();
            // jQuery('[name="stdyDscr/stdyInfo/sumDscr/universe/Clusion_E_fr"]').val(universeObj['clusion_E']).change();



            // let agentSchema = <?php echo json_encode($jsonRec['stdyDscr/citation/IDno/agentSchema']); ?>;
            // jQuery('select[name="stdyDscr/citation/IDno/agentSchema_fr"]').val(agentSchema).change();
            // jQuery('select[name="stdyDscr/citation/IDno/agentSchema_en"]').val(agentSchema).change();

            // let dataStandardName = <?php echo json_encode($jsonRec['stdyDscr/stdyInfo/qualityStatement/standardsCompliance/standard/standardName']); ?>;
            // dataStandardName = parsePseudoArray(dataStandardName)
            // let n_standardName = dataStandardName.length - 1
            // autoRepeatClickStandardName("#add-standard", n_standardName, dataStandardName);

            // let dataComplianceDescription = <?php echo json_encode($jsonRec['stdyDscr/stdyInfo/qualityStatement/complianceDescription']); ?>;
            // dataComplianceDescription = parsePseudoArray(dataComplianceDescription)
            // let n_complianceDescription = dataComplianceDescription.length - 1
            // autoRepeatClickComplianceDescription("#add-DataQuality", n_complianceDescription, dataComplianceDescription);


            // // Remplissage Radio Box

            // let activeFollowUp = <?php echo json_encode($jsonRec['additional/activeFollowUp']); ?>; // donne
            // let $radioActiveFollowUp_fr = jQuery(`[name="additional/activeFollowUp/isActiveFollowUp_fr"][value="${activeFollowUp}"]`);
            // let $radioActiveFollowUp_en = jQuery(`[name="additional/activeFollowUp/isActiveFollowUp_en"][value="${activeFollowUp}"]`);
            // $radioActiveFollowUp_fr.prop("disabled", false).prop("checked", true).trigger("change")
            // $radioActiveFollowUp_en.prop("disabled", false).prop("checked", true).trigger("change")
            // <?php if ($mode == 'detail') { ?>
            //     $radioActiveFollowUp_fr.prop("disabled", true);
            //     $radioActiveFollowUp_en.prop("disabled", true);
            // <?php } ?>


            // let dataCollectionIntegration = <?php echo json_encode($jsonRec['additional/dataCollectionIntegration']); ?>; // donne
            // let $radioDataCollectionIntegration_fr = jQuery(`[name="additional/dataCollectionIntegration/isDataIntegration_fr"][value="${dataCollectionIntegration}"]`);
            // let $radioDataCollectionIntegration_en = jQuery(`[name=" additional/dataCollectionIntegration/isDataIntegration_en"][value="${dataCollectionIntegration}"]`);
            // $radioDataCollectionIntegration_fr.prop("disabled", false).prop("checked", true).trigger("change")
            // $radioDataCollectionIntegration_en.prop("disabled", false).prop("checked", true).trigger("change")
            // <?php if ($mode == 'detail') { ?>
            //     $radioDataCollectionIntegration_fr.prop("disabled", true);
            //     $radioDataCollectionIntegration_en.prop("disabled", true);
            // <?php } ?>

            // // ---RADIO ---
            // //Caractéristiques données coll -> Accès aux données -> Existence d'un outil de demande d'accès aux données
            // let specPermRequired = <?php echo json_encode($jsonRec['stdyDscr/dataAccs/useStmt/specPerm/required_yes']); ?>;
            // if (specPermRequired == "vrai") {
            //     specPermRequired = "true"
            // } else {
            //     specPermRequired = "false"
            // }
            // let $radioSpecPermRequiredFr = jQuery(`[name="stdyDscr/dataAccs/useStmt/specPerm/required_yes_fr"][value="${specPermRequired}"]`);
            // let $radioSpecPermRequiredEn = jQuery(`[name="stdyDscr/dataAccs/useStmt/specPerm/required_yes_en"][value="${specPermRequired}"]`);
            // $radioSpecPermRequiredFr.prop("disabled", false).prop("checked", true).trigger("change")
            // $radioSpecPermRequiredEn.prop("disabled", false).prop("checked", true).trigger("change")

            // <?php if ($mode == 'detail') { ?>
            //     $radioSpecPermRequiredFr.prop("disabled", true);
            //     $radioSpecPermRequiredEn.prop("disabled", true);
            // <?php } ?>

            // //Caractéristiques données coll -> Accès aux données -> Existence d'un échantillon fictif
            // let mockSample = <?php echo json_encode($jsonRec['additional/mockSample/mockSampleAvailable']); ?>;
            // let $radiomockSampleFr = jQuery(`[name="additional/mockSample/mockSampleAvailable_fr"][value="${mockSample}"]`);
            // let $radiomockSampleEn = jQuery(`[name="additional/mockSample/mockSampleAvailable_en"][value="${mockSample}"]`);
            // $radiomockSampleFr.prop("disabled", false).prop("checked", true).trigger("change")
            // $radiomockSampleEn.prop("disabled", false).prop("checked", true).trigger("change")

            // <?php if ($mode == 'detail') { ?>
            //     $radiomockSampleFr.prop("disabled", true);
            //     $radiomockSampleEn.prop("disabled", true);
            // <?php } ?>

            // //Caractéristiques données coll -> Identifiant pérenne du jeu de données -> Existence d’un dictionnaire des variables
            // let variableDictionnary = <?php echo json_encode($jsonRec['additional/variableDictionnary/variableDictionnaryAvailable']); ?>;
            // let $radiovariableDictionnaryFr = jQuery(`[name="additional/variableDictionnary/variableDictionnaryAvailable_fr"][value="${variableDictionnary}"]`);
            // //let $radiovariableDictionnaryEn = jQuery(`[name="additional/variableDictionnary/variableDictionnaryAvailable_en"][value="${variableDictionnary}"]`);
            // $radiovariableDictionnaryFr.prop("disabled", false).prop("checked", true).trigger("change")
            // //$radiovariableDictionnaryEn.prop("disabled", false).prop("checked", true).trigger("change")
            <?php if ($mode == 'detail') { ?>
                $radiovariableDictionnaryFr.prop("disabled", true);
                // $radioVariableDictionnaryEn.prop("disabled", true);

                jQuery('#form-add-study input, textarea').each(function() {
                    let $field = jQuery(this);
                    let val = $field.val().trim();

                    if (val === "['']") {
                        $field.val(''); // supprime la valeur
                    }
                });

                /*** HIDE SELECT , CHECKBOX si vide */
                jQuery('#form-add-study').find('select.readInput').each(function() {
                    let $select = jQuery(this);

                    // Vérifie si la valeur est vide
                    if (!$select.val()) {
                        // Cacher le conteneur complet du select2
                        $select.next('.select2-container').hide();
                    }
                });

                jQuery('#form-add-study').find('input[type="text"], textarea').each(function() {
                    let $field = jQuery(this);
                    let val = ($field.val() || '').trim();

                    if (!val) {
                        // cacher seulement le champ ou son wrapper
                        $field.remove();
                    }
                });

                // Pour chaque groupe de checkbox (par exemple groupé dans .lang-input)
                jQuery('#form-add-study').find('input[type="checkbox"]').each(function() {
                    let $checkbox = jQuery(this);

                    if (!$checkbox.is(':checked')) {
                        $checkbox.closest('label, div').hide(); // adapter le wrapper
                    }
                });

                jQuery('#form-add-study input[type="radio"]').each(function() {
                    let $radio = jQuery(this);

                    if (!$radio.is(':checked')) {
                        $radio.hide(); // masque le radio
                        // masque le label correspondant
                        jQuery('label[for="' + $radio.attr('id') + '"]').hide();
                    }
                });

            <?php } ?>
        <?php } ?>

    });

    jQuery(document).ready(function() {
        jQuery('.parent-checkbox .form-check-input').on('change', function() {
            let parentName = jQuery(this).attr('data-parent').trim();
            let isChecked = jQuery(this).is(':checked');


            jQuery('.child-checkbox .form-check-input[data-child="' + parentName + '"]').each(function() {
                let wrapper = jQuery(this).closest('.child-checkbox');

                if (isChecked) {
                    wrapper.show();
                } else {
                    jQuery(this).prop('checked', false);
                    wrapper.hide();
                }
            });
        });

        jQuery('.parent-checkbox .form-check-input').trigger('change');
    });
</script>
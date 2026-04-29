<?php
$url_pdad = get_nada_pdad_url();
$currentLang = pll_current_language();
?>
<?php if (!empty($rows)) : ?>
    <?php foreach ($rows as $study) : ?>
        <?php
        // Sécuriser les valeurs
        $title        = esc_html($study['title'] ?? '');
        $idno         = esc_html($study['idno'] ?? '');
        $nation       = esc_html($study['nation'] ?? '');
        $ys           = esc_html($study['year_start'] ?? '');
        $ye           = esc_html($study['year_end'] ?? '');
        $authoring    = esc_html(str_replace(';', ' ', $study['authoring_entity'] ?? ''));
        $repositoryid = esc_html($study['repositoryid'] ?? '');
        $changed      = esc_html($study['changed'] ?? '');
        $total_views  = esc_html($study['total_views'] ?? '0');
        $hasContacts  = $study['isContactAvailable'] ?? false;
        $dataToolLink =  esc_html($study['linkSpecPerm'] ?? '');
        $hasToolLink = (!empty($dataToolLink) && str_contains($dataToolLink, $url_pdad)) ? true : false;
        // Lang
        $lang = get_locale() === "fr_FR" ? "fr" : "en";

        $detailsUrl = $lang === "fr"
            ? "/catalogue-detail/$idno"
            : "/en/study-details/$idno";
        ?>

        <div class="study-card">

            <a href="<?= $detailsUrl ?>">
                <div class="nada-id-card">
                    <div class="catag-card-header d-flex">
                        <h3 class="nada-id-title">

                            <span>
                                <i class="fa fa-database fa-nada-icon wb-title-icon" aria-hidden="true"></i>
                                <?= $title ?>
                            </span>

                            <span class="title-country">
                                <?php if ($nation): ?>
                                    <?= __("catalogCountry", "nada-id") ?> : <?= $nation ?>
                                <?php endif; ?>
                            </span>

                            <span class="title-country">
                                <?php
                                $showStart = !empty($ys) && $ys !== '3000';
                                $showEnd = !empty($ye) && $ye !== '3000';

                                if ($showStart || $showEnd):
                                    $period = $showStart && $showEnd ? "$ys - $ye" : ($showStart ? $ys : $ye);
                                ?>
                                    <?= __("catalogCollectionPeriod", "nada-id") ?> : <?= $period ?>
                                <?php endif; ?>
                            </span>

                        </h3>

                        <div class="pictos d-flex">
                            <?php if ($hasContacts): ?>
                                <div class="pointContact"
                                    title="<?= esc_attr($currentLang === 'fr' ? "Points de contact" : "Contact points"); ?>"
                                    data-bs-toggle="tooltip">
                                    <img src="<?php echo plugin_dir_url(__FILE__) . '../../img/mail.png'; ?>" />
                                </div>
                            <?php else: ?>
                                <div class="pointContact"
                                    title="<?= esc_attr($currentLang === 'fr' ? "Pas de points de contact" : "No contact points"); ?>"
                                    data-bs-toggle="tooltip">
                                    <img src="<?php echo plugin_dir_url(__FILE__) . '../../img/no-contact.png'; ?>" />
                                </div>
                            <?php endif; ?>


                            <?php if ($hasToolLink): ?>

                                <div class="pointContact">
                                    <a target="_blank" href="<?= esc_url($dataToolLink); ?>"
                                        data-bs-toggle="tooltip"
                                        title="<?= esc_attr($currentLang === 'fr' ? "Lien vers l'outil de demande d'accès" : "Link to the data access request tool"); ?>">
                                        <img src="<?php echo plugin_dir_url(__FILE__) . '../../img/link.png'; ?>" />
                                    </a>
                                </div>

                            <?php endif; ?>
                        </div>
                    </div>

                    <p class="nada-id-description">
                        <?php if ($authoring): ?>
                            <span><?= __("catalogPI", "nada-id") ?> : <?= $authoring ?></span>
                        <?php endif; ?>
                    </p>

                    <div class="nada-list noBorder">
                        <?php if ($repositoryid): ?>
                            <span class="grey-color">Collection :</span>
                            <span><?= $repositoryid ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex nada-meta" style="margin-top:8px;">

                        <?php if ($idno): ?>
                            <div class="nada-list">
                                <span class="grey-color">ID :</span>
                                <span><?= $idno ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($changed): ?>
                            <div class="nada-list">
                                <span class="grey-color"><?= __("catalogLastModified", "nada-id") ?> :</span>
                                <span class="grey-color"><?= format_date($changed, $lang, 'datetime') ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($total_views): ?>
                            <div class="nada-list">
                                <span class="grey-color"><?= __("catalogViews", "nada-id") ?> :</span>
                                <span class="grey-color"><?= $total_views ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </a>
        </div>

    <?php endforeach; ?>
<?php else : ?>
    <p><?= __("catalogNoStudies", "nada-id") ?></p>
<?php endif; ?>
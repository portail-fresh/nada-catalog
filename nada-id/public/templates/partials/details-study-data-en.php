<?php if (!defined('ABSPATH')) exit; ?>

<div class="container-fluid page-header">

    <?php if ($data['en']['published'] == 0) { ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
            <div>
                The content of this page has not been published yet and is only available for review.
            </div>
        </div>
    <?php } ?>


    <div class="row study-info m-0">
        <div class="col-md-12">
            <div>
                <div class="elementor-container">
                    <div class="elementor-row">
                        <div class="elementor-column mb-1 titleDetailStudy">
                            <h2 class="elementor-heading-title"><?php echo $data['en']['title']; ?></h2>
                            <div class="d-flex">
                                <?php
                                if ($hasContactsfr || $hasContactsen): ?>
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
                                        <a target="_blank" href="<?= nl2br(esc_html($currentLang === 'fr' ? $dataToolLinkFr : $dataToolLinkEn)); ?>"
                                            data-bs-toggle="tooltip"
                                            title="<?= esc_attr($currentLang === 'fr' ? "Lien vers l'outil de demande d'accès" : "Link to the data access request tool"); ?>">
                                            <img src="<?php echo plugin_dir_url(__FILE__) . '../../img/link.png'; ?>" />
                                        </a>
                                    </div>

                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="elementor-column">
                            <span class="sub-title float-left fs-15" id="dataset-sub-title">
                                <?php if ($data['en']['nation']) { ?>
                                    <span id="dataset-country"><?php echo $data['en']['nation']; ?></span>
                                <?php } ?>
                                <?php if ($data['en']['nation'] && $data['en']['year_start'] && $data['en']['year_start'] !== '3000') { ?>
                                    ,
                                <?php } ?>
                                <?php if ($data['en']['year_start'] && $data['en']['year_start'] !== '3000') { ?>
                                    <span id="dataset-year"><?php echo $data['en']['year_start']; ?>
                                        <?php if ($data['en']['year_end'] && $data['en']['year_end'] !== '3000') { ?>
                                            - <?php echo $data['en']['year_end']; ?>
                                        <?php } ?>
                                    </span>
                                <?php } ?>
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="study-info-content">
                <div class="row">
                    <div class="col pr-5">
                        <div class="row mt-4 mb-2 pb-2  border-bottom">
                            <div class="col-md-3">ID Reference </div>
                            <div class="col-md-3">
                                <div class="study-idno"><?php echo $data['en']['idno']; ?></div>
                            </div>
                        </div>
                        <div class="row mb-2 pb-2 border-bottom">
                            <div class="col-md-3">Principal Investigator</div>
                            <div class="col-md-3">
                                <div class="producers"><?php echo $data['en']['authoring_entity'] = str_replace(';', ' ', $data['en']['authoring_entity']); ?></div>
                            </div>
                        </div>
                        <div class="row mb-2 pb-2 mt-2 border-bottom">
                            <div class="col-md-3">Metadata</div>
                            <div class="col-md-3">
                                <div class="metadata">
                                    <!--metadata-->
                                    <span class="mr-2 link-col">
                                        <a class="download" href="<?php echo $url ?>/metadata/export/<?php echo $data['en']['id']; ?>/ddi" title="DDI Codebook (2.5)">
                                            <span class="badge badge-primary"> DDI/XML</span>
                                        </a>

                                        <a class="download" href="<?php echo $url ?>/metadata/export/<?php echo $data['en']['id']; ?>/json" title="JSON">
                                            <span class="badge badge-info">JSON</span>
                                        </a>
                                    </span>
                                    <!--end-metadata-->
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2 pb-2 border-bottom">
                            <div class="col-md-3">Created on</div>
                            <div class="col-md-3">
                                <div class="producers"><?php echo format_date_fr($data['en']['created'], "MMMM d, yyyy 'at' hh:mm a", "en_US"); ?></div>
                            </div>
                        </div>

                        <div class="row mb-2 pb-2 border-bottom">
                            <div class="col-md-3">Last modified</div>
                            <div class="col-md-3">
                                <div class="producers"><?php echo format_date_fr($data['en']['changed'], "MMMM d, yyyy 'at' hh:mm a", "en_US"); ?></div>
                            </div>
                        </div>

                        <div class="row pb-2 mb-4">
                            <div class="col-md-3">Page views</div>
                            <div class="col-md-3">
                                <div class="producers"><span><?php echo $data['en']['total_views']; ?> vue(s)</span></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
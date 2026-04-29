<?php if (!defined('ABSPATH')) exit; ?>

<div class="container-fluid page-header">
    <?php if ($data['fr']['published'] == 0) { ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
            <div>
                Le contenu de cette page n’est pas encore publié et est uniquement disponible pour relecture.
            </div>
        </div>
    <?php } ?>


    <?php if ($studyDetails['is_approved']) { ?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
            <div>
                Cette étude a été validée par l'investigateur principal
            </div>
        </div>
    <?php } ?>



    <div class="row study-info">
        <div class="col-md-9">
            <div>
                <div class="elementor-container">
                    <div class="elementor-row">
                        <div class="elementor-column mb-1">
                            <h2 class="elementor-heading-title"><?php echo $data['fr']['title']; ?></h2>
                        </div>
                        <div class="elementor-column">
                            <h6 class="sub-title float-left" id="dataset-sub-title">
                                <span id="dataset-country"><?php echo $data['fr']['nation']; ?></span>, <span id="dataset-year"><?php echo $data['fr']['year_start']; ?> - <?php echo $data['fr']['year_end']; ?></span>
                            </h6>
                        </div>

                    </div>
                </div>
            </div>

            <div class="study-info-content">
                <div class="col pr-5">
                    <div class="row mt-4 mb-2 pb-2  border-bottom">
                        <div class="col-md-2">Référence ID </div>
                        <div class="col">
                            <div class="study-idno"><?php echo $data['fr']['idno']; ?></div>
                        </div>
                    </div>
                    <div class="row mb-2 pb-2 border-bottom">
                        <div class="col-md-2">Producteur(s)</div>
                        <div class="col">
                            <div class="producers"><?php echo $data['fr']['authoring_entity']; ?></div>
                        </div>
                    </div>
                    <div class="row mb-2 pb-2 mt-2 mb-4">
                        <div class="col-md-2">Metadata</div>
                        <div class="col">
                            <div class="metadata">
                                <!--metadata-->
                                <span class="mr-2 link-col">
                                    <a class="download" href="<?php echo $url ?>/metadata/export/<?php echo $data['fr']['id']; ?>/ddi" title="DDI Codebook (2.5)">
                                        <span class="badge badge-primary"> DDI/XML</span>
                                    </a>

                                    <a class="download" href="<?php echo $url ?>/metadata/export/<?php echo $data['fr']['id']; ?>/json" title="JSON">
                                        <span class="badge badge-info">JSON</span>
                                    </a>
                                </span>
                                <!--end-metadata-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 border-left">
            <!--right-->
            <div class="study-header-right-bar">
                <div class="stat">
                    <div class="stat-label">Créé le </div>
                    <div class="stat-value"><?php echo format_date_fr($data['fr']['created'], "MMMM d, yyyy 'at' hh:mm a", 'en_US'); ?></div>
                </div>

                <div class="stat">
                    <div class="stat-label">Dernière modification</div>
                    <div class="stat-value"><?php echo format_date_fr($data['fr']['changed']); ?></div>
                </div>

                <div class="stat">
                    <div class="stat-label">Pages vues</div>
                    <div class="stat-value"><?php echo $data['fr']['total_views']; ?></div>
                </div>
            </div>
            <!--end-right-->
        </div>
    </div>
</div>
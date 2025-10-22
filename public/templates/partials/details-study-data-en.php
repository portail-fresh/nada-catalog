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


    <?php if ($studyDetails['is_approved']) { ?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
            <div>
                This study has been validated by the principal investigator
            </div>
        </div>
    <?php } ?>



    <div class="row study-info">
        <div class="col-md-9">
            <div>
                <div class="elementor-container">
                    <div class="elementor-row">
                        <div class="elementor-column mb-1">
                            <h2 class="elementor-heading-title"><?php echo $data['en']['title']; ?></h2>
                        </div>
                        <div class="elementor-column">
                            <h6 class="sub-title float-left" id="dataset-sub-title">
                                <span id="dataset-country"><?php echo $data['en']['nation']; ?></span>, <span id="dataset-year"><?php echo $data['en']['year_start']; ?> - <?php echo $data['en']['year_end']; ?></span>
                            </h6>
                        </div>

                    </div>
                </div>
            </div>

            <div class="study-info-content">
                <div class="col pr-5">
                    <div class="row mt-4 mb-2 pb-2  border-bottom">
                        <div class="col-md-2">ID Reference </div>
                        <div class="col">
                            <div class="study-idno"><?php echo $data['en']['idno']; ?></div>
                        </div>
                    </div>
                    <div class="row mb-2 pb-2 border-bottom">
                        <div class="col-md-2">Producer(s)</div>
                        <div class="col">
                            <div class="producers"><?php echo $data['en']['authoring_entity']; ?></div>
                        </div>
                    </div>
                    <div class="row mb-2 pb-2 mt-2 mb-4">
                        <div class="col-md-2">Metadata</div>
                        <div class="col">
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
                </div>
            </div>
        </div>

        <div class="col-md-3 border-left">
            <!--right-->
            <div class="study-header-right-bar">
                <div class="stat">
                    <div class="stat-label">Created on </div>
                    <div class="stat-value"><?php echo format_date_fr($data['en']['created'], "MMMM d, yyyy 'at' hh:mm a", "en_US"); ?></div>
                </div>

                <div class="stat">
                    <div class="stat-label">Last modified</div>
                    <div class="stat-value"><?php echo format_date_fr($data['en']['changed'], "MMMM d, yyyy 'at' hh:mm a", "en_US"); ?></div>
                </div>

                <div class="stat">
                    <div class="stat-label">Page views</div>
                    <div class="stat-value"><?php echo $data['en']['total_views']; ?></div>
                </div>
            </div>
            <!--end-right-->
        </div>
    </div>
</div>
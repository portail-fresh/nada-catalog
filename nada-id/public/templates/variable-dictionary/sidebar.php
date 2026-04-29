<?php
if (!defined('ABSPATH')) exit;
?>

<span class="technical-documentation-breadcrumb mb-3"></span>

<div class="technical-documentation-col-3 col-3">
    <button class="toggle-btn toggleSidebar">
        <i class="arrow-left"></i>
    </button>

    <aside class="technical-documentation-sidebar">
        <nav class="technical-documentation-sidebar-nav">
            <!-- Schéma des métadonnées FReSH -->
            <div class="technical-documentation-nav-section">
                <button class="technical-documentation-nav-toggle <?php echo ($type === 'metadata' || $is_introduction) ? 'active' : ''; ?>" type="button">
                    <span><?php echo __('metadataSchemaVD', 'nada-id'); ?></span>
                    <i class="technical-documentation-arrow fa <?php echo ($type === 'metadata' || $is_introduction) ?  'fa-angle-down' : 'fa-angle-right';  ?>"></i>
                </button>

                <div class="technical-documentation-nav-subsection <?php echo ($type === 'metadata' || $is_introduction) ? 'show' : ''; ?>">
                    <!-- Introduction -->
                    <a href="<?php echo esc_url($base_url . 'metadata/introduction/'); ?>"
                        class="technical-documentation-nav-link technical-documentation-nav-intro <?php echo $is_introduction ? 'active' : ''; ?>">
                        <?php echo __('introductionVD', 'nada-id'); ?>
                    </a>

                    <!-- Sections Header -->
                    <div class="technical-documentation-sections-header">
                        <button class="technical-documentation-sections-toggle <?php echo ($type === 'metadata' && !$is_introduction) ? 'active' : ''; ?>" type="button">
                            <span>Sections</span>
                            <i class="technical-documentation-arrow fa <?php echo ($type === 'metadata' && !$is_introduction) ? 'fa-angle-down' : 'fa-angle-right'; ?>"></i>
                        </button>
                    </div>

                    <!-- Sections List -->
                    <div class="technical-documentation-sections-list <?php echo ($type === 'metadata' && !$is_introduction) ? 'show' : ''; ?>">
                        <?php echo get_list_sections_schema($current_section, $lang, $base_url); ?>
                    </div>
                </div>
            </div>

            <!-- Vocabulaires contrôlés -->
            <div class="technical-documentation-nav-section">
                <button class="technical-documentation-nav-toggle <?php echo $type === 'vocabulary' ? 'active' : ''; ?>" type="button">
                    <span><?php echo __('controlledVocabulariesVD', 'nada-id'); ?></span>
                    <i class="technical-documentation-arrow fa <?php echo $type === 'vocabulary' ? 'fa-angle-down' : 'fa-angle-right'; ?>"></i>
                </button>

                <div class="technical-documentation-nav-subsection <?php echo $type === 'vocabulary' ? 'show' : ''; ?>">
                    <?php echo get_list_vocabulary_per_sections($current_ref, $lang, $base_url); ?>
                </div>
            </div>
        </nav>
    </aside>
</div>
<?php
if (!defined('ABSPATH')) exit;
?>
<div class="technical-documentation-container">
    <div class="technical-documentation-row row">
        <?php echo render_sidebar($type, $section, $refName, $is_introduction, $lang); ?>
        <div class="technical-documentation-col-9 col-9">
            <main class="technical-documentation-main-content">
                <?php
                if ($is_introduction) {
                    echo render_introduction_schema($lang);
                } elseif ($type === 'vocabulary') {
                    echo render_vocabulary($refName, $lang);
                } else {
                    echo render_metadata_fields($section, $lang);
                }
                ?>
            </main>
        </div>
    </div>
    
</div>
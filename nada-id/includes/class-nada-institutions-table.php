<?php
// Sécurité : empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('WP_Screen')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
}

if (!function_exists('add_screen_option')) {
    require_once ABSPATH . 'wp-admin/includes/screen.php';
}

if (!function_exists('convert_to_screen')) {
    require_once ABSPATH . 'wp-admin/includes/template.php';
}
$trait_path = __DIR__ . '/helpers/trait-table-helpers.php';
if (file_exists($trait_path)) {
    require_once $trait_path;
}

/**
 * Classe pour afficher le tableau des institutions
 */
class InstitutionsTable extends WP_List_Table
{
    use TableHelpersTrait;

    private $isActive;
    private $state;
    private $searchKey;
    private $lang;

    public function __construct($isActive, $searchKey, $state)
    {
        $this->isActive = (int)$isActive;
        $this->state = sanitize_key($state);
        $this->searchKey = sanitize_key($searchKey);
        $this->lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';


        parent::__construct([
                'singular' => 'institution',
                'plural' => 'institutions',
                'ajax' => false
        ]);
    }

    public function get_columns()
    {
        return [
                'identifier' => 'Identifiant',
                'label_fr' => 'Nom FR',
                'label_en' => 'Nom EN',
                'desc_fr' => 'Description FR',
                'desc_en' => 'Description EN',
                'uri' => 'URI',
                'siren' => 'SIREN',
                'status' => 'Statut',
                'actions' => 'Actions',
        ];
    }

    protected function get_primary_column_name()
    {
        return '';
    }


    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'uri':
                $url = $item[$column_name] ?? '';
                if (!empty($url)) {
                    return sprintf(
                            '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
                            esc_url($url),
                            esc_html($url)
                    );
                }
                return '';

            case 'actions':
                if ($this->isActive === 0) {
                    return sprintf(
                            '<div class="action-buttons-wrapper">
                            <a href="#" class="btn-action-trigger approve-btn"
                            data-action="approve"
                            data-id="%1$d"
                            data-name-fr="%2$s"
                            data-name-en="%3$s"
                            title="' . esc_attr__('actionApprove', 'nada-id') . '">
                                <i class="fa fa-check"></i>
                            </a>
                            <a href="#" class="btn-action-trigger reject-btn"
                            data-action="reject"
                            data-id="%1$d"
                            data-name-fr="%2$s"
                            data-name-en="%3$s"
                            title="' . esc_attr__('actionReject', 'nada-id') . '">
                                <i class="fa fa-backspace"></i>
                            </a>
                        </div>',
                            absint($item['id']),
                            esc_attr($item['label_fr'] ?? ''),
                            esc_attr($item['label_en'] ?? '')
                    );
                } else {
                    return sprintf(
                            '<span class="btn-edit-institution edit-institution-btn-nada"
                            data-institution-id="%d"
                            data-id="%d"
                            data-label-fr="%s"
                            data-label-en="%s"
                            data-desc-fr="%s"
                            data-desc-en="%s"
                            data-uri="%s"
                            data-identifier="%s"
                            data-siren="%s"
                            data-status="%s"
                            title="%s">
                            <i class="fa fa-edit"></i>
                        </span>',
                            absint($item['id']),
                            absint($item['id']),
                            esc_attr($item['label_fr'] ?? ''),
                            esc_attr($item['label_en'] ?? ''),
                            esc_attr($item['desc_fr'] ?? ''),
                            esc_attr($item['desc_en'] ?? ''),
                            esc_attr($item['uri'] ?? ''),
                            esc_attr($item['identifier'] ?? ''),
                            esc_attr($item['siren'] ?? ''),
                            esc_attr($item['status'] ?? ''),
                            esc_attr__('Modifier', 'nada-id')
                    );
                }
            default:
                $value = $item[$column_name] ?? '';
                return !empty($value) ? esc_html($value) : '';
        }
    }

    public function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nada_institutions';
        $per_page = $this->getPerPageCount();
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;
        $search_term = $this->getSearchTerm();
        $where = $this->buildWhereClause($search_term);
        $total_items = (int)$wpdb->get_var("SELECT COUNT(id) FROM {$table_name} {$where}");
        $query = $wpdb->prepare(
                "SELECT * FROM {$table_name} {$where} ORDER BY id DESC LIMIT %d OFFSET %d",
                $per_page,
                $offset
        );
        $this->items = $wpdb->get_results($query, ARRAY_A);
        $this->set_pagination_args([
                'total_items' => $total_items,
                'per_page' => $per_page,
                'total_pages' => ceil($total_items / $per_page)
        ]);

        $this->_column_headers = [$this->get_columns(), [], []];
    }

    private function buildWhereClause($search_term)
    {
        global $wpdb;

        $where = $wpdb->prepare(
                "WHERE is_active = %d AND state = %s",
                $this->isActive,
                $this->state
        );

        if (!empty($search_term)) {
            $search_like = $wpdb->esc_like($search_term);
            $where .= $wpdb->prepare(
                    " AND (
            label_fr LIKE CONCAT('%%', %s, '%%') OR
            label_en LIKE CONCAT('%%', %s, '%%') OR
            identifier LIKE CONCAT('%%', %s, '%%') OR
            siren LIKE CONCAT('%%', %s, '%%') OR
            desc_fr LIKE CONCAT('%%', %s, '%%') OR
            desc_en LIKE CONCAT('%%', %s, '%%') OR
            uri LIKE CONCAT('%%', %s, '%%') OR
            status LIKE CONCAT('%%', %s, '%%')
             )",
                    $search_like,
                    $search_like,
                    $search_like,
                    $search_like,
                    $search_like,
                    $search_like,
                    $search_like,
                    $search_like
            );
        }

        return $where;
    }

    public function display()
    {

        $singular = $this->_args['singular'];
        $this->display_tablenav('top');
        ?>

        <table class="wp-list-table <?php echo implode(' ', $this->get_table_classes()); ?>">
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>
            <tbody id="the-list" <?php
            if ($singular) {
                echo " data-wp-lists='list:$singular'";
            }
            ?>>
            <?php $this->display_rows_or_placeholder(); ?>
            </tbody>
        </table>
        <?php
        $this->display_tablenav('bottom');
    }

    protected function display_tablenav($which)
    {
        $current_per_page = isset($_REQUEST['per_page']) ? $_REQUEST['per_page'] : '10';
        $current_search = $this->getSearchTerm();
        ?>
        <div class="tablenav <?php echo esc_attr($which); ?>">

            <?php if ($which === 'top') : ?>
                <div class="row mb-2">
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="#" class="link-with-icon link-add btn-add-institution">
                            <i class="fa fa-database"></i>
                            <?php echo __('addInstitution', 'nada-id'); ?>
                        </a>
                    </div>
                </div>
                <div class="row my-6 justify-content-center">
                    <div class="col-md-6">
                        <form id="institution-search-form" style="display: flex;">
                            <input
                                    type="search"
                                    class="dt-search-input"
                                    id="dt-search-input"
                                    name="dt-search-input"
                                    value="<?php echo esc_attr($current_search); ?>"
                                    placeholder="<?php echo __('dtSearchPlaceholder', 'nada-id'); ?>"/>
                            <button
                                    id="nada-search-button"
                                    type="button"
                                    class="btn btn-primary institution-search-button">
                                <i class="fa fa-search"></i>
                                <?php echo __('dtSearchBtn', 'nada-id'); ?>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="row my-3 justify-content-start">
                    <div class="alignleft actions col-md-3">
                        <label for="nada-per-page-<?php echo esc_attr($this->searchKey); ?>">
                            <?php echo __('dtDisplay', 'nada-id'); ?>
                        </label>
                        <select name="per_page" id="nada-per-page-<?php echo esc_attr($this->searchKey); ?>"
                                class="dt-per-page-select">
                            <option value="10" <?php selected($current_per_page, '10'); ?>>10</option>
                            <option value="20" <?php selected($current_per_page, '20'); ?>>20</option>
                            <option value="50" <?php selected($current_per_page, '50'); ?>>50</option>
                            <option value="all" <?php selected($current_per_page, 'all'); ?>><?php echo __('dtDisplayOptionAll', 'nada-id'); ?></option>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($which === 'bottom') : ?>
                <?php $this->pagination($which); ?>
            <?php endif; ?>

            <br class="clear"/>
        </div>
        <?php
    }
}

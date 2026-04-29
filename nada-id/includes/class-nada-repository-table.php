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
 * Classe pour afficher le tableau des repositorys
 */
class RepositoryTable extends WP_List_Table
{
    use TableHelpersTrait;

    private string|PLL_Language $lang;

    public function __construct()
    {
        $this->lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';


        parent::__construct([
                'singular' => 'repository',
                'plural' => 'repositorys',
                'ajax' => false
        ]);
    }

    public function get_columns()
    {
        return [
                'referentiel_name' => __('refName', 'nada-id'),
                'actions' => 'Actions',
        ];

    }

    protected function get_primary_column_name()
    {
        return '';
    }


    protected function column_default($item, $column_name)
    {
        if ($column_name == 'actions') {
            $base_url = get_permalink(get_page_by_path($this->lang == 'fr' ? 'referentiel-detail' : 'repository-detail'));
            $items_url = trailingslashit($base_url) . absint($item['id']);

            return sprintf(
                    '<div class="action-buttons-wrapper">
                        <a href="%s" class="btn-action-trigger" data-id="%d" title="%s">
                            <i class="fa fa-eye"></i>
                        </a>
                    </div>',
                    esc_url($items_url),
                    absint($item['id']),
                    esc_attr__('displayItems', 'nada-id')
            );
        } else {
            $value = $item[$column_name] ?? '';
            return !empty($value) ? esc_html($value) : '';
        }
    }

    public function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'nada_referentiels';
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

        $where = $wpdb->prepare("WHERE is_enabled = %d", true);

        if (!empty($search_term)) {
            $search_like = $wpdb->esc_like($search_term);
            $where .= $wpdb->prepare(" AND (referentiel_name LIKE CONCAT('%%', %s, '%%'))", $search_like);
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

                <div class="row my-6 justify-content-center">
                    <div class="col-md-6">
                        <form id="repository-search-form" style="display: flex;">
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
                                    class="btn btn-primary repository-search-button">
                                <i class="fa fa-search"></i>
                                <?php echo __('dtSearchBtn', 'nada-id'); ?>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="row my-3 justify-content-start">
                    <div class="alignleft actions col-md-3">
                        <label for="nada-per-page">
                            <?php echo __('dtDisplay', 'nada-id'); ?>
                        </label>
                        <select name="per_page" id="nada-per-page"
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

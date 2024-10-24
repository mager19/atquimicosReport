<?php
if (!class_exists('ATQuimicosReportsCPT')) {
    class ATQuimicosReportsCPT
    {
        function __construct()
        {
            add_action('init', array($this, 'create_post_type'));
            add_action('init', array($this, 'create_year_taxonomy'));
            add_action('init', array($this, 'create_month_taxonomy'));
            add_filter('use_block_editor_for_post_type', array($this, 'disable_gutenberg_for_atquimicosreports'), 10, 2);
            add_filter('get_terms_orderby', array($this, 'order_month_terms'), 10, 2);
        }

        public function create_post_type()
        {
            register_post_type('atquimicosreports', [
                'label' => 'ATQuimicos Reports',
                'description' => 'ATQuimicos Reports Post Type',
                'labels' => array(
                    'name' => 'ATQ Reports',
                    'singular_name' => 'Report',
                ),
                'public' => true,
                'supports' => array('title', 'thumbnail'),
                'hierarchical' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => 55,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => false,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'show_in_rest' => true,
                'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode('<svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path fill="black" d="M1591 1448q56 89 21.5 152.5t-140.5 63.5h-1152q-106 0-140.5-63.5t21.5-152.5l503-793v-399h-64q-26 0-45-19t-19-45 19-45 45-19h512q26 0 45 19t19 45-19 45-45 19h-64v399zm-779-725l-272 429h712l-272-429-20-31v-436h-128v436z"/></svg>')

            ]);
        }

        public function create_year_taxonomy()
        {
            $labels = array(
                'name'              => _x('Años', 'taxonomy general name', 'textdomain'),
                'singular_name'     => _x('Año', 'taxonomy singular name', 'textdomain'),
                'search_items'      => __('Buscar Años', 'textdomain'),
                'all_items'         => __('Todos los Años', 'textdomain'),
                'parent_item'       => __('Año Padre', 'textdomain'),
                'parent_item_colon' => __('Año Padre:', 'textdomain'),
                'edit_item'         => __('Editar Año', 'textdomain'),
                'update_item'       => __('Actualizar Año', 'textdomain'),
                'add_new_item'      => __('Añadir Nuevo Año', 'textdomain'),
                'new_item_name'     => __('Nombre del Nuevo Año', 'textdomain'),
                'menu_name'         => __('Año', 'textdomain'),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => false,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'year'),
            );

            register_taxonomy('year', array('atquimicosreports'), $args);

            if (!term_exists('2024', 'year')) {
                wp_insert_term('2024', 'year');
            }
        }

        public function create_month_taxonomy()
        {
            $labels = array(
                'name'              => _x('Meses', 'taxonomy general name', 'textdomain'),
                'singular_name'     => _x('Mes', 'taxonomy singular name', 'textdomain'),
                'search_items'      => __('Buscar Meses', 'textdomain'),
                'all_items'         => __('Todos los Meses', 'textdomain'),
                'parent_item'       => __('Mes Padre', 'textdomain'),
                'parent_item_colon' => __('Mes Padre:', 'textdomain'),
                'edit_item'         => __('Editar Mes', 'textdomain'),
                'update_item'       => __('Actualizar Mes', 'textdomain'),
                'add_new_item'      => __('Añadir Nuevo Mes', 'textdomain'),
                'new_item_name'     => __('Nombre del Nuevo Mes', 'textdomain'),
                'menu_name'         => __('Mes', 'textdomain'),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => false,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array('slug' => 'mes'),
            );

            register_taxonomy('month', array('atquimicosreports'), $args);

            $months = array(
                'Enero',
                'Febrero',
                'Marzo',
                'Abril',
                'Mayo',
                'Junio',
                'Julio',
                'Agosto',
                'Septiembre',
                'Octubre',
                'Noviembre',
                'Diciembre'
            );

            foreach ($months as $month) {
                if (!term_exists($month, 'month')) {
                    wp_insert_term($month, 'month');
                }
            }
        }

        public function order_month_terms($orderby, $taxonomies)
        {
            if (in_array('month', $taxonomies)) {
                $orderby = "FIELD(name, 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre')";
            }
            return $orderby;
        }

        public function disable_gutenberg_for_atquimicosreports($use_block_editor, $post_type)
        {
            if ($post_type === 'atquimicosreports') {
                return false;
            }
            return $use_block_editor;
        }
    }
}

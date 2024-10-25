<?php
if (!class_exists('ATQuimicosReportsCPT')) {
    class ATQuimicosReportsCPT
    {
        function __construct()
        {
            require_once(ATQUIMICOS_REPORTS_PATH . 'fields/reportsFields.php');
            add_action('init', array($this, 'create_post_type'));
            add_filter('use_block_editor_for_post_type', array($this, 'disable_gutenberg_for_atquimicosreports'), 10, 2);
            add_filter('the_content', array($this, 'load_custom_template'));
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

        public function disable_gutenberg_for_atquimicosreports($use_block_editor, $post_type)
        {
            if ($post_type === 'atquimicosreports') {
                return false;
            }
            return $use_block_editor;
        }

        public function load_custom_template($content)
        {
            if (is_singular('atquimicosreports')) {

                ob_start();
                require_once ATQUIMICOS_REPORTS_PATH . 'templates/single-atquimicosreports.php';

                return ob_get_clean();
            }

            return $content; // Retorna el template original si no es el del CPT
        }
    }
}

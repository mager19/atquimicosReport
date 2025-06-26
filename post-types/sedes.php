<?php
if (!class_exists('ATQuimicosSedesCPT')) {
    class ATQuimicosSedesCPT
    {
        function __construct()
        {
            require_once(ATQUIMICOS_REPORTS_PATH . 'fields/sedesFields.php');
            add_action('init', array($this, 'create_post_type'));
            add_filter('use_block_editor_for_post_type', array($this, 'disable_gutenberg_for_atquimicosclients'), 10, 2);

            // Usar template_include en lugar de the_content para mejor control de templates
            add_filter('template_include', array($this, 'load_custom_template'));
        }

        public function create_post_type()
        {
            register_post_type('atquimicossedes', [
                'label' => 'ATQuimicos Sedes',
                'description' => 'ATQuimicos Sedes Clientes Post Type',
                'labels' => array(
                    'name' => 'ATQ Sedes',
                    'singular_name' => 'Sedes',
                ),
                'public' => true,
                'supports' => array('title', 'thumbnail'),
                'hierarchical' => true,
                'show_ui' => false,
                'show_in_menu' => true,
                'menu_position' => 56,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => false,
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'show_in_rest' => true,
                'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode('<svg fill="#000000" height="200px" width="200px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 496 496" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M496,0H352v40h-40c-10.816,0-20.896,3.136-29.464,8.472C265.32,42.984,247.008,40,228,40c-99.248,0-180,80.752-180,180 v33.96L3.584,335.384C1.24,339.688,0,344.552,0,349.456c0,14.52,10.84,27.032,25.224,29.096L56,382.936V408 c0,30.872,25.128,56,56,56h24v32h16v-48h-40c-22.056,0-40-17.944-40-40v-38.936l-44.504-6.36 C20.936,361.768,16,356.072,16,349.456c0-2.232,0.56-4.456,1.632-6.416l46.368-85V220c0-90.432,73.568-164,164-164 c13.896,0,27.32,1.928,40.208,5.192C260.592,70.76,256,82.848,256,96c0,30.872,25.128,56,56,56h65.056 C386.56,172.744,392,195.728,392,220c0,4.008-0.296,8-0.504,12H368v-40h-52.984c-17.168-29.712-48.512-48-83.016-48 c-52.936,0-96,43.064-96,96c0,52.936,43.064,96,96,96c34.504,0,65.848-18.288,83.016-48H368v-40h22.128 c-5.088,42.584-21.456,83.592-47.696,117.704L312,405.28V496h16v-85.28l27.128-35.264C383.504,338.568,401.08,294.104,406.24,248 H408c30.872,0,56-25.128,56-56s-25.128-56-56-56h-20.872c-7.768-14.648-17.504-28.088-28.84-40H496V0z M232,320 c-44.112,0-80-35.888-80-80s35.888-80,80-80c25.472,0,48.8,12.048,63.76,32H224v96h71.76C280.8,307.952,257.472,320,232,320z M256,208v64h-16v-64H256z M352,272h-80v-64h80V272z M408,152c22.056,0,40,17.944,40,40c0,22.056-17.944,40-40,40h-0.504 c0.192-4,0.504-7.992,0.504-12c0-24.056-4.776-47.008-13.376-68H408z M312,56h40v33.712c-14.08-13.4-30.296-24.56-48.112-32.88 C306.504,56.288,309.216,56,312,56z M368.592,136H312c-22.056,0-40-17.944-40-40c0-11.72,5.16-22.176,13.216-29.496 C320.264,79.616,349.592,104.312,368.592,136z M448,80h-80V16h80V80z M480,80h-16V16h16V80z"></path> </g> </g> </g></svg>')
            ]);

            add_filter('acf/load_field/key=field_66fd637608bc4', array($this, 'populate_acf_select_field'));
        }

        public function disable_gutenberg_for_atquimicosclients($use_block_editor, $post_type)
        {
            if ($post_type === 'atquimicosclients') {
                return false;
            }
            return $use_block_editor;
        }

        public function populate_acf_select_field($field)
        {
            $user = get_field('user', get_the_ID());
            if (!$user) {
                return $field;
            }

            $field['choices'] = [];

            $sedes = new WP_Query(array(
                'post_type' => 'atquimicossedes',
                'posts_per_page' => 5,
                'meta_query' => array(
                    array(
                        'key'     => 'cliente',
                        'value'   => $user,
                        'compare' => '='
                    )
                )
            ));

            if ($sedes->have_posts() && $field['key'] === 'field_66fd637608bc4') {
                while ($sedes->have_posts()) {
                    $sedes->the_post();
                    $field['choices'][get_the_ID()] = get_the_title();
                }
            } else {
                $field['choices'] = [];
            }

            return $field;
        }

        public function load_custom_template($template)
        {
            if (is_singular('atquimicossedes')) {
                $custom_template = ATQUIMICOS_REPORTS_PATH . 'templates/single-atquimicossedes.php';

                if (file_exists($custom_template)) {
                    return $custom_template;
                }
            }

            return $template; // Retorna el template original si no es el del CPT
        }
    }
}

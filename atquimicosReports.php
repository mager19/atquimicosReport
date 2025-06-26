<?php

/**
 * Plugin Name: AtquimicosReports
 * Plugin URI: https://www.wordpress.org/mv-translations
 * Description: Plugin generate client reports for Atquimicos
 * Version: 1.2.0
 * Requires at least: 5.6
 * Requires PHP: 7.0
 * Author: Mario Reyes C
 * Author URI: https://www.linkedin.com/in/mager19/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: atquimicos-reports
 * Domain Path: /languages
 */
/*
AtquimicosReports is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
AtquimicosReports is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with AtquimicosReports. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

require_once('utils/createPage.php');

use ATReports\utils;

// Cargar Composer autoloader si está disponible
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

if (!class_exists('ATQuimicosReports')) {
    class ATQuimicosReports
    {

        public function __construct()
        {

            $this->define_constants();
            add_action('init', array($this, 'enqueue_scripts'));
            add_action('admin_init', array($this, 'verify_pages_configuration'));
            add_action('wp', array($this, 'initialize_acf_form')); // Agregar hook para ACF

            // posttypes
            require_once(ATQUIMICOS_REPORTS_PATH . 'post-types/reports.php');
            $reports = new ATQuimicosReportsCPT();
            require_once(ATQUIMICOS_REPORTS_PATH . 'post-types/sedes.php');
            $sedes = new ATQuimicosSedesCPT();

            // shortcodes
            require_once(ATQUIMICOS_REPORTS_PATH . 'shortcodes/addsede.php');
            $addSede = new AddSede();
            require_once(ATQUIMICOS_REPORTS_PATH . 'shortcodes/addReport.php');
            $addReport = new AddReport();
            require_once(ATQUIMICOS_REPORTS_PATH . 'shortcodes/registerUser.php');
            $registerUser = new RegisterUser();
            require_once(ATQUIMICOS_REPORTS_PATH . 'shortcodes/loginUser.php');
            $loginUser = new LoginUser();
            require_once(ATQUIMICOS_REPORTS_PATH . 'shortcodes/loginTechnician.php');
            $loginTechnician = new LoginTechnician();
            require_once(ATQUIMICOS_REPORTS_PATH . 'shortcodes/diagnostics.php');
            $diagnostics = new ATQuimicosDiagnostics();

            // PDF Generator
            require_once(ATQUIMICOS_REPORTS_PATH . 'includes/pdf-generator.php');

            $myUpdateChecker = PucFactory::buildUpdateChecker(
                'https://github.com/mager19/atquimicosReport/',
                __FILE__,
                'ATQuimicosReports'
            );

            //Set the branch that contains the stable release.
            $myUpdateChecker->setBranch('releases');
        }

        public function verify_pages_configuration()
        {
            // Verificar que todas las páginas del plugin existan
            $pages_to_check = array(
                'atquimicos_reporte_page_id' => 'Reportes',
                'atquimicos_login_page_id' => 'Login ATQuimicos Clientes',
                'atquimicos_technician_login_page_id' => 'Login ATQuimicos Técnicos',
                'atquimicos_reports_page_id' => 'Crear Reporte',
                'atquimicos_sede_page_id' => 'Crear Sede'
            );

            $pages_missing = false;

            foreach ($pages_to_check as $option_name => $page_title) {
                $page_id = get_option($option_name);

                if (!$page_id || get_post_status($page_id) !== 'publish') {
                    $pages_missing = true;
                    break;
                }
            }

            // Si hay páginas faltantes, recrearlas
            if ($pages_missing) {
                ATReports\utils\ATQuimicosReportsCreatePage::create_page();
            }
        }

        public function initialize_acf_form()
        {
            // Verificar que estemos en una página y que ACF esté disponible
            if (!is_page() || !function_exists('acf_form_head')) {
                return;
            }

            global $post;
            if (!$post || !isset($post->post_content)) {
                return;
            }

            // Solo ejecuta acf_form_head en páginas donde se usa el shortcode
            $content = $post->post_content;
            if (
                has_shortcode($content, 'acf_form_sedes') ||
                has_shortcode($content, 'acf_form_sede') ||
                has_shortcode($content, 'acf_form_report')
            ) {
                acf_form_head();
            }
        }

        public function define_constants()
        {
            // Path/URL to root of this plugin, with trailing slash.
            define('ATQUIMICOS_REPORTS_PATH', plugin_dir_path(__FILE__));
            define('ATQUIMICOS_REPORTS_URL', plugin_dir_url(__FILE__));
            define('ATQUIMICOS_REPORTS_VERSION', '1.0.6');
        }

        /**
         * Activate the plugin
         */
        public static function activate()
        {

            // Crear el rol de cliente
            add_role(
                'cliente', // Slug del rol
                'Cliente', // Nombre del rol
                array(
                    'read' => true, // Permisos básicos (leer)

                )
            );

            add_role(
                'tecnico', // Slug del rol
                'Tecnico ATQuimicos', // Nombre del rol
                array(
                    'read' => true, // Permisos básicos (leer)
                )
            );

            ATReports\utils\ATQuimicosReportsCreatePage::check_and_create_page();

            // Limpiar la caché de roles y capacidades
            if (function_exists('wp_roles')) {
                wp_roles()->flush_caps();
            }

            update_option('rewrite_rules', '');
        }

        /**
         * Deactivate the plugin
         */
        public static function deactivate()
        {
            remove_role('cliente');
            remove_role('tecnico');
            // ATQuimicosReportsCreatePage::delete_page();
            ATReports\utils\ATQuimicosReportsCreatePage::delete_page();
            flush_rewrite_rules();
        }

        /**
         * Uninstall the plugin
         */
        public static function uninstall() {}

        public function enqueue_scripts()
        {
            wp_enqueue_style(
                'atquimicos-styles',
                ATQUIMICOS_REPORTS_URL . 'assets/css/styles.css',
                array(),
                ATQUIMICOS_REPORTS_VERSION,
                'all'
            );

            // Encolar JavaScript para funcionalidad de mensajes y formularios
            wp_enqueue_script(
                'atquimicos-scripts',
                ATQUIMICOS_REPORTS_URL . 'assets/js/sedes.js',
                array('jquery'),
                ATQUIMICOS_REPORTS_VERSION,
                true
            );

            wp_localize_script('acf-dynamic-sedes', 'atquimicos_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));

            add_action('wp_ajax_filter_reports', 'filter_reports');
            add_action('wp_ajax_nopriv_filter_reports', 'filter_reports');

            // Agregar acción AJAX para verificar estado de login
            add_action('wp_ajax_check_login_status', 'check_login_status');
            add_action('wp_ajax_nopriv_check_login_status', 'check_login_status');

            // Agregar acción AJAX para verificar estado de login de técnicos
            add_action('wp_ajax_check_technician_login_status', 'check_technician_login_status');
            add_action('wp_ajax_nopriv_check_technician_login_status', 'check_technician_login_status');

            function check_login_status()
            {
                $response = array(
                    'logged_in' => is_user_logged_in(),
                    'redirect_url' => ''
                );

                if (is_user_logged_in()) {
                    $current_user = wp_get_current_user();
                    if (in_array('cliente', $current_user->roles) || in_array('administrator', $current_user->roles)) {
                        // Crear instancia temporal de LoginUser para obtener URL
                        $login_user = new LoginUser();
                        $response['redirect_url'] = $login_user->get_user_reports_url($current_user->ID);
                    }
                }

                wp_send_json($response);
            }

            function check_technician_login_status()
            {
                $response = array(
                    'logged_in' => is_user_logged_in(),
                    'is_technician' => false,
                    'redirect_url' => ''
                );

                if (is_user_logged_in()) {
                    $current_user = wp_get_current_user();
                    if (in_array('tecnico', $current_user->roles) || in_array('administrator', $current_user->roles)) {
                        $response['is_technician'] = true;
                        $response['redirect_url'] = home_url('/crear-reporte/');
                    }
                }

                wp_send_json($response);
            }

            function filter_reports()
            {
                // Verificar nonce para seguridad
                if (!wp_verify_nonce($_POST['nonce'], 'filter_reports_nonce')) {
                    wp_die('Error de seguridad: Nonce inválido');
                }

                $year = sanitize_text_field($_POST['year']);
                $month = sanitize_text_field($_POST['month']);
                $sede = isset($_POST['sede']) ? intval($_POST['sede']) : 0;
                $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

                $current_user = wp_get_current_user();

                // Verificar que el usuario esté logueado y sea cliente
                if (!is_user_logged_in() || !in_array('cliente', $current_user->roles)) {
                    wp_die('Acceso no autorizado');
                }

                // Determinar qué user_id usar para la consulta
                $target_user_id = $current_user->ID; // Por defecto, usar el usuario actual

                // Si se proporciona un user_id diferente, verificar permisos
                if ($user_id && $user_id !== $current_user->ID) {
                    if (current_user_can('manage_options')) {
                        $target_user_id = $user_id; // Admin puede ver reportes de otros
                    }
                    // Si no es admin, mantener el user_id actual
                }

                $args = array(
                    'post_type' => 'atquimicosreports',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'cliente',
                            'value' => $target_user_id,
                            'compare' => '='
                        )
                    ),
                    'orderby' => 'post_date',
                    'order'   => 'DESC',
                );

                // Agregar filtro por sede si se especifica
                if (!empty($sede)) {
                    $args['meta_query'][] = array(
                        'key' => 'sedes',
                        'value' => '"' . $sede . '"',
                        'compare' => 'LIKE'
                    );
                }

                if (!empty($year) && !empty($month)) {
                    $args['date_query'] = array(
                        array(
                            'year' => intval($year),
                            'month' => intval($month)
                        )
                    );
                } elseif (!empty($year)) {
                    $args['date_query'] = array(
                        array(
                            'year' => intval($year)
                        )
                    );
                } elseif (!empty($month)) {
                    $args['date_query'] = array(
                        array(
                            'month' => intval($month)
                        )
                    );
                }

                $reports = new WP_Query($args);

                if ($reports->have_posts()) :
                    $grouped_reports = [];

                    while ($reports->have_posts()) : $reports->the_post();
                        $month = get_the_date('F Y');
                        $grouped_reports[$month][] = [
                            'title' => get_the_title(),
                            'link'  => get_permalink(),
                        ];
                    endwhile;

                    foreach ($grouped_reports as $month => $reports) :
                        echo "<h2>" . esc_html($month) . "</h2>";
                        echo "<ul>";
                        foreach ($reports as $report) :
                            echo "<li><a href='" . esc_url($report['link']) . "' target='_blank'>" . esc_html($report['title']) . "</a></li>";
                        endforeach;
                        echo "</ul>";
                    endforeach;
                else :
                    echo '<p>No se encontraron reportes para los filtros seleccionados.</p>';
                endif;

                wp_reset_postdata();
                wp_die();
            }
        }

        public function load_dependencies()
        {
            // Debug temporal para producción
            if (file_exists(ATQUIMICOS_REPORTS_PATH . 'debug-redirect.php')) {
                require_once(ATQUIMICOS_REPORTS_PATH . 'debug-redirect.php');
            }

            // Configuraciones específicas para producción
            if (file_exists(ATQUIMICOS_REPORTS_PATH . 'production-config.php')) {
                require_once(ATQUIMICOS_REPORTS_PATH . 'production-config.php');
            }

            // ...existing code...
        }
    }
}

if (class_exists('ATQuimicosReports')) {

    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('ATQuimicosReports', 'activate'));
    register_deactivation_hook(__FILE__, array('ATQuimicosReports', 'deactivate'));
    register_uninstall_hook(__FILE__, array('ATQuimicosReports', 'uninstall'));

    $atquimicosReports = new ATQuimicosReports();
}

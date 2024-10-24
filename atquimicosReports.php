<?php

/**
 * Plugin Name: AtquimicosReports
 * Plugin URI: https://www.wordpress.org/mv-translations
 * Description: Plugin generate client reports for Atquimicos
 * Version: 1.0
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

if (!class_exists('ATQuimicosReports')) {
    class ATQuimicosReports
    {

        public function __construct()
        {

            $this->define_constants();

            require_once(ATQUIMICOS_REPORTS_PATH . 'post-types/reports.php');
            $reports = new ATQuimicosReportsCPT();

            require_once(ATQUIMICOS_REPORTS_PATH . 'post-types/sedes.php');
            $sedes = new ATQuimicosSedesCPT();

            require_once(ATQUIMICOS_REPORTS_PATH . 'shortcodes/addsede.php');

            add_shortcode('acf_form_sedes', array($this, 'acf_formulario_sedes'));

            add_filter('acf/prepare_field/name=_post_title', array($this, 'my_acf_prepare_field'));

            add_action('init', array($this, 'enqueue_scripts'));

            $myUpdateChecker = PucFactory::buildUpdateChecker(
                'https://github.com/mager19/atquimicosReport/',
                __FILE__,
                'ATQuimicosReports'
            );

            //Set the branch that contains the stable release.
            $myUpdateChecker->setBranch('releases');
        }

        public function initialize_acf_form()
        {
            // Solo ejecuta acf_form_head en páginas donde se usa el shortcode
            if (is_page() && has_shortcode(get_post()->post_content, 'acf_form_sedes')) {
                acf_form_head();
            }
        }

        public function define_constants()
        {
            // Path/URL to root of this plugin, with trailing slash.
            define('ATQUIMICOS_REPORTS_PATH', plugin_dir_path(__FILE__));
            define('ATQUIMICOS_REPORTS_URL', plugin_dir_url(__FILE__));
            define('ATQUIMICOS_REPORTS_VERSION', '1.0.0');
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
            flush_rewrite_rules();
        }

        /**
         * Uninstall the plugin
         */
        public static function uninstall() {}

        public function acf_formulario_sedes()
        {
            if (function_exists('acf_form')) {
                ob_start(); // Usamos ob_start para evitar que acf_form_head se muestre fuera de lugar
                acf_form_head();
                acf_form(array(
                    'post_id'       => 'new_post',
                    'new_post'      => array(
                        'post_type'     => 'atquimicosreports',
                        'post_status'   => 'publish'
                    ),
                    'id'            => 'acf_form_atquimicos',
                    'field_groups'  => array('group_670ec5544293d'),
                    'submit_value'  => 'Enviar',
                    'post_content'   => false,
                    'post_title' => true,
                    'return' => '',
                ));
            } else {
                return '<p>El plugin ACF no está activo.</p>';
            }

            return ob_get_clean();
        }

        public function my_acf_save_post($post_id)
        {
            wp_redirect(get_permalink($post_id));
            exit;
        }


        public function my_acf_prepare_field($field)
        {

            $field['label'] = "Nombre del Reporte";
            $field['instructions'] = "Por favor ingrese el nombre del reporte según el formato de reporte";

            return $field;
        }

        public function enqueue_scripts()
        {
            wp_enqueue_script(
                'acf-dynamic-sedes',
                ATQUIMICOS_REPORTS_URL . 'assets/js/sedes.js',
                array('jquery'), // Include jQuery
                ATQUIMICOS_REPORTS_VERSION,
                true
            );

            wp_enqueue_style(
                'atquimicos-styles',
                ATQUIMICOS_REPORTS_URL . 'assets/css/styles.css',
                array(),
                ATQUIMICOS_REPORTS_VERSION,
                'all'
            );

            wp_localize_script('acf-dynamic-sedes', 'atquimicos_ajax', array('ajaxurl' => admin_url('admin-ajax.php')));
        }
    }
}

// Plugin Instantiation
if (class_exists('ATQuimicosReports')) {

    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('ATQuimicosReports', 'activate'));
    register_deactivation_hook(__FILE__, array('ATQuimicosReports', 'deactivate'));
    register_uninstall_hook(__FILE__, array('ATQuimicosReports', 'uninstall'));

    // Instatiate the plugin class
    $atquimicosReports = new ATQuimicosReports();
}

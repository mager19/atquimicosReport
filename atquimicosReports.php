<?php

/**
 * Plugin Name: AtquimicosReports
 * Plugin URI: https://www.wordpress.org/mv-translations
 * Description: Plugin generate client reports for Atquimicos
 * Version: 1.0.3
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
            add_action('init', array($this, 'enqueue_scripts'));

            require_once(ATQUIMICOS_REPORTS_PATH . 'post-types/reports.php');
            $reports = new ATQuimicosReportsCPT();

            require_once(ATQUIMICOS_REPORTS_PATH . 'post-types/sedes.php');
            $sedes = new ATQuimicosSedesCPT();

            require_once(ATQUIMICOS_REPORTS_PATH . 'shortcodes/addsede.php');
            require_once(ATQUIMICOS_REPORTS_PATH . 'shortcodes/addReport.php');
            $addReport = new AddReport();

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

        public function enqueue_scripts()
        {
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

if (class_exists('ATQuimicosReports')) {

    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('ATQuimicosReports', 'activate'));
    register_deactivation_hook(__FILE__, array('ATQuimicosReports', 'deactivate'));
    register_uninstall_hook(__FILE__, array('ATQuimicosReports', 'uninstall'));

    $atquimicosReports = new ATQuimicosReports();
}

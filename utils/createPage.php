<?php

namespace ATReports\utils;

if (!class_exists('ATQuimicosReportsCreatePage')) {
    class ATQuimicosReportsCreatePage
    {

        public static function create_page()
        {
            $createReport = array(
                'post_title' => 'Crear Reporte',
                'post_content' => '
                                    <!-- wp:shortcode -->
                                        [acf_form_report]
                                    <!-- /wp:shortcode -->
                ',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page'
            );

            $createSede = array(
                'post_title' => 'Crear Sede',
                'post_content' => '
                                    <!-- wp:shortcode -->
                                        [acf_form_sede]
                                    <!-- /wp:shortcode -->
                ',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page'
            );

            $createReport_page_id = wp_insert_post($createReport);
            $createSede_page_id = wp_insert_post($createSede);
            update_option('atquimicos_reports_page_id', $createReport_page_id);
            update_option('atquimicos_sede_page_id', $createSede_page_id);
        }

        public static function delete_page()
        {
            $createReport_page_id = get_option('atquimicos_reports_page_id');
            $createSede_page_id = get_option('atquimicos_sede_page_id');

            $pages = array($createReport_page_id, $createSede_page_id);

            foreach ($pages as $page_id) {
                if ($page_id) {
                    wp_delete_post($page_id, true);
                }
            }
        }

        public static function check_and_create_page()
        {
            $page_id = get_option('atquimicos_reports_page_id');
            if (!$page_id || get_post_status($page_id) === false) {
                self::create_page();
            }
        }
    }
}

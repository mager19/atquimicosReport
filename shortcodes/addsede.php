<?php

if (!class_exists('AddSede')) {
    class AddSede
    {
        public function __construct()
        {

            add_shortcode('acf_form_sede', array($this, 'acf_formulario_sede'));
            add_filter('acf/form/redirect', array($this, 'redirect_to_single_sede'), 10, 1);
            add_filter('the_content', array($this, 'message'));
        }

        public function acf_formulario_sede()
        {
            // Verificar si el usuario está logueado
            if (!is_user_logged_in()) {
                return '<p>Usted no tiene los permisos necesario para ver el contenido.</p>';
            }

            // Verificar si el usuario tiene el rol adecuado
            $current_user = wp_get_current_user();
            if (!in_array('tecnico', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
                return '<p>No tienes permisos para acceder a este formulario.</p>';
            }

            if (function_exists('acf_form')) {
                ob_start(); // Usamos ob_start para evitar que acf_form_head se muestre fuera de lugar
                acf_form_head();
                acf_form(array(
                    'post_id'       => 'new_post',
                    'new_post'      => array(
                        'post_type'     => 'atquimicossedes',
                        'post_status'   => 'publish'
                    ),
                    'id'            => 'acf_form_atquimicos',
                    'field_groups'  => array('group_670ff0360371a'),
                    'submit_value'  => 'Crear Sede',
                    'honeypot' => true,
                    'kses' => true,
                    'post_content'   => false,
                    'post_title' => true,
                    'return' => '%post_url%?sede=new',
                    'html_updated_message'  => '<div id="message" class="updated"><p>%s</p></div>',
                ));
            } else {
                return '<p>El plugin ACF no está activo. Este plugin es requerido para crear los reportes</p>';
            }

            return ob_get_clean();
        }

        public function message($content)
        {
            if (isset($_GET['sede']) && $_GET['sede'] === 'new') {

                $message = '<div class="atquimicos__notice">¡El reporte se ha creado exitosamente!</div>';
                // Concatenar el mensaje al contenido de la página
                return $message . $content;
            }

            return $content;
        }
    }
}

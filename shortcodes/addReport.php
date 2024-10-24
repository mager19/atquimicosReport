<?php
if (!class_exists('AddReport')) {

    class AddReport
    {
        public function __construct()
        {

            add_shortcode('acf_form_report', array($this, 'acf_formulario_report'));
            add_filter('acf/prepare_field/name=_post_title', array($this, 'my_acf_prepare_field'));
        }

        public function acf_formulario_report()
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

        public function my_acf_prepare_field($field)
        {

            $field['label'] = "Nombre del Reporte";
            $field['instructions'] = "Por favor ingrese el nombre del reporte según el formato de reporte";

            return $field;
        }
    }
    new AddReport();
}

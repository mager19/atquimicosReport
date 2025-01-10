<?php
add_action('acf/include_fields', 'add_acf_fields');
function add_acf_fields()
{
    acf_add_local_field_group(array(
        'key' => 'group_670ec5544293d',
        'title' => 'Report Fields',
        'fields' => array(
            array(
                'key' => 'field_6711a6d103984',
                'label' => 'Fecha',
                'name' => 'fecha',
                'aria-label' => '',
                'type' => 'date_picker',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'display_format' => 'd/m/Y',
                'return_format' => 'd/m/Y',
                'first_day' => 1,
                'allow_in_bindings' => 0,
            ),
            array(
                'key' => 'field_6711a7386dacf',
                'label' => 'Técnico ATQuímicos',
                'name' => 'tecnico_atquimicos',
                'aria-label' => '',
                'type' => 'user',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'role' => array(
                    0 => 'tecnico',
                ),
                'return_format' => 'array',
                'multiple' => 0,
                'allow_null' => 0,
                'allow_in_bindings' => 0,
                'bidirectional' => 0,
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_670ecb5d52c03',
                'label' => 'Cliente',
                'name' => 'cliente',
                'aria-label' => '',
                'type' => 'user',
                'instructions' => 'Seleccione el cliente relacionado con el reporte, más adelante se le solicitará la sede',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'role' => array(
                    0 => 'cliente',
                ),
                'return_format' => 'object',
                'multiple' => 0,
                'allow_null' => 0,
                'allow_in_bindings' => 0,
                'bidirectional' => 0,
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_67111001aedd0',
                'label' => 'Sedes',
                'name' => 'sedes',
                'aria-label' => '',
                'type' => 'relationship',
                'instructions' => 'Por favor selecciona una sede, puedes usar el buscador',
                'required' => 1,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_670ecb5d52c03',
                            'operator' => '!=empty',
                        ),
                    ),
                ),
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => array(
                    0 => 'atquimicossedes',
                ),
                'post_status' => array(
                    0 => 'publish',
                ),
                'taxonomy' => '',
                'filters' => array(
                    0 => 'search',
                ),
                'return_format' => 'object',
                'min' => 1,
                'max' => 1,
                'allow_in_bindings' => 0,
                'elements' => '',
                'bidirectional' => 0,
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_671120c319fde',
                'label' => 'Tipo',
                'name' => 'tipo',
                'aria-label' => '',
                'type' => 'select',
                'instructions' => '',
                'required' => 1,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_670ecb5d52c03',
                            'operator' => '!=empty',
                        ),
                    ),
                    array(
                        array(
                            'field' => 'field_67111001aedd0',
                            'operator' => '!=empty',
                        ),
                    ),
                ),
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array(
                    'caldera' => 'Caldera',
                    'torre' => 'Torre',
                    'chiller' => 'Chiller',
                ),
                'default_value' => false,
                'return_format' => 'value',
                'multiple' => 0,
                'allow_null' => 0,
                'allow_in_bindings' => 0,
                'ui' => 1,
                'ajax' => 0,
                'placeholder' => '',
            ),
            array(
                'key' => 'field_6711223a24414',
                'label' => 'Variables Caldera',
                'name' => 'variables_caldera',
                'aria-label' => '',
                'type' => 'group',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_671120c319fde',
                            'operator' => '==',
                            'value' => 'caldera',
                        ),
                        array(
                            'field' => 'field_67111001aedd0',
                            'operator' => '!=empty',
                        ),
                    ),
                ),
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_6711226224415',
                        'label' => 'Dureza del Suavizador',
                        'name' => 'dureza_del_suavizador',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '0.',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671122a271278',
                        'label' => 'pH',
                        'name' => 'ph',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '10.5 - 11.5',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671123b6d755b',
                        'label' => 'Dureza Total ppm',
                        'name' => 'dureza_total_ppm',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => 'Máximo 20',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_6711240625b37',
                        'label' => 'Alcalinidad P ppm',
                        'name' => 'alcalinidad_p_ppm',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_6711241d25b38',
                        'label' => 'Alcalinidad M ppm',
                        'name' => 'alcalinidad_m_ppm',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => 'Máximo 700',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671125c0c15cf',
                        'label' => 'Alcalinidad OH	ppm',
                        'name' => 'alcalinidad_oh__ppm',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '100-400',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671ab90f10865',
                        'label' => 'Solidos Disueltos ppm',
                        'name' => 'solidos_disueltos_ppm',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '30 - 60',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_67112694465e0',
                        'label' => 'Fosfatos ppm',
                        'name' => 'fosfatos_ppm',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '30 - 60',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671126af465e1',
                        'label' => 'Sulfitos ppm',
                        'name' => 'sulfitos_ppm',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '30 - 60',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671126c5465e2',
                        'label' => 'Hierro ppm',
                        'name' => 'hierro_ppm',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => 'Máximo 5',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671126de465e3',
                        'label' => 'Sílice ppm',
                        'name' => 'silice_ppm',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => 'Máximo 150',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671126f2465e4',
                        'label' => 'Oxígeno ppm',
                        'name' => 'oxigeno_ppm',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => '0.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                ),
            ),
            array(
                'key' => 'field_671155e6ef0c6',
                'label' => 'Variables otros',
                'name' => 'variables_otros',
                'aria-label' => '',
                'type' => 'group',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_671120c319fde',
                            'operator' => '!=',
                            'value' => 'caldera',
                        ),
                        array(
                            'field' => 'field_67111001aedd0',
                            'operator' => '!=empty',
                        ),
                    ),
                ),
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_671155e6ef0c7',
                        'label' => 'Dureza Total',
                        'name' => 'dureza_total',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => 'Máximo 250',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671155e6ef0c8',
                        'label' => 'pH',
                        'name' => 'ph',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => 'Máximo 9.0',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671155e6ef0cb',
                        'label' => 'Alcalinidad M',
                        'name' => 'alcalinidad_m',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => 'Máximo 500',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671155e6ef0cc',
                        'label' => 'Sólidos Disueltos',
                        'name' => 'solidos_disueltos',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => 'Máximo 1500',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671155e6ef0ce',
                        'label' => 'Fosfatos',
                        'name' => 'fosfatos',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => 'Min 5 - Máx 10',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671155e6ef0d1',
                        'label' => 'Sílice',
                        'name' => 'silice',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => 'Máximo 200',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_671155e6ef0d0',
                        'label' => 'Hierro',
                        'name' => 'hierro',
                        'aria-label' => '',
                        'type' => 'number',
                        'instructions' => 'Máximo 10',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'min' => '',
                        'max' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'step' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'atquimicosreports',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
}

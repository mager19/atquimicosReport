<?php

add_action('acf/include_fields', 'sedesFields');

function sedesFields()
{
    acf_add_local_field_group(array(
        'key' => 'group_670ff0360371a',
        'title' => 'Sedes Fields',
        'fields' => array(
            array(
                'key' => 'field_670ff0363e99b',
                'label' => 'Cliente',
                'name' => 'cliente',
                'aria-label' => '',
                'type' => 'user',
                'instructions' => '',
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
                'return_format' => 'id',
                'multiple' => 0,
                'allow_null' => 0,
                'allow_in_bindings' => 0,
                'bidirectional' => 0,
                'bidirectional_target' => array(),
            ),
            array(
                'key' => 'field_670ff0d597360',
                'label' => 'Contacto en la sede',
                'name' => 'contacto_en_la_sede',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_670ff0363e99b',
                            'operator' => '!=empty',
                        ),
                    ),
                ),
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'maxlength' => '',
                'allow_in_bindings' => 0,
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'atquimicossedes',
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

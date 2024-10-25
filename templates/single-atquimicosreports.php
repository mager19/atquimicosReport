<?php

$fecha = get_field('fecha');
$tecnico = get_field('tecnico_atquimicos');
$cliente = get_field('cliente');
$sede = get_field('sedes');
$tipo_reporte = get_field('tipo');
$type = get_field('tipo');
?>

<div class="container__atquimicos__report">
    <?php
    if (!is_user_logged_in()) {
        echo '<p>Debes iniciar sesión para ver este contenido.</p>';
        return;
    }
    ?>
    <h3>Informe: <?php the_title(); ?></h3>
    <p><span>Fecha: <?php echo $fecha; ?></p>
    <p><span>Técnico ATQuimicos: </span><?php echo $tecnico['display_name']; ?></p>
    <p><span>Cliente:</span> <?php echo $cliente->display_name; ?></p>
    <p><span>Sede: </span><?php echo $sede[0]->post_title; ?></p>


    <?php
    if ($type && $type === 'caldera') {
        $variables = get_field('variables_caldera');

        $parametros_fijos = array(
            'dureza_del_suavizador' => '0',
            'ph' => '10.5 - 11.5',
            'dureza_total_ppm' => 'Máximo 20',
            'alcalinidad_p_ppm' => '',
            'alcalinidad_m_ppm' => 'Máximo 700',
            'alcalinidad_oh__ppm' => '100 - 400',
            'solidos_disueltos_ppm' => 'Máximo 2500',
            'fosfatos_ppm' => '30 - 60',
            'sulfitos_ppm' => '30 - 60',
            'hierro_ppm' => 'Máximo 5',
            'silice_ppm' => 'Máximo 150',
            'oxigeno_ppm' => '0',
        );
    } else {
        $variables = get_field('variables_otros');
        $parametros_fijos = array(
            'dureza_total' => 'Máximo 250',
            'ph' => 'Máximo 9',
            'alcalinidad_m' => 'Máximo 500',
            'solidos_disueltos' => 'Máximo 1500',
            'fosfatos' => 'Min 5 - Máx 10',
            'silice' => 'Máximo 200',
            'hierro' => 'Máximo 10',
        );
    }


    if ($variables) {
        if ($variables) {
            echo '<table class="table_component" role="region" tabindex="0">';
            echo '<thead>';
            echo '    <tr>';
            echo '        <th>Variable</th>';
            echo '        <th>Valor</th>';
            echo '        <th>Parámetro</th>';
            echo '    </tr>';
            echo '</thead>';
            echo '<tbody>';

            // Iterar sobre el grupo de campos
            foreach ($variables as $key => $value) {
                // Supongamos que 'valor' y 'parametro' son subcampos en el grupo
                $parametro = isset($parametros_fijos[$key]) ? $parametros_fijos[$key] : 'N/A';
                echo '<tr>';
                echo '    <td>' . esc_html(str_replace('_', ' ', $key)) . '</td>';
                echo '    <td>' . esc_html($value) . '</td>';
                echo '    <td>' . esc_html($parametro) . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No hay variables disponibles.</p>';
        }
    }



    ?>
</div>
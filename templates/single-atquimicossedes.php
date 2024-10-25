<?php
$cliente = get_field('cliente');
$contacto = get_field('contacto_en_la_sede');

?>

<div class="container__atquimicos__report">
    <h3>Sede: <?php the_title(); ?></h3>
    <p><span>Cliente:</span> <?php echo $cliente->display_name; ?></p>
    <p><span>Contacto en la sede:</span> <?php echo $contacto; ?></p>
</div>
<div class="container__atquimicos__report reports-page">
    <?php
    if (!is_user_logged_in()) {
        echo '<p>Debes iniciar sesión para ver este contenido.</p>';
        return;
    }

    $current_user = wp_get_current_user();

    if (!in_array('cliente', $current_user->roles)) {
        echo "<p>Esta acción solo está disponible para clientes. Si usted es administrador o cliente puede usar.</p>";
        return;
    }

    // Verificar si hay un user_id en la URL (para administradores que ven reportes de otros clientes)
    $target_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : $current_user->ID;

    // Si el user_id es diferente al usuario actual, verificar permisos de administrador
    if ($target_user_id !== $current_user->ID && !current_user_can('manage_options')) {
        $target_user_id = $current_user->ID; // Forzar a ver solo sus propios reportes
    }

    // Obtener los años disponibles para este usuario específico usando WP_Query
    $years_query = new WP_Query(array(
        'post_type' => 'atquimicosreports',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'cliente',
                'value' => $target_user_id,
                'compare' => '='
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC',
        'fields' => 'ids' // Solo obtener IDs para optimizar
    ));

    $years = array();
    if ($years_query->have_posts()) {
        foreach ($years_query->posts as $post_id) {
            $year = get_the_date('Y', $post_id);
            if (!in_array($year, $years)) {
                $years[] = $year;
            }
        }
    }
    wp_reset_postdata();

    // Obtener las sedes disponibles para este usuario específico
    $sedes_query = new WP_Query(array(
        'post_type' => 'atquimicossedes',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'cliente',
                'value' => $target_user_id,
                'compare' => '='
            )
        ),
        'orderby' => 'title',
        'order' => 'ASC'
    ));

    $sedes = array();
    if ($sedes_query->have_posts()) {
        while ($sedes_query->have_posts()) {
            $sedes_query->the_post();
            $sedes[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title()
            );
        }
    }
    wp_reset_postdata();

    // Generar nonce para seguridad
    $filter_nonce = wp_create_nonce('filter_reports_nonce');
    ?>

    <!-- Header de la página -->
    <div class="reports-header">
        <h1>Mis Reportes</h1>
        <p>Consulta y filtra todos tus reportes de análisis químicos</p>
    </div>

    <!-- Contenedor de filtros -->
    <div class="filters-container">
        <h3>Filtros de búsqueda</h3>
        <form id="filter-form">
            <div class="filter-group">
                <label for="year">Año</label>
                <select id="year" name="year">
                    <option value="">Todos los años</option>
                    <?php
                    foreach ($years as $year) {
                        echo "<option value='$year'>$year</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="month">Mes</label>
                <select id="month" name="month">
                    <option value="">Todos los meses</option>
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $month = date('F', mktime(0, 0, 0, $i, 10));
                        echo "<option value='$i'>$month</option>";
                    }
                    ?>
                </select>
            </div>

            <?php if (count($sedes) > 1): ?>
                <div class="filter-group">
                    <label for="sede">Sede</label>
                    <select id="sede" name="sede">
                        <option value="">Todas las sedes</option>
                        <?php
                        foreach ($sedes as $sede) {
                            echo "<option value='" . esc_attr($sede['id']) . "'>" . esc_html($sede['title']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Contenedor de reportes -->
    <div class="reports-content">
        <div class="reports-content-header">
            <h2>Resultados</h2>
        </div>

        <div id="reports-container">
            <?php
            // Cargar todos los reportes del usuario inicialmente
            $args = array(
                'post_type' => 'atquimicosreports',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'cliente',
                        'value' => $target_user_id,
                        'compare' => '='
                    )
                ),
                'orderby' => 'post_date',
                'order'   => 'DESC',
            );

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

                foreach ($grouped_reports as $month => $reports_list) :
                    echo "<h2>" . esc_html($month) . "</h2>";
                    echo "<ul>";
                    foreach ($reports_list as $report) :
                        echo "<li><a href='" . esc_url($report['link']) . "' target='_blank'>" . esc_html($report['title']) . "</a></li>";
                    endforeach;
                    echo "</ul>";
                endforeach;
            else :
                echo '<div class="empty-state">';
                echo '<p>No se encontraron reportes para este cliente.</p>';
                echo '</div>';
            endif;

            wp_reset_postdata();
            ?>
        </div>
    </div>

    <script>
        document.getElementById('year').addEventListener('change', filterReports);
        document.getElementById('month').addEventListener('change', filterReports);
        <?php if (count($sedes) > 1): ?>
            document.getElementById('sede').addEventListener('change', filterReports);
        <?php endif; ?>

        function filterReports() {
            var year = document.getElementById('year').value;
            var month = document.getElementById('month').value;
            var sede = <?php echo count($sedes) > 1 ? "document.getElementById('sede').value" : "''"; ?>;

            // Mostrar estado de carga
            document.getElementById('reports-container').innerHTML = '<div class="loading-state"><p>Cargando reportes...</p></div>';

            var data = new FormData();
            data.append('action', 'filter_reports');
            data.append('year', year);
            data.append('month', month);
            data.append('sede', sede);
            data.append('user_id', '<?php echo $target_user_id; ?>');
            data.append('nonce', '<?php echo $filter_nonce; ?>');

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('reports-container').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('reports-container').innerHTML = '<div class="empty-state"><p>Error al cargar los reportes. Por favor, intenta de nuevo.</p></div>';
                });
        }
    </script>
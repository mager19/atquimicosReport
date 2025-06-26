<?php

/**
 * Generador de PDF para reportes ATQuímicos
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ATQuimicos_PDF_Generator
{

    public function __construct()
    {
        add_action('wp_ajax_generate_pdf', array($this, 'generate_pdf'));
        add_action('wp_ajax_nopriv_generate_pdf', array($this, 'generate_pdf'));
    }

    public function generate_pdf()
    {
        // Verificar que se ha pasado el ID del reporte
        if (!isset($_GET['report_id']) || empty($_GET['report_id'])) {
            wp_die('ID de reporte no válido');
        }

        $report_id = intval($_GET['report_id']);

        // Verificar que el post existe y es del tipo correcto
        $post = get_post($report_id);
        if (!$post || $post->post_type !== 'atquimicosreports') {
            wp_die('Reporte no encontrado');
        }

        // Verificar permisos
        if (!is_user_logged_in()) {
            wp_die('Debes iniciar sesión para descargar reportes');
        }

        // Obtener los datos del reporte
        $fecha = get_field('fecha', $report_id);
        $tecnico = get_field('tecnico_atquimicos', $report_id);
        $cliente = get_field('cliente', $report_id);
        $type = get_field('tipo', $report_id);

        // Obtener variables según el tipo
        if ($type && $type === 'caldera') {
            $variables = get_field('variables_caldera', $report_id);
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
            $variables = get_field('variables_otros', $report_id);
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

        // Generar el HTML del reporte
        $html = $this->generate_report_html($post, $fecha, $tecnico, $cliente, $variables, $parametros_fijos);

        // Usar TCPDF para generar el PDF
        $this->create_pdf($html, $post->post_title);
    }

    private function generate_report_html($post, $fecha, $tecnico, $cliente, $variables, $parametros_fijos)
    {
        ob_start();
?>
        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="UTF-8">
            <title>Reporte ATQuímicos - <?php echo esc_html($post->post_title); ?></title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.4;
                    color: #333;
                    margin: 20px;
                }

                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #0073aa;
                    padding-bottom: 20px;
                }

                .logo {
                    margin-bottom: 10px;
                }

                .logo-atquimicos {
                    max-width: 150px;
                    max-height: 80px;
                    margin-bottom: 10px;
                    display: block;
                    margin-left: auto;
                    margin-right: auto;
                }

                .logo-fallback {
                    width: 150px;
                    height: 80px;
                    background-color: #0073aa;
                    color: white;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    margin-bottom: 10px;
                    border-radius: 5px;
                }

                h1 {
                    color: #0073aa;
                    margin: 0;
                    font-size: 24px;
                }

                .report-info {
                    margin-bottom: 20px;
                }

                .report-info p {
                    margin: 5px 0;
                }

                .report-info span {
                    font-weight: bold;
                    color: #0073aa;
                }

                .tecnico-info {
                    background-color: #f9f9f9;
                    padding: 15px;
                    margin: 15px 0;
                    border-left: 4px solid #0073aa;
                }

                .img__cliente {
                    text-align: center;
                    margin: 10px 0;
                }

                .img__cliente img {
                    max-width: 100px;
                    max-height: 100px;
                    border-radius: 50%;
                    border: 2px solid #0073aa;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                th,
                td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }

                th {
                    background-color: #0073aa;
                    color: white;
                    font-weight: bold;
                }

                tr:nth-child(even) {
                    background-color: #f9f9f9;
                }

                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 10px;
                    color: #666;
                    border-top: 1px solid #ddd;
                    padding-top: 10px;
                }
            </style>
        </head>

        <body>
            <div class="header">
                <div class="logo">
                    <?php
                    // Intentar cargar el logo como base64 para DOMPDF
                    $logo_path = ATQUIMICOS_REPORTS_PATH . 'assets/img/logoAtquimicos.png';
                    if (file_exists($logo_path)) {
                        $logo_data = base64_encode(file_get_contents($logo_path));
                        $logo_src = 'data:image/png;base64,' . $logo_data;
                        echo '<img src="' . $logo_src . '" alt="ATQuimicos Logo" class="logo-atquimicos">';
                    } else {
                        // Fallback si no existe el logo
                        echo '<div class="logo-fallback">ATQuímicos</div>';
                    }
                    ?>
                    <p>Reporte Técnico</p>
                </div>
            </div>

            <div class="report-info">
                <h2>Informe: <?php echo esc_html($post->post_title); ?></h2>

                <p><span>Fecha: </span><?php echo $fecha ? esc_html($fecha) : 'No disponible'; ?></p>

                <div class="tecnico-info">
                    <p><span>Técnico ATQuímicos: </span><?php echo ($tecnico && isset($tecnico['display_name'])) ? esc_html($tecnico['display_name']) : 'No disponible'; ?></p>

                    <?php
                    // Mostrar imagen del técnico si existe
                    if ($tecnico && isset($tecnico['ID'])) {
                        $image = get_field('author_image', 'user_' . $tecnico['ID']);
                        if ($image) {
                            $avatarautor = $image['ID'];
                            $image_url = wp_get_attachment_image_src($avatarautor, 'full');
                            if ($image_url) {
                                echo '<div class="img__cliente">';
                                echo '<img src="' . esc_url($image_url[0]) . '" alt="Foto técnico" />';
                                echo '</div>';
                            }
                        }
                    }
                    ?>
                </div>

                <p><span>Cliente: </span><?php echo ($cliente && isset($cliente->display_name)) ? esc_html($cliente->display_name) : 'No disponible'; ?></p>
            </div>

            <?php if ($variables && is_array($variables) && !empty($variables)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Variable</th>
                            <th>Valor</th>
                            <th>Parámetro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($variables as $key => $value): ?>
                            <?php if (!empty($key) && $value !== null && $value !== ''): ?>
                                <?php $parametro = isset($parametros_fijos[$key]) ? $parametros_fijos[$key] : 'N/A'; ?>
                                <tr>
                                    <td><?php echo esc_html(str_replace('_', ' ', ucfirst($key))); ?></td>
                                    <td><?php echo esc_html($value); ?></td>
                                    <td><?php echo esc_html($parametro); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p><em>No hay variables disponibles para este reporte.</em></p>
            <?php endif; ?>

            <div class="footer">
                <p>Reporte generado el <?php echo date('d/m/Y H:i:s'); ?></p>
                <p>ATQuímicos - Soluciones en Tratamiento de Agua</p>
            </div>
        </body>

        </html>
<?php
        return ob_get_clean();
    }

    private function create_pdf($html, $filename)
    {
        // Debug: verificar constantes
        if (!defined('ATQUIMICOS_REPORTS_PATH')) {
            wp_die('Error: ATQUIMICOS_REPORTS_PATH no está definida');
        }

        // Verificar si Composer autoload está disponible
        $autoload_path = ATQUIMICOS_REPORTS_PATH . 'vendor/autoload.php';

        if (file_exists($autoload_path)) {
            require_once $autoload_path;

            // Intentar usar DOMPDF si está disponible
            if (class_exists('Dompdf\\Dompdf')) {
                $this->create_pdf_with_dompdf($html, $filename);
                return;
            }
        }

        // Fallback: generar HTML descargable
        $this->create_fallback_download($html, $filename);
    }

    private function create_pdf_with_dompdf($html, $filename)
    {
        try {
            $dompdf = new \Dompdf\Dompdf();

            // Configurar opciones
            $options = $dompdf->getOptions();
            $options->set(array(
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial',
                'dpi' => 150,
                'enable_css_float' => false,
                'enable_html5_parser' => true,
                'chroot' => ATQUIMICOS_REPORTS_PATH
            ));
            $dompdf->setOptions($options);

            // Cargar HTML
            $dompdf->loadHtml($html);

            // Configurar tamaño de papel
            $dompdf->setPaper('A4', 'portrait');

            // Renderizar PDF
            $dompdf->render();

            // Generar nombre de archivo
            $safe_filename = sanitize_file_name($filename . '-' . date('Y-m-d')) . '.pdf';

            // Output PDF
            $dompdf->stream($safe_filename, array("Attachment" => true));
            exit;
        } catch (Exception $e) {
            // Si hay error con DOMPDF, mostrar más información en desarrollo
            if (WP_DEBUG) {
                wp_die('Error generando PDF con DOMPDF: ' . $e->getMessage());
            }
            // En producción, usar fallback
            $this->create_fallback_download($html, $filename);
        }
    }

    private function create_fallback_download($html, $filename)
    {
        // Mejorar el HTML para que se vea mejor al imprimir
        $print_html = $this->add_print_styles($html);

        // Generar nombre de archivo
        $safe_filename = sanitize_file_name($filename . '-' . date('Y-m-d')) . '.html';

        // Configurar headers para descarga
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $safe_filename . '"');
        header('Content-Length: ' . strlen($print_html));
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');

        echo $print_html;
        exit;
    }

    private function add_print_styles($html)
    {
        // Agregar estilos específicos para impresión
        $print_styles = '
        <style media="print">
            @page {
                margin: 1in;
                size: A4;
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 12pt;
                line-height: 1.4;
                color: #000;
            }
            .no-print {
                display: none !important;
            }
            table {
                page-break-inside: avoid;
            }
            .header {
                page-break-after: avoid;
            }
        </style>
        <script>
            window.onload = function() {
                // Mostrar mensaje sobre cómo convertir a PDF
                alert("Para convertir a PDF: Archivo > Imprimir > Guardar como PDF");
            };
        </script>';

        // Insertar estilos antes de </head>
        $html = str_replace('</head>', $print_styles . '</head>', $html);

        return $html;
    }

    /**
     * Método de debug para probar la carga de imágenes
     * Puedes llamar este método temporalmente para verificar que todo funciona
     */
    public function debug_logo_loading()
    {
        if (!WP_DEBUG) {
            return;
        }

        echo "<h3>Debug: Verificación de Logo</h3>";

        // Verificar constantes
        echo "<p><strong>ATQUIMICOS_REPORTS_PATH:</strong> " . (defined('ATQUIMICOS_REPORTS_PATH') ? ATQUIMICOS_REPORTS_PATH : 'NO DEFINIDA') . "</p>";
        echo "<p><strong>ATQUIMICOS_REPORTS_URL:</strong> " . (defined('ATQUIMICOS_REPORTS_URL') ? ATQUIMICOS_REPORTS_URL : 'NO DEFINIDA') . "</p>";

        // Verificar archivo del logo
        $logo_path = ATQUIMICOS_REPORTS_PATH . 'assets/img/logoAtquimicos.png';
        echo "<p><strong>Ruta del logo:</strong> " . $logo_path . "</p>";
        echo "<p><strong>¿Existe el archivo?:</strong> " . (file_exists($logo_path) ? 'SÍ' : 'NO') . "</p>";

        if (file_exists($logo_path)) {
            $file_size = filesize($logo_path);
            echo "<p><strong>Tamaño del archivo:</strong> " . $file_size . " bytes</p>";

            // Mostrar imagen en base64
            $logo_data = base64_encode(file_get_contents($logo_path));
            $logo_src = 'data:image/png;base64,' . $logo_data;
            echo "<p><strong>Logo en base64:</strong></p>";
            echo '<img src="' . $logo_src . '" alt="Logo Test" style="max-width: 150px; border: 1px solid #ccc;">';
        }

        // Verificar DOMPDF
        echo "<p><strong>¿DOMPDF disponible?:</strong> " . (class_exists('Dompdf\\Dompdf') ? 'SÍ' : 'NO') . "</p>";
    }
}

// Inicializar la clase
new ATQuimicos_PDF_Generator();

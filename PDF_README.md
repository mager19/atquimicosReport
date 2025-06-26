# ATQuímicos Reports - Generador de PDF

## Instalación de dependencias para PDF

Para habilitar la generación de PDF verdaderos, necesitas instalar las dependencias de PHP usando Composer.

### Paso 1: Instalar Composer (si no lo tienes instalado)

```bash
# En macOS con Homebrew
brew install composer

# O descargar desde https://getcomposer.org/
```

### Paso 2: Instalar dependencias del plugin

Navega al directorio del plugin y ejecuta:

```bash
cd /ruta/al/plugin/atquimicos-reports
composer install
```

### Paso 3: Verificar instalación

Después de ejecutar `composer install`, deberías ver una carpeta `vendor/` en el directorio del plugin.

## Funcionalidades

### Generación de PDF

El plugin incluye tres métodos para generar reportes:

1. **PDF con DOMPDF** (recomendado): Genera PDF verdaderos si las dependencias están instaladas
2. **HTML optimizado para impresión**: Fallback que genera HTML que se puede convertir a PDF desde el navegador
3. **Descarga directa**: Descarga el reporte como archivo HTML

### Uso

1. Ve a cualquier reporte individual
2. Haz clic en el botón "Descargar PDF"
3. El archivo será generado y descargado automáticamente

## Estructura del HTML generado

El PDF/HTML incluye:

- Header con logo de ATQuímicos
- Información del reporte (fecha, técnico, cliente, sede)
- Foto del técnico (si está disponible)
- Tabla de variables con parámetros
- Footer con fecha de generación

## Personalización

Para personalizar los estilos del PDF, edita los estilos CSS en el método `generate_report_html()` en el archivo `includes/pdf-generator.php`.

## Solución de problemas

### Error "Class 'Dompdf\Dompdf' not found"

Esto significa que las dependencias no están instaladas. Ejecuta:

```bash
composer install
```

### El botón no aparece

Verifica que el usuario esté logueado, ya que el botón solo se muestra a usuarios autenticados.

### El PDF se ve mal

Los estilos CSS están optimizados para A4. Si necesitas cambiar el formato, modifica la configuración en `create_pdf_with_dompdf()`.

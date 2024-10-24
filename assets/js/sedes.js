(function ($) {
    // Escuchar cambios en el campo cliente
    // $(document).on('change', '[data-key="field_670ecb5d52c03"] input, [data-key="field_670ecb5d52c03"] select', function () {
    //     var clienteID = $(this).val(); // Obtener el ID del cliente seleccionado



    //     if (!clienteID) {
    //         return; // No hacer nada si no hay un cliente seleccionado
    //     }

    //     console.log('Obteniendo sedes del cliente con ID ' + clienteID);

    //     // Hacer una solicitud AJAX para obtener las sedes del cliente
    //     $.ajax({
    //         url: atquimicos_ajax.ajaxurl, // El URL de admin-ajax.php proporcionado por wp_localize_script
    //         type: 'POST',
    //         dataType: 'json',
    //         data: {
    //             action: 'obtener_sedes_cliente', // Acción definida en PHP
    //             cliente_id: clienteID // Pasar el ID del cliente
    //         },
    //         success: function (response) {
    //             console.log(response); // Verificar la respuesta AJAX

    //             var $selectSedes = $('select[name="acf[field_6710281bec131]"]');
    //             $selectSedes.empty();

    //             // Añadir las opciones de sedes al select
    //             if (response.success) {
    //                 $.each(response.data, function (id, title) {
    //                     $selectSedes.append(new Option(title, id));
    //                 });

    //                 // Recargar el select2 después de agregar opciones
    //                 $selectSedes.trigger('change');
    //             }
    //         }
    //     });
    // });

    $(document).on('change', '[data-key="field_670ecb5d52c03"] input, [data-key="field_670ecb5d52c03"] select', function () {
        var userId = $(this).val();

        console.log('Obteniendo sedes del usuario con ID ' + userId);
        // Hacer una llamada AJAX para obtener las sedes del usuario seleccionado
        $.ajax({
            url: atquimicos_ajax.ajaxurl,
            type: 'POST',
            data: {
                action: 'my_acf_loads_field',
                user_id: userId
            },

            success: function (response) {
                // Actualizar el campo de sedes con las opciones recibidas
                var sedesField = $('select[name="acf[field_671110aa38fd1]"]');

                console.log(response);
                sedesField.empty();
                $.each(response.data, function (index, sede) {
                    sedesField.append('<option value="test">test</option>');
                    // $selectSedes.append(new Option(title, id));
                });
            }
        });
    });
})(jQuery);
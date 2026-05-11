<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;


/*
    El widget Form indica que que generara el componente
    Form de yii2 pudiendo usar los diseños de bootstrap
    id: Identificador del formulario
    action: A adonde o a que url se envían los datos por defecto yii2 utiliza la URL en donde
    se genero el formulario en caso de no tenerla definida
    method: Define la forma de envío los datos por defecto se utilza POST
    options: Define los atributos del formulario 
    data-bs-target: indica el elmento al que afectara el boton
    */

$form = ActiveForm::begin([
    'id' => 'form-crear-lista',
    'action' => ['sitio/crear-lista']
]);
//Crea un input completo con label en base a la información del modelo
echo $form->field($lista, 'titulo');
//Boton submit para poder envíar el formulario
echo Html::submitButton('Listo', ['class' => 'btn btn-primary']);
ActiveForm::end();
?>


<?php
$js = <<<JS
    //FUNCIÓN PARA CREAR UNA LISTA
    $('#form-crear-lista').on('beforeSubmit', function () {
        var form = $(this);

        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            success: function (response) {
                //Se maneja la respuesta del servidor
                if(response.exito){
                    $('#modal-lista').modal('hide');
                    //Se recarga el contendor Pjax de las listas
                    $.pjax.reload({
                        container: '#contenedor-listas-pjax',
                        //async: false // Opcional: asegura que no interfiera con otros procesos
                    });
                    // 2. Construimos el HTML exacto que necesita tu menú lateral
                    var nuevoItemHtml = 
                        '<li class="list-group-item">' +
                            '<input class="form-check-input me-1" type="checkbox" id="' + response.nueva_lista_id + '">' +
                            '<label class="form-check-label" for="' + response.nueva_lista_id + '">' + response.nueva_lista_titulo + '</label>' +
                        '</li>';
                     // 3. Pegamos el nuevo elemento al final de la lista del <aside>
                    $('#menu-lateral-listas').append(nuevoItemHtml);
                } else {
                    alert('Error: ' + JSON.stringify(response.errores));
                }
            }
        });
        /*
            El return false en peticiones AJAX evita el comportamiento por defecto
            Cuando se usa el evento `beforeSubmit` de Yii2 o un `submit` normal de jQuery, 
            el navegador entiende que el formulario debe enviarse "a la antigüita" (recargando la página). 
            Al poner `return false`, le dices: *"¡Alto! Yo ya envié los datos por AJAX, no hagas nada más"*. 
            Si no se pone, entonces AJAX se ejecuta, pero milisegundos después la página se refresca.
         */
        return false; // <--- ESTO ES VITAL para evitar la recarga
    });

    //Se ejecuta cuando el modal se oculta
    $('#modal-lista').on('hidden.bs.modal', function () {
            var \$form = $('#form-crear-lista');
            // 1. Limpia los valores de los inputs
            \$form[0].reset(); 
            // 2. Limpia los mensajes y clases de validación de Yii2 ActiveForm
            \$form.yiiActiveForm('resetForm'); 
        });
    JS;
$this->registerJs($js);
?>
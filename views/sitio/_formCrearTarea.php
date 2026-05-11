<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$form = ActiveForm::begin([
    'id' => 'form-crear-tarea',
    'action' => ['sitio/crear-tarea'],
]);

echo $form->field($tarea, 'titulo');

echo $form->field($tarea, 'fecha_limite')->input('date');

echo $form->field($tarea, 'descripcion')->textarea(['rows' => 3]);

echo $form->field($tarea, 'lista_id')->dropDownList(
    $listas,
    ['prompt' => 'Selecciona lista']
);

echo Html::submitButton('Guardar', ['class' => 'btn btn-primary']);

ActiveForm::end();
?>

<?php
$js = <<<JS
    $('#form-crear-tarea').on('beforeSubmit', function () {
        var form = $(this);

        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            success: function (response) {
                if(response.exito){
                    $('#modal-tarea').modal('hide');
                    //Se recarga el contendor Pjax de las listas
                    $.pjax.reload({
                        container: '#contenedor-listas-pjax',
                        //async: false // Opcional: asegura que no interfiera con otros procesos
                    });
                } else {
                    alert('Error: ' + JSON.stringify(response.errores));
                }
            } 
        });
    return false;
});

JS;
$this->registerJs($js);
?>
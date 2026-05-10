<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;


    /*
    El widget Form indica que que generara el componente
    Form de yii2 pudiendo usar los diseños de bootstrap
    id: Identificador del formulario
    action: A adonde o a que url se envían los datos por defecto yii2 utiliza en la que esta actualmente el
    formulario en caso de no tenerla definida
    method: Define la forma de envío los datos por defecto se utilza POST
    options: Define los atributos del formulario 
    data-bs-target: indica el elmento al que afectara el boton
    */

$form = ActiveForm::begin(['id' => 'form-crear-lista', 'action' => ['sitio/crear-lista']]);
//Crea un input completo con label en base a la información del modelo
echo $form->field($lista, 'titulo');
//Boton submit para poder envíar el formulario
echo Html::submitButton('Listo', ['class' => 'btn btn-primary']);
ActiveForm::end();
?>


<?php
$js = <<<JS
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
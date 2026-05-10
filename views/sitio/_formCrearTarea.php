<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$form = ActiveForm::begin([
    'id' => 'form-tarea',
    'action' => ['sitio/crear-tarea'],
]);

echo $form->field($tarea, 'titulo');

echo $form->field($tarea, 'fecha_limite')->input('date');

echo $form->field($tarea, 'descripcion')->textarea(['rows' => 3]);

echo $form->field($tarea, 'lista_id')->dropDownList(
    $listas,
    ['prompt'=>'Selecciona lista']
);


echo Html::submitButton('Guardar', ['class' => 'btn btn-primary']);




ActiveForm::end();
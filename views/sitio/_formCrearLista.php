<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$form = ActiveForm::begin(['id' => 'form-crear-lista']);

echo $form->field($lista, 'titulo');
echo Html::submitButton('Listo', ['class' => 'btn btn-primary']);

ActiveForm::end();
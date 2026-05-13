<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>


<h1>Crear una cuenta nueva</h1>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($usuario, 'nombre')->textInput(['autofocus' => true]); ?>
<?= $form->field($usuario, 'email')->textInput(); ?>

<?= $form->field($usuario, 'password')->passwordInput(); ?>

<div class="form-group">
    <?= Html::submitButton('Registrarse', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
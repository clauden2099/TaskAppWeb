<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<h1>Iniciar Sesión</h1>

<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
    
    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'rememberMe')->checkbox([
        'template' => "<div class=\"custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Entrar', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
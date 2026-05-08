<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
?>


<!-- Pjax sirve para indicar que solo se actualice una parte específica de la página -->
<?php Pjax::begin(['id' => 'lista-lista-pjax']) ?>

<div class="nuva-lista-form">
    <?php $form = ActiveForm::begin(
        //Con la propiedad 'options' podemos agregar atributos al formulario,
        //como 'data-pjax' => true para que el formulario se envíe a través de Pjax.
        /*Osea que se envía atravez de ajax siendo así que solo se actualiza la información
        que esta adentro del contendor Pjax */
        // Esta línea es la llave secreta: le dice a Yii que intercepte 
        // el submit del formulario y lo envíe por AJAX en lugar de recargar.
        ['options' => ['data-pjax' => true]]
    ); ?>

    <?= $form->field($lista, 'titulo'); ?>
    <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary']); ?>
    
    <?php ActiveForm::end(); ?>
</div>


<div class="lista-resultados mt-4">
    <?php
    foreach ($listas as $li) {
        // Agregamos un div o un salto de línea para separar cada título
        echo "<div>- " . Html::encode($li->titulo) . "</div>";
    }
    ?>
</div>

<?php Pjax::end(); ?>
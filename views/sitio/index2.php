<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;

?>
<h1>Tareas</h1>

<div class="row">
    <aside class="col">
        <button>Crear Tarea</button>

        <p>Todas las tareas</p>
        <p>Destacadas</p>

        <button class="btn btn-primary"
            data-bs-toggle="collapse"
            data-bs-target="#collapseExample">
            Listas
        </button>

        <div class="collapse" id="collapseExample">
            <ul class="list-group">
                <?php foreach ($todas_las_listas as $l): ?>
                    <li class="list-group-item item-filtro">
                        <input class="form-check-input me-1" type="checkbox" id="<?= $l->id; ?>" checked>
                        <label class="form-check-label stretched-link" for="<?= $l->id ?>"><?= $l->titulo; ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?= Html::button(
            'Crear lista',
            [
                'class' => 'btn btn-success',
                //Indica que este boton va abrir un modal
                'data-bs-toggle' => 'modal', // Usa 'data-toggle' si es Bootstrap 3/4
                //Indica el id del modal que se va abrir al hacer click en este boton
                'data-bs-target' => '#mi-modal-estatico' // Usa 'data-target' si es Bootstrap 3/4
            ]
        ) ?>
    </aside>

    <?php Pjax::begin(['id' => 'contenedor-listas-pjax', 'options' => ['class' => 'col']]); ?>
    <section class="row">
        <?php foreach ($listas as $lista): ?>
            <article class="col card">
                <div class="card-body">
                    <h2 class="card-title"><?= $lista->titulo; ?></h2>
                </div>
            </article>
        <?php endforeach; ?>

    </section>
    <?php Pjax::end(); ?>
</div>

<?php Modal::begin([
    //Titulo del modal, puede ser texto plano o HTML
    'title' => '<h4>Titulo de mi Modal</h4>',
    //Id del modal
    'id' => 'mi-modal-estatico',
    //Tamaño del modal
    'size' => 'modal-sm',
]) ?>

<?php $form = ActiveForm::begin(['id' => 'form-crear-lista']); ?>

<?= $form->field($lista, 'titulo')->textInput(['autocomplete' => 'off']); ?>
<?= Html::submitButton('Listo', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end();  ?>

<?php Modal::end(); ?>


<?php
// 1. Calculamos la URL correcta en PHP ANTES de escribir el JavaScript
$urlFiltro = Url::to(['index']);
//Código JS para menejar el formulario
$script = <<< JS
// 'beforeSubmit' es un evento nativo de Yii2 que se dispara justo antes de enviar el formulario
$('#form-crear-lista').on('beforeSubmit', function (e) {
    var form = $(this);

    $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: form.serialize(),
        success: function (respuesta) {
        // Si el servidor (Yii2) nos responde que todo salió bien...
        if (respuesta.exito) {
            $('#mi-modal-estatico').modal('hide');
            // B. LA MAGIA: Le ordenamos a Pjax que actualice la lista de abajo
            // Darle un poco más de tiempo por si el servidor tarda
            $.pjax.reload({ container: '#contenedor-listas-pjax', timeout: 2000 });
        } else {
            alert('Error: ' + JSON.stringify(respuesta.errores));
        }
        },
    });
    // Retornamos false para evitar que la página haga la recarga tradicional
    return false;
    });

    // 2. Limpieza TOTAL al cerrar el modal
    // Este evento nativo de Bootstrap se dispara siempre que el modal se oculta por completo
    $('#mi-modal-estatico').on('hidden.bs.modal', function () {
    var form = $('#form-crear-lista');
    // A. Forzamos el vaciado del input de texto
    form.find('input[type="text"]').val('');
    // B. Quitamos los bordes rojos (error) o verdes (éxito) de Bootstrap
    form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
    // C. Borramos los textos de error ("El título no puede estar vacío", etc.)
    form.find('.invalid-feedback').empty();
    });


    // --- 2. LÓGICA DE FILTROS (NUEVA Y MEJORADA) ---

    // A. EL ANTÍDOTO CONTRA EL PÁNICO DE PJAX
    // Si Pjax detecta un error o cancela una petición, evitamos que recargue la página completa
    $(document).on('pjax:error', function (event) {
        event.preventDefault();
    });

    // B. EL CRONÓMETRO (DEBOUNCE)
    var temporizadorFiltro;

    // Detectar cuando cualquier checkbox de filtro cambia su estado
    $('.form-check-input').on('change', function () {
        // Si el usuario hace clic otra vez, cancelamos el envío anterior
        clearTimeout(temporizadorFiltro);

        // Esperamos 300 milisegundos (0.3 seg) después del último clic antes de enviar al servidor
        temporizadorFiltro = setTimeout(function() {
            
            var idsSeleccionados = [];

            $('.form-check-input:checked').each(function () {
                idsSeleccionados.push($(this).attr('id'));
            });

            // Ahora sí, ejecutamos Pjax de forma segura
            $.pjax.reload({
                container: '#contenedor-listas-pjax',
                url: '{$urlFiltro}',
                type: 'GET',
                data: { 
                    filtradas: idsSeleccionados, 
                    filtrando: 1 // Nuestra bandera mágica para cuando el arreglo esté vacío
                },
                push: false, 
                replace: false,
                timeout: 10000 // 10 segundos de paciencia para que no aborte por lentitud
            });

        }, 300); // <- Aquí están los 300ms de espera
    });
JS;
// Registramos el script en la vista
$this->registerJs($script);
?>
<?php

use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;

?>

<div class="row">

    <aside class="col">
        <?= Html::button('Crear Tarea', [
            'class' => 'btn btn-outline-primary',
            'id' => 'btn-crear-tarea'
        ]) ?>
        <?php Modal::begin(
            [
                'id' => 'modal-tarea',
                'title' => '<h4>Crear Tarea</h4>',
                'size' => Modal::SIZE_LARGE
            ]
        ); ?>
        <!--Contenido del modal todo va en medio del begin() y el end()-->
        <div id="modal-content">
            <!-- Aquí se inyecta el formulario -->
        </div>


        <?php Modal::end(); ?>
        <!--
            Se define el contendor principal del grupo de listas
            list-group: Contendor principal
            list-group-item: Cada item de la lista
            list-group-item-action: Agrega hover y active visual al hacer clic
            active: marca el elemento como activo
            list-group-flush: quita bordes extra y solo deja el de abajo
            bueno para integrarse en distintos elementos como las cards
        -->
        <div class="list-group">
            <button class="list-group-item list-group-item-action active">
                Todas las tareas
            </button>
            <button class="list-group-item list-group-item-action">
                Destacadas
            </button>
        </div>
        <!--
            Se define el disaprador que activa o desactiva el collapse
            data-bs-toggle: Activa el comportamiento
            data-bs-target: A que elemento controla
        -->
        <button class="btn btn-primary"
            data-bs-toggle="collapse"
            data-bs-target="#collapseExample">
            Listas
        </button>

        <div class="collapse" id="collapseExample">
            <ul class="list-group">
                <?php foreach ($listas as $itemLista): ?>
                    <li class="list-group-item">
                        <!--El estilo del radio en la lista
                        form-check-input: estilo del radio
                        me-1: margin de 1rem en el end del input
                        -->
                        <input class="form-check-input me-1" type="checkbox" id="<?= $itemLista->id ?>">
                        <label class="form-check-label" for="<?= $itemLista->id ?>"><?= $itemLista->titulo ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
            <!--Forma 2
                usando el label directamente como el item de la lista
                <div class="list-group">
                    <label class="list-group-item d-flex gap-2">
                        <input class="form-check-input" type="checkbox">
                        <span>Android</span>
                    </label>

                    <label class="list-group-item d-flex gap-2">
                        <input class="form-check-input" type="checkbox">
                        <span>Mis tareas</span>
                    </label>
                </div>
            -->
        </div>
        <!--
            El widget Button indica que que generara el componente
            button de boostrap
            '': Es el texto del boton
            class: son los estilos del boton
            data-bs-togle: indica lo que va a hacer
            data-bs-target: indica el elmento al que afectara el boton
        -->
        <?= Html::button('Crear Lista', [
            'class' => 'btn btn-outline-primary',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#modal-lista',
        ]) ?>
        <!--
            El widget Modal indica que que generara el componente
            modal de boostrap
            id: sirve para indentificar el modal para poder abrirlo
            title: Define el encabezado del modal
            size: tamaño del modal
        -->
        <?php Modal::begin(
            [
                'id' => 'modal-lista',
                'title' => '<h4>Crear lista</h4>',
                'size' => Modal::SIZE_SMALL
            ]
        ); ?>

        <!--Contenido del modal todo va en medio del begin() y el end()-->
        <?= $this->render('_formCrearLista', ['lista' => $lista]); ?>


        <?php Modal::end(); ?>
    </aside>

    <div class="col">
        <div class="row">
            <!--El estilo de la card
                card: contenedor o estrucutra principal
                card-body: contenido principal
                card-title: estilo de titulo
                -->
            <?php foreach ($listas as $cardLista): ?>
                <div class="col card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $cardLista->titulo ?></h5>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($tareas as $cardTarea): ?>
                                <?php if ($cardLista->id == $cardTarea->lista_id): ?>
                                    <!-- Grupo de radios propiedad
                                    name: agrupa los radio en un grupo para solo poder selecionar uno
                                    -->
                                    <li class="list-group-item">
                                        <input class="form-check-input me-1" type="radio" id="<?= $cardTarea->id; ?>" name="listaTareas<?= $cardLista->id; ?>">
                                        <label class="form-check-label" for="<?= $cardTarea->id; ?>"><?= $cardTarea->titulo; ?></label>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<?php
$js = <<<JS
    //Se ejecuta cuando se hace click en el boton
    $('#btn-crear-tarea').on('click', function () {
        $.ajax({
            type: "GET",
            /*En este caso el fomulario esta siendo creado desde esta URL
            o action asi que si no se define action en el fomulario cuando
            se envíe sera a esta URL, siendo así que formulario depende
            desde que acction o URL fue generado y no desde donde se este 
            viendo */
            url: "index.php?r=sitio/crear-tarea",
            success: function (response) {
                //Inyecta el formulario recibido
                $('#modal-content').html(response);
                //Abre el modal
                $('#modal-tarea').modal('show');
            }
        });
    });
    JS;

$this->registerJs($js);
?>
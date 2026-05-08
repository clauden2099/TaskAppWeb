<div class="row">
    <aside class="col">
        <button class="btn btn-outline-primary">Crear Tarea</button>

        <!--
            Se define el contendor principal del grupo de listas
            list-group: Contendor principal
            list-group-item: Cada item de la lista
            list-group-item-action: Agrega hover y active visual al hacer clic
            active: marca el elemento como activo
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
                <li class="list-group-item">
                    <!--El estilo del radio en la lista
                form-check-input: estilo del radio
                me-1: margin de 1rem en el end del input
                -->
                    <input class="form-check-input me-1" type="checkbox" id="tarea1">
                    <label class="form-check-label" for="tarea1">Mis tareas</label>
                </li>
                <li class="list-group-item">
                    <input class="form-check-input me-1" type="checkbox" id="tarea2">
                    <label class="form-check-label" for="tarea2">Android</label>
                </li>
                <li class="list-group-item">
                    <input class="form-check-input me-1" type="checkbox" id="tarea3">
                    <label class="form-check-label" for="tarea3">Prueba</label>
                </li>
            </ul>
        </div>


        <button class="btn btn-outline-primary">Crear Lista</button>

    </aside>

    <div class="col">
        <div class="row">
            <div class="col">
                <h2>Mis tareas</h2>
                <button>Agregar un tarea</button>
                <p>Cita con dermatologo</p>
                <p>d</p>
                <p>cd</p>
            </div>

            <div class="col">
                <h2>Mis tareas</h2>
                <button>Agregar un tarea</button>
                <p>Cita con dermatologo</p>
                <p>d</p>
                <p>cd</p>
            </div>

            <div class="col">
                <h2>Mis tareas</h2>
                <button>Agregar un tarea</button>
                <p>Cita con dermatologo</p>
                <p>d</p>
                <p>cd</p>
            </div>

        </div>



    </div>
</div>
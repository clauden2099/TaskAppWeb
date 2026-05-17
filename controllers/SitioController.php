<?php

namespace app\controllers;

use app\models\Lista;
use app\models\Tarea;
use ReflectionClass;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\Response;

class SitioController extends Controller
{
    /**
     * Comportamientos del controlador.
     * Aquí se configuran los filtros como AccessControl.
     */
    public function behaviors()
    {
        return [
            'access' => [
                /*Yii2 usa un filtro llamado AccessControl. Este filtro se ejecuta 
                antes de que cualquier acción (index, create, etc.) empiece a trabajar. 
                Si no cumples las reglas, te rebota. */
                'class' => AccessControl::class,
                'rules' => [
                    [
                        // Regla 1: ¿A qué acciones aplica?
                        'actions' => ['index', 'create', 'indice'],
                        // ¿Se permite el acceso? Sí (true)
                        'allow' => true,
                        // ¿A quién? '@' significa usuarios AUTENTICADOS (logueados)
                        // '?' significaría invitados (sin cuenta)
                        'roles' => ['@'],
                    ],
                    // Si intentas acceder a cualquier otra acción y no cumples la regla de arriba, 
                    // Yii2 automáticamente te bloqueará y te enviará al loginUrl.
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        // 1. Modelos básicos para los formularios de los modales
        $lista = new Lista;
        $tareas = Tarea::find()->all();

        // 2. EL MENÚ LATERAL: Esta variable siempre trae TODO. 
        // Nunca se filtra, así garantizamos que los checkboxes siempre estén visibles.
        // En lugar de buscar todos (all), filtramos con un WHERE
        // Buscamos donde la columna usuario_id sea igual al ID del usuario logueado
        $todasLasListas = Lista::find()->where(['usuario_id' => Yii::$app->user->id])->all();

        // 3. EL CONTENIDO CENTRAL (Lógica de Filtro para Pjax)
        $queryListas = Lista::find(); // Iniciamos la consulta sin ejecutarla aún

        // Leemos lo que JavaScript nos manda por la URL (GET)
        $filtros = Yii::$app->request->get('filtradas');
        $estaFiltrando = Yii::$app->request->get('filtrando'); // Nuestra bandera mágica

        // Solo aplicamos filtros si la petición viene de Pjax y si la bandera existe
        if (Yii::$app->request->isPjax && $estaFiltrando !== null) {

            if (empty($filtros)) {
                // CASO A: El usuario desmarcó todas las casillas.
                // Truco matemático: Forzamos a que la base de datos devuelva cero resultados.
                $queryListas->where('0=1');
            } else {
                // CASO B: El usuario tiene casillas marcadas.
                // Filtramos buscando solo las listas cuyos IDs coincidan con el arreglo.
                $queryListas->andWhere(['in', 'id', $filtros]);
            }
        }

        // Ejecutamos la consulta ya filtrada
        // Buscamos donde la columna usuario_id sea igual al ID del usuario logueado
        $listasPjax = $queryListas->andWhere(['usuario_id' => Yii::$app->user->id])
            ->all();

        // 4. Renderizamos la vista inyectando las variables separadas
        return $this->render('index', [
            'lista' => $lista,
            'todasLasListas' => $todasLasListas, // Para pintar los checkboxes
            'listas' => $listasPjax,             // Para pintar las tarjetas del centro
            'tareas' => $tareas
        ]);
    }

    public function actionIndice()
    {
        $session = Yii::$app->session;
        if (!$session->isActive) {
            $session->open();
        }
        echo sys_get_temp_dir();
        echo "\n";

        echo Yii::$app->session->getSavePath();
        echo "\n";

        // ID de sesión (hash)
        $sessionId = $session->getId(); // o $session->id

        // Nombre de la cookie de sesión (por defecto PHPSESSID)
        $sessionName = $session->name; // o session_name()

        // Valor de la cookie enviada por el navegador
        $cookieValue = isset($_COOKIE[$sessionName]) ? $_COOKIE[$sessionName] : null;

        VarDumper::dump([
            'sessionId_from_component' => $sessionId,
            'sessionName' => $sessionName,
            'cookie_value' => $cookieValue,
            'session_data' => iterator_to_array($session),
            'hasSessionId' => $session->hasSessionId, // true si la petición trajo ID
        ], 10, true);


        /*
            $userComponent = Yii::$app->user;

            // Asegúrate de abrir la sesión si necesitas datos dependientes de sesión
            $session = Yii::$app->session;
            if (!$session->isActive) {
                $session->open();
            }

            // Información básica del componente user
            $info = [
                'class' => get_class($userComponent),
                'isGuest' => $userComponent->isGuest,
                'id_via_component' => $userComponent->id, // shortcut a getId()
                'hasSessionId' => $session->hasSessionId ?? null,
            ];

            // Identity (puede ser null)
            $identity = $userComponent->identity;
            if ($identity === null) {
                $info['identity'] = null;
            } else {
                // Si tu identity es ActiveRecord (User model), usa getAttributes o toArray
                if (method_exists($identity, 'getAttributes')) {
                    $identityData = $identity->getAttributes();
                } elseif (method_exists($identity, 'toArray')) {
                    $identityData = $identity->toArray();
                } else {
                    // fallback: volcar propiedades públicas
                    $identityData = (array)$identity;
                }

                $info['identity'] = [
                    'class' => get_class($identity),
                    'id' => $identity->getId(),
                    'authKey' => method_exists($identity, 'getAuthKey') ? $identity->getAuthKey() : null,
                    'attributes' => $identityData,
                ];
            }

            \yii\helpers\VarDumper::dump($info, 10, true);

            var_dump(Yii::$app->user->identity->nombre);

            echo "\n";
        */
        echo Yii::$app->user->identity->nombre;
        echo "\n";
        echo "<pre>";
        $identity = Yii::$app->user->identity;
        if ($identity) {
            echo $identity->nombre;
            echo $identity->email;
            // o volcar atributos
            echo "<pre>";
            print_r($identity->attributes);
            echo "</pre>";
        }
        echo "</pre>";
        return "ok";
    }

    public function actionCrearLista()
    {
        $lista = new Lista();
        //Se la petición fue post mediante ajax
        /*La propiedad `Yii::$app->request->isAjax` sirve para que tu controlador sea "inteligente". 
        * Si es AJAX: Devuelves solo un pedazo de HTML (`renderAjax`) o un JSON.
        * Si no es AJAX: Devuelves la página completa (`render`). */
        if (Yii::$app->request->isAjax && $lista->load(Yii::$app->request->post())) {
            //Se obtiene el usuario con la sesion usando la variable global user de yii
            $lista->usuario_id = Yii::$app->user->id;
            //Indica que la respuesta se dara en Formato JSON
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($lista->validate()) {
                $lista->save(false);
                return [
                    'exito' => true,
                    'mensaje' => 'Lista guardada correctamente',
                    // ¡Agregamos esto! Mandamos la información de la nueva lista
                    'nueva_lista_id' => $lista->id,
                    'nueva_lista_titulo' => $lista->titulo
                ];
            } else {
                return [
                    'exito' => false,
                    'errores' => $lista->getErrors()
                ];
            }
        }
        // Si entran por URL directa (no ajax), redirigir o mostrar error
        return $this->redirect(['index']);
    }

    public function actionCrearTarea()
    {
        $tarea = new Tarea();
        $listas = Lista::find()->all();
        /* Se obtiene el id y el titulo de cada objeto en la lista de manera manual
            $listaMapa = []; 
            foreach($listas as $lista){
                echo " | ".$lista. "\n";
                $listaMapa[$lista->id] = $lista->titulo;
            }
            echo "------------------------------";
            foreach ($listaMapa as $key => $value) {
                echo " | $key: $value \n";
            }
        */
        /*Se obtiene la id y el titulo de cada objeto de manera más utomatica
        usando la clase arrayHelper */
        //Saca el key y el value de cada objeto de la lista ya que puede
        //mapear atributos anidados como en este caso sería algo como lista.titulo
        //para trae el value
        $listaMapa = ArrayHelper::map($listas, 'id', 'titulo');

        // 1. PRIMERO comprobamos si es AJAX Y si vienen datos POST (Guardar)
        if (Yii::$app->request->isAjax && $tarea->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($tarea->validate()) {
                $tarea->save(false);
                return [
                    'exito' => true,
                    'mensaje' => 'Tarea guardada correctamente',
                ];
            } else {
                return [
                    'exito' => false,
                    'errores' => $tarea->getErrors()
                ];
            }
        }
        // 2. DESPUÉS comprobamos si solo es AJAX para pedir el formulario (Abrir modal)
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_formCrearTarea', ['tarea' => $tarea, 'listas' => $listaMapa]);
        }

        // Si entran por URL directa (no ajax), redirigir o mostrar error
        return $this->redirect(['index']);
    }

    /*public function actionIndex()
    {
        $lista = new Lista();
        //Si se envía el formulario 
        //Se carga la información en el modelo
        if (Yii::$app->request->isAjax && $lista->load(Yii::$app->request->post())) {
            $lista->usuario_id = 1;
            //Se ejecutan las validaciones (rules)
            if ($lista->validate()) {
                // Configuramos la respuesta para devolver JSON en lugar de HTML
                Yii::$app->response->format = Response::FORMAT_JSON;
                //Se guarda la información en la BD
                $lista->save(false);
                // Esta es la respuesta que recibe nuestro "success: function(respuesta)" en JS
                return [
                    'exito' => true,
                    'mensaje' => 'Lista guardada correctamente'
                ];
            } else {
                // Si hay errores de validación, los enviamos para debug
                return [
                    'exito' => false,
                    'errores' => $lista->getErrors()
                ];
            }
        }
        // 2. LÓGICA DE FILTRADO
        // Empezamos la consulta
        // 2. LÓGICA DE FILTRADO
        $query = Lista::find();

        // Leemos los IDs y la nueva bandera
        $filtros = Yii::$app->request->get('filtradas');
        $estaFiltrando = Yii::$app->request->get('filtrando');

        // Verificamos si es Pjax Y si la bandera de filtro existe
        if (Yii::$app->request->isPjax && $estaFiltrando !== null) {

            if (empty($filtros)) {
                // Como 'estaFiltrando' existe pero '$filtros' está vacío, 
                // sabemos con 100% de seguridad que el usuario desmarcó TODAS.
                $query->where('0=1');
            } else {
                // Hay listas marcadas, las filtramos normal
                $query->andWhere(['in', 'id', $filtros]);
            }
        }
        // Si NO es Pjax (es decir, la primera vez que carga la página), 
        // se ignora el bloque anterior y trae todas por defecto.

        // 3. Obtener los resultados
        // Usamos 'all()' al final para ejecutar la consulta ya filtrada
        $listas_para_mostrar = $query->all();

        return $this->render('index', [
            'lista' => $lista,
            'listas' => $listas_para_mostrar, // Estas son las que se verán en el Pjax
            'todas_las_listas' => Lista::find()->all(), // Estas son para pintar los checkboxes del sidebar
        ]);
    }*/
}

<?php

namespace app\controllers;

use app\models\Lista;
use app\models\Tarea;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class SitioController extends Controller
{

    public function actionIndex()
    {
        /*Lista nueva se utiliza para los formularios */
        $lista = new Lista;
        /*Todas las listas se utilizan para mostrarlas de manera visual */
        $listas = Lista::find()->all();
        $tareas = Tarea::find()->all();
        return $this->render('index', ['lista' => $lista, 'listas' => $listas, 'tareas' => $tareas]);
    }

    public function actionCrearLista()
    {
        $lista = new Lista();
        //Se la petición fue post mediante ajax
        /*La propiedad `Yii::$app->request->isAjax` sirve para que tu controlador sea "inteligente". 
        * Si es AJAX: Devuelves solo un pedazo de HTML (`renderAjax`) o un JSON.
        * Si no es AJAX: Devuelves la página completa (`render`). */
        if (Yii::$app->request->isAjax && $lista->load(Yii::$app->request->post())) {
            $lista->usuario_id = 1;
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

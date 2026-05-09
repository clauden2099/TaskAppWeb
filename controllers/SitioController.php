<?php

namespace app\controllers;

use app\models\Lista;
use Yii;
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
        return $this->render('index', ['lista' => $lista, 'listas' => $listas]);
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

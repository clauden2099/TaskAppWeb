<?php

namespace app\controllers;

use app\models\Lista;
use app\models\LoginForm;
use app\models\SingupForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    // Apagamos la validación CSRF porque las APIs no usan formularios HTML
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Añadimos el autenticador de tokens
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            // Podemos excluir acciones que no necesitan seguridad (como login o registro)
            'except' => ['login-api', 'registro-api', 'listas'],
        ];

        return $behaviors;
    }

    /**
     * Endpoint para obtener la lista de proyectos en formato JSON
     */
    public function actionListas()
    {
        // 1. EL CAMBIO CLAVE: Le decimos a Yii2 que suspenda el HTML
        // y formatee cualquier respuesta (return) de este método como JSON.
        Yii::$app->response->format = Response::FORMAT_JSON;
        // 2. Hacemos la consulta a la base de datos (igual que siempre)
        // En lugar de find()->all(), filtramos por el usuario autenticado por el token
        $query = Lista::find()
            //->where(['usuario_id' => Yii::$app->user->id]); // <--- AQUÍ ESTÁ LA MAGIA
            ->where(['usuario_id' => 2]); // <--- AQUÍ ESTÁ LA MAGIA


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
        // 3. En lugar de hacer un $this->render('vista'), simplemente
        // devolvemos el arreglo de objetos. Yii2 se encarga de traducirlo a JSON.
        // 3. Retornamos el dataProvider. 
        // Yii2 es tan inteligente que si retornas esto en un entorno REST,
        // calculará la paginación, inyectará los Headers y enviará el JSON filtrado.
        return $dataProvider;
    }

    /**
     * Endpoint para crear un proyecto recibiendo JSON
     */
    public function actionCrearLista()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Lista();

        // En las APIs, los datos no vienen en $_POST tradicional.
        // Yii2 tiene un método especial para leer el cuerpo de una petición JSON cruda:
        $datosJson = Yii::$app->request->getBodyParams();


        // Cargamos los datos al modelo (pasando un string vacío '' como segundo parámetro 
        // porque en APIs no usamos el prefijo 'Proyecto[titulo]' de los formularios HTML)
        if ($model->load($datosJson, '')) {

            // Asignamos un usuario estático temporal para la prueba
            // Reemplazamos el ID estático por el ID del usuario validado por el token
            $model->usuario_id = Yii::$app->user->id; // <--- AQUÍ ESTÁ LA MAGIA

            if ($model->save()) {
                // El estándar REST dicta que si creaste algo con éxito, debes devolver 
                // el objeto recién creado y un código HTTP 201 (Created)
                Yii::$app->response->statusCode = 201;
                return $model;
            } else {
                // Si falla la validación (ej. faltó el título), devolvemos los errores
                // y un código HTTP 422 (Unprocessable Entity)
                Yii::$app->response->statusCode = 422;
                return $model->errors;
            }
        }

        // Si no se enviaron datos correctos
        Yii::$app->response->statusCode = 400; // Bad Request
        return ['error' => 'No se enviaron datos válidos'];
    }

    public function actionLoginApi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->getBodyParams(), '') && $model->validate()) {
            $user = $model->getUser();

            // Generamos un token nuevo si no tiene uno
            if (!$user->access_token) {
                $user->access_token = Yii::$app->security->generateRandomString(64);
                $user->save(false);
            }

            // Devolvemos el token a la app móvil
            return [
                'success' => true,
                'access_token' => $user->access_token,
                'nombre' => $user->nombre
            ];
        }

        Yii::$app->response->statusCode = 422;
        return $model->errors;
    }

    public function actionRegistroApi()
    {
        // 1. Establecemos el formato de respuesta a JSON
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 2. Usamos el modelo de formulario que ya tenías
        $model = new SingupForm();

        // 3. Cargamos los datos del cuerpo de la petición (JSON)
        // Usamos '' como segundo parámetro porque el JSON de la API no suele venir 
        // envuelto en un prefijo como "SignupForm[nombre]"
        if ($model->load(Yii::$app->request->getBodyParams(), '')) {

            $user = $model->signup();

            if ($user) {
                // OPCIONAL: Generar el token de una vez para que la App Móvil 
                // ya esté logueada tras el registro.
                $user->access_token = Yii::$app->security->generateRandomString(64);
                $user->save(false);

                // Devolvemos el estatus 201 (Creado) y los datos del nuevo usuario
                Yii::$app->response->statusCode = 201;
                return [
                    'success' => true,
                    'message' => 'Usuario registrado con éxito',
                    'user' => [
                        'id' => $user->id,
                        'nombre' => $user->nombre,
                        'email' => $user->email,
                        'access_token' => $user->access_token,
                    ]
                ];
            } else {
                // Si el método signup() devolvió null (falló la validación o guardado)
                Yii::$app->response->statusCode = 422;
                return [
                    'success' => false,
                    'errors' => $model->errors,
                ];
            }
        }

        Yii::$app->response->statusCode = 400;
        return ['error' => 'Datos inválidos'];
    }
}

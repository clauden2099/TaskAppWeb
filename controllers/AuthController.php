<?php

namespace app\controllers;

use app\models\SingupForm;
use Yii;
use yii\web\Controller;

class AuthController extends Controller
{

    public function actionRegistro()
    {
        //Se utiliza el modelo que obtiene los datos de la vista (form de registro)
        $model = new SingupForm();

        if ($model->load(Yii::$app->request->post())) {
            //Se ejecuta la función de registro del modelo form
            $user = $model->signup();

            if ($user) {
                // Si se guardó en BD correctamente, iniciamos sesión automáticamente
                // Yii::$app->user es el componente global de seguridad
                Yii::$app->user->login($user);

                // Lo mandamos a la lista de proyectos
                return $this->redirect(['sitio/index']);
            }
        }

        return $this->render('registo', ['usuario' => $model]);
    }
}

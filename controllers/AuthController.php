<?php

namespace app\controllers;

use app\models\LoginForm;
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

    public function actionLogin()
    {
        //Si el usuario ya inició sesión (no es un invitado), lo mandamos al index
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['sitio/index']);
        }

        $model = new LoginForm();

        // Si se envió el formulario y el login es exitoso
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['sitio/index']); // Login exitoso
        }

        // Si la contraseña estaba mal, o simplemente está abriendo la página, mostramos la vista
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        // Cierra la sesión global
        Yii::$app->user->logout();

        // Lo mandamos de regreso a la pantalla de login
        return $this->redirect(['auth/login']);
    }
}

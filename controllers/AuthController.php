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
            // Ejecutamos nuestra función personalizada que encripta la contraseña y guarda en BD
            $user = $model->signup();

            if ($user) {
                // Si se guardó en BD correctamente, iniciamos sesión automáticamente
                // Yii::$app->user es el componente global de seguridad
                /* * AL INICIAR SESIÓN DESDE EL REGISTRO (SIN DURACIÓN):
                 * * Al no pasarle un segundo parámetro de tiempo ($duration), Yii asume que es 0.
                 * Esto significa que Yii SOLO ejecuta el "Plan A":
                 * 1. Crea una variable global en el servidor ($_SESSION de PHP).
                 * 2. Guarda el ID del usuario ($user->getId()) dentro de esa sesión.
                 * * IMPORTANTE: Aquí NO se crea ninguna cookie de "Recuérdame", ni se usa el auth_key.
                 * Si el usuario cierra por completo su navegador web, la sesión de PHP morirá
                 * y tendrá que iniciar sesión manualmente la próxima vez.
                 */
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
        // Con el método isGuest() comprueba si el usuario es un invitado (no ha iniciado sesión)
        // Regresa un bool. Si NO es invitado, lo redirige.
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
        /* * ¿Qué hace el logout() internamente?
         * 1. Borra el ID del usuario de la memoria del servidor ($_SESSION).
         * 2. Busca en el navegador del usuario si existe la cookie persistente '_identity' 
         * (la que contiene el auth_key) y LA DESTRUYE.
         * 3. Te vuelve a convertir en un invitado (isGuest = true).
         */
        // Cierra la sesión global
        Yii::$app->user->logout();

        // Lo mandamos de regreso a la pantalla de login
        return $this->redirect(['auth/login']);
    }
}

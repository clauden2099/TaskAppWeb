<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true; //Casilla para "Recordar sesión"

    // Variable privada para guardar el usuario una vez que lo encontremos en la BD
    private $_user = null;

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['rememberMe', 'boolean'],
            /* Esta es una regla personalizada. Yii buscará una función 
               llamada 'validarPassword' dentro de este mismo modelo para ejecutarla. */
            ['password', 'validarPassword'],
        ];
    }

    /**
     * Esta es la función personalizada que verifica si la contraseña es correcta.
     * Solo se ejecuta si el email y password pasaron las reglas de 'required'.
     */
    public function validarPassword($attribute, $params)
    {
        // Si no hay otros errores de validación previos
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            // Si el usuario no existe, o si la contraseña no coincide con el hash
            // Usamos el método validatePassword() que pusimos en el modelo Usuario
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Correo o contraseña incorrectos.');
            }
        }
    }

    public function login(): bool
    {
        // Si no pasa todas las rules() (incluyendo validarPassword)
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();

        if (!$user) {
            return false;
        }
        // Calculamos el tiempo. Si marcó la casilla, son 30 días en segundos. Si no, es 0.
        $duracion = $this->rememberMe ? 3600 * 24 * 30 : 0;
        /* * AL INICIAR SESIÓN DESDE EL LOGIN (CON POSIBLE DURACIÓN):
         * * 1. Yii SIEMPRE ejecuta el "Plan A": Crea la sesión de PHP en el servidor con el ID del usuario.
         * * 2. ¿Qué pasa con el "Plan B" (La Cookie)?
         * - Si $duracion es 0: Hace lo mismo que en el registro. No hay cookie, solo sesión temporal.
         * - Si $duracion es mayor a 0 (ej. 30 días): Yii crea una cookie en el navegador 
         * llamada '_identity' que no se borrará al cerrar el navegador.
         * * ¿Qué guarda dentro de la cookie '_identity'?
         * 1) El ID del usuario.
         * 2) El 'auth_key' actual que ese usuario tiene en la base de datos.
         * * ¿Para qué servirá esa cookie?
         * Cuando la sesión del servidor (Plan A) muera por inactividad, Yii verá esta cookie.
         * Extraerá el auth_key de la cookie y lo comparará contra el de la base de datos
         * usando el método `validateAuthKey()`. Si coinciden, Yii reconstruirá la sesión 
         * de PHP automáticamente (Plan A) sin pedir contraseña.
         */
        return Yii::$app->user->login($user, $duracion);
    }

        /**
     * Busca al usuario en la BD usando el email.
     * Lo guarda en $_user para no tener que hacer la consulta SQL más de una vez.
     */
    public function getUser(): ? Usuario
    {
        if ($this->_user === null) {
            $this->_user = Usuario::findOne(['email' => $this->email]);
        }
        return $this->_user;
    }
}

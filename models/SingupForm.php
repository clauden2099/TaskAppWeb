<?php

namespace app\models;

use Override;
use Yii;
use yii\base\Model;

/*Este es un modelo sigue la idea de Form Object Pattern que es un modelo
que solo se utiliza en la vista ya que su función es recibir datos que no
necesariamente son igules a los que se van a almacenar en la BD o trbajar con
ellos así que se utilza estos modelos para recibir cierto tipo de datos y en
base a este poder modificarlos si es necesario sin afectar la consistencia
de los modelos activeRecord y la BD*/

class SingupForm extends Model
{
    //Propiedades o información en el formato especifico con las que trabajara el modelo
    public $nombre;
    public $email;
    public $password;

    #[Override]
    public function rules()
    {
        return [
            [['nombre', 'email', 'password'], 'required'],
            ['email', 'email'],
            // Esta regla es magia: verifica en la tabla 'usuario' que el email no exista ya
            ['email', 'unique', 'targetClass' => '\app\models\Usuario', 'message' => 'Este correo ya está registrado.'],
            ['password', 'string', 'min' => 6],
        ];
    }

    //Función de registro
    //Si todo esta válido, crea el Usuario y lo encripta y lo guarda
    public function signup()
    {
        // Validamos usando las rules() de arriba
        if (!$this->validate()) {
            return null; // Si hay error, no hacemos nada
        }

        // Instanciamos el modelo real de tu base de datos
        $user = new Usuario();
        $user->nombre = $this->nombre;
        $user->email = $this->email;

        // --- LA MAGIA DE LA SEGURIDAD ---
        // Generamos el hash indescifrable a partir del texto plano
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        // Generamos una llave aleatoria (requerida por Yii2 para las sesiones)
        $user->auth_key = Yii::$app->security->generateRandomString();

        // Guardamos en BD. Retorna el objeto $user si tuvo éxito, o null si falló.
        return $user->save() ? $user : null;
    }
}

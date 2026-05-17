<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/*Con la interfaz IdentityInterface sirve para implementar un sistema de 
autenticación que yii tien por defecto */

class Usuario extends ActiveRecord implements IdentityInterface
{
    /**
     * Inicializamos el controlador.
     * Aquí le decimos a Yii2 que apague el uso de sesiones de PHP
     * para el componente de usuario, obligándolo a ser 100% RESTful (Stateless).
     */
    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
    }
    
    /**
     * Nombre de la tabla asociada al modelo.
     * Yii2 usa este método para mapear automáticamente el modelo con la tabla 'usuario' en la BD.
     */
    public static function tableName()
    {
        return 'usuario';
    }

    /*El behaviors() es una función de los modelos y esta se ejecuta utomaticamente
    por defecto antes de guardar los elementos en la BD osea antes de usar save()
    esto sirve para modificar atributos o añadir valores a estos, la ejecución
    de un behaviors se puede cambiar para que no  se ejecute despues 
    de guardar en BD si no en diferentes ciclos de vida del modelo */
    public function behaviors()
    {
        /*Aquí se esta indicando que automaticamente rellene los campos 
        create_at y updated_at usando la función time() si es que estos 
        estan definidos en el modelo o BD antes de guardarlos en la BD para
        que cuando se ejecute el método save() estos ya estén listos */
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * Reglas de validación.
     * Estas reglas se aplican al guardar datos provenientes de formularios, vistas o servicios externos.
     * Permiten validar tipos, requerimientos y unicidad antes de ejecutar INSERT o UPDATE.
     */
    public function rules()
    {
        return [
            [['nombre', 'email', 'password_hash', 'auth_key'], 'required'], // Campos obligatorios
            ['email', 'email'], // Valida formato de correo
            ['email', 'unique'], // Evita duplicados en la BD
            [['created_at', 'updated_at'], 'integer'], // Campos numéricos
            [['nombre', 'email', 'password_hash', 'auth_key'], 'string', 'max' => 255], // Longitud máxima
        ];
    }


    // --- MÉTODOS OBLIGATORIOS DE IDENTITY INTERFACE ---
    /*Busca el usuario por su ID cuando se necesite a este una vez que ya 
    inicio sesión -> se usa para mantener la sesión activa */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Buscamos al usuario que tenga ese token exacto en la BD
        return static::findOne(['access_token' => $token]);
    }

    /*Devuelve el Id del usuario para saber quien esta en la sesión */
    public function getId()
    {
        return $this->id;
    }

    /*Devuelve una clave única usara para validar las cookies de la sesión*/
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /* Verifica que la clave de sesión coincida con la del usuario. */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    // Método extra muy útil para el login:
    //Valida la contraseña encriptada con la ingresada por el usuario
    public function validatePassword($password)
    {
        // Revisa si "admin123" coincide con el hash loco "$2y$13$jTXY..."
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}

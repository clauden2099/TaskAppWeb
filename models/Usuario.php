<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Usuario extends ActiveRecord implements IdentityInterface
{
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

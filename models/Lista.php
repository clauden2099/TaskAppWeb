<?php

namespace app\models;

use Override;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Lista extends ActiveRecord
{
    public static function tableName()
    {
        return 'lista';
    }

    #[Override]
    public function behaviors()
    {
        return [
            TimestampBehavior::class
        ];
    }

    #[Override]
    // Definimos la estructura del JSON Plano para las respuestas (GET)
    /*Define los atributos o relaciones que siempre se envíaran de un  modelo 
    cuando se envíe utilizando el enfoque JSON  */
    /*public function fields()
    {
        return [
            'id',
            'title',
            'status',
            'proyecto_id', // Enfoque plano: Solo el ID numérico, nada de objetos anidados
        ];
    }*/

    public function rules()
    {
        return [
            [['titulo'], 'required']
        ];
    }

    public function getTarea()
    {
        //Una lista tiene muchas tareas
        //Se enlaza la fk 'lista_id' de la tarea con el id 'id' de lista
        return $this->hasMany(Tarea::class, ['lista_id' => 'id']);
    }

    public function __toString()
    {
        return (string) "Id: {$this->id}, Usuario_Id: {$this->usuario_id}, Titulo: {$this->titulo}, Orden: {$this->orden}";
    }
}

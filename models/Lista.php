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
        return[
            TimestampBehavior::class
        ];
    }

    public function rules()
    {
        return[
            [['titulo'], 'required']
        ];
    }

    public function getTarea(){
        //Una lista tiene muchas tareas
        //Se enlaza la fk 'lista_id' de la tarea con el id 'id' de lista
        return $this->hasMany(Tarea::class, ['lista_id' => 'id']);
    }

    public function __toString()
    {
        return (string) "Id: {$this->id}, Usuario_Id: {$this->usuario_id}, Titulo: {$this->titulo}, Orden: {$this->orden}";
    }

}

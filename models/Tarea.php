<?php


namespace app\models;

use Override;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Tarea extends ActiveRecord{

    #[Override]
    public static function tableName()
    {
        return 'tarea';
    }

    #[Override]
    public function behaviors()
    {
        return[
            TimestampBehavior::class
        ];
    }

    #[Override]
    public function rules()
    {
        return[
            [['titulo', 'lista_id','fecha_limite'], 'required'],
            [['lista_id','created_at','updated_at'], 'integer'],
            /*Con la regla de validación exist se utiliza por lo general para validar claves
            foraneas en este caso asegura que el list_id realmente exista en la tabla lista */
            ['lista_id', 'exist', 'targetClass' => Lista::class, 'targetAttribute' => 'id'],
            ['descripcion', 'string'],
            ['titulo', 'string', 'max' => 100]
        ];
    }

    public function getLista(){
        //Una tarea pertenece a una lista 
        //Se enlaza el 'id' de la lista con la fk 'lista_id' de tarea
        return $this->hasOne(Lista::class, ['id' => 'lista_id']);
    }
}
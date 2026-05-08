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

}

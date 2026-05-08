<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tarea}}`.
 */
class m260428_033545_create_tarea_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tarea}}', [
            'id' => $this->primaryKey(),
            'lista_id' => $this->integer()->notNull(),
            'titulo' => $this->string()->notNull(),
            'descripcion' => $this->string(),
            'destacada' => $this->boolean(),
            'estado' => $this->smallInteger()->defaultValue(1),
            'fecha_limite' => $this->date(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        
        // Relación con Lista
        $this->addForeignKey(
            'fk-tarea-lista_id',
            '{{%tarea}}',
            'lista_id',
            '{{%lista}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tarea}}');
    }
}

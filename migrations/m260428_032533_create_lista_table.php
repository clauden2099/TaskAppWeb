<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lista}}`.
 */
class m260428_032533_create_lista_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%lista}}', [
            'id' => $this->primaryKey(),
            'usuario_id' => $this->integer()->notNull(),
            'titulo' => $this->string()->notNull(),
            'orden' => $this->integer()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        //Relación con Usuario
        $this->addForeignKey(
            'fk-lista-usuario_id', // nombre de la FK
            '{{%lista}}',  // tabla origen
            'usuario_id', // columna origen
            '{{%usuario}}',  // tabla destino
            'id', // columna destino
            'CASCADE' // ON DELETE: si borras el usuario, se borran sus listas
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%lista}}');
    }
}

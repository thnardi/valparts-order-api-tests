<?php

use Phinx\Migration\AbstractMigration;

class OrdersMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        // TABELAS PARA ENDEREÇOS
        $orders = $this->table('orders');
        $orders->addColumn('cli_id', 'integer', ['null' => true]);
        $orders->addColumn('produtos', 'text', ['null' => true]);
        $orders->addColumn('total', 'string', ['null' => true]);
        $orders->addColumn('pagamento', 'string', ['null' => true]);
        $orders->addColumn('endereco_entrega', 'text', ['null' => true]);
        $orders->addColumn('status', 'string', ['null' => true]);
        $orders->addTimestamps();
        $orders->create();
    }
}

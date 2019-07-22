<?php
declare(strict_types=1);

namespace Farol360\Ancora;

// use Farol360\Ancora\ModelSemaforo;

abstract class Model
{
    protected $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    // retorna true se liberar a transação
    // retorna false se não possível
    public function beginTransaction() {

      $this->db->beginTransaction();

      // if (ModelSemaforo::getInstance()->isFree()) {
      //     $this->db->beginTransaction();
      //
      //     ModelSemaforo::getInstance()->use();
      //
      //     return true;
      // }
      //
      // return false;

    }

    public function inTransaction() {
      return $this->db->inTransaction();
    }


    // retorna true em caso de sucesso do commit
    // retorna false caso n]ao commitou
    public function commit() {

      $this->db->commit();

      // if (ModelSemaforo::getInstance()->isUsed()) {
      //   $this->db->commit();
      //   ModelSemaforo::getInstance()->release();
      //
      //   return true;
      // }
      //
      //
      // return false;

    }

    // retorna true em caso de sucesso do rollback
    // retorna false caso n]ao rollback
    public function rollback() {

      $this->db->rollback();

      // if (ModelSemaforo::getInstance()->isUsed()) {
      //   $this->db->rollback();
      //   ModelSemaforo::getInstance()->release();
      //
      //   return true;
      // }
      // return false;
    }
}

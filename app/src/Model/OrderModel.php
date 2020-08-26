<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\Order;

class OrderModel extends Model
{
  public function get(int $id)
  {
    $sql =
      "SELECT
          *
      FROM
          orders
      WHERE
          id = :id
      LIMIT 1
    ";
    $parameters = [':id' => $id];
    $stmt = $this->db->prepare($sql);
    $exec = $stmt->execute($parameters);
    // verifica se ocorreu com sucesso o execute
    if ($exec) {
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Secretaria::class);
      $data['data'] = $stmt->fetch();
      $data['errorCode'] = null;
      $data['errorInfo'] = null;
    } else {
      $data['data'] = false;
      $data['errorCode'] = $stmt->errorCode();
      $data['errorInfo'] = $stmt->errorInfo();
    }
    // completa demais dados
    $data['status'] = $exec;
    $data['table'] = 'secretarias';
    $data['function'] = 'get';
    $modelReturn = new ModelReturn($data);
    return $modelReturn;
  }

  public function getAll(int $offset = 0, int $limit = PHP_INT_MAX)
  {
    $sql = "SELECT
            *
        FROM
            orders
        LIMIT ? , ?
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
    $exec = $stmt->execute();
    // verifica se ocorreu com sucesso o execute
    if ($exec) {
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Secretaria::class);
      $data['data'] = $stmt->fetchAll();
      $data['errorCode'] = null;
      $data['errorInfo'] = null;
    } else {
      $data['data'] = false;
      $data['errorCode'] = $stmt->errorCode();
      $data['errorInfo'] = $stmt->errorInfo();
    }
    // completa demais dados
    $data['status'] = $exec;
    $data['table'] = 'secretarias';
    $data['function'] = 'getAll';
    $modelReturn = new ModelReturn($data);
    return $modelReturn;
  }

  public function add(Order $order)
    {
      $sql = "INSERT INTO orders (
              cli_id,
              produtos,
              total,
              pagamento,
              endereco_entrega
              )
          VALUES (:cli_id, :produtos, :total, :pagamento, :endereco_entrega)";
      $parameters = [
          ':cli_id'           => $order->cli_id,
          ':produtos'         => $order->produtos,
          ':total'            => $order->total,
          ':pagamento'       => $order->pagamento,
          ':endereco_entrega' => $order->endereco_entrega
      ];
      $stmt = $this->db->prepare($sql);
      $exec = $stmt->execute($parameters);
      // verifica se ocorreu com sucesso o execute
      if ($exec) {
        $data['data'] = $this->db->lastInsertId();
        $data['errorCode'] = null;
        $data['errorInfo'] = null;
      } else {
        $data['data'] = false;
        $data['errorCode'] = $stmt->errorCode();
        $data['errorInfo'] = $stmt->errorInfo();
      }
      // completa demais dados
      $data['status'] = $exec;
      $data['table'] = 'secretarias';
      $data['function'] = 'add';
      $modelReturn = new ModelReturn($data);
      return $modelReturn;
    }

    public function update(Secretaria $secretarias){
      $sql = "
            UPDATE
                secretarias
            SET
                name         = :name,
                description  = :description,
                slug         = :slug

            WHERE
                id = :id
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':id'           => $secretarias->id,
            ':name'         => $secretarias->name,
            ':description'  => $secretarias->description,
            ':slug'  => $secretarias->slug
        ];
        return $query->execute($parameters);
  }

  public function delete(int $id): bool
  {
    $sql = "DELETE FROM orders WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $parameters = [':id' => $id];
    return $stmt->execute($parameters);
  }
}
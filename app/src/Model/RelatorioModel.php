<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\Relatorio;

class RelatorioModel extends Model
{
  public function add(Relatorio $relatorio)
  {
    $sql =
      "INSERT INTO relatorios (
        slug,
        name,
        description
      )
      VALUES (
        :slug,
        :name,
        :description
      )";
    $parameters = [
        ':slug'         => $relatorio->slug,
        ':name'         => $relatorio->name,
        ':description'  => $relatorio->description,
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
    $data['table'] = 'relatorios';
    $data['function'] = 'add';
    $modelReturn = new ModelReturn($data);
    return $modelReturn;
  }

  public function get(int $id)
  {
    $sql =
      "SELECT
          *
      FROM
          relatorios
      WHERE
          id = :id
      LIMIT 1
    ";
    $parameters = [':id' => $id];
    $stmt = $this->db->prepare($sql);
    $exec = $stmt->execute($parameters);
    // verifica se ocorreu com sucesso o execute
    if ($exec) {
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Relatorio::class);
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
    $data['table'] = 'relatorios';
    $data['function'] = 'get';
    $modelReturn = new ModelReturn($data);
    return $modelReturn;
  }

  public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
  {
    $sql = "SELECT
            *
        FROM
            relatorios
        ORDER BY
            date
        LIMIT ? , ?
    ";
    $query->bindValue(1, $offset, \PDO::PARAM_INT);
    $query->bindValue(2, $limit, \PDO::PARAM_INT);
    $stmt = $this->db->prepare($sql);
    $exec = $stmt->execute($parameters);
    // verifica se ocorreu com sucesso o execute
    if ($exec) {
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Relatorio::class);
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
    $data['table'] = 'relatorios';
    $data['function'] = 'getAll';
    $modelReturn = new ModelReturn($data);
    return $modelReturn;
  }

}

<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\Configuracoes;


class ConfiguracoesModel extends Model
{
  public function get(int $id)
  {
      $sql = "
          SELECT
              *
          FROM
              configuracoes
          WHERE
              id = :id
          LIMIT 1
      ";
      $stmt = $this->db->prepare($sql);
      $parameters = [':id' => $id];
      $stmt->execute($parameters);
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Configuracoes::class);
      return $stmt->fetch();
  }
  public function getByConfig($config)
  {
      $sql = "
          SELECT
              *
          FROM
              configuracoes
          WHERE
              name = :name
          LIMIT 1
      ";
      $stmt = $this->db->prepare($sql);
      $parameters = [':name' => $config];
      $stmt->execute($parameters);
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Configuracoes::class);
      return $stmt->fetch();
  }

  public function update(configuracoes $configuracoes): bool
  {
      $sql = "
          UPDATE
              configuracoes
          SET
              value = :value
          WHERE
              id = :id
      ";
      $stmt = $this->db->prepare($sql);
      $parameters = [
          ':id'           => $configuracoes->id,
          ':value'         => $configuracoes->value
      ];
      return $stmt->execute($parameters);
  }
}

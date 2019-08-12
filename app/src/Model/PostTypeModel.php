<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\ModelReturn;
use Farol360\Ancora\Model\PostType;

class PostTypeModel extends Model
{
  public function add(PostType $postType)
  {
    $sql = "INSERT INTO post_types (name, description, status, slug) VALUES (:name, :description, :status, :slug)";

    $stmt = $this->db->prepare($sql);
    $parameters = [
      ':name' => $postType->name,
      ':description' => $postType->description,
      ':status' => $postType->status,
      ':slug' => $postType->slug,
    ];
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
    $data['table'] = 'post_types';
    $data['function'] = 'add';
    $modelReturn = new ModelReturn($data);
    return $modelReturn;
  }

  public function delete($post_types)
  {
      $sql = "
        UPDATE
            post_types
        SET
            slug            = :slug,
            deleted         = 1
        WHERE
            id = :id
    ";
    $parameters =
    [

     ':id'   => (int)$post_types->id,
     ':slug' => $post_types->slug.'_deleted'
    ];
    $stmt = $this->db->prepare($sql);
    //var_dump($stmt);
    //var_dump($sql);
    //die;
    $exec = $stmt->execute($parameters);
    //var_dump($exec);die;
    }

  public function disable(int $id): bool
  {
      $sql = "
          UPDATE
              post_types
          SET
              status = 0
          WHERE
              id = :id
      ";
      $stmt = $this->db->prepare($sql);
      $parameters = [
          ':id' => $id,
      ];
      return $stmt->execute($parameters);
  }

  public function enable(int $id): bool
  {
      $sql = "
          UPDATE
              post_types
          SET
              status = 1
          WHERE
              id = :id
      ";
      $stmt = $this->db->prepare($sql);
      $parameters = [
          ':id' => $id,
      ];
      return $stmt->execute($parameters);
  }

  public function get(int $id)
  {
      $sql = "
          SELECT
              *
          FROM
              post_types
          WHERE
              id = :id
          LIMIT 1
      ";
      $stmt = $this->db->prepare($sql);
      $parameters = [':id' => $id];
      $stmt->execute($parameters);
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, PostType::class);
      return $stmt->fetch();
  }

  // TODO TEST
  public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
  {
       $sql = "
          SELECT
              *
          FROM
              post_types
          WHERE
              deleted != 1
          ORDER BY
              id DESC
          LIMIT ? , ?
      ";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
      $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
      $stmt->execute();
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, PostType::class);
      return $stmt->fetchAll();
  }

  public function getAmount()
  {
      $sql = "
          SELECT
              COUNT(id) AS amount
          FROM
              post_types

      ";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
  }

  public function getPublished(int $offset = 0, int $limit = PHP_INT_MAX): array
  {
       $sql = "
          SELECT
              *
          FROM
              post_types
          WHERE
              status = 1
          ORDER BY
              id DESC
          LIMIT ? , ?
      ";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
      $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
      $stmt->execute();
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, PostType::class);
      return $stmt->fetchAll();
  }

  public function getSlug(int $post_type_id = null, string $slug = "")
  {
    $sql = "
        SELECT
            *
        FROM
          post_types
        WHERE
          post_types.id = :id OR post_types.slug = :slug

    ";
    $stmt = $this->db->prepare($sql);
    $parameters = [':id' => $post_type_id, ':slug' => $slug];
    $stmt->execute($parameters);
    $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, PostType::class);
    return $stmt->fetch();
  }

  public function update(postType $postType): bool
  {
      $sql = "
          UPDATE
              post_types
          SET
              name = :name,
              description = :description,
              slug = :slug,
              status = :status
          WHERE
              id = :id
      ";
      $stmt = $this->db->prepare($sql);
      $parameters = [
          ':id'           => $postType->id,
          ':name'         => $postType->name,
          ':description'  => $postType->description,
          ':slug'         => $postType->slug,
          ':status'       => $postType->status
      ];
      return $stmt->execute($parameters);
  }
}

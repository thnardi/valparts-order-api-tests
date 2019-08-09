<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\UserType;
use RKA\Session;

class UserTypeModel extends Model
{
    public function add(UserType $users_type)
    {
        $sql = "
            INSERT INTO users_type (
                name,
                description,
                slug
            )
            VALUES (
                :name,
                :description,
                :slug
            )
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':name' => $users_type->name,
            ':description' => $users_type->description,
            ':slug' => $users_type->slug
        ];
        $stmt = $this->db->prepare($sql);
        $exec = $stmt->execute($parameters);
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
      $data['table'] = 'users_type';
      $data['function'] = 'add';
      $modelReturn = new ModelReturn($data);
      return $modelReturn;
    }

    public function delete($cliente)
    {
      $sql = "
        UPDATE
            users_type
        SET
            slug            = :slug,
            deleted         = 1
        WHERE
            id = :id
    ";
    $parameters =
    [

     ':id'   => (int)$cliente->id,
     ':slug' => $cliente->slug.'_deleted'
    ];
    $stmt = $this->db->prepare($sql);
    //var_dump($stmt);
    //var_dump($sql);
    //die;
    $exec = $stmt->execute($parameters);
    //var_dump($exec);die;
    }

    public function get(int $user_typeId = null)
    {
        if (!empty($user_typeId)) {
            $sql = "
                SELECT
                    users_type.*
                FROM
                    users_type
                WHERE
                    users_type.id = :id
            ";
            $stmt = $this->db->prepare($sql);
            $parameters = [':id' => $user_typeId];
            $stmt->execute($parameters);
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, UserType::class);
            return $stmt->fetch();
        }
        return new User();
    }

    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
               *
            FROM
                users_type
            WHERE
                deleted != 1
            ORDER BY
                users_type.name ASC
                LIMIT ? , ?
        ";
        $query = $this->db->prepare($sql);
        $query->bindValue(1, $offset, \PDO::PARAM_INT);
        $query->bindValue(2, $limit, \PDO::PARAM_INT);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, UserType::class);
        return $query->fetchAll();
    }


    public function getAmount()
  {
      $sql = "
          SELECT
              COUNT(id) AS amount
          FROM
            users_type
          WHERE
            deleted != 1
      ";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
  }

  public function getSlug(int $cliente_id = null, string $slug = "")
  {
      $session = new Session();
      if (empty($admin_ancora_id) && empty($slug) && !empty($session->get('admin_ancora'))) {
          if (isset($session->admin_ancora['id'])) {
              $admin_ancora_id = (int)$session->admin_ancora['id'];
          }
      }
      if (!empty($admin_ancora_id) || !empty($slug)) {
          $sql = "
              SELECT
                  *
              FROM
                users_type
              WHERE
                users_type.id = :id OR users_type.slug = :slug

          ";
          $stmt = $this->db->prepare($sql);
          $parameters = [':id' => $cliente_id, ':slug' => $slug];
          $stmt->execute($parameters);
          $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, UserType::class);
          return $stmt->fetch();
      }
      return new AdminAncora();
  }
    public function update($tipo_de_cliente)
    {

        $sql = "
            UPDATE
                users_type
            SET
                name = :name,
                slug = :slug,
                description = :description

            WHERE
                id = :id
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':id' => (int) $tipo_de_cliente->id,
            ':name' => $tipo_de_cliente->name,
            ':slug' => $tipo_de_cliente->slug,
            ':description' => $tipo_de_cliente->description
        ];
        $stmt = $this->db->prepare($sql);
        $exec = $stmt->execute($parameters);
        // verifica se ocorreu com sucesso o execute
        if ($exec) {
          $data['data'] = $stmt->rowCount();
          $data['errorCode'] = null;
          $data['errorInfo'] = null;
        } else {
          $data['data'] = false;
          $data['errorCode'] = $stmt->errorCode();
          $data['errorInfo'] = $stmt->errorInfo();
        }
        // completa demais dados
        $data['status'] = $exec;
        $data['table'] = 'users';
        $data['function'] = 'update';
        $modelReturn = new ModelReturn($data);
        return $modelReturn;
    }
}

<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\AdminAncora;
use RKA\Session;


class AdminAncoraModel extends Model
{
  public function add(AdminAncora $adminAncora)
  {
      $sql = "INSERT INTO admin_ancora (name,
          email,
          telefone,
          slug,
          type,
          password
      ) VALUES (
          :name,
          :email,
          :telefone,
          :slug,
          :type,
          :password
          )";
      $parameters = [
        ':name'             => $adminAncora->name,
        ':email'             => $adminAncora->email,
        ':telefone'             => $adminAncora->telefone,
        ':slug'     => $adminAncora->slug,
        ':type'     => $adminAncora->type,
        ':password'     => $adminAncora->password
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
      $data['table'] = 'admin_ancora';
      $data['function'] = 'add';
      $modelReturn = new ModelReturn($data);
      return $modelReturn;
  }

  public function delete(int $id): bool
  {
      $sql = "DELETE FROM admin_ancora WHERE id = :id";
      $stmt = $this->db->prepare($sql);
      $parameters = [':id' => $id];
      return $stmt->execute($parameters);
  }

  public function get(int $id = 0)
  {
    if ( ($id == 0) && isset($_SESSION['admin_ancora']) ) {
      $id = $_SESSION['admin_ancora']['id'];
    }
    $sql = "SELECT
          *
      FROM
        admin_ancora
      WHERE
        id = ?
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(1, $id, \PDO::PARAM_INT);
    $stmt->execute();
    $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, AdminAncora::class);
    return $stmt->fetch();

  }

  // TODO TEST
  public function getAll(int $offset = 0, int $limit = PHP_INT_MAX, int $trash = 0 ): array
  {
      $sql = "
          SELECT
              *
          FROM
            admin_ancora
          LIMIT ? , ?
      ";
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
      $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
      $stmt->execute();
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, AdminAncora::class);
      return $stmt->fetchAll();
  }

  public function getAllByTypePermission(int $offset = 0, int $limit = PHP_INT_MAX, int $trash = 0 ): array
  {
    $type = isset($_SESSION['admin_ancora']['type']) ? (int) $_SESSION['admin_ancora']['type'] : 0;
    $sql =
      "SELECT
          *
      FROM
        admin_ancora
      WHERE
        admin_ancora.type <= ?
      LIMIT ? , ?
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(1, $type, \PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
    $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
    $stmt->execute();
    $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, AdminAncora::class);
    return $stmt->fetchAll();
  }


  public function getAmount()
  {
      $sql = "
          SELECT
              COUNT(id) AS amount
          FROM
            admin_ancora
      ";
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
  }

  public function getSlug(int $admin_ancora_id = null, string $slug = "")
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
                admin_ancora
              WHERE
                admin_ancora.id = :id OR admin_ancora.slug = :slug

          ";
          $stmt = $this->db->prepare($sql);
          $parameters = [':id' => $admin_ancora_id, ':slug' => $slug];
          $stmt->execute($parameters);
          $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, AdminAncora::class);
          return $stmt->fetch();
      }
      return new AdminAncora();
  }

  public function update($adminAncora)
  {
      $sql = "
          UPDATE
              admin_ancora
          SET
              name            = :name,
              email           = :email,
              telefone           = :telefone,
              slug            = :slug,
              password        = :password,
              type            = :type

          WHERE
              id = :id
      ";
      $parameters =
      [
       ':id'           => (int) $adminAncora->id,
       ':name'         => $adminAncora->name,
       ':email'         => $adminAncora->email,
       ':telefone'         => $adminAncora->telefone,
       ':slug'         => $adminAncora->slug,
       ':password'      => $adminAncora->password,
       ':type'      => $adminAncora->type
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
      $data['table'] = 'admin_ancora';
      $data['function'] = 'update';
      $modelReturn = new ModelReturn($data);
      return $modelReturn;
  }
}

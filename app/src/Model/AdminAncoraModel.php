<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\AdminAncora;
use Farol360\Ancora\Model\ModelReturn;
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
          password,
          ativo,
          deleted
      ) VALUES (
          :name,
          :email,
          :telefone,
          :slug,
          :type,
          :password,
          :ativo,
          :deleted
          )";
      $parameters = [
        ':name'             => $adminAncora->name,
        ':email'             => $adminAncora->email,
        ':telefone'             => $adminAncora->telefone,
        ':slug'     => $adminAncora->slug,
        ':type'     => $adminAncora->type,
        ':password'     => $adminAncora->password,
        ':ativo'    => $adminAncora->ativo,
        ':deleted'  => $adminAncora->deleted
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

  public function delete($adminAncora)
  {


      $sql = "
          UPDATE
              admin_ancora
          SET
              slug            = :slug,
              deleted         = true
          WHERE
              id = :id
      ";
      $parameters =
      [

       ':id'   => (int)$adminAncora->id,
       ':slug' => $adminAncora->slug.'_deleted'
      ];
      $stmt = $this->db->prepare($sql);
      //var_dump($stmt);
      //var_dump($sql);
      //die;
      $exec = $stmt->execute($parameters);
      //var_dump($exec);die;
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

  public function getAllByTypePermission(int $order, int $filtro, int $offset = 0, int $limit = PHP_INT_MAX, int $trash = 0 ): array
  {
    $type = isset($_SESSION['admin_ancora']['type']) ? (int) $_SESSION['admin_ancora']['type'] : 0;
    $sql =
      "SELECT
          *
      FROM
        admin_ancora
      WHERE
        admin_ancora.type <= ?";
      if ($filtro == 1) {
        $sql .="
          AND admin_ancora.ativo = 1
        ";
      }
      if ($filtro == 2) {
        $sql .="
          AND admin_ancora.ativo = 0
        ";
      }
      if ($filtro == 3) {

      }
      if ($order == 1) {

      }
      if ($order == 2) {
        $sql .="
          ORDER BY
            admin_ancora.created_at DESC
        ";
      }
      $sql .="
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
              type            = :type,
              ativo           = :ativo,
              deleted         = :deleted

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
       ':type'      => $adminAncora->type,
       ':ativo'     => $adminAncora->ativo,
       ':deleted'   => $adminAncora->deleted
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

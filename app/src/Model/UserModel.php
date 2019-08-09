<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\User;
use RKA\Session;

class UserModel extends Model
{
    public function add(User $user)
    {
        $sql = "
            INSERT INTO users (
                email,
                name,
                slug,
                password,
                users_type,
                nascimento,
                is_cnpj,
                cpf,
                tel_numero,
                role_id,
                active,
                deleted
            )
            VALUES (
                :email,
                :name,
                :slug,
                :password,
                :users_type,
                :nascimento,
                :is_cnpj,
                :cpf,
                :tel_numero,
                :role_id,
                :active,
                :deleted
            )
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':email' => $user->email,
            ':name' => $user->name,
            ':slug' => $user->slug,
            ':password' => $user->password,
            ':users_type' => $user->users_type,
            ':role_id' => $user->role_id,
            ':nascimento' => $user->nascimento,
            ':is_cnpj' => 0,
            ':cpf' => $user->cpf,
            ':tel_numero' => $user->tel_numero,
            ':active' => $user->active,
            ':deleted' => 0
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
      $data['table'] = 'admin_ancora';
      $data['function'] = 'add';
      $modelReturn = new ModelReturn($data);
      return $modelReturn;
    }

    public function delete($cliente)
    {
      $sql = "
        UPDATE
            users
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

    public function get(int $userId = null, string $email = "")
    {
        $session = new Session();
        if (empty($userId) && empty($email) && !empty($session->get('user'))) {
            $userId = (int)$session->user['id'];
        }
        if (!empty($userId) || !empty($email)) {
            $sql = "
                SELECT
                    users.*,
                    roles.description AS role
                FROM
                    users
                    LEFT JOIN roles ON roles.id = users.role_id
                WHERE
                    (users.id = :id OR users.email = :email)
                    AND deleted != 1
            ";
            $stmt = $this->db->prepare($sql);
            $parameters = [':id' => $userId, ':email' => $email];
            $stmt->execute($parameters);
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, User::class);
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
                users
            ORDER BY
                users.name ASC
                LIMIT ? , ?
        ";
        $query = $this->db->prepare($sql);
        $query->bindValue(1, $offset, \PDO::PARAM_INT);
        $query->bindValue(2, $limit, \PDO::PARAM_INT);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, User::class);
        return $query->fetchAll();
    }

    public function getAllOrder(int $order, int $filtro, int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                users.*,
                roles.name AS role
            FROM
                users
                LEFT JOIN roles ON roles.id = users.role_id
            WHERE
                deleted != 1";
            if ($filtro == 1) {
        $sql .="
          AND users.active = 1 OR users.active = NULL
        ";
      }
      if ($filtro == 2) {
        $sql .="
          AND users.active = 0
        ";
      }
      if ($filtro == 3) {

      }
      if ($order == 1) {

      }
      if ($order == 2) {
        $sql .="
          ORDER BY
            users.created_at DESC
        ";
      }
      $sql .="
        LIMIT ? , ?
      ";
        $query = $this->db->prepare($sql);
        $query->bindValue(1, $offset, \PDO::PARAM_INT);
        $query->bindValue(2, $limit, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }

    public function getAmount()
  {
      $sql = "
          SELECT
              COUNT(id) AS amount
          FROM
            users
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
                users
              WHERE
                users.id = :id OR users.slug = :slug

          ";
          $stmt = $this->db->prepare($sql);
          $parameters = [':id' => $cliente_id, ':slug' => $slug];
          $stmt->execute($parameters);
          $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, User::class);
          return $stmt->fetch();
      }
      return new AdminAncora();
  }

    public function getByEmail(string $email)
    {
        $sql = "
            SELECT
                users.*
            FROM
                users
            WHERE
                email = :email
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':email' => $email];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, User::class);
        return $stmt->fetch();
    }

    public function getUserCourses(int $userId): array
    {
        $sql = "
            SELECT
                courses.*
            FROM
                users
                LEFT JOIN users_courses ON users_courses.user_id = users.id
                INNER JOIN courses ON courses.id = users_courses.course_id
            WHERE
                users.id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $userId];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, User::class);
        return $stmt->fetchAll();
    }

    public function getUserOrders(int $userId): array
    {
        $sql = "
            SELECT
                orders.*,
                courses.title AS course_name
            FROM
                users
                LEFT JOIN orders ON orders.user_id = users.id
                LEFT JOIN courses ON courses.id = orders.course_id
            WHERE
                users.id = :id
                AND orders.transaction IS NOT NULL
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $userId];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, User::class);
        return $stmt->fetchAll();
    }

    public function getUsers(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                users.*,
                roles.description AS role
            FROM
                users
                LEFT JOIN roles ON roles.id = users.role_id
            WHERE
                deleted != 1 AND roles.name = 'user'
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, User::class);
        return $stmt->fetchAll();
    }

    public function update($user)
    {

        $sql = "
            UPDATE
                users
            SET
            ";
        if (!empty($password)) {
            $sql .= "
                password = :password,
            ";
        }
        $sql .= "
                email= :email,
                name = :name,
                slug = :slug,
                users_type = :users_type,
                nascimento = :nascimento,
                cpf = :cpf,
                tel_numero = :tel_numero,
                active = :active

            WHERE
                id = :id
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':id' => (int) $user->id,
            ':email' => $user->email,
            ':name' => $user->name,
            ':slug' => $user->slug,
            ':users_type' => $user->users_type,
            //':password' => $user->password,
            ':nascimento' => $user->nascimento,
            ':cpf' => $user->cpf,
            ':tel_numero' => $user->tel_numero,
            ':active' => $user->active
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

    public function verify(int $userId): bool
    {
        $sql = "
            UPDATE
                users
            SET
                recover_token = NULL,
                verification_token = NULL,
                active = 1
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $userId
        ];
        return $stmt->execute($parameters);
    }
}

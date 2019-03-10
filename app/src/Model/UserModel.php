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
                password,
                nascimento,
                cpf,
                tel_area,
                tel_numero,
                end_rua,
                end_numero,
                end_complemento,
                end_bairro,
                end_cidade,
                end_estado,
                end_cep,
                role_id,
                active,
                deleted,
                created_at,
                updated_at
            )
            VALUES (
                :email,
                :name,
                :password,
                :nascimento,
                :cpf,
                :tel_area,
                :tel_numero,
                :end_rua,
                :end_numero,
                :end_complemento,
                :end_bairro,
                :end_cidade,
                :end_estado,
                :end_cep,
                :role_id,
                :active,
                :deleted,
                :created_at,
                :updated_at
            )
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':email' => $user->email,
            ':name' => $user->name,
            ':password' => $user->password,
            ':role_id' => $user->role_id,
            ':nascimento' => $user->nascimento,
            ':cpf' => $user->cpf,
            ':tel_area' => $user->tel_area,
            ':tel_numero' => $user->tel_numero,
            ':end_rua' => $user->end_rua,
            ':end_numero' => $user->end_numero,
            ':end_complemento' => $user->endComplemento,
            ':end_bairro' => $user->end_bairro,
            ':end_cidade' => $user->end_cidade,
            ':end_estado' => $user->end_estado,
            ':end_cep' => $user->end_cep,
            ':active' => 1,
            ':deleted' => 0,
            ':created_at' => time(),
            ':updated_at' => null
        ];
        if ($query->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $userId): bool
    {
         $sql = "UPDATE users SET deleted = 1 WHERE id = :id";
        $query = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $query->execute($parameters);
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
                users.*,
                roles.name AS role
            FROM
                users
                LEFT JOIN roles ON roles.id = users.role_id
            WHERE
                deleted != 1
            LIMIT ? , ?
        ";
        $query = $this->db->prepare($sql);
        $query->bindValue(1, $offset, \PDO::PARAM_INT);
        $query->bindValue(2, $limit, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
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

    public function update(User $user): bool
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
                email = :email,
                name = :name,
                nascimento = :nascimento,
                cpf = :cpf,
                tel_area = :tel_area,
                tel_numero = :tel_numero,
                end_rua = :end_rua,
                end_numero = :end_numero,
                end_complemento = :end_complemento,
                end_bairro = :end_bairro,
                end_cidade = :end_cidade,
                end_estado = :end_estado,
                end_cep = :end_cep,
                role_id = :role_id

            WHERE
                id = :id
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':id' => (int) $user->id,
            ':email' => $user->email,
            ':name' => $user->name,
            ':password' => $user->password,
            ':role_id' => $user->role_id,
            ':nascimento' => $user->nascimento,
            ':cpf' => $user->cpf,
            ':tel_area' => $user->tel_area,
            ':tel_numero' => $user->tel_numero,
            ':end_rua' => $user->end_rua,
            ':end_numero' => $user->end_numero,
            ':end_complemento' => $user->endComplemento,
            ':end_bairro' => $user->end_bairro,
            ':end_cidade' => $user->end_cidade,
            ':end_estado' => $user->end_estado,
            ':end_cep' => $user->end_cep

        ];
        if (!empty($password)) {
            $parameters[':password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        return $query->execute($parameters);
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

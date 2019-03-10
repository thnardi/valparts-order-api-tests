<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\Role;

class RoleModel extends Model
{
    public function add(Role $role)
    {
        $sql = "
            INSERT INTO roles (name, description, access_level)
            VALUES (:name, :description, :access_level)
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':name' => $role->name,
            ':description' => $role->description,
            ':access_level' => $role->access_level
        ];
        if ($query->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM roles WHERE id = :id";
        $query = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $query->execute($parameters);
    }

    public function get(int $id)
    {
        $sql = "
            SELECT
                *
            FROM
                roles
            WHERE
                id = :id
            LIMIT 1
        ";
        $query = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $query->execute($parameters);
        $query->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Role::class);
        return $query->fetch();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT
                *
            FROM
                roles
            ORDER BY
                access_level ASC
        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Role::class);
        return $query->fetchAll();
    }

    public function update(Role $role): bool
    {
        $sql = "
            UPDATE
                roles
            SET
                name = :name,
                description = :description,
                access_level = :access_level
            WHERE
                id = :id
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':name' => $role->name,
            ':description' => $role->description,
            ':access_level' => $role->access_level,
            ':id' => $role->id
        ];
        return $query->execute($parameters);
    }
}

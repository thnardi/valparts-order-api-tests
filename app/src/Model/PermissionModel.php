<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\Permission;

class PermissionModel extends Model
{
    public function add(Permission $permission)
    {
        $sql = "
            INSERT INTO permissions (
                resource,
                description,
                role_id
                )
            VALUES (:resource, :description, :role_id)
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':resource'     => $permission->resource,
            ':description'  => $permission->description,
            ':role_id'      => $permission->role_id
        ];
        if ($query->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id): bool
    {
       $sql = "DELETE FROM permissions WHERE id = :id";
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
                permissions
            WHERE
                id = :id
            LIMIT 1
        ";
        $query = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $query->execute($parameters);
        $query->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Permission::class);
        return $query->fetch();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT
                permissions.*,
                roles.name as role
            FROM
                permissions LEFT JOIN roles ON roles.id = permissions.role_id
            ORDER BY
                id ASC
        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        $query->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Permission::class);
        return $query->fetchAll();
    }

    public function update(Permission $permission): bool
    {
        $sql = "
            UPDATE
                permissions
            SET
                resource = :resource,
                description = :description,
                role_id = :role_id
            WHERE
                id = :id
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':resource'     => $permission->resource,
            ':description'  => $permission->description,
            ':role_id'      => $permission->role_id,
            ':id'           => $permission->id
        ];
        return $query->execute($parameters);
    }
}

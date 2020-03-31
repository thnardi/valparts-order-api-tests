<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\Post;


class PostModel extends Model
{
    public function add(Post $post)
    {
        $sql = "INSERT INTO posts (name,
            img_featured,
            id_post_type,
            description,
            status,
            trash

        ) VALUES (
            :name,
            :img_featured,
            :id_post_type,
            :description,
            :status,
            :trash
            )";

        $stmt = $this->db->prepare($sql);
        $parameters = [
         ':name'             => $post->name,
         ':img_featured'     => $post->img_featured,
         ':id_post_type'     => $post->id_post_type,
         ':description'      => $post->description,
         ':status'           => 1,
         ':trash'            => $post->trash

        ];

        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function disable(int $id): bool
    {
        $sql = "
            UPDATE
                posts
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

    public function disableBypostType(int $id): bool
    {
        $sql = "
            UPDATE
                posts
            SET
                status = 0
            WHERE
                id_post_type = :id
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
                posts
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
    public function get(int $id = 0)
    {

        if ($id == 0) {
            $sql = "
                SELECT
                    posts.*,
                    post_types.name AS post_type,
                    post_types.id AS post_types_id
                FROM
                    posts
                    LEFT JOIN post_types ON post_types.id = posts.id_post_type

                LIMIT 1
            ";
            $stmt = $this->db->prepare($sql);

            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Post::class);
            return $stmt->fetch();
        } else {
           $sql = "
                SELECT
                    posts.*,
                    post_types.name AS post_type,
                    post_types.id AS post_types_id
                FROM
                    posts
                    LEFT JOIN post_types ON post_types.id = posts.id_post_type
                WHERE
                    posts.id = :id
                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);
            $parameters = [':id' => $id];
            $stmt->execute($parameters);
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Post::class);
            return $stmt->fetch();
        }



    }

    // TODO TEST
    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX, int $trash = 0 ): array
    {
        $sql = "
            SELECT
                posts.*,
                post_types.name as post_type
            FROM
                posts
                LEFT JOIN post_types ON posts.id_post_type = post_types.id
            WHERE
                posts.trash = ?
            ORDER BY
                posts.id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $trash, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Post::class);
        return $stmt->fetchAll();
    }

    // TODO TEST
    public function getAllPublished(int $offset = 0, int $limit = PHP_INT_MAX, int $trash = 0 ): array
    {
        $sql = "
            SELECT
                *
            FROM
                posts
            WHERE
                trash = ?
                AND
                status = 1
            ORDER BY
                id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $trash, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Post::class);
        return $stmt->fetchAll();
    }



    public function getAmount()
    {
        $sql = "
            SELECT
                COUNT(id) AS amount
            FROM
                posts

        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetch();
    }

    public function getAmountBypostType(int $id_post_type)
    {
        $sql = "
            SELECT
                COUNT(id) AS amount
            FROM
                posts
            WHERE
                id_post_type = :id_post_type

        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_post_type' => $id_post_type];
        $stmt->execute($parameters);
        return $stmt->fetch();
    }

    public function getAmountPublishedBypostType(int $id_post_type)
    {
        $sql = "
            SELECT
                COUNT(id) AS amount
            FROM
                posts
            WHERE
                id_post_type = :id_post_type
                AND
                status = 1

        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_post_type' => $id_post_type];
        $stmt->execute($parameters);
        return $stmt->fetch();
    }

    // TODO: TESTS
    public function getBypostType(int $id_post_type):array
    {
        $sql = "
            SELECT
                *
            FROM
                posts
            WHERE
                id_post_type = :id_post_type

        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_post_type' => $id_post_type];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Post::class);
        return $stmt->fetchAll();
    }

    public function trashRemove(int $id): bool
    {
        $sql = "
            UPDATE
                posts
            SET
                trash = 0

            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id
        ];
        return $stmt->execute($parameters);
    }

    public function trashSend(int $id): bool
    {
        $sql = "
            UPDATE
                posts
            SET
                trash = 1,
                status = 0
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id
        ];
        return $stmt->execute($parameters);
    }


    public function update(Post $post): bool
    {
        $sql = "
            UPDATE
                posts
            SET
                name            = :name,
                img_featured    = :img_featured,
                id_post_type   = :id_post_type,
                description     = :description,
                status          = :status,
                trash           = :trash

            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters =
        [
         ':id'           => (int) $post->id,
         ':name'         => $post->name,
         ':img_featured' => $post->img_featured,
         ':id_post_type'=> $post->id_post_type,
         ':description'  => $post->description,
         ':status'       => $post->status,
         ':trash'        => $post->trash

        ];
        return $stmt->execute($parameters);
    }
}

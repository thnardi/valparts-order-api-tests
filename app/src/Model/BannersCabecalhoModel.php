<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\BannersCabecalhoModel;


class BannersCabecalhoModel extends Model
{
   public function get(int $id = 0)
    {

        if ($id == 0) {
            $sql = "
                SELECT
                    banners_cabecalho_site.*
                FROM
                  banners_cabecalho_site
                LIMIT 1
            ";
            $stmt = $this->db->prepare($sql);

            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, BannersCabecalho::class);
            return $stmt->fetch();
        } else {
           $sql = "
                SELECT
                banners_cabecalho_site.*
                FROM
                banners_cabecalho_site
                WHERE
                banners_cabecalho_site.id = :id
                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);
            $parameters = [':id' => $id];
            $stmt->execute($parameters);
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, BannersCabecalho::class);
            return $stmt->fetch();
        }
    }

    // TODO TEST
    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
              banners_cabecalho_site.*
            FROM
              banners_cabecalho_site
            ORDER BY
              banners_cabecalho_site.id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, BannersCabecalho::class);
        return $stmt->fetchAll();
    }

    public function getAmount()
    {
        $sql = "
            SELECT
                COUNT(id) AS amount
            FROM
              banners_cabecalho_site

        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetch();
    }

    public function update(BannersCabecalho $banners): bool
    {
        $sql = "
            UPDATE
              banners_cabecalho_site
            SET
                name            = :name,
                img_featured    = :img_featured,
                img_mobile    = :img_mobile,
                description    = :description,
                title_position            = :title_position

            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters =
        [
         ':id'              => (int) $banners->id,
         ':name'            => $banners->name,
         ':img_featured'    => $banners->img_featured,
         ':img_mobile'    => $banners->img_mobile,
         ':description'     => $banners->description,
         ':title_position'  => $banners->title_position

        ];
        return $stmt->execute($parameters);
    }
}

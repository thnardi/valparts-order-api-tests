<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class Post
{
    public $id;
    public $name;
    public $description;
    public $img_featured;
    public $id_post_type;
    public $status;
    public $trash;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id       = $data['id'] ?? null;
        $this->name     = $data['name'] ?? null;
        $this->description  = $data['description'] ?? null;
        $this->img_featured = $data['img_featured'] ?? null;
        $this->id_post_type = $data['id_post_type'] ?? null;
        $this->status       = $data['status'] ?? null;
        $this->trash        = $data['trash'] ?? null;
        $this->created_at   = $data['created_at'] ?? null;
        $this->updated_at   = $data['updated_at'] ?? null;
    }
}

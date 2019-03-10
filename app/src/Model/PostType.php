<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class PostType
{
    public $id;
    public $name;
    public $description;
    public $status;
    public $trash;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id       = $data['id'] ?? null;
        $this->name     = $data['name'] ?? null;
        $this->description  = $data['description'] ?? null;
        $this->status       = $data['status'] ?? null;
        $this->trash        = $data['trash'] ?? null;
        $this->created_at   = $data['created_at'] ?? null;
        $this->updated_at   = $data['updated_at'] ?? null;
    }
}

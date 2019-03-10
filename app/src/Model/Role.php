<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class Role
{
    public $id;
    public $name;
    public $description;
    public $access_level;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = !empty($data['name']) ? strtolower($data['name']) : null;
        $this->description = $data['description'] ?? null;
        $this->access_level = $data['access_level'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}

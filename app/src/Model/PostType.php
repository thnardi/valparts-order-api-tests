<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class PostType
{
  public $id;
  public $name;
  public $description;
  public $slug;
  public $status;
  public $created_at;
  public $updated_at;

  public function __construct(array $data = [])
  {
    $this->id       = $data['id'] ?? null;
    $this->name     = $data['name'] ?? null;
    $this->description  = $data['description'] ?? null;
    $this->slug         = $data['slug'] ?? null;
    $this->status       = $data['status'] ?? null;
    $this->created_at   = $data['created_at'] ?? null;
    $this->updated_at   = $data['updated_at'] ?? null;
  }
}

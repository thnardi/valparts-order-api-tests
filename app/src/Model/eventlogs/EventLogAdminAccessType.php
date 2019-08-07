<?php
 namespace Farol360\Ancora\Model\eventlogs;

class EventLogAdminAccessType
{
    public $id;
    public $slug;
    public $name;
    public $description;

    public function __construct(array $data = [])
    {
      $this->id                  = $data['id'] ?? null;
      $this->slug                = $data['slug'] ?? null;
      $this->name                = $data['name'] ?? null;
      $this->description         = $data['description'] ?? null;
    }
}

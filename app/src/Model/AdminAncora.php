<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class AdminAncora
{
    public $id;
    public $name;
    public $email;
    public $telefone;
    public $slug;
    public $type;
    public $password;
    public $ativo;
    public $deleted;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id           = $data['id'] ?? null;
        $this->name         = $data['name'] ?? null;
        $this->email        = $data['email'] ?? null;
        $this->telefone     = $data['telefone'] ?? null;
        $this->slug         = $data['slug'] ?? null;
        $this->type         = $data['type'] ?? null;
        $this->password     = !empty($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : null;
        $this->ativo        = $data['ativo'] ?? null;
        $this->deleted      = $data['deleted'] ?? null;
        $this->created_at   = $data['created_at'] ?? null;
        $this->updated_at   = $data['updated_at'] ?? null;
    }
}

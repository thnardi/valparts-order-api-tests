<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class User
{
    public $id;
    public $email;
    public $name;
    public $slug;
    public $password;
    public $users_type;
    public $nascimento;
    public $is_cnpj;
    public $cpf;
    public $tel_numero;
    public $role_id;
    public $recover_token;
    public $verification_token;
    public $active;
    public $session;
    public $deleted;
    public $deleted_at;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->email = !empty($data['email']) ? strtolower($data['email']) : null;
        $this->name = $data['name'] ?? null;
        $this->slug = $data['slug'] ?? null;
        $this->password = !empty($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : null;
        $this->users_type = $data['users_type'] ?? null;
        $this->nascimento = !empty($data['nascimento']) ? date('Y-m-d', strtotime($data['nascimento'])) : null;
        $this->is_cnpj = $data['is_cnpj'] ?? null;
        $this->cpf = $data['cpf'] ?? null;
        //$this->tel_area = $data['tel_area'] ?? null;
        $this->tel_numero = $data['tel_numero'] ?? null;

        $this->role_id = $data['role_id'] ?? null;
        $this->recover_token = $data['recover_token'] ?? null;
        $this->verification_token = $data['verification_token'] ?? null;
        $this->active = $data['active'] ?? null;
        $this->session = $data['session'] ?? null;
        $this->deleted = $data['deleted'] ?? null;
        $this->deleted_at = $data['deleted_at'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}

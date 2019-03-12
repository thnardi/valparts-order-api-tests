<?php

use Phinx\Migration\AbstractMigration;

class BaseMigration extends AbstractMigration
{
    public function change()
    {
        $roles = $this->table('roles');
        $roles->addColumn('name', 'string');
        $roles->addColumn('description', 'string');
        $roles->addColumn('access_level', 'integer');
        $roles->addTimestamps();
        $roles->create();

        $permissions = $this->table('permissions');
        $permissions->addColumn('resource', 'string');
        $permissions->addColumn('description', 'string');
        $permissions->addColumn('role_id', 'integer', ['null' => true]);
        $permissions->addColumn('role_list', 'text', ['null' => true]);
        $permissions->addTimestamps();
        $permissions->addForeignKey('role_id', 'roles', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'NO_ACTION',
        ]);
        $permissions->addIndex(['resource'], ['unique' => true]);
        $permissions->create();

        $users = $this->table('users');
        $users->addColumn('email', 'string');
        $users->addColumn('name', 'string');
        $users->addColumn('password', 'string');
        $users->addColumn('nascimento', 'date', ['null' => true]);
        $users->addColumn('cpf', 'string', ['null' => true]);
        $users->addColumn('tel_area', 'integer', ['null' => true]);
        $users->addColumn('tel_numero', 'integer', ['null' => true]);
        $users->addColumn('end_rua', 'string', ['null' => true]);
        $users->addColumn('end_numero', 'integer', ['null' => true]);
        $users->addColumn('end_complemento', 'string', ['null' => true]);
        $users->addColumn('end_bairro', 'string', ['null' => true]);
        $users->addColumn('end_cidade', 'string', ['null' => true]);
        $users->addColumn('end_estado', 'string', ['null' => true]);
        $users->addColumn('end_cep', 'string', ['null' => true]);
        $users->addColumn('role_id', 'integer', ['null' => true]);
        $users->addColumn('recover_token', 'string', ['null' => true]);
        $users->addColumn('verification_token', 'string', ['null' => true]);
        $users->addColumn('active', 'boolean', ['default' => false]);
        $users->addColumn('session', 'string', ["null" => true]);
        $users->addColumn('deleted', 'boolean', ['default' => false]);
        $users->addColumn('deleted_at', 'timestamp', ['null' => true]);
        $users->addTimestamps();
        $users->addIndex(['email'], ['unique' => true]);
        $users->addForeignKey('role_id', 'roles', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'NO_ACTION'
        ]);
        $users->create();

        $posts = $this->table('posts');
        $posts->addColumn('name', 'string');
        $posts->addColumn('img_featured', 'string');
        $posts->addColumn('id_post_type', 'integer');
        $posts->addColumn('description', 'text');
        $posts->addColumn('status', 'integer');
        $posts->addColumn('trash', 'integer');
        $posts->addTimestamps();
        $posts->create();

        $posts = $this->table('post_types');
        $posts->addColumn('name', 'string');
        $posts->addColumn('description', 'string');
        $posts->addColumn('status', 'integer');
        $posts->addColumn('trash', 'integer');
        $posts->addTimestamps();
        $posts->create();
    }
}

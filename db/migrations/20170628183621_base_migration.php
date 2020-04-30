<?php

use Phinx\Migration\AbstractMigration;

class BaseMigration extends AbstractMigration
{
    public function change()
    {
      $admin_ancora = $this->table('admin_ancora');
        $admin_ancora->addColumn('name', 'string', ["null" => true]);
        $admin_ancora->addColumn('email', 'string', ["null" => true]);
        $admin_ancora->addColumn('telefone', 'string', ["null" => true]);
        $admin_ancora->addColumn('slug', 'string');
        $admin_ancora->addColumn('session', 'string', ["null" => true]);
        $admin_ancora->addColumn('password', 'string');
        $admin_ancora->addColumn('type', 'integer');
        $admin_ancora->addColumn('ativo', 'boolean', ['null' => true]);
        $admin_ancora->addColumn('deleted', 'boolean', ['null' => true, 'default' => false]);
        $admin_ancora->addTimestamps();
      $admin_ancora->create();

      $banners_cabecalho = $this->table('banners_cabecalho_site');
            $banners_cabecalho->addColumn('name', 'string');
            $banners_cabecalho->addColumn('description', 'string');
            $banners_cabecalho->addColumn('img_featured', 'string');
            $banners_cabecalho->addColumn('img_mobile', 'string', ['null' => true]);
        $banners_cabecalho->create();

      $configuracoes = $this->table('configuracoes');
        $configuracoes->addColumn('name', 'string');
        $configuracoes->addColumn('value', 'integer');
      $configuracoes->create();

      $enderecos = $this->table('enderecos');
        $enderecos->addColumn('cep', 'string', ['null' => true]);
        $enderecos->addColumn('logradouro', 'string');
        $enderecos->addColumn('numero', 'string', ['null' => true]);
        $enderecos->addColumn('bairro', 'string', ['null' => true]);
        $enderecos->addColumn('cidade', 'string');
        $enderecos->addColumn('uf', 'integer');
        $enderecos->addColumn('latitude', 'string', ['null' => true]);
        $enderecos->addColumn('longitude', 'string', ['null' => true]);
        $enderecos->addTimestamps();
      $enderecos->create();

      $uf = $this->table('enderecos_user');
        $uf->addColumn('id_endereco', 'integer');
        $uf->addColumn('id_user', 'integer');
        $uf->addColumn('complemento', 'string', ['null' => true]);
        $uf->addTimestamps();
      $uf->create();

      $uf = $this->table('enderecos_uf');
        $uf->addColumn('uf', 'string');
        $uf->addColumn('name', 'string');
        $uf->addTimestamps();
      $uf->create();

      $event_logs = $this->table('event_logs_admin_access');
        $event_logs->addColumn('id_event_log_type', 'integer');
        $event_logs->addColumn('id_admin_ancora', 'integer', ['null' => true]);
        $event_logs->addColumn('description', 'string');
        $event_logs->addTimestamps();
      $event_logs->create();

      $event_logs = $this->table('event_logs_admin_actions');
        $event_logs->addColumn('id_event_log_type', 'integer');
        $event_logs->addColumn('id_admin_ancora', 'integer', ['null' => true]);
        $event_logs->addColumn('id_objects', 'string', ['null' => true]);
        $event_logs->addColumn('description', 'string');
        $event_logs->addTimestamps();
      $event_logs->create();

      $event_logs = $this->table('event_logs_user_access');
        $event_logs->addColumn('id_event_log_type', 'integer');
        $event_logs->addColumn('id_user', 'integer', ['null' => true]);
        $event_logs->addColumn('description', 'string');
        $event_logs->addTimestamps();
      $event_logs->create();

      $event_logs = $this->table('event_logs_user_actions');
        $event_logs->addColumn('id_event_log_type', 'integer');
        $event_logs->addColumn('id_user', 'integer', ['null' => true]);
        $event_logs->addColumn('id_objects', 'string', ['null' => true]);
        $event_logs->addColumn('description', 'string');
        $event_logs->addTimestamps();
      $event_logs->create();

      $event_logs_type = $this->table('event_log_types_admin_access');
        $event_logs_type->addColumn('slug', 'string');
        $event_logs_type->addColumn('name', 'string');
        $event_logs_type->addColumn('description', 'string');
      $event_logs_type->create();

      $event_logs_type = $this->table('event_log_types_admin_actions');
        $event_logs_type->addColumn('slug', 'string');
        $event_logs_type->addColumn('name', 'string');
        $event_logs_type->addColumn('description', 'string');
      $event_logs_type->create();

      $event_logs_type = $this->table('event_log_types_user_access');
        $event_logs_type->addColumn('slug', 'string');
        $event_logs_type->addColumn('name', 'string');
        $event_logs_type->addColumn('description', 'string');
      $event_logs_type->create();

      $event_logs_type = $this->table('event_log_types_user_actions');
        $event_logs_type->addColumn('slug', 'string');
        $event_logs_type->addColumn('name', 'string');
        $event_logs_type->addColumn('description', 'string');
      $event_logs_type->create();

      $permissions = $this->table('permissions');
        $permissions->addColumn('resource', 'string');
        $permissions->addColumn('description', 'string');
        $permissions->addColumn('id_admin_ancora_type', 'integer', ['null' => true]);
        $permissions->addColumn('role_list', 'text', ['null' => true]);
        $permissions->addTimestamps();
        // $permissions->addForeignKey('role_id', 'roles', 'id', [
        //     'delete' => 'SET_NULL',
        //     'update' => 'NO_ACTION',
        // ]);
        $permissions->addIndex(['resource'], ['unique' => true]);
      $permissions->create();

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
        $posts->addColumn('slug', 'string');
        $posts->addColumn('status', 'integer');
        $posts->addColumn('deleted', 'boolean', ['default' => false]);
        $posts->addTimestamps();
      $posts->create();

      $relatorios = $this->table('relatorios');
        $relatorios->addColumn('name', 'string');
        $relatorios->addColumn('description', 'string');
        $relatorios->addColumn('slug', 'string');
      $relatorios->create();

      $roles = $this->table('roles');
        $roles->addColumn('name', 'string');
        $roles->addColumn('description', 'string');
        $roles->addColumn('access_level', 'integer');
        $roles->addTimestamps();
      $roles->create();

      $users = $this->table('users');
        $users->addColumn('email', 'string', ['null' => true]);
        $users->addColumn('name', 'string');
        $users->addColumn('slug', 'string', ['null' => true]);
        $users->addColumn('password', 'string');
        $users->addColumn('users_type', 'string', ['null' => true]);
        $users->addColumn('nascimento', 'date', ['null' => true]);
        $users->addColumn('is_cnpj', 'boolean', ['default' => false]);
        $users->addColumn('cpf', 'string', ['null' => true]);
        $users->addColumn('tel_numero', 'string', ['null' => true]);
        $users->addColumn('role_id', 'integer', ['null' => true]);
        $users->addColumn('recover_token', 'string', ['null' => true]);
        $users->addColumn('verification_token', 'string', ['null' => true]);
        $users->addColumn('active', 'boolean', ['default' => false]);
        $users->addColumn('session_id', 'string', ["null" => true]);
        $users->addColumn('deleted', 'boolean', ['default' => false]);
        $users->addColumn('deleted_at', 'timestamp', ['null' => true]);
        $users->addTimestamps();
        /*
        $users->addForeignKey('role_id', 'roles', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'NO_ACTION'
        ]);
        */
      $users->create();

      $users_type = $this->table('users_type');
        $users_type->addColumn('name', 'string');
        $users_type->addColumn('description', 'string');
        $users_type->addColumn('slug', 'string');
        $users_type->addColumn('deleted', 'boolean', ['null' => true, 'default' => false]);
      $users_type->create();
    }
}

<?php

use Phinx\Migration\AbstractMigration;

class BaseDataMigration extends AbstractMigration
{
    public function up()
    {
        $password_root = password_hash('aws934#$77', PASSWORD_DEFAULT);
        $password_admin = password_hash('ancora123', PASSWORD_DEFAULT);

        $admin_ancora = [
            [
                'name' => 'Super Usuário',
                'email' => 'superadmin@farol360.com.br',
                'slug' => 'superadmin',
                'password' => $password_root,
                'type' => 4,
                'ativo' => true
            ],
            [
                'name' => 'Administrador Geral',
                'email' => 'superadmin@farol360.com.br',
                'slug' => 'admin',
                'password' => $password_admin,
                'type' => 3,
                'ativo' => true
            ]
        ];

        $this->insert('admin_ancora', $admin_ancora);

        $uf = [
            [
                'name' => 'Acre',
                'uf' => 'AC'
            ],
            [
                'name' => 'Alagoas',
                'uf' => 'AL'
            ],
            [
                'name' => 'Amapá',
                'uf' => 'AP'
            ],
            [
                'name' => 'Amazonas',
                'uf' => 'AM'
            ],
            [
                'name' => 'Bahia',
                'uf' => 'BA'
            ],
            [
                'name' => 'Ceará',
                'uf' => 'CE'
            ],
            [
                'name' => 'Distrito Federal',
                'uf' => 'DF'
            ],
            [
                'name' => 'Espírito Santo',
                'uf' => 'ES'
            ],
            [
                'name' => 'Goiás',
                'uf' => 'GO'
            ],
            [
                'name' => 'Maranhão',
                'uf' => 'MA'
            ],
            [
                'name' => 'Mato Grosso',
                'uf' => 'MT'
            ],
            [
                'name' => 'Mato Grosso do Sul',
                'uf' => 'MS'
            ],
            [
                'name' => 'Minas Gerais',
                'uf' => 'MG'
            ],
            [
                'name' => 'Pará',
                'uf' => 'PA'
            ],
            [
                'name' => 'Paraíba',
                'uf' => 'PB'
            ],
            [
                'name' => 'Paraná',
                'uf' => 'PR'
            ],
            [
                'name' => 'Pernambuco',
                'uf' => 'PE'
            ],
            [
                'name' => 'Piauí',
                'uf' => 'PI'
            ],
            [
                'name' => 'Rio de Janeiro',
                'uf' => 'RJ'
            ],
            [
                'name' => 'Rio Grande do Norte',
                'uf' => 'RN'
            ],
            [
                'name' => 'Rio Grande do Sul',
                'uf' => 'RS'
            ],
            [
                'name' => 'Rondonia',
                'uf' => 'RO'
            ],
            [
                'name' => 'Roraima',
                'uf' => 'RR'
            ],
            [
                'name' => 'Santa Catarina',
                'uf' => 'SC'
            ],
            [
                'name' => 'São Paulo',
                'uf' => 'SP'
            ],
            [
                'name' => 'Sergipe',
                'uf' => 'SE'
            ],
            [
                'name' => 'Tocantins',
                'uf' => 'TO'
            ]
        ];

        $this->insert('enderecos_uf', $uf);

        $event_log_types_admin_access = [
          [
            'slug' => 'login_admin_ancora',
            'name' => 'Evento de Login',
            'description' => 'Evento de Login Administrativo Ancora.'
          ],
          [
            'slug' => 'logout_admin_ancora',
            'name' => 'Evento de Logout',
            'description' => 'Evento de Logout Administrativo Ancora.'
          ]
        ];

        $this->insert('event_log_types_admin_access', $event_log_types_admin_access);

        $event_log_types_user_access = [
          [
            'slug' => 'login_user',
            'name' => 'Evento de User',
            'description' => 'Evento de Login User.'
          ],
          [
            'slug' => 'logout_user',
            'name' => 'Evento de User',
            'description' => 'Evento de Logout User.'
          ]
        ];

        $this->insert('event_log_types_user_access', $event_log_types_user_access);

        $relatorios = [
          [
            'slug' => 'accesso_admin',
            'name' => 'Acessos Administrativos',
            'description' => 'Relatório de acessos de Adminstradores.'
          ],
          [
            'slug' => 'accesso_user',
            'name' => 'Acessos Usuario',
            'description' => 'Relatório de acessos de Usuários.'
          ],
          [
            'slug' => 'action_admin',
            'name' => 'Ações Administrador',
            'description' => 'Relatório de acessos de Usuários.'
          ],
          [
            'slug' => 'action_user',
            'name' => 'Ações Usuario',
            'description' => 'Relatório de acessos de Usuários.'
          ]
        ];

        $this->insert('relatorios', $relatorios);
        $roles = [
            [
                'id' => 1,
                'name' => 'guest',
                'description' => 'Visitante Público.',
                'access_level' => 0
            ],
            [
                'id' => 2,
                'name' => 'user',
                'description' => 'Usuário da Plataforma.',
                'access_level' => 500
            ]
        ];
        $this->insert('roles', $roles);

        $permissions = [
            [
                'resource' => '/',
                'description' => 'Página inicial',
                'id_admin_ancora_type' => 0,
                'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/create_captcha',
                'description' => 'criar captcha',
                'id_admin_ancora_type' => 0,
                'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/login',
                'description' => 'Sign in',
                'id_admin_ancora_type' => 0,
                'role_list' => '["1"]'
            ],
            [
                'resource' => '/logout',
                'description' => 'Sign out',
                'id_admin_ancora_type' => 0,
                'role_list' => '["2"]'
            ],
            [
                'resource' => '/registrar',
                'description' => 'Sign up',
                'id_admin_ancora_type' => 0,
                'role_list' => '["1"]'
            ],
            [
                'resource' => '/admin',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/clientes',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/tipos_de_cliente',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/clientes/:id',
                'description' => 'Ver usuário',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/tipos_de_cliente/:id',
                'description' => 'Ver tipo usuário',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/clientes/add',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/tipos_de_cliente/add',
                'description' => 'Página para cadastro de um tipo novo de cliente',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/clientes/add/verify_slug',
                'description' => 'Verificar slug',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/tipos_de_cliente/add/verify_slug',
                'description' => 'Verificar slug',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/clientes/edit/:id',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/tipos_de_cliente/edit/:id',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/clientes/edit/verify_slug',
                'description' => 'Verificar slug na hora de editar cliente',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/tipos_de_cliente/edit/verify_slug',
                'description' => 'Verificar slug na hora de editar cliente',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/clientes/delete/:id',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/tipos_de_cliente/delete/:id',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/clientes/update',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/tipos_de_cliente/update',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/login',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/logout',
                'description' => 'Página administrativa',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/admin/sobre',
                'description' => 'Página Sobre',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/permission',
                'description' => 'Ver permissões',
                'id_admin_ancora_type' => 4,
                // 'role_list' => '["3"]'
            ],
            [
                'resource' => '/admin/permission/add',
                'description' => 'Adicionar permissão',
                'id_admin_ancora_type' => 4,
                // 'role_list' => '["3"]'
            ],
            [
                'resource' => '/admin/permission/delete/:id',
                'description' => 'Apagar permissão',
                'id_admin_ancora_type' => 4,
                // 'role_list' => '["3"]'
            ],
            [
                'resource' => '/admin/permission/edit/:id',
                'description' => 'Editar permissão',
                'id_admin_ancora_type' => 4,
                // 'role_list' => '["3"]'
            ],
            [
                'resource' => '/admin/permission/update',
                'description' => 'Atualizar permissão',
                'id_admin_ancora_type' => 4,
                // 'role_list' => '["3"]'
            ],
            [
                'resource' => '/admin/posts',
                'description' => 'Lista de postagens',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/posts/:id',
                'description' => 'Postagem Especifica',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/posts/add',
                'description' => 'Adicionar um novo post',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/posts/delete/:id',
                'description' => 'Remover post',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/posts/edit/:id',
                'description' => 'Remover post',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/posts/update',
                'description' => 'Remover post',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/post_types',
                'description' => 'Lista de postagens',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/post_types/:id',
                'description' => 'Postagem Especifica',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/post_types/add',
                'description' => 'Adicionar um novo post',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/post_types/delete/:id',
                'description' => 'Remover post',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/post_types/edit/:id',
                'description' => 'Remover post',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/post_types/update',
                'description' => 'Remover post',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/role',
                'description' => 'Ver cargos',
                'id_admin_ancora_type' => 4,
                // 'role_list' => '["3"]'
            ],
            [
                'resource' => '/admin/relatorios',
                'description' => 'Ver cargos',
                'id_admin_ancora_type' => 1,
                // 'role_list' => '["3"]'
            ],
            [
                'resource' => '/admin/role/add',
                'description' => 'Adicionar cargo',
                'id_admin_ancora_type' => 4,
                // 'role_list' => '["3"]'
            ],
            [
                'resource' => '/admin/role/delete/:id',
                'description' => 'Apagar cargo',
                'id_admin_ancora_type' => 4,
                // 'role_list' => '["3"]'
            ],
            [
                'resource' => '/admin/role/edit/:id',
                'description' => 'Editar cargo',
                'id_admin_ancora_type' => 4,
                // 'role_list' => '["3"]'
            ],
            [
                'resource' => '/admin/role/update',
                'description' => 'Atualizar cargo',
                'id_admin_ancora_type' => 4,
                // 'role_list' => '["3"]'
            ],
            [
                'resource' => '/admin/user',
                'description' => 'Ver usuários',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/user/all',
                'description' => 'Ver todos os usuários',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/user/:id',
                'description' => 'Ver usuário',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/user/add',
                'description' => 'Adicionar usuário',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/user/add/verify_slug',
                'description' => 'Verificar slug',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/user/delete/:id',
                'description' => 'Apagar usuário',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/user/edit/:id',
                'description' => 'Editar usuário',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/user/edit/verify_slug',
                'description' => 'Verificar slug na hora de editar usuário',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/user/update',
                'description' => 'Atualizar usuário',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/admin/user/export',
                'description' => 'Exportar usuários',
                'id_admin_ancora_type' => 2,
                // 'role_list' => '["2","3"]'
            ],
            [
                'resource' => '/posts',
                'description' => 'postagens',
                'id_admin_ancora_type' => 0,
                'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/posts/:id',
                'description' => 'postagem',
                'id_admin_ancora_type' => 0,
                'role_list' => '["1","2"]'
            ],
            [
                'resource' => '/perfil',
                'description' => 'Ver perfil',
                'id_admin_ancora_type' => 0,
                'role_list' => '["2"]'
            ],
            [
                'resource' => '/perfil/recuperar',
                'description' => 'Recuperar conta',
                'id_admin_ancora_type' => 0,
                'role_list' => '["2"]'
            ],
            [
                'resource' => '/perfil/recuperar/token/:token',
                'description' => 'Recuperar conta',
                'id_admin_ancora_type' => 0,
                'role_list' => '["2"]'
            ],
            [
                'resource' => '/perfil/verificar/:token',
                'description' => 'Verificar conta',
                'id_admin_ancora_type' => 0,
                'role_list' => '["2"]'
            ]
        ];
        $this->insert('permissions', $permissions);

        $post_types = [
            [
                'id' => 1,
                'name' => 'Categoria Padrão',
                'description' => 'Favor alterar esta categoria nas configurações da plataforma.',
                'slug' => 'padrao',
                'status' => 1
            ]
        ];
        $this->insert('post_types', $post_types);

        $posts = [
            [
                'id' => 1,
                'name' => 'Postagem Padrão.',
                'description' => 'Favor alterar esta categoria nas configurações da plataforma.',
                'img_featured' => '',
                'id_post_type' => 1,
                'status' => 1,
                'trash' => 0
            ],
            [
                'id' => 2,
                'name' => 'Olá Mundo.',
                'description' => 'Favor alterar esta categoria nas configurações da plataforma.',
                'img_featured' => '',
                'id_post_type' => 1,
                'status' => 1,
                'trash' => 0
            ],
            [
                'id' => 3,
                'name' => 'Postagem número 3.',
                'description' => 'Favor alterar esta categoria nas configurações da plataforma.',
                'img_featured' => '',
                'id_post_type' => 1,
                'status' => 1,
                'trash' => 0
            ]

        ];
        $this->insert('posts', $posts);

    }

    public function down()
    {
        $this->execute('DELETE FROM roles');
        $this->execute('DELETE FROM permissions');
        $this->execute('DELETE FROM users');
        $this->execute('DELETE FROM post_types');
        $this->execute('DELETE FROM posts');
        $this->execute('DELETE FROM admin_ancora');
        $this->execute('DELETE FROM enderecos_uf');
    }
}

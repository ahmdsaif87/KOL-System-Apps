<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Articles extends CI_Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `articles` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(200) NOT NULL,
                `slug` VARCHAR(220) NOT NULL,
                `excerpt` VARCHAR(500) DEFAULT NULL,
                `content` LONGTEXT NOT NULL,
                `cover_image` VARCHAR(500) DEFAULT NULL,
                `category` VARCHAR(100) DEFAULT NULL,
                `tags` VARCHAR(255) DEFAULT NULL,
                `author_id` BIGINT UNSIGNED DEFAULT NULL,
                `author_name` VARCHAR(150) DEFAULT NULL,
                `status` ENUM('DRAFT','PUBLISHED','ARCHIVED') NOT NULL DEFAULT 'DRAFT',
                `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
                `views` INT UNSIGNED NOT NULL DEFAULT 0,
                `meta_title` VARCHAR(200) DEFAULT NULL,
                `meta_description` VARCHAR(320) DEFAULT NULL,
                `published_at` DATETIME DEFAULT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_articles_slug` (`slug`),
                KEY `idx_articles_status_published` (`status`, `published_at`),
                KEY `idx_articles_category` (`category`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $permissions = [
            'articles_list'    => 'List Articles',
            'articles_view'    => 'View Article',
            'articles_add'     => 'Add Article',
            'articles_update'  => 'Update Article',
            'articles_delete'  => 'Delete Article',
            'articles_publish' => 'Publish Article',
        ];

        $read_permissions = ['articles_list', 'articles_view'];

        foreach ($permissions as $name => $definition) {
            $perm = $this->db->get_where('aauth_perms', ['name' => $name])->row();

            if ($perm) {
                $perm_id = $perm->id;
            } else {
                $this->db->insert('aauth_perms', [
                    'name'       => $name,
                    'definition' => $definition,
                ]);
                $perm_id = $this->db->insert_id();
            }

            $groups = in_array($name, $read_permissions, true) ? [1, 5, 6] : [1, 6];

            foreach ($groups as $group_id) {
                $exists = $this->db->get_where('aauth_perm_to_group', [
                    'perm_id'  => $perm_id,
                    'group_id' => $group_id,
                ])->row();

                if (!$exists) {
                    $this->db->insert('aauth_perm_to_group', [
                        'perm_id'  => $perm_id,
                        'group_id' => $group_id,
                    ]);
                }
            }
        }

        $parent = $this->db->get_where('menu', [
            'label'  => 'Konten',
            'parent' => 0,
            'app_id' => 1,
        ])->row();

        if ($parent) {
            $parent_id = $parent->id;
        } else {
            $this->db->insert('menu', [
                'label'        => 'Konten',
                'type'         => 'menu',
                'icon_color'   => '',
                'link'         => '#',
                'sort'         => 60,
                'parent'       => 0,
                'icon'         => 'fa-newspaper-o',
                'menu_type_id' => 1,
                'active'       => 1,
                'app_id'       => 1,
            ]);
            $parent_id = $this->db->insert_id();
        }

        $child = $this->db->get_where('menu', [
            'link'   => '{admin_url}/articles',
            'app_id' => 1,
        ])->row();

        if (!$child) {
            $this->db->insert('menu', [
                'label'        => 'Artikel',
                'type'         => 'menu',
                'icon_color'   => '',
                'link'         => '{admin_url}/articles',
                'sort'         => 61,
                'parent'       => $parent_id,
                'icon'         => 'fa-file-text-o',
                'menu_type_id' => 1,
                'active'       => 1,
                'app_id'       => 1,
            ]);
        }
    }

    public function down()
    {
        $this->db->where('link', '{admin_url}/articles')->where('app_id', 1)->delete('menu');
        $this->db->where('label', 'Konten')->where('parent', 0)->where('app_id', 1)->delete('menu');

        $names = [
            'articles_list',
            'articles_view',
            'articles_add',
            'articles_update',
            'articles_delete',
            'articles_publish',
        ];

        $perms = $this->db->where_in('name', $names)->get('aauth_perms')->result();

        foreach ($perms as $perm) {
            $this->db->where('perm_id', $perm->id)->delete('aauth_perm_to_group');
            $this->db->where('id', $perm->id)->delete('aauth_perms');
        }

        $this->db->query('DROP TABLE IF EXISTS `articles`;');
    }
}

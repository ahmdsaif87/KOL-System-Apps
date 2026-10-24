<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_app_id_to_menu extends CI_Migration {

    public function up()
    {
        $fields = array(
            'app_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => FALSE
            )
        );
        $this->dbforge->add_column('menu', $fields);

        // Add index on app_id
        $this->db->query("ALTER TABLE `menu` ADD INDEX `idx_menu_app_id` (`app_id`)");

        // Update all existing menus to app_id = 1 (KOL-System)
        $this->db->query("UPDATE `menu` SET `app_id` = 1");
    }

    public function down()
    {
        // Drop index
        $this->db->query("ALTER TABLE `menu` DROP INDEX `idx_menu_app_id`");
        
        $this->dbforge->drop_column('menu', 'app_id');
    }
}

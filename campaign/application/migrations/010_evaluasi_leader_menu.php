<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Evaluasi_leader_menu extends CI_Migration {

    public function up()
    {
        // T1: Update id 155: link → administrator/internal_evaluation/staff, label → Evaluasi Staff Operasional.
        $this->db->where('id', 155);
        $this->db->update('menu', [
            'link' => 'administrator/internal_evaluation/staff',
            'label' => 'Evaluasi Staff Operasional'
        ]);

        // Cari urutan sort dari menu id 155 untuk menempatkan menu leader setelahnya
        $menu_155 = $this->db->get_where('menu', ['id' => 155])->row();
        $sort = $menu_155 ? $menu_155->sort + 1 : 1;

        // T1: Insert: Evaluasi Leader → administrator/internal_evaluation/leader, parent=110, icon fa-user-circle, sort setelah 155.
        $this->db->insert('menu', [
            'menu_type_id' => 1,
            'label' => 'Evaluasi Leader',
            'link' => 'administrator/internal_evaluation/leader',
            'icon_color' => '',
            'icon' => 'fa-user-circle',
            'parent_id' => 110,
            'sort' => $sort,
            'active' => 1
        ]);
    }

    public function down()
    {
        // Kembalikan menu id 155 ke kondisi semula
        $this->db->where('id', 155);
        $this->db->update('menu', [
            'link' => 'administrator/internal_evaluation',
            'label' => 'Internal Evaluation'
        ]);

        // Hapus menu Evaluasi Leader
        $this->db->where('link', 'administrator/internal_evaluation/leader');
        $this->db->delete('menu');
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_kol_shipments extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'kol_shipments';
    private $field_search   = ['campaign_kol_id', 'recipient_name', 'shipping_address', 'courier', 'tracking_no', 'shipping_fee', 'status', 'shipped_at', 'delivered_at', 'notes'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "kol_shipments.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "kol_shipments.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "kol_shipments.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "kol_shipments.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "kol_shipments.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "kol_shipments.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('kol_shipments.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('campaign_kols', 'campaign_kols.id = kol_shipments.campaign_kol_id', 'LEFT');
        $this->db->join('kols', 'kols.id = campaign_kols.kol_id', 'LEFT');
        $this->db->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'LEFT');
        $this->db->join('aauth_users', 'aauth_users.id = kol_shipments.created_by', 'LEFT');
        
        $this->db->select('kol_shipments.*, campaign_kols.kol_id as campaign_kols_kol_id, kols.name as kol_name, campaigns.name as campaign_name, aauth_users.full_name as aauth_users_full_name');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    public function log_stock_change($shipment_id, $old_status, $new_status, $user_id = null)
    {
        $out_statuses = ['SHIPPED', 'DELIVERED'];
        $old_is_out = in_array($old_status, $out_statuses);
        $new_is_out = in_array($new_status, $out_statuses);

        $items = $this->db->get_where('kol_shipment_items', ['shipment_id' => $shipment_id])->result();
        if (empty($items)) return;

        $stock_change = 0;
        $change_type = '';
        $notes = '';

        // Goods went OUT (PENDING/PROCESSING/RETURNED -> SHIPPED/DELIVERED)
        if (!$old_is_out && $new_is_out) {
            $stock_change = -1;
            $change_type = 'out';
            $notes = 'Shipment #' . $shipment_id . ' dikirim';
        }
        // Goods came BACK from shipment (SHIPPED/DELIVERED -> PENDING/PROCESSING/RETURNED)
        else if ($old_is_out && !$new_is_out) {
            $stock_change = 1;
            $change_type = 'in';
            $notes = $new_status === 'RETURNED' ? 'Return diterima shipment #' . $shipment_id : 'Batalkan kirim shipment #' . $shipment_id;
        }

        if ($stock_change === 0) return;

        if (!$user_id && isset($this->aauth)) {
            $user_id = $this->aauth->get_user_id();
        }

        foreach ($items as $item) {
            $product = $this->db->get_where('products', ['id' => $item->product_id])->row();
            $stock_before = $product ? (int) $product->stock : 0;
            $qty = (int) $item->qty;
            $actual_change = $stock_change * $qty;
            $stock_after = max(0, $stock_before + $actual_change);

            $this->db->set('stock', "GREATEST(0, stock " . ($actual_change >= 0 ? "+ " . $actual_change : "- " . abs($actual_change)) . ")", FALSE);
            $this->db->where('id', $item->product_id);
            $this->db->update('products');

            $this->db->insert('stock_history', [
                'product_id'   => $item->product_id,
                'shipment_id'  => $shipment_id,
                'change_type'  => $change_type,
                'qty_change'   => abs($actual_change),
                'stock_before' => $stock_before,
                'stock_after'  => $stock_after,
                'notes'        => $notes,
                'created_by'   => $user_id ? $user_id : 1,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        }
    }

}

/* End of file Model_kol_shipments.php */
/* Location: ./application/models/Model_kol_shipments.php */
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_stock_history extends MY_Model {

    private $primary_key  = 'id';
    private $table_name   = 'stock_history';
    private $field_search = ['change_type', 'notes'];

    public function __construct()
    {
        $config = [
            'primary_key'  => $this->primary_key,
            'table_name'   => $this->table_name,
            'field_search' => $this->field_search,
        ];
        parent::__construct($config);
    }

    public function count_all($q = null, $field = null, $type = null)
    {
        $this->db->select('stock_history.*, products.name as product_name, products.sku as product_sku, kol_shipments.id as shipment_id_ref');
        $this->db->join('products', 'products.id = stock_history.product_id', 'left');
        $this->db->join('kol_shipments', 'kol_shipments.id = stock_history.shipment_id', 'left');

        if (!empty($type)) {
            $this->db->where('stock_history.change_type', $type);
        }

        if (!empty($q)) {
            $q = $this->scurity($q);
            if (!empty($field)) {
                $this->db->where("stock_history.{$field} LIKE '%{$q}%'");
            } else {
                $this->db->group_start();
                $this->db->like('products.name', $q);
                $this->db->or_like('products.sku', $q);
                $this->db->or_like('stock_history.notes', $q);
                $this->db->group_end();
            }
        }

        return $this->db->count_all_results($this->table_name);
    }

    public function get($q = null, $field = null, $type = null, $limit = 0, $offset = 0)
    {
        $this->db->select('stock_history.*, products.name as product_name, products.sku as product_sku');
        $this->db->join('products', 'products.id = stock_history.product_id', 'left');
        $this->db->join('kol_shipments', 'kol_shipments.id = stock_history.shipment_id', 'left');

        if (!empty($type)) {
            $this->db->where('stock_history.change_type', $type);
        }

        if (!empty($q)) {
            $q = $this->scurity($q);
            if (!empty($field)) {
                $this->db->where("stock_history.{$field} LIKE '%{$q}%'");
            } else {
                $this->db->group_start();
                $this->db->like('products.name', $q);
                $this->db->or_like('products.sku', $q);
                $this->db->or_like('stock_history.notes', $q);
                $this->db->group_end();
            }
        }

        $this->db->order_by('stock_history.created_at', 'DESC');

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get($this->table_name)->result();
    }

    public function find($id = NULL, $select_field = [])
    {
        $this->db->select('stock_history.*, products.name as product_name, products.sku as product_sku');
        $this->db->join('products', 'products.id = stock_history.product_id', 'left');
        $this->db->join('kol_shipments', 'kol_shipments.id = stock_history.shipment_id', 'left');
        return $this->db->where($this->table_name . '.id', $id)->get($this->table_name)->row();
    }
}

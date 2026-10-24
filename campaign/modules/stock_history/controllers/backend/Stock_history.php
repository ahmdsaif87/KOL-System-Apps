<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_history extends Admin {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_stock_history');
        $this->lang->load('web_lang', $this->current_lang);
    }

    public function index($offset = 0)
    {
        $this->is_allowed('stock_history_list');

        $filter = $this->input->get('q');
        $field  = $this->input->get('f');
        $type   = $this->input->get('type');

        $this->data['stock_historys'] = $this->model_stock_history->get($filter, $field, $type, $this->limit_page, $offset);
        $this->data['stock_history_counts'] = $this->model_stock_history->count_all($filter, $field, $type);

        $config = [
            'base_url'    => 'administrator/stock_history/index/',
            'total_rows'  => $this->data['stock_history_counts'],
            'per_page'    => $this->limit_page,
            'uri_segment' => 4,
        ];

        $this->data['pagination'] = $this->pagination($config);

        $this->template->title('Stock History');
        $this->render('backend/standart/administrator/stock_history/stock_history_list', $this->data);
    }

    public function view($id)
    {
        $this->is_allowed('stock_history_list');
        $this->data['stock_history'] = $this->model_stock_history->find($id);
        $this->template->title('Stock History Detail');
        $this->render('backend/standart/administrator/stock_history/stock_history_view', $this->data);
    }
}

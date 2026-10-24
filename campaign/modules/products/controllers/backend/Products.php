<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Products Controller
*| --------------------------------------------------------------------------
*| Products site
*|
*/
class Products extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_products');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Productss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('products_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['productss'] = $this->model_products->get($filter, $field, $this->limit_page, $offset);
		$this->data['products_counts'] = $this->model_products->count_all($filter, $field);

		// Product Summary / Mini Dashboard Metrics
		$stock_res = $this->db->select_sum('stock')->get('products')->row();
		$this->data['product_summary'] = [
			'total_products' => (int)$this->db->count_all('products'),
			'total_stock' => (int)($stock_res ? $stock_res->stock : 0),
			'active_products' => (int)$this->db->where('is_active', 1)->count_all_results('products'),
			'low_stock_products' => (int)$this->db->where('stock <=', 5)->count_all_results('products')
		];

		$config = [
			'base_url'     => 'administrator/products/index/',
			'total_rows'   => $this->model_products->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Products List');
		$this->render('backend/standart/administrator/products/products_list', $this->data);
	}
	
	/**
	* Add new productss
	*
	*/
	public function add()
	{
		$this->is_allowed('products_add');

		$this->template->title('Products New');
		$this->render('backend/standart/administrator/products/products_add', $this->data);
	}

	/**
	* Add New Productss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('products_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('sku', 'SKU', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('stock', 'Stock', 'trim|required');
		$this->form_validation->set_rules('price', 'Price', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('is_active', 'Status', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'sku' => $this->input->post('sku'),
				'name' => $this->input->post('name'),
				'category' => $this->input->post('category'),
				'stock' => $this->input->post('stock'),
				'price' => $this->input->post('price'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_products = $this->model_products->store($save_data);
            
			if ($save_products) {
				if ((int)$save_data['stock'] > 0) {
					$this->db->insert('stock_history', [
						'product_id' => $save_products,
						'shipment_id' => null,
						'change_type' => 'in',
						'qty_change' => (int)$save_data['stock'],
						'stock_before' => 0,
						'stock_after' => (int)$save_data['stock'],
						'notes' => 'Stok Awal',
						'created_by' => get_user_data('id') ?: 1,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}

				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_products;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/products/edit/' . $save_products, 'Edit Products'),
						anchor('administrator/products', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/products/edit/' . $save_products, 'Edit Products')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/products');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/products');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Productss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('products_update');

		$this->data['products'] = $this->model_products->find($id);

		$this->template->title('Products Update');
		$this->render('backend/standart/administrator/products/products_update', $this->data);
	}

	/**
	* Update Productss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('products_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('sku', 'SKU', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('category', 'Category', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('stock', 'Stock', 'trim|required');
		$this->form_validation->set_rules('price', 'Price', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('is_active', 'Status', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'sku' => $this->input->post('sku'),
				'name' => $this->input->post('name'),
				'category' => $this->input->post('category'),
				'stock' => $this->input->post('stock'),
				'price' => $this->input->post('price'),
				'is_active' => $this->input->post('is_active'),
			];

			$old_product = $this->model_products->find($id);
			$old_stock = $old_product ? (int)$old_product->stock : 0;
			$new_stock = (int)$save_data['stock'];
			$qty_change = $new_stock - $old_stock;

			$save_products = $this->model_products->change($id, $save_data);

			if ($save_products) {
				if ($qty_change != 0) {
					$this->db->insert('stock_history', [
						'product_id' => $id,
						'shipment_id' => null,
						'change_type' => $qty_change > 0 ? 'in' : 'out',
						'qty_change' => $qty_change,
						'stock_before' => $old_stock,
						'stock_after' => $new_stock,
						'notes' => 'Penyesuaian Stok Manual',
						'created_by' => get_user_data('id') ?: 1,
						'created_at' => date('Y-m-d H:i:s')
					]);
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/products', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/products');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/products');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Productss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('products_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'products'), 'success');
        } else {
            set_message(cclang('error_delete', 'products'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Productss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('products_view');

		$this->data['products'] = $this->model_products->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Products Detail');
		$this->render('backend/standart/administrator/products/products_view', $this->data);
	}
	
	/**
	* delete Productss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$products = $this->model_products->find($id);

		
		
		return $this->model_products->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('products_export');

		$this->model_products->export('products', 'products');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('products_export');

		$this->model_products->pdf('products', 'products');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('products_export');

		$table = $title = 'products';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_products->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}
}


/* End of file products.php */
/* Location: ./application/controllers/administrator/Products.php */
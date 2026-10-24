<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kol Shipment Items Controller
*| --------------------------------------------------------------------------
*| Kol Shipment Items site
*|
*/
class Kol_shipment_items extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kol_shipment_items');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kol Shipment Itemss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kol_shipment_items_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['kol_shipment_itemss'] = $this->model_kol_shipment_items->get($filter, $field, $this->limit_page, $offset);
		$this->data['kol_shipment_items_counts'] = $this->model_kol_shipment_items->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/kol_shipment_items/index/',
			'total_rows'   => $this->model_kol_shipment_items->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kol Shipment Items List');
		$this->render('backend/standart/administrator/kol_shipment_items/kol_shipment_items_list', $this->data);
	}
	
	/**
	* Add new kol_shipment_itemss
	*
	*/
	public function add()
	{
		$this->is_allowed('kol_shipment_items_add');

		$this->template->title('Kol Shipment Items New');
		$this->render('backend/standart/administrator/kol_shipment_items/kol_shipment_items_add', $this->data);
	}

	/**
	* Add New Kol Shipment Itemss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kol_shipment_items_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('shipment_id', 'Shipment Id', 'trim|required');
		$this->form_validation->set_rules('product_id', 'Product Id', 'trim|required');
		$this->form_validation->set_rules('qty', 'Qty', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'shipment_id' => $this->input->post('shipment_id'),
				'product_id' => $this->input->post('product_id'),
				'qty' => $this->input->post('qty'),
			];

			
			$save_kol_shipment_items = $this->model_kol_shipment_items->store($save_data);
            
			if ($save_kol_shipment_items) {
				// CUSTOM LOGIC: Auto-deduct stock
				$qty = (int)$this->input->post('qty');
				$product_id = $this->input->post('product_id');
				if ($qty > 0) {
					$this->db->set('stock', 'stock - ' . $qty, FALSE);
					$this->db->where('id', $product_id);
					$this->db->update('products');
				}
				
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kol_shipment_items;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kol_shipment_items/edit/' . $save_kol_shipment_items, 'Edit Kol Shipment Items'),
						anchor('administrator/kol_shipment_items', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kol_shipment_items/edit/' . $save_kol_shipment_items, 'Edit Kol Shipment Items')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kol_shipment_items');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kol_shipment_items');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Kol Shipment Itemss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kol_shipment_items_update');

		$this->data['kol_shipment_items'] = $this->model_kol_shipment_items->find($id);

		$this->template->title('Kol Shipment Items Update');
		$this->render('backend/standart/administrator/kol_shipment_items/kol_shipment_items_update', $this->data);
	}

	/**
	* Update Kol Shipment Itemss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kol_shipment_items_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('shipment_id', 'Shipment Id', 'trim|required');
		$this->form_validation->set_rules('product_id', 'Product Id', 'trim|required');
		$this->form_validation->set_rules('qty', 'Qty', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'shipment_id' => $this->input->post('shipment_id'),
				'product_id' => $this->input->post('product_id'),
				'qty' => $this->input->post('qty'),
			];

			// CUSTOM LOGIC: Adjust stock difference
			$old_item = $this->model_kol_shipment_items->find($id);
			
			$save_kol_shipment_items = $this->model_kol_shipment_items->change($id, $save_data);

			if ($save_kol_shipment_items) {
				if ($old_item) {
					$old_qty = (int)$old_item->qty;
					$new_qty = (int)$this->input->post('qty');
					
					// If product is the same, just adjust the difference
					if ($old_item->product_id == $this->input->post('product_id')) {
						$diff = $new_qty - $old_qty;
						if ($diff != 0) {
							$this->db->set('stock', 'stock - ' . $diff, FALSE);
							$this->db->where('id', $old_item->product_id);
							$this->db->update('products');
						}
					} else {
						// Product changed: refund old, deduct new
						$this->db->set('stock', 'stock + ' . $old_qty, FALSE);
						$this->db->where('id', $old_item->product_id);
						$this->db->update('products');
						
						$this->db->set('stock', 'stock - ' . $new_qty, FALSE);
						$this->db->where('id', $this->input->post('product_id'));
						$this->db->update('products');
					}
				}
				
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kol_shipment_items', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kol_shipment_items');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kol_shipment_items');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Kol Shipment Itemss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kol_shipment_items_delete');

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
            set_message(cclang('has_been_deleted', 'kol_shipment_items'), 'success');
        } else {
            set_message(cclang('error_delete', 'kol_shipment_items'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kol Shipment Itemss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kol_shipment_items_view');

		$this->data['kol_shipment_items'] = $this->model_kol_shipment_items->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kol Shipment Items Detail');
		$this->render('backend/standart/administrator/kol_shipment_items/kol_shipment_items_view', $this->data);
	}
	
	/**
	* delete Kol Shipment Itemss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kol_shipment_items = $this->model_kol_shipment_items->find($id);

		if ($kol_shipment_items) {
			$qty = (int)$kol_shipment_items->qty;
			$product_id = $kol_shipment_items->product_id;
			if ($qty > 0) {
				$this->db->set('stock', 'stock + ' . $qty, FALSE);
				$this->db->where('id', $product_id);
				$this->db->update('products');
			}
		}
		
		return $this->model_kol_shipment_items->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kol_shipment_items_export');

		$this->model_kol_shipment_items->export('kol_shipment_items', 'kol_shipment_items');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kol_shipment_items_export');

		$this->model_kol_shipment_items->pdf('kol_shipment_items', 'kol_shipment_items');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kol_shipment_items_export');

		$table = $title = 'kol_shipment_items';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kol_shipment_items->find($id);
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


/* End of file kol_shipment_items.php */
/* Location: ./application/controllers/administrator/Kol Shipment Items.php */
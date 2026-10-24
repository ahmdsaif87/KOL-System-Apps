<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kol Shipments Controller
*| --------------------------------------------------------------------------
*| Kol Shipments site
*|
*/
class Kol_shipments extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kol_shipments');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kol Shipmentss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kol_shipments_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['kol_shipmentss'] = $this->model_kol_shipments->get($filter, $field, $this->limit_page, $offset);
		$this->data['kol_shipments_counts'] = $this->model_kol_shipments->count_all($filter, $field);

		// Load specific shipped products for each shipment
		$shipment_ids = !empty($this->data['kol_shipmentss']) ? array_map(function($s){ return $s->id; }, $this->data['kol_shipmentss']) : [];
		$items_by_shipment = [];
		if (!empty($shipment_ids)) {
			$items = $this->db->select('kol_shipment_items.*, products.name as product_name, products.sku as product_sku, products.price as product_price, products.category as product_category')
				->from('kol_shipment_items')
				->join('products', 'products.id = kol_shipment_items.product_id', 'LEFT')
				->where_in('kol_shipment_items.shipment_id', $shipment_ids)
				->get()->result();
			foreach ($items as $item) {
				$items_by_shipment[$item->shipment_id][] = $item;
			}
		}
		$this->data['items_by_shipment'] = $items_by_shipment;

		$config = [
			'base_url'     => 'administrator/kol_shipments/index/',
			'total_rows'   => $this->model_kol_shipments->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kol Shipments List');
		$this->render('backend/standart/administrator/kol_shipments/kol_shipments_list', $this->data);
	}
	
	/**
	* Add new kol_shipmentss
	*
	*/
	public function add()
	{
		$this->is_allowed('kol_shipments_add');

		$this->template->title('Kol Shipments New');
		$this->render('backend/standart/administrator/kol_shipments/kol_shipments_add', $this->data);
	}

	/**
	* Add New Kol Shipmentss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kol_shipments_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('campaign_kol_id', 'Campaign Kol Id', 'trim|required');
		$this->form_validation->set_rules('recipient_name', 'Recipient Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('recipient_phone', 'Recipient Phone', 'trim|max_length[30]');
		$this->form_validation->set_rules('shipping_address', 'Shipping Address', 'trim|required');
		$this->form_validation->set_rules('courier', 'Courier', 'trim|max_length[50]');
		$this->form_validation->set_rules('tracking_no', 'Tracking No', 'trim|max_length[100]');
		$this->form_validation->set_rules('shipping_fee', 'Shipping Fee', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');


		if ($this->form_validation->run()) {

			$save_data = [
				'campaign_kol_id' => $this->input->post('campaign_kol_id'),
				'recipient_name' => $this->input->post('recipient_name'),
				'shipping_address' => $this->input->post('shipping_address'),
				'courier' => $this->input->post('courier'),
				'tracking_no' => $this->input->post('tracking_no'),
				'shipping_fee' => $this->input->post('shipping_fee'),
				'status' => $this->input->post('status'),
				'shipped_at' => $this->input->post('shipped_at'),
				'delivered_at' => $this->input->post('delivered_at'),
				'notes' => $this->input->post('notes'),
			];
			if ($this->db->field_exists('recipient_phone', 'kol_shipments')) {
				$save_data['recipient_phone'] = $this->input->post('recipient_phone');
			}


			$save_kol_shipments = $this->model_kol_shipments->store($save_data);
            

			if ($save_kol_shipments) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kol_shipments;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kol_shipments/edit/' . $save_kol_shipments, 'Edit Kol Shipments'),
						anchor('administrator/kol_shipments', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kol_shipments/edit/' . $save_kol_shipments, 'Edit Kol Shipments')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kol_shipments');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kol_shipments');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Kol Shipmentss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kol_shipments_update');

		$this->data['kol_shipments'] = $this->model_kol_shipments->find($id);

		$this->template->title('Kol Shipments Update');
		$this->render('backend/standart/administrator/kol_shipments/kol_shipments_update', $this->data);
	}

	/**
	* Update Kol Shipmentss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kol_shipments_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('campaign_kol_id', 'Campaign Kol Id', 'trim|required');
		$this->form_validation->set_rules('recipient_name', 'Recipient Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('recipient_phone', 'Recipient Phone', 'trim|max_length[30]');
		$this->form_validation->set_rules('shipping_address', 'Shipping Address', 'trim|required');
		$this->form_validation->set_rules('courier', 'Courier', 'trim|max_length[50]');
		$this->form_validation->set_rules('tracking_no', 'Tracking No', 'trim|max_length[100]');
		$this->form_validation->set_rules('shipping_fee', 'Shipping Fee', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');

		if ($this->form_validation->run()) {

			$save_data = [
				'campaign_kol_id' => $this->input->post('campaign_kol_id'),
				'recipient_name' => $this->input->post('recipient_name'),
				'shipping_address' => $this->input->post('shipping_address'),
				'courier' => $this->input->post('courier'),
				'tracking_no' => $this->input->post('tracking_no'),
				'shipping_fee' => $this->input->post('shipping_fee'),
				'status' => $this->input->post('status'),
				'shipped_at' => $this->input->post('shipped_at'),
				'delivered_at' => $this->input->post('delivered_at'),
				'notes' => $this->input->post('notes'),
			];
			if ($this->db->field_exists('recipient_phone', 'kol_shipments')) {
				$save_data['recipient_phone'] = $this->input->post('recipient_phone');
			}

			$old_shipment = $this->model_kol_shipments->find($id);
			
			$save_kol_shipments = $this->model_kol_shipments->change($id, $save_data);

			if ($save_kol_shipments) {
				$new_status = $this->input->post('status');
				if ($old_shipment && $old_shipment->status !== $new_status) {
					$this->model_kol_shipments->log_stock_change($id, $old_shipment->status, $new_status);
				}
				
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kol_shipments', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kol_shipments');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kol_shipments');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Kol Shipmentss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kol_shipments_delete');

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
            set_message(cclang('has_been_deleted', 'kol_shipments'), 'success');
        } else {
            set_message(cclang('error_delete', 'kol_shipments'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kol Shipmentss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kol_shipments_view');

		$this->data['kol_shipments'] = $this->model_kol_shipments->join_avaiable()->filter_avaiable()->find($id);

		// Load specific shipped products for this shipment
		$this->data['shipment_items'] = $this->db->select('kol_shipment_items.*, products.name as product_name, products.sku as product_sku, products.price as product_price, products.category as product_category')
			->from('kol_shipment_items')
			->join('products', 'products.id = kol_shipment_items.product_id', 'LEFT')
			->where('kol_shipment_items.shipment_id', $id)
			->get()->result();

		$this->template->title('Kol Shipments Detail');
		$this->render('backend/standart/administrator/kol_shipments/kol_shipments_view', $this->data);
	}
	
	/**
	* delete Kol Shipmentss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kol_shipments = $this->model_kol_shipments->find($id);

		
		
		return $this->model_kol_shipments->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kol_shipments_export');

		$this->model_kol_shipments->export('kol_shipments', 'kol_shipments');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kol_shipments_export');

		$this->model_kol_shipments->pdf('kol_shipments', 'kol_shipments');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kol_shipments_export');

		$table = $title = 'kol_shipments';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kol_shipments->find($id);
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

	/**
	* Quick update status via AJAX dropdown
	*/
	public function update_status()
	{
		header('Content-Type: application/json');
		if (!$this->is_allowed('kol_shipments_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access'),
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$id = (int)$this->input->post('id');
		$new_status = $this->input->post('status');
		$valid_statuses = ['PENDING', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'RETURN_PENDING', 'RETURNED'];

		if (!$id || !in_array($new_status, $valid_statuses)) {
			echo json_encode([
				'success' => false,
				'message' => 'Status atau ID pengiriman tidak valid.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$old_shipment = $this->model_kol_shipments->find($id);
		if (!$old_shipment) {
			echo json_encode([
				'success' => false,
				'message' => 'Data pengiriman tidak ditemukan.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$update_data = [
			'status' => $new_status,
			'updated_at' => date('Y-m-d H:i:s')
		];

		if (in_array($new_status, ['PENDING', 'PROCESSING'])) {
			$update_data['shipped_at'] = NULL;
			$update_data['delivered_at'] = NULL;
		} else if ($new_status == 'SHIPPED') {
			if (empty($old_shipment->shipped_at)) {
				$update_data['shipped_at'] = date('Y-m-d H:i:s');
			}
			$update_data['delivered_at'] = NULL;
		} else if ($new_status == 'DELIVERED') {
			if (empty($old_shipment->shipped_at)) {
				$update_data['shipped_at'] = date('Y-m-d H:i:s');
			}
			if (empty($old_shipment->delivered_at)) {
				$update_data['delivered_at'] = date('Y-m-d H:i:s');
			}
		}

		try {
			$this->db->trans_start();

			$this->db->where('id', $id)->update('kol_shipments', $update_data);
			if ($old_shipment->status !== $new_status) {
				$this->model_kol_shipments->log_stock_change($id, $old_shipment->status, $new_status);
			}

			$this->db->trans_complete();

			if ($this->db->trans_status() === TRUE) {
				echo json_encode([
					'success' => true,
					'message' => 'Status pengiriman berhasil diperbarui menjadi ' . $new_status,
					'status' => $new_status,
					'shipped_at' => array_key_exists('shipped_at', $update_data) ? ($update_data['shipped_at'] ?? '') : ($old_shipment->shipped_at ?? ''),
					'delivered_at' => array_key_exists('delivered_at', $update_data) ? ($update_data['delivered_at'] ?? '') : ($old_shipment->delivered_at ?? ''),
					'csrfHash' => $this->security->get_csrf_hash()
				]);
			} else {
				$db_err = $this->db->error();
				echo json_encode([
					'success' => false,
					'message' => 'Gagal memperbarui status: ' . ($db_err['message'] ?? 'Database error'),
					'csrfHash' => $this->security->get_csrf_hash()
				]);
			}
		} catch (Throwable $e) {
			echo json_encode([
				'success' => false,
				'message' => 'Exception: ' . $e->getMessage(),
				'csrfHash' => $this->security->get_csrf_hash()
			]);
		}
	}

	/**
	* Bulk update status for multiple shipments
	*/
	public function bulk_update_status()
	{
		header('Content-Type: application/json');
		if (!$this->is_allowed('kol_shipments_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access'),
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$ids = $this->input->post('id');
		$new_status = $this->input->post('status');
		$valid_statuses = ['PENDING', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'RETURN_PENDING', 'RETURNED'];

		if (empty($ids) || !is_array($ids) || !in_array($new_status, $valid_statuses)) {
			echo json_encode([
				'success' => false,
				'message' => 'Status target atau data pengiriman tidak valid.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$success_count = 0;
		$failed_count = 0;

		try {
			$this->db->trans_start();

			foreach ($ids as $id) {
				$id = (int)$id;
				if (!$id) continue;

				$old_shipment = $this->model_kol_shipments->find($id);
				if (!$old_shipment) {
					$failed_count++;
					continue;
				}

				$update_data = [
					'status' => $new_status,
					'updated_at' => date('Y-m-d H:i:s')
				];

				if (in_array($new_status, ['PENDING', 'PROCESSING'])) {
					$update_data['shipped_at'] = NULL;
					$update_data['delivered_at'] = NULL;
				} else if ($new_status == 'SHIPPED') {
					if (empty($old_shipment->shipped_at)) {
						$update_data['shipped_at'] = date('Y-m-d H:i:s');
					}
					$update_data['delivered_at'] = NULL;
				} else if ($new_status == 'DELIVERED') {
					if (empty($old_shipment->shipped_at)) {
						$update_data['shipped_at'] = date('Y-m-d H:i:s');
					}
					if (empty($old_shipment->delivered_at)) {
						$update_data['delivered_at'] = date('Y-m-d H:i:s');
					}
				}

				$this->db->where('id', $id)->update('kol_shipments', $update_data);
				if ($old_shipment->status !== $new_status) {
					$this->model_kol_shipments->log_stock_change($id, $old_shipment->status, $new_status);
				}
				$success_count++;
			}

			$this->db->trans_complete();

			if ($this->db->trans_status() === TRUE) {
				echo json_encode([
					'success' => true,
					'message' => "Berhasil memperbarui status {$success_count} pengiriman menjadi {$new_status}.",
					'updated_count' => $success_count,
					'status' => $new_status,
					'csrfHash' => $this->security->get_csrf_hash()
				]);
			} else {
				$db_err = $this->db->error();
				echo json_encode([
					'success' => false,
					'message' => 'Gagal memperbarui status massal: ' . ($db_err['message'] ?? 'Database error'),
					'csrfHash' => $this->security->get_csrf_hash()
				]);
			}
		} catch (Throwable $e) {
			echo json_encode([
				'success' => false,
				'message' => 'Exception: ' . $e->getMessage(),
				'csrfHash' => $this->security->get_csrf_hash()
			]);
		}
	}
}


/* End of file kol_shipments.php */
/* Location: ./application/controllers/administrator/Kol Shipments.php */
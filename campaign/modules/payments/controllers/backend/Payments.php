<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Payments Controller
*| --------------------------------------------------------------------------
*| Payments site
*|
*/
class Payments extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_payments');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Paymentss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('payments_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['paymentss'] = $this->model_payments->get($filter, $field, $this->limit_page, $offset);
		$this->data['payments_counts'] = $this->model_payments->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/payments/index/',
			'total_rows'   => $this->model_payments->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Payments List');
		$this->render('backend/standart/administrator/payments/payments_list', $this->data);
	}
	
	/**
	* Add new paymentss
	*
	*/
	public function add()
	{
		$this->is_allowed('payments_add');

		$this->template->title('Payments New');
		$this->render('backend/standart/administrator/payments/payments_add', $this->data);
	}

	/**
	* Add New Paymentss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('payments_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('invoice_id', 'Invoice Id', 'trim|required');
		$this->form_validation->set_rules('payment_date', 'Payment Date', 'trim|required');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|required|max_length[15]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'invoice_id' => $this->input->post('invoice_id'),
				'payment_date' => $this->input->post('payment_date'),
				'amount' => $this->input->post('amount'),
				'payment_method' => $this->input->post('payment_method'),
				'notes' => $this->input->post('notes'),
				'created_by' => $this->aauth->get_user_id(),
			];

			if (!is_dir(FCPATH . '/uploads/payments/')) {
				mkdir(FCPATH . '/uploads/payments/', 0777, true);
			}

			if (count((array) $this->input->post('payments_payment_proof_file_name'))) {
				foreach ((array) $_POST['payments_payment_proof_file_name'] as $idx => $file_name) {
					$payments_payment_proof_file_name_copy = date('YmdHis') . '-' . $file_name;

					rename(FCPATH . 'uploads/tmp/' . $_POST['payments_payment_proof_file_uuid'][$idx] . '/' .  $file_name, 
							FCPATH . 'uploads/payments/' . $payments_payment_proof_file_name_copy);

					$listed_image[] = $payments_payment_proof_file_name_copy;

					if (!is_file(FCPATH . '/uploads/payments/' . $payments_payment_proof_file_name_copy)) {
						echo json_encode([
							'success' => false,
							'message' => 'Error uploading file'
							]);
						exit;
					}
				}

				$save_data['payment_proof_file'] = implode($listed_image, ',');
			}
		
			
			$save_payments = $this->model_payments->store($save_data);
            

			if ($save_payments) {
				$this->_update_invoice_status($this->input->post('invoice_id'));
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_payments;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/payments/edit/' . $save_payments, 'Edit Payments'),
						anchor('administrator/payments', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/payments/edit/' . $save_payments, 'Edit Payments')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/payments');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/payments');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Paymentss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('payments_update');

		$this->data['payments'] = $this->model_payments->find($id);

		$this->template->title('Payments Update');
		$this->render('backend/standart/administrator/payments/payments_update', $this->data);
	}

	/**
	* Update Paymentss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('payments_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('invoice_id', 'Invoice Id', 'trim|required');
		$this->form_validation->set_rules('payment_date', 'Payment Date', 'trim|required');
		$this->form_validation->set_rules('amount', 'Amount', 'trim|required|max_length[15]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'invoice_id' => $this->input->post('invoice_id'),
				'payment_date' => $this->input->post('payment_date'),
				'amount' => $this->input->post('amount'),
				'payment_method' => $this->input->post('payment_method'),
				'notes' => $this->input->post('notes'),
			];

			$listed_image = [];
			if (count((array) $this->input->post('payments_payment_proof_file_name'))) {
				foreach ((array) $_POST['payments_payment_proof_file_name'] as $idx => $file_name) {
					if (isset($_POST['payments_payment_proof_file_uuid'][$idx]) AND !empty($_POST['payments_payment_proof_file_uuid'][$idx])) {
						$payments_payment_proof_file_name_copy = date('YmdHis') . '-' . $file_name;

						rename(FCPATH . 'uploads/tmp/' . $_POST['payments_payment_proof_file_uuid'][$idx] . '/' .  $file_name, 
								FCPATH . 'uploads/payments/' . $payments_payment_proof_file_name_copy);

						$listed_image[] = $payments_payment_proof_file_name_copy;

						if (!is_file(FCPATH . '/uploads/payments/' . $payments_payment_proof_file_name_copy)) {
							echo json_encode([
								'success' => false,
								'message' => 'Error uploading file'
								]);
							exit;
						}
					} else {
						$listed_image[] = $file_name;
					}
				}
			}
			
			$save_data['payment_proof_file'] = implode($listed_image, ',');
		
			
			$save_payments = $this->model_payments->change($id, $save_data);

			if ($save_payments) {
				$this->_update_invoice_status($this->input->post('invoice_id'));
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/payments', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/payments');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/payments');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Paymentss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('payments_delete');

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
            set_message(cclang('has_been_deleted', 'payments'), 'success');
        } else {
            set_message(cclang('error_delete', 'payments'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Paymentss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('payments_view');

		$this->data['payments'] = $this->model_payments->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Payments Detail');
		$this->render('backend/standart/administrator/payments/payments_view', $this->data);
	}
	
	/**
	* delete Paymentss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$payments = $this->model_payments->find($id);

		
		if (!empty($payments->payment_proof_file)) {
			foreach ((array) explode(',', $payments->payment_proof_file) as $filename) {
				$path = FCPATH . '/uploads/payments/' . $filename;

				if (is_file($path)) {
					$delete_file = unlink($path);
				}
			}
		}
		
		$res = $this->model_payments->remove($id);
		if ($res) {
			$this->_update_invoice_status($payments->invoice_id);
		}
		return $res;
	}
	
	
	/**
	* Upload Image Payments	* 
	* @return JSON
	*/
	public function upload_payment_proof_file_file()
	{
		if (!$this->is_allowed('payments_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'payments',
		]);
	}

	/**
	* Delete Image Payments	* 
	* @return JSON
	*/
	public function delete_payment_proof_file_file($uuid)
	{
		if (!$this->is_allowed('payments_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'payment_proof_file', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'payments',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/payments/'
        ]);
	}

	/**
	* Get Image Payments	* 
	* @return JSON
	*/
	public function get_payment_proof_file_file($id)
	{
		if (!$this->is_allowed('payments_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$payments = $this->model_payments->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'payment_proof_file', 
            'table_name'        => 'payments',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/payments/',
            'delete_endpoint'   => 'administrator/payments/delete_payment_proof_file_file'
        ]);
	}
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('payments_export');

		$this->model_payments->export('payments', 'payments');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('payments_export');

		$this->model_payments->pdf('payments', 'payments');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('payments_export');

		$table = $title = 'payments';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_payments->find($id);
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
	 * Get Invoice Balance
	 * 
	 * @return JSON
	 */
	public function get_invoice_balance($invoice_id)
	{
		if (!$this->is_allowed('payments_add', false) && !$this->is_allowed('payments_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$invoice = $this->db->get_where('invoices', ['id' => $invoice_id])->row();
		if (!$invoice) {
			echo json_encode(['success' => false, 'message' => 'Invoice not found']);
			exit;
		}

		$this->db->select_sum('amount');
		$this->db->where('invoice_id', $invoice_id);
		$total_paid = $this->db->get('payments')->row()->amount;
		
		$balance = $invoice->grand_total - $total_paid;
		if ($balance < 0) $balance = 0;

		echo json_encode([
			'success' => true,
			'balance' => $balance
		]);
	}

	private function _update_invoice_status($invoice_id)
	{
		if (!$invoice_id) return;
		
		$invoice = $this->db->get_where('invoices', ['id' => $invoice_id])->row();
		if (!$invoice) return;

		$this->db->select_sum('amount');
		$this->db->where('invoice_id', $invoice_id);
		$total_paid = $this->db->get('payments')->row()->amount;
		
		$status = 'APPROVED';
		if ($total_paid > 0) {
			if ($total_paid >= $invoice->grand_total) {
				$status = 'PAID';
			} else {
				$status = 'PARTIAL';
			}
		}
		
		$this->db->update('invoices', ['status' => $status], ['id' => $invoice_id]);
	}
}


/* End of file payments.php */
/* Location: ./application/controllers/administrator/Payments.php */
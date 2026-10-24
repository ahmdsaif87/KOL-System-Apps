<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Invoices Controller
*| --------------------------------------------------------------------------
*| Invoices site
*|
*/
class Invoices extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_invoices');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Invoicess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('invoices_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['invoicess'] = $this->model_invoices->get($filter, $field, $this->limit_page, $offset);
		$this->data['invoices_counts'] = $this->model_invoices->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/invoices/index/',
			'total_rows'   => $this->model_invoices->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Invoices List');
		$this->render('backend/standart/administrator/invoices/invoices_list', $this->data);
	}
	
	/**
	* Add new invoicess
	*
	*/
	public function add()
	{
		$this->is_allowed('invoices_add');

		$this->template->title('Invoices New');
		$this->render('backend/standart/administrator/invoices/invoices_add', $this->data);
	}

	/**
	* Add New Invoicess
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('invoices_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('campaign_kol_id', 'Campaign Kol Id', 'trim|required');

		$this->form_validation->set_rules('subtotal', 'Subtotal', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('tax_amount', 'Tax Amount', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('grand_total', 'Grand Total', 'trim|required|max_length[15]');

		

		if ($this->form_validation->run()) {
		
			$today = date('Ymd');
			$this->db->like('invoice_no', 'INV-'.$today);
			$this->db->order_by('id', 'DESC');
			$last_invoice = $this->db->get('invoices')->row();
			if ($last_invoice) {
				$last_no = explode('-', $last_invoice->invoice_no);
				$next_no = sprintf('%04d', intval(end($last_no)) + 1);
			} else {
				$next_no = '0001';
			}
			$generated_invoice_no = 'INV-' . $today . '-' . $next_no;

			$save_data = [
				'campaign_kol_id' => $this->input->post('campaign_kol_id'),
				'invoice_no' => $generated_invoice_no,
				'subtotal' => $this->input->post('subtotal'),
				'tax_amount' => $this->input->post('tax_amount'),
				'grand_total' => $this->input->post('subtotal') - $this->input->post('tax_amount'),
				'due_date' => $this->input->post('due_date'),
				'status' => 'UNPAID',
				'created_by' => $this->aauth->get_user_id(),
			];

			if (!is_dir(FCPATH . '/uploads/invoices/')) {
				mkdir(FCPATH . '/uploads/invoices/', 0777, true);
			}

			if (count((array) $this->input->post('invoices_invoice_file_name'))) {
				foreach ((array) $_POST['invoices_invoice_file_name'] as $idx => $file_name) {
					$invoices_invoice_file_name_copy = date('YmdHis') . '-' . $file_name;

					rename(FCPATH . 'uploads/tmp/' . $_POST['invoices_invoice_file_uuid'][$idx] . '/' .  $file_name, 
							FCPATH . 'uploads/invoices/' . $invoices_invoice_file_name_copy);

					$listed_image[] = $invoices_invoice_file_name_copy;

					if (!is_file(FCPATH . '/uploads/invoices/' . $invoices_invoice_file_name_copy)) {
						echo json_encode([
							'success' => false,
							'message' => 'Error uploading file'
							]);
						exit;
					}
				}

				$save_data['invoice_file'] = implode(',', $listed_image);
			}
		
			
			$save_invoices = $this->model_invoices->store($save_data);
            

			if ($save_invoices) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_invoices;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/invoices/edit/' . $save_invoices, 'Edit Invoices'),
						anchor('administrator/invoices', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/invoices/edit/' . $save_invoices, 'Edit Invoices')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invoices');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invoices');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}

	/**
	 * Auto generate invoice from deal
	 */
	public function create_from_deal($campaign_kol_id)
	{
		if (!$this->is_allowed('invoices_add', false)) {
			echo json_encode(['success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}

		// Check if invoice already exists
		$existing = $this->db->get_where('invoices', ['campaign_kol_id' => $campaign_kol_id])->row();
		if ($existing) {
			echo json_encode(['success' => false, 'message' => 'Invoice for this deal already exists.']);
			return;
		}

		// Get campaign kol and kol data
		$this->db->select('campaign_kols.deal_amount, kols.npwp_number');
		$this->db->join('kols', 'kols.id = campaign_kols.kol_id', 'left');
		$ck = $this->db->get_where('campaign_kols', ['campaign_kols.id' => $campaign_kol_id])->row();

		if (!$ck) {
			echo json_encode(['success' => false, 'message' => 'Deal not found.']);
			return;
		}

		if (empty($ck->deal_amount) || $ck->deal_amount <= 0) {
			echo json_encode(['success' => false, 'message' => 'Deal amount is empty or zero.']);
			return;
		}

		$subtotal = (float)$ck->deal_amount;
		$tax_amount = 0;
		if (!empty($ck->npwp_number)) {
			$tax_amount = $subtotal * 0.025; // 2.5% PPh 23
		} else {
			$tax_amount = $subtotal * 0.03; // 3% PPh 21
		}
		$grand_total = $subtotal - $tax_amount; // Net Payable / Total Ditransfer (Subtotal minus PPh)

		$today = date('Ymd');
		$this->db->like('invoice_no', 'INV-'.$today);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$last_invoice = $this->db->get('invoices')->row();

		if ($last_invoice) {
			$last_no = explode('-', $last_invoice->invoice_no);
			$next_no = sprintf('%04d', intval(end($last_no)) + 1);
		} else {
			$next_no = '0001';
		}
		$generated_invoice_no = 'INV-' . $today . '-' . $next_no;

		$save_data = [
			'campaign_kol_id' => $campaign_kol_id,
			'invoice_no'      => $generated_invoice_no,
			'subtotal'        => $subtotal,
			'tax_amount'      => $tax_amount,
			'grand_total'     => $grand_total,
			'due_date'        => date('Y-m-d', strtotime('+14 days')),
			'status'          => 'UNPAID',
			'created_by'      => $this->aauth->get_user_id(),
			'created_at'      => date('Y-m-d H:i:s'),
		];

		$save_invoices = $this->model_invoices->store($save_data);

		if ($save_invoices) {
			echo json_encode([
				'success' => true,
				'message' => 'Invoice generated successfully.',
				'invoice_id' => $save_invoices
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to generate invoice.'
			]);
		}
	}
	
		/**
	* Update view Invoicess
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('invoices_update');

		$this->data['invoices'] = $this->model_invoices->find($id);

		$this->template->title('Invoices Update');
		$this->render('backend/standart/administrator/invoices/invoices_update', $this->data);
	}

	/**
	* Update Invoicess
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('invoices_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('campaign_kol_id', 'Campaign Kol Id', 'trim|required');
		$this->form_validation->set_rules('invoice_no', 'Invoice No', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('subtotal', 'Subtotal', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('tax_amount', 'Tax Amount', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('grand_total', 'Grand Total', 'trim|required|max_length[15]');

		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'campaign_kol_id' => $this->input->post('campaign_kol_id'),
				'invoice_no' => $this->input->post('invoice_no'),
				'subtotal' => $this->input->post('subtotal'),
				'tax_amount' => $this->input->post('tax_amount'),
				'grand_total' => $this->input->post('subtotal') - $this->input->post('tax_amount'),
				'due_date' => $this->input->post('due_date'),
			];

			$listed_image = [];
			if (count((array) $this->input->post('invoices_invoice_file_name'))) {
				foreach ((array) $_POST['invoices_invoice_file_name'] as $idx => $file_name) {
					if (isset($_POST['invoices_invoice_file_uuid'][$idx]) AND !empty($_POST['invoices_invoice_file_uuid'][$idx])) {
						$invoices_invoice_file_name_copy = date('YmdHis') . '-' . $file_name;

						rename(FCPATH . 'uploads/tmp/' . $_POST['invoices_invoice_file_uuid'][$idx] . '/' .  $file_name, 
								FCPATH . 'uploads/invoices/' . $invoices_invoice_file_name_copy);

						$listed_image[] = $invoices_invoice_file_name_copy;

						if (!is_file(FCPATH . '/uploads/invoices/' . $invoices_invoice_file_name_copy)) {
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
			
			$save_data['invoice_file'] = implode($listed_image, ',');
		
			
			$save_invoices = $this->model_invoices->change($id, $save_data);

			if ($save_invoices) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/invoices', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/invoices');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/invoices');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Invoicess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('invoices_delete');

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
            set_message(cclang('has_been_deleted', 'invoices'), 'success');
        } else {
            set_message(cclang('error_delete', 'invoices'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Invoicess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('invoices_view');

		$this->data['invoices'] = $this->model_invoices->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Invoices Detail');
		$this->render('backend/standart/administrator/invoices/invoices_view', $this->data);
	}
	
	/**
	* delete Invoicess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$invoices = $this->model_invoices->find($id);

		
		if (!empty($invoices->invoice_file)) {
			foreach ((array) explode(',', $invoices->invoice_file) as $filename) {
				$path = FCPATH . '/uploads/invoices/' . $filename;

				if (is_file($path)) {
					$delete_file = unlink($path);
				}
			}
		}
		
		return $this->model_invoices->remove($id);
	}
	
	
	/**
	* Upload Image Invoices	* 
	* @return JSON
	*/
	public function upload_invoice_file_file()
	{
		if (!$this->is_allowed('invoices_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'invoices',
		]);
	}

	/**
	* Delete Image Invoices	* 
	* @return JSON
	*/
	public function delete_invoice_file_file($uuid)
	{
		if (!$this->is_allowed('invoices_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'invoice_file', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'invoices',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invoices/'
        ]);
	}

	/**
	* Get Image Invoices	* 
	* @return JSON
	*/
	public function get_invoice_file_file($id)
	{
		if (!$this->is_allowed('invoices_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$invoices = $this->model_invoices->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'invoice_file', 
            'table_name'        => 'invoices',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/invoices/',
            'delete_endpoint'   => 'administrator/invoices/delete_invoice_file_file'
        ]);
	}
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('invoices_export');

		$this->model_invoices->export('invoices', 'invoices');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('invoices_export');

		$this->model_invoices->pdf('invoices', 'invoices');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('invoices_export');

		$table = $title = 'invoices';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_invoices->find($id);
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

	public function print_pdf($id = null)
	{
		$this->is_allowed('invoices_view');
		$this->load->library('HtmlPdf');
		
		$invoice = $this->model_invoices->join_avaiable()->find($id);
		if (!$invoice) {
			show_404();
		}

		// Hitung Total yang telah dibayar dari payments
		$this->db->select_sum('amount');
		$this->db->where('invoice_id', $invoice->id);
		// Tidak ada kolom status di payments, anggap semua record di payments = verified/sukses dibayar
		$payment_query = $this->db->get('payments');
		$paid_amount = 0;
		if ($payment_query->num_rows() > 0) {
			$paid_amount = (float) $payment_query->row()->amount;
		}
		
		$invoice->amount_paid = $paid_amount;
		
		// Kalkulasi Finansial
		$subtotal = (float)$invoice->subtotal;
		$pph = (float)$invoice->tax_amount;
		$net_total = $subtotal - $pph;
		$balance_due = max(0, $net_total - $paid_amount);

		$invoice->grand_total = $net_total;
		
		if ($paid_amount >= $net_total && $net_total > 0) {
			$invoice->status = 'PAID';
			$this->db->where('id', $invoice->id)->update('invoices', ['status' => 'PAID']);
		} elseif ($paid_amount > 0) {
			$invoice->status = 'PARTIAL';
			$this->db->where('id', $invoice->id)->update('invoices', ['status' => 'PARTIAL']);
		}
		
		$config = array(
			'orientation' => 'p',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5)
		);
		
		$this->pdf = new HtmlPdf($config);
		
		$content = $this->pdf->loadHtmlPdf('backend/standart/administrator/invoices/invoices_pdf', [
			'invoice' => $invoice
		], TRUE);
		
		$this->pdf->initialize($config);
		$this->pdf->pdf->SetDisplayMode('fullpage');
		$this->pdf->writeHTML($content);
		$this->pdf->Output('Invoice_'.$invoice->invoice_no.'.pdf', 'I');
	}

	/**
	 * KOL Portal: Tagihan Saya (Invoices issued by/for this KOL)
	 */
	public function my_invoices()
	{
		if (!$this->aauth->is_loggedin()) {
			redirect('administrator/login', 'refresh');
		}

		$kol_id = get_current_user_kol_id();

		if (!$kol_id && !$this->aauth->is_admin()) {
			$this->data['unlinked_kol'] = true;
			$this->data['invoices'] = [];
			$this->template->title('Tagihan Saya');
			$this->render('backend/standart/administrator/invoices/kol_my_invoices', $this->data);
			return;
		}

		$this->db->select('
			invoices.*,
			campaigns.name as campaign_name,
			campaigns.brand_name,
			kols.name as kol_name,
			kols.bank_name,
			kols.bank_account_number,
			kols.bank_account_name,
			campaign_kol_contracts.id as contract_id,
			campaign_kol_contracts.contract_no,
			campaign_kols.deal_amount,
			COALESCE((SELECT SUM(amount) FROM payments WHERE payments.invoice_id = invoices.id), 0) as total_paid
		')
		->from('invoices')
		->join('campaign_kols', 'campaign_kols.id = invoices.campaign_kol_id', 'INNER')
		->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'LEFT')
		->join('kols', 'kols.id = campaign_kols.kol_id', 'LEFT')
		->join('campaign_kol_contracts', 'campaign_kol_contracts.campaign_kol_id = campaign_kols.id', 'LEFT');

		if ($kol_id) {
			$this->db->where('campaign_kols.kol_id', $kol_id);
		}

		$this->db->where_not_in('invoices.status', ['CANCELLED']);
		$this->db->order_by('invoices.id', 'DESC');
		$this->data['invoices'] = $this->db->get()->result();
		$this->data['unlinked_kol'] = false;
		$this->data['kol_id'] = $kol_id;

		$this->template->title('Tagihan Saya');
		$this->render('backend/standart/administrator/invoices/kol_my_invoices', $this->data);
	}
}


/* End of file invoices.php */
/* Location: ./application/controllers/administrator/Invoices.php */
<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Dashboard Controller
*| --------------------------------------------------------------------------
*| For see your board
*|
*/
class Dashboard extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}

		$data = [];
		$active_brand_id = function_exists('get_active_brand_id') ? get_active_brand_id() : null;

		// KPI Metrics
		if ($active_brand_id) {
			$total_kols_row = $this->db
				->select('COUNT(DISTINCT ck.kol_id) as total')
				->from('campaign_kols ck')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where('c.brand_id', $active_brand_id)
				->get()->row();
			$data['total_kols'] = $total_kols_row ? (int)$total_kols_row->total : 0;

			$active_kols_row = $this->db
				->select('COUNT(DISTINCT ck.kol_id) as total')
				->from('campaign_kols ck')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->join('kols k', 'k.id = ck.kol_id', 'INNER')
				->where('c.brand_id', $active_brand_id)
				->where('k.status', 'ACTIVE')
				->get()->row();
			$data['active_kols'] = $active_kols_row ? (int)$active_kols_row->total : 0;

			$data['active_campaigns'] = $this->db->where('status', 'ACTIVE')->where('brand_id', $active_brand_id)->count_all_results('campaigns');
			$budget_row = $this->db->select_sum('budget')->where('brand_id', $active_brand_id)->get('campaigns')->row();
		} else {
			$data['total_kols'] = $this->db->count_all_results('kols');
			$data['active_kols'] = $this->db->where('status', 'ACTIVE')->count_all_results('kols');
			$data['active_campaigns'] = $this->db->where('status', 'ACTIVE')->count_all_results('campaigns');
			$budget_row = $this->db->select_sum('budget')->get('campaigns')->row();
		}
		
		$pending_kols_count = 0; // Approval status removed
		if ($active_brand_id) {
			$pending_briefs_count = $this->db
				->from('briefs b')
				->join('tasks t', 't.id = b.task_id', 'INNER')
				->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'INNER')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where('b.status', 'PENDING_REVIEW')
				->where('c.brand_id', $active_brand_id)
				->count_all_results();

			$pending_drafts_count = $this->db
				->from('drafts d')
				->join('tasks t', 't.id = d.task_id', 'INNER')
				->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'INNER')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where('d.status', 'SUBMITTED')
				->where('c.brand_id', $active_brand_id)
				->count_all_results();

			$data['total_tasks'] = $this->db
				->from('tasks t')
				->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'INNER')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where('c.brand_id', $active_brand_id)
				->count_all_results();

			$data['live_postings'] = $this->db
				->from('tasks t')
				->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'INNER')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where_in('t.status', ['POSTED', 'DONE'])
				->where('c.brand_id', $active_brand_id)
				->count_all_results();
		} else {
			$pending_briefs_count = $this->db->where('status', 'PENDING_REVIEW')->count_all_results('briefs');
			$pending_drafts_count = $this->db->where('status', 'SUBMITTED')->count_all_results('drafts');
			$data['total_tasks'] = $this->db->count_all_results('tasks');
			$data['live_postings'] = $this->db->where_in('status', ['POSTED', 'DONE'])->count_all_results('tasks');
		}
		$data['pending_approvals'] = $pending_kols_count + $pending_briefs_count + $pending_drafts_count;
		$data['total_budget'] = $budget_row && $budget_row->budget ? (float)$budget_row->budget : 0;

		// Phase 5: Financial KPIs
		if ($active_brand_id) {
			$deals_row = $this->db->select('COALESCE(SUM(ck.deal_amount), 0) as total_deals')
				->from('campaign_kols ck')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where_not_in('ck.status', ['CANCELLED', 'REJECTED'])
				->where('c.brand_id', $active_brand_id)
				->get()->row();
			$data['total_deals'] = $deals_row ? (float)$deals_row->total_deals : 0;

			$inv_row = $this->db->select('COALESCE(SUM(i.grand_total), 0) as total_invoiced')
				->from('invoices i')
				->join('campaign_kols ck', 'ck.id = i.campaign_kol_id', 'INNER')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where('i.status !=', 'CANCELLED')
				->where('c.brand_id', $active_brand_id)
				->get()->row();
			$data['total_invoiced'] = $inv_row ? (float)$inv_row->total_invoiced : 0;

			$pay_row = $this->db->select('COALESCE(SUM(p.amount), 0) as total_paid')
				->from('payments p')
				->join('invoices i', 'i.id = p.invoice_id', 'INNER')
				->join('campaign_kols ck', 'ck.id = i.campaign_kol_id', 'INNER')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where('i.status !=', 'CANCELLED')
				->where('c.brand_id', $active_brand_id)
				->get()->row();
			$data['total_paid'] = $pay_row ? (float)$pay_row->total_paid : 0;
		} else {
			$deals_row = $this->db->select('COALESCE(SUM(deal_amount), 0) as total_deals')
				->where_not_in('status', ['CANCELLED', 'REJECTED'])
				->get('campaign_kols')->row();
			$data['total_deals'] = $deals_row ? (float)$deals_row->total_deals : 0;

			$inv_row = $this->db->select('COALESCE(SUM(grand_total), 0) as total_invoiced')
				->where('status !=', 'CANCELLED')
				->get('invoices')->row();
			$data['total_invoiced'] = $inv_row ? (float)$inv_row->total_invoiced : 0;

			$pay_row = $this->db->select('COALESCE(SUM(p.amount), 0) as total_paid')
				->from('payments p')
				->join('invoices i', 'i.id = p.invoice_id', 'INNER')
				->where('i.status !=', 'CANCELLED')
				->get()->row();
			$data['total_paid'] = $pay_row ? (float)$pay_row->total_paid : 0;
		}
		$data['total_outstanding'] = max(0, $data['total_invoiced'] - $data['total_paid']);

		// Pending Invoices for Finance Hub Widget (Unpaid / Partial)
		$pending_inv_builder = $this->db
			->select('i.id, i.invoice_no, i.grand_total, i.status, i.due_date, k.name as kol_name, c.name as campaign_name,
					  COALESCE(SUM(p.amount), 0) as total_paid,
					  (i.grand_total - COALESCE(SUM(p.amount), 0)) as unpaid_balance,
					  DATEDIFF(i.due_date, CURDATE()) as days_left')
			->from('invoices i')
			->join('campaign_kols ck', 'ck.id = i.campaign_kol_id', 'INNER')
			->join('kols k', 'k.id = ck.kol_id', 'INNER')
			->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
			->join('payments p', 'p.invoice_id = i.id', 'LEFT')
			->where_in('i.status', ['SUBMITTED', 'APPROVED', 'PARTIAL']);

		if ($active_brand_id) {
			$pending_inv_builder->where('c.brand_id', $active_brand_id);
		}

		$data['pending_invoices'] = $pending_inv_builder
			->group_by('i.id')
			->having('unpaid_balance >', 0)
			->order_by('i.due_date', 'ASC')
			->limit(3)
			->get()->result();

		// Phase 5: Production Workflow & Funnel Counts (Synchronized with Sidebar "Production")
		if ($active_brand_id) {
			$data['prod_monitoring'] = $data['total_tasks'];

			$data['prod_review_draft'] = $this->db
				->from('tasks t')
				->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'INNER')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where_in('t.status', ['WAITING_DRAFT', 'IN_REVIEW_DRAFT', 'REVISION_DRAFT'])
				->where('c.brand_id', $active_brand_id)
				->count_all_results();

			$data['prod_live_posting'] = $this->db
				->from('tasks t')
				->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'INNER')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where_in('t.status', ['APPROVED_DRAFT', 'POSTED', 'DONE'])
				->where('c.brand_id', $active_brand_id)
				->count_all_results();

			$data['prod_postings'] = $this->db
				->from('postings p')
				->join('tasks t', 't.id = p.task_id', 'INNER')
				->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'INNER')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where('c.brand_id', $active_brand_id)
				->count_all_results();

			$data['prod_schedule'] = $this->db
				->from('tasks t')
				->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'INNER')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where('t.estimated_posting_date IS NOT NULL')
				->where('c.brand_id', $active_brand_id)
				->count_all_results();
		} else {
			$data['prod_monitoring'] = $this->db->count_all_results('tasks');
			$data['prod_review_draft'] = $this->db->where_in('status', ['WAITING_DRAFT', 'IN_REVIEW_DRAFT', 'REVISION_DRAFT'])->count_all_results('tasks');
			$data['prod_live_posting'] = $this->db->where_in('status', ['APPROVED_DRAFT', 'POSTED', 'DONE'])->count_all_results('tasks');
			$data['prod_postings'] = $this->db->count_all_results('postings');
			$data['prod_schedule'] = $this->db->where('estimated_posting_date IS NOT NULL')->count_all_results('tasks');
		}

		// Backward compatibility
		$data['pipeline_waiting'] = $data['prod_monitoring'];
		$data['pipeline_in_review'] = $data['prod_review_draft'];
		$data['pipeline_revision'] = $data['prod_live_posting'];
		$data['pipeline_approved'] = $data['prod_postings'];
		$data['pipeline_live'] = $data['prod_schedule'];

		// Phase 5: Upcoming Deadlines & Overdue Tasks (Next 14 days & Overdue)
		$deadlines_builder = $this->db
			->select('t.id, t.title, t.estimated_posting_date, t.status, k.name as kol_name, c.name as campaign_name, p.name as platform_name,
					  DATEDIFF(t.estimated_posting_date, CURDATE()) as days_left')
			->from('tasks t')
			->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'INNER')
			->join('kols k', 'k.id = ck.kol_id', 'INNER')
			->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
			->join('platforms p', 'p.id = t.platform_id', 'LEFT')
			->where_not_in('t.status', ['POSTED', 'DONE'])
			->where('t.estimated_posting_date IS NOT NULL');

		if ($active_brand_id) {
			$deadlines_builder->where('c.brand_id', $active_brand_id);
		}

		$data['upcoming_deadlines'] = $deadlines_builder
			->order_by('t.estimated_posting_date', 'ASC')
			->limit(8)
			->get()->result();

		// Expiring Contracts (Expiring within next 14 days or already expired)
		$contracts_builder = $this->db
			->select('c.id, c.contract_no, c.status, c.expired_at, k.name as kol_name, camp.name as campaign_name,
					  DATEDIFF(c.expired_at, CURDATE()) as days_left')
			->from('campaign_kol_contracts c')
			->join('campaign_kols ck', 'ck.id = c.campaign_kol_id', 'INNER')
			->join('kols k', 'k.id = ck.kol_id', 'INNER')
			->join('campaigns camp', 'camp.id = ck.campaign_id', 'INNER')
			->where('c.status', 'ACTIVE')
			->where('c.expired_at IS NOT NULL')
			->where('DATEDIFF(c.expired_at, CURDATE()) <=', 14);

		if ($active_brand_id) {
			$contracts_builder->where('camp.brand_id', $active_brand_id);
		}

		$data['expiring_contracts'] = $contracts_builder
			->order_by('c.expired_at', 'ASC')
			->limit(5)
			->get()->result();

		// Recent pending KOLs
		$data['pending_kols_list'] = []; // Approval status removed

		// Recent Live Postings
		$postings_builder = $this->db->select('postings.*, tasks.title as task_title, platforms.name as platform_name, kols.name as kol_name')
			->from('postings')
			->join('tasks', 'tasks.id = postings.task_id', 'left')
			->join('platforms', 'platforms.id = postings.platform_id', 'left')
			->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'left')
			->join('kols', 'kols.id = campaign_kols.kol_id', 'left');

		if ($active_brand_id) {
			$postings_builder->join('campaigns camp_pst', 'camp_pst.id = campaign_kols.campaign_id', 'left')
				->where('camp_pst.brand_id', $active_brand_id);
		}

		$data['recent_postings'] = $postings_builder
			->order_by('postings.id', 'DESC')
			->limit(5)
			->get()
			->result();

		$this->render('backend/standart/dashboard', $data);
	}

	public function chart()
	{
		if (!$this->is_allowed('dashboard', false) && !$this->is_allowed('menu_leaderboard_kol', false) && !$this->is_allowed('menu_leaderboard', false)) {
			redirect('administrator/dashboard', 'refresh');
		}

		$data = [];
		$active_brand_id = function_exists('get_active_brand_id') ? get_active_brand_id() : null;

		// Leaderboard KOL Rankings
		$leaderboard_builder = $this->db->select('kols.*, tiers.name as tier_name, COUNT(DISTINCT tasks.id) as completed_tasks')
			->from('kols')
			->join('tiers', 'tiers.id = kols.tier_id', 'left')
			->join('campaign_kols', 'campaign_kols.kol_id = kols.id', 'left');

		if ($active_brand_id) {
			$leaderboard_builder->join('campaigns c_lead', 'c_lead.id = campaign_kols.campaign_id', 'inner')
				->where('c_lead.brand_id', $active_brand_id);
		}

		$data['leaderboard'] = $leaderboard_builder
			->join('tasks', 'tasks.campaign_kol_id = campaign_kols.id AND tasks.status IN ("POSTED", "DONE")', 'left')
			->group_by('kols.id')
			->order_by('kols.lifetime_score DESC, completed_tasks DESC')
			->get()
			->result();

		// Tier distribution for chart
		$data['tier_distribution'] = $this->db->select('COALESCE(tiers.name, "Standard") as tier_name, COUNT(kols.id) as total_kols')
			->from('kols')
			->join('tiers', 'tiers.id = kols.tier_id', 'left')
			->group_by('tiers.name')
			->get()
			->result();

		// Top 1 KOL
		$data['top_kol'] = !empty($data['leaderboard']) ? $data['leaderboard'][0] : null;
		
		// Total active kols
		if ($active_brand_id) {
			$active_kols_row = $this->db
				->select('COUNT(DISTINCT ck.kol_id) as total')
				->from('campaign_kols ck')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->join('kols k', 'k.id = ck.kol_id', 'INNER')
				->where('c.brand_id', $active_brand_id)
				->where('k.status', 'ACTIVE')
				->get()->row();
			$data['total_active_kols'] = $active_kols_row ? (int)$active_kols_row->total : 0;

			$data['total_completed_tasks'] = $this->db
				->from('tasks t')
				->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'INNER')
				->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
				->where_in('t.status', ['POSTED', 'DONE'])
				->where('c.brand_id', $active_brand_id)
				->count_all_results();
		} else {
			$data['total_active_kols'] = $this->db->where('status', 'ACTIVE')->count_all_results('kols');
			$data['total_completed_tasks'] = $this->db->where_in('status', ['POSTED', 'DONE'])->count_all_results('tasks');
		}

		// Average score
		$avg_row = $this->db->select('AVG(lifetime_score) as avg_score')->get('kols')->row();
		$data['avg_score'] = $avg_row ? round((float)$avg_row->avg_score, 1) : 0;

		$this->template->title('Leaderboard & Performa KOL');
		$this->render('backend/standart/chart', $data);
	}

	/**
	 * AJAX: Compare up to 5 KOLs side-by-side.
	 * Returns JSON { success, html, csrf_hash } or { success, message }.
	 */
	public function compare_kol()
	{
		if (!$this->is_allowed('dashboard', false)) {
			echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
			exit;
		}

		// --- Input validation ---
		$raw_ids = $this->input->post('kol_ids');
		if (empty($raw_ids) || !is_array($raw_ids)) {
			echo json_encode(['success' => false, 'message' => 'Tidak ada KOL yang dipilih.']);
			exit;
		}

		// Cast to int, filter invalid
		$ids = array_values(array_filter(array_map('intval', $raw_ids), function($v) { return $v > 0; }));

		if (count($ids) < 2) {
			echo json_encode(['success' => false, 'message' => 'Pilih minimal 2 KOL untuk dibandingkan.']);
			exit;
		}
		if (count($ids) > 5) {
			echo json_encode(['success' => false, 'message' => 'Maksimal 5 KOL dapat dibandingkan sekaligus.']);
			exit;
		}

		// --- Definitive comparison query ---
		$placeholders = implode(',', array_fill(0, count($ids), '?'));
		$sql = "
			SELECT
			    k.id,
			    k.name,
			    k.avatar_url,
			    k.lifetime_score,
			    t.name AS tier_name,
			    COALESCE(SUM(ck.deal_amount), 0)          AS total_deal_budget,
			    COALESCE(SUM(pm.views), 0)                AS total_views,
			    COALESCE(AVG(pm.engagement_rate), 0)      AS avg_er,
			    COUNT(DISTINCT p.id)                      AS total_postings,
			    COUNT(DISTINCT ta.id)                     AS total_tasks
			FROM kols k
			LEFT JOIN tiers t             ON t.id  = k.tier_id
			LEFT JOIN campaign_kols ck    ON ck.kol_id = k.id
			LEFT JOIN tasks ta            ON ta.campaign_kol_id = ck.id
			                             AND ta.status IN ('POSTED','DONE')
			LEFT JOIN postings p          ON p.task_id = ta.id
			LEFT JOIN posting_metrics pm  ON pm.id = p.latest_metric_id
			WHERE k.id IN ({$placeholders})
			GROUP BY k.id, k.name, k.avatar_url, k.lifetime_score, t.name
		";
		$kols = $this->db->query($sql, $ids)->result();

		if (empty($kols)) {
			echo json_encode(['success' => false, 'message' => 'Data KOL tidak ditemukan.']);
			exit;
		}

		// Compute CPV and build per-KOL metric arrays for "best" highlighting
		$views_arr  = [];
		$er_arr     = [];
		$cpv_arr    = [];
		$score_arr  = [];

		foreach ($kols as $kol) {
			$kol->views_count     = (int)$kol->total_views;
			$kol->avg_er          = (float)$kol->avg_er;
			$kol->lifetime_score  = (int)$kol->lifetime_score;
			$kol->total_deal      = (float)$kol->total_deal_budget;

			// CPV calculation: total_deal_budget / total_views (0 if views == 0)
			$kol->cpv = ($kol->views_count > 0 && $kol->total_deal > 0)
				? round($kol->total_deal / $kol->views_count, 2)
				: 0;

			$views_arr[$kol->id] = $kol->views_count;
			$er_arr[$kol->id]    = $kol->avg_er;
			$score_arr[$kol->id] = $kol->lifetime_score;
			if ($kol->cpv > 0) {
				$cpv_arr[$kol->id] = $kol->cpv;
			}
		}

		// Find winning (best) value per dimension
		$best = [
			'views' => !empty($views_arr) ? max($views_arr) : 0,
			'er'    => !empty($er_arr)    ? max($er_arr)    : 0,
			'score' => !empty($score_arr) ? max($score_arr) : 0,
			'cpv'   => !empty($cpv_arr)   ? min($cpv_arr)   : 0,
		];

		// Render the modal view into a string
		$html = $this->load->view('backend/standart/kol_compare_modal', [
			'kols' => $kols,
			'best' => $best,
		], true);

		echo json_encode([
			'success'   => true,
			'html'      => $html,
			'csrf_hash' => $this->security->get_csrf_hash(),
		]);
	}
}
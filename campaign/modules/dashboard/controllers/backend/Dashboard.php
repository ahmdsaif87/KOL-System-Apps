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

		// KPI Metrics
		$data['total_kols'] = $this->db->count_all_results('kols');
		$data['active_kols'] = $this->db->where('status', 'ACTIVE')->count_all_results('kols');
		$data['active_campaigns'] = $this->db->where('status', 'ACTIVE')->count_all_results('campaigns');
		
		$pending_kols_count = 0; // Approval status removed
		$pending_briefs_count = $this->db->where('status', 'PENDING_REVIEW')->count_all_results('briefs');
		$pending_drafts_count = $this->db->where('status', 'SUBMITTED')->count_all_results('drafts');
		$data['pending_approvals'] = $pending_kols_count + $pending_briefs_count + $pending_drafts_count;

		$data['total_tasks'] = $this->db->count_all_results('tasks');
		$data['live_postings'] = $this->db->where_in('status', ['POSTED', 'DONE'])->count_all_results('tasks');
		
		$budget_row = $this->db->select_sum('budget')->get('campaigns')->row();
		$data['total_budget'] = $budget_row && $budget_row->budget ? (float)$budget_row->budget : 0;

		// Phase 5: Financial KPIs
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
		$data['total_outstanding'] = max(0, $data['total_invoiced'] - $data['total_paid']);

		// Pending Invoices for Finance Hub Widget (Unpaid / Partial)
		$data['pending_invoices'] = $this->db
			->select('i.id, i.invoice_no, i.grand_total, i.status, i.due_date, k.name as kol_name, c.name as campaign_name,
					  COALESCE(SUM(p.amount), 0) as total_paid,
					  (i.grand_total - COALESCE(SUM(p.amount), 0)) as unpaid_balance,
					  DATEDIFF(i.due_date, CURDATE()) as days_left')
			->from('invoices i')
			->join('campaign_kols ck', 'ck.id = i.campaign_kol_id', 'INNER')
			->join('kols k', 'k.id = ck.kol_id', 'INNER')
			->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
			->join('payments p', 'p.invoice_id = i.id', 'LEFT')
			->where_in('i.status', ['SUBMITTED', 'APPROVED', 'PARTIAL'])
			->group_by('i.id')
			->having('unpaid_balance >', 0)
			->order_by('i.due_date', 'ASC')
			->limit(3)
			->get()->result();

		// Phase 5: Production Workflow & Funnel Counts (Synchronized with Sidebar "Production")
		$data['prod_monitoring'] = $this->db->count_all_results('tasks');
		$data['prod_review_draft'] = $this->db->where_in('status', ['WAITING_DRAFT', 'IN_REVIEW_DRAFT', 'REVISION_DRAFT'])->count_all_results('tasks');
		$data['prod_live_posting'] = $this->db->where_in('status', ['APPROVED_DRAFT', 'POSTED', 'DONE'])->count_all_results('tasks');
		$data['prod_postings'] = $this->db->count_all_results('postings');
		$data['prod_schedule'] = $this->db->where('estimated_posting_date IS NOT NULL')->count_all_results('tasks');

		// Backward compatibility
		$data['pipeline_waiting'] = $data['prod_monitoring'];
		$data['pipeline_in_review'] = $data['prod_review_draft'];
		$data['pipeline_revision'] = $data['prod_live_posting'];
		$data['pipeline_approved'] = $data['prod_postings'];
		$data['pipeline_live'] = $data['prod_schedule'];

		// Phase 5: Upcoming Deadlines & Overdue Tasks (Next 14 days & Overdue)
		$data['upcoming_deadlines'] = $this->db
			->select('t.id, t.title, t.estimated_posting_date, t.status, k.name as kol_name, c.name as campaign_name, p.name as platform_name,
					  DATEDIFF(t.estimated_posting_date, CURDATE()) as days_left')
			->from('tasks t')
			->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'INNER')
			->join('kols k', 'k.id = ck.kol_id', 'INNER')
			->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
			->join('platforms p', 'p.id = t.platform_id', 'LEFT')
			->where_not_in('t.status', ['POSTED', 'DONE'])
			->where('t.estimated_posting_date IS NOT NULL')
			->order_by('t.estimated_posting_date', 'ASC')
			->limit(8)
			->get()->result();

		// Expiring Contracts (Expiring within next 14 days or already expired)
		$data['expiring_contracts'] = $this->db
			->select('c.id, c.contract_no, c.status, c.expired_at, k.name as kol_name, camp.name as campaign_name,
					  DATEDIFF(c.expired_at, CURDATE()) as days_left')
			->from('campaign_kol_contracts c')
			->join('campaign_kols ck', 'ck.id = c.campaign_kol_id', 'INNER')
			->join('kols k', 'k.id = ck.kol_id', 'INNER')
			->join('campaigns camp', 'camp.id = ck.campaign_id', 'INNER')
			->where('c.status', 'ACTIVE')
			->where('c.expired_at IS NOT NULL')
			->where('DATEDIFF(c.expired_at, CURDATE()) <=', 14)
			->order_by('c.expired_at', 'ASC')
			->limit(5)
			->get()->result();

		// Recent pending KOLs
		$data['pending_kols_list'] = []; // Approval status removed

		// Recent Live Postings
		$data['recent_postings'] = $this->db->select('postings.*, tasks.title as task_title, platforms.name as platform_name, kols.name as kol_name')
			->from('postings')
			->join('tasks', 'tasks.id = postings.task_id', 'left')
			->join('platforms', 'platforms.id = postings.platform_id', 'left')
			->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'left')
			->join('kols', 'kols.id = campaign_kols.kol_id', 'left')
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

		// Leaderboard KOL Rankings
		$data['leaderboard'] = $this->db->select('kols.*, tiers.name as tier_name, COUNT(DISTINCT tasks.id) as completed_tasks')
			->from('kols')
			->join('tiers', 'tiers.id = kols.tier_id', 'left')
			->join('campaign_kols', 'campaign_kols.kol_id = kols.id', 'left')
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
		$data['total_active_kols'] = $this->db->where('status', 'ACTIVE')->count_all_results('kols');
		
		// Total completed tasks
		$data['total_completed_tasks'] = $this->db->where_in('status', ['POSTED', 'DONE'])->count_all_results('tasks');

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
		// CPV = SUM(deal_amount) / SUM(views) per KOL; zero-guarded in PHP below.
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

		foreach ($kols as $k) {
			$total_views  = (int)$k->total_views;
			$deal_budget  = (float)$k->total_deal_budget;
			// Zero-guard for CPV
			$cpv = ($total_views > 0) ? ($deal_budget / $total_views) : 0;
			$k->cpv = $cpv;

			$views_arr[]  = $total_views;
			$er_arr[]     = (float)$k->avg_er;
			$cpv_arr[]    = $cpv;
			$score_arr[]  = (float)$k->lifetime_score;
		}

		$max_views = !empty($views_arr) ? max($views_arr) : 0;
		$max_er    = !empty($er_arr)    ? max($er_arr)    : 0;
		$positive_cpvs = array_filter($cpv_arr, function($v) { return $v > 0; });
		$min_cpv   = !empty($positive_cpvs) ? min($positive_cpvs) : null;
		$max_score = !empty($score_arr) ? max($score_arr) : 0;

		// --- Build HTML grid ---
		ob_start();
		?>
		<div class="kol-compare-grid">
		<?php foreach ($kols as $k):
			$initials = strtoupper(mb_substr($k->name, 0, 1) . (mb_substr(trim(strstr($k->name, ' ')), 0, 1) ?: mb_substr($k->name, 1, 1)));
			$cpv      = $k->cpv;
			$views_class = ((int)$k->total_views === $max_views && $max_views > 0) ? 'metric-best' : '';
			$er_class    = ((float)$k->avg_er === $max_er && $max_er > 0)          ? 'metric-best' : '';
			$cpv_class   = ($min_cpv !== null && abs($cpv - $min_cpv) < 0.0001 && $cpv > 0) ? 'metric-best' : '';
			$score_class = ((float)$k->lifetime_score === $max_score && $max_score > 0) ? 'metric-best' : '';
		?>
		<div class="kol-compare-card">
		    <?php if (!empty($k->avatar_url)): ?>
		        <img src="<?= htmlspecialchars($k->avatar_url); ?>" alt="<?= htmlspecialchars($k->name); ?>" class="kol-avatar-circle" style="object-fit: cover; background: none;">
		    <?php else: ?>
		        <div class="kol-avatar-circle"><?= htmlspecialchars($initials); ?></div>
		    <?php endif; ?>
		    <div class="kol-cname"><?= htmlspecialchars($k->name); ?></div>
		    <div class="kol-ctier">
		        <span class="label label-primary" style="font-size:11px;"><?= htmlspecialchars($k->tier_name ?: 'Standard'); ?></span>
		    </div>

		    <div class="kol-compare-metric">
		        <span class="metric-label"><i class="fa fa-eye"></i> Total Views</span>
		        <span class="metric-value <?= $views_class ?>"><?= number_format($k->total_views); ?></span>
		    </div>
		    <div class="kol-compare-metric">
		        <span class="metric-label"><i class="fa fa-bar-chart"></i> Avg ER</span>
		        <span class="metric-value <?= $er_class ?>"><?= number_format($k->avg_er * 100, 2); ?>%</span>
		    </div>
		    <div class="kol-compare-metric">
		        <span class="metric-label"><i class="fa fa-money"></i> CPV</span>
		        <span class="metric-value <?= $cpv_class ?>">
		            <?= $cpv > 0 ? 'Rp ' . number_format($cpv, 0, ',', '.') : '<span class="text-muted">-</span>'; ?>
		        </span>
		    </div>
		    <div class="kol-compare-metric">
		        <span class="metric-label"><i class="fa fa-star"></i> Score</span>
		        <span class="metric-value <?= $score_class ?>"><?= number_format($k->lifetime_score, 1); ?></span>
		    </div>
		    <div class="kol-compare-metric">
		        <span class="metric-label"><i class="fa fa-tasks"></i> VT Live</span>
		        <span class="metric-value"><?= (int)$k->total_postings; ?></span>
		    </div>
		    <div style="margin-top: 10px; text-align: center;">
		        <a href="<?= site_url('administrator/kols/view/' . $k->id); ?>" class="btn btn-xs btn-default btn-flat" target="_blank"><i class="fa fa-eye"></i> Profil</a>
		    </div>
		</div>
		<?php endforeach; ?>
		</div>
		<p class="text-muted" style="font-size:11px; margin-top:10px; margin-bottom:0;">
		    <i class="fa fa-info-circle"></i>
		    <strong style="color:#16803c;">Hijau</strong> = nilai terbaik di antara KOL yang dipilih.
		    CPV = Total Deal Budget ÷ Total Views.
		</p>
		<?php
		$html = ob_get_clean();

		// Compute chart datasets
		$chart_labels = [];
		$chart_views  = [];
		$chart_er     = [];
		$chart_cpv    = [];
		$chart_score  = [];
		$chart_tasks  = [];

		foreach ($kols as $k) {
			$chart_labels[] = $k->name;
			$chart_views[]  = (int)$k->total_views;
			$chart_er[]     = round((float)$k->avg_er * 100, 2);
			$chart_cpv[]    = round((float)$k->cpv, 0);
			$chart_score[]  = round((float)$k->lifetime_score, 1);
			$chart_tasks[]  = (int)$k->total_postings;
		}

		$chart_data = [
			'labels' => $chart_labels,
			'views'  => $chart_views,
			'er'     => $chart_er,
			'cpv'    => $chart_cpv,
			'score'  => $chart_score,
			'tasks'  => $chart_tasks,
		];

		echo json_encode([
			'success'    => true,
			'html'       => $html,
			'chart_data' => $chart_data,
			'csrf_hash'  => $this->security->get_csrf_hash(),
		]);
		exit;
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/administrator/Dashboard.php */
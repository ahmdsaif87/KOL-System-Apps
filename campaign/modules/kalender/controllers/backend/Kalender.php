<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kalender extends Admin {

	public function __construct()
	{
		parent::__construct();
		$this->lang->load('web_lang', $this->current_lang);
	}

	public function index()
	{
		$this->is_allowed('menu_kalender');
		
		$this->data['campaigns'] = $this->db->select('id, name')->get('campaigns')->result();
		$this->data['kols'] = $this->db->select('id, name')->get('kols')->result();
		$this->data['platforms'] = $this->db->select('id, name')->get('platforms')->result();

		$this->template->title('Kalender Posting');
		$this->render('backend/standart/administrator/kalender/kalender', $this->data);
	}

	public function get_events()
	{
		$this->is_allowed('menu_kalender');

		$start = $this->input->get('start');
		$end = $this->input->get('end');
		$campaign_id = $this->input->get('campaign_id');
		$kol_id = $this->input->get('kol_id');
		$platform_id = $this->input->get('platform_id');
		$status = $this->input->get('status');

		$this->db->select('tasks.*, kols.name as kol_name, kols.id as real_kol_id, campaigns.name as campaign_name, campaigns.id as real_campaign_id, campaigns.start_date as campaign_start_date, campaigns.end_date as campaign_end_date, platforms.name as platform_name, variants.name as variant_name, postings.live_url as posting_live_url, briefs.content as brief_content, briefs.status as brief_status, briefs.title as brief_title, campaign_kols.deal_scope as sow_file');
		$this->db->from('tasks');
		$this->db->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'left');
		$this->db->join('kols', 'kols.id = campaign_kols.kol_id', 'left');
		$this->db->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'left');
		$this->db->join('platforms', 'platforms.id = tasks.platform_id', 'left');
		$this->db->join('variants', 'variants.id = tasks.variant_id', 'left');
		$this->db->join('postings', 'postings.task_id = tasks.id', 'left');
		$this->db->join('briefs', 'briefs.task_id = tasks.id', 'left');
		
		if ($start) {
			$this->db->where('tasks.estimated_posting_date >=', $start);
		}
		if ($end) {
			$this->db->where('tasks.estimated_posting_date <=', $end);
		}
		if ($campaign_id) {
			$this->db->where('campaign_kols.campaign_id', $campaign_id);
		}
		if ($kol_id) {
			$this->db->where('campaign_kols.kol_id', $kol_id);
		}
		if ($platform_id) {
			$this->db->where('tasks.platform_id', $platform_id);
		}
		if ($status) {
			if ($status === 'POSTED_LIVE') {
				$this->db->where_in('tasks.status', ['POSTED', 'DONE', 'SUCCESS']);
			} elseif ($status === 'NOT_POSTED') {
				$this->db->where_not_in('tasks.status', ['POSTED', 'DONE', 'SUCCESS', 'CANCELLED', 'REJECTED']);
			} else {
				$this->db->where('tasks.status', $status);
			}
		}

		// Exclude cancelled and rejected tasks from the calendar globally
		$this->db->where_not_in('tasks.status', ['CANCELLED', 'REJECTED']);

		$tasks = $this->db->get()->result();

		$events = [];
		$today_ts = strtotime(date('Y-m-d'));

		foreach ($tasks as $task) {
			$status_upper = strtoupper(trim($task->status ?? 'DRAFT'));

			// Status color mapping
			if (in_array($status_upper, ['POSTED', 'DONE', 'SUCCESS'])) {
				$color = '#16a34a'; // Green (Sudah Post Video Live)
			} elseif (in_array($status_upper, ['CANCELLED', 'REJECTED'])) {
				$color = '#dc2626'; // Bright Red (Dibatalkan)
			} else {
				$color = '#0284c7'; // Blue (Belum Upload Video Live)
			}

			// Calculate deadline urgency
			$task_date_ts = strtotime($task->estimated_posting_date);
			$diff_days = ($task_date_ts && $today_ts) ? (int) floor(($task_date_ts - $today_ts) / 86400) : 0;

			$is_completed = in_array($status_upper, ['POSTED', 'DONE', 'SUCCESS']);
			$is_cancelled = in_array($status_upper, ['CANCELLED', 'REJECTED']);

			if ($is_completed) {
				$deadline_type = 'completed';
				$deadline_label = 'Posted';
				$deadline_badge_bg = '#dcfce7';
				$deadline_badge_color = '#15803d';
			} elseif ($is_cancelled) {
				$deadline_type = 'cancelled';
				$deadline_label = 'Cancelled';
				$deadline_badge_bg = '#fee2e2';
				$deadline_badge_color = '#b91c1c';
			} elseif ($diff_days < 0) {
				$deadline_type = 'overdue';
				$deadline_label = 'Lewat ' . abs($diff_days) . 'h';
				$deadline_badge_bg = '#fee2e2';
				$deadline_badge_color = '#b91c1c';
			} elseif ($diff_days == 0) {
				$deadline_type = 'today';
				$deadline_label = 'Hari Ini';
				$deadline_badge_bg = '#fef3c7';
				$deadline_badge_color = '#b45309';
			} elseif ($diff_days <= 3) {
				$deadline_type = 'urgent';
				$deadline_label = 'H-' . $diff_days;
				$deadline_badge_bg = '#fef3c7';
				$deadline_badge_color = '#b45309';
			} else {
				$deadline_type = 'normal';
				$deadline_label = 'H-' . $diff_days;
				$deadline_badge_bg = '#e0f2fe';
				$deadline_badge_color = '#0369a1';
			}

			$campaign_str = $task->campaign_name ? $task->campaign_name : 'Campaign';
			$task_kol_str = $task->title ? $task->title : ($task->kol_name ? $task->kol_name : 'KOL');
			$title = '[' . $campaign_str . '] - ' . $task_kol_str;
			$live_url = !empty($task->live_url) ? $task->live_url : (!empty($task->posting_live_url) ? $task->posting_live_url : '');

			// Brief/SOW: prioritize campaign_kols.deal_scope (SOW from dealing kol),
			// then tasks.brief_file, then briefs.content (text only, no file)
			$sow_file = !empty($task->sow_file) ? $task->sow_file : '';
			$brief_task_file = !empty($task->brief_file) ? $task->brief_file : '';
			$brief_file_url = '';
			$brief_source = '';

			if (!empty($sow_file)) {
				$brief_file_url = base_url('uploads/campaign_kols/' . $sow_file);
				$brief_source = 'SOW dari Dealing Kol';
			} elseif (!empty($brief_task_file)) {
				$brief_file_url = base_url('uploads/tasks/' . $brief_task_file);
				$brief_source = 'Brief dari Task';
			}

			// Draft: get latest draft from drafts table, fallback to tasks.draft_url
			$draft_url = $task->draft_url ?: '';
			if (empty($draft_url)) {
				$latest_draft = $this->db->select('draft_url')->where('task_id', $task->id)->order_by('version', 'DESC')->limit(1)->get('drafts')->row();
				if ($latest_draft && !empty($latest_draft->draft_url)) {
					$draft_url = $latest_draft->draft_url;
				}
			}

			$events[] = [
				'id' => $task->id,
				'title' => $title,
				'start' => $task->estimated_posting_date,
				'allDay' => true,
				'backgroundColor' => $color,
				'borderColor' => $color,
				'url' => site_url('administrator/tasks/view/' . $task->id),
				'description' => 'Status: ' . $task->status,
				
				// Extended details for card rendering and modal
				'task_id' => $task->id,
				'task_number' => $task->task_number,
				'raw_title' => $task->title ?: ('VT #' . $task->task_number),
				'kol_name' => $task->kol_name ?: '-',
				'kol_id' => $task->real_kol_id,
				'campaign_name' => $task->campaign_name ?: '-',
				'campaign_id' => $task->real_campaign_id,
				'campaign_start_date' => $task->campaign_start_date,
				'campaign_end_date' => $task->campaign_end_date,
				'platform_name' => $task->platform_name ?: 'Platform',
				'variant_name' => $task->variant_name ?: '-',
				'content_type' => $task->content_type ?: '-',
				'status_raw' => $task->status,
				'rate_formatted' => ($task->currency_code ?: 'IDR') . ' ' . number_format((float)($task->budget ?? 0), 0, ',', '.'),
				'notes' => $task->notes ?: '',
				'brief_file' => $sow_file ?: $brief_task_file,
				'brief_file_url' => $brief_file_url,
				'brief_source' => $brief_source,
				'brief_note' => $task->brief_note ?: ($task->brief_title ?: ''),
				'draft_url' => $draft_url,
				'draft_note' => $task->draft_note ?: '',
				'live_url' => $live_url,
				
				// Deadline attributes
				'deadline_type' => $deadline_type,
				'deadline_label' => $deadline_label,
				'deadline_badge_bg' => $deadline_badge_bg,
				'deadline_badge_color' => $deadline_badge_color,
				'diff_days' => $diff_days
			];
		}

		// Generate banner events for unique campaigns
		$campaigns_bg = [];
		// Define a palette of soft pastel colors with distinct border accents
		$campaign_palettes = [
			['bg' => '#eef2ff', 'border' => '#6366f1', 'text' => '#4338ca'], // Indigo
			['bg' => '#f0fdf4', 'border' => '#16a34a', 'text' => '#166534'], // Emerald
			['bg' => '#f5f3ff', 'border' => '#8b5cf6', 'text' => '#5b21b6'], // Violet
			['bg' => '#fff7ed', 'border' => '#f97316', 'text' => '#9a3412'], // Orange
			['bg' => '#fdf2f8', 'border' => '#ec4899', 'text' => '#9d174d'], // Pink
			['bg' => '#f0fdfa', 'border' => '#14b8a6', 'text' => '#115e59'], // Teal
			['bg' => '#f8fafc', 'border' => '#475569', 'text' => '#1e293b']  // Slate
		];
		
		foreach ($tasks as $task) {
			if (!empty($task->real_campaign_id) && !isset($campaigns_bg[$task->real_campaign_id])) {
				if (!empty($task->campaign_start_date) && !empty($task->campaign_end_date)) {
					$campaigns_bg[$task->real_campaign_id] = true;
					
					// Pick a palette based on campaign ID modulo
					$color_idx = (int)$task->real_campaign_id % count($campaign_palettes);
					$palette = $campaign_palettes[$color_idx];

					$events[] = [
						'id' => 'camp_' . $task->real_campaign_id,
						'title' => 'Periode: ' . $task->campaign_name,
						'campaign_name' => $task->campaign_name,
						'start' => $task->campaign_start_date,
						// Add +1 day to end_date because fullcalendar end date is exclusive
						'end' => date('Y-m-d', strtotime($task->campaign_end_date . ' +1 day')),
						'allDay' => true,
						'backgroundColor' => $palette['bg'],
						'borderColor' => $palette['border'],
						'textColor' => $palette['text'],
						'camp_bg' => $palette['bg'],
						'camp_border' => $palette['border'],
						'camp_text' => $palette['text'],
						'is_campaign_banner' => true,
						'campaign_id' => $task->real_campaign_id
					];
				}
			}
		}

		header('Content-Type: application/json');
		echo json_encode($events);
	}
}

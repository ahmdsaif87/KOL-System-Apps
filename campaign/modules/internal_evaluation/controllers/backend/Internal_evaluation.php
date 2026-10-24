<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Internal_evaluation extends Admin {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('internal_evaluation/model_internal_evaluation');
    }

    public function index()
    {
        redirect('administrator/internal_evaluation/staff', 'refresh');
    }

    public function staff()
    {
        if (!$this->aauth->is_allowed('internal_evaluation_view')) {
            if (!$this->aauth->is_admin()) {
                show_error('Maaf, Anda tidak memiliki akses ke halaman ini.', 403, 'Akses Ditolak');
            }
        }

        if (get_option('internal_evaluation_toggle', 'yes') === 'no') {
            if (!$this->aauth->is_admin()) {
                show_error('Fitur Evaluasi Internal saat ini sedang dinonaktifkan oleh Administrator.', 403, 'Akses Ditolak');
            }
        }

        // Only campaign_id filter in header (no date filter)
        $campaign_id = (int)$this->input->get('campaign_id');
        $campaign_id = ($campaign_id > 0) ? $campaign_id : null;
        $this->data['active_campaign_id'] = $campaign_id;
        $this->data['campaigns'] = $this->model_internal_evaluation->get_campaigns_list();

        // Selected campaign details if filtered (404 on unknown id, not silent empty)
        $this->data['selected_campaign'] = null;
        if (!empty($campaign_id)) {
            foreach ($this->data['campaigns'] as $c) {
                if ((int)$c->id === $campaign_id) {
                    $this->data['selected_campaign'] = $c;
                    break;
                }
            }
            if ($this->data['selected_campaign'] === null) {
                show_404();
            }
        }

        // Staff pagination (perf guard for large rosters)
        $page = max(1, (int)$this->input->get('page'));
        $per_page = 200;
        $offset = ($page - 1) * $per_page;
        $this->data['page'] = $page;
        $this->data['per_page'] = $per_page;
        $this->data['total_staff'] = $this->model_internal_evaluation->get_staff_operational_count($campaign_id);
        $this->data['total_pages'] = max(1, (int)ceil($this->data['total_staff'] / $per_page));

        // Fetch Staff Operational KPIs & Team Aggregate Summary (batched, page-scoped)
        $staff_kpis = $this->model_internal_evaluation->get_staff_operational_kpis($campaign_id, $per_page, $offset);
        $this->data['staff_kpis'] = $staff_kpis;
        $this->data['team_summary'] = $this->model_internal_evaluation->get_operational_team_summary($staff_kpis);
        // Legend shares the model's scoring config — brackets shown here always match logic
        $this->data['legend'] = $this->model_internal_evaluation->get_legend();

        $this->template->title('Evaluasi Kinerja Staff Operasional');
        $this->render('backend/standart/administrator/internal_evaluation/evaluation_view', $this->data);
    }

    public function leader()
    {
        if (!$this->aauth->is_allowed('internal_evaluation_view')) {
            if (!$this->aauth->is_admin()) {
                show_error('Maaf, Anda tidak memiliki akses ke halaman ini.', 403, 'Akses Ditolak');
            }
        }

        if (get_option('internal_evaluation_toggle', 'yes') === 'no') {
            if (!$this->aauth->is_admin()) {
                show_error('Fitur Evaluasi Internal saat ini sedang dinonaktifkan oleh Administrator.', 403, 'Akses Ditolak');
            }
        }

        $campaign_id = (int)$this->input->get('campaign_id');
        $campaign_id = ($campaign_id > 0) ? $campaign_id : null;
        $this->data['active_campaign_id'] = $campaign_id;
        $this->data['campaigns'] = $this->model_internal_evaluation->get_campaigns_list();

        // Fetch Leader KPIs
        $leader_kpis = $this->model_internal_evaluation->get_leader_kpis($campaign_id, 200, 0);
        $this->data['leader_kpis'] = $leader_kpis;
        $this->data['team_summary'] = $this->model_internal_evaluation->get_leader_team_summary($leader_kpis);
        $this->data['legend'] = $this->model_internal_evaluation->get_legend();
        
        $this->template->title('Evaluasi Kinerja Leader');
        $this->render('backend/standart/administrator/internal_evaluation/evaluation_view_leader', $this->data);
    }

    public function export_excel()
    {
        // Backward compatibility: redirect bookmark lama ke staff_export_excel
        $query_string = $this->input->server('QUERY_STRING') ? '?' . $this->input->server('QUERY_STRING') : '';
        redirect('administrator/internal_evaluation/staff_export_excel' . $query_string, 'refresh');
    }

    public function leader_export_excel()
    {
        $campaign_id = (int)$this->input->get('campaign_id');
        $campaign_id = ($campaign_id > 0) ? $campaign_id : null;
        
        $leader_kpis = $this->model_internal_evaluation->get_leader_kpis($campaign_id, 2000, 0);
        $team_summary = $this->model_internal_evaluation->get_leader_team_summary($leader_kpis);
        
        $campaign_name = 'Semua Kampanye';
        if (!empty($campaign_id)) {
            $c = $this->db->get_where('campaigns', ['id' => $campaign_id])->row();
            if ($c) $campaign_name = $c->name;
        }

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Evaluasi_Kinerja_Leader_".date('Ymd').".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<style>
            .table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 12px; }
            .table th { background-color: #0f172a; color: #ffffff; padding: 10px; text-align: left; border: 1px solid #cbd5e1; }
            .table td { padding: 8px; border: 1px solid #cbd5e1; vertical-align: middle; }
        </style>";

        echo "<h2>Laporan Evaluasi Kinerja Leader</h2>";
        echo "<p>Kampanye: <strong>" . htmlspecialchars($campaign_name) . "</strong> | Tanggal Cetak: <strong>" . date('d F Y') . "</strong></p>";
        echo "<p>Views Attainment Tim: <strong>" . $team_summary->team_view_attainment . "%</strong> | Budget Efficiency: <strong>" . $team_summary->team_budget_efficiency . "%</strong> | Deal Conversion: <strong>" . $team_summary->team_deal_conversion . "%</strong> | Approval TAT: <strong>" . $team_summary->team_avg_approval_tat . " Jam</strong></p>";

        echo "<table class='table'>";
        echo "<tr>";
        echo "<th>No</th>";
        echo "<th>Nama Leader</th>";
        echo "<th>Username</th>";
        echo "<th>Campaign Di-handle</th>";
        echo "<th>Views Attainment</th>";
        echo "<th>CPV Efficiency</th>";
        echo "<th>CPE</th>";
        echo "<th>Deal Conversion</th>";
        echo "<th>Approval TAT</th>";
        echo "<th>Predikat Kinerja</th>";
        echo "</tr>";

        $no = 1;
        foreach ($leader_kpis as $l) {
            echo "<tr>";
            echo "<td>{$no}</td>";
            echo "<td><strong>" . htmlspecialchars($l->full_name) . "</strong></td>";
            echo "<td>@" . htmlspecialchars($l->username) . "</td>";
            echo "<td>" . $l->total_campaigns . " Campaign</td>";
            echo "<td>" . $l->view_attainment_pct . "% (" . number_format($l->actual_views,0,',','.') . "/" . number_format($l->total_target_views,0,',','.') . ")</td>";
            echo "<td>Rp " . number_format($l->actual_cpv,0,',','.') . " (" . $l->budget_efficiency_pct . "% spent)</td>";
            echo "<td>Rp " . number_format($l->actual_cpe,0,',','.') . " (" . number_format($l->actual_engagements,0,',','.') . " eng)</td>";
            echo "<td>" . $l->deal_conversion_pct . "% (" . $l->deal_success . "/" . $l->total_negotiated . ")</td>";
            
            $tat_text = ($l->avg_turnaround_hours !== null) ? $l->avg_turnaround_hours . ' Jam' : '-';
            echo "<td>" . $tat_text . "</td>";
            
            $grade_text = $l->grade_label;
            $score_text = ($l->score !== null) ? $l->score . ' (maks ' . $l->max_earned . ')' : '-';
            echo "<td><strong>" . htmlspecialchars($grade_text) . "</strong><br><span>" . htmlspecialchars($score_text) . "</span></td>";
            echo "</tr>";
            $no++;
        }

        if (empty($leader_kpis)) {
            echo "<tr><td colspan='10' style='text-align:center;'>Tidak ada data leader.</td></tr>";
        }
        echo "</table>";
    }

    public function staff_export_excel()
    {
        $campaign_id = (int)$this->input->get('campaign_id');
        $campaign_id = ($campaign_id > 0) ? $campaign_id : null;
        // Export covers the full roster, not just the current page
        $staff_kpis = $this->model_internal_evaluation->get_staff_operational_kpis($campaign_id, 2000, 0);
        $team_summary = $this->model_internal_evaluation->get_operational_team_summary($staff_kpis);
        
        $campaign_name = 'Semua Kampanye';
        if (!empty($campaign_id)) {
            $c = $this->db->get_where('campaigns', ['id' => $campaign_id])->row();
            if ($c) $campaign_name = $c->name;
        }

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Evaluasi_Kinerja_Staff_".date('Ymd').".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<style>
            .table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 12px; }
            .table th { background-color: #0f172a; color: #ffffff; padding: 10px; text-align: left; border: 1px solid #cbd5e1; }
            .table td { padding: 8px; border: 1px solid #cbd5e1; vertical-align: middle; }
            .eff-good { color: #059669; font-weight: bold; }
            .eff-bad { color: #e11d48; font-weight: bold; }
            .eff-null { color: #64748b; }
        </style>";

        echo "<h2>Laporan Evaluasi Kinerja Staff Operasional</h2>";
        echo "<p>Kampanye: <strong>" . htmlspecialchars($campaign_name) . "</strong> | Tanggal Cetak: <strong>" . date('d F Y') . "</strong></p>";
        echo "<p>Rata-rata On-Time Posting: <strong>" . $team_summary->team_on_time_rate . "%</strong> | Respon Review: <strong>" . $team_summary->team_avg_turnaround_hours . " Jam</strong> | Lead Time Kirim Sampel: <strong>" . $team_summary->team_avg_dispatch_days . " Hari</strong> | Task Selesai: <strong>" . $team_summary->completed_tasks . "/" . $team_summary->total_tasks . "</strong> | Avg CPV: <strong>Rp " . number_format($team_summary->team_avg_cpv, 0, ',', '.') . "</strong> | Avg CPE: <strong>Rp " . number_format($team_summary->team_avg_cpe, 0, ',', '.') . "</strong></p>";

        echo "<table class='table'>";
        echo "<tr>";
        echo "<th>No</th>";
        echo "<th>Nama Staf</th>";
        echo "<th>Username</th>";
        echo "<th>KOL Di-handle</th>";
        echo "<th>SLA On-Time</th>";
        echo "<th>Respon Review Draft</th>";
        echo "<th>Putaran Revisi</th>";
        echo "<th>Lead Time Kirim Sampel</th>";
        echo "<th>Task Tuntas</th>";
        echo "<th>CPV</th>";
        echo "<th>CPE</th>";
        echo "<th>Predikat Kinerja</th>";
        echo "</tr>";

        $no = 1;
        foreach ($staff_kpis as $s) {
            $sla_text = ($s->on_time_rate !== null) ? $s->on_time_rate . '% (' . $s->on_time_postings . '/' . $s->total_postings . ')' : '-';
            $tat_text = ($s->avg_turnaround_hours !== null) ? $s->avg_turnaround_hours . ' Jam' : '-';
            $disp_text = ($s->avg_dispatch_days !== null) ? $s->avg_dispatch_days . ' Hari' : '-';
            if (!empty($s->pending_shipments) && $s->pending_shipments > 0) {
                $disp_text .= ' (' . $s->pending_shipments . ' blm kirim)';
            }
            $rev_text = ($s->avg_revision_cycles !== null) ? $s->avg_revision_cycles . 'x' : '-';
            $task_text = $s->completed_tasks . '/' . $s->total_tasks;
            $cpv_text = ($s->actual_cpv > 0) ? 'Rp ' . number_format($s->actual_cpv, 0, ',', '.') : '-';
            $cpe_text = ($s->actual_cpe > 0) ? 'Rp ' . number_format($s->actual_cpe, 0, ',', '.') : '-';

            echo "<tr>";
            echo "<td>{$no}</td>";
            echo "<td><strong>" . htmlspecialchars($s->full_name) . "</strong></td>";
            echo "<td>@" . htmlspecialchars($s->username) . "</td>";
            echo "<td>" . $s->kol_count . " KOL</td>";
            echo "<td>" . $sla_text . "</td>";
            echo "<td>" . $tat_text . "</td>";
            echo "<td>" . $rev_text . "</td>";
            echo "<td>" . $disp_text . "</td>";
            echo "<td>" . $task_text . "</td>";
            echo "<td>" . $cpv_text . "</td>";
            echo "<td>" . $cpe_text . "</td>";
            $grade_text = $s->grade_label;
            if (!empty($s->is_provisional)) {
                $grade_text .= '*';
            }
            $score_text = ($s->score !== null) ? $s->score . ' (maks ' . $s->max_earned . ')' : '-';
            echo "<td><strong>" . htmlspecialchars($grade_text) . "</strong><br><span>" . htmlspecialchars($score_text) . "</span></td>";
            echo "</tr>";
            $no++;
        }

        if (empty($staff_kpis)) {
            echo "<tr><td colspan='12' style='text-align:center;'>Tidak ada data staf operasional.</td></tr>";
        }

        echo "</table>";

        // Legend (same config as scoring) + provisional footnote
        $legend = $this->model_internal_evaluation->get_legend();
        echo "<p><strong>Legenda:</strong></p>";
        echo "<table class='table'>";
        foreach ($legend['grades'] as $g) {
            echo "<tr><td><strong>" . htmlspecialchars($g['label']) . "</strong></td><td>" . htmlspecialchars($g['rule']) . "</td></tr>";
        }
        foreach ($legend['groups'] as $g) {
            echo "<tr><td><strong>" . htmlspecialchars($g['title']) . "</strong></td><td>" . htmlspecialchars($g['desc']) . "</td></tr>";
        }
        echo "</table>";
        echo "<p>* Nilai sementara: data masih sedikit (kurang dari " . $legend['minimums']['decided'] . " konten dinilai, kurang dari " . $legend['minimums']['drafts'] . " draf diulas) atau ada bagian yang belum ada datanya.</p>";
    }
}

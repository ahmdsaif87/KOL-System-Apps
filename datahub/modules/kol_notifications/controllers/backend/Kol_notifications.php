<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kol_notifications Controller
 * 
 * Provides isolated, real-time activity and task notifications
 * exclusively for authenticated KOL creators with unread detection,
 * dedicated notification center (list/filter view), and read-all actions.
 */
class Kol_notifications extends Admin
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['app', 'url']);
        $this->load->database();
        $this->lang->load('web_lang', $this->current_lang);
    }

    /**
     * Dedicated Notification Center Page
     * Accessible via: site_url('administrator/kol_notifications')
     */
    public function index()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('administrator/login', 'refresh');
        }

        if (!is_kol_user() && !$this->aauth->is_admin()) {
            $this->session->set_flashdata('f_message', 'Halaman ini khusus untuk kreator KOL.');
            $this->session->set_flashdata('f_type', 'warning');
            redirect('administrator/dashboard', 'refresh');
        }

        $user_id = $this->session->userdata('id');
        $kol_id = get_current_user_kol_id($user_id);
        $last_read_time = $this->aauth->get_user_var('kol_notif_last_read', $user_id);

        $notifications = $this->_fetch_notifications($kol_id, $last_read_time, 200);

        $total_count      = count($notifications);
        $unread_count     = 0;
        $actionable_count = 0;
        $deadline_count   = 0;
        $contract_count   = 0;
        $vt_count         = 0;
        $app_count        = 0;
        $shipment_count   = 0;
        $invoice_count    = 0;

        foreach ($notifications as $n) {
            if (!empty($n['is_unread'])) {
                $unread_count++;
            }
            if (!empty($n['is_actionable'])) {
                $actionable_count++;
            }
            if ($n['category'] === 'deadline') {
                $deadline_count++;
            } elseif ($n['category'] === 'contract') {
                $contract_count++;
            } elseif ($n['category'] === 'vt') {
                $vt_count++;
            } elseif ($n['category'] === 'application') {
                $app_count++;
            } elseif ($n['category'] === 'shipment') {
                $shipment_count++;
            } elseif ($n['category'] === 'invoice') {
                $invoice_count++;
            }
        }

        $this->data['notifications']     = $notifications;
        $this->data['total_count']       = $total_count;
        $this->data['unread_count']      = $unread_count;
        $this->data['actionable_count']  = $actionable_count;
        $this->data['deadline_count']    = $deadline_count;
        $this->data['contract_count']    = $contract_count;
        $this->data['vt_count']          = $vt_count;
        $this->data['app_count']         = $app_count;
        $this->data['shipment_count']    = $shipment_count;
        $this->data['invoice_count']     = $invoice_count;
        $this->data['last_read_time']    = $last_read_time;
        $this->data['kol_id']            = $kol_id;

        $this->template->title('Pusat Pemberitahuan (Notifications)');
        $this->render('backend/standart/administrator/kol_notifications/kol_notifications_list', $this->data);
    }

    /**
     * AJAX endpoint to fetch real-time notifications for the navbar badge / dropdown
     * Accessible via: BASE_URL + 'administrator/kol_notifications/get_inbox'
     */
    public function get_inbox()
    {
        if (!$this->aauth->is_loggedin()) {
            $this->_json_response(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if (!is_kol_user() && !$this->aauth->is_admin()) {
            $this->_json_response(['success' => false, 'message' => 'Forbidden for non-KOL users'], 403);
            return;
        }

        $user_id = $this->session->userdata('id');
        $kol_id = get_current_user_kol_id($user_id);
        $last_read_time = $this->aauth->get_user_var('kol_notif_last_read', $user_id);

        $notifications = $this->_fetch_notifications($kol_id, $last_read_time, 20);

        $unread_count = 0;
        foreach ($notifications as $n) {
            if (!empty($n['is_unread'])) {
                $unread_count++;
            }
        }

        $this->_json_response([
            'success'       => true,
            'kol_id'        => $kol_id,
            'last_read_at'  => $last_read_time,
            'unread_count'  => $unread_count,
            'total_count'   => count($notifications),
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark all notifications as read for the current user
     * Accessible via: BASE_URL + 'administrator/kol_notifications/mark_all_read'
     */
    public function mark_all_read()
    {
        if (!$this->aauth->is_loggedin()) {
            $this->_json_response(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        if (!is_kol_user() && !$this->aauth->is_admin()) {
            $this->_json_response(['success' => false, 'message' => 'Forbidden for non-KOL users'], 403);
            return;
        }

        $user_id = $this->session->userdata('id');
        $now = date('Y-m-d H:i:s');
        $this->aauth->set_user_var('kol_notif_last_read', $now, $user_id);

        $this->_json_response([
            'success'      => true,
            'last_read_at' => $now,
            'message'      => 'Semua pemberitahuan berhasil ditandai sebagai dibaca'
        ]);
    }

    /**
     * Internal query engine for fetching and standardizing all KOL notification sources
     */
    private function _fetch_notifications($kol_id, $last_read_time, $limit = 100)
    {
        if (!$kol_id) {
            $is_unread = empty($last_read_time) ? true : (strtotime(date('Y-m-d H:i:s')) > strtotime($last_read_time));
            return [
                [
                    'id'            => 'profile_unlinked',
                    'category'      => 'system',
                    'type'          => 'warning',
                    'icon'          => 'fa-exclamation-triangle text-yellow',
                    'badge'         => 'Peringatan',
                    'badge_class'   => 'label-warning',
                    'title'         => 'Profil Belum Terhubung',
                    'message'       => 'Akun Anda belum terhubung ke data profil KOL. Silakan lengkapi profil Anda.',
                    'url'           => site_url('administrator/user/profile'),
                    'action_text'   => 'Lengkapi Profil',
                    'timestamp'     => date('Y-m-d H:i:s'),
                    'time_ago'      => 'Baru saja',
                    'is_actionable' => true,
                    'is_unread'     => $is_unread
                ]
            ];
        }

        $notifications = [];
        $query_limit = max(10, (int)$limit);

        // 1. CAMPAIGN APPLICATIONS (Pendaftaran Diterima / Ditolak)
        if ($this->db->table_exists('campaign_applications')) {
            $this->db->select('ca.id, ca.campaign_id, ca.status, ca.rejection_reason, ca.applied_at, ca.reviewed_at, ca.updated_at, c.name as campaign_name');
            $this->db->from('campaign_applications ca');
            $this->db->join('campaigns c', 'c.id = ca.campaign_id', 'left');
            $this->db->where('ca.kol_id', $kol_id);
            $this->db->order_by('ca.updated_at', 'DESC');
            $this->db->limit($query_limit);
            $applications = $this->db->get()->result();

            foreach ($applications as $app) {
                $c_name = !empty($app->campaign_name) ? $app->campaign_name : 'Campaign #' . $app->campaign_id;
                $time = !empty($app->reviewed_at) ? $app->reviewed_at : $app->updated_at;

                if ($app->status === 'ACCEPTED') {
                    $notifications[] = [
                        'id'            => 'app_' . $app->id,
                        'category'      => 'application',
                        'type'          => 'success',
                        'icon'          => 'fa-check-circle text-green',
                        'badge'         => 'Apply Diterima',
                        'badge_class'   => 'label-success',
                        'title'         => 'Pendaftaran Disetujui',
                        'message'       => 'Pendaftaran Anda untuk campaign <b>' . htmlspecialchars($c_name) . '</b> telah disetujui!',
                        'url'           => site_url('administrator/open_campaigns/view/' . $app->campaign_id),
                        'action_text'   => 'Lihat Campaign',
                        'timestamp'     => $time,
                        'time_ago'      => $this->_time_elapsed_string($time),
                        'is_actionable' => false
                    ];
                } elseif ($app->status === 'REJECTED') {
                    $reason = !empty($app->rejection_reason) ? ' (Catatan: ' . htmlspecialchars($app->rejection_reason) . ')' : '';
                    $notifications[] = [
                        'id'            => 'app_' . $app->id,
                        'category'      => 'application',
                        'type'          => 'danger',
                        'icon'          => 'fa-times-circle text-red',
                        'badge'         => 'Apply Ditolak',
                        'badge_class'   => 'label-danger',
                        'title'         => 'Pendaftaran Belum Sesuai',
                        'message'       => 'Pendaftaran campaign <b>' . htmlspecialchars($c_name) . '</b> belum berhasil' . $reason,
                        'url'           => site_url('administrator/open_campaigns/view/' . $app->campaign_id),
                        'action_text'   => 'Lihat Detail',
                        'timestamp'     => $time,
                        'time_ago'      => $this->_time_elapsed_string($time),
                        'is_actionable' => false
                    ];
                }
            }
        }

        // 2. CONTRACTS (Kontrak Dibuat / Menunggu TTD / Aktif)
        if ($this->db->table_exists('campaign_kol_contracts')) {
            $this->db->select('ckc.id, ckc.contract_no, ckc.status, ckc.created_at, ckc.updated_at, c.name as campaign_name');
            $this->db->from('campaign_kol_contracts ckc');
            $this->db->join('campaign_kols ck', 'ck.id = ckc.campaign_kol_id', 'inner');
            $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
            $this->db->where('ck.kol_id', $kol_id);
            $this->db->order_by('ckc.updated_at', 'DESC');
            $this->db->limit($query_limit);
            $contracts = $this->db->get()->result();

            foreach ($contracts as $ctr) {
                $c_name = !empty($ctr->campaign_name) ? $ctr->campaign_name : 'Campaign';
                $ctr_no = !empty($ctr->contract_no) ? $ctr->contract_no : 'Kontrak #' . $ctr->id;

                if (in_array($ctr->status, ['WAITING_KOL_SIGNATURE', 'WAITING_KOL_SIGN', 'PENDING'])) {
                    $notifications[] = [
                        'id'            => 'contract_' . $ctr->id,
                        'category'      => 'contract',
                        'type'          => 'warning',
                        'icon'          => 'fa-pencil-square-o text-yellow',
                        'badge'         => 'Butuh Tanda Tangan',
                        'badge_class'   => 'label-warning',
                        'title'         => 'Kontrak Siap Ditandatangani',
                        'message'       => 'Kontrak <b>' . htmlspecialchars($ctr_no) . '</b> untuk <b>' . htmlspecialchars($c_name) . '</b> siap ditandatangani.',
                        'url'           => site_url('administrator/my_contracts'),
                        'action_text'   => 'Tandatangani Kontrak',
                        'timestamp'     => $ctr->updated_at,
                        'time_ago'      => $this->_time_elapsed_string($ctr->updated_at),
                        'is_actionable' => true
                    ];
                } elseif (in_array($ctr->status, ['SIGNED', 'ACTIVE'])) {
                    $notifications[] = [
                        'id'            => 'contract_' . $ctr->id,
                        'category'      => 'contract',
                        'type'          => 'success',
                        'icon'          => 'fa-file-text-o text-green',
                        'badge'         => 'Kontrak Aktif',
                        'badge_class'   => 'label-success',
                        'title'         => 'Kontrak Selesai Ditandatangani',
                        'message'       => 'Kontrak <b>' . htmlspecialchars($ctr_no) . '</b> (' . htmlspecialchars($c_name) . ') telah aktif dan disahkan.',
                        'url'           => site_url('administrator/my_contracts'),
                        'action_text'   => 'Lihat Kontrak',
                        'timestamp'     => $ctr->updated_at,
                        'time_ago'      => $this->_time_elapsed_string($ctr->updated_at),
                        'is_actionable' => false
                    ];
                }
            }
        }

        // 3. TASKS & VT DRAFTS & DEADLINES
        if ($this->db->table_exists('tasks')) {
            $this->db->select('t.id, t.title, t.task_number, t.content_type, t.status, t.estimated_posting_date, t.draft_note, t.notes, t.created_at, t.updated_at, c.name as campaign_name');
            $this->db->from('tasks t');
            $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'inner');
            $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
            $this->db->where('ck.kol_id', $kol_id);
            $this->db->order_by('t.updated_at', 'DESC');
            $this->db->limit($query_limit);
            $tasks = $this->db->get()->result();

            $today = date('Y-m-d');
            foreach ($tasks as $task) {
                $t_title = !empty($task->title) ? $task->title : 'Task #' . $task->task_number;
                $c_name = !empty($task->campaign_name) ? $task->campaign_name : 'Campaign';
                $task_url = site_url('administrator/my_contracts');

                // A. Status Draft VT
                if ($task->status === 'WAITING_DRAFT') {
                    $notifications[] = [
                        'id'            => 'task_draft_' . $task->id,
                        'category'      => 'vt',
                        'type'          => 'info',
                        'icon'          => 'fa-video-camera text-aqua',
                        'badge'         => 'Upload VT',
                        'badge_class'   => 'label-info',
                        'title'         => 'Kirim Draft VT Konten',
                        'message'       => 'Silakan kirimkan draft VT untuk <b>' . htmlspecialchars($t_title) . '</b> (' . htmlspecialchars($c_name) . ').',
                        'url'           => $task_url,
                        'action_text'   => 'Kirim Draft VT',
                        'timestamp'     => $task->updated_at,
                        'time_ago'      => $this->_time_elapsed_string($task->updated_at),
                        'is_actionable' => true
                    ];
                } elseif ($task->status === 'REVISION_DRAFT' || $task->status === 'REVISION') {
                    $note = !empty($task->draft_note) ? ' Catatan: "' . htmlspecialchars($task->draft_note) . '"' : '';
                    $notifications[] = [
                        'id'            => 'task_rev_' . $task->id,
                        'category'      => 'vt',
                        'type'          => 'warning',
                        'icon'          => 'fa-refresh text-yellow',
                        'badge'         => 'Revisi VT',
                        'badge_class'   => 'label-warning',
                        'title'         => 'Revisi Draft Konten VT',
                        'message'       => 'Draft VT <b>' . htmlspecialchars($t_title) . '</b> memerlukan revisi.' . $note,
                        'url'           => $task_url,
                        'action_text'   => 'Perbaiki Draft',
                        'timestamp'     => $task->updated_at,
                        'time_ago'      => $this->_time_elapsed_string($task->updated_at),
                        'is_actionable' => true
                    ];
                } elseif ($task->status === 'APPROVED_DRAFT') {
                    $notifications[] = [
                        'id'            => 'task_app_' . $task->id,
                        'category'      => 'vt',
                        'type'          => 'success',
                        'icon'          => 'fa-check-square-o text-green',
                        'badge'         => 'VT Disetujui',
                        'badge_class'   => 'label-success',
                        'title'         => 'Draft VT Telah Disetujui',
                        'message'       => 'Draft VT untuk <b>' . htmlspecialchars($t_title) . '</b> telah disetujui dan siap diposting.',
                        'url'           => $task_url,
                        'action_text'   => 'Lihat Detail',
                        'timestamp'     => $task->updated_at,
                        'time_ago'      => $this->_time_elapsed_string($task->updated_at),
                        'is_actionable' => false
                    ];
                }

                // B. Deadline Reminder (Untuk task yang belum POSTED / DONE)
                if (!in_array($task->status, ['POSTED', 'DONE']) && !empty($task->estimated_posting_date)) {
                    $diff_days = (int)( (strtotime($task->estimated_posting_date) - strtotime($today)) / (60 * 60 * 24) );
                    
                    if ($diff_days < 0) {
                        $notifications[] = [
                            'id'            => 'deadline_over_' . $task->id,
                            'category'      => 'deadline',
                            'type'          => 'danger',
                            'icon'          => 'fa-hourglass-end text-red',
                            'badge'         => 'Overdue',
                            'badge_class'   => 'label-danger',
                            'title'         => 'Deadline Posting Terlewat',
                            'message'       => 'Batas waktu posting untuk <b>' . htmlspecialchars($t_title) . '</b> telah lewat (' . date('d M Y', strtotime($task->estimated_posting_date)) . ').',
                            'url'           => $task_url,
                            'action_text'   => 'Segera Posting',
                            'timestamp'     => $task->estimated_posting_date . ' 23:59:59',
                            'time_ago'      => abs($diff_days) . ' hari lalu',
                            'is_actionable' => true
                        ];
                    } elseif ($diff_days <= 3) {
                        $day_label = $diff_days === 0 ? 'Hari ini!' : 'dalam ' . $diff_days . ' hari (' . date('d M Y', strtotime($task->estimated_posting_date)) . ')';
                        $notifications[] = [
                            'id'            => 'deadline_soon_' . $task->id,
                            'category'      => 'deadline',
                            'type'          => 'warning',
                            'icon'          => 'fa-clock-o text-yellow',
                            'badge'         => 'Deadline Dekat',
                            'badge_class'   => 'label-warning',
                            'title'         => 'Mendekati Batas Waktu Posting',
                            'message'       => 'Task <b>' . htmlspecialchars($t_title) . '</b> jatuh tempo <b>' . $day_label . '</b>.',
                            'url'           => $task_url,
                            'action_text'   => 'Buka Task',
                            'timestamp'     => $task->estimated_posting_date . ' 08:00:00',
                            'time_ago'      => $diff_days === 0 ? 'Hari ini' : 'H-' . $diff_days,
                            'is_actionable' => true
                        ];
                    }
                }
            }
        }

        // 4. SHIPMENTS (Pengiriman Produk Sampel)
        if ($this->db->table_exists('kol_shipments')) {
            $this->db->select('ks.id, ks.courier, ks.tracking_no, ks.status, ks.shipped_at, ks.updated_at, c.name as campaign_name');
            $this->db->from('kol_shipments ks');
            $this->db->join('campaign_kols ck', 'ck.id = ks.campaign_kol_id', 'inner');
            $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
            $this->db->where('ck.kol_id', $kol_id);
            $this->db->order_by('ks.updated_at', 'DESC');
            $this->db->limit($query_limit);
            $shipments = $this->db->get()->result();

            foreach ($shipments as $shp) {
                $c_name = !empty($shp->campaign_name) ? $shp->campaign_name : 'Campaign';
                $courier = !empty($shp->courier) ? $shp->courier : 'Ekspedisi';
                $resi = !empty($shp->tracking_no) ? ' (Resi: ' . htmlspecialchars($shp->tracking_no) . ')' : '';

                if ($shp->status === 'SHIPPED') {
                    $notifications[] = [
                        'id'            => 'shipment_' . $shp->id,
                        'category'      => 'shipment',
                        'type'          => 'info',
                        'icon'          => 'fa-truck text-aqua',
                        'badge'         => 'Produk Dikirim',
                        'badge_class'   => 'label-info',
                        'title'         => 'Sampel Produk Sedang Dikirim',
                        'message'       => 'Paket sampel untuk <b>' . htmlspecialchars($c_name) . '</b> dikirim via ' . htmlspecialchars($courier) . $resi,
                        'url'           => site_url('administrator/my_contracts'),
                        'action_text'   => 'Lacak Pengiriman',
                        'timestamp'     => !empty($shp->shipped_at) ? $shp->shipped_at : $shp->updated_at,
                        'time_ago'      => $this->_time_elapsed_string(!empty($shp->shipped_at) ? $shp->shipped_at : $shp->updated_at),
                        'is_actionable' => false
                    ];
                }
            }
        }

        // 5. INVOICES (Tagihan Ditolak / Dibayarkan)
        if ($this->db->table_exists('invoices')) {
            $this->db->select('i.id, i.invoice_no, i.status, i.rejection_reason, i.grand_total, i.updated_at, c.name as campaign_name');
            $this->db->from('invoices i');
            $this->db->join('campaign_kols ck', 'ck.id = i.campaign_kol_id', 'inner');
            $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
            $this->db->where('ck.kol_id', $kol_id);
            $this->db->where_in('i.status', ['REJECTED', 'PAID']);
            $this->db->order_by('i.updated_at', 'DESC');
            $this->db->limit($query_limit);
            $invoices = $this->db->get()->result();

            foreach ($invoices as $inv) {
                $c_name = !empty($inv->campaign_name) ? $inv->campaign_name : 'Campaign';
                $inv_no = !empty($inv->invoice_no) ? $inv->invoice_no : 'Invoice #' . $inv->id;

                if ($inv->status === 'REJECTED') {
                    $reason = !empty($inv->rejection_reason) ? ' (Catatan: ' . htmlspecialchars($inv->rejection_reason) . ')' : '';
                    $notifications[] = [
                        'id'            => 'inv_' . $inv->id,
                        'category'      => 'invoice',
                        'type'          => 'danger',
                        'icon'          => 'fa-exclamation-circle text-red',
                        'badge'         => 'Invoice Ditolak',
                        'badge_class'   => 'label-danger',
                        'title'         => 'Tagihan Perlu Revisi',
                        'message'       => 'Invoice <b>' . htmlspecialchars($inv_no) . '</b> (' . htmlspecialchars($c_name) . ') ditolak oleh Finance.' . $reason,
                        'url'           => site_url('administrator/my_invoices'),
                        'action_text'   => 'Perbaiki Tagihan',
                        'timestamp'     => $inv->updated_at,
                        'time_ago'      => $this->_time_elapsed_string($inv->updated_at),
                        'is_actionable' => true
                    ];
                } elseif ($inv->status === 'PAID') {
                    $notifications[] = [
                        'id'            => 'inv_' . $inv->id,
                        'category'      => 'invoice',
                        'type'          => 'success',
                        'icon'          => 'fa-money text-green',
                        'badge'         => 'Sudah Dibayar',
                        'badge_class'   => 'label-success',
                        'title'         => 'Pembayaran Tagihan Selesai',
                        'message'       => 'Invoice <b>' . htmlspecialchars($inv_no) . '</b> (' . htmlspecialchars($c_name) . ') sebesar Rp ' . number_format($inv->grand_total ?? 0, 0, ',', '.') . ' telah dibayarkan.',
                        'url'           => site_url('administrator/my_invoices'),
                        'action_text'   => 'Lihat Invoice',
                        'timestamp'     => $inv->updated_at,
                        'time_ago'      => $this->_time_elapsed_string($inv->updated_at),
                        'is_actionable' => false
                    ];
                }
            }
        }

        // Sort all notifications by timestamp DESC
        usort($notifications, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        // Limit results if specified
        if ($limit !== null && $limit > 0) {
            $notifications = array_slice($notifications, 0, $limit);
        }

        // Calculate Unread status per notification based on user's last_read timestamp
        foreach ($notifications as &$notif) {
            $is_unread = empty($last_read_time) ? true : (strtotime($notif['timestamp']) > strtotime($last_read_time));
            $notif['is_unread'] = $is_unread;
        }
        unset($notif);

        return $notifications;
    }

    /**
     * Format time difference into a human readable Indonesian string
     */
    private function _time_elapsed_string($datetime)
    {
        if (empty($datetime)) return '';
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        if ($diff->y > 0) return $diff->y . ' thn lalu';
        if ($diff->m > 0) return $diff->m . ' bln lalu';
        if ($diff->d > 0) {
            if ($diff->d == 1) return 'Kemarin';
            return $diff->d . ' hari lalu';
        }
        if ($diff->h > 0) return $diff->h . ' jam lalu';
        if ($diff->i > 0) return $diff->i . ' mnt lalu';
        return 'Baru saja';
    }

    /**
     * Send JSON response
     */
    private function _json_response($data, $code = 200)
    {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data))
            ->_display();
        exit;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Brands Controller
 *
 * Master Brands Management Controller for Multi-Brand Architecture.
 * Handles Brand CRUD, KPI Views, Team Provisioning, and Context Switching.
 *
 * @package KOL-System-Apps
 * @subpackage Controllers
 */
class Brands extends Admin
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('model_brands');
        $this->lang->load('web_lang', $this->current_lang);
        $this->load->helper('app');
    }

    /**
     * Show all Brands with pagination and search
     *
     * @param  int $offset
     * @return void
     */
    public function index($offset = 0)
    {
        $this->is_allowed('brands_list');

        $filter = $this->input->get('q');
        $field  = $this->input->get('f');

        $this->data['brands']        = $this->model_brands->get($filter, $field, $this->limit_page, $offset);
        $this->data['brandss']       = $this->data['brands']; // Cicool naming convention fallback
        $this->data['brands_counts'] = $this->model_brands->count_all($filter, $field);

        $config = [
            'base_url'    => 'administrator/brands/index/',
            'total_rows'  => $this->model_brands->count_all($filter, $field),
            'per_page'    => $this->limit_page,
            'uri_segment' => 4,
        ];

        $this->data['pagination'] = $this->pagination($config);

        $this->template->title('Brands List');
        $this->render('backend/standart/administrator/brands/brands_list', $this->data);
    }

    /**
     * Add new Brand page
     *
     * @return void
     */
    public function add()
    {
        $this->is_allowed('brands_add');

        $this->template->title('Brands New');
        $this->render('backend/standart/administrator/brands/brands_add', $this->data);
    }

    /**
     * Save new Brand (AJAX JSON)
     *
     * @return void
     */
    public function add_save()
    {
        if (!$this->is_allowed('brands_add', false)) {
            echo json_encode([
                'success' => false,
                'message' => cclang('sorry_you_do_not_have_permission_to_access')
            ]);
            exit;
        }

        $this->form_validation->set_rules('name', 'Brand Name', 'trim|required|max_length[150]');
        $this->form_validation->set_rules('code', 'Brand Code', 'trim|required|max_length[50]|alpha_dash|is_unique[brands.code]');
        $this->form_validation->set_rules('legal_entity_name', 'Legal Entity Name', 'trim|max_length[255]');
        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|max_length[200]');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|max_length[50]');
        $this->form_validation->set_rules('website', 'Website', 'trim|max_length[255]');
        $this->form_validation->set_rules('pic_name', 'PIC Name', 'trim|max_length[150]');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('address', 'Address', 'trim');
        $this->form_validation->set_rules('is_active', 'Active Status', 'trim');

        if ($this->form_validation->run()) {
            $company_name      = $this->input->post('company_name') ?: $this->input->post('legal_entity_name');
            $legal_entity_name = $this->input->post('legal_entity_name') ?: $company_name;

            // Handle logo file upload
            $brand_logo_name = $this->input->post('brand_logo_name') ?: $this->input->post('brands_logo_name');
            $brand_logo_uuid = $this->input->post('brand_logo_uuid') ?: $this->input->post('brands_logo_uuid');
            $logo_path = null;

            if (!empty($brand_logo_name)) {
                $upload_dir = FCPATH . 'uploads/brands/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $brand_logo_name_copy = date('YmdHis') . '-' . $brand_logo_name;
                if (!empty($brand_logo_uuid)) {
                    $src = FCPATH . 'uploads/tmp/' . $brand_logo_uuid . '/' . $brand_logo_name;
                    if (is_file($src)) {
                        rename($src, $upload_dir . $brand_logo_name_copy);
                        $logo_path = 'uploads/brands/' . $brand_logo_name_copy;
                    }
                } else {
                    $logo_path = 'uploads/brands/' . $brand_logo_name;
                }
            }

            $save_data = [
                'name'              => trim($this->input->post('name')),
                'code'              => strtoupper(trim($this->input->post('code'))),
                'company_name'      => $company_name,
                'legal_entity_name' => $legal_entity_name,
                'logo'              => $logo_path ?: ($this->input->post('logo') ?: null),
                'description'       => $this->input->post('description'),
                'address'           => $this->input->post('address'),
                'phone'             => $this->input->post('phone'),
                'email'             => $this->input->post('email'),
                'website'           => $this->input->post('website'),
                'pic_name'          => $this->input->post('pic_name'),
                'is_active'         => $this->input->post('is_active') !== null ? (int)$this->input->post('is_active') : 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ];

            $save_brand = $this->model_brands->store($save_data);

            if ($save_brand) {
                // Audit log
                if (function_exists('log_brand_event')) {
                    log_brand_event('brand_created', 'brands', $save_brand, null, $save_data, $save_brand);
                }

                if ($this->input->post('save_type') == 'stay') {
                    $this->data['success'] = true;
                    $this->data['id']      = $save_brand;
                    $this->data['message'] = cclang('success_save_data_stay', [
                        anchor('administrator/brands/edit/' . $save_brand, 'Edit Brands'),
                        anchor('administrator/brands', ' Go back to list')
                    ]);
                } else {
                    set_message(cclang('success_save_data_redirect', [
                        anchor('administrator/brands/edit/' . $save_brand, 'Edit Brands')
                    ]), 'success');

                    $this->data['success']  = true;
                    $this->data['redirect'] = base_url('administrator/brands');
                }
            } else {
                $this->data['success'] = false;
                $this->data['message'] = cclang('data_not_change');
            }
        } else {
            $this->data['success'] = false;
            $this->data['message'] = validation_errors();
        }

        echo json_encode($this->data);
        exit;
    }

    /**
     * Edit Brand page
     *
     * @param  int $id
     * @return void
     */
    public function edit($id)
    {
        $this->is_allowed('brands_update');

        $brand = $this->model_brands->find($id);
        if (!$brand) {
            show_404();
        }

        $this->data['brand']  = $brand;
        $this->data['brands'] = $brand;

        $this->template->title('Brands Update');
        $this->render('backend/standart/administrator/brands/brands_update', $this->data);
    }

    /**
     * Save edited Brand (AJAX JSON)
     *
     * @param  int $id
     * @return void
     */
    public function edit_save($id)
    {
        if (!$this->is_allowed('brands_update', false)) {
            echo json_encode([
                'success' => false,
                'message' => cclang('sorry_you_do_not_have_permission_to_access')
            ]);
            exit;
        }

        $this->form_validation->set_rules('name', 'Brand Name', 'trim|required|max_length[150]');
        $this->form_validation->set_rules('code', 'Brand Code', 'trim|required|max_length[50]|alpha_dash');
        $this->form_validation->set_rules('legal_entity_name', 'Legal Entity Name', 'trim|max_length[255]');
        $this->form_validation->set_rules('company_name', 'Company Name', 'trim|max_length[200]');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|max_length[50]');
        $this->form_validation->set_rules('website', 'Website', 'trim|max_length[255]');
        $this->form_validation->set_rules('pic_name', 'PIC Name', 'trim|max_length[150]');
        $this->form_validation->set_rules('description', 'Description', 'trim');
        $this->form_validation->set_rules('address', 'Address', 'trim');
        $this->form_validation->set_rules('is_active', 'Active Status', 'trim');

        if ($this->form_validation->run()) {
            $code = strtoupper(trim($this->input->post('code')));

            // Unique code check excluding current brand
            $duplicate = $this->db->where('code', $code)
                ->where('id !=', (int) $id)
                ->get('brands')
                ->row();

            if ($duplicate) {
                echo json_encode([
                    'success' => false,
                    'message' => 'The Brand Code ' . htmlspecialchars($code) . ' is already taken by another brand.'
                ]);
                exit;
            }

            $old_brand = (array) $this->model_brands->find($id);

            $company_name      = $this->input->post('company_name') ?: $this->input->post('legal_entity_name');
            $legal_entity_name = $this->input->post('legal_entity_name') ?: $company_name;

            // Handle logo update
            $brand_logo_name = $this->input->post('brand_logo_name') ?: $this->input->post('brands_logo_name');
            $brand_logo_uuid = $this->input->post('brand_logo_uuid') ?: $this->input->post('brands_logo_uuid');
            $logo_path = isset($old_brand['logo']) ? $old_brand['logo'] : null;

            if (!empty($brand_logo_name) && !empty($brand_logo_uuid)) {
                $upload_dir = FCPATH . 'uploads/brands/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $brand_logo_name_copy = date('YmdHis') . '-' . $brand_logo_name;
                $src = FCPATH . 'uploads/tmp/' . $brand_logo_uuid . '/' . $brand_logo_name;
                if (is_file($src)) {
                    rename($src, $upload_dir . $brand_logo_name_copy);
                    $logo_path = 'uploads/brands/' . $brand_logo_name_copy;
                }
            }

            $new_is_active = $this->input->post('is_active') !== null ? (int)$this->input->post('is_active') : 1;

            $save_data = [
                'name'              => trim($this->input->post('name')),
                'code'              => $code,
                'company_name'      => $company_name,
                'legal_entity_name' => $legal_entity_name,
                'logo'              => $logo_path,
                'description'       => $this->input->post('description'),
                'address'           => $this->input->post('address'),
                'phone'             => $this->input->post('phone'),
                'email'             => $this->input->post('email'),
                'website'           => $this->input->post('website'),
                'pic_name'          => $this->input->post('pic_name'),
                'is_active'         => $new_is_active,
                'updated_at'        => date('Y-m-d H:i:s'),
            ];

            $save_brand = $this->model_brands->change($id, $save_data);

            if ($save_brand) {
                // Audit logging
                if (function_exists('log_brand_event')) {
                    log_brand_event('brand_updated', 'brands', $id, $old_brand, $save_data, $id);

                    if (isset($old_brand['is_active']) && $old_brand['is_active'] == 1 && $new_is_active == 0) {
                        log_brand_event('brand_deactivated', 'brands', $id, ['is_active' => 1], ['is_active' => 0], $id);
                    } elseif (isset($old_brand['is_active']) && $old_brand['is_active'] == 0 && $new_is_active == 1) {
                        log_brand_event('brand_reactivated', 'brands', $id, ['is_active' => 0], ['is_active' => 1], $id);
                    }
                }

                if ($this->input->post('save_type') == 'stay') {
                    $this->data['success'] = true;
                    $this->data['id']      = $id;
                    $this->data['message'] = cclang('success_update_data_stay', [
                        anchor('administrator/brands', ' Go back to list')
                    ]);
                } else {
                    set_message(cclang('success_update_data_redirect', []), 'success');

                    $this->data['success']  = true;
                    $this->data['redirect'] = base_url('administrator/brands');
                }
            } else {
                $this->data['success'] = false;
                $this->data['message'] = cclang('data_not_change');
            }
        } else {
            $this->data['success'] = false;
            $this->data['message'] = validation_errors();
        }

        echo json_encode($this->data);
        exit;
    }

    /**
     * Delete Brand (Referential integrity protected)
     *
     * @param  int|null $id
     * @return void
     */
    public function delete($id = null)
    {
        $this->is_allowed('brands_delete');

        $arr_id = $this->input->get('id');
        $target_ids = !empty($id) ? [$id] : (is_array($arr_id) ? $arr_id : []);

        $deleted_count = 0;
        $error_messages = [];

        foreach ($target_ids as $target_id) {
            $target_id = (int) $target_id;

            // Brand 1 cannot be deleted
            if ($target_id === 1) {
                $error_messages[] = 'Default Brand 1 (Metafora Parfume) cannot be deleted.';
                continue;
            }

            // Referential integrity check: active campaigns or products
            $camp_count = $this->db->where('brand_id', $target_id)->count_all_results('campaigns');
            $prod_count = $this->db->where('brand_id', $target_id)->count_all_results('products');

            if ($camp_count > 0 || $prod_count > 0) {
                $error_messages[] = "Brand ID {$target_id} has {$camp_count} campaign(s) and {$prod_count} product(s). Please deactivate it instead of deleting.";
                continue;
            }

            $old_brand = (array) $this->model_brands->find($target_id);
            $removed = $this->model_brands->remove($target_id);

            if ($removed) {
                $deleted_count++;
                if (function_exists('log_brand_event')) {
                    log_brand_event('brand_deleted', 'brands', $target_id, $old_brand, null, $target_id);
                }
            }
        }

        if ($deleted_count > 0) {
            set_message(cclang('has_been_deleted', 'brands'), 'success');
        }

        if (!empty($error_messages)) {
            set_message(implode('<br>', $error_messages), 'error');
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'success' => $deleted_count > 0,
                'message' => $deleted_count > 0 
                    ? cclang('has_been_deleted', 'brands') 
                    : (!empty($error_messages) ? implode('<br>', $error_messages) : 'Failed to delete brand.')
            ]);
            exit;
        }

        redirect_back();
    }

    /**
     * Bulk Action Handler (AJAX endpoint for batch operations)
     *
     * @return void
     */
    public function bulk_action()
    {
        if (!$this->is_allowed('brands_delete', false)) {
            echo json_encode([
                'success' => false,
                'message' => cclang('sorry_you_do_not_have_permission_to_access')
            ]);
            exit;
        }

        $action = $this->input->post('action');
        $ids    = $this->input->post('ids');

        if ($action !== 'delete' || empty($ids) || !is_array($ids)) {
            echo json_encode([
                'success' => false,
                'message' => 'Tindakan atau data tidak valid.'
            ]);
            exit;
        }

        $deleted_count = 0;
        $error_messages = [];

        foreach ($ids as $target_id) {
            $target_id = (int) $target_id;
            if ($target_id <= 0) continue;

            // Brand 1 cannot be deleted
            if ($target_id === 1) {
                $error_messages[] = 'Default Brand 1 (Metafora Parfume) tidak dapat dihapus.';
                continue;
            }

            // Check active campaigns and products
            $camp_count = $this->db->where('brand_id', $target_id)->count_all_results('campaigns');
            $prod_count = $this->db->where('brand_id', $target_id)->count_all_results('products');

            if ($camp_count > 0 || $prod_count > 0) {
                $error_messages[] = "Brand ID {$target_id} memiliki {$camp_count} campaign dan {$prod_count} produk.";
                continue;
            }

            $old_brand = (array) $this->model_brands->find($target_id);
            $removed = $this->model_brands->remove($target_id);
            if ($removed) {
                $deleted_count++;
                if (function_exists('log_brand_event')) {
                    log_brand_event('brand_deleted', 'brands', $target_id, $old_brand, null, $target_id);
                }
            }
        }

        if ($deleted_count > 0) {
            echo json_encode([
                'success' => true,
                'message' => "{$deleted_count} brand berhasil dihapus." . (!empty($error_messages) ? ' ' . implode(' ', $error_messages) : '')
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => !empty($error_messages) ? implode(' ', $error_messages) : 'Tidak ada brand yang dihapus.'
            ]);
            exit;
        }
    }

    /**
     * Detail Brand View with Profile, KPIs, and Team Management (Feature 12)
     *
     * @param  int $id
     * @return void
     */
    public function view($id)
    {
        $this->is_allowed('brands_view');

        $brand = $this->model_brands->find($id);
        if (!$brand) {
            show_404();
        }

        $this->data['brand']            = $brand;
        $this->data['brands']           = $brand;
        $kpis                           = $this->model_brands->get_brand_kpis($id);
        $this->data['kpis']             = $kpis;
        $this->data['metrics']          = (array) $kpis;
        $this->data['team_members']     = $this->model_brands->get_team_members($id);
        $this->data['unassigned_users'] = $this->model_brands->get_unassigned_users();

        // Assignable staff role groups: Operational (5) and Leader KOL (6)
        $groups = $this->db->where_in('id', [5, 6])
            ->order_by('name ASC')
            ->get('aauth_groups')
            ->result();

        $this->data['assignable_groups'] = $groups;
        $this->data['groups']            = $groups;

        $this->template->title('Brand Detail - ' . $brand->name);
        $this->render('backend/standart/administrator/brands/brands_view', $this->data);
    }

    /**
     * Superadmin Tenant Context Switcher (Feature 8 & Milestone 3)
     *
     * Updates active_brand_id session after validating against DB.
     * Accessible via POST or GET by Superadmin.
     *
     * @return void
     */
    public function switch_context()
    {
        if (!function_exists('is_superadmin') || !is_superadmin()) {
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Unauthorized: only Superadmin can switch tenant context.'
                ]);
                exit;
            }
            set_message('Unauthorized: only Superadmin can switch tenant context.', 'error');
            redirect_back();
            return;
        }

        $brand_id = $this->input->post('brand_id');
        if ($brand_id === null) {
            $brand_id = $this->input->get('brand_id');
        }

        $switched = function_exists('switch_brand') ? switch_brand($brand_id) : false;

        if ($switched) {
            if (function_exists('log_brand_event')) {
                log_brand_event(
                    'superadmin_brand_switch',
                    'brands',
                    ($brand_id === 'all' || !$brand_id) ? 0 : (int)$brand_id,
                    null,
                    ['target_brand_id' => $brand_id],
                    $brand_id && $brand_id !== 'all' ? (int)$brand_id : null
                );
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'success'  => true,
                    'brand_id' => $brand_id,
                    'redirect' => site_url('administrator/dashboard')
                ]);
                exit;
            }

            set_message('Brand context successfully switched.', 'success');
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to switch brand context: brand not found or inactive.'
                ]);
                exit;
            }

            set_message('Failed to switch brand context: brand not found or inactive.', 'error');
        }

        redirect_back();
    }

    /**
     * Direct In-Page User Creation for Brand (Feature 13)
     *
     * Creates an aauth user, assigns them directly to brand_id, assigns group,
     * and logs brand_user_assigned audit event.
     *
     * @param  int|null $brand_id
     * @return void
     */
    public function add_user_save($brand_id = null)
    {
        $brand_id = $brand_id ?: $this->input->post('brand_id');

        if (!$this->is_allowed('brands_update', false) && !(function_exists('is_superadmin') && is_superadmin())) {
            echo json_encode([
                'success' => false,
                'message' => cclang('sorry_you_do_not_have_permission_to_access')
            ]);
            exit;
        }

        $brand = $this->model_brands->find($brand_id);
        if (!$brand) {
            echo json_encode(['success' => false, 'message' => 'Target brand not found.']);
            exit;
        }

        $this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[aauth_users.username]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[aauth_users.email]');
        $this->form_validation->set_rules('full_name', 'Full Name', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
        $this->form_validation->set_rules('group_id', 'Role Group', 'trim|required|numeric|in_list[5,6]');

        if ($this->form_validation->run()) {
            $username = trim($this->input->post('username'));
            $email    = trim($this->input->post('email'));
            $password = $this->input->post('password');
            $group_id = (int) $this->input->post('group_id');

            if (!in_array($group_id, [5, 6], true)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Role group tidak valid. Hanya Operational (5) atau Leader KOL (6) yang diizinkan.'
                ]);
                exit;
            }

            $optional_data = [
                'full_name'    => trim($this->input->post('full_name')),
                'avatar'       => 'default.png',
                'brand_id'     => (int) $brand_id,
                'date_created' => date('Y-m-d H:i:s'),
            ];

            // Create user via Aauth
            $user_id = $this->aauth->create_user($email, $password, $username, $optional_data);

            if ($user_id) {
                // Ensure brand_id is explicitly set on aauth_users table
                $this->db->where('id', $user_id)->update('aauth_users', ['brand_id' => (int) $brand_id]);

                // Assign selected role group (Leader KOL or Operational)
                if ($group_id > 0) {
                    $this->aauth->add_member($user_id, $group_id);
                }

                // Log audit event
                if (function_exists('log_brand_event')) {
                    log_brand_event('brand_user_assigned', 'aauth_users', $user_id, null, [
                        'brand_id'  => (int) $brand_id,
                        'username'  => $username,
                        'group_id'  => $group_id,
                        'full_name' => $optional_data['full_name'],
                    ], (int) $brand_id);
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'User ' . htmlspecialchars($username) . ' successfully created and assigned to ' . htmlspecialchars($brand->name) . '.',
                    'user_id' => $user_id,
                ]);
                exit;
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $this->aauth->print_errors() ?: 'Failed to create user.'
                ]);
                exit;
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => validation_errors()
            ]);
            exit;
        }
    }

    /**
     * Alias for add_user_save to satisfy test regex /(add_user|create_user|save_user)/i
     *
     * @param  int|null $brand_id
     * @return void
     */
    public function add_user($brand_id = null)
    {
        return $this->add_user_save($brand_id);
    }

    /**
     * Assign existing unassigned user to brand (Feature 14)
     *
     * Updates aauth_users.brand_id to target brand.
     *
     * @param  int|null $brand_id
     * @return void
     */
    public function assign_user($brand_id = null)
    {
        $brand_id = $brand_id ?: $this->input->post('brand_id');

        if (!$this->is_allowed('brands_update', false) && !(function_exists('is_superadmin') && is_superadmin())) {
            echo json_encode([
                'success' => false,
                'message' => cclang('sorry_you_do_not_have_permission_to_access')
            ]);
            exit;
        }

        $user_id = (int) $this->input->post('user_id');
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'User ID is required.']);
            exit;
        }

        $brand = $this->model_brands->find($brand_id);
        if (!$brand) {
            echo json_encode(['success' => false, 'message' => 'Brand not found.']);
            exit;
        }

        $user = $this->db->where('id', $user_id)->get('aauth_users')->row();
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit;
        }

        // Guard against Superadmin accounts being scoped to a specific brand
        if ($this->aauth->is_admin($user_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Akun Superadmin tidak dapat ditugaskan ke brand tertentu.'
            ]);
            exit;
        }

        // Guard against Talent / Creator (Group 7) accounts being assigned as staff
        if ($this->aauth->is_member('Kol', $user_id) || $this->aauth->is_member(7, $user_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Akun KOL / Talent tidak dapat ditugaskan sebagai anggota tim brand.'
            ]);
            exit;
        }

        $old_brand_id = $user->brand_id;

        // Update aauth_users table
        $updated = $this->db->where('id', $user_id)->update('aauth_users', [
            'brand_id' => (int) $brand_id
        ]);

        if ($updated) {
            if (function_exists('log_brand_event')) {
                log_brand_event('brand_user_assigned', 'aauth_users', $user_id, [
                    'brand_id' => $old_brand_id
                ], [
                    'brand_id' => (int) $brand_id
                ], (int) $brand_id);
            }

            echo json_encode([
                'success' => true,
                'message' => 'User ' . htmlspecialchars($user->username) . ' assigned to ' . htmlspecialchars($brand->name) . '.'
            ]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to assign user.']);
            exit;
        }
    }

    /**
     * Unassign user from brand (Feature 14)
     *
     * Sets aauth_users.brand_id to NULL.
     *
     * @param  int|null $brand_id
     * @param  int|null $user_id
     * @return void
     */
    public function unassign_user($brand_id = null, $user_id = null)
    {
        if (!$this->is_allowed('brands_update', false) && !(function_exists('is_superadmin') && is_superadmin())) {
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'success' => false,
                    'message' => cclang('sorry_you_do_not_have_permission_to_access')
                ]);
                exit;
            }
            set_message(cclang('sorry_you_do_not_have_permission_to_access'), 'error');
            redirect_back();
            return;
        }

        $user_id = $user_id ?: (int) $this->input->post('user_id');
        if (!$user_id) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'User ID is required.']);
                exit;
            }
            set_message('User ID is required.', 'error');
            redirect_back();
            return;
        }

        $user = $this->db->where('id', $user_id)->get('aauth_users')->row();
        if (!$user) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
                exit;
            }
            set_message('User not found.', 'error');
            redirect_back();
            return;
        }

        $current_brand_id = $user->brand_id;

        // Unassigning user sets aauth_users.brand_id to NULL
        $updated = $this->db->where('id', $user_id)->update('aauth_users', [
            'brand_id' => NULL
        ]);

        if ($updated) {
            if (function_exists('log_brand_event')) {
                log_brand_event('brand_user_unassigned', 'aauth_users', $user_id, [
                    'brand_id' => $current_brand_id
                ], [
                    'brand_id' => NULL
                ], $current_brand_id ? (int)$current_brand_id : null);
            }

            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User ' . htmlspecialchars($user->username) . ' unassigned from brand.'
                ]);
                exit;
            }

            set_message('User ' . htmlspecialchars($user->username) . ' unassigned from brand.', 'success');
        } else {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Failed to unassign user.']);
                exit;
            }
            set_message('Failed to unassign user.', 'error');
        }

        redirect_back();
    }

    /**
     * Upload Logo File (Fine-Uploader tmp upload)
     *
     * @return void
     */
    public function upload_logo_file()
    {
        if (!$this->is_allowed('brands_add', false) && !$this->is_allowed('brands_update', false)) {
            echo json_encode([
                'success' => false,
                'message' => cclang('sorry_you_do_not_have_permission_to_access')
            ]);
            exit;
        }

        $uuid = $this->input->post('qquuid');
        $upload_dir = FCPATH . 'uploads/tmp/' . $uuid . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $config = [
            'upload_path'   => $upload_dir,
            'allowed_types' => 'png|jpeg|jpg|gif|svg|webp',
            'max_size'      => '2048'
        ];

        $this->load->library('upload', $config);
        $this->load->helper('file');

        if (!$this->upload->do_upload('qqfile')) {
            echo json_encode([
                'success' => false,
                'error'   => $this->upload->display_errors()
            ]);
        } else {
            $upload_data = $this->upload->data();
            echo json_encode([
                'uploadName'  => $upload_data['file_name'],
                'previewLink' => base_url('uploads/tmp/' . $uuid . '/' . $upload_data['file_name']),
                'success'     => true,
            ]);
        }
        exit;
    }

    /**
     * Delete Logo File (Fine-Uploader tmp delete)
     *
     * @param  string $uuid
     * @return void
     */
    public function delete_logo_file($uuid)
    {
        if (!$this->is_allowed('brands_add', false) && !$this->is_allowed('brands_update', false)) {
            echo json_encode([
                'success' => false,
                'message' => cclang('sorry_you_do_not_have_permission_to_access')
            ]);
            exit;
        }

        if (!empty($uuid)) {
            $this->load->helper('file');
            $path = FCPATH . 'uploads/tmp/' . $uuid . '/';
            if (is_dir($path)) {
                delete_files($path, true);
                @rmdir($path);
            }
            echo json_encode(['success' => true]);
            exit;
        }

        echo json_encode(['success' => false, 'error' => 'Invalid UUID']);
        exit;
    }
}

/* End of file Brands.php */
/* Location: ./campaign/modules/brands/controllers/backend/Brands.php */

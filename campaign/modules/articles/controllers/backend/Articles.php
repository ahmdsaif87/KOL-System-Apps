<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Articles extends Admin
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('model_articles');
        $this->lang->load('web_lang', $this->current_lang);
    }

    public function index($offset = 0)
    {
        $this->is_allowed('articles_list');

        $filter   = $this->input->get('q');
        $field    = $this->input->get('f');
        $status   = $this->input->get('status');
        $category = $this->input->get('category');

        $this->data['articles']        = $this->model_articles->get($filter, $field, $this->limit_page, $offset, $status, $category);
        $this->data['articles_counts'] = $this->model_articles->count_all($filter, $field, $status, $category);
        $this->data['categories']      = $this->model_articles->categories();

        $config = [
            'base_url'    => 'administrator/articles/index/',
            'total_rows'  => $this->data['articles_counts'],
            'per_page'    => $this->limit_page,
            'uri_segment' => 4,
        ];

        $this->data['pagination'] = $this->pagination($config);

        $this->template->title('Artikel');
        $this->render('backend/standart/administrator/articles/articles_list', $this->data);
    }

    public function add()
    {
        $this->is_allowed('articles_add');

        $this->data['categories'] = $this->model_articles->categories();

        $this->template->title('Artikel Baru');
        $this->render('backend/standart/administrator/articles/articles_add', $this->data);
    }

    public function add_save()
    {
        $this->prepare_json_response();

        if ($this->input->method() !== 'post') {
            show_404();
        }

        if (!$this->is_allowed('articles_add', false)) {
            echo json_encode([
                'success' => false,
                'message' => cclang('sorry_you_do_not_have_permission_to_access'),
            ]);
            exit;
        }

        try {
            $this->form_validation->set_rules('title', 'Judul', 'trim|required|max_length[200]');
            $this->form_validation->set_rules('content', 'Konten', 'trim|required');
            $this->form_validation->set_rules('excerpt', 'Ringkasan', 'trim|max_length[500]');
            $this->form_validation->set_rules('tags', 'Tag', 'trim|max_length[255]');
            $this->form_validation->set_rules('meta_title', 'Meta title', 'trim|max_length[200]');
            $this->form_validation->set_rules('meta_description', 'Meta description', 'trim|max_length[300]');

            if (!$this->form_validation->run()) {
                echo json_encode([
                    'success' => false,
                    'message' => validation_errors(),
                ]);
                return;
            }

            $raw_cat = $this->input->post('category');
            if (is_array($raw_cat)) {
                $clean_cats = array_filter(array_map('trim', $raw_cat));
                $category = !empty($clean_cats) ? implode(', ', $clean_cats) : null;
            } else {
                $category = trim((string) $raw_cat) ?: null;
            }

            $title   = trim($this->input->post('title'));
            $content = $this->input->post('content');
            $status  = 'DRAFT';

            $save_data = [
                'title'            => $title,
                'slug'             => $this->unique_slug($title, $this->input->post('slug')),
                'excerpt'          => $this->build_excerpt($this->input->post('excerpt'), $content),
                'content'          => $content,
                'category'         => $category,
                'tags'             => $this->normalize_tags($this->input->post('tags')),
                'author_id'        => (int) $this->session->userdata('id'),
                'author_name'      => $this->current_author_name(),
                'status'           => $status,
                'is_featured'      => $this->input->post('is_featured') ? 1 : 0,
                'meta_title'       => trim($this->input->post('meta_title')) ?: null,
                'meta_description' => trim($this->input->post('meta_description')) ?: null,
                'published_at'     => $status === 'PUBLISHED' ? date('Y-m-d H:i:s') : null,
            ];

            $cover = $this->save_cover_from_tmp();
            if ($cover) {
                $save_data['cover_image'] = $cover;
            }

            $id = $this->model_articles->store($save_data);

            if (!$id) {
                echo json_encode([
                    'success' => false,
                    'message' => cclang('data_not_change'),
                ]);
                return;
            }

            if ($this->input->post('save_type') === 'stay') {
                $this->data['success'] = true;
                $this->data['id']      = $id;
                $this->data['message'] = cclang('success_save_data_stay', [
                    anchor('administrator/articles/edit/' . $id, 'Edit Artikel'),
                    anchor('administrator/articles', ' Kembali ke daftar'),
                ]);
            } else {
                set_message(cclang('success_save_data_redirect', [
                    anchor('administrator/articles/edit/' . $id, 'Edit Artikel'),
                ]), 'success');

                $this->data['success']  = true;
                $this->data['redirect'] = base_url('administrator/articles');
            }
        } catch (Throwable $e) {
            log_message('error', 'Articles add_save failed: ' . $e->getMessage());
            log_message('error', 'Database error: ' . json_encode($this->db->error()));

            $this->data['success'] = false;
            $this->data['message'] = 'Terjadi kesalahan saat menyimpan artikel.';
        }

        echo json_encode($this->data);
    }

    public function edit($id)
    {
        $this->is_allowed('articles_update');

        $article = $this->model_articles->find($id);

        if (!$article) {
            show_404();
        }

        $this->data['article']    = $article;
        $this->data['categories'] = $this->model_articles->categories();

        $this->template->title('Edit Artikel');
        $this->render('backend/standart/administrator/articles/articles_update', $this->data);
    }

    public function edit_save($id)
    {
        $this->prepare_json_response();

        if ($this->input->method() !== 'post') {
            show_404();
        }

        if (!$this->is_allowed('articles_update', false)) {
            echo json_encode([
                'success' => false,
                'message' => cclang('sorry_you_do_not_have_permission_to_access'),
            ]);
            exit;
        }

        try {
            $article = $this->model_articles->find($id);

            if (!$article) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Artikel tidak ditemukan.',
                ]);
                return;
            }

            $this->form_validation->set_rules('title', 'Judul', 'trim|required|max_length[200]');
            $this->form_validation->set_rules('content', 'Konten', 'trim|required');
            $this->form_validation->set_rules('excerpt', 'Ringkasan', 'trim|max_length[500]');
            $this->form_validation->set_rules('tags', 'Tag', 'trim|max_length[255]');
            $this->form_validation->set_rules('meta_title', 'Meta title', 'trim|max_length[200]');
            $this->form_validation->set_rules('meta_description', 'Meta description', 'trim|max_length[300]');

            if (!$this->form_validation->run()) {
                echo json_encode([
                    'success' => false,
                    'message' => validation_errors(),
                ]);
                return;
            }

            $raw_cat = $this->input->post('category');
            if (is_array($raw_cat)) {
                $clean_cats = array_filter(array_map('trim', $raw_cat));
                $category = !empty($clean_cats) ? implode(', ', $clean_cats) : null;
            } else {
                $category = trim((string) $raw_cat) ?: null;
            }

            $title   = trim($this->input->post('title'));
            $content = $this->input->post('content');
            $status  = $this->normalize_status($this->input->post('status'));

            if (!$this->aauth->is_allowed('articles_publish')) {
                $status = $article->status;
            }

            $save_data = [
                'title'            => $title,
                'slug'             => $this->unique_slug($title, $this->input->post('slug'), $id),
                'excerpt'          => $this->build_excerpt($this->input->post('excerpt'), $content),
                'content'          => $content,
                'category'         => $category,
                'tags'             => $this->normalize_tags($this->input->post('tags')),
                'status'           => $status,
                'is_featured'      => $this->input->post('is_featured') ? 1 : 0,
                'meta_title'       => trim($this->input->post('meta_title')) ?: null,
                'meta_description' => trim($this->input->post('meta_description')) ?: null,
            ];

            if ($status === 'PUBLISHED' && empty($article->published_at)) {
                $save_data['published_at'] = date('Y-m-d H:i:s');
            }

            if ($this->input->post('remove_cover') === '1') {
                $this->delete_cover_file_by_name($article->cover_image);
                $save_data['cover_image'] = null;
            }

            $cover = $this->save_cover_from_tmp();
            if ($cover) {
                $this->delete_cover_file_by_name($article->cover_image);
                $save_data['cover_image'] = $cover;
            }

            $saved = $this->model_articles->change($id, $save_data);

            if (!$saved) {
                echo json_encode([
                    'success' => false,
                    'message' => cclang('data_not_change'),
                ]);
                return;
            }

            if ($this->input->post('save_type') === 'stay') {
                $this->data['success'] = true;
                $this->data['id']      = $id;
                $this->data['message'] = cclang('success_update_data_stay', [
                    anchor('administrator/articles', ' Kembali ke daftar'),
                ]);
            } else {
                set_message(cclang('success_update_data_redirect', []), 'success');

                $this->data['success']  = true;
                $this->data['redirect'] = base_url('administrator/articles');
            }
        } catch (Throwable $e) {
            log_message('error', 'Articles edit_save failed: ' . $e->getMessage());
            log_message('error', 'Database error: ' . json_encode($this->db->error()));

            $this->data['success'] = false;
            $this->data['message'] = 'Terjadi kesalahan saat memperbarui artikel.';
        }

        echo json_encode($this->data);
    }

    public function view($id)
    {
        $this->is_allowed('articles_view');

        $article = $this->model_articles->find($id);

        if (!$article) {
            show_404();
        }

        $this->data['article'] = $article;

        $this->template->title('Detail Artikel');
        $this->render('backend/standart/administrator/articles/articles_view', $this->data);
    }

    public function delete($id = null)
    {
        $this->is_allowed('articles_delete');

        $this->load->helper('file');

        $arr_id = $this->input->get('id');
        $remove = false;

        if (!empty($id)) {
            $remove = $this->remove_article($id);
        } elseif (!empty($arr_id) && is_array($arr_id)) {
            foreach ($arr_id as $item_id) {
                $remove = $this->remove_article($item_id);
            }
        }

        if ($remove) {
            set_message(cclang('has_been_deleted', 'Artikel'), 'success');
        } else {
            set_message(cclang('error_delete', 'Artikel'), 'error');
        }

        redirect_back();
    }

    public function publish($id)
    {
        $this->change_status($id, 'PUBLISHED', 'articles_publish');
    }

    public function unpublish($id)
    {
        $this->change_status($id, 'DRAFT', 'articles_publish');
    }

    public function archive($id)
    {
        $this->change_status($id, 'ARCHIVED', 'articles_publish');
    }

    public function upload_cover_file()
    {
        $this->prepare_json_response();

        if (!$this->is_allowed('articles_add', false) && !$this->is_allowed('articles_update', false)) {
            echo json_encode([
                'success' => false,
                'error'   => cclang('sorry_you_do_not_have_permission_to_access'),
            ]);
            exit;
        }

        echo $this->upload_file([
            'uuid'          => $this->input->post('qquuid'),
            'table_name'    => 'articles',
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size'      => 2048,
        ]);
    }

    public function delete_cover_file($uuid = null)
    {
        $this->prepare_json_response();

        if (!$this->is_allowed('articles_update', false)) {
            echo json_encode([
                'success' => false,
                'error'   => cclang('sorry_you_do_not_have_permission_to_access'),
            ]);
            exit;
        }

        if (empty($uuid)) {
            $uuid = $this->input->get('qquuid') ?: $this->input->post('qquuid');
        }

        if (empty($uuid)) {
            echo json_encode([
                'success' => false,
                'error'   => 'File tidak ditemukan.',
            ]);
            return;
        }

        if ($this->input->get('by') === 'id') {
            echo $this->delete_file([
                'uuid'        => $uuid,
                'delete_by'   => 'id',
                'field_name'  => 'cover_image',
                'table_name'  => 'articles',
                'primary_key' => 'id',
                'upload_path' => 'uploads/articles/',
            ]);
            return;
        }

        echo $this->delete_file([
            'uuid'            => $uuid,
            'delete_by'       => 'tmp',
            'upload_path_tmp' => './uploads/tmp/',
            'table_name'      => 'articles',
        ]);
    }

    public function get_cover_file($id)
    {
        $this->prepare_json_response();

        if (!$this->is_allowed('articles_update', false)) {
            echo json_encode([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses file ini.',
            ]);
            exit;
        }

        echo $this->get_file([
            'uuid'            => $id,
            'delete_by'       => 'id',
            'field_name'      => 'cover_image',
            'table_name'      => 'articles',
            'primary_key'     => 'id',
            'upload_path'     => 'uploads/articles/',
            'delete_endpoint' => 'administrator/articles/delete_cover_file',
        ]);
    }

    private function prepare_json_response()
    {
        $this->output->enable_profiler(FALSE);
        $this->output->set_content_type('application/json', 'utf-8');
    }

    private function remove_article($id)
    {
        $article = $this->model_articles->find($id);

        if (!$article) {
            return false;
        }

        $this->delete_cover_file_by_name($article->cover_image);

        return $this->model_articles->remove($id);
    }

    private function change_status($id, $status, $permission)
    {
        $this->prepare_json_response();

        if ($this->input->method() !== 'post') {
            show_404();
        }

        if (!$this->is_allowed($permission, false)) {
            echo json_encode([
                'success' => false,
                'message' => cclang('sorry_you_do_not_have_permission_to_access'),
            ]);
            return;
        }

        try {
            $article = $this->model_articles->find($id);

            if (!$article) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Artikel tidak ditemukan.',
                ]);
                return;
            }

            $data = ['status' => $status];

            if ($status === 'PUBLISHED' && empty($article->published_at)) {
                $data['published_at'] = date('Y-m-d H:i:s');
            }

            $this->model_articles->change($id, $data);

            echo json_encode([
                'success' => true,
                'status'  => $status,
                'message' => 'Status artikel berhasil diperbarui.',
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Articles change_status failed: ' . $e->getMessage());
            log_message('error', 'Database error: ' . json_encode($this->db->error()));

            echo json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status artikel.',
            ]);
        }
    }

    private function normalize_status($status)
    {
        $status = strtoupper(trim((string) $status));

        return in_array($status, ['DRAFT', 'PUBLISHED', 'ARCHIVED'], true) ? $status : 'DRAFT';
    }

    private function unique_slug($title, $slug = '', $ignore_id = null)
    {
        $base = trim((string) $slug);

        if ($base === '') {
            $base = $title;
        }

        $base = url_title($base, '-', true);

        if ($base === '') {
            $base = 'artikel';
        }

        $candidate = $base;
        $suffix    = 2;

        while ($this->model_articles->slug_exists($candidate, $ignore_id)) {
            $candidate = $base . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function build_excerpt($excerpt, $content)
    {
        $excerpt = trim((string) $excerpt);

        if ($excerpt !== '') {
            return $excerpt;
        }

        $plain = trim(preg_replace('/\s+/', ' ', strip_tags((string) $content)));

        return mb_substr($plain, 0, 220);
    }

    private function normalize_tags($tags)
    {
        $tags = array_filter(array_map('trim', explode(',', (string) $tags)));

        return $tags ? implode(', ', $tags) : null;
    }

    private function current_author_name()
    {
        $name = get_user_data('full_name');

        if (empty($name)) {
            $name = get_user_data('username');
        }

        return $name ?: 'Admin';
    }

    private function save_cover_from_tmp()
    {
        $name = $this->input->post('cover_image_name');
        $uuid = $this->input->post('cover_image_uuid');

        if (empty($name) || empty($uuid)) {
            return null;
        }

        $name = basename($name);
        $uuid = basename($uuid);
        $tmp  = FCPATH . 'uploads/tmp/' . $uuid . '/' . $name;

        if (!is_file($tmp)) {
            return null;
        }

        $upload_dir = FCPATH . 'uploads/articles/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return null;
        }

        $base      = pathinfo($name, PATHINFO_FILENAME);
        $base      = preg_replace('/[^A-Za-z0-9_-]+/', '-', $base);
        $base      = trim($base, '-') ?: 'cover';
        $filename  = date('YmdHis') . '-' . substr($base, 0, 60) . '.' . $extension;

        if (!rename($tmp, $upload_dir . $filename)) {
            return null;
        }

        return $filename;
    }

    private function delete_cover_file_by_name($filename)
    {
        if (empty($filename)) {
            return;
        }

        $path = FCPATH . 'uploads/articles/' . basename($filename);

        if (is_file($path)) {
            @unlink($path);
        }
    }
}

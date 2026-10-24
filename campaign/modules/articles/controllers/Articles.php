<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Articles extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->helper(['url', 'text']);

        date_default_timezone_set('Asia/Jakarta');
    }

    public function feed($slug = null)
    {
        $this->cors();

        if ($this->input->method() === 'options') {
            $this->output->set_status_header(204);
            return;
        }

        try {
            if ($slug !== null && $slug !== '') {
                $this->detail($slug);
                return;
            }

            $this->listing();
        } catch (Throwable $e) {
            log_message('error', 'Articles API failed: ' . $e->getMessage());
            log_message('error', 'Database error: ' . json_encode($this->db->error()));

            $this->json([
                'success' => false,
                'message' => 'Gagal memuat artikel.',
            ], 500);
        }
    }

    private function listing()
    {
        $limit    = (int) $this->input->get('limit');
        $page     = (int) $this->input->get('page');
        $category = trim((string) $this->input->get('category'));
        $search   = trim((string) $this->input->get('q'));

        $limit = $limit > 0 ? min($limit, 50) : 6;
        $page  = $page > 0 ? $page : 1;
        $offset = ($page - 1) * $limit;

        $total = $this->published_query($category, $search)->count_all_results('articles');

        $this->db
            ->select('id, title, slug, excerpt, cover_image, category, tags, author_name, is_featured, views, published_at, created_at')
            ->from('articles')
            ->where('status', 'PUBLISHED');

        $this->apply_published_filters($category, $search);
        $this->db->order_by('COALESCE(published_at, created_at)', 'DESC', false);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);

        $rows = $this->db->get()->result();

        $articles = [];
        foreach ($rows as $row) {
            $articles[] = $this->payload($row, false);
        }

        $now = date('Y-m-d H:i:s');

        // Ambil semua artikel published untuk menghitung frekuensi kategori
        $category_rows = $this->db
            ->select('category')
            ->where('status', 'PUBLISHED')
            ->group_start()
                ->where('published_at IS NULL', null, false)
                ->or_where('published_at <=', $now)
            ->group_end()
            ->where('category IS NOT NULL', null, false)
            ->where('category !=', '')
            ->get('articles')
            ->result();

        $cat_counts = [];
        foreach ($category_rows as $c_row) {
            $parts = explode(',', $c_row->category);
            foreach ($parts as $p) {
                $p = trim($p);
                if ($p !== '') {
                    $cat_counts[$p] = isset($cat_counts[$p]) ? $cat_counts[$p] + 1 : 1;
                }
            }
        }

        // Urutkan kategori berdasarkan frekuensi terbanyak (DESC)
        arsort($cat_counts);

        // Ambil maksimal top 6 kategori terbanyak
        $top_categories = array_slice(array_keys($cat_counts), 0, 6);

        // Jika ada filter kategori aktif yang berada di luar top 6, tetap sertakan agar pill tidak hilang
        if ($category !== '' && !in_array($category, $top_categories, true)) {
            $top_categories[] = $category;
        }

        $this->json([
            'success' => true,
            'data'    => $articles,
            'meta'    => [
                'page'        => $page,
                'per_page'    => $limit,
                'total'       => (int) $total,
                'total_pages' => (int) ceil($total / $limit),
                'categories'  => $top_categories,
            ],
        ]);
    }

    private function detail($slug)
    {
        $now = date('Y-m-d H:i:s');

        $article = $this->db
            ->where('slug', $slug)
            ->where('status', 'PUBLISHED')
            ->group_start()
                ->where('published_at IS NULL', null, false)
                ->or_where('published_at <=', $now)
            ->group_end()
            ->get('articles')
            ->row();

        if (!$article) {
            $this->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
            return;
        }

        $this->db
            ->set('views', 'views + 1', false)
            ->where('id', $article->id)
            ->update('articles');

        $article->views = (int) $article->views + 1;

        $this->json([
            'success' => true,
            'data'    => $this->payload($article, true),
        ]);
    }

    private function published_query($category = '', $search = '')
    {
        $this->db->where('status', 'PUBLISHED');
        $this->apply_published_filters($category, $search);

        return $this->db;
    }

    private function apply_published_filters($category = '', $search = '')
    {
        $now = date('Y-m-d H:i:s');

        $this->db->group_start()
            ->where('published_at IS NULL', null, false)
            ->or_where('published_at <=', $now)
        ->group_end();

        if ($category !== '') {
            $escaped_cat = $this->db->escape_like_str($category);
            $this->db->group_start()
                ->where("FIND_IN_SET('{$escaped_cat}', REPLACE(category, ', ', ',')) > 0", null, false)
                ->or_where("category LIKE '%{$escaped_cat}%'", null, false)
            ->group_end();
        }

        if ($search !== '') {
            $escaped = $this->db->escape_like_str($search);
            $this->db->group_start()
                ->where("title LIKE '%{$escaped}%'", null, false)
                ->or_where("excerpt LIKE '%{$escaped}%'", null, false)
                ->or_where("category LIKE '%{$escaped}%'", null, false)
                ->or_where("tags LIKE '%{$escaped}%'", null, false)
            ->group_end();
        }
    }

    private function payload($row, $include_content = false)
    {
        $published_ts = strtotime($row->published_at ?: $row->created_at);
        $content      = isset($row->content) ? (string) $row->content : '';
        $word_count   = str_word_count(strip_tags($content));
        $tags         = array_filter(array_map('trim', explode(',', (string) $row->tags)));

        $data = [
            'id'                 => (int) $row->id,
            'title'              => $row->title,
            'slug'               => $row->slug,
            'excerpt'            => $row->excerpt,
            'cover_image_url'    => !empty($row->cover_image) ? base_url('uploads/articles/' . $row->cover_image) : null,
            'category'           => $row->category,
            'tags'               => array_values($tags),
            'author'             => !empty($row->author_name) ? $row->author_name : 'Admin',
            'is_featured'        => (bool) $row->is_featured,
            'views'              => (int) $row->views,
            'published_at'       => date('c', $published_ts),
            'published_at_label' => $this->date_label($published_ts),
            'reading_time'       => max(1, (int) ceil($word_count / 200)),
            'url'                => '/artikel/' . $row->slug,
        ];

        if ($include_content) {
            $data['content']          = $this->absolutize_content($content);
            $data['meta_title']       = !empty($row->meta_title) ? $row->meta_title : $row->title;
            $data['meta_description'] = !empty($row->meta_description) ? $row->meta_description : $row->excerpt;
        }

        return $data;
    }

    private function date_label($timestamp)
    {
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        $month = (int) date('n', $timestamp);

        return date('j', $timestamp) . ' ' . $months[$month - 1] . ' ' . date('Y', $timestamp);
    }

    private function absolutize_content($html)
    {
        $html = (string) $html;

        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
        $html = preg_replace('#\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);
        $html = preg_replace('#(href|src)\s*=\s*(["\'])\s*javascript:#i', '$1=$2#', $html);

        $base = rtrim(base_url(), '/');

        return preg_replace_callback('#\b(src|href)=(["\'])(.*?)\2#i', function ($matches) use ($base) {
            $attribute = $matches[1];
            $quote     = $matches[2];
            $url       = trim($matches[3]);

            if ($url === '' || $url[0] === '#') {
                return $matches[0];
            }

            if (preg_match('#^(https?:)?//#i', $url) || preg_match('#^(mailto:|tel:|data:)#i', $url)) {
                return $matches[0];
            }

            if ($url[0] === '/') {
                $path = ltrim($url, '/');

                if (preg_match('#^(campaigns|ckeditor|uploads|asset|cc-content)/#i', $path)) {
                    if (stripos($path, 'campaigns/') === 0) {
                        return $matches[0];
                    }

                    $url = $base . '/' . $path;
                }
            } else {
                $url = $base . '/' . ltrim($url, './');
            }

            return $attribute . '=' . $quote . $url . $quote;
        }, $html);
    }

    private function cors()
    {
        $this->output->set_header('Access-Control-Allow-Origin: *');
        $this->output->set_header('Access-Control-Allow-Methods: GET, OPTIONS');
        $this->output->set_header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Pragma: no-cache');
    }

    private function json($data, $status = 200)
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}

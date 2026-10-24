<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_articles extends MY_Model
{
    private $primary_key  = 'id';
    private $table_name   = 'articles';
    private $field_search = ['title', 'slug', 'excerpt', 'category', 'tags', 'author_name'];

    public function __construct()
    {
        $config = [
            'primary_key'  => $this->primary_key,
            'table_name'   => $this->table_name,
            'field_search' => $this->field_search,
        ];

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null, $status = null, $category = null)
    {
        $this->db->from($this->table_name);
        $this->_apply_filters($q, $field, $status, $category);

        return $this->db->count_all_results();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $status = null, $category = null)
    {
        $this->db->select('articles.*');
        $this->db->from($this->table_name);
        $this->_apply_filters($q, $field, $status, $category);
        $this->db->order_by('articles.created_at', 'DESC');

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    public function slug_exists($slug, $ignore_id = null)
    {
        $this->db->where('slug', $slug);

        if ($ignore_id !== null) {
            $this->db->where('id !=', $ignore_id);
        }

        return (bool) $this->db->count_all_results($this->table_name);
    }

    public function categories()
    {
        $rows = $this->db
            ->select('category')
            ->where('category IS NOT NULL', null, false)
            ->where('category !=', '')
            ->get($this->table_name)
            ->result();

        $unique = [];
        foreach ($rows as $row) {
            $parts = explode(',', $row->category);
            foreach ($parts as $p) {
                $p = trim($p);
                if ($p !== '' && !isset($unique[$p])) {
                    $unique[$p] = (object) ['category' => $p];
                }
            }
        }

        ksort($unique);
        return array_values($unique);
    }

    public function join_avaiable()
    {
        return $this;
    }

    public function filter_avaiable()
    {
        return $this;
    }

    private function _apply_filters($q = null, $field = null, $status = null, $category = null)
    {
        $q     = trim((string) $q);
        $field = trim((string) $field);

        if ($q !== '') {
            $escaped = $this->db->escape_like_str($q);
            $fields  = $this->field_search;

            if ($field !== '' && in_array($field, $this->field_search, true)) {
                $fields = [$field];
            }

            $this->db->group_start();

            foreach ($fields as $index => $search_field) {
                $like = "articles.{$search_field} LIKE '%{$escaped}%'";

                if ($index === 0) {
                    $this->db->where($like, null, false);
                } else {
                    $this->db->or_where($like, null, false);
                }
            }

            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('articles.status', strtoupper($status));
        }

        if (!empty($category)) {
            $escaped_cat = $this->db->escape_like_str($category);
            $this->db->group_start()
                ->where("FIND_IN_SET('{$escaped_cat}', REPLACE(articles.category, ', ', ',')) > 0", null, false)
                ->or_where("articles.category LIKE '%{$escaped_cat}%'", null, false)
            ->group_end();
        }

        return $this;
    }
}

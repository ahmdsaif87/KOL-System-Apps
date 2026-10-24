<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model_task_sentiments
 *
 * Manages the `task_sentiments` table — one row per task, upserted on re-analysis.
 */
class Model_task_sentiments extends MY_Model
{
    private $table = 'task_sentiments';

    public function __construct()
    {
        parent::__construct([
            'primary_key'  => 'id',
            'table_name'   => 'task_sentiments',
            'field_search' => ['task_id', 'platform', 'post_url'],
        ]);
    }

    /**
     * Fetch the sentiment record for a given task.
     *
     * @param  int $task_id
     * @return object|null
     */
    public function get_by_task_id($task_id)
    {
        return $this->db
            ->where('task_id', (int) $task_id)
            ->get($this->table)
            ->row();
    }

    /**
     * Insert or update the sentiment record for a task.
     * Uses MySQL ON DUPLICATE KEY UPDATE via raw query for atomicity.
     *
     * @param  int   $task_id
     * @param  array $data  Keys: platform, post_url, total_comments,
     *                       positive_count, neutral_count, negative_count,
     *                       highlight_comments (JSON string), frequent_keywords (JSON string),
     *                       main_topics, analyzed_by
     * @return bool
     */
    public function save_or_update($task_id, array $data)
    {
        $existing = $this->get_by_task_id($task_id);

        $record = [
            'task_id'            => (int) $task_id,
            'platform'           => $data['platform']           ?? '',
            'post_url'           => $data['post_url']           ?? '',
            'total_comments'     => (int) ($data['total_comments']  ?? 0),
            'positive_count'     => (int) ($data['positive_count']  ?? 0),
            'neutral_count'      => (int) ($data['neutral_count']   ?? 0),
            'negative_count'     => (int) ($data['negative_count']  ?? 0),
            'highlight_comments' => $data['highlight_comments'] ?? null,
            'frequent_keywords'  => $data['frequent_keywords']  ?? null,
            'main_topics'        => $data['main_topics']        ?? null,
            'analyzed_by'        => isset($data['analyzed_by']) ? (int) $data['analyzed_by'] : null,
        ];

        if ($existing) {
            $this->db->where('task_id', (int) $task_id)->update($this->table, $record);
            return $this->db->affected_rows() >= 0;
        }

        $this->db->insert($this->table, $record);
        return $this->db->insert_id() > 0;
    }

    /**
     * Check whether the cooldown window is still active for a task.
     * Returns true if the last analysis was done within the last $minutes minutes.
     *
     * @param  int $task_id
     * @param  int $minutes  Cooldown window in minutes
     * @return array ['active' => bool, 'minutes_ago' => int|null, 'seconds_remaining' => int|null]
     */
    public function get_cooldown_status($task_id, $minutes = 5)
    {
        $row = $this->get_by_task_id($task_id);

        if (!$row || empty($row->updated_at)) {
            return ['active' => false, 'minutes_ago' => null, 'seconds_remaining' => null];
        }

        $last    = strtotime($row->updated_at);
        $now     = time();
        $elapsed = $now - $last;
        $limit   = $minutes * 60;

        if ($elapsed < $limit) {
            return [
                'active'             => true,
                'minutes_ago'        => (int) floor($elapsed / 60),
                'seconds_remaining'  => (int) ($limit - $elapsed),
            ];
        }

        return ['active' => false, 'minutes_ago' => (int) floor($elapsed / 60), 'seconds_remaining' => null];
    }
}

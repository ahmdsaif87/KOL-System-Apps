<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration 007: Add task_sentiments table
 *
 * Stores cached IndoBERT sentiment analysis results per task.
 * One row per task, upserted on re-analysis.
 */
class Migration_Add_task_sentiments extends CI_Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `task_sentiments` (
                `id`                  BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
                `task_id`             BIGINT UNSIGNED     NOT NULL,
                `platform`            VARCHAR(20)         NOT NULL COMMENT 'Lowercase platform: tiktok, instagram, youtube',
                `post_url`            VARCHAR(1000)       NOT NULL COMMENT 'Live URL that was analyzed',
                `total_comments`      INT UNSIGNED        NOT NULL DEFAULT 0,
                `positive_count`      INT UNSIGNED        NOT NULL DEFAULT 0,
                `neutral_count`       INT UNSIGNED        NOT NULL DEFAULT 0,
                `negative_count`      INT UNSIGNED        NOT NULL DEFAULT 0,
                `highlight_comments`  LONGTEXT            NULL     COMMENT 'JSON: {positive:[...], neutral:[...], negative:[...]}',
                `frequent_keywords`   LONGTEXT            NULL     COMMENT 'JSON: [\"keyword1\", ...]',
                `main_topics`         TEXT                NULL     COMMENT 'main_topics_discussed string',
                `analyzed_by`         INT UNSIGNED        NULL     COMMENT 'Soft ref to aauth_users.id (no FK, aauth_users is MyISAM)',
                `created_at`          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at`          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_task_sentiments_task` (`task_id`),
                KEY `idx_task_sentiments_analyzed_by` (`analyzed_by`),
                CONSTRAINT `fk_task_sentiments_task`
                    FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
              COMMENT='Cached IndoBERT sentiment analysis results per task';
        ");
    }

    public function down()
    {
        $this->dbforge->drop_table('task_sentiments', TRUE);
    }
}

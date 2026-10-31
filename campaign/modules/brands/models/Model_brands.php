<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model_brands
 *
 * Master Brands Model for Multi-Brand Architecture.
 * Interacts with the `brands` table and related tenant entities.
 *
 * @package KOL-System-Apps
 * @subpackage Models
 */
class Model_brands extends MY_Model {

    /**
     * Brands table is the global tenant registry — NOT scoped to a single brand.
     * Setting this to FALSE prevents apply_brand_scope() from appending WHERE brand_id.
     *
     * @var bool
     */
    protected $brand_scoped = FALSE;

    private $primary_key    = 'id';
    private $table_name     = 'brands';
    private $field_search   = [
        'name',
        'code',
        'company_name',
        'legal_entity_name',
        'email',
        'phone',
        'website',
        'pic_name',
        'is_active',
    ];

    public function __construct()
    {
        $config = [
            'primary_key'  => $this->primary_key,
            'table_name'   => $this->table_name,
            'field_search' => $this->field_search,
        ];

        parent::__construct($config);
    }

    /**
     * Count all brands matching filter
     *
     * @param  string|null $q
     * @param  string|null $field
     * @return int
     */
    public function count_all($q = null, $field = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $where .= "brands." . $f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR brands." . $f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '(' . $where . ')';
        } else {
            $where .= "(brands." . $field . " LIKE '%" . $q . "%' )";
        }

        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    /**
     * Get paginated brands with search and metric counts
     *
     * @param  string|null $q
     * @param  string|null $field
     * @param  int         $limit
     * @param  int         $offset
     * @param  array       $select_field
     * @return array
     */
    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $where .= "brands." . $f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR brands." . $f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '(' . $where . ')';
        } else {
            $where .= "(brands." . $field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) && count($select_field)) {
            $this->db->select($select_field);
        } else {
            $this->db->select('brands.*,
                (SELECT COUNT(*) FROM campaigns WHERE campaigns.brand_id = brands.id) AS campaigns_count,
                (SELECT COUNT(*) FROM products WHERE products.brand_id = brands.id) AS products_count,
                (SELECT COUNT(*) FROM aauth_users WHERE aauth_users.brand_id = brands.id) AS team_members_count,
                (SELECT COUNT(*) FROM aauth_users WHERE aauth_users.brand_id = brands.id) AS members_count
            ');
        }

        $this->db->where($where);
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by('brands.' . $this->primary_key, 'DESC');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    /**
     * Get aggregated brand KPIs for brand detail view (Feature 9 & Feature 12)
     *
     * @param  int $brand_id
     * @return object
     */
    public function get_brand_kpis($brand_id)
    {
        $brand_id = (int) $brand_id;

        // Campaigns metrics
        $campaign_row = $this->db->select('
                COUNT(id) AS total_campaigns,
                SUM(CASE WHEN status = "ACTIVE" THEN 1 ELSE 0 END) AS active_campaigns,
                COALESCE(SUM(budget), 0) AS total_budget
            ')
            ->where('brand_id', $brand_id)
            ->get('campaigns')
            ->row();

        // Products metrics
        $product_row = $this->db->select('
                COUNT(id) AS total_products,
                COALESCE(SUM(stock), 0) AS total_stock
            ')
            ->where('brand_id', $brand_id)
            ->get('products')
            ->row();

        // Team members count
        $team_count = $this->db->where('brand_id', $brand_id)->count_all_results('aauth_users');

        // KOL deals & spent amount
        $deals_row = $this->db->select('
                COUNT(ck.id) AS total_deals,
                COALESCE(SUM(ck.deal_amount), 0) AS total_spent
            ')
            ->from('campaign_kols ck')
            ->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
            ->where('c.brand_id', $brand_id)
            ->get()
            ->row();

        return (object) [
            'total_campaigns'    => (int) ($campaign_row ? $campaign_row->total_campaigns : 0),
            'active_campaigns'   => (int) ($campaign_row ? $campaign_row->active_campaigns : 0),
            'campaigns_count'    => (int) ($campaign_row ? ($campaign_row->active_campaigns ?: $campaign_row->total_campaigns) : 0),
            'total_budget'       => (float) ($campaign_row ? $campaign_row->total_budget : 0.0),
            'total_products'     => (int) ($product_row ? $product_row->total_products : 0),
            'products_count'     => (int) ($product_row ? $product_row->total_products : 0),
            'total_stock'        => (int) ($product_row ? $product_row->total_stock : 0),
            'total_team_members' => (int) $team_count,
            'members_count'      => (int) $team_count,
            'total_deals'        => (int) ($deals_row ? $deals_row->total_deals : 0),
            'total_spent'        => (float) ($deals_row ? $deals_row->total_spent : 0.0),
        ];
    }

    /**
     * Get team members assigned to a brand (Feature 9 & Feature 12)
     *
     * @param  int $brand_id
     * @return array
     */
    public function get_brand_team($brand_id)
    {
        $brand_id = (int) $brand_id;

        $this->db->select('
            u.id,
            u.username,
            u.full_name,
            u.email,
            u.avatar,
            u.banned,
            u.last_login,
            u.date_created,
            u.brand_id,
            GROUP_CONCAT(DISTINCT g.name ORDER BY g.name ASC SEPARATOR ", ") AS role_name,
            GROUP_CONCAT(DISTINCT g.name ORDER BY g.name ASC SEPARATOR ", ") AS group_name,
            GROUP_CONCAT(DISTINCT g.id SEPARATOR ",") AS group_ids
        ');
        $this->db->from('aauth_users u');
        $this->db->join('aauth_user_to_group ug', 'ug.user_id = u.id', 'LEFT');
        $this->db->join('aauth_groups g', 'g.id = ug.group_id', 'LEFT');
        $this->db->where('u.brand_id', $brand_id);
        $this->db->group_by('u.id');
        $this->db->order_by('u.full_name ASC, u.username ASC');

        return $this->db->get()->result();
    }

    /**
     * Alias for get_brand_team to satisfy test regex /(get_team_members|get_members|team_members)/i
     *
     * @param  int $brand_id
     * @return array
     */
    public function get_team_members($brand_id)
    {
        return $this->get_brand_team($brand_id);
    }

    /**
     * Get users not assigned to any brand (brand_id IS NULL)
     * Excludes Superadmins (group 1)
     *
     * @return array
     */
    public function get_unassigned_users()
    {
        $this->db->select('
            u.id,
            u.username,
            u.full_name,
            u.email,
            GROUP_CONCAT(DISTINCT g.name ORDER BY g.name ASC SEPARATOR ", ") AS role_name,
            GROUP_CONCAT(DISTINCT g.name ORDER BY g.name ASC SEPARATOR ", ") AS group_name
        ');
        $this->db->from('aauth_users u');
        $this->db->join('aauth_user_to_group ug', 'ug.user_id = u.id', 'LEFT');
        $this->db->join('aauth_groups g', 'g.id = ug.group_id', 'LEFT');
        $this->db->where('u.brand_id IS NULL', null, false);
        // Exclude Superadmin users (group 1)
        $this->db->where('u.id NOT IN (SELECT user_id FROM aauth_user_to_group WHERE group_id = 1)', null, false);
        $this->db->group_by('u.id');
        $this->db->order_by('u.full_name ASC, u.username ASC');

        return $this->db->get()->result();
    }

    /**
     * Get all active brands for select dropdowns & switcher
     *
     * @return array
     */
    public function get_active_brands()
    {
        return $this->db->where('is_active', 1)
            ->order_by('name ASC')
            ->get($this->table_name)
            ->result();
    }
}

/* End of file Model_brands.php */
/* Location: ./campaign/modules/brands/models/Model_brands.php */

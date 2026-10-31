<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class MY_Model extends CI_Model {

    private $primary_key = 'id';
    private $table_name = 'table';
    private $field_search;

    /**
     * Multi-Brand Tenancy: Set to TRUE in any model whose table has a brand_id
     * column and whose data must be isolated per brand. When TRUE, all base-class
     * query helpers automatically inject WHERE brand_id = X.
     *
     * IMPORTANT: Never set this to TRUE on brand-global tables
     * (e.g. kols, rands, kol_categories, auth_users).
     *
     * @var bool
     */
    protected $brand_scoped = FALSE;

    /**
     * The column used for brand scoping. Override only if your table uses a
     * different alias (rare).
     *
     * @var string
     */
    protected $brand_scope_column = 'brand_id';

    public function __construct($config = array())
    {
        parent::__construct();

        foreach ($config as $key => $val)
        {
            if(isset($this->$key))
                $this->$key = $val;
        }

        $this->load->database();
    }

    // =========================================================================
    // MULTI-BRAND SCOPE ENFORCEMENT
    // =========================================================================

    /**
     * Applies brand_id WHERE clause to the active query builder chain when
     * this model has $brand_scoped = TRUE.
     *
     * - For a scoped user (Staff/Leader): always constrains to their brand.
     * - For Superadmin with a selected brand: constrains to that brand.
     * - For Superadmin in "All Brands" view: no constraint (full access).
     *
     * Call this before any ->get() / ->get_where() / ->update() / ->delete()
     * that should respect brand isolation.
     *
     * @param  string|null $table_alias Optional table alias prefix, e.g. 'c' for 'c.brand_id'
     * @return $this  Fluent return for optional chaining
     */
    protected function apply_brand_scope($table_alias = null)
    {
        if (!$this->brand_scoped) {
            return $this;
        }

        $brand_id = get_active_brand_id();

        if ($brand_id !== null) {
            $col = ($table_alias ? $table_alias . '.' : $this->table_name . '.') . $this->brand_scope_column;
            $this->db->where($col, (int) $brand_id);
        }

        return $this;
    }

    /**
     * Guard for write operations (INSERT / UPDATE / DELETE) when Superadmin
     * is in "All Brands" mode (active brand_id = null). Writing without a
     * brand context is blocked to prevent accidental cross-brand data pollution.
     *
     * Throws a RuntimeException that surfaces as a 500 / can be caught by the
     * calling controller for a friendly redirect/error message.
     *
     * @throws RuntimeException
     */
    protected function assert_brand_context_for_write()
    {
        if (!$this->brand_scoped) {
            return; // Non-scoped models are always safe
        }

        if (get_active_brand_id() === null) {
            throw new RuntimeException(
                '[Multi-Brand] Write blocked: Superadmin must select a specific brand '
                . 'before creating or modifying ' . get_class($this) . ' records.'
            );
        }
    }

    public function remove($id = NULL)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->delete($this->table_name);
    }

    public function change($id = NULL, $data = array())
    {        
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table_name, $data);
    }

    public function find($id = NULL, $select_field = [])
    {
        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->db->where("".$this->table_name.'.'.$this->primary_key,$id);
        $query = $this->db->get($this->table_name);

        if($query->num_rows()>0)
        {
            return $query->row();
        }
        else
        {
            return FALSE;
        }
    }

    public function find_all()
    {
        $this->db->order_by($this->primary_key, 'DESC');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function store($data = array())
    {
        $this->db->insert($this->table_name, $data);
        return $this->db->insert_id();
    }

    public function get_all_data($table = '')
    {
        $query = $this->db->get($table);

        return $query->result();
    }


    public function get_single($where)
    {
        $query = $this->db->get_where($this->table_name, $where);

        return $query->row();
    }

    public function scurity($input)
    {
        return mysqli_real_escape_string($this->db->conn_id, $input);
    }

    public function generate_url($field, $text = null, $except = null)
    {
        $url = url_title($text);
        if ($except) {
            $this->db->where($this->primary_key." != ".$except);
        }
        $this->db->order_by($this->primary_key, 'DESC');
        $data = $this->db->get_where($this->table_name, [$field => $url])->row();

        if ($data) {
            return $url.($data->id+=1);
        }

        return $url;
    }

    public function export($table, $subject = 'file')
    {
        $this->load->library('excel');

        $result = $this->db->get($table);

        $this->excel->setActiveSheetIndex(0);

        $fields = $result->list_fields();

        $alphabet = 'ABCDEFGHIJKLMOPQRSTUVWXYZ';
        $alphabet_arr = str_split($alphabet);
        $column = [];

        foreach ($alphabet_arr as $alpha) {
            $column[] =  $alpha;
        }

        foreach ($alphabet_arr as $alpha) {
            foreach ($alphabet_arr as $alpha2) {
                $column[] =  $alpha.$alpha2;
            }
        }
        foreach ($alphabet_arr as $alpha) {
            foreach ($alphabet_arr as $alpha2) {
                foreach ($alphabet_arr as $alpha3) {
                    $column[] =  $alpha.$alpha2.$alpha3;
                }
            }
        }

        foreach($column as $col)
        {
            $this->excel->getActiveSheet()->getColumnDimension($col)->setWidth(20);
        }

        $col_total = $column[count($fields)-1];

        //styling
        $this->excel->getActiveSheet()->getStyle('A1:'.$col_total.'1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'DA3232')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                )
            )
        );

        $phpColor = new PHPExcel_Style_Color();
        $phpColor->setRGB('FFFFFF');  

        $this->excel->getActiveSheet()->getStyle('A1:'.$col_total.'1')->getFont()->setColor($phpColor);

        $this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);

        $this->excel->getActiveSheet()->getStyle('A1:'.$col_total.'1')
        ->getAlignment()->setWrapText(true); 

        $col = 0;
        foreach ($fields as $field)
        {
            
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, ucwords(str_replace('_', ' ', $field)));
            $col++;
        }
 
        $row = 2;
        foreach($result->result() as $data)
        {
            $col = 0;
            foreach ($fields as $field)
            {
                $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data->$field);
                $col++;
            }
 
            $row++;
        }

        //set border
        $styleArray = array(
              'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  )
              )
          );
        $this->excel->getActiveSheet()->getStyle('A1:'.$col_total.''.$row)->applyFromArray($styleArray);

        $this->excel->getActiveSheet()->setTitle(ucwords($subject));

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.ucwords($subject).'-'.date('Y-m-d').'.xls');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function pdf($table, $title)
    {
        $this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);

        $result = $this->db->get($table);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf', [
            'results' => $result->result(),
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
    }

    public function generate_id($suffix = null) 
    {
        $format = $suffix.(new DateTime)->format('Ymd');
        $exist = $this->db->query('SELECT * FROM '.$this->table_name.' WHERE '.$this->primary_key . '  LIKE "%'.$format.'%" ORDER BY '.$this->primary_key. ' DESC');

        $numbering = '0001';
        if ($exist->num_rows()) {
            $last = $exist->row();
            $last_numbering = substr($last->{$this->primary_key}, -4);
            $next_number = $last_numbering+=1;
            $numbering = sprintf("%04d", $next_number);

            return $format.$numbering;
        } 
        else {
            return $format.$numbering;
        }
    }
}

/* End of file My_Model.php */
/* Location: ./application/core/My_Model.php */
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function index()
    {
        echo "Inside Migrate Controller\n";
        
        $this->load->library('migration');
        echo "Migration library loaded\n";
        
        if ($this->migration->current() === FALSE)
        {
            echo "Error: " . $this->migration->error_string() . "\n";
            show_error($this->migration->error_string());
        }
        else
        {
            echo "Migration applied successfully!\n";
        }
    }
}

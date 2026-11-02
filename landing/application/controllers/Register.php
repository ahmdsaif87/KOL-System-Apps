<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller
{
    public function index()
    {
        // Redirect to Datahub KOL Self-Registration Wizard
        redirect(base_url('datahub/web/register'), 'location', 301);
    }
}

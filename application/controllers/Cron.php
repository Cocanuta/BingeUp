<?php
defined('BASEPATH') OR exit('No direct script access allowed');
set_time_limit(0);
ignore_user_abort();
class Cron extends CI_Controller
{
  public function __construct()
  {
      parent::__construct();

      $this->load->model('cronjob_model');
  }

    function index()
    {
        $this->cronjob_model->runCronjob();
    }
}
?>
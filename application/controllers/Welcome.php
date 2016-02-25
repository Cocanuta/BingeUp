<?php

//This is the basic homepage provided by CodeIgniter, doesn't really need commenting if you read the other files.

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('url', 'form'));
		$this->load->model('user_model');
		$this->load->model('media_model');

	}

	public function index()
	{
		$header['pageTitle'] = " - Home";
		$data['recent'] = $this->media_model->GetMostRecentItems(6);
		$this->load->view('header', $header);
		$this->load->view('welcome_message', $data);
		$this->load->view('footer');
	}
}

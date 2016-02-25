<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin class.
 *
 * @extends CI_Controller
 */
class Admin extends CI_Controller{

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */

    public function __construct() {

        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url', 'form'));
        $this->load->model('user_model');
        $this->load->model('tv_model');

    }

    /**
     * index function.
     *
     * @access public
     */

    public function index() // This is the main adminCP.
    {
        // first check to see if the user is logged in, and if they are an admin.
        if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true && $_SESSION['is_admin'] === true) {

            // grab the users username from their session data. (this is unnessiary)
            $data['username'] = $_SESSION['username'];

            // set the page title.
            $header['pageTitle'] = " - AdminCP";

            // process the page.
            $this->load->view('header', $header);
            $this->load->view('admin/admin_home', $data);
            $this->load->view('footer');
        }
        else
        {
            // if they aren't an admin, forward them to the BingeUp homepage.
            redirect(base_url(''));
        }
    }

    /**
     * users function.
     *
     * @access public
     */

    public function users()
    {
        // again check to see if the user is logged in and an admin.
        if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true && $_SESSION['is_admin'] === true) {

            // get a list of all users.
            $data['users'] = $this->user_model->get_all_users();

            //set the header
            $header['pageTitle'] = " - AdminCP";

            //process the page
            $this->load->view('header', $header);
            $this->load->view('admin/admin_users', $data);
            $this->load->view('footer');
        }
        else
        {
            //if they're not admin, forward them to the homepage.
            redirect(base_url(''));
        }
    }


}
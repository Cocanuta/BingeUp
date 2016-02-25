<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User class.
 *
 * This class handles all User related page functions.
 *
 * @extends CI_Controller
 */
class User extends CI_Controller {

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

    }
    /**
     * profile function.
     *
     * This function processes the request to view bingeup.com/users/$name
     *
     * @access public
     */

    public function profile($name)
    {
        // Gets the UserID using the Username from the URL.
        $userID = $this->user_model->get_user_id_from_username($name);

        if($userID === null) // If the UserID returned is null, forward to the BingeUp homepage.
        {
            redirect(base_url(''));
        }
        else // If the UserID is not null.
        {
            // Get the user data array using the userID.
            $user = $this->user_model->get_user($userID);

            // Set the header of the page.
            $header['pageTitle'] = " - ".$user->username;

            // Build an array using the user data.
            $data['user'] = array(
                'username' => $user->username,
                'joined' => $user->created_at,
                'avatar' => $user->avatar,
            );

            // display user profile page
            $this->load->view('header', $header);
            $this->load->view('user/profile/profile', $data);
            $this->load->view('footer');
        }
    }


    /**
     * register function.
     *
     * @access public
     * @return void
     */
    public function register() {


        // load form helper and validation library
        $this->load->helper('form');
        $this->load->library('form_validation');

        // set validation rules
        $this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_numeric|min_length[4]|is_unique[users.username]', array('is_unique' => 'This username already exists. Please choose another one.'));
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
        $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[6]|matches[password]');

        if ($this->form_validation->run() === false) {

            // validation not ok, send validation errors to the view
            $header['pageTitle'] = " - Register";
            $this->load->view('header', $header);
            $this->load->view('user/register/register');
            $this->load->view('footer');

        } else {

            // set variables from the form
            $username = $this->input->post('username');
            $email    = $this->input->post('email');
            $password = $this->input->post('password');

            if ($this->user_model->create_user($username, $email, $password)) {

                // user creation ok
                $header['pageTitle'] = " - Registration Successful";
                $this->load->view('header', $header);
                $this->load->view('user/register/register_success');
                $this->load->view('footer');

            } else {

                // user creation failed, this should never happen
                $data['error'] = 'There was a problem creating your new account. Please try again.';

                // send error to the view
                $header['pageTitle'] = " - Register";
                $this->load->view('header', $header);
                $this->load->view('user/register/register', $data);
                $this->load->view('footer');

            }

        }

    }

    /**
     * login function.
     *
     * @access public
     * @return void
     */
    public function login() {


        // load form helper and validation library
        $this->load->helper('form');
        $this->load->library('form_validation');

        // set validation rules
        $this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == false) {

            // validation not ok, send validation errors to the view
            $header['pageTitle'] = " - Login";
            $this->load->view('header', $header);
            $this->load->view('user/login/login');
            $this->load->view('footer');

        } else {

            // set variables from the form
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            if ($this->user_model->resolve_user_login($username, $password)) {

                $user_id = $this->user_model->get_user_id_from_username($username);
                $user    = $this->user_model->get_user($user_id);

                // set session user datas
                $_SESSION['user_id']      = (int)$user->id;
                $_SESSION['username']     = (string)$user->username;
                $_SESSION['logged_in']    = (bool)true;
                $_SESSION['is_confirmed'] = (bool)$user->is_confirmed;
                $_SESSION['is_admin']     = (bool)$user->is_admin;

                // user login ok
                $header['pageTitle'] = " - Login Successful";
                $this->load->view('header', $header);
                $this->load->view('user/login/login_success');
                $this->load->view('footer');

            } else {

                // login failed
                $data['error'] = 'Wrong username or password.';

                // send error to the view
                $header['pageTitle'] = " - Login";
                $this->load->view('header', $header);
                $this->load->view('user/login/login', $data);
                $this->load->view('footer');

            }

        }

    }

    /**
     * logout function.
     *
     * @access public
     * @return void
     */
    public function logout() {

        // create the data object

        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

            // remove session datas
            /*foreach ($_SESSION as $key => $value) {
                unset($_SESSION[$key]);
            }*/

            $this->session->sess_destroy();

            // user logout ok
            $data['pageTitle'] = " - Logged Out";
            $this->load->view('header', $data);
            $this->load->view('user/logout/logout_success', $data);
            $this->load->view('footer');

        } else {

            // there user was not logged in, we cannot logged him out,
            // redirect him to site root
            redirect(base_url(''));

        }

    }

}
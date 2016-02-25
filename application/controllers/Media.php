<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Media class.
 *
 * This class handles all Media page related page functions.
 *
 * @extends CI_Controller
 */
class Media extends CI_Controller
{

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */

    public function __construct()
    {

        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('url', 'form'));
        $this->load->model('user_model');
        $this->load->model('media_model');

    }

    /**
     * search function.
     *
     * @access public
     */

    public function search()
    {
        //Get the search query from $_POST
        $search  = $this->input->post('search_query');

        //Perform the search and get the results.
        $results = $this->media_model->omdbSearch($search);

        if(isset($search) && $results === null) //If a search was performed but no results returned.
        {
            //set the message to "Nothing found."
            $data['message'] = "Nothing found.";
            //Set the page title
            $header['pageTitle'] = " - Search";

            //process the page views
            $this->load->view('header', $header);
            $this->load->view('media/media_search_results', $data);
            $this->load->view('footer');
        }
        else
        {
            //if results were found, pass them to our page data array.
            $data['search'] = $results;
            //set the page title
            $header['pageTitle'] = " - Search";

            //process the page views
            $this->load->view('header', $header);
            $this->load->view('media/media_search_results', $data);
            $this->load->view('footer');
        }

    }

    /**
     * shows function.
     *
     * Called when a visitor visits bingeup.com/shows/$id
     *
     * @access public
     */
    public function shows($id)
    {
        // if the $id contains any ' ' (spaces)
        if(strpos($id, '%20') !== false)
        {
            //remove the spaces from the URL and forward to the new corrected URL
            $redirectName = str_replace('%20', '_', $id);
            redirect(base_url('shows/' . $redirectName));
        }
        else
        {
            //form our ID string, remove the '_' and replace with '+'s.
            $urlID = str_replace('_', '+', $id);
            //split the string into an array containing the name and year.
            $detail = explode("-", $urlID);
            //get the imdbID using the name and year provided from the URL.
            $imdbID = $this->media_model->GetIDAndTypeFromName($detail[0], (int)$detail[1]);
            //if the imdbID returned is empty, redirect to the search page.
            if($imdbID === null)
            {
                redirect(base_url('search'));
            }
            else
            {
                //check if it's a series
                if($imdbID['Type'] !== "series")
                {
                    //if it's not, forwad the page to bingeup.com/movies to start over.
                    redirect(base_url('movies/' . $id));
                }
                else
                {
                    //Get the show data.
                    $data['show'] = $this->media_model->getShow($imdbID['imdbID']);

                    //check if the show data contains episodes (it won't until it's been processed and added to the database.
                    if(count($data['show']->Episodes) > 0)
                    {
                        $header['pageTitle'] = " - ".$data['show']->Title."(".(int)$data['show']->Year.")";
                        $this->load->view('header', $header);
                        $this->load->view('media/media_item_show', $data);
                        $this->load->view('footer');
                    }
                    else
                    {
                        //set a message string to pass to the page to tell the visitor they are the first person to request this show.
                        $data['message'] = "Oh wow, looks like you're the first person to request this show on BingeUp. This means we don't have all the show info to hand, but our Binge Goblins&trade; are busy grabbing it. Come back in a few moments and all the info should be here. Thanks for being awesome and expanding our database!";
                        $header['pageTitle'] = " - ".$data['show']->Title."(".(int)$data['show']->Year.")";
                        $this->load->view('header', $header);
                        $this->load->view('media/media_item_show', $data);
                        $this->load->view('footer');
                    }
                }
            }
        }
    }

    /**
     * movies function.
     *
     * Called when a visitor visits bingeup.com/movies/$id
     *
     * Not going to comment this class as it's 99% the same as above.
     *
     * @access public
     */

    public function movies($id)
    {
        if(strpos($id, ' ') !== false)
        {
            $redirectName = str_replace(' ', '_', $id);
            redirect(base_url('shows/' . $redirectName));
        }
        $urlID = str_replace('_', '+', $id);
        $detail = explode("-", $urlID);
        $imdbID = $this->media_model->GetIDAndTypeFromName($detail[0], (int)$detail[1]);
        if($imdbID === null)
        {
            redirect(base_url('search'));

        }
        else
        {
            if($imdbID['Type'] !== "movie")
            {
                redirect(base_url('shows/' . $id));
            }
            else
            {
                $data['movie'] = $this->media_model->getMovie($imdbID['imdbID']);

                $header['pageTitle'] = " - ".$data['movie']->Title."(".(int)$data['movie']->Year.")";
                $this->load->view('header', $header);
                $this->load->view('media/media_item_movie', $data);
                $this->load->view('footer');
            }
        }
    }
}


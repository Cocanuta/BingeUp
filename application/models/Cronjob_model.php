<?php
defined('BASEPATH') OR exit('No direct script access allowed');
set_time_limit(0);
ignore_user_abort();
class Cronjob_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function runCronjob()
    {
        $this->load->database();
        $this->load->model('media_model');
        $query = $this->db->get('process_queue');
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {

                $query = $this->db->get_where('media_items', array('imdbID' => $row->imdbID));
                if($query->num_rows() > 0)
                {
                    $this->db->delete('process_queue'. array('id' => $row->id));
                }
                else
                {
                    if($row->Type === "movie")
                    {
                        $this->media_model->mysqlAddMovie($this->media_model->omdbMovie($row->imdbID));
                        $this->db->delete('process_queue', array('id' => $row->id));
                    }
                    elseif($row->Type === "series")
                    {
                        $this->media_model->mysqlAddSeries($this->media_model->omdbSeries($row->imdbID));
                        $this->db->delete('process_queue', array('id' => $row->id));
                    }
                }
            }
        }
    }
}
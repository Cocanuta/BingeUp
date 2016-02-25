<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Playlist_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model(array('media_model', 'user_model'));
    }

    public function CreateNewList($userID, $name)
    {
        $playlist = array(
            'Title' => $name,
            'Items' => "",
            'userID' => $userID,
        );

        $this->db->insert('media_playlists', $playlist);

        $data = array(
            'userID' => $userID,
            'playlistID' => $this->db->insert_id(),
            'progress' => "",
            'sync' => true,
        );

        $this->db->insert('user_playlists', $data);
    }

    public function CloneList($userID, $playlistID)
    {
        $blueprint = $this->GetPlaylistBlueprint($playlistID);

        $data = array(
            'userID' => $userID,
            'playlistID' => $playlistID,
            'progress' => $this->ConvertArrayToString($this->ConvertBlueprintArrayToUserArray($blueprint['Items'])),
            'sync' => true,
        );

        $this->db->insert('user_playlists', $data);
    }

    public function GetPlaylistBlueprint($playlistID)
    {
        $query = $this->db->get_where('media_playlists', array('id' => $playlistID));
        $playlistItems = array();
        if($query->num_rows() > 0)
        {
            $data = array(
                'playlistID' => $playlistID,
                'Title' => $query->result('Title'),
                'Items' => $this->ConvertBlueprintStringToArray($query->result('Items')),
                'userID' => $query->result('userID'),
            );
            return $data;
        }
    }

    public function ConvertBlueprintStringToArray($string)
    {
        $playlistItems = array();
        $items = explode("#", $string);
        foreach($items as $item)
        {
            $newItem = explode("|", $item);
            $playlistItems[] = array(
                'imdbID' => $newItem[0],
                'Type' => $newItem[1],
            );
        }
        return $playlistItems;
    }
    public function ConvertUserPlaylistStringToArray($string)
    {
        $playlistItems = array();
        $items = explode("#", $string);
        foreach($items as $item)
        {
            $newItem = explode("|", $item);
            $playlistItems[] = array(
                'imdbID' => $newItem[0],
                'Type' => $newItem[1],
                'Watched' => $newItem[2],
            );
        }
        return $playlistItems;
    }
    public function ConvertArrayToString($array)
    {
        $newList = array();

        foreach($array as $item)
        {
            $newList[] = implode("|", $item);
        }

        return implode("#", $newList);
    }
    public function ConvertBlueprintArrayToUserArray($array)
    {
        $newList = array();

        foreach($array as $item)
        {
            $newList[] = array(
                'imdbID' => $item['imdbID'],
                'Type' => $item['Type'],
                'Watched' => "0",
            );
        }

        return $newList;
    }
}
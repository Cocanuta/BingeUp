<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Media_model class.
 *
 * This class handles all Media related functions
 *
 * @extends CI_Controller
 */

class Media_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * CheckExistsInDB function.
     *
     * Checks to see if the imdbID is already in the database.
     *
     * @access public
     * @param mixed $imdbID
     * @return bool
     */
    public function CheckExistsInDB($imdbID)
    {
        $firstQuery = $this->db->get_where('media_items', array('imdbID' => $imdbID));
        if($firstQuery->num_rows() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * GetMostRecentItems function.
     *
     * Retrieves the $count most recent items from the media_items table.
     *
     * @access public
     * @param int $count
     * @return array
     */
    public function GetMostRecentItems($count)
    {
        $this->db->order_by("Updated", "desc"); //Order the query by the Updated field in decending order.
        $query = $this->db->get('media_items', $count); //Grab the rows from the 'media_items' table with a limit set by $count
        $result = array(); //Create a results array.
        foreach($query->result() as $row)
        {
            if($row->Type === "movie") //if the row item is a movie.
            {
                //add the query result to the array as a Movie
                $result[] = new Movie(
                    $row->Title,
                    $row->Year,
                    $row->Rated,
                    $row->Released,
                    $row->Runtime,
                    $row->Genre,
                    $row->Director,
                    $row->Writer,
                    $row->Actors,
                    $row->Plot,
                    $row->Language,
                    $row->Country,
                    $row->Awards,
                    $row->Poster,
                    $row->Metascore,
                    $row->imdbRating,
                    $row->imdbVotes,
                    $row->imdbID
                );
            }
            else
            {
                //add the query result to the array as a Series
                $result[] = new Series(
                    $row->Title,
                    $row->Year,
                    $row->Rated,
                    $row->Released,
                    $row->Runtime,
                    $row->Genre,
                    $row->Director,
                    $row->Writer,
                    $row->Actors,
                    $row->Plot,
                    $row->Language,
                    $row->Country,
                    $row->Awards,
                    $row->Poster,
                    $row->Metascore,
                    $row->imdbRating,
                    $row->imdbVotes,
                    $row->imdbID,
                    array()
                );
            }
        }
        return $result; //return the array.
    }

    /**
     * GetIDAndTypeFromName function.
     *
     * Retrieves the imdbID and Type from a $name and $year provided.
     *
     * @access public
     * @param string $name, int $year
     * @return int
     */

    public function GetIDAndTypeFromName($name, $year)
    {
        //searches omdbAPI for an item with the $name and $year provided.
        $json = $this->get_json("?t=".$name."&y=".(int)$year);
        if($json === null) //if the search returned empty.
        {
            return null;
        }
        else
        {
            //Create an array with the imdbID and Type
            $data = array(
                'imdbID' => $json['imdbID'],
                'Type' => $json['Type'],
            );
            return $data; //Return the data.
        }
    }

    /**
     * omdbSearch function.
     *
     * Performs a search using a $query string using omdbAPI.com
     *
     * @access public
     * @param mixed $query
     * @return array
     */
    public function omdbSearch($query)
    {
        //urlencode the query (to convert any special characters and spaces (' ') into url apropriate characters ('%20')
        $query = urlencode($query);
        //perform a few string replaces to remove any common special characters.
        $query = str_replace('%20', '+', $query);
        $query = str_replace(',', '', $query);
        //Perform the search using the get_json function.
        $json = $this->get_json("?s=".$query);
        //if the $json returns empty.
        if($json === null)
        {
            return null;
        }
        else
        {
            $data = array(); //create an array to hold our results.
            foreach($json['Search'] as $result) //for each result in the $json.
            {
                //Add the required information to the $data array.
                $data[] = array(
                    'Title' => $result['Title'],
                    'Year' => $result['Year'],
                    'imdbID' => $result['imdbID'],
                    'Type' => $result['Type'],
                    'Poster' => $result['Poster'],
                );
            }
            return $data;
        }
    }

    /**
     * ConvertIDtoName function.
     *
     * Converts an imdbID to a Title string.
     *
     * @access public
     * @param mixed $imdbID
     * @return string
     */
    public function ConvertIDtoName($imdbID)
    {
        $json = $this->get_json("?i=".$imdbID);

        if($json === null)
        {
            return null;
        }
        else
        {
            $title = str_replace(' ', '+', $json['Title']);
            return $title;
        }
    }

    public function CheckProcessQueue($imdbID)
    {
        $query = $this->db->get_where('process_queue', array('imdbID' => $imdbID));
        if($query->num_rows() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getShow($imdbID)
    {
        if($this->CheckExistsInDB($imdbID))
        {
            return $this->mysqlGetSeries($imdbID);
        }
        else
        {
            if($this->CheckProcessQueue($imdbID))
            {
                return $this->omdbSeriesMin($imdbID);
            }
            else
            {
                $data = array(
                    'imdbID' => $imdbID,
                    'Type' => "series",
                );
                $this->db->insert('process_queue', $data);
                return $this->omdbSeriesMin($imdbID);
            }
        }
    }

    public function getMovie($imdbID)
    {
        if($this->CheckExistsInDB($imdbID))
        {
            return $this->mysqlGetMovie($imdbID);
        }
        else
        {
            if($this->CheckProcessQueue($imdbID))
            {
                return $this->omdbMovie($imdbID);
            }
            else
            {
                $data = array(
                    'imdbID' => $imdbID,
                    'Type' => "movie",
                );
                $this->db->insert('process_queue', $data);
                return $this->omdbMovie($imdbID);
            }
        }
    }

    public function get_json($query)
    {
        $json = json_decode(file_get_contents("http://www.omdbapi.com/".$query), true);
        if((string)$json['Response'] === "True")
        {
            return $json;
        }
        else
        {
            return null;
        }
    }

    public function mysqlAddMovie($movie)
    {
        $data = array(
            'imdbID' => $movie->imdbID,
            'Title' => $movie->Title,
            'Year' => $movie->Year,
            'Rated' => $movie->Rated,
            'Released' => date("Y-m-d", strtotime($movie->Released)),
            'Runtime' => $movie->Runtime,
            'Genre' => $movie->Genre,
            'Director' => $movie->Director,
            'Writer' => $movie->Writer,
            'Actors' => $movie->Actors,
            'Plot' => $movie->Plot,
            'Language' => $movie->Language,
            'Country' => $movie->Country,
            'Awards' => $movie->Awards,
            'Poster' => $movie->Poster,
            'Metascore' => $movie->Metascore,
            'imdbRating' => $movie->imdbRating,
            'imdbVotes' => $movie->imdbVotes,
            'Type' => "movie",
        );
        $this->db->insert('media_items', $data);
    }

    public function mysqlAddSeries($series)
    {
        $data = array(
            'imdbID' => $series->imdbID,
            'Title' => $series->Title,
            'Year' => $series->Year,
            'Rated' => $series->Rated,
            'Released' => date("Y-m-d", strtotime($series->Released)),
            'Runtime' => $series->Runtime,
            'Genre' => $series->Genre,
            'Director' => $series->Director,
            'Writer' => $series->Writer,
            'Actors' => $series->Actors,
            'Plot' => $series->Plot,
            'Language' => $series->Language,
            'Country' => $series->Country,
            'Awards' => $series->Awards,
            'Poster' => $series->Poster,
            'Metascore' => $series->Metascore,
            'imdbRating' => $series->imdbRating,
            'imdbVotes' => $series->imdbVotes,
            'Type' => "series",
        );
        foreach($series->Episodes as $episode)
        {
            $episode = array(
                'imdbID' => $episode->imdbID,
                'seriesID' => $episode->seriesID,
                'Title' => $episode->Title,
                'Year' => $episode->Year,
                'Rated' => $episode->Rated,
                'Released' => date("Y-m-d", strtotime($episode->Released)),
                'Season' => $episode->Season,
                'Episode' => $episode->Episode,
                'Runtime' => $episode->Runtime,
                'Genre' => $episode->Genre,
                'Director' => $episode->Director,
                'Writer' => $episode->Writer,
                'Actors' => $episode->Actors,
                'Plot' => $episode->Plot,
                'Language' => $episode->Language,
                'Country' => $episode->Country,
                'Awards' => $episode->Awards,
                'Poster' => $episode->Poster,
                'Metascore' => $episode->Metascore,
                'imdbRating' => $episode->imdbRating,
                'imdbVotes' => $episode->imdbVotes,
                'Type' => $episode->Type,
            );
            $this->db->insert('media_episodes', $episode);
        }
        $this->db->insert('media_items', $data);
    }

    public function mysqlGetMovie($imdbID)
    {
        $query = $this->db->get_where('media_items', array('imdbID' => $imdbID));
        if($query->num_rows() === 1)
        {
            $row = $query->row();

            $movie = new Movie(
                $row->Title,
                $row->Year,
                $row->Rated,
                $row->Released,
                $row->Runtime,
                $row->Genre,
                $row->Director,
                $row->Writer,
                $row->Actors,
                $row->Plot,
                $row->Language,
                $row->Country,
                $row->Awards,
                $row->Poster,
                $row->Metascore,
                $row->imdbRating,
                $row->imdbVotes,
                $row->imdbID
            );

            return $movie;
        }

    }

    public function mysqlGetSeries($imdbID)
    {
        $query = $this->db->get_where('media_items', array('imdbID' => $imdbID));
        if($query->num_rows() === 1)
        {
            $row = $query->row();

            $series = new Series(
                $row->Title,
                $row->Year,
                $row->Rated,
                $row->Released,
                $row->Runtime,
                $row->Genre,
                $row->Director,
                $row->Writer,
                $row->Actors,
                $row->Plot,
                $row->Language,
                $row->Country,
                $row->Awards,
                $row->Poster,
                $row->Metascore,
                $row->imdbRating,
                $row->imdbVotes,
                $row->imdbID,
                $this->mysqlGetEpisodes($imdbID)
            );
            return $series;
        }
    }

    public function mysqlGetEpisodes($imdbID)
    {
        $this->db->order_by("Season", "asc");
        $this->db->order_by("Episode", "asc");
        $query = $this->db->get_where('media_episodes', array('seriesID' => $imdbID));
        $episodes = array();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $episodes[] = new Episode(
                    $row->Title,
                    $row->Year,
                    $row->Rated,
                    $row->Released,
                    $row->Season,
                    $row->Episode,
                    $row->Runtime,
                    $row->Genre,
                    $row->Director,
                    $row->Writer,
                    $row->Actors,
                    $row->Plot,
                    $row->Language,
                    $row->Country,
                    $row->Awards,
                    $row->Poster,
                    $row->Metascore,
                    $row->imdbRating,
                    $row->imdbVotes,
                    $row->imdbID,
                    $row->seriesID
                );
            }
            return $episodes;
        }
    }

    public function mysqlGetSeason($seriesID, $season)
    {
        $query = $this->db->get_where('media_episodes', array('seriesID' => $seriesID, 'Season' => $season));
        $episodes = array();
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $episodes[] = new Episode(
                    $row->Title,
                    $row->Year,
                    $row->Rated,
                    $row->Released,
                    $row->Season,
                    $row->Episode,
                    $row->Runtime,
                    $row->Genre,
                    $row->Director,
                    $row->Writer,
                    $row->Actors,
                    $row->Plot,
                    $row->Language,
                    $row->Country,
                    $row->Awards,
                    $row->Poster,
                    $row->Metascore,
                    $row->imdbRating,
                    $row->imdbVotes,
                    $row->imdbID,
                    $row->seriesID
                );
            }
            return $episodes;
        }
    }

    public function mysqlGetEpisode($seriesID, $season, $episode)
    {
        $query = $this->db->get_where('media_episodes', array('seriesID' => $seriesID, 'Season' => $season, 'Episode' => $episode));
        if($query->num_rows() > 0)
        {
            $row = $query->row();
            $episode = new Episode(
                $row->Title,
                $row->Year,
                $row->Rated,
                $row->Released,
                $row->Season,
                $row->Episode,
                $row->Runtime,
                $row->Genre,
                $row->Director,
                $row->Writer,
                $row->Actors,
                $row->Plot,
                $row->Language,
                $row->Country,
                $row->Awards,
                $row->Poster,
                $row->Metascore,
                $row->imdbRating,
                $row->imdbVotes,
                $row->imdbID,
                $row->seriesID
            );
            return $episode;
        }
    }

    public function omdbMovie($imdbID)
    {
        $json = $this->get_json("?i=".$imdbID);
        if($json === null)
        {
            return null;
        }
        $movie = new Movie(
            $json['Title'],
            $json['Year'],
            $json['Rated'],
            $json['Released'],
            $json['Runtime'],
            $json['Genre'],
            $json['Director'],
            $json['Writer'],
            $json['Actors'],
            $json['Plot'],
            $json['Language'],
            $json['Country'],
            $json['Awards'],
            $json['Poster'],
            $json['Metascore'],
            $json['imdbRating'],
            $json['imdbVotes'],
            $json['imdbID'],
            $json['Type']
        );
        return $movie;
    }

    public function omdbSeriesMin($imdbID)
    {
        $json = $this->get_json("?i=".$imdbID);
        if($json === null)
        {
            return null;
        }
        $series = new Series(
            $json['Title'],
            $json['Year'],
            $json['Rated'],
            $json['Released'],
            $json['Runtime'],
            $json['Genre'],
            $json['Director'],
            $json['Writer'],
            $json['Actors'],
            $json['Plot'],
            $json['Language'],
            $json['Country'],
            $json['Awards'],
            $json['Poster'],
            $json['Metascore'],
            $json['imdbRating'],
            $json['imdbVotes'],
            $json['imdbID'],
            array(),
            $json['Type']
        );
        return $series;
    }

    public function omdbSeries($imdbID)
    {
        $json = $this->get_json("?i=".$imdbID);
        if($json === null)
        {
            return null;
        }
        $series = new Series(
            $json['Title'],
            $json['Year'],
            $json['Rated'],
            $json['Released'],
            $json['Runtime'],
            $json['Genre'],
            $json['Director'],
            $json['Writer'],
            $json['Actors'],
            $json['Plot'],
            $json['Language'],
            $json['Country'],
            $json['Awards'],
            $json['Poster'],
            $json['Metascore'],
            $json['imdbRating'],
            $json['imdbVotes'],
            $json['imdbID'],
            $this->omdbEpisodes($imdbID),
            $json['Type']
        );
        return $series;
    }
    public function omdbEpisode($imdbID)
    {
        $json = $this->get_json("?i=".$imdbID);
        if($json === null)
        {
            return null;
        }
        $episode = new Episode(
            $json['Title'],
            $json['Year'],
            $json['Rated'],
            $json['Released'],
            $json['Season'],
            $json['Episode'],
            $json['Runtime'],
            $json['Genre'],
            $json['Director'],
            $json['Writer'],
            $json['Actors'],
            $json['Plot'],
            $json['Language'],
            $json['Country'],
            $json['Awards'],
            $json['Poster'],
            $json['Metascore'],
            $json['imdbRating'],
            $json['imdbVotes'],
            $json['imdbID'],
            $json['seriesID'],
            $json['Type']
        );
        return $episode;
    }
    public function omdbEpisodes($imdbID)
    {
        $gettingSeriesCount = true;
        $seasons = 1;
        $episodes = array();
        while($gettingSeriesCount)
        {
            $seriesJson = $this->get_json("?i=".$imdbID."&Season=".$seasons);
            if($seriesJson != null)
            {
                foreach($seriesJson['Episodes'] as $episode)
                {
                    $episodes[] = $this->omdbEpisode($episode['imdbID']);
                }
                $seasons++;
            }
            else
            {
                $gettingSeriesCount = false;
            }
        }
        return $episodes;
    }
}

class Series
{
    public $Title;
    public $Year;
    public $Rated;
    public $Released;
    public $Runtime;
    public $Genre;
    public $Director;
    public $Writer;
    public $Actors;
    public $Plot;
    public $Language;
    public $Country;
    public $Awards;
    public $Poster;
    public $Metascore;
    public $imdbRating;
    public $imdbVotes;
    public $imdbID;
    public $Episodes = array();
    public $Type;

    public function __construct($title, $year, $rated, $released, $runtime, $genre, $director, $writer, $actors, $plot, $language, $country, $awards, $poster, $metascore, $imdbrating, $imdbvotes, $imdbid, $episodes, $type = "series")
    {
        $this->Title = $title;
        $this->Year = $year;
        $this->Rated = $rated;
        $this->Released = $released;
        $this->Runtime = $runtime;
        $this->Genre = $genre;
        $this->Director = $director;
        $this->Writer = $writer;
        $this->Actors = $actors;
        $this->Plot = $plot;
        $this->Language = $language;
        $this->Country = $country;
        $this->Awards = $awards;
        $this->Poster = $poster;
        $this->Metascore = $metascore;
        $this->imdbRating = $imdbrating;
        $this->imdbVotes = $imdbvotes;
        $this->imdbID = $imdbid;
        $this->Episodes = $episodes;
        $this->Type = $type;
    }
}

class Movie
{
    public $Title;
    public $Year;
    public $Rated;
    public $Released;
    public $Runtime;
    public $Genre;
    public $Director;
    public $Writer;
    public $Actors;
    public $Plot;
    public $Language;
    public $Country;
    public $Awards;
    public $Poster;
    public $Metascore;
    public $imdbRating;
    public $imdbVotes;
    public $imdbID;
    public $Type;

    public function __construct($title, $year, $rated, $released, $runtime, $genre, $director, $writer, $actors, $plot, $language, $country, $awards, $poster, $metascore, $imdbrating, $imdbvotes, $imdbid, $type = "movie")
    {
        $this->Title = $title;
        $this->Year = $year;
        $this->Rated = $rated;
        $this->Released = $released;
        $this->Runtime = $runtime;
        $this->Genre = $genre;
        $this->Director = $director;
        $this->Writer = $writer;
        $this->Actors = $actors;
        $this->Plot = $plot;
        $this->Language = $language;
        $this->Country = $country;
        $this->Awards = $awards;
        $this->Poster = $poster;
        $this->Metascore = $metascore;
        $this->imdbRating = $imdbrating;
        $this->imdbVotes = $imdbvotes;
        $this->imdbID = $imdbid;
        $this->Type = $type;
    }
}

class Episode
{
    public $Title;
    public $Year;
    public $Rated;
    public $Released;
    public $Season;
    public $Episode;
    public $Runtime;
    public $Genre;
    public $Director;
    public $Writer;
    public $Actors;
    public $Plot;
    public $Language;
    public $Country;
    public $Awards;
    public $Poster;
    public $Metascore;
    public $imdbRating;
    public $imdbVotes;
    public $imdbID;
    public $seriesID;
    public $Type;

    public function __construct($title, $year, $rated, $released, $season, $episode, $runtime, $genre, $director, $writer, $actors, $plot, $language, $country, $awards, $poster, $metascore, $imdbrating, $imdbvotes, $imdbid, $seriesid, $type = "episode")
    {
        $this->Title = $title;
        $this->Year = $year;
        $this->Rated = $rated;
        $this->Released = $released;
        $this->Season = $season;
        $this->Episode = $episode;
        $this->Runtime = $runtime;
        $this->Genre = $genre;
        $this->Director = $director;
        $this->Writer = $writer;
        $this->Actors = $actors;
        $this->Plot = $plot;
        $this->Language = $language;
        $this->Country = $country;
        $this->Awards = $awards;
        $this->Poster = $poster;
        $this->Metascore = $metascore;
        $this->imdbRating = $imdbrating;
        $this->imdbVotes = $imdbvotes;
        $this->imdbID = $imdbid;
        $this->seriesID = $seriesid;
        $this->Type = $type;
    }
}
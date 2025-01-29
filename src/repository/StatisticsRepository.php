<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/Statistics.php';

class StatisticsRepository extends Repository
{
    public function getStatistics() :Statistics {

        //get most recent concert
        $stmtLastConcert = $this->database->connect()->prepare('
            SELECT title FROM concerts WHERE date < NOW() ORDER BY date DESC LIMIT 1
        ');
        $stmtLastConcert->execute();

        $lastConcert = $stmtLastConcert->fetch(PDO::FETCH_ASSOC);

        //get number of attended concerts
        $stmtConcertsAttended = $this->database->connect()->prepare('
            SELECT COUNT(*) FROM concerts WHERE date < NOW()
        ');
        $stmtConcertsAttended->execute();
        $concertsAttended = $stmtConcertsAttended->fetch(PDO::FETCH_ASSOC);

        //get number of artists seen
        $stmtArtistsSeen = $this->database->connect()->prepare('
            SELECT COUNT(DISTINCT artist_id) FROM concert_artist
        ');
        $stmtArtistsSeen->execute();
        $artistsSeen = $stmtArtistsSeen->fetch(PDO::FETCH_ASSOC);

        //get number of concerts per year
        $stmtConcertsPerYear = $this->database->connect()->prepare('
            SELECT extract(year from date) as year, COUNT(*) FROM concerts GROUP BY year
        ');
        $stmtConcertsPerYear->execute();
        $concertsPerYear = $stmtConcertsPerYear->fetchAll(PDO::FETCH_ASSOC);

        $concertsPerYearArray = [];

        foreach($concertsPerYear as $concert) {
            $concertsPerYearArray[$concert['year']] = $concert['count'];
        }

        //get top 5 artists
        $stmtTopArtists = $this->database->connect()->prepare('
            SELECT artist_id, COUNT(*) FROM concert_artist GROUP BY artist_id ORDER BY COUNT(*) DESC LIMIT 5
        ');
        $stmtTopArtists->execute();
        $topArtists = $stmtTopArtists->fetchAll(PDO::FETCH_ASSOC);

        $topArtistsArray =[];

        for( $i = 0; $i < count($topArtists); $i++ )
        {
            $stmt = $this->database->connect()->prepare('
                SELECT name FROM artists WHERE artist_id = :artist_id
            ');
            $stmt->bindParam(':artist_id', $topArtists[$i]['artist_id'], PDO::PARAM_INT);
            $stmt->execute();
            $artist = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $topArtistsArray[$artist['name']] = $topArtists[$i]['count'];
        }

        //get top 5 genres
        $stmtTopGenres = $this->database->connect()->prepare('
            SELECT genre_id, COUNT(*) FROM concerts GROUP BY genre_id ORDER BY COUNT(*) DESC LIMIT 5
        ');
        $stmtTopGenres->execute();
        $topGenres = $stmtTopGenres->fetchAll(PDO::FETCH_ASSOC);
        $topGenresArray =[];
        for( $i = 0; $i < count($topGenres); $i++ )
        {
            $stmt = $this->database->connect()->prepare('
                SELECT name FROM concert_genre WHERE genre_id = :genre_id
            ');
            $stmt->bindParam(':genre_id', $topGenres[$i]['genre_id'], PDO::PARAM_STR);
            $stmt->execute();
            $genre = $stmt->fetch(PDO::FETCH_ASSOC);
            if($genre) {
                $topGenresArray[$genre['name']] = $genre['count'];
            }
        }

        //get top 5 venues
        $stmtTopVenues = $this->database->connect()->prepare('
            SELECT venue_id, COUNT(*) FROM concerts GROUP BY venue_id ORDER BY COUNT(*) DESC LIMIT 5
        ');
        $stmtTopVenues->execute();
        $topVenues = $stmtTopVenues->fetchAll(PDO::FETCH_ASSOC);
        $topVenuesArray =[];
        for( $i = 0; $i < count($topVenues); $i++ )
        {
            $stmt = $this->database->connect()->prepare('
                SELECT name FROM venues WHERE venue_id = :venue_id
            ');
            $stmt->bindParam(':venue_id', $topVenues[$i]['venue_id'], PDO::PARAM_INT);
            $stmt->execute();
            $venue = $stmt->fetch(PDO::FETCH_ASSOC);
            if($venue) {
                $topVenuesArray[$venue['name']] = $topVenues[$i]['count'];
            }
        }

        $statistics = new Statistics(
            $lastConcert['title'],
            $concertsAttended['count'],
            $artistsSeen['count'],
            $concertsPerYearArray,
            $topArtistsArray,
            $topGenresArray,
            $topVenuesArray
        );
        
        return $statistics;
    }
}
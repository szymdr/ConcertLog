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

        if (empty($lastConcert)) {
            $lastConcert['title'] = '';
        }

        //get number of attended concerts
        $stmtConcertsAttended = $this->database->connect()->prepare('
            SELECT COUNT(*) FROM concerts WHERE date < NOW()
        ');
        $stmtConcertsAttended->execute();
        $concertsAttended = $stmtConcertsAttended->fetch(PDO::FETCH_ASSOC);

        if (empty($concertsAttended)) {
            $concertsAttended['count'] = 0;
        }

        //get number of artists seen
        $stmtArtistsSeen = $this->database->connect()->prepare('
            SELECT COUNT(DISTINCT artist_id) FROM concert_artist
        ');
        $stmtArtistsSeen->execute();
        $artistsSeen = $stmtArtistsSeen->fetch(PDO::FETCH_ASSOC);

        if (empty($artistsSeen)) {
            $artistsSeen['count'] = 0;
        }

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
    
    public function getUserStatistics(int $user_id) :Statistics {
        // Most recent concert for the user
        $stmtLastConcert = $this->database->connect()->prepare('
            SELECT c.title
            FROM concerts c
            JOIN concert_user cu ON c.concert_id = cu.concert_id
            WHERE cu.user_id = :user_id
              AND c.date < NOW()
            ORDER BY c.date DESC
            LIMIT 1
        ');
        $stmtLastConcert->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtLastConcert->execute();
        $lastConcert = $stmtLastConcert->fetch(PDO::FETCH_ASSOC);

        if(empty($lastConcert)) {
            $lastConcert['title'] = 'No concerts attended yet';
        }
    
        // Number of concerts attended by the user
        $stmtConcertsAttended = $this->database->connect()->prepare('
            SELECT COUNT(*) as count
            FROM concerts c
            JOIN concert_user cu ON c.concert_id = cu.concert_id
            WHERE cu.user_id = :user_id
              AND c.date < NOW()
        ');
        $stmtConcertsAttended->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtConcertsAttended->execute();
        $concertsAttended = $stmtConcertsAttended->fetch(PDO::FETCH_ASSOC);

        if(empty($concertsAttended)) {
            $concertsAttended['count'] = 0;
        }

    
        // Number of distinct artists seen by the user
        $stmtArtistsSeen = $this->database->connect()->prepare('
            SELECT COUNT(DISTINCT ca.artist_id) as count
            FROM concert_artist ca
            JOIN concerts c ON c.concert_id = ca.concert_id
            JOIN concert_user cu ON c.concert_id = cu.concert_id
            WHERE cu.user_id = :user_id
        ');
        $stmtArtistsSeen->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtArtistsSeen->execute();
        $artistsSeen = $stmtArtistsSeen->fetch(PDO::FETCH_ASSOC);

        if(empty($artistsSeen)) {
            $artistsSeen['count'] = 0;
        }
    
        // Number of concerts per year for the user
        $stmtConcertsPerYear = $this->database->connect()->prepare('
            SELECT EXTRACT(YEAR FROM c.date) AS year, COUNT(*) AS count
            FROM concerts c
            JOIN concert_user cu ON c.concert_id = cu.concert_id
            WHERE cu.user_id = :user_id
            GROUP BY year
        ');
        $stmtConcertsPerYear->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtConcertsPerYear->execute();
        $concertsPerYear = $stmtConcertsPerYear->fetchAll(PDO::FETCH_ASSOC);
        $concertsPerYearArray = [];
        foreach ($concertsPerYear as $concert) {
            $concertsPerYearArray[$concert['year']] = $concert['count'];
        }

        // Get top 5 artists for this user
        $stmtTopArtists = $this->database->connect()->prepare('
            SELECT ca.artist_id, COUNT(*) as count
            FROM concert_artist ca
            JOIN concert_user cu ON ca.concert_id = cu.concert_id
            WHERE cu.user_id = :user_id
            GROUP BY ca.artist_id
            ORDER BY count DESC
            LIMIT 5
        ');
        $stmtTopArtists->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtTopArtists->execute();
        $topArtists = $stmtTopArtists->fetchAll(PDO::FETCH_ASSOC);

        $topArtistsArray = [];
        for ($i = 0; $i < count($topArtists); $i++) {
            $stmt = $this->database->connect()->prepare('
                SELECT name
                FROM artists
                WHERE artist_id = :artist_id
            ');
            $stmt->bindParam(':artist_id', $topArtists[$i]['artist_id'], PDO::PARAM_INT);
            $stmt->execute();
            $artist = $stmt->fetch(PDO::FETCH_ASSOC);
            $topArtistsArray[$artist['name']] = $topArtists[$i]['count'];
        }

        // Get top 5 genres for this user
        $stmtTopGenres = $this->database->connect()->prepare('
            SELECT c.genre_id, COUNT(*) as count
            FROM concerts c
            JOIN concert_user cu ON c.concert_id = cu.concert_id
            WHERE cu.user_id = :user_id
            GROUP BY c.genre_id
            ORDER BY count DESC
            LIMIT 5
        ');
        $stmtTopGenres->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtTopGenres->execute();
        $topGenres = $stmtTopGenres->fetchAll(PDO::FETCH_ASSOC);

        $topGenresArray = [];
        for ($i = 0; $i < count($topGenres); $i++) {
            $stmt = $this->database->connect()->prepare('
                SELECT name
                FROM concert_genre
                WHERE genre_id = :genre_id
            ');
            $stmt->bindParam(':genre_id', $topGenres[$i]['genre_id'], PDO::PARAM_INT);
            $stmt->execute();
            $genre = $stmt->fetch(PDO::FETCH_ASSOC);
            if($genre) {
                $topGenresArray[$genre['name']] = $topGenres[$i]['count'];
            }
        }

        // Get top 5 locations for this user
        $stmtTopLocations = $this->database->connect()->prepare('
            SELECT c.venue_id, COUNT(*) as count
            FROM concerts c
            JOIN concert_user cu ON c.concert_id = cu.concert_id
            WHERE cu.user_id = :user_id
            GROUP BY c.venue_id
            ORDER BY count DESC
            LIMIT 5
        ');
        $stmtTopLocations->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtTopLocations->execute();
        $topLocations = $stmtTopLocations->fetchAll(PDO::FETCH_ASSOC);

        $topVenuesArray = [];
        for ($i = 0; $i < count($topLocations); $i++) {
            $stmt = $this->database->connect()->prepare('
                SELECT name
                FROM venues
                WHERE venue_id = :venue_id
            ');
            $stmt->bindParam(':venue_id', $topLocations[$i]['venue_id'], PDO::PARAM_INT);
            $stmt->execute();
            $location = $stmt->fetch(PDO::FETCH_ASSOC);
            if($location) {
                $topVenuesArray[$location['name']] = $topLocations[$i]['count'];
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
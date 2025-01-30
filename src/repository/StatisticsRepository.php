<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/Statistics.php';

class StatisticsRepository extends Repository
{
    public function getStatistics(): Statistics
    {
        $pdo = $this->database->connect();
        $pdo->beginTransaction();

        try {
            // Get most recent concert
            $stmtLastConcert = $pdo->prepare('
                SELECT title FROM concerts WHERE date < NOW() ORDER BY date DESC LIMIT 1
            ');
            $stmtLastConcert->execute();
            $lastConcert = $stmtLastConcert->fetch(PDO::FETCH_ASSOC) ?: ['title' => ''];

            // Get number of attended concerts
            $stmtConcertsAttended = $pdo->prepare('
                SELECT COUNT(*) as count FROM concerts WHERE date < NOW()
            ');
            $stmtConcertsAttended->execute();
            $concertsAttended = $stmtConcertsAttended->fetch(PDO::FETCH_ASSOC) ?: ['count' => 0];

            // Get number of artists seen
            $stmtArtistsSeen = $pdo->prepare('
                SELECT COUNT(DISTINCT artist_id) as count FROM concert_artist
            ');
            $stmtArtistsSeen->execute();
            $artistsSeen = $stmtArtistsSeen->fetch(PDO::FETCH_ASSOC) ?: ['count' => 0];

            // Get number of concerts per year
            $stmtConcertsPerYear = $pdo->prepare('
                SELECT EXTRACT(YEAR FROM date) as year, COUNT(*) as count FROM concerts GROUP BY year
            ');
            $stmtConcertsPerYear->execute();
            $concertsPerYear = $stmtConcertsPerYear->fetchAll(PDO::FETCH_ASSOC);

            $concertsPerYearArray = [];
            foreach ($concertsPerYear as $concert) {
                $concertsPerYearArray[$concert['year']] = $concert['count'];
            }

            // Get top 5 artists
            $stmtTopArtists = $pdo->prepare('
                SELECT artist_id, COUNT(*) as count FROM concert_artist GROUP BY artist_id ORDER BY count DESC LIMIT 5
            ');
            $stmtTopArtists->execute();
            $topArtists = $stmtTopArtists->fetchAll(PDO::FETCH_ASSOC);

            $topArtistsArray = [];
            foreach ($topArtists as $artistData) {
                $stmt = $pdo->prepare('
                    SELECT name FROM artists WHERE artist_id = :artist_id
                ');
                $stmt->bindParam(':artist_id', $artistData['artist_id'], PDO::PARAM_INT);
                $stmt->execute();
                $artist = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($artist) {
                    $topArtistsArray[$artist['name']] = $artistData['count'];
                }
            }

            // Get top 5 genres
            $stmtTopGenres = $pdo->prepare('
                SELECT genre_id, COUNT(*) as count FROM concerts GROUP BY genre_id ORDER BY count DESC LIMIT 5
            ');
            $stmtTopGenres->execute();
            $topGenres = $stmtTopGenres->fetchAll(PDO::FETCH_ASSOC);

            $topGenresArray = [];
            foreach ($topGenres as $genreData) {
                $stmt = $pdo->prepare('
                    SELECT name FROM concert_genre WHERE genre_id = :genre_id
                ');
                $stmt->bindParam(':genre_id', $genreData['genre_id'], PDO::PARAM_INT);
                $stmt->execute();
                $genre = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($genre) {
                    $topGenresArray[$genre['name']] = $genreData['count'];
                }
            }

            // Get top 5 venues
            $stmtTopVenues = $pdo->prepare('
                SELECT venue_id, COUNT(*) as count FROM concerts GROUP BY venue_id ORDER BY count DESC LIMIT 5
            ');
            $stmtTopVenues->execute();
            $topVenues = $stmtTopVenues->fetchAll(PDO::FETCH_ASSOC);

            $topVenuesArray = [];
            foreach ($topVenues as $venueData) {
                $stmt = $pdo->prepare('
                    SELECT name FROM venues WHERE venue_id = :venue_id
                ');
                $stmt->bindParam(':venue_id', $venueData['venue_id'], PDO::PARAM_INT);
                $stmt->execute();
                $venue = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($venue) {
                    $topVenuesArray[$venue['name']] = $venueData['count'];
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

            $pdo->commit();
            return $statistics;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function getUserStatistics(int $user_id): Statistics
    {
        $pdo = $this->database->connect();
        $pdo->beginTransaction();

        try {
            // Most recent concert for the user
            $stmtLastConcert = $pdo->prepare('
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
            $lastConcert = $stmtLastConcert->fetch(PDO::FETCH_ASSOC) ?: ['title' => 'No concerts attended yet'];

            // Number of concerts attended by the user
            $stmtConcertsAttended = $pdo->prepare('
                SELECT COUNT(*) as count
                FROM concerts c
                JOIN concert_user cu ON c.concert_id = cu.concert_id
                WHERE cu.user_id = :user_id
                  AND c.date < NOW()
            ');
            $stmtConcertsAttended->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmtConcertsAttended->execute();
            $concertsAttended = $stmtConcertsAttended->fetch(PDO::FETCH_ASSOC) ?: ['count' => 0];

            // Number of distinct artists seen by the user
            $stmtArtistsSeen = $pdo->prepare('
                SELECT COUNT(DISTINCT ca.artist_id) as count
                FROM concert_artist ca
                JOIN concerts c ON c.concert_id = ca.concert_id
                JOIN concert_user cu ON c.concert_id = cu.concert_id
                WHERE cu.user_id = :user_id
            ');
            $stmtArtistsSeen->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmtArtistsSeen->execute();
            $artistsSeen = $stmtArtistsSeen->fetch(PDO::FETCH_ASSOC) ?: ['count' => 0];

            // Number of concerts per year for the user
            $stmtConcertsPerYear = $pdo->prepare('
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
            $stmtTopArtists = $pdo->prepare('
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
            foreach ($topArtists as $artistData) {
                $stmt = $pdo->prepare('
                    SELECT name
                    FROM artists
                    WHERE artist_id = :artist_id
                ');
                $stmt->bindParam(':artist_id', $artistData['artist_id'], PDO::PARAM_INT);
                $stmt->execute();
                $artist = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($artist) {
                    $topArtistsArray[$artist['name']] = $artistData['count'];
                }
            }

            // Get top 5 genres for this user
            $stmtTopGenres = $pdo->prepare('
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
            foreach ($topGenres as $genreData) {
                $stmt = $pdo->prepare('
                    SELECT name
                    FROM concert_genre
                    WHERE genre_id = :genre_id
                ');
                $stmt->bindParam(':genre_id', $genreData['genre_id'], PDO::PARAM_INT);
                $stmt->execute();
                $genre = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($genre) {
                    $topGenresArray[$genre['name']] = $genreData['count'];
                }
            }

            // Get top 5 locations for this user
            $stmtTopLocations = $pdo->prepare('
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
            foreach ($topLocations as $venueData) {
                $stmt = $pdo->prepare('
                    SELECT name
                    FROM venues
                    WHERE venue_id = :venue_id
                ');
                $stmt->bindParam(':venue_id', $venueData['venue_id'], PDO::PARAM_INT);
                $stmt->execute();
                $venue = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($venue) {
                    $topVenuesArray[$venue['name']] = $venueData['count'];
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

            $pdo->commit();
            return $statistics;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
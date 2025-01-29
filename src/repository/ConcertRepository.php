<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/Concert.php';

class ConcertRepository extends Repository
{
    public function addConcert(Concert $concert)
    {
        // Check if the artist is in the database
        $stmtArtist = $this->database->connect()->prepare('SELECT artist_id FROM artists WHERE name = :name');
        $artist = $concert->getArtist();
        $stmtArtist->bindParam(':name', $artist, PDO::PARAM_STR);
        $stmtArtist->execute();
        $artistId = $stmtArtist->fetchColumn();
    
        if (!$artistId) {
            $insertArtist = $this->database->connect()->prepare('
                INSERT INTO artists (name) VALUES (:name) RETURNING artist_id
            ');
            $insertArtist->bindParam(':name', $artist, PDO::PARAM_STR);
            $insertArtist->execute();
            $artistId = $insertArtist->fetchColumn();
        }

        $stmtGenre = $this->database->connect()->prepare('SELECT genre_id FROM concert_genre WHERE name = :name');
        $genre = $concert->getGenre();
        $stmtGenre->bindParam(':name', $genre, PDO::PARAM_STR);
        $stmtGenre->execute();
        $genreId = $stmtGenre->fetchColumn();
        if (!$genreId) {
            $insertGenre = $this->database->connect()->prepare('
                INSERT INTO concert_genre (name) VALUES (:name) RETURNING genre_id
            ');
            $insertGenre->bindParam(':name', $genre, PDO::PARAM_STR);
            $insertGenre->execute();
            $genreId = $insertGenre->fetchColumn();
        }
    
        // Check if the venue is in the database
        $stmtVenue = $this->database->connect()->prepare('SELECT venue_id FROM venues WHERE name = :name');
        $venue = $concert->getVenue();
        $stmtVenue->bindParam(':name', $venue, PDO::PARAM_STR);
        $stmtVenue->execute();
        $venueId = $stmtVenue->fetchColumn();
    
        if (!$venueId) {
            $insertVenue = $this->database->connect()->prepare('
                INSERT INTO venues (name) VALUES (:name) RETURNING venue_id
            ');
            $insertVenue->bindParam(':name', $venue, PDO::PARAM_STR);
            $insertVenue->execute();
            $venueId = $insertVenue->fetchColumn();
        }
    
        // Check if the location is in the database
        $stmtLocation = $this->database->connect()->prepare('SELECT location_id FROM locations WHERE name = :name');
        $location = $concert->getLocation();
        $stmtLocation->bindParam(':name', $location, PDO::PARAM_STR);
        $stmtLocation->execute();
        $locationId = $stmtLocation->fetchColumn();
    
        if (!$locationId) {
            $insertLocation = $this->database->connect()->prepare('
                INSERT INTO locations (name) VALUES (:name) RETURNING location_id
            ');
            $insertLocation->bindParam(':name', $location, PDO::PARAM_STR);
            $insertLocation->execute();
            $locationId = $insertLocation->fetchColumn();
        }
    
        // Insert the concert
        $stmt = $this->database->connect()->prepare('
            INSERT INTO concerts (date, title, genre_id, venue_id, location_id)
            VALUES (?, ?, ?, ?, ?) RETURNING concert_id
        ');
    
        $stmt->execute([
            $concert->getDate(),
            $concert->getTitle(),
            $genreId,
            $venueId,
            $locationId
        ]);
    
        $concertId = $stmt->fetchColumn();

        $stmtConcertArtist = $this->database->connect()->prepare('
            INSERT INTO concert_artist (concert_id, artist_id)
            VALUES (?, ?)
        ');
        $stmtConcertArtist->execute([
            $concertId,
            $artistId
        ]);
    
        // Insert the concert images
        $stmtConcertImage = $this->database->connect()->prepare('
            INSERT INTO concert_picture (concert_id, picture_path)
            VALUES (?, ?)
        ');

        foreach ($concert->getImages() as $image) {
            $stmtConcertImage->execute([
                $concertId,
                $image
            ]);
        }

        $stmConcertUser = $this->database->connect()->prepare('
        INSERT INTO concert_user (concert_id, user_id)
        VALUES (?, ?)
        ');
        $stmConcertUser->execute([
            $concertId,
            $_SESSION['user_id']
        ]);
        
    }

    public function getConcerts(): array
    {
        $result = [];
    
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM concerts ORDER BY created_at DESC;
        ');
        $stmt->execute();
        $concerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($concerts as $concert) {
            $stmtConcertArtist = $this->database->connect()->prepare('
                SELECT artist_id FROM concert_artist WHERE concert_id = :concert_id
            ');
            $stmtConcertArtist->bindParam(':concert_id', $concert['concert_id'], PDO::PARAM_INT);
            $stmtConcertArtist->execute();
            $artistId = $stmtConcertArtist->fetchColumn();
    
            $stmtArtist = $this->database->connect()->prepare('
                SELECT name FROM artists WHERE artist_id = :artist_id
            ');
            $stmtArtist->bindParam(':artist_id', $artistId, PDO::PARAM_INT);
            $stmtArtist->execute();
            $artist = $stmtArtist->fetchColumn();
    
            if (!$artist) {
                $artist = 'Unknown Artist';
            }

            $stmtGenre = $this->database->connect()->prepare('
                SELECT name FROM concert_genre WHERE genre_id = :genre_id
            ');
            $stmtGenre->bindParam(':genre_id', $concert['genre_id'], PDO::PARAM_STR);
            $stmtGenre->execute();
            $genre = $stmtGenre->fetchColumn();

            if (!$genre) {
                $genre = 'Unknown Genre';
            }

            $stmtVenue = $this->database->connect()->prepare('
                SELECT name FROM venues WHERE venue_id = :venue_id
            ');
            $stmtVenue->bindParam(':venue_id', $concert['venue_id'], PDO::PARAM_INT);
            $stmtVenue->execute();

            $venue = $stmtVenue->fetchColumn();
            if (!$venue) {
                $venue = 'Unknown Venue';
            }

            $stmtLocation = $this->database->connect()->prepare('
                SELECT name FROM locations WHERE location_id = :location_id
            ');
            $stmtLocation->bindParam(':location_id', $concert['location_id'], PDO::PARAM_INT);
            $stmtLocation->execute();
            $location = $stmtLocation->fetchColumn();
            if (!$location) {
                $location = 'Unknown Location';
            }

            $stmtImages = $this->database->connect()->prepare('
                SELECT picture_path FROM concert_picture WHERE concert_id = :concert_id
            ');
            $stmtImages->bindParam(':concert_id', $concert['concert_id'], PDO::PARAM_INT);
            $stmtImages->execute();
            $images = $stmtImages->fetchAll(PDO::FETCH_COLUMN);

            if (empty($images)) {
                $images = ['default_concert.jpg'];
            }
    
            $result[] = new Concert(
                $artist,
                $concert['date'],
                $concert['title'],
                $genre,
                $venue,
                $location,
                $images
            );
        }
    
        return $result;
    }

    public function getUserConcerts(int $user_id): array
    {
        $result = [];

        $stmt = $this->database->connect()->prepare('
            SELECT c.*
            FROM concerts c
            INNER JOIN concert_user cu ON c.concert_id = cu.concert_id
            WHERE cu.user_id = :user_id
            ORDER BY c.created_at DESC
        ');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $concerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($concerts as $concert) {
            $stmtConcertArtist = $this->database->connect()->prepare('
                SELECT artist_id FROM concert_artist WHERE concert_id = :concert_id
            ');
            $stmtConcertArtist->bindParam(':concert_id', $concert['concert_id'], PDO::PARAM_INT);
            $stmtConcertArtist->execute();
            $artistId = $stmtConcertArtist->fetchColumn();
    
            $stmtArtist = $this->database->connect()->prepare('
                SELECT name FROM artists WHERE artist_id = :artist_id
            ');
            $stmtArtist->bindParam(':artist_id', $artistId, PDO::PARAM_INT);
            $stmtArtist->execute();
            $artist = $stmtArtist->fetchColumn();
    
            if (!$artist) {
                $artist = 'Unknown Artist';
            }

            $stmtGenre = $this->database->connect()->prepare('
                SELECT name FROM concert_genre WHERE genre_id = :genre_id
            ');
            $stmtGenre->bindParam(':genre_id', $concert['genre_id'], PDO::PARAM_STR);
            $stmtGenre->execute();
            $genre = $stmtGenre->fetchColumn();

            if (!$genre) {
                $genre = 'Unknown Genre';
            }

            $stmtVenue = $this->database->connect()->prepare('
                SELECT name FROM venues WHERE venue_id = :venue_id
            ');
            $stmtVenue->bindParam(':venue_id', $concert['venue_id'], PDO::PARAM_INT);
            $stmtVenue->execute();

            $venue = $stmtVenue->fetchColumn();
            if (!$venue) {
                $venue = 'Unknown Venue';
            }

            $stmtLocation = $this->database->connect()->prepare('
                SELECT name FROM locations WHERE location_id = :location_id
            ');
            $stmtLocation->bindParam(':location_id', $concert['location_id'], PDO::PARAM_INT);
            $stmtLocation->execute();
            $location = $stmtLocation->fetchColumn();
            if (!$location) {
                $location = 'Unknown Location';
            }

            $stmtImages = $this->database->connect()->prepare('
                SELECT picture_path FROM concert_picture WHERE concert_id = :concert_id
            ');
            $stmtImages->bindParam(':concert_id', $concert['concert_id'], PDO::PARAM_INT);
            $stmtImages->execute();
            $images = $stmtImages->fetchAll(PDO::FETCH_COLUMN);

            if (empty($images)) {
                $images = ['default_concert.jpg'];
            }
    
            $result[] = new Concert(
                $artist,
                $concert['date'],
                $concert['title'],
                $genre,
                $venue,
                $location,
                $images
            );
        }
    
        return $result;
    }

}
<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/Concert.php';

class ConcertRepository extends Repository
{

    public function getConcert(int $id): ?Concert
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM concerts WHERE id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $concert = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($concert == false) {
            return null;
        }

        return new Concert(
            'artist',  
            $concert['date'],
            $concert['title'],
            $concert['venue_id'],
            $concert['location_id'],
            ['concert1.jpg']
        );
    }

    public function addConcert(Concert $concert)
    {
        //check if the artist is in the database
        $stmtArtist = $this->database->connect()->prepare('SELECT artist_id FROM artists WHERE name = :name');
        $artist = $concert->getArtist();
        $stmtArtist->bindParam(':name', $artist, PDO::PARAM_STR);
        $stmtArtist->execute();
        $artistId = $stmtArtist->fetchColumn();

        if (!$artistId) {
            $insertArtist = $this->database->connect()->prepare('INSERT INTO artists (name) VALUES (:name)');
            $insertArtist->bindParam(':name', $artist);
            $insertArtist->execute();
            $artistId = $this->database->connect()->lastInsertId('artists_artist_id_seq');
        }

        //check if the venue is in the database
        $stmtVenue = $this->database->connect()->prepare('SELECT venue_id FROM venues WHERE name = :name');
        $venue = $concert->getVenue();
        $stmtVenue->bindParam(':name', $venue, PDO::PARAM_STR);
        $stmtVenue->execute();
        $venueId = $stmtVenue->fetchColumn();
        if (!$venueId) {
            $insertVenue = $this->database->connect()->prepare('INSERT INTO venues (name) VALUES (:name)');
            $insertVenue->bindParam(':name', $venue);
            $insertVenue->execute();
            $venueId = $this->database->connect()->lastInsertId('venues_venue_id_seq');
        }

        //check if the location is in the database
        $stmtLocation = $this->database->connect()->prepare('SELECT location_id FROM locations WHERE name = :name');
        $location = $concert->getLocation();
        $stmtLocation->bindParam(':name', $location, PDO::PARAM_STR);
        $stmtLocation->execute();
        $locationId = $stmtLocation->fetchColumn();

        if (!$locationId) {
            $insertLocation = $this->database->connect()->prepare('INSERT INTO locations (name) VALUES (:name)');
            $insertLocation->bindParam(':name', $location, PDO::PARAM_STR);
            $insertLocation->execute();
            $locationId = $this->database->connect()->lastInsertId('locations_location_id_seq');
        }
        


        $stmt = $this->database->connect()->prepare('
            INSERT INTO concerts (date, title, venue_id, location_id)
            VALUES (?, ?, ?, ?)
        ');

        $stmt->execute([
            $concert->getDate(),
            $concert->getTitle(),
            $venueId,
            $locationId
        ]);

        $concertId = $this->database->connect()->lastInsertId('concerts_concert_id_seq');

        $stmtConcertArtist = $this->database->connect()->prepare('
            INSERT INTO concerts_artists (concert_id, artist_id)
            VALUES (?, ?)
        ');
        $stmtConcertArtist->execute([
            $concertId,
            $artistId
        ]);
    }

    public function getConcerts(): array
    {
        $result = [];
    
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM concerts;
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
    
            $result[] = new Concert(
                $artist,
                $concert['date'],
                $concert['title'],
                $venue,
                $location,
                ['background.jpg', 'background2.jpg']
            );
        }
    
        return $result;
    }

}
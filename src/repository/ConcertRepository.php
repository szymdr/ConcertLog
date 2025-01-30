<?php
require_once 'Repository.php';
require_once __DIR__.'/../models/Concert.php';

class ConcertRepository extends Repository
{
    public function addConcert(Concert $concert)
    {
        $pdo = $this->database->connect();
        $pdo->beginTransaction();

        try {
            // Check if the artist is in the database
            $stmtArtist = $pdo->prepare('SELECT artist_id FROM artists WHERE name = :name');
            $artist = $concert->getArtist();
            $stmtArtist->bindParam(':name', $artist, PDO::PARAM_STR);
            $stmtArtist->execute();
            $artistId = $stmtArtist->fetchColumn();

            if (!$artistId) {
                $insertArtist = $pdo->prepare('
                    INSERT INTO artists (name) VALUES (:name) RETURNING artist_id
                ');
                $insertArtist->bindParam(':name', $artist, PDO::PARAM_STR);
                $insertArtist->execute();
                $artistId = $insertArtist->fetchColumn();
            }

            // Check if the genre is in the database
            $stmtGenre = $pdo->prepare('SELECT genre_id FROM concert_genre WHERE name = :name');
            $genre = $concert->getGenre();
            $stmtGenre->bindParam(':name', $genre, PDO::PARAM_STR);
            $stmtGenre->execute();
            $genreId = $stmtGenre->fetchColumn();
            if (!$genreId) {
                $insertGenre = $pdo->prepare('
                    INSERT INTO concert_genre (name) VALUES (:name) RETURNING genre_id
                ');
                $insertGenre->bindParam(':name', $genre, PDO::PARAM_STR);
                $insertGenre->execute();
                $genreId = $insertGenre->fetchColumn();
            }

            // Check if the venue is in the database
            $stmtVenue = $pdo->prepare('SELECT venue_id FROM venues WHERE name = :name');
            $venue = $concert->getVenue();
            $stmtVenue->bindParam(':name', $venue, PDO::PARAM_STR);
            $stmtVenue->execute();
            $venueId = $stmtVenue->fetchColumn();

            if (!$venueId) {
                $insertVenue = $pdo->prepare('
                    INSERT INTO venues (name) VALUES (:name) RETURNING venue_id
                ');
                $insertVenue->bindParam(':name', $venue, PDO::PARAM_STR);
                $insertVenue->execute();
                $venueId = $insertVenue->fetchColumn();
            }

            // Check if the location is in the database
            $stmtLocation = $pdo->prepare('SELECT location_id FROM locations WHERE name = :name');
            $location = $concert->getLocation();
            $stmtLocation->bindParam(':name', $location, PDO::PARAM_STR);
            $stmtLocation->execute();
            $locationId = $stmtLocation->fetchColumn();

            if (!$locationId) {
                $insertLocation = $pdo->prepare('
                    INSERT INTO locations (name) VALUES (:name) RETURNING location_id
                ');
                $insertLocation->bindParam(':name', $location, PDO::PARAM_STR);
                $insertLocation->execute();
                $locationId = $insertLocation->fetchColumn();
            }

            $stmt = $pdo->prepare('
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

            $stmtConcertArtist = $pdo->prepare('
                INSERT INTO concert_artist (concert_id, artist_id)
                VALUES (?, ?)
            ');
            $stmtConcertArtist->execute([
                $concertId,
                $artistId
            ]);

            $stmtConcertImage = $pdo->prepare('
                INSERT INTO concert_picture (concert_id, picture_path)
                VALUES (?, ?)
            ');

            foreach ($concert->getImages() as $image) {
                $stmtConcertImage->execute([
                    $concertId,
                    $image
                ]);
            }

            $stmtConcertUser = $pdo->prepare('
                INSERT INTO concert_user (concert_id, user_id)
                VALUES (?, ?)
            ');
            $stmtConcertUser->execute([
                $concertId,
                $concert->getAddedBy()
            ]);

            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function getConcerts(): array
    {
        $pdo = $this->database->connect();
        $pdo->beginTransaction();

        try {
            $result = [];

            $stmt = $pdo->prepare('
                SELECT * FROM concerts ORDER BY created_at DESC;
            ');
            $stmt->execute();
            $concerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($concerts as $concert) {
                // Fetch the first user_id associated with this concert
                $stmtUser = $pdo->prepare('
                    SELECT u.username
                    FROM users u
                    INNER JOIN concert_user cu ON u.user_id = cu.user_id
                    WHERE cu.concert_id = :concert_id
                    LIMIT 1
                ');
                $stmtUser->bindParam(':concert_id', $concert['concert_id'], PDO::PARAM_INT);
                $stmtUser->execute();
                $addedBy = $stmtUser->fetchColumn() ?: 'Unknown';

                // Fetch artist
                $stmtConcertArtist = $pdo->prepare('
                    SELECT artist_id FROM concert_artist WHERE concert_id = :concert_id
                ');
                $stmtConcertArtist->bindParam(':concert_id', $concert['concert_id'], PDO::PARAM_INT);
                $stmtConcertArtist->execute();
                $artistId = $stmtConcertArtist->fetchColumn();

                $stmtArtist = $pdo->prepare('
                    SELECT name FROM artists WHERE artist_id = :artist_id
                ');
                $stmtArtist->bindParam(':artist_id', $artistId, PDO::PARAM_INT);
                $stmtArtist->execute();
                $artist = $stmtArtist->fetchColumn() ?: 'Unknown Artist';

                // Fetch genre
                $stmtGenre = $pdo->prepare('
                    SELECT name FROM concert_genre WHERE genre_id = :genre_id
                ');
                $stmtGenre->bindParam(':genre_id', $concert['genre_id'], PDO::PARAM_STR);
                $stmtGenre->execute();
                $genre = $stmtGenre->fetchColumn() ?: 'Unknown Genre';

                // Fetch venue
                $stmtVenue = $pdo->prepare('
                    SELECT name FROM venues WHERE venue_id = :venue_id
                ');
                $stmtVenue->bindParam(':venue_id', $concert['venue_id'], PDO::PARAM_INT);
                $stmtVenue->execute();
                $venue = $stmtVenue->fetchColumn() ?: 'Unknown Venue';

                // Fetch location
                $stmtLocation = $pdo->prepare('
                    SELECT name FROM locations WHERE location_id = :location_id
                ');
                $stmtLocation->bindParam(':location_id', $concert['location_id'], PDO::PARAM_INT);
                $stmtLocation->execute();
                $location = $stmtLocation->fetchColumn() ?: 'Unknown Location';

                // Fetch images
                $stmtImages = $pdo->prepare('
                    SELECT picture_path FROM concert_picture WHERE concert_id = :concert_id
                ');
                $stmtImages->bindParam(':concert_id', $concert['concert_id'], PDO::PARAM_INT);
                $stmtImages->execute();
                $images = $stmtImages->fetchAll(PDO::FETCH_COLUMN) ?: ['default_concert.jpg'];

                $singleConcert = new Concert(
                    $artist,
                    $concert['date'],
                    $concert['title'],
                    $genre,
                    $venue,
                    $location,
                    $images,
                    $addedBy
                );
                $singleConcert->setConcertId($concert['concert_id']);

                $result[] = $singleConcert;
            }

            $pdo->commit();
            return $result;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function getUserConcerts(int $user_id): array
    {
        $pdo = $this->database->connect();
        $pdo->beginTransaction();

        try {
            $result = [];

            $stmt = $pdo->prepare('
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
                // Fetch artist
                $stmtConcertArtist = $pdo->prepare('
                    SELECT artist_id FROM concert_artist WHERE concert_id = :concert_id
                ');
                $stmtConcertArtist->bindParam(':concert_id', $concert['concert_id'], PDO::PARAM_INT);
                $stmtConcertArtist->execute();
                $artistId = $stmtConcertArtist->fetchColumn();

                $stmtArtist = $pdo->prepare('
                    SELECT name FROM artists WHERE artist_id = :artist_id
                ');
                $stmtArtist->bindParam(':artist_id', $artistId, PDO::PARAM_INT);
                $stmtArtist->execute();
                $artist = $stmtArtist->fetchColumn() ?: 'Unknown Artist';

                // Fetch genre
                $stmtGenre = $pdo->prepare('
                    SELECT name FROM concert_genre WHERE genre_id = :genre_id
                ');
                $stmtGenre->bindParam(':genre_id', $concert['genre_id'], PDO::PARAM_STR);
                $stmtGenre->execute();
                $genre = $stmtGenre->fetchColumn() ?: 'Unknown Genre';

                // Fetch venue
                $stmtVenue = $pdo->prepare('
                    SELECT name FROM venues WHERE venue_id = :venue_id
                ');
                $stmtVenue->bindParam(':venue_id', $concert['venue_id'], PDO::PARAM_INT);
                $stmtVenue->execute();
                $venue = $stmtVenue->fetchColumn() ?: 'Unknown Venue';

                // Fetch location
                $stmtLocation = $pdo->prepare('
                    SELECT name FROM locations WHERE location_id = :location_id
                ');
                $stmtLocation->bindParam(':location_id', $concert['location_id'], PDO::PARAM_INT);
                $stmtLocation->execute();
                $location = $stmtLocation->fetchColumn() ?: 'Unknown Location';

                // Fetch images
                $stmtImages = $pdo->prepare('
                    SELECT picture_path FROM concert_picture WHERE concert_id = :concert_id
                ');
                $stmtImages->bindParam(':concert_id', $concert['concert_id'], PDO::PARAM_INT);
                $stmtImages->execute();
                $images = $stmtImages->fetchAll(PDO::FETCH_COLUMN) ?: ['default_concert.jpg'];

                // Fetch username
                $stmtUsername = $pdo->prepare('
                    SELECT username FROM users WHERE user_id = :user_id
                ');
                $stmtUsername->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmtUsername->execute();
                $username = $stmtUsername->fetchColumn() ?: '';

                $result[] = new Concert(
                    $artist,
                    $concert['date'],
                    $concert['title'],
                    $genre,
                    $venue,
                    $location,
                    $images,
                    $username
                );
            }

            $pdo->commit();
            return $result;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function getConcertByTitle(string $searchString): array
    {
        $pdo = $this->database->connect(); // Single PDO instance
        $pdo->beginTransaction();

        try {
            $searchString = '%' . strtolower($searchString) . '%';

            $stmt = $pdo->prepare('
                SELECT c.*, u.username
                FROM concerts c
                LEFT JOIN concert_user cu ON c.concert_id = cu.concert_id
                LEFT JOIN users u ON cu.user_id = u.user_id
                WHERE LOWER(c.title) LIKE :search
            ');
            $stmt->bindParam(':search', $searchString, PDO::PARAM_STR);
            $stmt->execute();

            $concerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [];
            foreach ($concerts as $concert) {
                // Fetch images
                $stmtImages = $pdo->prepare('
                    SELECT picture_path FROM concert_picture WHERE concert_id = :concert_id
                ');
                $stmtImages->bindParam(':concert_id', $concert['concert_id'], PDO::PARAM_INT);
                $stmtImages->execute();
                $images = $stmtImages->fetchAll(PDO::FETCH_COLUMN) ?: ['default_concert.jpg'];

                // Fetch artist
                $stmtConcertArtist = $pdo->prepare('
                    SELECT artist_id FROM concert_artist WHERE concert_id = :concert_id
                ');
                $stmtConcertArtist->bindParam(':concert_id', $concert['concert_id'], PDO::PARAM_INT);
                $stmtConcertArtist->execute();
                $artistId = $stmtConcertArtist->fetchColumn();

                $stmtArtist = $pdo->prepare('
                    SELECT name FROM artists WHERE artist_id = :artist_id
                ');
                $stmtArtist->bindParam(':artist_id', $artistId, PDO::PARAM_INT);
                $stmtArtist->execute();
                $artist = $stmtArtist->fetchColumn() ?: 'Unknown Artist';

                // Fetch genre
                $stmtGenre = $pdo->prepare('
                    SELECT name FROM concert_genre WHERE genre_id = :genre_id
                ');
                $stmtGenre->bindParam(':genre_id', $concert['genre_id'], PDO::PARAM_STR);
                $stmtGenre->execute();
                $genre = $stmtGenre->fetchColumn() ?: 'Unknown Genre';

                // Fetch venue
                $stmtVenue = $pdo->prepare('
                    SELECT name FROM venues WHERE venue_id = :venue_id
                ');
                $stmtVenue->bindParam(':venue_id', $concert['venue_id'], PDO::PARAM_INT);
                $stmtVenue->execute();
                $venue = $stmtVenue->fetchColumn() ?: 'Unknown Venue';

                // Fetch location
                $stmtLocation = $pdo->prepare('
                    SELECT name FROM locations WHERE location_id = :location_id
                ');
                $stmtLocation->bindParam(':location_id', $concert['location_id'], PDO::PARAM_INT);
                $stmtLocation->execute();
                $location = $stmtLocation->fetchColumn() ?: 'Unknown Location';

                $result[] = [
                    'title'    => $concert['title'],
                    'date'     => $concert['date'],
                    'images'   => $images,
                    'addedBy'  => $concert['username'] ?? 'unknown',
                    'genre'    => $genre,
                    'venue'    => $venue,
                    'location' => $location
                ];
            }

            $pdo->commit();
            return $result;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
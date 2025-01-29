<?php

class Statistics {
    private $last_concert;
    private $concerts_attended;
    private $artists_seen;
    private $concerts_per_year;
    private $top_artists;
    private $top_genres;
    private $top_venues;
    
    public function __construct(
        string $last_concert,
        int $concerts_attended,
        int $artists_seen,
        array $concerts_per_year,
        array $top_artists,
        array $top_genres,
        array $top_venues
    ) {
        $this->last_concert = $last_concert;
        $this->concerts_attended = $concerts_attended;
        $this->artists_seen = $artists_seen;
        $this->concerts_per_year = $concerts_per_year;
        $this->top_artists = $top_artists;
        $this->top_genres = $top_genres;
        $this->top_venues = $top_venues;
    }

    public function getLastConcert(): string {
        return $this->last_concert;
    }
    public function setLastConcert(string $last_concert): void
    {
        $this->last_concert = $last_concert;
    }
    public function getConcertsAttended(): int {
        return $this->concerts_attended;
    }
    public function setConcertsAttended(int $concerts_attended): void
    {
        $this->concerts_attended = $concerts_attended;
    }
    public function getArtistsSeen(): int {
        return $this->artists_seen;
    }
    public function setArtistsSeen(int $artists_seen): void
    {
        $this->artists_seen = $artists_seen;
    }
    public function getConcertsPerYear(): array {
        return $this->concerts_per_year;
    }
    public function setConcertsPerYear(array $concerts_per_year): void
    {
        $this->concerts_per_year = $concerts_per_year;
    }
    public function getTopArtists(): array {
        return $this->top_artists;
    }
    public function setTopArtists(array $top_artists): void
    {
        $this->top_artists = $top_artists;
    }
    public function getTopGenres(): array {
        return $this->top_genres;
    }
    public function setTopGenres(array $top_genres): void
    {
        $this->top_genres = $top_genres;
    }
    public function getTopVenues(): array {
        return $this->top_venues;
    }
    public function setTopVenues(array $top_venues): void
    {
        $this->top_venues = $top_venues;
    }
}
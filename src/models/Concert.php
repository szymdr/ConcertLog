<?php

class Concert
{
    private $artist;
    private $date;
    private $title;
    private $genre;
    private $venue;
    private $location;
    private $images;

    public function __construct(
        string $artist,
        string $date,
        string $title,
        string $genre,
        string $venue,
        string $location,
        array $images
    ) {
        $this->artist = $artist;
        $this->date = $date;
        $this->title = $title;
        $this->genre = $genre;
        $this->venue = $venue;
        $this->location = $location;
        $this->images = $images;
    }
    public function getArtist(): string
    {   
        return $this->artist;
    }
    public function setArtist(string $artist): void
    {
        $this->artist = $artist;
    }
    public function getDate(): string
    {
        return $this->date;
    }
    public function setDate(string $date): void
    {
        $this->date = $date;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
    public function getGenre(): string
    {
        return $this->genre;
    }
    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }
    public function getVenue(): string
    {
        return $this->venue;
    }
    public function setVenue(string $venue): void
    {
        $this->venue = $venue;
    }
    public function getLocation(): string
    {
        return $this->location;
    }
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }
    public function getImages(): array
    {
        return $this->images;
    }
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

}
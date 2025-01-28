<?php

require_once 'AppController.php';
require_once __DIR__.'/../models/Concert.php';
require_once __DIR__.'/../repository/ConcertRepository.php';

class ConcertController extends AppController {const MAX_FILE_SIZE = 1024*1024;
    const SUPPORTED_TYPES = ['image/png', 'image/jpeg'];
    const UPLOAD_DIRECTORY = '/../public/uploads/';

    private $message = [];
    private $concertRepository;

    public function __construct() {
        parent::__construct();
        $this->concertRepository = new ConcertRepository();
    }

    public function createConcert()
    {   
        if ($this->isPost() && is_uploaded_file($_FILES['images']['tmp_name']) && $this->validate($_FILES['images'])) {
            move_uploaded_file(
                $_FILES['images']['tmp_name'], 
                dirname(__DIR__).self::UPLOAD_DIRECTORY.$_FILES['images']['name']
            );

            $concert = new Concert($_POST['artist'], $_POST['date'], $_POST['title'], $_POST['venue'], $_POST['location'], $_FILES['images']['name']);

            $this->concertRepository->addConcert($concert);

            return $this->render('feed', ['messages' => $this->message, 'concerts' => $this->concertRepository->getConcerts()]);
        }
        return $this->render('add-project', ['messages' => $this->message]);
    }

    private function validate(array $file): bool
    {
        if ($file['size'] > self::MAX_FILE_SIZE) {
            $this->message[] = 'File is too large for destination file system.';
            return false;
        }

        if (!isset($file['type']) || !in_array($file['type'], self::SUPPORTED_TYPES)) {
            $this->message[] = 'File type is not supported.';
            return false;
        }
        return true;
    }
}

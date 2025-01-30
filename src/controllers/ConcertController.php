<?php

require_once 'AppController.php';
require_once __DIR__.'/../models/Concert.php';
require_once __DIR__.'/../repository/ConcertRepository.php';

class ConcertController extends AppController {
    const MAX_FILE_SIZE = 1024*1024;
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
        if ($this->isPost() && isset($_FILES['images']) && $this->validateFiles($_FILES['images'])) {
            $uploadedFiles = $_FILES['images'];
            $imagePaths = [];
    
            for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
                if($uploadedFiles['size'][$i] == 0){
                    break;
                }
                if (is_uploaded_file($uploadedFiles['tmp_name'][$i])) {
                    $filename = uniqid('ConcertPhoto', true) . '.' . pathinfo($uploadedFiles['name'][$i], PATHINFO_EXTENSION);
                    $destination = dirname(__DIR__) . self::UPLOAD_DIRECTORY . $filename;
                    if (move_uploaded_file($uploadedFiles['tmp_name'][$i], $destination)) {
                        $imagePaths[] = $filename;
                    } else {
                        $this->message[] = 'Failed to move uploaded file.';
                    }
                } else {
                    $this->message[] = 'File not found: ' . $uploadedFiles['tmp_name'][$i];
                }
            }

            if(empty($imagePaths)){
                $imagePaths[] = 'default_concert.jpg';
            }
    
            if (empty($this->message)) {
                $concert = new Concert(
                    $_POST['artist'],
                    $_POST['date'],
                    $_POST['title'],
                    $_POST['genre'],
                    $_POST['venue'],
                    $_POST['location'],
                    $imagePaths,
                    $_SESSION['user_id'],
                );
    
                $this->concertRepository->addConcert($concert);
                
                header("Location: /feed");
                exit();
            }
        }
        return $this->render('addconcert', ['messages' => $this->message]);
    }
    
    private function validateFiles(array $files): bool
    {
        if ($files['size'][0] == 0){
            return true;
        }
        foreach ($files['size'] as $size) {
            if ($size > self::MAX_FILE_SIZE) {
                $this->message[] = 'One or more files are too large for destination file system.';
                return false;
            }
        }
    
        foreach ($files['type'] as $type) {
            if (!in_array($type, self::SUPPORTED_TYPES)) {
                $this->message[] = 'One or more file types are not supported.';
                return false;
            }
        }
    
        return true;
    }

    public function search(): void
    {
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);

            header('Content-type: application/json');
            http_response_code(200);

            echo json_encode($this->concertRepository->getConcertByTitle($decoded['search']));
        }
    }
    
}

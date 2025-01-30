<?php

require_once 'AppController.php';
require_once __DIR__.'/../models/Concert.php';
require_once __DIR__.'/../repository/ConcertRepository.php';

class ConcertController extends AppController {
    const MAX_FILE_SIZE = 1024*1024; // 1MB
    const SUPPORTED_TYPES = ['image/png', 'image/jpeg', 'image/jpg'];
    const UPLOAD_DIRECTORY = '/../public/uploads/concertPhotos/';

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

            // Ensure 'name', 'size', and 'tmp_name' are arrays
            $fileNames = is_array($uploadedFiles['name']) ? $uploadedFiles['name'] : [$uploadedFiles['name']];
            $fileSizes = is_array($uploadedFiles['size']) ? $uploadedFiles['size'] : [$uploadedFiles['size']];
            $fileTmpNames = is_array($uploadedFiles['tmp_name']) ? $uploadedFiles['tmp_name'] : [$uploadedFiles['tmp_name']];

            for ($i = 0; $i < count($fileNames); $i++) {
                if ($fileSizes[$i] == 0) {
                    continue;
                }
                if (is_uploaded_file($fileTmpNames[$i])) {
                    $filename = uniqid('', true) . '.' . pathinfo($fileNames[$i], PATHINFO_EXTENSION);
                    $destination = dirname(__DIR__) . self::UPLOAD_DIRECTORY . $filename;
                    
                    if (move_uploaded_file($fileTmpNames[$i], $destination)) {
                        $imagePaths[] = $filename;
                    } else {
                        $this->message[] = 'Failed to move uploaded file: ' . htmlspecialchars($fileNames[$i]);
                    }
                } else {
                    $this->message[] = 'File not found or upload error: ' . htmlspecialchars($fileNames[$i]);
                }
            }

            // If no images were uploaded, assign the default image
            if (empty($imagePaths)) {
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
                    $_SESSION['user_id']
                );

                try {
                    $this->concertRepository->addConcert($concert);
                    header("Location: /feed");
                    exit();
                } catch (PDOException $e) {
                    // Log the error or handle it as needed
                    $this->message[] = 'Failed to add concert: ' . $e->getMessage();
                }
            }
        }
        return $this->render('addconcert', ['messages' => $this->message]);
    }

    private function validateFiles(array $files): bool
    {
        if ($files['size'][0] == 0){
            return true; // Allow empty file uploads to assign default image
        }
        foreach ($files['size'] as $size) {
            if ($size > self::MAX_FILE_SIZE) {
                $this->message[] = 'One or more files exceed the maximum allowed size of 1MB.';
                return false;
            }
        }

        foreach ($files['type'] as $type) {
            if (!in_array($type, self::SUPPORTED_TYPES)) {
                $this->message[] = 'One or more file types are not supported. Only PNG and JPEG are allowed.';
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

            echo json_encode($this->concertRepository->getConcertByTitleOrArtist($decoded['search']));
        }
    }
}
?>
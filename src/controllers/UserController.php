<?php

require_once 'AppController.php';
require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/StatisticsRepository.php';
require_once __DIR__.'/../repository/ConcertRepository.php';

class UserController extends AppController {
    const MAX_FILE_SIZE = 1024*1024;
    const SUPPORTED_TYPES = ['image/png', 'image/jpeg'];
    const UPLOAD_DIRECTORY = '/../public/uploads/profilePictures/';

    private $message = [];
    private $userRepository;
    private $statisticsRepository;
    private $concertRepository;

    public function __construct() {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->statisticsRepository = new StatisticsRepository();
        $this->concertRepository = new ConcertRepository();
    }


    public function saveProfileChanges(): void       
    {
        if ($this->isPost()) {
            if (!empty($_POST['username'])) {
                $this->userRepository->setUsername($_SESSION['email'], $_POST['username']);
                $_SESSION['username'] = $_POST['username'];
            }
            if(
            isset($_FILES['profile-picture']) &&
            is_uploaded_file($_FILES['profile-picture']['tmp_name']) &&
            $this->validate($_FILES['profile-picture']))
            {
                $fileName = uniqid('', true) . '.' . pathinfo($_FILES['profile-picture']['name'], PATHINFO_EXTENSION);
                move_uploaded_file(
                    $_FILES['profile-picture']['tmp_name'],
                    dirname(__DIR__) . self::UPLOAD_DIRECTORY . $fileName
                );
        
                $_SESSION['profile_picture'] = $fileName;
                $this->userRepository->setProfilePicture($_SESSION['email'], $fileName);
            }
            $concerts = $this->concertRepository->getUserConcerts($_SESSION['user_id']);
            
        }
        $this->render('profile', ['statistics'=> $this->statisticsRepository->getUserStatistics($_SESSION['user_id']), 'concerts' => $concerts]);
    }

    public function removeUser(): void
    {
        if (!$this->isPost()) {
            return;
        }
        $email = $_POST['email'];
        $this->userRepository->removeUser($email);
        $this->render('adminpage', ['users' => $this->userRepository->getAllUsers()]);
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

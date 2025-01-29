<?php

require_once 'AppController.php';
require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/StatisticsRepository.php';

class UserController extends AppController {

    private $message = [];
    private $userRepository;
    private $statisticsRepository;

    public function __construct() {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->statisticsRepository = new StatisticsRepository();
    }


    public function saveProfileChanges() {
        if (!$this->isPost()) {
            return $this->render('profile', ['statistics'=> $statisticsRepository->getStatistics()]);
        }

        $_POST['profile-picture'] = $_POST['profile-picture'] ?? 'default_profile_picture.png';

    }
    
}

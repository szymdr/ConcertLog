<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/ConcertRepository.php';
require_once __DIR__.'/../repository/StatisticsRepository.php';

class DefaultController extends AppController {

    public function index()
    {
        $this->render('login', ['email' => ['']]);
    }
    public function feed()
    {
        $concertRepository = new ConcertRepository();
        $concerts = $concertRepository->getConcerts();
        $this->render('feed', ['concerts' => $concerts]);  
    }
    public function changepassword()
    {
        $this->render('changepassword');
    }
    public function friends()
    {
        $this->render('friends');
    }
    public function addconcert()
    {
        $this->render('addconcert');
    }
    public function profile()
    {
        $userRepository = new UserRepository();
        $user = $userRepository->getUser('admin@admin.com');

        $statisticsRepository = new StatisticsRepository();
        $statistics = $statisticsRepository->getStatistics();
        $this->render('profile', ['statistics'=> $statistics, 'user' => $user]);
    }
    public function settings()
    {
        $this->render('settings');
    }
}
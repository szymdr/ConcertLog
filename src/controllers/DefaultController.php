<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/ConcertRepository.php';
require_once __DIR__.'/../repository/StatisticsRepository.php';

class DefaultController extends AppController {

    public function index()
    {
        $concertRepository = new ConcertRepository();
        $concerts = $concertRepository->getConcerts();
        $this->render('feed', ['concerts' => $concerts]);  
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
    public function addconcert()
    {
        $this->render('addconcert');
    }
    public function profile()
    {
        $statisticsRepository = new StatisticsRepository();
        $statistics = $statisticsRepository->getUserStatistics($_SESSION['user_id']);

        $concertRepository = new ConcertRepository();
        $concerts = $concertRepository->getUserConcerts($_SESSION['user_id']);

        $this->render('profile', ['statistics'=> $statistics, 'concerts' => $concerts]);
    }
    public function settings()
    {
        $this->render('settings');
    }
}
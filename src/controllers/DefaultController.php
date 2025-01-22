<?php

require_once 'AppController.php';

class DefaultController extends AppController {

    public function index()
    {
        $this->render('login');
    }
    public function feed()
    {
        $this->render('feed');
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
        $this->render('profile');
    }
    public function settings()
    {
        $this->render('settings');
    }
}
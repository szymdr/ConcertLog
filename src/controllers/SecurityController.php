<?php

require_once 'AppController.php';
require_once __DIR__ .'/../models/User.php';

class SecurityController extends AppController {

    public function login()
    {   

        $user = new User('admin', 'admin', 'John', 'Doe');

        if (!$this->isPost()) {
            return $this->render('login');
        }

        $email = $_POST['email'];


        if (isset($_POST['sign-in'])) {

            $password = $_POST['password'];
    
            if ($user->getEmail() !== $email) {
                //return $this->render('login', ['messages' => ['User with this email does not exist!']]);
            }
    
            if ($user->getPassword() !== $password) {
                //return $this->render('login', ['messages' => ['Wrong password!']]);
            }
    
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/feed");
          } else {
            return $this->render('signup', ['email' => [$email]]);
          }
    }
    public function signup()
    {
        $this->render('signup');
    }
    public function logout()
    {
        $this->render('login');
    }
}
<?php

require_once 'AppController.php';
require_once __DIR__ .'/../models/User.php';
require_once __DIR__ .'/../repository/UserRepository.php';

class SecurityController extends AppController {
    private $userRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
    }

    public function login()
    {   


        if (!$this->isPost()) {
            return $this->render('login', ['email' => ['']]);
        }

        $email = $_POST['email'];

        if (isset($_POST['sign-in'])) {
    
            $user = $this->userRepository->getUser($email);
            // $user = new User('admin', 'admin@admin.com', 'admin');

            if (!$user) {
                return $this->render('login', ['messages' => ['User not found!'], 'email' => [$email]]);
            }

            $password = md5($_POST['password']);
    
            if ($user->getEmail() !== $email) {
                return $this->render('login', ['messages' => ['User with this email does not exist!'], 'email' => [$email]]);
            }
    
            if ($user->getPassword() !== $password) {
                return $this->render('login', ['messages' => ['Wrong password!'], 'email' => [$email]]);
            }
    
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/feed");
          } else {
            return $this->render('signup', ['email' => [$email]]);
          }
    }   
    public function signup()
    {
        if (!$this->isPost()) {
            return $this->render('signup', ['email' => ['']]);
        }

        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmedPassword = $_POST['repeat-password'];

        if ($password !== $confirmedPassword) {
            return $this->render('register', ['messages' => ['Please provide proper password'], 'email' => [$email]]);
        }

        //TODO try to use better hash function
        $user = new User($username, $email, md5($password));

        $this->userRepository->addUser($user);

        return $this->render('login', ['messages' => ['You\'ve been succesfully registrated!'], 'email'=> ['']]);
    }
    public function logout()
    {
        $this->render('login', ['email' => ['']]);
    }
}
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

            if (!$user) {
                return $this->render('login', ['messages' => ['User not found!'], 'email' => [$email]]);
            }

            $password = md5($_POST['password']);
    
            if ($user->getEmail() !== $email) {
                return $this->render('login', ['messages' => ['User with this email does not exist!'], 'email' => [$email]]);
            }
    
            if ($user->getPassword() !== $password) {
                return $this->render('login', [
                    'messages' => ['Incorrect password. Try again.'],
                    'email' => [$email]
                ]);
            } else {
                session_start();
                $_SESSION['userType'] = $this->userRepository->getRole($email);
                $_SESSION['user_id'] = $this->userRepository->getUserID($email);
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['profile_picture'] = $user->getProfilePicture();
                $url = "http://$_SERVER[HTTP_HOST]";

                    header("Location: {$url}/feed");
                    exit();
            }
        } else if (isset($_POST['sign-up'])) {
            return $this->render('signup', ['email' => [$email]]);
        } else {
            return $this->render('login', ['email' => [$email]]);
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
        session_unset();
        session_destroy();
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/login");
        exit();
    }
}
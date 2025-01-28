<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/User.php';

class UserRepository extends Repository
{

    public function getUser(string $email): ?User
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users WHERE email = :email
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user == false) {
            return null;
        }

        return new User(
            $user['username'],  
            $user['email'],
            $user['password_hash']
        );
    }

    public function addUser(User $user)
    {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO users_details (profile_picture, bio)
            VALUES (?, ?)
        ');

        $stmt->execute([
            null,
            null
        ]);

        $stmt = $this->database->connect()->prepare('
            INSERT INTO users (username, email, password_hash, user_details_id) 
            VALUES (?, ?, ?, ?)
        ');

        $stmt->execute([
            $user->getUsername(),
            $user->getEmail(),
            $user->getPassword(),
            $this->getUserDetailsId($user)
        ]);
    }

    public function getUserDetailsId(User $user): int
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM public.users_details WHERE name = :name AND surname = :surname AND phone = :phone
        ');
        $stmt->bindParam(':name', $user->getName(), PDO::PARAM_STR);
        $stmt->bindParam(':surname', $user->getSurname(), PDO::PARAM_STR);
        $stmt->bindParam(':phone', $user->getPhone(), PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data['id'];
    }
}
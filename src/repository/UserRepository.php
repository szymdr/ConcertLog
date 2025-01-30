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

        $stmtProfilePicture = $this->database->connect()->prepare('
            SELECT profile_picture FROM user_details WHERE user_details_id = :user_details_id
        ');
        $stmtProfilePicture->bindParam(':user_details_id', $user['user_details_id'], PDO::PARAM_INT);
        $stmtProfilePicture->execute();
        $profile_picture = $stmtProfilePicture->fetch(PDO::FETCH_ASSOC);


        $return_user =  new User(
            $user['username'],  
            $user['email'],
            $user['password_hash']
        );
        $return_user->setProfilePicture($profile_picture['profile_picture']);
        return $return_user;
    }

    public function addUser(User $user)
    {
        $pdo = $this->database->connect();
        $pdo->beginTransaction();

        try {
            $defaultProfilePicture = 'default_profile_picture.png';

            $stmt = $pdo->prepare('
                INSERT INTO user_details (profile_picture, bio)
                VALUES (?, ?) RETURNING user_details_id
            ');

            $stmt->execute([
                $defaultProfilePicture,
                null
            ]);

            $userDetailsId = $stmt->fetchColumn();

            $stmt = $pdo->prepare('
                INSERT INTO users (username, email, password_hash, user_details_id) 
                VALUES (?, ?, ?, ?)
            ');

            $stmt->execute([
                $user->getUsername(),
                $user->getEmail(),
                $user->getPassword(),
                $userDetailsId
            ]);

            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function setUsername(string $email, string $username)
    {
        $pdo = $this->database->connect();
        $pdo->beginTransaction();

        try {
            // Aktualizacja nazwy użytkownika w tabeli users
            $stmt = $pdo->prepare('
                UPDATE users SET username = :username
                WHERE email = :email
            ');
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            // Aktualizacja kolumny updated_at w tabeli users
            $this->updateTimestamp($pdo, $email);

            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function setProfilePicture(string $email, string $profilePicture)
    {
        $pdo = $this->database->connect();
        $pdo->beginTransaction();

        try {
            // Aktualizacja zdjęcia profilowego w tabeli user_details
            $stmt = $pdo->prepare('
                UPDATE user_details SET profile_picture = :profile_picture
                WHERE user_details_id = (SELECT user_details_id FROM users WHERE email = :email)
            ');
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':profile_picture', $profilePicture, PDO::PARAM_STR);
            $stmt->execute();

            // Aktualizacja kolumny updated_at w tabeli users
            $this->updateTimestamp($pdo, $email);

            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function removeUser(User $user)
    {
        $pdo = $this->database->connect();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('
                DELETE FROM users WHERE email = :email
            ');
            $email = $user->getEmail();
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function getUserID(string $email): int
    {
        $stmt = $this->database->connect()->prepare('
            SELECT user_id FROM users WHERE email = :email
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    private function updateTimestamp(PDO $pdo, string $email)
    {
        $stmt = $pdo->prepare('
            UPDATE users SET updated_at = NOW()
            WHERE email = :email
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
    }
}
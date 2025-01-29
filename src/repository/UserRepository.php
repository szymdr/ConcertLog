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
        $defaultProfilePicture = 'default_profile_picture.png';

        $stmt = $this->database->connect()->prepare('
            INSERT INTO user_details (profile_picture, bio)
            VALUES (?, ?) RETURNING user_details_id
        ');

        $stmt->execute([
            $defaultProfilePicture,
            null
        ]);

        $userDetailsId = $stmt->fetchColumn();

        $stmt = $this->database->connect()->prepare('
            INSERT INTO users (username, email, password_hash, user_details_id) 
            VALUES (?, ?, ?, ?)
        ');

        $stmt->execute([
            $user->getUsername(),
            $user->getEmail(),
            $user->getPassword(),
            $userDetailsId
        ]);
    }

    public function setProfilePicture(string $email, string $profilePicture)
    {
        $stmt = $this->database->connect()->prepare('
            UPDATE user_details SET profile_picture = :profile_picture
            WHERE user_details_id = (SELECT user_details_id FROM users WHERE email = :email)
        ');

        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':profile_picture', $profilePicture, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function saveProfileChanges(User $user) {
        ;
    }

    public function removeUser(User $user)
    {
        $stmt = $this->database->connect()->prepare('
            DELETE FROM users WHERE email = :email
        ');
        $email = $user->getEmail();
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
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
}
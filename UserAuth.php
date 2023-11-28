<?php
require_once 'DBconfigs.php';
require_once 'configs.php';


class UserAuth implements StaticMessages
{
    use Database;
    use DataValidation;

    public function signup($name, $email, $password)
    {
        try {
            // filter and validate data
            $name = $this->filterData($name);
            $email = $this->filteredEmail($email);
            $password = password_hash($password, PASSWORD_BCRYPT);
            // check if email is valid or not
            if ($this->isValidEmail($email)) {
                $connection = $this->dbConnection();
                $statment = $connection->prepare('INSERT INTO `users`(`name`, `email`, `PASSWORD`) VALUES (:name,:email,:password)');
                $statment->bindParam(':name', $name);
                $statment->bindParam(':email', $email);
                $statment->bindParam(':password', $password);
                // handle error if email exists in database table
                try {
                    $statment->execute();
                } catch (PDOException $error) {
                    // return meassge that email exists
                    return UserAuth::EMAIL_EXISTS;
                }
                return UserAuth::SIGNUP_DONE;
            } else {
                return UserAuth::NOT_VALID_EMAIL;
            }
        } catch (PDOException $error) {
            return UserAuth::DATABASE_CONNECTION_ERROR . $error->getMessage();
        }
    }
    public function login($email, $password)
    {
        try {
            // filter email and hashing password
            $email = $this->filteredEmail($email);
            // check if email is valid or not
            if ($this->isValidEmail($email)) {
                // SQL statments
                $connection = $this->dbConnection();
                $statment  = $connection->prepare('SELECT * FROM users WHERE email = :email');
                // binding values
                $statment->bindParam(':email', $email);
                $statment->execute();
                // fetching data
                $user = $statment->fetchAll(pdo::FETCH_ASSOC);
                $hashed_password = $user[0]['PASSWORD'];

                if ($this->isValidPassword($password, $hashed_password)) {
                    session_regenerate_id();
                    $_SESSION['user'] = $user;
                    return UserAuth::LOGIN_SUCCESS;
                } else {
                    return UserAuth::LOGIN_FAILED;
                }
            } else {
                return UserAuth::NOT_VALID_EMAIL;
            }
        } catch (PDOException $error) {
            return UserAuth::DATABASE_CONNECTION_ERROR . $error->getMessage();
        };
    }
}

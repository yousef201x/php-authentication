<?php

require_once 'UserAuth.php';

class UserSettings implements StaticMessages
{

    use Database;
    use DataValidation;

    private function setUserId()
    {
        $userInfo = $_SESSION['user']['0'];
        $userId = $userInfo['id'];
        return $userId;
    }

    private function getUserId()
    {
        return $this->setUserId();
    }

    // ------------------------------------ //
    //        Fetch and update user
    // ------------------------------------ //

    private function fetchUpdatedUser($connection)
    {
        $userId = $this->getUserId();
        try {
            // QUERY
            $fetchUpdatedUser = $connection->prepare("SELECT * FROM users WHERE id = :id");
            $fetchUpdatedUser->bindparam(":id", $userId);
            $fetchUpdatedUser->execute();

            // Fetching 
            $updateUSer = $fetchUpdatedUser->fetchAll(PDO::FETCH_ASSOC);

            // Updating session
            $_SESSION['user'] = $updateUSer;

            // Error handling

        } catch (PDOException $Exception) {
            return UserSettings::CONNECTION_ERROR;
        }
    }

    // ------------------------------------ //
    //          Update user name
    // ------------------------------------ //

    public function updateUserName($name)
    {
        $userId = $this->getUserId();

        // filter Data
        $name = $this->filterData($name);

        try {
            // QUERY
            $connection = $this->dbConnection();
            $statment = $connection->prepare("UPDATE users SET name = :name WHERE id = :id ");
            $statment->bindParam(":name", $name);
            $statment->bindParam(":id", $userId);
            $statment->execute();

            // Update User session
            $this->fetchUpdatedUser($connection);

            // Static Message return 
            return UserSettings::NAME_CHANGED;

            // Error handling

        } catch (PDOException $Exception) {
            return UserSettings::CONNECTION_ERROR;
        }
    }

    // ------------------------------------ //
    //         Update user email   
    // ------------------------------------ //

    public function updateUserEmail($email)
    {
        $userId = $this->getUserId();

        // filter Data
        $email = $this->filteredEmail($email);

        // check if email is valid or not
        if ($this->isValidEmail($email)) {

            try {
                // Query
                $connection = $this->dbConnection();
                $statment = $connection->prepare("UPDATE users SET email = :email WHERE id = :id ");
                $statment->bindParam(":email", $email);
                $statment->bindParam(":id", $userId);

                // Expected error is dublication in email column 
                try {
                    $statment->execute();
                } catch (PDOException $Exception) {
                    return UserSettings::EMAIL_EXISTS;
                }

                // Update User
                $this->fetchUpdatedUser($connection);
                return UserSettings::EMAIL_CHANGED;
            } catch (PDOException $Exception) {
                return UserSettings::CONNECTION_ERROR;
            }
        } else {
            return UserSettings::NOT_VALID_EMAIL;
        }
    }

    // ------------------------------------ //
    //        Update user Password  
    // ------------------------------------ //

    public function updateUserPassword($password)
    {
        $userId = $this->getUserId();

        // filter Data
        $password = password_hash($password, PASSWORD_BCRYPT);

        try {
            // QUERY
            $connection = $this->dbConnection();
            $statment = $connection->prepare("UPDATE users SET PASSWORD = :password WHERE id = :id ");
            $statment->bindParam(":password", $password);
            $statment->bindParam(":id", $userId);
            $statment->execute();

            // Update User session
            $this->fetchUpdatedUser($connection);

            // Static Message return 
            return UserSettings::PASSWORD_CHANGED;

            // Error handling

        } catch (PDOException $Exception) {
            return UserSettings::CONNECTION_ERROR;
        }
    }

    public function deleteUser()
    {
        $userId = $this->getUserId();

        try {
            // QUERY
            $connection = $this->dbConnection();
            $statment = $connection->prepare("DELETE FROM users WHERE id = :id ");
            $statment->bindParam(":id", $userId);
            $statment->execute();

            // Error handling

        } catch (PDOException $Exception) {
            return UserSettings::CONNECTION_ERROR;
        }
    }
}

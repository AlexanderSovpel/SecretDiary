<?php

/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 22.04.15
 * Time: 06:58
 */
session_start();
error_reporting(E_ALL);

include("entity/User.php");

class AppController
{
    private $dbConnection;
    const connect_error_message = 'Не удалось соединиться: ';
    private $error = "";

    private $user;

    function __construct()
    {
        $this->dbConnection = new mysqli('mysql.hostinger.ru', 'u392919416_root', 'AlexIco!4)7', 'u392919416_diary');
        if ($this->dbConnection->connect_error) {
            die($this->connect_error_message . $this->dbConnection->connect_error);
        }
    }

    public function register($name, $email, $password, $passwordRepeat)
    {
        $name = $this->dbConnection->real_escape_string($name);
        $email = $this->dbConnection->real_escape_string($email);
        $password = $this->dbConnection->real_escape_string($password);
        if ($this->validateName($name)) {
            if ($this->validateEmail($email)) {
                if (!$this->isUserExists($email)) {
                    if ($this->validatePassword($password)) {
                        if ($passwordRepeat == $password) {

                            $passwordHash = md5($password);
                            $query = "INSERT INTO users (email, password_hash, name) VALUES ('$email',
                                      '$passwordHash', '$name')";
                            $result = $this->dbConnection->query($query);
                            if ($result) {
                                $userId = $this->selectUserId($email, $passwordHash);

                                if ($userId) {
                                    $this->user = new User($userId['id'], $name, $email, $passwordHash);

                                    $this->setSessionInfo($userId, $name);

                                    header("Location:../diary.html");
                                }
                            }
                        } else $this->error = "Введённые пароли не совпадают";
                    } else $this->error = "Введите пароль";
                } else $this->error = "Пользователь с данным e-mail уже зарегистрирован в системе";
            } else $this->error = "Введите e-mail";
        } else $this->error = "Введите имя";

        echo $this->error;
    }

    public function login($email, $password, $rememberMe)
    {
        $email = $this->dbConnection->real_escape_string($email); /* SQL-injection  prevent */
        $password = $this->dbConnection->real_escape_string($password);
        if ($this->validateEmail($email)) {
            if ($this->validatePassword($password)) {
                $passwordHash = md5($password);

                $userId = $this->selectUserId($email, $passwordHash);
                if ($userId) {
                    if ($rememberMe == 'checked') {
                        if ($_COOKIE) {
                            setcookie("email", $email);
                            setcookie("password_hash", $passwordHash);
                        }
                    }

                    $userInfo = $this->selectUserInfo($userId);

                    $this->setSessionInfo($userId, $userInfo['name']);

                    $this->user = new User($userId, $userInfo['name'], $email, $passwordHash);
                    $this->user->setNotificationTime($userInfo['notification_time']);
                    $this->user->setNotificationDay($userInfo['notification_day']);

                    $this->loadNotes();

                    $_SESSION['user'] = serialize($this->user);

                    header("Location:../diary.html");
                } else $this->error = "Неверное имя пользователя или пароль";
            } else $this->error = "Введите пароль";
        } else $this->error = "Введите e-mail";
        echo $this->error;
    }

    public function logout()
    {
        setcookie("email", "");
        setcookie("password_hash", "");

        unset($this->user);
    }

    public function autoLogin()
    {
        if ($_COOKIE['email'] != "") {
            echo $_COOKIE['email'];
            if ($_COOKIE['password_hash'] != "") {
                echo $_COOKIE['password_hash'];
                $email = $_COOKIE['email'];
                $passwordHash = $_COOKIE['password_hash'];

                $userId = $this->selectUserId($email, $passwordHash);
                if ($userId) {
                    $userInfo = $this->selectUserInfo($userId);

                    $_SESSION['user_id'] = $userId;
                    $_SESSION['name'] = $userInfo['name'];
                    echo $userInfo['name'];

                    $this->user = new User($userId['id'], $userInfo['name'], $email, $passwordHash);
                    $this->user->setNotificationTime($userInfo['notification_time']);
                    $this->user->setNotificationDay($userInfo['notification_day']);

                    $query = "SELECT * FROM notes WHERE user_id = '" . $userId . "'";
                    $result = $this->dbConnection->query($query);
                    while ($userNotes = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $note = new Note($userNotes['note_id'], $userNotes['title'], $userNotes['text'],
                            $userNotes['date']);
                        $this->user->addNote($note);
                    }

                    header("Location:../diary.html");
                }

                $this->login($email, $passwordHash, FALSE);
            }
        }
    }

    public function loadNotes()
    {
        $query = "SELECT * FROM notes WHERE user_id = '" . $this->user->getId() . "' ORDER BY date DESC";
        $result = $this->dbConnection->query($query);
        while ($notes = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $note = new Note($notes['note_id'], $notes['title'], $notes['text'], $notes['date']);
            $this->user->addNote($note);
        }
    }

    public function loadNotesToPage()
    {
        $this->user = unserialize($_SESSION['user']);
        $notes_list = "";
        $notes = $this->user->getNotes();
        foreach ($notes as $note) {
            $notes_list .= "<div class='note' onclick='editNote()'>
            <input type='hidden' name='note_id' value='" . $note->getId() . "'/>
            <div class='date'>" . $note->getDate() . "</div>
            <div class='title'>" . $note->getTitle() . "</div>
            <div class='text'>" . $note->getText() . "</div>
        </div>
        <hr>";
        }

        echo $notes_list;
    }


    public function saveNote($id, $title, $text)
    {
        $this->user = unserialize($_SESSION['user']);
        $date = date("Y-m-d H:i:s");
        if ($title != "") {
            if (!$this->isNoteExists($id)) {
                $query = "INSERT INTO notes (user_id, title, text, date)
                    VALUES ('" . $this->user->getId() . "', '$title', '$text', '$date')";
                $result = $this->dbConnection->query($query);
                if ($result) {
                    $query = "SELECT note_id FROM notes WHERE title='$title'";
                    $result = $this->dbConnection->query($query)->fetch_assoc();

                    $note = new Note($result['note_id'], $title, $text, $date);
                    $this->user->addNote($note);
                    echo $result['note_id'];
                }
            } else {
                $query = "UPDATE notes SET title='" . $title . "', text='" . $text . "', date='" . $date . "'
                WHERE note_id='" . $id . "'";
                $result = $this->dbConnection->query($query);
                if ($result) {
                    $note = $this->user->replaceNote($id, $title, $text, $date);
                }
            }
        }

        $_SESSION['user'] = serialize($this->user);
    }

    public function deleteNote($id)
    {
        $this->user = unserialize($_SESSION['user']);

        $query = "DELETE FROM notes WHERE note_id='" . $id . "'";
        $this->dbConnection->query($query);
        $this->user->deleteNote($id);

        $_SESSION['user'] = serialize($this->user);
    }

    public function restorePasswordStep1($email)
    {
        $passwordRestoration = new PasswordRestoration($email);
    }

    public function restorePasswordStep2($restoreCode)
    {
        if ($restoreCode == $_SESSION['restore_code']) {
            echo TRUE;
        } else echo FALSE;
    }

    public function restorePasswordStep3($password, $passwordRepeat)
    {
        if ($this->validatePassword($password)) {
            if ($this->validatePassword($passwordRepeat)) {
                if ($password == $passwordRepeat) {
                    $query = "UPDATE users SET password_hash='" . md5($password) . "'
                    WHERE email='" . $_SESSION['restore_email'] . "'";
                    $result = $this->dbConnection->query($query);
                    if ($result) {
                        header("Location:../diary.html");
                    }
                }
            }
        }
    }

    public function getAccountInfo()
    {
        $this->user = unserialize($_SESSION['user']);
        $paramsStr = "email=" . $this->user->getEmail() . "&" .
            "name=" . $this->user->getName() . "&" .
            "notification_time=" . $this->user->getNotificationTime() . "&" .
            "notification_day=" . $this->user->getNotificationDay();
        return $paramsStr;
    }

    public function saveSettings($email, $name, $password, $passwordRepeat, $notificationTime, $notificationDay)
    {
        $this->user = unserialize($_SESSION['user']);
        $query = "UPDATE users SET ";
        $subqueryArr = [];
        $error = "";

        if ($email != $this->user->getEmail() && $email != "") {
            if ($this->validateEmail($email)) {
                if (!$this->isUserExists($email)) {
                    $subqueryArr[] = "email='" . $email . "'";
                    $this->user->setEmail($email);
                } else $error = "Пользователь с таким e-mail уже зарегистрирован в системе";
            } else $error = "Неверный формат e-mail адреса";
        }

        if ($name != $this->user->getName() && $name != "") {
            if ($this->validateName($name)) {
                $subqueryArr[] = "name='" . $name . "'";
                $_SESSION['name'] = $name;
                $this->user->setName($name);
            }
        }

        if ($notificationTime != $this->user->getNotificationTime()) {
            $subqueryArr[] = "notification_time='" . $notificationTime . "'";
            $this->user->setNotificationTime($notificationTime);
        }

        if ($notificationDay != $this->user->getNotificationDay()) {
            $subqueryArr[] = "notification_day='" . $notificationDay . "'";
        }

        if ($password != "") {
            if ($this->validatePassword($password)) {
                if ($passwordRepeat == $password) {
                    $passwordHash = md5($password);
                    $subqueryArr[] = "password_hash='" . $passwordHash . "'";
                    $this->user->setPasswordHash($passwordHash);
                } else $error = "Введённые пароли не совпадают";
            } else $error = "Пароль должен содержать не менее 6 символов";
        }

        $subqueryArr = implode(', ', $subqueryArr);

        $query .= $subqueryArr . " WHERE id='" . $this->user->getId() . "'";
        if ($error != "") {
            echo $error;
            exit();
        }

        $result = $this->dbConnection->query($query);
        if ($result) {
            $_SESSION['user'] = serialize($this->user);
            echo "OK";
        } else {
            echo $this->dbConnection->connect_error;
        }
    }

    private function isNoteExists($id)
    {
        if ($id != "") {
            $query = "SELECT user_id FROM notes WHERE note_id='" . $id . "'";
            $result = $this->dbConnection->query($query);
            if ($result != FALSE)
                return TRUE;
            else return FALSE;
        } else return FALSE;
    }

    public function validateName($name)
    {
        if ($name != "" && preg_match("`[A-Za-zА-Яа-я]`", $name))
            return TRUE;
        else return FALSE;
    }

    public function validateEmail($email)
    {
        if ($email != "" && filter_var($email, FILTER_VALIDATE_EMAIL))
            return TRUE;
        else return FALSE;
    }

    public function validatePassword($password)
    {
        if ($password != "" && strlen($password) >= 6)
            return TRUE;
        else return FALSE;
    }

    public function isUserExists($email)
    {
        $query = "SELECT id FROM users WHERE email = '" . $email . "'";
        $result = $this->dbConnection->query($query);
        $userExists = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($userExists['id'] == "")
            return FALSE;
        else return TRUE;
    }

    public function selectUserInfo($userId)
    {
        $query = "SELECT name, notification_day, notification_time FROM users WHERE id = '" . $userId . "'";
        $result = $this->dbConnection->query($query);
        $userInfo = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $userInfo;
    }

    private function selectUserId($email, $passwordHash)
    {
        $query = "SELECT id FROM users WHERE email = '" . $email . "' AND password_hash = '" . $passwordHash . "'";
        $result = $this->dbConnection->query($query);
        $userId = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $userId['id'];
    }

    private function setSessionInfo($userId, $name)
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['name'] = $name;
    }
}

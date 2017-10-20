<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 06.05.15
 * Time: 07:14
 */
include("../entity/User.php");

class NotesSender {
    private $dbConnection;
    const connect_error_message = 'Не удалось соединиться: ';

    private $users = [];

    function __construct()
    {
        $this->dbConnection = new mysqli('mysql.hostinger.ru', 'u392919416_root', 'AlexIco!4)7', 'u392919416_diary');
        if ($this->dbConnection->connect_error) {
            die($this->connect_error_message . $this->dbConnection->connect_error);
        }

        $this->loadUsers();
        $this->sendMail();
    }

    private function loadUsers() {
        $query = "SELECT * FROM users";
        $result = $this->dbConnection->query($query);
        while ($usersInfo = $result->fetch_assoc()) {
            $user = new User($usersInfo['id'], $usersInfo['name'], $usersInfo['email'], $usersInfo['password_hash']);
            $user->setNotificationTime($usersInfo['notification_time']);
            $user->setNotificationDay($usersInfo['notification_day']);

            $query = "SELECT * FROM notes WHERE user_id = '" . $user->getId() . "' ORDER BY date DESC";
            $result = $this->dbConnection->query($query);
            while ($notes = $result->fetch_assoc()) {
                $note = new Note($notes['note_id'], $notes['title'], $notes['text'], $notes['date']);
                $user->addNote($note);
            }

            $this->users[] = $user;
            echo $user;
        }
    }

    private function getRandomNote($user) {
        $noteIndex = rand(0, count($user->getNotes()) - 1);
        return $user->getNotes()[$noteIndex];
    }

    private function  sendMail() {
        $weekDay = date('N');
        $hour = date('G');

        foreach ($this->users as $user) {
            if (preg_match('`'.$weekDay.'`', $user->getNotificationDay())) {
                if ($user->getNotificationTime() == $hour) {
                    $randomNote = $this->getRandomNote($user);
                    $to = $user->getEmail();
                    $subject = "Вы помните \"" . $randomNote->getTitle() . "\"?";
                    $message =
                        "<html>
                        <head>
                            <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
                            <title>$subject</title>
                        </head>
                        <body>
                            <h1>$randomNote->getTitle()</h1>
                            <p>$randomNote->getText()</p>
                            <p align='right'>$randomNote->getDate()</p>
                        </body>
                        </html>";
                    $from = 'From: SDiary Support Team <contact@sdiary.pe.hu> \r\n';
                    $from .= 'MIME-Version: 1.0' . "\r\n";
                    $from .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                    echo mail($to, $subject, $message, $from);
                }
            }
        }
    }
}

$notesSender = new NotesSender();

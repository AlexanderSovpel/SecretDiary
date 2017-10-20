<?php

/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 22.04.15
 * Time: 05:37
 */
session_start();
include("Note.php");

class User
{
    private $id;
    private $name;
    private $email;
    private $password_hash;
    private $notification_time;
    private $notification_day;
    private $notes;

    function __construct($id, $name, $email, $password_hash)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->notification_time = "6";
        $this->notification_day = "1";
        $this->notes = array();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    /**
     * @param mixed $password_hash
     */
    public function setPasswordHash($password_hash)
    {
        $this->password_hash = $password_hash;
    }

    /**
     * @return mixed
     */
    public function getNotificationTime()
    {
        return $this->notification_time;
    }

    /**
     * @param mixed $notification_time
     */
    public function setNotificationTime($notification_time)
    {
        $this->notification_time = $notification_time;
    }

    /**
     * @return mixed
     */
    public function getNotificationDay()
    {
        return $this->notification_day;
    }

    /**
     * @param mixed $notification_day
     */
    public function setNotificationDay($notification_day)
    {
        $this->notification_day = $notification_day;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function addNote($note)
    {
        $this->notes[] = $note;
    }

    public function deleteNote($noteId)
    {
        $this->notes = array_values($this->notes);
        for ($i = 0; $i < count($this->notes); ++$i) {
            if ($this->notes[$i]->getId() == $noteId) {
                array_splice($this->notes, $i, 1);
            }
        }
        foreach ($this->notes as $note) {
                echo $note."\r\n";
        }
    }

    public function replaceNote($noteId, $newTitle, $newText, $newDate) {
        foreach ($this->notes as $note) {
            echo $note."\r\n";
            if ($note->getId() == $noteId) {
                $note->setText($newText);
                $note->setTitle($newTitle);
                $note->setDate($newDate);
                return;
            }
        }
    }

    public function __toString() {
        return "ID: ".$this->getId() . "\n" .
            "Name: ".$this->getName() . "\n" .
            "e-mail: ".$this->getEmail() . "\n" .
            "Password hash: ".$this->getPasswordHash() . "\n" .
            "Notification time: ".$this->getNotificationTime() . "\n" .
            "Notification days: ".$this->getNotificationDay() . "\n";
    }
}

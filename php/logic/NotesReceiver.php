<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 08.05.15
 * Time: 03:46
 */
include("../entity/Note.php");

class NotesReceiver {
    private $dbConnection;
    private $sDiaryInbox;
    const connect_error_message = 'Не удалось соединиться: ';

    function __construct()
    {
        $this->dbConnection = new mysqli('localhost', 'root', '', 'diary');
        if ($this->dbConnection->connect_error) {
            die($this->connect_error_message . $this->dbConnection->connect_error);
        }

        try {
            $this->sDiaryInbox = imap_open("{mx1.hostinger.ru:143/imap}INBOX", "contact@sdiary.pe.hu", "AlexIco!4)7");
            if ($this->sDiaryInbox) {
                echo "Connected";
            }
            else echo "Can't connect";
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }
        $this->checkInbox();
    }

    private function checkInbox()
    {
        $newMailsCount = imap_num_recent($this->sDiaryInbox);
        for ($i = 0; $i < $newMailsCount; ++$i) {
            $note = $this->formNote($i);
            $email = $this->formEmail($i);
            $userId = $this->selectUserId($email);
            $this->addNote($note, $userId);
        }
    }

    private function formNote($messageNumber) {
        $header = imap_header($this->sDiaryInbox, $messageNumber);

        $subject = $this->mimeDecode($header->subject);

        $date = $header->date;

        $encoding = $this->detectEncoding($messageNumber);
        $body = imap_fetchbody($this->sDiaryInbox, $messageNumber, 1);
        if ($encoding)
            $body = base64_decode($body);

        $note = new Note(null, $subject, $body, $date);
        return $note;
    }

    private function formEmail($messageNumber) {
        $header = imap_header($this->sDiaryInbox, $messageNumber);
        $from = $header->from;
        $mailbox = $from->mailbox;
        $host = $from->host;
        $email = $mailbox . "@" . $host;
        return $email;
    }

    private function addNote($note, $userId) {
        $query = "INSERT INTO notes (user_id, title, text, date)
            VALUES ('" . $userId . "', '$note->title', '$note->text', '$note->date')";
        $result = $this->dbConnection->query($query);
    }

    private function selectUserId($email) {
        $query = "SELECT id FROM users WHERE email='". $email . "'";
        $result = $this->dbConnection->query($query);
        $userId = $result->fetch_assoc();
        return $userId['id'];
    }

    private function detectEncoding($messageNumber)
    {
        $body = imap_body($this->sDiaryInbox, $messageNumber);
        $encodingPattern = "#Content-Transfer-Encoding: ([\w]+)#";
        if (preg_match($encodingPattern, $body, $matches) != 0)
            $encoding = $matches[1];
        else $encoding = FALSE;
        return $encoding;
    }

    private function mimeDecode($text)
    {
        $elements = imap_mime_header_decode($text);
        return $elements[0]->text;
    }
}

$notesReceiver = new NotesReceiver();

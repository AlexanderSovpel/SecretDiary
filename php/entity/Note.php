<?php
/**
 * Created by PhpStorm.
 * User: Alexander
 * Date: 22.04.15
 * Time: 06:23
 */

class Note {
    private $id;
    private $title;
    private $text;
    private $date;

    function __construct($id, $title, $text, $date)
    {
        $this->id = $id;
        $this->title = $title;
        $this->text = $text;
        $this->date = $date;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    public function __toString() {
        return "ID: ".$this->getId() . "\n" .
        "Title: ".$this->getTitle() . "\n" .
        "Text: ".$this->getText() . "\n" .
        "Date: ".$this->getDate() . "\n";
    }
}
<?php

defined('ENGINE') or die;

class XUser {

    private $name;
    private $group;
    private $status;
    private $id;

    public function __construct($name, $group, $status, $id) {
        $this->name = $name;
        $this->group = $group;
        $this->status = $status;
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function getGroup() {
        return $this->group;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function getId() {
        return $this->id;
    }

}

?>
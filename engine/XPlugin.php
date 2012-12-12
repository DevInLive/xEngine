<?php

defined('ENGINE') or die;

abstract class XPlugin {

    protected $core;

    public function __construct($core) {
        $this->core = $core;
        $this->user = $user;
    }

    public function buildPage($do) {
        return false;
    }

}

?>

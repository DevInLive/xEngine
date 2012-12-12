<?php

defined('ENGINE') or die;

class XTemplator {

    private $tmpfolder;
    private $tmps;

    public function __construct($tmpname) {
        $this->tmpfolder = $_SERVER['DOCUMENT_ROOT'] . '/templates/' . $tmpname . '/';
        $this->tmps = array();
        $this->tmps['main.tpl'] = file_get_contents($this->tmpfolder . 'main.tpl');
    }

    public function set($key, $value, $where = 'main.tpl') {
        $this->tmps[$where] = str_replace($key, $value, $this->tmps[$where]);
    }

    public function loadTemplate($tmp) {
        $this->tmps[$tmp] = file_get_contents($this->tmpfolder . $tmp);
    }
    
    public function build($tmp = 'main.tpl') {
        return $this->tmps[$tmp];
    }

}

?>

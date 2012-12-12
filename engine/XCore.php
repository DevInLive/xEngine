<?php

defined('ENGINE') or die;

class XCore {

    public $db;
    public $tmp;
    public $user;

    /* Конструктор класса */

    public function __construct($host, $user, $password, $dbname, $tmp) {
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $this->tmp = $tmp;
            $this->user = $this->getUser();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /* Возвращает массив новостей на данной странице */

    public function getNews($page) {
        $page--;
        $min = ($page * 10) + 1;
        $max = ($page + 1) * 10;
        try {
            $query = $this->db->prepare('SELECT * FROM `news` ORDER BY `id` DESC LIMIT :min,:max');
            $query->execute(array(':min' => $min, ':max' => $max));
            $query->setFetchMode(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        $news = array();
        while ($row = $query->fetch()) {
            $news[$row->id] = array('title' => $row->title, 'author' => $row->author, 'date' => $row->date, 'text' => $row->text);
        }
        return $news;
    }

    /* Возвращает новость с id $id */

    public function getPublication($id) {
        try {
            $query = $this->db->prepare('SELECT * FROM `news` WHERE `id` = :id');
            $query->execute(array(':id' => $id));
            $query->setFetchMode(PDO::FETCH_OBJ);
            $row = $query->fetch();
            return array('title' => $row->title, 'author' => $row->author, 'date' => $row->date, 'text' => $row->text);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function getComments($pubid, $page) {
        $min = ($page * 10) + 1;
        $max = ($page + 1) * 10;
        try {
            $query = $this->db->prepare('SELECT * FROM `comments` WHERE `pubid` = :id ORDER BY `pubid` LIMIT :min,:max');
            $query->execute(array(':id' => $pubid, ':min' => $min, ':max' => $max));
            $query->setFetchMode(PDO::FETCH_OBJ);
            $row = $query->fetch();
            return array('author' => $row->author, 'date' => $row->date, 'text' => $row->text);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function getPage($pagename) {
        try {
            $query = $this->db->prepare('SELECT * FROM `pages` WHERE `name` = :name');
            $query->execute(array(':name' => $pagename));
            $query->setFetchMode(PDO::FETCH_OBJ);
            $row = $query->fetch();
            if (!$row) {
                throw new XException();
            } else {
                return array('name' => $row->name, 'text' => $row->text);
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /* Возвращает пользователя с именем $name, если не указано, то текущего */

    public function getUser($name = null) {
        $user = null;
        if ($name == null) {
            if (empty($_SESSION['id']) || $_SESSION['id'] == null) {
                $user = new XUser('Гость', 5, '', 0);
            } else {
                try {
                    $query = $this->db->prepare("SELECT * FROM `users` WHERE `id` = :id");
                    $query->execute(array(':id' => $id));
                    $query->setFetchMode(PDO::FETCH_OBJ);
                    $row = $query->fetch();
                    if (!$row) {
                        $user = new XUser('Гость', 5, '', 0);
                    } else {
                        $user = new XUser($row->name, $row->group, $row->status, $_SESSION['id']);
                    }
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
            }
        } else {
            try {
                $query = $this->db->prepare("SELECT * FROM `users` WHERE `name` = ':name'");
                $query->execute(array(':name' => $name));
                $query->setFetchMode(PDO::FETCH_OBJ);
                $row = $query->fetch();
                if (!$row) {
                    throw new XException();
                } else {
                    $user = new XUser($row->name, $row->group, $row->status, $row->id);
                }
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }
        return $user;
    }
    
    public function auth($name) {
        $this->user = $this->getUser($name);
        $_SESSION['id'] = $this->user['id'];
    }

    public function buildAll($content) {
        $this->tmp->set('{content}', $content);
        $this->tmp->loadTemplate('header.tpl');
        $this->tmp->set('{header}', $this->tmp->build('header.tpl'));
        $this->tmp->loadTemplate('sidebar.tpl');
        $this->tmp->set('{sidebar}', $this->tmp->build('sidebar.tpl'));
    }

}

?>

<?php

defined('ENGINE') or die;

$plugins[] = new PluginMain($core);

class PluginMain extends XPlugin {

    public function buildPage($do) {
        switch ($do) {
            case 'main':
                $this->core->buildAll($this->buildNewsAtPage(1));
                break;

            case 'newspage':
                $this->core->buildAll($this->buildNewsAtPage($_GET['page']));
                break;

            case 'page':
                $this->core->buildAll($this->buildStaticPage($_GET['page']));
                break;

            case 'news':
                $this->core->buildAll($this->buildPublication($_GET['id']));
                break;

            case 'register':
                if ($user->getGroup() != 5) {
                    // Регистрация невозможна
                } else {
                    // Выводим страницу регистрации
                }
                break;

            case 'login':
                if ($user->getGroup() != 5) {
                    $this->core->buildAll($this->buildNewsAtPage(1));
                } else {
                    $login = $_POST['login'];
                    $pass = $_POST['pass'];
                    $query = $core->db->prepare("SELECT * FROM `users` WHERE `name` = ':name' AND pass = ':pass'");
                    $query->execute(array(':name' => $login, ':pass' => md5(md5($pass))));
                    $query->setFetchMode(PDO::FETCH_OBJ);
                    $row = $query->fetch();
                    if (!$row) {
                        $this->core->tmp->loadTemplate('error.tpl');
                        $this->core->tmp->set('{error}', 'Введённая вами пара логин/пароль неправильна!', 'error.tpl');
                        $this->core->buildAll($this->core->tmp->build('error.tpl').$this->buildNewsAtPage(1));
                    } else {
                        $core->auth($login);
                    }
                }
                break;

            case 'logout':
                if ($user->getGroup() == 5) {
                    $this->core->buildAll($this->buildNewsAtPage(1));
                } else {
                    $_SESSION['id'] = null;
                    $this->core->buildAll($this->buildNewsAtPage(1));
                }
                break;

            case 'ajax':
                $ajax = true;
                die('Not supported now!');
                break;

            default:
                parent::buildPage($do);
                break;
        }
    }

    private function buildNewsAtPage($page) {
        $pubs = $this->core->getNews($page);
        $content = "";
        foreach ($pubs as $pub) {
            $this->core->tmp->loadTemplate('news.tpl');
            $this->core->tmp->set('{title}', $pub['title'], 'news.tpl');
            $this->core->tmp->set('{author}', $pub['author'], 'news.tpl');
            $this->core->tmp->set('{date}', $pub['date'], 'news.tpl');
            $this->core->tmp->set('{text}', $pub['text'], 'news.tpl');
            $content += $this->core->tmp->build('news.tpl');
        }
        return $content;
    }

    private function buildPublication($id) {
        $pub = $this->core->getPublication($id);
        $this->core->tmp->loadTemplate('news.tpl');
        $this->core->tmp->set('{title}', $pub['title'], 'news.tpl');
        $this->core->tmp->set('{author}', $pub['author'], 'news.tpl');
        $this->core->tmp->set('{date}', $pub['date'], 'news.tpl');
        $this->core->tmp->set('{text}', $pub['text'], 'news.tpl');
        return $this->core->tmp->build('news.tpl');
    }

    private function buildStaticPage($page) {
        $pagec = $this->core->getPage($page);
        $this->core->tmp->loadTemplate('static.tpl');
        $this->core->tmp->set('{name}', $pagec['name'], 'static.tpl');
        $this->core->tmp->set('{text}', $pagec['text'], 'static.tpl');
        return $this->core->tmp->build('static.tpl');
    }

}

?>

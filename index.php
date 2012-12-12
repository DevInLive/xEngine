<?php
/*
?do=register - регистрация
?do=login - авторизация
?do=logout - выход
?do=page&page=%page% - вывод страницы %page%
?do=news&id=%id% - вывод новости с ID %id%
?do=ajax&upd=sidebar - выводит чистый sidebar
?do=ajax&upd=[page/news]=[page/id] - выводит чистую часть страницы
not isset do & page - десять последних новостей
*/
define('ENGINE', 1);

include 'engine/init.php';

$tmp = new XTemplator('0xMC');
$core = new XCore('localhost', '', '', 'cms', $tmp); // Инициализация ядра

$plugins = array();
foreach(glob('/engine/plugins/Plugin*.php') as $plugin) {
    include('engine/plugins/'.$plugin);
}

$do;
if (isset($_GET['do'])) {
    $do = $_GET['do'];
} else {
    $do = 'main';
}
foreach ($plugins as $plugin) {
    if($plugin->buildPage($do) != false) {
        break;
    }
}
echo $core->tmp->build();
?>
<?php
require_once __DIR__ . "./resources/sql/InstallDB.php";
require_once __DIR__ . "./resources/Autoloader.php";
require_once __DIR__ . "./resources/Helpers/Helper.php";
require_once __DIR__ . "./config/config.php";

use \Autologin\Sessions;
use \Route\Router;

$db_install = new InstallDB();
$db_install->check_database();

$autologin = new Sessions();
$autologin->check_session();

$site_routes = [
    "/" => "public/main.php",
    "/add-task" => "public/add-task.php",
    "/edit-task" => "public/edit-task.php",
    "/register" => "public/register.php",
    "/404" => "public/404.php"
];

$route = new Router;
$route->setArrayRoutes($site_routes);
if (!$route->route()) {
    include_once "public/404.php";
}


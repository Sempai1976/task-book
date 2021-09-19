<?php
require_once __DIR__ . "./NamespaceAutoloader.php";

$autoloader = new NamespaceAutoloader();

$autoloader->addNamespace('Autologin', 'resources/Classes/Autologin');
$autoloader->addNamespace('Route', 'resources/Classes/Route');
$autoloader->addNamespace('Аuth', 'resources/Classes/Auth');
$autoloader->addNamespace('TaskBook', 'resources/Classes/TaskBook');
$autoloader->addNamespace('SimpleValidator', 'resources/Classes/SimpleValidator');
$autoloader->register();
?>
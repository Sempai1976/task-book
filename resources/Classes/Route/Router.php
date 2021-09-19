<?php
namespace Route;
/**
 * Класс для обработки ЧПУ-запросов
 * @author дизайн студия ox2.ru
 */
class Router 
{
    private $_route = [];

    /**
     * Метод для установки маршрута, и файла который будет открываться при заданом маршруте
     * @param <string> $dir - маршрут
     * @param <string> $file - адрес файла
     */
    public function setRoute($dir, $file) {
        $this->_route[trim($dir, "/")] = $file;
    }
    
    /**
     * Метод для установки маршрута, и файла который будет открываться при заданом маршруте
     * @param <array> routes = [$dir => $file]
     */
    public function setArrayRoutes($routes = []) {
    	foreach($routes as $dir => $file) {
			$this->_route[trim($dir, "/")] = $file;
		}
    }
 
    /**
     * Метод смотрит текущий адрес, и сверяет его с установленными маршрутами,
     * если для открытого адреса установлен маршрут, то открываем страницу
     * @return <boolean>
     */
    public function route() {
        if (!isset($_SERVER["PATH_INFO"])) { //Если открыта главная страница
            include_once "public/main.php"; //Открываем файл главной страницы
        } elseif (isset($this->_route[trim($_SERVER["PATH_INFO"], "/")])) { //Если маршрут задан
            include_once $this->_route[trim($_SERVER["PATH_INFO"], "/")]; //Открываем файл, для которого установлен маршрут
        } else {
//			include_once "public/404.php";
            return false; //Если маршрут не задан
		}
 
        return true;
    }
}
?>
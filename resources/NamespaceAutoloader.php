<?php
class NamespaceAutoloader 
{
    protected $namespaceMap = array();

    public function addNamespace($namespace, $rootDir){
        if (is_dir($rootDir)){
            $this->namespaceMap[$namespace] = $rootDir;
            return true;
        }
        return false;
    }

    public function register(){
        spl_autoload_register(array($this, 'autoload'));
    }

    protected function autoload($class){
        $pathParts = explode('\\', $class);
        if (is_array($pathParts)){
            $namespace = array_shift($pathParts);
            if (!empty($this->namespaceMap[$namespace])){
                $filePath = $this->namespaceMap[$namespace].'/'.implode('/', $pathParts).'.php';
                require_once $filePath;
                return true;
            }
        }
        return false;
    }
}
?>
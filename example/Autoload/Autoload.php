<?php
require_once(__dir__.'/Init.php');
class Autoload{
    protected static $_path;
    protected static $_class;
    public static function setPath($path){
        self::$_path = $path;
    }
    public static function included($name){
        if(isset(self::$_class[$name])){
            require_once(self::$_class[$name]);
            if(class_exists($name,false)){
                return true;
            }
        }
        return false;
    }
    public static function load($name){
    
        $classPath = str_replace('\\',DIRECTORY_SEPARATOR,$name);

        $classFile = APP_PATH.'/'.$classPath.'.php';

        if(isset(self::$_class[$name])){
        
            self::included($name);
        } 

        if(!file_exists($classFile)){
            throw new \Exception('** class '.$classFile.' not found **');
        } else {
            self::$_class[$name] = $classFile;  
            self::included($name);
            
        }
        
    }
}
spl_autoload_register('Autoload::load');
<?php 

class Database{
  public static $conn;
  public static $db;

}

/* Page object */
class Page{
  protected $_vars = array();
  public static $theme = 'themes/default/';
  
  public function render($template=''){
    include_once(self::$theme . 'layout.tpl');
  }

  public static function redirect($url){
    header('Location: ' . $url);
  }

  public function __set($var,$val){
    $this->_vars[$var] = $val;
  }

  public function __get($var){
    return $this->_vars[$var];
  }
}

/* Translations */
class Language{
  public static $translation = array();
  public static $language = 'english';

  public static function translate($text){
    if(isset(self::$translation[$text])){
      return self::$translation[$text];
    }else{
      return $text;
    }
  }
}


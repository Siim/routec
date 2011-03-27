<?php 


define('WWW_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('PREFIX', substr(getcwd(),strlen(WWW_ROOT),strlen(getcwd())));

interface DatabaseContract{

  /* Get database instance (maybe singleton) */
  public static function get();

}

/* Use Database::get() instead of new Database() */
class Database extends PDO{

  public static $connection = null;

  public function __construct(){
    
    if(self::$connection==null){
      try{
        $this->setup();
        self::$connection = $this;

      }catch(PDOException $e){
        echo "database exeption!";
      }
    }
  }

  /* Database config */
  static public function getUsername(){
    return '';
  }

  static public function getPassword(){
    return '';
  }

  static public function getDSN(){
    return 'mysql:host=localhost;dbname=test';
  }

  public static function get(){
    if(self::$connection==null){
      self::$connection = new Database();
    }
    return self::$connection;
  }


  public function setup(){
    parent::__construct(self::getDSN(),self::getUserName(),self::getPassword());
  }
}


/** Common class for data objects/items */
class DataItem{

  protected $query;  
  protected $fields = array();
  protected $db = null;
  protected $language = 'english';

  /* Singleton object */
  public static $object=null;

  public function tablename(){
    return strtolower(__class__);
  }

  public function __set($key,$val){
    $this->fields[$key] = $val;
  }

  public function __get($key){
    if(isset($this->fields[$key])){
      return $this->fields[$key];
    }else{
      return '';
    }
  }


  /* Fetch one item by id or section */
  public function fetchOne(){
    $res = array();

    if(isset($this->fields['id'])){
      $query = $this->db->prepare("SELECT * FROM $this->tablename WHERE id=? AND language=? LIMIT 1");
      $query->execute(array($this->fields['id'],$this->language));
      $res =  $query->fetch(PDO::FETCH_ASSOC);

    }else if(isset($this->fields['section'])){
      $query = $this->db->prepare("SELECT * FROM $this->tablename WHERE section=? AND language=? LIMIT 1");  
      $query->execute(array($this->fields['section'],$this->language));
      $res = $query->fetch(PDO::FETCH_ASSOC);
    }

    if(!empty($res)){
      foreach($res as $k => $v){
        $this->fields[$k] = $v;
      }

      return true;
    }

    return false;
  }

  /**
   * Fetch item by group. e.g different article groups in same page 
   * @return array
   */
  public function fetchGroup(){
    $res = array();

    if(isset($this->fields['group'],$this->fields['section'])){
      $query = $this->db->prepare("
        SELECT * FROM $this->tablename WHERE section=? AND group=?
      ");

      $query->execute(array(
        $this->section,
        $this->group
      ));
      $res = $query->fetch(PDO::FETCH_ASSOC);
    }else if(isset($this->fields['group'],$this->fields['id'])){
      $query = $this->db->prepare("
        SELECT * FROM $this->tablename WHERE section=? AND id=?
      ");

      $query->execute(array(
        $this->section,
        $this->id
      ));
      $res = $query->fetch(PDO::FETCH_ASSOC);
    }

    return $res;
  }


}

/* Page object */
class Page{
  protected $_vars = array();

  /* Paths */
  public static $theme = '/themes/default/';
  
  /* Theme full path */
  public static $theme_fp = '';
  public static $templates = '';

  
  /** Renders page with layout by default */
  public function render($template='', $layout=true){
    
    ob_start();
    if($layout){
      include(self::$templates . 'layout.tpl');
    }else{
      include(self::$templates . $template);
    }
    $html = ob_get_clean();

    if(class_exists('tidy') && !User::get()->isAdmin()){
      $config = array(
        'indent'         => true,
        'wrap'           => 200
      );

      // Tidy
      $tidy = new tidy;
      $tidy->parseString($html, $config, 'utf8');
      $tidy->cleanRepair();
      echo $tidy;
    }else{
      echo $html;
    }
  
  }

  /** Renders page without layout */
  public function sub($template=''){
    $this->render($template,false);
  }

  public function capture($template){
    $this->sub($template);
  }

  public function __set($var,$val){
    $this->_vars[$var] = $val;
  }

  public function __get($var){
    if(isset($this->_vars[$var]))return $this->_vars[$var];
    else return '';
  }

  /** Get instance of Page class */
  public static function get(){
    return new Page();
  }
}

Page::$templates = dirname(__file__) . '/themes/default/templates/';
Page::$theme = PREFIX . '/themes/default/';
Page::$theme_fp = dirname(__file__) . '/themes/default/';

/* Translations */
class Language{
  public static $translation = array();
  public static $language = 'estonian';

  public static function translate($text){
    if(isset(self::$translation[$text])){
      return self::$translation[$text];
    }else{
      return $text;
    }
  }

  public static function set($lang='estonian'){
    self::$language = $lang;
  }

  public static function get(){
    return self::$language;
  
  }
}


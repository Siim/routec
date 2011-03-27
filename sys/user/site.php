<?php
/** @file site.php */

site(array(
  
  '/login' =>
  
  function(){
    
    $fail = function(){
      $_SESSION['error'][] = "Login failed!";
      redirect('/admin');
    };

    if($user = User::create()){
      if($user->login()){
        
        // move this somewhere else
        unset($_SESSION['user']);
        
        $_SESSION['user'] = $user;
        redirect('/');
      }else{
        $fail();
      }
    
    }else{
      $fail();
    }
  },
  
  
  '/logout' =>
  
  function(){
    session_destroy();
    redirect('/');
  },

  '/admin' => 

  function(){
    $page = Page::get();
    $page->user = User::get();
    $page->sub('user/login.tpl');
  },

));


/*------------------------------------
 * Login dependencies 
 *-----------------------------------*/

/* All these methods are required to deal with user */
interface UserContract{
  function __construct($username,$password);
  public function login();
  public function logout();
  public static function get();
  public static function create();
}

/** 
 * User login mysql with mysql PDO 
 * To get User instance use User::get() instead of new User()
 */
class User extends DataItem{
  protected $username;
  protected $password;
  protected $loggedin;
  protected $admin;

  private function __construct($username='',$password=''){
    
    $db = Database::get();
    $tablename = strtolower(__class__);

    /* Table scheme here */
    PRODUCTION ? 0 : $db->query(
      "CREATE TABLE IF NOT EXISTS $tablename (
          `id` int(11) NOT NULL AUTO_INCREMENT
        , `username` VARCHAR(255)
        , `password` text
        , `admin` bool
        , CONSTRAINT `u_username` UNIQUE (`username`)
        , PRIMARY KEY (`id`)
      );
      
      INSERT INTO $tablename (username,password,admin) VALUES ('admin','admin',1);"
    );

    /* Sets up a parent class, calls parent constructor */
    $this->username = $username;
    $this->password = $password;
    $this->loggedin = false;
    $this->admin = false;
  }

  public function __get($key){
    return $this->$key;
  }

  public function __set($key,$val){
    $this->$key = $val;
  }

  /** @return bool Returns true if login successful */
  public function login(){
    $db = Database::get();
    $query = $db->prepare(
      'SELECT username,password,admin FROM user where username=? 
       AND password=? LIMIT 1'
    );

     $query->execute(array(
       $this->username,
       $this->password
     ));

     $res = $query->fetch(PDO::FETCH_ASSOC);

     if($res['username'] == $this->username && $res['password'] == $this->password){
       $this->loggedin = true;

       if($res['admin']){
         $this->admin = true;
       }

       return true;
     }else{
       return false;
     }
  }

  public function isLoggedIn(){
    return $this->loggedin;
  }

  public function isAdmin(){
    /* User must be logged in */
    return (bool) $this->admin;
  }


  public function logout(){
    $this->loggedin = false;
  }

  public function savepass($pass){
    $db = Database::get();
    $tablename = strtolower(__class__);
    $qry = $db->prepare("UPDATE $tablename set password=? WHERE username=?");
    return (bool) $qry->execute(array($pass,$this->username));
  }
  
  /** Get current user from session or return default user */
  public static function get(){
    if(isset($_SESSION['user']))return $_SESSION['user'];
    else return new User();
  }

  /** Create user according to POST data */
  public static function create($post=array()){
    
    if(empty($post)){
      $post = $_POST;
    }
    
    if(isset($post['username'], $post['password'])){
      return new User($post['username'],$post['password']);
    }
    return null;
  }
}


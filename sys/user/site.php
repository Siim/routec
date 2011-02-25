<?php

/* Site routes */
site(array(
  
  
  '/' => 
  
  function(){
    if(func_num_args()==0){
    
      $page = new Page();
      $page->user = User::get();
      $page->render('user/index.tpl');
    
    }else{
      header("HTTP/1.0 404 Not Found");
      echo "Page not found!";
    }
  },
  
  
  '/login' =>
  
  function(){
    
    $fail = function(){
      $_SESSION['error'][] = "Login failed!";
      header("location: /");
    };

    if($user = User::create()){
      if($user->login()){
        $_SESSION['user'] = $user;
        header("location: /");
      }else{
        $fail();
      }
    
    }else{
      $fail();
    }
  },
  
  
  '/logout' =>
  
  function(){
    $_SESSION['user']->logout();
    header("location: /");
  },

  '/language' => 

  function($lang){
    $_SESSION['language'] = $lang;
    Page::redirect('/');
  }

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

/** User login mongodb example */
class User implements UserContract{
  protected $username;
  protected $password;
  protected $loggedin;

  function __construct($username='',$password=''){
    $this->username = $username;
    $this->password = $password;
    $this->loggedin = false;
  }

  /** @return bool Returns true if login successful */
  public function login(){
    $database = Database::$db;
    $users = $database->users;
    $res = $users->findOne(array('username' => $this->username));

    if(!empty($res)){
      if($this->password === $res['password']){
        $this->loggedin = true;
        return true;
      }
    }
    return false;
  }

  public function isLoggedIn(){
    return $this->loggedin;
  }

  public function logout(){
    $this->loggedin = false;
  }
  
  /** Get current user from session or return default user */
  public static function get(){
    if(isset($_SESSION['user']))return $_SESSION['user'];
    else return new User();
  }

  /** Create user according to POST data */
  public static function create(){
    if(isset($_POST['username'], $_POST['password'])){
      return new User($_POST['username'],$_POST['password']);
    }
    return null;
  }
}

/*------------------------------------ 
 * Example route dependencies 
 *-----------------------------------*/

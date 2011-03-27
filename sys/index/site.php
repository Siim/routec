<?php
/* @file (index)site.php */

site(array(
  
  '/' => 
  
  function(){
    if(func_num_args()==0){
    
      $page = new Page();
      $page->user = User::get();
      $page->render('index/index.tpl');
    
    }else{
      header("HTTP/1.0 404 Not Found");
      $page->render('404.tpl');
    }
  }
));


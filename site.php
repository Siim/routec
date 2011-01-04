<?php

$site = array(
  
  "/" => 
  function(){
    if(func_num_args()==0){
      include_once('view/index.tpl');
    }else{
      header("HTTP/1.0 404 Not Found");
      echo "Page not found!";
    }
  },
  
  /* e.g. http://site/sayhello/John/Doe */
  "/sayhello" => 
  function($first="James",$last="Bond"){
    include_once('view/sayhello.tpl');
  }
);


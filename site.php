<?php

$site = array(
  
  "/" => 
  function(){
    if(func_num_args()==0){
      echo "Hello world!";
    }else{
      echo "Page not found!";
    }
  },
  
  "/sayhello" => 
  function($first="John",$last="Doe"){
    echo "Hello $last, $first $last.";
  
  }
);


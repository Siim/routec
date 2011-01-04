<?php

$site = array(
  
  "/" => function(){
    echo "Hello world!";
  },
  
  "/sayhello" => function($first="John",$last="Doe"){
    echo "Hello $last, $first $last.";
  
  },

  "default" => function(){
    echo "error! page not found!";
  }

);


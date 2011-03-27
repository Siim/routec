<?php

site(array(

  '/language' => 

  function($lang){
    $_SESSION['language'] = $lang;
    setcookie('language',$lang, time() + 3600, '/');
    redirect('/');
  }


));

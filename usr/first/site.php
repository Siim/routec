<?php

/* If you want to disable the module, just write return to the beginning of the file */
site(array(

  '/logout' => 

  function(){
    echo "you failed to logout haha!"; 
    $user = Database::$db->users->findOne();
    print_r($user);
  }

));

?>

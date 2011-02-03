<?php
  
  /* Scan sys and usr dirs for modules, usr dir same module always overrides sys */

  $sys = scanModules('sys/');
  $usr = scanModules('usr/');
  
  /* get all modules that will be loaded from sys dir */
  $sys_modules = array_intersect($usr,$sys);

  /* get all overridden modules */
  $usr_modules = array_intersect($sys,$usr);

  /* load all sys modules */
  loadModules('sys/',$sys_modules);
  
  /* load all overridden modules */
  loadModules('usr/',$usr_modules);

  session_start();

  if(isset($site)&&!empty($site)){
    $controller = $site[array_pop(route($site))];

    if(is_callable($controller)){
      $params = getParams(getCurrentRoute(),$site); 
      eval("\$controller($params);");
    }else{
      /* TODO: log internal error */
    }
  }

  /* load all modules */
  function loadModules($pfx,$modules){
    return array_map(function($module) use ($pfx) {
      $file = ($pfx . $module . '/' . 'site.php');

      echo $file . PHP_EOL;

      /* module loaded */
      if(file_exists($file)){
        include_once($file);
        return array($module => true);
      }

      return array($module => false);
    },$modules);
  }

  /** Scans modules from specified dirs */
  function scanModules($dir){
    $cwd = getcwd();
    chdir($dir);
    $contents = scandir('./');
    $dirs = getDirs($contents);
    chdir($cwd);
    return $dirs;
  }

  /**
   * Get all dirs in a path
   */
  function getDirs($arr){
    return array_filter($arr,function($el){
      return is_dir($el) && $el != '.' && $el != '..';
    });
  }

  /**
   * Lookup routes by comapring site prefix against a route. 
   * The longest prefix wins (it is the last element of the result array).
   * @param routes Array of routes (site array used to define routes)
   * @return array Array of all matches
   */
  function route($routes){
    return array_filter(array_keys($routes),function($el){
      if(isprefixOf($el,getCurrentRoute()))return $el;
    });
  }

  /** Checks if a string is prefix of other string */
  function isPrefixOf($needle,$haystack){
    return substr($haystack,0,strlen($needle))==$needle;
  }

  /** 
   * Request uri
   * @return string
   */
  function getCurrentRoute(){
    return $_SERVER['REQUEST_URI'];
  }

  /**
   * Get route params on order, e.g. /foo/$param1/$param2 
   * returns a string of "param1_value,param2_value", later
   * we can pass these parameters to a handler
   * @param route Current route path
   * @return string
   */
  function getParams($route,$site){
    if($route_pfx = array_pop(route($site))){

      if($route_pfx!=='/'){
        $params_str = explode($route_pfx,$route);
      }
      /* index page */
      else{
        $params_str = $route;
      }

      if(isset($params_str[1])){
        /* array to string */
        return implode(',',
          /* single-quote each element, e.g. foo will be 'foo' */
          array_map(function($el){
            return "'$el'";
          },
          /* filter out empty strings */
          array_filter(
            /* string to array */
            explode( "/", $params_str[1]),
            function($el){ return $el!=""; }
          )
        ));
      }
    }

    /* No params... So we return an empty string */
    return "";
  }


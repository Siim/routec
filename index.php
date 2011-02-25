<?php
  
  /* Scan sys and usr dirs for modules, usr dir same module always overrides sys */

  $sys = scanModules('sys/');
  $usr = scanModules('usr/');
  $site = array();

  /* prepare all sys modules */
  $load_sys = prepareModules('sys/',$sys);

  /* prepare all usr modules */
  $load_usr = prepareModules('usr/',$usr);
  
  $all_modules = array_merge($load_sys,$load_usr);

  /* include modules in global scope! */
  foreach($all_modules as $module){
    if(isset($module['module']))include_once($module['filename']);
  }

  session_start();

  if(isset($site)&&!empty($site)){
    $controller = $site[array_pop(route($site))];

    if(is_callable($controller)){
      $params = getParams(getCurrentRoute(),$site); 
      eval("\$controller($params);");
    }else{
      header("HTTP/1.0 500 Internal Error");
    }
  }

  /** Overridded route wins! */
  function site($routes){
    //all unique keys from original
    $unique_sys = array_diff_key($GLOBALS['site'],$routes);
    
    //duplicate keys
    $override = array_intersect_key($routes,$GLOBALS['site']);
    
    //unique keys from routes
    $unique_usr = array_diff_key($routes,$GLOBALS['site']);
    $GLOBALS['site'] = array_merge($unique_sys,$override,$unique_usr);
  }

  /* prepare all modules for loading */
  function prepareModules($pfx,$modules){
    return array_map(function($module) use ($pfx) {
      $file = ($pfx . $module . '/' . 'site.php');
      $disabled = ($pfx . $module . '/' . 'disabled');
      /* module loaded; if disabled file is in the dir, then module is disabled! */
      if(file_exists($file) && !file_exists($disabled)){
        return array('module' => $module ,'filename' => $file);
      }

      return array();
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

  /** Get all dirs in a path */
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

  function render($template){
  
  }

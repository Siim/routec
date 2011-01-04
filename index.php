<?php
  
  require_once 'site.php';

  $controller = $site[array_pop(route($site))];

  if(is_callable($controller)){
    $params = getParams(getCurrentRoute(),$site); 
    eval("\$controller($params);");
  }else{
    /* TODO: log internal error */
  }

  /**
   * Get current route
   * @param current_route Current route
   * @param last Return only last route
   * @return array Array of all matches or only last match by default
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
   * Get request uri
   * @return string
   */
  function getCurrentRoute(){
    return $_SERVER['REQUEST_URI'];
  }

  /**
   * Get route params on order, e.g. /foo/bar/$param
   * @param route Current route
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

    return "";
  }


<?php
require_once 'includes.php';

$file_config = "config.yml";
$config = QuiqueConfig::get_arr_yml_config($file_config);

$requestURI = explode('/', $_SERVER['REQUEST_URI']);

$route = new QuiqueRoute();
$routes_match = $route->match_route($_SERVER['REQUEST_URI']);
$params = array();

if($routes_match !== false) {
    define('MODULE_NAME',$routes_match["app"]);
    define('CONTROLLER_NAME',$routes_match["controller"]);
    define('ACTION_NAME',$routes_match["action"]);
    $params = $routes_match["params"];
}
else {
    $requestURI[1] = str_replace("?", "", $requestURI[1]);

    if($route->isModule($requestURI[1])) {
        if(count($requestURI) == 4) {
            define('MODULE_NAME',$requestURI[1]);
            define('CONTROLLER_NAME',$requestURI[2]);
            if(is_null($requestURI[3])) {
                define('ACTION_NAME',null);
            }
            else {
                define('ACTION_NAME',$requestURI[3]);
            }
            $params = array();
        }
        elseif(count($requestURI) == 3) {
            define('MODULE_NAME',$requestURI[1]);
            define('CONTROLLER_NAME',$requestURI[2]);
            define('ACTION_NAME',null);
            $params = array();
        }
        else {
            require_once 'html_errors/404.php';
        }
    }
    else {
        if((count($requestURI) == 3) || (count($requestURI) == 4 && $requestURI[3] == "")) {
            define('MODULE_NAME','default');
            define('CONTROLLER_NAME',$requestURI[1]);
            if(is_null($requestURI[2])) {
                define('ACTION_NAME',null);
            }
            else {
                define('ACTION_NAME',$requestURI[2]);
            }
            $params = array();
        }
        elseif(count($requestURI) == 2) {
            define('MODULE_NAME','default');
            define('CONTROLLER_NAME',$requestURI[1]);
            if(is_null($requestURI[2])) {
                define('ACTION_NAME',null);
            }
            else {
                define('ACTION_NAME',$requestURI[2]);
            }
            $params = array();
        }
        else {
            require_once 'html_errors/404.php';
        }
    }
}

if(!isset($config[MODULE_NAME])) {
    try {
        throw new QuiqueExceptions(SHOW_ERRORS,"Error Config",MODULE_NAME." No tiene configuracion definida.");
    }
    catch(QuiqueExceptions $ex) {
        $ex->echoHTMLMessage();
    }
}

$show_errors = $config[MODULE_NAME]["show-errors"];

if($show_errors) {
    ini_set('display_errors','On');
}
else {
    ini_set('display_errors','Off');
}

$time_zone = $config[MODULE_NAME]["timezone"];
date_default_timezone_set($time_zone);

$enconding = $config[MODULE_NAME]["encoding"];

defined('SHOW_ERRORS') || define('SHOW_ERRORS', $show_errors);
defined('ENCODING') || define('ENCODING', $enconding);

$is_cache = $config[MODULE_NAME]["cache"]["cache"];
$time_cache = $config[MODULE_NAME]["cache"]["time"];

if($is_cache) {
    $file_config = "cache_routes.yml";
    $config_cache = QuiqueConfig::get_arr_yml_config($file_config);
    
    $cache = new QuiqueCache($config_cache["cache_all"],$config_cache["page_cached"]);
    $cache->start($time_cache);
}

header('Content-type: text/html; charset='.ENCODING);

$require_path = APP_PATH.'/'.MODULE_NAME.'/controller/'.CONTROLLER_NAME.'_controller.php';
$require_controller_path = APP_PATH.'/'.MODULE_NAME.'/controller/controller.php';
$require_model_path = APP_PATH.'/'.MODULE_NAME.'/model/model.php';
$require_helper_path = APP_PATH.'/'.MODULE_NAME.'/helpers/'.CONTROLLER_NAME.'_helper.php';

defined('LIB_APP_PATH') || define('LIB_APP_PATH', realpath(PROJECT_PATH . '/apps/'.MODULE_NAME.'/libs'));
defined('MODEL_APP_PATH') || define('MODEL_APP_PATH', realpath(PROJECT_PATH . '/apps/'.MODULE_NAME.'/model'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(LIB_APP_PATH),
    get_include_path(),
)));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(MODEL_APP_PATH),
    get_include_path(),
)));

if(file_exists($require_path)) {
    try {
        require_once $require_controller_path;
        require_once $require_model_path;
        require_once $require_path;
        require_once $require_helper_path;
        
        $class_name = CONTROLLER_NAME.'_controller';
        $controller = new $class_name();
        $controller->set_params($params);
        $action_name = ACTION_NAME;
        if(method_exists($controller, $action_name)) {
            $controller->$action_name();
        }
        elseif(is_null($action_name)) {
            $controller->index();
        }
        elseif($action_name == "") {
            $controller->index();
        }
        else {
            require_once 'html_errors/404.php';
        }
    }
    catch(Exception $ex) {
        try {
            throw new QuiqueExceptions(SHOW_ERRORS,"Error Controller",$ex->getMessage());
        }
        catch(QuiqueExceptions $ex) {
            $ex->echoHTMLMessage();
        }
    }
}
else {
    require_once 'html_errors/404.php';
}

if($is_cache) {
    $cache->end();
}

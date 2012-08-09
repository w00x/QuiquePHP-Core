<?php
class QuiqueRoute {
    public function __construct() {
        
    }
    
    public function isModule($module) {
        $list_dir = scandir(APP_PATH);
        if(array_search($module, $list_dir) !== FALSE) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function match_route($url) {
        require_once 'spyc/spyc.php';
        
        $path_routes = CONFIG_PATH.'/routes.yml';
        if(file_exists($path_routes)) {
            $routes = Spyc::YAMLLoad($path_routes);
        }
        else {
            try {
                throw new QuiqueExceptions(SHOW_ERRORS,"Error Routes","Archivo de configuracion de rutas no existe");
            }
            catch(QuiqueExceptions $ex) {
                $ex->echoHTMLMessage();
            }
        }
        
        foreach ($routes as $key => $route) {
            $pos_dos_puntos = strpos($key, ":");
            $variables_ruta = array();
            
            if($pos_dos_puntos !== FALSE) {
                $ruta_pura = substr($key, 0,$pos_dos_puntos);
                $variables_ruta = $this->variables_route($key);
            }
            else {
                $ruta_pura = $key;
            }
            
            if($ruta_pura != "default" && $ruta_pura[strlen($ruta_pura)-1] != "/") {
                $ruta_pura = $ruta_pura."/";
            }
            
            if($url[strlen($url)-1] != "/") {
                $url = $url."/";
            }
            
            if($url == "/") {
                if(isset($routes["/"]["name"])){
                    $name_default = $routes["/"]["name"];
                }
                else {
                    try {
                        throw new QuiqueExceptions(SHOW_ERRORS,"Error Default","Default path no existe");
                    }
                    catch(QuiqueExceptions $ex) {
                        $ex->echoHTMLMessage();
                    }
                }
                $ruta_default = $this->get_ruta_by_nombre($name_default, $routes);
                
                if(!isset($ruta_default["app"])) {
                    try {
                        throw new QuiqueExceptions(SHOW_ERRORS,"Error Application","Applicación no existe");
                    }
                    catch(QuiqueExceptions $ex) {
                        $ex->echoHTMLMessage();
                    }
                }
                elseif(!isset($ruta_default["controller"])) {
                    try {
                        throw new QuiqueExceptions(SHOW_ERRORS,"Error Controller","Controlador no existe");
                    }
                    catch(QuiqueExceptions $ex) {
                        $ex->echoHTMLMessage();
                    }
                }
                elseif(!isset($ruta_default["action"])) {
                    try {
                        throw new QuiqueExceptions(SHOW_ERRORS,"Error Action","Acción no existe");
                    }
                    catch(QuiqueExceptions $ex) {
                        $ex->echoHTMLMessage();
                    }
                }
                else {
                    return array("app"=>$ruta_default["app"],"controller"=>$ruta_default["controller"],"action"=>$ruta_default["action"],"params"=>array());
                }
            }           
            elseif(strpos($url,$ruta_pura) === 0 && $ruta_pura != "/") {
                if(count($variables_ruta) > 0) {
                    $params_str = str_replace($ruta_pura, "", $url);
                    
                    if(strlen($params_str) != 0) {    
                        if($params_str[strlen($params_str)-1] == "/") {
                            $params_str = substr($params_str, 0,-1);
                        }
                        $params_arr = explode("/", $params_str);
                        if(count($params_arr) == count($variables_ruta)) {
                            $params = array();
                            for($i=0;$i<count($params_arr);$i++) {
                                $params[$variables_ruta[$i]] = $params_arr[$i];
                            }
                            
                            if(!isset($routes[$key])) {
                                try {
                                    throw new QuiqueExceptions(SHOW_ERRORS,"Error Ruta","Ruta no existe");
                                }
                                catch(QuiqueExceptions $ex) {
                                    $ex->echoHTMLMessage();
                                }
                            }
                            elseif(!isset($routes[$key]["app"])) {
                                try {
                                    throw new QuiqueExceptions(SHOW_ERRORS,"Error Application","Applicación no existe");
                                }
                                catch(QuiqueExceptions $ex) {
                                    $ex->echoHTMLMessage();
                                }
                            }
                            elseif(!isset($routes[$key]["controller"])) {
                                try {
                                    throw new QuiqueExceptions(SHOW_ERRORS,"Error Controller","Controlador no existe");
                                }
                                catch(QuiqueExceptions $ex) {
                                    $ex->echoHTMLMessage();
                                }
                            }
                            elseif(!isset($routes[$key]["action"])) {
                                try {
                                    throw new QuiqueExceptions(SHOW_ERRORS,"Error Action","Acción no existe");
                                }
                                catch(QuiqueExceptions $ex) {
                                    $ex->echoHTMLMessage();
                                }
                            }
                            return array("app"=>$routes[$key]["app"],"controller"=>$routes[$key]["controller"],"action"=>$routes[$key]["action"],"params"=>$params);
                        }
                    }
                }
                else {
                    if(!isset($routes[$key])) {
                        try {
                            throw new QuiqueExceptions(SHOW_ERRORS,"Error Ruta","Ruta no existe");
                        }
                        catch(QuiqueExceptions $ex) {
                            $ex->echoHTMLMessage();
                        }
                    }
                    elseif(!isset($routes[$key]["app"])) {
                        try {
                            throw new QuiqueExceptions(SHOW_ERRORS,"Error Application","Applicación no existe");
                        }
                        catch(QuiqueExceptions $ex) {
                            $ex->echoHTMLMessage();
                        }
                    }
                    elseif(!isset($routes[$key]["controller"])) {
                        try {
                            throw new QuiqueExceptions(SHOW_ERRORS,"Error Controller","Controlador no existe");
                        }
                        catch(QuiqueExceptions $ex) {
                            $ex->echoHTMLMessage();
                        }
                    }
                    elseif(!isset($routes[$key]["action"])) {
                        try {
                            throw new QuiqueExceptions(SHOW_ERRORS,"Error Action","Acción no existe");
                        }
                        catch(QuiqueExceptions $ex) {
                            $ex->echoHTMLMessage();
                        }
                    }
                    
                    return array("app"=>$routes[$key]["app"],"controller"=>$routes[$key]["controller"],"action"=>$routes[$key]["action"],"params"=>array());
                }
            }
        }
        return false;
    }
    
    private function variables_route($route) {
        $url_tmp = $route;
        
        if($route[strlen($route)-1] != "/") {
            $url_tmp = $url_tmp."/";
        }
        
        $variables = array();
        do {
            $pos_dos_puntos = strpos($url_tmp, ":");
            $url_tmp = substr($url_tmp,$pos_dos_puntos+1);
            $pos_slash = strpos($url_tmp,"/");
            $tmp = substr($url_tmp, 0,$pos_slash);
            if($tmp !== false) {
                $variables[] = $tmp;
            }
            $url_tmp = substr($url_tmp,$pos_slash+1);
        } while($pos_slash !== false);
        
        return $variables;
    }
    
    private function get_ruta_by_nombre($name,$routes) {
        foreach($routes as $key => $route) {
            if($route["name"] == $name) {
                $route["url"] = $key;
                return $route;
            }
        }
        return false;
    }
    
    public function url_for($name_route,$params = array()) {
        $path_routes = CONFIG_PATH.'/routes.yml';
        if(file_exists($path_routes)) {
            $routes = Spyc::YAMLLoad($path_routes);
        }
        else {
            return false;
        }
        
        $ruta = $this->get_ruta_by_nombre($name_route,$routes);
        $url = $ruta["url"];
        $variables_route = $this->variables_route($url);
        
        if($ruta !== false && strpos($url,":") !== false && count($variables_route) == count($params)) {
            foreach($params as $key => $param) {
                $url = str_replace($key, $param, $url);
            }
            
            if(strpos($url,":") === false) {
                return URL_BASE.$url;
            }
            else {
                return false;
            }
        }
        elseif(count($variables_route) != count($params)) {
            return false;
        }
        elseif($ruta === false) {
            return false;
        }
        else {
            return URL_BASE.$url;
        }
    }
}
?>

<?php
class QuiqueConfig {
    public static function get_arr_yml_config($file_name) {
        $file_path = CONFIG_PATH.'/'.$file_name;
        if(file_exists($file_path)) {
            return Spyc::YAMLLoad($file_path);
        }
        else {
            try {
                throw new QuiqueExceptions(SHOW_ERRORS,"Error Configure","Configuracion <b>".$file_path."</b> no existe");
            }
            catch(QuiqueExceptions $ex) {
                $ex->echoHTMLMessage();
            }
        }
    }
    
    public static function apend_array_to_yml($file_name,$array_to_append) {
        $file_path = CONFIG_PATH.'/'.$file_name;
        if(file_exists($file_path)) {
            $foo = new Spyc();
            $actual_data = $foo->loadFile($file_path);
            $actual_data = array_merge($actual_data, $array_to_append);
            $new_yml = $foo->dump($actual_data);
            
            $file = fopen($file_path,"w");
            fwrite($file, $new_yml);
            fclose($file);
        }
        else {
            try {
                throw new QuiqueExceptions(SHOW_ERRORS,"Error Configure","Configuracion <b>".$file_path."</b> no existe");
            }
            catch(QuiqueExceptions $ex) {
                $ex->echoHTMLMessage();
            }
        }
    }
}
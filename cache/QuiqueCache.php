<?php

class QuiqueCache {
    private $cache_link;
    private $cachedir;
    private $cache_all;
    private $request_uri_list;
    private $cachear;
    
    public function __construct($_cache_all,$_page_cached) {
        $this->cache_all = $_cache_all;
        $this->request_uri_list = $_page_cached;
    }
    
    public function start($time_second) {
        $this->cachedir = CACHE_PATH;
        $cachetime = $time_second;
        $request_uri = $_SERVER['REQUEST_URI'];
        $page = 'http://' . $_SERVER['HTTP_HOST'] . $request_uri;
        $this->cache_link = $this->cachedir."/".md5($page).".html";
        
        $this->cachear = false;
        if($this->cache_all) {
            $this->cachear = true;
        }
        elseif(array_search($request_uri, $this->request_uri_list)) {
            $this->cachear = true;
        }
        
        if($this->cachear) {
            if (@file_exists($this->cache_link)) {
                $cachelink_time = @filemtime($this->cache_link);
                if ((time() - $cachetime) < $cachelink_time)
                {
                    @readfile($this->cache_link);
                    die();
                }
            }
            ob_start();
        }
    }
    
    public function end() {
        if($this->cachear) {
            $fp = fopen($this->cache_link, 'w');
            $web_content = ob_get_contents();
            @fwrite($fp, $web_content);
            @fclose($fp);
            ob_end_flush();
        }
    }
}

<?php

class QuiqueExceptions extends Exception {
    public $debug;
    public $title;
    public $html_css_head;
    
    public function __construct($debug, $title, $message,$code = null) {
        parent::__construct( $message, $code );
        $this->debug = $debug;
        $this->title = $title;
        $this->html_css_head = '<head>
  <meta charset="'.ENCODING.'" />
  <style>
    body { background-color: #fff; color: #333; }

    body, p, ol, ul, td {
      font-family: helvetica, verdana, arial, sans-serif;
      font-size:   13px;
      line-height: 18px;
    }

    pre {
      background-color: #eee;
      padding: 10px;
      font-size: 11px;
      white-space: pre-wrap;
    }

    a { color: #000; }
    a:visited { color: #666; }
    a:hover { color: #fff; background-color:#000; }
  </style>
</head>';
    }
    
    public function echoHTMLMessage() {
        if($this->debug) {
            echo "<html>";
            echo $this->html_css_head;
            echo "<body>";
            echo "<h1>".$this->title."</h1>";
            echo "<p>".$this->getMessage()."</p>";
            echo "</body>";
            echo "</html>";
        }
        die;
    }
}
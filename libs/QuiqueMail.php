<?php

class QuiqueMail {
    public function send($de,$para,$titulo,$mensaje,$responder_a = false) {
        if($de == "") {
            return false;
        }
        elseif($para == "") {
            return false;
        }
        elseif($titulo == "") {
            return false;
        }
        elseif($mensaje == "") {
            return false;
        }
        
        $cabeceras = 'From: '. $de . "\r\n" .
            $responder_a===false ? "" : "Reply-To: " . htmlentities($responder_a) . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        return mail(htmlentities($para), htmlentities($titulo), htmlentities($mensaje), $cabeceras);
    }
}

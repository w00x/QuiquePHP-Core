<?php
class QuiqueCaptcha {

    private $text;

    function genText() {
        $textGen = substr(md5(date('YuGs')),0,5);
        
        $this->text = $textGen;
        return $textGen;
    }

    function showPngImage() {
        if(strlen($this->text) == 0) {
            return false;
        }
        
        $image = imagecreate(100, 30);

        $background = imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127));

        $line_color_one = imagecolorallocate($image, rand(128, 255), rand(128, 255), rand(128, 255));
        imageline($image, rand(20,100), rand(5,30), rand(20,100), rand(5,30), $line_color_one);

        $line_color_two = imagecolorallocate($image, rand(128, 255), rand(128, 255), rand(128, 255));
        imageline($image, rand(20,100), rand(5,30), rand(20,100), rand(5,30), $line_color_two);

        $line_color_tree = imagecolorallocate($image, rand(128, 255), rand(128, 255), rand(128, 255));
        imageline($image, rand(20,100), rand(5,30), rand(20,100), rand(5,30), $line_color_tree);

        $line_color_four = imagecolorallocate($image, rand(128, 255), rand(128, 255), rand(128, 255));
        imageline($image, rand(20,100), rand(5,30), rand(20,100), rand(5,30), $line_color_four);

        $line_color_five = imagecolorallocate($image, rand(128, 255), rand(128, 255), rand(128, 255));
        //imageline($image, rand(20,100), rand(5,30), rand(20,100), rand(5,30), $line_color_five);

        imagestring($image, rand(3,5), 5, rand(2,12), $this->text[0], $line_color_one);
        imagestring($image, rand(3,5), 25, rand(2,12), $this->text[1], $line_color_two);
        imagestring($image, rand(3,5), 45, rand(2,12), $this->text[2], $line_color_tree);
        imagestring($image, rand(3,5), 65, rand(2,12), $this->text[3], $line_color_four);
        imagestring($image, rand(3,5), 85, rand(2,12), $this->text[4], $line_color_five);
        
        header('Content-type: image/png');

        imagepng($image);
        imagedestroy($image);
    }
}
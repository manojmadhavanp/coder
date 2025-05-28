<?php
if (session_status() === PHP_SESSION_NONE) session_start();

class CaptchaImages
{

    var $font = 'monofont.ttf';
    var $width = 120;
    var $height = 40;
    var $characters = 6;

    function generateCode()
    {
        /* list all possible characters, similar looking characters and vowels have been removed */
        $possible = '23456789ABCdEfGhIjKlMnpQrStUvWxYz';
        $code = '';
        $i = 0;
        while ($i < $this->characters) {
            $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $i++;
        }
        return $code;
    }

    function __construct()
    {
        $code = $this->generateCode();
        /* font size will be 75% of the image height */
        $font_size = $this->height * 0.75;
        $image = @imagecreate($this->width, $this->height) or die('Cannot initialize new GD image stream');
        /* set the colours */
        $background_color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 20, 40, 100);
        $noise_color = imagecolorallocate($image, 100, 120, 180);
        /* generate random dots in background */
        for ($i = 0; $i < ($this->width * $this->height) / 3; $i++) {
            imagefilledellipse($image, mt_rand(0, $this->width), mt_rand(0, $this->height), 1, 1, $noise_color);
        }
        /* generate random lines in background */
        for ($i = 0; $i < ($this->width * $this->height) / 150; $i++) {
            imageline($image, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $noise_color);
        }
        /* create textbox and add text */
        $textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
        $x = ($this->width - $textbox[4]) / 2;
        $y = ($this->height - $textbox[5]) / 2;
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font, $code) or die('Error in imagettftext function');
        imagejpeg($image);
        imagedestroy($image);
        $_SESSION['humancheckcode'] = md5($code);
        print_r($_SESSION['humancheckcode']);
    }
}


header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: image/jpeg');
$captcha = new CaptchaImages();

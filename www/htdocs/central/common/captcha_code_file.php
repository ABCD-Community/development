<?php 
/*
* Code downloaded from
* https://html.form.guide/contact-form/html-contact-form-captcha/#codedownload
* That code is based on captcha code by Simon Jarvis 
* http://www.white-hat-web-design.co.uk/articles/php-captcha.php
*
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation
*
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*
* 20240530 fho4abcd Adapted for use in ABCD
* 20240604 fho4abcd No longer use imagettfbbox. Gave problems on some hosts. Unreliable.
*/

session_start();
include("../config.php");
//Settings: You can customize the captcha here
$image_width = 150;
$image_height = 40;
$characters_on_image = 5;
// The absolute path to the font is required
// Font file is defined in function to make it also available other files
include("captcha_get_font.php");
$font=captcha_font();
//The characters that can be used in the CAPTCHA code.
//avoid confusing characters (l 1 and i for example)
$possible_letters = '23456789ABCDbcdfghjkmnprstvwxyz';
$random_dots = 20;
$random_lines = 20;
$captcha_text_color="0x142864";
$captcha_noice_color = "0x142864";

$code = '';
$i = 0;
while ($i < $characters_on_image) { 
	$code .= substr($possible_letters, random_int(0, strlen($possible_letters)-1), 1);
	$i++;
}
$font_size = $image_height * 0.75;
$image = @imagecreate($image_width, $image_height);

/* setting the background, text and noise colours here */
$background_color = imagecolorallocate($image, 255, 255, 255);

$arr_text_color = hexrgb($captcha_text_color);
$text_color = imagecolorallocate($image, $arr_text_color['red'], 
		$arr_text_color['green'], $arr_text_color['blue']);

$arr_noice_color = hexrgb($captcha_noice_color);
$image_noise_color = imagecolorallocate($image, $arr_noice_color['red'], 
		$arr_noice_color['green'], $arr_noice_color['blue']);

/* generating the dots randomly in background */
for( $i=0; $i<$random_dots; $i++ ) {
	imagefilledellipse($image, mt_rand(0,$image_width),mt_rand(0,$image_height), 2, 3, $image_noise_color);
}

/* generating lines randomly in background of image */
for( $i=0; $i<$random_lines; $i++ ) {
	imageline($image, random_int(0,$image_width), random_int(0,$image_height),random_int(0,$image_width), random_int(0,$image_height), $image_noise_color);
}
/* create a text box and add some letters code in it*/
/*$textbox = imagettfbbox($font_size, 0, $font, $code); 
$x = ($image_width - $textbox[4])/2;
$y = ($image_height - $textbox[5])/2;
*/
/* This fails for unknown reasons, so we use fixed positions as alternative*/
$x=10;
$y=30;
imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code);

/* Show captcha image in the page html page */
header('Content-Type: image/jpeg');// defining the image type to be shown in browser widow
imagejpeg($image);//showing the image
imagedestroy($image);//destroying the image instance
$_SESSION['6_letters_code'] = $code;

function hexrgb ($hexstr)
{
  $int = hexdec($hexstr);
  return array("red" => 0xFF & ($int >> 0x10),
               "green" => 0xFF & ($int >> 0x8),
               "blue" => 0xFF & $int);
}
?>
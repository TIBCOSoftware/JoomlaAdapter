<?php

define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(__FILE__));   // should point to joomla root
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();


header("Content-type: image/png");

$str = "1,2,3,4,5,6,7,8,9,a,b,c,d,f,g";
$list = explode(",", $str);
$cmax = count($list) - 1;
$verifyCode = '';
for ( $i=0; $i < 5; $i++ ){
      $randnum = mt_rand(0, $cmax);
      $verifyCode .= $list[$randnum];
}
$_SESSION['code'] = $verifyCode;

$im = imagecreate(58,28); 
$black = imagecolorallocate($im, 0,0,0);
$white = imagecolorallocate($im, 255,255,255);
$gray = imagecolorallocate($im, 200,200,200);
$red = imagecolorallocate($im, 255, 0, 0);
imagefill($im,0,0,$white); 
  

imagestring($im, 5, 10, 8, $verifyCode, $black);
  
for($i=0;$i<50;$i++)
{
     imagesetpixel($im, rand(), rand(), $black); 
     imagesetpixel($im, rand(), rand(), $red);
     imagesetpixel($im, rand(), rand(), $gray);
     imagearc($im, rand(), rand(), 20, 20, 75, 170, $black); 
     imageline($im, rand(), rand(), rand(), rand(), $red); 
}
imagepng($im);
imagedestroy($im);
?>

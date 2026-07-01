<?php
session_start();
header("Content-type:image/png");
$width=80;
$height=44;
$img=imagecreate($width,$height);
$bg=imagecolorallocate($img,255,255,255);
$code="";
$str="0123456789abcdefghijklmnopqrstuvwxyz";
for($i=0;$i<4;$i++){
    $fontColor=imagecolorallocate($img,rand(0,150),rand(0,150),rand(0,150));
    $char=$str[rand(0,strlen($str)-1)];
    $code.=$char;
    // 改用内置点阵字体，不需要arial.ttf
    imagestring($img,5,$i*18+8,10,$char,$fontColor);
}
$_SESSION['verify_code']=$code;
// 干扰线
for($i=0;$i<5;$i++){
    $lineColor=imagecolorallocate($img,rand(100,200),rand(100,200),rand(100,200));
    imageline($img,rand(0,$width),rand(0,$height),rand(0,$width),rand(0,$height),$lineColor);
}
imagepng($img);
imagedestroy($img);
?>
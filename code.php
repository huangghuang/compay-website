<?php
session_start();
// 清除缓冲区，防止图片乱码破损
ob_clean();
// 禁止浏览器缓存验证码
header("Content-type:image/png");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$width = 80;
$height = 44;
$img = imagecreate($width, $height);
// 白色背景
$bg = imagecolorallocate($img, 255, 255, 255);

$code = "";
$str = "0123456789abcdefghijklmnopqrstuvwxyz";
$strLen = strlen($str) - 1;

// 生成4位验证码
for ($i = 0; $i < 4; $i++) {
    $fontColor = imagecolorallocate($img, rand(0, 140), rand(0, 140), rand(0, 140));
    $char = $str[rand(0, $strLen)];
    $code .= $char;
    // x坐标随机偏移，不固定死板间距
    $x = $i * 18 + rand(5, 10);
    $y = rand(8, 14);
    imagestring($img, 5, $x, $y, $char, $fontColor);
}
// 存入session供提交页面校验
$_SESSION['verify_code'] = strtoupper($code);

// 5条干扰线
for ($i = 0; $i < 5; $i++) {
    $lineColor = imagecolorallocate($img, rand(80, 210), rand(80, 210), rand(80, 210));
    imageline($img, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
}
// 增加噪点，提高识别难度
for ($i = 0; $i < 80; $i++) {
    $dotColor = imagecolorallocate($img, rand(120, 220), rand(120, 220), rand(120, 220));
    imagesetpixel($img, rand(0, $width), rand(0, $height), $dotColor);
}

imagepng($img);
imagedestroy($img);
exit;
?>
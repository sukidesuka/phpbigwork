<?php
/**
 * 生成图像验证码
 * 可以通过GET方法传入width和height设置图片大小
 * 生成之后通过$_SESSION["vcode"]获取验证码
 */

$width = 150;
$height = 50;
if(isset($_GET['width'])){
    $width = $_GET['width'];
}
if(isset($_GET['height'])){
    $height = $_GET['height'];
}
$fontSize = $height / 2;
$fontFile = 'C:\\WINDOWS\\FONTS\\SIMSUN.TTC';  //字体文件位置

//随机产生一个背景颜色(暗色)
function RandomBackColor($imgSource){
    return imagecolorallocate($imgSource, mt_rand(0, 128), mt_rand(0, 128),  mt_rand(0, 128));
}

//随机产生一个颜色(亮色)
function RandomColor($imgSource){
    return imagecolorallocate($imgSource, mt_rand(100, 255), mt_rand(100, 255),  mt_rand(100, 255));
}

$img = imagecreatetruecolor($width, $height); //创建画布
imagefill($img, 0, 0, RandomBackColor($img));     //填充背景

//添加一些干扰直线
for($i = 0; $i < 3; ++ $i){
    imageline($img, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), RandomColor($img));
}

//添加一些干扰弧线
for($i = 0; $i < 3; ++ $i){
    imagearc($img, mt_rand(- $width, $width), mt_rand(- $height, $height), mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, 360), mt_rand(0, 360), RandomColor($img));
} 

//添加一些干扰点
for($i = 0; $i < 25; ++ $i){
    imagesetpixel($img, mt_rand(0,150), mt_rand(0,60), RandomColor($img));
}

//生成验证码
$codeRange = '0123456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ'; //验证码字符的取值范围
$code = '';
for($i = 0, $len = strlen($codeRange); $i < 4; ++ $i){    //循环4次，就是有四个随机的字母或者数字   
    $code .= $codeRange[mt_rand(0, $len - 1)];
}

//添加验证码到图像
$x = 10;
$dx = ($width - 10)/4;
$y = $height - ($height - $fontSize)/2;
for($i = 0; $i < 4; ++ $i){
    imagettftext($img, $fontSize, mt_rand(-15, 15), $x, $y, RandomColor($img), $fontFile, $code[$i]);
    $x += $dx;
}

session_start();
$_SESSION["vcode"] = $code;    //验证码保存到seesion中
header("Content-Type: image/png");
imagepng($img);             // 输出图像
imagedestroy($img);         // 销毁图像
?> 
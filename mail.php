<?php
session_start();
// 验证码校验
$inputCode = trim($_POST['code'] ?? '');
$trueCode = $_SESSION['verify_code'] ?? '';
if(strtoupper($inputCode) !== strtoupper($trueCode) || empty($trueCode)){
    echo "<script>alert('验证码错误，请重新输入');history.back();</script>";
    exit;
}
unset($_SESSION['verify_code']);

// 过滤恶意字符
function clearStr($s){
    return htmlspecialchars(trim($s),ENT_QUOTES);
}
$name = clearStr($_POST['username']);
$gender = clearStr($_POST['gender']);
$phone = clearStr($_POST['phone']);
$company = clearStr($_POST['company']);
$msg = clearStr($_POST['content']);

// 后端二次校验
if(!preg_match('/^[\u4e00-\u9fa5a-zA-Z0-9]{2,12}$/',$name)){
    echo "<script>alert('姓名格式错误');history.back();</script>";exit;
}
if(!preg_match('/^1[3-9]\d{9}$/',$phone)){
    echo "<script>alert('手机号不正确');history.back();</script>";exit;
}
if(mb_strlen($msg) < 5){
    echo "<script>alert('留言内容不能少于5个字');history.back();</script>";exit;
}

// 发邮件配置，改成你的接收邮箱
$toMail = "206576114@qq.com";
$title = "网站右下角弹窗客户留言";
$mailText = "
姓名：{$name}
性别：{$gender}
电话：{$phone}
公司：{$company}
留言：{$msg}
";
$header = "From: 官网留言系统 <web@nbond.cn>\r\nContent-Type:text/plain;charset=utf-8";
$res = mail($toMail,$title,$mailText,$header);

if($res){
    echo "<script>alert('提交成功，我们尽快联系您');history.back();</script>";
}else{
    echo "<script>alert('提交失败，请稍后重试');history.back();</script>";
}
?>
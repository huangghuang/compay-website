<?php
session_start();
// 引入PHPMailer核心文件（免composer引入方式）
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 简单防跨站伪造
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$host = $_SERVER['HTTP_HOST'];
if (!str_contains($referer, $host)) {
    echo "<script>alert('非法提交请求');history.back();</script>";
    exit;
}

// 验证码校验
$inputCode = trim($_POST['code'] ?? '');
$trueCode = $_SESSION['verify_code'] ?? '';
if (strtoupper($inputCode) !== strtoupper($trueCode) || empty($trueCode)) {
    echo "<script>alert('验证码错误，请重新输入');history.back();</script>";
    exit;
}
unset($_SESSION['verify_code']);

// 防频繁提交，60秒内禁止重复提交
$now = time();
$limit_time = 60;
if (!empty($_SESSION['last_submit'])) {
    if ($now - $_SESSION['last_submit'] < $limit_time) {
        echo "<script>alert('提交过于频繁，请稍后再试');history.back();</script>";
        exit;
    }
}
$_SESSION['last_submit'] = $now;

// 过滤特殊字符
function clearStr($s)
{
    return htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
}

// 获取表单数据
$name = clearStr($_POST['username'] ?? '');
$gender = clearStr($_POST['gender'] ?? '');
$phone = clearStr($_POST['phone'] ?? '');
$company = clearStr($_POST['company'] ?? '');
$msg = clearStr($_POST['content'] ?? '');

// 必填项校验
if (empty($name) || empty($phone) || empty($msg)) {
    echo "<script>alert('姓名、手机号、留言内容不能为空');history.back();</script>";
    exit;
}

// 格式校验
if (!preg_match('/^[\u4e00-\u9fa5a-zA-Z0-9]{2,12}$/', $name)) {
    echo "<script>alert('姓名格式错误，2-12位中英文数字');history.back();</script>";
    exit;
}
if (!preg_match('/^1[3-9]\d{9}$/', $phone)) {
    echo "<script>alert('手机号格式不正确');history.back();</script>";
    exit;
}
if (mb_strlen($msg) < 5) {
    echo "<script>alert('留言内容不能少于5个字');history.back();</script>";
    exit;
}

// ===================== SMTP邮箱配置（修改这里）=====================
$smtp_host = 'smtp.qq.com';        // QQ邮箱SMTP服务器
$smtp_port = 465;                   // SSL端口
$smtp_secure = 'ssl';
$smtp_user = '206576114@qq.com';
$smtp_pwd = 'QQ邮箱授权码';         // 不是QQ密码，网页端开启POP3获取
$from_name = '官网留言系统';
$to_email = '206576114@qq.com';     // 接收留言的邮箱
$mail_title = '网站右下角弹窗客户留言';
// =================================================================

// 组装邮件内容
$mail_body = "
客户留言信息
提交时间：" . date('Y-m-d H:i:s') . "
姓名：{$name}
性别：{$gender}
电话：{$phone}
公司：{$company}
留言内容：{$msg}
";

try {
    // 实例化PHPMailer
    $mail = new PHPMailer(true);
    // 关闭调试（上线设为false，测试可true查看报错）
    $mail->SMTPDebug = false;
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_user;
    $mail->Password = $smtp_pwd;
    $mail->SMTPSecure = $smtp_secure;
    $mail->Port = $smtp_port;
    $mail->CharSet = 'UTF-8';

    // 发件人、收件人
    $mail->setFrom($smtp_user, $from_name);
    $mail->addAddress($to_email);

    // 邮件内容
    $mail->isHTML(false); // 纯文本邮件，true为HTML格式
    $mail->Subject = $mail_title;
    $mail->Body = $mail_body;

    // 发送
    $mail->send();
    // 成功跳转，防止重复提交
    echo "<script>alert('提交成功，我们会尽快联系您');location.href='index.html';</script>";
} catch (Exception $e) {
    // 失败输出错误信息，方便排查
    $err = $mail->ErrorInfo;
    echo "<script>alert('提交失败：{$err}');history.back();</script>";
}
exit;
?>
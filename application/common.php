<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

/**
 * 公共函数库
 */



/**
 * 返回正常状态
 * @param  string  $msg  [description]
 * @param  string  $url  [description]
 * @param  array   $data [description]
 * @param  integer $code [description]
 * @return [type]        [description]
 */
function success($msg = '', $url = null, $data = null, $code = 1) {
	return ['msg' => $msg, 'url' => $url, 'data' => $data, 'code' => $code];
}

/**
 * 返回错误状态
 * @param  string  $msg  [description]
 * @param  string  $url  [description]
 * @param  array   $data [description]
 * @param  integer $code [description]
 * @return [type]        [description]
 */
function error($msg = '', $url = null, $data = null, $code = 0) {
	return ['msg' => $msg, 'url' => $url, 'data' => $data, 'code' => $code];
}


function sendEmail($to = '' , $title = '' , $content = '') {
	$mail = new \email\PHPMailer();
	$mail->IsSMTP(); // 启用SMTP
    $mail->Host=config('mail_host');
    $mail->SMTPAuth = config('mail_smtpauth'); 
    $mail->Username = config('mail_username'); 
    $mail->Password = config('mail_password');
    $mail->From = config('mail_from');
    $mail->FromName = config('mail_fromname'); 
    $mail->AddAddress($to,"尊敬的客户");
    $mail->WordWrap = 50; 
    $mail->IsHTML(config('mail_ishtml'));
    $mail->CharSet=config('mail_charset');
    $mail->Subject =$title;
    $mail->Body = $content;
    $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端";
    return($mail->Send());
}
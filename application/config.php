<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

return [
    'url_route_on' => true,
    'log'          => [
        'type' => 'trace', // 支持 socket trace file
    ],
    'mail_host' 	=> 'smtp.163.com',//smtp服务器的名称
    'mail_smtpauth' => TRUE, //启用smtp认证
    'mail_username' => '18227593863@163.com',//你的邮箱名
    'mail_from' 	=> '18227593863@163.com',//发件人地址
    'mail_fromname'	=> '创业365测试邮件账户',//发件人姓名
    'mail_password' => 'lZ126520',//邮箱密码
    'mail_charset'	=> 'utf-8',//设置邮件编码
    'mail_ishtml' 	=> TRUE, // 是否HTML格式邮件
];

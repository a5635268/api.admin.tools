<?php
/**
 * Created by PhpStorm.
 * User: CTDJ1803111
 * Date: 2018/12/4
 * Time: 13:54
 */

namespace libs;

class Mail
{
    protected static $username = 'loongcent@163.com';
    protected static $password = 'loongcent2017';

    /**
     * @access public
     * @param $subject 邮件标题
     * @param $body 邮件内容
     * @param $sendTo 收件人对象 array
     * @param array $sendFrom 发件人 array
     * @return int
     */
    public static function sendMail($subject,$body,$sendTo,$sendFrom=['loongcent@163.com'=>'泰客空间']){
        //require_once ('./../vendor/swiftmailer/swiftmailer/lib/swift_required.php');
        $transport = (new \Swift_SmtpTransport('smtp.163.com', 25))
            ->setUsername(self::$username)
            ->setPassword(self::$password);
        // 创建mailer对象
        $mailer = new \Swift_Mailer($transport);
        // 创建message对象
        $message = new \Swift_Message();
        // 设置邮件主题
        $message->setSubject($subject)
            // 设置邮件内容,可以省略content-type
            ->setBody($body, 'text/html');
        // 用关联数组设置收件人地址，可以设置多个收件人
        $message->setTo($sendTo);
        //用关联数组设置发件人地址，可以设置多个发件人
        $message->setFrom($sendFrom);
        // 发送邮件
        $result = $mailer->send($message);
        return $result;
    }

}
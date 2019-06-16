<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019\6\13 0013
 * Time: 6:25
 */
namespace app\common\lib;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use think\facade\Env;
use think\facade\Log;

class MailUnit
{

    /**
     * @param $to 收件人 ，多人接受以数组形式传递
     * @param $subject 接收主题
     * @param $message 接收内容
     * @param $attachment 附件
     * @return string
     */
    public static function sendMail($toArr, $subject, $message, $attachment = [])
    {
        if (!is_array($toArr)) {
            $toArr = (array)$toArr;
        }
        /************** 发邮件的配置 ***************/

        $mailConfig = [
            'MAIL_ADDRESS'       => Env::get('MAIL_ADDRESS'),   // 发件人的email
            'MAIL_FROM_NICKNAME' => Env::get('MAIL_FROM_NICKNAME'), // 发件人姓名
            'MAIL_HOST'          => Env::get('MAIL_HOST'),      // 邮件服务器的地址
            'MAIL_USER_NAME'     => Env::get('MAIL_USER_NAME'), //邮箱登录名
            'MAIL_PASSWORD'      => Env::get('MAIL_PASSWORD'), //授权码，非登录密码
        ];

        $mail = new PHPMailer(true);
        try {
            //服务器配置
            $mail->CharSet    = "UTF-8";
            $mail->SMTPDebug  = 0;   // 是否开启调试模式
            $mail->isSMTP();         // Set mailer to use SMTP
            $mail->Host       = $mailConfig['MAIL_HOST'];  // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;      // Enable SMTP authentication
            $mail->Username   = $mailConfig['MAIL_USER_NAME'];  // SMTP username
            $mail->Password   = $mailConfig['MAIL_PASSWORD'];   // SMTP password
            $mail->SMTPSecure = 'ssl';    // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = 465;      // TCP port to connect to

            $mail->setFrom($mailConfig['MAIL_ADDRESS'], $mailConfig['MAIL_FROM_NICKNAME']);
            foreach ($toArr as $to) {
                $mail->addAddress($to);               // 收件人邮箱
            }
            $mail->addReplyTo($mailConfig['MAIL_ADDRESS'], $mailConfig['MAIL_FROM_NICKNAME']); // 回复人，一般和发件人一致，可自定义
            $mail->addCC('13866297830@163.com'); //抄送人
//            $mail->addBCC('bcc@example.com');
            // 附件
            if (!is_array($attachment)) {
                $attachment = (array)$attachment;
            }
            if (!empty($attachment)) {
                foreach ($attachment as $value) {
                    $mail->addAttachment($value);         // 附件地址，绝对位置
//                    $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                }
            }
            $mail->isHTML(true);      // 以 html格式发出
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = 'change your email'; // 不接受html格式时，显示的文字，一般不会出现这种情况
            $mail->send();
            return true;
        } catch (Exception $e) {
            Log::debug('email_send_error: ' . $mail->ErrorInfo );
            return false;
        }
    }

}
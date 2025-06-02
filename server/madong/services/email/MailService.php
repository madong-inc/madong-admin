<?php

namespace madong\services\email;

use app\common\services\system\SystemConfigService;
use madong\exception\AdminException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use support\Container;

/**
 * 邮件服务
 *
 * @author Mr.April
 * @since  1.0
 */
class MailService
{

    /**
     * config表的group_cod字段值
     */
    const SETTING_GROUP_CODE = 'email_setting';

    protected ?PHPMailer $mailer;

    public function __construct($host = null, $username = null, $password = null, $port = null, $encryption = null)
    {
        if (!extension_loaded('openssl')) {
            throw new \RuntimeException('PHP OpenSSL extension is not installed or enabled.');
        }
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            throw new \RuntimeException('请执行 composer require phpmailer/phpmailer');
        }
        $this->mailer = new PHPMailer(true); // 使用异常处理
        $this->configure($host, $username, $password, $port, $encryption);
    }

    protected function configure(?string $host = null, ?string $username = null, ?string $password = null, ?string $port = null, ?string $encryption = null): void
    {
        $systemConfigService = Container::make(SystemConfigService::class);
        $config              = $systemConfigService->getConfigContentValue(self::SETTING_GROUP_CODE);
        $this->mailer->isSMTP(); // 使用 SMTP
        $this->mailer->Host       = $host ?? $config['Host']; // 默认 SMTP 服务器地址
        $this->mailer->SMTPAuth   = true; // 启用 SMTP 身份验证
        $this->mailer->Username   = $username ?? $config['Username']; // 默认 SMTP 用户名
        $this->mailer->Password   = $password ?? $config['Password']; // 默认 SMTP 密码
        $this->mailer->SMTPSecure = $encryption ?? $config['SMTPSecure']; // 默认加密方式
        $this->mailer->Port       = $port ?? $config['Port']; // 默认 TCP 端口号
    }

    /**
     * @param string $from    发件人
     * @param string $to      收件人
     * @param string $subject 主题
     * @param string $content 内容
     *
     * @return true|array
     */
    public function send(string $from, string $to, string $subject, string $content): true|array
    {
        try {
            // 设置发件人
            $this->mailer->setFrom($from);

            // 收件人
            $this->mailer->addAddress($to);

            // 邮件内容
            $this->mailer->isHTML(true); // 设置邮件格式为 HTML
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $content;

            // 发送邮件
            $this->mailer->send();
            return true; // 发送成功
        } catch (Exception $e) {
            return throw new AdminException($e->getMessage());// 发送失败，返回错误信息
        }
    }
}

<?php

namespace Hunter\queue\Plugin\Job;

use Hunter\queue\Annotation\QueueJob;
use Fwolf\Wrapper\PHPMailer\PHPMailer;

/**
 * @QueueJob(
 *   id = "send_mail",
 *   title = "Mail Job",
 *   type = "queue_job"
 * )
 */
class SendMail {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPSecure = 'tls';
    $mail->Host = variable_get('smtp_host');
    $mail->SMTPAuth = true;
    $mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent, reduces SMTP overhead
    $mail->Port = variable_get('smtp_port');
    $mail->Username = variable_get('smtp_username');
    $mail->Password = variable_get('smtp_password');
    $mail->setFrom(variable_get('smtp_username'), 'DrupalHunter');
    $mail->addReplyTo(variable_get('smtp_username'), 'DrupalHunter');
    $mail->Subject = $data['title'];
    $mail->msgHTML($data['content']);
    $msg = '';

    $mail->addAddress($data['send_to'], $data['send_to']);
    if (!$mail->send()) {
        $msg = 'send error';
    } else {
        $msg = 'send success';
    }
    // Clear all addresses and attachments for next loop
    $mail->clearAddresses();
    $mail->clearAttachments();

    return $msg;
  }

}

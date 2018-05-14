<?php

    namespace yiitk\swiftmailer;

    use yiitk\jobs\MailerJob;

    /**
     * Class Mailer
     *
     * @package yiitk\swiftmailer
     */
    class QueuedMailer extends \yii\swiftmailer\Mailer
    {
        /**
         * {@inheritdoc}
         */
        protected function sendMessage($message)
        {
            /** @var \yii\swiftmailer\Message $message */
            $address = $message->getTo();

            if (is_array($address)) {
                $address = implode(', ', array_keys($address));
            }

            \Yii::info('Sending email "'.$message->getSubject().'" to "'.$address.'"', __METHOD__);

            /** @var \yii\queue\file\Queue $queue */
            $queue = \Yii::$app->get('queue');

            $queue->push(new MailerJob(['mailer' => $this, 'message' => $message]));

            return true;
        }
    }

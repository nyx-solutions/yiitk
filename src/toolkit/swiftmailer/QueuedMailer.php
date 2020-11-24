<?php

    namespace yiitk\swiftmailer;

    use Yii;
    use yii\queue\file\Queue;
    use yii\swiftmailer\Mailer;
    use yii\swiftmailer\Message;
    use yiitk\jobs\MailerJob;

    /**
     * Class Mailer
     *
     * @package yiitk\swiftmailer
     */
    class QueuedMailer extends Mailer
    {
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        protected function sendMessage($message)
        {
            /** @var Message $message */
            $address = $message->getTo();

            if (is_array($address)) {
                $address = implode(', ', array_keys($address));
            }

            Yii::info('Sending email "'.$message->getSubject().'" to "'.$address.'"', __METHOD__);

            /** @var Queue $queue */
            $queue = Yii::$app->get('queue');

            $queue->push(new MailerJob(['mailer' => $this, 'message' => $message]));

            return true;
        }
    }

<?php

    namespace yiitk\jobs;

    use yii\swiftmailer\Message;
    use yiitk\swiftmailer\QueuedMailer;

    /**
     * Class MailerJob.
     *
     * @property integer $ttr
     */
    class MailerJob extends \yii\base\BaseObject implements \yii\queue\RetryableJobInterface
    {
        /**
         * @var QueuedMailer
         */
        public $mailer;

        /**
         * @var Message
         */
        public $message;

        /**
         * {@inheritdoc}
         */
        public function execute($queue)
        {
            if ($this->mailer instanceof QueuedMailer) {
                if (!($this->mailer->getSwiftMailer()->send($this->message->getSwiftMessage())) > 0) {
                    throw new \Exception(\Yii::t('yiitk', 'The system could not send the e-mail message.'));
                }
            }
        }

        /**
         * {@inheritdoc}
         */
        public function getTtr()
        {
            return 60;
        }

        /**
         * {@inheritdoc}
         */
        public function canRetry($attempt, $error)
        {
            return ($attempt < 5);
        }
    }

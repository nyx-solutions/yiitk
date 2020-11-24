<?php

    namespace yiitk\jobs;

    use Yii;
    use yii\base\BaseObject;
    use yii\base\Exception as YiiException;
    use yii\queue\RetryableJobInterface;
    use yii\swiftmailer\Message;
    use yiitk\swiftmailer\QueuedMailer;

    /**
     * Class MailerJob.
     *
     * @property int $ttr
     */
    class MailerJob extends BaseObject implements RetryableJobInterface
    {
        /**
         * @var QueuedMailer|null
         */
        public ?QueuedMailer $mailer = null;

        /**
         * @var Message|null
         */
        public ?Message $message = null;

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function execute($queue)
        {
            if ($this->mailer instanceof QueuedMailer) {
                $sended = (int)$this->mailer->getSwiftMailer()->send($this->message->getSwiftMessage());

                if ($sended <= 0) {
                    throw new YiiException(Yii::t('yiitk', 'The system could not send the e-mail message.'));
                }
            }
        }

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function getTtr()
        {
            return 60;
        }

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function canRetry($attempt, $error)
        {
            return ($attempt < 5);
        }
    }

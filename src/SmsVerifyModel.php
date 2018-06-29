<?php
/**
 * Created by Cartman Chen <thrall.chen@gmail.com>.
 * Author: 陈章--大官人
 * Github: https://github.com/imdgr
 */

namespace imdgr\sms_verify;

use yii\db\ActiveRecord;
use yii2stack\validators\MobileValidator;

class SmsVerifyModel extends ActiveRecord
{
    /**
     * @var int 发送间隔
     */
    public $interval = 60;

    /**
     * @var int 一小时内最大发送量
     */
    public $hourMaxLimit = 5;

    /**
     * @return int 一天最大发送量
     */
    public $dayMaxLimit = 10;

    /**
     * @var int 验证码有效时间(秒）
     */
    public $expires = 300;

    public static function tableName()
    {
        return "{{%sms_verify_code}}";
    }

    public function rules()
    {
        return [
            ['mobile', 'required'],
            ['scenario', 'string'],
            ['mobile', MobileValidator::class],
            ['code', 'default', 'value' => mt_rand(100000, 999999)],
            ['sent_time', 'default', 'value' => time()],
            ['ip', 'ip'],
            ['ip', 'default', 'value' => \Yii::$app->request->getUserIP()],
            ['mobile', 'check'],
            ['valid', 'boolean'],
            ['success', 'boolean'],
        ];
    }

    public function check()
    {
        //与上次发送短信间隔是否超出限制
        $last = $this->getLastRecord($this->mobile, $this->scenario);
        if ($last && $last->success) {
            if ($last->sent_time + $this->interval > time()) {
                $this->addError('mobile', '发送太频繁，请稍候重试!');
                return;
            }
        }

        //一小时内发送数量是否超出限制
        if ($this->hourMaxLimit) {
            $count = $this->find()->where(['<=', 'sent_time', time()-3600])->andWhere(['success' => 1])->count();
            if ($count >= $this->hourMaxLimit) {
                $this->addError('mobile', '一小时内发送数量超出限制，请稍候重试!');
                return;
            }
        }

        //一天内发送数量是否超出限制
        if ($this->hourMaxLimit) {
            $count = $this->find()->where(['<=', 'sent_time',  strtotime(date('Y-m-d'))])->andWhere(['success' => 1])->count();
            if ($count >= $this->hourMaxLimit) {
                $this->addError('mobile', '当天发送数量超出限制，请稍候重试!');
                return;
            }
        }

    }

    public function getLastRecord($mobile, $scenario = '')
    {
        return $this->findOne(['mobile' => $mobile, 'scenario' => $scenario]);
    }

    public function isCodeValidate($mobile, $code, $scenario = '')
    {
        $record = $this->findOne(compact('mobile', 'code', 'scenario'));
        if ($record && $record->sent_time + $this->expires >= time()) {
            return true;
        }
        return false;
    }
}

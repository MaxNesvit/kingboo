<?php
namespace partner\models;

use common\models\PayMethod;
use common\models\User;
use Yii;

/**
 * Class PartnerUser
 * @package partner\models
 *
 * @property string $shopId
 * @property string $scid
 * @property string $shopPassword
 *
 * @property integer $allow_checkin_full_pay
 * @property integer $allow_payment_via_bank_transfer
 *
 * @property boolean $private_person
 * @property string  $company_name
 * @property string  $phone
 * @property string  $prima_login
 * @property string  $demo_expire
 * 
 * @property text  $system_info
 * @property decimal $currency_exchange_percent
 * @property text $payment_details
 */
class PartnerUser extends User
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partner_user}}';
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            foreach ($this->hotels as $hotel) {
                $hotel->delete();
            };
        }
        return true;
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        // Сигнал для системы сообщений
        if (isset(\Yii::$app->automaticSystemMessages)) {
            \Yii::$app->automaticSystemMessages->setDataUpdated();
        }
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = static::findByEmail($this->email);
        }

        return $this->_user;
    }

    public static function findByEmail($email)
    {
        return static::findOne([
            'email' => $email,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public function getHotels()
    {
        return $this->hasMany('\common\models\Hotel', ['partner_id' => 'id']);
    }

    public function getPayMethods() {
        return $this->hasMany(PayMethod::className(), ['id' => 'pay_method_id'])
            ->viaTable('partner_payMethods', ['partner_id' => 'id']);
    }

    public function sendConfirmEmail() {
        \Yii::$app->mailer->compose(
            [
                'html' => 'partnerConfirmEmail-html.php',
                'text' => 'partnerConfirmEmail-text.php',
            ], [
                'link' => 'https://partner.king-boo.com/confirm-email?code=' . md5($this->password_hash.$this->created_at),
                'resendCode' => 'https://partner.king-boo.com/confirm-email?code=' . md5($this->email,$this->password_hash),
            ]
        )
            ->setFrom(\Yii::$app->params['email.from'])
            ->setTo($this->email)
            ->setSubject(\Yii::t('partner_login', 'Email confirmation for site partner.king-boo.com'))
            ->send();
    }

    /**
     * Возвращет true если пробный период закончен. False в противном случае
     * @return int
     */
    public function getDemoExpired() {
        return (new \DateTime())->diff(new \DateTime($this->demo_expire))->invert;
    }

    /**
     * Возвращает количество дней между сегодняшней датой и датой истечения пробного периода
     * false - если срок прошел
     * @return mixed
     */
    public function getDemoLeft() {
        return !$this->getDemoExpired() ? (new \DateTime())->diff(new \DateTime($this->demo_expire))->days : false;
    }
}
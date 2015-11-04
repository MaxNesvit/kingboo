<?php

namespace common\models;

use Faker\Provider\cs_CZ\DateTime;
use partner\models\PartnerUser;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $number
 * @property integer $status
 * @property string $contact_email
 * @property string $contact_phone
 * @property string $contact_name
 * @property string $contact_surname
 * @property integer $contact_address
 * @property string $dateFrom
 * @property string $dateTo
 * @property double $sum
 * @property integer $sum_currency_id
 * @property integer $partial_pay
 * @property integer $partial_pay_percent
 * @property double $pay_sum
 * @property integer $pay_sum_currency_id
 * @property double $payment_system_sum
 * @property integer $payment_system_sum_currency_id
 * @property integer $hotel_id
 * @property string $lang
 * @property boolean $viewed
 * @property string $payment_url
 * @property string $partner_number
 * @property string $additional_info
 * @property integer $checkin_fullpay
 * @property integer $payment_via_bank_transfer
 */
class Order extends ActiveRecord
{
    const OS_CANCELED = 0; // аннулирован
    const OS_WAITING_PAY = 1; // ожидает оплаты
    const OS_PAYED = 2; // оплачен
    const OS_CHECKIN_FULLPAY = 3; // оплачен

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    public static function getOrderStatusTitle($id)
    {
        switch ($id) {
            case 0:
                return \Yii::t('orders', 'cancelled');
            case 1:
                return \Yii::t('orders', 'waiting for pay');
            case 2:
                return \Yii::t('orders', 'payed');
            case 3:
                return \Yii::t('orders', 'checkin full pay');
        }
    }

    static public function findNew()
    {
        return static::find()
            ->joinWith('hotel')
            ->where(['hotel.partner_id' => \Yii::$app->user->id, 'viewed' => '0'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['code', 'match', 'pattern' => '/^[a-zA-Z0-9\-+_!]+$/'],
            [['dateFrom', 'dateTo', 'code'], 'safe'],
            [['number', 'status', 'contact_email', 'contact_name', 'contact_surname', 'contact_address', 'dateFrom', 'dateTo', 'sum', 'partial_pay_percent', 'pay_sum', 'sum_currency_id', 'pay_sum_currency_id'], 'required'],
            [['status', 'contact_address', 'partial_pay', 'partial_pay_percent', 'hotel_id', 'checkin_fullpay', 'payment_via_bank_transfer', 'sum_currency_id', 'pay_sum_currency_id', 'payment_system_sum_currency_id' ], 'integer'],
            [['sum', 'pay_sum', 'payment_system_sum'], 'number'],
            [['viewed'], 'boolean'],
            [['number'], 'string', 'max' => 12, 'min' => 5],
            [['partner_number'], 'string'],
            [['number'], 'unique'],
            [['lang'], 'string', 'max' => 3],
            [['contact_email', 'contact_phone', 'contact_name', 'contact_surname'], 'string', 'max' => 255],
            ['additional_info', 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('orders', 'ID'),
            'created_at' => Yii::t('orders', 'Created At'),
            'updated_at' => Yii::t('orders', 'Updated At'),
            'number' => Yii::t('orders', 'Number'),
            'status' => Yii::t('orders', 'Status'),
            'contact_email' => Yii::t('orders', 'Email'),
            'contact_phone' => Yii::t('orders', 'Phone'),
            'contact_name' => Yii::t('orders', 'Name'),
            'contact_surname' => Yii::t('orders', 'Surname'),
            'contact_address' => Yii::t('orders', 'Address'),
            'dateFrom' => Yii::t('orders', 'Date From'),
            'dateTo' => Yii::t('orders', 'Date To'),
            'sum' => Yii::t('orders', 'Sum'),
            'partial_pay' => Yii::t('orders', 'Partial Pay'),
            'partial_pay_percent' => Yii::t('orders', 'Partial Pay Percent'),
            'pay_sum' => Yii::t('orders', 'Pay Sum'),
            'code' => \Yii::t('orders', 'Promo code'),
            'checkin_fullpay' => \Yii::t('orders', 'Full payment at checkin'),
            'payment_via_bank_transfer' => \Yii::t('orders', 'Payment via bank transfer'),
            'additional_info' => \Yii::t('orders', 'Additional information'),
            'sum_currency_id' => \Yii::t('orders', 'Currency ID'),
            'pay_sum_currency_id' => \Yii::t('orders', 'Pay sum currency ID'),
            'payment_system_sum' => \Yii::t('orders', 'Sum for payment system'),
            'payment_system_sum_currency_id' => \Yii::t('orders', 'Payment system sum currency ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttrs)
    {
        // проверяем изменение статуса уже созданого заказа
        if (!$insert && array_key_exists('status', $changedAttrs)) {
            switch ($this->status) {
                // если заказ был отменен - возвращаем его в номерной фонд за весь срок бронирования
                case static::OS_CANCELED:
                    foreach ($this->orderItems as $orderItem) {
                        /** @var OrderItem $orderItem */
                        $roomAvailabilityArray = RoomAvailability::find()
                            ->where(['>=', 'date', $this->dateFrom])
                            ->andWhere(['<', 'date', $this->dateTo])
                            ->andWhere([
                                'room_id' => $orderItem->room_id,
                            ])->all();
                        if (is_array($roomAvailabilityArray)) {
                            foreach ($roomAvailabilityArray as $ra) {
                                $ra->count = $ra->count + 1;
                                $ra->save(false);
                            }
                        }
                    }
                    break;
            }
        }

        // отправка писем о совершении заказа
        if ($insert) {
            $this->orderCreated();
        }

        if (!$insert && array_key_exists('status', $changedAttrs)) {
            $this->orderStatusChanged(['status' => ArrayHelper::getValue($changedAttrs, 'status')]);
        }
    }

    public function orderCreated()
    {
//        $this->sendEmailToClient();
//        $this->sendEmailToPartner();
    }

    public function sendEmailToClient()
    {
        \Yii::$app->mailer->compose([
            'html' => 'orderCreatedToClient-html',
            'text' => 'orderCreatedToClient-text',
        ], [
            'order' => $this,
            'lang' => $this->lang,
            'local' => Lang::findOne(['url' => $this->lang])->local,
        ])
            ->setFrom(\Yii::$app->params['email.from'])
            ->setTo([$this->contact_email => $this->contact_name . ' ' . $this->contact_surname])
            ->setSubject(\Yii::t('mails_order', 'Order on site king-boo.com'))
            ->send();
    }

    public function sendEmailToPartner()
    {
        $local = Lang::findOne(['url' => $this->hotel->partner->lang])->local;
        \Yii::$app->mailer->compose([
            'html' => 'orderCreatedToPartner-html',
            'text' => 'orderCreatedToPartner-text',
        ], [
            'order' => $this,
            'lang' => $this->hotel->partner->lang,
            'local' => $local,
        ])
            ->setFrom(\Yii::$app->params['email.from'])
            ->setTo($this->hotel->partner->email)
            ->setSubject(\Yii::t('mails_order', 'New order on site king-boo.com', [], $local))
            ->send();
    }

    public function orderStatusChanged($params = [])
    {
        $local = Lang::findOne(['url' => $this->lang])->local;
        // отправляем письмо клиенту
        \Yii::$app->mailer->compose('orderStatusChangedClient-html', [
            'oldStatus' => ArrayHelper::getValue($params, 'status'),
            'order' => $this,
            'lang' => $this->lang,
            'local' => $local,
        ])
            ->setFrom(\Yii::$app->params['email.from'])
            ->setTo([$this->contact_email => $this->contact_name . ' ' . $this->contact_surname])
            ->setSubject(\Yii::t('mails_order', 'Your order\'s #{n} status was changed', ['n' => $this->number], $local))
            ->send();

        $local = Lang::findOne(['url' => $this->hotel->partner->lang])->local;
        // отправляем письмо партнёру
        \Yii::$app->mailer->compose('orderStatusChangedPartner-html', [
            'oldStatus' => ArrayHelper::getValue($params, 'status'),
            'order' => $this,
            'lang' => $this->hotel->partner->lang,
            'local' => $local,
        ])
            ->setFrom(\Yii::$app->params['email.from'])
            ->setTo([$this->hotel->partner->email])
            ->setSubject(\Yii::t('mails_order', 'Order\'s #{n} status was changed', ['n' => $this->partner_number], $local))
            ->send();
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // поля created_at и updated_at
            if ($insert) {
                // генерация номра для партнера
                $this->generateOrderNumber();

                // При создании устанавливаем payment_url
                // по которому будет доступен платеж
                $this->payment_url = \Yii::$app->security->generateRandomString(64);

                // Номер
                $this->created_at = date('Y-m-d H:i:s');
                $this->updated_at = date('Y-m-d H:i:s');

            } else {
                $this->updated_at = date('Y-m-d H:i:s');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Генерирует номер заказа с внутренней нумерацией партнера
     */
    public function generateOrderNumber()
    {
        /** @var PartnerUser $partner_user */
        $partner_user = PartnerUser::findOne($this->hotel->partner_id);
        $this->partner_number = $partner_user->counter + 100000;
        $partner_user->counter++;
        $partner_user->save();
        return $this;
    }

    /**
     * Генерирует номер заказа. Формат: 8 цифр шеснадцитиричное число + '-' и последние 2 цифры года
     * 
     * @param string $str - произвольная строка для повышения уникальности
     * @return string
     */
    public static function generateNumber($str='') 
    {
        do {
            $number = hash("crc32b", \Yii::$app->getSecurity()->generateRandomString(15) . $str) . '-' . date('y');
            $order = self::findOne(['number' => $number]);
        } while (!is_null($order));
        return $number;
    }

    /**
     * Связь с элементами заказа
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::className(), ['order_id' => 'id']);
    }

    /**
     * Связь с отелем
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHotel()
    {
        return $this->hasOne(Hotel::className(), ['id' => 'hotel_id']);
    }

    public function getSumText()
    {
        if ($currency = $this->orderItems[0]->room->hotel->currency) {
            return $currency->getFormatted($this->sum);
        } else {
            return $this->sum;
        }
    }

    public function getNights()
    {
        return (new \DateTime($this->dateFrom))->diff((new \DateTime($this->dateTo)))->days;
    }

    public function getSumCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'sum_currency_id']);
    }

    public function getPaymentSystemSumCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'payment_system_sum_currency_id']);
    }


}

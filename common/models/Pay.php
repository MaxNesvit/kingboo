<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%pays}}".
 *
 * @property integer $id
 * @property integer $checked
 * @property integer $payed
 * @property integer $order_number
 * @property integer $invoiceId
 * @property integer $customerNumber
 * @property string $orderCreatedDatetime
 * @property string $paymentDatetime
 * @property double $orderSumAmount
 * @property integer $orderSumCurrencyPaycash
 * @property integer $orderSumBankPaycash
 * @property double $shopSumAmount
 * @property integer $shopSumCurrencyPaycash
 * @property integer $shopSumBankPaycash
 * @property string $paymentPayerCode
 * @property integer $paymentType
 * @property string $postParams
 */
class Pay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pays}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['checked', 'payed', 'order_number', 'invoiceId', 'customerNumber', 'orderSumCurrencyPaycash', 'orderSumBankPaycash', 'shopSumCurrencyPaycash', 'shopSumBankPaycash', 'paymentType'], 'integer'],
            [['order_number', 'invoiceId', 'customerNumber', 'orderCreatedDatetime', 'paymentDatetime', 'orderSumAmount', 'orderSumCurrencyPaycash', 'orderSumBankPaycash', 'shopSumAmount', 'shopSumCurrencyPaycash', 'shopSumBankPaycash', 'paymentPayerCode', 'paymentType'], 'required'],
            [['orderCreatedDatetime', 'paymentDatetime'], 'safe'],
            [['orderSumAmount', 'shopSumAmount'], 'number'],
            [['postParams'], 'string'],
            [['paymentPayerCode'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('orders', 'ID'),
            'checked' => Yii::t('orders', 'Checked'),
            'payed' => Yii::t('orders', 'Payed'),
            'order_number' => Yii::t('orders', 'Order Number'),
            'invoiceId' => Yii::t('orders', 'Invoice ID'),
            'customerNumber' => Yii::t('orders', 'Customer Number'),
            'orderCreatedDatetime' => Yii::t('orders', 'Order Created Datetime'),
            'paymentDatetime' => Yii::t('orders', 'Payment Datetime'),
            'orderSumAmount' => Yii::t('orders', 'Order Sum Amount'),
            'orderSumCurrencyPaycash' => Yii::t('orders', 'Order Sum Currency Paycash'),
            'orderSumBankPaycash' => Yii::t('orders', 'Order Sum Bank Paycash'),
            'shopSumAmount' => Yii::t('orders', 'Shop Sum Amount'),
            'shopSumCurrencyPaycash' => Yii::t('orders', 'Shop Sum Currency Paycash'),
            'shopSumBankPaycash' => Yii::t('orders', 'Shop Sum Bank Paycash'),
            'paymentPayerCode' => Yii::t('orders', 'Payment Payer Code'),
            'paymentType' => Yii::t('orders', 'Payment Type'),
            'postParams' => Yii::t('orders', 'Post Params'),
        ];
    }
}

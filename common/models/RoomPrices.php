<?php
// TODO: Сделать stop booking на определенную дату
namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%room_prices}}".
 *
 * @property integer $id
 * @property string $date
 * @property integer $room_id
 * @property integer $adults
 * @property integer $children
 * @property integer $kids
 * @property string $price
 * @property integer $price_currency
 */
class RoomPrices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%room_prices}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'room_id', 'adults', 'children', 'kids', 'price_currency'], 'required'],
            [['date'], 'safe'],
            [['room_id', 'adults', 'children', 'kids', 'price_currency'], 'integer'],
            [['price'], 'number'],
            [['date', 'room_id', 'adults', 'children', 'kids'], 'unique', 'targetAttribute' => ['date', 'room_id', 'adults', 'children', 'kids']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('room_prices', 'ID'),
            'date' => Yii::t('room_prices', 'Date'),
            'room_id' => Yii::t('room_prices', 'Room ID'),
            'adults' => Yii::t('room_prices', 'Adults'),
            'children' => Yii::t('room_prices', 'Children'),
            'kids' => Yii::t('room_prices', 'Kids'),
            'price' => Yii::t('room_prices', 'Price'),
            'price_currency' => Yii::t('room_prices', 'Price Currency'),
        ];
    }
    
    public function getRoom() {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }
    
    public function getCurrency() {
        return $this->hasOne(Currency::className(), ['id' => 'price_currency']);
    }

    public function afterDelete() {
        parent::afterDelete();
        // Сигнал для системы сообщений
        //\Yii::$app->automaticSystemMessages->setDataUpdated();
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            // Сигнал для системы сообщений
            //\Yii::$app->automaticSystemMessages->setDataUpdated();
        }
    }

    public static function groupDelete($room, $startDate, $endDate, $adults, $children, $kids) {
        $where = [ 'and',
            ['room_id' => $room->id],
            ['>=', 'date', $startDate],
            ['<=', 'date', $endDate],
            ['adults' => $adults],
            ['children' => $children],
            ['kids' => $kids],
        ];
        \Yii::$app->db->createCommand()->delete(self::tableName(), $where)->execute();
        // Сигнал для системы сообщений
        \Yii::$app->automaticSystemMessages->setDataUpdated();
    }

    public static function groupInsert($room, $startDate, $endDate, $adults, $children, $kids, $price) {
        $prices = [];
        $date = \DateTime::createFromFormat('Y-m-d', $startDate);
        $to = \DateTime::createFromFormat('Y-m-d', $endDate); 
        $currency = $room->hotel->currency->id;  
        while ($date <= $to) {
            $prices[] = [$date->format('Y-m-d'), $room->id, $adults, $children, $kids, $price, $currency];
            $date->modify('+1 day');
        }

        \Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['date', 'room_id', 'adults', 'children', 'kids', 'price', 'price_currency'], $prices)->execute();
        // Сигнал для системы сообщений
        \Yii::$app->automaticSystemMessages->setDataUpdated();
    }

}

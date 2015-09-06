<?php

namespace common\components;

use common\models\Room;
use common\models\RoomAvailability;
use common\models\RoomPrices;
use yii\helpers\ArrayHelper;

class FixedPriceCalculator
{
    /**
     * Вычисление цены комнаты
     *
     * @param $room   Room  Объект комнаты
     * @param $params array Параметры формирования цены
     *
     * @return array|null Массив priceInfo или null - если комната недоступна
     */
    public static function getPriceInfo(Room $room, $params)
    {
        $result = [];

        $dateFrom = ArrayHelper::getValue($params, 'dateFrom', false);
        $dateTo = ArrayHelper::getValue($params, 'dateTo', false);

        $roomPrices = RoomPrices::find()
            ->where(['>=', 'date', $dateFrom])
            ->andWhere(['<', 'date', $dateTo])
            ->andWhere(['>', 'price', 0])
            ->andWhere([
                'room_id'  => $room->id,
                'adults'   => 0,
                'children' => 0,
                'kids'     => 0,
            ])
            ->all();

        $days = (new \DateTime($dateFrom))->diff(new \DateTime($dateTo))->days;
        $prices_count = count($roomPrices);

        if ($days !== $prices_count) {
            return null;
        }

        // проверка на stop_sale и количество свободных номеров
        $availibleRooms = RoomAvailability::find()
            ->where(['>=', 'date', $dateFrom])
            ->andWhere(['<', 'date', $dateTo])
            ->andWhere(['>', 'count', 0])
            ->andWhere(['stop_sale' => 0, 'room_id'  => $room->id, ])
            ->orderBy('date')
            ->all()
        ;

        if (count($availibleRooms) != $days) {
            return null;
        }
        $result = ArrayHelper::map($roomPrices, 'date', function ($array) {
            return $array;
        });

        return ['days' => $result];
    }

    /**
     * Цикл по всем вариантам заселения
     * Возвращает количество вариантов и запускает для каждого варианта функцию $f с параметрами $params
     *
     * @param Room  $room   Объект комнаты
     * @param f     function()
     * @param params mixed
     *
     * @return int
     */
    public static function bookingTypes($room, $f=false, &$params=false) {
        $f($params, 0, 0, 0);
        return 1;
    }

    /**
     * Проверяет установлена ли цена на номер
     * Возвращает true, если цена установлена, и false, если нет
     *
     * @param Room  $room   Объект комнаты
     * @param array $params Параметры формирования цены
     *
     * @return boolean|null
     */
    public static function isPriceSet($room, $params)
    {
        $date = ArrayHelper::getValue($params, 'date', false);

        $price = RoomPrices::find()
            ->where([
                'room_id' => $room->id,
                'date'    => $date,
            ])
            ->one();

        return ($price && $price['price'] > 0);
    }

    /**
     * Возвращает объект, описывающий цену на номер в указанный день
     * 
     *
     * @param Room  $room   Объект комнаты
     * @param array $params Параметры поиска (date)
     */
    public static function getPrice($room, $params)
    {
        $date = ArrayHelper::getValue($params, 'date', false);

        $price = RoomPrices::find()
            ->where([
                'room_id' => $room->id,
                'date'    => $date,
            ])
            ->one();

        $priceObj = new \stdClass();
        $priceObj->set = ($price && $price['price'] > 0);
        $priceObj->prices = ['default' => new \stdClass()];
        if (!$price['price']) {
            $priceObj->prices = [];
        } else {
            $priceObj->prices['default']->price = $price ? $price['price'] : 0;
            $priceObj->prices['default']->price_currency = $price ? $price['price_currency'] : 0;
            $priceObj->prices['default']->adults = 0;
            $priceObj->prices['default']->children = 0;
            $priceObj->prices['default']->kids = 0;
        }

        return $priceObj;
    }

    /**
     * Возвращает массив с описаниями типов заселения
     * 
     *
     * @param Room  $room   Объект комнаты
     * @param array $params Параметры поиска (date)
     */
    public static function getPriceTitles($room, $params)
    {
        $a = new \stdClass();
        $a->title = \Yii::t('pricerules', 'Price per day');
        $a->adults = 0;
        $a->children = 0;
        $a->kids = 0;
        return ["default" => $a];
    }

    /**
     * Проверяет установлена сколько процентов цен установлено на указанный период
     * Возвращает число от 0 до 1
     *
     * @param Room  $room   Объект комнаты
     * @param array $params Параметры формирования цены
     *
     * @return float
     */
    public static function priceSetStatistic($room, $params)
    {
        $beginDate = ArrayHelper::getValue($params, 'beginDate', false);
        $endDate = ArrayHelper::getValue($params, 'endDate', false);

        $count = RoomPrices::find()
            ->where(['room_id' => $room->id])
            ->andWhere(['>=', 'date', $beginDate])
            ->andWhere(['<=', 'date', $endDate])
            ->count();
        if (!$count) {
            $count = 0;
        }

        $bd = \DateTime::createFromFormat('Y-m-d', $beginDate);
        $ed = \DateTime::createFromFormat('Y-m-d', $endDate);
        $d = $ed->diff($bd);
        $n = $d->days;
        if ($n == 0) {
            return 1;
        }

        return round($count / ($n + 1), 2);
    }

}

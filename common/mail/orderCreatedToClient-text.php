<?php

/* @var $this yii\web\View */
/* @var $order common\models\Order */
/* @var $lang string */

if (!isset($lang)) {
    $lang = $order->lang;
}
$translate_category = 'mails_order';
?>
<?= \Yii::t('mails_order', 'Hello, {name}!', ['name' => $order->contact_name . ' ' . $order->contact_surname], $lang) ?>
<?= \Yii::t('mails_order', 'You made a order on the site <a href="http://king-boo.com">king-boo.com</a>.', [], $lang) ?>
<?= \Yii::t($translate_category, 'Order number: {n}', ['n' => $order->number], $lang) ?>
<?= \Yii::t('mails_order', 'The rooms are now booked, but the booking will be canceled if payment is not received within 24 hours.', []) ?>
<?= \Yii::t($translate_category, "To make a payment, please open the link below in your browser: \n{url}", ['url' => 'http://king-boo.com/payment/'.$order->payment_url], $lang) ?>
<?= \Yii::t('mails_order', 'Order details') ?>:
<?= $this->render('_order-html', ['order' => $order]) ?>
<?= \Yii::t($translate_category, 'Best regards, team of king-boo.com', [], $lang) ?>
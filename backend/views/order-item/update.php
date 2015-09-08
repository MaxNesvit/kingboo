<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\OrderItem */

$this->title = Yii::t('backend_order', 'Update {modelClass}: ', [
    'modelClass' => 'Order Item',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend_order', 'Order Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('backend_order', 'Update');
?>
<div class="order-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

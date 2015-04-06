<?php
/* @var $this yii\web\View */

$this->title = 'BooBooking';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">

            <div class="col-md-12">
                <?= \yii\helpers\Html::a(Yii::t('backend_main', 'Manage clients'), ['/user/index'],['class'=>'btn btn-default']) ?>
                <?= \yii\helpers\Html::a(Yii::t('backend_main', 'Manage partners'), ['/partner-user/index'],['class'=>'btn btn-default']) ?>
                <?= \yii\helpers\Html::a(Yii::t('backend_main', 'Manage types of food '), ['/food-type/index'],['class'=>'btn btn-default']) ?>
                <?= \yii\helpers\Html::a(Yii::t('backend_main', 'Manage countries '), ['/country/index'],['class'=>'btn btn-default']) ?>
                <?= \yii\helpers\Html::a(Yii::t('backend_main', 'Manage currencies '), ['/currency/index'],['class'=>'btn btn-default']) ?>
                <?= \yii\helpers\Html::a('Gii', ['/gii'],['class'=>'btn btn-warning']) ?>
            </div>

        </div>

    </div>
</div>

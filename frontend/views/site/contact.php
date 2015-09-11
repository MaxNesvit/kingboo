<?php
use common\models\Lang;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form \yii\widgets\ActiveForm */
/* @var $model \frontend\models\ContactForm */
/* @vat $hotel \common\models\Hotel */

$this->title = \Yii::t('frontend', 'Contact us');
$this->params['breadcrumbs'][] = $this->title;
$l = Lang::$current->url;

if (!is_null($hotel))
    $this->params['appName'] = $hotel->{'title_' . $l};


?>
<div class="site-contact">
    <h1><?= $this->title ?></h1>
    <?php if(!is_null($hotel)): ?>
        <br>
        <address>
            <b><?= \Yii::t('frontend', 'Address', []) ?>:</b> <?= Html::encode($hotel->address) ?>
            <br>
            <b><?= \Yii::t('frontend', 'E-mail', []) ?>:</b> <?= Html::mailto($hotel->contact_email) ?>
            <br>
            <b><?= \Yii::t('frontend', 'Phone', []) ?>:</b> <?= Html::encode($hotel->contact_phone) ?>
        </address>
        <p>
            <?= \Yii::t('frontend', 'To contact the hotel, please fill out the form below. Thank you.') ?>
        </p>
        <br>
    <?php endif; ?>

    <?php if(is_null($hotel)): ?>
        <p>
            <?= \Yii::t('frontend', 'If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.') ?>
        </p>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-7">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'subject') ?>
                <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
                <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-md-3">{image}</div><div class="col-md-6">{input}</div><div class="col-md-3"><div class="form-group hidden-sm hidden-xs">'.Html::submitButton(\Yii::t('frontend', 'Submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']).'</div></div></div>',
                ]) ?>
                <div class="form-group visible-sm visible-xs">
                    <?= Html::submitButton(\Yii::t('frontend', 'Submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

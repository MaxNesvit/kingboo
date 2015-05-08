<?php
/* @var $this yii\web\View */
/* @var $model common\models\Hotel */
$l = \common\models\Lang::$current->url;
$directoryBower = Yii::$app->assetManager->getPublishedUrl('@bower');

$this->title = $model->{'title_' . $l};

// underscore.js
$this->registerJsFile($directoryBower . '/underscore/underscore-min.js');

//Подключаем colorbox
$this->registerJsFile($directoryBower . '/colorbox/jquery.colorbox-min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile($directoryBower . '/colorbox/i18n/jquery.colorbox-' . $l . '.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile($directoryBower . '/colorbox/example1/colorbox.css', [], 'colorbox');

// Подключаем библиотеку moment.js
$this->registerJsFile($directoryBower . '/moment/min/moment-with-locales.min.js');

// Галлерея
\frontend\assets\GalleryAsset::register($this);
$this->registerCssFile('/css/gallery.css', ['depends' => [\frontend\assets\GalleryAsset::className()]], 'gallery');

// Приложение angular
$this->registerJsFile('/js/search.js', ['depends' => [\frontend\assets\GalleryAsset::className()]]);
$this->registerJsFile('/js/search-app.js', ['depends' => [\partner\assets\AngularAsset::className()]]);

// Плагин datepicker
$this->registerJsFile($directoryBower . '/admin-lte/js/plugins/datepicker/bootstrap-datepicker.js', ['depends' => [\yii\bootstrap\BootstrapAsset::className(), \yii\bootstrap\BootstrapPluginAsset::className()]]);
$this->registerJsFile($directoryBower . '/admin-lte/js/plugins/datepicker/locales/bootstrap-datepicker.' . $l . '.js', ['depends' => [\yii\bootstrap\BootstrapAsset::className(), \yii\bootstrap\BootstrapPluginAsset::className()]]);
$this->registerCssFile($directoryBower . '/admin-lte/css/datepicker/datepicker3.css', ['depends' => [\frontend\assets\GalleryAsset::className()]]);
?>
<h1><?= $model->{'title_' . $l} ?></h1>
<div class="hotel_stars">
    <?php foreach (range(1, 5) as $i): ?>
        <?php if ($i <= $model->category) : ?>
            &#x2605;
        <?php else: ?>
            &#x2606;
        <?php endif ?>
    <?php endforeach; ?>
</div>

<div class="row">
    <div class="col-md-6 col-sm-9 images-container">
        <!-- Slider main container -->
        <div class="big-image" id="hotelImagesBig">
            <div class="big-image-div"></div>
        </div>
        <div class="swiper-container" id="hotel-images-container">
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">
                <!-- Slides -->
                <?php foreach ($model->images as $image): /* @var $image \common\models\RoomImage */ ?>
                    <div class="swiper-slide"
                         style="background-image: url('<?= $image->getThumbUploadUrl('image', 'preview') ?>');"
                         data-big-preview="<?= $image->getThumbUploadUrl('image', 'bigPreview') ?>">
                        <a href="<?= $image->getUploadUrl('image') ?>" rel="hotelImages"></a>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>

            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>

        </div>
    </div>
    <div class="col-md-6 col-sm-3">
        <ul class="hotel-facilities row">
            <?php foreach ($model->facilities as $f): ?>
                <li class="col-xs-6 col-sm-12 col-md-6">
                    <?= $f->{'name_' . $l} ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-md-6 col-sm-12 info-container">
        <div class="info-description">
            <?= $model->{'description_' . $l} ?>
        </div>
    </div>
</div>

<h2><?= Yii::t('frontend', 'Search') ?></h2>

<div ng-app="roomsSearch" ng-controller="searchCtrl" ng-init="search.hotelId = <?= $model->id ?>;">
    <div class="row well">
        <div class="input-daterange" id="datepicker">
            <div class="form-group col-md-2 col-sm-6">
                <label for="dateFrom"><?= Yii::t('frontend', 'Arrival date') ?></label>
                <input id="dateFrom" type="text" class="form-control" name="dateFrom"/>
            </div>

            <div class="form-group col-md-2 col-sm-6">
                <label for="dateTo"><?= Yii::t('frontend', 'Departure date') ?></label>
                <input id="dateTo" type="text" class="form-control" name="dateTo"/>
            </div>
        </div>
        <div class="col-md-2 col-xs-3">
            <label for="adults"><?= Yii::t('frontend', 'Adults') ?></label>
            <div class="input-group">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" ng-click="search.adults = search.adults - 1">
                        <i class="glyphicon glyphicon-minus"></i>
                    </button>
                </span>
                <input ng-model="search.adults" type="text" class="form-control" name="adults" id="adults">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" ng-click="search.adults = search.adults + 1">
                        <i class="glyphicon glyphicon-plus"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="col-md-2 col-xs-3">
            <label for="children"><?= Yii::t('frontend', 'Children') ?></label>
            <div class="input-group">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" ng-click="search.children = search.children - 1">
                        <i class="glyphicon glyphicon-minus"></i>
                    </button>
                </span>
                <input ng-model="search.children" type="text" class="form-control" name="children" id="children">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" ng-click="search.children = search.children + 1">
                        <i class="glyphicon glyphicon-plus"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="col-md-2 col-xs-3">
            <label for="kids"><?= Yii::t('frontend', 'Kids') ?></label>
            <div class="input-group">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" ng-click="search.kids = search.kids - 1">
                        <i class="glyphicon glyphicon-minus"></i>
                    </button>
                </span>
                <input ng-model="search.kids" type="text" class="form-control" name="kids" id="kids">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" ng-click="search.kids = search.kids + 1">
                        <i class="glyphicon glyphicon-plus"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="col-md-2 col-xs-3">
            <label for="">&nbsp;</label>
            <br/>
            <button class="btn btn-primary" ng-click="find()"><?= Yii::t('frontend', 'Find') ?></button>
        </div>

    </div>

    <div class="row result-item well" ng-repeat="r in results">
        <div class="col-md-3">
            <div class="gallery thumbnail">
	            <div class="swiper-container" id="{{'hotel-room-' + r.id}}" data-room-id="{{ r.id }}">
		            <div class="swiper-wrapper">
			            <!-- Slides -->
			            <a class="swiper-slide"
			                 ng-style="{'background-image':'url('+i.preview+')'}"
			                 href="{{ i.image }}"
			                 rel="{{ 'hotelImages' + r.id }}"
			                 ng-repeat="i in r.images">
			            </a>
		            </div>
		            <div class="swiper-pagination"></div>

		            <div class="swiper-button-prev"></div>
		            <div class="swiper-button-next"></div>

	            </div>
            </div>
        </div>
        <div class="col-md-7 info">
            <div class="row">
                <div class="title">{{r['title_' + LANG]}}</div>
            </div>
            <div class="row">
                <div class="description">{{r['description_' + LANG]}}</div>
            </div>
        </div>
        <div class="col-md-2 price">
            {{ r.price }}&nbsp;{{ r.sum_currency.code }}
	        <br/>
	        <br/>
	        <div class="btn btn-success" ng-click="goBooking(r)"><?= Yii::t('frontend', 'Booking') ?></div>
        </div>
    </div>
</div>


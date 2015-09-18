<?php
use frontend\assets\AppAsset;
use frontend\assets\HotelsBootstrapAsset;
use frontend\widgets\Alert;
use yii\base\View;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

HotelsBootstrapAsset::register($this);
AppAsset::register($this);
//Yii::$app->assetManager->publish('@bower');

$this->registerJsFile('/js/langs.js');
$this->registerJsFile('/js/format.js');

if (ArrayHelper::getValue($this->params, 'embedded', 0) == 0) {
//    $this->registerCss(".wrap > .container { padding: 70px 15px 20px;}");
} else {
    $this->registerJs('var EMBEDDED = true;', \yii\web\View::POS_HEAD);
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script>
        var LANG = '<?= \common\models\Lang::$current->url ?>';
    </script>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    if (ArrayHelper::getValue($this->params, 'embedded', 0) == 0) {
        NavBar::begin([
            'brandLabel' => isset($this->params['appName']) ? $this->params['appName'] : str_replace(['https://', 'http://'], '', \Yii::$app->request->hostInfo),
            'brandUrl' => \yii\helpers\Url::to(['site/index']),
            'options' => [
                'class' => 'navbar-default',
            ],
        ]);

        // Языки
        $langs = \common\models\Lang::sortedLangList();
        $menuItems = [];
        $defaultLang = \common\models\Lang::getDefaultLang();
        $otherLangs = [];
        foreach ($langs as $l) { // Получаем список опубликованных языков, отличных от текущего
            if ($l->url != \common\models\Lang::$current->url && $this->params['hotel']->published($l->url)) {
                $otherLangs[] = $l->url;
            }
        }
        if ($otherLangs) {
            echo '<ul id="w2" class="navbar-nav navbar-right nav" style="padding-left: 20px;">';
            foreach ($otherLangs as $l) {
                $url = $l == $defaultLang->url ? '@web/' : '/' . $l;
                $url = yii\helpers\Url::to($url);
                echo '<li><a href="' . $url . '" class="navbar-brand"><img alt="' . $l . '" src="/img/flag-' . $l . '.jpg"></a></li>';
            }
            echo '</ul>';
        }

        // Меню
        $menuItems = [
            ['label' => \Yii::t('frontend', 'Home'), 'url' => ['/site/index']],
            ['label' => \Yii::t('frontend', 'Contact us'), 'url' => ['/site/contact']],
        ];
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems,
        ]);
        NavBar::end();
    }
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'homeLink' => ['label' => \Yii::t('frontend', 'Home'), 'url' => ['site/index']],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>
<?php if (ArrayHelper::getValue($this->params, 'embedded', 0) == 0): ?>
    <footer class="footer">
        <div class="container">
            <p class="pull-right">&copy; <a href="http://itdesign.ru">IT Design Studio</a> <?= date('Y') ?></p>
        </div>
    </footer>
<?php endif ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

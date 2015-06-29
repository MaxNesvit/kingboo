<?php

namespace partner\controllers;

use common\components\ListPriceRules;
use common\models\PriceRules;
use common\models\Room;
use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

class PriceRulesController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'deactivate'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'deactivate' => ['post'],
                ],
            ],

        ];

    }

    /**
     * Список правил
     * @return string
     */
    public function actionIndex()
    {
        $price_rules = PriceRules::find()
            ->where(['partner_id' => \Yii::$app->user->id])
            ->all();

        return $this->render('index', [
            'price_rules' => $price_rules,
        ]);
    }

    /**
     * Деактивация правила
     * @param $id
     * @return \yii\web\Response
     * @throws ErrorException
     * @throws ForbiddenHttpException
     */
    public function actionDeactivate($id) {
        /** @var PriceRules $price_rule */
        if ($price_rule = PriceRules::findOne($id)) {
            if ($price_rule->partner_id == \Yii::$app->user->id) {
                $price_rule->active = 0;
                $r = $price_rule->save();
                if ($r) {
                    return $this->redirect(['index']);
                } else {
                    throw new ErrorException(\Yii::t('pricerules', 'Unknown error'));
                }
            } else {
                throw new ForbiddenHttpException(\Yii::t('pricerules', 'Price rule deactivation is not allowed'));
            }
        } else {
            throw new ForbiddenHttpException(\Yii::t('pricerules', 'Price rule not found'));
        }
    }

    /**
     * Создание правила
     * @param $type
     * @return string|\yii\web\Response
     */
    public function actionCreate($type)
    {
        /** @var PriceRules $model */
        $model = ListPriceRules::getModel($type);

        // пробуем получить данные из POST и сохранить правило
        $req = \Yii::$app->request;
        $post = \Yii::$app->request->post();
        if ($model->load($post)) {
            // проверяем условия
            $checkedValue = 'on';

            $valid = true;

            // диапазон ограничения даты бронирования
            if ($req->post('bookingRange', false) === $checkedValue) {
                $valid = $valid && !(new \DateTime($model->dateFromB))->diff(new \DateTime($model->dateToB))->invert;
            } else {
                $model->dateFromB = null;
                $model->dateToB = null;
            }

            // диапазон ограничения дат проживания
            if ($req->post('livingRange', false) === $checkedValue) {
                $valid = $valid && !(new \DateTime($model->dateFrom))->diff(new \DateTime($model->dateTo))->invert;
            } else {
                $model->dateFrom = null;
                $model->dateTo = null;
            }

            // минимальная сумма
            if ($req->post('minSum', false) === $checkedValue) {
                $valid = $valid && $model->minSum > 0;
            } else {
                $model->minSum = null;
            }

            // максимальная сумма
            if ($req->post('maxSum', false) === $checkedValue) {
                $valid = $valid && $model->maxSum > 0;
            } else {
                $model->minSum = null;
            }

            // код
            if ($req->post('checkCode', false) === $checkedValue) {
                $valid = $valid && $model->code !== '' && $model->code != null && preg_match('/^[a-zA-Z0-9\-+_!]+$/', $model->code);
            } else {
                $model->code = null;
            }

            if ($valid && $model->save()) {
                // сохраняем ссылки на комнаты
                foreach ($post['rooms'] as $k => $v) {
                     $model->link('rooms', Room::findOne($v));
                }
                return $this->redirect(['price-rules/index']);
            }
        }

        // создаём новую скидку
        $rooms = Room::find()
            ->joinWith('hotel.partner')
            ->where(['partner_user.id' => \Yii::$app->user->id])
            ->all();

        return $this->render('create', [
            'model' => $model,
            'rooms' => $rooms,
        ]);
    }
}

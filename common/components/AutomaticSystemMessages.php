<?php
namespace common\components;
 
use \Yii;
use yii\base\Component;
 
class AutomaticSystemMessages extends Component {
 
 	/**
 	 * Работа с системными сообщениями
 	 */

 	const TYPE_INFO = 'info';
 	const TYPE_WARNING = 'warning';
 	const TYPE_DANGER = 'danger';

    // Обработанные сообщения, подлежащие выдаче в настоящее время
 	protected $actualMessages = [];
    // Признак того, что произошли события, влияющие на сообщения
    protected $dataUpdated = false;

    /**
     * Привязывает события EVENT_BEFORE_REQUEST и EVENT_AFTER_REQUEST
     */
    public function init() {
    	Yii::$app->on(yii\base\Application::EVENT_BEFORE_REQUEST, [$this, 'prepareMessages']);
        Yii::$app->on(yii\base\Application::EVENT_AFTER_REQUEST, [$this, 'checkUpdates']);
    }

    /**
     * Возвращает массив сообщений, переопределяется в потомках
     * Массив имет вид ['query-key' => ['item-key' => ['type' => $v, 'condition' => $v, 'title' => $v, 'text' => $v]]] 
     * type - тип сообщения (TYPE_INFO, TYPE_WARNING, TYPE_DANGER)
     * condition - функция, которая обрабатывает сообщения (она же меняет сообщение и возвращает его измененным, например вставляет в текст ссылки)
     */
    public function messages() {
    	return [];
    }

    /**
     * Сообщения, которые надо выводить сейчас
     */
    public function actualMessages() {
        return $this->actualMessages;
    }

    public function setDataUpdated() {
        $this->dataUpdated = true;
    }

    /**
     * Запускает пересчет сообщений, если были изменения
     */
    public function checkUpdates() {
        if ($this->dataUpdated) {
            $this->resetMessages();
        }
    }

    /**
     * Пересчитывет сообщения
     */
    public function resetMessages() {
        \Yii::trace('Reset messages', 'debug');
        $this->actualMessages = [];
        // Цикл по последовательностям
        foreach ($this->messages() as $key0 => $query) {
            //Цикл по сообщениям
            foreach ($query as $key => $message) {
                $f = $message['condition'];
                // Проверяем условие и меняем сообщение, если это предусматривает функция
                if (!method_exists($this,$f)) {
                    throw new \Exception("Method '$f' is not defined in class " . get_class($this) );
                }
                $msg = $this->$f($message);
                if ($msg) {
                    $this->actualMessages["$key0-$key"] = $msg;
                }
            }
        }

    }

    /**
     * Готовит сообщения, переопределяется в потомках
     */
    public function prepareMessages() {
        if (\Yii::$app->request->isAjax || \Yii::$app->request->isPut) { //TODO: нихрена это не работает (при любом аяксе это преодолевается)
            return;
        }
        if (\Yii::$app->user->isGuest) {
            return;
        }
        $this->resetMessages();
    }
 
}
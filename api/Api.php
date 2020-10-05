<?php
/**
 * Базовый класс работы с API
 *
 * @author Oleg Gorbatov <o.i.gorbatov@yandex.ru>
 */
abstract class Api
{
    protected $action = 'viewAction';

    public $parentGroupId = 0;
    protected $queryArr = [];

    /**
     * Api constructor.
     */

    public function __construct() {
        $query = explode('&', $_SERVER['QUERY_STRING']);
        $queryArr = [];
        foreach ($query as $value) {
            list($k, $v) = explode('=', $value);
            $queryArr[$k] = $v;
        }
        $this->queryArr = $queryArr;

        if (array_key_exists('group', $queryArr)) {
            $this->parentGroupId = (int) $queryArr['group'];
        }
    }

    /**
     * Запуск метода АПИ
     *
     * @return mixed
     * @throws RuntimeException
     */
    public function run() {
        if (method_exists($this, $this->action)) {
            return $this->{$this->action}();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    abstract protected function viewAction();
}
<?php
require_once('models/GroupModel.php');
require_once 'Api.php';

/**
 * Дочерний класс работы с API
 *
 * @author Oleg Gorbatov <o.i.gorbatov@yandex.ru>
 */
class GroupsApi extends Api
{
    /**
     * GroupsApi constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Получение данных, для отправки их на фронт
     *
     * @return object | boolean $data
     */
    public function viewAction()
    {
        $content = new GroupModel();
        $data = $content->getData($this->parentGroupId);
        if ($data) {
            return $data;
        } else {
            throw new RuntimeException('Error'.GroupModel::$errorMessage, 405);
        }
    }

}
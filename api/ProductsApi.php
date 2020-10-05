<?php
require_once('models/ProductModel.php');
require_once 'Api.php';

/**
 * Дочерний класс работы с API
 *
 * @author Oleg Gorbatov <o.i.gorbatov@yandex.ru>
 */
class ProductsApi extends Api
{
    public $groups = [];

    /**
     * ProductsApi constructor.
     * @param array $data
     */

    public function __construct(array $data)
    {
        parent::__construct();

        $allGroups = [];
        foreach ($data as $group){
            if ( $group['id_parent'] == $this->parentGroupId or $group['id'] == $this->parentGroupId){
                $allGroups[] = $group['id'];
                $allGroups = array_merge($allGroups, $group['allGroups']);
            }
        }
        $this->groups = $allGroups;
    }

    /**
     * Получение данных, для отправки их на фронт
     *
     * @return object | boolean $data
     */
    public function viewAction()
    {
        $content = new ProductModel();
        $data = $content->getData($this->groups);
        if ($data) {
            return $data;
        } else {
            throw new RuntimeException('Error'.ProductModel::$errorMessage, 405);
        }
    }
}
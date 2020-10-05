<?php
require_once 'DbConnect.php';
/**
 * Класс для работы с данными Продукт модели
 *
 * @author Oleg Gorbatov <o.i.gorbatov@yandex.ru>
 */
class ProductModel
{
    static public $errorMessage = '';
    protected $db = '';
    protected $tableNameGroups = 'groups';
    protected $tableNameProducts = 'products';

    /**
     * ProductModel constructor. Подключается к базе данных используя класс DbConnect
     */
    public function __construct()
    {
        $this->db = DbConnect::getConnection();
    }

    /**
     * @return array | boolean $data
     * @param array $groups
     */
    public function getData($groups) {
        if (!($this->db instanceof PDO)) {
            self::$errorMessage = 'Error in dbConnect';
            return FALSE;
        }

        $data = '';
        $where = '';
        if (!empty($groups)) {
            $where = '  WHERE p.id_group in ( '.implode(', ',$groups).' )';
        }

        try {
            $data = $this->db->query("
                SELECT *
                    FROM $this->tableNameProducts AS p $where
                    
            ")->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            self::$errorMessage = 'Unable read data';
        }
        return $data;
    }

    /**
     * Создаёт новый контакт в БД
     *
     * @param string $data
     */
}
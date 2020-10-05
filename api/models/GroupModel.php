<?php
require_once 'DbConnect.php';
/**
 * Класс для работы с данными Групповой модели
 *
 * @author Oleg Gorbatov <o.i.gorbatov@yandex.ru>
 */
class GroupModel
{
    static public $errorMessage = '';
    protected $db = '';
    protected $tableNameGroups = 'groups';
    protected $tableNameProducts = 'products';

    /**
     * GroupModel constructor. Подключается к базе данных используя класс DbConnect
     */
    public function __construct()
    {
        $this->db = DbConnect::getConnection();
    }

    /**
     * @param int $parentId
     * @return array | boolean $data
     */
    public function getData(int $parentId)
    {
        if (!($this->db instanceof PDO)) {
            self::$errorMessage = 'Error in dbConnect';
            return FALSE;
        }
        //$where = ' WHERE g.id='.$parentId.' OR g.id_parent='.$parentId.'  ';


        $result = [];
        try {
            $result = $this->db->query("
                SELECT g.*, count(*) AS cnt
                    FROM $this->tableNameProducts AS p
                    INNER JOIN $this->tableNameGroups g ON p.id_group = g.id
                    GROUP BY p.id_group  
            ")->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            self::$errorMessage = 'Unable read data';
        }

        $data = [];
        foreach ($result as $row) {
            $data[$row['id']] = $row;
        }

        $data = $this->getGroups($data);
        $data = $this->getCounters($data);
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getGroups(array $data){
        $groups = [];
        foreach ($data as $k => $v){
            if (!array_key_exists($k, $groups)) {
                $groups[$key][] = $v['id'];
                $idParent = $v['id_parent'];
                while($idParent != 0) {
                    $groups[$idParent][] = $v['id'];
                    $parent = $data[$idParent];
                    $idParent = $parent['id_parent'];
                }
            }
        }
        foreach ($data as $k => $v) {
            if (array_key_exists($k, $groups)) {
                $data[$k]['allGroups'] = $groups[$k];
            } else {
                $data[$k]['allGroups'] = [];
            }
        }
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function getCounters($data) {
        foreach ($data as $k => $v) {
            $counter = $v['cnt'];
            foreach ($v['allGroups'] as $group) {
                if (array_key_exists($group, $data)) {
                    $counter +=$data[$group]['cnt'];
                }
            }
            $data[$k]['cnt'] = $counter;
        }
        return $data;
    }
}


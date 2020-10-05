<?php
require_once 'GroupsApi.php';
require_once 'ProductsApi.php';

/**
 * Инициализация АПИ
 */
$api = new GroupsApi();
$data = $api->run();
$parentId = $api->parentGroupId;

function mapTree(array $data, int $id_parent = 0) {
    $tree = array();

    foreach ($data as $id => &$node) {
        if ($node['id_parent'] == $id_parent) {
            if (is_numeric($node['id'])) {
                $node['groupChildren'][] = $node['id'];
            }
            $tree[$id] = &$node;
        } else {
            if (is_numeric($node['id'])) {
                $node['groupChildren'][] = $node['id'];
                $data[$node['id_parent']]['groupChildren'][] = $node['id'];
            }
            $data[$node['id_parent']]['children'][$id] = &$node;

        }
    }
    return $tree;
}
$dataProducts = $data;
$data = mapTree($data);

function viewGroups($data, $parentId) {
    $html = '';
    foreach ($data as $group) {
        if (isset($group["name"])) {
            if ($group['id_parent']==$parentId  or $group['id'] == $parentId) {
                $html .= '
                    <li class="list-group-item">
                        <a href="/index.php?group='.$group["id"].'" class="list-group-item list-group-item-action">'
                            .$group["name"].' ('.$group["cnt"].')</a>';
            }
        }

        if(!empty($group['children'])) {
            $html .= '<ul>';
            $html .= viewGroups($group['children'], $parentId);
            $html .= '</ul>';
        }
        $html .= '</li>';

    }
    return $html;
}

$HTML = '<h3>Группы</h3>
<ul class="list-group">'.viewGroups($data, $parentId).'</ul>';

$api = new ProductsApi($dataProducts);
$data = $api->run();

function viewProducts($data) {
    $html = '';
    foreach ($data as $product){
        $html .= '<li class="list-group-item">'.$product['name'].'</li>';
    }
    return $html;
}

$HTML .='<h3>Продукты</h3>
<ul class="list-group">'.viewProducts($data).'</ul>';

echo $HTML;



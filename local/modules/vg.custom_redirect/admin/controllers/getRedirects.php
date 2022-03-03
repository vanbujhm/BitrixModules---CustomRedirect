<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

if(!Loader::includeModule('vg.custom_redirect')){
    echo 'Ошибка подключения модуля "vg.custom_redirect"'; exit();
}

$getList = [
    'select' => ['ID', 'URL_OLD', 'URL_NEW', 'TYPE'],
    'order' => ['ID' => 'DESC'],
];
if(!$_GET['full']){
    $getList['limit'] = 10;
}

$redirs = [];
$redirectRes = VGCustomRedirectTable::getList($getList);
while($redir = $redirectRes->fetch()){
    $redirs[] = [
        'id' => $redir['ID'],
        'urlOld' => $redir['URL_OLD'],
        'urlNew' => $redir['URL_NEW'],
        'type' => $redir['TYPE'],
    ];
}
header('Content-Type: application/json');
echo json_encode($redirs); 
<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if($USER->isAdmin()){
    $aMenu = [
        [
            'parent_menu' => 'global_menu_content',
            'sort' => 400,
            'text' => Loc::getMessage('vg_custom_redirect'),
            'title' => Loc::getMessage('vg_custom_redirect'),
            'url' => 'custom_redirect_index.php',
            'items_id' => 'menu_references',
            'items' => [
                [
                    'text' => Loc::getMessage("module_tab_list"),
                    'url' => 'custom_redirect_index.php?lang='.LANGUAGE_ID,
                    'more_url' => array('custom_redirect_index.php?lang='.LANGUAGE_ID),
                    'title' => Loc::getMessage("module_tab_list"),
                ],
            ],
        ],
    ]; 
}
else{
    $aMenu = [];
}

return $aMenu;
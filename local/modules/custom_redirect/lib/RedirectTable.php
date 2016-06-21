<?php
namespace Custom_redirect;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class RedirectTable extends DataManager
{
    const LEFT_TO_RIGHT = 'Y';
    const RIGHT_TO_LEFT = 'N';

    public static function getFilePath()
    {
        return __FILE__;
    }
    
    public static function getTableName()
    {
        return 'b_custom_redirect';
    }

    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'autocomplete' => true,
                'primary' => true,
            ),
            'URL_OLD' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage("OLD_URL"),
            ),
            'URL_NEW' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage("NEW_URL"),
            ),
            'TYPE' => array(
                'data_type' => 'integer',
            ),
        );
    }
}

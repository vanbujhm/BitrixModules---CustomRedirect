<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'custom_redirect');

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}

$arAllOptions = array(
	array("use_autosave", GetMessage("BLOG_USE_AUTOSAVE"), "Y", Array("checkbox")),
);

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages($context->getServer()->getDocumentRoot()."/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl("tabControl", array(
    array(
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("MAIN_TAB_SET"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET"),
    ),
));

if((!empty($save) || !empty($restore)) && $request->isPost() && check_bitrix_sessid()){
    if(!empty($restore)){
        Option::delete(ADMIN_MODULE_NAME);
        CAdminMessage::showMessage(array(
            "MESSAGE" => Loc::getMessage("REFERENCES_OPTIONS_RESTORED"),
            "TYPE" => "OK",
        ));
    }else{
        if($request->getPost('trailing_slash_url') == 'Y'){
            Option::set(ADMIN_MODULE_NAME, 'trailing_slash_url', 'Y');
        }else{
            Option::set(ADMIN_MODULE_NAME, 'trailing_slash_url', 'N');
        }
        
        CAdminMessage::showMessage(array(
            "MESSAGE" => Loc::getMessage("REFERENCES_OPTIONS_SAVED"),
            "TYPE" => "OK",
        ));
    }
}

$tabControl->begin();
?>


<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <?php
    echo bitrix_sessid_post();
    $tabControl->beginNextTab();
    ?>

    <tr>
        <td width="50%" valign="top" class="adm-detail-content-cell-l"><label for="trailing_slash_url"><?=Loc::getMessage("trailing_slash_url") ?></label></td>
        <td width="50%" valign="middle" class="adm-detail-content-cell-r">
            <input type="checkbox"
                   value="Y"
                   <?if(Option::get(ADMIN_MODULE_NAME, "trailing_slash_url") == 'Y'):?>checked=""<?endif;?>
                   id="trailing_slash_url"
                   name="trailing_slash_url"
                   class="adm-designed-checkbox">
            <label class="adm-designed-checkbox-label" for="trailing_slash_url" title=""></label>					
        </td>
    </tr>

    <?php
    $tabControl->buttons();
    ?>
    <input type="submit"
           name="save"
           value="<?=Loc::getMessage("MAIN_SAVE") ?>"
           title="<?=Loc::getMessage("MAIN_OPT_SAVE_TITLE") ?>"
           class="adm-btn-save"
           />
    <input type="submit"
           name="restore"
           title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
           onclick="return confirm('<?= AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')"
           value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS") ?>"
           />
    <?php
    $tabControl->end();
    ?>
</form>


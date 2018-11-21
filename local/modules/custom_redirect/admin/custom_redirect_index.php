<?php
define('ADMIN_MODULE_NAME', 'custom_redirect');

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Custom_redirect\RedirectTable;

if(!$USER->isAdmin()){
    $APPLICATION->authForm('Nope');
}

Loc::loadMessages(__FILE__);

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

$tabControl = new CAdminTabControl("tabControl", array(
    array(
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("custom_redirect_MAIN_TAB_SET"),
        "TITLE" => Loc::getMessage("custom_redirect_MAIN_TAB_TITLE_SET"),
    ),
));

if($request->isPost() && check_bitrix_sessid()){
    if($request->getPost('add') == 'new'){
        $url_old = trim($request->getPost('url_old'));
        $url_new = trim($request->getPost('url_new'));
        $type = $request->getPost('type');
        if(!empty($url_old) && !empty($url_new) && !empty($type)){
            RedirectTable::add(array('URL_OLD' => $url_old, 'URL_NEW' => $url_new, 'TYPE' => $type));
            CAdminMessage::showMessage(array(
                "MESSAGE" => Loc::getMessage("custom_redirect_redirect_add_success"),
                "TYPE" => "OK",
            ));
        } else {
            CAdminMessage::showMessage(array(
                "MESSAGE" => Loc::getMessage("custom_redirect_redirect_add_error"),
                "TYPE" => "ERROR",
            ));
        }
    }
    elseif($request->getPost('edit') > 0){
        $id = $request->getPost('edit');
        $url_old = trim($request->getPost("url_old_$id"));
        $url_new = trim($request->getPost("url_new_$id"));
        $type = $request->getPost("type_$id");
        if(!empty($url_old) && !empty($url_new) && !empty($type)){
            RedirectTable::update($id, array('URL_OLD' => $url_old, 'URL_NEW' => $url_new, 'TYPE' => $type));
            CAdminMessage::showMessage(array(
                "MESSAGE" => Loc::getMessage("custom_redirect_redirect_edit_success"),
                "TYPE" => "OK",
            ));
        } else {
            CAdminMessage::showMessage(array(
                "MESSAGE" => Loc::getMessage("custom_redirect_redirect_edit_error"),
                "TYPE" => "ERROR",
            ));
        }
    }
    elseif($request->getPost('del') > 0){
        $id = $request->getPost('del');
        RedirectTable::delete($id);
        CAdminMessage::showMessage(array(
            "MESSAGE" => Loc::getMessage("custom_redirect_redirect_del"),
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
        <td colspan="2">
            <table width="100%">
                <tr>
                    <th style="width: 35%; text-align: left;"><?=Loc::getMessage("custom_redirect_url_old_title")?></th>
                    <th style="width: 35%; text-align: left;"><?=Loc::getMessage("custom_redirect_url_new_title")?></th>
                    <th style="width: 15%; text-align: left;"><?=Loc::getMessage("custom_redirect_type_title")?></th>
                    <th style="width: 15%; text-align: left;"></th>
                </tr>
                <tr>
                    <td><input type="text" name="url_old" style="width: 90%;"></td>
                    <td><input type="text" name="url_new" style="width: 90%;"></td>
                    <td>
                        <select name="type" style="width: 90%;">
                            <option value="301">301</option>
                            <option value="302">302</option>
                            <option value="303">303</option>
                            <option value="307">307</option>
                        </select>
                    </td>
                    <td>
                        <button name="add" value="new" style="width: 46%;" class="adm-btn-save"><?=Loc::getMessage("custom_redirect_add_button")?></button>
                    </td>
                </tr>
                <? $redirectRes = RedirectTable::getList(array('select' =>array('ID', 'URL_OLD', 'URL_NEW', 'TYPE'), 'order' => array('URL_OLD' =>'ASC')));
                while($redir = $redirectRes->fetch()):?>
                <tr id="item-<?=$redir["ID"];?>" class="item">
                    <td>
                        <div class="view"><?=$redir['URL_OLD'];?></div>
                        <div class="edit"><input type="text" name="url_old_<?=$redir['ID'];?>" value="<?=$redir['URL_OLD'];?>" style="width: 90%;"></div>
                    </td>
                    <td>
                        <div class="view"><?=$redir['URL_NEW'];?></div>
                        <div class="edit"><input type="text" name="url_new_<?=$redir['ID'];?>" value="<?=$redir['URL_NEW'];?>" style="width: 90%;"></div>
                    </td>
                    <td>
                        <div class="view"><?=$redir['TYPE'];?></div>
                        <div class="edit">
                            <select name="type_<?=$redir['ID'];?>" style="width: 90%;">
                                <option value="301"<?if($redir['TYPE'] == 301):?> selected<?endif;?>>301</option>
                                <option value="302"<?if($redir['TYPE'] == 302):?> selected<?endif;?>>302</option>
                                <option value="303"<?if($redir['TYPE'] == 303):?> selected<?endif;?>>303</option>
                                <option value="307"<?if($redir['TYPE'] == 307):?> selected<?endif;?>>307</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="view">
                            <button class="adm-btn-save" onclick="document.getElementById('item-<?=$redir["ID"];?>').className = 'item edit-mode'; return false;" style="width: 46%;"><?=Loc::getMessage("custom_redirect_edit_button")?></button>
                            <button name="del" value="<?=$redir['ID'];?>" style="width: 46%;"><?=Loc::getMessage("custom_redirect_del_button")?></button>
                        </div>
                        <div class="edit">
                            <button class="adm-btn-save" name="edit" value="<?=$redir['ID'];?>" style="width: 46%;"><?=Loc::getMessage("custom_redirect_ok_button")?></button>
                            <button class="view-mode" onclick="document.getElementById('item-<?=$redir["ID"];?>').className = 'item'; return false;" style="width: 46%;"><?=Loc::getMessage("custom_redirect_cancel_button")?></button>
                        </div>
                    </td>
                </tr>
                <?endwhile; ?>
            </table>
        </td>
    </tr>

    <?php
    $tabControl->buttons();
    $tabControl->end();
    ?>
</form>
<?require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
<?php
define('ADMIN_MODULE_NAME', 'vg.custom_redirect');

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

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
        "TAB" => Loc::getMessage("MAIN_TAB_SET"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET"),
    ),
));

if($request->isPost() && check_bitrix_sessid()){
    if($request->getPost('add') == 'new'){
        if($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
            $domain = 'https://';
        }else{
            $domain = 'http://';
        }
        $domain .= $_SERVER['HTTP_HOST'];
        
        $url_old = trim(str_replace($domain, '', $request->getPost('url_old')));
        $url_new = trim(str_replace($domain, '', $request->getPost('url_new')));
        $type = $request->getPost('type');
        if(!empty($url_old) && !empty($url_new) && !empty($type) && $url_old != $url_new){
            VGCustomRedirectTable::add(array('URL_OLD' => $url_old, 'URL_NEW' => $url_new, 'TYPE' => $type));
            CAdminMessage::showMessage(array(
                "MESSAGE" => Loc::getMessage("redirect_add_success"),
                "TYPE" => "OK",
            ));
        } else {
            CAdminMessage::showMessage(array(
                "MESSAGE" => Loc::getMessage("redirect_add_error"),
                "TYPE" => "ERROR",
            ));
        }
    }
    elseif($request->getPost('edit') > 0){
        if($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
            $domain = 'https://';
        }else{
            $domain = 'http://';
        }
        $domain .= $_SERVER['HTTP_HOST'];
        
        $id = $request->getPost('edit');
        $url_old = trim(str_replace($domain, '', $request->getPost("url_old_$id")));
        $url_new = trim(str_replace($domain, '', $request->getPost("url_new_$id")));
        $type = $request->getPost("type_$id");
        if(!empty($url_old) && !empty($url_new) && !empty($type) && $url_old != $url_new){
            VGCustomRedirectTable::update($id, array('URL_OLD' => $url_old, 'URL_NEW' => $url_new, 'TYPE' => $type));
            CAdminMessage::showMessage(array(
                "MESSAGE" => Loc::getMessage("redirect_edit_success"),
                "TYPE" => "OK",
            ));
        } else {
            CAdminMessage::showMessage(array(
                "MESSAGE" => Loc::getMessage("redirect_edit_error"),
                "TYPE" => "ERROR",
            ));
        }
    }
    elseif($request->getPost('del') > 0){
        $id = $request->getPost('del');
        VGCustomRedirectTable::delete($id);
        CAdminMessage::showMessage(array(
            "MESSAGE" => Loc::getMessage("redirect_del"),
            "TYPE" => "OK",
        ));
    }
}

$tabControl->begin();
?>
<link rel="stylesheet" href="/local/modules/vg.custom_redirect/admin/css/bulma-docs.min.css">
<style>
    span.adm-detail-tab.adm-detail-tab-active{
        padding-top: 4px;
    }
</style>
<script src="/local/modules/vg.custom_redirect/admin/js/axios.min.js"></script>
<script type="module" src="/local/modules/vg.custom_redirect/admin/js/main.js?v=<?=time();?>"></script>
<form method="post" action="<?=sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID)?>">
    <?php
    echo bitrix_sessid_post();
    $tabControl->beginNextTab();
    ?>
    <tr>
        <td colspan="2">
            <div id="redirect-list">
                <table width="100%" class="table">
                    <tr>
                        <th style="width: 35%; text-align: left;"><?=Loc::getMessage("search")?></th>
                        <th style="width: 35%; text-align: left;"></th>
                        <th style="width: 15%; text-align: left;"></th>
                        <th style="width: 15%; text-align: left;"></th>
                    </tr>
                    <tr>
                        <td><input type="text" name="url_old" v-model="filterOld" style="width: 90%;" placeholder="<?=Loc::getMessage("url_old_title")?>"></td>
                        <td><input type="text" name="url_new" v-model="filterNew" style="width: 90%;" placeholder="<?=Loc::getMessage("url_new_title")?>"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr style="background: #f5f9f9">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th style="width: 35%; text-align: left;"><?=Loc::getMessage("url_old_title")?></th>
                        <th style="width: 35%; text-align: left;"><?=Loc::getMessage("url_new_title")?></th>
                        <th style="width: 15%; text-align: left;"><?=Loc::getMessage("type_title")?></th>
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
                            <button name="add" value="new" style="width: 46%;" class="button is-small is-primary"><?=Loc::getMessage("add_button")?></button>
                        </td>
                    </tr>
                    <tr v-for="item in listItems" class="item">
                        <td>
                            <div v-if="!item.edit" class="view">{{ item.urlOld }}</div>
                            <div v-if="item.edit" class="edit"><input type="text" :name="'url_old_' + item.id" v-model="itemCopy.urlOld" style="width: 90%;"></div>
                        </td>
                        <td>
                            <div v-if="!item.edit" class="view">{{ item.urlNew }}</div>
                            <div v-if="item.edit" class="edit"><input type="text" :name="'url_new_' + item.id" v-model="itemCopy.urlNew" style="width: 90%;"></div>
                        </td>
                        <td>
                            <div v-if="!item.edit" class="view">{{ item.type }}</div>
                            <div v-if="item.edit" class="edit">
                                <select :name="'type_' + item.id" v-model="itemCopy.type" style="width: 90%;">
                                    <option value="301">301</option>
                                    <option value="302">302</option>
                                    <option value="303">303</option>
                                    <option value="307">307</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div v-if="!item.edit" class="view">
                                <span class="button is-small is-warning" v-on:click="itemEdit(item)" style="width: 46%;"><?=Loc::getMessage("edit_button")?></span> &nbsp;
                                <button class="button is-small is-danger" name="del" :value="item.id" style="width: 46%;"><?=Loc::getMessage("del_button")?></button>
                            </div>
                            <div v-if="item.edit" class="edit">
                                <button class="button is-small is-success" name="edit" :value="item.id" style="width: 46%;"><?=Loc::getMessage("ok_button")?></button> &nbsp;
                                <span class="button is-small" v-on:click="item.edit = false" style="width: 46%;"><?=Loc::getMessage("cancel_button")?></span>
                            </div>
                        </td>
                    </tr>
                </table>
                <br>
                <nav v-if="total > 1" class="pagination" role="navigation" aria-label="pagination">
                    <a @click.prevent="prevPage" :class="{ 'is-disabled': onFirstPage }" class="pagination-previous">&laquo;</a>
                    <a @click.prevent="nextPage" :class="{ 'is-disabled': onLastPage }" class="pagination-next">&raquo;</a>
                    
                    <ul class="pagination-list">
                        
                        <li v-for="paginator in paginators">
                            <a @click.prevent="setPage(paginator.value)" :class="paginator.class">{{ paginator.value }}</a>
                        </li>
                        
                    </ul>
                </nav>
            </div>
        </td>
    </tr>

    <?php
    $tabControl->buttons();
    $tabControl->end();
    ?>
</form>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
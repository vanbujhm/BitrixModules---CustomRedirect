<?php
namespace Custom_redirect;

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'custom_redirect');

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Custom_redirect\RedirectTable;

Loc::loadMessages(__FILE__);

class Redirect{
    public static function trailingSlashUrl(){
        $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
        if(preg_match("/^((.*\.php)|(.*\.html))$/", $uri_parts[0]) && preg_match("/^((\/bitrix\/)|(\/_?ajax\/))/", $uri_parts[0]) == false){
            $url = str_replace(array('index.php', 'index.html'), array(''), $uri_parts[0]);
            if(preg_match("/^((.*\.php)|(.*\.html))$/", $url)){
                require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
                \Bitrix\Iblock\Component\Tools::process404("404", true, true, true);
            }
            else{
                if(!empty($uri_parts[1])){
                    $url .= '?'.$uri_parts[1];
                }
                header("Location: $url", TRUE, 301);
                exit();
            }
        }
        if(Option::get(ADMIN_MODULE_NAME, "trailing_slash_url") == 'Y'){
            if(preg_match("/^((\/bitrix\/)|(.*\/)|(.*\.php)|(.*\.xml)|(.*\.js)|(.*\.jpeg)|(.*\.jpg)|(.*\.png)|(.*\.pdf)|(.*\.css)|(.*\.svg)|(.*\.doc))$/", $uri_parts[0]) == false){
                $url = $uri_parts[0].'/';
                if(!empty($uri_parts[1])){
                    $url .= '?'.$uri_parts[1];
                }
                header("Location: $url", TRUE, 301);
                exit();
            }
        }
    }
    
    public static function redirectUrl(){
        $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
        $path = $uri_parts[0];
        $redirectRes = RedirectTable::getList(array('select' =>array('ID', 'URL_OLD', 'URL_NEW', 'TYPE'), 'order' => array('URL_OLD' =>'ASC')));
        while($redir = $redirectRes->fetch()){
            $default_path = str_replace('\*', '(\w+)', preg_quote($redir['URL_OLD'], '/'));
            if(preg_match("/^{$default_path}$/", $path, $matches)){
                $url = $redir['URL_NEW'];
                if(!empty($uri_parts[1]))
                    $url .= '?'.$uri_parts[1];
                if($redir['TYPE'] == 301){
                    // 301 Moved Permanently
                    header("Location: $url", TRUE, 301);
                }
                elseif($redir['TYPE'] == 302){
                    // 302 Found
                    header("Location: $url", TRUE, 302);
                }
                elseif($redir['TYPE'] == 303){
                    // 303 See Other
                    header("Location: $url", TRUE, 303);
                }
                else{
                    // 307 Temporary Redirect
                    header("Location: $url", TRUE, 307);
                }
                exit();
            }
        }
    }
}
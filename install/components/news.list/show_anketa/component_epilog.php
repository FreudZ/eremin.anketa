<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//use Bitrix\Main\Page\Asset;
//¬ файле component_epilog.php доступны:
/*
    $arParams - параметры, чтение/изменение не затрагивает одноименный член компонента.
    $arResult Ч результат, чтение/изменение не затрагивает одноименный член класса компонента.
    $componentPath Ч путь к папке с компонентом от DOCUMENT_ROOT (например /bitrix/components/bitrix/iblock.list).
    $component Ч ссылка на $this.
    $this Ч ссылка на текущий вызванный компонент (объект класса CBitrixComponent), можно использовать все методы класса.
    $epilogFile Ч путь к файлу component_epilog.php относительно DOCUMENT_ROOT
    $templateName - им€ шаблона компонента (например: .dеfault)
    $templateFile Ч путь к файлу шаблона от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list/templates/.default/template.php)
    $templateFolder Ч путь к папке с шаблоном от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list/templates/.default)
    $templateData Ч обратите внимание, таким образом можно передать данные из template.php в файл component_epilog.php, причем эти данные закешируютс€ и будут доступны в component_epilog.php на каждом хите/
    $APPLICATION, $USER, $DB Ч глобальные переменные.*/?>
<?php

 // $APPLICATION->AddHeadScript($templateFolder.'/js/jquery.validate.min.js');

?>

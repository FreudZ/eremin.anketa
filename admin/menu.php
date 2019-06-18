<?

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetAdditionalCSS("/bitrix/panel/eremin.anketa/main/eank_menu.css");


if ($APPLICATION->GetGroupRight("eremin.anketa") != "D") {
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => "eremin.anketa",
        "sort" => 900,
        "icon" => "eank_menu_icon",
        "text" => GetMessage("EANK_MENU_ITEM_TEXT"),
        "title" => GetMessage("EANK_MENU_ITEM_TITLE"),
        "items_id" => "eremin_anketa",
        "items" => array()

    );
    $aMenu["items"][] = array(

        "text" => GetMessage("EANK_PANEL"),
        "title" => GetMessage("EANK_PANEL_ALT"),
        "url" => "eank_panel.php?lang=" . LANGUAGE_ID,
        "icon" => "eank_panel_icon",
        "page_icon" => "",
        "items" => array()
    );
    return $aMenu;

}
return false;


?>
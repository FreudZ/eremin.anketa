<?
global $MESS;
IncludeModuleLangFile(__FILE__);

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");

$module_id = "eremin.anketa";
CModule::IncludeModule($module_id);
$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);

$ANKETA_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_IBLOCK", 0);
$ANKETA_ANSWERS_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_ANSWERS_IBLOCK", 0);
$ANKETA_CRITERIA_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_CRITERIA_IBLOCK", 0);

if ($MOD_RIGHT >= "Y" || $USER->IsAdmin()):

    if ($REQUEST_METHOD == "POST" && strlen($Update) > 0 && check_bitrix_sessid()) {
        $ANKETA_IBLOCK = $_POST['ANKETA_IBLOCK'];
        COption::SetOptionInt("eremin.anketa", "ANKETA_IBLOCK", $ANKETA_IBLOCK);

        $ANKETA_ANSWERS_IBLOCK = $_POST['ANKETA_ANSWERS_IBLOCK'];
        COption::SetOptionInt("eremin.anketa", "ANKETA_ANSWERS_IBLOCK", $ANKETA_ANSWERS_IBLOCK);

        $ANKETA_CRITERIA_IBLOCK = $_POST['ANKETA_CRITERIA_IBLOCK'];
        COption::SetOptionInt("eremin.anketa", "ANKETA_CRITERIA_IBLOCK", $ANKETA_CRITERIA_IBLOCK);

    }

    $aTabs = array(
	 	  array("DIV" => "edit1", "TAB" => GetMessage("EANK_OPT_TAB_COMMON"), "TITLE" => GetMessage("EANK_OPT_TAB_COMMON")),
        array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "main_settings", "TITLE" => GetMessage("MAIN_TAB_RIGHTS")),


    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs);
    ?>
    <?
    $tabControl->Begin();
    ?>

    <style>
        #tblTYPES tr td {
            vertical-align: top;
        }

        #tblTYPES .wd-quick-edit {
            display: none;
            width: 500px;
        }

        #tblTYPES .wd-quick-view {
            padding: 3px;
            border: 1px solid transparent;
            width: 800px;
        }

        #tblTYPES .wd-input-hover {
            background-color: #F8F8F8;
            border: 1px solid #bbbbbb;
            cursor: pointer;
        }

        textarea {
            word-wrap: break-word;
        }
    </style>

    <form method="POST"
          action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialchars($mid) ?>&lang=<?= LANGUAGE_ID ?>"
          name="webdav_settings">


        <? $tabControl->BeginNextTab(); ?>
        <tr>
            <td><? echo GetMessage('EANK_OPT_ANKETA_IBLOCK') ?></td>
            <td><input type="text" name="ANKETA_IBLOCK"  width="170" id="ANKETA_IBLOCK" value=<?= $ANKETA_IBLOCK ?>></td>
        </tr>

        <tr>
            <td><? echo GetMessage('EANK_OPT_ANKETA_ANSWERS_IBLOCK') ?></td>
            <td><input type="text" name="ANKETA_ANSWERS_IBLOCK" id="ANKETA_ANSWERS_IBLOCK" value=<?= $ANKETA_ANSWERS_IBLOCK ?>></td>
        </tr>

        <tr>
            <td><? echo GetMessage('EANK_OPT_ANKETA_CRITERIA_IBLOCK') ?></td>
            <td><input type="text" name="ANKETA_CRITERIA_IBLOCK" id="ANKETA_CRITERIA_IBLOCK" value=<?= $ANKETA_CRITERIA_IBLOCK ?>></td>
        </tr>






		  <? $tabControl->BeginNextTab(); ?>
        <? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php"); ?>


        <? $tabControl->Buttons(); ?>

        <input type="submit" name="Update" <? if ($MOD_RIGHT < "W") echo "disabled" ?>
               value="<? echo GetMessage("MAIN_SAVE") ?>">
        <input type="reset" name="reset" value="<? echo GetMessage("MAIN_RESET") ?>">
        <input type="hidden" name="Update" value="Y">

		  <?= bitrix_sessid_post(); ?>

        <? $tabControl->End(); ?>
    </form>
<? endif; ?>

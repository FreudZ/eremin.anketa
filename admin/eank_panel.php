<?
// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/eremin.anketa/include.php"); // инициализация модуля
$APPLICATION->SetAdditionalCSS("/bitrix/components/bitrix/desktop/templates/admin/style.css");
$APPLICATION->SetAdditionalCSS("/bitrix/panel/eremin.anketa/main/vidget.css");

CJSCore::Init (
   array("clipboard","jquery","date")
);

\Bitrix\Main\UI\Extension::load("ui.buttons");
\Bitrix\Main\UI\Extension::load("ui.icons");
\Bitrix\Main\UI\Extension::load("ui.buttons.icons");

// подключим языковой файл
IncludeModuleLangFile(__FILE__);
if (!CModule::IncludeModule("iblock")) return;
// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight("eremin.anketa");
// если нет прав - отправим к форме авторизации с сообщением об ошибке
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
?>
<?
$aTabs = array(
  array("DIV" => "edit1", "TAB" => GetMessage("EANK_PANEL_TAB1"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("EANK_PANEL_TAB1_TITLE")),
  array("DIV" => "edit2", "TAB" => GetMessage("EANK_PANEL_TAB2"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("EANK_PANEL_TAB2_TITLE")),
);

$date_start = date('01.m.y');
$date_end = date("t.m.y");

// Применение фильтра по дате
if(
    $REQUEST_METHOD == "POST" // проверка метода вызова страницы
//    &&
//    ($save!="" || $apply!="") // проверка нажатия кнопок "Сохранить" и "Применить"
    &&
    $POST_RIGHT=="W"          // проверка наличия прав на запись для модуля
    &&
    check_bitrix_sessid()     // проверка идентификатора сессии
)
{
  if(!empty($_POST["date_start"])){
  	$date_start = $_POST["date_start"];
  } else {
  	$date_start = date('01.m.Y');
  }
  if(!empty($_POST["date_end"])){
  	$date_end = $_POST["date_end"];
  }    else {
	$date_end = date("01.m.Y", strtotime("+1 month"));
  }
}




$tabControl = new CAdminTabControl("tabControl", $aTabs);
// здесь будет вся серверная обработка и подготовка данных
$ANKETA_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_IBLOCK", 0);
$ANKETA_ANSWERS_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_ANSWERS_IBLOCK", 0);
$ANKETA_CRITERIA_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_CRITERIA_IBLOCK", 0);
$anketaTabs = array();
$anketaTabs[] = array("DIV" => "ank_edit1", "TAB" => GetMessage("EANK_PANEL_ANK_TAB_COMMON"), "ICON"=>"main_user_edit", "TITLE"=>"");
$anketaTabs[] = array("DIV" => "ank_edit2", "TAB" => GetMessage("EANK_PANEL_ANK_TAB_V"), "ICON"=>"main_user_edit", "TITLE"=>"");
$anketaTabs[] = array("DIV" => "ank_edit3", "TAB" => GetMessage("EANK_PANEL_ANK_TAB_N"), "ICON"=>"main_user_edit", "TITLE"=>"");

$anketsTable = array();
//Получаем разделы инфоблока первого уровня (анкеты)
    $arFilter = Array('IBLOCK_ID'=>$ANKETA_IBLOCK, 'ACTIVE'=>'Y', 'DEPTH_LEVEL'=>1, 'INCLUDE_SUBSECTIONS'=>'N');
    $db_list = CIBlockSection::GetList(Array("SORT"=>"ASC"), $arFilter, true, array("*", "UF_PARAM1", "UF_PARAM2", "UF_COLOR"));
	 $arAnketa = array();
	 $arHeadings = array();
	 $ank_cnt = 1;
    while($ar_result = $db_list->GetNext())
    {
		  $arAnketa[$ar_result["ID"]] = $ar_result;
		  //$anketaTabs[] = array("DIV" => "ank_edit".$ank_cnt, "TAB" => GetMessage("EANK_PANEL_ANK_TAB", array("#ANKETA#"=>$ar_result["NAME"])), "ICON"=>"main_user_edit", "TITLE"=>"");
		  $ank_cnt++;
		  //Таблица для построения вывода данных
		  $anketsTable[$ar_result["ID"]] = array("NAME"=>$ar_result["NAME"], "COLOR"=>$ar_result["UF_COLOR"], "QUESTIONS"=>array());

	 }


//Получаем вопросы анкет и группируем по анкетам
$allAnswers = array();
$arSelect = Array("ID", "NAME", "IBLOCK_SECTION_ID", "IBLOCK_ID", "PROPERTY_SHOW_AS_SUBHEADER");
$arFilter = Array("IBLOCK_ID"=>IntVal($ANKETA_IBLOCK), "INCLUDE_SUBSECTIONS"=>"Y", "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array("SORT"=>"ASC"), $arFilter, false, false, $arSelect);
while($ob = $res->GetNextElement()){
 $arFields = $ob->GetFields();
 $arProps = $ob->GetProperties();

 if($arProps["SHOW_AS_SUBHEADER"]["VALUE"]!="Y"){
 $allAnswers[$arFields["ID"]] = $arFields["NAME"];
 $rsPathToParent = GetIBlockSectionPath($ANKETA_IBLOCK, $arFields["IBLOCK_SECTION_ID"]);
 $arPathToParent = $rsPathToParent->Fetch();
 $anketsTable[$arPathToParent["ID"]]["QUESTIONS"][$arFields["ID"]] = array();
 }
}

//Получаем критерии оценки для вопросов
$arCriteria = array();
$arSelect = Array("ID", "NAME", "IBLOCK_ID","PROPERTY_*");
$arFilter = Array("IBLOCK_ID"=>IntVal($ANKETA_CRITERIA_IBLOCK), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while($ob = $res->GetNextElement()){
 $arFields = $ob->GetFields();
 $arCriteria[$arFields["ID"]] = $arFields["NAME"];
}

//Собираем ответы и группируем по организациям

function GetFullOrgList($res){
	$result = array("ORG");
	foreach($res as $element){
	$result["ORG"][$element["NAME"]] = array();//Для объединения ответов в разрезе организации ключем принято считать имя организации
	$result["ORG_NAMES"][$element["ID"]] = $element["NAME"];
 }
	return $result;
}

$arAnswers = array();
$arSelect = Array("ID", "NAME", "IBLOCK_ID","PROPERTY_*");

$arFilter = Array("IBLOCK_ID"=>IntVal($ANKETA_ANSWERS_IBLOCK), "PROPERTY_ANKETA_ID_VALUE"=>array_keys($arAnketa), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y", ">=DATE_CREATE"=>date('d.m.Y H:i:s',MakeTimeStamp($date_start)), "<=DATE_CREATE"=>date('d.m.Y 23:59:59',MakeTimeStamp($date_end)));

$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
$arTmpArray = array();
while($ob = $res->GetNextElement()){ //Заполняем временный массив, что бы не делать несколько одинаковых выборок
 $arFields = $ob->GetFields();
 $arFields["PROPERTIES"] = $ob->GetProperties();
 $arTmpArray[$arFields["ID"]] = $arFields;
}



$arFinalAnswersByOrg = array();
$arFullOrgList = GetFullOrgList($arTmpArray);//Полный список организаций
foreach($arTmpArray as $ktmp=>$arElement){
 foreach($arElement["PROPERTIES"]["ANSWERS"]["VALUE"] as $k=>$arAnswer){
 $anketsTable[$arElement["PROPERTIES"]["ANKETA_ID"]["VALUE"]]["QUESTIONS"][$arAnswer]["ORG"] = $arFullOrgList["ORG"];
 }
 }

foreach($arTmpArray as $ktmp=>$arElement){
 $arFields = $arElement;
 $arProps = $arElement["PROPERTIES"];

 $arPropAnswers = array();

 foreach($arProps["ANSWERS"]["VALUE"] as $k=>$arAnswer){

	 $unser = unserialize(htmlspecialchars_decode($arProps["ANSWERS"]["DESCRIPTION"][$k]));
 	 $arPropAnswers[$arAnswer] = $unser;
	 $arFinalAnswersByOrg[$arProp["ORGANIZATION"]["VALUE"]]["NAME"] = $arFields["NAME"];
	 $arFinalAnswersByOrg[$arProp["ORGANIZATION"]["VALUE"]]["ANKETS"][$arProps["ANKETA_ID"]["VALUE"]] = $unser;

	 $anketsTable[$arProps["ANKETA_ID"]["VALUE"]]["QUESTIONS"][$arAnswer]["ORG"][$arFields["NAME"]] = $unser;
	 $arAnswers[$arProps["ANKETA_ID"]["VALUE"]][$arFields["ID"]] = array("ORGANIZATION"=>$arFields["NAME"], "ANSWER_TEXT" => $k, "ANSWERS"=>$arPropAnswers );
 }
}

 //Добавим пустые массивы организаций для случая если ответов на анкету нет за указанный период
 foreach($anketsTable as $ak=>$anketa){
	foreach($anketa["QUESTIONS"] as $kQ=>$question){
		if(!count($question)){
		 $anketsTable[$ak]["QUESTIONS"][$kQ]["ORG"] = $arFullOrgList["ORG"];
		}
	}
 }

unset($arTmpArray);

/* Генерация Excel */
if(
    $REQUEST_METHOD == "POST" // проверка метода вызова страницы
    &&
    $POST_RIGHT=="W"          // проверка наличия прав на запись для модуля
    &&
    check_bitrix_sessid()     // проверка идентификатора сессии
)
{
  if($_POST["action"]=='genexcel'){
  	// Create new PHPExcel object

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/eremin.anketa/lib/PHPExcel.php"); // инициализация модуля
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/eremin.anketa/lib/anketatools.php"); // инициализация модуля
$objPHPExcel = new PHPExcel();

// Set document properties

$objPHPExcel->getProperties()->setCreator('test')
    ->setLastModifiedBy('test')
    ->setTitle('test')
    ->setSubject('test')
    ->setDescription('test')
    ->setKeywords('test')
    ->setCategory('test');


  //Заполняем листы
  $objWorkSheet = $objPHPExcel->GetActiveSheet();/* номер листа */
  $objWorkSheet->setTitle(GetMessage('EANK_EXCEL_WORKSHEET1_TITLE'));
  //Заполняем общую таблицу

  AnketaTools::createExcelTableOnSheet($objPHPExcel, 0, $arFullOrgList, $arAnketa, $anketsTable, $allAnswers);





  /* след. лист */
  $objWorkSheet = $objPHPExcel->createSheet(1);/* номер листа */
  $objWorkSheet->setTitle(GetMessage('EANK_EXCEL_WORKSHEET2_TITLE'));
  AnketaTools::createExcelTableOnSheet($objPHPExcel, 1, $arFullOrgList, $arAnketa, $anketsTable, $allAnswers);

  /* след. лист */
  $objWorkSheet = $objPHPExcel->createSheet(2);/* номер листа */
  $objWorkSheet->setTitle(GetMessage('EANK_EXCEL_WORKSHEET3_TITLE'));

  AnketaTools::createExcelTableOnSheet($objPHPExcel, 2, $arFullOrgList, $arAnketa, $anketsTable, $allAnswers);
	$objPHPExcel->setActiveSheetIndex(0);
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	//$filename = $_SERVER["DOCUMENT_ROOT"].'/upload/anketa/anketa.xlsx';
	$filename = $_SERVER["DOCUMENT_ROOT"].'/upload/anketa/anketa.xlsx';
	$rel_filename = '/upload/anketa/anketa.xlsx';
	$sem = sem_get(1);
	if ( sem_acquire($sem) && file_exists($filename) ) @unlink($filename);
	sem_remove($sem);
	$objWriter->setPreCalculateFormulas(true);
  $objWriter->save($filename);





  } //genexcel


}
/* Генерация Excel */


?>
<?


// ******************************************************************** //
//                ВЫВОД                                                 //
// ******************************************************************** //


// установим заголовок страницы
$APPLICATION->SetTitle(GetMessage("SIOKE_LIST_PAGE_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // второй общий пролог
?>
<style type="text/css">
    .vertical_text{
/*      -webkit-transform: rotate(-90deg);
      -moz-transform: rotate(-90deg);
      -ms-transform: rotate(-90deg);
      -o-transform: rotate(-90deg);
      transform: rotate(-90deg);
		word-wrap: break-word;*/
    }
	 .adm_anketa_table td,
	 .adm_anketa_table th{
		 padding:2px 5px;
	 }

</style>
<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" id="anketa_post_form" name="anketa_post_form">
<?// проверка идентификатора сессии ?>
<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?
// отобразим заголовки закладок
$tabControl->Begin();
?>
<?
$tabControl->BeginNextTab(); ?>
 <tr>
 	<td><?= GetMessage("EANK_PANEL_ANK_DATE") ?></td>
 	<td><?= GetMessage("EANK_PANEL_ANK_DATE_START") ?> <input type="text" value="<?=$date_start?>" name="date_start" onclick="BX.calendar({node: this, field: this, bTime: false});"> <?= GetMessage("EANK_PANEL_ANK_DATE_END") ?> <input type="text" value="<?= $date_end ?>" name="date_end" onclick="BX.calendar({node: this, field: this, bTime: false});">  <button class="ui-btn ui-btn-primary ui-btn-xs" onclick="BX('anketa_post_form').submit()"><?= GetMessage('EANK_PANEL_ANK_DATE_APPLY') ?></button>
	&nbsp;&nbsp;<button class="ui-btn ui-btn-success ui-btn-xs ui-btn-icon-download" type="button" onclick="GenExcelStart();"><?= GetMessage('EANK_EXCEL_DOWLOAD') ?></button></td>
 </tr>
 <tr>
 	<td colspan="2"></td>
 </tr>
<script>
function GenExcelStart(){
	var input_gen = BX.create('input', {'attrs':{'type':'hidden', 'name':'action', 'value':'genexcel'}});
	BX.prepend(input_gen, BX('anketa_post_form'));
	BX('anketa_post_form').submit();
	return false;
}
</script>


 <tr><td colspan="2">
<?	 $anketaTabControl = new CAdminTabControl("anketaTabControl", $anketaTabs);
	 $anketaTabControl->Begin();

	 	$anketaTabControl->BeginNextTab();?>

<!--		<tr class="heading">
			<td colspan="2" ><?= GetMEssage("EANK_PANEL_ANK_TAB_COMMMON_HEADING") ?></td>
		</tr>-->

		<tr>
			<td colspan="2">
<div class="bx-gadgets-colourful bx-gadgets bx-gadgets-perfmon" id="tADMIN_SITESPEED@381271605">
					<div class="bx-gadgets-content">

<div class="bx-gadgets-speed-top bx-gadgets-speed-ready" id="speed-top-block">
	<div class="bx-gadgets-title bx-gadgets-title-speed">
		<?=GetMessage('EANK_VIDGET_TITLE');?>
	</div>
	<div class="bx-gadget-speed-num-block" id="bx-gadget-speed-num-block" >
		<span class="bx-gadget-speed-num" id="i_avg_header"></span>
	</div>
</div>
<div class="bx-gadget-speed-speedo-block">
	<div class="bx-gadget-speed-ruler">
	<span class="bx-gadget-speed-ruler-start">-1</span>
	<span class="bx-gadget-speed-ruler-04">0,4</span>
	<span class="bx-gadget-speed-ruler-07">0,7</span>
	<span class="bx-gadget-speed-ruler-end">1</span>
	</div>
	<div class="bx-gadget-speed-graph">
	   <span class="bx-gadget-speed-graph-part bx-gadget-speed-graph-veryslow">
			<span class="bx-gadget-speed-graph-text"><?=GetMessage('EANK_VIDGET_LOW')?></span>
		</span><span class="bx-gadget-speed-graph-part bx-gadget-speed-graph-notfast">
			<span class="bx-gadget-speed-graph-text"><?=GetMessage('EANK_VIDGET_MID')?></span>
		</span><span class="bx-gadget-speed-graph-part bx-gadget-speed-graph-fast">
			<span class="bx-gadget-speed-graph-text"><?=GetMessage('EANK_VIDGET_HI')?></span>
		</span><div class="bx-gadget-speed-pointer" id="site-speed-pointer">
			<div class="bx-gadget-speed-value" id="site-speed-pointer-index"></div>
		</div>
	</div>
</div>


</div>
				</div>
			</td>
		</tr>
		<?if(count($arAnswers)<=0):?>
		 <tr>
		 	<td colspan="2"><?= GetMessage("OPEN_ANK_ANSWERS_NOT_FOUND"); ?></td>
		 </tr>
		<?else:?>
		 <tr>
		 	<td colspan="2">
		 		<table border="1px" class="adm_anketa_table" style="width: 100%; border-size: 1px; border-collapse: collapse; padding:5px;">
		 			<tr  style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>">
		 				<th width="20%"><?= GetMessage("OPEN_ANK_TABLE_HEADING") ?></th>
						<?//foreach($arAnswers[$anketa["ID"]] as $answ_k=>$answ_val):?>
						<?foreach($arFullOrgList["ORG"] as $org_k=>$org_id):?>
						<th colspan="2" class="vertical_text">
						  <?= $org_k ?>
						</th>
						<?endforeach;?>
						<th rowspan="2"><?= GetMessage('OPEN_ANK_TABLE_VCP') ?></th>
						<th rowspan="2"><?= GetMessage('OPEN_ANK_TABLE_NCP') ?></th>
						<th rowspan="2"><?= GetMessage('OPEN_ANK_TABLE_I') ?></th>
					</tr>
					<tr>
						<th style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING2_BG_COLOR')?>"><?=GetMessage("OPEN_ANK_TABLE_CHARACT_HEADING")?></th>
						<?for($i=0;$i<count($arFullOrgList["ORG"]); $i++):?>
						<th style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING2_BG_COLOR')?>">V</th>
						<th style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING2_BG_COLOR')?>">N</th>
						<?endfor;?>

					</tr>
					  <?$arMidValue = array();
					    $arMidQuestion = array();
						 $QuestionsCnt = 0;

					  ?>
				  <? foreach($arAnketa as $anketa): //Выводим общую информацию по анкетам  ?>
					 <? $arAnk = $anketsTable[$anketa["ID"]]; ?>
					  <?foreach($arAnk["QUESTIONS"] as $q_id=>$arQuestion):?>

					  <? $QuestionsCnt++; ?>
					  <tr>
					  	<td style="background-color:<?=$arAnk["COLOR"]?>"><?= $allAnswers[$q_id]  ?></td>
						<? $ORGCnt = 0; ?>
						<?foreach($arQuestion["ORG"] as $org_id=>$arOrg):?>
						<? $ORGCnt++; ?>
						<? //сумма по вопросам в строки
						$arMidQuestion[$q_id]["V"] = $arMidQuestion[$q_id]["V"]+$arOrg[$anketa["UF_PARAM1"]];
						$arMidQuestion[$q_id]["V_Q_CNT"] = $arMidQuestion[$q_id]["V_Q_CNT"]+(intval($arOrg[$anketa["UF_PARAM1"]])>0?1:0);
						$arMidQuestion[$q_id]["N"] = $arMidQuestion[$q_id]["N"]+$arOrg[$anketa["UF_PARAM2"]];
						$arMidQuestion[$q_id]["N_Q_CNT"] = $arMidQuestion[$q_id]["N_Q_CNT"]+(intval($arOrg[$anketa["UF_PARAM2"]])>0?1:0);
						 $VRcp = round($arMidQuestion[$q_id]["V"]/$arMidQuestion[$q_id]["V_Q_CNT"],2);
						 $NRcp = round($arMidQuestion[$q_id]["N"]/$arMidQuestion[$q_id]["N_Q_CNT"],2);
						 $IRorg = ($NRcp-3)*($VRcp*$VRcp)/50;
						 if(!empty($IRorg))
						$arMidQuestion[$q_id]["I"] = $IRorg;

						//Сумма по организациям в колонки
						$arMidValue[$org_id]["V"] = $arMidValue[$org_id]["V"]+$arOrg[$anketa["UF_PARAM1"]];
						$arMidValue[$org_id]["V_Q_CNT"] = $arMidValue[$org_id]["V_Q_CNT"]+(intval($arOrg[$anketa["UF_PARAM1"]])>0?1:0);
						$arMidValue[$org_id]["N"] = $arMidValue[$org_id]["N"]+$arOrg[$anketa["UF_PARAM2"]];
						$arMidValue[$org_id]["N_Q_CNT"] = $arMidValue[$org_id]["N_Q_CNT"]+(intval($arOrg[$anketa["UF_PARAM2"]])>0?1:0);

						 $Vcp = round($arMidValue[$org_id]["V"]/$arMidValue[$org_id]["V_Q_CNT"],2);
						 $Ncp = round($arMidValue[$org_id]["N"]/$arMidValue[$org_id]["N_Q_CNT"],2);
						 $Iorg = ($Ncp-3)*($Vcp*$Vcp)/50;

						 $arMidValue[$org_id]["I"] = $Iorg;
						?>
						  <td style="background-color:<?=$arAnk["COLOR"]?>"><?= $arOrg[$anketa["UF_PARAM1"]]  ?></td>
					     <td style="background-color:<?=$arAnk["COLOR"]?>"><?= $arOrg[$anketa["UF_PARAM2"]]  ?></td>
						 <?endforeach;?>
						 <td style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>"><?= floatval($arMidQuestion[$q_id]["V"])>0?round($arMidQuestion[$q_id]["V"]/$arMidQuestion[$q_id]["V_Q_CNT"],2):0 ?></td>
						 <td style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>"><?= floatval($arMidQuestion[$q_id]["N"])>0?round($arMidQuestion[$q_id]["N"]/$arMidQuestion[$q_id]["N_Q_CNT"],2):0 ?></td>

						 <td style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>"><?= !is_nan($arMidQuestion[$q_id]["I"])?round($arMidQuestion[$q_id]["I"],2):'' ?></td>
					  </tr>

					  <?endforeach;?>
					<?endforeach;?>
					 <tr style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>">
					 	<td><?= GetMessage('OPEN_ANK_TABLE_VCP') ?></td>
					  <?foreach($arMidValue as $org=>$Vmid):?>
						<td colspan="2"><?= intval($Vmid["V_Q_CNT"])>0?round($Vmid["V"]/$Vmid["V_Q_CNT"],2):0; ?></td>
					  <?endforeach;?>
					  <? $Index = 0;
							$valCnt = 0;
							$valSum = 0;
							foreach($arMidValue as $org=>$Vmid){
							if(!is_nan($Vmid["I"])){
							  $valCnt++;
							}
							  $valSum = $valSum+$Vmid["I"];
							}
							$Index = round(($valSum/$valCnt),2);?>
					  <td colspan="3" rowspan="3" id="i_avg_table" data-index="<?= $Index ?>"><b>

						  <? echo  GetMessage('OPEN_ANK_TABLE_ICP_ORG').$Index;?></b>
					  </td>
					 </tr>

					 <tr style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>">
					 	<td><?= GetMessage('OPEN_ANK_TABLE_NCP') ?></td>
					  <?foreach($arMidValue as $org=>$Nmid):?>
						<td colspan="2"><?= intval($Nmid["N_Q_CNT"])>0?round($Nmid["N"]/$Nmid["N_Q_CNT"],2):0; ?></td>
					  <?endforeach;?>
					 </tr>
					 <tr style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>">
					 	<td><?= GetMessage('OPEN_ANK_TABLE_I') ?></td>
					  <?foreach($arMidValue as $org=>$Vmid):?>
					   <? //calculate I
						 $Vcp = round($Vmid["V"]/$Vmid["V_Q_CNT"],2);
						 $Ncp = round($Vmid["N"]/$Vmid["N_Q_CNT"],2);
						 $Iorg = ($Ncp-3)*($Vcp*$Vcp)/50;

						 ?>
						<td colspan="2" ><?=round($Vmid["I"],2) ?></td>
					  <?endforeach;?>
					 </tr>
		 		</table>

		 	</td>
		 </tr>
		 <?endif;//count $arAnswers?>
		<?

	 $anketaTabControl->BeginNextTab();//Важность?>
		<?if(count($arAnswers)<=0):?>
		 <tr>
		 	<td colspan="2"><?= GetMessage("OPEN_ANK_ANSWERS_NOT_FOUND"); ?></td>
		 </tr>
		<?else:?>
		 <tr>
		 	<td colspan="2">
		 		<table border="1px" class="adm_anketa_table" style="width: 100%; border-size: 1px; border-collapse: collapse; padding:5px;">
		 			<tr style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>">
		 				<th width="20%"><?= GetMessage("OPEN_ANK_TABLE_HEADING_V") ?></th>
						<?//foreach($arAnswers[$anketa["ID"]] as $answ_k=>$answ_val):?>
						<?foreach($arFullOrgList["ORG"] as $org_k=>$org_id):?>
						<th class="vertical_text">
						  <?= $org_k ?>
						</th>
						<?endforeach;?>
						<th rowspan="2"><?= GetMessage('OPEN_ANK_TABLE_VCP') ?></th>
					</tr>
					<tr>
						<th  style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING2_BG_COLOR')?>"><?=GetMessage("OPEN_ANK_TABLE_CHARACT_HEADING")?></th>
						<?for($i=0;$i<count($arFullOrgList["ORG"]); $i++):?>
						<th  style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING2_BG_COLOR')?>">V</th>
						<?endfor;?>

					</tr>
					  <?$arMidValue = array();
					    $arMidQuestion = array();
						 $QuestionsCnt = 0;
						 $ORGCnt = 0;
					  ?>
				  <? foreach($arAnketa as $anketa): //Выводим общую информацию по анкетам  ?>
					 <? $arAnk = $anketsTable[$anketa["ID"]]; ?>
					  <?foreach($arAnk["QUESTIONS"] as $q_id=>$arQuestion):?>

					  <? $QuestionsCnt++; ?>
					  <tr>
					  	<td style="background-color:<?=$arAnk["COLOR"]?>"><?= $allAnswers[$q_id]  ?></td>
						<?foreach($arQuestion["ORG"] as $org_id=>$arOrg):?>
						<? $ORGCnt++; ?>
						<? //сумма по вопросам в строки
						$arMidQuestion[$q_id]["V"] = $arMidQuestion[$q_id]["V"]+$arOrg[$anketa["UF_PARAM1"]];
						$arMidQuestion[$q_id]["V_Q_CNT"] = $arMidQuestion[$q_id]["V_Q_CNT"]+(intval($arOrg[$anketa["UF_PARAM1"]])>0?1:0);

						//Сумма по организациям в колонки
						$arMidValue[$org_id]["V"] = $arMidValue[$org_id]["V"]+$arOrg[$anketa["UF_PARAM1"]];
						$arMidValue[$org_id]["V_Q_CNT"] = $arMidValue[$org_id]["V_Q_CNT"]+(intval($arOrg[$anketa["UF_PARAM1"]])>0?1:0);
						?>
						  <td style="background-color:<?=$arAnk["COLOR"]?>"><?= $arOrg[$anketa["UF_PARAM1"]]  ?></td>
						 <?endforeach;?>
						 <td style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>"><?= floatval($arMidQuestion[$q_id]["V"])>0?round($arMidQuestion[$q_id]["V"]/$arMidQuestion[$q_id]["V_Q_CNT"],2):0 ?></td>

					  </tr>

					  <?endforeach;?>
					<?endforeach;?>
					 <tr  style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>">
					 	<td><?= GetMessage('OPEN_ANK_TABLE_VCP') ?></td>
					  <?foreach($arMidValue as $org=>$Vmid):?>
						<td ><?= intval($Vmid["V_Q_CNT"])>0?round($Vmid["V"]/$Vmid["V_Q_CNT"],2):0; ?></td>
					  <?endforeach;?>
					 </tr>
		 		</table>

		 	</td>
		 </tr>
		 <?endif;//count $arAnswers?>
	 <? $anketaTabControl->BeginNextTab(); //Удовлетворенность?>
	<?if(count($arAnswers)<=0):?>
		 <tr>
		 	<td colspan="2"><?= GetMessage("OPEN_ANK_ANSWERS_NOT_FOUND"); ?></td>
		 </tr>
		<?else:?>
		 <tr>
		 	<td colspan="2">
		 		<table border="1px" class="adm_anketa_table" style="width: 100%; border-size: 1px; border-collapse: collapse; padding:5px;">
		 			<tr style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>">
		 				<th width="20%"><?= GetMessage("OPEN_ANK_TABLE_HEADING_N") ?></th>
						<?//foreach($arAnswers[$anketa["ID"]] as $answ_k=>$answ_val):?>
						<?foreach($arFullOrgList["ORG"] as $org_k=>$org_id):?>
						<th class="vertical_text">
						  <?= $org_k ?>
						</th>
						<?endforeach;?>
						<th rowspan="2"><?= GetMessage('OPEN_ANK_TABLE_NCP') ?></th>
					</tr>
					<tr>
						<th  style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING2_BG_COLOR')?>"><?=GetMessage("OPEN_ANK_TABLE_CHARACT_HEADING")?></th>
						<?for($i=0;$i<count($arFullOrgList["ORG"]); $i++):?>
						<th  style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING2_BG_COLOR')?>">N</th>
						<?endfor;?>

					</tr>
					  <?$arMidValue = array();
					    $arMidQuestion = array();
						 $QuestionsCnt = 0;
						 $ORGCnt = 0;
					  ?>
				  <? foreach($arAnketa as $anketa): //Выводим общую информацию по анкетам  ?>
					 <? $arAnk = $anketsTable[$anketa["ID"]]; ?>
					  <?foreach($arAnk["QUESTIONS"] as $q_id=>$arQuestion):?>

					  <? $QuestionsCnt++; ?>
					  <tr>
					  	<td style="background-color:<?=$arAnk["COLOR"]?>"><?= $allAnswers[$q_id]  ?></td>
						<?foreach($arQuestion["ORG"] as $org_id=>$arOrg):?>
						<? $ORGCnt++; ?>
						<? //сумма по вопросам в строки
						$arMidQuestion[$q_id]["N"] = $arMidQuestion[$q_id]["N"]+$arOrg[$anketa["UF_PARAM2"]];
						$arMidQuestion[$q_id]["N_Q_CNT"] = $arMidQuestion[$q_id]["N_Q_CNT"]+(intval($arOrg[$anketa["UF_PARAM2"]])>0?1:0);

						//Сумма по организациям в колонки
						$arMidValue[$org_id]["N"] = $arMidValue[$org_id]["N"]+$arOrg[$anketa["UF_PARAM2"]];
						$arMidValue[$org_id]["N_Q_CNT"] = $arMidValue[$org_id]["N_Q_CNT"]+(intval($arOrg[$anketa["UF_PARAM2"]])>0?1:0);
						?>
						  <td style="background-color:<?=$arAnk["COLOR"]?>"><?= $arOrg[$anketa["UF_PARAM2"]]  ?></td>
						 <?endforeach;?>
						 <td style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>"><?= floatval($arMidQuestion[$q_id]["N"])>0?round($arMidQuestion[$q_id]["N"]/$arMidQuestion[$q_id]["N_Q_CNT"],2):0 ?></td>
					  </tr>

					  <?endforeach;?>
					<?endforeach;?>
					 <tr  style="background-color:<?=GetMessage('OPEN_ANK_TABLE_HEADING_BG_COLOR')?>">
					 	<td><?= GetMessage('OPEN_ANK_TABLE_NCP') ?></td>
					  <?foreach($arMidValue as $org=>$Vmid):?>
						<td ><?= intval($Vmid["N_Q_CNT"])>0?round($Vmid["N"]/$Vmid["N_Q_CNT"],2):0; ?></td>
					  <?endforeach;?>
					 </tr>

		 		</table>

		 	</td>
		 </tr>
		 <?endif;//count $arAnswers?>
	  <?	$anketaTabControl->End();



?>
</td></tr>


<?
$tabControl->BeginNextTab();     ?>
   		<tr class="heading">
			<td colspan="3"><?= GetMEssage("EANK_PANEL_ANKETS_LIST_HEADING") ?></td>
		</tr>
<?
  foreach($arAnketa as $anketa){ //Выводим общую информацию по анкетам
?>

 		<tr>
			<td><span style="color:#330099"><?= $anketa["NAME"] ?>:</span> </td>
			<td><span id="ankLincCopyText<?= $anketa["ID"] ?>"><b><?= $_SERVER["SERVER_NAME"] ?><?= $anketa["SECTION_PAGE_URL"] ?></b></span></td>
			<td>
				   <button class="ui-btn ui-btn-primary ui-btn-xs" id="ankLincCopyBtn<?= $anketa["ID"] ?>"><?= GetMessage("COPY_TO_CLIPBOARD")?></button> <a class="ui-btn ui-btn-success ui-btn-xs" href="<?= $anketa["SECTION_PAGE_URL"] ?>" target="_blank"><?= GetMessage("OPEN_ANK_IN_NEW_TAB") ?></a>
			 <script>
			 BX.clipboard.bindCopyClick(
    BX('ankLincCopyBtn<?= $anketa["ID"] ?>'),
    {
        text: BX('ankLincCopyText<?= $anketa["ID"] ?>')
    }
);
          </script>
			</td>
		</tr>


 <? }
// завершаем интерфейс закладки
$tabControl->End();
?>
</form>
<script>
$(document).ready(function(){
 	 var i_org = $('#i_avg_table').data('index');


				var siteIndexPercent = i_org*100/2;
				//siteIndexPercent = Math.min(Math.max(siteIndexPercent, 4), 98);
				if($('#i_avg_table').data('index')<0){
					siteIndexPercent = 50+siteIndexPercent;
				} else {
					siteIndexPercent = 50-siteIndexPercent;
				}
				$("#site-speed-index").html(i_org);
				$("#site-speed-pointer-index").html(i_org);
				$("#speed-top-block").addClass("bx-gadgets-speed-ready");
				$("#site-speed-pointer").css('left',siteIndexPercent + "%");

	$('#i_avg_header').html(i_org);
});




  var filename = '<?=$rel_filename;?>';
  if(filename.length){
	window.open('<?= $rel_filename ?>', "_blank");
  }
</script>
<?
// дополнительное уведомление об ошибках - вывод иконки около поля, в котором возникла ошибка
$tabControl->ShowWarnings("post_form", $message);
?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
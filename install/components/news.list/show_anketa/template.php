<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
define("NEED_AUTH", true);
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$this->addExternalCss($templateFolder."/css/bootstrap.min.css");
$this->addExternalCss($templateFolder."/css/style.css");
?>
<div class="anketa-box">
<div class="container-fluid">
 <div class="row">
 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
 <?= htmlspecialchars_decode($arResult["ANKETA"]["DESCRIPTION"]); ?>
</div>
</div>
</div>
	  <? $anketa_param1 = $arResult["ANKETA"]["TABLE_PARAM_1"]["ID"]; ?>
	  <? $anketa_param2 = $arResult["ANKETA"]["TABLE_PARAM_2"]["ID"]; ?>
<form id="anketa"  name="anketa" action="javascript:void(null);" method="POST" enctype="multipart/form-data">
	<input type="hidden" value="add" name="ACTION">
	<input type="hidden" value="<?= $arResult["ANKETA"]["ID"] ?>" name="ANKETA_ID">
	<input type="hidden" value="<?= $anketa_param1 ?>" name="PARAM1_ID">
	<input type="hidden" value="<?= $anketa_param2 ?>" name="PARAM2_ID">
<div class="container-fluid">
 <div class="row">
 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
 	<div class="form-group">
 		<input type="text" class="form-control required" name="ORGANIZATION" placeholder="* <?= GetMessage('ANKETA_INPUT_ORGANIZATION_PLACEHOLDER') ?>">
 	</div>
 </div>
 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
 	<div class="form-group">
 		<input type="text" class="form-control required" name="FIO" placeholder="* <?= GetMessage('ANKETA_INPUT_FIO_PLACEHOLDER') ?>">
 	</div>
	<div class="form-group">
 		<input type="text" class="form-control required" name="DOLGNOST" placeholder="* <?= GetMessage('ANKETA_INPUT_DOLGNOST_PLACEHOLDER') ?>">
 	</div>
</div>
 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
 	<div class="form-group">
 		<input type="text" class="form-control required" name="PHONE" placeholder="* <?= GetMessage('ANKETA_INPUT_PHONE_PLACEHOLDER') ?>">
 	</div>
	<div class="form-group">
 		<input type="text" class="form-control " name="EMAIL" placeholder="<?= GetMessage('ANKETA_INPUT_EMAIL_PLACEHOLDER') ?>">
 	</div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<?= htmlspecialchars_decode($arResult["ANKETA"]["UF_DESCRIPTION1"]); ?>
</div>
</div>

<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
  <table class="table table-bordered rb-box">
	  <tr class="active">
	  	<th class="col-md-4"><?= $arResult["ANKETA"]["TABLE_PARAM_1"]["NAME"] ?></th>
	  	<th class="col-md-4"><?=GetMessage("ANKETA_TABLE_CENTER_HEADER")?></th>
	  	<th class="col-md-4"><?= $arResult["ANKETA"]["TABLE_PARAM_2"]["NAME"] ?></th>
	  </tr>

	  <?foreach($arResult["HEADINGS"] as $ks=>$arSection):?>
		<?if($arSection["UF_SHOW_HEADER"]==1):?>
		  <tr class="active">
		  	<td></td>
		  	<td><strong><?= $arSection["NAME"] ?></strong></td>
		  	<td></td>
		  </tr>
		<?endif;?>
	  	 <? foreach ($arResult["ITEMS"][$ks]["ITEMS"] as $key => $value) :  ?>
		  <tr>
		  	<td>

  	<?if($value['PROPERTIES']['SHOW_AS_SUBHEADER']['VALUE']!='Y'):?>
	<div id="rb-<?= $value["ID"] ?>" class="rb">
	<?if(!in_array($anketa_param1,$value["PROPERTIES"]["HIDE_ANSWER_FIELDS"]["VALUE"])):?>
  	<?for($i=1;$i<=5;$i++):?>
    <div class="rb-tab cont_<?= $anketa_param1 ?>_<?= $value["ID"] ?>_<?= $i ?>" id="cont_<?= $anketa_param1 ?>_<?= $value["ID"] ?>" data-value="<?= $i ?>" data-param-id="<?= $anketa_param1 ?>" data-question-id="<?= $value["ID"] ?>">
      <div class="rb-spot">
        <span class="rb-txt" data-option-name="QUESTION_<?= $anketa_param1 ?>_<?= $value["ID"] ?>"><?= $i ?></span>
		  <input type="radio" class="required" data-param-id="<?= $anketa_param1 ?>" data-question-id="<?= $value["ID"] ?>" name="QUESTION_<?= $anketa_param1 ?>_<?= $value["ID"] ?>" id="QUESTION_<?= $anketa_param1 ?>_<?= $value["ID"] ?>" value="<?= $i ?>">
      </div>
    </div>
	<?endfor;?>
	<?endif;?>
  </div>
  <?endif;?>
		  	</td>
		  	<td <?if(!empty($value['PROPERTIES']['ADD_CSS_CLASS']['VALUE'])):?>class="<?= $value['PROPERTIES']['ADD_CSS_CLASS']['VALUE'] ?>"<?endif;?>>
		  		<?if($value['PROPERTIES']['SHOW_BOLD']['VALUE']=='Y'):?>
				<strong>
				<?endif;?>
		  		<?= $value["NAME"] ?>
		  		<?if($value['PROPERTIES']['SHOW_BOLD']['VALUE']=='Y'):?>
				</strong>
				<?endif;?>
			</td>
		  	<td>
<?if($value['PROPERTIES']['SHOW_AS_SUBHEADER']['VALUE']!='Y'):?>
<div id="rb-<?= $value["ID"] ?>" class="rb">
		<?if(!in_array($anketa_param2,$value["PROPERTIES"]["HIDE_ANSWER_FIELDS"]["VALUE"])):?>
  	<?for($i=1;$i<=5;$i++):?>
    <div class="rb-tab cont_<?= $anketa_param2 ?>_<?= $value["ID"] ?>_<?= $i ?>" id="cont_<?= $anketa_param2 ?>_<?= $value["ID"] ?>" data-value="<?= $i ?>" data-param-id="<?= $anketa_param2 ?>" data-question-id="<?= $value["ID"] ?>">
      <div class="rb-spot">
        <span class="rb-txt" data-option-name="QUESTION_<?= $anketa_param2 ?>_<?= $value["ID"] ?>"><?= $i ?></span>
		  <input type="radio" class="required" data-param-id="<?= $anketa_param2 ?>" data-question-id="<?= $value["ID"] ?>"  name="QUESTION_<?= $anketa_param2 ?>_<?= $value["ID"] ?>" id="QUESTION_<?= $anketa_param2 ?>_<?= $value["ID"] ?>" value="<?= $i ?>">
      </div>
    </div>
	<?endfor;?>
	<?endif;?>
  </div>
  <?endif;?>
		  	</td>
		  </tr>
	    <?endforeach;?>
	  <?endforeach;?>
  </table>
  <div class="form-group">
  	<label for="COMMENTS"><?= GetMessage("ANKETA_INPUT_COMMENT_PLACEHOLDER") ?></label>
  <textarea class="form-control" name="COMMENTS" rows="3"></textarea>
  </div>

  	<div class="error_text hidden bg-danger text-danger ">
		 <?= GetMessage('ANKETA_ERROR_TEXT'); ?>
  	</div>
	<div class="template success_message"><?= $arResult["ANKETA"]["UF_SUCCESS_MESSAGE"] ?></div>
  <div class="form-group text-center">
  <input class="btn btn-primary" id="submit_button" type="button" value="<?= GetMessage("ANKETA_BUTTON_SUBMIT_TEXT") ?>">
  </div>
</div>



</div>
</div>
</form>
<? if($GLOBALS["USER"]->IsAdmin()): ?>
<input class="btn btn-primary" id="addData_button" type="button" value="Заполнить тестовыми данными" >
<?endif;?>
 <div class="results"></div>
 </div> 
 <script>
 var TEMPLATE_FOLDER = '<?= $templateFolder ?>';
 var REDIRECT = '<?=$arResult["ANKETA"]["UF_REDIRECT"]?>';
</script>
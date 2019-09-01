<?php

Class AnketaTools {
	public static function createExcelTableOnSheet($objPHPExcel, $activeSheet = 0, $arFullOrgList, $arAnketa, $anketsTable, $allAnswers) {

		$objPHPExcel->setActiveSheetIndex($activeSheet);
		$objWorkSheet = $objPHPExcel->getActiveSheet();
		/* определяем бордер ячейкам */
		$styleBorderThin = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		$styleBorderMedium = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_MEDIUM
				)
			)
		);
		$styleBorderDataAll = array(
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'left' => array(
					'style' => PHPExcel_Style_Border::BORDER_MEDIUM
				),
				'right' => array(
					'style' => PHPExcel_Style_Border::BORDER_MEDIUM
				)
			)
		);
		$styleBorderDataLeftBold = array(
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'left' => array(
					'style' => PHPExcel_Style_Border::BORDER_MEDIUM
				),
				'right' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		$styleBorderDataRightBold = array(
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'left' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				),
				'right' => array(
					'style' => PHPExcel_Style_Border::BORDER_MEDIUM
				)
			)
		);
		/* заполняем ячейки */
		$objWorkSheet->setCellValue('A2', '');

		$currentRow = 2;
		$tmpcnt = 0;
		$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, "", true); //Левый угловой пустой блок
		$objCurrentCell->getStyle()->applyFromArray($styleBorderMedium); //Рамка
		$tmpcnt++;
		if ($activeSheet == 0) {
			foreach ($arFullOrgList["ORG"] as $org_k => $org_id) { //Заполняем шапку таблицы с названиями организаций
				$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, $arFullOrgList["ORG_NAMES"][$org_id], true);
				$objCurrentCell->getStyle()->getAlignment()->setTextRotation(90)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание по горизонтали
				$rangeCurrCell = PHPExcel_Cell::stringFromColumnIndex($tmpcnt) . $currentRow . ":" . PHPExcel_Cell::stringFromColumnIndex($tmpcnt + 1) . $currentRow; //Символьный диапазон ячеек
				$objCurrentCell = $objWorkSheet->mergeCells($rangeCurrCell);
				$objWorkSheet->getStyle($rangeCurrCell)->applyFromArray($styleBorderMedium); //Рамка
				$objWorkSheet->getStyle($rangeCurrCell)->getAlignment()->setWrapText(true); //Перенос по словам
				$objWorkSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($tmpcnt))->setWidth(5); //Ширина составляющей ячейки
				$objWorkSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($tmpcnt + 1))->setWidth(5); //Ширина составляющей ячейки
				$tmpcnt = $tmpcnt + 2;
			}
			for ($i = $tmpcnt; $i < $tmpcnt + 3; $i++) { //Дополняем тремя ячейками с тонкой рамкой для средних значений
				$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($i, $currentRow, "", true);
				$objCurrentCell->getStyle()->applyFromArray($styleBorderThin);
			}
		} else {
			foreach ($arFullOrgList["ORG"] as $org_k => $org_id) { //Заполняем шапку таблицы с названиями организаций  для раздельных листов
				$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, $arFullOrgList["ORG_NAMES"][$org_id], true);
				$objCurrentCell->getStyle()->getAlignment()->setTextRotation(90)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание по горизонтали
				$objCurrentCell->getStyle()->applyFromArray($styleBorderMedium); //Рамка
				$objCurrentCell->getStyle()->getAlignment()->setWrapText(true); //Перенос по словам
				$objWorkSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($tmpcnt))->setWidth(10); //Ширина составляющей ячейки
				$tmpcnt++;
			}

				$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, "", true);
				$objCurrentCell->getStyle()->applyFromArray($styleBorderThin);


		}
		$objWorkSheet->getRowDimension($currentRow)->setRowHeight(150); //Высота строки с заголовками организаций
		$currentRow++;

		/* подзаголовок */
		$tmpcnt = 0;
		$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, GetMessage('EANK_EXCEL_TABLE_SUBTITLE_QUESTION'), true);
		$objWorkSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($tmpcnt))->setAutoSize(true); //Ширина ячейки
		$objCurrentCell->getStyle()->applyFromArray($styleBorderMedium); //Рамка
		$objCurrentCell->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->applyFromArray(array('rgb' => 'FFFF00'));
		$tmpcnt++;
		if ($activeSheet == 0) {
			foreach ($arFullOrgList["ORG"] as $org_k => $org_id) {
				$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, "V", true);
				$objCurrentCell->getStyle()->applyFromArray($styleBorderMedium); //Рамка
				$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание по горизонтали
				$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt + 1, $currentRow, "N", true);
				$objCurrentCell->getStyle()->applyFromArray($styleBorderMedium); //Рамка
				$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание по горизонтали
				$rangeCurrCell = PHPExcel_Cell::stringFromColumnIndex($tmpcnt) . $currentRow . ":" . PHPExcel_Cell::stringFromColumnIndex($tmpcnt + 1) . $currentRow; //Символьный диапазон ячеек
				$objWorkSheet->getStyle($rangeCurrCell)->applyFromArray($styleBorderMedium); //Рамка
				$objWorkSheet->getStyle($rangeCurrCell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->applyFromArray(array('rgb' => 'FFFF00'));
				$tmpcnt = $tmpcnt + 2;
			}

			$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, GetMessage('OPEN_ANK_TABLE_VCP'), true);
			$objCurrentCell->getStyle()->applyFromArray($styleBorderMedium); //Рамка
			$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание по горизонтали
			$tmpcnt++;
			$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, GetMessage('OPEN_ANK_TABLE_NCP'), true);
			$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание по горизонтали
			$objCurrentCell->getStyle()->applyFromArray($styleBorderMedium); //Рамка
			$tmpcnt++;
			$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, GetMessage('OPEN_ANK_TABLE_I'), true);
			$objCurrentCell->getStyle()->applyFromArray($styleBorderMedium); //Рамка
			$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание по горизонтали
		} else {
			foreach ($arFullOrgList["ORG"] as $org_k => $org_id) {
				$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, $activeSheet == 1 ? "V" : "N", true);
				$objCurrentCell->getStyle()->applyFromArray($styleBorderMedium); //Рамка
				$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание по горизонтали
				$objCurrentCell->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->applyFromArray(array('rgb' => 'FFFF00'));
				$tmpcnt++;
			}

			$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, $activeSheet == 1 ? GetMessage('OPEN_ANK_TABLE_VCP') : GetMessage('OPEN_ANK_TABLE_NCP'), true);
			$objCurrentCell->getStyle()->applyFromArray($styleBorderMedium); //Рамка
			$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание по горизонтали
			$tmpcnt++;

		}

		/* Вывод строк со значениями и итоговой строки */
		$arMidValue = array();
		$arMidQuestion = array();
		$QuestionsCnt = 0;
		$topStringNumQuestion = $currentRow+1;
		$leftColumnData = 1;
		foreach ($arAnketa as $anketa) { //Выводим общую информацию по анкетам
			$arAnk = $anketsTable[$anketa["ID"]];
			foreach ($arAnk["QUESTIONS"] as $q_id => $arQuestion) { //Обход вопросов в анкете
				$tmpcnt = 0;
				$currentRow++;
				$QuestionsCnt++;
				$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, $allAnswers[$q_id], true);
				$objCurrentCell->getStyle()->applyFromArray($styleBorderDataAll); //Рамка
				$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); //Выравнивание
				$objCurrentCell->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->applyFromArray(array('rgb' => str_replace("#", "", $arAnk["COLOR"])));
				$objWorkSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($tmpcnt))->setAutoSize(false)->setWidth(50); //Ширина ячейки
				$objCurrentCell->getStyle()->getAlignment()->setWrapText(true); //Перенос по словам
				$tmpcnt = 1;
				$ORGCnt = 0;

				foreach ($arQuestion["ORG"] as $org_id => $arOrg) { //Обход организаций внутри вопроса
					$rightColumnData = $tmpcnt+1;
					$ORGCnt++;
					//сумма по вопросам в строки
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
				  if ($activeSheet == 0) {
					$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, $arOrg[$anketa["UF_PARAM1"]], true);
					$objCurrentCell->getStyle()->applyFromArray($styleBorderDataLeftBold); //Рамка
					$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); //Выравнивание
					$objCurrentCell->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->applyFromArray(array('rgb' => str_replace("#", "", $arAnk["COLOR"])));

					$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt + 1, $currentRow, $arOrg[$anketa["UF_PARAM2"]], true);
					$objCurrentCell->getStyle()->applyFromArray($styleBorderDataRightBold); //Рамка
					$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); //Выравнивание
					$objCurrentCell->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->applyFromArray(array('rgb' => str_replace("#", "", $arAnk["COLOR"])));

					$tmpcnt = $tmpcnt + 2;
					} else {
					$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, $activeSheet==1?$arOrg[$anketa["UF_PARAM1"]]:$arOrg[$anketa["UF_PARAM2"]], true);
					$objCurrentCell->getStyle()->applyFromArray($styleBorderDataLeftBold); //Рамка
					$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); //Выравнивание
					$objCurrentCell->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->applyFromArray(array('rgb' => str_replace("#", "", $arAnk["COLOR"])));

				   $tmpcnt++;
					}
				} //Обход организаций внутри вопроса

					//Тут три колонки с итогами строки
				$formulaRangeNV_Top = "$".PHPExcel_Cell::stringFromColumnIndex($leftColumnData)."$3:$" . PHPExcel_Cell::stringFromColumnIndex($rightColumnData) . "$3"; //Символьный диапазон ячеек
				$formulaRangeV = "$".PHPExcel_Cell::stringFromColumnIndex($leftColumnData) . $currentRow . ":$" . PHPExcel_Cell::stringFromColumnIndex($rightColumnData) . $currentRow; //Символьный диапазон ячеек
				$columnLetterV = PHPExcel_Cell::stringFromColumnIndex($rightColumnData+1);
				$formulaV_row = '=SUMPRODUCT('.$formulaRangeV.'*(('.$formulaRangeNV_Top.')="V"))/SUMPRODUCT((('.$formulaRangeNV_Top.')="V")*(('.$formulaRangeV.')>0))';
				$formulaN_row = '=SUMPRODUCT('.$formulaRangeV.'*(('.$formulaRangeNV_Top.')="N"))/SUMPRODUCT((('.$formulaRangeNV_Top.')="N")*(('.$formulaRangeV.')>0))';
				if ($activeSheet == 0) {
				$columnLetterN = PHPExcel_Cell::stringFromColumnIndex($rightColumnData+2);
				$columnLetterI = PHPExcel_Cell::stringFromColumnIndex($rightColumnData+3);
				$objWorkSheet->setCellValue($columnLetterV.$currentRow, $formulaV_row)->getStyle($columnLetterV.$currentRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);//Вставляем формулу в ячейку
				$objCurrentCell->getStyle($columnLetterV.$currentRow)->applyFromArray($styleBorderThin); //Рамка
				$objCurrentCell->getStyle($columnLetterV.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); //Выравнивание
				$objCurrentCell->getStyle($columnLetterV.$currentRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
				$tmpcnt++;


				$objWorkSheet->setCellValue($columnLetterN.$currentRow, $formulaN_row)->getStyle($columnLetterN.$currentRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);//Вставляем формулу в ячейку
				$objCurrentCell->getStyle($columnLetterN.$currentRow)->applyFromArray($styleBorderThin); //Рамка
				$objCurrentCell->getStyle($columnLetterN.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); //Выравнивание
				$objCurrentCell->getStyle($columnLetterN.$currentRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
				$tmpcnt++;

				//$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, (!is_nan($arMidQuestion[$q_id]["I"]) > 0 ? round($arMidQuestion[$q_id]["I"], 2) : ''), true);
				$objWorkSheet->setCellValue($columnLetterI.$currentRow, '=('.$columnLetterN.$currentRow.'-3)*('.$columnLetterV.$currentRow.'*'.$columnLetterV.$currentRow.')/50')->getStyle($columnLetterI.$currentRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);//Вставляем формулу в ячейку
				$objCurrentCell->getStyle($columnLetterI.$currentRow)->applyFromArray($styleBorderThin); //Рамка
				$objCurrentCell->getStyle($columnLetterI.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); //Выравнивание
				$objCurrentCell->getStyle($columnLetterI.$currentRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
				} else {
				//	$cell_val = $activeSheet==1?(intval($arMidQuestion[$q_id]["V"]) > 0 ? round($arMidQuestion[$q_id]["V"] / $ORGCnt, 2) : 0):(intval($arMidQuestion[$q_id]["N"]) > 0 ? round($arMidQuestion[$q_id]["N"] / $ORGCnt, 2) : 0);
				$columnLetterV = PHPExcel_Cell::stringFromColumnIndex($rightColumnData);
			   //$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, $cell_val, true);
				$objCurrentCell = $objWorkSheet->setCellValue($columnLetterV.$currentRow, ($activeSheet==1?$formulaV_row:$formulaN_row));//Вставляем формулу в ячейку

				$objCurrentCell->getStyle($columnLetterV.$currentRow)->applyFromArray($styleBorderThin); //Рамка
				$objCurrentCell->getStyle($columnLetterV.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP); //Выравнивание
				$objCurrentCell->getStyle($columnLetterV.$currentRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
				$tmpcnt++;
				}


			} //Обход вопросов в анкете
		} //Выводим общую информацию по анкетам
		$bottomStringNumQuestion = $currentRow;

		//Добавляем три итоговые строки по столбцам и задаем формулы расчета
		$tmpcnt = 0;
		$currentRow++;
		if ($activeSheet == 0) {
		$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, GetMessage('OPEN_ANK_TABLE_VCP'), true);
		$objCurrentCell->getStyle()->applyFromArray($styleBorderDataAll); //Рамка
		$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); //Выравнивание
		$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow + 1, GetMessage('OPEN_ANK_TABLE_NCP'), true);
		$objCurrentCell->getStyle()->applyFromArray($styleBorderDataAll); //Рамка
		$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); //Выравнивание
		$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow + 2, GetMessage('OPEN_ANK_TABLE_I'), true);
		$objCurrentCell->getStyle()->applyFromArray($styleBorderDataAll); //Рамка
		$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); //Выравнивание
		} else {
		$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, $activeSheet==1?GetMessage('OPEN_ANK_TABLE_VCP'):GetMessage('OPEN_ANK_TABLE_NCP'), true);
		$objCurrentCell->getStyle()->applyFromArray($styleBorderDataAll); //Рамка
		$objCurrentCell->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT); //Выравнивание
		}
		$tmpcnt++;
		foreach ($arMidValue as $org => $Vmid) {
		   $columnLetterV = PHPExcel_Cell::stringFromColumnIndex($tmpcnt); //Получаем букву текущей колонки V
			$cellsColumnV =  $columnLetterV.$topStringNumQuestion.":".$columnLetterV.$bottomStringNumQuestion; //Получаем диапазон для формулы (столбец)
		  if ($activeSheet == 0) {

			$columnLetterN = PHPExcel_Cell::stringFromColumnIndex($tmpcnt+1); //Получаем букву текущей колонки N
			$cellsColumnN =  $columnLetterN.$topStringNumQuestion.":".$columnLetterN.$bottomStringNumQuestion; //Получаем диапазон для формулы (столбец)

			//Считаем Vcp для столбца - задаем формулу
		   $rangeCurrCell_V = PHPExcel_Cell::stringFromColumnIndex($tmpcnt) . $currentRow . ":" . PHPExcel_Cell::stringFromColumnIndex($tmpcnt + 1) . $currentRow; //Символьный диапазон ячеек
			$objWorkSheet->setCellValue($columnLetterV.($currentRow), '=AVERAGE('.$cellsColumnV.')')->getStyle($rangeCurrCell_V)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);//Вставляем формулу в ячейку
			$objCurrentCell = $objWorkSheet->mergeCells($rangeCurrCell_V);
			$objWorkSheet->getStyle($rangeCurrCell_V)->applyFromArray($styleBorderDataAll); //Рамка
			$objWorkSheet->getStyle($rangeCurrCell_V)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание

			//Считаем Ncp для столбца - задаем формулу
			$rangeCurrCell_N = PHPExcel_Cell::stringFromColumnIndex($tmpcnt) . ($currentRow + 1) . ":" . PHPExcel_Cell::stringFromColumnIndex($tmpcnt + 1) . ($currentRow + 1); //Символьный диапазон ячеек
			$objWorkSheet->setCellValue($columnLetterV.($currentRow+1), '=AVERAGE('.$cellsColumnN.')')->getStyle($rangeCurrCell_N)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);//Вставляем формулу в ячейку
			$objCurrentCell = $objWorkSheet->mergeCells($rangeCurrCell_N);
			$objWorkSheet->getStyle($rangeCurrCell_N)->applyFromArray($styleBorderDataAll); //Рамка
			$objWorkSheet->getStyle($rangeCurrCell_N)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание

			//Считаем I для столбца - задаем формулу
			$rangeCurrCell = PHPExcel_Cell::stringFromColumnIndex($tmpcnt) . ($currentRow + 2) . ":" . PHPExcel_Cell::stringFromColumnIndex($tmpcnt + 1) . ($currentRow + 2); //Символьный диапазон ячеек
			$objWorkSheet->setCellValue($columnLetterV.($currentRow+2), '=('.$columnLetterV.($currentRow + 1).'-3)*('.$columnLetterV.$currentRow.'*'.$columnLetterV.$currentRow.')/50')->getStyle($rangeCurrCell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);//Вставляем формулу в ячейку
			$objCurrentCell = $objWorkSheet->mergeCells($rangeCurrCell);
			$objWorkSheet->getStyle($rangeCurrCell)->applyFromArray($styleBorderDataAll); //Рамка
			$objWorkSheet->getStyle($rangeCurrCell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание

			$tmpcnt = $tmpcnt + 2;
			} else {
			 //$cell_val = $activeSheet==1?(intval($QuestionsCnt) > 0 ? round($Vmid["V"] / $QuestionsCnt, 2) : 0):(intval($QuestionsCnt) > 0 ? round($Vmid["N"] / $QuestionsCnt, 2) : 0);
		   //$objCurrentCell = $objWorkSheet->setCellValueByColumnAndRow($tmpcnt, $currentRow, $cell_val, true);
			$objCurrentCell=$objWorkSheet->setCellValue($columnLetterV.($currentRow), '=AVERAGE('.$cellsColumnV.')');//Вставляем формулу в ячейку
			$objCurrentCell->getStyle($columnLetterV.$currentRow)->applyFromArray($styleBorderDataAll); //Рамка
			$objCurrentCell->getStyle($columnLetterV.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //Выравнивание
			$objCurrentCell->getStyle($columnLetterV.$currentRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00); //формат
			$tmpcnt++;
			}
		}
	}
}

?>

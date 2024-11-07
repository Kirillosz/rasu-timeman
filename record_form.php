<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}
//Пишу сюда изменение
use Bitrix\Main\Localization\Loc;
//Пишу сюда ещё одно изменение
$startTimeInputName = $arResult['WORKTIME_RECORD_FORM_NAME'] . '[recordedStartTime]';
$endTimeInputName = $arResult['WORKTIME_RECORD_FORM_NAME'] . '[recordedStopTime]';
$breakLengthInputName = $arResult['WORKTIME_RECORD_FORM_NAME'] . '[recordedBreakLengthTime]';
$recordFormHelper = new \Bitrix\Timeman\Helper\Form\Worktime\RecordFormHelper();
$arResult['FIELD_CELLS']['START']['DATA_ROLE'] = 'start-time';
$arResult['FIELD_CELLS']['BREAK']['DATA_ROLE'] = 'break-time';
$arResult['FIELD_CELLS']['END']['DATA_ROLE'] = 'end-time';
$arResult['FIELD_CELLS']['DURATION']['DATA_ROLE'] = 'duration-time';

$APPLICATION->IncludeComponent(
	"bitrix:timeman.interface.popup.timepicker",
	".default",
	[
		'TIME_PICKER_CONTENT_ATTRIBUTE_DATA_ROLE' => 'timeman-time-picker-content',
		'START_INPUT_NAME' => 'startTime',
		'START_INPUT_ID' => 'startTimeClock',
		'END_INPUT_NAME' => 'endTime',
		'END_INPUT_ID' => 'endTimeClock',
		'START_DATE_INPUT_SELECTOR_ROLE' => 'start-date',
		'END_DATE_INPUT_SELECTOR_ROLE' => 'end-date',
		'END_INIT_TIME' => $arResult['FIELD_CELLS']['END']['TIME_PICKER_INIT_TIME'],
		'SHOW_EDIT_REASON' => false,
		'SHOW_EDIT_BREAK_LENGTH' => true,
		'SHOW_START_DATE_PICKER' => true,
		'SHOW_END_DATE_PICKER' => true,
		'START_DATE_DEFAULT_VALUE' => $arResult['FIELD_CELLS']['START']['TIME_PICKER_INIT_DATE'],
		'END_DATE_DEFAULT_VALUE' => $arResult['FIELD_CELLS']['END']['TIME_PICKER_INIT_DATE'],
		'EDIT_BREAK_LENGTH_ATTRIBUTE_NAME' => 'breakLength',
		'EDIT_REASON_ATTRIBUTE_NAME' => $arResult['WORKTIME_EVENT_FORM_NAME'] . '[reason]',
		'START_INIT_TIME' => $arResult['FIELD_CELLS']['START']['RECORDED_VALUE'],
		'BREAK_LENGTH_VALUE' => $arResult['FIELD_CELLS']['BREAK']['RECORDED_VALUE'],
	]
);
?>
<input type="hidden" name="scheduleId" value="<?= htmlspecialcharsbx($arResult['record']['SCHEDULE_ID']) ?>">
<input type="hidden" name="shiftId" value="<?= htmlspecialcharsbx($arResult['record']['SHIFT_ID']) ?>">
<div class="timeman-report-decs">
	<?
	//if($USER->GetId() == 3071):
	//echo '<pre>';
	//print_r($arResult["RECORD_LEGEND"]);
	//endif; ?>
	<form data-role="worktime-record-form">
		<input type="hidden" name="<?php echo $arResult['WORKTIME_RECORD_FORM_NAME']; ?>[useEmployeesTimezone]"
				value="<?= $arResult['useEmployeesTimezone'] ? '1' : '0' ?>">
		<input
			type="hidden"
			name="<?php echo $arResult['WORKTIME_RECORD_FORM_NAME']; ?>[id]"
			value="<?= htmlspecialcharsbx($arResult['record']['ID']) ?>"
		>
		<input
			type="hidden"
			name="<?php echo $arResult['WORKTIME_RECORD_FORM_NAME']; ?>[recordedStartDateFormatted]"
			data-role="start-date"
		>
		<input
			type="hidden"
			name="<?php echo $arResult['WORKTIME_RECORD_FORM_NAME']; ?>[recordedStopDateFormatted]"
			data-role="end-date"print_r
		>
		<input
			type="hidden"
			name="<?= htmlspecialcharsbx($startTimeInputName); ?>"
			value="<?= htmlspecialcharsbx($arResult['RECORDED_START_TIME'] ?? '') ?>"
		>
		<input
			type="hidden"
			name="<?= htmlspecialcharsbx($endTimeInputName); ?>"
			value="<?= htmlspecialcharsbx($arResult['RECORDED_STOP_TIME'] ?? '') ?>"
		>
		<input
			type="hidden"
			name="<?= htmlspecialcharsbx($breakLengthInputName); ?>"
			value="<?= htmlspecialcharsbx($arResult['BREAK_LENGTH_RECORDED_TIME'] ?? '') ?>"
		>
<? 
    //тут пишем перерыва время из кастома
	// if($USER->GetId() == 3071):
		// echo '<pre>';
		// print_r($arResult["FIELD_CELLS"]);
	// endif;
	if(is_array($arResult["RECORD_LEGEND"])):
	$arResult["FIELD_CELLS"]["BREAK"]["RECORDED_VALUE"] = $arResult["RECORD_LEGEND"][count($arResult["RECORD_LEGEND"])-1]['TIME_LEAKS_OVERALL'];
	$arResult["FIELD_CELLS"]["BREAK"]["ACTUAL_VALUE"] = $arResult["RECORD_LEGEND"][count($arResult["RECORD_LEGEND"])-1]['TIME_LEAKS_OVERALL'];
	endif;
	//echo '<pre>';
 //print_r($arResult["RECORD_LEGEND"]); ?>
		<div class="timeman-report-time-list">
			<? foreach ($arResult['FIELD_CELLS'] as $index => $fieldCell) :  ?>
				<? $cssClasses = 'timeman-report-time-item-data'; ?>
				<? $changedTime = false; ?>
				<? if (!$arResult['IS_RECORD_APPROVED'] && !empty($fieldCell['WARNINGS'])): ?>
					<? $cssClasses = 'timeman-report-time-item-unapproved'; ?>
				<? else: ?>
					<? if (!empty($fieldCell['VIOLATIONS'])): ?>
						<? foreach ($fieldCell['VIOLATIONS'] as $violation) : ?>
							<? /** @var \Bitrix\Timeman\Service\Worktime\Violation\WorktimeViolation $violation */
							if ($violation->isManuallyChangedTime()): ?>
								<? $cssClasses = 'timeman-report-time-item-changed'; ?>
							<? else: ?>
								<? $cssClasses = 'timeman-report-time-item-unapproved'; ?>
								<? break; ?>
							<? endif; ?>
						<? endforeach; ?>
					<? endif; ?>
				<? endif; ?>
				<div
					class="timeman-report-time-item <?= htmlspecialcharsbx($cssClasses); ?> <?= ($fieldCell['HIDE'] ?? null) && !($fieldCell['CHANGED_TIME'] ?? null) ? 'timeman-hide' : ''; ?>"
					data-role="<?= htmlspecialcharsbx($fieldCell['DATA_ROLE']) ?>-container"
				>
					<div class="timeman-report-time-item-title">
						<span class="timeman-report-time-item-title-text"><?= htmlspecialcharsbx($fieldCell['TITLE']) ?></span>
					</div>
					<div class="timeman-report-time-item-inner">
						<div class="timeman-report-time-item-data">
							<span class="timeman-report-time-item-value"
									data-role="<?= htmlspecialcharsbx($fieldCell['DATA_ROLE']) ?>" <? 

                                   if (!empty($fieldCell['RECORDED_VALUE'])):
							       //if (!empty($fieldCell['RECORDED_VALUE_HINT'])): ?>
								data-hint-html
								data-hint-no-icon
								data-hint="<?= htmlspecialcharsbx($fieldCell['RECORDED_VALUE_HINT']); ?>"
							<? endif; ?>
							><?=
								htmlspecialcharsbx($fieldCell['RECORDED_VALUE'])
								?></span>
							<? if (isset($fieldCell['DATE'])): ?>
								<span class="timeman-report-time-item-value-real"><?=
									'(' . htmlspecialcharsbx($fieldCell['DATE']) . ')';
									?></span>
							<? endif; ?>
							<div class="<?= ($fieldCell['CHANGED_TIME'] ?? null) ? '' : 'timeman-hide'; ?>">
								<span class="timeman-report-time-item-value-real" <?
								if (!empty($fieldCell['ACTUAL_VALUE_HINT'])): ?>
									data-hint-no-icon
									data-hint-html
									data-hint="<?= htmlspecialcharsbx($fieldCell['ACTUAL_VALUE_HINT']); ?>"
								<? endif; ?>
								><?=
									'(' . htmlspecialcharsbx(Loc::getMessage('JS_CORE_TMR_REPORT_ORIG') . ' ' . $fieldCell['ACTUAL_VALUE']) . ')';
									?></span>
								<div class="<?= empty($fieldCell['ACTUAL_INFO']) ? 'timeman-hide' : ''; ?>" <?
								if (!empty($fieldCell['ACTUAL_INFO_HINT'])): ?>
									data-hint-no-icon
									data-hint-html
									data-hint="<?= htmlspecialcharsbx($fieldCell['ACTUAL_INFO_HINT']); ?>"
								<? endif; ?>>
									<div class="timeman-report-time-item-edited">
										<span class="timeman-record-violation-icon <?php echo $recordFormHelper->getCssClassForViolations(
											$fieldCell['EDITED_VIOLATIONS'] ?? null, $fieldCell['OTHER_VIOLATIONS'] ?? null, $arResult['record']
										) ?>"></span>
										<span><?php echo
											htmlspecialcharsbx(Loc::getMessage('JS_CORE_TMR_A') . ' ' . ($fieldCell['ACTUAL_INFO']['EDITED_USER_TIME'] ?? ''))
											?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<br>
				</div>
			<? endforeach; ?>	</div>
										</div>
										<!-- внедрение тут-->			
			<? //if($USER->GetId() == 3071): ?>
<? $stages = ["Вход", "Выход", "Перерыв"]; ?>
		<?//print_r($cssClasses); ?>
					<?foreach($arResult["RECORD_LEGEND"] as $recordLegend): ?>
						<div class="timeman-report-time-list">
					<?	foreach($stages as $stage): ?> <?//print_r($recordLegend); ?>
						<? if($stage == 'Вход'):
							$timeVar = $recordLegend['TIME_START'];
							$cssClasses = 'timeman-report-time-item-data';
							elseif($stage == 'Перерыв' && !empty($recordLegend['TIME_LEAKS'])):
								$timeVar = $recordLegend['TIME_LEAKS'];	
								$cssClasses = 'timeman-report-time-item-leaks';
								elseif($stage == 'Перерыв' && empty($recordLegend['TIME_LEAKS'])):
									$timeVar = $recordLegend['TIME_LEAKS'];	
									$cssClasses = 'timeman-report-time-item-blank';
									elseif($stage == 'Выход' && !empty($recordLegend['TIME_FINISH'])):
										$timeVar = $recordLegend['TIME_FINISH'];	
										$cssClasses = 'timeman-report-time-item-changed';
										elseif($stage == 'Выход' && empty($recordLegend['TIME_FINISH'])):
											$timeVar = $recordLegend['TIME_FINISH'];	
											$cssClasses = 'timeman-report-time-item-blank';
								
							
						
						endif;?>
							<div
					class="timeman-report-time-item <?= htmlspecialcharsbx($cssClasses); ?> <?= ($fieldCell['HIDE'] ?? null) && !($fieldCell['CHANGED_TIME'] ?? null) ? 'timeman-hide' : ''; ?>"
					data-role="<?= htmlspecialcharsbx($stage) ?>-container"
				>
						<div class="timeman-report-time-item-title">
						<span class="timeman-report-time-item-title-text"><? if($cssClasses != 'timeman-report-time-item-blank'): echo htmlspecialcharsbx($stage); endif; ?></span>
					</div>
					<div class="timeman-report-time-item-inner">
						<div class="timeman-report-time-item-data">
							<span class="timeman-report-time-item-value"
									data-role="<?= htmlspecialcharsbx($stage) ?>" <?
							if (!empty($fieldCell['RECORDED_VALUE_HINT'])): ?>
								data-hint-html
								data-hint-no-icon
								data-hint="<?= htmlspecialcharsbx($fieldCell['RECORDED_VALUE_HINT']); ?>"
							<? endif; ?>
							><?=
								htmlspecialcharsbx($timeVar)
								?></span>
							<? if (isset($fieldCell['DATE'])): ?>
								<span class="timeman-report-time-item-value-real"><?=
									'(' . htmlspecialcharsbx($fieldCell['DATE']) . ')';
									?></span>
							<? endif; ?>
							<div class="<?= ($fieldCell['CHANGED_TIME'] ?? null) ? '' : 'timeman-hide'; ?>">
								<span class="timeman-report-time-item-value-real" <?
								if (!empty($fieldCell['ACTUAL_VALUE_HINT'])): ?>
									data-hint-no-icon
									data-hint-html
									data-hint="<?= htmlspecialcharsbx($fieldCell['ACTUAL_VALUE_HINT']); ?>"
								<? endif; ?>
								><?=
									'(' . htmlspecialcharsbx(Loc::getMessage('JS_CORE_TMR_REPORT_ORIG') . ' ' . $fieldCell['ACTUAL_VALUE']) . ')';
									?></span>
								<div class="<?= empty($fieldCell['ACTUAL_INFO']) ? 'timeman-hide' : ''; ?>" <?
								if (!empty($fieldCell['ACTUAL_INFO_HINT'])): ?>
									data-hint-no-icon
									data-hint-html
									data-hint="<?= htmlspecialcharsbx($fieldCell['ACTUAL_INFO_HINT']); ?>"
								<? endif; ?>>
									<div class="timeman-report-time-item-edited">
										<span class="timeman-record-violation-icon <?php echo $recordFormHelper->getCssClassForViolations(
											$fieldCell['EDITED_VIOLATIONS'] ?? null, $fieldCell['OTHER_VIOLATIONS'] ?? null, $arResult['record']
										) ?>"></span>
										<span><?php echo
											htmlspecialcharsbx(Loc::getMessage('JS_CORE_TMR_A') . ' ' . ($fieldCell['ACTUAL_INFO']['EDITED_USER_TIME'] ?? ''))
											?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
					</div><?endforeach; ?>
					</div>	<br>
					<?endforeach; ?>
										</div><br>	
					<?//endif; ?>
					<!-- до тут -->
	
	</form>
</div>
<? foreach ($arResult['FIELD_CELLS'] as $item) : ?>
	<? if ($item['ACTUAL_INFO']): ?>
		<div class="timeman-report-decs">
			<div class="timeman-report-title timeman-report-title-edited">
				<div class="timeman-report-title-text"><?=
					htmlspecialcharsbx($item['ACTUAL_INFO']['TITLE'])
					?></div>
			</div>
			<div class="timeman-report-decs-inner">
				<?= htmlspecialcharsbx($item['ACTUAL_INFO']['EDITED_REASON']); ?>
			</div>
		</div>
	<? endif; ?>
<? endforeach; ?>

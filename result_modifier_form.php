<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Main;

function currentRecord($userId, $tmstmp = false, $date) {

	if($tmstmp == false):
	$query = "SELECT `ID`, `TIME_START`, `TIME_FINISH`, `TIME_LEAKS`, `DURATION` FROM rasu_timeman_record WHERE `U_ID` = $userId AND DATE(`DATE`) = '".$date."'";
    else:
	$query = "SELECT `ID` FROM rasu_timeman_record WHERE `U_ID` = $userId AND `TIME_START` = $tmstmp AND DATE(`DATE`) = '".$date."'";
	endif;
	try
	{
		$connection = Main\Application::getInstance()->getConnection();
		$queryResult = $connection->query($query);

	//тут записываем значения которые есть
		foreach ($queryResult as $data)
		{ 
			$data['ENTRANCE'] = 'Вход';
			$data['EXIT'] = 'Выход';
			$data['TIMEOUT'] = 'Перерыв';
			$data['LEAK'] = 'Длительность';
			if(isset($data['TIME_START']))
			$data['TIME_START'] =  gmdate("H:i", $data['TIME_START']);
			if(isset($data['TIME_FINISH'])):
			$data['TIME_FINISH'] =  gmdate("H:i",$data['TIME_FINISH']);
			$data['DURATION'] =  strtotime($data['TIME_FINISH']) - strtotime($data['TIME_START']);
			$data['DURATION_ORIGINAL'] = gmdate("H:i", $data['DURATION']);
			endif;
		    if(isset($data['TIME_LEAKS']))
			$data['TIME_LEAKS'] =  gmdate("H:i",$data['TIME_LEAKS']);
			//if(isset($data['DURATION']))
			
			$records[] = $data;
			

		  $data = null;
		}

		//тут считаем перерыв
		//тут считаем перерыв
		if($records):
			reset($records);
			for($i = 0; $i < count($records) - 1; ++$i) {
				$current = current($records);
				$next = next($records);
				
				
				/*
				if($current['TIME_START'] == $next['TIME_START']):
					if(empty($current['TIME_FINISH'])):
					$delEl[] = $i;
						elseif(empty($next['TIME_FINISH'])):
							$delEl[] = $i+1;TIME_DURATION_OVERALL
					//unset($records[$i]);
					endif;
					endif;
*/
					if($current['TIME_START'] == $current['TIME_FINISH']):
						$delEl[] = $i;
						endif;

					if (isset($current['TIME_FINISH']) && !empty($next['TIME_START'])) {
						$records[$i]['TIME_LEAKS'] = gmdate("H:i",(strtotime($next['TIME_START']) - strtotime($current['TIME_FINISH']))); 
						$leaks['TIME_LEAKS_OVERALL'] += strtotime($next['TIME_START']) - strtotime($current['TIME_FINISH']);
					}
	
					if (isset($current['TIME_FINISH'])):
				//$records[$i]['DURATION'] = strtotime($current['TIME_FINISH']) - strtotime($current['TIME_START']); 
				//$leaks['TIME_DURATION_OVERALL'] += $records[$i]['DURATION'] ;
					 if(isset($records[count($records)-1]["TIME_FINISH"])):
						$records[$i]['DURATION'] = strtotime($records[count($records)-1]['TIME_FINISH']) - strtotime($records[0]['TIME_START']) - $leaks['TIME_LEAKS_OVERALL'] ; 
	$leaks['TIME_DURATION_OVERALL'] = $records[$i]['DURATION'] - 10800 ;
					   endif; 
	                else: 
						$leaks['TIME_DURATION_OVERALL']  = strtotime(gmdate("H:i")) - strtotime($records[0]['TIME_START']) - $leaks['TIME_LEAKS_OVERALL']; 

			//	$leaks['TIME_DURATION_OVERALL'] += $records[$i]['DURATION'];
					endif;
	
	
		  
				//	if (!isset($next['TIME_FINISH'])):
				//	$leaks['TIME_DURATION_OVERALL'] = strtotime(gmdate("H:i")) - strtotime($records[0]['TIME_START']) - $leaks['TIME_LEAKS_OVERALL'];
				//	endif;
					if (!isset($current['TIME_FINISH'])):
					//$leaks['TIME_DURATION_OVERALL'] = strtotime(gmdate("H:i")) - strtotime($records[0]['TIME_START']) - $leaks['TIME_LEAKS_OVERALL'];
						endif;
					
					
			}
	  

	//$last_el = max($records)['TIME_LEAKS_OVERALL'];
		
	
	foreach($delEl as $val):
		unset($records[$val]);
		endforeach;

	endif;
 
	if(is_array($records)):
		$records = array_values($records);


 
	$records[count($records)-1]['TIME_LEAKS_OVERALL']  = gmdate("H:i", $leaks['TIME_LEAKS_OVERALL']);
	$records[count($records)-1]['TIME_DURATION_OVERALL']  = date("Hч iм", $leaks['TIME_DURATION_OVERALL']);
if(gmdate("Hч iм", $leaks['TIME_LEAKS_OVERALL'])== gmdate("Hч iм")):
$records[count($records)-1]['TIME_LEAKS_OVERALL'] = "00:00";
endif;

	
		if(gmdate("Hч iм", $leaks['TIME_DURATION_OVERALL'])== gmdate("Hч iм")):
			if(isset($records[count($records)-1]['TIME_FINISH'])):
			$leaks['TIME_DURATION_OVERALL'] = strtotime($records[count($records)-1]['TIME_FINISH']) - strtotime($records[0]['TIME_START']);
			$records[count($records)-1]['TIME_DURATION_OVERALL'] = gmdate("Hч iм", $leaks['TIME_DURATION_OVERALL']);
			else:
			$leaks['TIME_DURATION_OVERALL'] = strtotime(date("H:i:s")) - strtotime($records[0]['TIME_START']);
			$records[count($records)-1]['TIME_DURATION_OVERALL'] = gmdate("Hч iм", $leaks['TIME_DURATION_OVERALL']  - $leaks['TIME_LEAKS_OVERALL']);
			endif;
		endif;

/*
	if($records[0]['TIME_START'] == $records[1]['TIME_START']):
		unset($records[0]);
		endif;
*/


	endif;





	}
	catch( Main\DB\SqlException $e )
	{
		var_dump($e->getMessage());
	}
	
   if(is_array($records)):
	//$recordLast = max($records);
	return $records;
   else:
	return false;
   endif;

}


//echo ($arResult["FIELD_CELLS"]["START"]["TIME_PICKER_INIT_DATE"]);
//if($USER->GetId() == 3071):
$sqlDate_ = date('Y-m-d', strtotime($arResult["FIELD_CELLS"]["START"]["TIME_PICKER_INIT_DATE"]));
//echo '<pre>';
//echo $sqlDate_;
$arResult["RECORD_LEGEND"] = currentRecord($arResult['user']['ID'], false, $sqlDate_);
//endif;
?>

<? 

save_excel_file($arResult);
 function save_excel_file($arResult) {
global $USER;

  function convert_time($input) {

    list($hours, $minutes) = explode(':', $input); 
    $result = explode('.', $hours . '.' .(( $minutes / 60 ) * 1000) / 100);
    $result[0] = intval($result[0]); 
    if($result[0])
    return $v['columns'][$rdata] = $result[0].'.'.$result[1];

  }
   //global $arResult;
    require_once $_SERVER['DOCUMENT_ROOT'].'/deps_from_1c/PHP_XLSXWriter-master/xlsxwriter.class.php';
   
 //  echo '<pre>';
  //  print_r($arResult['RECORD_GRID'][3071]['2024-06-03']['TIME_START']."<p>");
 //  print_r($arResult['ROWS']['columns']);
   // логика
$header = array();
      foreach($arResult['HEADERS'] as $k):
        if($k['id'] == 'USER_NAME'):
        $k['id'] = 'Сотрудник';
        $header[$k['id']] = 'string';
        else:
       $header[$k['id']] = '';
        endif;
      endforeach;


$matrix = array_keys($header);


     foreach($arResult['ROWS'] as $k => $v):  
//print_r($v['columns']['2024-06-03']);
if(is_array($v['columns'])):
foreach($v['columns'] as $rdata => $detail):

  /*
if($rdata !== 'USER_NAME'):
     $detail = explode('duration-value">', $detail);
     $detail = explode('</span>', $detail[1]);
     $detail[0] = trim($detail[0]);
$v['columns'][$rdata]  = $detail[0];
*/

  if($rdata !== 'USER_NAME'):
    $detail = explode('duration-value">', $detail);
    $detail = explode('</span>', $detail[1]);
    $detail[0] = trim($detail[0]);
    $detail[0] = str_replace('ч ',':', $detail[0]);
    $detail[0] = str_replace('м', '', $detail[0]);


    $v['columns'][$rdata] = convert_time($detail[0]);;



endif;

endforeach;


$matrix = array_merge($header, $v['columns']);
unset($matrix['Сотрудник']);
array_pop($matrix);


endif;
//сопоставление id - имя

$nameText = explode("href", $v['columns']['USER_NAME']);
if(!preg_match("/([0-9]{4})/", $nameText[1], $uid))
preg_match("/([0-9]{3})/", $nameText[1], $uid);

$name = (explode(">",$v['columns']['USER_NAME']));
$nameE = (explode("</a",$name[2]));
if(str_contains($nameE[0], 'timeman')):
$nameE[0] = str_replace('</a', ' ', trim($name[14]));
$nameE[0] = trim($nameE[0]);
$nameE[0] =  html_entity_decode(html_entity_decode($nameE[0]));
endif;
$nameE[0] = str_replace('&quot;', '"', $nameE[0]);

$names[$uid[0]] = $nameE[0];

$styles = array( 'font'=>'Times New Roman','font-size'=>11, 'halign'=>'center', 'border'=>'left,right,top,bottom', 'font-style'=>'bold');
$styles1 = array( 'font'=>'Times New Roman','font-size'=>10, 'halign'=>'center', 'border'=>'left,right,top,bottom');
  // Запись данных
  $data[] = array($nameE[0]) + $matrix;

      endforeach;


   
    
    $writer = new XLSXWriter();

//Итерация - 1 общий свод
    $writer->writeSheetHeader('Общий свод', $header, $col_options = ['widths'=>[100]], $styles1 ); 
foreach($data as $row)
if(preg_grep('/Дирекция/', $row) || preg_grep('/Департамент/', $row) || preg_grep('/Отдел/',$row)
|| preg_grep('/Генеральный/', $row) || preg_grep('/Блок/', $row)|| preg_grep('/Проект/', $row)
|| preg_grep('/Управлен/', $row) || preg_grep('/Бухгалтерия/', $row) || preg_grep('/Секретариат/', $row)
|| preg_grep('/Казначейство/', $row) || preg_grep('/Планово/', $row) || preg_grep('/Договорн/', $row)):
  $writer->writeSheetRow('Общий свод', $row, $styles );
else:
	$writer->writeSheetRow('Общий свод', $row, $styles1 );
endif;
  
//Итерация - 2 деталька

unset($header);
unset($data);



$header = array(' ' => 'string', ' ' => 'string', ' ' => 'string', ' ' => 'string', ' ' => 'string');
$writer->writeSheetHeader('Подробно', $header, $col_options = ['widths'=>[35, 25, 25, 25 , 25]], $styles1 ); 
$writer->markMergedCell('Подробно', $start_row=0, $start_col=0, $end_row=$n, $end_col=0);

//Имя и даты
$arr = $arResult['RECORD_GRID'];
 //echo '<pre>';
//print_r($arr);
$n = 1;
foreach($arr as $key => $val):
  $writer->markMergedCell('Подробно', $start_row=$n, $start_col=0, $end_row=$n, $end_col=4);
$res[$key] = array_keys($arr[$key]);
$writer->writeSheetRow('Подробно', array($names[$key]), $styles);
$writer->writeSheetRow('Подробно', array('Дата', 'Время входа', 'Время выхода', 'Длительность работы', 'Перерыв'), $styles1);
    $final[] = array($key);
foreach($res[$key] as $k => $v):
  $n++;
        if($v !== '1970-01-01'):
        $arr[$key][$v]['TIME_DURATION_OVERALL'] = str_replace('ч ',':', $arr[$key][$v]['TIME_DURATION_OVERALL']);
        $arr[$key][$v]['TIME_DURATION_OVERALL'] = str_replace('м', '', $arr[$key][$v]['TIME_DURATION_OVERALL']);
       $writer->writeSheetRow('Подробно', array($v, $arr[$key][$v]['TIME_START'], $arr[$key][$v]['TIME_FINISH'], convert_time($arr[$key][$v]['TIME_DURATION_OVERALL']), convert_time($arr[$key][$v]['TIME_LEAKS_OVERALL'])), $styles1);
        endif;  
      endforeach;
        
        $n++;
        $writer->writeSheetRow('Подробно', array(), $styles);  
        $n++;
    endforeach;

    $writer->writeToFile('/home/bitrix/www/upload/timeman/timeman_export_'.$USER->GetId().'.xlsx');
    
    header('X-Accel-Redirect:/upload/timeman/timeman_export_'.$USER->GetId().'.xlsx');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="timeman_export.xlsx"');
   // unlink('/home/bitrix/www/upload/timeman/timeman_export_'.$USER->GetId().'.xlsx');
    //die();


 //$writer->writeToString();
}


?>

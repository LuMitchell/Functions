<?php
require_once "app.php";

$country = 0;
$export = 1;
$month = '2018-09';
$month_start = strtotime('2018-09-01 00:00:00');
$month_end = strtotime('2018-10-01 00:00:00');

$sql = "select t.id, t.place_id, p.title, p.option_title, t.upload_time, t.expire_time, t.admin_id, t.total, t.origin_price, t.origin_country, t.notes, t.batch_code from ticket_batch as t left join places as p on t.place_id = p.id where t.upload_time >= '$month_start' and t.upload_time <= '$month_end'";
$result = $q->query($sql);
foreach ($result as $k => $res)
{
    $upload_time = date('Y-m-d H:i:s', $res['upload_time']);
    $expire_time = date('Y-m-d H:i:s', $res['expire_time']);

    $data[$k] = array($res['id'], $res['place_id'], $res['title'], $res['option_title'], $upload_time, $expire_time, $res['admin_id'], $res['total'], $res['origin_price'], $res['origin_country'], $res['notes'], $res['batch_code']);
}


function exportToExcel($filename, $titleArray=array(), $dataArray=array())
{
    ini_set('memory_limit','512M');
    ini_set('max_execution_time',0);
    ob_end_clean();
    ob_start();
    header("Content-Type: text/csv");
    header("Content-Disposition:filename=".$filename);
    $fp=fopen('php://output','w');
    fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));//转码 防止乱码
    fputcsv($fp,$titleArray);
    $index = 0;
    foreach ($dataArray as $item) {
        if($index==1000){
            $index=0;
            ob_flush();
            flush();
        }
        $index++;
        fputcsv($fp,$item);
    }

    ob_flush();
    flush();
    ob_end_clean();
}


if($export == 1)
{
    $filename = 'tickets - '.$month.'.xls';
    $title = array('id', 'place_id', 'title', 'option_title', 'upload_time', 'expire_time', 'admin_id', 'total', 'origin_price', 'origin_country', 'notes', 'batch_code');
    exportToExcel($filename, $title, $data);
}
//

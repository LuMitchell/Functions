<?php

//数组分组
public function fetchAllSeedByUserId($user_id)
{
    /*
    ...
    */
    $query = $this->db->get();
    $result = $query->result();

    $seed = array();
    foreach ($result as $r) 
    {
        $seed[$r->lottery_id][] = $r;
    }
    return $seed;
}

//进制转换
function from62to10($str){
    $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $len = strlen($str);
    $dec = 0;
    for($i = 0;$i<$len;$i++)
    {
        //找到对应字典的下标
        $pos = strpos($dict, $str[$i]);
        $dec += $pos * pow(62,$len-$i-1);
    }
    return $dec;
}

function from10to62($dec) {
    $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $result = '';
    do
    {
        $result = $dict[$dec % 62] . $result;
        $dec = intval($dec / 62);
    } while ($dec != 0);
    
    return $result;
}

//正则匹配中文
function preg()
{
    $str = '云南省昆明市富民县赤鹫镇东核村委会';
    preg_match('#^(.*?)[县|区]{3}#is', $str, $matches);
    var_dump($matches);
}

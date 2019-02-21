<?php

//数组分组排序
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

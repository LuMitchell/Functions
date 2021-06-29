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

//Laravel ORM withCount & withSum
public function all(Request $request)
{
    $name = $request->input('name');
    $phone = $request->input('phone');
    $process = $request->input('process');
    $pageSize = $request->input('pageSize') ?? 20;
    $month = $request->input('month') ?? $this->GetMonth(1);

    if($month <= '2021-05') return $this->success_back('success', ['users'=>[], 'month'=>'']);

    $start_time = $month.'-01 00:00:00';
    $end_time = date('Y-m-d', strtotime("$start_time +1 month -1 day")).' 23:59:59';

    $query = User::query()->select('id', 'name', 'email', 'phone', 'gat_account', 'real_name', 'internal_user', 'is_block', 'is_ban')->whereNotNull('phone');

    if($name) $query->where('name', 'like', "%{$name}%");
    if($phone) $query->where('phone', 'like', "%{$phone}%");
    if($process) $query->where('process_bonus', '<>', 0);

    $users = $query->withCount([
        'valid_resumes as resume_count' => function ($q) use ($start_time, $end_time) {
            $q->where('created_at', '>=', $start_time)->where('created_at', '<=', $end_time);
        },
        'valid_resumes as on_boarding_count' => function ($q) use ($start_time, $end_time) {
            $q->where('process_node_id', ProcessNode::ID_ONBOARDING)->where('created_at', '>=', $start_time)->where('created_at', '<=', $end_time);
        },
    ])->withSum([
        'balance_record as total_bonus' => function ($q) use ($start_time, $end_time) {
            $q->where('type_id', 1)->where('created_at', '>=', $start_time)->where('created_at', '<=', $end_time);
        },
        'balance_record as withdraw_bonus' => function ($q) use ($start_time, $end_time) {
            $q->where('type_id', 2)->whereNotNull('completed_at')->where('created_at', '>=', $start_time)->where('created_at', '<=', $end_time);
        },
        'balance_record as process_bonus' => function ($q) use ($start_time, $end_time) {
            $q->where('type_id', 2)->whereNull('completed_at')->where('created_at', '>=', $start_time)->where('created_at', '<=', $end_time);
        },
    ], 'add')->withSum([
        'balance_record as balance_bonus' => function ($q) use ($start_time, $end_time) {
            $q->where('created_at', '>=', $start_time)->where('created_at', '<=', $end_time)->where('last_flag', 1);
        },
    ], 'after_add')->orderBy('process_bonus_time')->paginate($pageSize);

    return $this->success_back('success', ['users'=>$users, 'month'=>$month]);
}

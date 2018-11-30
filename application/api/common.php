<?php
function show($status, $message = '', $data = []){
	return [
	      'status' => intval($status),
          'message' => $message,
          'data' => $data,
	];

}

function toJson($code,$msg="",$count,$data=array()){
    $result=array(
        'code'=>$code,
        'msg'=>$msg,
        'count'=>$count,
        'data'=>$data
    );
    //输出json
    echo json_encode($result);
    exit;
}
function error($code,$msg="",$data=array()){
    $result=array(
        'code'=>$code,
        'msg'=>$msg,
        'data'=>$data
    );
    //输出json
    echo json_encode($result);
    exit;
}
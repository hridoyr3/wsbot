<?php
define('BOT_TOKEN', 'PASTE_YOUR_BOT_TOKEN_HERE');
define('BOT_USERNAME', 'Wsmarketing5412_bot');
define('ADMIN_ID', 7553242935);
define('CHANNEL_USERNAME', '@wswebsite1');
define('CHANNEL_LINK', 'https://t.me/wswebsite1');
function tg($m,$d=[]){ $u='https://api.telegram.org/bot'.BOT_TOKEN.'/'.$m; $ch=curl_init($u); curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$d]); $r=curl_exec($ch); curl_close($ch); return json_decode($r,true); }
function load($f){ if(!file_exists($f)) return []; $j=json_decode(file_get_contents($f),true); return is_array($j)?$j:[]; }
function save($f,$d){ @mkdir(dirname($f),0777,true); file_put_contents($f,json_encode($d,JSON_PRETTY_PRINT)); }
function getSet(){ $s=load("data/settings.json"); if(empty($s)){ $s=['default_price'=>50,'withdraw_min'=>100,'withdraw_charge'=>10,'refer_bonus'=>30,'refer_min_sell'=>3,'country_price'=>['+880'=>50,'+91'=>40,'+1'=>80]]; save("data/settings.json",$s); } return $s; }
function getU($cid){ $f="data/users/$cid.json"; $u=load($f); if(empty($u)){ $u=['id'=>$cid,'balance'=>0,'total_sell'=>0,'total_reject'=>0,'total_withdraw'=>0,'total_earn'=>0,'refer_earn'=>0,'refer_count'=>0]; save($f,$u); } return $u; }
function updU($cid,$u){ save("data/users/$cid.json",$u); }
function getPrice($num,$set){ foreach($set['country_price'] as $c=>$p){ if(strpos($num,$c)===0) return (int)$p; } return (int)$set['default_price']; }
function isJoined($uid){ $r=tg('getChatMember',['chat_id'=>CHANNEL_USERNAME,'user_id'=>$uid]); $s=$r['result']['status']??'left'; return in_array($s,['member','administrator','creator']); }
$update=json_decode(file_get_contents('php://input')?:'{}',true);
$msg=$update['message']??null; $cb=$update['callback_query']??null;
if($cb){ $cid=$cb['message']['chat']['id']; $fid=$cb['from']['id']; $data=$cb['data']; $mid=$cb['message']['message_id']; if($data=='check_join'){ if(isJoined($fid)){ tg('editMessageText',['chat_id'=>$cid,'message_id'=>$mid,'text'=>"Joined!"]); save("data/state/$fid.json",['step'=>'await_number']); tg('sendMessage',['chat_id'=>$cid,'text'=>"Now send number"]); } else tg('answerCallbackQuery',['callback_query_id'=>$cb['id'],'text'=>"Not joined!",'show_alert'=>true]); exit; } tg('answerCallbackQuery',['callback_query_id'=>$cb['id']]); exit; }
if($msg && isset($msg['chat']['id'])){
 $cid=$msg['chat']['id']; $text=trim($msg['text']??'');
 if(preg_match('~^/start~i',$text)){ $kb=[[ ['text'=>'My Account'],['text'=>'Withdrawal']],[['text'=>'Sell Account'],['text'=>'History']],[['text'=>'Refer & Earn'],['text'=>'Support']] ]; if($cid==ADMIN_ID) $kb[]=[['text'=>'Admin Panel']]; tg('sendMessage',['chat_id'=>$cid,'text'=>"Welcome",'reply_markup'=>json_encode(['keyboard'=>$kb,'resize_keyboard'=>true])]); exit; }
 if($text=='Sell Account'){ if(!isJoined($cid) && $cid!=ADMIN_ID){ $kb=['inline_keyboard'=>[[['text'=>'Join','url'=>CHANNEL_LINK]],[['text'=>'Joined','callback_data'=>'check_join']]]]; tg('sendMessage',['chat_id'=>$cid,'text'=>"Join channel",'reply_markup'=>json_encode($kb)]); exit; } tg('sendMessage',['chat_id'=>$cid,'text'=>"Send number"]); save("data/state/$cid.json",['step'=>'await_number']); exit; }
}

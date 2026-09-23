<?php
define('BOT_TOKEN', '8760295700:AAF2YqHAkJlh8lq-A9MHvlzmebrmNa4hTlE');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
define('OWNER_ID', 8403357959);

function bot($method, $data=[]){
    $ch=curl_init(API_URL.$method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    return json_decode(curl_exec($ch), true);
}

$content=file_get_contents("php://input");
$update=json_decode($content, true);
if(!$update){ echo "Bot is running"; exit; }

$message=$update['message'] ?? $update['edited_message'] ?? null;
$chat_id=$message['chat']['id'] ?? null;
$chat_type=$message['chat']['type'] ?? '';
$text=$message['text'] ?? '';
$from_id=$message['from']['id'] ?? 0;

if($chat_type=='group' || $chat_type=='supergroup'){
    bot('sendMessage', ['chat_id'=>$chat_id,'text'=>"🚫 Group Blocked!\nYe bot sirf private me kaam karta hai."]);
    bot('leaveChat', ['chat_id'=>$chat_id]);
    exit;
}

if(!$chat_id) exit;

if($text=='/start'){
    $msg="👋 Welcome to WS Marketing Bot!\n\n✅ Auto Features:\n✅ Welcome Message\n✅ Fast Reply\n✅ 24/7 Online\n\nCommands:\n/start - Start bot\n/help - Help menu";
    bot('sendMessage', ['chat_id'=>$chat_id,'text'=>$msg]);
}
elseif($text=='/help'){
    bot('sendMessage', ['chat_id'=>$chat_id,'text'=>"📚 Help Menu\nJust add me to your channel as Admin.\nSupport: @Hridoyr3"]);
}
else{
    if($from_id!=OWNER_ID){
        bot('sendMessage', ['chat_id'=>$chat_id,'text'=>"✅ Message received!\nAdmin will reply soon.\n⚡️ Powered by @Wsmarketing5412_bot"]);
        bot('forwardMessage', ['chat_id'=>OWNER_ID,'from_chat_id'=>$chat_id,'message_id'=>$message['message_id']]);
    }
}
?>

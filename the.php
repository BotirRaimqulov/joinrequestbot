<?php
require_once("config.php");
require_once("bot.php");
$db = new mysqli("localhost", "bots_joinbot", "aOFd4RTfpfm13VS6", "bots_joinbot"); // connect mysql
$bot = new bot();
$update = $bot->update();
$msg = $update->message;
$txt = $msg->text;
$cid = $msg->chat->id;
$username = $msg->chat->username;
$mid = $msg->message_id;
$data = $update->callback_query->data;
$mid1 = $update->callback_query->message->message_id;
$msg1 = $update->callback_query->message;
$cid1 = $update->callback_query->message->chat->id;
$chname = $update->chat_join_request->chat->title;
$firstname = $msg->chat->first_name;
$lastname = $msg->chat->last_name;
$firstname1 = $msg1->chat->first_name;
$lastname1 = $msg1->chat->last_name;
$chuid = $update->chat_join_request->from->id;
$chid = $update->chat_join_request->chat->id;
$join = $update->chat_join_request;
mkdir('channels');

$channels = file_get_contents("channels/channels.txt");

if ($join) {
    $sql = $db->query("SELECT * FROM `channels` WHERE channel_id=$chid");
    $row = $sql->fetch_assoc();
    if ($row) {
        $sqll = $db->query("SELECT * FROM `approve` WHERE `channel_id`=$chid AND `user_id`=$chuid");
        $roww = $sqll->fetch_assoc();
        if (!$roww) {
            $db->query("INSERT INTO `approve`(`user_id`, `channel_id`) VALUES ('$chuid','$chid')");
        }
        $count = $row['count'] + 1;
        $db->query("UPDATE `channels` SET `count`=$count WHERE `channel_id`=$chid");
        $bot->request('approveChatJoinRequest', [
            'chat_id' => $chid,
            'user_id' => $chuid,
        ]);
        $link = $bot->request('getChat', [
            'chat_id' => $chid
        ])->result->invite_link;
        $bot->request('sendmessage', [
            'chat_id' => $chuid,
            'text' => "<b>Siz </b><a href='$link'>$chname</a><b> kanaliga obuna bo'ldingiz</b>",
            'disable_web_page_preview' => "true",
            'parse_mode' => "html",
        ]);
    }
}

if ($txt == "/start") {
    $sql = $db->query("SELECT * FROM `users` WHERE user_id=$cid");
    $row = $sql->fetch_assoc();
    if (!$row) {
        $db->query("INSERT INTO `users`(`user_id`) VALUES ('$cid')");
    }
    $bot->request('sendmessage', [
        'chat_id' => $cid,
        'text' => "<b>👋Hi!</b>\n<b>I'm a channel actions bot, mainly focused on working with the new admin approval invite links💯</b>\n\n<i>I can:</i>\n\n<i>- Auto approve new join requests👌</i>\n\n<pre>Click the below button to know how to use me!</pre>",
        'disable_web_page_preview' => "true",
        'parse_mode' => "html",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "How to use me ❓", 'callback_data' => "qanday"]],
                [['text' => "Updates", 'url' => "https://t.me/t_me_bots"]],
            ]
        ])
    ]);
}
$back = json_encode([
    'inline_keyboard' => [
        [['text' => "Back", 'callback_data' => "back"]]
    ]
]);
if ($data == "back") {
    $bot->request('deleteMessage', [
        'chat_id' => $cid1,
        'message_id' => $mid1,
    ]);
    $bot->request('sendmessage', [
        'chat_id' => $cid1,
        'text' => "<b>👋Hi!</b>\n<b>I'm a channel actions bot, mainly focused on working with the new admin approval invite links💯</b>\n\n<i>I can:</i>\n\n<i>- Auto approve new join requests👌</i>\n\n<pre>Click the below button to know how to use me!</pre>",
        'disable_web_page_preview' => "true",
        'parse_mode' => "html",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "How to use me ❓", 'callback_data' => "qanday"]],
                [['text' => "Updates", 'url' => "https://t.me/t_me_bots"]],
            ]
        ])
    ]);
}
if ($data == "qanday") {
    $bot->request('deleteMessage', [
        'chat_id' => $cid1,
        'message_id' => $mid1,
    ]);
    $bot->request('sendPhoto', [
        'photo' => new CURLFILE("dobavit.jpg"),
        'chat_id' => $cid1,
        'caption' => "<b>Usage instructions.</b>\n\n<b>Add me to your channel, as administrator, with 《 add users 》 permission, and forward me a message from that chat to set me up!</b>\n\n<i>Bot Managed By @t_me_bots</i>",
        'disable_web_page_preview' => "true",
        'parse_mode' => "html",
        'reply_markup' => $back
    ]);
}
if (isset($msg->forward_from_chat)) {
    $fid = $msg->forward_from_chat->id;
    $by_admin = $bot->request('getChatMember', [
        'chat_id' => $fid,
        'user_id' => $bot_id
    ])->result;
    $status = $by_admin->status;
    $type = $msg->forward_from_chat->type;
    if ($type == "channel") {
        $sql = $db->query("SELECT * FROM `channels` WHERE channel_id=$fid");
        $row = $sql->fetch_assoc();
        if (!$row) {
            if ($status == "administrator") {
                $bot->request('sendMessage', [
                    'chat_id' => $cid,
                    'text' => "*Very nice!\n\nOur bot now automatically accepts join requests from your channel!*",
                    'parse_mode' => "markdown"
                ]);
                $db->query("INSERT INTO `channels`(`channel_id`, `admin_id`, `count`) VALUES ('$fid','$cid','0')");
            } else {
                $bot->request('sendMessage', [
                    'chat_id' => $cid,
                    'text' => "The bot is not a channel administrator! Try again."
                ]);
            }
        }
    }
}

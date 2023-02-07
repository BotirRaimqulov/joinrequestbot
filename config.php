<?php
define("API_KEY", "TOKEN"); // bot tokeni
$get=json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getMe"),true);
$bot_user=$get['result']['username'];
$bot_id=$get['result']['id'];
echo $bot_user;
?>

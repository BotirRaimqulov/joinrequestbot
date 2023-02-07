<?php
require_once("config.php");
class bot
{
  public function request($method, $data = []) {
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
      var_dump(curl_error($ch));

    } else {
      return json_decode($res);
    }
  }
  public function update() {
    return json_decode(file_get_contents("php://input"));
  }
}

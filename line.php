<?php

$API_URL = 'https://api.line.me/v2/bot/message/reply';
$ACCESS_TOKEN = 'zfSRSw0HuumsRzK6EUbpbuauPkWmqlY/shvTWs40W16hfOf9qDSMr1et1rELso4OND+Ww4yQsSVNzxUUL38pWNFUnYfNO8u1ghUs1qECMJ8p75ClNzDod9jdixzZJYN47ZTMJBgtyZLfHp1295nWkQdB04t89/1O/w1cDnyilFU='; // Access Token ค่าที่เราสร้างขึ้น
$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

$request = file_get_contents('php://input');   // Get request content
$request_array = json_decode($request, true);   // Decode JSON to Array

if ( sizeof($request_array['events']) > 0 )
{

 foreach ($request_array['events'] as $event)
 {
  $reply_message = '';
  $reply_token = $event['replyToken'];

  if ( $event['type'] == 'message' ) 
  {
   
   if( $event['message']['type'] == 'text' )
   {
		$text = $event['message']['text'];
		
		if(($text == "อยากทราบยอด COVID-19 ครับ")||($text == "ยอด COVID-19")||($text == "อยากทราบยอด COVID-19 ครับ")){
			$cumulative = 955;
			$death = 4;
			$fine = 50;
			$reply_message = '"รายงานสถานการณ์ ยอดผู้ติดเชื้อไวรัสโคโรนา 2019 (COVID-19) ในประเทศไทย" \n';
			$reply_message .= 'ผู้ป่วยสะสม	จำนวน '.$cumulative.' ราย \n';
			$reply_message .= 'ผู้เสียชีวิต	จำนวน '.$death.' ราย \n';
			$reply_message .= 'รักษาหาย	จำนวน '.$fine.' ราย \n';
			$reply_message .= 'ผู้รายงานข้อมูล: 59160180 นายธนภร เกลี้ยกล่อม';
		}
		else if(($text== "ข้อมูลส่วนตัวของผู้พัฒนาระบบ")||($text== "ข้อมูลส่วนตัว")||($text== "ข้อมูลผู้พัฒนา")||($text== "ข้อมูลผู้พัฒนาระบบ")){
			$reply_message = 'ชื่อนายธนภร เกลี้ยกล่อม อายุ 22ปี น้ำหนัก 68kg. สูง 170cm. ขนาดรองเท้าเบอร์ 8 ใช้หน่วย US';
		}
		else
		{
			$reply_message = 'ระบบได้รับข้อความ ('.$text.') ของคุณแล้ว';
    		}
   
   }
   else
    $reply_message = 'ระบบได้รับ '.ucfirst($event['message']['type']).' ของคุณแล้ว';
  
  }
  else
   $reply_message = 'ระบบได้รับ Event '.ucfirst($event['type']).' ของคุณแล้ว';
 
  if( strlen($reply_message) > 0 )
  {
   //$reply_message = iconv("tis-620","utf-8",$reply_message);
   $data = [
    'replyToken' => $reply_token,
    'messages' => [['type' => 'text', 'text' => $reply_message]]
   ];
   $post_body = json_encode($data, JSON_UNESCAPED_UNICODE);

   $send_result = send_reply_message($API_URL, $POST_HEADER, $post_body);
   echo "Result: ".$send_result."\r\n";
  }
 }
}

echo "OK";

function send_reply_message($url, $post_header, $post_body)
{
 $ch = curl_init($url);
 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 $result = curl_exec($ch);
 curl_close($ch);

 return $result;
}

?>

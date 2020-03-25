<?php

$API_URL = 'https://api.line.me/v2/bot/message/reply';
$ACCESS_TOKEN = 'zfSRSw0HuumsRzK6EUbpbuauPkWmqlY/shvTWs40W16hfOf9qDSMr1et1rELso4OND+Ww4yQsSVNzxUUL38pWNFUnYfNO8u1ghUs1qECMJ8p75ClNzDod9jdixzZJYN47ZTMJBgtyZLfHp1295nWkQdB04t89/1O/w1cDnyilFU='; // Access Token ค่าที่เราสร้างขึ้น
$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

$COVID_APT_URL = 'https://opend.data.go.th/get-ckan/datastore_search?resource_id=93f74e67-6f76-4b25-8f5d-b485083100b6&limit=5';
$COVID_ACCESS_TOKEN = 'YBDoHoTGjOzeCRXKd7jNoZBLNH7CDOaY';

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

	if(($text == "อยากทราบยอด COVID-19 ครับ")||($text == "ยอด COVID-19")||($text == "อยากทราบยอด COVID-19 ครับ"||($text == "COVID-19")||($text == "โควิด"))){
		$cumulative = 955;
		$death = 4;
		$fine = 50;
		//$reply_message = getCovidData($COVID_APT_URL, $COVID_ACCESS_TOKEN);
 		$reply_message = '"รายงานสถานการณ์ ยอดผู้ติดเชื้อไวรัสโคโรนา 2019 (COVID-19) ในประเทศไทย"
ผู้ป่วยสะสม	จำนวน '.$cumulative.' ราย
ผู้เสียชีวิต	จำนวน '.$death.' ราย
รักษาหาย	จำนวน '.$fine.' ราย
ผู้รายงานข้อมูล: 59160180 นายธนภร เกลี้ยกล่อม';
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

function getCovidData($url, $token){

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
		"api-key: ".$token
		)
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
		echo $response;
	}
}
?>

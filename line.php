<?php

$API_URL = 'https://api.line.me/v2/bot/message/reply';
$ACCESS_TOKEN = '2o+T4Y0AV6kAJUeVVu6INETXt2jd3xeFVSVZPA4yuSTo7oJ1/b9lZbDzJFHidsLSLBaQpiu0YbB+HDyKkVPHIKmlXO1O1NtoJlMmxLIKZm7OU/sSi6g8NCeLYqL+6w3xiWKOrRneH6n2C7j7nZAqowdB04t89/1O/w1cDnyilFU='; // Access Token ค่าที่เราสร้างขึ้น
$POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

$COVID_APT_URL = 'https://covid-193.p.rapidapi.com/statistics?country=Thailand';
$COVID_APT_HOST = 'covid-193.p.rapidapi.com';
$COVID_ACCESS_TOKEN = '26b94cb73dmshabed9734718d564p1c0051jsnca21f366ada3';

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
	$text = strtolower($event['message']['text']);

	if(($text == "อยากทราบยอด covid-19 ครับ")||
   	(strpos($text, "รายงาน") !== FALSE)||
	(strpos($text, "ยอด covid-19") !== FALSE)||
	(strpos($text, "ยอด covid") !== FALSE)||
	(strpos($text, "ยอดcovid") !== FALSE)||
	(strpos($text, "covid") !== FALSE)||
   	(strpos($text, "โคโรน่า") !== FALSE)||
   	(strpos($text, "ไวรัส") !== FALSE)||
	(strpos($text, "ยอดโควิด") !== FALSE)){
		$data = getCovidData($COVID_APT_URL, $COVID_APT_HOST, $COVID_ACCESS_TOKEN);
		if(strpos($data, "cURL Error #") === TRUE){
		  echo $data;
		  return;
		}
		$covidData = json_decode($data)->response[0];
		$datetime = new DateTime($covidData->time);
$reply_message = '"รายงานสถานการณ์ ยอดผู้ติดเชื้อไวรัสโคโรนา 2019 (COVID-19) ในประเทศไทย"
🤧 ติดเชื้อเพิ่ม  '.number_format(str_replace('+', '', $covidData->cases->new)).' ราย
😷 ติดเชื้อสะสม '.number_format($covidData->cases->total).' ราย'.($covidData->deaths->new === NULL ? '' : '👻 เสียชีวิต '.number_format($covidData->deaths->new).' ราย').'
💀 ยอดรวมผู้เสียชีวิต '.number_format($covidData->deaths->total).' ราย
😀 รักษาหาย '.number_format($covidData->cases->recovered).' ราย
🤒 กำลังรักษา '.number_format($covidData->cases->active).' ราย
😱 อาการวิกฤต '.number_format($covidData->cases->critical).' ราย
ข้อมูล ณ วันที่ '.formatDate($datetime).'
เวลา '.$datetime->format('H:i').' น.';
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

function getCovidData($url, $host, $token){

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			"x-rapidapi-host: ".$host,
			"x-rapidapi-key: ".$token
		),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		return "cURL Error #:" . $err;
	} else {
		return $response;
	}
}

function formatDate($date){
  $monthTH = [null,'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
  $thai_date_return = $date->format("j");   
  $thai_date_return.=" ".$monthTH[$date->format("n")];   
  $thai_date_return.= " ".($date->format("Y")+543);   
  return $thai_date_return;  
}

?>

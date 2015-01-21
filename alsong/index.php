<?php
header("Content-Type:text/html; charset=UTF-8"); 
//require($_SERVER['DOCUMENT_ROOT'].'/HttpClient.class.php');
require('./HttpClient.class.php');

$title=$_GET["title"];
$artist=$_GET["artist"];
/* 파일 정보로 읽어오기 샘플.
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://www.w3.org/2003/05/soap-envelope" xmlns:SOAP-ENC="http://www.w3.org/2003/05/soap-encoding" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:ns2="ALSongWebServer/Service1Soap" xmlns:ns1="ALSongWebServer" xmlns:ns3="ALSongWebServer/Service1Soap12"><SOAP-ENV:Body><ns1:GetLyric5><ns1:stQuery><ns1:strChecksum>2339265d788a1207883da263d0f9fe96</ns1:strChecksum><ns1:strVersion>1.93</ns1:strVersion><ns1:strMACAddress>005056C00001</ns1:strMACAddress><ns1:strIPAddress>192.168.1.2</ns1:strIPAddress></ns1:stQuery></ns1:GetLyric5></SOAP-ENV:Body></SOAP-ENV:Envelope>
*/

$string = '<?xml version="1.0" encoding="UTF-8"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://www.w3.org/2003/05/soap-envelope" xmlns:SOAP-ENC="http://www.w3.org/2003/05/soap-encoding" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:ns2="ALSongWebServer/Service1Soap" xmlns:ns1="ALSongWebServer" xmlns:ns3="ALSongWebServer/Service1Soap12"> <SOAP-ENV:Body> <ns1:GetResembleLyric2> <ns1:stQuery> <ns1:strTitle>'.$title.'</ns1:strTitle> <ns1:strArtistName>'.$artist.'</ns1:strArtistName> <ns1:nCurPage>0</ns1:nCurPage> </ns1:stQuery> </ns1:GetResembleLyric2> </SOAP-ENV:Body></SOAP-ENV:Envelope>';

$client = new HttpClient('lyrics.alsong.co.kr');
$client->setDebug(false);
$client->setUserAgent('Content-Type: application/soap+xml; charset=utf-8');
$client->post('/alsongwebservice/service1.asmx', $string);
//HttpClient.class.php 에서 Content-Type':'application/soap+xml 을 강제 변환 해줘야 함.

echo processText($client->getContent(), 0);

function isDuplicatedZero($text)
{
	if(substr_count($text, '[00:00.00]')>15) return true;
	else return false;
}

function getSplitText($text, $startPos)
{
	$endDup=strpos($text, '<strInfoID>', $startPos+20); //if it has a duplicated entry //170->220
	$endSingle=strpos($text, '</ST_GET_RESEMBLELYRIC2_RETURN>', $startPos); //4400
	echo "$endDup ------------- $endSingle ---------------";

	if($endDup>$endSingle) $end=$endSingle;
	else $end=$endDup;

	$splitText=substr($text,$startPos,$end-$startPos); //substr from startPost to end-startPos(length)
	if(isDuplicatedZero($splitText)) return $end; //if 00 shows more than 15times, return the next entry startpoint.
	else return $splitText; //it's ok to return the text
}

function processText($text, $startPos)
{
	$nextStartPos=strpos($text, '<strInfoID>', $startPos);
	if($nextStartPos===false) return 'Cannot find proper caption';
	else
	{
		$textOrNum=getSplitText($text, $nextStartPos); //150

		if(is_numeric($textOrNum)) return processText($text, $textOrNum);
		else return $textOrNum;
	}
}
?>

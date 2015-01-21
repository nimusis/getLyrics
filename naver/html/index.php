<?php
//require($_SERVER['DOCUMENT_ROOT'].'/Snoopy-2.0.0/Snoopy.class.php');
require('../Snoopy-2.0.0/Snoopy.class.php');

$getSong = $_GET["song"];
$getArtist = $_GET["artist"];

$snoopy = new Snoopy;

if($getSong == null || $getArtist == null || $getSong == "" || $getArtist == "")
{
	exit(0);
}

$getSong = urlencode($getSong);
$getArtist = urlencode($getArtist);

$url_for_trackID='http://music.naver.com/search/search.nhn?target=track&query=';
$url_for_lyrics='http://music.naver.com/lyric/index.nhn?trackId=';

$snoopy->fetch($url_for_trackID.$getSong.'+'.$getArtist);
preg_match('/artistdata\="\[\{\'(.*?)\'/is', $snoopy->results, $buff_trackID);
$trackID = $buff_trackID[1];

if($trackID <= 0)
{
	exit(0);
}

$snoopy->fetch($url_for_lyrics.$trackID);

preg_match('/show_lyrics">(.*?)<\/div>/is', $snoopy->results, $buff_lyrics);
$lyrics = $buff_lyrics[1];

preg_match('/target="_blank"\ title="(.*?)"/is', $snoopy->results, $buff_artist);
$artist = $buff_artist[1];

preg_match('/<span class="album">(.*?)<\/span>/is', $snoopy->results, $buff_song);
$song = $buff_song[1];

echo $artist.','.$song.'</br></br></br>'.$lyrics;

?>

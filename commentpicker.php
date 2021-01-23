<?php
# by : tra
###################################################################

$validasi = false; //Validasi Mention, giveaway biasanya ada syarat mention 3 org blabla
$min = 3; //Minimal mention
$urlPost = "https://www.instagram.com/p/XXXXXXXXX/"; //url postnya

###################################################################

unlink("cookie_ig.txt");
cookies($urlPost);
function getStr($a, $b, $c){
	return @explode($b, @explode($a, $c)[1])[0];
}
function cookies($url){
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


	$headers = array();
	$headers[] = 'Host: www.instagram.com';
	$headers[] = 'Connection: close';
	$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.89 Safari/537.36';
	$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
	$headers[] = 'Accept-Language: en-US,en;q=0.9';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie_ig.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie_ig.txt");
	$result = curl_exec($ch);
	curl_close($ch);
}
function cek($post, $ec = ''){
	$code = getStr("instagram.com/p/","/", $post);
	if(empty($code)) $code = @explode("instagram.com/p/", $post)[1];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://www.instagram.com/graphql/query/?query_hash=bc3296d1ce80a24b1b6e40b1e72903f5&variables=%7B%22shortcode%22%3A%22'.$code.'%22%2C%22first%22%3A50%2C%22after%22%3A%22'.$ec.'%22%7D');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	$headers = array();
	$headers[] = 'Host: www.instagram.com';
	$headers[] = 'Connection: close';
	$headers[] = 'Accept: */*';
	$headers[] = 'X-Requested-With: XMLHttpRequest';
	$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.89 Safari/537.36';
	$headers[] = 'Cookie: shbid=2042';
	$headers[] = 'Accept-Language: en-US,en;q=0.9';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie_ig.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie_ig.txt");
	$result = curl_exec($ch);
	$res = @json_decode($result,true)['data']['shortcode_media']['edge_media_to_parent_comment'];
	$ress[0] = $res['edges'];
	$ress[1] = $res['page_info']['end_cursor'];
	$ress[2] = $res['count'];
	return $ress;
}
function random($comment){
	return $comment[rand(0, (@count($comment)-1))];
}
$p = "";
$comment = array();
Awal:
	$getComment = cek($urlPost, $p);
	if($p=="") $getComment = cek($urlPost);
	$p = str_replace("+","",urlencode(str_replace('"','\"',$getComment[1])));
	$cmn = $getComment[0];
	if(!@$count) $count = $getComment[2];
	for($a=0;$a<@count($cmn);$a++){
		$text = $cmn[$a]['node']['text'];
		if($validasi){
			if(count(@explode("@", $text))<$min) continue;
		}
		$comment[] = array("text" => $text, "username" => $cmn[$a]['node']['owner']['username']);
	}
	if(!empty($p)) goto Awal;
	print_r(random(@$comment));

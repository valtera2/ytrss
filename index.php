<?php
define("BASEURL", "https://www.youtube.com/feeds/videos.xml?channel_id=");
define("EMBEDBASE", "https://www.youtube.com/embed/");
$config = parse_ini_file("extract.ini",true);

include_once("header.php");

function open_xml($filename) {
	if (file_exists($filename)) {
	$xml = simplexml_load_file($filename);
	return $xml;
	}
}

function strip_yurl($url) {
	$strip_url = parse_url($url);
	$strip_url = substr($strip_url['query'],2);
	return $strip_url;
}

/** 
*
* @param object $obj, string $title, int $conf_key config key.
*
**/

function gen_out($obj, $title, $conf_key) {
	$refresh_url = "<a href=" . $_SERVER['PHP_SELF'] . "?conf=" . $conf_key . ">" . "refresh rss" . "</a>";
	echo "<div class='title'>" . $title . "</div>";
	echo "<div class='toolbar'>" . $refresh_url . "</div>";
	echo "<table>";
	if ($obj) {
	foreach ($obj->entry as $value) {
		$title = $value->title;
		echo "<td>";
		echo $title;
		echo "</td>";
		foreach ($value->link[0]->attributes() as $key => $attr_val) {
			if ($key == "href") {
				echo "<td>";
				//echo "<a href=" . "$attr_val" . ">" . "full link" . "</a>";
				echo $attr_val;
				echo "</td>";
				echo "<td>";
				echo "<a href=" . EMBEDBASE . strip_yurl($attr_val) . ">" . "embed link" . "</a>";
				echo "<tr>";
			}
		}
		
	}
	echo "</table>";
	} else {
		echo "<div class='error'>" . "missing xml - run refresh to download" . "</div>";
	}
	
	
}

function get_ini_filename($conf) {
	global $config;
	$filename = $config[$conf]['path'];
return $filename;
}

function get_ini_id($conf) {
	global $config;
	$id = $config[$conf]['id'];
	return $id; //channel id
}

function curl_client($url, $filename) {
	$ch = curl_init($url);
	$file = fopen($filename, "w");
	curl_setopt($ch, CURLOPT_FILE, $file);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_exec($ch);
	$error = curl_error($ch);
	curl_close($ch);
	fclose($file);
	$result = (empty($error)) ? "Refresh ok" : $error;
	return $result;
}

if (isset($_GET['conf']))
{
	$channel = get_ini_id($_GET['conf']);
	$file = get_ini_filename($_GET['conf']);
	$url = BASEURL . $channel;
	$out = curl_client($url, $file);
	$out = ($out != 'Refresh ok') ? ("<div class='error'>" . $out . "</div>") : $out;
	echo $out;
}

foreach ($config as $key => $value) {
	$obj = open_xml($config[$key]['path']);
	gen_out($obj, $config[$key]['title'], $key);
}

include_once("footer.php");




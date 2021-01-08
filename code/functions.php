<?php

// greška 404
function notFoundAdmin(){
	header("HTTP/1.0 404 Not Found"); 
  require_once("page/_error.php");
  loadError();
}
// greška 404
function notFound(){
  global $content, $domain;
	header("HTTP/1.0 404 Not Found"); 
  require_once("page/error.php");
  require_once("codes/template2.php");
  die();
}



// uklanja prazna mjesta
function removeSpace($str){
  $ovo = array("\r\n", "\n", "&nbsp;", "  ");
	$str = str_replace($ovo, " ", $str, $c);
	if($c>0) return removeSpace($str);
  if(substr($str, -1, 1) == " ") $str = substr($str, 0, -1);
  if(substr($str, 0, 1) == " ") $str = substr($str, 1);
	return $str;
}
// uklanja hypene
function removeHyphen($str){
	$str = str_replace("--", "-", $str, $c);
	if($c>0) return removeHyphen($str);
	else return $str;
}



function removeSpecial($tekst){
	$tekst = removeSpace(strip_tags($tekst));
	
  // pretvaranje u -
  $ovo = array(" ", "=", ".", ":", "+", "_");
  $tekst = str_replace($ovo, "-", $tekst);
  unset($ovo);
  
  // pretvaranje specijalnih znakova u odgovarajuce normalne znakove
  $ovo = array(1=>"&amp;", 2=>"č", 3=>"ć", 4=>"ž", 5=>"š", 6=>"đ", 7=>"Č", 8=>"Ć", 9=>"Ž", 10=>"Š", 11=>"Đ",
               12=>"&#269;", 13=>"&#268;", 14=>"&#263;", 15=>"&#262;", 16=>"&#382;", 17=>"&#381;", 18=>"&scaron;", 19=>"&Scaron;", 20=>"&#273;", 21=>"&#272;",
               22=>"©", 23=>"®", 24=>"€", 25=>"%", 26=>"$", 27=>"@", 28=>"ä", 29=>"Ä", 30=>"ü", 31=>"Ü", 32=>"ß", 33=>"ö", 34=>"Ö", 35=>"ó");
  $u = array(1=>"&", 2=>"c", 3=>"c", 4=>"z", 5=>"s", 6=>"dj", 7=>"C", 8=>"C", 9=>"Z", 10=>"S", 11=>"Dj",
             12=>"c", 13=>"C", 14=>"c", 15=>"C", 16=>"z", 17=>"Z", 18=>"s", 19=>"S", 20=>"dj", 21=>"Dj",
             22=>"C", 23=>"R", 24=>"-eur", 25=>"-posto", 26=>"-dol", 27=>"-at-", 28 =>"a", 29=>"A", 30=>"u", 31=>"U", 32=>"ss", 33=>"o", 34=>"O", 35=>"o",);
  $tekst = str_replace($ovo, $u, $tekst);
  unset($ovo, $u);
  
  // brisanje specijalnih znakovi koji nisu potrebni
  $ovo = array("&quot;", "&lt;", "&gt;", "&raquo;", "&laquo;", '"', "'", "(", ")",	"'", ",", "!", "?", "&#190;", "%B9", "*", "~", "<", ">", "ˇ", "^", "˘", "°", "˛", "`", "˙", "´", "˝", "¨", "¸", "/", "[", "]", "{", "}", "§", "ł", "Ł", "÷", "×", "¤", "|", "\\", "„", "”", "≠", "#", ";");
  $tekst = str_replace($ovo, "", $tekst);
	unset($ovo);
  
  $tekst = removeHyphen(strtolower(str_replace("&", "-i-", $tekst)));
  if(substr($tekst, -1, 1) == "-") $tekst = substr($tekst, 0, -1);
  if(substr($tekst, 0, 1) == "-") $tekst = substr($tekst, 1);
  return $tekst;
}



// uklanja specijalne znakove iz varijable
function cleanup($data){
  $chars = array("%", "'");
  $data = trim(strip_tags(htmlspecialchars(str_replace($chars, "", $data))));
  return $data;
}



// siječe određen broj znakova
function cutText($text, $broj, $tip = 1){
  $text = removeSpace(strip_tags($text));
	$broj_rijeci = strlen($text);
	if($broj_rijeci > $broj){
  	if($tip == 2){
      $text = substr($text, 0, $broj);
    }
    else{
    	$text = substr($text, 0, $broj);
      $broj = strrpos($text, " ");
      $text = substr($text, 0, $broj);
    }
    $signs = array(".", ",", ":", ";", "!", "?", "(", ")", "-");
    if(in_array(substr($text, -1, 1), $signs))
      $text = substr($text, 0, -1);
    $text = $text."...";
	}
  
	return $text;
}



// valjana email adresa
function is_mail($mail){
  if(strlen($mail) < 10 || strlen($mail) > 60) return 0;
  else return (preg_match('/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`{|}~]+@([-0-9A-Z]+\.)+([0-9A-Z]){2,4}$/i', trim($mail)));
}
// valjana web adresa
function is_url($url){
  if(strlen($url) < 12 || strlen($url) > 150) return 0;
  else return preg_match("/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_\!]+$/i", $url);
}



// slanje emaila
function sendMail($mail, $title, $txt, $send=null){
  $header = "MIME-Version: 1.0 \r\n";
  $header .= "Content-type: text/html; charset=utf-8 \r\n";
  $header .= "From: ".($send ? "$send" : "Facebook Hrvatska <info@facebook-hrvatska.com>")."\r\n";

  $success = mail($mail, $title, $txt, $header);
  return $success;
}



// broj stranica i rezultata
function getPages($field, $table, $where = "", $num=2){
  global $results, $page, $p, $db, $domain;
  
  if($num < 1 || $num > 5) $num = 2;
  $redirect = $domain['url'];
  for($i = 1; $i < $num; $i++) $redirect = $redirect.$p[$i]."/";
  if($p[$num] == 1) header("Location: $redirect");
  
  if(!$p[$num] || $p[$num] < 1) $p[$num] = 1;
  $db->query("SELECT $field FROM $table".($where ? " $where" : ""));
  $results['all']=$db->num_rows;
  $page['all'] = ceil($results['all'] / $results['limit']);
  if(!$page['all']) $page['all'] = 1;
  $page['current'] = $p[$num];
  if($page['current'] > $page['all']) header("Location: $redirect");
  $results['start'] = ($page['current'] * $results['limit']) - $results['limit'];
  
  $results['do'] = $results['start'] + $results['limit'];
  if($results['do'] > $results['all']) $results['do'] = $results['all'];
  $results['start2'] = $results['start'] + 1;
  if($results['all'] < $results['start2']) $results['start2'] = 0;
  
  return $page;
  return $results;
}



// ispis tagova
function getTags($var){
	$n=array();
	$array=explode("-", removeSpecial($var));
  $small = array("tv", "et", "dj", "hr");
	foreach($array as $a){
		if((in_array($a, $small) || strlen($a)>2) && !in_array($a, $n))
			$n[]=$a;
	}
	return implode(", ", $n);
}



// slika teksta
function getImage($id, $link="", $size=1, $path=""){
	if(is_file($path."upload/text/$id.jpg"))
		return $path."upload/text".($size == 2 ? "-small" : "")."/".($link ? "$link-" : "")."$id.jpg";
	else
		return $path."images/facebook".($size == 2 ? "-small" : "").".jpg";
}



// slika teksta
function getGravatar($mail){
  $mail = md5(removeSpace($mail));
	return "http://www.gravatar.com/avatar/$mail.jpg?s=64";
}



// dobivanje hosta iz linka
function getHost($url){
	$url=str_replace("www.", "", parse_url($url));
	return $url["host"];
}



// ispis vremena - prije 16 dana - prije 21 h - prije 56 min
function passedTime($time){
	$cur = time();
	if($time+300>$cur){
		$var=intval(($cur-$time)/60+1);
		return "maloprije";
	}elseif($time+3600>$cur){
		$var=intval(($cur-$time)/60+1);
		return "prije $var min";
	}elseif($time+86400>$cur){
		$var=intval(($cur-$time)/3600);
		return "prije $var h";
	}elseif($time+2592000>$cur){
		$var=intval(($cur-$time)/86400);
		return "prije $var ".($var==1 || $var==21 ? "dan" : "dana");
	}else
		return date("d.m.Y.", $time);
}

?>

<?php
set_magic_quotes_runtime(false);
if(get_magic_quotes_gpc()){
  $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
  while(list($key, $val) = each($process)){
    foreach ($val as $k => $v) {
      unset($process[$key][$k]);
      if(is_array($v)){
        $process[$key][stripslashes($k)] = $v;
        $process[] = &$process[$key][stripslashes($k)];
      }
      else $process[$key][stripslashes($k)] = stripslashes($v);
    }
  }
  unset($process);
}

ob_start("ob_gzhandler");
ini_set("memory_limit", "80M");

$domain['root'] = "www.facebook-hrvatska.com";

$url = $_SERVER['HTTP_HOST'];
$arr = explode("www",$url);
if(count($arr) == 1){
  $redirect = "http://$domain[root]";
  $req = $_SERVER['REQUEST_URI'];
	header("Location:$redirect$req");
}

$domain['url'] = "http://$domain[root]/admin/";
$domain['name'] = "Facebook HR";
$domain['protect'] = "facebookhr#123";

$cms['name'] = "CMS";
$cms['version'] = "1.1.9";
$cms['time'] = date("Y");

// privilegije moderatora
$cms['mod_days'] = 7;
$cms['mod_time'] = time()-60*60*24*$cms['mod_days'];

// broj rezultata po stranici
$results['limit'] = 20;

$author['name'] = "Frano Šašvari";
$author['website'] = "Facebook Hrvatska";
$author['link'] = "http://www.facebook-hrvatska.com/";
$author['email'] = "ccrogames@gmail.com";

for($i = 1; $i <= 4; $i++){
  $p[$i] = cleanup($_GET["p$i"]);
}

$content['categs'] = array(
  1=>"Novosti",
  2=>"Tutorijali",
  3=>"Igre",
  4=>"Aplikacije",
  5=>"Zanimljivosti"
);

$content['upload']['allow'] = array("image/jpg", "image/jpeg", "image/pjpeg", "image/gif", "image/png");
$content['upload']['remove'] = array(".jpg", ".png", ".gif");
$content['upload']['check'] = array(1=>"image/");
$content['upload']['format'] = array(1=>".jpg");
$content['upload']['folder'] = array(
  1=>"upload/images/", 2=>"upload/images-small/", // kod upload forme
  3=>"upload/text/", 4=>"upload/text-small/", 5=>"upload/text-smaller/" // kod unosa teksta
);

// veličina slike
$content['upload']['image_size'] = array(
  1=>"400", 2=>"240", // velika slika
  3=>"200", 4=>"120",  // mala slika
  5=>"50", 6=>"50",  // najmanja slika
  7=>"600",  // upload forma
  8=>"135", 9=>"90"  // najmanja slika
);



$categ = array(
	1=>array(
		"name"=>"Novosti",
		"link"=>"facebook-novosti",
		"h1"=>"Facebook novosti",
		"h2"=>"Novosti, članca i tekstovi o Facebooku.",
		"desc"=>"Donosimo vam najnovije novosti, članke, tekstove i vijesti o danas najpopularnijoj društvenoj mreži, <strong>Facebooku</strong>.",
		"meta-title"=>"Facebook novosti, članci i tekstovi",
		"meta-desc"=>"Donosimo vam najnovije novosti, članke, tekstove i vijesti o danas najpopularnijoj društvenoj mreži, Facebooku.",
		"meta-keywords"=>"novosti, clanci, tekstovi, vijesti"
	),
	2=>array(
		"name"=>"Tutorijali",
		"link"=>"facebook-pomoc",
		"h1"=>"Facebook pomoć",
		"h2"=>"Pomoć i tutorijali za Facebook.",
		"desc"=>"Donosimo vam detaljno opisane tutorijale koji su popraćeni slikama, za pomoć i lakše snalaženje na <strong>Facebooku</strong>.",
		"meta-title"=>"Facebook pomoć i tutorijali",
		"meta-desc"=>"Donosimo vam detaljno opisane tutorijale koji su popraćeni slikama, za pomoć i lakše snalaženje na Facebooku.",
		"meta-keywords"=>"tutorijali, pomoc, help, clanci, tekstovi"
	),
	3=>array(
		"name"=>"Igre",
		"link"=>"facebook-igre",
		"h1"=>"Facebook igre",
		"h2"=>"Igre i igrice na Facebooku.",
		"desc"=>"Donosimo vam detaljne recenzije i najave za <strong>Facebook</strong> igre i igrice, popraćene screenshotovima i video klipovima.",
		"meta-title"=>"Facebook igre i igrice",
		"meta-desc"=>"Recenzije, najave i pregled Facebook igara i igrica, Zynga igre, sve to popraćeno screenshotovima i video klipovima na Facebook platformi.",
		"meta-keywords"=>"igre, igrice, games, game, zynga"
	),
	4=>array(
		"name"=>"Aplikacije",
		"link"=>"facebook-aplikacije",
		"h1"=>"Facebook aplikacije",
		"h2"=>"Aplikacije za Facebook.",
		"desc"=>"Lista <strong>Facebook</strong> aplikacija. Donosimo vam tekstove i detaljan opis aplikacija (Facebook Apps) za <strong>Facebook</strong> platformu.",
		"meta-title"=>"Facebook aplikacije - Facebook Apps",
		"meta-desc"=>"Lista Facebook aplikacija. Donosimo vam detaljan opis svih aplikacija (Facebook Apps) za Facebook platformu.",
		"meta-keywords"=>"aplikacije, apps, applications"
	),
	5=>array(
		"name"=>"Zanimljivosti",
		"link"=>"facebook-zanimljivosti",
		"h1"=>"Facebook zanimljivosti",
		"h2"=>"Zabava na Facebooku.",
		"desc"=>"Zanimljivosti i zabava na <strong>Facebooku</strong>. Pregled slika, fotografija, video zapisi, smiješni i šaljivi tekstovi, statusi, smajlići i smajliji. Prikaz hrvatske estradne scene na <strong>Facebooku</strong>.",
		"meta-title"=>"Facebook zanimljivosti - Zabava - Fotografije, Slike - Video",
		"meta-desc"=>"Zanimljivosti i zabava na Facebooku. Pregled slika, fotografija, video zapisi, smješnih i šaljivih tekstova, statusa, smajlića. Pregled hrvatske estradne scene na Facebooku.",
		"meta-keywords"=>"zanimljivosti, zabava, slike, fotografije, video"
	)
);

?>

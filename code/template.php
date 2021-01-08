<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<title><?php echo "$content[title] | $domain[name] $cms[name]";?></title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<meta http-equiv='content-language' content='hr' />
<base href='<?php echo "$domain[url]";?>' />
<link rel='stylesheet' href='css/style.css' media='screen' type='text/css' />
<!--[if IE 6]><link rel='stylesheet' href='css/style-ie.css' media='screen' type='text/css' /><![endif]-->
<link rel='stylesheet' href='css/style-print.css' media='print' type='text/css' />
<script type='text/javascript' src='http://code.jquery.com/jquery-1.8.0.min.js'></script>
<script type='text/javascript' src='js/tiny_mce/tiny_mce.js'></script><?php echo "$content[js]";?>

</head>
<body>
<div id='wrapper_header'>
  <div id='container_header'>
    <div id='name'>
      <h1><?php echo "$domain[name]";?></h1>
      <p><small><?php echo "$cms[name] ($cms[version])";?></small></p>
    </div>
    <div class='info'>
      <h2>Korisnički podaci</h2>
      <p><small>Korisničko ime: <b><?php echo "$user[username]";?></b><br />E-mail: <b><?php echo "$user[email]";?></b><br />Privilegije: <?php echo ($user['is_admin'] ? "<b class='admin'>administrator</b>" : "<b class='mod'>moderator</b>")?></small></p>
    </div>
    <div class='info'>
      <h2>User CP</h2>
      <p><span><a href='usercp/' class='usercp'>Izmjena podataka</a></span><br /><span><a href='logout/' class='logout'>Odjavi se</a></span></p>
    </div>
    <div class='clear'></div>
  </div>
</div>
<div id='wrapper_mid'>
  <div id='container_mid'>
    <div id='column_left'>
      <p><span><a href='' class='home'>Početna stranica</a></span></p>
      <h3>Tekstovi</h3>
      <div class='box'>
        <ul>
          <li><span><a href='text/' class='view'>Tekstovi</a></span></li>
          <li><span><a href='tags/' class='tags'>Tagovi</a></span></li>
          <li><span><a href='http://www.facebook-hrvatska.com/aktivnosti/' target='_blank' class='comments'>Komentari</a></span></li>
        </ul>
      </div>
      <h3>Upload slika</h3>
      <div class='box'>
        <ul>
          <li><span><a href='upload/' class='picture'>Slike</a></span></li>
        </ul>
      </div>
      <h3>FBpages</h3>
      <div class='box'>
        <ul>
          <li><span><a href='fbpages/' class='fbpages'>Facebook stranice</a></span></li>
        </ul>
      </div>
      <h3>Ankete</h3>
      <div class='box'>
        <ul>
          <li><span><a href='poll/' class='poll'>Ankete</a></span></li>
        </ul>
      </div>
      <h3>Linkovi</h3>
      <div class='box'>
        <ul>
          <li><span><a href='links/' class='views'>Linkovi</a></span></li>
        </ul>
      </div>
      <h3>Korisnici</h3>
      <div class='box'>
        <ul>
          <li><span><a href='users/' class='views'>Lista korisnika</a></span></li>
        </ul>
      </div>
      <p><span><a href='usercp/' class='usercp'>Izmjena podataka</a><br /><a href='logout/' class='logout'>Odjavi se</a></span></p>
    </div>
    <div id='main'><?php echo "$content[text]";?>
    </div>
    <div class='clear'></div>
  </div>
</div>
<div id='wrapper_footer'>
  <p>&copy;<?php echo "$cms[time]";?> <b><?php echo "$cms[name]";?> (<?php echo "$cms[version]";?>)</b> za <b><?php echo "$domain[name]";?></b> web stranicu. Dizajn i k&#244;d by: <a href='<?php echo "$author[link]";?>' target='_blank'><?php echo "$author[website]";?></a>. Sva prava pridržana.</p>
</div>
</body>
</html>
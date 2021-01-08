<?php
function loadLinksForm($edit = 0){
  global $db, $p, $content, $domain, $cms, $user;
  
  if($edit){
    $var['title'] = "Izmjeni (ID: $p[3])";
    $var['button'] = "Izmjeni";
    
    $form=$db->fetch_first("SELECT * FROM links WHERE ID=$p[3]");
  }
  else{
    $var['title'] = "Dodaj";
    $var['button'] = "Dodaj";
  }
  
  $content["title"] = $var['title']." - Tekstovi";
  
  $content["js"]= "
<script type='text/javascript'>
  tinyMCE.init({
    // General options
    mode : 'exact',
    elements : 'text',
    theme : 'advanced',
    plugins : 'style,advimage,advlink,inlinepopups,media,contextmenu,directionality,visualchars,xhtmlxtras,template,wordcount,advlist,autosave',
    
    // Theme options
    theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,sub,sup,|,formatselect,blockquote,|,bullist,numlist,|,link,unlink,|,hr,image,media,charmap',
    theme_advanced_buttons2 : 'cleanup,removeformat,|,code,',
    theme_advanced_buttons3 : '',
    theme_advanced_buttons4 : '',
    theme_advanced_toolbar_location : 'top',
    theme_advanced_toolbar_align : 'left',
    theme_advanced_statusbar_location : 'bottom',
    theme_advanced_resizing : false,
    theme_advanced_blockformats : 'p,h3',
    
    // Example content CSS (should be your site CSS)
    content_css : 'js/tiny_mce/themes/advanced/skins/default/content.css',
    
    // Drop lists for link/image/media/template dialogs
    template_external_list_url : 'lists/template_list.js',
    external_link_list_url : 'lists/link_list.js',
    external_image_list_url : 'lists/image_list.js',
    media_external_list_url : 'lists/media_list.js',
    
    // Style formats
    style_formats : [
      {title : 'Bold text', inline : 'b'},
      {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
      {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
      {title : 'Example 1', inline : 'span', classes : 'example1'},
      {title : 'Example 2', inline : 'span', classes : 'example2'},
      {title : 'Table styles'},
      {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
    ],
    
    // Replace values for the template plugin
    template_replace_values : {
      username : 'Some User',
      staffid : '991234'
    }
    });
</script>";
  
  $slanje = "";
  $greska = 1;

  if($_POST['submit_ok']){
    $form['name'] = removeSpace($_POST['name']);
    $form['title'] = removeSpace($_POST['title']);
    $form['url'] = cleanup($_POST['url']);
    
    $form['protect'] = cleanup($_POST['protect']);

    $greska = 0;
    
    if($greska == 0 && (strlen($form['name']) < 3 || strlen($form['name']) > 80)){
      $slanje = "<p class='error'>Naziv linka nije ispravno napisan! Mora biti između 3 i 80 znakova.</p>";
      $greska = 1;
    }
    if($greska == 0 && (strlen($form['title']) < 3 || strlen($form['title']) > 80)){
      $slanje = "<p class='error'>Title tag linka nije ispravno napisan! Mora biti između 3 i 80 znakova.</p>";
      $greska = 1;
    }
    if($greska == 0 && !is_url($form['url'])){
      $slanje = "<p class='error'>URL nije ispravan! Mora sadržavati <b>http://</b> i ostale potrebne znakove.</p>";
      $greska = 1;
    }
    if($greska == 0){
      $referrer = parse_url(strtolower($_SERVER['HTTP_REFERER']));
      $host = "$referrer[host]";
        
      if($form['protect'] != $domain['protect'] || $host != $domain['root']){
        $slanje = "<p class='error'>Pokušaj hakiranja ili spama!</p>";
        $greska = 1;
      }
    }
    if($greska == 0){
      if($edit){
        $row=$db->update("links", array("name"=>$form['name'], "title"=>$form['title'], "url"=>$form['url']), "ID='$p[3]'");
        $greska = 1;
      }
      else
        $row=$db->insert("links", array("name"=>$form['name'], "title"=>$form['title'], "url"=>$form['url']));
      
      if($row) $slanje = "<p class='ok'>Link <b>$form[name]</b> je uspješno ".($edit ? "izmjenjen" : "dodan u bazu")."! <span><a href='$p[1]/' class='views'>Pregled linkova &raquo;</a></span></p>";
      else $slanje = "<p class='error'>Došlo je do greške prilikom obrade podataka. Pokušajte ponovno!</p>";
    }
  }
  
  $content["text"]="
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; <a href='$p[1]/'>Tekstovi</a> &gt; $var[name]</small></p>
      <h4>Tekstovi - $var[title]</h4>
      ".($greska ? "<p><span><a href='$p[1]/' class='views'>Pregled linkova &raquo;</a></span></p>
      <form method='post' action='$p[1]/$p[2]/".($edit ? "$p[3]/" : "")."' enctype='multipart/form-data'>" :"" )."
        $slanje";
      if($greska){
        $content["text"].=($edit ? "<p><label for='id'>* ID:</label><input name='id' id='id' type='text' value='$p[3]' class='text' style='width:50px;color:#999;background-color:#f5f5f5' disabled='disabled' /></p>" : "")."
        <p><label for='name'>* Naziv:</label><input name='name' id='name' type='text' value='$form[name]' maxlength='65' class='text' style='width:300px' /></p>
        <p><label for='title'>* Title tag:</label><input name='title' id='title' type='text' value='$form[title]' maxlength='65' class='text' style='width:300px' /></p>
        <p><label for='url'>* Link:</label><input name='url' id='url' type='text' value='$form[url]' maxlength='150' class='text' style='width:495px' /></p>
        <p class='button_align'><input name='protect' id='protect' type='hidden' value='$domain[protect]' />
        <input type='submit' name='submit_ok' id='submit_ok' value='$var[button]' class='button' /></p>
        <p class='button_align'><b>polja označena zvjezdicom (*) su obavezna</b></p>
      </form>
      <p><span><a href='$p[1]/' class='views'>Pregled linkova &raquo;</a></span></p>";
    }
}
?>

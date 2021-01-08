<?php
function loadTagsForm($edit = 0){
  global $db, $p, $content, $domain, $cms, $user;
  
  if($edit){
    $var['title'] = "Izmjeni (ID: $p[3])";
    $var['button'] = "Izmjeni";
    
    $form=$db->fetch_first("SELECT * FROM text_tag WHERE tag_link='$p[3]'");
  }
  else{
    $var['title'] = "Dodaj";
    $var['button'] = "Dodaj";
  }
  
  $content["title"] = $var['title']." - Tagovi";
  
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
    $form['title'] = removeSpace($_POST['title']);
    $form['text'] = removeSpace($_POST['text']);
    $ovo = array("&Scaron;", "&scaron;", "<p>&nbsp;</p>");
    $u = array("Š", "š", "");
    $form['text'] = str_replace($ovo, $u, $form['text']);
    if(substr($form['text'], -6, 6) == "<br />") $form['text'] = substr($form['text'], 0, -6);
    
    $form['protect'] = cleanup($_POST['protect']);

    $greska = 0;

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
        $row=$db->update("text_tag", array("title"=>$form['title'], "text"=>$form['text']), "tag_link='$p[3]'");

        $greska = 1;
      }
      
      if($row) $slanje = "<p class='ok'>Tag <b>$form[title]</b> je uspješno ".($edit ? "izmjenjen" : "dodan u bazu")."! <span><a href='$p[1]/' class='tags'>Pregled tagova &raquo;</a></span></p>";
      else $slanje = "<p class='error'>Došlo je do greške prilikom obrade podataka. Pokušajte ponovno!</p>";
    }
  }
  
  $titleImage = ($form['titleImage'] ? $form['titleImage'] : "Facebook");
  
  $content["text"]="
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; <a href='$p[1]/'>Tagovi</a> &gt; $var[title]</small></p>
      <h4>Tagovi - $var[title]</h4>
      ".($greska ? "<p><span><a href='$p[1]/' class='tags'>Pregled tagova &raquo;</a></span></p>
      <form method='post' action='$p[1]/$p[2]/".($edit ? "$p[3]/" : "")."' enctype='multipart/form-data'>" :"" )."
        $slanje";
      if($greska){
        $content["text"].="<p><label for='title'>* Tag:</label><input name='tag' id='tag' type='text' value='$form[tag]' maxlength='85' class='text' style='width:300px;color:#999;background-color:#f5f5f5' disabled='disabled' /></p>
        <p><label for='subtitle'>Naslov:</label><input name='title' id='title' type='text' value='$form[title]' maxlength='150' class='text' style='width:835px' /></p>
        <p><label for='text'>Tekst:</label><textarea name='text' id='text' cols='104' rows='24' style='width:850px;height:500px'>".(!$edit && !$_POST['submit_ok'] ? "" : $form['text'])."</textarea></p>
        ".($edit ? "<p><label>Trenutna slika:</label><img src='../upload/tags/$form[tag_link].jpg' alt='' title='' width='160' height='90' class='image' /></p>" : "")."
        <p class='button_align'><input name='protect' id='protect' type='hidden' value='$domain[protect]' />
        <input type='submit' name='submit_ok' id='submit_ok' value='$var[button]' class='button' /></p>
        <p class='button_align'><b>polja označena zvjezdicom (*) su obavezna</b></p>
      </form>
      <p><span><a href='$p[1]/' class='tags'>Pregled tagova &raquo;</a></span></p>";
    }
}
?>

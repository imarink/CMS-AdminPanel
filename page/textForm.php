<?php
function loadTextForm($edit = 0){
  global $db, $p, $content, $domain, $cms, $user, $categ;
  
  if($edit){
    $var['title'] = "Izmjeni (ID: $p[3])";
    $var['button'] = "Izmjeni";
    
    $form=$db->fetch_first("SELECT text.*, users.username FROM text LEFT JOIN users ON text.ID_USER=users.ID_USER WHERE text.ID_TEXT=$p[3]");
    $form['categ_old'] = $form['categ'] = $form['ID_CAT'];
    $form['tags_old'] = $form['tags'];
    $form['author'] = $form['username'];
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
    plugins : 'style,advimage,advlink,inlinepopups,media,contextmenu,directionality,visualchars,xhtmlxtras,template,wordcount,advlist,autosave,table',
    
    // Theme options
    theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,sub,sup,|,formatselect,blockquote,|,bullist,numlist,|,link,unlink,|,hr,image,media,charmap',
    theme_advanced_buttons2 : 'tablecontrols',
    theme_advanced_buttons3 : 'cleanup,removeformat,|,code',
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
    $form['subtitle'] = cleanup($_POST['subtitle']);
    $form['text'] = removeSpace($_POST['text']);
    $form['tags'] = strtolower(removeSpace($_POST['tags']));
    $ovo = array("&Scaron;", "&scaron;", "<p>&nbsp;</p>");
    $u = array("Š", "š", "");
    $form['text'] = str_replace($ovo, $u, $form['text']);
    if(substr($form['text'], -6, 6) == "<br />") $form['text'] = substr($form['text'], 0, -6);
    
    $form['categ'] = cleanup($_POST['categ']);
    $form['tags'] = cleanup($_POST['tags']);

    $form['source'] = cleanup($_POST['source']);
    $form['sourceImage'] = cleanup($_POST['sourceImage']);
    $form['titleImage'] = cleanup($_POST['titleImage']);
    $form['longurl'] = ($edit ? cleanup($_POST['longurl']) : "");
    $form['special'] = (isset($_POST['special']) && $_POST['special'] ? 1 : 0);
    $form['published'] = (isset($_POST['published']) && $_POST['published'] ? 1 : 0);
    
    $form['protect'] = cleanup($_POST['protect']);

    $greska = 0;
    
    if($greska == 0 && (strlen($form['title']) < 5 || strlen($form['title']) > 85)){
      $slanje = "<p class='error'>Naslov nije ispravno napisan! Mora biti između 5 i 85 znakova.</p>";
      $greska = 1;
    }
    if($greska == 0 && strlen($form['text']) < 50){
      $slanje = "<p class='error'>Tekst nije ispravno napisan! Mora biti minimalno 50 znakova.</p>";
      $greska = 1;
    }
    if($greska == 0 && ($form['categ'] < 1 || $form['categ'] > 5)){
      $slanje = "<p class='error'>Morate izabrati kategoriju!</p>";
      $greska = 1;
    }
    if($greska == 0 && (!isset($_FILES["file"]) || !is_array($_FILES["file"]))){
      $slanje = "<p class='error'>Datoteka nije ispravna! Pokušajte ponovno.</p>";
      $greska = 1;
    }
    if($greska == 0 && $_FILES["file"]["name"]){
      if(!$_FILES['file']['name'] || !$_FILES['file']['type']){
        $slanje = "<p class='error'>Morate izabrati dokument koji želite uploadati!</p>";
        $greska = 1;
      }
      if($greska == 0 && !in_array($_FILES['file']['type'], $content['upload']['allow'])){
        $slanje = "<p class='error'>Format datoteke nije podržan! Podržani su slijedeći formati: slike (jpg, gif, png).</p>";
        $greska = 1;
      }
      if($greska == 0){
        for($i = 1; $i < 2; $i++){
          if(substr_count($_FILES['file']['type'], $content['upload']['check'][$i]) > 0 && !$break){
            $form['type'] = $i;
            $break = 1;
          }
        }
      }
    }
    if($greska == 0 && $form['source']){
      if(!is_url($form['source'])){
        $slanje = "<p class='error'>Izvor nije ispravan! Mora sadržavati <b>http://</b> i ostale potrebne znakove.</p>";
        $greska = 1;
      }
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
        if($user['is_admin'] && !$form['published']) $time = time();
        else $time = $form['time'];
        $row=$db->update("text", array("title"=>$form['title'], "subtitle"=>$form['subtitle'], "text"=>$form['text'], "tags"=>$form['tags'], "source"=>$form['source'], "titleImage"=>$form['titleImage'], "sourceImage"=>$form['sourceImage'], "longurl"=>$form['longurl'], "special"=>$form['special'], "ID_CAT"=>$form['categ'], "published"=>$form['published'], "time"=>$time), "ID_TEXT='$p[3]'");
        if($_FILES["file"]["name"]){
          if(is_file("../".$content['upload']['folder'][3].$form['ID_TEXT'].$content['upload']['format'][1]))
            unlink("../".$content['upload']['folder'][3].$form['ID_TEXT'].$content['upload']['format'][1]);
          if(is_file("../".$content['upload']['folder'][4].$form['ID_TEXT'].$content['upload']['format'][1]))
            unlink("../".$content['upload']['folder'][4].$form['ID_TEXT'].$content['upload']['format'][1]);
          if(is_file("../".$content['upload']['folder'][5].$form['ID_TEXT'].$content['upload']['format'][1]))
            unlink("../".$content['upload']['folder'][5].$form['ID_TEXT'].$content['upload']['format'][1]);

          resizePicCrop($_FILES["file"], "../".$content['upload']['folder'][3].$form['ID_TEXT'].$content['upload']['format'][1], $content['upload']['image_size'][1], $content['upload']['image_size'][2]);
          resizePicCrop($_FILES["file"], "../".$content['upload']['folder'][4].$form['ID_TEXT'].$content['upload']['format'][1], $content['upload']['image_size'][3], $content['upload']['image_size'][4]);
          resizePicCrop($_FILES["file"], "../".$content['upload']['folder'][5].$form['ID_TEXT'].$content['upload']['format'][1], $content['upload']['image_size'][5], $content['upload']['image_size'][6]);
        }
        // izmjena tagova
        if($form['tags'] != $form['tags_old']){
          // unos novih tagova
          $tags = explode(",", $form['tags']);
          $num = count($tags);
          for($i = 0; $i < $num; $i++){
            $tag = removeSpace($tags[$i]);
            if(strlen($tag) > 2){
              $link = removeSpecial($tag);
              $row_tag=$db->fetch_first("SELECT tag_link FROM text_tag WHERE tag_link='$link'");
              if($db->num_rows>0) $db->update("text_tag", array("count"=>"count+1"), "tag_link='$link'");
              else $db->insert("text_tag", array("tag"=>$tag, "tag_link"=>$link, "count"=>1));
            }
          }
          // izmjene starih tagova
          $tags = explode(",", $form['tags_old']);
          $num = count($tags);
          for($i = 0; $i < $num; $i++){
            $tag = removeSpace($tags[$i]);
            if(strlen($tag) > 2){
              $link = removeSpecial($tag);
              $row_tag=$db->fetch_first("SELECT tag_link FROM text_tag WHERE tag_link='$link' AND count>0");
              if($db->num_rows>0) $db->update("text_tag", array("count"=>"count-1"), "tag_link='$link'");
            }
          }
        }
        $greska = 1;
      }
      else{
        $row=$db->insert("text", array("title"=>$form['title'], "subtitle"=>$form['subtitle'], "text"=>$form['text'], "tags"=>$form['tags'], "source"=>$form['source'], "titleImage"=>$form['titleImage'], "sourceImage"=>$form['sourceImage'], "special"=>$form['special'], "ID_CAT"=>$form['categ'], "ID_USER"=>$user['id'], "time"=>time(), "published"=>$form['published']));
        $form['ID_TEXT'] = $row;
        $form['longurl']=$categ[$form['categ']]['link']."/".removeSpecial($form['title'])."-$form[ID_TEXT]/";
        $db->update("text", array("longurl"=>$form['longurl']), "ID_TEXT='$form[ID_TEXT]'");
        if($_FILES["file"]["name"]){
          resizePicCrop($_FILES["file"], "../".$content['upload']['folder'][3].$form['ID_TEXT'].$content['upload']['format'][1], $content['upload']['image_size'][1], $content['upload']['image_size'][2]);
          resizePicCrop($_FILES["file"], "../".$content['upload']['folder'][4].$form['ID_TEXT'].$content['upload']['format'][1], $content['upload']['image_size'][3], $content['upload']['image_size'][4]);
          resizePicCrop($_FILES["file"], "../".$content['upload']['folder'][5].$form['ID_TEXT'].$content['upload']['format'][1], $content['upload']['image_size'][5], $content['upload']['image_size'][6]);
        }
        // unos tagova
        $tags = explode(",", $form['tags']);
        $num = count($tags);
        for($i = 0; $i < $num; $i++){
          $tag = removeSpace($tags[$i]);
          if(strlen($tag) > 2){
            $link = removeSpecial($tag);
            $row_tag=$db->fetch_first("SELECT tag_link FROM text_tag WHERE tag_link='$link'");
            if($db->num_rows>0) $db->update("text_tag", array("count"=>"count+1"), "tag_link='$link'");
            else $db->insert("text_tag", array("tag"=>$tag, "tag_link"=>$link, "count"=>1));
          }
        }
      }
      
      if($row) $slanje = "<p class='ok'>Tekst <b>$form[title]</b> je uspješno ".($edit ? "izmjenjen" : "dodan u bazu")."! <span><a href='$p[1]/' class='view'>Pregled tekstova &raquo;</a></span></p>";
      else $slanje = "<p class='error'>Došlo je do greške prilikom obrade podataka. Pokušajte ponovno!</p>";
    }
  }
  
  $titleImage = ($form['titleImage'] ? $form['titleImage'] : "Facebook");
  
  $content["text"]="
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; <a href='$p[1]/'>Tekstovi</a> &gt; $var[title]</small></p>
      <h4>Tekstovi - $var[title]</h4>
      ".($greska ? "<p><span><a href='$p[1]/' class='view'>Pregled tekstova &raquo;</a></span></p>
      <form method='post' action='$p[1]/$p[2]/".($edit ? "$p[3]/" : "")."' enctype='multipart/form-data'>" :"" )."
        $slanje";
      if($greska){
        $content["text"].=($edit ? "<p><label for='id'>* ID:</label><input name='id' id='id' type='text' value='$p[3]' class='text' style='width:50px;color:#999;background-color:#f5f5f5' disabled='disabled' /></p>" : "")."
        <p><label for='title'>* Naslov:</label><input name='title' id='title' type='text' value='$form[title]' maxlength='85' class='text' style='width:300px' /></p>
        <p><label for='subtitle'>Podnaslov:</label><input name='subtitle' id='subtitle' type='text' value='$form[subtitle]' maxlength='150' class='text' style='width:835px' /></p>
        <p><label for='text'>* Tekst:</label><textarea name='text' id='text' cols='104' rows='24' style='width:850px;height:600px'>".(!$edit && !$_POST['submit_ok'] ? "" : $form['text'])."</textarea></p>
        <p><label for='categ'>* Kategorija:</label><select name='categ' id='categ' class='text_select' style='width:314px'>
          <option value='1'".($form['categ'] == 1 ? " selected='selected'" : "").">Novosti</option>
          <option value='2'".($form['categ'] == 2 ? " selected='selected'" : "").">Tutorijali</option>
          <option value='3'".($form['categ'] == 3 ? " selected='selected'" : "").">Igre</option>
          <option value='4'".($form['categ'] == 4 ? " selected='selected'" : "").">Aplikacije</option>
          <option value='5'".($form['categ'] == 5 ? " selected='selected'" : "").">Zanimljivosti</option>
        </select></p>
        <p><label for='tags'>Tagovi:</label><input name='tags' id='tags' type='text' value='$form[tags]' maxlength='200' class='text' style='width:500px' /></p>
        <p><label for='source'>Izvor:</label><input name='source' id='source' type='text' value='$form[source]' maxlength='150' class='text' style='width:500px' /></p>
        <p><label for='file'>Upload slike:</label><input name='file' id='file' type='file' value='' maxlength='200' class='text' size='68' /></p>
        ".($edit ? "<p><label>Trenutna slika:</label><img src='../".getImage($form['ID_TEXT'], removeSpecial($titleImage), 1, "../")."' alt='' title='' width='400' height='240' class='image' /></p>" : "")."
        <p><label for='titleImage'>Naziv slike:</label><input name='titleImage' id='titleImage' type='text' value='$form[titleImage]' maxlength='60' class='text' style='width:300px' /></p>
        <p><label for='sourceImage'>Autor slike:</label><input name='sourceImage' id='sourceImage' type='text' value='$form[sourceImage]' maxlength='120' class='text' style='width:500px' /></p>
        ".($edit ? "<p><label for='longurl'>* URL za Like dugme:</label><input name='longurl' id='longurl' type='text' value='$form[longurl]' maxlength='150' class='text' style='width:500px' /></p>" : "")."
        ".($user['is_admin'] ? "<p><label for='special'>Izdvojeno:</label><input name='special' id='special' type='checkbox' value='1'".($form['special'] ? " checked='checked'" : "")." class='checkbox' /></p>" : "")."
        ".($user['is_admin'] ? "<p><label for='published'>Aktivno (objavljeno):</label><input name='published' id='published' type='checkbox' value='1'".($form['published'] ? " checked='checked'" : "")." class='checkbox' /></p>" : "")."
        ".($edit ? "<p><label for='author'>* Autor:</label><input name='author' id='author' type='text' value='$form[author]' maxlength='20' class='text' style='width:200px;color:#999;background-color:#f5f5f5' disabled='disabled' /></p>" : "")."
        ".($edit ? "<p><label for='time'>* Datum:</label><input name='time' id='time' type='text' value='".date("d.m.Y. H:i", $form['time'])."' maxlength='30' class='text' style='width:200px;color:#999;background-color:#f5f5f5' disabled='disabled' /></p>" : "")."
        <p class='button_align'><input name='protect' id='protect' type='hidden' value='$domain[protect]' />
        <input type='submit' name='submit_ok' id='submit_ok' value='$var[button]' class='button' /></p>
        <p class='button_align'><b>polja označena zvjezdicom (*) su obavezna</b></p>
      </form>
      <p><span><a href='$p[1]/' class='view'>Pregled tekstova &raquo;</a></span></p>";
    }
}
?>

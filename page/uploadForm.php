<?php
function loadUploadForm($edit = 0){
  global $db, $p, $content, $domain, $cms, $user;

  if($edit){
    $var['title'] = "Izmjeni (ID: $p[3])";
    $var['button'] = "Izmjeni";
    
    $form = $db->fetch_first("SELECT upload.ID_UP, upload.name, upload.ID_TEXT, users.username FROM upload LEFT JOIN users ON upload.ID_USER=users.ID_USER WHERE upload.ID_UP='$p[3]'");
    $form['author'] = $form['username'];
  }
  else{
    $var['title'] = "Dodaj";
    $var['button'] = "Dodaj";
  }
  
  $texts=$db->fetch_all("SELECT ID_TEXT, title FROM text ORDER BY ID_TEXT desc");
  
  $content["title"]= $var['title']." - Upload slike";
  
  $slanje = "";
  $greska = 1;
  
  if($_POST['submit_ok']){
    $form['name'] = cleanup(removeSpace($_POST['upload_name']));
    $form['ID_TEXT'] = $_POST['ID_TEXT'];

    $form['protect'] = cleanup($_POST['protect']);

    $greska = 0;
    
    if($greska == 0 && (strlen($form['name']) < 3 || strlen($form['name']) > 70)){
      $slanje = "<p class='error'>Naziv slike nije ispravno napisan! Mora biti između 3 i 70 znakova.</p>";
      $greska = 1;
    }
    if($greska == 0 && (!isset($_FILES["file"]) || !is_array($_FILES["file"]))){
      $slanje = "<p class='error'>Slika nije ispravna! Pokušajte ponovno.</p>";
      $greska = 1;
    }
    if($greska == 0 && ($_FILES["file"]["name"] || !$edit)){
      if(!$_FILES['file']['name'] || !$_FILES['file']['type']){
        $slanje = "<p class='error'>Morate izabrati dokument koji želite uploadati!</p>";
        $greska = 1;
      }
      if($greska == 0 && !in_array($_FILES['file']['type'], $content['upload']['allow'])){
        $slanje = "<p class='error'>Format slike nije podržan! Podržani su slijedeći formati: slike (jpg, gif, png).</p>";
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
      $break = 0;
      
      if($edit){
        $row=$db->update("upload", array("name"=>$form['name'], "ID_TEXT"=>$form['ID_TEXT']), "ID_UP='$p[3]'");
        if($_FILES["file"]["name"]){
          unlink("../".$content['upload']['folder'][1].$form['ID_UP'].$content['upload']['format'][1]);

          resizePic($_FILES["file"], $content['upload']['image_size'][7], $form['ID_UP'].$content['upload']['format'][1], "../".$content['upload']['folder'][1]);
          if($form['ID_TEXT']){
            if(is_file("../".$content['upload']['folder'][2].$form['ID_UP'].$content['upload']['format'][1])) unlink("../".$content['upload']['folder'][2].$form['ID_UP'].$content['upload']['format'][1]);
            resizePicCrop($_FILES["file"], "../".$content['upload']['folder'][2].$form['ID_UP'].$content['upload']['format'][1], $content['upload']['image_size'][8], $content['upload']['image_size'][9]);
          }
        }
        $greska = 1;
      }
      else{
        $row=$db->insert("upload", array("name"=>$form['name'], "ID_TEXT"=>$form['ID_TEXT'], "ID_USER"=>$user['id'], "time"=>time()));
        $form['ID_UP']=$row;

        resizePic($_FILES["file"], $content['upload']['image_size'][7], $form['ID_UP'].$content['upload']['format'][1], "../".$content['upload']['folder'][1]);
        if($form['ID_TEXT']){
          resizePicCrop($_FILES["file"], "../".$content['upload']['folder'][2].$form['ID_UP'].$content['upload']['format'][1], $content['upload']['image_size'][8], $content['upload']['image_size'][9]);
        }
      }
      
      if($row) $slanje = "<p class='ok'>Slika <b>$form[name]</b> je uspješno ".($edit ? "izmjenjena" : "dodana")."! <span><a href='$p[1]/' class='picture'>Pregled slika &raquo;</a></span></p>";
      else $slanje = "<p class='error'>Došlo je do greške prilikom obrade podataka. Pokušajte ponovno!</p>";
    }
  }
  $content["text"]="
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; <a href='$p[1]/'>Upload slika</a> &gt; $var[title]</small></p>
      <h4>Upload slika - $var[title]</h4>
      ".($greska ? "<p><span><a href='$p[1]/' class='picture'>Pregled slika &raquo;</a></span></p>
      <form method='post' action='$p[1]/$p[2]/".($edit ? "$p[3]/" : "")."' enctype='multipart/form-data'>" :"" )."
        $slanje";
      if($greska){
        $content["text"].=($edit ? "<p><label for='id'>* ID:</label><input name='id' id='id' type='text' value='$p[3]' class='text' style='width:50px;color:#999;background-color:#f5f5f5' disabled='disabled' /></p>" : "")."
        <p><label for='upload_name'>* Naziv slike:</label><input name='upload_name' id='upload_name' type='text' value='$form[name]' maxlength='70' class='text' style='width:400px' /></p>
        <p><label for='ID_TEXT'>Veza tekst:</label><select name='ID_TEXT' id='ID_TEXT' class='text_select' style='width:314px'>
          <option value='0'".($form['ID_TEXT'] == 0 ? " selected='selected'" : "").">-</option>";
          foreach($texts as $row2){
             $content["text"].="<option value='$row2[ID_TEXT]'".($form['ID_TEXT'] == $row2['ID_TEXT'] ? " selected='selected'" : "").">[$row2[ID_TEXT]] $row2[title]</option>";
          }
        $content["text"].="</select></p>
        <p><label for='file'>".($edit ? "" : "* ")."Upload slike:</label><input name='file' id='file' type='file' value='' maxlength='200' class='text' size='68' /></p>
        ".($edit ? "<p><label>Trenutna slika:</label><img src='../upload/images/".removeSpecial($form['name'])."-$p[3].jpg' alt='' title='' width='' height='' class='image' /></p>" : "")."
        ".($edit ? "<p><label for='author'>* Autor:</label><input name='author' id='author' type='text' value='$form[author]' maxlength='20' class='text' style='width:200px;color:#999;background-color:#f5f5f5' disabled='disabled' /></p>" : "")."
        <p class='button_align'><input name='protect' id='protect' type='hidden' value='$domain[protect]' />
        <input type='submit' name='submit_ok' id='submit_ok' value='$var[button]' class='button' /></p>
        <p class='button_align'><b>polja označena zvjezdicom (*) su obavezna</b></p>
      </form>
      <p><span><a href='$p[1]/' class='picture'>Pregled slika &raquo;</a></span></p>
      <p class='help'><b>Pomoć:</b> Podržani su slijedeći formati: slike (jpg, gif, png).</p>";
    }
}
?>

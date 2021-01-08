<?php
function loadPollForm(){
  global $db, $p, $content, $domain, $cms, $user;
  
  $var['title'] = "Dodaj";
  $var['button'] = "Dodaj";
  
  $content["title"] = $var['title']." - Ankete";
  
  $slanje = "";
  $greska = 1;

  if($_POST['submit_ok']){
    $form['question'] = removeSpace($_POST['question']);
    for($i = 1; $i <= 6; $i++){
      $form["answer_$i"] = removeSpace($_POST["answer_$i"]);
    }
    
    $form['protect'] = cleanup($_POST['protect']);

    $greska = 0;
    
    if($greska == 0 && (strlen($form['question']) < 15 || strlen($form['question']) > 200)){
      $slanje = "<p class='error'>Pitanje nije ispravno napisano! Mora biti između 15 i 200 znakova.</p>";
      $greska = 1;
    }
    if($greska == 0){
      $row = $db->fetch_first("SELECT ID_POLL from poll WHERE question='$form[question]'");
      if($db->num_rows > 0){
        $slanje = "<p class='error'>Anketa <b>$form[question]</b> već postoji u bazi!</p>";
        $greska = 1;
      }
    }
    if($greska == 0 && (strlen($form['answer_1']) < 2 || strlen($form['answer_1']) > 80)){
      $slanje = "<p class='error'>Prvi odgovor nije ispravno napisan! Mora biti između 2 i 80 znakova.</p>";
      $greska = 1;
    }
    if($greska == 0 && (strlen($form['answer_2']) < 2 || strlen($form['answer_2']) > 80)){
      $slanje = "<p class='error'>Drugi odgovor nije ispravno napisan! Mora biti između 2 i 80 znakova.</p>";
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
      $row=$db->insert("poll", array("question"=>$form['question'], "ID_USER"=>$user['id'], "time"=>time()));
      $form['ID_POLL'] = mysql_insert_id();
      $form['answers'] = 0;
      for($i = 1; $i <= 6; $i++){
        if(strlen($form["answer_$i"]) > 1 && strlen($form["answer_$i"]) < 81){
          $db->insert("poll_answers", array("answer"=>$form["answer_$i"], "ID_POLL"=>$form['ID_POLL']));
          $form['answers']++;
        }
      }
      $db->update("poll", array("answers"=>$form['answers']), "ID_POLL='$form[ID_POLL]'");
      
      if($row) $slanje = "<p class='ok'>Anketa <b>$form[question]</b> je uspješno dodana u bazu! <span><a href='$p[1]/' class='poll'>Pregled anketa &raquo;</a></span></p>";
      else $slanje = "<p class='error'>Došlo je do greške prilikom obrade podataka. Pokušajte ponovno!</p>";
    }
  }
  
  $content["text"]="
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; <a href='$p[1]/'>Ankete</a> &gt; $var[title]</small></p>
      <h4>Ankete - $var[title]</h4>
      ".($greska ? "<p><span><a href='$p[1]/' class='poll'>Pregled anketa &raquo;</a></span></p>
      <form method='post' action='$p[1]/$p[2]/'>" : "")."
        $slanje";
      if($greska){        
        $content["text"].="<p><label for='question'>* Pitanje:</label><input name='question' id='question' type='text' value='$form[question]' maxlength='200' class='text' style='width:500px' /></p>";
        
        for($i = 1; $i <= 6; $i++){
        $content["text"].="<p><label for='answer_$i'>".($i < 3 ? "* " : "")."Odgovor $i:</label><input name='answer_$i' id='answer_$i' type='text' value='".$form["answer_$i"]."' maxlength='80' class='text' style='width:300px' /></p>
        ";
        }
        
        $content["text"].="<p class='button_align'><input name='protect' id='protect' type='hidden' value='$domain[protect]' />
        <input type='submit' name='submit_ok' id='submit_ok' value='$var[button]' class='button' /></p>
        <p class='button_align'><b>polja označena zvjezdicom (*) su obavezna</b></p>
      </form>
      <p><span><a href='$p[1]/' class='poll'>Pregled anketa &raquo;</a></span></p>";
    }
}
?>

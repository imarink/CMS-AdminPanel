<?php
function loadUpload($page, $results){
  global $db, $p, $content, $domain, $cms, $user, $upload;

  if($user['is_admin']) $akcije = 1;
  else{
    $array=$db->fetch_all("SELECT ID_USER, time FROM upload ORDER BY time DESC LIMIT $results[start], $results[limit]");
    foreach($array as $var)
      if($var["time"] > $cms['mod_time'] && $var['ID_USER'] == $user['id'] && !$akcije) $akcije = 1;
  }
 
  $content["title"]= "Upload slika";

  if($akcije)
    $content["js"]="
<script type='text/javascript'> 
  $().ready(function(){
    $('a.delete').click(function(){
      if(confirm('Jeste li sigurni da želite izbrisati sliku \"'+$('#naziv_'+$(this).attr('rel')).html()+'\"? Akcija je nepovratna!')){
        $.ajax({
          type: 'POST',
          url: 'ajax.php?action=uploadDelete',
          data: 'id='+$(this).attr('rel'),
          success:function(r){
            window.location = '$domain[url]$p[1]/';
          }
        })
      }
      return false;
    });
  });
</script>";

  $content["text"]="
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; Upload slika</small></p>
      <h4>Upload slika - Pregled svih</h4>
      <p class='float_r'><small>Stranica <b>$page[current]</b> od <b>$page[all]</b>. Rezultati <b>$results[start2]-$results[do]</b> od <b>$results[all]</b>.</small></p>
      <p><span><a href='$p[1]/add/' class='add'>Dodaj sliku</a></span></p>
      <table>
        <tr>
          <th class='align_c' style='width:25px'>ID</th><th style='width:".($akcije ? "280" : "370")."px'>Naziv</th><th>Slika</th><th class='align_c' style='width:70px'>Datum</th>".($akcije ? "<th style='width:130px'>Akcije</th>" : "")."
        </tr>";
      $j = 0;
      $data=$db->fetch_all("SELECT * FROM upload ORDER BY time DESC LIMIT $results[start], $results[limit]");
      foreach($data as $row){
      $content["text"].="
        <tr".($j % 2 == 1 ? " class='dark'" : "").">
          <td class='align_c'>$row[ID_UP]</td><td><em id='naziv_$row[ID_UP]'>$row[name]</em></td><td><span><a href='../".$content['upload']['folder'][1].removeSpecial($row['name'])."-$row[ID_UP]".$content['upload']['format'][1]."' class='link' target='_blank'>".removeSpecial($row['name'])."-$row[ID_UP]".$content['upload']['format'][1]."</a></span></td><td class='align_c'>".date("d.m.Y.", $row['time'])."</td>".($akcije ? "<td>".($user['is_admin'] || ($row["time"] > $cms['mod_time'] && $row['ID_USER'] == $user['id']) ? "<span><a href='$p[1]/edit/$row[ID_UP]/' class='edit'>Izmjeni</a> <a href='#' class='delete' rel='$row[ID_UP]'>Obriši</a></span>" : "")."</td>" : "")."
        </tr>";
        $j++;
      }
  $content["text"].="
      </table>";
  if($page['all'] > 1){
    $content["text"].="
      <p class='float_r'><small>Stranica:";
    for($i = 1; $i <= $page['all']; $i++){
      if($page['current'] == $i) $content["text"].=" <em class='page_active'>$i</em>";
      else $content["text"].=" <a href='$p[1]/".($i > 1 ? "$i/" : "")."' class='page'>$i</a>";
    }
    $content["text"].="</small></p>";
  }
      $content["text"].= "<p><span><a href='$p[1]/add/' class='add'>Dodaj sliku</a></span></p>
      <p class='help'><b>Pomoć:</b> Podržani su slijedeći formati: slike (jpg, gif, png).</p>";
}
?>

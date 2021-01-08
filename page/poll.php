<?php
function loadPoll($page, $results){
  global $db, $p, $content, $domain, $cms, $user;
  
  if($user['is_admin']) $akcije = 1;
  else $akcije = 0;

  $content["title"]= "Ankete";
  
  if($akcije)
  $content["js"]="
<script type='text/javascript'>
  $().ready(function(){
    $('a.delete').click(function(){
      if(confirm('Jeste li sigurni da želite izbrisati anketu \"'+$('#naziv_'+$(this).attr('rel')).html()+'\"? Akcija je nepovratna!')){
        $.ajax({
          type: 'POST',
          url: 'ajax.php?action=pollDelete',
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
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; Ankete</small></p>
      <h4>Ankete - Pregled svih</h4>
      <p class='float_r'><small>Stranica <b>$page[current]</b> od <b>$page[all]</b>. Rezultati <b>$results[start2]-$results[do]</b> od <b>$results[all]</b>.</small></p>
      ".($akcije ? "<p><span><a href='$p[1]/add/' class='add'>Dodaj anketu</a></span></p>" : "")."
      <table>
        <tr>
          <th class='align_c' style='width:25px'>ID</th><th>Pitanje</th><th class='align_c' style='width:70px'>Odgovora</th><th class='align_c' style='width:60px'>Glasova</th><th class='align_c' style='width:70px'>Datum</th>".($akcije ? "<th class='align_c' style='width:60px'>Akcije</th>" : "")."
        </tr>";
      $j = 0;
      $data=$db->fetch_all("SELECT * FROM poll ORDER BY time DESC LIMIT $results[start], $results[limit]");
      foreach($data as $row){
        $content["text"].="
        <tr".($j % 2 == 1 ? " class='dark'" : "").">
          <td class='align_c'>".$row['ID_POLL']."</td><td id='naziv_$row[ID_POLL]'>$row[question]</td><td class='align_c'>$row[answers]</td><td class='align_c'>$row[votes]</td><td class='align_c'>".date("d.m.Y.", $row['time'])."</td>".($akcije ? "<td><span><a href='#' class='delete' rel='$row[ID_POLL]'>Obriši</a></span></td>" : "")."
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
  if($akcije)
  $content["text"].="
      <p><span><a href='$p[1]/add/' class='add'>Dodaj anketu</a></span></p>";
}
?>

<?php
function loadLinks($page, $results){
  global $db, $p, $content, $domain, $cms, $user, $upload;

  if($user['is_admin']) $akcije = 1;
 
  $content["title"]= "Linkovi";

  if($akcije)
    $content["js"]="
<script type='text/javascript'> 
  $().ready(function(){
    $('a.delete').click(function(){
      if(confirm('Jeste li sigurni da želite izbrisati link \"'+$('#naziv_'+$(this).attr('rel')).html()+'\"? Akcija je nepovratna!')){
        $.ajax({
          type: 'POST',
          url: 'ajax.php?action=linksDelete',
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
      <p><small><a href=''>$domain[name] $cms[name]</a> &gt; Linkovi</small></p>
      <h4>Linkovi - Pregled svih</h4>
      <p class='float_r'><small>Stranica <b>$page[current]</b> od <b>$page[all]</b>. Rezultati <b>$results[start2]-$results[do]</b> od <b>$results[all]</b>.</small></p>
      ".($akcije ? "<p><span><a href='$p[1]/add/' class='add'>Dodaj link</a></span></p>" : "")."
      <table>
        <tr>
          <th class='align_c' style='width:25px'>ID</th><th style='width:".($akcije ? "350" : "410")."px'>Naziv</th><th>Link</th><th class='align_c' style='width:60px'>Klikova</th>".($akcije ? "<th style='width:130px'>Akcije</th>" : "")."
        </tr>";
      $j = 0;
      $data=$db->fetch_all("SELECT * FROM links LIMIT $results[start], $results[limit]");
      foreach($data as $row){
      $content["text"].="
        <tr".($j % 2 == 1 ? " class='dark'" : "").">
          <td class='align_c'>$row[ID]</td><td><em id='naziv_$row[ID]'>$row[name]</em></td><td><span><a href='$row[url]' class='link' target='_blank'>".getHost($row['url'])."</a></span></td><td class='align_c'>$row[clicks]</td>".($akcije ? "<td><span><a href='$p[1]/edit/$row[ID]/' class='edit'>Izmjeni</a> <a href='#' class='delete' rel='$row[ID]'>Obriši</a></span></td>" : "")."
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
  $content["text"].="<p><span><a href='$p[1]/add/' class='add'>Dodaj link</a></span></p>";
}
?>

<?php

// reže sliku na fiksnu veličinu
function resizePicCrop($file, $location, $w, $h){
  global $path;
  if(false !== (list($ws,$hs) = @getimagesize($file["tmp_name"]))){
    $r=$w/$h;
    if($ws<$hs*$r){
      $dimx=$ws;
      $dimy=$ws/$r;
      $x=0;
      $y=($hs-$dimy)/2;
    }elseif($ws>$hs*$r){
      $dimx=$hs*$r;
      $dimy=$hs;
      $x=($ws-$dimx)/2;
      $y=0;
    }else{
      $dimx=$hs*$r;
      $dimy=$hs;
      $x=0;
      $y=0;
    }
    $thumb = imagecreatetruecolor($w,$h);
    $array=explode(".", $file["name"]);
    $n=count($array)-1;
    if($array[$n]=='jpg' || $array[$n]=='jpeg' || $array[$n]=='JPG')
      $source = imagecreatefromjpeg($file["tmp_name"]);
    elseif($array[$n]=='png')
      $source = imagecreatefrompng($file["tmp_name"]);
    elseif($array[$n]=='gif')
      $source = imagecreatefromgif($file["tmp_name"]);

    imagecopyresampled($thumb,$source,0,0,$x,$y,$w,$h,$dimx,$dimy);

    imagejpeg($thumb, "$location", 80);
    imagedestroy($thumb);
  }else return false;
}


// smanjuje sliku na određeno maksimalnu veličinu
function resizePic($file, $w, $name, $folder){
  if(false !== (list($ws,$hs) = @getimagesize($file["tmp_name"]))){
    if($w > $ws) $w = $ws;
    $h=intval($w/$ws*$hs);
    $thumb = imagecreatetruecolor($w,$h);
    $array=explode(".", $file["name"]);
    $n=count($array)-1;
    if($array[$n]=='jpg' || $array[$n]=='jpeg' || $array[$n]=='JPG')
      $source = imagecreatefromjpeg($file["tmp_name"]);
    elseif($array[$n]=='png')
      $source = imagecreatefrompng($file["tmp_name"]);
    elseif($array[$n]=='gif')
      $source = imagecreatefromgif($file["tmp_name"]);
    imagecopyresampled($thumb,$source,0,0,0,0,$w,$h,$ws,$hs);
    imagedestroy($source);
    imagejpeg($thumb, "$folder{$name}", 80);
    imagedestroy($thumb);
  }
}

?>

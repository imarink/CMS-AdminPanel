<?php
// cleanup
setcookie("admin", "", 1, "/");
unset($user);
  
header("Location: $domain[url]login/");
?>

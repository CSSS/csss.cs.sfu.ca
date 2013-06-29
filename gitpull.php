<?php 
@include('includes/sessions.inc.php');
writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], 'Pulled from git');
header('Content-Type: application/json');
echo '{"status_msg":"';
echo rtrim(`git pull`);
echo '<br />';
echo rtrim(`git submodule update`);
echo '", "sha":"';
echo rtrim(`git rev-parse HEAD`);
echo '"}';
?>

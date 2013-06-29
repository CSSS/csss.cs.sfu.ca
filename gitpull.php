<?php 
@include('includes/sessions.inc.php');
writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], 'Pulled from git');
header('Content-Type: application/json');
$rev = `git rev-parse HEAD`;
echo '{"status_msg":"';
echo rtrim(`git pull`);
echo '<br />';
echo rtrim(`git submodule update`);
echo '", "sha":"';
$newrev = `git rev-parse HEAD`;
echo rtrim($newrev);
echo '"}';
if ($newrev != $rev) {
	writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], 'Git now at ' . `git rev-parse --short HEAD`);
}
?>

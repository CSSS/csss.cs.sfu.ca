<?php 
@include('includes/sessions.inc.php');
writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], 'Pulled from git');
header('Content-Type: application/json');
$rev = `git rev-parse HEAD`;
$output = '{"status_msg":"';
$output .= (`git pull`);
$output .= (`git submodule update`);
$output .= '", "sha":"';
$newrev = `git rev-parse HEAD`;
$output .= ($newrev);
$output .= '"}';
if ($newrev != $rev) {
	writeToLog($dbSession, $SESSION['username'], $SESSION['ip'], 'Git now at ' . `git rev-parse --short HEAD`);
}
$output = str_replace('\n', '<br />', $output);
echo $output;
?>

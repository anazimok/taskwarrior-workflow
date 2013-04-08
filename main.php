<?php
require_once("workflows.php");

$CMD_PATH="/usr/local/bin/task";

$alias = array(
    "ls" => "pending",
    "del" => "deleted",
    "wait" => "waiting",
    "comp" => "completed",
    "recur" => "recurring"
);

$q = trim($argv[1]);
$wf = new Workflows();

$output = "[" . shell_exec($CMD_PATH . " export status:".$alias[$q]) . "]";

$json = json_decode($output);

foreach($json as $task) {

    $wf->result(
        $task->id,
        $task->uuid,
        $task->description,
        buildSubtitle($task),
        'icon.png'
    );
}

echo $wf->toxml();


function buildSubtitle($task) {
    $start = date_parse($task->entry);
    $end = date_parse($task->end);

    $startDate = sprintf("Entered On: %s/%s/%s",
        $start['month'],
        $start['day'],
        $start['year']);

    $endDate = sprintf("     Completed On: %s/%s/%s",
        $end['month'],
        $end['day'],
        $end['year']);

    if ($end['year'] == NULL) {
        return $startDate . "    Press Cmd + Enter to complete the task";
    } else {
        return $startDate . $endDate;
    }
}

?>
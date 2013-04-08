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
    $startDate = buildDate("Entered On:", date_parse($task->entry));
    $endDate = buildDate("     Completed On:", date_parse($task->end));
    $dueDate = buildDate("     Due On:", date_parse($task->due));

    if (strlen($endDate) == 0) {
        return $startDate . $dueDate . "     Press Cmd + Enter to complete";
    } else {
        return $startDate . $endDate;
    }
}

function buildDate($prefixText, $date) {
    if ($date['year'] != NULL) {
        return sprintf("%s %s/%s/%s", $prefixText, $date['month'], $date['day'], $date['year']);
    } else {
        return "";
    }
}

?>
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

if(substr($argv[1], 0, 4) == "add ") {
    shell_exec($CMD_PATH . " " . $argv[1]);
} elseif (substr($argv[1], 0, 5) == "exec ") {
    shell_exec($CMD_PATH . " " . substr($argv[1], 5, strlen($argv[1])));
} else {
    $wf = new Workflows();

    $output = shell_exec($CMD_PATH . " export status:".$alias[$q]);

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
}

function buildSubtitle($task) {
    $dueDate = buildDate("Due On:", date_parse($task->due));
    return $dueDate;
}

function buildDate($prefixText, $date) {
    if ($date['year'] != NULL) {
        return sprintf("%s %s/%s/%s", $prefixText, $date['month'], $date['day'], $date['year']);
    } else {
        return "";
    }
}


?>

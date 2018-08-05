<?php
require_once("workflows.php");

$CMD_PATH="/usr/local/bin/task";

$alias = array(
    "l" => "pending",
    "d" => "deleted",
    "w" => "waiting",
    "c" => "completed",
    "r" => "recurring"
);

$q = trim($argv[1]);

if(substr($argv[1], 0, 4) == "add ") {
    shell_exec($CMD_PATH . " " . $argv[1]);
} elseif (substr($argv[1], 0, 5) == "exec ") {
    shell_exec($CMD_PATH . " " . substr($argv[1], 5, strlen($argv[1])));
} else {
    $wf = new Workflows();

    if(strpos($q, "due:")) {
        $output = shell_exec($CMD_PATH . " export status:" . $alias[substr($q, 0, 1)] . " " . substr($q, strpos($q, "due:")));
    } else {
        $output = shell_exec($CMD_PATH . " export status:" . $alias[substr($q, 0, 1)]);
    }

    $json = json_decode($output);

    if(empty($json)) {
        warning_handler();
    } else {
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
}

function warning_handler() {
    echo "<?xml version=\"1.0\"?><items><item uid=\"1\" valid=\"no\" autocomplete=\"\"><title>NO DATA FOUND</title><icon>icon.png</icon></item></items>";
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

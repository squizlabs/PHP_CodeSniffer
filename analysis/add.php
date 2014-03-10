<?php
require_once __DIR__.'/_assets/common.php';
$repos       = json_decode(file_get_contents(__DIR__.'/_assets/repos.json'));
$filterRepos = array();
$sniffs      = array();
foreach ($_SERVER['argv'] as $arg) {
    if (substr($arg, 0, 8) === '--repos=') {
        $filterRepos = explode(',', substr($arg, 8));
    } else if (substr($arg, 0, 9) === '--sniffs=') {
        $sniffs = explode(',', substr($arg, 9));
    }
}

if (empty($filterRepos) === true) {
    $repoCount = count($repos);
} else {
    $repoCount = count($filterRepos);
}

$totalFilename = __DIR__.'/results.json';
$totals        = json_decode(file_get_contents($totalFilename), true);

// Determine the dates that we need to regen for.
$dates = array();
foreach ($totals as $metric => $data) {
    foreach ($data['trends'] as $date => $values) {
        $dates[] = $date;
    }
}

$dates     = array_unique($dates);
$dateCount = count($dates);

$repoNum = 0;
foreach ($repos as $repo) {
    if (empty($filterRepos) === false && in_array($repo->url, $filterRepos) === false) {
        continue;
    }

    $repoNum++;

    $dateNum = 0;
    foreach ($dates as $date) {
        $dateNum++;

        echo 'Processing '.$repo->name." ($repoNum / $repoCount) $date ($dateNum / $dateCount)".PHP_EOL;
        $resultFile = processRepo($repo, $date, true, true, $sniffs);
        $results    = json_decode(file_get_contents($resultFile), true);
        echo "\t=> Updating metric totals".PHP_EOL;
        foreach ($results['metrics'] as $metric => $data) {
            $results['metrics'][$metric]['trends'][$date] = $data['values'];
            foreach ($data['values'] as $value => $count) {
                echo "\t\t* change total $metric ($value) from ".$totals[$metric]['trends'][$date][$value];
                $totals[$metric]['trends'][$date][$value] += $count;
                echo ' to '.$totals[$metric]['trends'][$date][$value].PHP_EOL;
            }
        }

        file_put_contents($resultFile, json_encode($results, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT));

    }//end foreach

    echo PHP_EOL;

}//end foreach

file_put_contents($totalFilename, json_encode($totals, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT));

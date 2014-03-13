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

$repoNum = 0;
foreach ($repos as $repo) {
    if (empty($filterRepos) === false && in_array($repo->url, $filterRepos) === false) {
        continue;
    }

    $repoNum++;

    $dirs       = getRepoDirs($repo);
    $resultFile = $dirs['repo'].'/results.json';
    $tempFile   = $dirs['repo'].'/results.tmp';
    $results    = json_decode(file_get_contents($resultFile), true);

    // Determine the dates that we need to regen for.
    $dates = array();
    foreach ($results['metrics'] as $metric => $data) {
        foreach ($data['trends'] as $date => $values) {
            $dates[] = $date;
        }
    }

    $dates     = array_unique($dates);
    $dateCount = count($dates);
    $dateNum   = 0;
    foreach ($dates as $date) {
        $dateNum++;

        echo 'Processing '.$repo->name." ($repoNum / $repoCount) $date ($dateNum / $dateCount)".PHP_EOL;
        processRepo($repo, $date, true, true, $sniffs, $tempFile);
        $newResults = json_decode(file_get_contents($tempFile), true);
        echo "\t=> Comparing updated metric values".PHP_EOL;
        foreach ($newResults['metrics'] as $metric => $data) {
            if (isset($results['metrics'][$metric]) === false) {
                echo "\t\t* new metric detected; setting initial repo values *".PHP_EOL;
                $results['metrics'][$metric]           = $data;
                $results['metrics'][$metric]['trends'] = array();
                $results['metrics'][$metric]['trends'][$date] = array();
            } else if (isset($results['metrics'][$metric]['trends'][$date]) === false) {
                echo "\t\t* new trend date detected; setting initial repo values *".PHP_EOL;
                $results['metrics'][$metric]['trends'][$date] = array();
            }

            if (isset($totals[$metric]) === false) {
                echo "\t\t* new metric detected; setting initial total values *".PHP_EOL;
                $totals[$metric]           = $data;
                $totals[$metric]['trends'] = array();
                $totals[$metric]['trends'][$date] = array();
            } else if (isset($totals[$metric]['trends'][$date]) === false) {
                echo "\t\t* new trend date detected; setting initial total values *".PHP_EOL;
                $totals[$metric]['trends'][$date] = array();
            }

            $old = $results['metrics'][$metric]['trends'][$date];
            $new = $data['values'];
            foreach ($new as $value => $count) {
                if (isset($old[$value]) === true) {
                    if ($old[$value] === $count) {
                        continue;
                    }

                    echo "\t\t* change $metric ($value) from ".$old[$value]." to $count".PHP_EOL;
                } else {
                    echo "\t\t* set $metric ($value) to $count".PHP_EOL;
                    $old[$value] = 0;
                }

                $results['metrics'][$metric]['trends'][$date][$value] = $count;

                if (isset($totals[$metric]['trends'][$date][$value]) === true) {
                    echo "\t\t* change total $metric ($value) from ".$totals[$metric]['trends'][$date][$value];
                    $totals[$metric]['trends'][$date][$value] += ($count - $old[$value]);
                    echo ' to '.$totals[$metric]['trends'][$date][$value].' *'.PHP_EOL;
                } else {
                    $totals[$metric]['trends'][$date][$value] = $count;
                    echo "\t\t* added total $metric ($value) with count ".$totals[$metric]['trends'][$date][$value].' *'.PHP_EOL;
                }
            }//end foreach
        }//end foreach
    }//end foreach

    file_put_contents($resultFile, json_encode($results, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT));
    echo PHP_EOL;

}//end foreach

file_put_contents($totalFilename, json_encode($totals, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT));

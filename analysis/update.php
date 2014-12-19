<?php
require_once __DIR__.'/_assets/common.php';
$repos        = json_decode(file_get_contents(__DIR__.'/_assets/repos.json'));
$filterRepos  = array();
$sniffs       = array();
$checkoutDate = null;
foreach ($_SERVER['argv'] as $arg) {
    if (substr($arg, 0, 7) === '--date=') {
        $checkoutDate = substr($arg, 7);
    } else if (substr($arg, 0, 8) === '--repos=') {
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

$progressFile = __DIR__.'/progress.json';
if (is_file($progressFile) === true) {
    $progress = json_decode(file_get_contents($progressFile), true);
} else {
    $progress = array();
}

$repoNum = 0;
foreach ($repos as $repo) {
    if (empty($filterRepos) === false && in_array($repo->url, $filterRepos) === false) {
        continue;
    }

    if (isset($progress[$repo->url]) === false) {
        $progress[$repo->url] = array();
    }

    $repoNum++;

    $dirs       = getRepoDirs($repo);
    $resultFile = $dirs['repo'].'/results.json';
    $tempFile   = $dirs['repo'].'/results.tmp';
    $results    = json_decode(file_get_contents($resultFile), true);

    // Determine the dates that we need to regen for.
    if ($checkoutDate === null) {
        $dates = array();
        foreach ($results['metrics'] as $metric => $data) {
            foreach ($data['trends'] as $date => $values) {
                $dates[] = $date;
            }
        }

        $dates = array_unique($dates);
    } else {
        $dates = array($checkoutDate);
    }

    $dateCount = count($dates);
    $dateNum   = 0;
    foreach ($dates as $date) {
        $dateNum++;

        if (isset($progress[$repo->url][$date]) === true) {
            echo 'Already processed '.$repo->name." ($repoNum / $repoCount) $date ($dateNum / $dateCount)".PHP_EOL;
            continue;
        }

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
            }//end foreach

            foreach ($old as $value => $count) {
                if (isset($new[$value]) === true) {
                    continue;
                }

                echo "\t\t* remove $metric ($value); previous was $count".PHP_EOL;
                unset($results['metrics'][$metric]['trends'][$date][$value]);
            }
        }//end foreach

        // Update the repo's result file.
        file_put_contents($resultFile, jsonpp(json_encode($results, JSON_FORCE_OBJECT)));
        unlink($tempFile);

        // Save the progress of the run.
        $progress[$repo->url][$date] = true;
        file_put_contents($progressFile, jsonpp(json_encode($progress, JSON_FORCE_OBJECT)));
    }//end foreach

    echo PHP_EOL;

}//end foreach

unlink($progressFile);
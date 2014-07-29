<?php
/*
require_once __DIR__.'/_assets/common.php';
$resultFiles = array();
$repos       = json_decode(file_get_contents(__DIR__.'/_assets/repos.json'));
$repoCount   = count($repos);

$GLOBALS['repoList'] = array();
require_once __DIR__.'/_assets/metricText.php';
$GLOBALS['metric_text']  = $metricText;

$repoNum = 0;
foreach ($repos as $repo) {
    echo 'Processing '.$repo->name." ($repoNum / $repoCount)".PHP_EOL;
    $resultFiles[$repo->url] = processRepo($repo, null, false, false);
    echo PHP_EOL;

    $GLOBALS['repoList'][$repo->url] = $repo->name;
}

$resultFiles[] = __DIR__.'/results.json';
$totals        = json_decode(file_get_contents(__DIR__.'/results.json'), true);
*/

$repoResults = array();

$globalEvents = array();
$allEvents    = array();
$repoEvents   = array();

$trendFiles   = $resultFiles;
$trendFiles[] = __DIR__.'/results.json';

foreach ($trendFiles as $file) {
    $results = json_decode(file_get_contents($file), true);
    if (isset($results['project']) === false) {
        $repo      = null;
        $metrics   = $results;
        $minChange = 0.55;
        $totals    = $results;
        #echo "Processing main result file: $file\n";
    } else {
        $repo      = $results['project']['path'];
        $metrics   = $results['metrics'];
        $minChange = 3;
        #echo "Processing result file for $repo: $file\n";
        $repoResults[$repo] = $results;
    }

    foreach ($metrics as $metric => $data) {
        $lastTotal  = null;
        $lastScores = array();
        foreach ($data['trends'] as $date => $trendValues) {
            $trendTotal = array_sum($trendValues);
            $allTotal   = array_sum($totals[$metric]['trends'][$date]);

            if ($repo !== null) {
                if ($lastTotal !== null && $trendTotal !== $lastTotal) {
                    $diff = ($trendTotal - $lastTotal);
                    if ($lastTotal === 0) {
                        $diffPercent = 100;
                    } else {
                        $diffPercent = round(($diff / $allTotal) * 100, 2);
                    }

                    if ($diffPercent > 0.55 || $diffPercent < -0.55) {
                        #echo "\t=> $date / $metric: total changed by $diffPercent ($diff)\n";
                        $repoEvents[$repo][$metric][$date]['total'] = $diff;
                        $allEvents[$metric][$date]['total'][$repo]  = $diff;
                    }
                }

                $lastTotal = $trendTotal;
            }

            // Popular the scores for the first date, but don't record trends
            // as there is no data to compare.
            if (empty($lastScores) === true) {
                foreach ($trendValues as $trendValue => $trendCount) {
                    $lastScores[$trendValue] = round(($trendCount / $trendTotal * 100), 2);
                }

                continue;
            }

            foreach ($trendValues as $trendValue => $trendCount) {
                $score = round(($trendCount / $trendTotal * 100), 2);
                if (isset($lastScores[$trendValue]) === true) {
                    $lastScore = $lastScores[$trendValue];
                } else {
                    $lastScore = 0;
                }

                if ($lastScore === $score) {
                    continue;
                }

                $diff = round($score - $lastScore, 2);
                if ($diff > $minChange || $diff < ($minChange * -1)) {
                    #echo "\t=> $date / $metric / $trendValue: score changed by $diff\n";
                    if ($repo !== null) {
                        $repoEvents[$repo][$metric][$date][$trendValue] = $diff;
                        $allEvents[$metric][$date][$trendValue][$repo]  = $diff;
                    } else {
                        $globalEvents[$metric][$date][$trendValue] = $diff;
                    }
                }

                $lastScores[$trendValue] = $score;
            }//end foreach

            foreach ($lastScores as $trendValue => $diff) {
                if (isset($trendValues[$trendValue]) === true) {
                    continue;
                }

                if ($diff > $minChange || $diff < ($minChange * -1)) {
                    #echo "\t=> $date / $metric / $trendValue: score changed by $diff\n";
                    if ($repo !== null) {
                        $repoEvents[$repo][$metric][$date][$trendValue] = ($diff * -1);
                        $allEvents[$metric][$date][$trendValue][$repo]  = ($diff * -1);
                    } else {
                        $globalEvents[$metric][$date][$trendValue] = ($diff * -1);
                    }
                }

                $lastScores[$trendValue] = 0;
            }
        }//end foreach
    }//end foreach
}//end foreach


$trends = array();


foreach ($repoEvents as $repo => $events) {
    #echo "======================================================= Process $repo\n";
    foreach ($events as $metric => $eventDates) {
        #echo "****** Trend events for $metric *****\n";
        foreach ($eventDates as $eventDate => $eventValues) {
            $dateTrends = array();

            #print_r($eventValues);
            foreach ($eventValues as $eventValue => $eventChange) {
                if ($eventValue === 'total') {
                    continue;
                }

                if ($eventChange < 0) {
                    $source      = $eventValue;
                    $destination = null;
                } else {
                    $source      = null;
                    $destination = $eventValue;
                }

                // See if the values went to, or were sourced from, another value.
                $foundMe = false;
                foreach ($eventValues as $value => $change) {
                    if ($value === $eventValue) {
                        $foundMe = true;
                        continue;
                    } else if (round($change, 1) === round(($eventChange * -1), 1)) {
                        // The values went to, or came from, here.
                        if ($foundMe === false) {
                            // Handled by the destination.
                            continue(2);
                        }

                        if ($eventChange > 0) {
                            $source = $value;
                        } else {
                            $destination = $value;
                        }
                    }
                }

                // We need a positive value to print a message.
                $change = $eventChange;
                if ($eventChange < 0) {
                    $change *= -1;
                }

                if ($source === null) {
                    $dateTrends[] = "there was a $change% swing towards <em>$eventValue</em>";
                } else if ($destination === null) {
                    $dateTrends[] = "there was a $change% swing away from <em>$eventValue</em>";
                } else {
                    $dateTrends[] = "there was a $change% swing from <em>$source</em> to <em>$destination</em>";
                }
            }//end foreach

            if (empty($dateTrends) === false) {
                $trends[$repo][$metric][$eventDate] = $dateTrends;
            }
        }//end foreach
    }//end foreach
}//end foreach



foreach ($globalEvents as $metric => $eventDates) {
    #echo "****** Trend events for $metric *****\n";
    $metricid = preg_replace('/[^0-9a-zA-Z]/', '-', strtolower($metric));

    foreach ($eventDates as $eventDate => $eventValues) {
        $dateTrends = array();

        foreach ($eventValues as $eventValue => $eventChange) {
            #echo "PROCESS $eventValue with $eventChange\n";
            if ($eventChange < 0) {
                $source      = $eventValue;
                $destination = null;
            } else {
                $source      = null;
                $destination = $eventValue;
            }

            // See if the values went to, or were sourced from, another value.
            $foundMe = false;
            foreach ($eventValues as $value => $change) {
                if ($value === $eventValue) {
                    $foundMe = true;
                    continue;
                } else if (round($change, 1) === round(($eventChange * -1), 1)) {
                    // The values went to, or came from, here.
                    if ($foundMe === false) {
                        // Handled by the destination.
                        continue(2);
                    }

                    if ($eventChange > 0) {
                        $source = $value;
                    } else {
                        $destination = $value;
                    }
                }
            }

            // We need a positive value to print a message.
            $change = $eventChange;
            if ($eventChange < 0) {
                $change *= -1;
            }

            if ($source === null) {
                $dateTrends[] = "there was a $change% swing towards <em>$eventValue</em>";
            } else if ($destination === null) {
                $dateTrends[] = "there was a $change% swing away from <em>$eventValue</em>";
            } else {
                $dateTrends[] = "there was a $change% swing from <em>$source</em> to <em>$destination</em>";
            }

            #echo "SOURCE: $source DEST: $destination\n";

            // Find what repo-specific swings contributed to this change.
            $sourceEvents = array();
            if (isset($allEvents[$metric][$eventDate][$source]) === true) {
                $sourceEvents = $allEvents[$metric][$eventDate][$source];
            }

            $destEvents = array();
            if (isset($allEvents[$metric][$eventDate][$destination]) === true) {
                $destEvents = $allEvents[$metric][$eventDate][$destination];
            }

            #print_r($sourceEvents);
            #print_r($destEvents);

            foreach ($sourceEvents as $repo => $change) {
                // Only interested in events that follow the same direction
                // as the main trend.
                if ($change > 0) {
                    continue;
                }

                $repoName = "<a href=\"$repo/index.html#$metricid\">".$GLOBALS['repoList'][$repo].'</a>';
                $change  *= -1;

                if (isset($destEvents[$repo]) === true
                    && round($change, 1) === round($destEvents[$repo], 1)
                ) {
                    $dateTrends[] = "$repoName saw a $change% swing from <em>$source</em> to <em>$destination</em>";
                    unset($destEvents[$repo]);
                } else {
                    $dateTrends[] = "$repoName saw a $change% swing away from <em>$source</em>";
                }
            }//end foreach

            foreach ($destEvents as $repo => $change) {
                // Only interested in events that follow the same direction
                // as the main trend.
                if ($change < 0) {
                    continue;
                }

                $repoName = "<a href=\"$repo/index.html#$metricid\">".$GLOBALS['repoList'][$repo].'</a>';

                if (isset($sourceEvents[$repo]) === true
                    && round($change, 1) === round($sourceEvents[$repo], 1)
                ) {
                    $dateTrends[] = "$repoName saw a $change% swing from <em>$source</em> to <em>$destination</em>";
                } else {
                    $dateTrends[] = "$repoName saw a $change% swing towards <em>$destination</em>";
                }
            }//end foreach

            if (isset($allEvents[$metric][$eventDate]['total']) === true) {
                foreach ($allEvents[$metric][$eventDate]['total'] as $repo => $change) {
                    $itemName = $GLOBALS['metric_text'][$metric]['items'];
                    $repoName = "<a href=\"$repo/index.html#$metricid\">".$GLOBALS['repoList'][$repo].'</a>';

                    // Find the previous date's trend data.
                    $previousDate = null;
                    foreach ($repoResults[$repo]['metrics'][$metric]['trends'] as $trendDate => $trendData) {
                        if ($trendDate === $eventDate) {
                            break;
                        } else {
                            $previousDate = $trendDate;
                        }
                    }

                    $previousValues = $repoResults[$repo]['metrics'][$metric]['trends'][$previousDate];
                    $currentValues  = $repoResults[$repo]['metrics'][$metric]['trends'][$eventDate];
                    #print_r($previousValues);
                    #print_r($currentValues);
                    if (isset($currentValues[$source]) === true
                        && isset($previousValues[$source]) === true
                    ) {
                        $valueCount = ($currentValues[$source] - $previousValues[$source]);
                    } else if (isset($previousValues[$source]) === true) {
                        $valueCount = ($previousValues[$source] * -1);
                    } else {
                        $valueCount = 0;
                    }

                    $trendString = '';
                    if ($valueCount < 0) {
                        $valueCount *= -1;
                        $trendString = "$repoName removed $valueCount $itemName using <em>$source</em>";
                    }

                    if ($destination !== null) {
                        if (isset($currentValues[$destination]) === true
                            && isset($previousValues[$destination]) === true
                        ) {
                            $valueCount = ($currentValues[$destination] - $previousValues[$destination]);
                        } else if (isset($currentValues[$destination]) === true) {
                            $valueCount = $currentValues[$destination];
                        } else {
                            $valueCount = 0;
                        }

                        if ($valueCount > 0) {
                            if ($trendString !== '') {
                                $trendString .= ' and';
                            } else {
                                $trendString = "$repoName";
                            }

                            $trendString .= " added $valueCount $itemName using <em>$destination</em>";
                        }
                    }//end if

                    if ($trendString !== '') {
                        $dateTrends[] = $trendString;
                    }
                }//end foreach
            }//end if
        }//end foreach

        if (empty($dateTrends) === false) {
            $trends['global'][$metric][$eventDate] = $dateTrends;
        }
    }//end foreach
}//end foreach

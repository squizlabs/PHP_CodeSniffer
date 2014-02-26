#!/usr/bin/env php
<?php
$results = array();
$repos   = json_decode(file_get_contents(dirname(__FILE__).'/_assets/repos.json'));
foreach ($repos as $repo) {
    list($orgName, $repoName) = explode('/', $repo->url);

    $orgDir = dirname(__FILE__)."/$orgName";
    if (is_dir($orgDir) === false) {
        mkdir($orgDir);
    }

    $repoDir = $orgDir."/$repoName";
    if (is_dir($repoDir) === false) {
        mkdir($repoDir);
        mkdir($repoDir.'/results');
    }

    $cloneDir = $repoDir.'/src';
    $cloneURL = 'https://github.com/'.$repo->url.'.git';

    echo 'Processing '.$repo->name." ($cloneURL)".PHP_EOL;

    if (is_dir($cloneDir) === true) {
        echo 'Repository clone already exists, updating'.PHP_EOL;
        $cmd = "cd $cloneDir; git pull; git submodule update --init --recursive";
    } else {
        $cmd = "git clone --recursive $cloneURL $cloneDir";
    }

//    $output = array();
//    $retVal = null;
//    exec($cmd, $output, $retVal);
//    echo implode(PHP_EOL, $output);
//    if (empty($output) === false) {
//        echo PHP_EOL;
//    }
//
//    $reportFile = $repoDir.'/results/'.date('Y-m-d').'.json';
//    if (file_exists($reportFile) === false) {
//        $checkDir   = $cloneDir.'/'.$repo->path;
//        $reportPath = dirname(__FILE__).'/_assets/PHPCSInfoReport.php';
//        $cmd        = 'phpcs --standard='.dirname(__FILE__).'/_assets/ruleset.xml --extensions=php,inc -d memory_limit=256M';
//        $cmd       .= ' --ignore=*/tests/*,'.$repo->ignore;
//        $cmd       .= " --report=$reportPath --report-file=$reportFile $checkDir";
//
//        echo 'Running PHPCS'.PHP_EOL."\t=> $cmd".PHP_EOL;
//
//        exec($cmd);
//        exec("cp $reportFile $repoDir/results/latest.json");
//    } else {
//        echo 'Skipping PHPCS step'.PHP_EOL;
//    }

    $results[] = $repoDir.'/results/latest.json';
    echo str_repeat('-', 30).PHP_EOL;
}//end foreach

$totals = array();
foreach ($results as $file) {
    $parts = explode('/', $file);
    $num   = count($parts);
    $repo  = $parts[($num - 4)].'/'.$parts[($num - 3)];

    echo "Processing result file for $repo: $file".PHP_EOL;
    $results = json_decode(file_get_contents($file));
    foreach ($results as $metric => $data) {
        if (isset($totals[$metric]) === false) {
            $totals[$metric] = array(
                                'sniffs'      => array(),
                                'total'       => 0,
                                'total_repos' => 0,
                                'values'      => array(),
                                'repos'       => array(),
                               );
        }

        foreach ($data->sniffs as $sniff) {
            $totals[$metric]['sniffs'][] = $sniff;
        }

        $totals[$metric]['sniffs'] = array_unique($totals[$metric]['sniffs']);

        $winner      = '';
        $winnerCount = 0;
        foreach ($data->values as $value => $count) {
            if (isset($totals[$metric]['values'][$value]) === false) {
                $totals[$metric]['values'][$value] = $count;
            } else {
                $totals[$metric]['values'][$value] += $count;
            }

            $totals[$metric]['total'] += $count;

            if ($count > $winnerCount) {
                $winner      = $value;
                $winnerCount = $count;
            }
        }

        if (isset($totals[$metric]['repos'][$winner]) === false) {
            $totals[$metric]['repos'][$winner] = array();
        }

        $totals[$metric]['repos'][$winner][] = $repo;
        $totals[$metric]['total_repos']++;

    }//end foreach
}//end foreach

foreach ($totals as $metric => $data) {
    $winner      = '';
    $winnerCount = 0;
    foreach ($data['values'] as $value => $count) {
        if ($count > $winnerCount) {
            $winner      = $value;
            $winnerCount = $count;
        }
    }

    $totals[$metric]['winner'] = $winner;
}

$filename = dirname(__FILE__).'/_results/'.date('Y-m-d').'.json';
file_put_contents($filename, json_encode($totals, JSON_FORCE_OBJECT));
exec("cp $filename ".dirname(__FILE__).'/_results/latest.json');

$html = '';
$js   = '';

$chartNum = 0;
$colours  = array(
             '#4D5360',
             '#D4CCC5',
             '#9D9B7F',
             '#949FB1',
             '#7D4F6D',
             '#584A5E',
            );

$js .= 'var valOptions = {animation:false,segmentStrokeWidth:1,percentageInnerCutout:60};'.PHP_EOL;
$js .= 'var repoOptions = {animation:false,segmentStrokeWidth:1,percentageInnerCutout:90};'.PHP_EOL;

// Imports $metricText variable.
require_once(dirname(__FILE__).'/_assets/metricText.php');

uasort($totals, 'sort_metrics');
foreach ($totals as $metric => $data) {
    $description = '';
    if (isset($metricText[$metric]['description']) === true) {
        $description = $metricText[$metric]['description'];
    }

    $items = 'items';
    if (isset($metricText[$metric]['items']) === true) {
        $items = $metricText[$metric]['items'];
    }

    $chartNum++;
    $html .= '<div class="metric"><h2 title="'.implode(',', $data['sniffs']).'">'.$metric.'</h2><p>'.$description.'</p>'.PHP_EOL;
    $html .= '<canvas class="chart-value" id="chart'.$chartNum.'" width="400" height="400"></canvas>'.PHP_EOL;
    $html .= '<canvas class="chart-repo" id="chart'.$chartNum.'r" width="240" height="240"></canvas>'.PHP_EOL;
    $html .= '<div class="chart-data"><table>';

    $valsData = 'var data = [';
    $repoData = 'var data = [';

    $valueNum  = 0;
    $other     = 0;
    $numValues = count($data['values']);

    $sort = SORT_STRING;
    if (isset($metricText[$metric]['sort']) === true) {
        $sort = $metricText[$metric]['sort'];
    }

    ksort($data['values'], $sort);
    foreach ($data['values'] as $value => $count) {
        $colour = $colours[$valueNum];

        if (isset($data['repos'][$value]) === true) {
            $numRepos     = count($data['repos'][$value]);
            $percentRepos = round($numRepos / $data['total_repos'] * 100, 2);
        } else {
            $numRepos     = 0;
            $percentRepos = 0;
        }

        $repoData .= '{value:'.$percentRepos.',color:"'.$colour.'"},';

        $percent = round($count / $data['total'] * 100, 2);
        if ($numRepos === 0 && $numValues > 4 && $percent < 1) {
            $other += $count;
            continue;
        }

        $count     = number_format($count, 0, '', ',');
        $valsData .= '{value:'.$percent.',color:"'.$colour.'"},';

        $html .= '<tr title="'.$count.' '.$items.'"><td><span class="colour-box" style="background-color:'.$colour.'"></span></td>';
        $html .= "<td>$value</td><td>$percent%</td></tr>";

        $html .= '<tr title="'.$numRepos.' projects"><td colspan=3 class="preferred">preferred by '.$percentRepos.'% of projects';
        $html .= '</td>'.PHP_EOL;
        $valueNum++;
    }//end foreach

    $repoData  = substr($repoData, 0, -1);
    $repoData .= ']'.PHP_EOL;
    $valsData  = substr($valsData, 0, -1);
    $valsData .= ']'.PHP_EOL;

    $js .= $valsData;
    $js .= 'var c = document.getElementById("chart'.$chartNum.'").getContext("2d");'.PHP_EOL;
    $js .= 'new Chart(c).Doughnut(data,valOptions);'.PHP_EOL;
    $js .= $repoData;
    $js .= 'var c = document.getElementById("chart'.$chartNum.'r").getContext("2d");'.PHP_EOL;
    $js .= 'new Chart(c).Doughnut(data,repoOptions);'.PHP_EOL;

    if ($other > 0) {
        $percent = round($other / $data['total'] * 100, 2);
        $other   = number_format($other, 0, '', ',');
        $html   .= '<tr title="'.$other.' '.$items.'"><td colspan=2>other</td><td colspan=2>'.$percent.'%</td></tr>';
        $html   .= '<tr><td colspan=3 class="preferred">&nbsp;</td></tr>';
    }

    $html .= '</table>';
    $html .= 'Based on '.number_format($data['total'], 0, '', ',')." $items in ".$data['total_repos'].' projects';
    $html .= '</div></div>'.PHP_EOL;
}//end foreach

$output = file_get_contents(dirname(__FILE__).'/_assets/index.html.template');
$output = str_replace('((html))', $html, $output);
$output = str_replace('((js))', $js, $output);
file_put_contents(dirname(__FILE__).'/index.html', $output);

// Comparison function
function sort_metrics($a, $b) {
    $aPercent = ($a['values'][$a['winner']] / $a['total']);
    $bPercent = ($b['values'][$b['winner']] / $b['total']);
    if ($aPercent < $bPercent) {
        return -1;
    } else {
        return 1;
    }
}

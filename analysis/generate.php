<?php
$resultFiles = array();
$repos       = json_decode(file_get_contents(__DIR__.'/_assets/repos.json'));

$today        = date('Y-m-d');
$checkoutDate = $today;
$recordTrend  = false;
$runPHPCS     = true;
$runGit       = true;
foreach ($_SERVER['argv'] as $arg) {
    if (substr($arg, 0, 7) === '--date=') {
        $checkoutDate = substr($arg, 7);
    } else if ($arg === '--trend') {
        $recordTrend = true;
    } else if ($arg === '--no-phpcs') {
        $runPHPCS = false;
    } else if ($arg === '--no-git') {
        $runGit = false;
    }
}

$repoCount = count($repos);
$repoNum   = 0;
foreach ($repos as $repo) {
    list($orgName, $repoName) = explode('/', $repo->url);

    $orgDir = __DIR__."/$orgName";
    if (is_dir($orgDir) === false) {
        mkdir($orgDir);
    }

    $repoDir = $orgDir."/$repoName";
    if (is_dir($repoDir) === false) {
        mkdir($repoDir);
        mkdir($repoDir.'/results');
    }

    $cloneDir      = $repoDir.'/src';
    $cloneURL      = 'https://github.com/'.$repo->url.'.git';
    $resultFile    = $repoDir.'/results.json';
    $resultFiles[] = $resultFile;
    $prevTotals    = null;

    $repoNum++;
    echo 'Processing '.$repo->name." ($repoNum / $repoCount)".PHP_EOL;
    echo "\tclone URL: $cloneURL".PHP_EOL;

    if (is_dir($cloneDir) === false) {
        if ($runGit === false) {
            echo "\t* respository has not been cloned, skipping *".PHP_EOL;
            continue;
        }

        // Clone it.
        echo "\t=> Cloning new repository".PHP_EOL;
        $cmd = "git clone --recursive $cloneURL $cloneDir";
        echo "\t\tcmd: $cmd".PHP_EOL;
        $output = shell_exec($cmd);
        echo implode(PHP_EOL, $output);
    } else if ($runPHPCS === true && file_exists($resultFile) === true) {
        // Load in old trend values.
        echo "\t=> Loading old trend values from $resultFile".PHP_EOL;
        $prevTotals = json_decode(file_get_contents($resultFile), true);
    }

    if ($runGit === true) {
        // Figure out the HEAD ref and use that.
        echo "\t=> Determining branch to use".PHP_EOL;
        $cmd = "cd $cloneDir; cat .git/refs/remotes/origin/HEAD";
        echo "\t\tcmd: ";
        echo str_replace('; ', PHP_EOL."\t\tcmd: ", $cmd).PHP_EOL;
        $branch = trim(shell_exec($cmd));
        echo "\t\tout: $branch".PHP_EOL;
        $branch = substr($branch, (strpos($branch, 'origin/') + 7));
        echo "\t\t* using branch $branch *".PHP_EOL;

        $cmd = "cd $cloneDir; ";
        if ($checkoutDate !== $today) {
            echo "\t=> Checking out specific date: $checkoutDate".PHP_EOL;
            $cmd .= "git checkout `git rev-list -n 1 --before=\"$checkoutDate 00:00\" $branch` 2>&1; ";
        } else {
            echo "\t=> Updating repository".PHP_EOL;
            $cmd .= "git checkout $branch 2>&1; git pull 2>&1; ";
        }

        $cmd .= 'git submodule update --init --recursive 2>&1';

        echo "\t\tcmd: ";
        echo str_replace('; ', PHP_EOL."\t\tcmd: ", $cmd).PHP_EOL;

        $output = trim(shell_exec($cmd));
        echo "\t\tout: ";
        echo str_replace(PHP_EOL, PHP_EOL."\t\tout: ", $output).PHP_EOL;
    } else {
        echo "\t* skipping respository update step *".PHP_EOL;
    }

    if ($runPHPCS === true) {
        $checkDir          = $cloneDir.'/'.$repo->path;
        $infoReportPath    = __DIR__.'/_assets/PHPCSInfoReport.php';
        $summaryReportPath = __DIR__.'/_assets/PHPCSSummaryReport.php';
        $cmd        = 'phpcs -d memory_limit=256M '.$checkDir.' --standard='.__DIR__.'/_assets/ruleset.xml --extensions=php,inc';
        $cmd       .= ' --ignore=*/tests/*,'.$repo->ignore;
        $cmd       .= ' --runtime-set project '.$repo->url;
        $cmd       .= " --report=$summaryReportPath --report-$infoReportPath=$resultFile";
        echo "\t=> Running PHP_CodeSniffer".PHP_EOL;
        echo "\t\tcmd: ";
        echo str_replace(' --', PHP_EOL."\t\tcmd: --", $cmd).PHP_EOL;
        $output = trim(shell_exec($cmd));
        echo "\t\tout: ";
        echo str_replace(PHP_EOL, PHP_EOL."\t\tout: ", $output).PHP_EOL;
    } else {
        echo "\t* skipping PHP_CodeSniffer step *".PHP_EOL.PHP_EOL;
        continue;
    }

    echo PHP_EOL;

    if ($prevTotals !== null) {
        // Copy old trend data into the new result set.
        $newTotals = json_decode(file_get_contents($resultFile), true);
        foreach ($prevTotals['metrics'] as $metric => $data) {
            if (isset($data['trends']) === false) {
                continue;
            }

            if (isset($newTotals['metrics'][$metric]) === false) {
                $newTotals['metrics'][$metric] = array(
                                                  'sniffs'      => array(),
                                                  'total'       => 0,
                                                  'values'      => array(),
                                                  'percentages' => array(),
                                                  'trends'      => $data['trends'],
                                                 );
                continue;
            }

            foreach ($data['trends'] as $date => $values) {
                $newTotals['metrics'][$metric]['trends'][$date] = $values;
            }

            ksort($newTotals['metrics'][$metric]['trends']);
        }//end foreach

        file_put_contents($resultFile, json_encode($newTotals, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT));
    }//end if

}//end foreach

// Imports $metricText variable.
require_once __DIR__.'/_assets/metricText.php';
$colours = array(
            '#4D5360',
            '#D4CCC5',
            '#9D9B7F',
            '#949FB1',
            '#7D4F6D',
            '#584A5E',
           );

echo "Generating HTML files".PHP_EOL;

$totals = array();
foreach ($resultFiles as $file) {
    $results = json_decode(file_get_contents($file), true);
    $repo    = $results['project']['path'];
    echo "\t=> Processing result file for $repo: $file".PHP_EOL;

    foreach ($results['metrics'] as $metric => $data) {
        if (isset($totals[$metric]) === false) {
            $totals[$metric] = array(
                                'sniffs'      => array(),
                                'total'       => 0,
                                'total_repos' => 0,
                                'values'      => array(),
                                'repos'       => array(),
                                'trends'      => array(),
                               );
        }

        foreach ($data['sniffs'] as $sniff) {
            $totals[$metric]['sniffs'][] = $sniff;
        }

        $totals[$metric]['sniffs'] = array_unique($totals[$metric]['sniffs']);

        $winner      = '';
        $winnerCount = 0;
        foreach ($data['values'] as $value => $count) {
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

        if ($recordTrend === true) {
            $results['metrics'][$metric]['trends'][$checkoutDate] = $data['values'];
        }

        // Needed for sorting this result set later on.
        $results['metrics'][$metric]['winner'] = $winner;
        if (isset($totals[$metric]['repos'][$winner]) === false) {
            $totals[$metric]['repos'][$winner] = array();
        }

        $totals[$metric]['repos'][$winner][] = $repo;
        $totals[$metric]['total_repos']++;

    }//end foreach

    $html = '';
    $js   = 'var valOptions = {animation:false,segmentStrokeWidth:1,percentageInnerCutout:60};'.PHP_EOL;
    $js  .= 'var trendOptions = {animation:false,scaleLineColor:"none",scaleLabel:"<%=value%>%",scaleFontSize:8,scaleFontFamily:"verdana",bezierCurve:false,pointDot:true,datasetFill:false};'.PHP_EOL;

    uasort($results['metrics'], 'sortMetrics');
    $chartNum = 0;
    foreach ($results['metrics'] as $metric => $data) {
        $description = '';
        if (isset($metricText[$metric]['description']) === true) {
            $description = $metricText[$metric]['description'];
        }

        $items = 'items';
        if (isset($metricText[$metric]['items']) === true) {
            $items = $metricText[$metric]['items'];
        }

        $chartNum++;
        $id    = str_replace(' ', '-', strtolower($metric));
        $html .= '<h2 id="'.$id.'" title="'.implode(',', $data['sniffs']).'">'.$metric.'</h2>'.PHP_EOL;
        $html .= '<div class="metric"><p>'.$description.'</p>'.PHP_EOL;
        $html .= '  <canvas class="chart-value" id="chart'.$chartNum.'" width="400" height="400"></canvas>'.PHP_EOL;
        $html .= '  <canvas class="chart-trend" id="chart'.$chartNum.'t" width="860" height="145"></canvas>'.PHP_EOL;
        $html .= '  <div class="chart-data"><table>';

        $valsData  = 'var data = [';
        $trendData = '';
        $valueNum  = 0;
        $other     = 0;
        $numValues = count($data['values']);

        $sort = SORT_STRING;
        if (isset($metricText[$metric]['sort']) === true) {
            $sort = $metricText[$metric]['sort'];
        }

        ksort($data['values'], $sort);
        foreach ($data['values'] as $value => $count) {
            $colour  = $colours[$valueNum];
            $percent = round($count / $data['total'] * 100, 2);
            if ($numValues > 4 && $percent < 1) {
                $other += $count;
                continue;
            }

            $count     = number_format($count, 0, '', ',');
            $valsData .= '{value:'.$percent.',color:"'.$colour.'"},';

            $html .= '<tr title="'.$count.' '.$items.'">';
            if ($value === $data['winner']) {
                $html .= '<td><div class="winner"><span class="colour-box winner" style="background-color:'.$colour.'"></span></div></td>';
            } else {
                $html .= '<td><span class="colour-box" style="background-color:'.$colour.'"></span></td>';
            }

            $html .= "<td>$value</td><td>$percent%</td></tr>";

            $trendData .= "{strokeColor:\"$colour\",pointStrokeColor:\"$colour\",pointColor:\"#FFF\",data:[";
            ksort($data['trends']);
            foreach ($data['trends'] as $date => $trendValues) {
                $trendTotal = array_sum($trendValues);
                $addedValue = false;
                foreach ($trendValues as $trendValue => $trendCount) {
                    if ($trendValue !== $value) {
                        continue;
                    }

                    $trendData .= round(($trendCount / $trendTotal * 100), 2).',';
                    $addedValue = true;
                    break;
                }

                if ($addedValue === false) {
                    // Percentage must have been 0 as no values recorded.
                    $trendData .= '0.00,';
                }
            }

            $trendData  = rtrim($trendData, ',');
            $trendData .= ']},';
            $valueNum++;
        }//end foreach

        $valsData  = substr($valsData, 0, -1);
        $valsData .= ']'.PHP_EOL;

        $js .= $valsData;
        $js .= 'var c = document.getElementById("chart'.$chartNum.'").getContext("2d");'.PHP_EOL;
        $js .= 'new Chart(c).Doughnut(data,valOptions);'.PHP_EOL;

        $js .= 'var data = {labels:[';
        $numDates = count($data['trends']);
        $dateStep = ceil($numDates / 4);
        $dateNum  = 1;
        foreach (array_keys($data['trends']) as $date) {
            //if ($dateNum === 1 || $dateNum === $numDates || $dateNum % $dateStep === 0) {
                $time = strtotime($date);
                $js .= '"'.date('d-M', $time).'",';
            //} else {
            //    $js .= '"",';
            //}

            $dateNum++;
            continue;
        }
        $js  = rtrim($js, ',');

        $trendData = rtrim($trendData, ',');
        $js       .= "],datasets:[$trendData]};".PHP_EOL;
        $js       .= 'var c = document.getElementById("chart'.$chartNum.'t").getContext("2d");'.PHP_EOL;
        $js       .= 'new Chart(c).Line(data,trendOptions);'.PHP_EOL;

        if ($other > 0) {
            $percent = round($other / $data['total'] * 100, 2);
            $other   = number_format($other, 0, '', ',');
            $html   .= '<tr title="'.$other.' '.$items.'"><td colspan=2>other</td><td colspan=2>'.$percent.'%</td></tr>';
        }

        $html .= '</table>';
        $html .= 'Based on '.number_format($data['total'], 0, '', ',')." $items";
        $html .= '</div></div>'.PHP_EOL;
    }//end foreach

    $intro  = "<h1>Analysis of Coding Conventions for</br>$repo</h1>".PHP_EOL;
    $intro .= '<p><a href="https://github.com/squizlabs/PHP_CodeSniffer">PHP_CodeSniffer</a>, using a custom coding standard and report, was used to record various coding conventions for this project. The graphs for each coding convention show the percentage of each style variation used throughout the project.</p><p>You can <a href="./results.json">view the raw data</a> used to generate this report, and use it in any way you want.</p>'.PHP_EOL;
    $intro .= '<p>You can also <a href="../../index.html">view a combined analysis</a> that covers '.count($resultFiles).' PHP projects</p>'.PHP_EOL;

    $commitid = $results['project']['commitid'];
    $footer   = 'Report generated on '.date('r')."<br/>Using master branch of <a href=\"https://github.com/$repo\">$repo</a> @ commit <a href=\"https://github.com/$repo/commit/$commitid\">$commitid";

    $output = file_get_contents(__DIR__.'/_assets/index.html.template');
    $output = str_replace('((title))', $repo.' - Coding Standards Analysis', $output);
    $output = str_replace('((intro))', $intro, $output);
    $output = str_replace('((html))', $html, $output);
    $output = str_replace('((footer))', $footer, $output);
    $output = str_replace('((js))', $js, $output);
    $output = str_replace('((assetPath))', '../../', $output);
    file_put_contents(__DIR__.'/'.$repo.'/index.html', $output);
    file_put_contents($file, json_encode($results, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT));

}//end foreach

// Load in old trend values.
$filename   = __DIR__.'/results.json';
$prevTotals = json_decode(file_get_contents($filename), true);
foreach ($prevTotals as $metric => $data) {
    if (isset($data['trends']) === false) {
        continue;
    }

    if (isset($totals[$metric]) === false) {
        $totals[$metric] = array(
                            'sniffs'      => array(),
                            'total'       => 0,
                            'total_repos' => 0,
                            'values'      => array(),
                            'repos'       => array(),
                            'trends'      => $data['trends'],
                           );
        continue;
    }

    foreach ($data['trends'] as $date => $values) {
        $totals[$metric]['trends'][$date] = $values;
    }
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

    if ($recordTrend === true) {
        $totals[$metric]['trends'][$checkoutDate] = $data['values'];
        ksort($totals[$metric]['trends']);
    }
}

file_put_contents($filename, json_encode($totals, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT));

$html = '';
$js   = 'var valOptions = {animation:false,segmentStrokeWidth:1,percentageInnerCutout:60};'.PHP_EOL;
$js  .= 'var repoOptions = {animation:false,segmentStrokeWidth:1,percentageInnerCutout:90};'.PHP_EOL;
$js  .= 'var trendOptions = {animation:false,scaleLineColor:"none",scaleLabel:"<%=value%>%",scaleFontSize:8,scaleFontFamily:"verdana",bezierCurve:false,pointDot:true,datasetFill:false};'.PHP_EOL;

uasort($totals, 'sortMetrics');
$chartNum = 0;
foreach ($totals as $metric => $data) {
    if ($data['total'] === 0) {
        continue;
    }

    $description = '';
    if (isset($metricText[$metric]['description']) === true) {
        $description = $metricText[$metric]['description'];
    }

    $items = 'items';
    if (isset($metricText[$metric]['items']) === true) {
        $items = $metricText[$metric]['items'];
    }

    $chartNum++;
    $metricid = str_replace(' ', '-', strtolower($metric));

    $html .= '<h2 id="'.$metricid.'" title="'.implode(',', $data['sniffs']).'">'.$metric.'</h2>'.PHP_EOL;
    $html .= '<div class="metric">'.PHP_EOL."  <p>$description</p>".PHP_EOL;
    $html .= '  <canvas class="chart-value" id="chart'.$chartNum.'" width="400" height="400"></canvas>'.PHP_EOL;
    $html .= '  <canvas class="chart-repo" id="chart'.$chartNum.'r" width="240" height="240"></canvas>'.PHP_EOL;
    $html .= '  <canvas class="chart-trend" id="chart'.$chartNum.'t" width="860" height="145"></canvas>'.PHP_EOL;
    $html .= '  <div class="chart-data">'.PHP_EOL;
    $html .= '    <table>'.PHP_EOL;

    $valsData      = 'var data = [';
    $repoData      = 'var data = [';
    $trendData     = '';
    $repoHTML      = '';
    $repoResetCode = '';

    $valueNum  = 0;
    $other     = 0;
    $numValues = count($data['values']);

    $sort = SORT_STRING;
    if (isset($metricText[$metric]['sort']) === true) {
        $sort = $metricText[$metric]['sort'];
    }

    ksort($data['values'], $sort);
    foreach ($data['values'] as $value => $count) {
        $colour  = $colours[$valueNum];
        $valueid = str_replace(' ', '-', strtolower($value));

        if (isset($data['repos'][$value]) === true) {
            $numRepos     = count($data['repos'][$value]);
            $percentRepos = round($numRepos / $data['total_repos'] * 100, 2);

            if ($numRepos === 1) {
                $title = '1 project prefers';
            } else {
                $title = "$numRepos projects prefer";
            }
            
            $repoHTML .= '  <div id="'.$metricid.'-'.$valueid.'-repos" class="repo-data">'.PHP_EOL;
            $repoHTML .= "    <p><span onclick=\"document.getElementById('{$metricid}-{$valueid}-repos').style.display='none';\" class=\"close\">[close]</span><strong>$title <em>$value</em></strong></p>".PHP_EOL;
            $repoHTML .= '    <ul>'.PHP_EOL;

            sort($data['repos'][$value], SORT_STRING | SORT_FLAG_CASE);
            foreach ($data['repos'][$value] as $repo) {
                $href      = $repo.'/index.html#'.$metricid;
                $repoHTML .= "      <a href=\"$href\"><li>$repo</li></a>".PHP_EOL;
            }

            $repoHTML .= '    </ul>'.PHP_EOL;
            $repoHTML .= '  </div>'.PHP_EOL;
        } else {
            $numRepos     = 0;
            $percentRepos = 0;
        }//end if

        $repoData .= '{value:'.$percentRepos.',color:"'.$colour.'"},';

        $percent = round($count / $data['total'] * 100, 2);
        if ($numRepos === 0 && $numValues > 4 && $percent < 1) {
            $other += $count;
            continue;
        }

        $count     = number_format($count, 0, '', ',');
        $valsData .= '{value:'.$percent.',color:"'.$colour.'"},';

        $html .= '      <tr title="'.$count.' '.$items.'">'.PHP_EOL;

        if ($value === $data['winner']) {
            $html .= '        <td><div class="winner"><span class="colour-box winner" style="background-color:'.$colour.'"></span></div></td>'.PHP_EOL;
        } else {
            $html .= '        <td><span class="colour-box" style="background-color:'.$colour.'"></span></td>'.PHP_EOL;
        }

        $html .= "        <td>$value</td><td>$percent%</td>".PHP_EOL;
        $html .= '      </tr>'.PHP_EOL;
        $html .= '      <tr title="'.$numRepos.' projects">'.PHP_EOL;
        $html .= '        <td colspan=3 class="preferred"';
        if ($numRepos > 0) {
            $repoResetCode .= "document.getElementById('{$metricid}-{$valueid}-repos').style.display='none';";
            $html          .= ' onclick="var item=document.getElementById(\''.$metricid.'-'.$valueid.'-repos\');if(item.style.display==\'block\'){item.style.display=\'none\';}else{((repoResetCode))item.style.display=\'block\';}"';
        }

        $html .= '>preferred by '.$percentRepos.'% of projects</td>'.PHP_EOL;
        $html .= '      </tr>'.PHP_EOL;

        $trendData .= "{strokeColor:\"$colour\",pointStrokeColor:\"$colour\",pointColor:\"#FFF\",data:[";
        foreach ($data['trends'] as $date => $trendValues) {
            $trendTotal = array_sum($trendValues);
            foreach ($trendValues as $trendValue => $trendCount) {
                if ($trendValue !== $value) {
                    continue;
                }

                $trendData .= round(($trendCount / $trendTotal * 100), 2).',';
            }
        }

        $trendData  = rtrim($trendData, ',');
        $trendData .= ']},';

        $valueNum++;
    }//end foreach

    $html = str_replace('((repoResetCode))', $repoResetCode, $html);

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

    $js .= 'var data = {labels:[';
    $numDates = count($data['trends']);
    $dateStep = ceil($numDates / 4);
    $dateNum  = 1;
    foreach (array_keys($data['trends']) as $date) {
        //if ($dateNum === 1 || $dateNum === $numDates || $dateNum % $dateStep === 0) {
            $time = strtotime($date);
            $js .= '"'.date('d-M', $time).'",';
        //} else {
        //    $js .= '"",';
        //}

        $dateNum++;
        continue;
    }

    $js  = rtrim($js, ',');

    $trendData = rtrim($trendData, ',');
    $js       .= "],datasets:[$trendData]};".PHP_EOL;
    $js       .= 'var c = document.getElementById("chart'.$chartNum.'t").getContext("2d");'.PHP_EOL;
    $js       .= 'new Chart(c).Line(data,trendOptions);'.PHP_EOL;

    if ($other > 0) {
        $percent = round($other / $data['total'] * 100, 2);
        $other   = number_format($other, 0, '', ',');
        $html   .= '      <tr title="'.$other.' '.$items.'">'.PHP_EOL;
        $html   .= '        <td colspan=2>other</td><td colspan=2>'.$percent.'%</td>'.PHP_EOL;
        $html   .= '      </tr>'.PHP_EOL;
        $html   .= '      <tr>'.PHP_EOL;
        $html   .= '        <td colspan=3 class="preferred">&nbsp;</td>'.PHP_EOL;
        $html   .= '      </tr>'.PHP_EOL;
    }

    $html .= '    </table>'.PHP_EOL;
    $html .= '    <p>Based on '.number_format($data['total'], 0, '', ',')." $items in ".$data['total_repos'].' projects</p>'.PHP_EOL;
    $html .= '  </div>'.PHP_EOL;
    $html .= $repoHTML;
    $html .= '</div>'.PHP_EOL;
}//end foreach

$intro  = '<h1>Analysis of Coding Conventions</h1>'.PHP_EOL;
$intro .= '<p><a href="https://github.com/squizlabs/PHP_CodeSniffer">PHP_CodeSniffer</a>, using a custom coding standard and report, was used to record various coding conventions across '.count($resultFiles).' PHP projects. This is the same output produced by the <em>info</em> report, but it has been JSON encoded and modified slightly.</p>'.PHP_EOL;
$intro .= '<p>The graphs for each coding convention show the percentage of each style variation used across all projects (the outer ring) and the percentage of projects that primarily use each variation (the inner ring). Clicking the <em>preferred by</em> line under each style variation will show a list of projects that primarily use it, with the ability to click through and see a coding convention report for the project.</p>'.PHP_EOL;
$intro .= '<p>You can <a href="./results.json">view the raw data</a> used to generate this report, and use it in any way you want.</p>'.PHP_EOL;


$footer = 'Report generated on '.date('r');

$output = file_get_contents(__DIR__.'/_assets/index.html.template');
$output = str_replace('((title))', 'Coding Standards Analysis', $output);
$output = str_replace('((intro))', $intro, $output);
$output = str_replace('((html))', $html, $output);
$output = str_replace('((footer))', $footer, $output);
$output = str_replace('((js))', $js, $output);
$output = str_replace('((assetPath))', '', $output);
file_put_contents(__DIR__.'/index.html', $output);

// Comparison function
function sortMetrics($a, $b) 
{
    if (empty($a['values']) === true) {
        return -1;
    } else if (empty($b['values']) === true) {
        return 1;
    }

    $aPercent = ($a['values'][$a['winner']] / $a['total']);
    $bPercent = ($b['values'][$b['winner']] / $b['total']);
    if ($aPercent < $bPercent) {
        return -1;
    } else {
        return 1;
    }
}//end sortMetrics()

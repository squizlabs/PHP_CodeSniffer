<?php
$resultFiles = array();
$commitids   = array();

$repos = json_decode(file_get_contents(dirname(__FILE__).'/_assets/repos.json'));
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

    //$output = array();
    //$retVal = null;
    //exec($cmd, $output, $retVal);
    //echo implode(PHP_EOL, $output);
    //if (empty($output) === false) {
    //    echo PHP_EOL;
    //}

    $output = array();
    $cmd    = 'cd '.$cloneDir.'; git log -n 1 --pretty=format:%H;';
    exec($cmd, $output);
    $commitids[$repo->url] = $output[0];
    echo 'At commit '.$output[0].PHP_EOL;

    //$reportFile = $repoDir.'/results/'.date('Y-m-d').'.json';
    //if (file_exists($reportFile) === false) {
    //    $checkDir   = $cloneDir.'/'.$repo->path;
    //    $reportPath = dirname(__FILE__).'/_assets/PHPCSInfoReport.php';
    //    $cmd        = 'phpcs --standard='.dirname(__FILE__).'/_assets/ruleset.xml --extensions=php,inc -d memory_limit=256M';
    //    $cmd       .= ' --ignore=*/tests/*,'.$repo->ignore;
    //    $cmd       .= " --report=$reportPath --report-file=$reportFile $checkDir";

    //    echo 'Running PHPCS'.PHP_EOL."\t=> $cmd".PHP_EOL;

    //    exec($cmd);
    //    exec("cp $reportFile $repoDir/results/latest.json");
    //} else {
    //    echo 'Skipping PHPCS step'.PHP_EOL;
    //}

    $resultFiles[] = $repoDir.'/results/latest.json';
    echo str_repeat('-', 30).PHP_EOL;
}//end foreach

// Imports $metricText variable.
require_once dirname(__FILE__).'/_assets/metricText.php';
$colours = array(
            '#4D5360',
            '#D4CCC5',
            '#9D9B7F',
            '#949FB1',
            '#7D4F6D',
            '#584A5E',
           );

$totals = array();
foreach ($resultFiles as $file) {
    $parts = explode('/', $file);
    $num   = count($parts);
    $repo  = $parts[($num - 4)].'/'.$parts[($num - 3)];

    echo "Processing result file for $repo: $file".PHP_EOL;
    $results = json_decode(file_get_contents($file), true);
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

        // Needed for sorting this result set later on.
        $results[$metric]['winner'] = $winner;

        if (isset($totals[$metric]['repos'][$winner]) === false) {
            $totals[$metric]['repos'][$winner] = array();
        }

        $totals[$metric]['repos'][$winner][] = $repo;
        $totals[$metric]['total_repos']++;

    }//end foreach

    $html = '';
    $js   = 'var valOptions = {animation:false,segmentStrokeWidth:1,percentageInnerCutout:60};'.PHP_EOL;

    uasort($results, 'sortMetrics');
    $chartNum = 0;
    foreach ($results as $metric => $data) {
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
        $html .= '<h2 id="'.$id.'" title="'.implode(',', $data['sniffs']).'">'.$metric.'</h2><div class="metric"><p>'.$description.'</p>'.PHP_EOL;
        $html .= '<canvas class="chart-value" id="chart'.$chartNum.'" width="400" height="400"></canvas>'.PHP_EOL;
        $html .= '<div class="chart-data"><table>';

        $valsData  = 'var data = [';
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

            $html .= '<tr title="'.$count.' '.$items.'"><td><span class="colour-box" style="background-color:'.$colour.'"></span></td>';
            $html .= "<td>$value</td><td>$percent%</td></tr>";
            $valueNum++;
        }//end foreach

        $valsData  = substr($valsData, 0, -1);
        $valsData .= ']'.PHP_EOL;

        $js .= $valsData;
        $js .= 'var c = document.getElementById("chart'.$chartNum.'").getContext("2d");'.PHP_EOL;
        $js .= 'new Chart(c).Doughnut(data,valOptions);'.PHP_EOL;

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
    $intro .= '<p><a href="https://github.com/squizlabs/PHP_CodeSniffer">PHP_CodeSniffer</a>, using a custom coding standard and report, was used to record various coding conventions for this project. The graphs for each coding convention show the percentage of each style variation used throughout the project.</p><p>You can <a href="https://raw.github.com/squizlabs/PHP_CodeSniffer/gh-pages/analysis/'.$repo.'/results/latest.json">view the raw data</a> used to generate this report, and use it in any way you want.</p>'.PHP_EOL;
    $intro .= '<p>You can also <a href="../../index.html">view a combined analysis</a> that covers '.count($resultFiles).' PHP projects</p>'.PHP_EOL;

    $commitid = $commitids[$repo];
    $footer = 'Report generated on '.date('r')."<br/>Using master branch of <a href=\"https://github.com/$repo\">$repo</a> @ commit <a href=\"https://github.com/$repo/commit/$commitid\">$commitid";

    $output = file_get_contents(dirname(__FILE__).'/_assets/index.html.template');
    $output = str_replace('((title))', $repo.' - Coding Standards Analysis', $output);
    $output = str_replace('((intro))', $intro, $output);
    $output = str_replace('((html))', $html, $output);
    $output = str_replace('((footer))', $footer, $output);
    $output = str_replace('((js))', $js, $output);
    $output = str_replace('((assetPath))', '../../', $output);
    file_put_contents(dirname(__FILE__).'/'.$repo.'/index.html', $output);

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
$js   = 'var valOptions = {animation:false,segmentStrokeWidth:1,percentageInnerCutout:60};'.PHP_EOL;
$js  .= 'var repoOptions = {animation:false,segmentStrokeWidth:1,percentageInnerCutout:90};'.PHP_EOL;

uasort($totals, 'sortMetrics');
$chartNum = 0;
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
    $metricid = str_replace(' ', '-', strtolower($metric));

    $html .= '<h2 id="'.$metricid.'" title="'.implode(',', $data['sniffs']).'">'.$metric.'</h2>'.PHP_EOL;
    $html .= '<div class="metric">'.PHP_EOL."  <p>$description</p>".PHP_EOL;
    $html .= '  <canvas class="chart-value" id="chart'.$chartNum.'" width="400" height="400"></canvas>'.PHP_EOL;
    $html .= '  <canvas class="chart-repo" id="chart'.$chartNum.'r" width="240" height="240"></canvas>'.PHP_EOL;
    $html .= '  <div class="chart-data">'.PHP_EOL;
    $html .= '    <table>'.PHP_EOL;

    $valsData      = 'var data = [';
    $repoData      = 'var data = [';
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

            $repoHTML .= '  <div id="'.$metricid.'-'.$valueid.'-repos" class="repo-data">'.PHP_EOL;
            $repoHTML .= "    <p><span onclick=\"document.getElementById('{$metricid}-{$valueid}-repos').style.display='none';\" class=\"close\">[close]</span><strong>$numRepos projects prefer <em>$value</em></strong></p>".PHP_EOL;
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
        }

        $repoData .= '{value:'.$percentRepos.',color:"'.$colour.'"},';

        $percent = round($count / $data['total'] * 100, 2);
        if ($numRepos === 0 && $numValues > 4 && $percent < 1) {
            $other += $count;
            continue;
        }

        $count     = number_format($count, 0, '', ',');
        $valsData .= '{value:'.$percent.',color:"'.$colour.'"},';

        $html .= '      <tr title="'.$count.' '.$items.'">'.PHP_EOL;
        $html .= '        <td><span class="colour-box" style="background-color:'.$colour.'"></span></td>'.PHP_EOL;
        $html .= "        <td>$value</td><td>$percent%</td>".PHP_EOL;
        $html .= '      </tr>'.PHP_EOL;
        $html .= '      <tr title="'.$numRepos.' projects">'.PHP_EOL;
        $html .= '        <td colspan=3 class="preferred"';
        if ($numRepos > 0) {
            $repoResetCode .= "document.getElementById('{$metricid}-{$valueid}-repos').style.display='none';";
            $html          .= ' onclick="((repoResetCode))document.getElementById(\''.$metricid.'-'.$valueid.'-repos\').style.display=\'block\';"';
        }

        $html .= '>preferred by '.$percentRepos.'% of projects</td>'.PHP_EOL;
        $html .= '      </tr>'.PHP_EOL;

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
$intro .= '<p>You can <a href="https://raw.github.com/squizlabs/PHP_CodeSniffer/gh-pages/analysis/_results/latest.json">view the raw data</a> used to generate this report, and use it in any way you want.</p>'.PHP_EOL;


$footer = 'Report generated on '.date('r');

$output = file_get_contents(dirname(__FILE__).'/_assets/index.html.template');
$output = str_replace('((title))', 'Coding Standards Analysis', $output);
$output = str_replace('((intro))', $intro, $output);
$output = str_replace('((html))', $html, $output);
$output = str_replace('((footer))', $footer, $output);
$output = str_replace('((js))', $js, $output);
$output = str_replace('((assetPath))', '', $output);
file_put_contents(dirname(__FILE__).'/index.html', $output);

// Comparison function
function sortMetrics($a, $b) 
{
    $aPercent = ($a['values'][$a['winner']] / $a['total']);
    $bPercent = ($b['values'][$b['winner']] / $b['total']);
    if ($aPercent < $bPercent) {
        return -1;
    } else {
        return 1;
    }
}//end sortMetrics()

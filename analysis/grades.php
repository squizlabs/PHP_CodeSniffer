<?php
$grades       = array();
$gradeFiles   = $resultFiles;
$gradeFiles[] = __DIR__.'/results.json';

foreach ($gradeFiles as $file) {
    $results = json_decode(file_get_contents($file), true);
    if (isset($results['project']) === false) {
        $repo    = null;
        $metrics = $results;
    } else {
        $repo    = $results['project']['path'];
        $metrics = $results['metrics'];
        #echo "\t=> Processing consistency data for $repo\n";
    }

    $consistency = array(
                    'pos'    => 0,
                    'neg'    => 0,
                    'scores' => array(),
                   );

    foreach ($metrics as $metric => $data) {
        if (empty($data['values']) === true || $data['total'] === 0) {
            continue;
        }

        // Skip line length as it is more informative only.
        if ($metric === 'Line length') {
            continue;
        }

        // Skip metrics that every project agrees on 100% of the time.
        $winner = $totals[$metric]['winner'];
        $score  = round(($totals[$metric]['values'][$winner] / $totals[$metric]['total'] * 100), 2);
        if ($score === 100) {
            continue;
        }

        foreach ($data['values'] as $value => $count) {
            if ($value === $data['winner']) {
                $consistency['pos']     += $count;
                $consistency['scores'][] = round(($count / $data['total'] * 100), 2);
            } else {
                $consistency['neg'] += $count;
            }
        }
    }//end foreach

    $score   = round(($consistency['pos'] / ($consistency['pos'] + $consistency['neg']) * 100), 2);
    $average = round(array_sum($consistency['scores']) / count($consistency['scores']), 2);
    #echo "\t\tpositive: ".$consistency['pos'].', negative: '.$consistency['neg'].", score: $score, average: $average\n";

    if ($score >= 99) {
        $grade  = 'A+';
        $colour = 'green';
        $img    = 'a-plus.svg';
    } else if ($score >= 96) {
        $grade  = 'A';
        $colour = 'green';
        $img    = 'a.svg';
    } else if ($score >= 93) {
        $grade  = 'A-';
        $colour = 'green';
        $img    = 'a-minus.svg';
    } else if ($score >= 90) {
        $grade  = 'B+';
        $colour = 'blue';
        $img    = 'b-plus.svg';
    } else if ($score >= 87) {
        $grade  = 'B';
        $colour = 'blue';
        $img    = 'b.svg';
    } else if ($score >= 84) {
        $grade  = 'B-';
        $colour = 'blue';
        $img    = 'b-minus.svg';
    } else if ($score >= 81) {
        $grade  = 'C+';
        $colour = 'yellow';
        $img    = 'c-plus.svg';
    } else if ($score >= 78) {
        $grade  = 'C';
        $colour = 'yellow';
        $img    = 'fc.svg';
    } else if ($score >= 75) {
        $grade  = 'C-';
        $colour = 'yellow';
        $img    = 'c-minus.svg';
    } else if ($score >= 72) {
        $grade  = 'D+';
        $colour = 'orange';
        $img    = 'd-plus.svg';
    } else if ($score >= 69) {
        $grade  = 'D';
        $colour = 'orange';
        $img    = 'd.svg';
    } else if ($score >= 66) {
        $grade  = 'D-';
        $colour = 'orange';
        $img    = 'd-minus.svg';
    } else {
        $grade  = 'F';
        $colour = 'red';
        $img    = 'f.svg';
    }//end if

    #echo "$repo: $score ($grade)\n";

    $grades[$repo] = array(
                      'score'  => $score,
                      'grade'  => $grade,
                      'colour' => $colour,
                      'img'    => $img,
                     );
}//end foreach

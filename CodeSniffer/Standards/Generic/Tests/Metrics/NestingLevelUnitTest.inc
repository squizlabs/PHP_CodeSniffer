<?php

function nestingOne()
{
    if ($condition) {
        echo 'hi';
    }
}

function nestingFive()
{
    if ($condition) {
        echo 'hi';
        switch ($condition)
        {
            case '1':
                if ($condition === '1') {
                    if ($cond) {
                        echo 'hi';
                    }
                }
            break;
        }
    }
}

function nestingSix()
{
    if ($condition) {
        echo 'hi';
        switch ($condition)
        {
            case '1':
                if ($condition === '1') {
                    if ($cond) {
                        foreach ($conds as $cond) {
                            echo 'hi';
                        }
                    }
                }
            break;
        }
    }
}

function nestingTen()
{
    if ($condition) {
        echo 'hi';
        switch ($condition)
        {
            case '1':
                if ($condition === '1') {
                    if ($cond) {
                        switch ($cond) {
                            case '1':
                                if ($cond === '1') {
                                    foreach ($conds as $cond) {
                                        if ($cond === 'hi') {
                                            echo 'hi';
                                        }
                                    }
                                }
                            break;
                        }
                    }
                }
            break;
        }
    }
}

function nestingEleven()
{
    if ($condition) {
        echo 'hi';
        switch ($condition)
        {
            case '1':
                if ($condition === '1') {
                    if ($cond) {
                        switch ($cond) {
                            case '1':
                                if ($cond === '1') {
                                    foreach ($conds as $cond) {
                                        if ($cond === 'hi') {
                                            if ($cond !== 'bye') {
                                                echo 'hi';
                                            }
                                        }
                                    }
                                }
                            break;
                        }
                    }
                }
            break;
        }
    }
}

?>

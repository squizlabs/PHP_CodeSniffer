<?php

/* testStartPoint */
/* condition 0: namespace */
namespace Conditions\HorribleCode {

    /* condition 1: if */
    if (!function_exists('letsGetSerious') ) {

        /* condition 2: function */
        function letsGetSerious() {

            /* condition 3-1: if */
            if (isset($loadthis)) {
                doing_something();
            /* condition 3-2: else */
            } else {

                /* condition 4: if */
                if (!class_exists('SeriouslyNestedClass')) {

                    /* condition 5: nested class */
                    class SeriouslyNestedClass extends SomeOtherClass {

                        /* condition 6: class method */
                        public function SeriouslyNestedMethod(/* testSeriouslyNestedMethod */ $param) {

                            /* condition 7: switch */
                            switch ($param) {

                                /* condition 8a: case */
                                case 'testing':

                                    /* condition 9: while */
                                     while ($a < 10 ) {

                                        /* condition 10-1: if */
                                        if ($a === $b) {

                                            /* condition 11-1: nested anonymous class */
                                            return new class() {

                                                /* condition 12: nested anonymous class method */
                                                private function DidSomeoneSayNesting() {

                                                    /* condition 13: closure */
                                                    $c = function() {
                                                        /* testDeepestNested */
                                                        return 'closure';
                                                    };
                                                }
                                            };
                                        /* condition 10-2: elseif */
                                        } elseif($bool) {
                                            echo 'hello world';
                                        }

                                        /* condition 10-3: foreach */
                                        foreach ($array as $k => $v) {

                                            /* condition 11-2: try */
                                            try {
                                                --$k;

                                            /* condition 11-3: catch */
                                            } catch (Exception $e) {
                                                /* testInException */
                                                echo 'oh darn';
                                            /* condition 11-4: finally */
                                            } finally {
                                                return true;
                                            }
                                        }

                                        $a++;
                                    }
                                    break;

                                /* condition 8b: default */
                                default:
                                    /* testInDefault */
                                    $return = 'nada';
                                    return $return;
                            }
                        }
                    }
                }
            }
        }
    }
}

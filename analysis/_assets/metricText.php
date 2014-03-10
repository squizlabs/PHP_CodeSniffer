<?php
$metricText = array(
               'Array keyword case'                                => array(
                                                                       'description' => 'When defining an array, how is the <em>array</em> keyword written?',
                                                                       'items'       => 'array definitions',
                                                                      ),
               'One class per file'                                => array(
                                                                       'description' => 'Is each class defined in a file by itself?',
                                                                       'items'       => 'classes',
                                                                      ),
               'Class defined in namespace'                        => array(
                                                                       'description' => 'Is each class defined in a namespace of at least 1 level?',
                                                                       'items'       => 'classes',
                                                                      ),
               'CamelCase class name'                              => array(
                                                                       'description' => 'Are class names defined using CamelCase?',
                                                                       'items'       => 'classes',
                                                                      ),
               'Control structure defined inline'                  => array(
                                                                       'description' => 'Are control structures defined inline (without braces)?',
                                                                       'items'       => 'control structures',
                                                                      ),
               'Spaces after control structure open parenthesis'   => array(
                                                                       'description' => 'How many spaces follow the opening parenthesis of a control structure?',
                                                                       'items'       => 'control structures',
                                                                      ),
               'Spaces before control structure close parenthesis' => array(
                                                                       'description' => 'How many spaces precede the closing parenthesis of a control structure?',
                                                                       'items'       => 'control structures',
                                                                      ),
               'Use of ELSE IF or ELSEIF'                          => array(
                                                                       'description' => 'Are elseif statements defined using <em>elseif</em> or <em>else if</em>?',
                                                                       'items'       => 'elseif statements',
                                                                      ),
               'EOL char'                                          => array(
                                                                       'description' => 'What character is used for line breaks?',
                                                                       'items'       => 'files',
                                                                      ),
               'Line length'                                       => array(
                                                                       'description' => 'Including whitespace and comments, how many characters are in non-empty code lines?',
                                                                       'items'       => 'lines',
                                                                       'sort'        => SORT_NUMERIC,
                                                                      ),
               'Lowercase filename'                                => array(
                                                                       'description' => 'Are PHP filenames lowercase?',
                                                                       'items'       => 'files',
                                                                      ),
               'Declarations and side effects mixed'               => array(
                                                                       'description' => 'Do files both declare symbols (classes, functions, constants, etc.) and cause side-effects (e.g. generate output, change .ini settings, etc.)?',
                                                                       'items'       => 'files',
                                                                      ),
               'Number of newlines at EOF'                         => array(
                                                                       'description' => 'How many newline characters appear at the end of the file?',
                                                                       'items'       => 'files',
                                                                      ),
               'File extension for class files'                    => array(
                                                                       'description' => 'For files that contain classes, what extension does the file use?',
                                                                       'items'       => 'class files',
                                                                      ),
               'PHP closing tag at EOF'                            => array(
                                                                       'description' => 'Is there a PHP closing tag at the end of the file?',
                                                                       'items'       => 'files',
                                                                      ),
               'File extension for non-class files'                => array(
                                                                       'description' => 'For files that do not contain classes, what extension does the file use?',
                                                                       'items'       => 'non-class files',
                                                                      ),
               'Multiple statements on same line'                  => array(
                                                                       'description' => 'Are multiple statements defined on the same line of code?',
                                                                       'items'       => 'statements',
                                                                      ),
               'Adjacent assignments aligned'                      => array(
                                                                       'description' => 'For a block of assignments, are the assignment operators (equals, plus-equals etc.) aligned?',
                                                                       'items'       => 'assignment blocks',
                                                                      ),
               'Spacing after cast statement'                      => array(
                                                                       'description' => 'How many spaces are there between a cast statement and the variable?',
                                                                       'items'       => 'cast statements',
                                                                      ),
               'Function opening brace placement'                  => array(
                                                                       'description' => 'Is the opening brace of a function on the same line as the <em>function</em> keyword or the next line?',
                                                                       'items'       => 'functions',
                                                                      ),
               'CamelCase method name'                             => array(
                                                                       'description' => 'Are class method names defined using CamelCase?',
                                                                       'items'       => 'methods',
                                                                      ),
               'CamelCase function name'                           => array(
                                                                       'description' => 'Are global function names defined using CamelCase?',
                                                                       'items'       => 'global functions',
                                                                      ),
               'Constant name case'                                => array(
                                                                       'description' => 'What case are user-defined constants defined in?',
                                                                       'items'       => 'constants',
                                                                      ),
               'PHP short open tag used'                           => array(
                                                                       'description' => 'Is the PHP short open tag syntax used?',
                                                                       'items'       => 'open tags',
                                                                      ),
               'PHP keyword case'                                  => array(
                                                                       'description' => 'What case are PHP keywords (function, echo, foreach etc.) written in?',
                                                                       'items'       => 'keywords',
                                                                      ),
               'PHP constant case'                                 => array(
                                                                       'description' => 'What case are PHP constants (true, false, null) written in?',
                                                                       'items'       => 'constants',
                                                                      ),
               'Spacing before string concat'                      => array(
                                                                       'description' => 'How many spaces precede the string concatenation operator?',
                                                                       'items'       => 'operators',
                                                                      ),
               'Spacing after string concat'                       => array(
                                                                       'description' => 'How many spaces follow the string concatenation operator?',
                                                                       'items'       => 'operators',
                                                                      ),
               'Brackets around echoed strings'                    => array(
                                                                       'description' => 'Are parenthesis placed around <em>echo</em>\'d strings?',
                                                                       'items'       => 'echo statements',
                                                                      ),
               'Space before operator'                             => array(
                                                                       'description' => 'How many spaces precede comparison, arithmetic and assignment operators?',
                                                                       'items'       => 'operators',
                                                                      ),
               'Space after operator'                              => array(
                                                                       'description' => 'How many spaces precede comparison, arithmetic and assignment operators?',
                                                                       'items'       => 'operators',
                                                                      ),
               'Line indent'                                       => array(
                                                                       'description' => 'Are lines indented using spaces or tabs?',
                                                                       'items'       => 'lines',
                                                                      ),
               'Spacing before object operator'                    => array(
                                                                       'description' => 'How many spaces precede the object operator (->)?',
                                                                       'items'       => 'operators',
                                                                      ),
               'Spacing after object operator'                     => array(
                                                                       'description' => 'How many spaces follow the object operator (->)?',
                                                                       'items'       => 'operators',
                                                                      ),
              );

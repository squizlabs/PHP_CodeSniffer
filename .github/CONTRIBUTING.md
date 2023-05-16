Contributing
-------------

Thank you for your interest in contributing to PHP_CodeSniffer!


## Reporting Bugs

Please search the [open issues](https://github.com/squizlabs/PHP_CodeSniffer/issues) to see if your issue has been reported already and if so, comment in that issue if you have additional information, instead of opening a new one.

Before reporting a bug, you should check what sniff an error is coming from.
Running `phpcs` with the `-s` flag will show the name of the sniff for each error.

If the error code starts with anything other than `Generic`, `MySource`, `PEAR`, `PSR1`, `PSR2`, `PSR12`, `Squiz` or `Zend`, the error is likely coming from an external PHP_CodeSniffer standard.
**Please report bugs for externally maintained sniffs to the appropriate repository.**

Bug reports containing a minimal code sample which can be used to reproduce the issue are highly appreciated as those are most easily actionable.

:point_right: Reports which only include a _screenshot_ of the code will be closed without hesitation as not actionable.


### Reporting Security Issues

PHP_CodeSniffer is a developer tool and should generally not be used in a production environment.

Having said that, responsible disclosure of security issues is highly appreciated.
Issues can be reported privately to the maintainers by opening a [Security vulnerability report](https://github.com/squizlabs/PHP_CodeSniffer/security/advisories/new).


### Support/Questions About Using PHP_CodeSniffer

Please read the [documentation in the wiki](https://github.com/squizlabs/PHP_CodeSniffer/wiki) before opening an issue with a support question.

The [discussion forum](https://github.com/squizlabs/PHP_CodeSniffer/discussions) or [StackOverflow](https://stackoverflow.com/questions/tagged/phpcodesniffer) are the appropriate places for support questions.


## Contributing Without Writing Code

### Issue Triage

We welcome issue triage.

Issue triage is the action of verifying a reported issue is reproducible and is actually an issue.
It includes checking whether the issue is something which should be fixed in **_this_** repository.

To find issues which need triage, look for [issues without labels](https://github.com/squizlabs/PHP_CodeSniffer/issues?q=is%3Aopen+is%3Aissue+no%3Alabel) or issues with the ["Status: triage"](https://github.com/squizlabs/PHP_CodeSniffer/labels/Status%3A%20triage) label.

#### Typical issue triage tasks:
* Verify whether the issue is reproducible with the given information.
* Ask for additional information if it is not.
* If you find the issue is reported to the wrong repo, ask the reporter to report it to the correct external standard repo and suggest closing the issue.

Additionally for older issues:
* Check whether an issue still exists or has been fixed in `master` since the issue was initially reported.
* If it has been fixed, document (in a comment) which commit/PR was responsible for fixing the issue.


### Testing Open Pull Requests

Testing pull requests to verify they fix the issue they are supposed to fix without side-effects is an important task.

To get access to a PHPCS version which includes the patch from a pull request, you can:
* Either use a git clone of the PHP_CodeSniffer repository and check out the PR.
* Or download the PHAR file(s) for the PR, which is available from the [Test workflow](https://github.com/squizlabs/PHP_CodeSniffer/actions/workflows/test.yml) as an artifact of the workflow run.
    The PHAR files can be found on the summary page of the test workflow run for the PR.
    If the workflow has not been run (yet), the PHAR artifact may not be available (yet).

#### Typical test tasks:
* Verify that the patch solves the originally reported problem.
* Verify that the tests added in the PR fail without the fix and pass with the fix.
* For a fix for false negatives: verify that the correct error message(s) are thrown by the patched code.
* Run the patched PHPCS version against real codebases to see if the fix creates any side-effects (new false positives/false negatives).


### Writing sniff documentation

Sniffs in PHP_CodeSniffer should preferably be accompanied by documentation. There is currently still a lot of documentation missing.

Sniff documentation is provided via XML files in the standard's `Docs` directory and is available to end-users via the command-line and/or can be compiled into an HTML or Markdown file.

To see an example of some of the available documentation, run:
```bash
phpcs --standard=PSR12 --generator=Text
```

Pull requests to update existing documentation, or to add documentation for sniffs which currently don't have any, are welcome.

For the documentation to be recognized, the naming conventions have to be followed.

For example, for the sniff named `Generic.NamingConventions.ConstructorName`:
* The sniff lives in the `src/Standards/Generic/Sniffs/NamingConventions/ConstructorNameSniff.php` file.
* The associated documentation should be in a `src/Standards/Generic/Docs/NamingConventions/ConstructorNameStandard.xml` file.

The XML files consist of several parts:
* The `title` attribute in the `<documentation>` tag should generally reflect the name of the sniff.
* Each XML file can contain multiple `<standard>` blocks.
* Each `<standard>` block can be accompanied by one or more `<code_comparison>` blocks.
* Each code comparison block should have two `<code>` blocks, the first one showing "valid" code, the second one showing "invalid" code.

Some guidelines to get you started:
* Keep it as simple as possible.
* When a sniff shows multiple different errors/warnings, it is recommended to have a separate `<standard>` block for each error/warning.
* The title of a "good" code sample (on the left) should start with `Valid:`.
* The title of a "bad" code sample (on the right) should start with `Invalid:`.
* Don't use example code which can be traced back to a specific project.
* Each line within the code sample should be max 48 characters.
* Code used in code samples should be valid PHP.
* To highlight the "good" and the "bad" bits in the code examples, surround those bits with `<em> ...</em>` tags.
    These will be removed automatically when using the text generator, but ensure highlighting of the code in Markdown/HTML.
* The indentation in the XML file should use spaces only. Four spaces for each indent.

Make sure to test the documentation before submitting a PR by running:
```bash
phpcs --standard=StandardName --generator=Text --sniffs=StandardName.Category.SniffName
```


## Contributing With Code

### Requesting/Submitting New Features

Ideally, start by [opening an issue](https://github.com/squizlabs/PHP_CodeSniffer/issues/new/choose) to check whether the feature is something which is suitable to be included in PHP_CodeSniffer.

It's possible that a feature may be rejected at an early stage, and it's better to find out before you write the code.

It is also good to discuss potential different implementation options ahead of time and get guidance for the preferred option from the maintainers.

Note: There may be an issue or PR open already. If so, please join the discussion in that issue or PR instead of opening a duplicate issue/PR.

> Please note: Auto-fixers will only be accepted for "non-risky" sniffs.
> If a fixer could cause parse errors or a change in the behaviour of the scanned code, the fixer will **NOT** be accepted in PHP_CodeSniffer and may be better suited to an external standard.


### Getting started

1. Fork/clone the repository.
2. Run `composer install`.
    When installing on PHP >= 8.0, use `composer install --ignore-platform-req=php+`.
3. Create a new branch off the `master` branch to hold your patch.
    If there is an open issue associated with your patch, including the issue number in the branch name is good practice.


### While working on a patch

Please make sure your code conforms to the PHPCS coding standard, is covered by tests and that all the PHP_CodeSniffer unit tests still pass.

Also, please make sure your code is compatible with the PHP_CodeSniffer minimum support PHP version, PHP 5.4.

To help you with this, a number of convenience scripts are available:
* `composer check-all` will run the `cs` + `test` + `check-package` checks in one go.
* `composer cs` will check for code style violations.
* `composer cbf` will run the autofixers for code style violations.
* `composer test` will run the unit tests (only works when on PHP < 8.1).
* `composer test-php8` will run the unit tests when you are working on PHP 8.1+.
    Please note that using a `phpunit.xml` overload config file will not work with this script!
* `composer coverage` will run the unit tests with code coverage (only works when on PHP < 8.1).
    Note: you may want to use a custom `phpunit.xml` overload config file to tell PHPUnit where to place an HTML report.
    Alternative run it like so: `composer coverage -- --coverage-html /path/to/report-dir/` to specify the location for the HTML report on the command line.
* `composer check-package` will check that any and all files are listed in the `package.xml` file.
* `composer build` will build the phpcs.phar and phpcbf.phar files.

N.B.: You can ignore any skipped tests as these are for external tools.


### Writing tests

Tests for the PHP_CodeSniffer engine can be found in the `tests/Core` directory.
Tests for individual sniffs can be found in the `src/Standards/[StandardName]/Tests/[Category]/` directory.

Tests will, in most cases, consist of a set of related files and follow strict naming conventions.

For example, for the sniff named `Generic.NamingConventions.ConstructorName`:
* The sniff lives in the `src/Standards/Generic/Sniffs/NamingConventions/` directory.
* The tests live in the `src/Standards/Generic/Tests/NamingConventions/` directory.
* The tests consist of two files:
    - `src/Standards/Generic/Tests/NamingConventions/ConstructorNameUnitTest.inc` which is the test _case_ file containing code for the sniff to analyse.
    - `src/Standards/Generic/Tests/NamingConventions/ConstructorNameUnitTest.php` which is the test file, containing two methods, `getErrorList()` and `getWarningList()`, which should each return an array with as the keys the line number in the test _case_ file and as the values the number of errors or warnings which are expected on each line.
        Only lines on which errors/warnings are expected need to be included in the lists. All other lines will automatically be set to expect no errors and no warnings.

#### Multiple test case files

At times, one test _case_ file is not enough, for instance when the sniff needs to behave differently depending on whether or code is namespaced or not, or when a sniff needs to check something at the top of a file.

The test framework allows for multiple test _case_ files.
In that case, the files should be numbered and the number should be placed between the file name and the extension.

So for the above example, the `src/Standards/Generic/Tests/NamingConventions/ConstructorNameUnitTest.inc` would be renamed to `src/Standards/Generic/Tests/NamingConventions/ConstructorNameUnitTest.1.inc` and additional test case files should be numbered sequentially like `src/Standards/Generic/Tests/NamingConventions/ConstructorNameUnitTest.2.inc`, `src/Standards/Generic/Tests/NamingConventions/ConstructorNameUnitTest.3.inc` etc.

The `getErrorList()` and the `getWarningList()` methods will receive an optional `$testFile=''` parameter with the file name of the file being scanned - `ConstructorNameUnitTest.2.inc` - and should return the appropriate array for each file.

#### Testing fixers

If a sniff contains errors/warnings which can be auto-fixed, these fixers should also be tested.

This is done by adding an additional test _case_ file with an extra `.fixed` extension for each test _case_ file which expects fixes.

For example, if the above `Generic.NamingConventions.ConstructorName` would contain an auto-fixer, there should be an additional `src/Standards/Generic/Tests/NamingConventions/ConstructorNameUnitTest.inc.fixed` file containing the code as it is expected to be after the fixer has run.

The test framework will compare the actual fixes made with the expected fixes and will fail the tests if these don't match.


## Licensing

By contributing code to this repository, you agree to license your code for use under the [BSD-3-Clause license](https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt).

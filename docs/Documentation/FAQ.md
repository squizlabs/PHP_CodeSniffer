## Does PHP_CodeSniffer perform any code coverage or unit testing?
No. PHP_CodeSniffer is not a tool for testing that your PHP application works correctly. All PHP_CodeSniffer will do is ensure your PHP code meets the standards that you are following.

## My code is fine! Why do I need PHP_CodeSniffer?
Maybe you don't, but if you want to ensure you adhere to a set of coding standards, PHP_CodeSniffer is a quick and easy way to do that. PHP_CodeSniffer is a replacement for the more manual task of checking coding standards in code reviews. With PHP_CodeSniffer, you can reserve code reviews for the checking of code correctness.

Coding standards are a good thing. They will make your code easier to read and maintain, especially when multiple developers are working on the same application. Consider using coding standards if you don't already.

## Does PHP_CodeSniffer parse my code to ensure it will execute?
No. PHP_CodeSniffer does not actually parse your code, and so cannot accurately tell if your code contains parse errors. PHP_CodeSniffer does know about some parse errors and will warn you if it finds code that it is unable to sniff correctly due to a suspected parse error. However, as there is no actual parsing taking place, PHP_CodeSniffer may return an incorrect number of errors when checking code that does contain parse errors.

You can easily check for parse errors in a file using the PHP command line interface and the `-l` (lowercase L) option.

    $ php -l /path/to/code/myfile.inc
    No syntax errors detected in /path/to/code/myfile.inc
    
## I don't agree with your coding standards! Can I make PHP_CodeSniffer enforce my own?
Yes. At its core, PHP_CodeSniffer is just a framework for enforcing coding standards. PHP_CodeSniffer is released with some sample coding standards to help developers get started on projects where there is no standard defined. If you want to write your own standard, read the tutorial on creating coding standards.

## How come PHP_CodeSniffer reported errors, I fixed them, now I get even more?
Sometimes, errors mask the existence of other errors, or new errors are created as you fix others. For example, PHP_CodeSniffer might tell you that an inline IF statement needs to be defined with braces. Once you make this change, PHP_CodeSniffer may report that the braces you added are not correctly aligned.

Always run PHP_CodeSniffer until you get a passing result. Once you've made the changes PHP_CodeSniffer recommends, run PHP_CodeSniffer again to ensure no new errors have been added.

## What does PHP_CodeSniffer use to tokenize my code?
For PHP files, PHP_CodeSniffer uses [PHP's inbuilt tokenizer functions](http://www.php.net/tokenizer) to parse your code. It then modifies that output to include much more data about the file, such as matching function braces to function keywords.

For all other file types, PHP_CodeSniffer includes a custom tokenizer that either makes use of PHP's inbuilt tokenizer or emulates it. In both cases, the token array must be checked and changed manually before all the standard PHP_CodeSniffer matching rules are applied, making tokenizing a bit slower for these file types.
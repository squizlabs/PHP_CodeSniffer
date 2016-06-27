# Contributing

Before you contribute code to Symplify\PHP7_CodeSniffer, please make sure it conforms to the PSR-2 coding standard and that the PHP\_CodeSniffer unit tests still pass. The easiest way to contribute is to work on a checkout of the repository, or your own fork, rather than an installed PEAR version.

If you do this, you can run the following commands to check if everything is ready to submit:

```bash
php bin/phpcs
```

Which should give you no output, indicating that there are no coding standard errors. And then:

```bash
vendor/bin/phpunit
```

Which should give you no failures or errors. You can ignore any skipped tests as these are for external tools.

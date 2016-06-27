# Contributing

Before you contribute code to Symplify\PHP7_CodeSniffer, please make sure **it conforms to the PSR-2 coding standard**.

```bash
php bin/phpcs
```

Which should give you no output, indicating that there are no coding standard errors.

**And that all tests passes**:

```bash
vendor/bin/phpunit
```

Which should give you no failures or errors. You can ignore any skipped tests as these are for external tools.

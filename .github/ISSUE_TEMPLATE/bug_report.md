---
name: Bug report
about: Create a report to help us improve
title: ''
labels: 'Status: triage', 'Type: bug'
assignees: ''

---

<!--
Before reporting a sniff related bug, please check the error code using `phpcs -s`.

If the error code starts with anything other than `Generic`, `MySource`, `PEAR`,
`PSR1`, `PSR2`, `PSR12`, `Squiz` or `Zend`, the error is likely coming from an
external PHP_CodeSniffer standard.

Please report bugs for externally maintained sniffs to the appropriate external
standard repository (not here).
-->

**Describe the bug**
A clear and concise description of what the bug is.

**Code sample**
```php
echo "A short code snippet that can be used to reproduce the bug";
```

**Custom ruleset**
```xml
<?xml version="1.0"?>
<ruleset name="My Custom Standard">
  <description>If you are using a custom ruleset, please enter it here.</description>
</ruleset>
```

**To reproduce**
Steps to reproduce the behavior:
1. Create a file called `test.php` with the code sample above...
2. Run `phpcs test.php ...`
3. See error message displayed
```
PHPCS output here
```

**Expected behavior**
A clear and concise description of what you expected to happen.

**Versions (please complete the following information):**
 - OS: [e.g., Windows 10, MacOS 10.15]
 - PHP: [e.g., 7.2, 7.4]
 - PHPCS: [e.g., 3.5.5, master]
 - Standard: [e.g., PSR2, PSR12, Squiz]

**Additional context**
Add any other context about the problem here.

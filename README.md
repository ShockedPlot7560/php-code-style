# PHP CS Fixer: custom fixers

A set of custom fixers for [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)

## Fixers 

### Shockedplot7560/no_space_before_braces_on_control

Open braces for control structures MUST NOT be preceded by a space.

```diff
<?php

- if (true) {
+ if (true){
    echo 'Hello world';
}
```
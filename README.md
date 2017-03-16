# semantify.it api for php #

api can be easily used:

```
#!php

include_once "SemantifyIt.php";

$websiteKey = "rkvpGNrix";

$sem = new SemantifyIt(websiteKey);

$list = $sem->getAnnotationList();
$annotation = $sem->getAnnotation("rJL4cNBsg");

var_dump($list);
var_dump($annotation);
```
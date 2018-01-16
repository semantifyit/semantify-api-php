<?php

use STI\SemantifyIt\SemantifyIt;
include_once "SemantifyIt.php";

$sem = new SemantifyIt("Syi-T1jeb");

$sem->setError(true);
//echo $sem->getAnnotation("rJL4cNBsg");
echo "<pre>";
var_dump($sem->getAnnotationList());

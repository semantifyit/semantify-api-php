<?php


include_once "SemantifyIt.php";

$sem = new SemantifyIt("rkvpGNrix");

$sem->setError(true);
//echo $sem->getAnnotation("rJL4cNBsg");
echo "<pre>";
var_dump($sem->getAnnotationList());

<?php


include_once "SemantifyIt.php";

$sem = new SemantifyIt("rkvpGNrix");

//echo $sem->getAnnotation("rJL4cNBsg");
echo "<pre>";
var_dump($sem->getAnnotationList());

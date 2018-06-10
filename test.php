<?php

use STI\SemantifyIt\SemantifyIt;
include_once "SemantifyIt.php";

$sem = new SemantifyIt("Hkqtxgmkz", "ef0a64008d0490fc4764c2431ca4797b");

$sem->setError(true);
//echo $sem->getAnnotation("rJL4cNBsg");
echo "<pre>";
//var_dump($sem->getAnnotationList());


$json = '[{"content":{"@context":"http://schema.org/","@type":"Recipe","author":{"@type":"Person","name":"sfsdf"},"image":"sdfsdfsd","name":"fsdfsd"}}]';

var_dump($sem->postAnnotation($json));
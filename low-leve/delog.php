<?php

$file = 'turn.txt';
#unlink('turn.txt');
if(file_put_contents($file, '') !== FALSE)		echo 'Clean Complete!';
else	echo 'Clean Failure!';
?>
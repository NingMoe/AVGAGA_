<?php

include 'define_head.php';
include 'sprite/ffmpeg.php';

$input = '/root/http/html/demonv/sign.wmv';
$output = '/root/http/html/demonv/';

// 
/*
$ff = new FFmpeg();	// webm mp4 ogg
$ff->input($input);
$ff->bitrate( '3000k' );
$ff->size('720x405');
$ff->output($output.'722_405.mp4');
$ff->ready();
*/
/*
$ff = new FFmpeg();	// webm mp4 ogg
$ff->input($input);
$ff->bitrate( '3000k' );
$ff->size('640x360');
$ff->output($output.'640_360.mp4');
$ff->ready();
*/

$ff = new FFmpeg();	// webm mp4 ogg
$ff->input($input);
$ff->bitrate( '3000k' );
$ff->size('480x270');
$ff->output($output.'480_270.mp4');
$ff->ready();

?>
--TEST--
ffmpeg getFrameNumber test
--SKIPIF--
<?php 
require_once '../../ffmpeg_movie.php';
require_once '../../ffmpeg_frame.php';
require_once '../../ffmpeg_animated_gif.php';
$ignore_demo_files = true;
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
require_once $dir.'/examples/example-config.php';
$tmp_dir = PHPVIDEOTOOLKIT_EXAMPLE_ABSOLUTE_BATH.'tmp/';
?>
--FILE--
<?php
$mov = new PHPVideoToolkit_movie($dir.'/examples/to-be-processed/cat.mpeg', false, $tmp_dir);

/* move frame point to frame 5 */
$mov->getFrame(5);
printf("ffmpeg getFrameNumber(): %d\n", $mov->getFrameNumber());
?>
--EXPECT--
ffmpeg getFrameNumber(): 5

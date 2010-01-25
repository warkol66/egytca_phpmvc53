--TEST--
ffmpeg getPixelAspectRatio test
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
printf('frame number = ' . $mov->getFrameNumber() . "\n");
printf("ffmpeg getPixelAspectRatio(): %s\n", $mov->getPixelAspectRatio());
printf( 'frame number = ' . $mov->getFrameNumber() . "\n");

?>
--EXPECT--
frame number = 1
ffmpeg getPixelAspectRatio(): -1
frame number = 1

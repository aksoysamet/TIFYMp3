<?php
function ConvertMP3($id)
{
	exec('ffmpeg -loglevel panic -i /var/www/html/ibizavvideo/'.$id.'.mp4 -vn -acodec libmp3lame /var/www/html/ibizamusicenc/'.$id.'.mp3');
}
function IsMp4Exist($id)
{
	return file_exists('/var/www/html/ibizavvideo/'.$id.'.mp4');
}
function IsMp3Exist($id)
{
	return file_exists('/var/www/html/ibizamusicenc/'.$id.'.mp3');
}
function DownloadFile($id)
{
	exec('youtube-dl \'https://www.youtube.com/watch?v='.$id.'\' -f \'(mp4)[height<=480]\' -o \'/var/www/html/ibizavvideo/%(id)s.%(ext)s\' -q --restrict-filenames --no-cache-dir');
}
function GetDirectorySize($f)
{
    $io = popen ( '/usr/bin/du -sk ' . $f, 'r' );
    $size = fgets ( $io, 4096);
    $size = substr ( $size, 0, strpos ( $size, "\t" ) );
    pclose ( $io );
    return $size;
}
?>
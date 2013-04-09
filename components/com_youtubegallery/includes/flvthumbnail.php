<?php

$videofile= $_GET['videofile'];
$videofile= '../../../'.urldecode($videofile);
//$videofile= urldecode($videofile);
if(!file_exists($videofile))
{
    echo 'File "'.$videofile.'" not found.';
    die;
}

require_once('flv4php/FLV.php');

$flv = new FLV($videofile);

echo $flv->getFlvThumb();

?>
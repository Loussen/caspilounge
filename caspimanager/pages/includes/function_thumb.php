<?php
function create_thumbnail($path,$save,$width,$height)
{
    $info=getimagesize($path);
    if($width==0) $width=$info[0];
    if($height==0) $height=$info[1];
    $size=array($info[0],$info[1]);

    if($info["mime"]=="image/png")
    {
        $src=imagecreatefrompng($path);
    }
    elseif($info["mime"]=="image/jpeg")
    {
        $src=imagecreatefromjpeg($path);
    }
    elseif($info["mime"]=="image/gif")
    {
        $src=imagecreatefromgif($path);
    }
    else
    {
        return false;
    }

    $thumb=imagecreatetruecolor($width,$height);

    $src_aspect=$size[0]/$size[1];
    $thumb_aspect=$width/$height;

    if($src_aspect<$thumb_aspect)
    {
        $scale=$width/$size[0];
        $new_size=array($width,$width/$src_aspect);
    }
    elseif($src_aspect>$thumb_aspect)
    {
        $scale=$height/$size[1];
        $new_size=array($height*$src_aspect,$height);
    }
    else $new_size=array($width,$height);

    $new_size[0]=max($new_size[0],1);
    $new_size[1]=max($new_size[1],1);

    if($size[0]>$width) $leftMarg=intval(($size[0]-$width)/2); else $leftMarg=0;
    if($size[1]>$height) $topMarg=intval(($size[1]-$height)/2); else $topMarg=0;
    $src_pos=array($leftMarg,$topMarg);


    imagecopyresampled($thumb,$src,0,0,$src_pos[0],$src_pos[1],$new_size[0],$new_size[1],$size[0],$size[1]);

    if($save==false)
    {
        return imagejpeg($thumb,100);
    }
    else
    {
        return imagejpeg($thumb,$save,100);
    }

}
?>
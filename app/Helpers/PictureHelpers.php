<?php
/**
 * Created by PhpStorm.
 * User: jbr
 * Date: 14-07-2018
 * Time: 18:32
 */

namespace App\Helpers;

/**
 * Class PictureHelpers, used in HomeController.
 * @package App\Helpers
 */
class PictureHelpers
{
    /**
     * Collect the pictures for a house with a certain id returns an array, which can be used in a view.
     *
     * @param int $houseid
     * @param int $galleryid
     * @return array
     */
    static function getrandompicture($houseid, $galleryid)
    {
        $h = array();
        $library = public_path() . '/housegraphics/' . $houseid . '/gallery' . $galleryid . '/';
        $usedpath = str_replace(public_path(), '', $library);
        $extensionlist = "gif,jpg,png,bmp,swf,dcr,mov,qt,ram,rm,avi,mpg,mpeg,asf,flv";
        $extensionssmall = explode(',', $extensionlist);
        $extensionsbig = explode(',', strtoupper($extensionlist));
        $dirtyfiles = scandir($library);
        $files = array();
        foreach ($dirtyfiles as $file)
        {
            $fileok = false;
            $filearray = explode('.', $file);
            $extension = '';
            $filearraylength = sizeof($filearray);
            if ($filearraylength > 1) $extension = $filearray[$filearraylength-1];
            if (in_array($extension, $extensionssmall)) $fileok = true;
            if (in_array($extension, $extensionsbig)) $fileok = true;
            if ($fileok) $files[] = $file;
        }
        $nooffiles = sizeof($files);
        $r = rand(0, $nooffiles - 1);
        $h['filepath'] = $usedpath . $files[$r];
        $h['text'] = __(str_replace('_', ' ',substr($files[$r], 0, -4)));
        $h['text'] = 'mmm';
        $h['r'] = $r;
        return $h;
    }

    /**
     * @param $houseid
     * @param $galleryid
     * @param int $size
     * @return array
     */
    static function getPictureArray($houseid, $galleryid, $size = 0)
    {
        $h = array();
        $library = public_path() . '/housegraphics/' . $houseid . '/gallery' . $galleryid . '/';
        $usedpath = '/housegraphics/'.$houseid.'/gallery'.$galleryid.'/';
        $extemnsionslist = "gif,jpg,png,bmp,swf,dcr,mov,qt,ram,rm,avi,mpg,mpeg,asf,flv";
        $extensionssmall = explode(',', $extemnsionslist);
        $extensionsbig = explode(',', strtoupper($extemnsionslist));
        $dirtyfiles = scandir($library);
        $galleryprefix = 'gallery.' . $houseid . '.';
        //if ($houseid == 1) $galleryprefix = '';
        $files = array();
        foreach ($dirtyfiles as $file)
        {
            $fileok = false;
            $filearray = explode('.', $file);
            $extension = '';
            $filearraylength = sizeof($filearray);
            if ($filearraylength > 1) $extension = $filearray[$filearraylength-1];
            if (in_array($extension, $extensionssmall)) $fileok = true;
            if (in_array($extension, $extensionsbig)) $fileok = true;
            if ($fileok) $files[] = $file;
        }
        $nooffiles = sizeof($files);
        if ($size > 0) $size = min($nooffiles, $size);
        else $size = $nooffiles;
        shuffle($files);
        for ($i=0; $i < $size; $i++)
        {
            $filename = $files[$i];
            $h['path'][] = $usedpath . $filename;
            $text = explode('.', $filename);

            $textarraylength = sizeof($text);
            $surname = $text[$textarraylength-1];
            $text = substr($filename,0,-strlen($surname)-1);
            $h['text'][] = $galleryprefix . str_replace('_', ' ',$text);

            $imagesize = getimagesize($library . $filename);
            $h['x'][] = $imagesize[0];
            $h['y'][] = $imagesize[1];
        }
        return $h;
    }

}
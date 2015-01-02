<?php
/* Copyright (C) 2008 App Tsunami, Inc. */
/* 
 *  This program is free software: you can redistribute it and/or modify 
 *  it under the terms of the GNU General Public License as published by 
 *  the Free Software Foundation, either version 3 of the License, or 
 *  (at your option) any later version. 
 * 
 *  This program is distributed in the hope that it will be useful, 
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of 
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 *  GNU General Public License for more details. 
 * 
 *  You should have received a copy of the GNU General Public License 
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 */
/* CecHelperImage.php */
ini_set('memory_limit', '200M');

$rDir = '';
require_once($rDir.'cec/php/CecDebugUtil.php');
require_once($rDir.'cec/php/helpers/image/CecHelperImageResize.php');
require_once($rDir.'cec/php/helpers/image/CecHelperImageSharpen.php');

class CecHelperImage {

  const DOT = '.';
  const SLASH = DIRECTORY_SEPARATOR;
  const MIME_TYPE_SEP = '/';

  /* the const should match commonly used file extensions */
  const MIME_IMAGE = 'image/';
  const MIME_TYPE_GD = 'image/gd';
  const MIME_TYPE_GD2 = 'image/gd2';
  const MIME_TYPE_GIF = 'image/gif';
  const MIME_TYPE_JPEG = 'image/jpeg';
  const MIME_TYPE_PNG = 'image/png';
  const MIME_TYPE_BMP = 'image/bmp';

  /* fields in outputOptionArray */
  const OPTION_PIPELINE = 'pipeline';
  const OPTION_FILENAME = 'fileName';

  const MINIMUM_OUTPUT_QUALITY = 0;
  const MAXIMUM_OUTPUT_QUALITY = 100;
  const DEFAULT_OUTPUT_QUALITY = 100; /* set to maximum */

  protected $tmpDirectoryName;
  protected $tmpFileNamePrefix;
  public $outputQuality; /* 0 to 100 */

  public function __construct($tmpDirectoryName='/tmp',
      $tmpFileNamePrefix='cecImage') {
    $this->tmpDirectoryName = $tmpDirectoryName;
    $this->tmpFileNamePrefix = $tmpFileNamePrefix;
    $this->outputQuality = self::DEFAULT_OUTPUT_QUALITY;
  } // __construct

  /*
   * curlToFile makes a local copy of a file from a URL.
   * It is NOT required before calling the other methods.
   */
  public function curlToFile($imageUrl, $tmpFileName=null) {
    if (is_null($tmpFileName)) {
      $tmpFileName = tempnam ($this->tmpDirectoryName, 
        $this->tmpFileNamePrefix);
    } // if
    $fp = fopen($tmpFileName, "w");

    /* copy over */
    $ch = curl_init($imageUrl);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return($tmpFileName);
  } // curlToFile

  private function parseFileName($originalFileName) {
    $dotPos = strrpos($originalFileName, self::DOT);
    $slashPos = strrpos($originalFileName, self::SLASH);
    $len = $dotPos - $slashPos -1;

    $directoryName = substr($originalFileName, 0, $slashPos);
    $fileName = substr($originalFileName, $slashPos+1, $len);
    $extension = substr($originalFileName, $dotPos+1);
    return(Array($directoryName, $fileName, $extension));
  } // parseFileName

  private function getFileExtension($originalFileName) {
    $pos = strrpos($originalFileName, self::DOT);
    /* return the substring after the last dot */
    return(substr($originalFileName, $pos+1));
  } // getFileExtension

  public function generateNewFileName($originalFileName, $fileName, $fileNameSuffix, $newDirectory) {
    $fileNameParts = $this->parseFileName($originalFileName);
    $newFileNameFormat = '%s'.self::SLASH.'%s'.$fileNameSuffix.self::DOT.'%s';
    /* use the original file name and file extension */
    if (!is_null($fileName)) {
      $newFileName = sprintf($newFileNameFormat, $newDirectory, $fileName,
        $fileNameParts[2]);
    } else {
      $newFileName = sprintf($newFileNameFormat, $newDirectory, $fileNameParts[1], 
        $fileNameParts[2]);
    } // else
    return($newFileName);
  } // generateNewFileName

  protected function getMimeType($fileName) {
      $info=getimagesize($fileName);
      return($info['mime']);
  } // getMimeType

  private function loadImage($originalFileName, $mimeType) {
    if (is_null($mimeType)) {
      /* try to guess the format from the file name extension */
      $mimeType = $this->getMimeType($originalFileName);
    } // if

    $image = null;
    switch ($mimeType) {
    case self::MIME_TYPE_GD:
      $image = imageCreateFromGd($originalFileName);
      break;
    case self::MIME_TYPE_GD2:
      $image = imageCreateFromGd2($originalFileName);
      break;
    case self::MIME_TYPE_GIF:
      $image = imageCreateFromGif($originalFileName);
      break;
    case self::MIME_TYPE_JPEG:
      $image = imageCreateFromJpeg($originalFileName);
      break;
    case self::MIME_TYPE_PNG:
      $image = imageCreateFromPng($originalFileName);
      break;
    case self::MIME_TYPE_BMP:
      $image = imageCreateFromWbmp($originalFileName);
      break;
    } // switch
    return($image);
  } // loadImage

  private function writeImage($image, $fileName, $imageMime) {
    switch ($imageMime) {
    case self::MIME_TYPE_GD:
      imageGd($image, $fileName);
      break;
    case self::MIME_TYPE_GD2:
      imageGd2($image, $fileName);
      break;
    case self::MIME_TYPE_GIF:
      imageGif($image, $fileName, $this->outputQuality);
      break;
    case self::MIME_TYPE_JPEG:
      imageJpeg($image, $fileName, $this->outputQuality);
      break;
    case self::MIME_TYPE_PNG:
      imagePng($image, $fileName);
      break;
    case self::MIME_TYPE_BMP:
      imageWbmp($image, $fileName);
      break;
    } // switch
  } // writeImage

  private function cloneImage($originalImage) {
    if (is_null($originalImage)) return null;
    $originalW = imagesx($originalImage);
    $originalH = imagesy($originalImage);
    $newImage = imageCreateTrueColor($originalW, $originalH);
    $result = imagecopy($newImage, $originalImage, 0, 0, 0, 0, $originalW, $originalH);
    if ($result === FALSE) {
      imageDestroy($newImage);
      return(null);
    } // if
    return($newImage);
  } // cloneImage

  /*
   * originalFileName can be a file path or a url.
   * targetArray is
     Array(
       Array(width1, height1, targetFileName1),
       Array(width2, height2, targetFileName2),
       ...
     )
   * targetFileName should be a file path where the output is saved.
   */
  public function execute($originalFileName, $mimeType, $targetArray) {
    $originalFileName = trim($originalFileName);
    if (empty($originalFileName)) return;
    $originalImage = $this->loadImage($originalFileName, $mimeType);
    if (empty($originalImage)) return;
    foreach($targetArray as $target) {
      $image = $this->cloneImage($originalImage);
      if (!is_null($image)) {
        $newImage = $this->executePipeline($target[self::OPTION_PIPELINE], $image);
        /* save output */
        imageJpeg($newImage, $target[self::OPTION_FILENAME], $this->outputQuality);
        if ($newImage != $image) {
          if (!is_null($newImage)) {
            imageDestroy($newImage);
          } // if
        } // if
        if (!is_null($image)) {
          imageDestroy($image);
        } // if
      } else {
        /* write out the original if cloneImage failed */
        imageJpeg($originalImage, $target[self::OPTION_FILENAME], $this->outputQuality);
      } // if
    } // foreach

    /* free resources */
    imageDestroy($originalImage);
  } // execute

  private function executePipeline($pipeline, &$originalImage) {
    if (is_null($originalImage)) return(null);
    if (is_null($pipeline)) return($originalImage);
    $image = $originalImage;
    foreach($pipeline as $stage) {
      $newImage = $stage->execute($image);
      if (!is_null($newImage) && ($newImage != $image)) {
        /* the old image is no longer needed */
        imageDestroy($image);
        $originalImage = null;
        $image = $newImage;
      } // if
    } // foreach
    return($image);
  } // executePipeline

  public function createResizeSharpenPipeline($newWidth, $newHeight, $sharpenEffect=0.75) {
    /* this is an example of how to create a pipeline */
    $resizeStage = new CecHelperImageResize();
    $resizeStage->newWidth = $newWidth;
    $resizeStage->newHeight = $newHeight;

    $sharpenStage = new CecHelperImageSharpen();
    $sharpenStage->sharpenEffect = $sharpenEffect;

    $pipeline = Array (
       $resizeStage,
       $sharpenStage
    );
    return($pipeline);
  } // createResizeSharpenPipeline

  public function createResizePipeline($newWidth, $newHeight) {
    /* this is an example of how to create a pipeline */
    $resizeStage = new CecHelperImageResize();
    $resizeStage->newWidth = $newWidth;
    $resizeStage->newHeight = $newHeight;

    $pipeline = Array (
       $resizeStage
    );
    return($pipeline);
  } // createResizePipeline

  public function createTarget($pipeline, $outputFileName) {
    $target = Array ();
    $target[self::OPTION_PIPELINE] = $pipeline;
    $target[self::OPTION_FILENAME] = $outputFileName;
    return($target);
  } // createTarget

  public static function getLastMimePart($mimeType) {
    if (empty($mimeType)) return null;
    $parts = explode(self::MIME_TYPE_SEP, $mimeType);
    return $parts[count($parts)-1];
  } // getLastMimePart

} // CecHelperImage
?>

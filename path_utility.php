<?php
define('RAW_PICTURE', 0);
define('SMALL_THUMB', 1);
define('LARGE_THUMB', 2);

class PathUtility{
  public static function getPathById($uuid, $photoid, $mode){
    switch ($mode) {
      case RAW_PICTURE:
        $id = 'pic-';
        break;
      case SMALL_THUMB:
        $id = 'st-';
        break;
      case LARGE_THUMB:
        $id = 'lt-';
        break;
      default:
        $id = '';
        break;
    }
    $path = './images/uploads/' . $uuid . '/' . $id . $photoid . '.jpg';
    return $path;
  }
}
?>

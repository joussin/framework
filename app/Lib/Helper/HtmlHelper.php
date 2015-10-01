<?php
/**
 * Created by PhpStorm.
 * User: wal21
 * Date: 01/10/15
 * Time: 13:14
 */

namespace App\Lib\Helper;

class HtmlHelper{


    /**
     * @return mixed
     */
    public function getAssetDirectory()
    {
        return WEB_PATH;
    }


    public function asset($filePath)
    {
        return $this->getAssetDirectory().$filePath;
    }




}
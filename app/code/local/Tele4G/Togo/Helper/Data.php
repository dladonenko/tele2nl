<?php
class Tele4G_Togo_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function formatHours($date = '')
    {
        if (empty($date)) {
            return "";
        }
        $time = "";
        preg_match("#T([\d]{1,2})[^\d]{1}([\d]{1,2})#", $date, $matches);
        if (isset($matches[1]) && isset($matches[2])) {
            $time = $matches[1] . "." . $matches[2];
        }
        return $time;
        //return strftime("%H.%M", strtotime($date));
    }
    
    public function getResellerPic($chain)
    {
        $pic = null;
        switch ($chain) {
            case "PRESSBYRAN":
                $pic = "https://www.comviq.se/gfx/comviqtogo/pressbyran.png";
            break;
            case "SEVEN_ELEVEN":
                $pic = "https://www.comviq.se/gfx/comviqtogo/7-eleven.png";
            break;
        }
        return $pic;
    }
}

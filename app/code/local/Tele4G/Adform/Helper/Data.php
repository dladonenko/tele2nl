<?php
class Tele4G_Adform_Helper_Data extends Adform_Track_Helper_Data
{
    /**
     * getAge
     *
     * @param type $ssn
     * @return integer
     */
    public function getAge($ssn = null)
    {
        $age = '';
        if ($ssn) {
            $customerBirthYear =  (int) substr($ssn, 0, 4);
            $customerBirthMonth = (int) substr($ssn, 4, 2);
            $customerBirthDay   = (int) substr($ssn, 6, 2);
            $age = (date("Y") - $customerBirthYear);
            if ($customerBirthMonth > date("m")) {
                $age--;
            } elseif (
                $customerBirthMonth == date("m") &&
                $customerBirthDay > date("d")
            ) {
                $age--;
            }
        }

        return $age;
    }

    /**
     * getAgeGroup
     *
     * @param type $ssn
     * @return string
     */
    public function getAgeGroup($ssn = null)
    {
        $ageGroups = array(
            7 => "A",
            17 => "B",
            30 => "C",
            40 => "D",
            50 => "E",
            60 => "F",
            70 => "G",
            80 => "H"
        );

        $groupName = "UNKNOWN";

        $age = $this->getAge($ssn);

        foreach ($ageGroups as $key => $group) {
            if ($age > $key) {
                $groupName = $group;
            }
        }
        return $groupName;
    }

    /**
     * getGenderFromSsn
     *
     * @param type $ssn
     * @return string
     */
    public function getGenderFromSsn($ssn)
    {
        $genderDigit = (int) substr($ssn, strlen($ssn) - 2, 1);
        $gender = "";
        if ($genderDigit % 2 == 0)
        {
            $gender = "K";// It's even
        }
        else
        {
            $gender = "M";// It's odd
        }
        return $gender;
    }
    
    /**
     * 
     * @param type $order
     * @return string
     */
    public function getCity($order)
    {
        $cityName = "UNKNOWN";
        if ($order) {
            $cityName = $order->getBillingAddress()->getCity();
        }
        return $cityName;
    }

    /**
     * Retrive Country name
     * 
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getCountry($order = null)
    {
        $countryName = "SWEDEN";
        //if ($order) {
        //    $countryName = $order->getBillingAddress()->getCountry();
        //}
        return $countryName;
    }

    /**
     * Retrive Country name
     * 
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getOfferPhoneNumber($order = null)
    {
        $offerPhoneNumbers = array();
        if ($order && $offerData = unserialize($order->getOfferData())) {
            if ($offerData) {
                foreach ($offerData as $_offers) {
                    if (isset($_offers['number'])) {
                        $offerPhoneNumbers[] = $_offers['number'];
                    }
                }
            }
        }
        return $offerPhoneNumbers;
    }

    /**
     * Retrive Products names
     * 
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getProductNames($order = null)
    {
        $products = array();
        if ($order && $items = $order->getAllItems()) {
            foreach ($items as $_item) {
                if ($_item->getProductType() == 'simple') {
                    $products[] = $_item->getName();
                }
            }
        }
        return $products;
    }
}

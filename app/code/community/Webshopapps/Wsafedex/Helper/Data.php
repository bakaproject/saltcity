<?php
/* Dimensional Shipping
 * @category   Webshopapps
 * @package    Webshopapps_UsaShipping
 * @copyright  Copyright (c) 2012 Zowta Ltd (http://www.webshopapps.com)
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

/**** 
 * Helper Methods
 **/
class Webshopapps_Wsafedex_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	protected static $_debug;
	protected static $_wholeWeightRounding;
	
	public static function isDebug() {
		if (self::$_debug==NULL) {
			self::$_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Wsafedex');
		}
		return self::$_debug;
	}
	
	
	
	public static function isWholeWeightRounding() {
		if (self::$_wholeWeightRounding==NULL) {
			self::$_wholeWeightRounding = Mage::getStoreConfig('shipping/wsafedex/whole_weight');
		}
		return self::$_wholeWeightRounding;
	}
	
	
	public static function getHandlingProductModel() {
		return self::$_handlingProdModel;
	}
	
	public function getWeightCeil($weight) {
		if ($this->isWholeWeightRounding()) {
			return ceil(round($weight,2));
		}	else {
			return round($weight,2);
		}
	}
	
	/**	  
	 * Simple function to round a value to two significant figures
	 * @param int $value The value to be rounded
	 */
	public function toTwoDecimals($value=-1) {
		return round($value,2);		// changed from ceil as worried about above causing an issue
	}
	
  	
    
    
	
}

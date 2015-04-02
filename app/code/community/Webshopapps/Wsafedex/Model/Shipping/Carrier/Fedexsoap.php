<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* UsaShipping
 *
 * @category   Webshopapps
 * @package    Webshopapps_UsaShipping
 * @copyright  Copyright (c) 2011 Zowta Ltd (http://www.webshopapps.com)
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

class Webshopapps_Wsafedex_Model_Shipping_Carrier_Fedexsoap
    extends Webshopapps_Wsafedex_Model_Shipping_Carrier_Fedex
{

    protected $_code = 'fedexsoap';
    
  	public function isTrackingAvailable()
    {
        return false;
    }
    
    protected $_applyHandlingPackage = FALSE;

 	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
    	if (!$this->getConfigFlag('active')) {
    		return false;
        }
        
        $this->getConfigData('handling_action') != 'O' ? $this->_applyHandlingPackage = TRUE : 0;

        $this->setRequest($request);

        $this->_result = $this->_getQuotes();

        $this->_updateFreeMethodQuote($request);

        return $this->getResult();
    }

     public function setRequest(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_request = $request;

        $r = new Varien_Object();

        if ($request->getLimitMethod()) {
            $r->setService($request->getLimitMethod());
        }

        if ($request->getFedexAccount()) {
            $account = $request->getFedexAccount();
        } else {
            $account = $this->getConfigData('account');
        }
        $r->setAccount($account);
        
        if ($request->getFedexSoapKey()) {
            $key = $request->getFedexSoapKey();
        } else {
            $key = $this->getConfigData('key');
        }
        $r->setAccountKey($key);
        
        
        if ($request->getFedexPassword()) {
            $password = $request->getFedexPassword();
        } else {
            $password = $this->getConfigData('fedex_password');
        }
        $r->setPassword($password);
        
        if ($request->getFedexMeterNumber()) {
            $meterNo = $request->getFedexMeterNumber();
        } else {
            $meterNo = $this->getConfigData('meter_no');
        }
        $r->setAccountMeterNo($meterNo);
        

        if ($request->getFedexDropoff()) {
            $dropoff = $request->getFedexDropoff();
        } else {
            $dropoff = $this->getConfigData('dropoff');
        }
        $r->setDropoffType($dropoff);

        if ($request->getFedexPackaging()) {
            $packaging = $request->getFedexPackaging();
        } else {
            $packaging = $this->getConfigData('packaging');
        }
        $r->setPackaging($packaging);

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = Mage::getStoreConfig('shipping/origin/country_id', $this->getStore());
        }
        $r->setOrigCountry(Mage::getModel('directory/country')->load($origCountry)->getIso2Code());

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(Mage::getStoreConfig('shipping/origin/postcode', $this->getStore()));
        }
        
        if ($this->_request->getUpsDestType()) {
        	if ($this->_request->getUpsDestType() == "RES") {
        		$r->setDestType(1);
        	} else {
        		$r->setDestType(0);
        	}
        } else {
        	$r->setDestType($this->getConfigData('residence_delivery'));
        }
        

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }
        $r->setDestCountry(Mage::getModel('directory/country')->load($destCountry)->getIso2Code());

        if ($request->getDestPostcode()) {
            $r->setDestPostal(trim($request->getDestPostcode()));
        } else {

        }

        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $r->setWeight($weight);
        if ($request->getFreeMethodWeight()!= $request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        $r->setValue($request->getPackagePhysicalValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());

        $r->setIgnoreFreeItems(false);
    	$r->setMaxPackageWeight($this->getConfigData('max_package_weight'));

        $this->_rawRequest = $r;

        return $this;
    }
    
 

    protected function _getXmlQuotes() {

        $r = $this->_rawRequest;
			
		$date = date('c');
		
		if(!$this->getConfigData('saturday_pickup')){
			if(date('w')==6){
				$date = date('c', time() + 172800); //adds 2 days if it's a Saturday.
				if ($this->getDebugFlag()) {
        			$this->_debug('Date modified to '.$date);
				}
			} else if (date('w')==0){
				$date = date('c', time() + 86400); //adds 1 day if it's a Sunday.
				if ($this->getDebugFlag()) {
        			$this->_debug('Date modified to '.$date);
				}
			}
		}
		
		//The WSDL is not included with the sample code.
		//Please include and reference in $path_to_wsdl variable.
		if ($this->getConfigData('gateway_url')=='LIVE') {
			$path_to_wsdl = Mage::getBaseDir().'/lib/wsa/RateService_v9.wsdl';
		} else {
			$path_to_wsdl = Mage::getBaseDir().'/lib/wsa/RateService_v9_test.wsdl';
		}

		ini_set("soap.wsdl_cache_enabled", "0");

		$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
      	
        $fedReq['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request v9 using PHP ***');
		$fedReq['Version'] = array('ServiceId' => 'crs', 'Major' => '9', 'Intermediate' => '0', 'Minor' => '0');
		$displayTransitTime = $this->getConfigData('display_transit_time');
		if ($displayTransitTime) {
			$fedReq['ReturnTransitAndCommit'] = true; 
		} else {
			$fedReq['ReturnTransitAndCommit'] = false; 
		}
		
		$fedReq['RequestedShipment']['DropoffType'] = $r->getDropoffType(); // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
		$fedReq['RequestedShipment']['ShipTimestamp'] = $date;
		$fedReq['RequestedShipment']['ServiceType'] = $r->getService(); // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...

		$fedReq['RequestedShipment']['PackagingType'] = $r->getPackaging(); // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
		if ($this->getConfigFlag('monetary_value')) {
			$fedReq['RequestedShipment']['TotalInsuredValue']=array('Amount'=>$r->getValue(),'Currency'=>'USD');
		}
		$fedReq['RequestedShipment']['Shipper'] =  array(
				'Address' => array(
					//'StreetLines' => array('Address Line 1'),
		           // 'City' => 'Los Angeles',
		          //  'StateOrProvinceCode' => 'CA',
		            'PostalCode' => $r->getOrigPostal(),
		            'CountryCode' => $r->getOrigCountry(),
		            'Residential' => $r->getDestType())
				);
		$fedReq['RequestedShipment']['Recipient'] = array(
				'Address' => array(
					//'StreetLines' => array('Address Line 1'),
		          //  'City' => 'Richmond',
		          //  'StateOrProvinceCode' => 'BC',
		            'PostalCode' => $r->getDestPostal(),
		            'CountryCode' => $r->getDestCountry(),
		            'Residential' => $r->getDestType())
				);
	//	$fedReq['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
	//	                                                        'Payor' => array('AccountNumber' => '510087704',
	//	                                                                     'CountryCode' => 'US'));

		$fedReq['RequestedShipment']['RateRequestTypes'] = $this->getConfigData('request_type');
		$fedReq['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';  //  Or PACKAGE_SUMMARY


        
        
		
		if ($this->getConfigFlag('monetary_value')) {
				$fedReq['RequestedShipment']['RequestedPackageLineItems'][] = array (
							'InsuredValue' => array (
							'Currency' => 'USD',
							'Amount' => (float)$r->getValue()),
				        	'Weight' => array('Value' => (float)$r->getWeight(),
			            	'Units' => 'LB'),
			        	);
		}
		else{
				$fedReq['RequestedShipment']['RequestedPackageLineItems'][] = array (
				        	'Weight' => array('Value' => (float)$r->getWeight(),
			            	'Units' => 'LB'),
			        	);
		}
		
		$fedReq['RequestedShipment']['PackageCount'] = count($fedReq['RequestedShipment']['RequestedPackageLineItems']);
		       			
	  	$fedReq['WebAuthenticationDetail'] = array('UserCredential' =>
                                      array('Key' => $r->getAccountKey(), 'Password' =>  $r->getPassword()));
        $fedReq['ClientDetail'] = array('AccountNumber' => $r->getAccount(), 'MeterNumber' =>  $r->getAccountMeterNo());
        $debugData = array(	'request' => $fedReq);  
           		
		try
		{
		    $responseBody = $client->getRates($fedReq);
		
            $debugData['request_xml'] = $client->__getLastRequest();
            $debugData['result'] = $client->__getLastResponse();
            
		} catch (Exception $e) {
		   	$debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            Mage::logException($e);
		   	$responseBody = '';
		}
		
	    $this->_debug($debugData);
		
        
        return $this->_parseDimXmlResponse($debugData,$responseBody);
    }

	protected function _parseDimXmlResponse($debugData,$response)
    {
		$displayTransitTime = $this->getConfigData('display_transit_time');
		$requestType = $this->getConfigData('request_type');
		
    	$costArr = array();
        $priceArr = array();
        $timeArr = array();
        
       	//if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR' && $response -> HighestSeverity != 'WARNING')
       	if (is_object($response) && isset($response -> RateReplyDetails) && $response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
       	{

            $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));
            
            if ($requestType == "LIST") {
            	$requestType = "PAYOR_LIST_PACKAGE";
            } else {
            	$requestType = "PAYOR_ACCOUNT_PACKAGE";
            }

            $rateReplyDetails =$response -> RateReplyDetails ;
            
            if (count($response -> RateReplyDetails)>1) {
	               	foreach ($rateReplyDetails as $entry) {
	                      if (in_array((string)$entry->ServiceType, $allowedMethods)) {
	                      		if (!is_array($entry->RatedShipmentDetails)) {
	                      			if ($requestType == (string) $entry->RatedShipmentDetails->ShipmentRateDetail->RateType) {
	                          		$totalNetCharge = (string)$entry->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount;
	                      			}
	                      		} else {
	                      			foreach ($entry->RatedShipmentDetails as $rateType) {
	                      				if ($requestType == (string) $rateType->ShipmentRateDetail->RateType) {
	                      					$totalNetCharge = (string)$rateType->ShipmentRateDetail->TotalNetCharge->Amount;
	                      				}
	                      			}
	                      			
	                      		}
	                      	
	                      		$costArr[(string)$entry->ServiceType] = $totalNetCharge;
	                      	
	                          	$priceArr[(string)$entry->ServiceType] = $this->getMethodPrice($totalNetCharge, (string)$entry->ServiceType);
	                      }
	               	   if($displayTransitTime && isset($entry->TransitTime)) {
	   					 	$timeArr[(string)$entry->ServiceType] = $entry->TransitTime;
	   					 }
	   					 if($displayTransitTime && isset($entry->DeliveryTimestamp)) {
	   					 	$timeArr[(string)$entry->ServiceType] = $entry->DeliveryTimestamp;
	   					 }
	                  	}
	               } else {
	               	// single reply on freemethodquote
	               	$entry=$rateReplyDetails;
	              		if (in_array((string)$entry->ServiceType, $allowedMethods)) {
	              			
	                  		if (!is_array($entry->RatedShipmentDetails)) {
	                  			if ($requestType == (string) $entry->RatedShipmentDetails->ShipmentRateDetail->RateType) {
	                  				$totalNetCharge = (string)$entry->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount;
	                  			}
	                      	} else {
	                      		foreach ($entry->RatedShipmentDetails as $rateType) {
	                      			if ($requestType == (string) $rateType->ShipmentRateDetail->RateType) {
	                      				$totalNetCharge = (string)$rateType->ShipmentRateDetail->TotalNetCharge->Amount;
	                      			}
	                      		}
	                      		
	                      	}
	                      
	                      	$costArr[(string)$entry->ServiceType] = $totalNetCharge;
	                      
	                      	$priceArr[(string)$entry->ServiceType] = $this->getMethodPrice($totalNetCharge, (string)$entry->ServiceType);
	                   
	                 	}
	                	if($displayTransitTime && isset($entry->TransitTime)) {
	   				 	$timeArr[(string)$entry->ServiceType] = $entry->TransitTime;
	   				 }
	   				 if($displayTransitTime && isset($entry->DeliveryTimestamp)) {
	   				 	$timeArr[(string)$entry->ServiceType] = $entry->DeliveryTimestamp;
	   				 }
            }

               asort($priceArr);
        } else {
            $errorTitle = 'error retrieving rates';
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            //$error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier($this->_code);
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                if ($this->getConfigFlag('home_ground') && $this->getCode('method', $method) == 'Home Delivery') {
                	$rate->setMethodTitle('Ground');
                }
                else {
                	$rate->setMethodTitle($this->getCode('method', $method));	
                }
                if ($displayTransitTime && $timeArr[$method]!= '') {
					$rate->setShip($timeArr[$method]);
                }
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }
        return $result;
    }
    

  public function getCode($type, $code='')
    {
        $codes = array(

         'method' => array(
                'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => Mage::helper('usa')->__('Europe First Priority'),
                'FEDEX_1_DAY_FREIGHT'                 => Mage::helper('usa')->__('1 Day Freight'),
                'FEDEX_2_DAY_FREIGHT'                 => Mage::helper('usa')->__('2 Day Freight'),
                'FEDEX_2_DAY'                         => Mage::helper('usa')->__('2 Day'),
                'FEDEX_3_DAY_FREIGHT'                 => Mage::helper('usa')->__('3 Day Freight'),
                'FEDEX_EXPRESS_SAVER'                 => Mage::helper('usa')->__('Express Saver'),
                'FEDEX_GROUND'                        => Mage::helper('usa')->__('Ground'),
                'FIRST_OVERNIGHT'                     => Mage::helper('usa')->__('First Overnight'),
                'GROUND_HOME_DELIVERY'                => Mage::helper('usa')->__('Home Delivery'),
                'INTERNATIONAL_ECONOMY'               => Mage::helper('usa')->__('International Economy'),
                'INTERNATIONAL_ECONOMY_FREIGHT'       => Mage::helper('usa')->__('Intl Economy Freight'),
                'INTERNATIONAL_FIRST'                 => Mage::helper('usa')->__('International First'),
                'INTERNATIONAL_GROUND'                => Mage::helper('usa')->__('International Ground'),
                'INTERNATIONAL_PRIORITY'              => Mage::helper('usa')->__('International Priority'),
                'INTERNATIONAL_PRIORITY_FREIGHT'      => Mage::helper('usa')->__('Intl Priority Freight'),
                'PRIORITY_OVERNIGHT'                  => Mage::helper('usa')->__('Priority Overnight'),
                'SMART_POST'                          => Mage::helper('usa')->__('Smart Post'),
                'STANDARD_OVERNIGHT'                  => Mage::helper('usa')->__('Standard Overnight'),
                'FEDEX_FREIGHT'                       => Mage::helper('usa')->__('Freight'),
                'FEDEX_NATIONAL_FREIGHT'              => Mage::helper('usa')->__('National Freight'),
            ),

            'dropoff'=>array(
                'REGULAR_PICKUP'         => Mage::helper('usa')->__('Regular Pickup'),
                'REQUEST_COURIER'        => Mage::helper('usa')->__('Request Courier'),
                'DROP_BOX'               => Mage::helper('usa')->__('Drop Box'),
                'BUSINESS_SERVICE_CENTER' => Mage::helper('usa')->__('Business Service Center'),
                'STATION'               => Mage::helper('usa')->__('Station'),
            ),

            'packaging'=>array(
                'FEDEX_ENVELOPE' => Mage::helper('usa')->__('FedEx Envelope'),
                'FEDEX_PAK'      => Mage::helper('usa')->__('FedEx Pak'),
                'FEDEX_BOX'      => Mage::helper('usa')->__('FedEx Box'),
                'FEDEX_TUBE'     => Mage::helper('usa')->__('FedEx Tube'),
                'FEDEX_10KG_BOX'  => Mage::helper('usa')->__('FedEx 10kg Box'),
                'FEDEX_25KG_BOX'  => Mage::helper('usa')->__('FedEx 25kg Box'),
                'YOUR_PACKAGING' => Mage::helper('usa')->__('Your Packaging'),
            ),
            'gatewayurl'=>array(
                'TEST' => Mage::helper('usa')->__('Test'),
                'LIVE'      => Mage::helper('usa')->__('Live'),
            ),

        );

        if (!isset($codes[$type])) {
//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid FedEx XML code type: %s', $type));
            return false;
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid FedEx XML code for type %s: %s', $type, $code));
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

   
    
    protected function _setFreeMethodRequest($freeMethod)
    {
    	if (!Mage::getStoreConfig('carriers/fedexsoap/active')) {
    		return parent::_setFreeMethodRequest($freeMethod);
    	}
    	parent::_setFreeMethodRequest($freeMethod);
    	$this->_rawRequest->setIgnoreFreeItems(true);
    	
        
    }
    



    /*********************************************************************************************
     * ALL METHODS BELOW ARE REQUIRED FOR 1.4.0.1 ONLY - SEE ORIGINAL UPS FILE
     *********************************************************************************************/
    
	/**
     * Get correct weigt.
     *
     * Namely:
     * Checks the current weight to comply with the minimum weight standards set by the carrier.
     * Then strictly rounds the weight up until the first significant digit after the decimal point.
     *
     * @param float|integer|double $weight
     * @return float
     */
    protected function _getCorrectWeight($weight)
    {
        $minWeight = $this->getConfigData('min_package_weight');

        if($weight < $minWeight){
            $weight = $minWeight;
        }

        //rounds a number to one significant figure
        $weight = ceil($weight*10) / 10;

        return $weight;
    }
    


}


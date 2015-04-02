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

class Webshopapps_Wsafedex_Model_Shipping_Carrier_Fedex
    extends Mage_Usa_Model_Shipping_Carrier_Fedex
{
    protected $_code = 'fedexsoap';
	
   /**
     * Path to wsdl file of track service
     *
     * @var string
     */
    protected $_trackServiceWsdl = null;
    
	public function __construct()
    {
        parent::__construct();
        $this->_trackServiceWsdl = $path_to_wsdl = Mage::getBaseDir().'/lib/wsa/TrackService_v5.wsdl';
    }
    
     /**
     * Create track soap client
     *
     * @return SoapClient
     */
    protected function _createTrackSoapClient()
    {
        return $this->_createSoapClient($this->_trackServiceWsdl, 1);
    }
    
/**
     * Create soap client with selected wsdl
     *
     * @param string $wsdl
     * @param bool|int $trace
     * @return SoapClient
     */
    protected function _createSoapClient($wsdl, $trace = false)
    {
        $client = new SoapClient($wsdl, array('trace' => $trace));
        $client->__setLocation($this->getConfigFlag('sandbox_mode')
            ? 'https://wsbeta.fedex.com:443/web-services/rate'
            : 'https://ws.fedex.com:443/web-services/rate'
        );

        return $client;
    }
        
  /**
     * Send request for tracking
     *
     * @param array $tracking
     * @return void
     */
    protected function _getXMLTracking($tracking)
    {
        $trackRequest = array(
            'WebAuthenticationDetail' => array(
                'UserCredential' => array(
                    'Key'      => Mage::getStoreConfig('carriers/fedexsoap/key'),
                    'Password' => Mage::getStoreConfig('carriers/fedexsoap/fedex_password')
                )
            ),
            'ClientDetail' => array(
                'AccountNumber' =>  Mage::getStoreConfig('carriers/fedexsoap/account'),
                'MeterNumber'   =>  Mage::getStoreConfig('carriers/fedexsoap/meter_no')
            ),
            'Version' => array(
                'ServiceId'    => 'trck',
                'Major'        => '5',
                'Intermediate' => '0',
                'Minor'        => '0'
            ),
            'PackageIdentifier' => array(
                'Type'  => 'TRACKING_NUMBER_OR_DOORTAG',
                'Value' => $tracking,
            ),
            /*
             * 0 = summary data, one signle scan structure with the most recent scan
             * 1 = multiple sacn activity for each package
             */
            'IncludeDetailedScans' => 1,
        );
        
        $requestString = serialize($trackRequest);
        $debugData = array('request' => $trackRequest);
        try {
        	$client = $this->_createTrackSoapClient();
            $response = $client->track($trackRequest);
            $debugData['result'] = $response;
        } catch (Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            Mage::logException($e);
        }

        $this->_debug($debugData);

        $this->_parseTrackingResponse($tracking, $response);
    }

    /**
     * Parse tracking response
     *
     * @param array $trackingValue
     * @param stdClass $response
     */
    protected function _parseTrackingResponse($trackingValue, $response)
    {
    	$errorTitle='';
        if (is_object($response)) {
            if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR') {
                $errorTitle = (string)$response->Notifications->Message;
            } elseif (isset($response->TrackDetails)) {
                $trackInfo = $response->TrackDetails;
                $resultArray['status'] = (string)$trackInfo->StatusDescription;
                $resultArray['service'] = (string)$trackInfo->ServiceInfo;
                $timestamp = isset($trackInfo->EstimatedDeliveryTimestamp) ?
                    $trackInfo->EstimatedDeliveryTimestamp : $trackInfo->ActualDeliveryTimestamp;
                $timestamp = strtotime((string)$timestamp);
                if ($timestamp) {
                    $resultArray['deliverydate'] = date('Y-m-d', $timestamp);
                    $resultArray['deliverytime'] = date('H:i:s', $timestamp);
                }

                if (isset($trackInfo->EstimatedDeliveryAddress)) {
                	$deliveryLocation = $trackInfo->EstimatedDeliveryAddress;
                } elseif (isset($trackInfo->ActualDeliveryAddress)) {
                	$deliveryLocation = $trackInfo->ActualDeliveryAddress;
                } elseif (isset($trackInfo->DestinationAddress)) {
                	$deliveryLocation = $trackInfo->DestinationAddress;
                }
                $deliveryLocationArray = array();                
              
                if (isset($deliveryLocation->City)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->City;
                }
                if (isset($deliveryLocation->StateOrProvinceCode)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->StateOrProvinceCode;
                }
                if (isset($deliveryLocation->CountryCode)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->CountryCode;
                }
                if ($deliveryLocationArray) {
                    $resultArray['deliverylocation'] = implode(', ', $deliveryLocationArray);
                }

                if (isset($deliveryLocation->DeliverySignatureName)) {
                	$resultArray['signedby'] = (string)$trackInfo->DeliverySignatureName;
                }
                $resultArray['shippeddate'] = date('Y-m-d', (int)$trackInfo->ShipTimestamp);
                if (isset($trackInfo->PackageWeight) && isset($trackInfo->Units)) {
                    $weight = (string)$trackInfo->PackageWeight;
                    $unit = (string)$trackInfo->Units;
                    $resultArray['weight'] = "{$weight} {$unit}";
                }

                $packageProgress = array();
                if (isset($trackInfo->Events)) {
                    $events = $trackInfo->Events;
                    if (isset($events->Address)) {
                        $events = array($events);
                    }
                    foreach ($events as $event) {
                        $tempArray = array();
                        $tempArray['activity'] = (string)$event->EventDescription;
                        $timestamp = strtotime((string)$event->Timestamp);
                        if ($timestamp) {
                            $tempArray['deliverydate'] = date('Y-m-d', $timestamp);
                            $tempArray['deliverytime'] = date('H:i:s', $timestamp);
                        }
                        if (isset($event->Address)) {
                            $addressArray = array();
                            $address = $event->Address;
                            if (isset($address->City)) {
                                $addressArray[] = (string)$address->City;
                            }
                            if (isset($address->StateOrProvinceCode)) {
                                $addressArray[] = (string)$address->StateOrProvinceCode;
                            }
                            if (isset($address->CountryCode)) {
                                $addressArray[] = (string)$address->CountryCode;
                            }
                            if ($addressArray) {
                                $tempArray['deliverylocation'] = implode(', ', $addressArray);
                            }
                        }
                        $packageProgress[] = $tempArray;
                    }
                }

                $resultArray['progressdetail'] = $packageProgress;
            }
        }

        if(!$this->_result){
            $this->_result = Mage::getModel('shipping/tracking_result');
        }

        if(isset($resultArray)) {
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier($this->_code);
            $tracking->setCarrierTitle(Mage::getStoreConfig('carriers/fedexsoap/title'));
            $tracking->setTracking($trackingValue);
            $tracking->addData($resultArray);
            $this->_result->append($tracking);
        } else {
           $error = Mage::getModel('shipping/tracking_result_error');
           $error->setCarrier($this->_code);
           $error->setCarrierTitle(Mage::getStoreConfig('carriers/fedexsoap/title'));
           $error->setTracking($trackingValue);
           $error->setErrorMessage($errorTitle ? $errorTitle : Mage::helper('usa')->__('Unable to retrieve tracking'));
           $this->_result->append($error);
        }
    }

  
    /**
     * Get tracking response
     *
     * @return string
     */
    public function getResponse()
    {
        $statuses = '';
        if ($this->_result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $this->_result->getAllTrackings()) {
                foreach ($trackings as $tracking){
                    if($data = $tracking->getAllData()){
                        if (!empty($data['status'])) {
                            $statuses .= Mage::helper('usa')->__($data['status'])."\n<br/>";
                        } else {
                            $statuses .= Mage::helper('usa')->__('Empty response')."\n<br/>";
                        }
                    }
                }
            }
        }
        if (empty($statuses)) {
            $statuses = Mage::helper('usa')->__('Empty response');
        }
        return $statuses;
    }
 
       /**
     * Log debug data to file
     *
     * @param mixed $debugData
     */
    public function _debug($debugData)
    {
  	
        if ($this->getDebugFlag()) {
           Mage::log($debugData);
        }
    }

    /**
     * Define if debugging is enabled
     *
     * @return bool
     */
    public function getDebugFlag()
    {
        return Mage::getStoreConfig('carriers/fedexsoap/debug');
    }
    
       
}
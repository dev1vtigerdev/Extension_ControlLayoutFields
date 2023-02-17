<?php
/* ********************************************************************************
 * The content of this file is subject to the Conditional Layouts ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class ControlLayoutFields_Activate_Action extends Vtiger_Action_Controller
{
    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    function __construct() {
        parent::__construct();
        $this->exposeMethod('activate');
        $this->exposeMethod('valid');
    }

    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    function valid(Vtiger_Request $request) {
        global $adb;
        $response = new Vtiger_Response();
        $module = $request->getModule();
        $adb->pquery("UPDATE `vte_modules` SET `valid`='1' WHERE (`module`=?);",array($module));
        $response->setResult('success');
        $response->emit();
    }

    function activate(Vtiger_Request $request) {
        global $site_URL;
        $response = new Vtiger_Response();
        $module = $request->getModule();

        try{
            $vTELicense=new ControlLayoutFields_VTELicense_Model($module);
            $data=array('site_url'=>$site_URL,'license'=>$request->get('license'));
            $vTELicense->activateLicense($data);
            $response->setResult(array('message'=>$vTELicense->message));
        }catch(Exception $e) {
            $response->setError($e->getCode(),$e->getMessage());
        }
        $response->emit();
    }
}
<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class ControlLayoutFields_Save_Action extends Vtiger_Save_Action {
    public function checkPermission(Vtiger_Request $request) {
        return;
    }
	public function process(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->get('selected_module');
		$conditions = $request->get('conditions');
        $descriptions = $request->get('descriptions');
		//$filterSavedInNew = $request->get('filtersavedinnew');
        $response = new Vtiger_Response();
        $adb = PearDatabase::getInstance();
        $json = new Zend_Json();
        if(empty($recordId)) {
            $sql="INSERT INTO `vte_control_layout_fields` (`module`, `description`,`condition`) VALUES (?, ?, ?)";
            $adb->pquery($sql,array($moduleName,$descriptions,$json->encode($conditions)));
            $recordId = $adb->getLastInsertID();
        }else {
            $sql="UPDATE `vte_control_layout_fields` SET `module`=?, `description`=?,`condition`=?  WHERE `id`=?";
            $adb->pquery($sql,array($moduleName,$descriptions, $json->encode($conditions),$recordId));
        }
		$response->setResult(array('id' => $recordId,'selected_module'=>$moduleName));
		$response->emit();
	}
        
    public function validateRequest(Vtiger_Request $request) {
        $request->validateWriteAccess();
    }
} 
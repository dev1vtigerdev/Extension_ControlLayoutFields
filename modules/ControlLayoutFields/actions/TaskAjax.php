<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ControlLayoutFields_TaskAjax_Action extends Vtiger_IndexAjax_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('Delete');
		$this->exposeMethod('ChangeStatus');
		$this->exposeMethod('Save');
	}

	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}

	public function Delete(Vtiger_Request $request){
		$record = $request->get('task_id');
		if(!empty($record)) {
            ControlLayoutFields_TaskRecord_Model::delete($record);
			$response = new Vtiger_Response();
			$response->setResult(array('ok'));
			$response->emit();
		}
	}

	public function ChangeStatus(Vtiger_Request $request) {
		$record = $request->get('task_id');
		if(!empty($record)) {
            ControlLayoutFields_TaskRecord_Model::active($request);
			$response = new Vtiger_Response();
			$response->setResult(array('ok'));
			$response->emit();
		}
	}

	public function Save(Vtiger_Request $request) {
		$clfId = $request->get('for_clf');
		if(!empty($clfId)) {
            ControlLayoutFields_TaskRecord_Model::save($request);
			$response = new Vtiger_Response();
			$response->setResult(array('for_clf'=>$clfId));
			$response->emit();
		}
	}
    public function validateRequest(Vtiger_Request $request) {
        $request->validateWriteAccess();
    }
}
<?php
/* ********************************************************************************
 * The content of this file is subject to the VTEQuickEdit ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class ControlLayoutFields_QuickEditAjax_View extends Vtiger_QuickCreateAjax_View {
    function __construct() {
        parent::__construct();
        $this->vteLicense();
    }

    function vteLicense() {
        $vTELicense=new ControlLayoutFields_VTELicense_Model('ControlLayoutFields');
		if(!$vTELicense->validate()){
			header("Location: index.php?module=ControlLayoutFields&parent=Settings&view=ListAll&mode=step2");
		}
    }
    
    public function process(Vtiger_Request $request) {
        global  $adb;
        $viewer = $this->getViewer($request);
        $moduleName = $request->get('moduleEditName');

        $recordId = $request->get('record');
        $request->set('module',  $moduleName);

        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $fieldList = $moduleModel->getFields();
        $strfields = $request->get('arrFieldsName');
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        foreach($requestFieldList as $fieldName => $fieldValue){
            $fieldModel = $fieldList[$fieldName];
            foreach($strfields as $strfieldName){
                if ($fieldModel->isEditable() && in_array($fieldName, $strfieldName)) {
                    $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
                }
            }
        }
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE',Vtiger_Functions::jsonEncode($picklistDependencyDatasource));
        $viewer->assign('RECORD_ID',$recordId);
        $viewer->assign('RECORD_MODEL',$recordModel);
        $viewer->assign('ALL_FIELDS',$fieldList);
        $viewer->assign('ADD_FIELDS', $strfields);
        $viewer->assign('MODULE_NAME',$moduleName);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        echo $viewer->view('ControlLayoutFieldsQuickEdit.tpl','ControlLayoutFields',true);
    }

}
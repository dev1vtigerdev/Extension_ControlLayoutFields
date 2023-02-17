<?php
/* * *******************************************************************************
 * The content of this file is subject to the Conditional Layouts  ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class ControlLayoutFields_Settings_Model extends Vtiger_Base_Model {

	var $user;
	var $db;
	
	function __construct() {
		global $current_user;

		$this->user = $current_user;
		$this->db = PearDatabase::getInstance();
	}
    function getEntityModulesName(){
        $ignore_modules = "'Documents','Calendar','Emails','Commnets','PBXManager','SMSNotifier','Webmails'";
        $result = $this->db->pquery("SELECT vtiger_tab.*
                                    FROM vtiger_tab
                                    WHERE vtiger_tab.isentitytype = 1
                                        AND vtiger_tab.customized = 0
                                        AND vtiger_tab.presence = 0
                                        AND vtiger_tab.name NOT IN( $ignore_modules )
                                    ORDER BY vtiger_tab.name ", array());
        $arr = array();
        if($this->db->num_rows($result)){
            while($row = $this->db->fetch_array($result)){
                $row['module_name'] = $row['name'];
                $row['name'] = vtranslate($row['name'], $row['name']);
                $arr[] = $row;
            }
        }
        return $arr;
    }

    function saveModuleSetting($data){
        $module_setting = $this -> getModuleSettings($data['module_name']);
        if(!$module_setting){
            $this->db->pquery('INSERT INTO vte_document_manager_setting(`module`,`settings`) VALUES(?,?)', array($data['module_name'],$data['module_setting']));
        }
        else{
            $this->db->pquery('UPDATE vte_document_manager_setting SET `settings` =  ? WHERE id=?', array($data['module_setting'],$module_setting['id']));
        }
        //Insert to link table
        $obj_settings = json_decode($data['module_setting']);
        $tabId = getTabid($data['module_name']);
        $url = "module=ControlLayoutFields&view=TreeFolder&mode=showWidget&parent_module=".$data['module_name'];
        if($obj_settings -> enable_widget == 1){
            Vtiger_Link:: addLink($tabId, 'DETAILVIEWSIDEBARWIDGET', 'Documents', $url);
        }
        else{
            Vtiger_Link:: deleteLink($tabId, 'DETAILVIEWSIDEBARWIDGET', 'Documents', $url);
        }
        return true;
    }
    function getModuleSettings($moduleName){
       $result = $this->db->pquery('SELECT * FROM vte_document_manager_setting
                                    WHERE vte_document_manager_setting.module LIKE ?
                                    LIMIT 0, 1',
                                    array($moduleName));
        $module_data = array();
        if($this->db->num_rows($result) > 0){
            $module_data['id'] = $this->db->query_result($result, 0, 'id');
            $module_data['module'] = $moduleName;
            $module_data['settings'] =$this->db->query_result($result, 0, 'settings');
        }
        else {
            return false;
        }
		return $module_data;
    }
}
<?php
/* ********************************************************************************
 * The content of this file is subject to the Conditional Layouts ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once 'vtlib/Vtiger/Module.php';

class ControlLayoutFields extends CRMEntity {
	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		if($event_type == 'module.postinstall') {
            self::addWidgetTo();
            self::resetValid();
		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
            self::removeWidgetTo();
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.
            self::addWidgetTo();
		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.
            self::removeWidgetTo();
            self::removeValid();
		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {
            self::removeWidgetTo();
			self::addWidgetTo();
			self::resetValid();
		}
	}

    static function resetValid() {
        global $adb;
        $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;",array('ControlLayoutFields'));
        $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);",array('ControlLayoutFields','0'));
    }
    static function removeValid() {
        global $adb;
        $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;",array('ControlLayoutFields'));
    }
    /**
     * Add header script to other module.
     * @return unknown_type
     */
    static function addWidgetTo() {
        global $adb,$vtiger_current_version;
        $widgetType = 'HEADERSCRIPT';
        $widgetName = 'ControlLayoutFieldsJs';
        if(version_compare($vtiger_current_version, '7.0.0', '<')) {
            $template_folder= "layouts/vlayout";
        }else{
            $template_folder= "layouts/v7";
        }
        $link = $template_folder . '/modules/ControlLayoutFields/resources/ControlLayoutFields.js';
        include_once 'vtlib/Vtiger/Module.php';

        $moduleNames = array('ControlLayoutFields');
        foreach($moduleNames as $moduleName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if($module) {
                $module->addLink($widgetType, $widgetName, $link);
            }
        }
        $max_id=$adb->getUniqueID('vtiger_settings_field');
		$result =  $adb->pquery("SELECT * FROM vtiger_settings_field WHERE `name` = ?",array('Conditional Layouts'));
		$numRows = $adb->num_rows($result);	
		if(!($numRows > 0)){
			$adb->pquery("INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `description`, `linkto`, `sequence`) VALUES (?, ?, ?, ?, ?, ?)",array($max_id,'4','Conditional Layouts','Settings area for Conditional Layouts', 'index.php?module=ControlLayoutFields&parent=Settings&view=ListAll&mode=listAll',$max_id));
		}        
    }

    static function removeWidgetTo() {
        global $adb,$vtiger_current_version;
        $widgetType = 'HEADERSCRIPT';
        $widgetName = 'ControlLayoutFieldsJs';
        if(version_compare($vtiger_current_version, '7.0.0', '<')) {
            $template_folder= "layouts/vlayout";
            $vtVersion='vt6';
            $linkVT6 = $template_folder.'/modules/ControlLayoutFields/resources/ControlLayoutFields.js';
        }else{
            $template_folder= "layouts/v7";
            $vtVersion='vt7';
        }
        $link = $template_folder . '/modules/ControlLayoutFields/resources/ControlLayoutFields.js';
        include_once 'vtlib/Vtiger/Module.php';

        $moduleNames = array('ControlLayoutFields');
        foreach($moduleNames as $moduleName) {
            $module = Vtiger_Module::getInstance($moduleName);
            if($module) {
                $module->deleteLink($widgetType, $widgetName, $link);

                // remove existed link on vt6 when current vt is vt7
                if($vtVersion!='vt6'){
                    $module->deleteLink($widgetType, $widgetName, $linkVT6);
                }
            }
        }
        $adb->pquery("DELETE FROM vtiger_settings_field WHERE `name` = ?", array('Conditional Layouts'));
		
		if(version_compare($vtiger_current_version, '7.0.0', '<')) {
			$needDeleteFolder = 'layouts/v7';
		}else{
            $needDeleteFolder= "layouts/vlayout";
        }
		
		if (is_dir($needDeleteFolder)) {
			rmdir($needDeleteFolder);
		}
    }
}
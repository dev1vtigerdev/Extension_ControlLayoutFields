<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class ControlLayoutFields_Record_Model extends Vtiger_Record_Model {
    public function getTasks(){
        //$id = $this ->getId();
        $adb = PearDatabase::getInstance();
        $sql="SELECT * FROM vte_control_layout_fields_task WHERE clf_id = ?";
        $result = $adb->pquery($sql,array($this ->getId()));
        $taskList = array();
        $noOfFields = $adb->num_rows($result);
        if($noOfFields > 0){
            for ($i = 0; $i < $noOfFields; ++$i) {
                $taskId = $adb->query_result($result, $i, 'id');
                $clf_id= $adb->query_result($result, $i, 'clf_id');
                $active = $adb->query_result($result, $i, 'active');
                $title = $adb->query_result($result, $i, 'name');
                $actions = $adb->query_result($result, $i, 'actions');
                $active_link = '?module=ControlLayoutFields&parent=Settings&action=TaskAjax&mode=ChangeStatus&task_id='.$taskId.'&active='.$active;
                $remove_link = '?module=ControlLayoutFields&parent=Settings&action=TaskAjax&mode=Delete&task_id='.$taskId;
                $taskList[$i] = array('id' =>$taskId,'active' => $active,'title' =>$title,'clf_id' =>$clf_id,'actions' =>$actions,'active_url' =>$active_link,'remove_link' =>$remove_link);
            }
        }
        return $taskList;
    }
    public function getInfo(){
        $adb = PearDatabase::getInstance();
        $sql="SELECT * FROM vte_control_layout_fields WHERE id = ? LIMIT 0,1";
        $result = $adb->pquery($sql,array($this ->getId()));
        $clf_info = array();
        if($noOfFields = $adb->num_rows($result) > 0){
            $clf_info['id'] = $adb->query_result($result, 0, 'id');
            $clf_info['module'] = $adb->query_result($result, 0, 'module');
            $clf_info['description'] = $adb->query_result($result, 0, 'description');
            $clf_info['condition'] = $adb->query_result($result, 0, 'condition');
        }
        return $clf_info;
    }
//Enc class
}
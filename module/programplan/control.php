<?php
/**
 * The control file of programplan currentModule of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     programplan
 * @version     $Id: control.php 5107 2013-07-12 01:46:12Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class programplan extends control
{
    /**
     * __construct
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
    }

    /**
     * Common action.
     *
     * @param  int     $projectID
     * @param  int     $productID
     * @param  string  $extra
     * @access public
     * @return void
     */
    public function commonAction($projectID, $productID = 0, $extra = '')
    {
        $products  = $this->loadModel('product')->getProductPairsByProject($projectID);
        $productID = $this->product->saveState($productID, $products);
        $project   = $this->loadModel('project')->getByID($projectID);

        $this->session->set('hasProduct', $project->hasProduct);
        $this->productID = $productID;
        $this->project->setMenu($projectID);
    }

    /**
     * Browse program plans.
     *
     * @param  int     $projectID
     * @param  int     $productID
     * @param  string  $type
     * @param  string  $orderBy
     * @param  int     $baselineID
     * @access public
     * @return void
     */
    public function browse($projectID = 0, $productID = 0, $type = 'gantt', $orderBy = 'id_asc', $baselineID = 0)
    {
        $this->app->loadLang('stage');
        $this->commonAction($projectID, $productID, $type);
        $this->session->set('projectPlanList', $this->app->getURI(true), 'project');

        if(!defined('RUN_MODE') || RUN_MODE != 'api') $projectID = $this->project->saveState((int)$projectID, $this->project->getPairsByProgram());

        $products = $this->loadModel('product')->getProducts($projectID);
        if($this->session->hasProduct) $this->lang->modulePageNav = $this->product->select($products, $this->productID, 'programplan', 'browse', $type, 0, 0, '', false);

        $selectCustom = 0; // Display date and task settings.
        $dateDetails  = 1; // Gantt chart detail date display.
        if($type == 'gantt')
        {
            $this->loadModel('setting');
            $owner        = $this->app->user->account;
            $module       = 'programplan';
            $section      = 'browse';
            $object       = 'stageCustom';
            if(!isset($this->config->programplan->browse->stageCustom)) $this->setting->setItem("$owner.$module.browse.stageCustom", 'date,task');

            $selectCustom = $this->setting->getItem("owner={$owner}&module={$module}&section={$section}&key={$object}");

            if(strpos($selectCustom, 'date') !== false) $dateDetails = 0;

            $plans = $this->programplan->getDataForGantt($projectID, $this->productID, $baselineID, $selectCustom, false);

            /* Set Custom. */
            foreach(explode(',', $this->config->programplan->custom->customGanttFields) as $field) $customFields[$field] = $this->lang->programplan->ganttCustom[$field];
            $this->view->customFields = $customFields;
            $this->view->showFields   = $this->config->programplan->ganttCustom->ganttFields;
        }

        if($type == 'assignedTo')
        {
            $owner        = $this->app->user->account;
            $module       = 'programplan';
            $section      = 'browse';
            $object       = 'stageCustom';
            $selectCustom = $this->loadModel('setting')->getItem("owner={$owner}&module={$module}&section={$section}&key={$object}");
            if(strpos($selectCustom, 'date') !== false) $dateDetails = 0;

            $plans = $this->programplan->getDataForGanttGroupByAssignedTo($projectID, $this->productID, $baselineID, $selectCustom, false);

            /* Set Custom. */
            foreach(explode(',', $this->config->programplan->custom->customGanttFields) as $field) $customFields[$field] = $this->lang->programplan->ganttCustom[$field];
            $this->view->customFields = $customFields;
            $this->view->showFields   = $this->config->programplan->ganttCustom->ganttFields;
        }

        if($type == 'lists')
        {
            $sort  = common::appendOrder($orderBy);
            $this->loadModel('datatable');
            $plans = $this->programplan->getPlans($projectID, $this->productID, $sort);
        }

        $zooming = !empty($this->config->programplan->ganttCustom->zooming) ? $this->config->programplan->ganttCustom->zooming : 'day';
        $this->view->title        = $this->lang->programplan->browse;
        $this->view->position[]   = $this->lang->programplan->browse;
        $this->view->projectID    = $projectID;
        $this->view->project      = $this->project->getByID($projectID);
        $this->view->productID    = $this->productID;
        $this->view->productList  = $this->loadModel('product')->getProductPairsByProject($projectID);
        $this->view->type         = $type;
        $this->view->plans        = $plans;
        $this->view->orderBy      = $orderBy;
        $this->view->selectCustom = $selectCustom;
        $this->view->dateDetails  = $dateDetails;
        $this->view->users        = $this->loadModel('user')->getPairs('noletter');
        $this->view->zooming      = $zooming;
        $this->view->ganttType    = $type;
        $this->display();
    }

    /**
     * Create a project plan.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  int    $planID
     * @access public
     * @return void
     */
    public function create($projectID = 0, $productID = 0, $planID = 0)
    {
        $this->commonAction($projectID, $productID);
        $this->app->loadLang('project');
        if($_POST)
        {
            $this->programplan->create($projectID, $this->productID, $planID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $locate = $this->createLink('project', 'execution', "status=all&projectID=$projectID&orderBy=order_asc&productID=$productID");
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locate));
        }

        $productList = array();
        $this->app->loadLang('stage');
        $project = $this->loadModel('project')->getById($projectID);
        if($this->session->hasProduct) $productList = $this->loadModel('product')->getProductPairsByProject($projectID);

        $this->view->title      = $this->lang->programplan->create . $this->lang->colon . $project->name;
        $this->view->position[] = html::a($this->createLink('programplan', 'browse', "projectID=$projectID"), $project->name);
        $this->view->position[] = $this->lang->programplan->create;

        $visibleFields  = array();
        $requiredFields = array();
        foreach(explode(',', $this->config->programplan->customCreateFields) as $field) $customFields[$field] = $this->lang->programplan->$field;
        $showFields = $this->config->programplan->custom->createFields;
        foreach(explode(',', $showFields) as $field)
        {
            if($field) $visibleFields[$field] = '';
        }

        foreach(explode(',', $this->config->programplan->create->requiredFields) as $field)
        {
            if($field)
            {
                $requiredFields[$field] = '';
                if(strpos(",{$this->config->programplan->customCreateFields},", ",{$field},") !== false) $visibleFields[$field] = '';
            }
        }

        $this->view->productList    = $productList;
        $this->view->project        = $project;
        $this->view->productID      = $productID ? $productID : key($productList);
        $this->view->stages         = empty($planID) ? $this->loadModel('stage')->getStages('id_asc') : array();
        $this->view->programPlan    = $this->project->getById($planID, 'stage');
        $this->view->plans          = $this->programplan->getStage($planID ? $planID : $projectID, $this->productID, 'parent');
        $this->view->planID         = $planID;
        $this->view->type           = 'lists';
        $this->view->PMUsers        = $this->loadModel('user')->getPairs('noclosed|nodeleted|pmfirst',  $project->PM);
        $this->view->customFields   = $customFields;
        $this->view->showFields     = $showFields;
        $this->view->visibleFields  = $visibleFields;
        $this->view->requiredFields = $requiredFields;
        $this->view->colspan        = count($visibleFields) + 3;

        $this->display();
    }

    /**
     * Edit a project plan.
     *
     * @param  int    $planID
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function edit($planID = 0, $projectID = 0)
    {
        $this->app->loadLang('project');
        $this->app->loadLang('execution');
        $plan = $this->programplan->getByID($planID);

        global $lang;
        $lang->executionCommon = $lang->execution->stage;
        include $this->app->getModulePath('', 'execution') . 'lang/' . $this->app->getClientLang() . '.php';

        if($_POST)
        {
            $changes = $this->programplan->update($planID, $projectID);

            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('execution', $planID, 'edited');
                $this->action->logHistory($actionID, $changes);
            }
            $locate = isonlybody() ? 'parent' : inlink('browse', "program=$plan->program&type=lists");
            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $locate));
        }

        $this->app->loadLang('stage');
        $this->view->title        = $this->lang->programplan->edit;
        $this->view->position[]   = $this->lang->programplan->edit;
        $this->view->parentStage  = $this->programplan->getParentStageList($this->session->project, $planID, $plan->product);
        $this->view->isCreateTask = $this->programplan->isCreateTask($planID);
        $this->view->plan         = $plan;

        $this->display();
    }

    /**
     * Save custom settings via ajax.
     *
     * @access public
     * @return void
     */
    public function ajaxCustom()
    {
        $owner  = $this->app->user->account;
        $module = 'programplan';
        $this->app->loadLang('execution');
        $this->loadModel('datatable');
        $this->loadModel('setting');

        $stageCustom = $this->setting->getItem("owner=$owner&module=$module&section=browse&key=stageCustom");
        $ganttFields = $this->setting->getItem("owner=$owner&module=$module&section=ganttCustom&key=ganttFields");
        $zooming     = $this->setting->getItem("owner=$owner&module=$module&section=ganttCustom&key=zooming");

        if($_POST)
        {
            $data        = fixer::input('post')->get();
            $zooming     = empty($data->zooming) ? '' : $data->zooming;
            $stageCustom = empty($data->stageCustom) ? '' : implode(',', $data->stageCustom);
            $ganttFields = empty($data->ganttFields) ? '' : implode(',', $data->ganttFields);

            $this->setting->setItem("$owner.$module.browse.stageCustom", $stageCustom);
            $this->setting->setItem("$owner.$module.ganttCustom.ganttFields", $ganttFields);
            $this->setting->setItem("$owner.$module.ganttCustom.zooming", $zooming);

            return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'parent'));
        }

        /* Set Custom. */
        foreach(explode(',', $this->config->programplan->custom->customGanttFields) as $field) $customFields[$field] = $this->lang->programplan->ganttCustom[$field];

        $this->view->zooming      = $zooming;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->programplan->ganttCustom->ganttFields;
        $this->view->ganttFields  = $ganttFields;
        $this->view->stageCustom  = $stageCustom;

        $this->display();
    }

    /**
     * Response gantt drag event.
     *
     * @access public
     * @return void
     */
    public function ajaxResponseGanttDragEvent()
    {
        if(!empty($_POST))
        {
            if(!isset($_POST['id']) or empty($_POST['id'])) return $this->send(array('result' => 'fail', 'message' => ''));
            $objectID =  $_POST['id'];

            $this->loadModel('task')->updateEsDateByGantt($objectID, $_POST['type']);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            return $this->send(array('result' => 'success'));
        }
    }

    /**
     * Response gantt move event.
     *
     * @access public
     * @access public
     * @return void
     */
    public function ajaxResponseGanttMoveEvent()
    {
        if(!empty($_POST))
        {
            $idList = explode('-', $_POST['id']);
            $taskID = $idList[1];

            $this->loadModel('task')->updateOrderByGantt();
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('task', $taskID, 'ganttMove');
            return $this->send(array('result' => 'success'));
        }
    }
}

<?php
/**
 * The control file of case currentModule of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     case
 * @version     $Id: control.php 5112 2013-07-12 02:51:33Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class testcase extends control
{
    /**
     * All products.
     *
     * @var    array
     * @access public
     */
    public $products = array();

    /**
     * Project id.
     *
     * @var    int
     * @access public
     */
    public $projectID = 0;

    /**
     * Construct function, load product, tree, user auto.
     *
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $this->loadModel('product');
        $this->loadModel('tree');
        $this->loadModel('user');
        $this->loadModel('qa');

        /* Get product data. */
        $products = array();
        $objectID = 0;
        $tab      = ($this->app->tab == 'project' or $this->app->tab == 'execution') ? $this->app->tab : 'qa';
        if(!isonlybody())
        {
            if($this->app->tab == 'project')
            {
                $objectID = $this->session->project;
                $products = $this->product->getProducts($objectID, 'all', '', false);
            }
            elseif($this->app->tab == 'execution')
            {
                $objectID = $this->session->execution;
                $products = $this->product->getProducts($objectID, 'all', '', false);
            }
            else
            {
                $mode     = ($this->app->methodName == 'create' and empty($this->config->CRProduct)) ? 'noclosed' : '';
                $products = $this->product->getPairs($mode, 0, '', 'all');
            }
            if(empty($products) and !helper::isAjaxRequest()) return print($this->locate($this->createLink('product', 'showErrorNone', "moduleName=$tab&activeMenu=testcase&objectID=$objectID")));
        }
        else
        {
            $products = $this->product->getPairs('', 0, '', 'all');
        }
        $this->view->products = $this->products = $products;
    }

    /**
     * Browse cases.
     *
     * @param  int        $productID
     * @param  int|string $branch
     * @param  string     $browseType
     * @param  int        $param
     * @param  string     $orderBy
     * @param  int        $recTotal
     * @param  int        $recPerPage
     * @param  int        $pageID
     * @param  int        $projectID
     * @access public
     * @return void
     */
    public function browse($productID = 0, $branch = '', $browseType = 'all', $param = 0, $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1, $projectID = 0)
    {
        $this->loadModel('datatable');

        /* Set browse type. */
        $browseType = strtolower($browseType);

        /* Set browseType, productID, moduleID and queryID. */
        $productID = $this->app->tab != 'project' ? $this->product->saveState($productID, $this->products) : $productID;
        $branch    = ($this->cookie->preBranch !== '' and $branch === '') ? $this->cookie->preBranch : $branch;
        setcookie('preProductID', $productID, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);
        setcookie('preBranch', $branch, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, true);

        if($this->cookie->preProductID != $productID or $this->cookie->preBranch != $branch)
        {
            $_COOKIE['caseModule'] = 0;
            setcookie('caseModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        }
        if($browseType == 'bymodule') setcookie('caseModule', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
        if($browseType == 'bysuite')  setcookie('caseSuite', (int)$param, 0, $this->config->webRoot, '', $this->config->cookieSecure, true);
        if($browseType != 'bymodule') $this->session->set('caseBrowseType', $browseType);

        $moduleID = ($browseType == 'bymodule') ? (int)$param : ($browseType == 'bysearch' ? 0 : ($this->cookie->caseModule ? $this->cookie->caseModule : 0));
        $suiteID  = ($browseType == 'bysuite') ? (int)$param : ($browseType == 'bymodule' ? ($this->cookie->caseSuite ? $this->cookie->caseSuite : 0) : 0);
        $queryID  = ($browseType == 'bysearch') ? (int)$param : 0;

        /* Set menu, save session. */
        if($this->app->tab == 'project')
        {
            $linkedProducts = $this->product->getProducts($projectID, 'all', '', false);
            $this->products = count($linkedProducts) > 1 ? array('0' => $this->lang->product->all) + $linkedProducts : $linkedProducts;
            $productID      = count($linkedProducts) > 1 ? $productID : key($linkedProducts);
            $hasProduct     = $this->dao->findById($projectID)->from(TABLE_PROJECT)->fetch('hasProduct');
            if(!$hasProduct) unset($this->config->testcase->search['fields']['product']);

            $this->loadModel('project')->setMenu($projectID);
        }
        else
        {
            $this->qa->setMenu($this->products, $productID, $branch, $browseType);
        }

        $uri = $this->app->getURI(true);
        $this->session->set('caseList', $uri, $this->app->tab);
        $this->session->set('productID', $productID);
        $this->session->set('moduleID', $moduleID);
        $this->session->set('browseType', $browseType);
        $this->session->set('orderBy', $orderBy);

        /* Load lang. */
        $this->app->loadLang('testtask');

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);
        $sort  = common::appendOrder($orderBy);

        /* Get test cases. */
        $cases = $this->testcase->getTestCases($productID, $branch, $browseType, $browseType == 'bysearch' ? $queryID : $suiteID, $moduleID, $sort, $pager);
        if(empty($cases) and $pageID > 1)
        {
            $pager = pager::init(0, $recPerPage, 1);
            $cases = $this->testcase->getTestCases($productID, $branch, $browseType, $browseType == 'bysearch' ? $queryID : $suiteID, $moduleID, $sort, $pager);
        }

        /* save session .*/
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', false);

        /* Process case for check story changed. */
        $cases = $this->loadModel('story')->checkNeedConfirm($cases);
        $cases = $this->testcase->appendData($cases);

        /* Build the search form. */
        $currentModule = $this->app->tab == 'project' ? 'project'  : 'testcase';
        $currentMethod = $this->app->tab == 'project' ? 'testcase' : 'browse';
        $projectParam  = $this->app->tab == 'project' ? "projectID={$this->session->project}&" : '';
        $actionURL = $this->createLink($currentModule, $currentMethod, $projectParam . "productID=$productID&branch=$branch&browseType=bySearch&queryID=myQueryID");
        $this->config->testcase->search['onMenuBar'] = 'yes';

        $searchProducts = $this->product->getPairs('', 0, '', 'all');
        $this->testcase->buildSearchForm($productID, $searchProducts, $queryID, $actionURL, $projectID);

        $showModule = !empty($this->config->datatable->testcaseBrowse->showModule) ? $this->config->datatable->testcaseBrowse->showModule : '';

        /* Get module tree.*/
        if($projectID and empty($productID))
        {
            $moduleTree = $this->tree->getCaseTreeMenu($projectID, $productID, 0, array('treeModel', 'createCaseLink'));
        }
        else
        {
            $moduleTree = $this->tree->getTreeMenu($productID, 'case', 0, array('treeModel', 'createCaseLink'), array('projectID' => $projectID, 'productID' => $productID), $branch);
        }

        $product = $this->product->getById($productID);

        $showBranch      = false;
        $branchOption    = array();
        $branchTagOption = array();
        if($product and $product->type != 'normal')
        {
            /* Display of branch label. */
            $showBranch = $this->loadModel('branch')->showBranch($productID);

            /* Display status of branch. */
            $branches = $this->loadModel('branch')->getList($productID, $projectID, 'all');
            foreach($branches as $branchInfo)
            {
                $branchOption[$branchInfo->id]    = $branchInfo->name;
                $branchTagOption[$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
            }
        }

        /* Assign. */
        $tree = $moduleID ? $this->tree->getByID($moduleID) : '';
        $this->view->title           = $this->products[$productID] . $this->lang->colon . $this->lang->testcase->common;
        $this->view->position[]      = html::a($this->createLink('testcase', 'browse', "productID=$productID&branch=$branch"), $this->products[$productID]);
        $this->view->position[]      = $this->lang->testcase->common;
        $this->view->projectID       = $projectID;
        $this->view->productID       = $productID;
        $this->view->product         = $product;
        $this->view->productName     = $this->products[$productID];
        $this->view->modules         = $this->tree->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, $branch == 'all' ? '0' : $branch);
        $this->view->moduleTree      = $moduleTree;
        $this->view->moduleName      = $moduleID ? $tree->name : $this->lang->tree->all;
        $this->view->moduleID        = $moduleID;
        $this->view->projectType     = !empty($projectID) ? $this->dao->select('model')->from(TABLE_PROJECT)->where('id')->eq($projectID)->fetch('model') : '';
        $this->view->summary         = $this->testcase->summary($cases);
        $this->view->pager           = $pager;
        $this->view->users           = $this->user->getPairs('noletter');
        $this->view->orderBy         = $orderBy;
        $this->view->browseType      = $browseType;
        $this->view->param           = $param;
        $this->view->cases           = $cases;
        $this->view->branch          = (!empty($product) and $product->type != 'normal') ? $branch : 0;
        $this->view->branchOption    = $branchOption;
        $this->view->branchTagOption = $branchTagOption;
        $this->view->suiteList       = $this->loadModel('testsuite')->getSuites($productID);
        $this->view->suiteID         = $suiteID;
        $this->view->setModule       = true;
        $this->view->modulePairs     = $showModule ? $this->tree->getModulePairs($productID, 'case', $showModule) : array();
        $this->view->showBranch      = $showBranch;
        $this->view->libraries       = $this->loadModel('caselib')->getLibraries();

        $this->display();
    }

    /**
     * Group case.
     *
     * @param  int    $productID
     * @param  string $groupBy
     * @access public
     * @return void
     */
    public function groupCase($productID = 0, $branch = '', $groupBy = 'story', $projectID = 0)
    {
        $groupBy   = empty($groupBy) ? 'story' : $groupBy;
        $productID = $this->product->saveState($productID, $this->products);
        if($branch === '') $branch = $this->cookie->preBranch;

        $this->app->loadLang('testtask');

        $this->app->tab == 'project' ? $this->loadModel('project')->setMenu($this->session->project) : $this->testcase->setMenu($this->products, $productID, $branch);
        if($this->app->tab == 'project')
        {
            $products = array('0' => $this->lang->product->all) + $this->product->getProducts($this->session->project, 'all', '', false);
            $this->lang->modulePageNav = $this->product->select($products, $productID, 'testcase', 'groupCase', "projectID=$projectID", $branch);
        }

        $this->session->set('caseList', $this->app->getURI(true), $this->app->tab);

        $cases = $this->testcase->getModuleCases($productID, $branch, 0, $groupBy);
        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', false);
        $cases = $this->loadModel('story')->checkNeedConfirm($cases);
        $cases = $this->testcase->appendData($cases);

        $groupCases  = array();
        $groupByList = array();
        foreach($cases as $case)
        {
            if($groupBy == 'story')
            {
                if($case->storyDeleted) continue;
                $groupCases[$case->story][] = $case;
                $groupByList[$case->story]  = $case->storyTitle;
            }
        }

        $this->app->loadLang('execution');
        $this->app->loadLang('task');

        $this->view->title       = $this->products[$productID] . $this->lang->colon . $this->lang->testcase->common;
        $this->view->position[]  = html::a($this->createLink('testcase', 'groupTask', "productID=$productID&groupBy=$groupBy"), $this->products[$productID]);
        $this->view->position[]  = $this->lang->testcase->common;
        $this->view->projectID   = $projectID;
        $this->view->productID   = $productID;
        $this->view->productName = $this->products[$productID];
        $this->view->users       = $this->user->getPairs('noletter');
        $this->view->browseType  = 'group';
        $this->view->groupBy     = $groupBy;
        $this->view->orderBy     = $groupBy;
        $this->view->groupByList = $groupByList;
        $this->view->cases       = $groupCases;
        $this->view->suiteList   = $this->loadModel('testsuite')->getSuites($productID);
        $this->view->suiteID     = 0;
        $this->view->moduleID    = 0;
        $this->view->branch      = $branch;
        $this->view->product     = $this->product->getByID($productID);
        $this->display();
    }

    /**
     * Show zero case story.
     *
     * @param  int    $productID
     * @param  int    $branchID
     * @param  string $orderBy
     * @param  int    $projectID
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function zeroCase($productID = 0, $branchID = 0, $orderBy = 'id_desc', $projectID = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $orderBy = empty($orderBy) ? 'id_desc' : $orderBy;
        $this->session->set('storyList', $this->app->getURI(true) . '#app=' . $this->app->tab, 'product');
        $this->session->set('caseList', $this->app->getURI(true), $this->app->tab);

        $this->loadModel('story');
        if($this->app->tab == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
            $this->app->rawModule = 'qa';
            $this->lang->project->menu->qa['subMenu']->testcase['subModule'] = 'story';
            $products  = $this->product->getProducts($this->session->project, 'all', '', false);
            $productID = $this->product->saveState($productID, $products);
            $this->lang->modulePageNav = $this->product->select($products, $productID, 'testcase', 'zeroCase', "projectID=$projectID", $branchID);
        }
        else
        {
            $products  = $this->product->getPairs();
            $productID = $this->product->saveState($productID, $products);
            $this->loadModel('qa');
            $this->app->rawModule = 'testcase';
            foreach($this->config->qa->menuList as $module) $this->lang->navGroup->$module = 'qa';
            $this->qa->setMenu($products, $productID, $branchID);
        }

        /* Append id for secend sort. */
        $sort    = common::appendOrder($orderBy);
        $stories = $this->story->getZeroCase($productID, $branchID, $sort);

        /* Pager. */
        $this->app->loadClass('pager', $static = true);
        $recTotal = count($stories);
        $pager    = new pager($recTotal, $recPerPage, $pageID);
        $stories  = array_chunk($stories, $pager->recPerPage);

        $this->view->title      = $this->lang->story->zeroCase;
        $this->view->position[] = html::a($this->createLink('testcase', 'browse', "productID=$productID"), $products[$productID]);
        $this->view->position[] = $this->lang->story->zeroCase;

        $this->view->stories    = empty($stories) ? $stories : $stories[$pageID - 1];
        $this->view->users      = $this->user->getPairs('noletter');
        $this->view->projectID  = $projectID;
        $this->view->productID  = $productID;
        $this->view->branchID   = $branchID;
        $this->view->orderBy    = $orderBy;
        $this->view->suiteList  = $this->loadModel('testsuite')->getSuites($productID);
        $this->view->browseType = '';
        $this->view->product    = $this->product->getByID($productID);
        $this->view->pager      = $pager;
        $this->display();
    }

    /**
     * Create a test case.
     * @param        $productID
     * @param string $branch
     * @param int    $moduleID
     * @param string $from
     * @param int    $param
     * @param int    $storyID
     * @param string $extras
     * @access public
     * @return void
     */
    public function create($productID, $branch = '', $moduleID = 0, $from = '', $param = 0, $storyID = 0, $extras = '')
    {
        $testcaseID  = ($from and strpos('testcase|work|contribute', $from) !== false) ? $param : 0;
        $bugID       = $from == 'bug' ? $param : 0;
        $executionID = $from == 'execution' ? $param : 0;

        $extras = str_replace(array(',', ' '), array('&', ''), $extras);
        parse_str($extras, $output);

        $this->loadModel('story');
        if(!empty($_POST))
        {
            $response['result'] = 'success';

            setcookie('lastCaseModule', (int)$this->post->module, $this->config->cookieLife, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $caseResult = $this->testcase->create($bugID);
            if(!$caseResult or dao::isError())
            {
                $response['result']  = 'fail';
                $response['message'] = dao::getError();
                return $this->send($response);
            }

            $caseID = $caseResult['id'];
            if($caseResult['status'] == 'exists')
            {
                $response['message'] = sprintf($this->lang->duplicate, $this->lang->testcase->common);
                $response['locate']  = $this->createLink('testcase', 'view', "caseID=$caseID");
                return $this->send($response);
            }

            $this->loadModel('action');
            $this->action->create('case', $caseID, 'Opened');
            if($this->testcase->getStatus('create') == 'wait') $this->action->create('case', $caseID, 'submitReview');

            /* If the story is linked project, make the case link the project. */
            $this->testcase->syncCase2Project($caseResult['caseInfo'], $caseID);

            $message = $this->executeHooks($caseID);
            if($message) $this->lang->saveSuccess = $message;
            $response['message'] = $this->lang->saveSuccess;

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'id' => $caseID));
            /* If link from no head then reload. */
            if(isonlybody()) return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'closeModal' => true));

            setcookie('caseModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);

            /* Use this session link, when the tab is not QA, a session of the case list exists, and the session is not from the Dynamic page. */
            $useSession         = ($this->app->tab != 'qa' and $this->session->caseList and strpos($this->session->caseList, 'dynamic') === false);
            $locateLink         = $this->app->tab == 'project' ? $this->createLink('project', 'testcase', "projectID={$this->session->project}") : $this->createLink('testcase', 'browse', "productID={$this->post->product}&branch={$this->post->branch}&browseType=all&param=0&orderBy=id_desc");
            $response['locate'] = $useSession ? $this->session->caseList : $locateLink;
            return $this->send($response);
        }
        if(empty($this->products)) $this->locate($this->createLink('product', 'create'));

        /* Init vars. */
        $type         = 'feature';
        $stage        = '';
        $pri          = 3;
        $caseTitle    = '';
        $precondition = '';
        $keywords     = '';
        $steps        = array();
        $color        = '';

        /* If testcaseID large than 0, use this testcase as template. */
        if($testcaseID > 0)
        {
            $testcase     = $this->testcase->getById($testcaseID);
            $productID    = $testcase->product;
            $type         = $testcase->type ? $testcase->type : 'feature';
            $stage        = $testcase->stage;
            $pri          = $testcase->pri;
            $storyID      = $testcase->story;
            $caseTitle    = $testcase->title;
            $precondition = $testcase->precondition;
            $keywords     = $testcase->keywords;
            $steps        = $testcase->steps;
            $color        = $testcase->color;
        }

        /* If bugID large than 0, use this bug as template. */
        if($bugID > 0)
        {
            $bug       = $this->loadModel('bug')->getById($bugID);
            $type      = $bug->type;
            $pri       = $bug->pri ? $bug->pri : $bug->severity;
            $storyID   = $bug->story;
            $caseTitle = $bug->title;
            $keywords  = $bug->keywords;
            $steps     = $this->testcase->createStepsFromBug($bug->steps);
        }

        /* Set productID and branch. */
        $productID = $this->product->saveState($productID, $this->products);
        if($branch === '') $branch = $this->cookie->preBranch;

        /* Set menu. */
        if($this->app->tab == 'project' or $this->app->tab == 'execution') $this->loadModel('execution');
        if($this->app->tab == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
        }
        elseif($this->app->tab == 'execution')
        {
            $this->execution->setMenu($this->session->execution);
        }
        else
        {
            $this->qa->setMenu($this->products, $productID, $branch);
        }

        /* Set branch. */
        $product = $this->product->getById($productID);
        if(!isset($this->products[$productID])) $this->products[$productID] = $product->name;
        if($this->app->tab == 'execution' or $this->app->tab == 'project')
        {
            $objectID        = $this->app->tab == 'project' ? $this->session->project : $executionID;
            $productBranches = (isset($product->type) and $product->type != 'normal') ? $this->execution->getBranchByProduct($productID, $objectID, 'noclosed|withMain') : array();
            $branches        = isset($productBranches[$productID]) ? $productBranches[$productID] : array();
            $branch          = key($branches);
        }
        else
        {
            $branches = (isset($product->type) and $product->type != 'normal') ? $this->loadModel('branch')->getPairs($productID, 'active') : array();
        }

        /* Padding the steps to the default steps count. */
        if(count($steps) < $this->config->testcase->defaultSteps)
        {
            $paddingCount = $this->config->testcase->defaultSteps - count($steps);
            $step = new stdclass();
            $step->type   = 'item';
            $step->desc   = '';
            $step->expect = '';
            for($i = 1; $i <= $paddingCount; $i ++) $steps[] = $step;
        }

        $title      = $this->products[$productID] . $this->lang->colon . $this->lang->testcase->create;
        $position[] = html::a($this->createLink('testcase', 'browse', "productID=$productID&branch=$branch"), $this->products[$productID]);
        $position[] = $this->lang->testcase->common;
        $position[] = $this->lang->testcase->create;

        /* Set story and currentModuleID. */
        if($storyID)
        {
            $story = $this->loadModel('story')->getByID($storyID);
            if(empty($moduleID)) $moduleID = $story->module;
        }

        $currentModuleID = $moduleID ? (int)$moduleID : (int)$this->cookie->lastCaseModule;
        /* Get the status of stories are not closed. */
        $storyStatus = $this->lang->story->statusList;
        unset($storyStatus['closed']);
        $modules = array();
        if($currentModuleID)
        {
            $productModules = $this->tree->getOptionMenu($productID, 'story');
            $storyModuleID  = array_key_exists($currentModuleID, $productModules) ? $currentModuleID : 0;
            $modules        = $this->loadModel('tree')->getStoryModule($storyModuleID);
            $modules        = $this->tree->getAllChildID($modules);
        }

        $stories = $this->story->getProductStoryPairs($productID, $branch, $modules, array_keys($storyStatus), 'id_desc', 50, 'null', 'story', false);
        if($this->app->tab != 'qa' and $this->app->tab != 'product')
        {
            $projectID = $this->app->tab == 'project' ? $this->session->project : $this->session->execution;
            $stories   = $this->story->getExecutionStoryPairs($projectID, $productID, $branch, $modules);
        }
        if($storyID and !isset($stories[$storyID])) $stories = $this->story->formatStories(array($storyID => $story)) + $stories;//Fix bug #2406.
        $productInfo = $this->loadModel('product')->getById($productID);

        /* Set custom. */
        foreach(explode(',', $this->config->testcase->customCreateFields) as $field) $customFields[$field] = $this->lang->testcase->$field;

        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->testcase->custom->createFields;

        $this->view->title            = $title;
        $this->view->position         = $position;
        $this->view->projectID        = isset($projectID) ? $projectID : 0;
        $this->view->productID        = $productID;
        $this->view->executionID      = $executionID;
        $this->view->productInfo      = $productInfo;
        $this->view->productName      = $this->products[$productID];
        $this->view->moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, ($branch === 'all' or !isset($branches[$branch])) ? 0 : $branch);
        $this->view->currentModuleID  = $currentModuleID;
        $this->view->gobackLink       = (isset($output['from']) and $output['from'] == 'global') ? $this->createLink('testcase', 'browse', "productID=$productID") : '';
        $this->view->stories          = $stories;
        $this->view->caseTitle        = $caseTitle;
        $this->view->color            = $color;
        $this->view->type             = $type;
        $this->view->stage            = $stage;
        $this->view->pri              = $pri;
        $this->view->storyID          = $storyID;
        $this->view->precondition     = $precondition;
        $this->view->keywords         = $keywords;
        $this->view->steps            = $steps;
        $this->view->hiddenProduct    = !empty($product->shadow);
        $this->view->users            = $this->user->getPairs('noletter|noclosed|nodeleted');
        $this->view->branch           = $branch;
        $this->view->product          = $product;
        $this->view->branches         = $branches;

        $this->display();
    }


    /**
     * Create a batch test case.
     *
     * @param  int   $productID
     * @param  int   $moduleID
     * @param  int   $storyID
     * @access public
     * @return void
     */
    public function batchCreate($productID, $branch = '', $moduleID = 0, $storyID = 0)
    {
        $this->loadModel('story');
        if(!empty($_POST))
        {
            $caseIDList = $this->testcase->batchCreate($productID, $branch, $storyID);
            if(dao::isError()) return print(js::error(dao::getError()));
            if(isonlybody())
            {
                $execution = $this->loadModel('execution')->getByID($this->session->execution);
                if($this->app->tab == 'execution' and $execution->type == 'kanban')
                {
                    return print(js::closeModal('parent.parent', ''));
                }
                else
                {
                    return print(js::closeModal('parent.parent', 'this'));
                }
            }

            if($this->viewType == 'json') return $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'idList' => $caseIDList));

            setcookie('caseModule', 0, 0, $this->config->webRoot, '', $this->config->cookieSecure, false);
            $currentModule = $this->app->tab == 'project' ? 'project'  : 'testcase';
            $currentMethod = $this->app->tab == 'project' ? 'testcase' : 'browse';
            $projectParam  = $this->app->tab == 'project' ? "projectID={$this->session->project}&" : '';
            return print(js::locate($this->createLink($currentModule, $currentMethod, $projectParam . "productID=$productID&branch=$branch&browseType=all&param=0&orderBy=id_desc"), 'parent'));
        }
        if(empty($this->products)) $this->locate($this->createLink('product', 'create'));

        /* Set productID and currentModuleID. */
        $productID = $this->product->saveState($productID, $this->products);
        if($branch === '') $branch = $this->cookie->preBranch;
        if($storyID and empty($moduleID))
        {
            $story    = $this->loadModel('story')->getByID($storyID);
            $moduleID = $story->module;
        }
        $currentModuleID = (int)$moduleID;

        /* Set menu. */
        $this->app->tab == 'project' ? $this->loadModel('project')->setMenu($this->session->project) : $this->testcase->setMenu($this->products, $productID, $branch);

        /* Set story list. */
        $story       = $storyID ? $this->story->getByID($storyID) : '';
        $storyPairs  = $this->loadModel('story')->getProductStoryPairs($productID, $branch === 'all' ? 0 : $branch);
        $storyPairs += $storyID ? array($storyID => $story->id . ':' . $story->title) : array('');

        /* Set custom. */
        $product = $this->product->getById($productID);
        foreach(explode(',', $this->config->testcase->customBatchCreateFields) as $field)
        {
            if($product->type != 'normal') $customFields[$product->type] = $this->lang->product->branchName[$product->type];
            $customFields[$field] = $this->lang->testcase->$field;
        }

        if($product->type != 'normal')
        {
            $this->config->testcase->custom->batchCreateFields = sprintf($this->config->testcase->custom->batchCreateFields, $product->type);
        }
        else
        {
            $this->config->testcase->custom->batchCreateFields = trim(sprintf($this->config->testcase->custom->batchCreateFields, ''), ',');
        }

        $showFields = $this->config->testcase->custom->batchCreateFields;
        if($product->type == 'normal')
        {
            $showFields = str_replace(array(0 => ",branch,", 1 => ",platform,"), '', ",$showFields,");
            $showFields = trim($showFields, ',');
        }

        if($this->app->tab == 'project' and $product->type != 'normal')
        {
            $this->lang->product->branch = sprintf($this->lang->product->branch, $this->lang->product->branchName[$product->type]);

            $productBranches = $this->loadModel('execution')->getBranchByProduct($productID, $this->session->project, 'noclosed|withMain');
            $branches        = isset($productBranches[$productID]) ? $productBranches[$productID] : array();
            $branch          = key($branches);
        }
        else
        {
            $branches = $this->loadModel('branch')->getPairs($productID, 'active');
        }

        /* Set module option menu. */
        $moduleOptionMenu          = $this->tree->getOptionMenu($productID, 'case', 0, $branch === 'all' ? 0 : $branch);
        $moduleOptionMenu['ditto'] = $this->lang->testcase->ditto;


        $this->view->customFields = $customFields;
        $this->view->showFields   = $showFields;

        $this->view->title            = $this->products[$productID] . $this->lang->colon . $this->lang->testcase->batchCreate;
        $this->view->position[]       = html::a($this->createLink('testcase', 'browse', "productID=$productID&branch=$branch"), $this->products[$productID]);
        $this->view->position[]       = $this->lang->testcase->common;
        $this->view->position[]       = $this->lang->testcase->batchCreate;
        $this->view->product          = $product;
        $this->view->productID        = $productID;
        $this->view->story            = $story;
        $this->view->storyPairs       = $storyPairs;
        $this->view->productName      = $this->products[$productID];
        $this->view->moduleOptionMenu = $moduleOptionMenu;
        $this->view->currentModuleID  = $currentModuleID;
        $this->view->branch           = $branch;
        $this->view->branches         = $branches;
        $this->view->needReview       = $this->testcase->forceNotReview() == true ? 0 : 1;

        $this->display();
    }

    /**
     * Create bug.
     *
     * @param  int    $productID
     * @param  string $extras
     * @access public
     * @return void
     */
    public function createBug($productID, $branch = 0, $extras = '')
    {
        $extras = str_replace(array(',', ' '), array('&', ''), $extras);
        parse_str($extras, $params);
        extract($params);

        $this->loadModel('testtask');
        $case = '';
        if($runID)
        {
            $case    = $this->testtask->getRunById($runID)->case;
            $results = $this->testtask->getResults($runID);
        }
        elseif($caseID)
        {
            $case    = $this->testcase->getById($caseID);
            $results = $this->testtask->getResults(0, $caseID);
        }

        if(!$case) return print(js::error($this->lang->notFound) . js::locate('back', 'parent'));

        if(!isset($this->products[$productID]))
        {
            $product = $this->product->getByID($productID);
            $this->products[$productID] = $product->name;
        }

        $this->view->title   = $this->products[$productID] . $this->lang->colon . $this->lang->testcase->createBug;
        $this->view->runID   = $runID;
        $this->view->case    = $case;
        $this->view->caseID  = $caseID;
        $this->view->version = $version;
        $this->display();
    }

    /**
     * View a test case.
     *
     * @param  int    $caseID
     * @param  int    $version
     * @param  string $from
     * @access public
     * @return void
     */
    public function view($caseID, $version = 0, $from = 'testcase', $taskID = 0)
    {
        $this->session->set('bugList', $this->app->getURI(true), $this->app->tab);

        $caseID = (int)$caseID;
        $case   = $this->testcase->getById($caseID, $version);

        if(!$case)
        {
            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'fail', 'message' => '404 Not found'));
            return print(js::error($this->lang->notFound) . js::locate($this->createLink('qa', 'index')));
        }

        $case = $this->loadModel('story')->checkNeedConfirm($case);

        if($from == 'testtask')
        {
            $run = $this->loadModel('testtask')->getRunByCase($taskID, $caseID);
            $case->assignedTo    = $run->assignedTo;
            $case->lastRunner    = $run->lastRunner;
            $case->lastRunDate   = $run->lastRunDate;
            $case->lastRunResult = $run->lastRunResult;
            $case->caseStatus    = $case->status;
            $case->status        = $run->status;

            $results = $this->testtask->getResults($run->id);
            $result  = array_shift($results);
            if($result)
            {
                $case->xml      = $result->xml;
                $case->duration = $result->duration;
            }
        }

        $isLibCase = ($case->lib and empty($case->product));
        if($isLibCase)
        {
            $libraries = $this->loadModel('caselib')->getLibraries();
            $this->app->tab == 'project' ? $this->loadModel('project')->setMenu($this->session->project) : $this->caselib->setLibMenu($libraries, $case->lib);

            $this->view->title      = "CASE #$case->id $case->title - " . $libraries[$case->lib];
            $this->view->position[] = html::a($this->createLink('caselib', 'browse', "libID=$case->lib"), $libraries[$case->lib]);

            $this->view->libName = $libraries[$case->lib];
        }
        else
        {
            $productID = $case->product;
            $product   = $this->product->getByID($productID);
            $branches  = $product->type == 'normal' ? array() : $this->loadModel('branch')->getPairs($productID);

            if($this->app->tab == 'project')   $this->loadModel('project')->setMenu($this->session->project);
            if($this->app->tab == 'execution') $this->loadModel('execution')->setMenu($this->session->execution);
            if($this->app->tab == 'qa')        $this->testcase->setMenu($this->products, $productID, $case->branch);

            $this->view->title      = "CASE #$case->id $case->title - " . $product->name;

            $this->view->product     = $product;
            $this->view->branches    = $branches;
            $this->view->productName = $product->name;
            $this->view->branchName  = $product->type == 'normal' ? '' : zget($branches, $case->branch, '');
        }

        $caseFails = $this->dao->select('COUNT(*) AS count')->from(TABLE_TESTRESULT)
            ->where('caseResult')->eq('fail')
            ->andwhere('`case`')->eq($caseID)
            ->beginIF($from == 'testtask')->andwhere('`run`')->eq($taskID)->fi()
            ->fetch('count');
        $case->caseFails = $caseFails;

        $this->executeHooks($caseID);

        $this->view->position[] = $this->lang->testcase->common;
        $this->view->position[] = $this->lang->testcase->view;

        $this->view->case       = $case;
        $this->view->from       = $from;
        $this->view->taskID     = $taskID;
        $this->view->version    = $version ? $version : $case->version;
        $this->view->modulePath = $this->tree->getParents($case->module);
        $this->view->caseModule = empty($case->module) ? '' : $this->tree->getById($case->module);
        $this->view->users      = $this->user->getPairs('noletter');
        $this->view->actions    = $this->loadModel('action')->getList('case', $caseID);
        $this->view->preAndNext = $this->loadModel('common')->getPreAndNextObject('testcase', $caseID);
        $this->view->runID      = $from == 'testcase' ? 0 : $run->id;
        $this->view->isLibCase  = $isLibCase;
        $this->view->caseFails  = $caseFails;

        if(defined('RUN_MODE') and RUN_MODE == 'api' and !empty($this->app->version)) return $this->send(array('status' => 'success', 'case' => $case));
        $this->display();
    }

    /**
     * Edit a case.
     *
     * @param  int   $caseID
     * @param  bool  $comment
     * @param  int   $executionID
     * @access public
     * @return void
     */
    public function edit($caseID, $comment = false, $executionID = 0)
    {
        $this->loadModel('story');

        $case = $this->testcase->getById($caseID);
        if(!$case) return print(js::error($this->lang->notFound) . js::locate('back'));

        $testtasks = $this->loadModel('testtask')->getGroupByCases($caseID);
        $testtasks = empty($testtasks[$caseID]) ? array() : $testtasks[$caseID];

        if(!empty($_POST))
        {
            $changes = array();
            if($comment == false or $comment == 'false')
            {
                $changes = $this->testcase->update($caseID, $testtasks);
                if(dao::isError()) return print(js::error(dao::getError()));
            }
            if($this->post->comment != '' or !empty($changes))
            {
                $this->loadModel('action');
                $action = !empty($changes) ? 'Edited' : 'Commented';
                $actionID = $this->action->create('case', $caseID, $action, $this->post->comment);
                $this->action->logHistory($actionID, $changes);

                if($case->status != 'wait' and $this->post->status == 'wait') $this->action->create('case', $caseID, 'submitReview');
            }

            $this->executeHooks($caseID);

            if(defined('RUN_MODE') && RUN_MODE == 'api')
            {
                return $this->send(array('status' => 'success', 'data' => $caseID));
            }
            else
            {
                return print(js::locate($this->createLink('testcase', 'view', "caseID=$caseID"), 'parent'));
            }
        }

        if($case->auto == 'unit')
        {
            $this->lang->testcase->subMenu->testcase->feature['alias']  = '';
            $this->lang->testcase->subMenu->testcase->unit['alias']     = 'view';
            $this->lang->testcase->subMenu->testcase->unit['subModule'] = 'testcase';
        }

        if(empty($case->steps))
        {
            $step = new stdclass();
            $step->type   = 'step';
            $step->desc   = '';
            $step->expect = '';
            $case->steps[] = $step;
        }

        $isLibCase = ($case->lib and empty($case->product));
        if($isLibCase)
        {
            $libraries = $this->loadModel('caselib')->getLibraries();
            $this->app->tab == 'project' ? $this->loadModel('project')->setMenu($this->session->project) : $this->caselib->setLibMenu($libraries, $case->lib);

            $title      = "CASE #$case->id $case->title - " . $libraries[$case->lib];
            $position[] = html::a($this->createLink('caselib', 'browse', "libID=$case->lib"), $libraries[$case->lib]);

            $this->view->libID     = $case->lib;
            $this->view->libName   = $libraries[$case->lib];
            $this->view->libraries = $libraries;
            $this->view->moduleOptionMenu = $this->tree->getOptionMenu($case->lib, $viewType = 'caselib', $startModuleID = 0);
        }
        else
        {
            $productID  = $case->product;
            $product    = $this->product->getById($productID);
            if(!isset($this->products[$productID])) $this->products[$productID] = $product->name;

            $title      = $this->products[$productID] . $this->lang->colon . $this->lang->testcase->edit;
            $position[] = html::a($this->createLink('testcase', 'browse', "productID=$productID"), $this->products[$productID]);

            /* Set menu. */
            if($this->app->tab == 'project' or $this->app->tab == 'execution')
            {
                $this->loadModel('execution');
                if($this->app->tab == 'project') $this->loadModel('project')->setMenu($case->project);
                if($this->app->tab == 'execution')
                {
                    if(!$executionID) $executionID = $case->execution;
                    $this->execution->setMenu($executionID);
                }
            }
            if($this->app->tab == 'qa') $this->testcase->setMenu($this->products, $productID, $case->branch);

            $moduleOptionMenu = $this->tree->getOptionMenu($productID, $viewType = 'case', $startModuleID = 0, $case->branch);
            if($case->lib and $case->fromCaseID)
            {
                $libName    = $this->loadModel('caselib')->getById($case->lib)->name;
                $libModules = $this->tree->getOptionMenu($case->lib, 'caselib');
                foreach($libModules as $moduleID => $moduleName)
                {
                    if($moduleID == 0) continue;
                    $moduleOptionMenu[$moduleID] = $libName . $moduleName;
                }
            }

            if(!isset($moduleOptionMenu[$case->module])) $moduleOptionMenu += $this->tree->getModulesName($case->module);

            /* Get product and branches. */
            if($this->app->tab == 'execution' or $this->app->tab == 'project')
            {
                $objectID = $this->app->tab == 'project' ? $case->project : $executionID;
            }

            /* Display status of branch. */
            $branches = $this->loadModel('branch')->getList($productID, isset($objectID) ? $objectID : 0, 'all');
            $branchTagOption = array();
            foreach($branches as $branchInfo)
            {
                $branchTagOption[$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
            }
            if(!isset($branchTagOption[$case->branch]))
            {
                $caseBranch = $this->branch->getById($case->branch, $case->product, '');
                $branchTagOption[$case->branch] = $case->branch == BRANCH_MAIN ? $caseBranch : ($caseBranch->name . ($caseBranch->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : ''));
            }

            $moduleIdList = $case->module;
            if($case->module) $moduleIdList = $this->tree->getAllChildID($case->module);

            $moduleIdList = (!in_array($this->app->tab, array('execution', 'project')) and empty($stories)) ? 0 : $moduleIdList;
            $stories = $this->story->getProductStoryPairs($productID, $case->branch, $moduleIdList, 'all','id_desc', 0, 'full', 'story', false);

            $this->view->productID        = $productID;
            $this->view->product          = $product;
            $this->view->products         = $this->products;
            $this->view->branchTagOption  = $branchTagOption;
            $this->view->productName      = $this->products[$productID];
            $this->view->moduleOptionMenu = $moduleOptionMenu;
            $this->view->stories          = $stories;
        }
        $forceNotReview = $this->testcase->forceNotReview();
        if($forceNotReview) unset($this->lang->testcase->statusList['wait']);

        $this->view->title           = $title;
        $this->view->currentModuleID = $case->module;
        $this->view->users           = $this->user->getPairs('noletter');
        $this->view->case            = $case;
        $this->view->actions         = $this->loadModel('action')->getList('case', $caseID);
        $this->view->isLibCase       = $isLibCase;
        $this->view->forceNotReview  = $forceNotReview;
        $this->view->testtasks       = $testtasks;

        $this->display();
    }

    /**
     * Batch edit case.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  string $type
     * @param  string $tab
     * @access public
     * @return void
     */
    public function batchEdit($productID = 0, $branch = 0, $type = 'case', $tab = '')
    {
        if(!$this->post->caseIDList) return print(js::locate($this->session->caseList));
        $caseIDList = array_unique($this->post->caseIDList);
        $testtasks  = $this->loadModel('testtask')->getGroupByCases($caseIDList);
        if($this->post->title)
        {
            $allChanges = $this->testcase->batchUpdate($testtasks);
            if($allChanges)
            {
                foreach($allChanges as $caseID => $changes )
                {
                    if(empty($changes)) continue;

                    $actionID = $this->loadModel('action')->create('case', $caseID, 'Edited');
                    $this->action->logHistory($actionID, $changes);
                }
            }

            return print(js::locate($this->session->caseList, 'parent'));
        }

        $branchProduct = false;

        if($this->app->tab == 'project')               $this->loadModel('project')->setMenu($this->session->project);
        if($this->app->tab == 'qa' and $type != 'lib') $this->testcase->setMenu($this->products, $productID, $branch);
        if($this->app->tab == 'execution')             $this->loadModel('execution')->setMenu($this->session->execution);

        /* The cases of a product. */
        if($productID)
        {
            /* Get the edited cases. */
            $cases = $this->testcase->getByList($caseIDList);

            if($type == 'lib')
            {
                $libID     = $productID;
                $libraries = $this->loadModel('caselib')->getLibraries();

                /* Remove story custom fields from caselib */
                $this->config->testcase->customBatchEditFields   = str_replace(',story', '', $this->config->testcase->customBatchEditFields);
                $this->config->testcase->custom->batchEditFields = str_replace(',story', '', $this->config->testcase->custom->batchEditFields);

                /* Set caselib menu. */
                $this->caselib->setLibMenu($libraries, $libID);

                /* Set modules. */
                $modules[$productID][$branch] = $this->tree->getOptionMenu($libID, 'caselib', 0, $branch);

                $this->view->title      = $libraries[$libID] . $this->lang->colon . $this->lang->testcase->batchEdit;
                $this->view->position[] = html::a($this->createLink('caselib', 'browse', "libID=$libID"), $libraries[$libID]);
            }
            else
            {
                $product = $this->product->getByID($productID);

                if($product->type != 'normal') $branchProduct = true;

                /* Set branches and modules. */
                $branches        = array();
                $branchTagOption = array();
                $modules         = array();
                if($product->type != 'normal')
                {
                    $branches = $this->loadModel('branch')->getList($productID, 0, 'all');
                    foreach($branches as $branchInfo)
                    {
                        $branchTagOption[$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
                    }
                    if($this->app->tab == 'project')
                    {
                        $branchTagOption = $this->loadModel('branch')->getPairsByProjectProduct($this->session->project, $productID);
                    }
                    foreach($branchTagOption as $branchID => $branchName) $modules[$productID][$branchID] = $this->tree->getOptionMenu($productID, 'case', 0, $branchID);
                }
                else
                {
                    $modules[$productID][BRANCH_MAIN] = $this->tree->getOptionMenu($productID, 'case');
                }

                $this->view->branchTagOption = array($productID => $branchTagOption);
                $this->view->title           = $product->name . $this->lang->colon . $this->lang->testcase->batchEdit;
                $this->view->product         = $product;
            }
        }
        else
        {
            /* Get the edited cases. */
            $cases = $this->dao->select('t1.*,t2.id as runID')->from(TABLE_CASE)->alias('t1')
                ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.id = t2.case')
                ->where('t1.deleted')->eq(0)
                ->andWhere('t1.id')->in($caseIDList)
                ->fetchAll('id');
            $caseIDList = array_keys($cases);

            /* The cases of my. */
            $this->app->loadLang('my');
            $this->lang->testcase->menu = $this->lang->my->menu->work;
            $this->lang->my->menu->work['subModule'] = 'testcase';

            $this->view->position[] = html::a($this->server->http_referer, $this->lang->my->myTestCase);
            $this->view->title      = $this->lang->testcase->batchEdit;

            $productIdList = array();
            foreach($cases as $case) $productIdList[$case->product] = $case->product;

            $branchTagOption = array();
            $products        = $this->product->getByIdList($productIdList);
            foreach($products as $product)
            {
                $branches = 0;
                if($product->type != 'normal')
                {
                    $branches = $this->loadModel('branch')->getList($product->id, 0, 'all');
                    foreach($branches as $branchInfo) $branchTagOption[$product->id][$branchInfo->id] = '/' . $product->name . '/' . $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
                    $branches      = array_keys($branches);
                    $branchProduct = true;
                }

                $modulePairs = $this->tree->getOptionMenu($product->id, 'case', 0, $branches);
                $modules[$product->id] = $product->type != 'normal' ? $modulePairs : array(0 => $modulePairs);
            }

            $this->view->products        = $products;
            $this->view->branchTagOption = $branchTagOption;
        }

        /* Judge whether the editedCases is too large and set session. */
        $countInputVars  = count($cases) * (count(explode(',', $this->config->testcase->custom->batchEditFields)) + 3);
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        $stories = $this->loadModel('story')->getProductStoryPairs($productID, $branch);
        $this->view->stories = array('' => '', 'ditto' => $this->lang->testcase->ditto) + $stories;

        /* Set custom. */
        foreach(explode(',', $this->config->testcase->customBatchEditFields) as $field) $customFields[$field] = $this->lang->testcase->$field;
        $this->view->customFields = $customFields;
        $this->view->showFields   = $this->config->testcase->custom->batchEditFields;

        /* Append module when change product type. */
        $modulePairs = array(0 => '/');
        foreach($cases as $case)
        {
            $caseProduct = $type == 'lib' ? $productID : $case->product;
            if(isset($modules[$caseProduct][$case->branch]))
            {
                $modulePairs[$case->id] = $modules[$caseProduct][$case->branch];
            }
            else
            {
                $modulePairs[$case->id] = $modules[$caseProduct][0] + $this->tree->getModulesName($case->module);
            }
        }

        /* Assign. */
        $this->view->position[]     = $this->lang->testcase->common;
        $this->view->position[]     = $this->lang->testcase->batchEdit;
        $this->view->caseIDList     = $caseIDList;
        $this->view->productID      = $productID;
        $this->view->branchProduct  = $branchProduct;
        $this->view->priList        = array('ditto' => $this->lang->testcase->ditto) + $this->lang->testcase->priList;
        $this->view->typeList       = array('' => '', 'ditto' => $this->lang->testcase->ditto) + $this->lang->testcase->typeList;
        $this->view->cases          = $cases;
        $this->view->forceNotReview = $this->testcase->forceNotReview();
        $this->view->modulePairs    = $modulePairs;
        $this->view->testtasks      = $testtasks;
        $this->view->isLibCase      = $type == 'lib' ? true : false;

        $this->display();
    }

    /**
     * Review case.
     *
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function review($caseID)
    {
        if($_POST)
        {
            $changes = $this->testcase->review($caseID);
            if(dao::isError()) return print(js::error(dao::getError()));

            if(is_array($changes))
            {
                $result = $this->post->result;
                $actionID = $this->loadModel('action')->create('case', $caseID, 'Reviewed', $this->post->comment, ucfirst($result));
                $this->action->logHistory($actionID, $changes);

                $this->executeHooks($caseID);

                return print(js::reload('parent.parent'));
            }
        }

        $this->view->users   = $this->user->getPairs('noletter|noclosed|nodeleted');
        $this->view->case    = $this->testcase->getById($caseID);
        $this->view->actions = $this->loadModel('action')->getList('case', $caseID);
        $this->display();
    }

    /**
     * Batch review case.
     *
     * @param  string $result
     * @access public
     * @return void
     */
    public function batchReview($result)
    {
        if(!$this->post->caseIDList) return print(js::locate($this->session->caseList, 'parent'));
        $caseIdList = array_unique($this->post->caseIDList);
        $actions    = $this->testcase->batchReview($caseIdList, $result);

        if(dao::isError()) return print(js::error(dao::getError()));
        echo js::locate($this->session->caseList, 'parent');
    }

    /**
     * Delete a test case
     *
     * @param  int    $caseID
     * @param  string $confirm yes|noe
     * @access public
     * @return void
     */
    public function delete($caseID, $confirm = 'no')
    {
        if($confirm == 'no')
        {
            return print(js::confirm($this->lang->testcase->confirmDelete, inlink('delete', "caseID=$caseID&confirm=yes")));
        }
        else
        {
            $case = $this->testcase->getById($caseID);
            $this->testcase->delete(TABLE_CASE, $caseID);

            $message = $this->executeHooks($caseID);
            if($message) $response['message'] = $message;

            /* if ajax request, send result. */
            if($this->server->ajax)
            {
                if(dao::isError())
                {
                    $response['result']  = 'fail';
                    $response['message'] = dao::getError();
                }
                else
                {
                    $response['result']  = 'success';
                    $response['message'] = '';
                }
                return $this->send($response);
            }

            $locateLink = $this->session->caseList ? $this->session->caseList : inlink('browse', "productID={$case->product}");
            if(defined('RUN_MODE') && RUN_MODE == 'api') return $this->send(array('status' => 'success'));
            return print(js::locate($locateLink, 'parent'));
        }
    }

    /**
     * Batch delete cases.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function batchDelete($productID = 0)
    {
        if(!$this->post->caseIDList) return print(js::locate($this->session->caseList));
        $caseIDList = array_unique($this->post->caseIDList);

        foreach($caseIDList as $caseID) $this->testcase->delete(TABLE_CASE, $caseID);
        echo js::locate($this->session->caseList);
    }

    /**
     * Batch change branch.
     *
     * @param  int    $branchID
     * @access public
     * @return void
     */
    public function batchChangeBranch($branchID)
    {
        if($this->post->caseIDList)
        {
            $caseIDList = $this->post->caseIDList;
            $caseIDList = array_unique($caseIDList);
            unset($_POST['caseIDList']);
            $allChanges = $this->testcase->batchChangeBranch($caseIDList, $branchID);
            if(dao::isError()) return print(js::error(dao::getError()));
            foreach($allChanges as $caseID => $changes)
            {
                $this->loadModel('action');
                $actionID = $this->action->create('case', $caseID, 'Edited');
                $this->action->logHistory($actionID, $changes);
            }
        }

        echo js::locate($this->session->caseList, 'parent');
    }

    /**
     * Batch change the module of case.
     *
     * @param  int    $moduleID
     * @access public
     * @return void
     */
    public function batchChangeModule($moduleID)
    {
        if($this->post->caseIDList)
        {
            $caseIDList = $this->post->caseIDList;
            $caseIDList = array_unique($caseIDList);
            unset($_POST['caseIDList']);
            $allChanges = $this->testcase->batchChangeModule($caseIDList, $moduleID);
            if(dao::isError()) return print(js::error(dao::getError()));
            foreach($allChanges as $caseID => $changes)
            {
                $this->loadModel('action');
                $actionID = $this->action->create('case', $caseID, 'Edited');
                $this->action->logHistory($actionID, $changes);
            }
        }

        echo js::locate($this->session->caseList, 'parent');
    }

    /**
     * Batch review case.
     *
     * @param  string $result
     * @access public
     * @return void
     */
    public function batchCaseTypeChange($result)
    {
        if(!$this->post->caseIDList) return print(js::locate($this->session->caseList, 'parent'));
        $caseIdList = array_unique($this->post->caseIDList);
        $this->testcase->batchCaseTypeChange($caseIdList, $result);

        if(dao::isError()) return print(js::error(dao::getError()));
        echo js::locate($this->session->caseList, 'parent');
    }

    /**
     * Link related cases.
     *
     * @param  int    $caseID
     * @param  string $browseType
     * @param  int    $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function linkCases($caseID, $browseType = '', $param = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        /* Get case and queryID. */
        $case    = $this->testcase->getById($caseID);
        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;

        /* Set menu. */
        $this->testcase->setMenu($this->products, $case->product, $case->branch);

        /* Build the search form. */
        $actionURL = $this->createLink('testcase', 'linkCases', "caseID=$caseID&browseType=bySearch&queryID=myQueryID", '', true);
        $objectID  = 0;
        if($this->app->tab == 'project') $objectID = $case->project;
        if($this->app->tab == 'execution') $objectID = $case->execution;

        unset($this->config->testcase->search['fields']['product']);
        $this->testcase->buildSearchForm($case->product, $this->products, $queryID, $actionURL, $objectID);

        /* Get cases to link. */
        $cases2Link = $this->testcase->getCases2Link($caseID, $browseType, $queryID);

        /* Pager. */
        $this->app->loadClass('pager', true);
        $recTotal   = count($cases2Link);
        $pager      = new pager($recTotal, $recPerPage, $pageID);
        $cases2Link = array_chunk($cases2Link, $pager->recPerPage);

        /* Assign. */
        $this->view->title      = $case->title . $this->lang->colon . $this->lang->testcase->linkCases;
        $this->view->position[] = html::a($this->createLink('product', 'view', "productID=$case->product"), zget($this->products, $case->product, ''));
        $this->view->position[] = html::a($this->createLink('testcase', 'view', "caseID=$caseID"), $case->title);
        $this->view->position[] = $this->lang->testcase->linkCases;
        $this->view->case       = $case;
        $this->view->cases2Link = empty($cases2Link) ? $cases2Link : $cases2Link[$pageID - 1];
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->pager      = $pager;

        $this->display();
    }

    /**
     * Link related bugs.
     *
     * @param  int    $caseID
     * @param  string $browseType
     * @param  int    $param
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function linkBugs($caseID, $browseType = '', $param = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('bug');

        /* Get case and queryID. */
        $case    = $this->testcase->getById($caseID);
        $queryID = ($browseType == 'bySearch') ? (int)$param : 0;

        /* Build the search form. */
        $actionURL = $this->createLink('testcase', 'linkBugs', "caseID=$caseID&browseType=bySearch&queryID=myQueryID", '', true);
        $objectID  = 0;
        if($this->app->tab == 'project')   $objectID = $case->project;
        if($this->app->tab == 'execution') $objectID = $case->execution;

        /* Unset search field 'plan' in single project. */
        unset($this->config->bug->search['fields']['product']);
        if($case->project and ($this->app->tab == 'project' or $this->app->tab == 'execution'))
        {
            $project = $this->loadModel('project')->getById($case->project);
            if(!$project->hasProduct and $project->model == 'waterfall') unset($this->config->bug->search['fields']['plan']);
        }

        $this->bug->buildSearchForm($case->product, $this->products, $queryID, $actionURL, $objectID);

        /* Get cases to link. */
        $bugs2Link = $this->testcase->getBugs2Link($caseID, $browseType, $queryID);

        /* Pager. */
        $this->app->loadClass('pager', true);
        $recTotal  = count($bugs2Link);
        $pager     = new pager($recTotal, $recPerPage, $pageID);
        $bugs2Link = array_chunk($bugs2Link, $pager->recPerPage);

        /* Assign. */
        $this->view->position[] = $this->lang->testcase->linkBugs;
        $this->view->case       = $case;
        $this->view->bugs2Link  = empty($bugs2Link) ? $bugs2Link : $bugs2Link[$pageID - 1];
        $this->view->users      = $this->loadModel('user')->getPairs('noletter');
        $this->view->pager      = $pager;

        $this->display();
    }

    /**
     * Confirm testcase changed.
     *
     * @param  int    $caseID
     * @param  int    $taskID
     * @param  string $from
     * @access public
     * @return void
     */
    public function confirmChange($caseID, $taskID = 0, $from = 'view')
    {
        $case = $this->testcase->getById($caseID);
        $this->dao->update(TABLE_TESTRUN)->set('version')->eq($case->version)->where('`case`')->eq($caseID)->exec();
        if($from == 'view') return print(js::locate(inlink('view', "caseID=$caseID&version=$case->version&from=testtask&taskID=$taskID"), 'parent'));
        echo js::reload('parent');
    }

    /**
     * Confirm libcase changed.
     *
     * @param  int    $caseID
     * @param  int    $libcaseID
     * @param  int    $version
     * @access public
     * @return void
     */
    public function confirmLibcaseChange($caseID, $libcaseID)
    {
        $case    = $this->testcase->getById($caseID);
        $libCase = $this->testcase->getById($libcaseID);
        $version = $case->version + 1;

        $this->dao->delete()->from(TABLE_FILE)->where('objectType')->eq('testcase')->andWhere('objectID')->eq($caseID)->exec();
        foreach($libCase->files as $fileID => $file)
        {
            $fileName = pathinfo($file->pathname, PATHINFO_FILENAME);
            $datePath = substr($file->pathname, 0, 6);
            $realPath = $this->app->getAppRoot() . "www/data/upload/{$this->app->company->id}/" . "{$datePath}/" . $fileName;

            $rand        = rand();
            $newFileName = $fileName . 'copy' . $rand;
            $newFilePath = $this->app->getAppRoot() . "www/data/upload/{$this->app->company->id}/" . "{$datePath}/" .  $newFileName;
            copy($realPath, $newFilePath);

            $newFileName = $file->pathname;
            $newFileName = str_replace('.', "copy$rand.", $newFileName);

            unset($file->id, $file->realPath, $file->webPath);
            $file->objectID = $caseID;
            $file->pathname = $newFileName;
            $this->dao->insert(TABLE_FILE)->data($file)->exec();
        }

        $this->dao->update(TABLE_CASE)
            ->set('version')->eq($version)
            ->set('fromCaseVersion')->eq($version)
            ->set('precondition')->eq($libCase->precondition)
            ->set('title')->eq($libCase->title)
            ->where('id')->eq($caseID)
            ->exec();

        foreach($libCase->steps as $step)
        {
            unset($step->id);
            $step->case    = $caseID;
            $step->version = $version;
            $this->dao->insert(TABLE_CASESTEP)->data($step)->exec();
        }

        echo js::locate($this->createLink('testcase', 'view', "caseID=$caseID&version=$version"), 'parent');
    }

    /**
     * Ignore libcase changed.
     *
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function ignoreLibcaseChange($caseID)
    {
        $case = $this->testcase->getById($caseID);
        $this->dao->update(TABLE_CASE)->set('fromCaseVersion')->eq($case->version)->where('id')->eq($caseID)->exec();
        echo js::reload('parent');
    }

    /**
     * Confirm story changes.
     *
     * @param  int    $caseID
     * @param  bool   $reload
     * @access public
     * @return void
     */
    public function confirmStoryChange($caseID, $reload = true)
    {
        $case = $this->testcase->getById($caseID);
        if($case->story)
        {
            $this->dao->update(TABLE_CASE)->set('storyVersion')->eq($case->latestStoryVersion)->where('id')->eq($caseID)->exec();
            $this->loadModel('action')->create('case', $caseID, 'confirmed', '', $case->latestStoryVersion);
        }
        if($reload) return print(js::reload('parent'));
    }

    /**
     * Batch ctory change cases.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function batchConfirmStoryChange($productID = 0)
    {
        if(!$this->post->caseIDList) return print(js::locate($this->session->caseList));
        $caseIDList = array_unique($this->post->caseIDList);

        foreach($caseIDList as $caseID) $this->confirmStoryChange($caseID,false);
        echo js::locate($this->session->caseList);
    }

    /**
     * export
     *
     * @param  int    $productID
     * @param  string $orderBy
     * @param  int    $taskID
     * @param  string $browseType
     * @access public
     * @return void
     */
    public function export($productID, $orderBy, $taskID = 0, $browseType = '')
    {
        if(strpos($orderBy, 'case') !== false)
        {
            list($field, $sort) = explode('_', $orderBy);
            $orderBy = '`' . $field . '`_' . $sort;
        }

        $product  = $this->loadModel('product')->getById($productID);
        $products = $this->loadModel('product')->getPairs('', 0, '', 'all');
        if($product->type != 'normal')
        {
            $this->lang->testcase->branch = $this->lang->product->branchName[$product->type];
        }
        else
        {
            $this->config->testcase->exportFields = str_replace('branch,', '', $this->config->testcase->exportFields);
        }

        if($product->shadow) $this->config->testcase->exportFields = str_replace('product,', '', $this->config->testcase->exportFields);

        if($_POST)
        {
            $this->loadModel('file');
            $this->app->loadLang('testtask');
            $caseLang   = $this->lang->testcase;
            $caseConfig = $this->config->testcase;

            /* Create field lists. */
            $fields  = $this->post->exportFields ? $this->post->exportFields : explode(',', $caseConfig->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                if(!($product->type == 'normal' and $fieldName == 'branch'))
                {
                    $fields[$fieldName] = isset($caseLang->$fieldName) ? $caseLang->$fieldName : $fieldName;
                }
                unset($fields[$key]);
            }

            /* Get cases. */
            if($this->session->testcaseOnlyCondition)
            {
                $caseIDList = array();
                if($taskID) $caseIDList = $this->dao->select('`case`')->from(TABLE_TESTRUN)->where('task')->eq($taskID)->fetchPairs();

                $cases = $this->dao->select('*')->from(TABLE_CASE)->where($this->session->testcaseQueryCondition)
                    ->beginIF($taskID)->andWhere('id')->in($caseIDList)->fi()
                    ->beginIF($this->post->exportType == 'selected')->andWhere('id')->in($this->cookie->checkedItem)->fi()
                    ->orderBy($orderBy)
                    ->beginIF($this->post->limit)->limit($this->post->limit)->fi()
                    ->fetchAll('id');
            }
            else
            {
                $cases   = array();
                $orderBy = " ORDER BY " . str_replace(array('|', '^A', '_'), ' ', $orderBy);
                $stmt    = $this->dao->query($this->session->testcaseQueryCondition . $orderBy . ($this->post->limit ? ' LIMIT ' . $this->post->limit : ''));
                while($row = $stmt->fetch())
                {
                    $caseID = isset($row->case) ? $row->case : $row->id;
                    if($this->post->exportType == 'selected' and strpos(",{$this->cookie->checkedItem},", ",$caseID,") === false) continue;
                    $cases[$caseID] = $row;
                    $row->id        = $caseID;
                }
            }
            if($taskID) $caseLang->statusList = $this->lang->testcase->statusList;

            $stmt = $this->dao->select('t1.*')->from(TABLE_TESTRESULT)->alias('t1')
                ->leftJoin(TABLE_TESTRUN)->alias('t2')->on('t1.run=t2.id')
                ->where('t1.`case`')->in(array_keys($cases))
                ->beginIF($taskID)->andWhere('t2.task')->eq($taskID)->fi()
                ->orderBy('id_desc')
                ->query();
            $results = array();
            while($result = $stmt->fetch())
            {
                if(!isset($results[$result->case])) $results[$result->case] = unserialize($result->stepResults);
            }

            /* Get users, products and projects. */
            $users    = $this->loadModel('user')->getPairs('noletter');
            $branches = $this->loadModel('branch')->getPairs($productID);

            /* Get related objects id lists. */
            $relatedStoryIdList  = array();
            $relatedCaseIdList   = array();

            foreach($cases as $case)
            {
                $relatedStoryIdList[$case->story]   = $case->story;
                $relatedCaseIdList[$case->linkCase] = $case->linkCase;

                /* Process link cases. */
                $linkCases = explode(',', $case->linkCase);
                foreach($linkCases as $linkCaseID)
                {
                    if($linkCaseID) $relatedCaseIdList[$linkCaseID] = trim($linkCaseID);
                }
            }

            /* Get related objects title or names. */
            $relatedModules = $this->loadModel('tree')->getAllModulePairs('case');
            $relatedStories = $this->dao->select('id,title')->from(TABLE_STORY) ->where('id')->in($relatedStoryIdList)->fetchPairs();
            $relatedCases   = $this->dao->select('id, title')->from(TABLE_CASE)->where('id')->in($relatedCaseIdList)->fetchPairs();
            $relatedSteps   = $this->dao->select('id,parent,`case`,version,type,`desc`,expect')->from(TABLE_CASESTEP)->where('`case`')->in(@array_keys($cases))->orderBy('version desc,id')->fetchGroup('case', 'id');
            $relatedFiles   = $this->dao->select('id, objectID, pathname, title')->from(TABLE_FILE)->where('objectType')->eq('testcase')->andWhere('objectID')->in(@array_keys($cases))->andWhere('extra')->ne('editor')->fetchGroup('objectID');

            $cases = $this->testcase->appendData($cases);
            foreach($cases as $case)
            {
                $case->stepDesc   = '';
                $case->stepExpect = '';
                $case->real       = '';
                $result = isset($results[$case->id]) ? $results[$case->id] : array();

                $case->openedDate     = !helper::isZeroDate($case->openedDate)     ? $case->openedDate     : '';
                $case->lastEditedDate = !helper::isZeroDate($case->lastEditedDate) ? $case->lastEditedDate : '';
                $case->lastRunDate    = !helper::isZeroDate($case->lastRunDate)    ? $case->lastRunDate    : '';

                $case->real = '';
                if(!empty($result) and !isset($relatedSteps[$case->id]))
                {
                    $firstStep  = reset($result);
                    $case->real = $firstStep['real'];
                }

                if(isset($relatedSteps[$case->id]))
                {
                    $i = $childId = 0;
                    foreach($relatedSteps[$case->id] as $step)
                    {
                        $stepId = 0;
                        if($step->type == 'group' or $step->type == 'step')
                        {
                            $i++;
                            $childId = 0;
                            $stepId  = $i;
                        }
                        else
                        {
                            $stepId = $i . '.' . $childId;
                        }
                        if($step->version != $case->version) continue;
                        $sign = (in_array($this->post->fileType, array('html', 'xml'))) ? '<br />' : "\n";
                        $case->stepDesc   .= $stepId . ". " . $step->desc . $sign;
                        $case->stepExpect .= $stepId . ". " . $step->expect . $sign;
                        $case->real .= $stepId . ". " . (isset($result[$step->id]) ? $result[$step->id]['real'] : '') . $sign;
                        $childId ++;
                    }
                }
                $case->stepDesc     = trim($case->stepDesc);
                $case->stepExpect   = trim($case->stepExpect);
                $case->real         = trim($case->real);
                $case->precondition = htmlspecialchars_decode($case->precondition, ENT_QUOTES);

                if($this->post->fileType == 'csv')
                {
                    $case->stepDesc   = str_replace('"', '""', $case->stepDesc);
                    $case->stepExpect = str_replace('"', '""', $case->stepExpect);
                }

                /* fill some field with useful value. */
                $case->product = !isset($products[$case->product])     ? '' : $products[$case->product] . "(#$case->product)";
                $case->branch  = !isset($branches[$case->branch])      ? '' : $branches[$case->branch] . "(#$case->branch)";
                $case->module  = !isset($relatedModules[$case->module])? '' : $relatedModules[$case->module] . "(#$case->module)";
                $case->story   = !isset($relatedStories[$case->story]) ? '' : $relatedStories[$case->story] . "(#$case->story)";

                if(isset($caseLang->priList[$case->pri]))              $case->pri           = $caseLang->priList[$case->pri];
                if(isset($caseLang->typeList[$case->type]))            $case->type          = $caseLang->typeList[$case->type];
                if(isset($caseLang->statusList[$case->status]))        $case->status        = $this->processStatus('testcase', $case);
                if(isset($users[$case->openedBy]))                     $case->openedBy      = $users[$case->openedBy];
                if(isset($users[$case->lastEditedBy]))                 $case->lastEditedBy  = $users[$case->lastEditedBy];
                if(isset($users[$case->lastRunner]))                   $case->lastRunner    = $users[$case->lastRunner];
                if(isset($caseLang->resultList[$case->lastRunResult])) $case->lastRunResult = $caseLang->resultList[$case->lastRunResult];

                $case->bugsAB       = $case->bugs;       unset($case->bugs);
                $case->resultsAB    = $case->results;    unset($case->results);
                $case->stepNumberAB = $case->stepNumber; unset($case->stepNumber);
                unset($case->caseFails);

                $case->stage = explode(',', $case->stage);
                foreach($case->stage as $key => $stage) $case->stage[$key] = isset($caseLang->stageList[$stage]) ? $caseLang->stageList[$stage] : $stage;
                $case->stage = join("\n", $case->stage);

                $case->openedDate     = substr($case->openedDate, 0, 10);
                $case->lastEditedDate = substr($case->lastEditedDate, 0, 10);
                $case->lastRunDate    = helper::isZeroDate($case->lastRunDate) ? '' : $case->lastRunDate;

                if($case->linkCase)
                {
                    $tmpLinkCases = array();
                    $linkCaseIdList = explode(',', $case->linkCase);
                    foreach($linkCaseIdList as $linkCaseID)
                    {
                        $linkCaseID = trim($linkCaseID);
                        $tmpLinkCases[] = isset($relatedCases[$linkCaseID]) ? $relatedCases[$linkCaseID] . "(#$linkCaseID)" : $linkCaseID;
                    }
                    $case->linkCase = join("; \n", $tmpLinkCases);
                }

                /* Set related files. */
                $case->files = '';
                if(isset($relatedFiles[$case->id]))
                {
                    foreach($relatedFiles[$case->id] as $file)
                    {
                        $fileURL = common::getSysURL() . $this->createLink('file', 'download', "fileID={$file->id}");
                        $case->files .= html::a($fileURL, $file->title, '_blank') . '<br />';
                    }
                }
            }
            if($this->config->edition != 'open') list($fields, $cases) = $this->loadModel('workflowfield')->appendDataFromFlow($fields, $cases);

            $this->post->set('fields', $fields);
            $this->post->set('rows', $cases);
            $this->post->set('kind', 'testcase');
            $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        }

        $fileName    = $this->lang->testcase->common;
        $browseType  = isset($this->lang->testcase->featureBar['browse'][$browseType]) ? $this->lang->testcase->featureBar['browse'][$browseType] : '';

        if($taskID) $taskName = $this->dao->findById($taskID)->from(TABLE_TESTTASK)->fetch('name');

        $this->view->fileName        = zget($products, $productID, '') . $this->lang->dash . ($taskID ? $taskName . $this->lang->dash : '') . $browseType . $fileName;
        $this->view->allExportFields = $this->config->testcase->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

    /**
     * Export template.
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function exportTemplate($productID)
    {
        if($_POST)
        {
            $product = $this->loadModel('product')->getById($productID);

            if($product->type != 'normal') $fields['branch'] = $this->lang->product->branchName[$product->type];
            $fields['module']       = $this->lang->testcase->module;
            $fields['title']        = $this->lang->testcase->title;
            $fields['precondition'] = $this->lang->testcase->precondition;
            $fields['stepDesc']     = $this->lang->testcase->stepDesc;
            $fields['stepExpect']   = $this->lang->testcase->stepExpect;
            $fields['keywords']     = $this->lang->testcase->keywords;
            $fields['pri']          = $this->lang->testcase->pri;
            $fields['type']         = $this->lang->testcase->type;
            $fields['stage']        = $this->lang->testcase->stage;

            $fields[''] = '';
            $fields['typeValue']  = $this->lang->testcase->lblTypeValue;
            $fields['stageValue'] = $this->lang->testcase->lblStageValue;
            if($product->type != 'normal') $fields['branchValue'] = $this->lang->product->branchName[$product->type];

            $projectID = $this->app->tab == 'project' ? $this->session->project : 0;
            $branches  = $this->loadModel('branch')->getPairs($productID, '' , $projectID);
            $this->loadModel('tree');
            $modules = $product->type == 'normal' ? $this->tree->getOptionMenu($productID, 'case', 0, 0) : array();

            foreach($branches as $branchID => $branchName)
            {
                $branches[$branchID] = $branchName . "(#$branchID)";
                $modules += $this->tree->getOptionMenu($productID, 'case', 0, $branchID);
            }

            $rows    = array();
            $num     = (int)$this->post->num;
            for($i = 0; $i < $num; $i++)
            {
                foreach($modules as $moduleID => $module)
                {
                    $row = new stdclass();
                    $row->module     = $module . "(#$moduleID)";
                    $row->stepDesc   = "1. \n2. \n3.";
                    $row->stepExpect = "1. \n2. \n3.";

                    if(empty($rows))
                    {
                        $row->typeValue   = join("\n", $this->lang->testcase->typeList);
                        $row->stageValue  = join("\n", $this->lang->testcase->stageList);
                        if($product->type != 'normal') $row->branchValue = join("\n", $branches);
                    }
                    $rows[] = $row;
                }
            }

            $this->post->set('fields', $fields);
            $this->post->set('kind', 'testcase');
            $this->post->set('rows', $rows);
            $this->post->set('extraNum', $num);
            $this->post->set('fileName', 'Template');
            $this->fetch('file', 'export2csv', $_POST);
        }

        $this->display();
    }

    /**
     * Import csv
     *
     * @param  int    $productID
     * @access public
     * @return void
     */
    public function import($productID, $branch = 0)
    {
        if($_FILES)
        {
            $file = $this->loadModel('file')->getUpload('file');
            $file = $file[0];

            $fileName = $this->file->savePath . $this->file->getSaveName($file['pathname']);
            move_uploaded_file($file['tmpname'], $fileName);

            $rows   = $this->file->parseCSV($fileName);
            $fields = $this->testcase->getImportFields($productID);
            $fields = array_flip($fields);
            $header = array();
            foreach($rows[0] as $i => $rowValue)
            {
                if(empty($rowValue)) break;
                $header[$i] = $rowValue;
            }
            unset($rows[0]);

            $columnKey = array();
            foreach($header as $title)
            {
                if(!isset($fields[$title])) continue;
                $columnKey[] = $fields[$title];
            }

            if(count($columnKey) <= 3 or $this->post->encode != 'utf-8')
            {
                $encode = $this->post->encode != "utf-8" ? $this->post->encode : 'gbk';
                $fc     = file_get_contents($fileName);
                $fc     = helper::convertEncoding($fc, $encode, 'utf-8');
                file_put_contents($fileName, $fc);

                $rows      = $this->file->parseCSV($fileName);
                $columnKey = array();
                $header    = array();
                foreach($rows[0] as $i => $rowValue)
                {
                    if(empty($rowValue)) break;
                    $header[$i] = $rowValue;
                }
                unset($rows[0]);
                foreach($header as $title)
                {
                    if(!isset($fields[$title])) continue;
                    $columnKey[] = $fields[$title];
                }
                if(count($columnKey) == 0) return print(js::alert($this->lang->testcase->errorEncode));
            }

            $this->session->set('fileImport', $fileName);

            return print(js::locate(inlink('showImport', "productID=$productID&branch=$branch"), 'parent.parent'));
        }

        $this->display();
    }

    /**
     * Import case from lib.
     *
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $libID
     * @param  string $orderBy
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function importFromLib($productID, $branch = 0, $libID = 0, $orderBy = 'id_desc', $browseType = '', $queryID = 0, $recTotal = 0, $recPerPage = 20, $pageID = 1, $projectID = 0)
    {
        $browseType = strtolower($browseType);
        $queryID    = (int)$queryID;
        $product    = $this->loadModel('product')->getById($productID);
        $branches   = array();
        if($branch == '') $branch = 0;

        $this->loadModel('branch');
        if($product->type != 'normal') $branches = array(BRANCH_MAIN => $this->lang->branch->main) + $this->branch->getPairs($productID, 'active', $projectID);

        $libraries = $this->loadModel('caselib')->getLibraries();
        if(empty($libraries))
        {
            echo js::alert($this->lang->testcase->noLibrary);
            return print(js::locate($this->session->caseList));
        }
        if(empty($libID) or !isset($libraries[$libID])) $libID = key($libraries);

        if($_POST)
        {
            $this->testcase->importFromLib($productID, $libID, $branch);
            return print(js::reload('parent'));
        }

        $this->app->tab == 'project' ? $this->loadModel('project')->setMenu($this->session->project) : $this->testcase->setMenu($this->products, $productID, $branch);

        /* Build the search form. */
        $actionURL = $this->createLink('testcase', 'importFromLib', "productID=$productID&branch=$branch&libID=$libID&orderBy=$orderBy&browseType=bySearch&queryID=myQueryID");
        $this->config->testcase->search['module']    = 'testsuite';
        $this->config->testcase->search['onMenuBar'] = 'no';
        $this->config->testcase->search['actionURL'] = $actionURL;
        $this->config->testcase->search['queryID']   = $queryID;
        $this->config->testcase->search['fields']['lib'] = $this->lang->testcase->lib;
        $this->config->testcase->search['params']['lib'] = array('operator' => '=', 'control' => 'select', 'values' => array('' => '', $libID => $libraries[$libID], 'all' => $this->lang->caselib->all));
        $this->config->testcase->search['params']['module']['values']  = $this->loadModel('tree')->getOptionMenu($libID, $viewType = 'caselib');
        if(!$this->config->testcase->needReview) unset($this->config->testcase->search['params']['status']['values']['wait']);
        unset($this->config->testcase->search['fields']['product']);
        unset($this->config->testcase->search['fields']['branch']);
        $this->loadModel('search')->setSearchParams($this->config->testcase->search);

        $this->loadModel('testsuite');
        foreach($branches as $branchID => $branchName) $canImportModules[$branchID] = $this->testsuite->getCanImportModules($productID, $libID, $branchID);
        if(empty($branches)) $canImportModules[0] = $this->testsuite->getCanImportModules($productID, $libID, 0);

        /* Load pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init(0, $recPerPage, $pageID);

        $this->view->title      = $this->lang->testcase->common . $this->lang->colon . $this->lang->testcase->importFromLib;
        $this->view->position[] = $this->lang->testcase->importFromLib;

        $this->view->libraries        = $libraries;
        $this->view->libID            = $libID;
        $this->view->product          = $product;
        $this->view->productID        = $productID;
        $this->view->branch           = $branch;
        $this->view->cases            = $this->testsuite->getCanImportCases($productID, $libID, $branch, $orderBy, $pager, $browseType, $queryID);
        $this->view->libModules       = $this->tree->getOptionMenu($libID, 'caselib');
        $this->view->pager            = $pager;
        $this->view->orderBy          = $orderBy;
        $this->view->branches         = $branches;
        $this->view->browseType       = $browseType;
        $this->view->queryID          = $queryID;
        $this->view->canImportModules = $canImportModules;

        $this->display();
    }

    /**
     * Show import data
     *
     * @param  int        $productID
     * @param  int        $branch
     * @param  int        $pagerID
     * @param  int        $maxImport
     * @param  string|int $insert     0 is covered old, 1 is insert new.
     * @access public
     * @return void
     */
    public function showImport($productID, $branch = 0, $pagerID = 1, $maxImport = 0, $insert = '')
    {
        $file    = $this->session->fileImport;
        $tmpPath = $this->loadModel('file')->getPathOfImportedFile();
        $tmpFile = $tmpPath . DS . md5(basename($file));

        if($_POST)
        {
            $this->testcase->createFromImport($productID, (int)$branch);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($this->post->isEndPage)
            {
                unlink($tmpFile);

                if($this->app->tab == 'project')
                {
                    return print(js::locate($this->createLink('project', 'testcase', "projectID={$this->session->project}&productID=$productID"), 'parent'));
                }
                else
                {
                    return print(js::locate(inlink('browse', "productID=$productID"), 'parent'));
                }
            }
            else
            {
                return print(js::locate(inlink('showImport', "productID=$productID&branch=$branch&pagerID=" . ($this->post->pagerID + 1) . "&maxImport=$maxImport&insert=" . zget($_POST, 'insert', '')), 'parent'));
            }
        }

        if($this->app->tab == 'project')
        {
            $this->loadModel('project')->setMenu($this->session->project);
        }
        else
        {
            $this->testcase->setMenu($this->products, $productID, $branch);
        }

        $caseLang   = $this->lang->testcase;
        $caseConfig = $this->config->testcase;
        $branches   = $this->loadModel('branch')->getPairs($productID, 'active');
        $stories    = $this->loadModel('story')->getProductStoryPairs($productID, $branch);
        $fields     = $this->testcase->getImportFields($productID);
        $fields     = array_flip($fields);

        $branchModules = $this->loadModel('tree')->getOptionMenu($productID, 'case', 0, empty($branches) ? array(0) : array_keys($branches));
        foreach($branchModules as $branchID => $moduleList)
        {
            foreach($moduleList as $moduleID => $moduleName) $modules[$branchID][$moduleID] = $moduleName;
        }

        if(!empty($maxImport) and file_exists($tmpFile))
        {
            $data = unserialize(file_get_contents($tmpFile));
            $caseData = $data['caseData'];
            $stepData = $data['stepData'];
        }
        else
        {
            $pagerID = 1;
            $rows    = $this->loadModel('file')->parseCSV($file);
            $header  = array();
            foreach($rows[0] as $i => $rowValue)
            {
                if(empty($rowValue)) break;
                $header[$i] = $rowValue;
            }
            unset($rows[0]);

            $endField = end($fields);
            $caseData = array();
            $stepData = array();
            $stepVars = 0;
            foreach($rows as $row => $data)
            {
                $case = new stdclass();
                foreach($header as $key => $title)
                {
                    if(!isset($fields[$title])) continue;
                    $field = $fields[$title];

                    if(!isset($data[$key])) continue;
                    $cellValue = $data[$key];
                    if($field == 'story' or $field == 'module' or $field == 'branch')
                    {
                        $case->$field = 0;
                        if(strrpos($cellValue, '(#') !== false)
                        {
                            $id = trim(substr($cellValue, strrpos($cellValue,'(#') + 2), ')');
                            $case->$field = $id;
                        }
                    }
                    elseif(in_array($field, $caseConfig->export->listFields))
                    {
                        if($field == 'stage')
                        {
                            $stages = explode("\n", $cellValue);
                            foreach($stages as $stage) $case->stage[] = array_search($stage, $caseLang->{$field . 'List'});
                            $case->stage = join(',', $case->stage);
                        }
                        else
                        {
                            $case->$field = array_search($cellValue, $caseLang->{$field . 'List'});
                        }
                    }
                    elseif($field != 'stepDesc' and $field != 'stepExpect')
                    {
                        $case->$field = $cellValue;
                    }
                    else
                    {
                        $steps = (array)$cellValue;
                        if(strpos($cellValue, "\n"))
                        {
                            $steps = explode("\n", $cellValue);
                        }
                        elseif(strpos($cellValue, "\r"))
                        {
                            $steps = explode("\r", $cellValue);
                        }

                        $stepKey  = str_replace('step', '', strtolower($field));
                        $caseStep = array();

                        foreach($steps as $step)
                        {
                            $trimedStep = trim($step);
                            if(empty($trimedStep)) continue;
                            if(preg_match('/^(([0-9]+)\.[0-9]+)([.、]{1})/U', $step, $out))
                            {
                                $num     = $out[1];
                                $parent  = $out[2];
                                $sign    = $out[3];
                                $signbit = $sign == '.' ? 1 : 3;
                                $step    = trim(substr($step, strlen($num) + $signbit));
                                if(!empty($step)) $caseStep[$num]['content'] = $step;
                                $caseStep[$num]['type']    = 'item';
                                $caseStep[$parent]['type'] = 'group';
                            }
                            elseif(preg_match('/^([0-9]+)([.、]{1})/U', $step, $out))
                            {
                                $num     = $out[1];
                                $sign    = $out[2];
                                $signbit = $sign == '.' ? 1 : 3;
                                $step    = trim(substr($step, strpos($step, $sign) + $signbit));
                                if(!empty($step)) $caseStep[$num]['content'] = $step;
                                $caseStep[$num]['type'] = 'step';
                            }
                            elseif(isset($num))
                            {
                                if(!isset($caseStep[$num]['content'])) $caseStep[$num]['content'] = '';
                                $caseStep[$num]['content'] .= "\n" . $step;
                            }
                            else
                            {
                                if($field == 'stepDesc')
                                {
                                    $num = 1;
                                    $caseStep[$num]['content'] = $step;
                                    $caseStep[$num]['type']    = 'step';
                                }
                                if($field == 'stepExpect' and isset($stepData[$row]['desc']))
                                {
                                    end($stepData[$row]['desc']);
                                    $num = key($stepData[$row]['desc']);
                                    $caseStep[$num]['content'] = $step;
                                }
                            }
                        }
                        unset($num);
                        unset($sign);
                        $stepVars += count($caseStep, COUNT_RECURSIVE) - count($caseStep);
                        $stepData[$row][$stepKey] = $caseStep;
                    }
                }

                if(empty($case->title)) continue;
                $caseData[$row] = $case;
                unset($case);
            }

            $data['caseData'] = $caseData;
            $data['stepData'] = $stepData;
            file_put_contents($tmpFile, serialize($data));
        }

        if(empty($caseData))
        {
            echo js::alert($this->lang->error->noData);
            return print(js::locate($this->createLink('testcase', 'browse', "productID=$productID&branch=$branch")));
        }

        $allCount = count($caseData);
        $allPager = 1;
        if($allCount > $this->config->file->maxImport)
        {
            if(empty($maxImport))
            {
                $this->view->allCount  = $allCount;
                $this->view->maxImport = $maxImport;
                $this->view->productID = $productID;
                $this->view->branch    = $branch;
                return print($this->display());
            }

            $allPager = ceil($allCount / $maxImport);
            $caseData = array_slice($caseData, ($pagerID - 1) * $maxImport, $maxImport, true);
        }
        if(empty($caseData)) return print(js::locate(inlink('browse', "productID=$productID&branch=$branch")));

        /* Judge whether the editedCases is too large and set session. */
        $countInputVars  = count($caseData) * 12 + (isset($stepVars) ? $stepVars : 0);
        $showSuhosinInfo = common::judgeSuhosinSetting($countInputVars);
        if($showSuhosinInfo) $this->view->suhosinInfo = extension_loaded('suhosin') ? sprintf($this->lang->suhosinInfo, $countInputVars) : sprintf($this->lang->maxVarsInfo, $countInputVars);

        $this->view->title      = $this->lang->testcase->common . $this->lang->colon . $this->lang->testcase->showImport;
        $this->view->position[] = $this->lang->testcase->showImport;

        $this->view->stories    = $stories;
        $this->view->modules    = $modules;
        $this->view->cases      = $this->testcase->getByProduct($productID);
        $this->view->caseData   = $caseData;
        $this->view->stepData   = $stepData;
        $this->view->productID  = $productID;
        $this->view->branches   = $branches;
        $this->view->isEndPage  = $pagerID >= $allPager;
        $this->view->allCount   = $allCount;
        $this->view->allPager   = $allPager;
        $this->view->pagerID    = $pagerID;
        $this->view->branch     = $branch;
        $this->view->product    = $this->product->getByID($productID);
        $this->view->maxImport  = $maxImport;
        $this->view->dataInsert = $insert;

        $this->display();
    }

    /**
     * Import cases to library.
     *
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function importToLib($caseID = 0)
    {
        if($this->server->request_method == 'POST')
        {
            $this->testcase->importToLib($caseID);
            if(dao::isError()) return $this->send(array('result' => 'fail', 'message' => dao::getError()));
            if(!empty($caseID)) return $this->send(array('result' => 'success', 'message' => $this->lang->importSuccess, 'closeModal' => true));
            return $this->send(array('result' => 'success', 'message' => $this->lang->importSuccess, 'locate' => 'reload'));
        }
        $this->view->libraries = $this->loadModel('caselib')->getLibraries();
        $this->display();
    }

    /**
     * Case bugs.
     *
     * @param  int    $runID
     * @param  int    $caseID
     * @param  int    $version
     * @access public
     * @return void
     */
    public function bugs($runID, $caseID = 0, $version = 0)
    {
        $this->view->title = $this->lang->testcase->bugs;
        $this->view->bugs  = $this->loadModel('bug')->getCaseBugs($runID, $caseID, $version);
        $this->view->users = $this->loadModel('user')->getPairs('noletter');
        $this->display();
    }

    /**
     * Export case getModuleByStory
     *
     * @params int $storyID
     * @return void
     */
    public function ajaxGetStoryModule($storyID)
    {
        $story = $this->dao->select('module')->from(TABLE_STORY)->where('id')->eq($storyID)->fetch();
        $moduleID = !empty($story) ? $story->module : 0;
        echo json_encode(array('moduleID'=> $moduleID));
    }

    /**
     * Get status by ajax.
     *
     * @param  string $methodName
     * @param  int    $caseID
     * @access public
     * @return void
     */
    public function ajaxGetStatus($methodName, $caseID = 0)
    {
        $case   = $this->testcase->getByID($caseID);
        $status = $this->testcase->getStatus($methodName, $case);
        if($methodName == 'update') $status = zget($status, 1, '');
        echo $status;
    }

    /**
     * Ajax: Get count of need review casese.
     *
     * @access public
     * @return int
     */
    public function ajaxGetReviewCount()
    {
        echo $this->dao->select('count(id) as count')->from(TABLE_CASE)->where('status')->eq('wait')->fetch('count');
    }
}

<?php
$lang->navIcons              = array();
$lang->navIcons['my']        = "<i class='icon icon-menu-my'></i>";
$lang->navIcons['program']   = "<i class='icon icon-program'></i>";
$lang->navIcons['product']   = "<i class='icon icon-product'></i>";
$lang->navIcons['project']   = "<i class='icon icon-project'></i>";
$lang->navIcons['execution'] = "<i class='icon icon-run'></i>";
$lang->navIcons['qa']        = "<i class='icon icon-test'></i>";
$lang->navIcons['devops']    = "<i class='icon icon-devops'></i>";
$lang->navIcons['kanban']    = "<i class='icon icon-kanban'></i>";
$lang->navIcons['doc']       = "<i class='icon icon-doc'></i>";
$lang->navIcons['report']    = "<i class='icon icon-statistic'></i>";
$lang->navIcons['system']    = "<i class='icon icon-group'></i>";
$lang->navIcons['admin']     = "<i class='icon icon-cog-outline'></i>";

global $config;
list($programModule, $programMethod)     = explode('-', $config->programLink);
list($productModule, $productMethod)     = explode('-', $config->productLink);
list($projectModule, $projectMethod)     = explode('-', $config->projectLink);
list($executionModule, $executionMethod) = explode('-', $config->executionLink);

if(defined('TUTORIAL'))
{
    $programModule   = 'program';
    $programMethod   = 'browse';
    $productModule   = 'product';
    $productMethod   = 'all';
    $projectModule   = 'project';
    $projectMethod   = 'browse';
    $executionModule = 'execution';
    $executionMethod = 'task';
}

/* Main Navigation. */
$lang->mainNav            = new stdclass();
$lang->mainNav->my        = "{$lang->navIcons['my']} {$lang->my->shortCommon}|my|index|";
$lang->mainNav->program   = "{$lang->navIcons['program']} {$lang->program->common}|$programModule|$programMethod|";
$lang->mainNav->product   = "{$lang->navIcons['product']} {$lang->product->common}|$productModule|$productMethod|";
$lang->mainNav->project   = "{$lang->navIcons['project']} {$lang->project->common}|$projectModule|$projectMethod|";
$lang->mainNav->execution = "{$lang->navIcons['execution']} {$lang->execution->common}|$executionModule|$executionMethod|";
$lang->mainNav->qa        = "{$lang->navIcons['qa']} {$lang->qa->common}|qa|index|";
$lang->mainNav->devops    = "{$lang->navIcons['devops']} DevOps|repo|browse|";
$lang->mainNav->kanban    = "{$lang->navIcons['kanban']} {$lang->kanban->common}|kanban|space|";
$lang->mainNav->doc       = "{$lang->navIcons['doc']} {$lang->doc->common}|doc|index|";
$lang->mainNav->report    = "{$lang->navIcons['report']} {$lang->report->common}|report|productSummary|";
$lang->mainNav->system    = "{$lang->navIcons['system']} {$lang->system->common}|my|team|";
$lang->mainNav->admin     = "{$lang->navIcons['admin']} {$lang->admin->common}|admin|index|";

$lang->dividerMenu = ',kanban,oa,admin,';

$lang->mainNav->menuOrder[5] = 'my';
$lang->mainNav->menuOrder[10] = 'program';
$lang->mainNav->menuOrder[15] = 'product';
$lang->mainNav->menuOrder[20] = 'project';
$lang->mainNav->menuOrder[25] = 'execution';
$lang->mainNav->menuOrder[30] = 'qa';
$lang->mainNav->menuOrder[35] = 'devops';
$lang->mainNav->menuOrder[40] = 'kanban';
$lang->mainNav->menuOrder[45] = 'doc';
$lang->mainNav->menuOrder[50] = 'report';
$lang->mainNav->menuOrder[55] = 'system';
$lang->mainNav->menuOrder[60] = 'admin';

if($config->systemMode == 'light') unset($lang->mainNav->program, $lang->mainNav->menuOrder[10]);

/* My menu. */
$lang->my->menu             = new stdclass();
$lang->my->menu->index      = array('link' => "$lang->dashboard|my|index");
$lang->my->menu->calendar   = array('link' => "$lang->calendar|my|calendar|", 'subModule' => 'todo', 'alias' => 'todo');
$lang->my->menu->work       = array('link' => "{$lang->my->work}|my|work|mode=task", 'subModule' => 'task');
$lang->my->menu->audit      = array('link' => "{$lang->review->common}|my|audit|type=all&param=&orderBy=time_desc", 'subModule' => 'review');
$lang->my->menu->project    = array('link' => "{$lang->project->common}|my|project|");
$lang->my->menu->execution  = array('link' => "{$lang->execution->common}|my|execution|type=undone");
$lang->my->menu->contribute = array('link' => "$lang->contribute|my|contribute|mode=task");
$lang->my->menu->dynamic    = array('link' => "$lang->dynamic|my|dynamic|");
$lang->my->menu->score      = array('link' => "{$lang->score->shortCommon}|my|score|", 'subModule' => 'score');
$lang->my->menu->contacts   = array('link' => "$lang->contact|my|managecontacts|");

/* My menu order. */
$lang->my->menuOrder[5]  = 'index';
$lang->my->menuOrder[10] = 'calendar';
$lang->my->menuOrder[15] = 'work';
$lang->my->menuOrder[20] = 'audit';
$lang->my->menuOrder[25] = 'project';
$lang->my->menuOrder[30] = 'execution';
$lang->my->menuOrder[35] = 'contribute';
$lang->my->menuOrder[40] = 'dynamic';
$lang->my->menuOrder[45] = 'score';
$lang->my->menuOrder[50] = 'contacts';

if(!$config->systemScore) unset($lang->my->menu->score, $lang->my->menuOrder[45]);

$lang->my->menu->work['subMenu']              = new stdclass();
$lang->my->menu->work['subMenu']->task        = array('link' => "{$lang->task->common}|my|work|mode=task", 'subModule' => 'task');
$lang->my->menu->work['subMenu']->requirement = "$lang->URCommon|my|work|mode=requirement";
$lang->my->menu->work['subMenu']->story       = "$lang->SRCommon|my|work|mode=story";
$lang->my->menu->work['subMenu']->bug         = "{$lang->bug->common}|my|work|mode=bug";
$lang->my->menu->work['subMenu']->testcase    = array('link' => "{$lang->testcase->common}|my|work|mode=testcase&type=assigntome", 'subModule' => 'testtask');
$lang->my->menu->work['subMenu']->testtask    = "{$lang->testtask->common}|my|work|mode=testtask&type=wait";

$lang->my->menu->work['menuOrder'][5]  = 'task';
$lang->my->menu->work['menuOrder'][10] = 'requirement';
$lang->my->menu->work['menuOrder'][15] = 'story';
$lang->my->menu->work['menuOrder'][20] = 'bug';
$lang->my->menu->work['menuOrder'][25] = 'testcase';
$lang->my->menu->work['menuOrder'][30] = 'testtask';
$lang->my->menu->work['menuOrder'][35] = 'audit';

if(!$config->URAndSR) unset($lang->my->menu->work['subMenu']->requirement, $lang->my->menu->work['menuOrder'][10]);

$lang->my->menu->contribute['subMenu']              = new stdclass();
$lang->my->menu->contribute['subMenu']->task        = "{$lang->task->common}|my|contribute|mode=task";
$lang->my->menu->contribute['subMenu']->requirement = "$lang->URCommon|my|contribute|mode=requirement";
$lang->my->menu->contribute['subMenu']->story       = "$lang->SRCommon|my|contribute|mode=story";
$lang->my->menu->contribute['subMenu']->bug         = "{$lang->bug->common}|my|contribute|mode=bug";
$lang->my->menu->contribute['subMenu']->testcase    = "{$lang->testcase->shortCommon}|my|contribute|mode=testcase&type=openedbyme";
$lang->my->menu->contribute['subMenu']->testtask    = "{$lang->testtask->common}|my|contribute|mode=testtask&type=done";
$lang->my->menu->contribute['subMenu']->audit       = array('link' => "{$lang->review->common}|my|contribute|mode=audit&type=reviewedbyme", 'subModule' => 'review');
$lang->my->menu->contribute['subMenu']->doc         = "{$lang->doc->common}|my|contribute|mode=doc&type=openedbyme";

$lang->my->menu->contribute['menuOrder'][5]  = 'task';
$lang->my->menu->contribute['menuOrder'][10] = 'requirement';
$lang->my->menu->contribute['menuOrder'][15] = 'story';
$lang->my->menu->contribute['menuOrder'][20] = 'bug';
$lang->my->menu->contribute['menuOrder'][25] = 'testcase';
$lang->my->menu->contribute['menuOrder'][30] = 'testtask';
$lang->my->menu->contribute['menuOrder'][35] = 'audit';
$lang->my->menu->contribute['menuOrder'][40] = 'doc';

if(!$config->URAndSR) unset($lang->my->menu->contribute['subMenu']->requirement, $lang->my->menu->contribute['menuOrder'][10]);

$lang->my->dividerMenu = ',work,dynamic,';

/* Program menu. */
$lang->program->homeMenu = new stdclass();
$lang->program->homeMenu->browse = array('link' => "{$lang->program->list}|program|browse|", 'alias' => 'create,edit', 'subModule' => 'project');
$lang->program->homeMenu->kanban = array('link' => "{$lang->program->kanban}|program|kanban|");

$lang->program->menu = new stdclass();
$lang->program->menu->product     = array('link' => "{$lang->product->common}|program|product|programID=%s", 'alias' => 'view');
$lang->program->menu->project     = array('link' => "{$lang->project->common}|program|project|programID=%s");
$lang->program->menu->personnel   = array('link' => "{$lang->personnel->common}|personnel|invest|programID=%s");
$lang->program->menu->stakeholder = array('link' => "{$lang->stakeholder->common}|program|stakeholder|programID=%s", 'alias' => 'createstakeholder');

/* Program menu order. */
$lang->program->menuOrder[5]  = 'product';
$lang->program->menuOrder[10] = 'project';
$lang->program->menuOrder[15] = 'personnel';
$lang->program->menuOrder[20] = 'stakeholder';

$lang->program->menu->personnel['subMenu'] = new stdClass();
$lang->program->menu->personnel['subMenu']->invest     = array('link' => "{$lang->personnel->invest}|personnel|invest|programID=%s");
$lang->program->menu->personnel['subMenu']->accessible = array('link' => "{$lang->personnel->accessible}|personnel|accessible|programID=%s");
$lang->program->menu->personnel['subMenu']->whitelist  = array('link' => "{$lang->whitelist}|personnel|whitelist|objectID=%s", 'alias' => 'addwhitelist');

/* Product menu. */
$lang->product->homeMenu = new stdclass();
$lang->product->homeMenu->home   = array('link' => "{$lang->dashboard}|product|index|");
$lang->product->homeMenu->list   = array('link' => $lang->product->list . '|product|all|', 'alias' => 'create,batchedit,manageline');
$lang->product->homeMenu->kanban = array('link' => "{$lang->product->kanban}|product|kanban|");

$lang->product->menu              = new stdclass();
$lang->product->menu->dashboard   = array('link' => "{$lang->dashboard}|product|dashboard|productID=%s");
$lang->product->menu->story       = array('link' => "$lang->SRCommon|product|browse|productID=%s", 'alias' => 'batchedit', 'subModule' => 'story');
$lang->product->menu->plan        = array('link' => "{$lang->productplan->shortCommon}|productplan|browse|productID=%s", 'subModule' => 'productplan,bug');
$lang->product->menu->project     = array('link' => "{$lang->project->common}|product|project|status=all&productID=%s");
$lang->product->menu->release     = array('link' => "{$lang->release->common}|release|browse|productID=%s", 'subModule' => 'release');
$lang->product->menu->roadmap     = array('link' => "{$lang->roadmap}|product|roadmap|productID=%s");
$lang->product->menu->requirement = array('link' => "{$lang->URCommon}|product|browse|productID=%s&branch=&browseType=unclosed&param=0&storyType=requirement", 'alias' => 'batchedit', 'subModule' => 'story');
$lang->product->menu->track       = array('link' => "{$lang->track}|product|track|productID=%s");
$lang->product->menu->doc         = array('link' => "{$lang->doc->common}|doc|tableContents|type=product&objectID=%s", 'subModule' => 'doc');
$lang->product->menu->dynamic     = array('link' => "{$lang->dynamic}|product|dynamic|productID=%s");
$lang->product->menu->settings    = array('link' => "{$lang->settings}|product|view|productID=%s", 'subModule' => 'tree,branch', 'alias' => 'edit,whitelist,addwhitelist');

/* Product menu order. */
$lang->product->menuOrder[5]  = 'dashboard';
$lang->product->menuOrder[10] = 'story';
$lang->product->menuOrder[15] = 'plan';
$lang->product->menuOrder[20] = 'project';
$lang->product->menuOrder[25] = 'release';
$lang->product->menuOrder[30] = 'roadmap';
$lang->product->menuOrder[35] = 'requirement';
$lang->product->menuOrder[40] = 'track';
$lang->product->menuOrder[45] = 'doc';
$lang->product->menuOrder[50] = 'dynamic';
$lang->product->menuOrder[55] = 'settings';
$lang->product->menuOrder[60] = 'create';
$lang->product->menuOrder[65] = 'all';

$lang->product->menu->doc['subMenu'] = new stdclass();

$lang->product->menu->settings['subMenu'] = new stdclass();
$lang->product->menu->settings['subMenu']->view      = array('link' => "{$lang->overview}|product|view|productID=%s", 'alias' => 'edit');
$lang->product->menu->settings['subMenu']->module    = array('link' => "{$lang->module}|tree|browse|product=%s&view=story", 'subModule' => 'tree');
$lang->product->menu->settings['subMenu']->branch    = array('link' => "@branch@|branch|manage|product=%s", 'subModule' => 'branch');
$lang->product->menu->settings['subMenu']->whitelist = array('link' => "{$lang->whitelist}|product|whitelist|product=%s", 'subModule' => 'personnel');

$lang->product->dividerMenu = $config->URAndSR ? ',story,requirement,settings,' : ',story,track,settings,';

/* Project menu. */
$lang->project->homeMenu         = new stdclass();
$lang->project->homeMenu->browse = array('link' => "{$lang->project->list}|project|browse|", 'alias' => 'batchedit,create');
$lang->project->homeMenu->kanban = array('link' => "{$lang->project->kanban}|project|kanban|");

/* Scrum menu. */
$lang->scrum->menu              = new stdclass();
$lang->scrum->menu->index       = array('link' => "{$lang->dashboard}|project|index|project=%s");
$lang->scrum->menu->execution   = array('link' => "$lang->executionCommon|project|execution|status=all&projectID=%s", 'exclude' => 'execution-testreport', 'subModule' => 'task');
$lang->scrum->menu->storyGroup  = array('link' => "{$lang->common->story}|projectstory|story|projectID=%s&product=%s",'class' => 'dropdown dropdown-hover', 'exclude' => 'tree-browse');
$lang->scrum->menu->story       = array('link' => "$lang->SRCommon|projectstory|story|projectID=%s", 'subModule' => 'projectstory,tree', 'alias' => 'story,track');
$lang->scrum->menu->projectplan = array('link' => "{$lang->productplan->shortCommon}|projectplan|browse|productID=%s", 'subModule' => 'productplan');
$lang->scrum->menu->doc         = array('link' => "{$lang->doc->common}|doc|tableContents|type=project&objectID=%s", 'subModule' => 'doc');
$lang->scrum->menu->qa          = array('link' => "{$lang->qa->common}|project|bug|projectID=%s", 'subModule' => 'testcase,testtask,bug,testreport', 'alias' => 'bug,testtask,testcase,testreport', 'exclude' => 'execution-create,execution-batchedit');
$lang->scrum->menu->devops      = array('link' => "{$lang->repo->common}|repo|browse|repoID=0&branchID=&objectID=%s", 'subModule' => 'repo');
$lang->scrum->menu->build       = array('link' => "{$lang->build->common}|project|build|project=%s", 'subModule' => 'projectbuild');
$lang->scrum->menu->release     = array('link' => "{$lang->release->common}|projectrelease|browse|project=%s", 'subModule' => 'projectrelease');
$lang->scrum->menu->dynamic     = array('link' => "$lang->dynamic|project|dynamic|project=%s");
$lang->scrum->menu->settings    = array('link' => "$lang->settings|project|view|project=%s", 'subModule' => 'tree,stakeholder', 'alias' => 'edit,manageproducts,group,managemembers,manageview,managepriv,whitelist,addwhitelist,team,managerepo');

$lang->scrum->menu->storyGroup['dropMenu'] = new stdclass();
$lang->scrum->menu->storyGroup['dropMenu']->story       = array('link' => "{$lang->SRCommon}|projectstory|story|projectID=%s&productID=%s", 'subModule' => 'projectstory,tree');
$lang->scrum->menu->storyGroup['dropMenu']->requirement = array('link' => "{$lang->URCommon}|projectstory|story|projectID=%s&productID=%s&branch=0&browseType=&param=0&storyType=requirement", 'subModule' => 'projectstory,tree');

$lang->scrum->dividerMenu = ',execution,programplan,doc,settings,';

/* Scrum menu order. */
$lang->scrum->menuOrder[5]  = 'index';
$lang->scrum->menuOrder[10] = 'execution';
$lang->scrum->menuOrder[15] = 'story';
$lang->scrum->menuOrder[16] = 'storyGroup';
$lang->scrum->menuOrder[18] = 'projectplan';
$lang->scrum->menuOrder[20] = 'qa';
$lang->scrum->menuOrder[25] = 'devops';
$lang->scrum->menuOrder[30] = 'doc';
$lang->scrum->menuOrder[35] = 'build';
$lang->scrum->menuOrder[40] = 'release';
$lang->scrum->menuOrder[48] = 'dynamic';
$lang->scrum->menuOrder[50] = 'settings';

$lang->scrum->menu->doc['subMenu'] = new stdclass();

$lang->scrum->menu->qa['subMenu'] = new stdclass();
//$lang->scrum->menu->qa['subMenu']->index      = array('link' => "$lang->dashboard|project|qa|projectID=%s");
$lang->scrum->menu->qa['subMenu']->bug        = array('link' => "{$lang->bug->common}|project|bug|projectID=%s", 'subModule' => 'bug');
$lang->scrum->menu->qa['subMenu']->testcase   = array('link' => "{$lang->testcase->shortCommon}|project|testcase|projectID=%s", 'subModule' => 'testsuite,testcase,caselib,tree');
$lang->scrum->menu->qa['subMenu']->testtask   = array('link' => "{$lang->testtask->common}|project|testtask|projectID=%s", 'subModule' => 'testtask', 'class' => 'dropdown dropdown-hover');
$lang->scrum->menu->qa['subMenu']->testreport = array('link' => "{$lang->testreport->common}|project|testreport|projectID=%s", 'subModule' => 'testreport');

$lang->scrum->menu->settings['subMenu']              = new stdclass();
$lang->scrum->menu->settings['subMenu']->view        = array('link' => "$lang->overview|project|view|project=%s", 'alias' => 'edit');
$lang->scrum->menu->settings['subMenu']->products    = array('link' => "{$lang->product->common}|project|manageProducts|project=%s", 'alias' => 'manageproducts');
$lang->scrum->menu->settings['subMenu']->members     = array('link' => "{$lang->team->common}|project|team|project=%s", 'alias' => 'managemembers,team');
$lang->scrum->menu->settings['subMenu']->whitelist   = array('link' => "{$lang->whitelist}|project|whitelist|project=%s", 'subModule' => 'personnel');
$lang->scrum->menu->settings['subMenu']->stakeholder = array('link' => "{$lang->stakeholder->common}|stakeholder|browse|project=%s", 'subModule' => 'stakeholder');
$lang->scrum->menu->settings['subMenu']->group       = array('link' => "{$lang->priv}|project|group|project=%s", 'alias' => 'group,manageview,managepriv');
$lang->scrum->menu->settings['subMenu']->module      = array('link' => "{$lang->module}|tree|browse|product=%s&view=story");
$lang->scrum->menu->settings['subMenu']->managerepo  = array('link' => "{$lang->repo->codeRepo}|project|managerepo|project=%s");

/* Waterfall menu. */
$lang->waterfall->menu = new stdclass();
$lang->waterfall->menu->index      = array('link' => "$lang->dashboard|project|index|project=%s");
$lang->waterfall->menu->execution  = array('link' => "{$lang->stage->common}|project|execution|status=all&projectID=%s", 'subModule' => 'programplan,task');
$lang->waterfall->menu->storyGroup = array('link' => "{$lang->common->story}|projectstory|story|projectID=%s",'class' => 'dropdown dropdown-hover', 'exclude' => 'tree-browse,projectstory-track');
$lang->waterfall->menu->story      = array('link' => "$lang->SRCommon|projectstory|story|projectID=%s", 'subModule' => 'projectstory,tree', 'alias' => 'story,track', 'exclude' => 'projectstory-track');
$lang->waterfall->menu->design     = array('link' => "{$lang->design->common}|design|browse|project=%s");
$lang->waterfall->menu->qa         = array('link' => "{$lang->qa->common}|project|bug|projectID=%s", 'subModule' => 'testcase,testtask,bug,testreport', 'alias' => 'bug,testtask,testcase,testreport');
$lang->waterfall->menu->doc        = array('link' => "{$lang->doc->common}|doc|tableContents|type=project&objectID=%s");
$lang->waterfall->menu->devops     = array('link' => "{$lang->repo->common}|repo|browse|repoID=0&branchID=&objectID=%s", 'subModule' => 'repo');
$lang->waterfall->menu->build      = array('link' => "{$lang->build->common}|project|build|project=%s");
$lang->waterfall->menu->release    = array('link' => "{$lang->release->common}|projectrelease|browse|project=%s", 'subModule' => 'projectrelease');
$lang->waterfall->menu->dynamic    = array('link' => "$lang->dynamic|project|dynamic|project=%s");

$lang->waterfall->menu->settings = $lang->scrum->menu->settings;
$lang->waterfall->dividerMenu    = ',programplan,build,dynamic,';

$lang->waterfall->menu->storyGroup['dropMenu'] = $lang->scrum->menu->storyGroup['dropMenu'];

/* Waterfall menu order. */
$lang->waterfall->menuOrder[5]  = 'index';
$lang->waterfall->menuOrder[15] = 'programplan';
$lang->waterfall->menuOrder[20] = 'execution';
$lang->waterfall->menuOrder[25] = 'story';
$lang->waterfall->menuOrder[26] = 'storyGroup';
$lang->waterfall->menuOrder[30] = 'design';
$lang->waterfall->menuOrder[35] = 'devops';
$lang->waterfall->menuOrder[55] = 'qa';
$lang->waterfall->menuOrder[60] = 'doc';
$lang->waterfall->menuOrder[65] = 'build';
$lang->waterfall->menuOrder[70] = 'release';
$lang->waterfall->menuOrder[80] = 'dynamic';

$lang->waterfall->menu->doc['subMenu'] = new stdclass();

$lang->waterfall->menu->programplan['subMenu'] = new stdclass();
$lang->waterfall->menu->programplan['subMenu']->lists = array('link' => "{$lang->stage->list}|programplan|browse|projectID=%s&productID=0&type=lists", 'alias' => 'create');

$lang->waterfall->menu->qa['subMenu'] = new stdclass();
$lang->waterfall->menu->qa['subMenu']->bug        = array('link' => "{$lang->bug->common}|project|bug|projectID=%s", 'subModule' => 'bug');
$lang->waterfall->menu->qa['subMenu']->testcase   = array('link' => "{$lang->testcase->shortCommon}|project|testcase|projectID=%s", 'subModule' => 'testsuite,testcase,caselib,tree');
$lang->waterfall->menu->qa['subMenu']->testtask   = array('link' => "{$lang->testtask->common}|project|testtask|projectID=%s", 'subModule' => 'testtask', 'class' => 'dropdown dropdown-hover');
$lang->waterfall->menu->qa['subMenu']->testreport = array('link' => "{$lang->testreport->common}|project|testreport|projectID=%s", 'subModule' => 'testreport');

$lang->waterfall->menu->design['subMenu'] = new stdclass();
$lang->waterfall->menu->design['subMenu']->all      = array('link' => "$lang->all|design|browse|projectID=%s&productID=0&browseType=all");
$lang->waterfall->menu->design['subMenu']->hlds     = array('link' => "{$lang->design->HLDS}|design|browse|projectID=%s&productID=0&browseType=HLDS");
$lang->waterfall->menu->design['subMenu']->dds      = array('link' => "{$lang->design->DDS}|design|browse|projectID=%s&productID=0&browseType=DDS");
$lang->waterfall->menu->design['subMenu']->dbds     = array('link' => "{$lang->design->DBDS}|design|browse|projectID=%s&productID=0&browseType=DBDS");
$lang->waterfall->menu->design['subMenu']->ads      = array('link' => "{$lang->design->ADS}|design|browse|projectID=%s&productID=0&browseType=ADS");
$lang->waterfall->menu->design['subMenu']->bysearch = array('link' => '<a href="javascript:;" class="querybox-toggle"><i class="icon-search icon"></i> ' . $lang->searchAB . '</a>');

/* Kanban project menu. */
$lang->kanbanProject = new stdclass();
$lang->kanbanProject->menu = new stdclass();
$lang->kanbanProject->menu->index    = array('link' => "{$lang->kanban->common}|project|index|project=%s");
$lang->kanbanProject->menu->build    = array('link' => "{$lang->build->common}|project|build|project=%s");
$lang->kanbanProject->menu->settings = array('link' => "$lang->settings|project|view|project=%s", 'subModule' => 'tree,stakeholder', 'alias' => 'edit,manageproducts,group,managemembers,manageview,managepriv,whitelist,addwhitelist,team');

$lang->kanbanProject->dividerMenu = '';

$lang->kanbanProject->menuOrder     = array();
$lang->kanbanProject->menuOrder[5]  = 'index';
$lang->kanbanProject->menuOrder[10] = 'build';
$lang->kanbanProject->menuOrder[15] = 'settings';

$lang->kanbanProject->menu->settings['subMenu']            = new stdclass();
$lang->kanbanProject->menu->settings['subMenu']->view      = array('link' => "$lang->overview|project|view|project=%s", 'alias' => 'edit');
$lang->kanbanProject->menu->settings['subMenu']->products  = array('link' => "{$lang->product->common}|project|manageProducts|project=%s", 'alias' => 'manageproducts');
$lang->kanbanProject->menu->settings['subMenu']->members   = array('link' => "{$lang->team->common}|project|team|project=%s", 'alias' => 'managemembers,team');
$lang->kanbanProject->menu->settings['subMenu']->whitelist = array('link' => "{$lang->whitelist}|project|whitelist|project=%s", 'subModule' => 'personnel');
$lang->kanbanProject->menu->settings['subMenu']->module    = array('link' => "{$lang->module}|tree|browse|product=%s&view=story");

/* Execution menu. */
$lang->execution->homeMenu = new stdclass();
$lang->execution->homeMenu->all             = array('link' => "{$lang->execution->all}|execution|all|", 'alias' => 'create,batchedit');
$lang->execution->homeMenu->executionkanban = array('link' => "{$lang->execution->executionKanban}|execution|executionkanban|");

$lang->execution->menu = new stdclass();
$lang->execution->menu->task   = array('link' => "{$lang->task->common}|execution|task|executionID=%s", 'subModule' => 'task,tree', 'alias' => 'importtask,importbug', 'exclude' => 'tree-browse');
$lang->execution->menu->kanban = array('link' => "$lang->executionKanban|execution|taskkanban|executionID=%s");
$lang->execution->menu->burn   = array('link' => "$lang->burn|execution|burn|executionID=%s");
$lang->execution->menu->view   = array('link' => "$lang->view|execution|grouptask|executionID=%s", 'alias' => 'grouptask,tree,taskeffort,gantt,calendar,relation,maintainrelation');

if($config->edition != 'open') $lang->execution->menu->view = array('link' => "$lang->view|execution|gantt|executionID=%s", 'alias' => 'grouptask,tree,taskeffort,gantt,calendar,relation,maintainrelation');

$lang->execution->menu->storyGroup = array('link' => "{$lang->common->story}|execution|story|executionID=%s",'class' => 'dropdown dropdown-hover', 'subModule' => 'story', 'alias' => 'batchcreate,storykanban');
$lang->execution->menu->story      = array('link' => "$lang->SRCommon|execution|story|executionID=%s", 'subModule' => 'story', 'alias' => 'storykanban');
$lang->execution->menu->qa         = array('link' => "{$lang->qa->common}|execution|bug|executionID=%s", 'subModule' => 'bug,testcase,testtask,testreport', 'alias' => 'qa,bug,testcase,testtask,testreport');
$lang->execution->menu->devops     = array('link' => "{$lang->repo->common}|repo|browse|repoID=0&branchID=&objectID=%s", 'subModule' => 'repo');
$lang->execution->menu->doc        = array('link' => "{$lang->doc->common}|doc|tableContents|type=execution&objectID=%s", 'subModule' => 'doc');
$lang->execution->menu->build      = array('link' => "{$lang->build->common}|execution|build|executionID=%s", 'subModule' => 'build');
$lang->execution->menu->action     = array('link' => "$lang->dynamic|execution|dynamic|executionID=%s");
$lang->execution->menu->settings   = array('link' => "$lang->settings|execution|view|executionID=%s", 'subModule' => 'personnel', 'alias' => 'edit,manageproducts,team,whitelist,addwhitelist,managemembers', 'class' => 'dropdown dropdown-hover');
$lang->execution->menu->more       = array('link' => "$lang->more|execution|more|%s");

$lang->execution->menu->storyGroup['dropMenu'] = new stdclass();
$lang->execution->menu->storyGroup['dropMenu']->story       = array('link' => "{$lang->SRCommon}|execution|story|executionID=%s", 'subModule' => 'story');
$lang->execution->menu->storyGroup['dropMenu']->requirement = array('link' => "{$lang->URCommon}|execution|story|executionID=%s&storyType=requirement", 'subModule' => 'story');

/* Execution menu order. */
$lang->execution->menuOrder[5]  = 'task';
$lang->execution->menuOrder[10] = 'kanban';
$lang->execution->menuOrder[15] = 'CFD';
$lang->execution->menuOrder[20] = 'burn';
$lang->execution->menuOrder[25] = 'view';
$lang->execution->menuOrder[30] = 'story';
$lang->execution->menuOrder[35] = 'qa';
$lang->execution->menuOrder[40] = 'repo';
$lang->execution->menuOrder[45] = 'devops';
$lang->execution->menuOrder[50] = 'doc';
$lang->execution->menuOrder[55] = 'build';
$lang->execution->menuOrder[60] = 'release';
$lang->execution->menuOrder[65] = 'action';
$lang->execution->menuOrder[70] = 'settings';

$lang->execution->menu->doc['subMenu'] = new stdclass();

$lang->execution->menu->view['subMenu']            = new stdclass();
$lang->execution->menu->view['subMenu']->groupTask = "$lang->groupView|execution|grouptask|executionID=%s";
$lang->execution->menu->view['subMenu']->tree      = "$lang->treeView|execution|tree|executionID=%s";

$lang->execution->menu->qa['subMenu'] = new stdclass();
//$lang->execution->menu->qa['subMenu']->qa         = array('link' => "$lang->dashboard|execution|qa|executionID=%s");
$lang->execution->menu->qa['subMenu']->bug        = array('link' => "{$lang->bug->common}|execution|bug|executionID=%s", 'subModule' => 'bug');
$lang->execution->menu->qa['subMenu']->testcase   = array('link' => "{$lang->testcase->shortCommon}|execution|testcase|executionID=%s", 'subModule' => 'testcase');
$lang->execution->menu->qa['subMenu']->testtask   = array('link' => "{$lang->testtask->common}|execution|testtask|executionID=%s", 'subModule' => 'testtask');
$lang->execution->menu->qa['subMenu']->testreport = array('link' => "{$lang->testreport->common}|execution|testreport|exeutionID=%s", 'subModule' => 'testreport');

$lang->execution->menu->qa['menuOrder'][5]  = 'qa';
$lang->execution->menu->qa['menuOrder'][10] = 'bug';
$lang->execution->menu->qa['menuOrder'][15] = 'testcase';
$lang->execution->menu->qa['menuOrder'][20] = 'testtask';

$lang->execution->menu->settings['subMenu'] = new stdclass();
$lang->execution->menu->settings['subMenu']->view      = array('link' => "$lang->overview|execution|view|executionID=%s", 'subModule' => 'view', 'alias' => 'edit,start,suspend,putoff,close');
$lang->execution->menu->settings['subMenu']->products  = array('link' => "$lang->productCommon|execution|manageproducts|executionID=%s");
$lang->execution->menu->settings['subMenu']->team      = array('link' => "{$lang->team->common}|execution|team|executionID=%s", 'alias' => 'managemembers');
$lang->execution->menu->settings['subMenu']->whitelist = array('link' => "$lang->whitelist|execution|whitelist|executionID=%s", 'subModule' => 'personnel', 'alias' => 'addwhitelist');

$lang->execution->dividerMenu = ',story,build,';

$lang->project->noMultiple                          = new stdclass();
$lang->project->noMultiple->scrum                   = new stdclass();
$lang->project->noMultiple->scrum->menu             = new stdclass();
$lang->project->noMultiple->scrum->menu->task       = $lang->execution->menu->task;
$lang->project->noMultiple->scrum->menu->kanban     = $lang->execution->menu->kanban;
$lang->project->noMultiple->scrum->menu->burn       = $lang->execution->menu->burn;
$lang->project->noMultiple->scrum->menu->view       = $lang->execution->menu->view;
$lang->project->noMultiple->scrum->menu->story      = $lang->execution->menu->story;
$lang->project->noMultiple->scrum->menu->storyGroup = $lang->execution->menu->storyGroup;
$lang->project->noMultiple->scrum->menu->qa         = $lang->scrum->menu->qa;
$lang->project->noMultiple->scrum->menu->devops     = $lang->scrum->menu->devops;
$lang->project->noMultiple->scrum->menu->doc        = $lang->scrum->menu->doc;
$lang->project->noMultiple->scrum->menu->build      = $lang->scrum->menu->build;
$lang->project->noMultiple->scrum->menu->release    = $lang->scrum->menu->release;
$lang->project->noMultiple->scrum->menu->dynamic    = $lang->scrum->menu->dynamic;
$lang->project->noMultiple->scrum->menu->settings   = $lang->scrum->menu->settings;

$lang->project->noMultiple->kanban                 = new stdclass();
$lang->project->noMultiple->kanban->menu           = new stdclass();
$lang->project->noMultiple->kanban->menu->kanban   = array('link' => "{$lang->kanban->common}|execution|kanban|executionID=%s");
$lang->project->noMultiple->kanban->menu->build    = $lang->kanbanProject->menu->build;
$lang->project->noMultiple->kanban->menu->settings = $lang->kanbanProject->menu->settings;

$lang->project->noMultiple->scrum->dividerMenu  = ',story,build,';
$lang->project->noMultiple->kanban->dividerMenu = '';

$lang->project->noMultiple->scrum->menuOrder[5]  = 'task';
$lang->project->noMultiple->scrum->menuOrder[10] = 'kanban';
$lang->project->noMultiple->scrum->menuOrder[15] = 'burn';
$lang->project->noMultiple->scrum->menuOrder[20] = 'view';
$lang->project->noMultiple->scrum->menuOrder[25] = 'story';
$lang->project->noMultiple->scrum->menuOrder[30] = 'qa';
$lang->project->noMultiple->scrum->menuOrder[35] = 'devops';
$lang->project->noMultiple->scrum->menuOrder[40] = 'doc';
$lang->project->noMultiple->scrum->menuOrder[45] = 'build';
$lang->project->noMultiple->scrum->menuOrder[48] = 'release';
$lang->project->noMultiple->scrum->menuOrder[50] = 'dynamic';
$lang->project->noMultiple->scrum->menuOrder[55] = 'settings';

$lang->project->noMultiple->kanban->menuOrder[5]  = 'kanban';
$lang->project->noMultiple->kanban->menuOrder[10] = 'build';
$lang->project->noMultiple->kanban->menuOrder[15] = 'settings';

/* QA menu.*/
$lang->qa->menu = new stdclass();
$lang->qa->menu->index      = array('link' => "$lang->dashboard|qa|index");
$lang->qa->menu->bug        = array('link' => "{$lang->bug->common}|bug|browse|productID=%s", 'subModule' => 'bug');
$lang->qa->menu->testcase   = array('link' => "{$lang->testcase->shortCommon}|testcase|browse|productID=%s", 'subModule' => 'testcase,story');
$lang->qa->menu->testsuite  = array('link' => "{$lang->testcase->testsuite}|testsuite|browse|productID=%s", 'subModule' => 'testsuite');
$lang->qa->menu->testtask   = array('link' => "{$lang->testtask->common}|testtask|browse|productID=%s", 'subModule' => 'testtask', 'alias' => 'view,edit,linkcase,cases,start,close,batchrun,groupcase,report,importunitresult');
$lang->qa->menu->report     = array('link' => "{$lang->testreport->common}|testreport|browse|productID=%s", 'subModule' => 'testreport');
$lang->qa->menu->caselib    = array('link' => "{$lang->testcase->caselib}|caselib|browse|libID=0", 'subModule' => 'caselib');
$lang->qa->menu->automation = array('link' => "{$lang->automation->common}|automation|browse|productID=%s", 'subModule' => 'automation', 'alias' => '');

/* QA menu order. */
$lang->qa->menuOrder[5]  = 'product';
$lang->qa->menuOrder[10] = 'index';
$lang->qa->menuOrder[15] = 'bug';
$lang->qa->menuOrder[20] = 'testcase';
$lang->qa->menuOrder[25] = 'testsuite';
$lang->qa->menuOrder[30] = 'testtask';
$lang->qa->menuOrder[35] = 'report';
$lang->qa->menuOrder[40] = 'caselib';
$lang->qa->menuOrder[45] = 'automation';

// $lang->qa->menu->automation['subMenu'] = new stdclass();
// $lang->qa->menu->automation['subMenu']->browse      = array('link' => "{$lang->intro}|automation|browse|productID=%s", 'alias' => '');
// $lang->qa->menu->automation['subMenu']->framework   = array('link' => '框架|automation|framework|productID=%s', 'alias' => '');
// $lang->qa->menu->automation['subMenu']->data        = array('link' => '数据|automation|date|productID=%s', 'alias' => '');
// $lang->qa->menu->automation['subMenu']->interface   = array('link' => '接口|automation|interface|productID=%s', 'alias' => '');
// $lang->qa->menu->automation['subMenu']->environment = array('link' => '环境|automation|environment|productID=%s', 'alias' => '');

$lang->qa->dividerMenu = ',bug,testtask,caselib,';

/* DevOps menu. */
$lang->devops->menu = new stdclass();
$lang->devops->menu->code    = array('link' => "{$lang->repo->common}|repo|browse|repoID=%s", 'alias' => 'diff,view,revision,log,blame,showsynccommit');
$lang->devops->menu->mr      = array('link' => "{$lang->devops->mr}|mr|browse|repoID=%s");
$lang->devops->menu->compile = array('link' => "{$lang->devops->compile}|job|browse|repoID=%s", 'subModule' => 'compile,job');
$lang->devops->menu->app     = array('link' => "{$lang->app->common}|app|serverlink|%s");
$lang->devops->menu->set     = array('link' => "{$lang->devops->set}|repo|maintain", 'subModule' => 'gitlab,jenkins,sonarqube,gitea,gogs', 'alias' => 'setrules,create,edit');

$lang->devops->menuOrder[5]  = 'code';
$lang->devops->menuOrder[10] = 'mr';
$lang->devops->menuOrder[15] = 'compile';
$lang->devops->menuOrder[20] = 'app';
$lang->devops->menuOrder[25] = 'set';

$lang->devops->dividerMenu = ',set,';

$lang->devops->menu->set['subMenu'] = new stdclass();
$lang->devops->menu->set['subMenu']->repo      = array('link' => "{$lang->devops->repo}|repo|maintain", 'alias' => 'create,edit');
$lang->devops->menu->set['subMenu']->gitlab    = array('link' => 'GitLab|gitlab|browse', 'subModule' => 'gitlab');
$lang->devops->menu->set['subMenu']->gogs      = array('link' => 'Gogs|gogs|browse', 'subModule' => 'gogs');
$lang->devops->menu->set['subMenu']->gitea     = array('link' => 'Gitea|gitea|browse', 'subModule' => 'gitea');
$lang->devops->menu->set['subMenu']->jenkins   = array('link' => 'Jenkins|jenkins|browse', 'subModule' => '');
$lang->devops->menu->set['subMenu']->sonarqube = array('link' => 'SonarQube|sonarqube|browse', 'subModule' => 'sonarqube');
$lang->devops->menu->set['subMenu']->setrules  = array('link' => "{$lang->devops->rules}|repo|setrules");

/* Kanban menu. */
$lang->kanban->menu = new stdclass();

/* Doc menu. */
$lang->doc->menu = new stdclass();
$lang->doc->menu->dashboard = array('link' => "{$lang->dashboard}|doc|index");
$lang->doc->menu->recent    = array('link' => "{$lang->doc->recent}|doc|browse|browseTyp=byediteddate", 'alias' => 'recent');
$lang->doc->menu->my        = array('link' => "{$lang->doc->my}|doc|browse|browseTyp=openedbyme", 'alias' => 'my');
$lang->doc->menu->collect   = array('link' => "{$lang->doc->favorite}|doc|browse|browseTyp=collectedbyme", 'alias' => 'collect');
$lang->doc->menu->product   = array('link' => "{$lang->doc->product}|doc|tableContents|type=product", 'alias' => 'showfiles,product');
$lang->doc->menu->api       = array('link' => "{$lang->doc->api}|api|index", 'alias' => 'api');
$lang->doc->menu->project   = array('link' => "{$lang->doc->project}|doc|tableContents|type=project", 'alias' => 'showfiles,project');
$lang->doc->menu->execution = array('link' => "{$lang->doc->execution}|doc|tableContents|type=execution", 'alias' => 'showfiles,execution');
$lang->doc->menu->custom    = array('link' => "{$lang->doc->custom}|doc|tableContents|type=custom", 'alias' => 'custom');

$lang->doc->dividerMenu = ',product,';

/* Doc menu order. */
$lang->doc->menuOrder[5]  = 'dashboard';
$lang->doc->menuOrder[10] = 'recent';
$lang->doc->menuOrder[15] = 'my';
$lang->doc->menuOrder[20] = 'collect';
$lang->doc->menuOrder[25] = 'product';
$lang->doc->menuOrder[30] = 'project';
$lang->doc->menuOrder[35] = 'execution';
$lang->doc->menuOrder[36] = 'api';
$lang->doc->menuOrder[40] = 'custom';

$lang->doc->menu->product['subMenu']   = new stdclass();
$lang->doc->menu->project['subMenu']   = new stdclass();
$lang->doc->menu->execution['subMenu'] = new stdclass();
$lang->doc->menu->custom['subMenu']    = new stdclass();

$lang->doc->menu->api['subMenu'] = new stdclass();
$lang->doc->menu->api['subMenu']->index  = array('link' => "{$lang->doc->apiDoc}|api|index|libID=%s", 'alias' => 'create,edit');
$lang->doc->menu->api['subMenu']->struct = array('link' => "{$lang->doc->apiStruct}|api|struct|libID=%s", 'alias' => 'createstruct,editstruct');

/* Report menu.*/
$lang->report->menu             = new stdclass();
$lang->report->menu->annual     = array('link' => "{$lang->report->annual}|report|annualData|year=&dept=&userID=" . (isset($_SESSION['user']) ? zget($_SESSION['user'], 'id', 0) : 0), 'target' => '_blank');
$lang->report->menu->pivotTable = array('link' => "{$lang->report->pivotTable}|report|productsummary", 'alias' => 'projectdeviation,bugcreate,workload,bugassign');

/* Report menu order. */
$lang->report->menuOrder[5]  = 'annual';
$lang->report->menuOrder[10] = 'pivotTable';

$lang->report->menu->pivotTable['subMenu']       = new stdclass();
$lang->report->menu->pivotTable['subMenu']->product = array('link' => "{$lang->product->common}|report|productsummary");
$lang->report->menu->pivotTable['subMenu']->project = array('link' => "{$lang->project->common}|report|projectdeviation");
$lang->report->menu->pivotTable['subMenu']->test    = array('link' => "{$lang->qa->common}|report|bugcreate", 'alias' => 'bugassign');
$lang->report->menu->pivotTable['subMenu']->staff   = array('link' => "{$lang->system->common}|report|workload");

$lang->report->menu->pivotTable['menuOrder'][5]  = 'product';
$lang->report->menu->pivotTable['menuOrder'][10] = 'project';
$lang->report->menu->pivotTable['menuOrder'][15] = 'test';
$lang->report->menu->pivotTable['menuOrder'][20] = 'staff';

/* Company menu.*/
$lang->company->menu              = new stdclass();
$lang->company->menu->browseUser  = array('link' => "{$lang->user->common}|company|browse", 'subModule' => ',user,');
$lang->company->menu->dept        = array('link' => "{$lang->dept->common}|dept|browse", 'subModule' => 'dept');
$lang->company->menu->browseGroup = array('link' => "$lang->priv|group|browse", 'subModule' => 'group');

/* Company menu order. */
$lang->company->menuOrder[5]  = 'browseUser';
$lang->company->menuOrder[10] = 'dept';
$lang->company->menuOrder[15] = 'browseGroup';
$lang->company->menuOrder[20] = 'addGroup';
$lang->company->menuOrder[25] = 'batchAddUser';
$lang->company->menuOrder[30] = 'addUser';

/* Admin menu. */
$lang->admin->menu            = new stdclass();
$lang->admin->menu->index     = array('link' => "$lang->indexPage|admin|index", 'alias' => 'register,certifytemail,certifyztmobile,ztcompany');
$lang->admin->menu->company   = array('link' => "{$lang->personnel->common}|company|browse|", 'subModule' => ',user,dept,group,');
$lang->admin->menu->model     = array('link' => "$lang->model|custom|browsestoryconcept|", 'class' => 'dropdown dropdown-hover', 'exclude' => 'custom-index,custom-set,custom-product,custom-execution,custom-kanban,custom-required,custom-flow,custom-score,custom-feedback,custom-timezone,custom-mode');
$lang->admin->menu->custom    = array('link' => "{$lang->custom->common}|custom|index", 'exclude' => 'custom-browsestoryconcept,custom-timezone,custom-estimate,custom-code,custom-mode');
$lang->admin->menu->extension = array('link' => "{$lang->extension->common}|extension|browse", 'subModule' => 'extension');
$lang->admin->menu->dev       = array('link' => "$lang->redev|dev|api", 'alias' => 'db', 'subModule' => 'dev,editor,entry');
$lang->admin->menu->message   = array('link' => "{$lang->message->common}|message|index", 'subModule' => 'message,mail,webhook');
$lang->admin->menu->system    = array('link' => "{$lang->admin->system}|custom|mode", 'subModule' => 'cron,backup,action,admin,search,convert', 'exclude' => 'admin-index,admin-xuanxuan,admin-register,admin-ztcompany');

$lang->admin->menu->model['dropMenu'] = new stdclass();
$lang->admin->menu->model['dropMenu']->allModel = array('link' => "{$lang->globalSetting}|custom|browsestoryconcept|", 'subModule' => 'measurement,report,sqlbuilder,subject,custom,meetingroom,baseline');

if($config->edition == 'max') $lang->admin->menu->model['dropMenu']->scrum = array('link' => "{$lang->scrumModel}|auditcl|scrumbrowse|processID=0&browseType=scrum", 'subModule' => 'auditcl,process,activity,zoutput,classify,');
$lang->admin->menu->model['dropMenu']->waterfall = array('link' => "{$lang->waterfallModel}|stage|setType|", 'subModule' => 'stage,auditcl,cmcl,process,activity,zoutput,classify,reviewcl,reviewsetting,design');

$lang->admin->menu->allModel['subMenu'] = new stdclass();
$lang->admin->menu->allModel['subMenu']->storyConcept = array('link' => "{$lang->storyConcept}|custom|browsestoryconcept|");
$lang->admin->menu->allModel['subMenu']->code         = array('link' => "{$lang->code}|custom|code|");

$lang->admin->menu->allModel['menuOrder'][5]  = 'storyConcept';
$lang->admin->menu->allModel['menuOrder'][30] = 'code';

$lang->admin->menu->waterfall['subMenu'] = new stdclass();
$lang->admin->menu->waterfall['subMenu']->stage = array('link' => "{$lang->stage->common}|stage|setType|", 'subModule' => 'stage');

/* Admin menu order. */
$lang->admin->menuOrder[5]  = 'index';
$lang->admin->menuOrder[10] = 'company';
$lang->admin->menuOrder[15] = 'model';
$lang->admin->menuOrder[20] = 'custom';
$lang->admin->menuOrder[25] = 'message';
$lang->admin->menuOrder[30] = 'extension';
$lang->admin->menuOrder[35] = 'dev';
$lang->admin->menuOrder[40] = 'system';

$lang->admin->menu->message['subMenu']          = new stdclass();
$lang->admin->menu->message['subMenu']->message = array();
$lang->admin->menu->message['subMenu']->mail    = array('link' => "{$lang->mail->common}|mail|index", 'subModule' => 'mail');
$lang->admin->menu->message['subMenu']->webhook = array('link' => "Webhook|webhook|browse", 'subModule' => 'webhook');
$lang->admin->menu->message['subMenu']->browser = array('link' => "$lang->browser|message|browser");
$lang->admin->menu->message['subMenu']->setting = array('link' => "$lang->settings|message|setting");

$lang->admin->menu->message['menuOrder'][5]  = 'mail';
$lang->admin->menu->message['menuOrder'][10] = 'webhook';
$lang->admin->menu->message['menuOrder'][15] = 'browser';
$lang->admin->menu->message['menuOrder'][20] = 'setting';

$lang->admin->menu->company['subMenu']              = new stdclass();
$lang->admin->menu->company['subMenu']->browseUser  = array('link' => "{$lang->user->common}|company|browse", 'subModule' => 'user');
$lang->admin->menu->company['subMenu']->dept        = array('link' => "{$lang->dept->common}|dept|browse", 'subModule' => 'dept');
$lang->admin->menu->company['subMenu']->browseGroup = array('link' => "{$lang->priv}|group|browse", 'subModule' => 'group');

$lang->admin->menu->dev['subMenu']         = new stdclass();
$lang->admin->menu->dev['subMenu']->api    = array('link' => "API|dev|api");
$lang->admin->menu->dev['subMenu']->db     = array('link' => "$lang->db|dev|db");
$lang->admin->menu->dev['subMenu']->editor = array('link' => "$lang->editor|dev|editor");
$lang->admin->menu->dev['subMenu']->entry  = array('link' => "{$lang->admin->entry}|entry|browse", 'subModule' => 'entry');

$lang->admin->menu->dev['menuOrder'][5]  = 'api';
$lang->admin->menu->dev['menuOrder'][10] = 'db';
$lang->admin->menu->dev['menuOrder'][15] = 'editor';
$lang->admin->menu->dev['menuOrder'][20] = 'entry';

$lang->admin->menu->system['subMenu']              = new stdclass();
$lang->admin->menu->system['subMenu']->mode        = array('link' => "{$lang->custom->mode}|custom|mode");
$lang->admin->menu->system['subMenu']->setModule   = array('link' => "{$lang->admin->module}|admin|setmodule");
$lang->admin->menu->system['subMenu']->backup      = array('link' => "{$lang->backup->common}|backup|index");
$lang->admin->menu->system['subMenu']->trash       = array('link' => "{$lang->action->trash}|action|trash");
$lang->admin->menu->system['subMenu']->safe        = array('link' => "$lang->security|admin|safe", 'alias' => 'checkweak,resetpwdsetting');
$lang->admin->menu->system['subMenu']->cron        = array('link' => "{$lang->admin->cron}|cron|index", 'subModule' => 'cron');
$lang->admin->menu->system['subMenu']->timezone    = array('link' => "$lang->timezone|custom|timezone");
$lang->admin->menu->system['subMenu']->buildIndex  = array('link' => "{$lang->admin->buildIndex}|search|buildindex|");
$lang->admin->menu->system['subMenu']->tableEngine = array('link' => "{$lang->admin->tableEngine}|admin|tableengine|");
if(version_compare(phpversion(), 5.6) > 0) $lang->admin->menu->system['subMenu']->convertJira = array('link' => "{$lang->convert->importJira}|convert|convertjira|", 'subModule' => 'convert');

$lang->admin->dividerMenu = ',company,message,system,';

$lang->subject->menu               = new stdclass();
$lang->subject->menu->storyConcept = array('link' => "{$lang->storyConcept}|custom|browsestoryconcept|");

/* System menu. */
$lang->system->menu          = new stdclass();
$lang->system->menu->team    = array('link' => "{$lang->team->common}|my|team|", 'subModule' => 'user');
$lang->system->menu->dynamic = array('link' => "$lang->dynamic|company|dynamic|");
$lang->system->menu->view    = array('link' => "{$lang->company->common}|company|view");

/* System menu order. */
$lang->system->menuOrder[5]  = 'team';
$lang->system->menuOrder[10] = 'calendar';
$lang->system->menuOrder[15] = 'dynamic';
$lang->system->menuOrder[20] = 'view';

/* Nav group.*/
$lang->navGroup         = new stdclass();
$lang->navGroup->my     = 'my';
$lang->navGroup->effort = 'my';
$lang->navGroup->score  = 'my';
$lang->navGroup->todo   = 'my';

$lang->navGroup->program   = 'program';
$lang->navGroup->personnel = 'program';

$lang->navGroup->product     = 'product';
$lang->navGroup->productplan = 'product';
$lang->navGroup->release     = 'product';
$lang->navGroup->branch      = 'product';
$lang->navGroup->story       = 'product';
$lang->navGroup->requirement = 'product';

$lang->navGroup->project     = 'project';
$lang->navGroup->deploy      = 'project';
$lang->navGroup->programplan = 'project';
$lang->navGroup->design      = 'project';
$lang->navGroup->stakeholder = 'project';

$lang->navGroup->projectbuild   = 'project';
$lang->navGroup->projectstory   = 'project';
$lang->navGroup->projectplan    = 'project';
$lang->navGroup->review         = 'project';
$lang->navGroup->reviewissue    = 'project';
$lang->navGroup->pssp           = 'project';
$lang->navGroup->auditplan      = 'project';
$lang->navGroup->cm             = 'project';
$lang->navGroup->nc             = 'project';
$lang->navGroup->projectrelease = 'project';
$lang->navGroup->build          = 'project';
$lang->navGroup->measrecord     = 'project';
$lang->navGroup->milestone      = 'project';

$lang->navGroup->execution    = 'execution';
$lang->navGroup->task         = 'execution';
$lang->navGroup->build        = 'execution';
$lang->navGroup->team         = 'execution';

$lang->navGroup->kanbanspace  = 'kanban';
$lang->navGroup->kanban       = 'kanban';
$lang->navGroup->kanbanregion = 'kanban';
$lang->navGroup->kanbanlane   = 'kanban';
$lang->navGroup->kanbancolumn = 'kanban';
$lang->navGroup->kanbancard   = 'kanban';

$lang->navGroup->doc    = 'doc';
$lang->navGroup->doclib = 'doc';
$lang->navGroup->api    = 'doc';

$lang->navGroup->report = 'report';

$lang->navGroup->qa         = 'qa';
$lang->navGroup->bug        = 'qa';
$lang->navGroup->testcase   = 'qa';
$lang->navGroup->testtask   = 'qa';
$lang->navGroup->automation = 'qa';
$lang->navGroup->testreport = 'qa';
$lang->navGroup->testcase   = 'qa';
$lang->navGroup->testtask   = 'qa';
$lang->navGroup->testsuite  = 'qa';
$lang->navGroup->caselib    = 'qa';

$lang->navGroup->devops           = 'devops';
$lang->navGroup->repo             = 'devops';
$lang->navGroup->job              = 'devops';
$lang->navGroup->jenkins          = 'devops';
$lang->navGroup->mr               = 'devops';
$lang->navGroup->gitlab           = 'devops';
$lang->navGroup->gogs             = 'devops';
$lang->navGroup->gitea            = 'devops';
$lang->navGroup->sonarqube        = 'devops';
$lang->navGroup->sonarqubeproject = 'devops';
$lang->navGroup->compile          = 'devops';
$lang->navGroup->ci               = 'devops';
$lang->navGroup->svn              = 'devops';
$lang->navGroup->git              = 'devops';
$lang->navGroup->app              = 'devops';

$lang->navGroup->company = 'system';

$lang->navGroup->attend   = 'attend';
$lang->navGroup->leave    = 'attend';
$lang->navGroup->makeup   = 'attend';
$lang->navGroup->overtime = 'attend';
$lang->navGroup->lieu     = 'attend';

$lang->navGroup->admin     = 'admin';
$lang->navGroup->dept      = 'admin';
$lang->navGroup->user      = 'admin';
$lang->navGroup->group     = 'admin';
$lang->navGroup->dept      = 'admin';
$lang->navGroup->webhook   = 'admin';
$lang->navGroup->sms       = 'admin';
$lang->navGroup->message   = 'admin';
$lang->navGroup->custom    = 'admin';
$lang->navGroup->cron      = 'admin';
$lang->navGroup->backup    = 'admin';
$lang->navGroup->mail      = 'admin';
$lang->navGroup->dev       = 'admin';
$lang->navGroup->entry     = 'admin';
$lang->navGroup->extension = 'admin';
$lang->navGroup->action    = 'admin';
$lang->navGroup->convert   = 'admin';
$lang->navGroup->stage     = 'admin';

$lang->navGroup->search  = 'search';
$lang->navGroup->index   = 'index';
$lang->navGroup->tree    = 'tree';
$lang->navGroup->misc    = 'misc';
$lang->navGroup->upgrade = 'upgrade';

if(!$config->URAndSR) unset($lang->product->menu->requirement, $lang->product->menuOrder[35]);
if(!helper::hasFeature('product_roadmap')) unset($lang->product->menu->roadmap, $lang->product->menuOrder[30]);
if(!helper::hasFeature('product_track'))
{
    unset($lang->product->menu->track, $lang->product->menuOrder[40]);
    $lang->product->dividerMenu = str_replace(',track,', ',doc,', $lang->product->dividerMenu);
}
if(!helper::hasFeature('waterfall')) unset($lang->admin->menu->model['dropMenu']->waterfall);

if(!helper::hasFeature('devops'))
{
    unset($lang->mainNav->devops,         $lang->mainNav->menuOrder[35]);
    unset($lang->scrum->menu->devops,     $lang->scrum->menuOrder[25]);
    unset($lang->waterfall->menu->devops, $lang->waterfall->menuOrder[35]);
    unset($lang->execution->menu->devops, $lang->execution->menuOrder[45]);
}

if(!helper::hasFeature('kanban'))
{
    unset($lang->mainNav->kanban, $lang->mainNav->menuOrder[40]);
    $lang->dividerMenu = str_replace(',kanban,' , ',doc,', $lang->dividerMenu);
}

if($config->edition == 'max' and !helper::hasFeature('scrum_auditplan'))
{
    if(!helper::hasFeature('scrum_process'))
    {
        $lang->admin->menu->model['dropMenu']->scrum = array('link' => "{$lang->scrumModel}|process|scrumbrowse|processID=0&browseType=scrum", 'subModule' => 'auditcl,process,activity,zoutput,classify,');
    }
    else
    {
        unset($lang->admin->menu->model['dropMenu']->scrum);
    }
}

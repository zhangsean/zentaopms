<?php
$config->program = new stdclass();
$config->program->suffix['c']    = "cpp";
$config->program->suffix['cpp']  = "cpp";
$config->program->suffix['asp']  = "asp";
$config->program->suffix['php']  = "php";
$config->program->suffix['cs']   =  "cs";
$config->program->suffix['sh']   = "bash";
$config->program->suffix['jsp']  = "java";
$config->program->suffix['lua']  = "lua";
$config->program->suffix['sql']  = "sql";
$config->program->suffix['js']   = "javascript";
$config->program->suffix['ini']  = "ini";
$config->program->suffix['conf'] = "apache";
$config->program->suffix['bat']  = "dos";
$config->program->suffix['py']   = "python";
$config->program->suffix['rb']   = "ruby";
$config->program->suffix['as']   = "actionscript";
$config->program->suffix['html'] = "xml";
$config->program->suffix['xml']  = "xml";
$config->program->suffix['htm']  = "xml";
$config->program->suffix['pl']   = "perl";

$config->repo->cacheTime = 10;
$config->repo->syncTime  = 10;
$config->repo->batchNum  = 100;
$config->repo->images    = '|png|gif|jpg|ico|jpeg|bmp|';
$config->repo->binary    = '|pdf|';

$config->repo->editor = new stdclass();
$config->repo->editor->create = array('id' => 'desc', 'tools' => 'simpleTools');
$config->repo->editor->edit   = array('id' => 'desc', 'tools' => 'simpleTools');
$config->repo->editor->view   = array('id' => 'commentText', 'tools' => 'simpleTools');
$config->repo->editor->diff   = array('id' => 'commentText', 'tools' => 'simpleTools');

$config->repo->switcherModuleList = array('repo', 'job', 'compile', 'mr');
$config->repo->switcherMethodList = array('browse', 'review', 'view', 'diff', 'log', 'revision', 'blame');

$config->repo->create = new stdclass();
$config->repo->create->requiredFields = 'product,SCM,name,encoding';

$config->repo->edit = new stdclass();
$config->repo->edit->requiredFields = 'product,SCM,name,encoding';

$config->repo->svn = new stdclass();
$config->repo->svn->requiredFields = 'account,password';

$config->repo->gitlab = new stdclass;
$config->repo->gitlab->perPage = 300;
$config->repo->gitlab->apiPath = "%s/api/v4/projects/%s/repository/";

$config->repo->gitea = new stdclass;
$config->repo->gitea->apiPath = "%s/api/v1/repos/%s/";

$config->repo->gitServiceList = array('gitlab', 'gitea', 'gogs');
$config->repo->gitTypeList    = array('Gitlab', 'Gitea', 'Gogs', 'Git');

$config->repo->rules['module']['task']     = 'Task';
$config->repo->rules['module']['bug']      = 'Bug';
$config->repo->rules['module']['story']    = 'Story';
$config->repo->rules['task']['start']      = 'Start';
$config->repo->rules['task']['finish']     = 'Finish';
$config->repo->rules['task']['logEfforts'] = 'Effort';
$config->repo->rules['task']['consumed']   = 'Cost';
$config->repo->rules['task']['left']       = 'Left';
$config->repo->rules['bug']['resolve']     = 'Fix';
$config->repo->rules['id']['mark']         = '#';
$config->repo->rules['id']['split']        = ',';
$config->repo->rules['mark']['consumed']   = ':';
$config->repo->rules['mark']['left']       = ':';
$config->repo->rules['unit']['consumed']   = 'h';
$config->repo->rules['unit']['left']       = 'h';

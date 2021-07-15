<?php
/**
 * The upgrade module English file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license  ZPL (http://zpl.pub/page/zplv12.html)
 * @author   Nguyễn Quốc Nho <quocnho@gmail.com>
 * @package  upgrade
 * @version  $Id: vi.php 5119 2013-07-12 08:06:42Z wyd621@gmail.com $
 * @link  http://www.zentao.net
 */
$lang->upgrade->common          = 'Cập nhật';
$lang->upgrade->start           = 'Start';
$lang->upgrade->result          = 'Kết quả';
$lang->upgrade->fail            = 'Thất bại';
$lang->upgrade->successTip      = 'Erfolgreich';
$lang->upgrade->success         = "<p><i class='icon icon-check-circle'></i></p><p>Chúc mừng!</p><p> ZenTao của bạn đã được cập nhật.</p>";
$lang->upgrade->tohome          = 'Visit ZenTao';
$lang->upgrade->license         = 'ZenTao is under Z PUBLIC LICENSE(ZPL) 1.2.';
$lang->upgrade->warnning        = 'Cảnh báo';
$lang->upgrade->checkExtension  = 'Kiểm tra Extensions';
$lang->upgrade->consistency     = 'Kiểm tra tính nhất quán';
$lang->upgrade->warnningContent = <<<EOT
<p>Vui lòng sao lưu cơ sở dữ liệu của bạn trước khi cập nhật ZenTao!</p>
<pre>
1. Sử dụng phpMyAdmin để sao lưu.
2. Sử dụng mysqlCommand để sao lưu.
   $> mysqldump -u <span class='text-danger'>username</span> -p <span class='text-danger'>dbname</span> > <span class='text-danger'>filename</span>
   Thay đổi văn bản màu đỏ thành tên người dùng và cơ sở dữ liệu tương ứng.
   ví dụ:  mysqldump -u root -p zentao >zentao.bak
</pre>
EOT;
$lang->upgrade->createFileWinCMD   = 'Mở dòng lệnh và xử lý <strong style="color:#ed980f">echo > %s</strong>';
$lang->upgrade->createFileLinuxCMD = 'Xử lý dòng lệnh: <strong style="color:#ed980f">touch %s</strong>';
$lang->upgrade->setStatusFile      = '<h4>Please complete the following actions</h4>
           <ul style="line-height:1.5;font-size:13px;">
           <li>%s</li>
           <li>Or xóa "<strong style="color:#ed980f">%s</strong>" and create <strong style="color:#ed980f">ok.txt</strong> and leave it blank.</li>
           </ul>
           <p><strong style="color:red">I have read and done as instructed above. <a href="upgrade.php">Continue upgrading.</a></strong></p>';

$lang->upgrade->selectVersion = 'Phiên bản';
$lang->upgrade->continue      = 'Tiếp tục';
$lang->upgrade->noteVersion   = "Chọn the compatible version, or it might cause data loss.";
$lang->upgrade->fromVersion   = 'Từ';
$lang->upgrade->toVersion     = 'Tới';
$lang->upgrade->confirm       = 'Xác nhận SQL';
$lang->upgrade->sureExecute   = 'Xử lý';
$lang->upgrade->forbiddenExt  = 'The extension is incompatible with the version. It has been deactivated:';
$lang->upgrade->updateFile    = 'File information has to be updated.';
$lang->upgrade->noticeSQL     = 'Your database is inconsistent with the standard and it failed to fix it. Vui lòng run the following SQL and refresh.';
$lang->upgrade->afterDeleted  = 'File không là deleted. Vui lòng refresh after you xóa it.';
$lang->upgrade->mergeProgram  = 'Data Merge';
$lang->upgrade->mergeTips     = 'Data Migration Tips';
$lang->upgrade->toPMS15Guide  = 'ZenTao open source version 15.0.beta1 upgrade';
$lang->upgrade->toPRO10Guide  = 'ZenTao profession version 10.0.rc1 upgrade';
$lang->upgrade->toBIZ5Guide   = 'ZenTao enterprise version 5.0.rc1 upgrade';
$lang->upgrade->to15Desc      = <<<EOD
<p>Dear users, ZenTao has made adjustments to navigation and concepts since version 15. The main changes are as follows:</p>
<ol>
<p><li>Added the concept of program. A program set can include multiple products and multiple projects.</li></p>
<p><li>Subdivided the concept of project and iteration, a project can contain multiple iterations.</li></p>
<p><li>The navigation adds a left menu and supports multi-page operations.</li></p>
</ol>
<br/>
<p>You can experience the latest version of the function online to decide whether to enable the mode: <a class='text-info' href='http://zentaomax.demo.zentao.net' target='_blank'>Demo</a></p>
</br>
<p><strong>How do you plan to use the new version of ZenTao?</strong></p>
EOD;

$lang->upgrade->to15Mode['classic'] = 'Keep the old version';
$lang->upgrade->to15Mode['new']     = 'New program management mode';

$lang->upgrade->selectedModeTips['classic'] = 'You can also switch to the new program set management mode in the background-Customize in the future.';
$lang->upgrade->selectedModeTips['new']     = 'Switching to the program management mode requires merging the previous data, and the system will guide you to complete this operation.';

$lang->upgrade->demoURL       = 'http://zentao20.demo.zentao.net';
$lang->upgrade->videoURL      = 'https://qc.zentao.net/zentao20.mp4';
$lang->upgrade->to20Tips      = 'Zentao 20 upgrade tips';
$lang->upgrade->to20Button    = 'I have done the backup, start the upgrade!！';
$lang->upgrade->to20TipsHeader= "<p>Dear user, thank you for your support of ZenTao。Since version 20, Zendo has been fully upgraded to a universal project management platform. Please see the following video for more information：</p><br />";
$lang->upgrade->to20Desc      = <<<EOD
<div class='text-warning'>
  <p>Friendly reminder：</p>
  <ol>
    <li>You can start by installing a version 20 of ZenTao to experience the concepts and processes inside.</li>
    <li>Version 20 of Zendo has made some major changes, please make a backup before upgrading.</li>
    <li>Please feel free to upgrade, even if the first upgrade is not in place, subsequent adjustments can be made without affecting system data.</li>
  </ol>
</div>
EOD;
$lang->upgrade->mergeProgramDesc = <<<EOD
<p>Next, ZenTao will migrate the existing data of {$lang->productCommon} and {$lang->projectCommon} to Program and Project. It will be one of the followings:</p><br />
<h4>1. Manage {$lang->productCommon} and {$lang->projectCommon} by {$lang->productCommon} Line </h4>
<p>Migrate the data of {$lang->productCommon} and {$lang->projectCommon} by {$lang->productCommon} Line to a Program. You can also migrate it separately.</p>
<h4>2. Manage {$lang->projectCommon} by {$lang->productCommon}</h4>
<p>You can migrate the data of several {$lang->productCommon}s and {$lang->projectCommon}s to one Program. Or select {$lang->projectCommon}s of a {$lang->productCommon} and {$lang->productCommon} to a Program.</p>
<h4>2. Independent {$lang->projectCommon}</h4>
<p>Choose several {$lang->projectCommon}s and migrate them to one Program, or migrate them separately.</p>
<h4>4. Link {$lang->projectCommon}s of several {$lang->productCommon}s</h4>
<p>You can set {$lang->projectCommon}s as one new project.</p>
EOD;

$lang->upgrade->line          = 'Product Line';
$lang->upgrade->allLines      = 'All Lines';
$lang->upgrade->program       = 'Merge Project';
$lang->upgrade->existProgram  = 'Existing programs';
$lang->upgrade->existProject  = 'Existing projects';
$lang->upgrade->existLine     = 'Existing' . $lang->productCommon . ' lines';
$lang->upgrade->product       = $lang->productCommon;
$lang->upgrade->project       = 'Iteration';
$lang->upgrade->repo          = 'Repo';
$lang->upgrade->mergeRepo     = 'Merge Repo';
$lang->upgrade->setProgram    = 'Set the project to which the program belongs';
$lang->upgrade->dataMethod    = 'Data migration method';
$lang->upgrade->begin         = 'Begin Date';
$lang->upgrade->end           = 'End Date';
$lang->upgrade->selectProject = 'The target project';
$lang->upgrade->projectName   = 'Project Name';

$lang->upgrade->newProgram         = 'Create';
$lang->upgrade->projectEmpty       = 'Project must be not empty.';
$lang->upgrade->mergeSummary       = "Dear users, there are %s {$lang->productCommon} and %s {$lang->projectCommon} in your system waiting for Migration. By System Calculation, we recommend your migration plan as follows, you can also adjust according to your own situation:";
$lang->upgrade->mergeByProductLine = "PRODUCTLINE-BASED {$lang->projectCommon}: Consolidate the entire {$lang->productCommon} line and the {$lang->productCommon} and {$lang->projectCommon} below it into one large project.";
$lang->upgrade->mergeByProduct     = "PRODUCT-BASED {$lang->projectCommon}: You can select multiple {$lang->productCommon} and their lower {$lang->projectCommon} to merge into a large project, or you can select a {$lang->productCommon} to merge its lower {$lang->projectCommon} into a larger project";
$lang->upgrade->mergeByProject     = "Independent {$lang->projectCommon}: You can select several {$lang->projectCommon} and merge them into one large project, or merge them independently";
$lang->upgrade->mergeByMoreLink    = "{$lang->projectCommon} that relates multiple {$lang->productCommon}: select which project the {$lang->projectCommon} belongs to.";
$lang->upgrade->mergeRepoTips      = "Merge the selected version library under the selected product.";
$lang->upgrade->needBuild4Add      = 'Full text retrieval has been added in this upgrad. Vui lòng create an index.';

$lang->upgrade->projectType['project']   = "Upgrade the historical {$lang->projectCommon} as a project";
$lang->upgrade->projectType['execution'] = "Upgrade the historical {$lang->projectCommon} as an execution";

$lang->upgrade->createProjectTip = <<<EOT
<p>After the upgrade, the existing {$lang->projectCommon} will be Project in the new version.</p>
<p>ZenTao will create an item in Execute with the same name of {$lang->projectCommon} according to the data in {$lang->projectCommon}, and move the tasks, stories, and bugs in {$lang->projectCommon} to it.</p>
EOT;
$lang->upgrade->createExecutionTip = <<<EOT
<p>ZenTao will upgrade existing {$lang->projectCommon} as execution.</p>
<p>After the upgrade, the data of existing {$lang->projectCommon} will be in a Project - Execute of the new version .</p>
EOT;

include dirname(__FILE__) . '/version.php';

<?php
/**
 * The browse view file of compile module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2017 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chenqi <chenqi@cnezsoft.com>
 * @package     compile
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php
    echo html::a($this->createLink('integration', 'browse'), "<span class='text'>{$lang->ci->plan}</span>", '', "class='btn btn-link'");
    echo html::a($this->createLink('compile', 'browse'), "<span class='text'>{$lang->ci->history}</span>", '', "class='btn btn-link btn-active-text'");
    ?>
  </div>
</div>

<div id='mainContent'>
  <form class='main-table' id='ajaxForm' method='post'>
    <table id='buildList' class='table has-sort-head table-fixed'>
      <thead>
        <tr class='text-center'>
          <?php $vars = "integrationID={$integrationID}&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";?>
          <th class='w-60px'><?php common::printOrderLink('id', $orderBy, $vars, $lang->compile->id);?></th>
          <th class='text-left'><?php common::printOrderLink('name', $orderBy, $vars, $lang->compile->name);?></th>
          <th class='text-left'><?php echo $lang->integration->repo;?></th>
          <th class='w-250px text-left'><?php echo $lang->integration->jenkins;?></th>
          <th class='w-200px text-left'><?php echo $lang->integration->triggerType;?></th>
          <th class='w-80px text-left'><?php common::printOrderLink('status', $orderBy, $vars, $lang->compile->status);?></th>
          <th class='w-130px'><?php common::printOrderLink('createdDate', $orderBy, $vars, $lang->compile->time);?></th>
          <th class='c-actions-1'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($buildList as $id => $build):?>
        <tr>
          <td class='text-center'><?php echo $id;?></td>
          <td class='text' title='<?php echo $build->name;?>'><?php echo $build->name;?></td>
          <td class='text' title='<?php echo $build->repoName;?>'><?php echo $build->repoName;?></td>
          <td class='text' title='<?php echo $build->jenkinsName;?>'><?php echo urldecode($build->jkJob) . "@{$build->jenkinsName}";?></td>
          <?php
          $triggerType = zget($lang->integration->triggerTypeList, $build->triggerType);
          if($build->triggerType == 'tag' and !empty($build->svnDir)) $triggerType = $lang->integration->dirChange;

          $triggerConfig = '';
          if($build->triggerType == 'commit')
          {
              $triggerConfig = "({$build->comment})";
          }
          elseif($build->triggerType == 'schedule')
          {
              $atDay = '';
              foreach(explode(',', $build->atDay) as $day) $atDay .= zget($lang->datepicker->dayNames, trim($day), '') . ',';
              $atDay = trim($atDay, ',');

              $triggerConfig = "({$atDay}, {$build->atTime})";
          }
          ?>
          <td class='text' title='<?php echo $triggerType . $triggerConfig;?>'><?php echo $triggerType . $triggerConfig;?></td>
          <?php $buildStatus = zget($lang->compile->statusList, $build->status);?>
          <td class='text' title='<?php echo $buildStatus;?>'><?php echo $buildStatus;?></td>
          <td class='text' title='<?php echo $build->createdDate;?>'><?php echo $build->createdDate;?></td>
          <td class='c-actions text-center'>
            <?php common::printIcon('compile', 'logs', "buildID=$id", '', 'list', 'file-text', '', '', '', '', $lang->compile->logs);?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if($buildList):?>
    <div class='table-footer'><?php $pager->show('right', 'pagerjs');?></div>
    <?php endif; ?>
  </form>
</div>
<?php include '../../common/view/footer.html.php';?>

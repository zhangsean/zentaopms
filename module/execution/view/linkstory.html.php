<?php
/**
 * The link story view of execution module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     execution
 * @version     $Id: linkstory.html.php 4129 2013-01-18 01:58:14Z wwccss $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/tablesorter.html.php';?>
<style>
.search-form .form-actions {padding-bottom: 10px!important;}
</style>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <span class='btn btn-link btn-active-text'><span class='text'><?php echo $lang->execution->linkStory;?></span></span>
  </div>
  <?php if(!isonlybody()):?>
  <div class='btn-toolbar pull-right'>
    <?php echo html::a($browseLink, '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-primary'");?>
  </div>
  <?php endif;?>
</div>
<div id="mainContent">
  <div class="cell space-sm">
    <div id='queryBox' data-module='story' class='show no-margin'></div>
  </div>
  <form class='main-table table-story' method='post' data-ride='table' id='linkStoryForm'>
    <table class='table table-fixed tablesorter' id='linkStoryList'>
      <thead>
        <tr>
          <th class='c-id'>
            <?php if($allStories):?>
            <div class="checkbox-primary check-all tablesorter-noSort" title="<?php echo $lang->selectAll?>">
              <label></label>
            </div>
            <?php endif;?>
            <?php echo $lang->idAB;?>
          </th>
          <th class='c-pri' title=<?php echo $lang->execution->pri;?>><?php echo $lang->priAB;?></th>
          <th><?php echo $lang->story->title;?></th>
          <?php if($project->hasProduct):?>
          <th class='c-object'><?php echo $lang->story->product;?></th>
          <?php endif;?>
          <th class='c-module'><?php echo $lang->story->module;?></th>
          <th class='c-plan <?php if(empty($project->hasProduct) && $project->model != 'scrum') echo 'hide';?>'><?php echo $lang->story->plan;?></th>
          <th class='c-stage'><?php echo $lang->story->stage;?></th>
          <?php if($project->hasProduct && $productType != 'normal'):?>
          <th class='c-branch'><?php echo $lang->product->branchName[$productType];?></th>
          <?php endif;?>
          <th class='c-user'><?php echo $lang->openedByAB;?></th>
          <th class='c-estimate text-right'><?php echo $lang->story->estimateAB;?></th>
        </tr>
      </thead>
      <tbody>
      <?php $storyCount = 0;?>
      <?php foreach($allStories as $story):?>
      <?php $storyLink = $this->createLink('execution', 'storyView', "storyID=$story->id", '', true);?>
      <tr>
        <td class='cell-id'>
          <?php echo html::checkbox('stories', array($story->id => sprintf('%03d', $story->id)));?>
          <?php echo html::hidden("products[$story->id]", $story->product);?>
        </td>
        <td><span class='label-pri <?php echo 'label-pri-' . $story->pri;?>' title='<?php echo zget($lang->story->priList, $story->pri, $story->pri);?>'><?php echo zget($lang->story->priList, $story->pri, $story->pri);?></span></td>
        <td class='text-left nobr' title="<?php echo $story->title?>">
          <?php
          if($story->parent > 0) echo "<span class='label label-badge label-light'>{$lang->story->childrenAB}</span>";
          if(common::hasPriv('execution', 'storyView'))
          {
              echo html::a($storyLink, $story->title, '', "class='iframe' data-width='80%'");
          }
          else
          {
              echo '<a>' . $story->title . '</a>';
          }
          ?>
        </td>
        <?php if($project->hasProduct):?>
        <td class='text-left' title='<?php echo $products[$story->product]->name?>'><?php echo html::a($this->createLink('product', 'browse', "productID=$story->product&branch=$story->branch"), $products[$story->product]->name);?></td>
        <?php endif;?>
        <td class='c-module text-left' title='<?php echo zget($modules, $story->module, '')?>'><?php echo zget($modules, $story->module, '')?></td>
        <td class='text-ellipsis <?php if(empty($project->hasProduct) && $project->model != 'scrum') echo 'hide';?>' title='<?php echo $story->planTitle;?>'><?php echo $story->planTitle;?></td>
        <td><?php echo zget($lang->story->stageList, $story->stage);?></td>
        <?php if($project->hasProduct && $productType != 'normal'):?>
        <td><?php if(isset($branchGroups[$story->product][$story->branch])) echo $branchGroups[$story->product][$story->branch];?></td>
        <?php endif;?>
        <td class='c-user'><?php echo zget($users, $story->openedBy);?></td>
        <td class='text-right c-estimate' title="<?php echo $story->estimate . ' ' . $lang->hourCommon;?>"><?php echo $story->estimate . $config->hourUnit;?></td>
      </tr>
      <?php $storyCount++;?>
      <?php endforeach;?>
      </tbody>
    </table>
    <?php if($storyCount):?>
    <div class='table-footer'>
      <div class="checkbox-primary check-all"><label><?php echo $lang->selectAll?></label></div>
      <div class='table-actions btn-toolbar show-always'>
        <?php echo html::submitButton('', '', 'btn');?>
      </div>
      <?php $pager->show('right', 'pagerjs')?>
    </div>
    <?php else:?>
    <div class="table-empty-tip">
      <p><span class="text-muted"><?php echo $lang->{$app->rawModule}->whyNoStories;?></p>
    </div>
    <?php endif;?>
  </form>
</div>
<?php if(commonModel::isTutorialMode()): ?>
<style>
#linkStoryList .c-user,
#linkStoryList .c-estimate,
#linkStoryList .c-module {display: none;}
</style>
<?php endif; ?>
<?php include '../../common/view/footer.html.php';?>

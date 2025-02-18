<?php
/**
 * The batch close view of story module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     story
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php js::set('storyType', $storyType);?>
<?php js::set('app', $app->tab);?>
<div class='main-content' id='mainContent'>
  <div class='main-header'>
    <h2><?php echo ($storyType == 'story' ? $lang->SRCommon : $lang->URCommon) . $lang->colon . $lang->story->batchClose;?></h2>
  </div>
  <?php if(isset($suhosinInfo)):?>
  <div class='alert alert-info'><?php echo $suhosinInfo;?></div>
  <?php else:?>
  <form method='post' target='hiddenwin' action="<?php echo inLink('batchClose', "from=storyBatchClose")?>">
    <table class='table table-fixed table-form with-border'>
    <thead>
      <tr class='text-center'>
        <th class='c-id'><?php echo $lang->idAB;?></th>
        <th class='text-left'><?php echo $lang->story->title;?></th>
        <th class='c-status'><?php echo $lang->story->status;?></th>
        <th class='c-reason'><?php echo $lang->story->closedReason;?></th>
        <th id='duplicateStoryTitle' class='w-p15' style='display:none;'><?php echo $lang->story->duplicateStory;?></th>
        <th class='w-p30'><?php echo $lang->story->comment;?></th>
      </tr>
    </thead>
      <?php foreach($stories as $storyID => $story):?>
      <tr class='text-center'>
        <td><?php echo $storyID . html::hidden("storyIdList[$storyID]", $storyID);?></td>
        <td class='text-left'><?php echo $story->title;?></td>
        <td class='story-<?php echo $story->status;?>'><?php echo $this->processStatus('story', $story);?></td>
        <td class='reasons-td'>
          <?php if($story->status == 'draft') unset($reasonList['cancel']);?>
          <table class='w-p100 table-form'>
            <tr>
              <td class='pd-0'>
                <?php echo html::select("closedReasons[$storyID]", $reasonList, 'done', "class=form-control onchange=setDuplicateAndChild(this.value,$storyID) style='min-width: 80px'");?>
              </td>
              <td class='pd-0' id='<?php echo 'childStoryBox' . $storyID;?>' <?php if($story->closedReason != 'subdivided') echo "style='display:none'";?>>
              <?php echo html::input("childStoriesIDList[$storyID]", '', "class='form-control' placeholder='{$lang->idAB}'");?>
              </td>
            </tr>
          </table>
        </td>
        <td class='text-left' id='<?php echo 'duplicateStoryBox' . $storyID;?>' <?php if($story->closedReason != 'duplicate') echo "style='display:none'";?>>
          <?php echo html::select("duplicateStoryIDList[$storyID]", array('' => '') + $productStoryList[$story->product][$story->branch], $story->duplicateStory ? $story->duplicateStory : '', "class='form-control' placeholder='{$lang->bug->duplicateTip}'");?>
        </td>
        <td><?php echo html::input("comments[$storyID]", '', "class='form-control'");?></td>
      </tr>
      <?php endforeach;?>
      <tr>
        <td colspan='5' class='text-center form-actions'>
          <?php echo html::submitButton();?>
          <?php echo html::backButton();?>
        </td>
      </tr>
    </table>
  </form>
  <?php endif;?>
</div>
<?php include '../../common/view/footer.html.php';?>

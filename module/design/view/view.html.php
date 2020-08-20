<?php
/**
 * The view of design module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     design
 * @version     $Id: view.html.php 4903 2013-06-26 05:32:59Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<div id="mainMenu" class="clearfix">
  <div class="btn-toolbar pull-left">
    <?php echo html::a($this->createLink('design', 'browse', "productID=$design->product"), '<i class="icon icon-back icon-sm"></i> ' . $lang->goback, '', "class='btn btn-secondary'");?>
    <div class="divider"></div>
    <div class="page-title">
      <span class="label label-id"><?php echo $design->id?></span>
      <span class="text" title="<?php echo $design->name;?>"><?php echo $design->name;?></span>
    </div>
  </div>
</div>
<div id="mainContent" class="main-row">
  <div class="main-col col-8">
    <div class="cell">
      <div class="detail">
        <div class="detail-title"><?php echo $lang->design->desc;?></div>
        <div class="detail-content article-content">
          <?php echo $design->desc;?>
        </div>
      </div>
      <?php echo $this->fetch('file', 'printFiles', array('files' => $design->files, 'fieldset' => 'true'));?>
    </div>
    <div class='cell'><?php include '../../common/view/action.html.php';?></div>
    <div class='main-actions'>
      <div class="btn-toolbar">
        <?php common::printBack($this->session->designList);?>
        <?php if(!isonlybody()) echo "<div class='divider'></div>";?>
        <?php if(!$design->deleted):?>
        <?php
        common::printIcon('design', 'commit', "designID=$design->id", $design, 'button', 'link', '', 'iframe showinonlybody', true);
        common::printIcon('design', 'edit',   "designID=$design->id", $design, 'button', 'fork', '', '', true);
        common::printIcon('design', 'delete', "designID=$design->id", $design, 'button', 'trash', 'hiddenwin');
        ?>
        <?php endif;?>
      </div>
    </div>
  </div>
  <div class='side-col col-4'>
    <div class='cell'>
      <div class="detail">
        <div class='detail-title'><?php echo $lang->design->basicInfo;?></div>
        <div class='detail-content'>
          <table class='table table-data'>
            <tr>
              <th><?php echo $lang->design->type;?></th>
              <td><?php echo zget($lang->design->typeList, $design->type);?></td>
            </tr>
            <tr>
              <th><?php echo $lang->design->product;?></th>
              <td><?php echo $design->productName;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->design->story;?></th>
              <td><?php echo $design->story ? html::a($this->createLink('story', 'view', "id=$design->story"), zget($stories, $design->story)) : '';?></td>
            </tr>
            <tr>
              <th><?php echo $lang->design->commit;?></th>
              <td><?php echo $design->commit;?></td>
            </tr>
            <tr>
              <th><?php echo $lang->design->createdBy;?></th>
              <td><?php echo zget($users, $design->createdBy);?></td>
            </tr>
            <tr>
              <th><?php echo $lang->design->createdDate;?></th>
              <td><?php echo substr($design->createdDate, 0, 11);?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>

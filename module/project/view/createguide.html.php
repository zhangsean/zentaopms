<?php $isAjaxRequest = helper::isAjaxRequest();?>
<?php js::set('isAjaxRequest', $isAjaxRequest);?>
<?php if(!$isAjaxRequest): ?>
<?php include "../../common/view/header.html.php"?>
<?php endif;?>
<div class='modal-dialog' id='guideDialog'>
  <style>
  #guideDialog {width: 780px}
  #guideDialog h2 {margin: 10px 0 30px 0; font-size: 16px; font-weight: normal}
  #guideDialog h3 {margin: 5px 0; font-size: 20px;}
  #guideDialog .modal-footer {border-top: none; text-align: center; padding-top: 10px; padding-bottom: 40px;}
  #guideDialog .modal-footer .btn + .btn {margin-left: 20px}
  #guideDialog .project-type {padding: 0 40px}
  #guideDialog .project-type-img {width: 280px; border: 1px solid #CBD0DB; border-radius: 2px; margin-bottom: 10px; cursor: pointer; margin-top: 1px}
  #guideDialog .project-type-img:hover {border-color: #006AF1; box-shadow: 0 0 10px 0 rgba(0,0,0,.25);}
  #guideDialog .project-type.active img {border-color: #006AF1; border-width: 2px; margin-top: 0}
  </style>
  <div class='modal-content'>
    <div class='modal-body'>
      <button class="close" data-dismiss="modal">x</button>
      <h2 class='text-center'><?php echo $lang->project->chooseProgramType; ?></h2>
      <div class='row'>
        <?php
        $hasWaterfall = helper::hasFeature('waterfall');
        $colClass     = $hasWaterfall ? 'col-xs-4' : 'col-xs-6';
        ?>
        <div class='<?php echo $colClass?>'>
        <?php $tab = $from == 'global' ? 'project' : $app->tab;?>
          <div class='project-type text-center'>
            <?php echo html::a($this->createLink("project", "create", "model=scrum&programID=$programID&copyProjectID=0&extra=productID=$productID,branchID=$branchID"), "<img class='project-type-img' data-type='scrum' src='{$config->webRoot}theme/default/images/main/scrum.png'>", '', "data-app='{$tab}' class='createButton'")?>
            <h3><?php echo $lang->project->scrum; ?></h3>
            <p><?php echo $lang->project->scrumTitle; ?></p>
          </div>
        </div>
        <?php if($hasWaterfall):?>
        <div class='<?php echo $colClass?>'>
          <div class='project-type text-center'>
            <?php echo html::a($this->createLink("project", "create", "model=waterfall&programID=$programID&copyProjectID=0&extra=productID=$productID,branchID=$branchID"), "<img class='project-type-img' data-type='waterfall' src='{$config->webRoot}theme/default/images/main/waterfall.png'>", '', "data-app='{$tab}' class='createButton'")?>
            <h3><?php echo $lang->project->waterfall; ?></h3>
            <p><?php echo $lang->project->waterfallTitle; ?></p>
          </div>
        </div>
        <?php endif;?>
        <div class='<?php echo $colClass?>'>
          <div class='project-type text-center'>
            <?php echo html::a($this->createLink("project", "create", "model=kanban&programID=$programID&copyProjectID=0&extra=productID=$productID,branchID=$branchID"), "<img class='project-type-img' data-type='kanban' src='{$config->webRoot}theme/default/images/main/kanban.png'>", '', "data-app='{$tab}' class='createButton'")?>
            <h3><?php echo $lang->project->kanban;?></h3>
            <p><?php echo $lang->project->kanbanTitle;?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$('.createButton').on('click', function()
{
    $.closeModal();
});
</script>
<?php if(!$isAjaxRequest): ?>
<?php include "../../common/view/footer.html.php"?>
<?php endif;?>

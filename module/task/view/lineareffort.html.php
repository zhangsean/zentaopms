<?php if(!empty($task) and !empty($task->team) and $task->mode == 'linear'):?>
<style>
#mainContent {min-height: 500px;}
#mainContent .main-header {padding-bottom: 5px;}
#linearefforts {margin-top: -18px;}
#linearefforts .nav-tabs{margin-bottom: 10px;}
#linearefforts div.caption {height:25px; margin: 10px 0px;}
#linearefforts div.caption .account{font-weight: bolder;}
#linearefforts .tabs ul > li > a { padding-top: 6px; padding-bottom: 4px;}
</style>
<?php
$this->app->loadLang('execution');
$teamOrders = array();
foreach($task->team as $team) $teamOrders[$team->order] = $team->account;

$myOrders   = array();
$allEfforts = array();
$recorders  = array();
$index      = 0;
$allOrders  = array();
$efforts    = array_values($efforts);
foreach($efforts as $key => $effort)
{
    $prevEffort = $key > 0 ? $efforts[$key - 1] : null;
    $order      = (!$prevEffort or $prevEffort->order == $effort->order) ? $index : ++$index;
    $account    = $effort->account;

    $allEfforts[$order][]        = $effort;
    $recorders[$order][$account] = $account;
    if($app->user->account == $account)
    {
        if(!isset($myOrders[$order])) $myOrders[$order] = 0;
        $myOrders[$order] += 1;
    }
    $allOrders[$order] = $effort->order + 1;
}
?>
<div id='linearefforts'>
  <div class='tabs'>
    <ul class='nav nav-tabs'>
      <li class='my-effort'><a href='#legendMyEffort' data-toggle='tab'><?php echo $lang->task->myEffort;?></a></li>
      <li class='all-effort'><a href='#legendAllEffort' data-toggle='tab'><?php echo $lang->task->allEffort;?></a></li>
    </ul>
    <div class='tab-content'>
      <div class='tab-pane' id='legendMyEffort'>
        <?php if(!empty($myOrders)):?>
        <table class='table table-bordered table-fixed table-recorded has-sort-head'>
          <thead>
            <tr class='text-center'>
              <?php
              $vars    = (isset($objectType) ? "objectType=$objectType&" : '') . "taskID=$task->id&from=$from&orderBy=%s";
              $sort    = explode(',', $orderBy);
              $orderBy = zget($sort, '0', '');
              if(!strpos($orderBy, '_')) $orderBy .= '_asc';
              ?>
              <th class="w-60px"><?php common::printOrderLink('order', $orderBy, $vars, $lang->task->teamOrder);?></th>
              <th class="w-120px"><?php common::printOrderLink('date', $orderBy, $vars, $lang->task->date);?></th>
              <th class="w-120px"><?php echo $lang->task->recordedBy;?></th>
              <th><?php echo $lang->task->work;?></th>
              <th class="thWidth"><?php echo $lang->task->consumedAB;?></th>
              <th class="thWidth"><?php echo $lang->task->left;?></th>
              <th class='c-actions-2'><?php echo $lang->actions;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($myOrders as $order => $count):?>
            <?php $showOrder = false;?>
            <?php $index     = 1;?>
            <?php foreach($allEfforts[$order] as $effort):?>
            <?php if($effort->account != $this->app->user->account) continue;?>
            <tr class="text-center">
              <?php if(!$showOrder):?>
              <td rowspan='<?php echo $count;?>'><?php echo $allOrders[$order];?></td>
              <?php $showOrder = true;?>
              <?php endif;?>
              <td><?php echo $effort->date;?></td>
              <td><?php echo zget($users, $effort->account);?></td>
              <td class="text-left" title="<?php echo $effort->work;?>"><?php echo $effort->work;?></td>
              <td title="<?php echo $effort->consumed . ' ' . $lang->execution->workHour;?>"><?php echo $effort->consumed . ' ' . $lang->execution->workHourUnit;?></td>
              <td title="<?php echo $effort->left     . ' ' . $lang->execution->workHour;?>"><?php echo $effort->left     . ' ' . $lang->execution->workHourUnit;?></td>
              <td align='center' class='c-actions'>
                <?php
                $canOperateEffort = $this->task->canOperateEffort($task, $effort);
                common::printIcon($this->config->edition == 'open' ? 'task' : 'effort', $this->config->edition == 'open' ? 'editEstimate' : 'edit', "effortID=$effort->id", '', 'list', 'edit', '', 'showinonlybody', true, $canOperateEffort ? '' : 'disabled');
                $deleteDisable = false;
                if(!$canOperateEffort or ($index == $count and $effort->left == 0)) $deleteDisable = true;
                common::printIcon($this->config->edition == 'open' ? 'task' : 'effort', $this->config->edition == 'open' ? 'deleteEstimate' : 'delete', "effortID=$effort->id", '', 'list', 'trash', 'hiddenwin', 'showinonlybody', false, $deleteDisable ? 'disabled' : '');
                ?>
              </td>
            </tr>
            <?php $index ++;?>
            <?php endforeach;?>
            <?php endforeach;?>
          </tbody>
        </table>
        <?php endif;?>
      </div>
      <div class='tab-pane' id='legendAllEffort'>
        <table class='table table-bordered table-fixed table-recorded has-sort-head'>
          <thead>
            <tr class='text-center'>
              <?php $vars = (isset($objectType) ? "objectType=$objectType&" : '') . "taskID=$task->id&from=$from&orderBy=%s";?>
              <th class="w-60px  order-btn"><?php common::printOrderLink('order', $orderBy, $vars, $lang->task->teamOrder);?></th>
              <th class="w-120px order-btn"><?php common::printOrderLink('date', $orderBy, $vars, $lang->task->date);?></th>
              <th class="w-120px"><?php echo $lang->task->recordedBy;?></th>
              <th><?php echo $lang->task->work;?></th>
              <th class="thWidth"><?php echo $lang->task->consumedAB;?></th>
              <th class="thWidth"><?php echo $lang->task->left;?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($recorders as $order => $accounts):?>
            <?php $showOrder = false;?>
            <?php $index     = 1;?>
            <?php $count     = count($allEfforts[$order]);?>
            <?php foreach($allEfforts[$order] as $effort):?>
            <tr class="text-center">
              <?php if(!$showOrder):?>
              <td rowspan='<?php echo $count;?>'><?php echo $allOrders[$order];?></td>
              <?php $showOrder = true;?>
              <?php endif;?>
              <td><?php echo $effort->date;?></td>
              <td><?php echo zget($users, $effort->account);?></td>
              <td class="text-left" title="<?php echo $effort->work;?>"><?php echo $effort->work;?></td>
              <td title="<?php echo $effort->consumed . ' ' . $lang->execution->workHour;?>"><?php echo $effort->consumed . ' ' . $lang->execution->workHourUnit;?></td>
              <td title="<?php echo $effort->left     . ' ' . $lang->execution->workHour;?>"><?php echo $effort->left     . ' ' . $lang->execution->workHourUnit;?></td>
            </tr>
            <?php $index ++;?>
            <?php endforeach;?>
            <?php endforeach;?>
          </tbody>
        </table>
      <?php if(!empty($myOrders)):?>
      </div>
    </div>
    <?php endif;?>
  </div>
</div>
<?php endif;?>

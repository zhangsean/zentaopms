/**
 * Load branches
 *
 * @param  int $productID
 * @access public
 * @return void
 */
function loadBranches(productID)
{
    $('#branch').remove();
    $('#branch_chosen').remove();
    var oldBranch = 0;
    if(typeof(productGroups[productID]) != "undefined")
    {
        oldBranch = productGroups[productID]['branches'];
    }

    projectID = currentTab == 'execution' ? executionID : projectID;
    $.get(createLink('branch', 'ajaxGetBranches', 'productID=' + productID + '&oldBranch=0&param=active&projectID=' + projectID), function(data)
    {
        if(data)
        {
            $('#product').closest('.input-group').append(data);
            $('#branch').chosen();
        }
    });
}

function loadBranch() {return false;}

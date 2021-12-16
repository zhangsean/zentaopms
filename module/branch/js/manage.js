function addItem(obj)
{
    var $inputgroup = $(obj).closest('.input-group').clone();
    $inputgroup.find('input').val('');
    $(obj).closest('.input-group').after($inputgroup);
}

function deleteItem(obj)
{
    if($(obj).closest('#newbranches').find("input[id^='newbranch']").size() <= 1) return;
    $(obj).closest('.input-group').remove();
}

$(function()
{
    $('#branchTableList').addClass('sortable').sortable(
    {
        reverse: orderBy === 'order_desc',
        selector: 'tr',
        dragCssClass: 'drag-row',
        trigger: $('#branchTableList').find('.sort-handler').length ? '.sort-handler' : null,

        canMoveHere: function($ele, $target)
        {
            return $target.data('id') != 0;
        },

        finish: function(e)
        {
            var branches = '';
            e.list.each(function()
            {
                branches += $(this.item).data('id') + ',';
            });

            $.post(createLink('branch', 'sort'), {'branches': branches, 'orderBy': orderBy});
        }
    });

    $('td.c-name.flex').mouseenter(function()
    {
        $(this).find('.setDefault').removeClass('hidden');
    }).mouseleave(function()
    {
        $(this).find('.setDefault').addClass('hidden');
    })

    $('#newBranch').change(function()
    {
        if($(this).prop('checked'))
        {
            $('#targetBranch').attr('disabled', true).trigger('chosen:updated');

            var newBranchName = '<tr><th>' + branchLang.name + "</th><td><input type='text' name='name' id='name' class='form-control' /></td></tr>";
            var newBranchDesc = '<tr><th>' + branchLang.desc + "</th><td><input type='text' name='desc' id='desc' class='form-control' /></td></tr>";
            $(this).closest('tr').after(newBranchName + newBranchDesc);
        }
        else
        {
            $('#targetBranch').attr('disabled', false).trigger('chosen:updated');

            $('#name').closest('tr').remove();
            $('#desc').closest('tr').remove();
        }
    })

    $('#saveButton').on('click', function()
    {
        var mergedBranchIDList = [];
        $("input:checkbox[name^='branchIDList']:checked").each(function()
        {
            mergedBranchIDList.push($(this).val());
        });

        var isChecked = $('#newBranch').attr('checked') ? true : false;
        if(isChecked)
        {
            var postData = {'newBranchName' : $('#name').val(), 'newBranchDesc' : $('#desc').val(), 'newBranch' : isChecked, 'mergedBranchIDList' : mergedBranchIDList};
        }
        else
        {
            var postData = {'targetBranch' : $('#targetBranch').val(), 'mergedBranchIDList' : mergedBranchIDList};
        }

        $.post(createLink('branch', 'mergeBranch'), postData, function()
        {
            $('#mergeBranch').modal('hide');
            window.location.reload();
        });
    });
});

function changeUser(account)
{
    if(account == '')
    {
        link = createLink('company', 'dynamic', 'type=all');
    }
    else
    {
        link = createLink('company', 'dynamic', 'type=account&param=' + account);
    }
    location.href = link;
}
function changeExecution(execution)
{
    link = createLink('company', 'dynamic', 'type=execution&param=' + execution);
    location.href = link;
}
function changeProduct(product)
{
    link = createLink('company', 'dynamic', 'type=product&param=' + product);
    location.href = link;
}

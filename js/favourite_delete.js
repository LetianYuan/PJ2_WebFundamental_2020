var delete_btn = document.getElementsByClassName("delete");
for(var i = 0; i < delete_btn.length; i++)
{
    delete_btn[i].onclick = function()
    {
        alert("删除成功");
    };
}
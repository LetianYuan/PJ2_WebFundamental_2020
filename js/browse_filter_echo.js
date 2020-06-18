//此文件必须在filter_onchange之后调用
//用于在下拉栏里回显筛选条件
let ctnt = getQueryVariable('content');
let ctry = getQueryVariable('country');
let cty = getQueryVariable('city');
let contents = document.getElementById('content');
let countries = document.getElementById('country');
if(ctnt == false && ctry == false && cty == false)//不能写===，因为这里有类型转换
{
    ctnt = 'none';
    ctry = 'none';
    cty = 'none';
}
for(let e of contents.children)
{
    if(e.getAttribute('value') === ctnt)
    {
        e.selected = true;
        break;
    }
    else
    {
        e.selected = false;
    }
}
for(let e of countries.children)
{
    if(e.getAttribute('value') === ctry)
    {
        e.selected = true;
        break;
    }
    else
    {
        e.selected = false;
    }
}
for(let e of city[ctry])
{
    let option_city = document.createElement('option');
    option_city.innerHTML = e;
    option_city.setAttribute('value', e);
    if(cty === e)
    {
        option_city.selected = true;
    }
    select_city.appendChild(option_city);
}

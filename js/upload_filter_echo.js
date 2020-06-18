//此文件必须在filter_onchange之后调用
//用于回显图片信息
let contentInfo = document.getElementById('content');
let countryInfo = document.getElementById('country');
let titleInfo = document.getElementById('pic_title');
let descriptionInfo = document.getElementById('pic_description');
if(content)//如果成功传参，回显图片信息
{
    for(let e of contentInfo.children)
    {
        if(e.getAttribute('value') === content)
        {
            e.selected = true;
            break;
        }
        else
        {
            e.selected = false;
        }
    }
    for(let e of countryInfo.children)
    {
        if(e.getAttribute('value') === countryName)
        {
            e.selected = true;
            break;
        }
        else
        {
            e.selected = false;
        }
    }
    for(let e of city[countryName])
    {
        let option_city = document.createElement('option');
        option_city.innerHTML = e;
        option_city.setAttribute('value', e);
        if(cityName === e)
        {
            option_city.selected = true;
        }
        select_city.appendChild(option_city);
    }
    descriptionInfo.value = description;
    titleInfo.value = title;
}
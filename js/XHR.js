function createXHR()
{
    if(typeof XMLHttpRequest != "undefined")
    {
        return new XMLHttpRequest();
    }
    else if(typeof ActiveXObject != "undefined")
    {
        if(typeof arguments.callee.activeXString != "string")
        {
            var versions = ["MSXML2.XMLHttp.6.0", "MSXML2.XMLHttp.3.0", "MSXML2.XMLHttp"], i, len;
            for(i = 0, len = versions.length; i < len; i++)
            {
                try
                {
                    var xhr = new ActiveXObject(versions[i]);
                    arguments.callee.activeXString = versions[i];
                    return xhr;
                }
                catch(ex)
                {
                    //skip
                }
            }
        }
        return new ActiveXObject(arguments.callee.activeXString);
    }
    else
    {
        throw new Error("No XHR object available.");
    }
}

function addURLParam(url, name, value)
{
    url += (url.indexOf("?") === -1 ? "?" : "&");
    url += encodeURIComponent(name) + "=" + encodeURIComponent(value);
    return url;
}
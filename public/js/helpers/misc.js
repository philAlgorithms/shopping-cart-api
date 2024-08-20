function startLoading(object)
{
    object.find('span').removeClass('d-none');
}

function stopLoading(object)
{
    object.find('span').addClass('d-none');
}

function removeDuplicates(array)
{
    return [...new Set(array)];
}

function swapView(old, novel)
{
    switch(Array.isArray(old))
    {
        case true:
            for(var val of old)
            {
                $(val).addClass('d-none');
            }
            break;
        case false:
            $(old).addClass('d-none');
            break;
        default:
            //
    }

    
    switch(Array.isArray(novel))
    {
        case true:
            for(var val of novel)
            {
                $(val).removeClass('d-none');
            }
            break;
        case false:
            $(novel).removeClass('d-none');
            break;
        default:
            //
    }
}

function mimeFromDataUrl(dataUrl)
{
    return dataUrl.substring(dataUrl.indexOf(":")+1, dataUrl.indexOf(";"));
}
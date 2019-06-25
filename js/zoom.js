localStorage["scale"] = 1.00;

function isChrome()
{
    return !!window.chrome;
}

function zoomIn()
{
    var scale = (parseFloat(localStorage["scale"]) + 0.05).toFixed(2);
    var pusher = document.getElementById("pusher");
    //var pusherHeight = pusher.clientHeight + 15;
    //pusher.setAttribute("style", "height: " + pusherHeight + " px;");
    var mainRow = document.getElementById("mainrow");

    if (isChrome())
    {
        mainRow.setAttribute("style", "zoom: " + scale + ";");
    }
    else
    {
        mainRow.setAttribute("style", "-moz-transform: scale(" + scale + ");");
    }

    if (scale >= 1.10)
    {
        document.getElementById("nav").style.visibility = "hidden";
    }

    localStorage["scale"] = scale;
}

function zoomOut()
{
    var scale = parseFloat(localStorage["scale"]) - 0.05;
    if (scale < 1.00)
    {
        return;
    }

    // var pusher = document.getElementById("pusher");
    // pusher.clientHeight -= 15;
    var mainRow = document.getElementById("mainrow");

    if (isChrome())
    {
        mainRow.setAttribute("style", "zoom: " + scale + ";");
    }
    else
    {
        mainRow.setAttribute("style", "-moz-transform: scale(" + scale + ");");
    }

    if (scale < 1.10)
    {
        document.getElementById("nav").style.visibility = "visible";
    }

    localStorage["scale"] = scale;
}
localStorage["scale"] = 1.00;

function isChrome()
{
    return !!window.chrome;
}

function zoomIn()
{
    var scale = (parseFloat(localStorage["scale"]) + 0.05).toFixed(2);

    if (scale >= 1.70)
    {
        $("#myModal .modal-dialog .modal-content .modal-title").html("<span style=\"font-weight: bold;\">Uwaga!</span>");
        $("#myModal .modal-dialog .modal-body").html("<div class=\"alert alert-warning\"><span style=\"font-weight: bold;\">Nie można bardziej powiększyć!</span></div>");
        $("#myModal").modal("show");
        return;
    }

    var pusher = document.getElementById("pusher");
    //var pusherHeight = pusher.clientHeight + 15;
    //pusher.setAttribute("style", "height: " + pusherHeight + " px;");
    var mainRow = document.getElementById("mainrow");

    if (isChrome())
    {
        mainRow.setAttribute("style", "zoom: " + scale + "; margin-top: 30px;");
    }
    else
    {
        mainRow.setAttribute("style", "-moz-transform: scale(" + scale + "); margin-top: 30px;");
    }

    if (scale >= 1.25)
    {
        document.getElementById("nav").style.visibility = "hidden";
    }

    if (scale >= 1.55)
    {
        document.getElementsByTagName("footer")[0].style.visibility = "hidden";
        if (isChrome())
        {
            mainRow.setAttribute("style", "zoom: " + scale + "; margin-top: 90px;");
        }
        else
        {
            mainRow.setAttribute("style", "-moz-transform: scale(" + scale + "); margin-top: 90px;");
        }
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
        mainRow.setAttribute("style", "zoom: " + scale + "; margin-top: 30px;");
    }
    else
    {
        mainRow.setAttribute("style", "-moz-transform: scale(" + scale + "); margin-top: 30px;");
    }

    if (scale < 1.25)
    {
        document.getElementById("nav").style.visibility = "visible";
    }

    if (scale < 1.55)
    {
        document.getElementsByTagName("footer")[0].style.visibility = "visible";
        if (isChrome())
        {
            mainRow.setAttribute("style", "zoom: " + scale + "; margin-top: 90px;");
        }
        else
        {
            mainRow.setAttribute("style", "-moz-transform: scale(" + scale + "); margin-top: 90px;");
        }
    }

    localStorage["scale"] = scale;
}
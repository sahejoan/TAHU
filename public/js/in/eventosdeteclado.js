function enfocar(event,number)
    {		
        if((event.keyCode == 13) || (event.which == 13))
        {
            var wow = document.getElementById(number);
            wow.focus();
        }        
    }
/*
function numfloat(e) {
    evt = e ? e : event;
    tcl = (window.Event) ? evt.which : evt.keyCode;
    if ((tcl < 48 || tcl > 57) && (tcl != 8 && tcl != 0 && tcl != 46 && tcl != 44 ))
    {
        return false;
    }
    return true;
}
*/
function numfloat(e) {
    evt = e ? e : event;
    tcl = (window.Event) ? evt.which : evt.keyCode;
    if ((tcl < 48 || tcl > 57) && (tcl != 8 && tcl != 0 && tcl != 46 && tcl != 44 ))
    {
        return false;
    }
    return true;
}
function numentero(e) {
    evt = e ? e : event;
    tcl = (window.Event) ? evt.which : evt.keyCode;
    if ((tcl < 48 || tcl > 57) && (tcl != 8 && tcl != 0 ))
    {
        return false;
    }
    return true;
}

function numtime(e) {
    evt = e ? e : event;
    tcl = (window.Event) ? evt.which : evt.keyCode;
    if ((tcl < 48 || tcl > 57) && (tcl != 8 && tcl != 0 ))
    {
        return false;
    }
    return true;
}

function let(e) {
    evt = e ? e : event;
    tcl = (window.Event) ? evt.which : evt.keyCode;
    if ((tcl >= 48 && tcl <= 57) && (tcl != 8 && tcl != 0 && tcl != 46))
    {
        return false;
    }
    return true;
}

function tecla(e)
{
    var evt = e ? e : event;
    var key = window.Event ? evt.which : evt.keyCode;
    alert (key);
	//return evt.which;
}


function handleClick(val) {
    var element = document.getElementById('banner_div');

    if (val.checked == true) {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}


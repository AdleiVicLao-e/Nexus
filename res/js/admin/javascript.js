function openTab(tabName) {
    var i, tabContent, tabs;
    tabContent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].style.display = "none";
    }

    tabs = document.getElementsByClassName("tab");
    for (i = 0; i < tabs.length; i++) {
        tabs[i].className = tabs[i].className.replace(" active", "");
    }

    document.getElementById(tabName).style.display = "block";
    document.querySelector(`.tab[onclick="openTab('${tabName}')"]`).className += " active";
}

document.addEventListener("DOMContentLoaded", function() {
    openTab('search');
});



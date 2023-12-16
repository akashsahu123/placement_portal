document.getElementById('show-menu').onclick = () => {
    let sidebar = document.getElementById('sidebar');
    sidebar.style.left = '0';
    document.getElementById('show-menu').classList.toggle('d-none');
    document.getElementById('close-menu').classList.toggle('d-none');
}


document.getElementById('close-menu').onclick = () => {
    let sidebar = document.getElementById('sidebar');
    sidebar.style.left = '-280px';
    document.getElementById('show-menu').classList.toggle('d-none');
    document.getElementById('close-menu').classList.toggle('d-none');
}

document.getElementById('logout-btn').onclick = () => {
    window.location = 'logout.php';
}

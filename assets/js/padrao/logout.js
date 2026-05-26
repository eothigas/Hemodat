document.getElementById('logout').addEventListener('click', function (event) {
    event.preventDefault();
    fetch(BASE_URL + '/includes/actions/auth.php?action=logout')
        .then(response => {
            if (response.ok) {
                window.location.href = BASE_URL + '/login';
            }
        })
        .catch(() => {
            window.location.href = BASE_URL + '/login';
        });
});

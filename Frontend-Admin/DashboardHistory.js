document.addEventListener("DOMContentLoaded", function () {
    fetchAppointmentHistory();
});

function fetchAppointmentHistory() {
    fetch('../Backend-Admin/fetch_appointment_history.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('history-body').innerHTML = data;
        })
        .catch(error => console.error('Error fetching appointment history:', error));
}

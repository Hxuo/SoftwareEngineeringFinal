document.addEventListener("DOMContentLoaded", function () {
    const calendarBody = document.getElementById("calendar-body");
    const selectedDateElement = document.getElementById("selected-date");

    let today = new Date();
    let firstDay = new Date(today.getFullYear(), today.getMonth(), 1).getDay();
    let daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();

    let reservedDays = [3, 10, 17, 24]; // Example reserved dates
    let closedDays = [1, 8, 15, 22]; // Example closed days

    let date = 1;
    for (let i = 0; i < 6; i++) {
        let row = document.createElement("tr");

        for (let j = 0; j < 7; j++) {
            let cell = document.createElement("td");

            if (i === 0 && j < firstDay) {
                cell.classList.add("empty");
            } else if (date > daysInMonth) {
                break;
            } else {
                // Create a box inside the date cell
                let box = document.createElement("div");
                box.classList.add("calendar-box-inner");
                box.textContent = date;

                if (reservedDays.includes(date)) {
                    cell.classList.add("reserved-slot");
                } else if (closedDays.includes(date)) {
                    cell.classList.add("closed-slot");
                } else {
                    cell.classList.add("available-slot");
                    cell.addEventListener("click", function () {
                        selectedDateElement.textContent = "SELECTED DATE: " + date + " " + today.toLocaleString('default', { month: 'long' }) + " " + today.getFullYear();
                    });
                }

                // Append the box inside the cell
                cell.appendChild(box);
                date++;
            }
            row.appendChild(cell);
        }
        calendarBody.appendChild(row);
    }
});

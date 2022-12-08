function addShift() {
    let empValue = document.getElementById("selectEmployeeAdd").value
    let startValue = document.getElementById("start").value
    let endValue = document.getElementById("end").value

    const data = {
        employeeID: empValue,
        startTime: startValue,
        endTime: endValue
    };

    fetch('./api/shifts/addShift.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            console.log(response)
        })
        .catch((error) => {
            console.log(error);
        });
}
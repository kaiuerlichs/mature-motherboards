function addShift() {
    let empValue = document.getElementById("selectEmployeeAdd").value
    let startValue = document.getElementById("start").value.replace("T", " ")
    let endValue = document.getElementById("end").value.replace("T", " ")
    console.log(startValue)
    const data = {
        "shiftDetails":{
            "startTime": startValue,
            "endTime": endValue
        },
        "employeeID": empValue
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
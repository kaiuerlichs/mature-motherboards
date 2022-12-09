function scheduleRepair() {
    document.getElementById("repairButton").innerHTML = '<i class="fa-solid fa-gear fa-spin" id="spinner"></i>'
    //format of post request branchId, Time, Duration, Description, Email, firstname, lastname-->
    let fnameValue = document.getElementById("firstname").value;
    let lnameValue = document.getElementById("lastname").value;
    let emailValue = document.getElementById("email").value;
    let dateValue = document.getElementById("date").value.replace("T", " ");
    let durValue = document.getElementById("duration").value;
    let descValue = document.getElementById("description").value;

    const data = {
        firstname: fnameValue,
        lastname: lnameValue,
        Email: emailValue,
        Time: dateValue,
        Duration: durValue,
        Description: descValue
    };

    fetch('./api/repairs/ScheduleRepair.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            document.getElementById("repairButton").innerHTML = 'Submit';
            console.log(data)
        })
        .catch((error) => {
            document.getElementById("repairButton").innerHTML = 'Submit';
            console.log(error);
        });
}

function scheduleRepairWithID() {
    document.getElementById("repairButton").innerHTML = '<i class="fa-solid fa-gear fa-spin" id="spinner"></i>'
    //format of post request branchId, Time, Duration, Description, Email, firstname, lastname-->
    let fnameValue = document.getElementById("firstname").value;
    let lnameValue = document.getElementById("lastname").value;
    let emailValue = document.getElementById("email").value;
    let dateValue = document.getElementById("date").value.replace("T", " ");
    let durValue = document.getElementById("duration").value;
    let descValue = document.getElementById("description").value;
    let branchValue = document.getElementById("branchID").value;

    const data = {
        firstname: fnameValue,
        lastname: lnameValue,
        Email: emailValue,
        Time: dateValue,
        Duration: durValue,
        Description: descValue,
        branchId: branchValue
    };

    fetch('./api/repairs/ScheduleRepairWithID.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            document.getElementById("repairButton").innerHTML = 'Submit';
            console.log(data)
        })
        .catch((error) => {
            document.getElementById("repairButton").innerHTML = 'Submit';
            console.log(error);
        });
}
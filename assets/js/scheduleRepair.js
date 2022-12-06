function scheduleRepair() {
    //format of post request branchId, Time, Duration, Description, Email, firstname, lastname-->
    let fnameValue = document.getElementById("firstname").value;
    let lnameValue = document.getElementById("lastname").value;
    let emailValue = document.getElementById("email").value;
    let dateValue = document.getElementById("date").value;
    let durValue = document.getElementById("duration").value;
    let descValue = document.getElementById("description").value;
    let branchValue = document.getElementById("branchID").value;

    const data = {
        firstname: fnameValue,
        lastname: lnameValue,
        email: emailValue,
        time: dateValue,
        duration: durValue,
        description: descValue,
        branchId: branchValue
    };

    fetch('./api/repairs/scheduleRepair.php', {
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
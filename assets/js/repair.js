function scheduleRepair() {
    let nameValue = document.getElementById("name").value
    let prodNameValue = document.getElementById("productName").value
    let dateValue = document.getElementById("date").value
    let durValue = document.getElementById("duration").value
    let descValue = document.getElementById("description").value

    const data = {
        name: nameValue,
        productName: prodNameValue,
        date: dateValue,
        duration: durValue,
        description: descValue,
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
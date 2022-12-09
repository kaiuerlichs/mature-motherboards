function inputOrder() {
    //format of post request branchId, Time, Duration, Description, Email, firstname, lastname-->
    let email = document.getElementById("emailOrder").value;
    let productId = document.getElementById("productNo").value;
    let charge = document.getElementById("charge").value;

    const data = {"email" : email, "productID" : productId, "charge": charge};

    fetch('./api/orders/InputOrder.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            console.log(data)
        })
        .catch((error) => {
            console.log(error);
        });
}
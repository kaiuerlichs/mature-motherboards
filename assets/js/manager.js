function addProduct() {
    let nameValue = document.getElementById("name").value
    let descValue = document.getElementById("description").value
    let typeValue = document.getElementById("type").value
    let priceValue = document.getElementById("price").value
    let stockValue = document.getElementById("stock").value
    let dateValue = document.getElementById("date").value
    let osValue = document.getElementById("os").value
    let archValue = document.getElementById("architecture").value
    let storageValue = document.getElementById("storage").value
    let ramValue = document.getElementById("memory").value
    let pagesValue = document.getElementById("pageCount").value
    let linkValue = document.getElementById("link").value

    const data = {
        name: nameValue,
        description: descValue,
        type: typeValue,
        price: priceValue,
        stock: stockValue,
        date: dateValue,
        os: osValue,
        architecture: archValue,
        storage: storageValue,
        memory: ramValue,
        pageCount: pagesValue,
        link: linkValue
    };

    fetch('./api/products/AddProduct.php', {
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
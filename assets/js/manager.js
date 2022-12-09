function addProduct() {
    document.getElementById("addProductButton").innerHTML='<i class="fa-solid fa-gear fa-spin"></i>'
    let nameValue = document.getElementById("name").value
    let descValue = document.getElementById("description").value
    let typeValue = document.getElementById("type").value
    let priceValue = document.getElementById("price").value
    let stockValue = document.getElementById("stock").value
    let dateValue = document.getElementById("date").value.replace("T", " ")
    let osValue = document.getElementById("os").value
    let archValue = document.getElementById("architecture").value
    let storageValue = document.getElementById("storage").value
    let ramValue = document.getElementById("memory").value
    let pagesValue = document.getElementById("pageCount").value
    let linkValue = document.getElementById("link").value

    const data = {
        "Name": nameValue,
        "Desc": descValue,
        "Type": typeValue,
        "Price": priceValue,
        "Stock": stockValue,
        "date": dateValue,
        "OperatingSystem": osValue,
        "Architecture": archValue,
        "Storage": storageValue,
        "Memory": ramValue,
        "PageCount": pagesValue,
        "Image": linkValue
    };
    console.log(JSON.stringify(data))

    fetch('./api/products/AddProduct.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            document.getElementById("addProductButton").innerHTML='Submit'
            console.log(response)
        })
        .catch((error) => {
            document.getElementById("addProductButton").innerHTML='Submit'
            console.log(error);
        });
}
function addStock() {
    document.getElementById("addStockButton").innerHTML='<i class="fa-solid fa-gear fa-spin"></i>'
    let IDValue = document.getElementById("productID").value
    let quanValue = document.getElementById("quantity").value

    const data = {
        "productID": IDValue,
        "stock": quanValue,
    };
    console.log(JSON.stringify(data))

    fetch('./api/products/IncreaseStock.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            console.log(response)
            document.getElementById("addStockButton").innerHTML='Submit'
        })
        .catch((error) => {
            console.log(error);
            document.getElementById("addStockButton").innerHTML='Submit'
        });
}
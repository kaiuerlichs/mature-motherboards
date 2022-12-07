
var products = [
    {
        "ProductID": 1,
        "Price": "250",
        "Type": "Computer",
        "Name": "Sinclair ZX80",
        "Image": "https://picsum.photos/1200/400"
    },
    {
        "ProductID": 4,
        "Price": "200",
        "Type": "Computer",
        "Name": "Atari 400",
        "Image": "https://picsum.photos/1200/400"
    },
    {
        "ProductID": 5,
        "Price": "205",
        "Type": "Computer",
        "Name": "Atari 800",
        "Image": "https://picsum.photos/1200/400"
    },
    {
        "ProductID": 6,
        "Price": "400",
        "Type": "Computer",
        "Name": "IBM 5120",
        "Image": "https://picsum.photos/1200/400"
    },
    {
        "ProductID": 7,
        "Price": "199",
        "Type": "Computer",
        "Name": "Commodore 64",
        "Image": "https://picsum.photos/1200/400"
    },
    {
        "ProductID": 8,
        "Price": "75",
        "Type": "Computer",
        "Name": "Sinclair ZX Spectrum",
        "Image": "https://picsum.photos/1200/400"
    },
    {
        "ProductID": 9,
        "Price": "190",
        "Type": "Computer",
        "Name": "Compaq Portable",
        "Image": "https://picsum.photos/1200/400"
    },
    {
        "ProductID": 10,
        "Price": "3700",
        "Type": "Computer",
        "Name": "Apple Macintosh 128K",
        "Image": "https://picsum.photos/1200/400"
    },
    {
        "ProductID": 11,
        "Price": "900",
        "Type": "Computer",
        "Name": "Amiga 1000",
        "Image": "https://picsum.photos/1200/400"
    },
    {
        "ProductID": 12,
        "Price": "1200",
        "Type": "Computer",
        "Name": "Apple Macintosh Portable",
        "Image": "https://picsum.photos/1200/400"
    },
    {
        "ProductID": 13,
        "Price": "20",
        "Type": "UserGuide",
        "Name": "Sinclair ZX Spectrum User Guide",
        "Image": "https://picsum.photos/1200/400"
    }
]; //Can be removed when working
var cart = {};
var source = "https://picsum.photos/1200/400";
var productsSorted;

let filter = document.getElementById("filter");
filter.addEventListener('click', filterClick);

function filterClick(){
    let sortBy = document.getElementById("sortBy");
    let valueSortBy = sortBy.value;
    let type = document.getElementById("productType");
    let asc = true;
    if(valueSortBy == 2) asc = false;
    if(type.value != 0) displayProducts(productsSorted, asc, type.options[type.selectedIndex].text);
}

//displayProducts(products, false);
/*productsSorted = products.sort(function(a, b) {
    var keyA = Number(a.Price),
    keyB = Number(b.Price);
    if (keyA < keyB) return -1;
    if (keyA > keyB) return 1;
    return 0;
  }); */

fetch('./api/products/GetProductsList.php', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
    }
}).then((response) => {
    displayProducts(response, false);
    productsSorted = response.sort(function(a, b) {
        var keyA = Number(a.Price),
        keyB = Number(b.Price);
        if (keyA < keyB) return -1;
        if (keyA > keyB) return 1;
        return 0;
      });


})
    .catch((error) => {
        console.log(error);
});





function displayProducts(data, asc, type){
    let cards = document.getElementById("containerCards");
    cards.innerHTML = "";
    data = products; //REMOVE WHEN CONNECTED TO DATABASE
    
    var onClick = (event) => {
        window.location.href = 'product.html?'+event.target.id;
    }

    for(let i = (asc ? 0 : data.length-1); (asc ? i<data.length : i>=0); (asc ? i++: i--)){
        if (type !== undefined && type != "Product Type" && type != data[i].Type){
            continue;
        }
        let div = document.createElement("div");
        div.classList.add("col-xl-3", "col-lg-4", "col-md-6", "col-sm-12", "mb-5");

        let div1 = document.createElement("div");
        div1.classList.add("card");
        div1.style.width = "18rem";
        div1.style.height = "22rem";

        let img = document.createElement("img");
        img.classList.add("card-img-top");
        img.style.width = "18rem";
        img.style.height = "150px";
        img.setAttribute("src", data[i].Image);

        let div2 = document.createElement("div");
        div2.classList.add("card-body", "d-flex", "flex-column");

        let title = document.createElement("h5");
        title.classList.add("card-title");
        title.innerText = data[i].Name;
        title.setAttribute('id', data[i].ProductID+"!title");
        let price = document.createElement("h6");
        price.classList.add("card-subtitle", "mb-2", "text-muted");
        price.innerText = "£"+data[i].Price;
        
        let desc = document.createElement("p");
        desc.classList.add("card-text");
        desc.innerText = data[i].Type;
        let intoCart = document.createElement("a");
        intoCart.setAttribute('id', data[i].ProductID);
        intoCart.classList.add("btn", "btn-primary", "mt-auto");
        intoCart.innerText = "Go to product"
        intoCart.addEventListener('click', onClick);
        
        div2.appendChild(title);
        div2.appendChild(price);
        div2.appendChild(desc);
        div2.appendChild(intoCart);
        div1.appendChild(img);
        div1.appendChild(div2);
        div.appendChild(div1)
        cards.appendChild(div);

    }
}



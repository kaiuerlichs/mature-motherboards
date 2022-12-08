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

fetch('./api/products/GetProductsList.php', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
    }

})
.then((response) => response.json())
.then((data) => {
    document.getElementById("cog").remove();
    displayProducts(data, false);
    productsSorted = data.sort(function(a, b) {
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




var products = { 'First Product': [ "Description of first product", "1.11", "1"], 
'Second Product': [ "Description of second product", "2.22", "2"], 
'Third Product': [ "Description of third product", "3.33", "3"], 
'Fourth Product': [ "Description of third product", "3.33", "3"],
'First2 Product': [ "Description of first product", "1.11", "1"], 
'Second2 Product': [ "Description of second product", "2.22", "2"], 
'Third2 Product': [ "Description of third product", "3.33", "3"], 
'Fourth2 Product': [ "Description of third product", "3.33", "3"]}; //SQL Query
var cart = {};
var source = "https://picsum.photos/1200/400";


var cards = document.getElementById("containerCards");

displayProducts();

function displayProducts(){

    var onClick = (event) => {
        window.location.href = 'product.html?'+event.target.id;
    }

    for(var key in products) {
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
        img.setAttribute("src", source)

        let div2 = document.createElement("div");
        div2.classList.add("card-body", "d-flex", "flex-column");

        let title = document.createElement("h5");
        title.classList.add("card-title");
        title.innerText = key;
        title.setAttribute('id', products[key][2]+"!title");
        let price = document.createElement("h6");
        price.classList.add("card-subtitle", "mb-2", "text-muted");
        price.innerText = "£"+products[key][1];
        
        let desc = document.createElement("p");
        desc.classList.add("card-text");
        desc.innerText = products[key][0];
        let intoCart = document.createElement("a");
        intoCart.setAttribute('id', products[key][2]);
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



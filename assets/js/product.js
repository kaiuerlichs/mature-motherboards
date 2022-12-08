try {
    cart = JSON.parse(localStorage.getItem('cart'))
} catch (error) {
    console.log(error);
}

function onClick() {
    document.getElementById("addToCart").innerHTML = '<i class="fa-solid fa-gear" id="spinner"></i>';
    let spinner = document.getElementById("spinner");
    spinner.classList.add("fa-spin");
    let alreadyInCart = false;
    let targetID = window.location.href.split("?")[1];

    for (let id in cart) {
        if (id == targetID) {
            cart[id] = cart[id] + 1;
            alreadyInCart = true;
            console.log(cart[id]);
            spinner.classList.remove("fa-spin")
            document.getElementById("addToCart").innerHTML = 'Add To Cart';
            document.getElementById("alert").classList.add("alert-danger");
            document.getElementById("alert").innerHTML = "Already Added!";
            document.getElementById("alert").removeAttribute("hidden");
            break;
        }
    }
    if (!alreadyInCart) {
        if (cart == null) {
            cart = {};
        }
        cart = Object.assign(cart, { [targetID]: 1 });
        spinner.classList.remove("fa-spin");
        document.getElementById("addToCart").innerHTML = 'Add To Cart';
        document.getElementById("alert").classList.add("alert-primary");
        document.getElementById("alert").innerHTML = "Added Successfully!";
        document.getElementById("alert").removeAttribute("hidden");

    }
    localStorage.setItem('cart', JSON.stringify(cart));
}





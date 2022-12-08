try {
    cart = JSON.parse(localStorage.getItem('cart'))
} catch (error) {
    console.log(error);
}

function onClick() {
    let alreadyInCart = false;
    let targetID = window.location.href.split("?")[1];

    for (let id in cart) {
        if (id == targetID) {
            cart[id] = cart[id] + 1;
            alreadyInCart = true;
            console.log(cart[id]);
            break;
        }
    }
    if (!alreadyInCart) {
        if (cart == null) {
            cart = {};
        }
        cart = Object.assign(cart, { [targetID]: 1 });

    }
    localStorage.setItem('cart', JSON.stringify(cart));
}





var order = ["Hawkhill", "15/09/2022"];

// Put into storage
localStorage.setItem('order', JSON.stringify(order));

// Retrieve the object from storage
var retrievedObject = JSON.parse(localStorage.getItem('order'));
//call main when website loads
window.onload = function() {
    main();
  }

  function main() {

    var pathArray = window.location.search.split('?');
    let idNumber = pathArray[1];

    fetch('./api/orders/GetOrderById.php?id=' + idNumber)
      .then((resp) => resp.json())
      .then(function(data) {
        console.log(data);

        //Gets data from the API and writes it to the HTML
        document.getElementById('address').innerHTML = "Address: " + data.address;
        document.getElementById('dateOrdered').innerHTML = "Date Ordered: " + data.dateOrdered;
        document.getElementById('productImage').src = data.Image;
        document.getElementById('name').innerHTML = data.Name;
        document.getElementById('description').innerHTML = "Description:" + data.Description;
        document.getElementById('price').innerHTML = "Price" + data.Price;
      })
      .catch(function(error) {
        console.log(error);
      });
  }
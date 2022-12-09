
var cart = { '1': "2"
           }; //Replace with SQL Query of IDs from Cart

var orderPlace =  {
    "orderDetails": {
      "firstname": "",
      "lastname": "",
      "email": ""
    },
    
    "addressDetails": {
      "line1": "",
      "line2": "",
      "line3": "",
      "town": "",
      "postcode": ""
    },
  
    "cardDetails": {
      "name": "",
      "number": "",
      "cvv": "",
      "expiry": "",
      "line1": "",
      "line2": "",
      "line3": "",
      "town": "",
      "postcode": ""
    },
  
    "products": []
  };
var product1 = {
    "ProductID": 1,
    "Price": "250",
    "Type": "Computer",
    "Name": "Sinclair ZX80",
  "Image": null
};


//localStorage.setItem('cart', JSON.stringify(cart)); //WHEN TESTING UNCOMMENT THIS LINE

// Retrieve the object from storage
var retrievedObject = JSON.parse(localStorage.getItem('cart'));



var totalAmount = 0;
var totalNumber = 0;

const ul = document.getElementById('cart');
const spanNumber = document.getElementById('totalNumber');

(function() {
    'use strict';

    window.addEventListener('load', function() {
      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.getElementsByClassName('needs-validation');
      document.getElementById("same-address").checked = false;  

      // Loop over them and prevent submission
      var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', async function(event) {
          event.preventDefault();
          if(document.getElementById("same-address").checked == true){
            document.getElementById("firstNameShipping").value = document.getElementById("firstName").value;
            document.getElementById("lastNameShipping").value = document.getElementById("lastName").value;
            document.getElementById("addressShipping").value = document.getElementById("address").value;
            document.getElementById("address2Shipping").value = document.getElementById("address2").value;
            document.getElementById("address3Shipping").value = document.getElementById("address3").value;
            document.getElementById("townShipping").value = document.getElementById("town").value;
            document.getElementById("zipShipping").value = document.getElementById("zip").value;
          }
          if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }else{
            orderPlace.orderDetails.email = document.getElementById("email").value;
            orderPlace.orderDetails.firstname = document.getElementById("firstName").value;
            orderPlace.orderDetails.lastname = document.getElementById("lastName").value;
            orderPlace.addressDetails.line1 = document.getElementById("addressShipping").value;
            orderPlace.addressDetails.line2 = document.getElementById("address2Shipping").value;
            orderPlace.addressDetails.line3 = document.getElementById("address3Shipping").value;
            orderPlace.addressDetails.postcode = document.getElementById("zipShipping").value;
            orderPlace.addressDetails.town = document.getElementById("town").value;
            orderPlace.cardDetails.cvv = document.getElementById("cc-cvv").value;
            orderPlace.cardDetails.expiry = document.getElementById("cc-expiration").value;
            orderPlace.cardDetails.number = document.getElementById("cc-number").value;
            orderPlace.cardDetails.name = document.getElementById("cc-name").value;
            orderPlace.cardDetails.line1 = document.getElementById("address").value;
            orderPlace.cardDetails.line2 = document.getElementById("address2").value;
            orderPlace.cardDetails.line3 = document.getElementById("address3").value;
            orderPlace.cardDetails.town = document.getElementById("town").value;
            orderPlace.cardDetails.postcode = document.getElementById("zip").value;


            

            for(var key in retrievedObject) {
                for(let i = 0; i<Number(retrievedObject[key]);i++){
                    orderPlace.products.push(key);
                }
            }
            await uploadOrder(orderPlace);
           
            
          }
          form.classList.add('was-validated');
        }, false);
      });
      
    }, false);
  })();

async function uploadOrder(orderPlacing){
  fetch('./api/orders/CreateOrder.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify(orderPlacing),
  })
      .then((response) => response.json())
      .then((data) => {
          console.log(data);
          alert("Your order has been placed successfully!");
          location.href = "index.html"
      })
      .catch((error) => {
        console.log(error);
        alert("There has been an error with your order, please try again");
      });
}

var onClick = (event) => {
    
    let id = event.target.id.replace("~", '');
    let amount = Number(document.getElementById("£"+id).textContent.replace("£", "")) / Number(document.getElementById("x"+id).innerText.replace("x", ""));
    totalAmount =  Math.round((totalAmount - amount) * 100) / 100 ;   
    document.getElementById("total").textContent = "£" + totalAmount;
    
    if(document.getElementById("x"+id).innerText == "x1"){
        delete retrievedObject[id];
        document.getElementById(id).remove();
        totalNumber -= 1;
        spanNumber.textContent = totalNumber;
    }else{
        retrievedObject[id] = (Number(retrievedObject[id]) - 1).toString(); 
        
        document.getElementById("x"+id).innerText = "x" + retrievedObject[id];
        document.getElementById("£"+id).innerText =  "£"+ (Math.round((Number(document.getElementById("£"+id).innerText.replace("£", "")) - amount) * 100) / 100).toString();
    }
    
    localStorage.setItem('cart', JSON.stringify(retrievedObject));
    
}


initializeCart();

async function getProduct(key) {
  return fetch('./api/products/GetProductById.php?id=' + key)
      .then((response)=>response.json())
      .then((responseJson)=>{return responseJson});
}


async function initializeCart(){

    for(var key in retrievedObject) {
        let response = await getProduct(key);
        //let response = product1; //WHEN TEESTING UNCOMMENT THIS
        let li = document.createElement('li');
        li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'lh-condensed');
        li.setAttribute('id', key);
        let div = document.createElement('div');
    
        let h6 = document.createElement('h6');
        h6.classList.add('my-0');
        if(response["Name"].length > 17){
          h6.style.fontSize = "12px";
        }
        h6.textContent = response["Name"];
    
        let small = document.createElement('small');
        small.classList.add('text-muted');
        small.textContent =  "x" +retrievedObject[key];
        
        small.setAttribute('id', "x"+key);
        let span = document.createElement('span');
        span.classList.add("text-muted");
        span.textContent = "£" + Number(response["Price"]) * Number(retrievedObject[key]);
        span.setAttribute('id', "£"+key);
        let span2 = document.createElement('span');
        span2.classList.add("text-danger");
        span2.setAttribute('id', "~"+key);
        span2.textContent = "remove";
        span2.addEventListener('click', onClick);
        span2.addEventListener("mouseover", mOver, false);
        span2.addEventListener("mouseout", mOut, false);
        function mOver() {
          span2.setAttribute("style", "cursor: pointer;")
        }
        function mOut() {  
          span2.setAttribute("style", "cursor: auto;")
        }
    
        div.appendChild(h6);
        div.appendChild(small);
        li.appendChild(div);
        li.appendChild(span2);
        li.appendChild(span);
        
        ul.appendChild(li);
        
    
        totalAmount +=  Number(response["Price"]) * Number(retrievedObject[key]);
        totalNumber++;

      
    
        
    } 
    spanNumber.textContent = totalNumber;
    initializeTotal();
    initialized = true;   

}

function initializeTotal(){
    let li = document.createElement('li');
    li.classList.add('list-group-item', 'd-flex', 'justify-content-between');
    let span = document.createElement('span');
    span.textContent = "Total (GPB)";
    let strong = document.createElement('strong');
    strong.setAttribute('id', 'total');
    strong.textContent = "£" + totalAmount;
    li.appendChild(span)
    li.appendChild(strong)
    ul.appendChild(li)
}

function checkBoxShipping() {
    // Get the checkbox
    let checkBox = document.getElementById("same-address");
    // Get the output text
    let section = document.getElementById("sectionShipping");
    let hr = document.getElementById("hrShipping");
  
    // If the checkbox is checked, display the output text
    if (checkBox.checked == true){
      document.getElementById("firstNameShipping").value = document.getElementById("firstName").value;
      document.getElementById("lastNameShipping").value = document.getElementById("lastName").value;
      document.getElementById("addressShipping").value = document.getElementById("address").value;
      document.getElementById("address2Shipping").value = document.getElementById("address2").value;
      document.getElementById("address3Shipping").value = document.getElementById("address3").value;
      document.getElementById("townShipping").value = document.getElementById("town").value;
      document.getElementById("zipShipping").value = document.getElementById("zip").value;
      section.style.display = "none";
      hr.style.display = "none";
    } else {
      section.style.display = "block";
      hr.style.display = "block";
    }
  } 



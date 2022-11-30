
var cart = { 'First Product': [ "Description of first product", "1.11"], 
                  'Second Product': [ "Description of second product", "2.22"], 
                  'Third Product': [ "Description of third product", "3.33"] };

// Put the object into storage
localStorage.setItem('cart', JSON.stringify(cart));

// Retrieve the object from storage
var retrievedObject = JSON.parse(localStorage.getItem('cart'));


var totalAmount = 0;
var totalNumber = 0;

const ul = document.getElementById('cart');
const spanNumber = document.getElementById('totalNumber');
initializeCart();


function initializeCart(){

    var onClick = (event) => {
        console.log(event.target.id);
        let id = event.target.id.replace("~", '');
        let amount = document.getElementById("£"+id).textContent.replace("£", "");
        totalNumber--;
        totalAmount =  Math.round((totalAmount - Number(amount)) * 100) / 100    
        document.getElementById("total").textContent = "£" + totalAmount;
        spanNumber.textContent = totalNumber;
        delete retrievedObject[id]
        localStorage.setItem('cart', JSON.stringify(retrievedObject))
        document.getElementById(id).remove();
    }
      

    var retrievedObject = JSON.parse(localStorage.getItem('cart'));
    for(var key in retrievedObject) {
        let li = document.createElement('li');
        li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'lh-condensed');
        li.setAttribute('id', key);
        let div = document.createElement('div');
    
        let h6 = document.createElement('h6');
        h6.classList.add('my-0');
        
        h6.textContent = key;
    
        let small = document.createElement('small');
        small.classList.add('text-muted');
        small.textContent = retrievedObject[key][0]; 
    
        let span = document.createElement('span');
        span.classList.add("text-muted");
        span.textContent = "£" + retrievedObject[key][1];
        span.setAttribute('id', "£"+key);
        let span2 = document.createElement('span');
        span2.classList.add("text-danger");
        span2.setAttribute('id', "~"+key);
        span2.textContent = "remove";
        span2.addEventListener('click', onClick);
    
        div.appendChild(h6);
        div.appendChild(small);
        li.appendChild(div);
        li.appendChild(span2);
        li.appendChild(span);
        
        ul.appendChild(li);
        
    
        totalAmount += Number(retrievedObject[key][1]);
        totalNumber++;
    }   
    spanNumber.textContent = totalNumber ;
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
      section.style.display = "none";
      hr.style.display = "none";
    } else {
      section.style.display = "block";
      hr.style.display = "block";
    }
  } 



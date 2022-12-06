
var branches = { 1 : [ "Postcode", "Number", "Street", "Country", "Opening times", "BranchName"], 
2 : [ "DD2 5PA", "10", "Friary Gardens", "Great Britan", "5-12", "My Home"],
3 : [ "EH9 1NX", "29", "Sciennes Rd", "GB", "Opening times", "BranchName"]}; //SQL Query

var source = "https://picsum.photos/1200/400";

var cards = document.getElementById("containerCards");

API_KEY = "AIzaSyCRGzepAJM76Tvb_PZ2HqU-T4fbT8zJXuA"



function getFullAddress(id, forGoogle){

    console.log(id);
    if(forGoogle){
        return branches[Number(id)][0] + " " + branches[Number(id)][1] + " " + branches[Number(id)][2] + " " + branches[Number(id)][3] + "&components=country:GB";
    }
    if(typeof id === "number"){
        return branches[id][0] + " " + branches[id][1] + " " + branches[id][2] + " " + branches[id][3] + " " + branches[id][4];
    }else{
        return branches[Number(id)][0] + " " + branches[Number(id)][1] + " " + branches[Number(id)][2] + " " + branches[Number(id)][3] + " " + branches[Number(id)][4];
    }
    
}

function setDistance(el, lat, lng){
    let address = getFullAddress(el.id, true);
    fetch("https://maps.googleapis.com/maps/api/geocode/json?address="+address+'&key='+API_KEY)
      .then(response => response.json())
      .then(data => {
        const latitude = data.results[0].geometry.location.lat;
        const longitude = data.results[0].geometry.location.lng;
        
        var distance = google.maps.geometry.spherical.computeDistanceBetween(new google.maps.LatLng(latitude, longitude), new google.maps.LatLng(lat, lng));
        console.log(Math.round(distance/1000));
        el.innerText = Math.round(distance/1000) + " km away";
        console.log({latitude, longitude})
      })
  }
function getCoordinates()
{
  navigator.geolocation.getCurrentPosition(function(location) {
    var lat = location.coords.latitude;
    var lng = location.coords.longitude;
    console.log("Location permission granted");
    console.log("lat: " + lat + " - lng: " + lng);

    for(let i=0;i<cards.children.length;i++){
        setDistance(cards.children[i].children[0].children[0].children[1], lat, lng);
    }
    
  },
  function(error) {
    console.log("Location permission denied");
  });
}


function initMap(){
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 56.461430, lng: -2.968110},
        zoom: 8
      });
      
}


function createDescription(id){
    return "<p>Opening hours: " + branches[id][4] + " </p> <p>Address: " + getFullAddress(id) + "</p>" //TODO proper formatting for SQL opening hours query
}

function getDistance(id){

}

getCoordinates();

displayProducts();


function displayProducts(){


    for(var key in branches) {
        //Key equivalent to ID in this case here
        let div = document.createElement("div");
        div.classList.add("col-xl-3", "col-lg-4", "col-md-6", "col-sm-12", "mb-5");

        let div1 = document.createElement("div");
        div1.classList.add("card");
        div1.style.width = "18rem";
        div1.style.height = "22rem";

        let div2 = document.createElement("div");
        div2.classList.add("card-body", "d-flex", "flex-column");

        let title = document.createElement("h5");
        title.classList.add("card-title");
        title.innerText = branches[key][5];
        let address = document.createElement("h6");
        address.classList.add("card-subtitle", "mb-2", "text-muted");
        address.innerText = "Please allow location to get the distance to the branch";
        address.setAttribute('id', key);
        
        let desc = document.createElement("p");
        desc.classList.add("card-text");
        desc.innerHTML = createDescription(key);
        
        let getDirection = document.createElement("a");
        getDirection.classList.add("btn", "btn-primary", "mt-auto");
        getDirection.innerText = "Get Directions";
        getDirection.setAttribute('id', key+"!");
        getDirection.addEventListener('click', function(event) { window.open("https://maps.google.com/?q="+ getFullAddress(event.target.id.replace("!", ""), false), "_blank"); });
        
        div2.appendChild(title);
        div2.appendChild(address);
        div2.appendChild(desc);
        div2.appendChild(getDirection);
        div1.appendChild(div2);
        div.appendChild(div1)
        cards.appendChild(div);

    }
}



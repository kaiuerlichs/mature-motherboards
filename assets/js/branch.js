
var branches = {
    "1":{
       "name":"Dundee",
       "address":{
          "addressName":"Mature Motherboards Dundee",
          "line1":"1 Law Rd",
          "line2":null,
          "line3":null,
          "town":"Dundee",
          "postcode":"DD36ET"
       },
       "hours":{
          "mon":[
             "11:00:00.000000",
             "18:00:00.000000"
          ],
          "tue":[
             "11:00:00.000000",
             "18:00:00.000000"
          ],
          "wed":[
             "10:00:00.000000",
             "15:00:00.000000"
          ],
          "thu":[
             "11:00:00.000000",
             "18:00:00.000000"
          ],
          "fri":[
             "10:00:00.000000",
             "15:00:00.000000"
          ]
       }
    },
    "2":{
       "name":"Stirling",
       "address":{
          "addressName":"Mature Motherboards Stirling",
          "line1":"2 Upper Castlehill",
          "line2":"Unit 2",
          "line3":null,
          "town":"Stirling",
          "postcode":"FK81FU"
       },
       "hours":{
          "mon":[
             "10:00:00.000000",
             "16:00:00.000000"
          ],
          "tue":[
             "10:00:00.000000",
             "16:00:00.000000"
          ],
          "wed":[
             "10:00:00.000000",
             "15:00:00.000000"
          ],
          "thu":[
             "10:00:00.000000",
             "16:00:00.000000"
          ],
          "fri":[
             "10:00:00.000000",
             "16:00:00.000000"
          ],
          "sat":[
             "10:00:00.000000",
             "15:00:00.000000"
          ],
          "sun":[
             "12:00:00.000000",
             "15:00:00.000000"
          ]
       }
    },
    "3":{
       "name":"St. Andrews",
       "address":{
          "addressName":"Mature Motherboards St. Andrews",
          "line1":"80 North St",
          "line2":null,
          "line3":null,
          "town":"St. Andrews",
          "postcode":"KY169AH"
       },
       "hours":{
          "tue":[
             "10:00:00.000000",
             "15:00:00.000000"
          ],
          "wed":[
             "10:00:00.000000",
             "15:00:00.000000"
          ],
          "fri":[
             "10:00:00.000000",
             "15:00:00.000000"
          ],
          "sat":[
             "10:00:00.000000",
             "13:00:00.000000"
          ]
       }
    }
 }; //This can be removed only done for testing

const days = {
    "mon" : "Monday",
    "tue" : "Tuesday",
    "wed" : "Wednesday",
    "thu" : "Thursday",
    "fri" : "Friday",
    "sat" : "Saturday",
    "sun" : "Sunday"
};

var cards = document.getElementById("containerCards");

API_KEY = "AIzaSyCRGzepAJM76Tvb_PZ2HqU-T4fbT8zJXuA"

//displayBranches(branches);
//getCoordinates();

fetch('./api/branch/GetAllBranches.php', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
    }
   })
   .then((response) => response.json())
   .then((data) => {

   displayBranches(data);
   getCoordinates();
   console.log(data)

})
    .catch((error) => {
        console.log(error);
});



function getFullAddress(id, forGoogle){

    if(forGoogle){
        return branches[Number(id)]["address"]["line1"] + " " + ((branches[Number(id)]["address"]["line2"] == null ? "" : branches[Number(id)]["address"]["line2"] + " ")) + ((branches[Number(id)]["address"]["line3"] == null ? "" : branches[Number(id)]["address"]["line3"] + " ")) + branches[Number(id)]["address"]["town"] +" " + branches[Number(id)]["address"]["postcode"] + "&components=country:GB";
    }
    if(typeof id === "number"){
        return  branches[id]["address"]["addressName"] + "<br>" + branches[id]["address"]["line1"] + "<br>" + ((branches[id]["address"]["line2"] == null ? "" : branches[id]["address"]["line2"] + "<br>")) + ((branches[id]["address"]["line3"] == null ? "" : branches[id]["address"]["line3"] + "<br>")) + branches[id]["address"]["postcode"] +" " + branches[id]["address"]["town"];
    }else{
        return  branches[Number(id)]["address"]["addressName"] + "<br>" + branches[Number(id)]["address"]["line1"] + "<br>" + ((branches[Number(id)]["address"]["line2"] == null ? "" : branches[Number(id)]["address"]["line2"] + "<br>")) + ((branches[Number(id)]["address"]["line3"] == null ? "" : branches[Number(id)]["address"]["line3"] + "<br>")) + branches[Number(id)]["address"]["postcode"] +" " + branches[Number(id)]["address"]["town"];
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





function createDescription(id){
    let desc = '<p style="font-size: 14px;">'+getFullAddress(id)+"</p>"
    desc += '<p style="font-size: 10px;">';
    let i = 0;
    for(hours in branches[id].hours){
        desc += days[hours] + " : " + branches[id].hours[hours][0].slice(0,5) + "-" + branches[id].hours[hours][1].slice(0,5) + "&emsp;"
        if(i % 2 == 1) desc += "<br>";
        i++;
    }
    return desc + "</p>";
}


function displayBranches(data){
    //data = branches; //remove when in use

    for(var key in data) {
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
        title.innerText = data[key]["name"]; //Either Name or data[key]
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
        getDirection.addEventListener('click', function(event) { window.open("https://maps.google.com/?q="+ getFullAddress(event.target.id.replace("!", ""), true), "_blank"); });
        
        div2.appendChild(title);
        div2.appendChild(address);
        div2.appendChild(desc);
        div2.appendChild(getDirection);
        div1.appendChild(div2);
        div.appendChild(div1);
        cards.appendChild(div);

    }
}



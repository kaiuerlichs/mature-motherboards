function main() {
  let url = "./api/shifts/.php" // ADD PHP

  //clears the container
  document.getElementById('repair').innerHTML = "";
  const shiftContainer = document.getElementById('repair');
  //creates shiftSearch variable to the users input
  let shiftSearch = document.getElementById('repair').value;

  fetch(url)
    .then((resp) => resp.json())
    .then(function(value) {
      document.getElementById("spinner").remove()

      value.map(function(repair) {
        shiftContainer.innerHTML += "<tr><td>" + repair.customerNumber + "</td><td>" + repair.time + "</td><td>" + repair.duration + "</td><td>" + repair.description + "</td><tr>"
      })
    })
    .catch(function(error) {
      console.log(error);
    });
}

main()
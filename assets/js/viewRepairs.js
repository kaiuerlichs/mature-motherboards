function main() {
  let url = "./api/repairs/GetRepairsByBranch.php" // ADD PHP

  //clears the container
  document.getElementById('repair').innerHTML = "";
  const shiftContainer = document.getElementById('repair');
  //creates shiftSearch variable to the users input
  let shiftSearch = document.getElementById('repair').value;

  fetch(url)
    .then((resp) => resp.json())
    .then(function(value) {
      document.getElementById("spinnerRepairs").remove()

      value.map(function(repair) {
        shiftContainer.innerHTML += "<tr><td>" + repair.Email + "</td><td>" + repair.Time + "</td><td>" + repair.Duration + "</td><td>" + repair.Description + "</td><tr>"
      })
    })
    .catch(function(error) {
      console.log(error);
    });
}

main()
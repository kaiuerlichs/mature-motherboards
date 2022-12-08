function main() {
  let url = "./api/repairs/GetRepairsByBranch.php" // ADD PHP
  let status = "";

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
        if (repair.Status == 0) {
          status = "Scheduled";
        } else if (repair.Status == 1) {
          status = "In progress";
        } else {
          status = "Complete";
        }

        shiftContainer.innerHTML += "<tr><td>" + repair.Email + "</td><td>" + repair.Time + "</td><td>" + repair.Duration + "</td><td>" + repair.Description + "</td><td>" + status + "</td><td>" + "<select class='form-control update' repairid='" + repair.RepairID + "'><option>Update Status</option><option>Scheduled</option><option>In Progress</option><option>Complete</option></select>" + "</td><tr>"
      })
    })
    .catch(function(error) {
      console.log(error);
    });
}

main()

Array.from(document.getElementsByClassName("update")).forEach(el =>{
  el.addEventListener("change", function(){
    let RepairID = el.getAttribute("repairid")
    console.log(repairID)
  }
  )
})
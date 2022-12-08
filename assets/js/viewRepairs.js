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
    .then(function (value) {
      document.getElementById("spinnerRepairs").remove()

      value.map(function (repair) {
        if (repair.Status == 0) {
          status = "Scheduled";
        } else if (repair.Status == 1) {
          status = "In progress";
        } else {
          status = "Complete";
        }

        shiftContainer.innerHTML += "<tr><td>" + repair.Email + "</td><td>" + repair.Time + "</td><td>" + repair.Duration + "</td><td>" + repair.Description + "</td><td id='repair" + repair.RepairID + "'>" + status + "</td><td>" + "<select class='form-control update' repairid='" + repair.RepairID + "'><option selected disabled>Update Status</option><option value='0'>Scheduled</option><option value='1'>In Progress</option><option value='2'>Complete</option></select>" + "</td><tr>"

        Array.from(document.getElementsByClassName("update")).forEach(el => {
          el.addEventListener("change", function () {
            const data = {
              id: el.getAttribute("repairid"),
              status: el.value
            };

            document.getElementById("repair" + el.getAttribute("repairid")).innerHTML = '<i class="fa-solid fa-gear fa-spin"></i>'

            let selected = el.options[el.selectedIndex].text

            fetch('./api/repairs/UpdateStatus.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify(data),
            })
              .then((response) => response.json())
              .then((data) => {
                if(data["code"] == 0){
                  document.getElementById("repair" + el.getAttribute("repairid")).innerHTML = selected
                }
              })
              .catch((error) => {
                document.getElementById("repair" + el.getAttribute("repairid")).innerHTML = "Update error."
              });
          })
        })
      })
    })
    .catch(function (error) {
      console.log(error);
    });
}

main()
document.getElementById("selectEmployee").addEventListener("change", function () {
  Array.from(document.getElementsByClassName("shift")).forEach(el => {
    if(el.getAttribute("employee") == this.value){
      el.classList.remove("d-none")
    }
    else{
      el.classList.add("d-none")
    }
  })
})

function main() {
  let url = "./api/shifts/GetAllShifts.php"

  const shiftContainer = document.getElementById('shift');

  fetch(url)
    .then((resp) => resp.json())
    .then(function (value) {
      let employees = {}
      value.forEach(shift => {
        if (!employees.hasOwnProperty(shift.FirstName + " " + shift.LastName)) {
          employees[shift.FirstName + " " + shift.LastName] = shift.EmployeeID
        }
      });

      let select = document.getElementById("selectEmployee")
      let select2 = document.getElementById("selectEmployeeAdd")
      Object.entries(employees).forEach(([key, value]) => {
        select.innerHTML += '<option value="' + value + '">' + key + '</option>'
        select2.innerHTML += '<option value="' + value + '">' + key + '</option>'
      })

      document.getElementById("disabledOption").innerHTML = "Please select..."
      document.getElementById("disabledOptionAdd").innerHTML = "Please select..."

      value.map(function (shift) {
        shiftContainer.innerHTML += "<tr class='d-none shift' employee='"+ shift.EmployeeID + "'><td>" + shift.Start + "</td><td>" + shift.End + "</td><tr>"
      })
    })
    .catch(function (error) {
      console.log(error);
    });
}

main()
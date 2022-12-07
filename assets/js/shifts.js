function main() {
  let url = "./api/shifts/GetShifts.php"

  //clears the container
  document.getElementById('shift').innerHTML = "";
  const shiftContainer = document.getElementById('shift');
  //creates shiftSearch variable to the users input
  let shiftSearch = document.getElementById('shift').value;

  fetch(url)
    .then((resp) => resp.json())
    .then(function(value) {
      console.log(value)
      document.getElementById("spinner").remove()

      value.map(function(shift) {
        shiftContainer.innerHTML += "<tr><td>" + shift.Start + "</td><td>" + shift.End + "</td><tr>"
      })
    })
    .catch(function(error) {
      console.log(error);
    });
}

main()
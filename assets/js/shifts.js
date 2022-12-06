function main() {
  //clears the container
  document.getElementById('shift').innerHTML = "";
  const shiftContainer = document.getElementById('shift');
  //creates shiftSearch variable to the users input
  let shiftSearch = document.getElementById('shift').value;

  fetch(url)
    .then((resp) => resp.json())
    .then(function(value) {
      var shift = value.Search;
      shift.map(function(shift) {

        var tr = createNode('tr');
        var th = createNode('th');
        var timeStart = createNode('timeStart');
        var timeEnd = createNode('timeEnd');
        
        timeStart.innerHTML = shift.timeStart;
        timeEnd.innerHTML = shift.timeEnd;

        append(th, tr);
        append(timeStart, tr);
        append(timeEnd, tr);
        append(shiftContainer, tr);
      })
    })
    .catch(function(error) {
      console.log(error);
    });
}

main()
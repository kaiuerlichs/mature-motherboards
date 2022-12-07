document.getElementById("lookupButton").addEventListener("click", () => {
    let url = "./api/employee/GetAllEmployees.php"
    let container = document.getElementById("employeeAccordion")

    fetch(url)
        .then((resp) => resp.json())
        .then(function (data) {
            document.getElementById("spinnerLookup").classList.add("d-none")
            document.getElementById("employeeList").classList.remove("d-none")

            data.sort(function(a,b){
                var x = a["BranchID"]; var y = b["BranchID"];
                return ((x < y) ? -1 : ((x > y) ? 1 : 0));
            })

            data.forEach(employee => {
                let html = 
                    '<div class="card branch' + employee.BranchID + '"><div class="card-header" id="heading' + employee.EmployeeID + '"><h5 class="mb-0"><button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse' + employee.EmployeeID + '" aria-expanded="false" aria-controls="collapse' + employee.EmployeeID + '">' 
                    + employee.FirstName + " " + employee.LastName + " (Branch " + employee.BranchID + ")"
                    + '</button></h5></div><div id="collapse' + employee.EmployeeID + '" class="collapse" aria-labelledby="heading' + employee.EmployeeID + '" data-parent="#accordion"><div class="card-body">'
                    + '<p><span class="font-weight-bold">Employee ID: </span>' + employee.EmployeeID + '</p>'
                    + '<p><span class="font-weight-bold">Email: </span>' + employee.Email + '</p>'
                    + '<p><span class="font-weight-bold">Phone: </span>' + employee.Phone + '</p>'
                    + '</div></div></div>'
                
                container.innerHTML += html;
            });
        })
        .catch(function (error) {
            console.log(error);
        });
})

document.getElementById("checkDundee").addEventListener("change", function() {
    if(this.checked) {
        Array.from(document.getElementsByClassName("branch1")).forEach(el => {
            el.classList.remove("d-none")
        })
    }
    else{
        Array.from(document.getElementsByClassName("branch1")).forEach(el => {
            el.classList.add("d-none")
        })
    }
})

document.getElementById("checkStirling").addEventListener("change", function() {
    if(this.checked) {
        Array.from(document.getElementsByClassName("branch2")).forEach(el => {
            el.classList.remove("d-none")
        })
    }
    else{
        Array.from(document.getElementsByClassName("branch2")).forEach(el => {
            el.classList.add("d-none")
        })
    }
})

document.getElementById("checkStAndrews").addEventListener("change", function() {
    if(this.checked) {
        Array.from(document.getElementsByClassName("branch3")).forEach(el => {
            el.classList.remove("d-none")
        })
    }
    else{
        Array.from(document.getElementsByClassName("branch3")).forEach(el => {
            el.classList.add("d-none")
        })
    }
})

document.getElementById("closeButton").addEventListener("click", () => {
    document.getElementById("spinnerLookup").classList.remove("d-none")
    document.getElementById("employeeList").classList.add("d-none")
    document.getElementById("employeeAccordion").innerHTML = ""
})
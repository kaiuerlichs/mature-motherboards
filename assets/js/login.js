function login() {
    document.getElementById("login").innerHTML = '<i class="fa-solid fa-gear" id="spinner"></i>'
    let spinner = document.getElementById("spinner")
    spinner.classList.add("fa-spin")

    let emailValue = document.getElementById("email").value
    let passwordValue = document.getElementById("password").value

    const data = {
        email: emailValue,
        password: passwordValue
    };

    fetch('./api/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            spinner.classList.remove("fa-spin")
            if (data.code == 101) {
                console.log(data["redirectUrl"])
                window.location.href = data.redirectUrl;
            } else {
                document.getElementById("errorText").innerHTML = data.error;
                document.getElementById("login").innerHTML = 'Log In';
            }
        })
        .catch((error) => {
            spinner.classList.remove("fa-spin")
            document.getElementById("errorText").innerHTML = "Error when logging in.";
            document.getElementById("login").innerHTML = 'Log In';
        });
}
function logOut() {
    
    document.getElementById("logout").innerHTML = '<i class="fa-solid fa-gear" id="spinner"></i>'
    let spinner = document.getElementById("spinner")
    spinner.classList.add("fa-spin")
    
    const data = {};

    fetch('./api/logout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            spinner.classList.remove("fa-spin")
            if (data.code == 201) {
                window.location.href = "index.html";
            } else {
                document.getElementById("logout").innerHTML = data.error;
                document.getElementById("logout").innerHTML = 'Log Out';
            }
        })
        .catch((error) => {
            spinner.classList.remove("fa-spin")
            document.getElementById("logout").innerHTML = "Error when logging out.";
            document.getElementById("logout").innerHTML = 'Log Out';
        });
}

fetch("./api/redirect.php")
.then((response) => response.json())
.then((data) => {

    if (data.code===201 && !window.location.toString().includes(data.redirectUrl.split("/")[1])){
        window.location.href = data.redirectUrl;
    }else if (data.code===200 &&!window.location.toString().includes("login_page.html")){
        window.location.href = "index.html";
    }
})

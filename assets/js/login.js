function login() {
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
            }
        })
        .catch((error) => {
            spinner.classList.remove("fa-spin")
            document.getElementById("errorText").innerHTML = "Error when logging in.";
        });

}
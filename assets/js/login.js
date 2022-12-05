function login() {
    {
        let emailValue = document.getElementById("email").value
        let passwordValue = document.getElementById("password").value

        const data = {
            email: emailValue,
            password: passwordValue
        };

        fetch('api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.code == 101) {
                    window.location.href = data.redirectURL;
                } else {
                    document.getElementById("errorText").innerHTML = data.error;
                }
                console.log('Success:', data);
            })
            .catch((error) => {
                document.getElementById("errorText").innerHTML = "Error when POSTing";
                console.error('Error:', error);
            });
    }
}
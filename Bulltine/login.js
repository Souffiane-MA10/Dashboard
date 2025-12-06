function login() {
    let username = document.getElementById("username").value.trim();
    let password = document.getElementById("password").value.trim();
    let error = document.getElementById("error");


    if (username === "" || password === "") {
        error.textContent = "Veuillez remplir tous les champs !";
        return;
    }

    if (username === "admin" && password === "1234") {

        
        localStorage.setItem("username", username);


        window.location.href = "dashboard.html";

    } else {
        error.textContent = "Nom ou mot de passe incorrect !";
    }
}

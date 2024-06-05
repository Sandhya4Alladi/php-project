const logobtn = document.getElementById("streambox-logo");
logobtn.addEventListener("click", function () {
    fetch("/video/home", {
        method: "get",
    })
    .then((response) => {
        if (response.ok) {
            window.location.href = "/video/home";
        } else {
            console.error("Error fetching data:", response.status);
        }
    })
    .catch((error) => {
        console.error("Fetch error:", error);
    });
    
});
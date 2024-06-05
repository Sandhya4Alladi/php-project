function display(data) {
  const n = data.length;
  if (n === 0) {
    document.getElementById("videos").innerHTML = "No Videos";
  } else {
    for (let i = 0; i < n; i++) {
      const card = document.createElement("div");
      card.classList.add("card");

      const overlay = document.createElement("div");
      overlay.classList.add("overlay");

      const key = data[i].videoKey;
      
      const a = document.createElement("a");
      const id = data[i]._id.$oid;
      a.href =  `/video/playvideo?data=${key}&id=${id}`

      card.appendChild(overlay);
      const title = document.createElement("button")
      title.innerText = data[i].title;
      title.className = "btn btn-primary";
      title.id = "video-button";
      title.style.width = "100%"; 

      a.appendChild(title);

      a.addEventListener("click", function () {
        console.log("hi")

        fetch("/video/addview?id=" + id, {
          method: "PUT",
        }).then(
          console.log("hi")
        );
      });

      const imgElement = document.createElement("img");
      imgElement.src = `https://d2fpvsof67xqc9.cloudfront.net/${data[i].imgKey}`;
      card.appendChild(imgElement);
      card.appendChild(a);

      document.getElementById("videos").appendChild(card);
    }
  }
}

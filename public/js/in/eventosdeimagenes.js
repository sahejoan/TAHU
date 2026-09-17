function doClick() {
  var el = document.getElementById("foto");
  if (el) {
    el.click();
  }
}

////////////////////////////////////////////////////////////

function handleFiles(files) {
  var d = document.getElementById("fileList");
  if (!files.length) {
    d.innerHTML = "<p>Imagen no seleccionada!</p>";
  } else {                
    removeAllChilds('fileList');
    for (var i=0; i < files.length; i++) {          
      var img = document.createElement("img");
      img.src = window.URL.createObjectURL(files[i]);;
      img.height = 150;
      img.width = 245;
      img.onload = function() {
        window.URL.revokeObjectURL(this.src);
      }
      d.appendChild(img);          
      //var info = document.createElement("span");
      //info.innerHTML = files[i].name + ": " + files[i].size + " bytes";
      //li.appendChild(info);
    }
  }
}

///////////////////////////////////////////////////////////

function removeAllChilds(a) {
  var a=document.getElementById(a);
  while(a.hasChildNodes())
  a.removeChild(a.firstChild);  
}    

function valueSelect() {
  let x = document.getElementById("liste").value;
  let y = document.getElementById("link");
  y.setAttribute("href", "user/update/" + x);
}
function valueSelect2() {
  let x = document.getElementById("liste2").value;
  let y = document.getElementById("link2");
  y.setAttribute("href", "coach/update/" + x);
}
function valueSelect3() {
  let x = document.getElementById("liste3").value;
  let y = document.getElementById("link3");
  y.setAttribute("href", "produit/edit/" + x);
}
function valueSelect4() {
  let x = document.getElementById("liste4").value;
  let y = document.getElementById("link4");
  y.setAttribute("href", "blog/edit/" + x);
}
function valueSelect5() {
  let x = document.getElementById("liste5").value;
  let y = document.getElementById("link5");
  y.setAttribute("href", "parties_blog/new/" + x);
}


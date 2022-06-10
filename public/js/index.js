let burger = document.getElementById('clickjs');
let absolute = document.querySelector('.abso')


burger.addEventListener("click", () => {
    absolute.classList.toggle('hidden');
});


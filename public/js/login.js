const inputs = document.querySelectorAll(".input");

function addFocus() {
    const parent = this.closest(".input-div");
    parent.classList.add("focus");
}

function removeFocus() {
    const parent = this.closest(".input-div");
    if (this.value === "") {
        parent.classList.remove("focus");
    }
}

inputs.forEach(input => {
    input.addEventListener("focus", addFocus);
    input.addEventListener("blur", removeFocus);
});

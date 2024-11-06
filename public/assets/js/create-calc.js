const typeOfPrice = document.getElementById("typeOfPrice");

if (typeOfPrice.value !== "pax") {
    document.getElementById("subsPrice") &&
        document.getElementById("subsPrice").remove();
}
if (typeOfPrice.value !== "min_pp") {
    document.getElementById("percentage") &&
        document.getElementById("percentage").remove();
    document.getElementById("minPrice") &&
        document.getElementById("minPrice").remove();
    $("#price .help-block").hide();
}

typeOfPrice.addEventListener("input", (e) => {
    if (e.target.value === "pax") {
        let html = `
            <label>Subsequent Price</label>
            <input type="number" name="subsequent_price" value="" class="form-control">
        `;

        const el = document.createElement("div");
        el.setAttribute("id", "subsPrice");
        el.setAttribute("class", "form-group col-sm-12");
        el.setAttribute("element", "div");
        el.innerHTML = html;
        e.target.parentElement.parentElement.appendChild(el);
    } else {
        document.getElementById("subsPrice") &&
            document.getElementById("subsPrice").remove();
    }
    if (e.target.value === "min_pp") {
        $("#price .help-block").show();

        let html = `
            <label>Percentage</label>
            <input type="number" name="percentage" value="" class="form-control">
        `;

        let el = document.createElement("div");
        el.setAttribute("id", "percentage");
        el.setAttribute("class", "form-group col-sm-12");
        el.setAttribute("element", "div");
        el.innerHTML = html;
        e.target.parentElement.parentElement.appendChild(el);

        html = `
            <label>Minimum Price</label>
            <input type="number" name="min_price" value="" class="form-control">
        `;

        el = document.createElement("div");
        el.setAttribute("id", "minPrice");
        el.setAttribute("class", "form-group col-sm-12");
        el.setAttribute("element", "div");
        el.innerHTML = html;
        e.target.parentElement.parentElement.appendChild(el);
    } else {
        $("#price .help-block").hide();
        document.getElementById("percentage") &&
            document.getElementById("percentage").remove();
        document.getElementById("minPrice") &&
            document.getElementById("minPrice").remove();
    }
});


document.getElementById("addCourse").addEventListener("click", function () {

    let tableBody = document.getElementById("courseBody");

    let newRow = document.createElement("tr");

    newRow.innerHTML = `
    <td><input type="text" class="form-control" placeholder="e.g. Course"></td>
    <td><input type="number" class="form-control credits" placeholder="e.g. 3"></td>
    <td>
      <select class="form-select grade">
        <option value="4">A</option>
        <option value="3">B</option>
        <option value="2">C</option>
        <option value="1">D</option>
        <option value="0">F</option>
      </select>
    </td>
    <td><button class="btn btn-danger remove">Remove</button></td>
  `;

    tableBody.appendChild(newRow);
});

document.addEventListener("click", function (e) {

    if (e.target.classList.contains("remove")) {

        let row = e.target.closest("tr");

        // يمنع حذف آخر صف (اختياري لكن احترافي)
        let rows = document.querySelectorAll("#courseBody tr");

        if (rows.length > 1) {
            row.remove();
        } else {
            alert("You must have at least one course!");
        }
    }

});
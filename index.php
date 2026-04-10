<?php
$conn = new mysqli("localhost", "root", "", "gpa_db");

/* ===== AJAX SAVE ===== */
if(isset($_GET['action']) && $_GET['action']=="save"){
    $data = json_decode(file_get_contents("php://input"), true);

    $conn->query("INSERT INTO gpa_records 
    (student_name, semester, courses, credits, grades, gpa) 
    VALUES (
    '".$data['name']."',
    '".$data['semester']."',
    '".json_encode($data['courses'])."',
    '".json_encode($data['credits'])."',
    '".json_encode($data['grades'])."',
    '".$data['gpa']."'
    )");
    exit;
}

/* ===== LOAD HISTORY ===== */
if(isset($_GET['action']) && $_GET['action']=="history"){
    $res = $conn->query("SELECT * FROM gpa_records ORDER BY id DESC");
    while($row = $res->fetch_assoc()){
        echo "<div class='card p-2 mb-2 shadow-sm'>";
        echo "<b>".$row['student_name']."</b> - ".$row['semester'];
        echo "<br> GPA: ".$row['gpa'];
        echo "</div>";
    }
    exit;
}

/* ===== CSV EXPORT ===== */
if(isset($_GET['action']) && $_GET['action']=="csv"){
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="gpa.csv"');

    $out = fopen("php://output","w");
    fputcsv($out, ["Name","Semester","GPA"]);

    $res = $conn->query("SELECT * FROM gpa_records");
    while($row = $res->fetch_assoc()){
        fputcsv($out, [$row['student_name'],$row['semester'],$row['gpa']]);
    }
    fclose($out);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>GPA Professional</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: #f4f5f7;
    font-family: 'Segoe UI', sans-serif;
}
.card{
    border-radius:12px;
}
h3{
    color:#1f2937;
    font-weight:bold;
}
.btn-primary{
    background:#1f2937;
    border:none;
}
.btn-primary:hover{
    background:#111827;
}
.btn-secondary{
    background:#6b7280;
    border:none;
    color:white;
}
.btn-secondary:hover{
    background:#4b5563;
}
.btn-success{
    background:#10b981;
    border:none;
}
.btn-success:hover{
    background:#059669;
}
input, select{
    border-radius:6px !important;
}
.progress{
    height:25px;
    border-radius:12px;
    overflow:hidden;
    margin-top:10px;
}
.progress-bar{
    font-weight:bold;
}
#history .card{
    background:#e5e7eb;
}
.table td{
    vertical-align: middle;
}
.remove-btn{
    background:#ef4444;
    border:none;
    color:white;
    border-radius:6px;
}
.remove-btn:hover{
    background:#b91c1c;
}
</style>
</head>

<body class="p-4">

<div class="container">
<div class="card p-4 shadow">

<h3>GPA Calculator</h3>

<div class="row mb-2">
    <div class="col">
        <input id="name" class="form-control" placeholder="Student Name">
    </div>
    <div class="col">
        <input id="semester" class="form-control" placeholder="Semester">
    </div>
</div>

<table class="table" id="table">
<tr><th>Course</th><th>Credit</th><th>Grade</th><th>Action</th></tr>
<tr>
<td><input class="form-control course" placeholder="Course name"></td>
<td><input class="form-control credit" type="number"></td>
<td>
<select class="form-control grade">
<option value="4">A</option>
<option value="3">B</option>
<option value="2">C</option>
<option value="1">D</option>
<option value="0">F</option>
</select>
</td>
<td><button class="remove-btn" onclick="removeRow(this)">Delete</button></td>
</tr>
</table>

<div class="mb-2">
<button class="btn btn-secondary" onclick="addRow()">Add Course</button>
<button class="btn btn-primary" onclick="calc()">Calculate & Save</button>
<button class="btn btn-success" onclick="csv()">Export CSV</button>
</div>

<h4 id="result" class="text-center"></h4>

<div class="progress">
<div id="bar" class="progress-bar"></div>
</div>

</div>

<h4 class="mt-4 text-center">History</h4>
<div id="history"></div>

</div>

<script>
function addRow(){
    let row = `
    <tr>
    <td><input class="form-control course" placeholder="Course name"></td>
    <td><input class="form-control credit" type="number"></td>
    <td>
    <select class="form-control grade">
    <option value="4">A</option>
    <option value="3">B</option>
    <option value="2">C</option>
    <option value="1">D</option>
    <option value="0">F</option>
    </select>
    </td>
    <td><button class="remove-btn" onclick="removeRow(this)">Delete</button></td>
    </tr>`;
    document.getElementById("table").innerHTML += row;
}

function removeRow(btn){
    let row = btn.closest("tr");
    row.remove();
}

function calc(){
    let name = document.getElementById("name").value;
    let semester = document.getElementById("semester").value;

    if(name=="" || semester=="") return alert("Enter name and semester");

    let courses = document.querySelectorAll(".course");
    let credits = document.querySelectorAll(".credit");
    let grades = document.querySelectorAll(".grade");

    let totalC=0,totalP=0,names=[];

    for(let i=0;i<courses.length;i++){
        if(courses[i].value=="") return alert("Enter course name");
        if(names.includes(courses[i].value)) return alert("Duplicate course!");
        names.push(courses[i].value);

        let c=parseFloat(credits[i].value);
        let g=parseFloat(grades[i].value);

        if(c>5) return alert("Max credit 5");

        totalC+=c;
        totalP+=c*g;
    }

    let gpa=(totalP/totalC).toFixed(2);
    document.getElementById("result").innerText="GPA: "+gpa;

    let bar=document.getElementById("bar");
    bar.style.width=(gpa/4*100)+"%";
    if(gpa>=3.5) bar.className="progress-bar bg-success";
    else if(gpa>=3) bar.className="progress-bar bg-primary";
    else if(gpa>=2) bar.className="progress-bar bg-warning";
    else bar.className="progress-bar bg-danger";

    fetch("index.php?action=save",{
        method:"POST",
        body: JSON.stringify({
            name:name,
            semester:semester,
            courses:[...courses].map(e=>e.value),
            credits:[...credits].map(e=>e.value),
            grades:[...grades].map(e=>e.value),
            gpa:gpa
        })
    }).then(()=>load());
}

function load(){
    fetch("index.php?action=history")
    .then(res=>res.text())
    .then(data=>document.getElementById("history").innerHTML=data);
}

function csv(){
    window.location="index.php?action=csv";
}

load();
</script>

</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GPA Calculator</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>GPA Calculator</h1>

<div class="container">

  <form method="POST">

    <table class="table table-bordered text-center align-middle">
      <thead class="table-light">
        <tr>
          <th>Course</th>
          <th>Credits</th>
          <th>Grade</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody id="courseBody">
        <tr>
          <td><input type="text" class="form-control" placeholder="e.g. Math"></td>

          <td>
            <input type="number" name="credits[]" class="form-control credits" placeholder="3">
          </td>

          <td>
            <select name="grade[]" class="form-select grade">
              <option value="4">A</option>
              <option value="3">B</option>
              <option value="2">C</option>
              <option value="1">D</option>
              <option value="0">F</option>
            </select>
          </td>

          <td>
            <button type="button" class="btn btn-danger remove">Remove</button>
          </td>
        </tr>
      </tbody>
    </table>

    <button type="button" id="addCourse" class="btn">+ Add Course</button>

    <button type="submit" name="calculateBtn" class="btn btn-primary w-100 mt-3">
      Calculate GPA
    </button>

  </form>

  
  <div class="mt-3">
  
    <?php
if (isset($_POST['calculateBtn'])) {

  $credits = $_POST['credits'];
  $grades = $_POST['grade'];

  $totalCredits = 0;
  $totalPoints = 0;

  for ($i = 0; $i < count($credits); $i++) {

    $credit = floatval($credits[$i]);
    $grade = floatval($grades[$i]);

    if ($credit > 0) {
      $totalCredits += $credit;
      $totalPoints += $credit * $grade;
    }
  }

  if ($totalCredits > 0) {
    $gpa = round($totalPoints / $totalCredits, 2);

    // 🎯 تحديد التقييم
    if ($gpa >= 3.5) {
      $status = "Excellent ⭐";
      $class = "good";
    } elseif ($gpa >= 2.5) {
      $status = "Good 👍";
      $class = "good";
    } elseif ($gpa >= 2) {
      $status = "Average ";
      $class = "average";
    } else {
      $status = "Poor ❌";
      $class = "poor";
    }

    echo "<div class='result-box $class'>
            🎓 GPA: $gpa <br>
            📊 Status: $status
          </div>";
  }
}
?>
  </div>

</div>

<script src="script.js"></script>

</body>
</html>
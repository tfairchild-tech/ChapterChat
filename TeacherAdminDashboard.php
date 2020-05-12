<?php
$teacherid = $_SESSION["teacherid"];

include('db_connect.php');
$call = mysqli_prepare($conn, 'CALL `ReadEdu`.`GetAllBooksAndQuizzes`();');
mysqli_stmt_execute($call);

$result = $call -> get_result();
$row_count = $result->num_rows;

echo '
        <div class="table-responsive">
        <table id="quizDatatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%;">
                <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Book<br>Level</th>
                    <th>Num<br>Ques</th>
                    <th>Quiz Action</th>
                </tr>
                </thead>
                <tbody>
        ';

        while ($row = $result->fetch_assoc()) {
            $bookid = $row["bookId"];
            $booktitle = $row["bookTitle"];
            $booklevel = $row["BookLevel"];
            $genre = $row["Genre"];
            $possiblepoints = $row["possiblePoints"];
            $picPath = $row["picPath"];
            $authorname = $row["authorname"];
            $isbn = $row["ISBN"];
            $quizid = $row["BookQuizId"];
            $numquestions = $row["numOfQuestions"];
            $ispractice = $row["IsPractice"];
            if ($ispractice) {
               $extra=" <span class=\"label label-success\">practice</span>";
            } else {
               $extra = "";
            }

            echo ' <tr><td>' . $booktitle . $extra . '</td><td>' . $authorname . '</td><td>' . $booklevel . '</td><td>' . $numquestions . '</td>
            <td>
                <button type="button" class="btn btn-info btn-xs"><i class="fa fa-user"></i> view</button>
                <button type="button" class="btn btn-info btn-xs"><i class="fa fa-pencil-square"></i> update</button>
                <button type="button" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i> delete</button>
            </td>
        </tr>
        ';

        }

        echo '</tbody></table></div>
        ';

 ?>


<script>

    $('#quizDatatable').dataTable( {
        "dom": '<"pull-right"f><"pull-left"l>tip',
        "paging": true
    } );

</script>

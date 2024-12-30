<?php

include_once '../config/db.php';
include_once '../classes/book.php';
session_start();
if(!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin')){
    header('Location: index.php');
    exit;
}
$database = new DataBase();
$conn = $database->getConnection();

$book = new Book($conn);
// echo json_encode($book->getBookStatistics());
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Statistics Chart</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.debug.js"></script>
</head>
<body>
    <div id="reportPage">
        <canvas id="myChart"></canvas>
        <h1>Total Books: <span id="tot"></span></h1>
        <h1>Borrowed Books: <span id="bor"></span></h1>
    </div>
    <button id="downloadPdf">Download PDF</button>

    <script>
       const stats = <?= json_encode($book->getBookStatistics()); ?>;
       document.querySelector("#tot").textContent = stats.total_books;
       document.querySelector("#bor").textContent = stats.total_borrowings;
       const data = {
                    labels: Object.keys(stats),
                    datasets: [{
                        label: 'Book Statistics',
                        backgroundColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 205, 86)'
                        ],
                        data: Object.values(stats)
                    }]
                };

                const config = {
                    type: 'pie',
                    data: data,
                    options: {
                        responsive: true,
                        title: { display: true, text: "Book Statistics" }
                    }
                };

                new Chart(document.getElementById("myChart"), config);

               // Replace the existing PDF generation code with this:
$('#downloadPdf').click(function(event) {
    var reportPageHeight = $('#reportPage').innerHeight();
    var reportPageWidth = $('#reportPage').innerWidth();
    
    var pdfCanvas = $('<canvas />').attr({
        id: "canvaspdf",
        width: reportPageWidth,
        height: reportPageHeight
    });
    
    var pdfctx = $(pdfCanvas)[0].getContext('2d');
    
    var chartCanvas = $("#myChart")[0];
    pdfctx.drawImage(chartCanvas, 0, 0, chartCanvas.width, chartCanvas.height);
    
    pdfctx.font = "24px Arial";
    pdfctx.fillStyle = "#000000";
    
    var totalText = "Total Books: " + $("#tot").text();
    pdfctx.fillText(totalText, 50, chartCanvas.height + 50);
    
    var borrowedText = "Borrowed Books: " + $("#bor").text();
    pdfctx.fillText(borrowedText, 50, chartCanvas.height + 100);
    
    var pdf = new jsPDF('l', 'pt', [reportPageWidth, reportPageHeight]);
    pdf.addImage($(pdfCanvas)[0], 'PNG', 0, 0);
    pdf.save('library_statistics.pdf');
});
//$('#downloadPdf').click();
//document.location = '../admin/';
    </script>
</body>
</html></body></head>
<?php
require(__DIR__ . '/fpdf/fpdf.php');
include 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user']) || !isset($_SESSION['payment'])){
    die("No ticket data available");
}

$user = $_SESSION['user']['name'];
$event_id = $_SESSION['payment']['event_id'];
$amount = $_SESSION['payment']['amount'];
$persons = $_SESSION['payment']['persons'] ?? 1;

// FETCH EVENT
$res = $conn->query("SELECT * FROM events WHERE id=$event_id");
$row = $res->fetch_assoc();

$event = $row['title'];
$date = $row['date'];
$location = $row['location'];

// 🎫 TICKET ID
$ticket_id = "EVT" . rand(10000,99999);

// CREATE PDF
$pdf = new FPDF();
$pdf->AddPage();


// 🌈 FULL PAGE GRADIENT BORDER (ADVANCED LOOK)
for($i=0; $i<5; $i++){
    $pdf->SetDrawColor(255-$i*20, 50+$i*20, 150+$i*10);
    $pdf->Rect(5+$i,5+$i,200-($i*2),287-($i*2));
}


// 🌈 GRADIENT HEADER
for($i=0; $i<40; $i++){
    $pdf->SetFillColor(255 - $i*2, 80, 120 + $i);
    $pdf->Rect(0, $i, 210, 1, 'F');
}

// TITLE
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',20);
$pdf->SetXY(10,12);
$pdf->Cell(0,10,'EVENT TICKET',0,1);

// SUBTITLE
$pdf->SetFont('Arial','',10);
$pdf->SetXY(10,20);
$pdf->Cell(0,10,'Premium Entry Pass',0,1);

// RESET COLOR
$pdf->SetTextColor(0,0,0);


// 📦 MAIN CARD
$pdf->SetFillColor(245,245,245);
$pdf->Rect(15,55,180,120,'F');

// BORDER
$pdf->SetDrawColor(200,200,200);
$pdf->Rect(15,55,180,120);


// 🎉 EVENT NAME
$pdf->SetFont('Arial','B',16);
$pdf->SetXY(25,65);
$pdf->Cell(0,10,$event);


// 📋 DETAILS
$pdf->SetFont('Arial','',12);

$pdf->SetXY(25,80);
$pdf->Cell(0,8,"Name: $user");

$pdf->SetXY(25,90);
$pdf->Cell(0,8,"Date: $date");

$pdf->SetXY(25,100);
$pdf->Cell(0,8,"Location: $location");

$pdf->SetXY(25,110);
$pdf->Cell(0,8,"Persons: $persons");

$pdf->SetXY(25,120);
$pdf->Cell(0,8,"Amount Paid: Rs. $amount");


// 🎫 TICKET ID BOX
$pdf->SetFillColor(255,64,108);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',12);
$pdf->SetXY(25,135);
$pdf->Cell(80,10,"Ticket ID: $ticket_id",0,0,'C',true);

// RESET COLOR
$pdf->SetTextColor(0,0,0);


// ✂️ TEAR LINE
for($i=15;$i<195;$i+=6){
    $pdf->Line($i,150,$i+3,150);
}


// 🎟️ RIGHT SIDE PASS
$pdf->SetFont('Arial','B',12);
$pdf->SetXY(135,70);
$pdf->Cell(50,10,'ENTRY PASS',0,1,'C');

$pdf->SetFont('Arial','',10);
$pdf->SetXY(135,85);
$pdf->MultiCell(50,6,$event,0,'C');


// 🟢 STATUS
$pdf->SetTextColor(0,150,0);
$pdf->SetFont('Arial','B',12);
$pdf->SetXY(135,110);
$pdf->Cell(50,10,"CONFIRMED",0,1,'C');


// FOOTER
$pdf->SetTextColor(120,120,120);
$pdf->SetFont('Arial','I',10);
$pdf->SetY(180);
$pdf->Cell(0,10,"Show this ticket at entry | Thank you!",0,1,'C');


// DOWNLOAD
$pdf->Output("D","Advanced_Ticket.pdf");
?>
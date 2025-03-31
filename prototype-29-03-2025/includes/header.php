<?php
// File: includes/header.php
// Common header for all pages
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ROLSA Technologies - Green Energy Solutions</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  
  <!-- Custom CSS -->
  <link href="/assets/css/index.css" rel="stylesheet">
  <link href="/assets/css/app.css" rel="stylesheet">


  <!-- had to add inline styles because for some reason code was not being found and bootstrap was using its default -->
  <style>
    .btn-yellow-rolsa {
      background-color: #ECA400 !important;
      color: #16262E !important;
    }

    .btn-dark-rolsa {
      background-color: #16262E !important;
      color: white !important;
    }
  </style>

</head>

<body>
  <!-- Navigation Bar -->
  <?php include "includes/navbar.php"; ?>
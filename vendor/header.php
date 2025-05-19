<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PLURN</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .sidebar { min-height: 100vh; background-color: #6f42c1; color: white; }
    .sidebar a { color: white; display: block; padding: 10px; text-decoration: none; }
    .sidebar a:hover { background-color: #5a32a3; }
    .header-bar { background-color: #6f42c1; color: white; padding: 10px 20px; }
    .content { margin-left: 220px; padding-top: 60px; }
    .user-dropdown { color: white; }
  </style>
</head>
<body>
<div class="header-bar d-flex justify-content-between align-items-center fixed-top shadow">
  <div>
    <button class="btn btn-light me-2" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
    <a href="/plurn/home.php" class="text-white fw-bold text-decoration-none fs-5">PLURN</a>
  </div>
  <div class="dropdown">
    <a class="dropdown-toggle user-dropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      <?= $_SESSION['username'] ?? 'User' ?>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="/plurn/profile.php">Profile</a></li>
      <li><a class="dropdown-item" href="/plurn/settings.php">Settings</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item text-danger" href="/plurn/logout.php">Logout</a></li>
    </ul>
  </div>
</div>

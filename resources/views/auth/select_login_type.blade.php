<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Select Login Type</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
margin:0;
font-family:Arial, sans-serif;
background:linear-gradient(135deg,#5dade2,#1e69de);
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
padding:40px 20px;
}

.container{
background:#f3f4f6;
padding:40px;
border-radius:25px;
width:100%;
max-width:1000px;
text-align:center;
box-shadow:0 20px 40px rgba(0,0,0,0.1);
}

h2{
margin-bottom:35px;
font-weight:bold;
}

.grid{
display:grid;
grid-template-columns:repeat(2,1fr);
gap:30px;
}

@media(max-width:768px){
.grid{
grid-template-columns:1fr;
}
}

.card{
background:#ffffff;
padding:35px 20px;
border-radius:18px;
text-decoration:none;
color:#000;
box-shadow:0 6px 15px rgba(0,0,0,0.08);
transition:0.3s;
}

.card:hover{
transform:translateY(-6px);
}

.icon-box{
width:65px;
height:65px;
background:#e5e7eb;
border-radius:15px;
margin:0 auto 20px;
display:flex;
align-items:center;
justify-content:center;
}

.icon-box i{
font-size:24px;
color:#1e3a8a;
}

.card h3{
margin:10px 0;
}

.card p{
font-size:14px;
color:#555;
}

/* Admin card in center */
.admin-card{
grid-column:span 2;
max-width:600px;
margin:auto;
}

.btn{
margin-top:35px;
display:inline-block;
background:#1e4f91;
color:white;
padding:14px 40px;
border-radius:12px;
text-decoration:none;
font-weight:bold;
transition:0.3s;
}

.btn:hover{
background:#163d73;
}

@media (max-height:700px){
body{
align-items:flex-start;
}
}

</style>
</head>

<body>

<div class="container">

<h2>Select Login Type</h2>

<div class="grid">

<a href="{{ route('patient.login') }}" class="card">
<div class="icon-box">
<i class="fa-regular fa-user"></i>
</div>
<h3>Login as Patient</h3>
<p>Access your medical records and appointments</p>
</a>

<a href="{{ route('doctor.login') }}" class="card">
<div class="icon-box">
<i class="fa-solid fa-user-doctor"></i>
</div>
<h3>Login as Doctor</h3>
<p>Manage patients and medical procedures</p>
</a>

<a href="{{ route('staff.login') }}" class="card">
<div class="icon-box">
<i class="fa-solid fa-user-tie"></i>
</div>
<h3>Login as Staff</h3>
<p>Access staff portal and hospital resources</p>
</a>

<a href="{{ route('nurse.login') }}" class="card">
<div class="icon-box">
<i class="fa-solid fa-user-nurse"></i>
</div>
<h3>Login as Nurse</h3>
<p>Access Nurse portal and hospital resources</p>
</a>

<!-- Admin -->
<a href="{{ route('admin.login') }}" class="card admin-card">
<div class="icon-box">
<i class="fa-solid fa-shield-halved"></i>
</div>
<h3>Login as Admin</h3>
<p>Manage hospital operations and settings</p>
</a>

</div>

<a href="#" class="btn">
<i class="fa-solid fa-globe"></i> Visit Our Website
</a>

</div>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Electronic Signature</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #5dade2, #1e69de);
        }
        .container { padding: 30px 80px 60px; }
        .card {
            background: #f3f4f6;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            margin-bottom: 40px;
        }
        .signature-box {
            border: 2px solid #bbb;
            border-radius: 10px;
            background: white;
        }
        canvas {
            width: 100%;
            height: 200px;
            cursor: crosshair;
        }
        .btn-group {
            margin-top: 20px;
            display: flex;
            gap: 15px;
        }
        .btn {
            padding: 10px 25px;
            border-radius: 10px;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-clear {
            background: white;
            border: 2px solid #123a6f;
            color: #123a6f;
        }
        .btn-save {
            background: #123a6f;
            color: white;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">

    @if ($errors->any())
        <div class="alert-danger">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <h1>Sign Medical Document</h1>
        <p>Please review the medical document carefully and provide your electronic signature below.</p>
    </div>
        <div class="card">
            <h2>{{ $document->title }}</h2>
            <p>{{ $document->content }}</p>
        </div>
    <div class="card">
        <h2>Doctor Signature</h2>

        <form method="POST" action="{{ route('signature.store') }}" onsubmit="return prepareSignature(event)">
            @csrf

            <input type="hidden" name="signature" id="signatureInput">
            <input type="hidden" name="document_id" value="{{ $document->id }}">

            <div class="signature-box">
                <canvas id="signatureCanvas"></canvas>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-clear" onclick="clearCanvas()">Clear</button>
                <button type="submit" class="btn btn-save">Save Signature</button>
            </div>
        </form>

    </div>
</div>

<script>
    const canvas = document.getElementById('signatureCanvas');
    const ctx = canvas.getContext('2d');

    canvas.width = canvas.offsetWidth;
    canvas.height = 200;

    let drawing = false;
    let hasSigned = false;

    canvas.addEventListener('mousedown', () => {
        drawing = true;
        hasSigned = true;
    });

    canvas.addEventListener('mouseup', () => {
        drawing = false;
        ctx.beginPath();
    });

    canvas.addEventListener('mousemove', draw);

    function draw(e) {
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    }

    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSigned = false;
    }

    function prepareSignature(event) {
        if (!hasSigned) {
            alert("Please sign before saving.");
            event.preventDefault();
            return false;
        }
        document.getElementById('signatureInput').value = canvas.toDataURL('image/png');
        return true;
    }
</script>

</body>
</html>

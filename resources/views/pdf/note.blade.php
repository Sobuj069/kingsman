<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Note PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 20px;
        }

        .note-content {
            font-size: 14px;
            line-height: 1.6;
        }

        /* Optional: style elements inside editor output */
        h1, h2, h3 {
            color: #007BFF;
        }
        p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="note-content">
        {!! $note !!}
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Estimate Summary</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            color: #3d4852;
            line-height: 1.5;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #e8e5ef;
            border-radius: 8px;
            background-color: #ffffff;
        }
        h1 {
            font-size: 24px;
            color: #2d3748;
            border-bottom: 1px solid #e8e5ef;
            padding-bottom: 15px;
        }
        h2 {
            font-size: 18px;
            color: #2d3748;
            border-bottom: 1px solid #e8e5ef;
            padding-bottom: 10px;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e8e5ef;
        }
        .total td {
            font-weight: bold;
            font-size: 1.1em;
            border-top: 2px solid #cbd5e0;
        }
        ul {
            list-style-type: none;
            padding-left: 0;
        }
        li {
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Project Estimate Summary</h1>
        <p>A new project estimate has been submitted. Please see the details below.</p>

        <h2>Project Details</h2>
        <table>
            <tr>
                <td><strong>Project / Domain:</strong></td>
                <td>{{ $project->name }}</td>
            </tr>
            <tr>
                <td><strong>Submitted By:</strong></td>
                <td>{{ $project->user->name }} ({{ $project->user->email }})</td>
            </tr>
             <tr>
                <td><strong>Status:</strong></td>
                <td style="text-transform: capitalize;">{{ $project->status }}</td>
            </tr>
        </table>

        <h2>Selected Services</h2>
        <table>
            @php
                $total = 0;
            @endphp
            @foreach($project->packages as $package)
                <tr>
                    <td>{{ ucfirst($package->type) }} - {{ $package->name }}</td>
                    <td style="text-align: right;">${{ number_format($package->price, 2) }}</td>
                </tr>
                @php
                    $total += $package->price;
                @endphp
            @endforeach
            <tr class="total">
                <td>Total Estimate</td>
                <td style="text-align: right;">${{ number_format($total, 2) }}</td>
            </tr>
        </table>

        @if($project->uploads->count() > 0)
            <h2>Uploaded Files</h2>
            <ul>
                @foreach($project->uploads as $upload)
                    <li>- {{ $upload->original_filename }}</li>
                @endforeach
            </ul>
        @endif

    </div>
</body>
</html>

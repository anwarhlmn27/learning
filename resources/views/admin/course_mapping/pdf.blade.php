<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Mapping</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        h2 {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .stage-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: center;
        }
        th {
            background-color: #a30000; /* Dark red background matching screenshot */
            color: white;
            font-weight: bold;
        }
        td.course-name {
            text-align: left;
            width: 25%;
        }
    </style>
</head>
<body>
    <h2>PLO &ndash; COURSE MAPPING (I-R-M)</h2>
    
    @foreach($stages as $stageName => $stageData)
        <div class="stage-title">{{ $stageName }}</div>
        <table>
            <thead>
                <tr>
                    <th class="course-name">Course</th>
                    @foreach($plos as $index => $plo)
                        <th>PLO{{ $index + 1 }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($stageData['courses'] as $course)
                    <tr>
                        <td class="course-name">{{ $course->nama_subject }}</td>
                        @foreach($plos as $plo)
                            <td>
                                {{ $matrix[$course->id][$plo->id] ?? '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>

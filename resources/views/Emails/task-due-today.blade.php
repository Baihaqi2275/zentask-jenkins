<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .header h1 {
            color: #3b82f6;
            margin: 0;
        }
        .task-list {
            margin: 20px 0;
        }
        .task-item {
            background: #f8fafc;
            border-left: 4px solid #fbbf24;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .task-title {
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .task-info {
            color: #64748b;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Task Deadline Hari Ini</h1>
            <p style="color: #64748b; margin-top: 10px;">
                Halo {{ $user->name }}, kamu punya {{ $taskCount }} task yang harus diselesaikan hari ini!
            </p>
        </div>

        <div class="task-list">
            @foreach($tasks as $task)
            <div class="task-item">
                <div class="task-title">{{ $task->title }}</div>
                <div class="task-info">
                    📁 Category: {{ $task->category ?? '-' }}
                </div>
                <div class="task-info">
                    📅 Deadline: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                </div>
            </div>
            @endforeach
        </div>

        <div style="text-align: center;">
            <a href="{{ url('/tasks') }}" class="button">
                Lihat Semua Task
            </a>
        </div>

        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem Zentask.</p>
            <p>© {{ date('Y') }} Zentask. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

# WorkLog – Daily Work Tracker
## Setup Instructions (XAMPP)

### 1. Place the project
Copy the entire `worklog` folder to:
```
C:\xampp\htdocs\worklog\
```

### 2. Set up the database
1. Start **Apache** and **MySQL** in XAMPP Control Panel
2. Open browser → go to `http://localhost/phpmyadmin`
3. Click **SQL** tab at the top
4. Copy-paste the contents of `database.sql` and click **Go**

### 3. Configure database (if needed)
Open `config.php` and update if your XAMPP uses different credentials:
```php
define('DB_USER', 'root');   // your MySQL username
define('DB_PASS', '');        // your MySQL password (empty by default in XAMPP)
```

### 4. Run the project
Open browser → `http://localhost/worklog/`

---

## Project Structure
```
worklog/
├── index.php         ← Main app (log work + view records)
├── report.php        ← Report viewer & PDF print page
├── save.php          ← Handles form submission
├── get_sessions.php  ← Returns list of work sessions
├── get_session.php   ← Returns single session data
├── config.php        ← Database connection config
├── database.sql      ← Run this to create tables
├── uploads/          ← Uploaded images stored here (auto-created)
└── README.md
```

## How to Use

1. **Log Work**: Select date, enter task title + details + images, click Save
2. **Add more tasks**: Click "Add More Work" button for each task
3. **View Records**: Click "View Records" in sidebar to see all logged dates
4. **View Report**: Click "View Report" to open a printable report in browser
5. **Download PDF**: Click "Download PDF" — browser's print dialog opens (Save as PDF)

## Notes
- The `uploads/` folder is created automatically on first save
- One session per date (re-saving same date updates it)
- PDF is generated via browser print (File → Print → Save as PDF)

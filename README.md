# Todo List Application

A modern, responsive todo list application built with PHP and featuring a beautiful gradient UI design.

## 🌟 Features

- **Modern UI Design**: Beautiful purple gradient interface with smooth animations
- **Task Management**: Add, edit, and manage your daily tasks
- **Progress Tracking**: Visual progress indicators with completion counters
- **Responsive Design**: Works seamlessly across desktop and mobile devices
- **Database Integration**: Persistent storage using PHP and SQL
- **Bootstrap Framework**: Clean, professional styling with Bootstrap CSS

![To-Do List App Interface](https://github.com/HopeFlynn/To-Do-List-App/blob/todolist/Screenshot%20(479).png)

## 📸 Preview

The application features a clean, intuitive interface with:
- Task input field with "Add Task" button
- Progress indicators showing completed vs pending tasks
- Numbered task organization (6 total, 2 completed, 4 pending)
- Modern card-based layout with purple gradient background

📁 Project Structure

```
HTDOCS/
├── ToDoList/
│   ├── .gitattributes
│   ├── api.php
│   ├── auth.php
│   ├── bootstrap.css
│   ├── config.php
│   ├── index.php
│   ├── README.md
│   ├── script.js
│   └── todo.sql
├── dashboard/
├── img/
├── test/
│   └── form.php
├── webalizer/
├── xampp/
├── applications.html
├── bitnami.css
├── favicon.ico
└── index.php
```

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- Web server (Apache/Nginx)
- MySQL or SQLite database
- Modern web browser

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone [your-repository-url]
   cd ToDoList
   ```

2. **Database Setup**
   - Import the `todo.sql` file into your database
   - Update database credentials in `config.php`

3. **Configuration**
   ```php
   // config.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'todo_db');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

4. **Start the Application**
   - Place files in your web server directory
   - Access via `http://localhost/ToDoList`

## 💻 Usage

1. **Adding Tasks**: Type your task in the input field and click "Add Task"
2. **Managing Tasks**: Use the interface to mark tasks as complete
3. **Progress Tracking**: Monitor your progress with the visual indicators
4. **Authentication**: Use `auth.php` for user login functionality

## 🎨 Customization

### Styling
- Modify `bootstrap.css` for layout changes
- Update custom CSS for color schemes and animations
- Responsive breakpoints can be adjusted in the CSS

### Functionality
- Extend `api.php` for additional API endpoints
- Modify `script.js` for enhanced interactivity
- Add new features through the modular PHP structure


## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Hope Flynn**
- GitHub: [https://github.com/HopeFlynn)
- Email: hopemwangi004@gmail.com

## 🙏 Acknowledgments

- Bootstrap team for the excellent CSS framework
- PHP community for robust backend capabilities
- All contributors who help improve this project



**Happy Task Managing!** ✅



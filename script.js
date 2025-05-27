// IIFE
(() => {
    // UI variables
    const form = document.querySelector(".form");
    const input = form.querySelector(".form_input");
    const ul = document.querySelector(".toDoList");

    // Load todos when page loads
    document.addEventListener('DOMContentLoaded', loadTodos);

    // event listeners
    form.addEventListener("submit", async (e) => {
        // prevent default behaviour - Page reload
        e.preventDefault();

        // get input value
        let toDoItem = input.value.trim();
        
        if (!toDoItem) {
            showMessage('Please enter a task', 'error');
            return;
        }

        // Add to database instead of local array
        await addTodoToDatabase(toDoItem);
        
        // clear the input box
        input.value = "";
    });

    ul.addEventListener("click", async (e) => {
        let li = e.target.closest('li');
        if (!li) return;
        
        let id = li.getAttribute("data-id");
        if (!id) return;

        // Remove from database instead of local array
        await removeTodoFromDatabase(id);
    });

    // Database functions
    async function addTodoToDatabase(text) {
        try {
            const response = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&text=${encodeURIComponent(text)}`
            });
            
            const result = await response.json();
            if (result.success) {
                loadTodos(); // Reload todos from database
                showMessage('Task added successfully!', 'success');
            } else {
                showMessage('Error adding task', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('Error adding task', 'error');
        }
    }

    async function removeTodoFromDatabase(id) {
        try {
            const response = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&id=${id}`
            });
            
            const result = await response.json();
            if (result.success) {
                loadTodos(); // Reload todos from database
                showMessage('Task deleted successfully!', 'success');
            } else {
                showMessage('Error deleting task', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('Error deleting task', 'error');
        }
    }

    async function loadTodos() {
        try {
            const response = await fetch('api.php?action=get');
            const result = await response.json();
            
            if (result.success && result.todos) {
                displayTodos(result.todos);
            } else {
                console.log('No todos found or error loading');
                ul.innerHTML = '<li>No tasks found</li>';
            }
        } catch (error) {
            console.error('Error loading todos:', error);
            showMessage('Error loading tasks', 'error');
        }
    }

    function displayTodos(todos) {
        // Clear existing todos
        ul.innerHTML = '';
        
        if (todos.length === 0) {
            ul.innerHTML = '<li>No tasks found</li>';
            return;
        }

        todos.forEach(todo => {
            const li = document.createElement("li");
            li.setAttribute("data-id", todo.id);
            li.innerText = todo.text || todo.task || todo.description; // Handle different column names
            li.style.cursor = 'pointer';
            li.title = 'Click to delete';
            ul.appendChild(li);
        });
    }

    function showMessage(message, type) {
        // Create or update message element
        let messageDiv = document.querySelector('.message');
        if (!messageDiv) {
            messageDiv = document.createElement('div');
            messageDiv.className = 'message';
            form.appendChild(messageDiv);
        }
        
        messageDiv.textContent = message;
        messageDiv.className = `message ${type}`;
        
        // Hide message after 3 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 3000);
    }
})();
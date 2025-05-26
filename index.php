<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List - PHP Integrated</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Gochi+Hand');

        body {
            background-color: #a39bd2;
            min-height: 70vh;
            padding: 1rem;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #494a4b;
            font-family: 'Gochi Hand', cursive;
            text-align: center;
            font-size: 130%;
        }

        @media only screen and (min-width: 500px) {
            body {
                min-height: 100vh;
            }
        }

        .container {
            width: 100%;
            height: auto;
            min-height: 500px;
            max-width: 500px;
            min-width: 250px;
            background: #1f1f58;
            background-image: radial-gradient(#bfc0c1 7.2%, transparent 0);
            background-size: 25px 25px;
            border-radius: 20px;
            box-shadow: 4px 3px 7px 2px #00000040;
            padding: 1rem;
            box-sizing: border-box;
        }

        .heading {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .heading_img {
            width: 50px;
            height: 50px;
            margin-right: 1rem;
        }

        .heading_title {
            font-size: 2rem;
            color: #fff;
        }

        .form {
            margin-top: 20px;
        }

        .form_label {
            display: block;
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 10px;
        }

        .form_input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
            outline: none;
        }

        .form_input:focus {
            border: solid 3px #a5d5e6;
        }

        @media only screen and (min-width: 500px) {
            .form_input {
                width: 60%;
            }
        }

        .button {
            background: none;
            border: none;
            transform: rotate(4deg);
            transform-origin: center;
            font-family: 'Gochi Hand', cursive;
            padding: 10px 20px;
            background-color: #ff6f61;
            color: #fff;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.3);
        }

        .button:hover {
            background-color: #ff5733;
        }

        .button:focus {
            outline: none;
        }

        .toDoList {
            list-style: none;
            padding: 0;
            margin: 20px 0 0;
        }

        .toDoList li {
            background: #fff;
            color: #494a4b;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .toDoList li.completed {
            text-decoration: line-through;
            text-decoration-color: #ff6f61;
            opacity: 0.6;
        }

        .toDoList li:hover {
            background-color: #f0f0f0;
        }

        .delete-btn {
            background: #ff6f61;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.8rem;
            font-family: 'Gochi Hand', cursive;
        }

        .delete-btn:hover {
            background: #ff5733;
        }

        .loading {
            color: #fff;
            text-align: center;
            margin: 20px 0;
        }

        .error {
            color: #ff6f61;
            text-align: center;
            margin: 10px 0;
            font-size: 1rem;
        }
    </style>
</head>

<body>
    <section class="container">
        <div class="heading">
            <img class="heading_img" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/756881/laptop.svg" alt="Laptop Image">
            <h1 class="heading_title">To-Do List</h1>
        </div>
        <form class="form">
            <div>
                <label class="form_label" for="todo">Today I need to ...</label>
                <input class="form_input" type="text" id="todo" name="to-do" size="30" required>
            </div>
            <button class="button" type="submit"><span>Submit</span></button>
        </form>
        <div class="loading" id="loading" style="display: none;">Loading...</div>
        <div class="error" id="error" style="display: none;"></div>
        <div>
            <ul class="toDoList"></ul>
        </div>
    </section>

    <script>
        (() => {
            // UI variables
            const form = document.querySelector(".form");
            const input = form.querySelector(".form_input");
            const ul = document.querySelector(".toDoList");
            const loading = document.getElementById("loading");
            const errorDiv = document.getElementById("error");

            // API base URL - adjust this to your server path
            const API_URL = 'api.php';

            // Load todos on page load
            loadTodos();

            // Event listeners
            form.addEventListener("submit", async (e) => {
                e.preventDefault();
                const task = input.value.trim();
                if (!task) return;

                await addTodo(task);
                input.value = "";
            });

            ul.addEventListener("click", async (e) => {
                const li = e.target.closest('li');
                if (!li) return;

                const id = li.getAttribute("data-id");
                
                if (e.target.classList.contains('delete-btn')) {
                    // Delete todo
                    await deleteTodo(id);
                } else {
                    // Toggle completion
                    const isCompleted = li.classList.contains('completed');
                    await toggleTodo(id, !isCompleted);
                }
            });

            // API functions
            async function apiRequest(method, data = null) {
                showLoading(true);
                showError('');
                
                try {
                    const options = {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    };

                    if (data) {
                        options.body = JSON.stringify(data);
                    }

                    const response = await fetch(API_URL, options);
                    const result = await response.json();

                    if (!result.success) {
                        throw new Error(result.error || 'An error occurred');
                    }

                    return result;
                } catch (error) {
                    showError(error.message);
                    throw error;
                } finally {
                    showLoading(false);
                }
            }

            async function loadTodos() {
                try {
                    const result = await apiRequest('GET');
                    renderTodos(result.data);
                } catch (error) {
                    console.error('Error loading todos:', error);
                }
            }

            async function addTodo(task) {
                try {
                    const result = await apiRequest('POST', { task });
                    addTodoToDOM(result.data);
                } catch (error) {
                    console.error('Error adding todo:', error);
                }
            }

            async function toggleTodo(id, completed) {
                try {
                    await apiRequest('PUT', { id: parseInt(id), completed });
                    const li = document.querySelector(`[data-id="${id}"]`);
                    if (li) {
                        li.classList.toggle('completed', completed);
                    }
                } catch (error) {
                    console.error('Error toggling todo:', error);
                }
            }

            async function deleteTodo(id) {
                try {
                    await apiRequest('DELETE', { id: parseInt(id) });
                    const li = document.querySelector(`[data-id="${id}"]`);
                    if (li) {
                        li.remove();
                    }
                } catch (error) {
                    console.error('Error deleting todo:', error);
                }
            }

            // DOM manipulation functions
            function renderTodos(todos) {
                ul.innerHTML = '';
                todos.forEach(todo => addTodoToDOM(todo));
            }

            function addTodoToDOM(todo) {
                const li = document.createElement("li");
                li.setAttribute("data-id", todo.id);
                li.className = todo.completed ? 'completed' : '';

                li.innerHTML = `
                    <span>${escapeHtml(todo.task)}</span>
                    <button class="delete-btn">Delete</button>
                `;

                ul.appendChild(li);
            }

            function showLoading(show) {
                loading.style.display = show ? 'block' : 'none';
            }

            function showError(message) {
                errorDiv.textContent = message;
                errorDiv.style.display = message ? 'block' : 'none';
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        })();
    </script>
</body>
</html>
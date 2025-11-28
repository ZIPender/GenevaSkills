# GenevaSkills

GenevaSkills is a platform designed to connect developers and projects in Geneva. It features user authentication, project management, and a real-time messaging system to facilitate collaboration.

## Stack

### Backend
- **Language**: PHP 8+
- **Architecture**: Custom MVC (Model-View-Controller)
- **Database**: MySQL (using PDO for database interactions)

### Real-time Communication
- **Library**: [Ratchet](http://socketo.me/) (WebSockets for PHP)
- **Event Loop**: [ReactPHP](https://reactphp.org/)

### Frontend
- **Core**: Vanilla HTML, CSS, and JavaScript
- **Styling**: Custom CSS (located in `public/assets/css`)
- **Interactivity**: Vanilla JavaScript (located in `public/assets/js`)

## Project Goal
The main goal of GenevaSkills is to create a dynamic ecosystem where:
- **Developers** can showcase their skills and find interesting projects.
- **Project Owners** can find the right talent for their needs.
- **Collaboration** is seamless through real-time messaging and project management tools.

## Setup

1.  **Clone the repository**
    ```bash
    git clone <repository-url>
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    ```

3.  **Database Configuration**
    - Import the database schema (if available).
    - Configure your database credentials in `src/Config/Database.php` or your environment configuration file.

4.  **Run the Application**
    - For the web server:
      ```bash
      php -S localhost:8000 -t public
      ```
    - For the WebSocket server (messaging):
      ```bash
      php bin/chat-server.php
      ```

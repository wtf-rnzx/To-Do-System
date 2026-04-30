<div align="center">
  <h1>🦌 StagStack</h1>
  <p><strong>A Gamified Productivity & To-Do System</strong></p>
  <p>
    <a href="#project-overview">Overview</a> •
    <a href="#features">Features</a> •
    <a href="#tech-stack">Tech Stack</a> •
    <a href="#installation-guide">Installation</a> •
    <a href="#usage-instructions">Usage</a> •
    <a href="#system-architecture">Architecture</a> •
    <a href="#contributing-guidelines">Contributing</a>
  </p>
</div>

---

## 📖 Project Overview

**StagStack** is a powerful productivity web application that bridges the gap between traditional task management and light role-playing gamification. Built with Laravel, it empowers users to organize their daily workflows, break down complex tasks into manageable subtasks, and stay motivated through a rewarding progression system. 

As users complete tasks, they earn Experience Points (EXP), level up their ranks, unlock custom badges, and achieve milestones. This positive reinforcement loop transforms mundane to-do lists into an engaging experience.

---

## ✨ Features

### 🎯 Task Management
* **Comprehensive CRUD:** Create, read, update, and delete tasks seamlessly.
* **Subtasks & Checklists:** Break down large tasks into trackable subtasks.
* **Smart Organization:** Categorize by priority (`Low`, `Medium`, `High`) and set due dates.
* **Recurring Tasks:** Set tasks to repeat daily, weekly, or monthly automatically.
* **Snooze Functionality:** Defer tasks with a full snooze history audit trail.
* **Bulk Actions:** Select multiple tasks for quick deletion or status toggling.

### 🎮 Gamification Engine
* **EXP & Rank System:** Earn Experience Points for task completion and progress through dynamically scaling ranks.
* **Achievement System:** Unlock custom achievements based on productivity milestones. Choose which achievements to showcase on your profile.
* **Badges:** Earn distinct badges for specific actions (e.g., maintaining streaks, completing weekly goals).
* **Weekly Goals & Streaks:** Set personalized weekly completion goals and track daily continuity streaks.

### 👑 User & Admin Portals
* **Personalized Dashboard (User):** View KPI cards, trend charts, recent activities, and progress metrics.
* **Admin Dashboard:** Access high-level platform metrics, user growth charts (daily/weekly/monthly), and platform activity logs.
* **User Management:** Admins can monitor, promote, or remove users as needed.

### 🎨 UI/UX Excellence
* **Responsive Design:** Fully fluid interfaces built with Tailwind CSS.
* **Dark/Light Mode:** Persistent theme preference stored locally.
* **Micro-Interactions:** Reactive components powered by Alpine.js for modal dialogs, dropdowns, and inline editing.

---

## 🛠️ Tech Stack

StagStack is built on a robust, modern PHP ecosystem utilizing the TALL stack principles (excluding Livewire for a Blade-focused approach).

**Backend:**
* **Language:** PHP 8.3+
* **Framework:** Laravel 10/11
* **Authentication:** Laravel Breeze & Sanctum
* **Database:** MySQL / PostgreSQL (managed via Eloquent ORM)

**Frontend:**
* **Templates:** Laravel Blade
* **Styling:** Tailwind CSS (with Forms plugin)
* **Interactivity:** Alpine.js
* **Bundler:** Vite

---

## 🚀 Installation Guide

Follow these step-by-step instructions to get StagStack running on your local machine.

### Prerequisites
* PHP >= 8.3
* Composer
* Node.js >= 18.x and npm
* A local database server (MySQL, PostgreSQL, or SQLite)

### Step-by-Step Setup

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd todo_project
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM dependencies:**
   ```bash
   npm install
   ```

4. **Environment Configuration:**
   Copy the example environment file and generate a new application key.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: Open the `.env` file and configure your `DB_*` credentials to point to your local database.*

5. **Run Migrations & Seeders:**
   This will create the necessary database tables and populate the system with default achievements and an admin user (if configured in seeders).
   ```bash
   php artisan migrate --seed
   ```

6. **Link Storage:**
   Ensure public disk files (like profile images) are accessible.
   ```bash
   php artisan storage:link
   ```

---

## 💻 Usage Instructions

### Starting the Development Servers
You will need two terminal windows to run the backend and frontend concurrently:

**Terminal 1 (Laravel backend):**
```bash
php artisan serve
```

**Terminal 2 (Vite frontend):**
```bash
npm run dev
```

### Navigating the System

1. **Registration/Login:** Access `http://localhost:8000` to create a new user account.
2. **Dashboard:** Upon logging in, you will be greeted by the Dashboard. Here you can set your **Weekly Goal** by clicking the edit icon next to your goal widget.
3. **Managing Tasks:** Navigate to the **Todos** tab. Click "Add Task" to create a new entry. You can click on any task to view its details, add subtasks, or snooze it.
4. **Gamification:** Complete tasks to see your EXP bar fill up. Navigate to the **Profile** or **Achievements** tab to view unlocked badges and toggle their visibility.
5. **Admin Access:** If your user has the `admin` role, an "Admin Panel" link will appear in the navigation, granting access to user analytics and system activity logs.

---

## 📸 Screenshots / Demo

*(Add screenshots here to showcase your beautiful UI. Replace the placeholders below with actual image links.)*

| Dashboard View | Task Details |
| :---: | :---: |
| `![Dashboard Placeholder](https://via.placeholder.com/400x250.png?text=Dashboard+View)` | `![Task Placeholder](https://via.placeholder.com/400x250.png?text=Task+Details)` |
| **Gamification Profile** | **Admin Analytics** |
| `![Profile Placeholder](https://via.placeholder.com/400x250.png?text=Profile+View)` | `![Admin Placeholder](https://via.placeholder.com/400x250.png?text=Admin+Dashboard)` |

---

## 🏗️ System Architecture

StagStack follows a strict **Model-View-Controller (MVC)** architecture provided by Laravel, enhanced with dedicated **Service Classes** to maintain clean controllers.

### Core Architectural Patterns:
* **Service Layer:** Complex business logic is abstracted into dedicated services located in `app/Services/`.
  * `ExperienceService`: Handles EXP calculations, level thresholds, and rank-ups.
  * `AchievementService`: Evaluates task completion metrics against achievement unlock criteria.
  * `BadgeService`: Manages the distribution of unique badges.
* **Event-Driven Logging:** The system uses a custom `ActivityLogger` to asynchronously record critical user actions (logins, task completions) without blocking the main request lifecycle.
* **Soft Deletes:** `Todo` models utilize Laravel's SoftDeletes, ensuring tasks are never permanently lost and allowing for potential recovery features.
* **Component-Based UI:** Blade components are heavily utilized in `resources/views/components/` to ensure DRY (Don't Repeat Yourself) frontend code, paired with Alpine.js for isolated, component-level state management.

---

<div align="center">
  <p>Built with ❤️ by the open-source community.</p>
</div>

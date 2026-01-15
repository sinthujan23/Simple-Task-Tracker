# Simple Task Tracker

A full-stack task tracking application built with **Flutter** (Frontend) and **Laravel** (Backend).

## 🚀 Setup Steps

### Backend (Laravel)
1. Navigate to the `backend` directory.
2. Install dependencies:
   ```bash
   composer install
   ```
3. Set up environment file:
   ```bash
   cp .env.example .env
   ```
   *Configure your database settings in `.env` (MySQL or SQLite).*
4. Run migrations:
   ```bash
   php artisan migrate
   ```
5. Start the server:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

### Frontend (Flutter)
1. Navigate to the `frontend` directory.
2. Install dependencies:
   ```bash
   flutter pub get
   ```
3. Run the application:
   ```bash
   flutter run
   ```
   *Note: If running on Android Emulator, the app connects to `http://10.0.2.2:8000`. If running on Web/Windows/iOS Simulator, it connects to `http://localhost:8000`. Modify `lib/services/api_service.dart` if needed.*

## 📡 API Endpoints

- **POST** `/api/task/create`
  - Body: `{ "title": "Buy groceries" }`
  - Response: `{ "status": true, "message": "Task created", "data": {...} }`

- **GET** `/api/task/list`
  - Response: `{ "status": true, "data": [ ... ] }`

- **POST** `/api/task/complete/{id}`
  - Response: `{ "status": true, "message": "Task completed" }`

## 🧠 Debugging & Reasoning

### Scenario: Flutter list doesn’t update after marking complete

**1. Identification:**
I noticed that after tapping the completion checkmark, the UI remained unchanged (the task didn't strike through) even though the network request might have succeeded.

**2. Explanation:**
This happens because the UI in Flutter is declarative and depends on the state. Even if the backend data is updated, the local Flutter widget doesn't know about it automatically. If we performed the API call but didn't trigger a rebuild or update the local `_tasks` list, the old data remains on screen.

**3. Fix:**
I ensured that after the API call `_apiService.completeTask(id)` succeeds, I explicitly refresh the data source.
In `TaskListScreen`, I established a flow:
- Call API `completeTask(id)`.
- On success `await`, call `_fetchTasks()`.
- `_fetchTasks` calls `setState()`, which triggers a rebuild with the fresh list from the server.

Alternatively, I could have locally updated the list using `setState` to toggle the boolean immediately for better performance (Optimistic UI), but fetching from server ensures absolute consistency.

**4. Code Reference:**
See `lib/screens/task_list_screen.dart`:
```dart
Future<void> _markComplete(int id) async {
  try {
    await _apiService.completeTask(id);
    _fetchTasks(); // Reloads data and calls setState
    // ...
  } catch (e) { ... }
}
```

## 📝 Assumptions & Improvements

**Assumptions:**
- The backend controls the `created_at` timestamp.
- The `is_completed` field is boolean equivalent (0/1).
- No authentication is required for this simple test.

**Improvements (with more time):**
- **State Management:** Use `Riverpod` or `Bloc` instead of `setState` for better separation of concerns and testability.
- **Error Handling:** Implement more granular error messages and retry logic.
- **Optimistic UI:** Update the UI immediately before the API responds to make the app feel faster, then roll back on error.
- **Dependency Injection:** Inject `ApiService` rather than instantiating it directly in widgets.
- **Tests:** Add unit tests for `ApiService` and widget tests for screens.

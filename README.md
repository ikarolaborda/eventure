# Eventure

![Tests](https://github.com/ikarolaborda/eventure/actions/workflows/tests.yml/badge.svg)


Eventure is an online event reservation system built with Laravel and JWT authentication. It allows users to register, log in, create events, and reserve tickets. Authenticated users can:

- Create and manage events (including setting title, description, start/end dates, booking deadlines, attendee limits, price, and location).
- Reserve tickets for events if booking conditions are met.
- View their reservations and leave reviews after attending events.

The system is designed following the SOLID principles, using repositories, services, and single-responsibility controllers. It also provides a Swagger-generated API documentation for easy integration and testing.

## Features
- **User Authentication (JWT)**: Users can register, log in, refresh their token, and log out.
- **Event Management**: Authenticated users can create new events. All events can be listed and individually viewed.
- **Ticket Reservation**: Users can reserve tickets to events if they meet conditions (e.g., before booking deadline and not fully booked).
- **Validation**: All incoming requests are validated using Laravel’s Form Requests.
- **SOLID & Clean Architecture**:
- Controllers handle only one action (Single Responsibility Principle).
- Repository Interfaces and their Eloquent implementations abstract database logic.
- JWT authentication is used for secure token-based access.
- **Swagger Documentation**: Provides a visual, interactive API documentation at /api/documentation.

- ## Requirements
- PHP 8.2 or higher
- Composer
- Laravel 11.x or higher
- SQLite/MySQL/PostgreSQL database (configurable)

## Setup Instructions
1. **Clone the Repository**:

```bash
git clone https://github.com/your-username/eventure.git
cd eventure
```
2. **Install Dependencies**:

```bash
composer install
```

3. **Environment Setup**: Copy the .env.example file to .env:

```bash
cp .env.example .env
```
Update database and JWT settings in .env as needed:

```env

DB_CONNECTION=sqlite
DB_DATABASE=./database/database.sqlite

JWT_SECRET=your_generated_jwt_secret
```
Generate the JWT secret:

```bash
php artisan jwt:secret
```
4. **Migrate Database**:

```bash
php artisan migrate
```
5. **Run the Server**:

```bash
php artisan serve
```
The application will be available at `http://127.0.0.1:8000`.

## Authentication
**Register**: `POST /api/auth/register` with `name, email, password, password_confirmation`.

**Login**: `POST /api/auth/login` with `email, password`.

The response will include a `token` (JWT). For subsequent requests requiring authentication, include it in the `Authorization` header:

```http request
Authorization: Bearer your_jwt_token
```

**Refresh Token**: `POST /api/auth/refresh` to refresh an expired token.

**Logout**: `POST /api/auth/logout` to invalidate the current token.

## Endpoints Overview
- **Events**:
- `GET /api/v1/events`: List all events (publicly accessible).
- `GET /api/v1/events/{id}`: Show details of a specific event.
- `POST /api/v1/events`: Create a new event (authenticated).
- **Reservations**:
- `POST /api/v1/events/{id}/reserve`: Reserve a ticket for an event (authenticated).
- `GET /api/v1/reservations`: List user’s reservations (authenticated).
- `GET /api/v1/reservations/{id}`: Show a specific reservation for the user (authenticated).
- **Reviews**:
- `POST /api/v1/events/{id}/reviews`: Create a review for an event after attending (authenticated).
- `GET /api/v1/events/{id}/reviews`: List reviews of an event (public).
- 
Check the Swagger docs for the full list of endpoints and request/response schemas.

## Testing
Tests include both Feature and Unit tests for authentication, events, reservations, and reviews.

- **Run Tests**:
```bash
php artisan test
```
Tests are organized under `tests/Feature` and `tests/Unit` directories. They ensure correct authentication flow, event creation validation, reservation logic, and more.

## Swagger Documentation
The API is documented using OpenAPI/Swagger attributes. To view the docs:

**Access the Swagger UI**: Open your browser and visit:

```
http://127.0.0.1:8000/api/documentation
```
You will see a fully interactive Swagger UI where you can test endpoints and view request/response schemas.

## Code Structure
- **Controllers**: Located in `app/Http/Controllers/Api`, separated by domain (`Auth`, `Event`, `Reservation`and `Review`). Each action is a single invokable controller.
- **Requests**: Located in `app/Http/Requests/Api`, containing validation logic.
- **Repositories**: `app/Repositories` and `app/Repositories/Eloquent` define interfaces and their Eloquent implementations to abstract the database layer.
- **Models**: `app/Models` contains Eloquent models (`User`, `Event`, `Reservation`, `Review`).
- **SwaggerOpenApi**: Defined in `app/Http/Controllers/Api/Documentation/SwaggerOpenApi.php` for global OpenAPI configuration.

## Contribution
Contributions are welcome! Feel free to open issues or submit pull requests.

- Follow PSR-12 coding style.
- Ensure all tests pass before submitting a PR.
- Document any new endpoints or changes in the Swagger doc and README.

## License
Eventure is open-sourced software licensed under the MIT license.

### Enjoy using Eventure!

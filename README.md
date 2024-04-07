# Microservices Project with Laravel: Authentication and Message Registration

## Overview

This project is structured as a microservices architecture, including two main services: `auth-service` and `message-service`. Built with Laravel, this setup utilizes Docker to containerize and manage the services efficiently. The `auth-service` is responsible for user authentication, including login and logout functionalities, leveraging JWT tokens for secure communication. The `message-service` allows for message registration, accessible only via a valid token issued by the `auth-service`.

## Architecture and Design Patterns

- **Microservices Architecture**: The project is divided into two distinct services, each with its own database and environment, ensuring loose coupling and high cohesion.
- **Repository Pattern**: Used for abstracting the data layer, promoting a more testable and maintainable codebase.
- **Service Layer**: Contains business logic, serving as an intermediary between the controllers and the repository.
- **JWT Authentication**: Secures communication between services, ensuring that only authenticated users can register messages.

## Prerequisites

- Docker and Docker Compose installed on your machine.
- Basic understanding of Laravel and microservices concepts.

## Running the Project

1. Clone the repository to your local machine.
2. Navigate to the project directory and run the following command to start the services:

````
docker-compose up --build
````
Accessing the Services
Authentication Service (auth-service): Accessible at http://localhost/api/auth. Endpoints include:

````
/login: Authenticate a user and retrieve a JWT token.
/logout: Invalidate a user's JWT token.
````

Message Service (message-service): Accessible at http://localhost/api/message. Endpoints include:

/: Register a new message (Requires a valid JWT token).

Running Tests
To execute the tests for each service, you can enter the Docker container using the following command:
````
php artisan test
````

This will execute the test suites defined within each Laravel project.

## Note
Ensure that the .env files for each service are correctly configured based on the .env.example provided in the respective service directories. Adjust database credentials and any other environment-specific settings as necessary.
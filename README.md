# Expense Tracker

Expense Tracker is a web application designed to help users manage their personal finances efficiently. Built with Symfony, it allows users to add, categorize, and track their expenses, providing insightful statistics and reports. This project showcases the use of the Symfony framework along with various other technologies to create a full-fledged expense management system.

## Features

- **User Authentication**: User registration, login, and logout.
- **Expense Management**: Add, edit, and delete expenses. Categorize expenses for better organization.
- **Expense Statistics**: View total expenses over time, expenses by category, and graphical representation of expenses.
- **User Dashboard**: Overview of recent expenses and summary of total expenses.
- **Notifications**: Email notifications for expense tracking.
- **Data Export**: Export expense data to CSV or Excel.
- **Security Enhancements**: Password reset functionality and two-factor authentication (2FA).

# Expense Tracker - TODOs

## Version 0.1
- **User Authentication**
  - [x] Implement authentication system
  - [x] User registration
  - [x] User login and logout

- **Expense Management**
  - [x] Add new expenses
  - [x] Display a list of expenses

## Version 0.2
- **Edit and Delete Expenses**
  - [x] Edit existing expenses
  - [x] Delete expenses

- **Expense Categorization**
  - [x] Add expense categories
  - [x] Filter expenses by categories

## Version 0.3
- **Expense Statistics**
  - [ ] Display total expenses over a selected period
  - [ ] Display expenses by categories
  - [ ] Basic graphical representation of expenses (e.g., pie chart)

- **User Dashboard**
  - [ ] Overview of recent expenses
  - [ ] Summary of total expenses and category breakdown

## Version 0.4
- **Notifications**
  - [ ] Set up email notifications for expense tracking
  - [ ] Configurable notification settings

- **Improved UI/UX**
  - [ ] Enhance the user interface for better usability
  - [ ] Responsive design for mobile and tablet

## Version 0.5
- **Data Export**
  - [ ] Export expense data to CSV or Excel
  - [ ] Option to download filtered data

- **Security Enhancements**
  - [ ] Implement password reset functionality
  - [ ] Add two-factor authentication (2FA)

## Version 1.0
- **Comprehensive Reports**
  - [ ] Generate detailed expense reports
  - [ ] Monthly and yearly summaries

- **Advanced Filtering and Sorting**
  - [ ] Advanced filters for expenses (e.g., by date range, amount)
  - [ ] Sorting options (e.g., by date, amount, category)

- **Deployment and Documentation**
  - [ ] Prepare the project for deployment
  - [ ] Write comprehensive documentation and user manual


## Installation

To run this project locally, follow these steps:

1. Clone the repository:
   ```sh
   git clone https://github.com/jakuubkoo/Expense-Tracker.git
   cd expense-tracker
   ```
2. Install dependencies:
   ```sh
   composer install
   npm install
   npm run dev
   ```
3. Set up the database:
   ```sh
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
4. Run the server:
   ```sh
   symfony server:start
   ```
5. Access the application:
   ```sh
   Open your web browser and navigate to `http://localhost:8000`
   ```

## Contributing

Contributions are welcome! Please fork this repository and submit pull requests for any features, bug fixes, or enhancements.

Please make sure to update tests as appropriate.

## License

This project is licensed under the MIT License. See the [LICENSE](https://choosealicense.com/licenses/mit/) file for details.

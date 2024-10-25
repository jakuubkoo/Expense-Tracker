<?php

namespace App\Utils;

class ErrorMessage {

    const INVALID_INPUT = 'The input provided is invalid.';
    const DATABASE_ERROR = 'There was an error connecting to the database.';
    const UNEXPECTED_ERROR = 'There was an unexpected error.';
    const NO_FIRST_NAME = 'First name is required.';
    const NO_LAST_NAME = 'Last name is required.';
    const NO_EMAIL = 'Email is required.';
    const NO_PASSWORD = 'Password is required.';
    const NO_CONF_PASSWORD = 'Confirmation password is required.';
    const EMAIL_ALREADY_EXISTS = 'An account with this email already exists.';
    const PASSWORD_CONFIRMATION_MISMATCH = 'Password confirmation does not match.';
    const PASSWORD_TOO_SHORT = 'Password must be at least 8 characters long.';
    const ACCESS_DENIED = 'You do not have permission to access this resource.';
    const UNEXPECTED_REGISTER_ERROR = 'An unexpected error occurred during registration.';
    const UNEXPECTED_LOGOUT_ERROR = 'An unexpected error occurred during logout.';
    const ALL_FIELDS_REQ_ERROR = 'All fields are required.';

    const NO_ID = 'ID is required.';

    const NO_TITLE = 'Title is required.';
    const NO_AMOUNT = 'Amount is required.';
    const NO_DATE = 'Date is required.';
    const NO_CATEGORY = 'Category is required.';
    const NO_DESCRIPTION = 'Description is required.';
    const VALUE_EMPTY = 'This value cannot be empty.';

    const NO_CATEGORY_FOUND = 'No category found for id.';
    const NO_NAME = 'Name is required.';
    const UNEXPECTED_CATEGORY_ERROR = 'An unexpected error occurred while processing the category.';

}
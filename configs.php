<?php

interface StaticMessages
{
    // STATIC MESSAGES
    const LOGIN_FAILED = 'LOGIN FAILED';
    const EMAIL_EXISTS = 'Email exists';
    const NOT_VALID_EMAIL = 'Not valid email';
    const SIGNUP_DONE = 'SIGNUP SUCCESS';
    const LOGIN_SUCCESS = 'login success';
    const DATABASE_CONNECTION_ERROR = 'Database connection Error';
    const CONNECTION_ERROR = 'CONNECTION ERROR';
    const NAME_CHANGED = 'NAME CHANGED';
    const EMAIL_CHANGED = 'EMAIL CHANGED';
    const PASSWORD_CHANGED = 'PASSWORD CHANGED';
}

trait DataValidation
{
    function filterData($value)
    {
        return htmlspecialchars(trim($value));
    }

    function filteredEmail($email)
    {
        $email = $this->filterData($email);
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    function isValidEmail($email)
    {
        $email = $this->filteredEmail($email);

        // check if email is valid or not
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function isValidPassword($password, $hashed_password)
    {
        // check if password is valid or not
        return password_verify($password, $hashed_password);
    }
}

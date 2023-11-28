<?php
session_start();

const USERNAME = 'root';
const PASSWORD = '';
const DATABASE = 'oop';

trait Database
{
    function dbConnection()
    {
        return new PDO('mysql:host=localhost;dbname=' . DATABASE, USERNAME, PASSWORD);
    }
}

<?php

namespace forDataBase;


use PDO;

class DataBase{

    private static $db = null;


   static function migrate(){
    self::$db->exec("CREATE TABLE IF NOT EXISTS tasks(id INTEGER PRIMARY KEY AUTOINCREMENT, type TEXT, title TEXT, description TEXT, priority TEXT, status TEXT, tags TEXT, created_at TEXT, created_by TEXT, interval TEXT NULL)");
    self::$db->exec("CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY AUTOINCREMENT, login TEXT UNIQUE, password_hash TEXT, created_at TEXT)");

    }

    public static function  get(){
        if(is_null(self::$db)){
            try{
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ];
                self::$db = new PDO('sqlite:host=localhost;dbname=tasks', 'root', '', $options);



            }catch(\PDOException $e){
                error_log($e->getMessage());
                die("Coś stalo oopsie");
            }
        }

    return self::$db;

    }

}
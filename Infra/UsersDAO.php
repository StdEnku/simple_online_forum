<?php

namespace Infra;

require_once __DIR__ . '/InfraException.php';

use PDO;
use Generator;

class UserDTO {
    public function __construct(
        public ?int $Id,
        public string $Name,
        public string $SignUpDay,
        public string $PasswordHash,
        public bool $IsExists
    ) {}

    public static function FromArray(array $row) : self {
        return new self(
            (int)$row['id'],
            (string)$row['name'],
            (string)$row['sign_up_day'],
            (string)$row['password_hash'],
            (bool)$row['is_exists']
        );
    }
}

class UsersDAO {
    private PDO $_pdo;

    public function __construct(PDO $pdo) {
        $this->_pdo = $pdo;
    }

    public function Create(UserDTO $userDto) {
        $isIdNull = $userDto->Id === null;
        if (!$isIdNull) 
            throw new InfraException('DAOのCreateメソッドに入力するDTOのidはnullでなければなりません。');
    
        $query = '
            INSERT INTO users (name, password_hash, is_exists)
            VALUES(:name, :password_hash, :is_exists);
        ';

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':name', $userDto->Name, PDO::PARAM_STR);
        $stmt->BindValue(':password_hash', $userDto->PasswordHash, PDO::PARAM_STR);
        $stmt->BindValue(':is_exists', $userDto->IsExists, PDO::PARAM_BOOL);
        $stmt->Execute();
    }

    public function Read() : Generator {
        $query = '
            SELECT * FROM users;
        ';

        $stmt = $this->_pdo->query($query);
        foreach($stmt as $row)
            yield UserDTO::FromArray($row);
    }

    public function Update(UserDTO $userDto) {
        $isIdNull = $userDto->Id === null;
        if ($isIdNull) 
            throw new InfraException('DAOのUpdateメソッドに入力するDTOのidはnull以外の値でなければなりません。');
    
        $query = '
            UPDATE users SET 
                name = :name, 
                sign_up_day = :sign_up_day, 
                password_hash = :password_hash,
                is_exists = :is_exists 
            WHERE ID = :id;
        ';

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':name', $userDto->Name, PDO::PARAM_STR);
        $stmt->BindValue(':sign_up_day', $userDto->SignUpDay, PDO::PARAM_STR);
        $stmt->BindValue(':password_hash', $userDto->PasswordHash, PDO::PARAM_STR);
        $stmt->BindValue(':is_exists', $userDto->IsExists, PDO::PARAM_BOOL);
        $stmt->BindValue(':id', $userDto->Id, PDO::PARAM_INT);
        $stmt->Execute();
    }
}
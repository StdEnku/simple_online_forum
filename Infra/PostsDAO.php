<?php

namespace Infra;

require_once __DIR__ . '/InfraException.php';

use PDO;
use Generator;

// #region DTO
class PostDTO {
    public function __construct(
        public ?int $Id,
        public int $UserId,
        public string $PostDateTime,
        public string $Content,
        public bool $IsExists
    ) {}

    public static function FromArray(array $row) : self {
        return new self(
            (int)$row['id'],
            (int)$row['user_id'],
            (string)$row['post_date_time'],
            (string)$row['content'],
            (bool)$row['is_exists']
        );
    }
}

class PostWithUserDTO {
    public function __construct(
        public int $Id,
        public int $UserId,
        public string $UserName, 
        public string $PostDateTime,
        public string $Content,
        public bool $IsExists
    ) {}

    public static function FromArray(array $row) : self {
        return new self(
            (int)$row['id'],
            (int)$row['user_id'],
            (string)$row['user_name'],
            (string)$row['post_date_time'],
            (string)$row['content'],
            (bool)$row['is_exists']
        );
    }
}
// #endregion

class PostsDAO {
    private PDO $_pdo;

    public function __construct(PDO $pdo) {
        $this->_pdo = $pdo;
    }

    // #region Create系処理
    public function Create(PostDTO $postDto) {
        $isIdNull = $postDto->Id === null;
        if (!$isIdNull) 
            throw new InfraException('DAOのCreateメソッドに入力するDTOのidはnullでなければなりません。');
    
        $query = '
            INSERT INTO posts (user_id, content, is_exists) 
            VALUE (:user_id, :content, :is_exists);
        ';

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':user_id', $postDto->UserId, PDO::PARAM_INT);
        $stmt->BindValue(':content', $postDto->Content, PDO::PARAM_STR);
        $stmt->BindValue(':is_exists', $postDto->IsExists, PDO::PARAM_BOOL);
        $stmt->Execute();
    }
    // #endregion

    // #region Read系処理
    public function Read(bool $isDateDesc = true) : Generator {
        $descOrAsc = $isDateDesc ? 'DESC' : 'ASC';
        $query = "
            SELECT * FROM posts
            ORDER BY post_date_time {$descOrAsc};
        ";

        $stmt = $this->_pdo->Query($query);
        foreach($stmt as $row)
            yield PostDTO::FromArray($row);
    }

    public function PostWithUser(bool $isPostDateDesc = true) : Generator {
        $descOrAsc = $isPostDateDesc ? 'DESC' : 'ASC';
        $query = "
            SELECT 
                posts.id AS id, 
                users.id AS user_id, 
                users.name AS user_name, 
                posts.post_date_time AS post_date_time, 
                posts.content AS content, 
                posts.is_exists AS is_exists 
            FROM posts
            INNER JOIN users ON posts.user_id = users.id
            ORDER BY posts.post_date_time {$descOrAsc};
        ";

        $stmt = $this->_pdo->Query($query);
        foreach($stmt as $row)
            yield PostWithUserDTO::FromArray($row);
    }
    // #endregion

    // #region Update系処理
    public function Update(PostDto $postDto) {
        $isIdNull = $postDto->Id === null;
        if ($isIdNull) 
            throw new InfraException('DAOのUpdateメソッドに入力するDTOのidはnull以外の値でなければなりません。');
    
        $query = '
            UPDATE posts SET 
                user_id = :user_id, 
                post_date_time = :post_date_time, 
                content = :content, 
                is_exists = :is_exists 
            WHERE ID = :id;
        ';

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':user_id', $postDto->UserId, PDO::PARAM_INT);
        $stmt->BindValue(':post_date_time', $postDto->PostDateTime, PDO::PARAM_STR);
        $stmt->BindValue(':content', $postDto->Content, PDO::PARAM_STR);
        $stmt->BindValue(':is_exists', $postDto->IsExists, PDO::PARAM_BOOL);
        $stmt->BindValue(':id', $postDto->Id, PDO::PARAM_INT);
        $stmt->Execute();
    }
    // #endregion
}
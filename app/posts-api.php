<?php

declare(strict_types=1);

require_once './utils/parse-url.php';
require_once './utils/sanitize.php';

interface IPostApi
{
    public static function get(?string $url): void;
    public static function post(): void;

    public static function delete(): void;
    public static function patch(): void;


}

class PostsAPI implements IPostApi
{
    private static PostsAPI|null $instance = null;
    private PDO $db;
    private array $url_params;

    private function __construct()
    {
        $this->db = Database::getDBInstance()->getConnection();
        $this->url_params = parse_url_string();
    }

    private static function getPostApiInstance(): PostsAPI | null
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function get(?string $url = ''): void
    {

        $params = self::getPostApiInstance()->url_params;

        if ($params["url"] === 'posts') {

            if (!empty($params["id"])) {

                (int) $id = $params["id"];
                $stmt = self::getPostApiInstance()->db->prepare("SELECT * FROM `users` WHERE `id` = ?");

                $stmt->execute([$id]);
                $post = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($post) {

                    echo json_encode($post);
                } else {
                    http_response_code(404);

                    echo json_encode([
                        "status" => false,
                        "message" => 'Post not found'
                    ]);
                }
            } else {

                $stmt = self::getPostApiInstance()->db->query("SELECT * FROM `users`");
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($posts);
            }
        }

    }


    public static function post(): void
    {

        $title = $_POST['title'] ?? null;
        $body = $_POST['body'] ?? null;


        $params = self::getPostApiInstance()->url_params;
        if ($params["url"] === 'posts') {

            if ((empty($title) && empty($body)) || (!isset($title) && !isset($body))) {
                http_response_code(400);

                echo json_encode([
                    "status" => false,
                    "message" => "body or title must be provided!",

                ]);

                return;
            }

            $query = "INSERT INTO `users` (title, body) VALUES (?, ?)";

            try {


                $stmt = self::getPostApiInstance()->db->prepare($query);
                if ($stmt->execute([sanitize_input($title), sanitize_input($body)])) {

                    $stmt2 = self::getPostApiInstance()->db->query("SELECT * FROM `users` ORDER BY `id` DESC LIMIT 1");
                    $post = $stmt2->fetch(PDO::FETCH_ASSOC);

                    http_response_code(201);

                    echo json_encode([
                        "status" => true,
                        "message" => "Post was created",
                        "item" => $post ?? null
                    ]);
                } else {
                    echo json_encode([
                        "status" => false,
                        "message" => "Post was not created",
                        "item" => null
                    ]);
                }
            } catch (PDOException $e) {

                echo $e;
            }


        }

    }

    public static function delete(): void
    {
        $params = self::getPostApiInstance()->url_params;

        if ($params["url"] === 'posts') {
            if (!empty($params["id"])) {
                if (!is_numeric($params["id"])) {
                    http_response_code(400);

                    echo json_encode([
                        "status" => true,
                        "message" => "id must be as numeric type not string"
                    ]);
                    return;
                }

                (int) $id = $params["id"];

                try {
                    $delete_query = "DELETE from `users` WHERE id = ?";
                    $found_post_query = "SELECT * FROM `users` WHERE id = ?";

                    $post_stmt = self::getPostApiInstance()->db->prepare($found_post_query);
                    $post_stmt->execute([$id]);
                    $post = $post_stmt->fetch(PDO::FETCH_ASSOC);

                    if ($post) {

                        $stmt = self::getPostApiInstance()->db->prepare($delete_query);

                        if ($stmt->execute([$id])) {
                            http_response_code(200);
                            echo json_encode([
                                "status" => true,
                                "message" => "Post deleted"
                            ]);
                        } else {
                            http_response_code(400);
                            echo json_encode([
                                "status" => false,
                                "message" => "Operation failed"
                            ]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode([
                            "status" => false,
                            "message" => "Operation failed such element does not exist!"
                        ]);
                    }


                } catch (PDOException $e) {
                    echo $e;
                }
            }
        }
    }




    public static function patch(): void
    {

        $params = self::getPostApiInstance()->url_params;
        $data = json_decode(file_get_contents('php://input'), true);


        if ($params["url"] === 'posts') {

            if (!empty($params["id"])) {
                if (!is_numeric($params["id"])) {
                    http_response_code(400);

                    echo json_encode([
                        "status" => true,
                        "message" => "id must be as int type not string"
                    ]);
                    return;
                }

                (int) $id = $params["id"];
                    

                if ((empty($data['body']))) {
              
                    http_response_code(400);

                    echo json_encode([
                        "status" => false,
                        "message" => "body  must be provided!",

                    ]);

                    return;
                }

                // $title = sanitize_input($data['title']);

                $body = sanitize_input($data['body']);
                $query = "UPDATE `users` SET  body = ? WHERE id = ?";

                try {


                    $stmt = self::getPostApiInstance()->db->prepare($query);
                    if ($stmt->execute([$body, $id])) {

                        $stmt2 = self::getPostApiInstance()->db->query("SELECT * FROM `users` ORDER BY `id` DESC LIMIT 1");
                        $post = $stmt2->fetch(PDO::FETCH_ASSOC);

                        http_response_code(201);

                        echo json_encode([
                            "status" => true,
                            "message" => "Post was edited",
                            "item" => $post ??  null
                        ]);
                    } else {
                        echo json_encode([
                            "status" => false,
                            "message" => "Post was not edited",
                            "item" => null
                        ]);
                    }
                } catch (PDOException $e) {

                    echo $e;
                }


            }



        }
        else {
            echo json_encode('Failed');

        }
    }
   
}
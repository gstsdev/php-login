<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

// Include config file
require_once "config.php";

class Request
{

    /**
     * @param string $param_name
     * @param string &$binding
     * @param ?string $error_msg
     * @param ?string &$error_output
     * @return boolean
     */
    public static function get_body_param($param_name, &$binding, $error_msg = null, &$error_output = null)
    {
        if (isset($_POST)) {
            if (isset($_POST[$param_name])) {
                $binding = $_POST[$param_name];
                return true;
            } else if ($error_msg !== null) {
                if ($error_output) {
                    $error_output = $error_msg;
                } else {
                    echo $error_msg;
                }
            }
        }

        return false;
    }
}

class ResultSet implements ArrayAccess
{
    public function __construct(protected array $rows)
    {
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->rows[$offset];
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->rows[$offset]->offsetExists($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new Exception("not implemented");
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new Exception("not implemented");
    }

    public function count(): int
    {
        return count($this->rows);
    }
}

abstract class Model
{
    protected static string $table;
    protected static $fields;
    protected static \mysqli | null $conn = null;

    public static function setConnection($connection)
    {
        self::$conn = $connection;
    }

    /** 
     * @param ?mixed $query
     * @param ?array $projection
     * @return ResultSet 
     */
    public static function findAll(mixed $query = null, array $projection = null)
    {
        $sql_query = "SELECT";

        if ($projection != null && count($projection)) {
            $sql_query .= " " . implode(", ", $projection) . " ";
        } else {
            $sql_query .= " * ";
        }

        $sql_query .= "FROM " . self::$table . "";

        if ($query !== null && count(array_keys($query)) > 0) {
            $sql_query_fields = "";
            foreach ($query as $field) {
                if (!array_search($field, self::$fields)) {
                    throw new Error("invalid field $field");

                    $sql_query_fields .= "$field = ?, ";
                }
            }

            $sql_query_fields = rtrim($sql_query_fields, ", ");
            $sql_query .= " WHERE " . $sql_query_fields;
        }

        $sql_query .= ";";

        if ($stmt = self::$conn->prepare($sql_query)) {
            foreach ($query as $_ => $value) {
                $stmt->bind_param("s", $param_value);
                $param_value = $value;
            }

            if (!$stmt->execute() || !$stmt->fetch()) {
                throw new Error("Oops! Something went wrong. Please try again later.");
            }

            $result = $stmt->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);

            return new ResultSet($rows);
        }
    }
}

class User extends Model
{
    static string $table = "users";
    static $fields = ['username', 'password'];
}

User::setConnection($mysqli);

$username_err = $password_err = $login_err = "";

if (
    Request::get_body_param("username", $username, "Please enter username", $username_err)
    && Request::get_body_param("password", $password,  "Please enter your password", $password_err)
) {
    $users = User::findAll(["username" => $username]);

    if ($users->count() === 1) {
        $foundUser = $users[0];

        if (password_verify($password, $foundUser->password)) {
            // Password is correct, so start a new session
            session_start();

            // Store data in session variables
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $id;
            $_SESSION["username"] = $username;

            // Redirect user to welcome page
            header("location: welcome.php");
        } else {
            $login_err = "Invalid username or password.";
        }
    } else {
        $login_err = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 360px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php
        if (!empty($login_err)) {
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
</body>

</html>